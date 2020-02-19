<?php
namespace app\index\controller;

use app\admin\model\GeeOsgroup; // 前置操作
use app\admin\model\GeeOstype; //产品分类表
use app\index\controller\Common; //产品组表
use app\index\model\GeeBilling; //产品表
use app\index\model\GeeProduct; //订单表
use app\index\model\GeeProductClass; //服务器组表
use app\index\model\GeeProductGroup; //服务器组表
use app\index\model\GeeServer;
use app\index\model\GeeServerAdded;
use app\index\model\GeeServerAddedItems;

//服务器组表

class Server extends Common
{
    public function index()
    {
        $server = new GeeServer();
        $osgroup = new GeeOsgroup();
        $ostype = new GeeOstype();
        $serverlist = $server->where('user_id = ' . session('_userInfo')['id'])->order('id desc')->paginate(10);
        foreach($serverlist as $k=>$v){
          if($v['end_time'] <= time()){
            $server->where('id = '.$v['id'])->update(['status'=>1]);
            $v['status'] = 1;
          }
        }
        $this->assign('list', $serverlist);
        $oslist = $osgroup->order('sort desc,id desc')->select();
        $this->assign('oslist', $oslist);
        $defualtOs = $ostype->where('group_id = ' . $oslist[0]['id'])->select();
        $this->assign('ostypelist', $defualtOs);

        return $this->fetch('Server/index');
    }
    /**
     * 租用物理服务器详情
     */
    public function detail()
    {
        $server = new GeeServer();
        $osgroup = new GeeOsgroup();
        $ostype = new GeeOstype();
        $sg = new GeeServerAdded();
        $sgi = new GeeServerAddedItems();
        $pro = new GeeProduct();
        $group = new GeeProductGroup();
        $b = new GeeBilling();

        $g = $_GET;
        $info = $server->where('id = ' . $g['id'])->find();
        $info['groupname'] = $group->where('id = ' . $info['pro_group_id'])->find()['name'];
        $info['proname'] = $pro->where('id = ' . $info['pro_id'])->find()['name'];
        $info['config'] = $pro->where('id = ' . $info['pro_id'])->find()['describe'];
        $info['config'] = to_verticalbar($info['config']);
        foreach (json_decode($info['server_added'], false) as $k => $v) {
            $name = $sg->where('name = "' . $k . '"')->find();
            $added[$k][0] = $name['title'];
            if ($name['type'] == 3) {
                $addedid = explode(',', $v);
                $sgiinfo = $sgi->where('id = ' . $addedid[0])->find();
                $added[$k][1] = $addedid[1] . $sgiinfo['title'];
            } else {
                $sgiinfo = $sgi->where('id = ' . $v)->find();
                $added[$k][1] = $v == 0 ? '未使用该服务' : $sgiinfo['title'];
            }
        }
        $info['added'] = json_encode($added);
        // dump($info);
        $this->assign('info', $info);
        return $this->fetch('Server/detail');
    }

    /**
     * 物理服务器信息修改
     */
    public function edits()
    {
        $s = new GeeServer();
        $data = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $id = $data['id'];
        unset($data['id']);
        foreach ($data as $key => $var) {
            if (empty($var) && $var != '0') {
                unset($data[$key]);
            }
        }
        $w['id'] = $id;
        $sres = $s->save($data, $w);
        if ($sres) {
            return json_encode($ret);
        } else {
            $ret['status'] = 422;
            $ret['msg'] = '网络异常!请稍后再试!';
            return json_encode($ret);
        }
    }
    public function add()
    {
        $group = new GeeProductGroup();
        $pro = new GeeProduct();
        $osgroup = new GeeOsgroup();
        $ostype = new GeeOstype();
        // dump($_GET['type']);
        if ($_GET['type'] != 'renew') {
            $proItems = $pro->where('type=8')->select();
            $groups = [];
            $groupList = [];
            // dump($proItems);
            foreach ($proItems as $k => $v) {
                $groups[$k] = $v['group_id'];
            }
            $groups = array_unique($groups);
            $num = 0;
            foreach ($groups as $k => $v) {
                $groupList[$num] = $group->where('id = ' . $v)->find();
                $num++;
            }
            $this->assign('group', $groupList);

            if ($_GET['id']) {
                foreach ($items as $k => $v) {
                    $defualtPro[$k] = $pro->where('id = ' . $v . ' and group_id = ' . $groupList[0]['id'])->find();
                }
                // $defualtPro = $pro->where('group_id = '.$groupList[0]['id'])->select();
            } else {
                $defualtPro = $pro->where('group_id = ' . $groupList[0]['id'])->select();
            }
            $this->assign('prolist', $defualtPro);

            $oslist = $osgroup->order('sort desc,id desc')->select();
            $this->assign('oslist', $oslist);

            $defualtOs = $ostype->where('group_id = ' . $oslist[0]['id'])->select();
            $this->assign('ostypelist', $defualtOs);
        } else {
            $server = new GeeServer();
            $sinfo = $server->where('id = ' . $_GET['id'])->find();
            $dAdded = json_decode($sinfo['server_added'], true);
            $added = new GeeServerAdded();
            $addeditem = new GeeServerAddedItems();
            foreach ($dAdded as $k => $v) {
                $group = $added->where('name = "' . $k . '"')->find();
                // dump($group);
                if ($group['type'] != 3) {
                    $dAdded[$k] = $addeditem->where('group_id = ' . $group['id'] . ' and title="' . $v . '"')->find()['id'];
                } else {
                    // dump($group);
                    // dump($v);
                    $dAdded[$k] = $addeditem->where('group_id = ' . $group['id'])->find()['id'] . ',' . $v;
                }

            }
            // dump($dAdded);

            // dump($sinfo);
            $this->assign('sinfo', $dAdded);
        }

        return $this->fetch('Server/add');
    }
    /**
     * 租用物理服务器
     */
    public function addAuth()
    {
        $data = $_POST;
        $class = new GeeProductClass();
        $group = new GeeProductGroup();
        $pro = new GeeProduct();
        $billing = new GeeBilling();
        $sadded = new GeeServerAdded();
        $saddeditem = new GeeServerAddedItems();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $userinfo = session('_userInfo');
        if($userinfo['realverify'] != 2){
          $ret['status'] = 422;
          $ret['msg'] = '请先进行实名认证!';
          return json_encode($ret);
        }
        $item = $pro->where('id = ' . $data['pro_id'])->find();
        $pro_id = json_decode($item['plug_config'], true)['product_id'];
        $items = $pro->where('id = ' . $data['pro_id'])->find();
        $type = $data['type'];
        $addeds = $data['added'];
        $price = json_decode($this->getPrice(['type' => $data['type'], 'pro_id' => $data['pro_id'], 'pay_length' => $data['pay_length'], 'id' => $data['id'], 'name' => $data['name'], 'added' => $addeds]), true)['data']['price'];
        $price = str_replace(',', '', $price);
        $pinfo = [
            [
                'group' => $group->where('id = ' . $items['group_id'])->value('name'),
                'class' => $class->where('id = ' . $items['type'])->value('title'),
                'name' => $items['name'],
                'type' => $type,
                'id' => $data['id'] ? $data['id'] : 0,
                'num' => $data['num'],
                'config' => $items['describe'],
                'years' => $data['pay_length'],
                'price' => str_replace(",","",$price),
                'pro_type' => $class->where('id = ' . $items['type'])->value('name'),
                'group_id' => $items['group_id'],
                'pro_id' => $data['pro_id'],
                'hostname' => $data['name'] ? $data['name'] : $this->vali_name('number', rand_name(8), 8, 'rand_name'),
                'added' => $data['added'],
                'remake' => $data['remake'],
                'user_id' => session('_userInfo')['id'],
                'osgroup' => $data['osgroup'],
                'ostype' => $data['ostype'],
                'username' => $data['username'],
                'password' => $data['password'],
            ],
        ];
        // dump($pinfo);
        // return;

        $number = $this->vali_name('number', rand_name(8), 8, 'rand_name');
        $order_number = date('Ymdhis', time()) . rand(10000, 99999);
        $billing_save = [
            'number' => $number,
            'order_number' => $order_number,
            'pro_list' => json_encode($pinfo),
            'user_id' => session('_userInfo')['id'],
            'type' => '0',
            'order_type' => $type,
            'money' => (double) str_replace(",","",$price),
            'balance' => (double) session('_userInfo')['balance'] - (double) $pinfo['price'],
            'cash' => 0,
            'channel_type' => '0',
            'remarks' => '',
            'status' => '0',
            'order_status' => '2',
        ];

        // dump($billing_save);
        // return;
        $_SESSION['_pro_order'] = $billing_save['order_number'];
        $_SESSION['_pro_info'] = $pinfo;
        $_SESSION['_pro_order'] = $billing_save['order_number'];

        // dump($pinfo);
        // dump($billing_save);
        // return;
        $billing->save($billing_save);
        return json_encode($ret);
    }
    /**
     * 获取服务器子项信息
     */
    public function getAddedItems()
    {
        $id = $_POST['id'];
        $pro = new GeeProduct();
        if ($_POST['type'] == 'update') {
            $pro = new GeeProduct();
            $item = $pro->where('id = ' . $_POST['pro_id'])->find();
            $items = explode(',', $item['update_list']);
            $num = 0;
            foreach ($items as $k => $v) {
                $proitem = $pro->where('id = ' . $v . ' and group_id = ' . $_POST['id'])->find();
                if ($proitem) {
                    $proList[$num] = $proitem;
                } else {
                    continue;
                }
                $num++;
            }
        } else {
            $proList = $pro->where('group_id = ' . $id)->select();
        }
        return json_encode($proList);
    }
    /**
     * 获取服务器子项增值服务
     */
    public function getAdded()
    {
        $id = $_POST['id'];
        $pro = new GeeProduct();
        $added = new GeeServerAdded();
        $addeditem = new GeeServerAddedItems();
        $proinfo = $pro->where('id = ' . $id)->find();
        $addeds = explode(',', $proinfo['added']);
        // dump($addeds);
        foreach ($addeds as $k => $v) {
            $data[$k] = $added->where('id = ' . $v)->order('sort desc, id desc')->find();
            $items = $addeditem->where('group_id = ' . $v)->order('sort desc, id desc')->select();
            // dump($items);
            $data[$k]['child'] = list_toArray($items);
        }
        // dump($data);
        return json_encode($data);

        // $addedVal = $added->where()

    }
    /**
     * 获取操作系统版本
     */
    public function getOstypes()
    {
        $id = $_POST['id'];
        $ostype = new GeeOstype();
        $oslist = $ostype->where('group_id = ' . $id)->order('sort desc,id desc')->select();

        return json_encode($oslist);

    }
    /**
     * 获取服务器密码
     */
    public function getpass()
    {
        $id = $_POST['id'];
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ser = new GeeServer();
        $serinfo = $ser->where('id = ' . $id)->find();
        if (!$serinfo) {
            $ret['status'] = 422;
            $ret['msg'] = '未找到该服务器!';
            return json_encode($ret);
        }
        $ret['data'] = $serinfo['password'];
        // dump($data);
        return json_encode($ret);
    }
    /**
     * 重装操作系统
     */
    public function resetos()
    {
        $data = $_POST;
        $id = $data['id'];
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ser = new GeeServer();
        $info = $ser->where('id = ' . $id)->find();
        if ($info['status'] != 3) {
            $ret['status'] = 422;
            $ret['msg'] = '服务器状态异常!请稍后再试!';
            return json_encode($ret);
        }
        if (!isset($data['password']) || empty($data['password'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入密码!';
            return json_encode($ret);
        }
        $upres = $ser->where('id = ' . $id)->update(['status' => 2, 'osgroup' => $data['osgroup'], 'ostype' => $data['ostype'], 'username' => $data['username'], 'password' => $data['password']]);
        if (!$upres) {
            $ret['status'] = 422;
            $ret['msg'] = '未找到该服务器!';
            return json_encode($ret);
        }
        // dump($data);
        return json_encode($ret);
    }
    /**
     * 验证随机名称
     */
    public function vali_name($key, $val, $len, $func)
    {
        if (!is_int($val) && !is_bool($va)) {
            $w = '"' . $val . '"';
        }
        $has = db('gee_billing')->where('`' . $key . '` = ' . $w)->find();
        if ($has) {
            $vali = $this->vali_name($key, $func($len), $len, $func);
            return $vali;
        } else {
            return $val;
        }
    }

    /**
     * 获取价格
     */
    public function getPrice($post = [])
    {
        $data = $post ? $post : $_POST;
        $pro = new GeeProduct();
        $added = new GeeServerAdded();
        $addeditem = new GeeServerAddedItems();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $item = $pro->where('id = ' . $data['pro_id'])->find();
        $pro_id = json_decode($item['plug_config'], true)['product_id'];
        $added = json_decode($data['added'], false);
        $addedprice = 0;
        // dump($added);
        foreach ($added as $k => $v) {
            if ($v) {
                if (strpos($v, ',')) {
                    $id = explode(',', $v);
                    $ainfo = $addeditem->where('id = ' . $id[0])->find();
                    $addedprice += (double) $this->addedPrice($data['pay_length'], $ainfo, $id[1]);
                } else {
                    $ainfo = $addeditem->where('id = ' . $v)->find();
                    $addedprice += (double) $this->addedPrice($data['pay_length'], $ainfo);
                }
            } else {
                $addedprice += 0;
            }
        }
        if ($data['type'] != 'update') {
            // dump($item);
            //固定换算价格
            switch ((int) $data['pay_length']) {
                case 1:
                    $lengthPrice = $item['month'] * 1;
                    break;
                case 2:
                    $lengthPrice = $item['month'] * 2;
                    break;
                case 3:
                    $lengthPrice = $item['quarter'] * 1;
                    break;
                case 4:
                    $lengthPrice = $item['quarter'] * 1 + $item['month'] * 1;
                    break;
                case 5:
                    $lengthPrice = $item['quarter'] * 1 + $item['month'] * 2;
                    break;
                case 6:
                    $lengthPrice = $item['semestrale'] * 1;
                    break;
                case 7:
                    $lengthPrice = $item['semestrale'] * 1 + $item['month'] * 1;
                    break;
                case 8:
                    $lengthPrice = $item['semestrale'] * 1 + $item['month'] * 2;
                    break;
                case 9:
                    $lengthPrice = $item['semestrale'] * 1 + $item['quarter'] * 1;
                    break;
                case 10:
                    $lengthPrice = $item['years'];
                    break;
                case 11:
                    $lengthPrice = $item['years'];
                    break;
                case 12:
                    $lengthPrice = $item['years'];
                    break;
                case 24:
                    $lengthPrice = $item['biennium'];
                    break;
                case 36:
                    $lengthPrice = $item['triennium'];
                    break;
                default:
                    $ret['status'] = 422;
                    $ret['msg'] = '非法操作！';
                    return json_encode($ret);
                    break;
            }
        } else {
            $lengthPrice = $item['month'] * 1;
        }
        // dump(json_decode($data['added'],false));
        $totalprice = $lengthPrice + $addedprice;
        // dump($addedprice);
        // dump($lengthPrice);
        // dump($totalprice);
        $ret['data'] = ['price' => number_format($totalprice, 2)];
        return json_encode($ret);
    }
    public function addedPrice($l, $item, $num = 1)
    {
        // dump($item);
        switch ((int) $l) {
            case 1:
                $lengthPrice = $item['month'] * $num * 1;
                break;
            case 2:
                $lengthPrice = $item['month'] * $num * 2;
                break;
            case 3:
                $lengthPrice = $item['quarter'] * $num * 1;
                break;
            case 4:
                $lengthPrice = $item['quarter'] * $num * 1 + $item['month'] * 1;
                break;
            case 5:
                $lengthPrice = $item['quarter'] * $num * 1 + $item['month'] * 2;
                break;
            case 6:
                $lengthPrice = $item['semestrale'] * $num * 1;
                break;
            case 7:
                $lengthPrice = $item['semestrale'] * $num * 1 + $item['month'] * 1;
                break;
            case 8:
                $lengthPrice = $item['semestrale'] * $num * 1 + $item['month'] * 2;
                break;
            case 9:
                $lengthPrice = $item['semestrale'] * $num * 1 + $item['quarter'] * 1;
                break;
            case 10:
                $lengthPrice = $item['years'] * $num;
                break;
            case 11:
                $lengthPrice = $item['years'] * $num;
                break;
            case 12:
                $lengthPrice = $item['years'] * $num;
                break;
            case 24:
                $lengthPrice = $item['years'] * $num * 2;
                break;
            case 36:
                $lengthPrice = $item['years'] * $num * 3;
                break;
            default:
                return 0;
                break;
        }
        return $lengthPrice ? $lengthPrice : 0;
    }
}

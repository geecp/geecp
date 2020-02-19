<?php
namespace app\index\controller;

use app\admin\model\GeeAddons; // 前置操作
use app\admin\model\GeeDomainContact; // 请求类
use app\admin\model\GeeDomainPrice;
use app\admin\model\GeeLog; //产品分类表
use app\admin\model\GeeProduct; //产品分类表
use app\admin\model\GeeProductClass; //
use app\admin\model\GeeProductGroup; //产品组表
use app\admin\model\GeeUser; //产品表
use app\index\controller\Common; //插件表
use app\index\model\GeeBilling; //产品购买配置表
use app\index\model\GeeDomain; // 日志表
use app\index\model\GeeProConfig; // 用户表
use app\index\model\GeeServer; //
use app\index\model\GeeServerAdded; //
use app\index\model\GeeServerAddedItems; //
use think\Controller;

//订单表
//订单表

class Buy extends Common
{
    /**
     * 确认订单
     */
    public function confirm()
    {
        if (!$_SESSION['_pro_info']) {
            echo '<script>window.history.go(-1);</script>';
            exit;
        }
        $info = $_SESSION['_pro_info'];
        $order = $_SESSION['_pro_order'];
        // dump($info);
        // dump($order);
        // dump($_SESSION);
        $class = [];
        $money = 0;
        foreach ($_SESSION['_pro_info'] as $k => $v) {
            $class[$k] = $v['class'];
            $str = $v['price'];
            //中文标点
            $char = ",。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";

            $pattern = array(
                '/[' . $char . ']/u', //中文标点符号
                '/[ ]{2,}/',
            );
            $price = preg_replace($pattern, '', $str);
            $money += (double) str_replace(",", "", $price);
        }
        $class = array_unique($class);
        if (count($class) <= 1) {
            $this->assign('proname', $class[0]);
        } else {
            $this->assign('proname', '组合型产品');
        }
        $this->assign('info', $info);
        $this->assign('order', $order);
        $this->assign('money', number_format(str_replace(",", "", $money), 2));
        unset($_SESSION['_pro_info']);
        unset($_SESSION['_pro_order']);
        // dump($info);
        return $this->fetch('Buy/confirm');
    }
    /**
     * 线上支付
     */
    public function onlinepay()
    {
        $billing = new GeeBilling();
        $order_id = $_GET['order'];
        $info = $billing->where('`order_number` = "' . $order_id . '"')->find();
        $prolist = json_decode($info['pro_list']);
        $this->assign('prolist', $prolist);
        $this->assign('money', $info['money']);
        // dump($_SESSION);
        if ($info['status'] != '0' && ($info['order_status'] != '0' && $info['order_status'] != '2')) {
            return $this->redirect('index/Billing/order');
        }
        return $this->fetch('Buy/onlinepay');
    }
    /**
     * 付款接口
     */
    public function pay()
    {
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $data = $_POST;
        $billing = new GeeBilling();
        $info = $billing->where('`order_number` = "' . $data['order'] . '"')->find();
        $up_w['id'] = $info['id'];
        $user = db('gee_user')->where('id = ' . session('_userInfo')['id'])->find();

        if ($info['status'] != '0' && ($info['order_status'] != '0' && $info['order_status'] != '2')) {
            $ret['status'] = 400;
            $ret['msg'] = '该订单状态已更变';
            return json_encode($ret);
        }
        if ((int) $data['type'] == 1) {
            //第三方支付
            $billing_save_data['cash'] = '1';
            $prolist = json_decode($info['pro_list']);
            if ($prolist != '0' && $prolist) {
                foreach ($prolist as $k => $v) {
                    $v = object_toArray($v);
                    if ($k != 0) {
                        $name .= ',' . $v['class'];
                    } else {
                        $name .= $v['class'];
                    }
                }
            }
            /**
             * 支付必要传参
             */
            $paypost['trade_no'] = $data['order'];
            $paypost['total_amount'] = (double) $info['money'];
            $paypost['subject'] = $info['pro_list'] == '0' ? '账户充值' : '产品购买';
            $paypost['body'] = $info['pro_list'] == '0' ? '账户充值 - 金额为:' . to_double($info['money']) : '产品购买 - 购买产品:' . $name;
            $html = alipay($paypost, 'http://' . $_SERVER['HTTP_HOST'] . '/api/notify_url', 'http://' . $_SERVER['HTTP_HOST'] . '/api/return_url', 1);
        } else {
        	
	        if ((double) $user['balance'] < (double) $info['money']) {
	            $ret['status'] = 400;
	            $ret['msg'] = '余额不足';
	            return json_encode($ret);
	        }
            //余额支付
            $userup = db('gee_user')->where('id = ' . session('_userInfo')['id'])->update(['balance' => (double) $user['balance'] - (double) $info['money']]);
            // dump($user);
            // dump($info);
            if (!$userup) {
                $ret['status'] = 400;
                $ret['msg'] = '余额扣款失败！';
                return json_encode($ret);
            }
            $pc = new GeeProConfig();
            //执行所属产品操作
            if ($info['pro_list'] != '0' && $info['pro_list']) {
                $pcs = $pc->where('order_number = "' . $data['order'] . '"')->find();
                $configs = json_decode($pcs['config'], true);
                $selfPro = json_decode($info['pro_list'], false);
                // dump($info);
                // return ;
                if ($configs['_create_putData']) {
                    if ($configs['_create_putData']['class'] == 'domain') {
                        $d = new GeeDomain();
                        $dc = new GeeDomainContact();
                        $dp = new GeeDomainPrice();
                        $pro = new GeeProduct();
                        $addons = new GeeAddons();
                        $plug = new $configs['_create_putData']['plug']();
                        $func = $configs['_create_putData']['class'];
                        // dump($configs['_create_putData']);
                        foreach ($configs['_create_putData']['data'] as $k => $v) {
                            // dump($v);
                            $suffix = '.' . explode(".", $v['domainname'])[1];
                            $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
                            $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
                            $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
                            if ($configs['_create_putData']['action'] != 'domainRenew') {
                                $dcinfo = $dc->where('id = ' . $v['userid'])->find();
                                $userid = json_decode($dcinfo['contact_id'], true)[$proinfo['name']]['value'];
                            }
                            // dump(json_decode($dcinfo['contact_id'],true));
                            if ($configs['_create_putData']['action'] == 'domainRenew') {
                                $putData = [
                                    'way' => $adninfo['name'],
                                    'pro_id' => $dpinfo['pro_id'],
                                    'function' => 'control',
                                    'action' => $configs['_create_putData']['action'],
                                    'data' => [
                                        "domainname" => $v['domainname'],
                                        "years" => $v['years'],
                                        "exptme" => $v['exptme'],
                                    ],
                                ];
                            } else {
                                $putData = [
                                    'way' => $adninfo['name'],
                                    'pro_id' => $dpinfo['pro_id'],
                                    'function' => 'control',
                                    'action' => 'createDom',
                                    'data' => [
                                        'userid' => $userid,
                                        'domainname' => $v['domainname'],
                                        'years' => $v['years'],
                                        'domainpass' => $v['domainpass'],
                                        'dns1' => $v['dns1'],
                                        'dns2' => $v['dns2'],
                                    ],
                                ];
                            }
                            // dump($v);
                            // dump($putData);

                            $res = $plug->$func($putData);
                            $res = json_decode($res, true);
                            if ($res['status'] == 'failed') {
                                $ret['status'] = 400;
                                $ret['msg'] = '请求超时！请联系管理员处理！错误码:' . explode(' ', $res['data'])[0];
                                $isfailed = true;
                                break;
                            } else {
                                if ($configs['_create_putData']['action'] == 'domainRenew') {
                                    $save = [
                                        'user_id' => session('_userInfo')['id'],
                                        'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                    ];
                                    $d->where('domainname = "' . $v['domainname'] . '"')->update($save);
                                } else {
                                    $save = [
                                        'user_id' => session('_userInfo')['id'],
                                        'userid' => $userid,
                                        'domainname' => $v['domainname'],
                                        'years' => $v['years'],
                                        'domainpass' => $v['domainpass'],
                                        'isname' => 0,
                                        'dns' => json_encode(['dns1' => $v['dns1'], 'dns2' => $v['dns2']]),
                                        'status' => 0,
                                        'r_status' => 0,
                                        'newstas' => 0,
                                        'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                    ];
                                    $d->save($save);
                                }
                            }
                        }
                        // return;

                        // dump($ret);
                        if ($isfailed) {
                            // dump(session('_userInfo')['balance']);
                            // dump($info['money']);
                            $userup = db('gee_user')->where('id = ' . session('_userInfo')['id'])->update(['balance' => ((double) session('_userInfo')['balance'] - (double) $info['money']) + (double) $info['money']]);
                            return json_encode($ret);
                        }
                    } else {
                        $plug = new $configs['_create_putData']['plug']();
                        $func = $configs['_create_putData']['class'];
                        $putData = $configs['_create_putData']['data'];
                        if (!$putData['function']) {
                            $putData['function'] = $configs['_create_putData']['function'];
                        }
                        // dump($configs);
                        // dump($configs['_create_putData']);
                        // dump($putData);
                        // return ;
                        $res = $plug->$func($putData);
                        // dump($res);
                        // $ret['status'] = 400;
                        // $ret['msg'] = '请求超时！';
                        // return json_encode($ret);
                    }
                } else {
                    $server = new GeeServer();
                    $selfPro = object_toArray($selfPro);
                    foreach ($selfPro as $k => $v) {
                        // dump($v['pro_type']);
                        if ($v['pro_type'] == 'server') {
                            // dump($info);
                            if ($info['order_type'] == 'renew') {
                                $sinfo = $server->where('id', (int) $v['id'])->find();
                                // dump($v['id']);
                                $dt = date('Y-m-d H:i:s', $sinfo['end_time']);
                                $updata = [
                                    'server_added' => $v['added'],
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime($dt . "+" . (int) $v['years'] . " month"))),
                                ];
                                if ($sinfo['status'] == 1) {
                                    $updata['status'] = 3;
                                }
                                // dump($sinfo);
                                // dump($dt);
                                // dump(date("Y-m-d H:i:s", strtotime($dt."+".(int)$v['years']." month")));
                                // dump($updata);
                                // dump($v);
                                // return false;
                                $server->where('id = ' . $v['id'])->update($updata);

                            } else {
                                $sinfo = [
                                    'pro_group_id' => $v['group_id'],
                                    'pro_id' => $v['pro_id'],
                                    'server_added' => $v['added'],
                                    'name' => $v['hostname'],
                                    'user_id' => $v['user_id'],
                                    'remake' => $v['remake'],
                                    'username' => $v['username'],
                                    'password' => $v['password'],
                                    'osgroup' => (int) $v['osgroup'],
                                    'ostype' => (int) $v['ostype'],
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . (int) $v['years'] . " month"))),
                                    'status' => '0',
                                ];
                                // dump($sinfo);
                                // return;
                                $server->save($sinfo);
                            }
                            // dump($v);
                            // dump($sinfo);
                            // return ;
                            // dump(json_decode($info['pro_list'],false));
                            // return;
                        }

                    }
                    // dump($selfPro);
                    // dump($selfPro['pro_type'] == 'server');
                    // return;
                }
            }
        }
        // dump($html);
        // dump(json_encode($ret));
        // return ;
        $billing_save_data['channel_type'] = $data['type'];
        $billing_save_data['balance'] = (double) $user['balance'] - (double) $info['money'];
        if ((int) $data['type']) {
            $billing_save_data['status'] = '0';
        } else {
            $billing_save_data['status'] = '1';
        }
        $billing_save_data['order_status'] = '1';
        $billing_save_data['is_invoice'] = '1';
        $info_up = $billing->save($billing_save_data, $up_w);
        if (!$info_up) {
            $ret['status'] = 400;
            $ret['msg'] = '请求超时！';
            return json_encode($ret);
        }
        // dump($_SESSION);
        // dump($res);

        if ($html) {
            var_dump($html);
            return;
        }
        return json_encode($ret);
    }
    public function paystatus()
    {

        return $this->fetch('Buy/paystatus');
    }
}

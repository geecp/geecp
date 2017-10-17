<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;

use app\admin\model\Finance_detailed;
use app\admin\model\Finance_invoicelist;
use app\admin\model\Finance_operation;
use app\admin\model\Finance_order;
use app\admin\model\Invoice_info;
use think\Db;
use think\Session;

class Finance extends Base
{
    public function index()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else if (!in_array($res1, $adm_group)) {
            //菜单id
            $id = input('id');
            $url = Db::name('right')->whereIn('id', $adm_group)->where('pid', $id)->order('num')->find()['url'];
            if ($url != '') {
                return $this->redirect($url);
            } else {
                return $this->redirect('error/index');
            }
        } else {
            //控制订单下拉状态
            Session('ostatus', null);
            //查询session删掉
            Session::set('product', null);
            //消费
            $whe=[
                'transaction_type'=>2,
                'status'=>1,
            ];
            $xf = Db::name('finance_detailed')->where($whe)->sum('money');
            $this->assign('xf', round($xf,2));
            //充值
            $whe=[
                'transaction_type'=>1,
                'status'=>1,
            ];
            $cz = Db::name("finance_detailed")->where($whe)->sum('money');
            $this->assign('cz', round($cz,2));
            //余额
            $ye = $cz - $xf;
            $this->assign('ye', round($ye,2));

            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set('num', 1);

            $where = [];
            //查询
            if (input('product')) {
                $where['product'] = array('like', input('product') . '%');
                Session::set('product', input('product'));
            }
            if (input('status')) {
                $where['status'] = input('status');
                Session::set('ostatus', input('status'));
            }

            //订单表
            $finance = new Finance_order();
            $res = $finance->where($where)->paginate(10);
            foreach ($res as $val) {
                $val['userid'] = Db::name("userlist")->where('id', $val['userid'])->find()['username'];
            }

            $this->assign('res', $res);
            $this->assign('count', count($res));
            return view();
        }

    }

    //订单详情
    public function orderInfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];

        //拿到路径
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];

        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            if (input('id')) {
                $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
                $this->assign('name', $name);
                Session::set('id', 5);
                $right=Session::get("right");
                $this->assign('right',$right);
                $where['id'] = input('id');
                $res = Db::name("finance_order")->where($where)->find();

                $res['userid'] = Db::name("userlist")->where('id', $res['userid'])->find()['username'];

                $this->assign('res', $res);
                return view();
            } else {
                $this->error('非法操作');
            }

        }

    }

    //财务操作
    public function billinglist()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {

            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set('num', 2);
            //控制订单下拉状态
            Session('dstatus', null);
            $where = [];
            //如果提交数据过来

            if (input('dstatus')) {
                $where['type'] = input('dstatus');
                Session::set('dstatus', input('dstatus'));
            }
            if (input('time')) {
                $time = explode('-', input('time'));

                $min = $time[0];
                $max = $time[1];
                $where['creat_time'] = ['>', $min];
                $where['creat_time'] = ['<', $max];

            }
            $finance = new Finance_detailed();

            $res = $finance->where('transaction_type', '>', '2')->where('status', '>', '3')->where($where)->order('creat_time', 'desc')->paginate(10);

            foreach ($res as $val) {
                $val['userid'] = Db::name("userlist")->where('userid', $val['userid'])->find()['username'];
                $val['auditor'] = Db::name("admmember")->where("id", $val['auditor'])->find()['username'];
            }
            $this->assign('res', $res);
            return view();
        }
    }

    //手动添加流水信息
    public function adddetail()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            return json(['msg' => 6]);
        }
        if (input('post.')) {

            $data = input('post.');
            $data['creat_time'] = $arr['creat_time'] = date('Y-m-d H:i:s', time());
            $data['auditor'] = $data['auditor'] = Session::get('admin')['id'];
            $data['product'] = '其他';
            if (input('post.transaction_type') == '') {
                return json(['msg' => 4]);
            }
            $user = Db::name("userlist")->where('userid', input('post.userid'))->find();
            if (!$user) {
                return json(['msg' => 5]);
            }
            //如果是入款
            if (input('post.transaction_type') == '4') {
                if (input('post.receivables') == '' || input('post.status') == '') {
                    return json(['msg' => 4]);
                }
                if ($data['receivables'] == '实收') {
                    $data['status'] = 4;
                }
            } else {
                $data['status'] = 6;
            }


            $rand = rand('0000001', '9999999');
            $data['order_id'] = date('YmdHi', time()) . $rand;

            //类型

            //流水记录
            $result = Db::name("finance_detailed")->insert($data);

            if ($result) {
                return json(['msg' => 1]);
            }

        } else {
            return json(['msg' => 7]);
        }
    }

    //财务操作详情
    public function detailinfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        }
        if (input('id')) {
            $where = [];
            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set('num', 2);

            $where['id'] = input('id');
            $res = Db::name("finance_detailed")->where($where)->find();
            $res['auditor'] = Db::name("admmember")->where("id", $res['auditor'])->find()['username'];
            $res['userid'] = Db::name("userlist")->where("id", $res['userid'])->find()['username'];
            $this->assign('res', $res);
        } else {
            $this->error('非法操作');
        }
        return view();
    }

    //发票资质
    public function ticketlist()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {

            //控制订单下拉状态
            Session('istatus', null);
            Session('head', null);
            $where = [];
            if (input('istatus')) {
                $where['status'] = input('istatus');
                Session::set('istatus', input('istatus'));
            }
            if (input('head')) {
                $where['head'] = array('like', input('head') . '%');;
                Session::set('head', input('head'));
            }
            //左侧菜单处理
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set('num', 3);

            //总的消费金额
            $count = Db::name("finance_order")->where('status', 1)->sum('money');
            //已经开好的发票金额
            $money = Db::name("finance_invoicelist")->where('status', '<>', 1)->where('status', '<>', 4)->sum('money');
            //可开发票总金额
            $summoney = $count - $money;
            if ($summoney < 500) {
                $jishumoney = 0;
            } else {
                $jishumoney = $summoney;
            }
            $finance = new Invoice_info();
            $res = $finance->where($where)->paginate(10);
            $this->assign('jishumoney', $jishumoney);
            $this->assign('summoney', $summoney);
            $this->assign('res', $res);
            $this->assign('count', count($res));
            return view();
        }

    }

    //修改发票资质状态
    public function savestatus()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group = explode(',', $adm_group);
        $arr = explode(',', input('data'));
        if (!in_array($res, $adm_group)) {
            return json(['msg' => 2]);
        } else {
            $where = [];
            $data = [];
            $where['id'] = input('post.id');
            //如果是3 就是取消审核,返回审核中
            if (input('post.status') == 3) {
                $invoice['invoice']=0;
                $data['status'] = 2;
                //如果是1 就是审核通过
            } elseif (input('post.status') == 1) {
                $where['id'] = input('post.id');
                $invoice['invoice']=1;
                $data['status'] = 1;
            }
            //同时将审核状态写入到用户表中
            $user=Db::name("invoice_info")->where($where)->field('userid')->find();
            $whe['id']=$user['userid'];
            Db::name('userlist')->where($whe)->update($invoice);
            $res = Db::name("invoice_info")->where($where)->update($data);
            if ($res) {
                return json(['msg' => 1]);
            }
        }

    }

    //发票资质详情
    public function ticketInfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            if (input('id')) {
                $where = [];
                //左侧菜单
                $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
                $this->assign('name', $name);
                Session::set('id', 5);
                $right=Session::get("right");
                $this->assign('right',$right);
                Session::set('num', 3);
                if (input('id')) {
                    $where['id'] = input('id');
                    $res = Db::name("invoice_info")->where($where)->find();
                    /*
                                $res['userid']=Db::name("userlist")->where('id',$res['userid'])->find()['username'];
                                $res['auditor']=Db::name("admmember")->where("id",$res['auditor'])->find()['username'];*/
                    /* if ($res['expnum'])*/
                    $this->assign('res', $res);

                }
                return view();
            } else {
                $this->error('非法操作');
            }

        }

    }

    //发票审核
    public function ticketapply()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            //搜索
            Session('fstatus', null);
            Session('invoice_header', null);

            $where = [];
            if (input('fstatus')) {
                $where['status'] = input('fstatus');
                Session::set('fstatus', input('fstatus'));
            }
            if (input('invoice_header')) {
                $where['invoice_header'] = array('like', input('invoice_header') . '%');;
                Session::set('invoice_header', input('invoice_header'));
            }
            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set('num', 4);
            $finance = new Finance_invoicelist();
            $res = $finance->where($where)->paginate(10);
            foreach ($res as $val) {
                $val['userid'] = Db::name("userlist")->where('id', $val['userid'])->find()['username'];
            }
            $this->assign('res', $res);
            return view();
        }
    }

    //修改发票状态
    public function saveostatus()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group = explode(',', $adm_group);
        $arr = explode(',', input('data'));
        if (!in_array($res, $adm_group)) {
            return json(['msg' => 2]);
        } else {
            $where = [];
            $data = [];
            $data['auditor'] = Session::get('admin')['id'];
            if (input('post.expnum') != '') {
                $where['id'] = input('post.id');
                $data['status'] = 3;
                $data['expnum'] = input('post.expnum');
                $res = Db::name("finance_invoicelist")->where($where)->update($data);

                if ($res) {
                    return json(['msg' => 1]);
                }
            }
            if (input('post.status') == 4) {
                $where['id'] = input('post.id');
                $data['status'] = 4;
            } elseif (input('post.status') == 1) {
                $where['id'] = input('post.id');
                $data['status'] = 2;
            }
            $res = Db::name("finance_invoicelist")->where($where)->update($data);
            if ($res) {
                return json(['msg' => 1]);
            }
        }

    }

    //发票详情
    public function applyInfo()
    {

        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];

        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            if (input('id')) {
                $where = [];
                //左侧菜单
                $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
                $this->assign('name', $name);
                Session::set('id', 5);
                $right = Db::name('right')->where('pid', 5)->order('num')->select();
                $this->assign('right', $right);
                Session::set('num', 4);

                $where['id'] = input('id');
                $res = Db::name("finance_invoicelist")->where($where)->find();
                $res['userid'] = Db::name("userlist")->where('id', $res['userid'])->find()['username'];
                $res['auditor'] = Db::name("admmember")->where("id", $res['auditor'])->find()['username'];
                $address = Db::name("finance_invoiceaddress")->where('id', $res['address'])->find();
                $res['address'] = $address['address'];
                $res['phone'] = $address['phone'];
                $res['postcode'] = $address['postcode'];
                $res['name'] = $address['name'];
                unset($address);
                $this->assign('res', $res);
            } else {
                $this->error('非法操作');
            }
            return view();
        }
    }

    //流水记录
    public function flowrecord()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            Session::set('product', null);
            //处理左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right = Db::name('right')->where('pid', 5)->order('num')->select();
            $this->assign('right', $right);
            Session::set('num', 5);
            Session('t_type', null);

            $where = [];
            //查询
            if (input('t_type')) {
                $where['transaction_type'] = input('t_type');
                Session::set('t_type', input('t_type'));
            }
            if (input('product')) {
                $where['product'] = array('like', input('product') . '%');;
                Session::set('product', input('product'));
            }

            $finance = new Finance_detailed();
            $res = $finance->where($where)->paginate(10);


            foreach ($res as $val) {
                $val['userid'] = Db::name("userlist")->where('userid', $val['userid'])->find()['username'];
                if ($val['status'] == '') {
                    $val['status'] = Db::name("finance_order")->where('order_id', $val['order_id'])->find()['status'];
                }
                if ($val['product'] == '') {
                    $val['product'] = '其他';
                }

            }
            $this->assign('res', $res);
            return view();
        }
    }

    //流水记录详情
    public function flowreinfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];

        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        }
        if (input('id')) {
            $where = [];
            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right = Db::name('right')->where('pid', 5)->order('num')->select();
            $this->assign('right', $right);
            Session::set('num', 5);

            $where['id'] = input('id');
            $res = Db::name("finance_detailed")->where($where)->find();
            $res['auditor'] = Db::name("admmember")->where("id", $res['auditor'])->find()['username'];
            $res['userid'] = Db::name("userlist")->where("id", $res['userid'])->find()['username'];
            $this->assign('res', $res);
        } else {
            $this->error('非法操作');
        }
        return view();
    }

    //代理级别管理
    public function agent()
    {
        //查询所有的代理级别
        $res=Db::name('agent')->select();
        $this->assign('res',$res);
        return view();
    }

    //编辑代理级别
    public function editAgent()
    {
        $id['id']=input('id');
        if($id['id']){
            //修改操作
            $res=Db::name('agent')->where($id)->find();
            $this->assign('data',$res);
        }
        return view();
    }

    //保存代理等级设置
    public function saveAgent()
    {
        $id['id']=input('post.id');
        $data=input('post.');
        if($id['id']!=""){
            //修改保存
            unset($data['id']);
            $res=Db::name('agent')->where($id)->update($data);
            if($res){
                return json(['msg'=>'编辑成功','code'=>'1']);
            }else{
                return json(['msg'=>'编辑失败','code'=>'2']);
            }
        }else{
            //新增
            $res=Db::name('agent')->insert($data);
            if($res){
                return json(['msg'=>'新增成功','code'=>'1']);
            }else{
                return json(['msg'=>'新增失败','code'=>'2']);
            }
        }



    }

    //删除代理状态
    public function delagent()
    {
        $where['id']=input('post.id');
        $res=Db::name('agent')->delete($where);
        if($res){
            return json(['msg'=>'1']);
        }
    }

    //修改代理状态
    public function statusagent()
    {
        $where['id']=input('post.id');
        $data['status']=input('post.status');
        $res=Db::name('agent')->where($where)->update($data);
        if($res){
            return json(['msg'=>'1','status'=>$data['status']]);
        }
    }


}

<?php
namespace app\index\controller;

use app\index\controller\Common; // 前置操作
use app\index\model\GeeBilling; // 账单表
use app\index\model\GeeInvoice; // 发票表
use app\index\model\GeeInvoiceAddr; // 发票地址表
use app\index\model\GeeInvoiceInfo; // 发票信息表
use app\index\model\GeeUser; // 请求类
use app\index\model\GeeUserEnterprise;
use think\Controller;

// 请求类

class Invoice extends Common
{
    function list() {
        $b = new GeeBilling();
        $i = new GeeInvoice();
        $u = new GeeUser();
        $ii = new GeeInvoiceInfo();
        $isinvoice = $b->where('is_invoice = "1" and user_id = ' . session('_userInfo')['id'])->select();
        $timoney = 0;
        $cimoney = 0;

        foreach ($isinvoice as $k => $v) {
            $timoney += (double) $v['money'];
            if ($v['type'] == "0") {
                $cimoney += (double) $v['money'];
            }
        }
        $ilist = $i->where('user_id = ' . session('_userInfo')['id'])->order('id desc')->paginate(10);
        $hasii = $ii->where('user_id = ' . session('_userInfo')['id'])->find();

        $cimoney = ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) >= 0 ? ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) : 0;

        // dump(empty($hasii));
        // dump(session('_userInfo'));
        $this->assign('timoney', $timoney);
        $this->assign('cimoney', $cimoney);
        $this->assign('hasii', $hasii);
        $this->assign('list', $ilist);
        return $this->fetch('Invoice/list');
    }
    public function applyinvoice()
    {
        $b = new GeeBilling();
        $i = new GeeInvoice();
        $u = new GeeUser();
        $ii = new GeeInvoiceInfo();
        $ue = new GeeUserEnterprise();
        $ia = new GeeInvoiceAddr();
        $isinvoice = $b->where('is_invoice = "1" and user_id = ' . session('_userInfo')['id'])->select();

        $timoney = 0;
        $cimoney = 0;
        foreach ($isinvoice as $k => $v) {
            $timoney += (double) $v['money'];
            if ($v['type'] == "0") {
                $cimoney += (double) $v['money'];
            }
        }
        $cimoney = ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) >= 0 ? ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) : 0;

        $cinfo = $ue->where("user_id = " . session('_userInfo')['id'])->find();
        $iinfo = $ii->where("user_id = " . session('_userInfo')['id'])->find();
        $addrlist = $ia->where('user_id = ' . session('_userInfo')['id'])->order('id desc')->select();
        $defualtaddrlist = $ia->where('user_id = ' . session('_userInfo')['id'] . ' and is_defualt = 1')->order('id desc')->find();
        $hascount = $ia->where('user_id = ' . session('_userInfo')['id'])->count();

        $this->assign('count', $hascount);
        $this->assign('addrlist', $addrlist);
        $this->assign('daddr', $defualtaddrlist);
        $this->assign('cinfo', $cinfo);
        $this->assign('iinfo', $iinfo);
        $this->assign('timoney', $timoney);
        $this->assign('cimoney', $cimoney);
        return $this->fetch('Invoice/applyinvoice');
    }
    public function subapplyinvoice()
    {
        $b = new GeeBilling();
        $u = new GeeUser();
        $i = new GeeInvoice();
        $ii = new GeeInvoiceInfo();
        $ia = new GeeInvoiceAddr();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        $isinvoice = $b->where('is_invoice = "1" and user_id = ' . session('_userInfo')['id'])->select();
        $timoney = 0;
        $cimoney = 0;

        foreach ($isinvoice as $k => $v) {
            $timoney += (double) $v['money'];
            if ($v['type'] == "0") {
                $cimoney += (double) $v['money'];
            }
        }
        //可开票金额 = 订单所有消费金额 - 已开票金额 - 开票冻结金额
        $cimoney = ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) >= 0 ? ((double) $cimoney - (double) session('_userInfo')['invoice_money'] - (double) session('_userInfo')['free_money']) : 0;
        // dump($p['money']);
        // return;
        if (!isset($p['money']) || empty($p['money']) || $p['money'] < 500 || $p['money'] > $cimoney) {
            $ret['status'] = 422;
            $ret['msg'] = '本次申请金额不符合规格!';
            return json_encode($ret);
        }
        if (!isset($p['title']) || empty($p['title'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入发票抬头!';
            return json_encode($ret);
        }
        //假如为企业类发票
        if ($p['n_type'] == 1) {
            if (!isset($p['taxpayerno']) || empty($p['taxpayerno'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入纳税人识别号!';
                return json_encode($ret);
            }
            if (!isset($p['bank']) || empty($p['bank'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入开户银行名称!';
                return json_encode($ret);
            }
            if (!isset($p['bankuser']) || empty($p['bankuser'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入开户帐号!';
                return json_encode($ret);
            }
            if (!isset($p['address']) || empty($p['address'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入地址!';
                return json_encode($ret);
            }
            if (!isset($p['tel']) || empty($p['tel'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入电话!';
                return json_encode($ret);
            }
        }
        if (!isset($p['addressid']) || empty($p['addressid'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请选择收取地址!';
            return json_encode($ret);
        }
        $addrinfo = $ia->where('id = ' . $p['addressid'])->find();
        // dump($addrinfo);
        $subinfo = [
            'number' => '',
            'title' => $p['title'],
            'money' => $p['money'],
            'content' => '',
            'type' => $p['type'],
            'n_type' => $p['n_type'],
            'status' => '0',
            'express' => '',
            'remark' => '',
            'addr_name' => $addrinfo['name'],
            'addr_region' => $addrinfo['region'],
            'addr_address' => $addrinfo['address'],
            'addr_code' => $addrinfo['code'],
            'addr_tel' => $addrinfo['tel'],
            'user_id' => session('_userInfo')['id'],
        ];
        if ($p['n_type'] == 1) {
            $subinfo['taxpayerno'] = $p['taxpayerno'];
            $subinfo['bank'] = $p['bank'];
            $subinfo['bankuser'] = $p['bankuser'];
            $subinfo['address'] = $p['address'];
            $subinfo['tel'] = $p['tel'];
        }

        $res = $i->save($subinfo);

        if ($res) {
            $u->where('id = ' . session('_userInfo')['id'])->update(['free_money' => ((double) session('_userInfo')['free_money'] + (double) $p['money'])]);
            return json_encode($ret);
        } else {
            $ret['status'] = 422;
            $ret['msg'] = '网络异常!请稍后再试';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    //取消申请
    public function cancelInvoice()
    {
        $u = new GeeUser();
        $i = new GeeInvoice();
        $ii = new GeeInvoiceInfo();
        $ia = new GeeInvoiceAddr();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        $money = $i->where('id = ' . $p['id'])->find()['money'];
        $res = $i->where('id = ' . $p['id'])->update(['status' => '2']);
        if ($res) {
            $u->where('id = ' . session('_userInfo')['id'])->update(['free_money' => (double) session('_userInfo')['free_money'] - (double) $money]);
            return json_encode($ret);
        } else {
            $ret['status'] = 422;
            $ret['msg'] = '网络异常!请稍后再试';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    public function template()
    {
        $ii = new GeeInvoiceInfo();
        $u = new GeeUserEnterprise();
        $cinfo = $u->where("user_id = " . session('_userInfo')['id'])->find();
        $iinfo = $ii->where("user_id = " . session('_userInfo')['id'])->find();
        $this->assign('cinfo', $cinfo);
        $this->assign('iinfo', $iinfo);
        return $this->fetch('Invoice/template');
    }
    public function subtemp()
    {
        $ii = new GeeInvoiceInfo();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        if (!isset($p['title']) || empty($p['title'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入发票抬头!';
            return json_encode($ret);
        }
        //假如为企业类发票
        if ($p['n_type'] == 1) {
            if (!isset($p['taxpayerno']) || empty($p['taxpayerno'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入纳税人识别号!';
                return json_encode($ret);
            }
            if (!isset($p['bank']) || empty($p['bank'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入开户银行名称!';
                return json_encode($ret);
            }
            if (!isset($p['bankuser']) || empty($p['bankuser'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入开户帐号!';
                return json_encode($ret);
            }
            if (!isset($p['address']) || empty($p['address'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入地址!';
                return json_encode($ret);
            }
            if (!isset($p['tel']) || empty($p['tel'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入电话!';
                return json_encode($ret);
            }
        }
        $saves = [
            'type' => $p['type'],
            'n_type' => $p['n_type'],
            'title' => $p['title'],
            'taxpayerno' => $p['taxpayerno'],
            'bank' => $p['bank'],
            'bankuser' => $p['bankuser'],
            'address' => $p['address'],
            'tel' => $p['tel'],
            'user_id' => session('_userInfo')['id'],
        ];
        $iiinfo = $ii->where('user_id = ' . session('_userInfo')['id'])->find();
        if (!empty($iiinfo)) {
            $ires = $ii->where('user_id = ' . session('_userInfo')['id'])->update($saves);
        } else {
            $ires = $ii->save($saves);
        }
        if ($ires) {
            return json_encode($ret);
        } else {
            $ret['status'] = 422;
            $ret['msg'] = '网络异常!请稍后再试';
            return json_encode($ret);
        }
    }
    public function addresslist()
    {
        $ia = new GeeInvoiceAddr();
        $list = $ia->where('user_id = ' . session('_userInfo')['id'])->order('id desc')->select();
        $hascount = $ia->where('user_id = ' . session('_userInfo')['id'])->count();
        $this->assign('list', $list);
        $this->assign('count', $hascount);
        return $this->fetch('Invoice/addresslist');
    }
    public function subaddress()
    {

        $ia = new GeeInvoiceAddr();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        if (!isset($p['id']) || empty($p['id'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请传入地址ID!';
            return json_encode($ret);
        }
        if (!isset($p['name']) || empty($p['name'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入收取人姓名!';
            return json_encode($ret);
        }
        if (!isset($p['address']) || empty($p['address'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入街道地址!';
            return json_encode($ret);
        }
        if (!isset($p['tel']) || empty($p['tel'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入联系电话!';
            return json_encode($ret);
        }
        if ($p['id']) {
            $iainfo = $ia->where('id = ' . $p['id'])->find();
        } else {
            $iainfo = false;
        }
        $saves = [
            'name' => $p['name'],
            'region' => $p['region'],
            'address' => $p['address'],
            'code' => $p['code'],
            'tel' => $p['tel'],
            'is_defualt' => $p['is_defualt'],
            'user_id' => session('_userInfo')['id'],
        ];
        //假如本条为默认地址则将所有之前数据转为普通地址
        if ($p['is_defualt'] == 1) {
            $ia->where('user_id = ' . session('_userInfo')['id'])->update(['is_defualt' => 0]);
        }
        if ($iainfo) {
            //修改地址信息
            $iares = $ia->where('id = ' . $p['id'])->update($saves);
        } else {
            //添加地址信息
            $count = $ia->where('user_id = ' . session('_userInfo')['id'])->count();
            if ($count < 10) {
                $iares = $ia->save($saves);
            } else {
                $ret['status'] = 422;
                $ret['msg'] = '可添加的地址信息达到上限!';
                return json_encode($ret);
            }
        }
        if ($iares) {
            return json_encode($ret);
        } else {
            $ret['status'] = 422;
            $ret['msg'] = '网络异常!请稍后再试';
            return json_encode($ret);
        }
    }
    public function deladdress()
    {
        $ia = new GeeInvoiceAddr();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        if (!isset($p['id']) || empty($p['id'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请传入地址ID!';
            return json_encode($ret);
        }
        $dres = $ia->where('id = ' . $p['id'] . ' and user_id = ' . session('_userInfo')['id'])->delete();
        if (!$dres) {
            $ret['status'] = 422;
            $ret['msg'] = '未找到该地址信息或您的身份验证已过期!';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
}

<?php
namespace app\index\controller;

use app\index\controller\Common; // 前置操作
use app\index\model\GeeBilling; //订单表
use app\index\model\GeeUser; //用户表
use think\Controller;

// 请求类

class Billing extends Common
{
    public function overview()
    {
      
      // dump($times_sc);
      // dump(date('Y-m-d',time()));
        return $this->fetch('Billing/overview');
    }
    public function consumption()
    {
        return $this->fetch('Billing/consumption');
    }
    public function dealrecord()
    {
      $uinfo = session('_userInfo');
      $o = new GeeBilling();
      $g = $_GET;
      if ($g['start']) {
          $w .= ' and `create_time` >= '.strtotime($g['start']);
      }
      if ($g['end']) {
          $w .= ' and `create_time` <= '.strtotime($g['end']);
      }
      if ($g['type'] && $g['type'] !== '-1') {
          $w .= ' and `type` = "'.$g['type'].'"';
      }
      if ($g['channel'] && $g['channel'] !== '-1') {
          $w .= ' and `channel_type` = "'.$g['channel'].'"';
      }
      $list = $o->where('user_id = ' . $uinfo['id'].' and `status` = "1" ' .$w)->order('id desc')->paginate(10, false, ['query' => request()->param()]);
      $this->assign('list', $list);
      return $this->fetch('Billing/dealrecord');
    }
    public function dealrecorddetails()
    {
        $o = new GeeBilling();
        $info = $o->where('order_number = ' . $_GET['order'])->find();
        $pros = json_decode($info['pro_list'], false);
        $pros = object_toArray($pros);
        $info['prolist'] = $pros;
        $info['proname'] = count($pros) > 1 ? '多产品' : $pros[0]['class'];
        $this->assign('info', $info);
        // dump($info['prolist'][0]);
        return $this->fetch('Billing/dealrecorddetails');
    }
    public function invoice()
    {
        return $this->fetch('Billing/invoice');
    }
    public function order()
    {
        $uinfo = session('_userInfo');
        $o = new GeeBilling();
        $g = $_GET;
        if ($g['start']) {
            $w .= ' and `create_time` >= '.strtotime($g['start']);
        }
        if ($g['end']) {
            $w .= ' and `create_time` <= '.strtotime($g['end']);
        }
        if ($g['type'] && $g['type'] !== '-1') {
            $w .= ' and `type` = '.$g['type'];
        }
        if ($g['channel'] && $g['channel'] !== '-1') {
            $w .= ' and `channel_type` = '.$g['channel'];
        }
        $list = $o->where('user_id = ' . $uinfo['id'] .$w)->order('id desc')->paginate(10, false, ['query' => request()->param()]);
        $this->assign('list', $list);
        return $this->fetch('Billing/order');
    }
    public function orderdetails()
    {
        $o = new GeeBilling();
        $info = $o->where('order_number = ' . $_GET['order'])->find();
        $pros = json_decode($info['pro_list'], false);
        $pros = object_toArray($pros);
        $info['prolist'] = $pros;
        $info['proname'] = count($pros) > 1 ? '多产品' : $pros[0]['class'];
        $this->assign('info', $info);
        // dump($info['prolist'][0]);
        return $this->fetch('Billing/orderdetails');
    }
    public function cancelorder(){
      $uinfo = session('_userInfo');
      $o = new GeeBilling();
      $g = $_GET;
      $o->where('order_number = "'.$g['order'].'"')->update(['status'=>'2']);
    }
    public function delOrder(){
      $uinfo = session('_userInfo');
      $o = new GeeBilling();
      $g = $_GET;
      $oinfo = $o->where('order_number = "'.$g['order'].'"')->find();
      // dump($oinfo);
      // return ;
      if($oinfo['status'] == '2'){
        $o->where('order_number = "'.$g['order'].'"')->delete();
      }
    }
    public function renew()
    {
        return $this->fetch('Billing/renew');
    }
    public function refund()
    {
        return $this->fetch('Billing/refund');
    }
    public function contract()
    {
        return $this->fetch('Billing/contract');
    }
    public function recharge()
    {
        return $this->fetch('Billing/recharge');
    }
    public function rechargeauth()
    {
        $data = $_POST;
        /**
         * 支付必要传参
         */
        $paypost['trade_no'] = date('Ymdhis', time()) . rand(10000, 99999);
        $paypost['total_amount'] = $data['money'];
        $paypost['subject'] = $data['title'];
        $paypost['body'] = $data['cont'];
        // dump($data);
        // dump($paypost);
        alipay($paypost, 'http://localhost:201/api/return_url', 'http://localhost:201/api/notify_url');

        $order = new GeeBilling();
        $u = new GeeUser();
        $orderdata['number'] = vali_name('number', rand_name(8), 8, 'rand_name');
        $orderdata['order_number'] = $paypost['trade_no'];
        $orderdata['pro_list'] = '0';
        $orderdata['user_id'] = session('_userInfo')['id'];
        $orderdata['type'] = '1';
        $orderdata['order_type'] = '0';
        $orderdata['money'] = $data['money'];
        $orderdata['remarks'] = $data['cont'];
        $orderdata['balance'] = (double) session('_userInfo')['balance'] - (double) $data['money'];
        $orderdata['channel_type'] = $data['channel'] != 0 ? '1' : '0';
        $orderdata['order_status'] = '0';
        $orderdata['status'] = '0';
        $orderdata['is_invoice'] = '1';
        $order->save($orderdata);
    }
    public function withdraw()
    {
        return $this->fetch('Billing/withdraw');
    }
}

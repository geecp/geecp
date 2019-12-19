<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeBilling; // 交易表
use app\admin\model\GeeUser; // 用户表
use app\admin\model\GeeInvoice; // 发票表

class Finance extends Common
{
    public function index()
    {
        $business = new GeeBilling();
        $list = $business->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch('Finance/index');
    }
    public function details()
    {
        $business = new GeeBilling();
        $list = $business->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch('Finance/details');
    }
    public function recharge()
    {
        $business = new GeeBilling();
        $list = $business->where('type="1"')->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch('Finance/recharge');
    }
    public function order()
    {
        $business = new GeeBilling();
        $list = $business->where('pro_list <> "0"')->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch('Finance/order');
    }
    public function invoice()
    {
        $invoice = new GeeInvoice();
        $list = $invoice->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch('Finance/invoice');
    }
    //编辑发票信息
    public function editinvoice(){
      $user = new GeeUser();
      $log = new GeeLog();
      $i = new GeeInvoice();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      
      if (!isset($data['number']) || empty($data['number'])) {
          $ret['status'] = 422;
          $ret['msg'] = '请输入发票号!';
          return json_encode($ret);
      }
      if (!isset($data['express']) || empty($data['express'])) {
          $ret['status'] = 422;
          $ret['msg'] = '请输入快递单号!';
          return json_encode($ret);
      }
      $res = $i->where('id = '.$data['id'])->update([
        'number' => $data['number'],
        'express' => $data['express'],
      ]);
      if($res){
        return json_encode($ret);
      } else {
        $ret['status'] = 422;
        $ret['msg'] = '网络异常!请稍后再试';
        return json_encode($ret);
      }
    }
    //通过发票申请
    public function passinvoice(){
    	$user = new GeeUser();
      $log = new GeeLog();
      $i = new GeeInvoice();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      
      if (!isset($data['number']) || empty($data['number'])) {
          $ret['status'] = 422;
          $ret['msg'] = '请输入发票号!';
          return json_encode($ret);
      }
      if (!isset($data['express']) || empty($data['express'])) {
          $ret['status'] = 422;
          $ret['msg'] = '请输入快递单号!';
          return json_encode($ret);
      }
      $iinfo = $i->where('id = '.$data['id'])->find();
      $uinfo = $user->where('id = '.$iinfo['user_id'])->find();
      // dump($uinfo);
      // dump($iinfo);
      // return;
      $res = $i->where('id = '.$data['id'])->update([
        'status' => '1',
        'number' => $data['number'],
        'express' => $data['express'],
      ]);
      // dump($uinfo);
      // return;
      if($res){
        //通过后  用户开票 冻结金额减少本次开票金额   已开票金额累加本次开票金额
        $user->where('id = '.$iinfo['user_id'])->update([
          'invoice_money' => (double)$uinfo['invoice_money'] + (double)$iinfo['money'],
          'free_money' => (double)$uinfo['free_money'] - (double)$iinfo['money']
        ]);
        return json_encode($ret);
      } else {
        $ret['status'] = 422;
        $ret['msg'] = '网络异常!请稍后再试';
        return json_encode($ret);
      }
    }
    //拒绝发票申请
    public function nopassinvoice(){
    	$user = new GeeUser();
      $log = new GeeLog();
      $i = new GeeInvoice();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      
      $iinfo = $i->where('id = '.$data['id'])->find();
      $uinfo = $user->where('id = '.$iinfo['user_id'])->find();
      $res = $i->where('id = '.$data['id'])->update([
        'status' => '3',
      ]);
      if($res){
        //拒绝后  用户开票 冻结金额减少本次开票金额
        $user->where('id = '.$iinfo['user_id'])->update([
          'free_money' => (double)$uinfo['free_money'] - (double)$iinfo['money']
        ]);
        return json_encode($ret);
      } else {
        $ret['status'] = 422;
        $ret['msg'] = '网络异常!请稍后再试';
        return json_encode($ret);
      }
    }
}

<?php
namespace app\index\controller;

use app\index\controller\Common; // 前置操作
use app\index\model\GeeBilling; //用户表
// use app\index\model\GeeDeal; //收支明细表
use app\index\model\GeeOrder; //交易记录表
use app\index\model\GeeProConfig; //订单表
use app\index\model\GeeUser; //订单表
use think\Controller;

// 请求类

class Api extends Common
{
    /**
     * 支付宝同步接口
     */
    public function return_url()
    {
        $this->redirect('Index/index');
    }
    /**
     * 支付宝异步接口
     */
    public function notify_url()
    {
        // $deal = new GeeDeal();
        $u = new GeeUser();
        $o = new GeeOrder();
        $b = new GeeBilling();
        $pc = new GeeProConfig();

        $post = input();
        // $deal->where('num = ' . $post['out_trade_no'])->update(['dealnum' => $post['trade_no']]);
        if ($post['trade_status'] == "TRADE_SUCCESS") {
            //操作数据库 修改状态
            $dinfo = $b->where('order_number = ' . $post['out_trade_no'])->find();
            if ($dinfo['title'] == '账户充值') {
                $uinfo = $u->where('id = ' . $dinfo['user_id'])->find();
                $u->where('id = ' . $dinfo['user_id'])->update(['balance' => ((double) $uinfo['balance'] + (double) $dinfo['money'])]);
            } else if ($dinfo['title'] == '产品购买') {
                $pcs = $pc->where('order_number = "' . $data['order'] . '"')->find();
                $configs = json_decode($pcs['config'], true);
                if($configs['_create_putData']){
                  //vps 或 插件类购买
                  $plug = new $configs['_create_putData']['plug']();
                  $func = $configs['_create_putData']['class'];
                  $putData = $configs['_create_putData']['data'];
                  if (!$putData['function']) {
                      $putData['function'] = $configs['_create_putData']['function'];
                  }
                  $res = $plug->$func($putData);
                } else {
                  //租用物理服务器
                  $server = new GeeServer();
                  $selfPro = object_toArray($selfPro);
                  foreach($selfPro as $k=>$v){
                    if($v['pro_type'] == 'server') {
                      if($info['order_type'] == 'renew'){
                        $sinfo = $server->where('id',(int)$v['id'])->find();
                        $dt=date('Y-m-d H:i:s',$sinfo['end_time']);
                        $updata = [
                          'server_added' => $v['added'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime($dt."+".(int)$v['years']." month"))),
                        ];
                        if($sinfo['status'] == 1){
                          $updata['status'] = 3;
                        }
                        $server->where('id = '.$v['id'])->update($updata);
                      } else {
                        $sinfo = [
                          'pro_group_id'=> $v['group_id'],
                          'pro_id'=> $v['pro_id'],
                          'server_added'=> $v['added'],
                          'name'=> $v['hostname'],
                          'user_id'=> $v['user_id'],
                          'remake'=> $v['remake'],
                          'username'=> $v['username'],
                          'password'=> $v['password'],
                          'osgroup'=> (int)$v['osgroup'],
                          'ostype'=> (int)$v['ostype'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime("+".(int)$v['years']." month"))),
                          'status'=> '0'
                        ];
                        $server->save($sinfo);
                      }
                    }
                  }
                }
                
            }
            // $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 1]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 1]);
            echo "SUCCESS";
        } else {
            // $deal->where('num = ' . $post['out_trade_no'])->update(['status' => 3]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 0]);
            $this->redirect('billing/dealrecord');
        }
        //写在文本里看一下参数
        $data = json_encode($post);
        file_put_contents("alipaytext.txt", $data);
    }
}

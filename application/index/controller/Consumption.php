<?php
namespace app\index\controller;
use app\index\controller\Common; // 前置操作
use app\index\model\GeeBilling; //订单表
use app\index\model\GeeInvoice; // 发票表
use app\index\model\GeeUser; // 请求类
use app\index\model\GeeUserEnterprise;
use think\Request; // 请求类
use think\Controller;

class Consumption extends Common
{
    public function overview()
    {
      $b = new GeeBilling();
      //默认历史消费趋势
      $times_sc = statistics_time(['start_time'=>date('Y-m-d', strtotime('-30 day')),'end_time'=>date('Y-m-d', strtotime('0 day'))]);
      foreach($times_sc as $k=>$v){
        /* 时间条件 */
        $map =array();
        $map["time"]=array(array('egt',strtotime($v." 00:00:00")),array('ELT',strtotime($v." 23:59:59")));
        /* 当天所消费金额 */
        $binfo = $b->where(array('create_time'=>$map['time']))->where('type = "0" or type = "4"')->where('status = "1"')->where('user_id = '.session('_userInfo')['id'])->select();
        $money = 0;
        foreach($binfo as $key=>$val){
        // dump(str_replace(",","",$val['money']));
        $money += (double)str_replace(",","",$val['money']);
        }
        $historical[$k]['date'] = $v;
        $historical[$k]['value'] = to_double((double)$money);
        $hdate[$k] = $v;
        $hval[$k] = to_double((double)$money);
      }
      // dump(json_encode($hdate));
      $this->assign('historical',$historical);
      $this->assign('hdate',$hdate);
      $this->assign('hval',$hval);
      //消费统计
      // $dtime = ['start_time'=>date('Y-m-d', strtotime('-30 day')),'end_time'=>date('Y-m-d', strtotime('0 day'))];
      $map = [];
      $map["time"]=[['egt',strtotime(date('Y-m-d', strtotime('-30 day'))." 00:00:00")],['ELT',strtotime(date('Y-m-d', strtotime('0 day'))." 23:59:59")]];
      $blist = $b->where('type = "0" or type = "4"')->where('status = "1"')->where(['create_time'=>$map['time']])->where('user_id = '.session('_userInfo')['id'])->select();
      $allblist = [];
      foreach($blist as $k=>$v){
        $prolist = json_decode($v['pro_list'],true);
        // dump($prolist);
        foreach($prolist as $key=>$val){
          array_push($allblist,[
            'name'=>$val['class'],
            'value'=>str_replace(",","",$val['price']),
          ]);
        }
      }
      // dump($allblist);
      $tree = Array2Tree($allblist,'name');
      $new_arr = [];
      foreach ($allblist as $key=>$val){
          $new_arr[$val['name']][] = $val;
      }
      // dump($new_arr);
      $proTree = [];
      foreach($tree as $k=>$v){
        $price = 0;
        foreach($v as $key=>$val){
          // dump(str_replace(",","",$val['price']));
          $price += (double)$val['value'];
        }
        array_push($proTree,[
          'name'=>$k,
          'value'=>$price
        ]);
      }
      // dump($tree);
      // dump($proTree);
      $allPrice = 0;
      foreach($proTree as $k=>$v){
        $allPrice += (double)$v['value'];
      }
      $this->assign('proTree',$proTree);
      $this->assign('allPrice',$allPrice);
      return $this->fetch('Consumption/overview');
    }
    public function gethistory(){
      $data = $_POST;
      $ret = [
          'status' => 200,
          'msg' => '操作成功',
          'data' => '',
      ];
        $b = new GeeBilling();
      if($data['ctype'] == 'pie'){
        $ret['data'] = [];
        $ret['data']['allPrice'] = 0;
        $ret['data']['data'] = [];
        $map = [];
        $map["time"]=[['egt',strtotime($data['start']." 00:00:00")],['ELT',strtotime($data['end']." 23:59:59")]];
        $blist = $b->where('type = "0" or type = "4"')->where('status = "1"')->where(['create_time'=>$map['time']])->where('user_id = '.session('_userInfo')['id'])->select();
        $allblist = [];
        foreach($blist as $k=>$v){
          $prolist = json_decode($v['pro_list'],true);
          // dump($prolist);
          foreach($prolist as $key=>$val){
            array_push($allblist,[
              'name'=>$val['class'],
              'value'=>str_replace(",","",$val['price']),
            ]);
          }
        }
        // dump($allblist);
        $tree = Array2Tree($allblist,'name');
        $new_arr = [];
        foreach ($allblist as $key=>$val){
            $new_arr[$val['name']][] = $val;
        }
        // dump($new_arr);
        $proTree = [];
        foreach($tree as $k=>$v){
          $price = 0;
          foreach($v as $key=>$val){
            // dump(str_replace(",","",$val['price']));
            $price += (double)$val['value'];
          }
          array_push($proTree,[
            'name'=>$k,
            'value'=>$price
          ]);
        }
        // dump($tree);
        // dump($proTree);
        $allPrice = 0;
        foreach($proTree as $k=>$v){
          $allPrice += (double)$v['value'];
        }
        // $this->assign('proTree',$proTree);
        // $this->assign('allPrice',$allPrice);
        
        $ret['data']['allPrice'] = $allPrice;
        $ret['data']['data'] = $proTree;
      } else {
        $times_sc = statistics_time(['start_time'=>$data['start'],'end_time'=>$data['end']]);
        $ret['data'] = [];
        $ret['data']['date'] = [];
        $ret['data']['data'] = [];
        foreach($times_sc as $k=>$v){
          /* 时间条件 */
          $map =array();
          $map["time"]=array(array('egt',strtotime($v." 00:00:00")),array('ELT',strtotime($v." 23:59:59")));
          /* 当天所消费金额 */
          $binfo = $b->where(array('create_time'=>$map['time']))->where('type = "0" or type = "4"')->where('status = "1"')->where('user_id = '.session('_userInfo')['id'])->select();
          $money = 0;
          foreach($binfo as $key=>$val){
            $money += (double)str_replace(",","",$val['money']);
          }
          $ret['data']['date'][$k] = $v;
          $ret['data']['data'][$k] = to_double((double)$money);
        }
      }
      return json_encode($ret);
    }
    public function list()
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
      // if ($g['type'] && $g['type'] !== '-1') {
      //     $w .= ' and `type` = "'.$g['type'].'"';
      // }
      // if ($g['channel'] && $g['channel'] !== '-1') {
      //     $w .= ' and `channel_type` = "'.$g['channel'].'"';
      // }
      $list = $o->where('type = "0" or type = "4"')->where('user_id = ' . $uinfo['id'].' and `status` = "1" ' .$w)->order('id desc')->paginate(10, false, ['query' => request()->param()]);
      $this->assign('list', $list);
      return $this->fetch('Consumption/list');
    }
    public function resource()
    {
      return $this->fetch('Consumption/resource');
    }
}

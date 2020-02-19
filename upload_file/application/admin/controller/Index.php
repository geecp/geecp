<?php
namespace app\admin\controller;

use app\admin\controller\Common; // 请求类
use app\index\model\GeeBilling; // 前置操作
use app\index\model\GeeUser; // 用户表
use app\index\model\GeeDomain; // 插件表
use app\index\model\GeeServer; //财务表
use app\index\model\GeeTicket; //域名表
use app\index\model\GeeVps; //物理服务器表
use think\Controller; //工单表
//VPS表

class Index extends Common
{
    public function index()
    {
        if (!isset($_COOKIE['token']) && !empty($_COOKIE['token']) && jwt_decode($_COOKIE['token'])) {
            return $this->redirect('admin/Login/index');
        }
        //用户数据
        $u = new GeeUser();
        $reg = $u->count();
        $puser = $u->where('type = "0"')->count();
        $euser = $u->where('type = "1"')->count();
        $this->assign('reg', $reg);
        $this->assign('puser', $puser);
        $this->assign('euser', $euser);
        //订单数据
        $o = new GeeBilling();
        $all = $o->count(); //总订单
        $now = $o->where('create_time >= '.strtotime(date('Y-m-d') . " 00:00:00").' and create_time <= '.strtotime(date('Y-m-d') . " 23:59:59"))->count(); //今日订单
        $pay = $o->where('`status` = "1" ')->count(); //已付款订单
        //总交易流水
        $paymoney = $o->where('`status` = "1" ')->select();
        $pmoney = 0;
        foreach($paymoney as $k=>$v){
          $pmoney += $v['money'];
        }
        $this->assign('all', $all);
        $this->assign('now', $now);
        $this->assign('pay', $pay);
        $this->assign('pmoney', $pmoney);
        //工单数据
        $t = new GeeTicket();
        $ft = $t->where('status = 0')->count();
        $wt = $t->where('status = 2')->count();
        $allt = $t->count();
        $this->assign('ft', $ft);
        $this->assign('wt', $wt);
        $this->assign('allt', $allt);
        //注册用户统计
        //默认注册用户
        $times_sc = statistics_time(['start_time' => date('Y-m-d', strtotime('-30 day')), 'end_time' => date('Y-m-d', strtotime('0 day'))]);
        foreach ($times_sc as $k => $v) {
            /* 时间条件 */
            $map = array();
            $map["time"] = array(array('egt', strtotime($v . " 00:00:00")), array('ELT', strtotime($v . " 23:59:59")));
            /* 当天注册用户 */
            $ucount = $u->where(array('create_time' => $map['time']))->where('type = "0" or type = "4"')->where('status = "1"')->count();
            $historical[$k]['date'] = $v;
            $historical[$k]['value'] = $ucount?$ucount:0;
            $hdate[$k] = $v;
            $hval[$k] = $ucount?$ucount:0;
        }
        $this->assign('uhistorical', $historical);
        $this->assign('uhdate', $hdate);
        $this->assign('uhval', $hval);
        //消费统计
        $b = new GeeBilling();
        //默认历史消费趋势
        $times_sc = statistics_time(['start_time' => date('Y-m-d', strtotime('-30 day')), 'end_time' => date('Y-m-d', strtotime('0 day'))]);
        foreach ($times_sc as $k => $v) {
            /* 时间条件 */
            $map = array();
            $map["time"] = array(array('egt', strtotime($v . " 00:00:00")), array('ELT', strtotime($v . " 23:59:59")));
            /* 当天所消费金额 */
            $binfo = $b->where(array('create_time' => $map['time']))->where('type = "0" or type = "4"')->where('status = "1"')->select();
            $money = 0;
            foreach ($binfo as $key => $val) {
                $money += (double) str_replace(",", "", $val['money']);
            }
            $historical[$k]['date'] = $v;
            $historical[$k]['value'] = to_double((double) $money);
            $hdate[$k] = $v;
            $hval[$k] = to_double((double) $money);
        }
        $this->assign('historical', $historical);
        $this->assign('hdate', $hdate);
        $this->assign('hval', $hval);
        //域名统计
        $d = new GeeDomain();
        $dcount = $d->count();
        $dvcount = $d->where('r_state = "run"')->count();
        $decount = $d->where('end_time <= ' . (time() - 60 * 60 * 24 * 30))->count();
        $endcount = $d->where('end_time <= ' . time())->count();
        $this->assign('dcount', $dcount);
        $this->assign('dvcount', $dvcount);
        $this->assign('decount', $decount);
        $this->assign('endcount', $endcount);
        //VPS主机
        $vps = new GeeVps();
        $vcount = $vps->count();
        $vucount = $vps->where('status = "正常"')->count();
        $vecount = $vps->where('end_time <= ' . (time() - 60 * 60 * 24 * 30))->count();
        $vendcount = $vps->where('end_time <= ' . time())->count();
        $this->assign('vcount', $vcount);
        $this->assign('vucount', $vucount);
        $this->assign('vecount', $vecount);
        $this->assign('vendcount', $vendcount);

        //物理服务器
        $s = new GeeServer();
        $scount = $s->count();
        $sucount = $s->where('status = 3')->count();
        $secount = $s->where('end_time <= ' . (time() - 60 * 60 * 24 * 30))->count();
        $sendcount = $s->where('end_time <= ' . time())->count();
        $this->assign('scount', $scount);
        $this->assign('sucount', $sucount);
        $this->assign('secount', $secount);
        $this->assign('sendcount', $sendcount);
        return $this->fetch('Index/index');
    }
}

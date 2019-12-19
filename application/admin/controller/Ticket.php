<?php
namespace app\admin\controller;

use app\admin\controller\Common; // 前置操作
use app\admin\model\GeeLog; // 请求类
use app\admin\model\GeeTicket;
use app\admin\model\GeeTicketDetails; // 日志表
use think\Controller; // 工单表
// 工单详细表

class Ticket extends Common
{
    function list() {
        $ticket = new GeeTicket();
        $list = $ticket->order('id desc')->select();
        $this->assign('list', $list);
        return $this->fetch('Ticket/list');
    }
    public function details()
    {
      $ticket = new GeeTicket();
      $ticketdetails = new GeeTicketDetails();
      $ticketInfo = $ticket->where('id = ' . $_GET['id'])->find();
      $logs = $ticketdetails->where('tid = '.$_GET['id'])->select();
      $this->assign('info', $ticketInfo);
      $this->assign('log', $logs);
        return $this->fetch('Ticket/details');
    }
    public function group()
    {
        $ticket = new GeeTicket();
        $list = $ticket->order('id desc')->select();
        $this->assign('list', $list);
        return $this->fetch('Ticket/group');
    }
    public function join()
    {
        $ticket = new GeeTicket();
        $ainfo = session('_adminInfo');
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ticketres = $ticket->where('id = ' . $_GET['id'])->update(['replierid' => $ainfo['user_id'],'status'=> 1]);
        if (!$ticketres) {
            $ret['status'] = 422;
            $ret['msg'] = '网络请求失败!请稍后再试!';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    
    public function reply()
    {
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $uinfo = session('_adminInfo');
        // dump($uinfo);
        $data = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        
        if (!isset($data['content']) || empty($data['content'])) {
            $ret['status'] = 422;
            $ret['msg'] = '回复内容不能为空！';
            return json_encode($ret);
        }
        $ticketInfo = $ticket->where('id = ' . $data['tid'])->find();
        $data['title'] = $ticketInfo['title'];
        $data['fromid'] = $uinfo['user_id'];
        $data['replierid'] = $ticketInfo['fromid'];

        $ticketres = $ticketdetails->save($data);
        if (!$ticketres) {
            $ret['status'] = 422;
            $ret['msg'] = '网络请求失败!请稍后再试!';
            return json_encode($ret);
        }
        $ticket->where('id = ' . $data['tid'])->update(['status'=> 3]);

        return json_encode($ret);
    }
}

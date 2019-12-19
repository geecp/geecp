<?php
namespace app\index\controller;

use app\admin\model\GeeLog; // 前置操作
use app\index\controller\Common; // 请求类
use app\index\model\GeeTicket;
use app\index\model\GeeTicketDetails; // 日志表
use think\Controller;

// 工单表
// 工单详细表

class Ticket extends Common
{
    function list() {
        $uinfo = session('_userInfo');
        $list = new GeeTicket();
        $ticketList = $list->where('fromid = ' . $uinfo['id'])->order('id desc')->paginate(10);
        // dump($ticketList);
        $this->assign('list', $ticketList);
        return $this->fetch('Ticket/list');
    }
    public function add()
    {
        if ($_GET['id']) {
            $id = $_GET['id'];
            $ticket = new GeeTicket();
            $ticketInfo = $ticket->where('id = ' . $id)->find();
            $this->assign('info', $ticketInfo);
        }
        return $this->fetch('Ticket/add');
    }
    public function addauth()
    {
        $ticket = new GeeTicket();
        $log = new GeeLog();
        $uinfo = session('_userInfo');
        $data = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        if (!isset($data['type']) || empty($data['type'])) {
            $ret['status'] = 422;
            $ret['msg'] = '工单类型提交有误！';
            return json_encode($ret);
        }
        if (!isset($data['title']) || empty($data['title'])) {
            $ret['status'] = 422;
            $ret['msg'] = '工单标题提交有误！';
            return json_encode($ret);
        }
        if (!isset($data['content']) || empty($data['content'])) {
            $ret['status'] = 422;
            $ret['msg'] = '工单描述提交有误！';
            return json_encode($ret);
        }
        $data['fromid'] = $uinfo['id'];
        $data['num'] = date('Ymdhis', time()) . rand(10000, 99999);
        $ticketres = $ticket->save($data);
        if (!$ticketres) {
            $ret['status'] = 422;
            $ret['msg'] = '网络请求失败!请稍后再试!';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    public function details()
    {
        $uinfo = session('_userInfo');
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $ticketInfo = $ticket->where('id = ' . $_GET['id'])->find();
        $logs = $ticketdetails->where('tid = '.$_GET['id'])->select();
        $this->assign('info', $ticketInfo);
        $this->assign('log', $logs);
        return $this->fetch('Ticket/details');
    }
    public function reply()
    {
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $uinfo = session('_userInfo');
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
        $data['fromid'] = $uinfo['id'];
        $data['replierid'] = $ticketInfo['replierid'];

        $ticketres = $ticketdetails->save($data);
        if (!$ticketres) {
            $ret['status'] = 422;
            $ret['msg'] = '网络请求失败!请稍后再试!';
            return json_encode($ret);
        }
        $ticket->where('id = ' . $data['tid'])->update(['status'=> 2]);
        return json_encode($ret);
    }
    public function confirm()
    {
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $data = $_GET;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ticket->where('id = ' . $data['id'])->update(['status'=> 5]);
        return json_encode($ret);
    }
    public function cancel()
    {
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $data = $_GET;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ticket->where('id = ' . $data['id'])->update(['status'=> 4]);
        return json_encode($ret);
    }
    public function del()
    {
        $ticket = new GeeTicket();
        $ticketdetails = new GeeTicketDetails();
        $data = $_GET;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $ticket->where('id = ' . $data['id'])->delete();
        return json_encode($ret);
    }
}

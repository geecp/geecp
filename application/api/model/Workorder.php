<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 11:01
 */
namespace app\api\model;
use think\Model;
class Workorder extends Model
{
    //工单列表
    public static function workOrder()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $display=input('post.display/s','','htmlspecialchars');
        $current=input('post.current/s','','htmlspecialchars');
        $type=input('post.type/s','','htmlspecialchars');
        $offerset=$display*($current - 1);
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $whe['coustom']=$auth->id;
            if($type){
                $whe['status']=$type;
            }
            $ordernum=Workorder::where($whe)->count();
            $type=Workorder::where($whe)->limit($offerset,$display)->field('workorderid,questiontitle,type,describe,status,time')->order('id desc')->select();
            $msg['data']=$type;
            $msg['ordernum']=$ordernum;
        }else{
            $msg='';
        }
        return $msg;
    }

    //工单详情
    public static function worderInfo()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $workid=input('post.workorderid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $res=Workorder::getByWorkorderid($workid);
            $whe=[
                'workorderid'=>$workid,
                'userid'=>$auth->id
            ];
            $chat=Chatlist::where($whe)->select();
            $result->orderinfo=$res;
            $result->chatlist=$chat;
        }else{
            $result='';
        }
        return $result;
    }

    //创建工单
    public static function createWorder()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $data=input('post.','','htmlspecialchars');
        $result=new \stdClass();
        $auth=Userlist::getByUserid($userid);
        if($auth){
            unset($data['uid']);
            switch ($data['describe']){
                case 1:
                    $data['describe'] ='投诉与建议';
                    break;
                case 2:
                    $data['describe']='普通报错';
                    break;
                case 3:
                    $data['describe']='系统故障';
                    break;
                case 4:
                    $data['describe']='系统故障恢复';
            }
            $data['status']=1;
            $data['coustom']=$auth->id;
            $data['time']=date('Y-m-d H:i:s',time());
            $data['workorderid']=date('YmdHis',time()).rand(10000,99999);
            $worker=Admmember::where('adm_group=6||adm_group=13')->field('id')->select();
            $rand=rand(0,count($worker)-1);
            $data['workid']=$worker[$rand]['id'];
            $work=Workorder::create($data);
            $data['id']=$work->id;
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }

    //更改工单状态
    public static function editWorderStatus()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $wid=input('post.id/s','','htmlspecialchars');
        $wstatus=input('post.status/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $worder=Workorder::getByWorkorderid($wid);
            $worder->status=$wstatus;
            $result=$worder->save();
        }else{
            $result='';
        }
        return $result;
    }

    //工单聊天
    public static function saveChalist()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $data=input('post.','','htmlspecialchars');
        if($auth){
            unset($data['uid']);
            $data['reply_time']=date('Y-m-d H:i:s',time());
            $data['userid']=$auth->id;
            $data['status']='1';
            $res=Chatlist::create($data);
            $dat['id']=$res->id;
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }
}
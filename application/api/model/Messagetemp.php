<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 15:46
 */
namespace app\api\model;
use think\Model;
class Messagetemp extends Model
{
    public static function messageView()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $display=input('post.display/s','','htmlspecialchars');
        $current=input('post.current/s','','htmlspecialchars');
        $type=input('post.temp/s','','htmlspecialchars');
        $offerset=$display*($current - 1);
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $whe['sendee']=$auth->id;
            if(!empty($type)){
                $whe['temp']=$type;
            }
            $msgnum=Messagetemp::where($whe)->count();
            $message=Messagetemp::where($whe)->limit($offerset,$display)->select();
            $result->msgnum=$msgnum;
            $result->data=$message;
        }else{
            $result='';
        }
        return $result;
    }

    public static function readMsg()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $id=input('post.msgid/s','','htmlspecialchars');
            $msg=Messagetemp::get($id);
            $msg->is_read=1;
            $result=$msg->save();
        }else{
            $result='';
        }
        return $result;
    }


}
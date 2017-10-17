<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 17:48
 */
namespace app\api\model;
use app\admin\model\Sms;
use think\Model;
class SmsAppname extends Model
{

    public static function appList()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $where['userid']=$auth->id;
            $res=SmsAppname::where($where)->order('appid desc')->select();
            $result->appcount=count($res);
            $result->data=$res;
        }else{
            $result='';
        }
        return $result;
    }


    public static function createApp()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $data['userid']=$auth->id;
            $data['appname']=input('post.appname/s','','htmlspecialchars');
            $data['smsname']=input('post.smsname/s','','htmlspecialchars');
            $data['num']=input('post.num/s','','htmlspecialchars');
            $data['create_time']=date('Y-m-d H:i:s',time());
            $data['status']=1;
            $res=SmsAppname::create($data);
            $data['appid']=$res->appid;
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }

    public static function delSmsApp()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $appid=input('post.appid/s','','htmlspecialchars');
            $smsapp=SmsAppname::get($appid);
            $data=$smsapp->delete();
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }


}
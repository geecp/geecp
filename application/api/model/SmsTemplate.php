<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 22:13
 */
namespace app\api\model;
use think\Model;
class SmsTemplate extends Model
{
    public static function tempList()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $where['userid']=$auth->id;
            $res=SmsTemplate::where($where)->order('tempid desc')->select();
            $result->tempcount=count($res);
            $result->data=$res;
        }else{
            $result='';
        }
        return $result;
    }

    public static function createTemplate()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $data['userid']=$auth->id;
            $data['type']=input('post.type/s','','htmlspecialchars');
            $data['content']=input('post.content/s','','htmlspecialchars');
            $data['create_time']=date('Y-m-d H:i:s',time());
            $data['status']=1;
            $res=SmsTemplate::create($data);
            $data['tempid']=$res->tempid;
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }

    public static function delSmsTemp()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $tempid=input('post.tempid/s','','htmlspecialchars');
            $smstemp=SmsTemplate::get($tempid);
            $data=$smstemp->delete();
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }
}
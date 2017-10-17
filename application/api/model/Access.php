<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27
 * Time: 16:35
 */
namespace app\api\model;
use think\Model;
class Access extends Model
{
    public static function access()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $res_count=Access::where('userid',$auth->id)->count();
            $res_content=Access::where('userid',$auth->id)->order('id desc')->select();
            $result->acenum=$res_count;
            $result->data=$res_content;
        }else{
            $result='';
        }
        return $result;
    }

    public static function createAccess()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            //查询当前用户的所有消息
            $data['userid']=$auth->id;
            $data['keyid']= md5(mt_rand(10000,99999999999999));
            $data['key'] = md5(mt_rand(10000,99999999999999));
            $data['create_time']=date('Y-m-d H:i:s',time());
            $data['note'] = '';
            $access=Access::create($data);
            $data['id']=$access->id;
            $result=$data;
        }else{
            $result='';
        }
        return $result;
    }

    public static function saveNote()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $id=input('post.id/s','','htmlspecialchars');
            $note = input('post.note/s','','htmlspecialchars');
            $ace=Access::get($id);
            $ace->note=$note;
            $result=$ace->save();
        }else{
            $result='';
        }
        return $result;
    }


    public static function delAcess()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        $result=new \stdClass();
        if($auth){
            $id=input('post.aceid/s','','htmlspecialchars');
            $ace=Access::get($id);
            $result=$ace->delete();
        }else{
            $result='';
        }
        return $result;
    }
}
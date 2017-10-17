<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 17:29
 */
namespace app\api\model;
use think\Model;
class Questiontype extends Model
{
    public static function WorkorderType()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $que=new Questiontype();
            $res=$que->where('parentid','0')->select();
            foreach ($res as $k=> $re){
                $result=Questiontype::where('parentid',$re['id'])->field('id,name')->select();
                $re->types=$result;
                $res[$k]=$re;
            }
        }else{
            $res='';
        }
        return $res;
    }

}
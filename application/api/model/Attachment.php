<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 15:20
 */
namespace app\api\model;
use think\Model;
class Attachment extends Model
{
    public static function bosInfo()
    {
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $res=Attachment::get(1);
            $res=$res->visible(['bucket','domain','ak','sk'])->toArray();
        }else{
            $res='';
        }
        return $res;
    }
}
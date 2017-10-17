<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 1:01
 */
namespace app\api\model;
use app\api\model\Userlist;
use think\Model;

class Settingsite extends Model
{
    public static function getLogo()
    {
        //接受uid，判断是否真是用户
        $where['userid']=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::get($where);
        if($auth){
            $result['success']='success';
            $set=Settingsite::get(1);
            $result['data']=$set->pic;
        }else{
            $result['success']='false';
        }
        return $result;
    }
}
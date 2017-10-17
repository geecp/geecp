<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 13:58
 */
namespace app\api\model;
use think\Model;
class Product extends Model
{
    protected $resultSetType = 'collection';

    public static function alls()
    {

        //接受uid，判断是否真是用户
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $result['success']='success';
            $res=Product::all();
            $result['data']=array_column($res->toArray(),'name','id');
        }else{
            $result['success']='false';
        }
        return $result;

    }
}
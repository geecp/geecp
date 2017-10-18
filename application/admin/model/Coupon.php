<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/19
 * Time: 17:07
 */
namespace app\admin\model;
use think\Model;
class Coupon extends Model
{

    public function userq()
    {
        return $this->hasOne('userlist','id','userid',['coupon'=>'c','userlist'=>'u']);
    }

    public static function getOver()
    {
        $res=Coupon::with('userq')->order('create_time desc')->paginate(15);
        return $res;
    }


}
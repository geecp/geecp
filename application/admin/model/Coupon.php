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
    public static function getOver()
    {
        $res=Coupon::paginate(15);
        return $res;
    }


}
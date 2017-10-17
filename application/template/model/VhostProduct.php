<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/29
 * Time: 15:37
 */
namespace app\template\model;
use think\Model;
class VhostProduct extends Model
{
    public function vhostPrice()
    {
        return $this->hasMany('Product_price','title','p_id');
    }
    
    public static function getVhost()
    {
        //获取插件状态
        $addons=Addons::where(['status'=>1,'range'=>'vhost'])->find();
        $erji=$addons->toArray()['name'];
        //获取所有路线
        $group=Productgroup::where(['status'=>1,'erji'=>$erji])->select();

        return $group;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/30
 * Time: 14:12
 */
namespace app\admin\model;
use think\Model;
class Addons extends Model
{
    public static function addonsName($data)
    {
        $res=Addons::all(['range'=>$data]);
        return $res;
    }
}

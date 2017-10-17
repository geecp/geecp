<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/27
 * Time: 16:27
 */
namespace app\admin\model;
use think\Model;

class Vhostproduct extends Model
{
    public static function getUserList()
    {
        $res=Userlist::all();
        return $res;
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/8
 * Time: 21:52
 */
namespace app\admin\model;
use think\Model;
class Vps extends Model
{
    public function userq()
    {
        return $this->hasOne('userlist','id','userid',['vps'=>'v','userlist'=>'u']);
    }

    public function pgroup()
    {
        return $this->hasOne('vhostproduct','id','productid',['vps'=>'v','userlist'=>'u']);
    }

    public static function vpsList()
    {
        $res=Vps::with('userq,pgroup')->order('create_time desc')->paginate(15);
        return $res;
    }
}
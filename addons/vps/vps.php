<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/25
 * Time: 17:05
 */
namespace addons\vps;
use think\Addons;
use think\Db;
class vps extends Addons
{
    public function install()
    {

    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function vps($data)
    {
        //查询哪一个分类插件的状态为启用,并调用方法
        $where['range']='vps';
        $where['status']='1';
        $res=Db::name('addons')->where($where)->find();
        if($res){
            $way=$res['name'];
            $path = $way.'/controller/'.$way.'.php';
            include_once $path;
            $result= new $way();
            $function=$way.'_'.$data['function'];
            $code=$result->$function($data['data']);
        }else{
            $code='0';
        }
        return $code;
    }
}
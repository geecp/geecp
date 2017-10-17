<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/10
 * Time: 15:28
 */
namespace addons\vhost;
use think\Addons;
use think\Db;
class vhost extends Addons
{
    public function install()
    {
        // TODO: Implement install() method.
    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function vhost($data)
    {
        //查询哪一个分类插件的状态为启用,并调用方法
        $where['range']='vhost';
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


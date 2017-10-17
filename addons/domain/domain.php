<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/6
 * Time: 16:54
 */
namespace addons\domain;
use think\Addons;
use think\Controller;
use think\Db;
class domain  extends Addons
{
    public function install()
    {
        // TODO: Implement install() method.
    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function domain($data)
    {
        //查询哪一个分类插件的状态为启用,并调用方法
        $where['range']='domain';
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
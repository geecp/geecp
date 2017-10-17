<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/13
 * Time: 17:28
 */
namespace addons\cloud;
use think\Addons;
use think\Db;
class cloud extends Addons
{
    public function install()
    {
        // TODO: Implement install() method.
    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    public function cloud()
    {
        //查询哪一个分类插件的状态为启用,并调用方法
        $where['range']='cloud';
        $where['status']='1';
        $res=Db::name('addons')->where($where)->find();
        if($res){
            $way=$res['name'];
            $path = $way.'/controller/'.$way.'.php';
            include $path;
            $result= new $way();
            $function=$way.'_'.$data['function'];
            $code=$result->$function($data['data']);
        }else{
            $code='0';
        }
        return $code;
    }
}
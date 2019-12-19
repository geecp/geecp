<?php
namespace addons\vhost;
use think\Addons;
use think\Db;
class vhost extends Addons
{
    //安装
    public function install()
    {

        // TODO: Implement install() method.
    }
    //卸载
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }
    //主控
    public function vhost($data)
    {
        //查询哪一个分类插件的状态为启用,并调用方法
        $where['range']='vhost';
        $where['status']='2';
        $res=Db::name('gee_addons')->where($where)->find();
        if($res){
            $way=$res['name'];
            $path = $way.'/controller/'.$way.'.php';
            include_once $path;
            
            $className = '\addons\vhost\\'.$way.'\controller\\'.$way;
            $result= new $className();
            $function=$data['function'];
            $data['data']['config'] = $res['config'];
            dump($data);
            $code=$result->$function($data['data']);
          }else{
            $code='0';
        }
        return $code;
    }
}


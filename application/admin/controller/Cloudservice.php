<?php
namespace app\admin\controller;

use app\admin\controller\Common; // 前置操作
use think\Cache;
use think\Config;
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表

class Cloudservice extends Common
{
    public function index()
    {
        return $this->redirect('admin/Cloudservice/update');
    }
    public function update()
    {
        return $this->fetch('Cloudservice/update');
    }
    public function regsite()
    {
        return $this->fetch('Cloudservice/regsite');
    }
    public function diagnose()
    {
        return $this->fetch('Cloudservice/diagnose');
    }

    /**
     * 确认更新
     * @param string $name
     * @return \think\response\Json
     * @throws \gee\UpdateException
     * @throws \think\Exception
     */
    public function confirm($name="update")
    {
        $version =\gee\Service::getVersion();

        $res['status']=200;
        $res['msg']='更新成功';
        if (! (\gee\Service::update($name,true))) {
            $res['status']=100;
            $res['msg']='更新失败';
        }
        Cache::clear();
        return json($res);
    }

    /**
     * 立即检查版本
     */
    public function checkversion($name="update"){

        $version =\gee\Service::getVersion();

        $version_name = $name.'-'.$version;

        $res['status']=200;
        $res['msg'] ="有新版本";
        if (false === @file_get_contents(config("geecp.api_url")."/".$version_name.".zip",0,null,0,1)) {

            $res['status']=100;
            $res['msg']='你的已经是最新版,线上没有版本';
        }else{
            $res['files']=$this->checkfile($name);
        }

        return json($res);
    }

    /**
     * 检查新版本更新文件列表
     * @param string $name
     * @throws \gee\UpdateException
     * @throws \think\Exception
     */
    public function checkfile($name="update")
    {
        $version =\gee\Service::getVersion();

        $name = $name.'-'.$version;

        \gee\Service::download($name);

        //解压
        \gee\Service::unzip($name);

        //获取变动文件数
        $files =\gee\Service::getfilesNum($name);

        return $files;
    }
}

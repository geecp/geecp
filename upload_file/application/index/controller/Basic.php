<?php
namespace app\index\controller;
use app\index\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeUser; // 用户表

class Basic extends Common
{
    public function index()
    {
        return $this->redirect('index/Basic/info');
    }
    public function info()
    {
    	$user = new GeeUser();
        return $this->fetch('Basic/index');
    }
    public function safety(){

        return $this->fetch('Basic/safety');
    }
    public function auth(){

        return $this->fetch('Basic/auth');
    }
}

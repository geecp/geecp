<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;

class Index extends Common
{
    public function index()
    {
    	if(!isset($_COOKIE['token']) && !empty($_COOKIE['token']) && jwt_decode($_COOKIE['token'])){
            return $this->redirect('admin/Login/index');
        }
        return $this->fetch('Index/index');
    }
}

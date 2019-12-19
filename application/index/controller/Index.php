<?php
namespace app\index\controller;
use app\index\controller\Common; // 前置操作

class Index extends Common
{
    public function index()
    {
        return $this->fetch('Index/index');
    }
}

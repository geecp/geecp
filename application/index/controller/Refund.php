<?php
namespace app\index\controller;
use app\index\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;

class Refund extends Common
{
    public function list()
    {
      return $this->fetch('Refund/list');
    }
    public function record()
    {
      return $this->fetch('Refund/record');
    }
}

<?php
namespace app\home\controller;
use app\home\controller\Common; // 前置操作

class Product extends Common
{
    public function vps()
    {
        return $this->fetch('Product/vps');
    }
}

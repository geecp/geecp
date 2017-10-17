<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.qiduo.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: free < 185291445@qq.com>
// +----------------------------------------------------------------------
namespace app\common\controller;
use think\Controller;

class Commoninit extends Controller
{
    // 空操作
    public function _empty()
    {
        abort(404,'error');
    }
}

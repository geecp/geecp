<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.qiduo.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: free < 185291445@qq.com>
// +----------------------------------------------------------------------
namespace app\index\controller;

use think\Request;

class Error 
{
    public function index(Request $request)
    {
        abort(404,'error');
    }

}
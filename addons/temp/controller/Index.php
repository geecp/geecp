<?php
// +----------------------------------------------------------------------
// | thinkphp5 Addons [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------

namespace addons\temp\controller;

use think\addons\Controller;

class Index extends Controller
{
    public function exec()
    {
        echo '<p>我是temp插件中Index控制器的exec方法</p>';
        return $this->fetch();
    }

    public function abc()
    {
        echo "abc";
    }
}

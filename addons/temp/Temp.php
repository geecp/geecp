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
namespace addons\temp;

use think\Addons;

/**
 * 插件测试
 * @author byron sampson
 */
class Temp extends Addons
{
    public $info = [
        'name' => 'test',
        'title' => '插件测试',
        'description' => 'thinkph5插件测试',
        'status' => 0,
        'author' => 'byron sampson',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的testHook钩子方法
     * @return mixed
     */
    public function temphook($param)
    {
        $this->getConfig();
        return $this->fetch('index');
    }

}

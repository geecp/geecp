<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 13:54
 */
namespace addons\West263;

use think\Addons;
class West263 extends Addons
{
    public $info = [
        'name' => 'west',
        'title' => '西部数据接口',
        'description' => '西部数据接口',
        'status' => 0,
        'author' => 'nameless',
        'version' => '1.0'
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
     * 实现的westHook钩子方法
     * @return mixed
     */
    public function westhook($param)
    {

        $this->getConfig();
        return $this->fetch('content');
    }
}
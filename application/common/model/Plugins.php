<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.qiduo.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: free < 185291445@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;
use think\Validate;

class Plugins extends Model
{
    // 新增插件
    public function saveData($data)
    {
        if (isset($data['name'])&&$this->getPluginsByName($data['name'])) {
            $this->error='插件已存在！';
            return false;
        }

        // $this->data($data);
        return $this->allowField(true)->save($data);
    }

    public function getPluginsByName($name)
    {
        $res=$this
        ->where('name',$name)
        ->find();
        return $res;
    }

    public function getPluginsById($id)
    {
        $res=$this
        ->where('id',$id)
        ->find();
        return $res;
    }


    public function getAllPlugins($pno=1,$psize=50)
    {
        return $this
            ->limit($psize)
            ->page($pno)
            ->select();
    }


    /**
     * [getPluginsVersion description]
     * @param  [type] $plugins [单个 string  多个 array]
     * @return [type]          [description]
     */
    public function getPluginsVersion($plugins)
    {
        $parr=(!is_array($plugins))?array($plugins):$plugins;
        $p=$this
        ->field('name','version')
        ->where('name','in',$plugins)
        ->select();
        return $p;
    }


    public function getPluginsFile($plugins)
    {
        $res=$this
        ->query("select 
            p.id,p.name,pv.filepath,pv.version,pv.md5,pv.sha256 
            from gee_plugins p
            LEFT JOIN gee_plugins_version pv
            ON pv.pid=p.id and pv.version=p.version
            where(p.name='".$plugins."')");
        return$res;
    }
}
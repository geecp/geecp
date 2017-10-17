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

class PluginsVersion extends Model
{
    // 新增插件版本
    public function saveData($data)
    {
        if (isset($data['name'])&&isset($data['version'])&&$this->getPluginsVersion($data)) {
            $this->error='插件版本已存在！';

            return false;
        }

        // $this->data($data);
        return $this->allowField(true)->save($data);
    }

    public function getPluginsVersion($data)
    {
        $res=$this
        ->where(['name'=>$data['name'],'version'=>$data['version']])
        ->find();
        return $res;
    }

    public function updateVersion($data)
    {
        // p($data);die;
        if (isset($data['name'])&&isset($data['version'])&&$this->getPluginsVersion($data)) {
            $this->error='插件版本已存在！';

            return false;
        }

        $res=$this->allowField(true)->save($data);
        if ($res) {
            model('Plugins')
            ->where('id', $data['pid'])
            ->update(['version' => $data['version']]);
        }

        return $res;
    }

}
<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.qiduo.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: free < 185291445@qq.com>
// +----------------------------------------------------------------------
namespace app\common\service;

use \think\Request;

class Plugins 
{
    public function getPluginsCloudVersion($data)
    {
        $resUrl = httpUrl('getPluginsVersion',$data,['name'=>'qdapi','type'=>'get','version'=>'qdapiv1','resjson'=>0]);
        // dump($resUrl);
        $resInfo = httpGet($resUrl);
        // dump($resInfo);die;
        return $resInfo;
    }

    public function getPluginsFileByVersion($data)
    {
        $resUrl = httpUrl('getPluginsFile',$data,['name'=>'qdapi','type'=>'get','version'=>'qdapiv1','resjson'=>0]);
        // dump($resUrl);
        $resInfo = httpGet($resUrl);
        // dump($resInfo);die;
        return $resInfo;
    }

    public function getNewPluginsFile($data)
    {
        $resUrl = httpUrl('downloadPlugins',$data,['name'=>'qdapi','type'=>'get','version'=>'qdapiv1','resjson'=>0]);
        // dump($resUrl);
        $resInfo = qd_getFile($resUrl);
        // dump($resInfo);die;
        return $resInfo;
    }
}
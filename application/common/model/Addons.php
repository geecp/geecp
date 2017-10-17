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

class Addons extends Model
{
    public function  getAddonsInfo($id)
    {
        $res=$this
        ->where('id',$id)
        ->find()->toArray();
        return $res;
    }
}
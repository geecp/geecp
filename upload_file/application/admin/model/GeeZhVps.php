<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\GeeProduct; // 用户组表

/**
 * 纵横VPS表
 */
class GeeZhVps extends Model
{
	
	//用户类型
    public function getProductTypeAttr($var,$data)
    {
        $pro = new GeeProduct();
        return  $pro->where('id = '.$data['product_id'])->find()['name'];
    }
}

<?php
namespace app\index\model;
use think\Model;
use app\index\model\GeeProductClass; //产品分类表
/**
 * 产品表
 */
class GeeProduct extends Model
{
	//用户状态
  public function getTypeClassAttr($var,$data)
  {
    $class = new GeeProductClass();
    return $class->where('id = '.$data['type'])->value('title');
  }
}

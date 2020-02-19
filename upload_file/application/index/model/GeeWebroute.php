<?php
namespace app\index\model;
use think\Model;
use Think\Db;
/**
 * 前台路由表
 */
class GeeWebroute extends Model
{
  public function getProTypeAttr($var,$data)
  {
    $title = Db::name('gee_product_type')->where('id = '.$data['is_pro'])->find()['title'];
    return $title?$title:'无';
  }
}

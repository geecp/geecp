<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\GeeProduct; // 产品表
use app\admin\model\GeeProductGroup; // 产品组表
use app\admin\model\GeeProductClass; // 产品分类表
use app\admin\model\GeeProductType; // 产品类型表
use app\admin\model\GeeUser; // 

/**
 * VPS表
 */
class GeeVps extends Model
{
	
	//用户类型
    public function getProductTypeAttr($var,$data)
    {
        $pro = new GeeProduct();
        return  $pro->where('id = '.$data['product_id'])->find()['name'];
    }
    public function getUserAttr($var,$data)
    {
        $u = new GeeUser();
        return  $u->where('id = '.$data['user_id'])->find()['name'];
    }
    public function getProListAttr($var,$data)
    {
        $class = new GeeProductClass();
        $group = new GeeProductGroup();
        $pro = new GeeProduct();
        $item = $pro->where('id = '.$data['product_id'])->find()->toArray();
        $d = explode("|", $item['describe']);
        foreach($d as $k=>$v){
          $dhtml .= $v.'<br/>';
        }
        $html = '产品名称：'.$item["name"].'<br />'.$dhtml;
        // dump($item);
        return $html;
    }
}

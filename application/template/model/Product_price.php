<?php
namespace app\admin\model;
use think\Model;

class  Product_price extends Model
{
    public function Price()
    {
        return $this->hasMany('VhostProduct','title','p_id');
    }
}
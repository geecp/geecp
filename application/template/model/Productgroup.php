<?php
namespace app\template\model;
use think\Model;

class  Productgroup extends Model
{
    public function Productgroup()
    {
        return $this->hasOne('VhostProduct','id','pro_id');
    }
}
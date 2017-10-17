<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 15:44
 */
namespace app\api\model;
use think\Model;
class Pushlist extends Model
{
    //
    public function getSmsCount($data)
    {
        foreach ($data as $k=>$v){
            $where['creat_time']=['in',$v];
            $res[$k]=Pushlist::where($where)->count();
        }
        return $res;
    }
}
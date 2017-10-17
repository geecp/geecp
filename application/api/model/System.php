<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9
 * Time: 11:01
 */
namespace app\api\model;
use think\Model;
class System extends Model
{
    public static function getServerOs($data)
    {
        //获取服务器，vps操作系统
        if($data!='vhost'&&$data!='serverhost'){
            $data='server';
        }
        $res=System::where('type',$data)->group('name')->field('id,name,type')->select();
        return $res;
    }

    public static function getServerVersion($data)
    {
        $res=System::where('name',$data)->select();
        return $res;
    }
}
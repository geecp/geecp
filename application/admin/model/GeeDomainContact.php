<?php
namespace app\admin\model;
use think\Model;
use think\Db;

/**
 * 用户域名联系人表
 */
class GeeDomainContact extends Model
{
	
    //申请用户
    public function getIscheckAttr($var, $data)
    {
      switch($data['ischecked']){
        case '0':
          $ck = '未审核';
        break;
        case '1':
          $ck = '待审核';
        break;
        case '2':
          $ck = '审核通过';
        break;
        case '3':
          $ck = '审核失败';
        break;
        case '5':
          $ck = '未上传资料';
        break;
        case '6':
          $ck = '黑名单';
        break;
        case '8':
          $ck = '上传中';
        break;
      }
      return $ck;
    }
    
    public function getUserAttr($var, $data)
    {
        $info = Db::name('gee_user')->where('id = '.$data['user_id'])->find();
        return $info['name'];
    }
}

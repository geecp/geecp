<?php
namespace app\index\model;
use think\Model;

/**
 * 物理服务器租用表
 */
class GeeServer extends Model
{
  public function getIspassAttr($var,$data){
    if($data['password']){
      return '<a href="javascript:;" data-pass="">查看</a>';
    } else {
      return '';
    }
  }
	public function getStatussAttr($var, $data)
    {
        switch ($data['status']) {
            case '0':
                return '开通中';
                break;
            case '1':
                return '已到期';
                break;
            case '2':
                return '正在重装系统';
                break;
            case '3':
                return '正在运行';
                break;
            case '4':
                return '服务器异常';
                break;
        }
    }
}

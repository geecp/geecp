<?php
namespace app\admin\model;

use think\Model;
use app\index\model\GeeUser; // 用户表

/**
 * 发票表
 */
class GeeInvoice extends Model
{

    //申请用户
    public function getUsernameAttr($var, $data)
    {
      $u = new GeeUser();
      return $u->where('id = '.$data['user_id'])->find()['username'];
    }
    //发票类型
    public function getTypesAttr($var, $data)
    {
      // dump($data['type']);
        switch ($data['type']) {
            case '0':
                return '增值税普通发票';
                break;
            case '1':
                return '增值税专用发票';
                break;
        }
    }
    //发票状态
    public function getStatussAttr($var, $data)
    {
        switch ($data['status']) {
            case '0':
                return '审核中';
                break;
            case '1':
                return '已寄出';
                break;
            case '2':
                return '已取消';
                break;
            case '3':
                return '未通过';
                break;
        }
    }
    //普票类型
    public function getNTypesAttr($var, $data)
    {
        switch ($data['n_type']) {
            case '0':
                return '个人普票';
                break;
            case '1':
                return '企业类普票';
                break;
        }
    }
}

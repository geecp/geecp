<?php
namespace app\index\model;

use think\Model;
use think\Db;

// use app\admin\model\GeeProduct; // 用户组表

/**
 * 域名表
 */
class GeeDomain extends Model
{
    //状态
    public function getRStateAttr($var, $data)
    {
      $type = $data['runstate']?$data['runstate']:$data['r_state'];
        switch ($type) {
            case 'unpay':
                return '未开通';
                break;
            case 'run':
                return '运行中';
                break;
            case 'pause':
                return '暂停中';
                break;
            case 'processing':
                return '处理中';
                break;
            case 'deleted':
                return '已删除';
                break;
            case 'stopped by admin':
                return '解析停止';
                break;
            case 'waiting':
                return '待开通';
                break;
            case 'registered':
                return '已被注册';
                break;
            case 'transfering':
                return '已过期';
                break;
            case 'renewal process:':
                return '续费处理中';
                break;
            case 'renewals failure':
                return '续费失败';
                break;
            case 'Failed':
                return '注册失败';
                break;
            case 'Not certified':
                return '尚未实名';
                break;
            case 'Illegal':
                return '非法';
                break;
        }
    }
    public function getDStateAttr($var, $data)
    {
        switch ($data['dnvcstate']) {
            case 'pass':
                return '通过';
                break;
            case 'unpass':
                return '未通过';
                break;
            default:
                return '审核中';
              break;
        }
    }
    public function getDTypeAttr($var, $data)
    {
        switch ($data['domaintype']) {
            case 'personal':
                return '个人';
                break;
            default:
                return '企业';
              break;
        }
    }
    public function getUserAttr($var, $data)
    {
        $info = Db::name('gee_user')->where('id = '.$data['user_id'])->find();
        return $info['name'];
    }
}

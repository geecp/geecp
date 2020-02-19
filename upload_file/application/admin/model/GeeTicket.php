<?php
namespace app\admin\model;

use think\Model;

/**
 * 工单表
 */
class GeeTicket extends Model
{
    //工单状态
    public function getTStatusAttr($var, $data)
    {
        switch ($data['status']) {
            case '0':
                return '待接入';
                break;
            case '1':
                return '处理中';
                break;
            case '2':
                return '待回复';
                break;
            case '3':
                return '待您确认';
                break;
            case '4':
                return '已撤销';
                break;
            case '5':
                return '已完成';
                break;
        }
    }
}

<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\GeeStaffgroup; // 用户组表

/**
 * 员工表
 */
class GeeAdminuser extends Model
{
	//员工组
    public function getStaffGroupAttr($var,$data)
    {
        return db('gee_staffgroup')->where('id = '.$data['group_id'])->find()['name']?db('gee_staffgroup')->where('id = '.$data['group_id'])->find()['name']:'暂无分组';
    }
	//员工状态
    public function getStaffStatusAttr($var,$data)
    {
    	switch ($data['status']) {
    		case '0':
    			return '正常';
			break;
    		case '1':
    			return '欠费';
			break;
    		case '2':
    			return '锁定';
			break;
    	}
    }
}

<?php
namespace app\admin\model;
use think\Model;

/**
 * 消息模板表
 */
class GeeMsgmodel extends Model
{
	// 模板状态
    public function getTempTypeAttr($var,$data)
    {
    	switch ($data['type']) {
    		case '0':
    			return '短信验证码';
			break;
    		case '1':
    			return '短信通知';
			break;
    		case '2':
    			return '邮件验证码';
			break;
    		case '3':
    			return '邮件通知';
			break;
    	}
    }
	// 模板状态
    public function getTempStatusAttr($var,$data)
    {
    	switch ($data['status']) {
    		case '0':
    			return '可用';
			break;
    		case '1':
    			return '禁用';
			break;
    	}
    }
}

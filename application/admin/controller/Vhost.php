<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeVhostGroup; // 虚拟主机配置分组表
use app\admin\model\GeeVhostConfig; // 虚拟主机配置表

class Vhost extends Common
{
    public function index()
    {
    	$vhost = new GeeVhostConfig();
    	$vhostList = $vhost->order('id desc')->select();
    	$this->assign('list',$vhostList);
        return $this->fetch('Vhost/index');
    }
    public function add(){
    	$group = new GeeVhostGroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('groupList',$groupList);

    	if($_GET['id']){
    		$id = $_GET['id'];
    		$vhost = new GeeVhostConfig();
    		$vhostInfo = $vhost->where('id = '.$id)->find();
    		$this->assign('info',$vhostInfo);
    	}

        return $this->fetch('Vhost/add');
    }
    //用户数据提交操作
    public function addAuth(){
    	$vhost = new GeeVhostConfig();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改用户
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	} else {
    	   //如果是新增配置
			$hasVhost = $vhost->where('name = "'.$data['name'].'"')->find();
			if($hasVhost){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '产品已存在！';
	    		return json_encode($ret);
			}
    	}
		if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品标识提交有误！';
    		return json_encode($ret);
    	}
        if(!isset($data['title']) || empty($data['title'])){
            $ret['status'] = 422;
            $ret['msg'] = '产品名称提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['information']) || empty($data['information'])){
            $ret['status'] = 422;
            $ret['msg'] = '产品信息提交有误！';
            return json_encode($ret);
        }
    	if(!isset($data['email']) || !vali_data('email',$data['email'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '邮箱提交有误！';
    		return json_encode($ret);
    	}
        if(!isset($data['label']) || empty($data['label'])){
            $ret['status'] = 422;
            $ret['msg'] = '产品标签提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['month']) || empty($data['month'])){
            $ret['status'] = 422;
            $ret['msg'] = '月价格提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['quarter']) || empty($data['quarter'])){
            $ret['status'] = 422;
            $ret['msg'] = '季度价格提交有误！';
            return json_encode($ret);
        }
    	if(!isset($data['semestrale']) || empty($data['semestrale'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '半年提交有误！';
    		return json_encode($ret);
    	}
        if(!isset($data['years']) || empty($data['years'])){
            $ret['status'] = 422;
            $ret['msg'] = '年提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['biennium']) || empty($data['biennium'])){
            $ret['status'] = 422;
            $ret['msg'] = '两年提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['triennium']) || empty($data['triennium'])){
            $ret['status'] = 422;
            $ret['msg'] = '三年提交有误！';
            return json_encode($ret);
        }
	
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$vhostres = $vhost->save($data,$w);
    		if($vhostres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了虚拟主机配置 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$vhostres = $vhost->save($data);
    		if($vhostres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了虚拟主机配置 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function del(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isDelete()){
    		$vhost = new GeeVhostConfig();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delVhost = $vhost->where('id = '.$id)->delete();
    		if($delVhost){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了虚拟主机配置ID '.$id,
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
    		}
    	} else {
    		$ret['status'] = 500;
    		$ret['msg'] = '操作超时';
    	}
		return json_encode($ret);
    }

    public function group(){
    	$group = new GeeVhostGroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$groupList);
        return $this->fetch('Vhost/group');
    }

    public function addgroup(){
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$group = new GeeVhostGroup();
    		$groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}

        return $this->fetch('Vhost/addgroup');
    }

    public function addgroupAuth(){
    	$group = new GeeVhostGroup();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改用户组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	} else {
    	//如果是新增用户组
			$hasGroup = $group->where('name = "'.$data['name'].'"')->find();
			if($hasGroup){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '虚拟主机组已存在！';
	    		return json_encode($ret);
			}
    	}
		if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '虚拟主机组名提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$groupres = $group->save($data,$w);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了虚拟主机组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了虚拟主机组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function delgroup(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isDelete()){
    		$group = new GeeVhostGroup();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了虚拟主机组ID '.$id,
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
    		}
    	} else {
    		$ret['status'] = 500;
    		$ret['msg'] = '操作超时';
    	}
		return json_encode($ret);
    }
}

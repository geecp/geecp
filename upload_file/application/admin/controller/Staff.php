<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeAdminuser; // 员工表
use app\admin\model\GeeStaffgroup; // 员工组表

class Staff extends Common
{
    public function index()
    {
        return $this->redirect('admin/Staff/list');
    }
    public function list()
    {
    	$staff = new GeeAdminuser();
    	$staffList = $staff->order('id desc')->select();
    	$this->assign('list',$staffList);
        return $this->fetch('Staff/index');
    }
    public function add(){
    	$group = new GeeStaffgroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('groupList',$groupList);

    	if($_GET['id']){
    		$id = $_GET['id'];
    		$staff = new GeeAdminuser();
    		$staffInfo = $staff->where('id = '.$id)->find();
    		$this->assign('info',$staffInfo);
    	}

        return $this->fetch('Staff/add');
    }
    //员工数据提交操作
    public function addAuth(){
    	$staff = new GeeAdminuser();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改员工
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
	    	if(isset($data['password']) && !vali_data('pwd',$data['password'])){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '请设置6-18字符登录密码';
	    		return json_encode($ret);
	    	} else {
				$data['password'] = passToHash($data['password']);
	    	}
    	} else {
    	//如果是新增员工
	    	if(!isset($data['password']) || !vali_data('pwd',$data['password'])){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '请设置6-18字符登录密码';
	    		return json_encode($ret);
	    	} else {
				$data['password'] = passToHash($data['password']);
	    	}

			$hasStaff = $staff->where('username = "'.$data['username'].'"')->find();
			if($hasStaff){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '用户已存在！';
	    		return json_encode($ret);
			}
            $data['ip'] = get_ip();
    	}
		if(!isset($data['username']) || empty($data['username'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '用户名提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '名称提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['email']) || !vali_data('email',$data['email'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '邮箱提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['phone']) ||!vali_data('phone',$data['phone'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '手机号提交有误！';
    		return json_encode($ret);
    	}

    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$staffres = $staff->save($data,$w);
    		if($staffres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了员工 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$staffres = $staff->save($data);
    		if($staffres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了员工 '.$data['name'],
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
    		$staff = new GeeAdminuser();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delStaff = $staff->where('id = '.$id)->delete();
    		if($delStaff){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了员工ID '.$id,
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

    public function disabled(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isPut()){
    		$staff = new GeeAdminuser();
    		$log = new GeeLog();
    		$id['id'] = request()->param()['id'];
    		$disabledStaff = $staff->save(['status'=>request()->param()['status']],$id);
    		if($disabledStaff){
    			$saveInfo = [
					'content' => request()->param()['status'] == '1' ? $this->_adminInfo['name'].' 禁用了员工ID '.$id['id'] :  $this->_adminInfo['name'].' 解禁了员工ID '.$id['id'],
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
    	$group = new GeeStaffgroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$groupList);
        return $this->fetch('Staff/group');
    }

    public function addgroup(){
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$group = new GeeStaffgroup();
    		$groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}

        return $this->fetch('Staff/addgroup');
    }

    public function addgroupAuth(){
    	$group = new GeeStaffgroup();
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
	    		$ret['msg'] = '用户组已存在！';
	    		return json_encode($ret);
			}
    	}
		if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '用户组名提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$groupres = $group->save($data,$w);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了员工组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了员工组 '.$data['name'],
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
    		$group = new GeeStaffgroup();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了员工组ID '.$id,
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

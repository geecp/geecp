<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeUser; // 用户表
use app\admin\model\GeeUsergroup; // 用户组表
use app\index\model\GeeUserEnterprise; // 用户企业认证表

class User extends Common
{
    public function index()
    {
        return $this->redirect('admin/User/list');
    }
    public function list()
    {
    	$user = new GeeUser();
      $userList = $user->order('id desc')->select();
      // dump($userList);
    	$this->assign('list',$userList);
        return $this->fetch('User/index');
    }
    public function add(){
    	$group = new GeeUsergroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('groupList',$groupList);

    	if($_GET['id']){
    		$id = $_GET['id'];
    		$user = new GeeUser();
    		$userInfo = $user->where('id = '.$id)->find();
    		$this->assign('info',$userInfo);
    	}

        return $this->fetch('User/add');
    }
    //用户数据提交操作
    public function addAuth(){
    	$user = new GeeUser();
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

	    	if(isset($data['password']) && !vali_data('pwd',$data['password'])){
                $ret['status'] = 422;
                $ret['msg'] = '请设置6-18字符登录密码';
                return json_encode($ret);
            } else {
				$data['password'] = passToHash($data['password']);
	    	}
    	} else {
    	//如果是新增用户
	    	if(isset($data['password']) && !vali_data('pwd',$data['password'])){
                $ret['status'] = 422;
                $ret['msg'] = '请设置6-18字符登录密码';
                return json_encode($ret);
            } else {
				$data['password'] = passToHash($data['password']);
	    	}

			$hasUser = $user->where('username = "'.$data['username'].'"')->find();
			if($hasUser){
	    		$ret['status'] = 422;
	    		$ret['msg'] = '用户已存在！';
	    		return json_encode($ret);
			}
    		$data['create_ip'] = get_ip();
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
    	if(!isset($data['tel']) || empty($data['tel'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '固话提交有误！';
    		return json_encode($ret);
    	}
	
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$userres = $user->save($data,$w);
    		if($userres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了用户 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$userres = $user->save($data);
    		if($userres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了用户 '.$data['name'],
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
    		$user = new GeeUser();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delUser = $user->where('id = '.$id)->delete();
    		if($delUser){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了用户ID '.$id,
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
    		$user = new GeeUser();
    		$log = new GeeLog();
    		$id['id'] = request()->param()['id'];
    		$disabledUser = $user->save(['status'=>request()->param()['status']],$id);
    		if($disabledUser){
    			$saveInfo = [
					'content' => request()->param()['status'] == '2' ? $this->_adminInfo['name'].' 禁用了用户ID '.$id['id'] :  $this->_adminInfo['name'].' 解禁了用户ID '.$id['id'],
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
    	$group = new GeeUsergroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$groupList);
        return $this->fetch('User/group');
    }

    public function addgroup(){
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$group = new GeeUsergroup();
    		$groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}

        return $this->fetch('User/addgroup');
    }

    public function addgroupAuth(){
    	$group = new GeeUsergroup();
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
					'content' => $this->_adminInfo['name'].' 修改了用户组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了用户组 '.$data['name'],
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
    		$group = new GeeUsergroup();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了用户组ID '.$id,
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
    public function realverify(){
      $u = new GeeUser();
    	$userList = $u->where('realverify = 1')->order('id desc')->select();
    	$this->assign('list',$userList);
      return $this->fetch('User/realverify');
    }
    public function passreal(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isPut()){
    		$user = new GeeUser();
    		$log = new GeeLog();
    		$id['id'] = request()->param()['id'];
    		$passUser = $user->where($id)->update(['realverify'=>2]);
    		if($passUser){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 通过了用户ID '.$id['id'].' 的实名认证',
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
    public function rejectreal(){
      $ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isPut()){
    		$user = new GeeUser();
    		$log = new GeeLog();
    		$id['id'] = request()->param()['id'];
    		$passUser = $user->where($id)->update(['realverify'=>3]);
    		if($passUser){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 拒绝了用户ID '.$id['id'].' 的实名认证',
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
    public function enterpriseverify(){
      $u = new GeeUser();
      $ue = new GeeUserEnterprise();
    	$userList = $ue->where('status = 0')->order('id desc')->select();
    	$this->assign('list',$userList);
      return $this->fetch('User/enterpriseverify');
    }
    public function passenterprise(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isPut()){
    		$user = new GeeUser();
        $ue = new GeeUserEnterprise();
        $log = new GeeLog();
    		$id['id'] = request()->param()['id'];
        $info = $user->where($id)->find();
        $uid['id'] = $info['user_id'];
        $passUser = $ue->where($id)->update(['status'=>1]);
    		if($passUser){
          if($info['realverify'] != 2){
            $user->where($uid)->update(['realverify'=>2]);
          }
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 通过了用户ID '.$id['id'].' 的企业认证',
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
    public function rejectenterprise(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isPut()){
    		$user = new GeeUser();
        $ue = new GeeUserEnterprise();
        $log = new GeeLog();
        $id['id'] = request()->param()['id'];
        $info = $user->where($id)->find();
        $uid['id'] = $info['user_id'];
        $passUser = $ue->where($id)->update(['status'=>2]);
    		if($passUser){
          if($info['realverify'] != 2){
            $user->where($uid)->update(['realverify'=>3]);
          }
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 拒绝了用户ID '.$id['id'].' 的企业认证',
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

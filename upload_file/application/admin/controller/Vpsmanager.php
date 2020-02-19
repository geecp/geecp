<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeZhVps; // 用户vps表
use addons\vps\vps; // 调用vps插件

class Vpsmanager extends Common
{
    public function zhvps()
    {
    	$vhost = new GeeZhVps();
    	$vhostList = $vhost->order('id desc')->select();
    	$this->assign('list',$vhostList);
      return $this->fetch('Vpsmanager/zhvps');
    }
    public function add(){
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$vhost = new GeeZhVps();
    		$vhostInfo = $vhost->where('id = '.$id)->find();
    		$this->assign('info',$vhostInfo);
    	}
      return $this->fetch('Vpsmanager/add');
    }
    //用户数据提交操作
    public function addAuth(){
    	$vhost = new GeeZhVps();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改配置
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
        } else {
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
    		$vhost = new GeeZhVps();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delVpsmanager = $vhost->where('id = '.$id)->delete();
    		if($delVpsmanager){
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

    public function sync(){
      $zhvps = new GeeZhVps();
      $plug = new vps();
      $data = [
        'function'=>'control',
        'data'=>[
          'userid'=>'',
          'userstr'=>'',
          'data'=>'',
          'action'=>'',
          'attach'=>'',
          'openX'=>""
        ]
      ];
      $plug->vps($data);
    }
}

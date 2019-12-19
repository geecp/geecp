<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\index\model\GeeWebroute; // 前台路由表
use app\admin\model\GeeProductType; // 产品类型表

class Routes extends Common
{
    public function index()
    {
        $route = new GeeWebroute();
        if($_GET['fid']){
          $list = $route->where('f_id = '.$_GET['fid'])->select();
        }else{
          $list = $route->where('f_id = 0')->select();
        }
        $this->assign('list',$list);
        return $this->fetch('routes/index');
    }
    public function add(){
      $route = new GeeWebroute();
      $type = new GeeProductType();
      $typelist = $type->select();
      $this->assign('typelist',$typelist);
      if($_GET['id']){
        $info = $route->where('id = '.$_GET['id'])->find();
        $this->assign('info',$info);
      }
      return $this->fetch('routes/add');
    }
    public function addAuth(){
      $route = new GeeWebroute();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改路由
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	}
		  if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '导航名称提交有误！';
    		return json_encode($ret);
      }
      if($data['f_id'] && $data['f_id'] != 0){
        $is_has = $route->where('`f_id` = '.$data['f_id'].' and `code` = "'.$data['code'].'"')->find();
      } else {
        $is_has = $route->where('`f_id` = 0 and `code` = "'.$data['code'].'"')->find();
      }
      if($is_has && $is_has['id'] != $id){
    		$ret['status'] = 422;
    		$ret['msg'] = '导航标识已存在！';
    		return json_encode($ret);
      }
      unset($data['f_id']);

    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$routeres = $route->save($data,$w);
    		if($routeres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了路由 '.$data['title'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$routeres = $route->save($data);
    		if($routeres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了路由 '.$data['title'],
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
      $route = new GeeWebroute();
      $log = new GeeLog();
    	if(request()->isDelete()){
        $id = request()->param()['id'];
        $has_child = $route->where('`f_id` = '.$id)->find();
        if(!$has_child){
          $del = $route->where('id = '.$id)->delete();
        } else {
          $ret['status'] = 500;
          $ret['msg'] = '请先删除该路由下的子路由';
        }
    	} else {
    		$ret['status'] = 500;
    		$ret['msg'] = '操作超时';
      }
      return json_encode($ret);
    }
}

<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeProduct; // 产品表
use app\admin\model\GeeProductGroup; // 产品组表
use app\admin\model\GeeProductClass; // 产品分类表
use app\admin\model\GeeProductType; // 产品类型表
use app\index\model\GeeProConfig; // 用户表
use app\admin\model\GeeServer; // 物理服务器表
use app\admin\model\GeeServerAdded; // 物理服务器增值服务组表
use app\admin\model\GeeServerAddedItems; // 物理服务器增值服务子项表
use app\admin\model\GeeMsgmodel; // 邮件模板表
use app\admin\model\GeeAddons; // 
use app\admin\model\GeeUser; // 
use app\admin\model\GeeVps; //vps表
use app\index\model\GeeBilling; //vps表
use app\index\model\GeeDomain; //域名表
use app\index\model\GeeDomainPrice; //域名价格表
use app\admin\model\GeeDomainContact; //域名模板表

use think\Db;

class Service extends Common
{
    public function index()
    {
        return $this->redirect('admin/Server/list');
    }
    public function server()
    {
      $server = new GeeServer();
      // $group = new GeeServerAdded();
    	$serverList = $server->order('id desc')->select();
    	// $groupList = $group->order('id desc')->select();
    	$this->assign('list',$serverList);
    	// $this->assign('grouplist',$groupList);
      return $this->fetch('Server/index');
    }
    public function getserver(){
      $g = $_GET;
      $s = new GeeServer();
      $u = new GeeUser();
      $users = $u->select();
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
      $sinfo = $s->where('id = '.$g['id'])->find();
      if(!$sinfo){
    		$ret['status'] = 422;
    		$ret['msg'] = '未找到该服务器!';
    		return json_encode($ret);
      }
      $sinfo['end_time'] = date("Y-m-d H:i",$sinfo['end_time']);
      $sinfo['users'] = $users;
      $ret['data'] = $sinfo;
      // dump($_SERVER['HTTP_REFERER']);
      return json_encode($ret);
    }
    public function editserver(){
      $s = new GeeServer();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
      $id = $data['id'];
      unset($data['id']);
      foreach($data as $key=>$var){
          if(empty($var) && $var != '0'){
              unset($data[$key]);
          }
      }
      $data['end_time'] = strtotime($data['end_time']);
      $w['id'] = $id;
      $sres = $s->save($data,$w);
      if($sres){
        $saveInfo = [
        'content' => $this->_adminInfo['name'].' 修改了物理服务器信息 ID:'.$data['id'],
        'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
      return json_encode($ret);
      }
    }
    public function delivery(){
      $post = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
      $server = new GeeServer();
		  $log = new GeeLog();
      $serverinfo = $server->where('id = '.$post['id'])->find();
      if(!$serverinfo['ip'] || !$serverinfo['intranetip'] || !$serverinfo['username'] || !$serverinfo['password']){
    		$ret['status'] = 422;
    		$ret['msg'] = '请先确认服务器的IP/用户名/密码是否填写!';
    		return json_encode($ret);
      }
      $res = $server->where('id = '.$post['id'])->update(['status'=> 3]);
      if($res){
        $saveInfo = [
        'content' => $this->_adminInfo['name'].' 交付了物理服务器 ID:'.$post['id'],
        'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
        return json_encode($ret);
      }
    }
    public function deliveryauth(){
      $post = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
      $server = new GeeServer();
      $log = new GeeLog();
      
      if(!$post['ip'] || !$post['intranetip'] || !$post['username'] || !$post['password']){
    		$ret['status'] = 422;
    		$ret['msg'] = '请先确认服务器的IP/用户名/密码是否填写!';
    		return json_encode($ret);
      }
      $res = $server->where('id = '.$post['id'])->update(['ip'=> $post['ip'],'intranetip'=>$post['intranetip'],'username'=> $post['username'],'password'=> $post['password'],'status'=> 3]);
      if($res){
        $saveInfo = [
          'content' => $this->_adminInfo['name'].' 补充了物理服务器信息 ID:'.$post['id'],
          'ip' => get_ip()
        ];
        $logres = $log->save($saveInfo);
        return json_encode($ret);
      }
    }
    public function add(){
    	$group = new GeeProductGroup();
    	$groupList = $group->order('sort desc,id desc')->select();
    	$this->assign('groupList',$groupList);
    	$model = new GeeMsgmodel();
    	$modelList = $model->order('id desc')->select();
      $this->assign('modelList',$modelList);
    	$addons = new GeeAddons();
    	$addonslList = $addons->where('status = 2')->order('id desc')->select();
      $this->assign('addonslList',$addonslList);
      $product = new GeeProduct();
    	$updatelist = $product->order('id desc')->select();
      $this->assign('updatelist',$updatelist);
      $class = new GeeProductClass();
      $classlist = $class->order('id')->select();
      $this->assign('classlist',$classlist);
      
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$productInfo = $product->where('id = '.$id)->find();
        $updatelist = $product->where('type = '.$productInfo['type'])->order('id desc')->select();
        $this->assign('updatelist',$updatelist);
        $this->assign('info',$productInfo);
        if($productInfo['plug_config']){
          $nowConfig = json_decode($productInfo['plug_config'],true);
          $nowPlugConfig = $addons->where('id',$productInfo['plug'])->find()['config'];
          $nowPlugConfig = json_decode($nowPlugConfig,true);
          $html = '';
          foreach($nowPlugConfig as $k=>$v){
            switch($v['type']){
              case 'text':
                  $html .= '<div class="form-group"><label for="'.$k.'" class="col-sm-2 control-label">'.$k.'</label><div class="col-sm-10"><input type="'.$v['type'].'" class="form-control" id="'.$k.'" name="'.$k.'" placeholder="请输入'.$k.'" value="'.$nowConfig[$k].'" autocomplete="off"></div></div>';
                  break;
              case 'password':
                  $html .= '<div class="form-group"><label for="'.$k.'" class="col-sm-2 control-label">'.$k.'</label><div class="col-sm-10"><input type="'.$v['type'].'" class="form-control" id="'.$k.'" name="'.$k.'" placeholder="请输入'.$k.'" value="'.$nowConfig[$k].'" autocomplete="off"></div></div>';
                  break;
              case 'textarea':
                  $html .= '<div class="form-group"><label for="'.$k.'" class="col-sm-2 control-label">'.$k.'</label><div class="col-sm-10"><textarea name="'.$k.'" id="'.$k.'" class="form-control" cols="30" rows="10" placeholder="请输入'.$k.'">'.$nowConfig[$k].'</textarea></div></div>';
                  break;
            }
          }
          $this->assign('plug_config',$html);
        }
    	}

        return $this->fetch('Product/add');
    }
    //产品数据提交操作
    public function addAuth(){
      $product = new GeeProduct();
      $plug = new GeeAddons();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改产品
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	}
		  if(!isset($data['type']) || empty($data['type'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品类型提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品名称提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['email_model']) || empty($data['email_model'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '邮件模板ID提交有误！';
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
    		$ret['msg'] = '半年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['years']) || empty($data['years'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['biennium']) || empty($data['biennium'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '两年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['triennium']) || empty($data['triennium'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '三年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['sort']) || empty($data['sort'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品排序提交有误！';
    		return json_encode($ret);
      }
      if(!empty($data['plug'])){
        $conf = $plug->where('id',$data['plug'])->find();
        $config = json_decode($conf['config'],true); 
        foreach($config as $k=>$v){
          $saveConfig[$k] =  $data[$k];
          unset($data[$k]);
        }
        $saveConfig = json_encode($saveConfig);
        $data['plug_config'] = $saveConfig;
        // dump($saveConfig);
      }
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$productres = $product->save($data,$w);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了产品 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$productres = $product->save($data);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了产品 '.$data['name'],
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
    		$product = new GeeProduct();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delProduct = $product->where('id = '.$id)->delete();
    		if($delProduct){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了产品ID '.$id,
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
    	$group = new GeeProductGroup();
    	$groupList = $group->order('sort desc,id desc')->select();
    	$this->assign('list',$groupList);
        return $this->fetch('Product/group');
    }

    public function addgroup(){
    	if($_GET['id']){
        $id = $_GET['id'];
    		$group = new GeeProductGroup();
        $groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}
      return $this->fetch('Product/addgroup');
    }

    public function addgroupAuth(){
    	$group = new GeeProductGroup();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改产品组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	} else {
        //如果是新增产品组
        $hasGroup = $group->where('name = "'.$data['name'].'"')->find();
        if($hasGroup){
            $ret['status'] = 422;
            $ret['msg'] = '产品组已存在！';
            return json_encode($ret);
        }
    	}
		  if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品组名提交有误！';
    		return json_encode($ret);
    	}
		  if(!isset($data['slogan']) || empty($data['slogan'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '分组标语提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$groupres = $group->save($data,$w);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了产品组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了产品组 '.$data['name'],
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
      $product = new GeeProduct();
      $group = new GeeProductGroup();
      $log = new GeeLog();
    	if(request()->isDelete()){
    		$id = request()->param()['id'];
        $items = $product->where('group_id = '.$id)->find();
        if($items){
          $ret['status'] = 422;
          $ret['msg'] = '该产品分组下还存在产品！';
          return json_encode($ret);
        }
        $delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了产品组ID '.$id,
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
    public function getPlugConfig(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $id = $_GET['id'];
      if(!$id){
    		$ret['status'] = 500;
    		$ret['msg'] = '操作超时';
        return json_encode($ret);
      }
    	$plug = new GeeAddons();
      $plugConfig = $plug->where('id = '.$id)->find()['config'];
      $ret['data'] = $plugConfig;
      return json_encode($ret);
    }
    public function type(){
    	$type = new GeeProductType();
    	$typelist = $type->order('id desc')->select();
    	$this->assign('list',$typelist);
        return $this->fetch('Product/type');
    }

    public function addtype(){
      $route = new GeeWebroute();
      if($_GET['id']){
        $id = $_GET['id'];
        $type = new GeeProductType();
        $typeInfo = $type->where('id = '.$id)->find();
        $routeInfo = $route->where('is_pro = '.$id)->select();
    		$this->assign('info',$typeInfo);
      }
      $routes = $route->where('is_show = "1"')->select();
      $this->assign('webroutes',$routes);
      return $this->fetch('Product/addtype');
    }

    public function addtypeAuth(){
      $route = new GeeWebroute();
    	$type = new GeeProductType();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改产品组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
          }
          $routeItems = $route->where('is_pro = '.$id)->select();
          if($routeItems){
            foreach($routeItems as $k=>$v){
              $route->where('id = '.$v['id'])->update(['is_pro'=>0]);
            }
          }
    	} else {
        //如果是新增产品组
        $hastype = $type->where('title = "'.$data['title'].'"')->find();
        if($hastype){
            $ret['status'] = 422;
            $ret['msg'] = '产品类型已存在！';
            return json_encode($ret);
        }
      }
      
      $data['is_pro'] = explode(',',$data['is_pro']);
      foreach($data['is_pro'] as $k=>$v){
        $route->where('id = '.$v)->update(['is_pro'=>$id]);
      }
      // dump($data['is_pro']);
      unset($data['is_pro']);
      // exit;
      if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '产品类型名称提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$typeres = $type->save($data,$w);
    		if($typeres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了产品类型 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$typeres = $type->save($data);
    		if($typeres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了产品类型 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function deltype(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $product = new GeeProduct();
      $type = new GeeProductType();
      $log = new GeeLog();
    	if(request()->isDelete()){
    		$id = request()->param()['id'];
        $items = $product->where('group_id = '.$id)->find();
        if($items){
          $ret['status'] = 422;
          $ret['msg'] = '该产品分组下还存在产品！';
          return json_encode($ret);
        }
        $deltype = $type->where('id = '.$id)->delete();
    		if($deltype){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了产品类型ID '.$id,
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
    public function class(){
    	$class = new GeeProductClass();
    	$classlist = $class->order('id desc')->select();
    	$this->assign('list',$classlist);
        return $this->fetch('Product/class');
    }

    public function addclass(){
      if($_GET['id']){
        $id = $_GET['id'];
        $class = new GeeProductClass();
        $classInfo = $class->where('id = '.$id)->find();
    		$this->assign('info',$classInfo);
      }
      return $this->fetch('Product/addclass');
    }

    public function addclassAuth(){
    	$class = new GeeProductClass();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改产品组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
        unset($data['id']);
        foreach($data as $key=>$var){
            if(empty($var) && $var != '0'){
                unset($data[$key]);
            }
        }
    	} else {
        //如果是新增产品组
        $hasclass = $class->where('name = "'.$data['name'].'"')->find();
        if($hasclass){
            $ret['status'] = 422;
            $ret['msg'] = '分类标识已存在！';
            return json_encode($ret);
        }
      }
      if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '分类名称提交有误！';
    		return json_encode($ret);
    	}
      if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '分类标识提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$classres = $class->save($data,$w);
    		if($classres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了产品分类 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$classres = $class->save($data);
    		if($classres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了产品分类 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function delclass(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $product = new GeeProduct();
      $class = new GeeProductClass();
      $log = new GeeLog();
    	if(request()->isDelete()){
    		$id = request()->param()['id'];
        $items = $product->where('type = '.$id)->find();
        if($items){
          $ret['status'] = 422;
          $ret['msg'] = '该产品分类下还存在产品！';
          return json_encode($ret);
        }
        $delclass = $class->where('id = '.$id)->delete();
    		if($delclass){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了产品分类ID '.$id,
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
    public function vps(){
      $vps = new GeeVps();
      $addons = new GeeAddons();
      $plug= new \addons\vps\vps();
      $way = $addons->where('`range` = "vps" and `status` = 2')->find();
      //引入已启用的vps插件表
      $putData = [
        'function'=>'updateList',
        'data'=>[
          'user_id'=> '-1',
          'action'=>'',
          'data'=>[
          ]
        ]
      ];
      $plug->vps($putData);
      $list = $vps->order('id desc')->select();
      foreach($list as $k=>$v){
          $item = Db::name($v['plug_type'])->where('id = '.$v['pro_id'])->find();
          if($item){
            foreach($item as $key=>$var){
              if($key == 'id'){
                continue;
              }
              $v[$key] = $var;
            }
          }
      }
    	$this->assign('list',$list);
      return $this->fetch('Server/vps');
    }
    
    /**
     * 续费VPS
     */
    public function renewvps(){
      $data = $_POST;
      $class = new GeeProductClass();
      $group = new GeeProductGroup();
      $pro = new GeeProduct();
      $pc = new GeeProConfig();
      $billing = new GeeBilling();
      $log = new GeeLog();
      $plug= new \addons\vps\vps();
      $item = $pro->where('id = '.$data['pro_id'])->find();
      $pro_id = json_decode($item['plug_config'],true)['product_id'];
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      if((int)$data['pay_length'] / 12 >= 1){
        $years = number_format((int)$data['pay_length'] / 12,0);
      } else{
        $years = '0.'.$data['pay_length'];
      }
      if(!$data['type']){
        $action = 'activate';
        $datas = [
          'year'=> $years,
          'idc'=> '',
          'productid'=> $pro_id
        ];
        $type = 'create';
      } else if($data['type'] == 'renew'){
        $action = 'renew';
        $datas = [
          'year'=> $years,
          'vpsname'=> $data['name']
        ];
        $type = 'renew';
      } else if($data['type'] == 'update'){
        $action = 'update';
        $datas = [
          'vpsname'=> $data['name']
        ];
        $type = 'update';
      } else {
        $ret['status'] = 422;
        $ret['msg'] = '非法操作！';
        return $ret;
      }
      $plugAddr = '\addons\vps\vps';
      $putData = [
        'plug'=> $plugAddr,
        'class'=>'vps',
        'function'=>'control',
        'data'=>[
          'plug_id'=>$data['pro_id'],
          'user_id'=> session('_userInfo')['id'],
          'action'=>$action,
          'data'=>$datas,
          'attach'=>'',
          'openX'=>""
        ]
      ];
      // dump($putData);
      // exit;
      $items = $pro->where('id = '.$data['pro_id'])->find();
      $_SESSION['_create_putData'] = $putData;
      $pinfo = [
        [
          'group'=> $group->where('id = '.$items['group_id'])->value('name'),
          'class'=> $class->where('id = '.$items['type'])->value('title'),
          'name'=>$items['name'],
          'type'=>$type,
          'num'=>$data['num'],
          'config'=>$items['describe'],
          'years'=>$data['pay_length'],
        ]
      ];
      $_SESSION['_pro_info'] = $pinfo;
      // dump($_SESSION);
      foreach($_SESSION['_pro_info'] as $k=>$v){
        $str = $v['price'];
        //中文标点
        $char = ",。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";
        
        $pattern = array(
            '/['.$char.']/u', //中文标点符号
            '/[ ]{2,}/'
        );
      }
      $order_number = date('Ymdhis', time()) . rand(10000, 99999);
      
      $pcConfig['order_number'] = $order_number;
      $pcConfig['config'] = json_encode([
        '_create_putData' => $putData,
        '_pro_info' => $pinfo
      ]);

      $pcs = $pcConfig;
      $configs = json_decode($pcs['config'], true);
      $selfPro = $pinfo;
      // dump($info);
      // return ;
      if($configs['_create_putData']){
        $plug = new $configs['_create_putData']['plug']();
        $func = $configs['_create_putData']['class'];
        $putData = $configs['_create_putData']['data'];
        if (!$putData['function']) {
            $putData['function'] = $configs['_create_putData']['function'];
        }
        $res = $plug->$func($putData);
        
    		if($res){
    			$saveInfo = [
            'content' => $this->_adminInfo['name'].' 续费了 用户 '.$data['user'].' 的VPS主机 '.$data['name'],
            'ip' => get_ip()
          ];
          $logres = $log->save($saveInfo);
        }
      }
      return json_encode($ret);
    }
    /**
     * 控制面板
     */
    public function vpsmanager(){
      $data = $_GET;
      $plug= new \addons\vps\vps();
      $putData = [
        'function'=>'control',
          'plug_id'=>$data['pro_id'],
          'user_id'=> $this->_userInfo['id'],
          'action'=> 'manager',
          'data'=>[
            'vpsname'=>$data['vpsname'],
            'password'=>$data['password']
          ],
          'attach'=>'',
          'openX'=>""
      ];
      $res = $plug->vps($putData);
      // dump($res);
      echo $res;
    }

    public function domain(){
      $d = new GeeDomain();
      $dp = new GeeDomainPrice();
      $pro = new GeeProduct();
      $addons = new GeeAddons();
      $plug = new \addons\domain\domain();
      $list = $d->order('id')->select();
      foreach ($list as $k => $v) {
          $suffix = '.' . explode(".", $v['domainname'])[1];
          $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
          $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
          $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
          $putData = [
              'way' => $adninfo['name'],
              'pro_id' => $dpinfo['pro_id'],
              'function' => 'control',
              'action' => 'domainDetail',
              'data' => [
                  'domainname' => $v['domainname'],
              ],
          ];
          $adnres = $plug->domain($putData);
          $adnres = json_decode($adnres, true);
          $v['runstate'] = $adnres['data'][0]['runstate'];
          $v['dnvcstate'] = $adnres['data'][0]['dnvcstate'];
          $v['domaintype'] = $adnres['data'][0]['domaintype'];

          $d->where('id = ' . $v['id'])->update([
              'r_state' => $v['runstate'],
              'd_state' => $v['dnvcstate'],
              'domaintype' => $v['domaintype'],
              'domainpass' => $adnres['data'][0]['password'],
              'userid' => $adnres['data'][0]['userid'],
              'dns' => json_encode([
                  'dns1' => ['host' => $adnres['data'][0]['host1'], 'ip' => $adnres['data'][0]['hostip1']],
                  'dns2' => ['host' => $adnres['data'][0]['host2'], 'ip' => $adnres['data'][0]['hostip2']],
                  'dns3' => ['host' => $adnres['data'][0]['host3'], 'ip' => $adnres['data'][0]['hostip3']],
                  'dns4' => ['host' => $adnres['data'][0]['host4'], 'ip' => $adnres['data'][0]['hostip4']],
                  'dns5' => ['host' => $adnres['data'][0]['host5'], 'ip' => $adnres['data'][0]['hostip5']],
                  'dns6' => ['host' => $adnres['data'][0]['host6'], 'ip' => $adnres['data'][0]['hostip6']],
              ]),
              'end_time' => strtotime($adnres['data'][0]['ExpireTime']),
              'newstas' => $adnres['data'][0]['d_constt'],
              'isname' => $adnres['data'][0]['isNameDomain'],
          ]);
      }
      $this->assign('list', $list);
      return $this->fetch('Server/domain');
    }
    /**
     * 测试domain接口
     */
    public function domainchecked(){
      $p = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $addons = new GeeAddons();
      $plug= new \addons\domain\domain();
      $way = $addons->where('`range` = "domain" and `status` = 2')->find();

      $putData = [
        'function'=>'control',
        'user_id'=> session('_userInfo')['id'],
        'action'=>'checked',
        'plug_name'=> 'cndns',
        'data'=>[
        ]
      ];
      $res = $plug->domain($putData);
      // dump($res);
      return $res;
      // return json_encode($res);
    }
    
    public function domainprice(){
      $dp = new GeeDomainPrice();
      $pro = new GeeProduct();
      $prolist = $pro->where('type = "5"')->order('id desc')->select();
      // dump($prolist);
      $list = $dp->order('id desc')->select();
    	$this->assign('list',$list);
    	$this->assign('prolist',$prolist);
      return $this->fetch('Server/domainprice');
    }
    public function adddomainpriceauth(){
      $dp = new GeeDomainPrice();
      $p = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      foreach($p as $k=>$v){
          if((empty($v)) && $k != 'id' && $k != 'twelvemonth' && $k != 'biennia' && $k != 'triennium' && $k != 'quadrennium' && $k != 'lustrum' && $k != 'decade'){
            $ret['status'] = 422;
            $ret['msg'] = '域名价格信息不符合规范！请修改后再提交';
            return json_encode($ret);
            break;
          }
      }
      if(!$p['id']){
        unset($p['id']);
        $has = $dp->where('domain = "'.$p['domain'].'"')->find();
        if($has){
          $ret['status'] = 422;
          $ret['msg'] = '已存在相同的域名!请修改后重新提交!';
          return json_encode($ret);
        }
        $res = $dp->save($p);
      } else {
        $id = $p['id'];
        unset($p['id']);
        $res = $dp->where('id = '.$id)->update($p);
      }

      if(!$res){
        $ret['status'] = 422;
        $ret['msg'] = '网络错误!请稍后再试';
        return json_encode($ret);
      }
      return json_encode($ret);
    }
    public function domaintempaudit(){
      $d = new GeeDomain();
      $dp = new GeeDomainPrice();
      $dc = new GeeDomainContact();
      $list = $dc->select();

      //插件提交更新联系人信息
      $pro = new GeeProduct();
      $addons = new GeeAddons();
      $plug = new \addons\domain\domain();
      foreach ($list as $k => $v) {
          foreach (json_decode($v['contact_id'], true) as $key => $val) {
              $putData = [
                  'way' => $key,
                  'pro_id' => $val['pro_id'],
                  'function' => 'control',
                  'action' => 'contactList',
                  'data' => [
                      'userid' => $val['value'],
                  ],
              ];
              // dump($putData);
              $adnres = $plug->domain($putData);
              // dump(json_decode($adnres,true));
              $v['ischecked'] = json_decode($adnres, true)['data'][0]['ischecked'];
              $v['status'] = json_decode($adnres, true)['data'][0]['isforbidden'];
              $dc->where('id = ' . $v['id'])->update(['ischecked' => json_decode($adnres, true)['data'][0]['ischecked'], 'status' => json_decode($adnres, true)['data'][0]['isforbidden']]);
          }
      }

      $this->assign('list', $list);
      return $this->fetch('Server/domaintempaudit');
    }
}

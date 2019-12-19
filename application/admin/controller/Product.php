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
use app\admin\model\GeeServerAdded; // 增值服务组表
use app\admin\model\GeeServerAddedItems; // 增值服务子项表
use app\index\model\GeeWebroute; // 前台路由表
use app\admin\model\GeeMsgmodel; // 邮件模板表
use app\admin\model\GeeAddons; // 邮件模板表
use app\admin\model\GeeOsgroup; // 操作系统表
use app\admin\model\GeeOstype; // 操作系统版本表


class Product extends Common
{
    public function index()
    {
        return $this->redirect('admin/Product/list');
    }
    public function list()
    {
      $product = new GeeProduct();
      $group = new GeeProductGroup();
    	$productList = $product->order('sort desc,id desc')->select();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$productList);
    	$this->assign('grouplist',$groupList);
      return $this->fetch('Product/index');
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
      $added = new GeeServerAdded();
      $addedlist = $added->select();
      $this->assign('added',$addedlist);
      
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
    	// if(!isset($data['email_model']) || empty($data['email_model'])){
    	// 	$ret['status'] = 422;
    	// 	$ret['msg'] = '邮件模板ID提交有误！';
    	// 	return json_encode($ret);
    	// }
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
      $class = new GeeProductClass();
      $classlist = $class->order('id')->select();
      $this->assign('classlist',$classlist);
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
    
    public function added()
    {
      $item = new GeeServerAddedItems();
      $group = new GeeServerAdded();
    	$itemList = $item->order('id desc')->select();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$itemList);
      $this->assign('grouplist',$groupList);
      return $this->fetch('Product/added');
    }
    public function addaddedgroup(){
    	if($_GET['id']){
        $id = $_GET['id'];
    		$group = new GeeServerAdded();
        $groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}
      return $this->fetch('Product/addaddedgroup');
    }

    public function addaddedgroupAuth(){
    	$group = new GeeServerAdded();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改增值服务组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	} else {
        //如果是新增增值服务组
        $hasGroup = $group->where('name = "'.$data['name'].'"')->find();
        if($hasGroup){
            $ret['status'] = 422;
            $ret['msg'] = '增值服务组已存在！';
            return json_encode($ret);
        }
    	}
		  if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '增值服务组标识提交有误！';
    		return json_encode($ret);
    	}
		  if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '增值服务名称提交有误！';
    		return json_encode($ret);
    	}
		  if(!isset($data['type']) || empty($data['type'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '增值服务类型提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$groupres = $group->save($data,$w);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了增值服务组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了增值服务组 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function deladdedgroup(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $product = new GeeServerAddedItems();
      $group = new GeeServerAdded();
      $log = new GeeLog();
    	if(request()->isDelete()){
    		$id = request()->param()['id'];
        $items = $product->where('group_id = '.$id)->find();
        if($items){
          $ret['status'] = 422;
          $ret['msg'] = '该分组下还存在增值服务项！';
          return json_encode($ret);
        }
        $delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了增值服务组ID '.$id,
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
    
    public function addadded(){
    	$group = new GeeServerAdded();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('groupList',$groupList);
      $product = new GeeServerAddedItems();
      
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$productInfo = $product->where('id = '.$id)->find();
        $this->assign('info',$productInfo);
    	}
        return $this->fetch('Product/addadded');
    }
    //产品数据提交操作
    public function addaddedAuth(){
      $product = new GeeServerAddedItems();
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
		  if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '增值服务名称提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['month']) || $data['month'] < 0){
    		$ret['status'] = 422;
    		$ret['msg'] = '月价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['quarter']) || $data['quarter'] < 0){
    		$ret['status'] = 422;
    		$ret['msg'] = '季度价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['semestrale']) || $data['semestrale'] < 0){
    		$ret['status'] = 422;
    		$ret['msg'] = '半年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['years']) || $data['years'] < 0){
    		$ret['status'] = 422;
    		$ret['msg'] = '年价格提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['sort']) || empty($data['sort'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '排序提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$productres = $product->save($data,$w);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了增值服务 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$productres = $product->save($data);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了增值服务 '.$data['name'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function deladded(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isDelete()){
    		$product = new GeeServerAddedItems();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delProduct = $product->where('id = '.$id)->delete();
    		if($delProduct){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了增值服务ID '.$id,
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

    public function os()
    {
      $item = new GeeOstype();
      $group = new GeeOsgroup();
    	$itemList = $item->order('id desc')->select();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('list',$itemList);
      $this->assign('grouplist',$groupList);
      return $this->fetch('Product/os');
    }
    public function addosgroup(){
    	if($_GET['id']){
        $id = $_GET['id'];
    		$group = new GeeOsgroup();
        $groupInfo = $group->where('id = '.$id)->find();
    		$this->assign('info',$groupInfo);
    	}
      return $this->fetch('Product/addosgroup');
    }

    public function addosgroupAuth(){
    	$group = new GeeOsgroup();
		  $log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改操作系统组
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	} else {
        //如果是新增系统版本
        $hasGroup = $group->where('title = "'.$data['title'].'"')->find();
        if($hasGroup){
            $ret['status'] = 422;
            $ret['msg'] = '操作系统已存在！';
            return json_encode($ret);
        }
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$groupres = $group->save($data,$w);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了操作系统 '.$data['title'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$groupres = $group->save($data);
    		if($groupres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了操作系统 '.$data['title'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function delosgroup(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      $product = new Geeostype();
      $group = new Geeosgroup();
      $log = new GeeLog();
    	if(request()->isDelete()){
    		$id = request()->param()['id'];
        $items = $product->where('group_id = '.$id)->find();
        if($items){
          $ret['status'] = 422;
          $ret['msg'] = '该分组下还存在操作系统版本！';
          return json_encode($ret);
        }
        $delGroup = $group->where('id = '.$id)->delete();
    		if($delGroup){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了操作系统ID '.$id,
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
    
    public function addostype(){
    	$group = new Geeosgroup();
    	$groupList = $group->order('id desc')->select();
    	$this->assign('groupList',$groupList);
      $product = new Geeostype();
      
    	if($_GET['id']){
    		$id = $_GET['id'];
    		$productInfo = $product->where('id = '.$id)->find();
        $this->assign('info',$productInfo);
    	}
        return $this->fetch('Product/addostype');
    }
    //产品数据提交操作
    public function addostypeAuth(){
      $product = new Geeostype();
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
		  if(!isset($data['title']) || empty($data['title'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '操作系统名称提交有误！';
    		return json_encode($ret);
    	}
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$productres = $product->save($data,$w);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了操作系统 '.$data['title'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$productres = $product->save($data);
    		if($productres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了操作系统 '.$data['title'],
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }

    public function delostype(){
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	if(request()->isDelete()){
    		$product = new Geeostype();
    		$log = new GeeLog();
    		$id = request()->param()['id'];
    		$delProduct = $product->where('id = '.$id)->delete();
    		if($delProduct){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 删除了操作系统ID '.$id,
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

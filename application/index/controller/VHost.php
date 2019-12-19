<?php
namespace app\index\controller;
use app\index\model\GeeProductVHost; //产品表-vhost
class VHost extends Common
{
    public function index()
    {

        return $this->fetch('VHost/index');
    }



    public function add(){
    	$vhost = new GeeProductVHost();
    	$groups = $vhost -> getGroup();

    	if(empty($groups) || count($groups) == 0){
    		$products = [];
    	} else {
    		$products = $vhost -> getProductByGroup($groups[0]['id']);
    	}

    	$this->assign('groups', $groups);
    	$this->assign('products', $products);
        return $this->fetch('VHost/add');
    }

    public function getProducts(){
    	$id = $_GET['id'];
    	if(empty($id)){
    		return json_encode([
                'code' => 1006,
                'msg' => 'Parameter error'
            ]);
    	}
      	$vhost = new GeeProductVHost();
      	$products = $vhost -> getProductByGroup($id);
      	return json_encode([
            'code' => 0,
            'msg' => 'ok',
            'data' => $products
        ]);
    }

    public function getPrice(){
        $id = $_GET['id'];
        $len = $_GET['len'];
        if(empty($id) || empty($len)){
            return json_encode([
                'code' => 1006,
                'msg' => 'Parameter error'
            ]);
        }
        $vhost = new GeeProductVHost();
        $data = $vhost -> getProductPrice($id, $len);
        if(empty($data)){
            return json_encode([
                'code' => 1012,
                'msg' => 'source not already exist'
            ]);
        }
        return json_encode([
            'code' => 0,
            'msg' => 'ok',
            'data' => $data
        ]);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/6
 * Time: 11:21
 */
namespace app\member\controller;
use think\Db;
use think\Session;
class Index extends Common {
    public function index(){
        $userid=Session::get('home')['userid'];
        $this->assign('userid',$userid);
        return view();
    }


}
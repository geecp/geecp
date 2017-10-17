<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;

use think\Db;
use think\Session;

class Error extends Base
{
    //二级菜单权限打开,但是子类菜单都没赋予权限的处理
    public function index()
    {
        $id=Session::get('id');
        //左侧菜单
         $name=Db::name('right')->where('id',$id)->field('munu')->find()['munu'];
         $this->assign('name',$name);

         $right='维护中';
         $this->assign('right',$right);
        return view();
    }
}
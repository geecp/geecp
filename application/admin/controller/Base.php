<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;

class Base extends Controller
{
    public function _initialize()
    {
//        if (empty(input('session.uid'))) {
//            //判断如果没有登录,则跳转到login模块/index/index
//            return $this->redirect('login/index/index');
//            exit;
//        }

        if(Session::get('admin')['username']==''){
            $this->redirect('Login/index');
        }

        //获取当前用户所在用户组的权限
        $adm_group=Db::name('admgroup')->where('id',Session::get('admin')['adm_group'])->find()['authority'];
        //获取当前登录的用户名
        $user=Session::get('admin');
        $this->assign('user',$user);

        //根据用户当前的用户组，获得左侧菜单栏
        $menu=Db::name('right')->whereIn('id',$adm_group)->where('pid',0)->order('num')->select();
        //将目录传到前台
        $this->assign('menu',$menu);

        $this->assign('admin',Session::get('admin'));
        $id=input('lid');
        if (is_numeric($id)){
            Session::set('id',$id);
        }else{
            $id=Session::get('id');
        }
        $right=Db::name('right')->whereIn('id',$adm_group)->where('pid',Session::get('id'))->order('num')->select();
        Session::set('right',$right);
        $this->assign('right',$right);
        $num=input('num');
        if (is_numeric($num)){
            $num=input('num');
            Session::set('num',$num);
        }else{
           $num=1;
            Session::set('num',$num);
        }

        $name=Db::name('right')->where('id',$id)->field('munu')->find()['munu'];
        $this->assign('name',$name);
    }
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->view->replace(['__PUBLIC__' => '/static',]);
    }

    //验证工码正确不正确
    public function yznum()
    {
        $worknum=md5(input('post.worknum'));
        $num=md5(Session::get('admin')['worknum']);

        if ($worknum == $num){
            return json([ 'msg' =>1]);
        }else{
            return json([ 'msg' =>2]);
        }
    }

}
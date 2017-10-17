<?php
namespace app\member\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Common extends Controller {
    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->view->replace(['__PUBLIC__' => '/static',]);
    }

    public function is_login(){
        if(Session::get('home')['userid']==''){
            $this->redirect('Login/login');
        }else{
            $session = session('home');
            $this->assign('res',Session::get('home'));
            $sum = Db::name('messagetemp')->where('is_published AND is_read = 0')->count();
            $this->assign('sumMessagetemp',$sum);
        }
    }





}
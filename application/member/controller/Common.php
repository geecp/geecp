<?php
namespace app\member\controller;
use think\Controller;
use think\Db;
use think\Session;
class Common extends Controller {
    public function __construct()
    {
        //判断session是否存在
        if(Session::get('home')['userid']==''){
            $this->redirect("login/index");
        }

        parent::__construct();
        $this->view->replace(['__PUBLIC__' => './static',]);


    }

}
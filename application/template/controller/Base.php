<?php
namespace app\template\controller;
use think\Controller;
use think\Request;

class Base extends Controller
{

    public function __construct(Request $request = null)
    {

        parent::__construct($request);
        $this->view->replace(['__PUBLIC__' => '/static/plugins/themes/nitian',]);

        define('FRAMEWORK_PATH',__DIR__.'/tp/');
    }
}

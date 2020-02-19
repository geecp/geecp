<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use \think\Route;
use app\admin\model\GeeRoute; // 后台路由表
use app\index\model\GeeWebroute; // 前台控制台路由表
use app\home\model\GeeHomeroute; // 前台模板路由表
$webRoute = new GeeWebroute();
$homeRoute = new GeeHomeroute();
/**
 * 公共路由
 */
//单图上传
Route::rule('uploadImg','admin/Common/uploadimg');
//邮件发送
Route::rule('sendEmail','admin/Common/sendEmail');
Route::rule('/api/return_url','index/Api/return_url');
Route::rule('/api/notify_url','index/Api/notify_url');

/**
 *  插件路由
 */
//虚拟主机
Route::rule('manager/vhost/index','admin/Vhosts/index');
Route::rule('manager/vhost/add','admin/Vhosts/add');
Route::rule('manager/vhost/addAuth','admin/Vhosts/addAuth');
Route::rule('manager/vhost/del','admin/Vhosts/del');
Route::rule('manager/vhost/group','admin/Vhosts/group');
Route::rule('manager/vhost/addgroup','admin/Vhosts/addgroup');
Route::rule('manager/vhost/addgroupAuth','admin/Vhosts/addgroupAuth');
Route::rule('manager/vhost/delgroup','admin/Vhosts/delgroup');
Route::rule('manager/vhost/vhost','admin/Vhosts/vhost');
Route::rule('manager/vhost/addvhost','admin/Vhosts/addvhost');
Route::rule('manager/vhost/addvhostAuth','admin/Vhosts/addvhostAuth');
Route::rule('manager/vhost/syncvhost','admin/Vhosts/syncvhost');

//VPS通用操作(前台)
// Route::rule('vps/index','index/Vpss/index');
// Route::rule('vps/add','index/Vpss/add');
// Route::rule('vps/addAuth','index/Vpss/addAuth');
// Route::rule('vps/up','index/Vpss/up');
// Route::rule('vps/del','index/Vpss/del');
// Route::rule('vps/renew','index/Vpss/renew');
// Route::rule('vps/on','index/Vpss/on');
// Route::rule('vps/off','index/Vpss/off');
// Route::rule('vps/changepass','index/Vpss/changepass');
// Route::rule('vps/getProItem','index/Vpss/getProItem');
// Route::rule('vps/getPrice','index/Vpss/getPrice');
// Route::rule('vps/manager','index/Vpss/manager');

// $webPlugRoute = $webRoute->where('f_id = 0 and is_plug != ""')->select();
// foreach ($webPlugRoute as $key => $var) {
// 	Route::rule('/'.$var['code'],$var['is_plug'].'/'.ucwords($var['code']).'/index');
// 	Route::rule('/'.$var['code'].'/index',$var['is_plug'].'/'.ucwords($var['code']).'/index');
// 	Route::rule('/'.$var['code'].'/list',$var['is_plug'].'/'.ucwords($var['code']).'/index');
// 	$webPlugRouteRes = $webRoute->where('`f_id` = '.$var['id'])->select();
// 	foreach ($webPlugRouteRes as $k => $v) {
// 		Route::rule('/'.$var['code'].'/'.$v['code'],$var['is_plug'].'/'.ucwords($var['code']).'/'.$v['code']);
// 	}
// }

//vps操作
Route::rule('console/vps/add','index/Vps/add');
Route::rule('console/vps/addAuth','index/Vps/addAuth');
Route::rule('console/vps/up','index/Vps/up');
Route::rule('console/vps/del','index/Vps/del');
Route::rule('console/vps/del','index/Vps/release');
Route::rule('console/vps/renew','index/Vps/renew');
Route::rule('console/vps/on','index/Vps/on');
Route::rule('console/vps/off','index/Vps/off');
Route::rule('console/vps/changepass','index/Vps/changepass');
Route::rule('console/vps/getProItem','index/Vps/getProItem');
Route::rule('console/vps/getPrice','index/Vps/getPrice');
Route::rule('console/vps/manager','index/Vps/manager');

/**
 * 前台用户端控制台路由
 */
//首页
Route::rule('console/','index/Index/index');
Route::rule('console/index','index/Index/index');
//根据路由表生成管理端路由
$webStartRoute = $webRoute->select();
// dump($webStartRoute);
foreach ($webStartRoute as $key => $var) {
  if($var['f_id'] == 0){
    Route::rule('console/'.$var['code'] ,'index/'.ucwords($var['code']).'/index');
    Route::rule('console/'.$var['code'].'/index' ,'index/'.ucwords($var['code']).'/index');
  } else {
    $froute = $webRoute->where('`id` = '.$var['f_id'])->find();
    Route::rule('console/'.$froute['code'].'/'.$var['code'],'index/'.ucwords($froute['code']).'/'.$var['code']);
    Route::rule('console/'.$froute['code'].'/'.$var['code'],'index/'.ucwords($froute['code']).'/'.$var['code']);
  }
}

/**
 * 前台用户端路由
 */
// 登录页面
Route::rule('/login','index/Login/index');
// 登录操作		
Route::rule('/auth','index/Login/auth');
// 注册页面
Route::rule('/register','index/Login/register');
// 注册操作		
Route::rule('/regauth','index/Login/regauth');
//注册协议
Route::rule('/regdeal','index/Login/regdeal');
// 忘记密码页面
Route::rule('/forget','index/Login/forget');
// 忘记密码操作		
Route::rule('/forgetauth','index/Login/forgetauth');
// 退出
Route::rule('/logout','index/Login/logout');
//首页
Route::rule('/','home/Index/index');
Route::rule('/index','home/Index/index');
//根据路由表生成管理端路由
$homeStartRoute = $homeRoute->select();
// dump($webStartRoute);
foreach ($homeStartRoute as $key => $var) {
  if($var['f_id'] == 0){
    Route::rule('/'.$var['code'] ,'home/'.ucwords($var['code']).'/index');
    Route::rule('/'.$var['code'].'/index' ,'home/'.ucwords($var['code']).'/index');
  } else {
    $froute = $homeRoute->where('`id` = '.$var['f_id'])->find();
    Route::rule('/'.$froute['code'].'/'.$var['code'],'home/'.ucwords($froute['code']).'/'.$var['code']);
    Route::rule('/'.$froute['code'].'/'.$var['code'],'home/'.ucwords($froute['code']).'/'.$var['code']);
  }
}

/**
 * 后台管理端路由
 */

/**
 * 登录相关操作
 */
// 登录页面
Route::rule('manager/login','admin/Login/index');
// 登录操作		
Route::rule('manager/auth','admin/Login/auth');
// 退出
Route::rule('manager/logout','admin/Login/logout');

/*
 * 管理端首页
 */
// 管理端首页
Route::rule('manager','admin/Login/index');
// 管理端首页
Route::rule('manager/index','admin/Index/index');
//根据路由表生成管理端路由
$route = new GeeRoute();
$startRoute = $route->where('`f_id` = 0 and `is_show` = "1"')->select();
foreach ($startRoute as $key => $var) {
	Route::rule('manager/'.$var['code'],'admin/'.ucwords($var['code']).'/index');
	Route::rule('manager/'.$var['code'].'/index','admin/'.ucwords($var['code']).'/index');
	$routeRes = $route->where('`f_id` = '.$var['id'])->select();
	foreach ($routeRes as $k => $v) {
		Route::rule('manager/'.$var['code'].'/'.$v['code'],'admin/'.ucwords($var['code']).'/'.$v['code']);
	}
}
// 用户新增
Route::rule('manager/user/useradd','admin/User/add');
// 用户新增验证API
Route::rule('manager/user/useraddAuth','admin/User/addAuth');
// 用户删除
Route::rule('manager/user/del','admin/User/del');
// 用户状态
Route::rule('manager/user/disabled','admin/User/disabled');
// 用户组新增
Route::rule('manager/user/groupadd','admin/User/addgroup');
// 用户组新增验证API
Route::rule('manager/user/groupaddAuth','admin/User/addgroupAuth');
// 用户组删除
Route::rule('manager/user/delgroup','admin/User/delgroup');


// 员工新增
Route::rule('manager/staff/staffadd','admin/Staff/add');
// 员工新增验证API
Route::rule('manager/staff/staffaddAuth','admin/Staff/addAuth');
// 员工删除
Route::rule('manager/staff/del','admin/Staff/del');
// 员工状态
Route::rule('manager/staff/disabled','admin/Staff/disabled');
// 员工组新增
Route::rule('manager/staff/groupadd','admin/Staff/addgroup');
// 员工组新增验证API
Route::rule('manager/staff/groupaddAuth','admin/Staff/addgroupAuth');
// 员工组删除
Route::rule('manager/staff/delgroup','admin/Staff/delgroup');


// 基本信息新增验证API
Route::rule('manager/system/basicAuth','admin/System/basicAuth');
// SMTP邮件配置新增验证API
Route::rule('manager/system/emailAuth','admin/System/emailAuth');
// 附件设置新增验证API
Route::rule('manager/system/annexAuth','admin/System/annexAuth');
// 消息模板新增
Route::rule('manager/system/addtemplate','admin/System/addtemplate');
// 消息模板新增验证API
Route::rule('manager/system/addtemplateAuth','admin/System/addtemplateAuth');
// 消息模板删除
Route::rule('manager/system/deltemplate','admin/System/deltemplate');
// 消息模板状态操作
Route::rule('manager/system/disatemplate','admin/System/disatemplate');


// 产品新增
Route::rule('manager/product/add','admin/Product/add');
// 产品新增验证API
Route::rule('manager/product/addAuth','admin/Product/addAuth');
// 产品删除
Route::rule('manager/product/del','admin/Product/del');
// 产品组新增
Route::rule('manager/product/addgroup','admin/Product/addgroup');
// 产品组新增验证API
Route::rule('manager/product/addgroupAuth','admin/Product/addgroupAuth');
// 产品组删除
Route::rule('manager/product/delgroup','admin/Product/delgroup');
// 获取插件配置项
Route::rule('manager/product/getPlugConfig','admin/Product/getPlugConfig');
// 产品类型新增
Route::rule('manager/product/addtype','admin/Product/addtype');
// 产品类型新增验证API
Route::rule('manager/product/addtypeAuth','admin/Product/addtypeAuth');
// 产品类型删除
Route::rule('manager/product/deltype','admin/Product/deltype');


// 插件安装
Route::rule('manager/addons/install','admin/Addons/install');
// 插件卸载
Route::rule('manager/addons/uninstall','admin/Addons/uninstall');
// 插件启用
Route::rule('manager/addons/on','admin/Addons/on');
// 插件禁用
Route::rule('manager/addons/off','admin/Addons/off');
// 插件配置
Route::rule('manager/addons/edit','admin/Addons/edit');

// 系统更新确认
Route::rule('manager/cloudservice/confirm','admin/Cloudservice/confirm');
//系统检查变动文件
Route::rule('manager/cloudservice/checkfile','admin/Cloudservice/checkfile');
//系统检查版本
Route::rule('manager/cloudservice/checkversion','admin/Cloudservice/checkversion');

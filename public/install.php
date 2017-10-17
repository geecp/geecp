<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/2
 * Time: 23:30
 */
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define ( 'BIND_MODULE','install');

if(!is_writable('../runtime')){
	echo '<h3>./runtime 目录不可写</h3>';
	echo '权限更改之后请刷新当前页';
}else{
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
}



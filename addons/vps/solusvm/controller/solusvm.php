<?php
// +----------------------------------------------------------------------
// | 
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.qiduo.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: free < 185291445@qq.com>
// +----------------------------------------------------------------------
// namespace addons\vhost;
use think\Controller;
use think\Db;
use SolusVm\SolusVm;
class solusvm extends Controller
{
	private $SV;
	public function __construct()
	{
		// 从数据库获取配置信息，可以将配置信息json_encode 放在一个字段里面，这里解析出来
		// 这里我用的死数据
		$config=[
        	'url' => '',
        	'id '=> 'jbFEUe909KwoVzplWwL9x0KW1J7Ww17yvziAxiyw',
        	'key' => 'TVLEl1Q8nq7LqHwhfs4Cj12Gpa7Niva3gLwsBcqP'

		];

		$this->SV = new SolusVm($config['url'], $config['id'], $config['key']);
	}

	public function reboot($serverID)
	{
		return $this->SV->reboot($serverID);
	}

	public  function servicelist()
	{

		return $this->SV->listClients();
	}
}
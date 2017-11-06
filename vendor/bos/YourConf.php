<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 16:07
 */// 报告所有 PHP 错误

error_reporting(-1);

define('__BOS_CLIENT_ROOT', dirname(__DIR__));

// 设置BosClient的Access Key ID、Secret Access Key和ENDPOINT
$BOS_TEST_CONFIG =
    array(
        'credentials' => array(
            'ak' => 'e18f6506444c4b52b4f09d27ec2672ff',
            'sk' => '9e7801d374e34327ab450a7525a72925',
        ),
        'endpoint' => 'img.woliao.me',
        'protocol' => 'https',


    );

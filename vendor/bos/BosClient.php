<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/7
 * Time: 11:17
 */
include VENDOR_PATH . 'bos/BaiduBce.phar';

use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\Time;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\BosOptions;

//调用配置文件中的参数
class BaiduBos
{
    function upload($bucketName, $objectKey, $fileName,$BOS_TEST_CONFIG)
    {//百度bos 上传
        /*$BOS_TEST_CONFIG =
            array(
                'credentials' => array(
                    'ak' => 'e18f6506444c4b52b4f09d27ec2672ff',
                    'sk' => '9e7801d374e34327ab450a7525a72925',
                ),
                'endpoint' => 'http://bj.bcebos.com',
            );*/

//新建BosClient

        $client = new BosClient($BOS_TEST_CONFIG);
        $options = array(
            BosOptions::CONTENT_TYPE => "image/jpeg",
        );

        $client->putObjectFromFile($bucketName, $objectKey, $fileName, $options);

    }

    function download($bucket, $key,$BOS_TEST_CONFIG)
    {//百度bos返回下在地址
        $BOS_TEST_CONFIG =

        //新建BosClient
        //$bucket = '';
        $client = new BosClient($BOS_TEST_CONFIG);
        $url = $client->generatePreSignedUrl($bucket, $key);
        return $url;
    }

    function delete($bucket, $key,$BOS_TEST_CONFIG)
    {//删除Bos上的图片


        $client = new BosClient($BOS_TEST_CONFIG);
        $client->deleteObject($bucket, $key);

        $status = $this->download($bucket, $key);
        return $status;
    }
}


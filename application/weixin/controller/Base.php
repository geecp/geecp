<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/18
 * Time: 15:28
 */
namespace app\weixin\controller;
use app\weixin\model\Pay;
use think\Controller;
use think\Session;

date_default_timezone_set('PRC');
class Base extends Controller
{
    //获取Access_token
    public function base()
    {
        $pay=Pay::get();
        $app_id=$pay->WxpayAPPID;
        $app_secret=$pay->WxpayAPPSECRET;
        $token_url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$app_id."&secret=".$app_secret;
        $data=httpGet($token_url);
        return $data;
    }

    //获取模板列表
    public function getTempList()
    {
        $res=$this->base();
        $temp_url='https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$res['access_token'];
        $data['template_list']=httpGet($temp_url);
        $data['access_token']=$res['access_token'];
        return $data;
    }

    //获取用户列表
    public function getUserList()
    {
        $res=$this->base();
        $url='https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$res['access_token'].'&next_openid=';
        $rest=httpGet($url);
        return $rest;
    }

    //获取用户信息
    public function getUserInfo($data)
    {
        $res=$this->base();
        $openid=$data;
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$res['access_token'].'&openid='.$openid.'&lang=zh_CN';
        $rest=httpGet($url);
        return $rest;
    }
}
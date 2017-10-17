<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10
 * Time: 21:04
 */
namespace app\api\Controller;
use think\Controller;
class Sms extends Controller
{
   //短信发送
    public function sendSMS()
    {
        //接收数据
        $data['data']['userid']=input('post.userid/s','','htmlspecialchars');
        $data['data']['keyid']=input('post.keyid/s','','htmlspecialchars');
        $data['data']['key']=input('post.key/s','','htmlspecialchars');
        $data['data']['phone']=input('post.phone/s','','htmlspecialchars');
        $data['data']['appid']=input('post.appid/s','','htmlspecialchars');
        $data['data']['tempid']=input('post.tempid/s','','htmlspecialchars');
        $data['data']['code']=input('post.code/s','','htmlspecialchars');
        $sms=new \addons\sms\sms();
        $data['function']='sendSMS';
        $res=$sms->sms($data);
        return $res;
    }
}
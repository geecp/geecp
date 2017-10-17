<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 15:49
 */
namespace app\api\model;
use think\Model;
use app\api\model\BaiduSmsClient;
class Smstime extends Model
{
    public static function sendSms()
    {
        $phone=substr(input('post.phone/s','','htmlspecialchars'),3);
        $userid=input('post.uid/s','','htmlspecialchars');
        $auth=Userlist::getByUserid($userid);
        if($auth){
            $config = array(
                'endPoint' => 'sms.bj.baidubce.com',
                'accessKey' => '',
                'secretAccessKey' => '',
            );

            $smsClient = new BaiduSmsClient($config);
            $code=rand(100000,999999);
            $message = array(
                "invokeId" => "",
                "phoneNumber" => substr(input('post.phone'),3),
                "templateCode" => "smsTpl:",
                "contentVar" => array(
                    "code" =>  (string)$code,
                ),
            );
            $rest=Smstime::getByPhone($phone);
            $newtime=date('Y-m-d H:i:s',time());
            if($rest){
                if($rest->time<$newtime){
                    //距离上次发送验证码时间已超过10分钟
                    $ret = $smsClient->sendMessage($message);
                    $resu = json_decode( json_encode($ret),true);
                    Smstime::create([
                        'phone'=>$phone,
                        'time'=>date("Y-m-d H:i:s", strtotime("+10 minute")),
                        'count'=>1,
                        'code'=>$code
                    ]);
                    if($resu['code']!='1000'){
                        $code='ERROR';
                    }else{
                        $code=1;
                    }
                }else if($rest->count<2 && $rest->time>$newtime){
                    //距离上次时间10分钟之类，并且次数未达到5次
                    $ret = $smsClient->sendMessage($message);
                    $resu = json_decode( json_encode($ret),true);
                    Smstime::update([
                        'count'=>$rest->count+1,
                        'code'=>$code
                    ],['phone'=>$phone]);
                    if($resu['code']!='1000'){
                        $code='ERROR';
                    }else{
                        $code=1;
                    }
                }else{
                    Smstime::update([
                        'time'=>date("Y-m-d H:i:s", strtotime("+1 hour"))
                    ],['phone'=>$phone]);
                    $code='LOCK';
                }
            }else{
                //第一次发送
                $ret = $smsClient->sendMessage($message);
                $resu = json_decode( json_encode($ret),true);
                Smstime::create([
                    'phone'=>$phone,
                    'time'=>date("Y-m-d H:i:s", strtotime("+10 minute")),
                    'count'=>1,
                    'code'=>$code]);
                if($resu['code']!='1000'){
                    $code='ERROR';
                }else{
                    $code=1;
                }

            }
        }else{
            $code='ERROR';
        }
        return $code;
    }
}
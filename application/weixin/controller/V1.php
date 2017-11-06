<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/18
 * Time: 15:28
 */
namespace app\weixin\controller;
use app\weixin\model\Userlist;
use app\weixin\model\Wxopen;

class V1 extends Base
{
    //域名到期
    public function Remind()
    {
        $token=$this->base();
        //接受数据
        $data=input('post.');
        $user=Wxopen::getByUserid($data['userid']);
        if($user['openid']){
            $code['touser']=$user['openid'];
            //生成跳转url
            $redirect_uri=urlencode("https://".$_SERVER['SERVER_NAME']."/weixin/Oauth/oauth2");
            $jumpUrl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1dfeb43f767c2594&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

            //获取所要发送数据
            $msg=new MessageTemp();
            $message=$msg->template($data);
            if($message['template_id']){
                $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token['access_token'];
                $code['template_id']=$message['template_id'];
                $code['url']=$jumpUrl;
                $code['data']=$message['array'];
                $rest=postCurl($url,json_encode($code,JSON_UNESCAPED_UNICODE));
            }
            //删除当前所使用的模板
            $url2="https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=".$token['access_token'];
            $post_data['template_id']=$message['template_id'];
            $res=postCurl($url2,json_encode($post_data,JSON_UNESCAPED_UNICODE));
        }
    }

}
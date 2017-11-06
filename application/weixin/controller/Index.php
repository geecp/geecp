<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/19
 * Time: 17:25
 */
namespace app\weixin\controller;
use app\weixin\model\Wxopen;
use think\Db;
class Index extends Base
{
    public function index()
    {
        $res=$this->base();
        //生成二维码
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$res['access_token'];
        $data=[
            'expire_seconds'=>302400,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>'801111491'
                ]
            ]
        ];
        $this->assign('userid','801111491');
        $data=json_encode($data,JSON_UNESCAPED_UNICODE);
        $result=postCurl($url,$data);
        $ticket=urlencode($result['ticket']);
        $code_url='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        $this->assign('url',$code_url);
        $this->assign('durl','https://'.$_SERVER['SERVER_NAME'].'/member/index/index.html#/ucenter');
        return view();
    }


    public function getMessage()
    {
        $nonce     = input('nonce');
        $token     = 'gee';
        $timestamp = input('timestamp');
        $echostr   = input('echostr');
        $signature = input('signature');
        if($echostr) {
            //形成数组，然后按字典序排序
            $array = array();
            $array = array($token, $timestamp,$nonce);
            sort($array,SORT_STRING);
            //拼接成字符串,sha1加密 ，然后与signature进行校验
            $str = sha1( implode('',$array ) );
            if( $str == $signature && $echostr ){
                ob_clean();
                echo  $echostr;

            }
        }else{
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $scene_id = str_replace("qrscene_", "", $postObj->EventKey);

            $openid = $postObj->FromUserName; //openid
            $ToUserName = $postObj->ToUserName;  //转换角色
            $Event = strtolower($postObj->Event);
            $user=$this->getUserInfo($openid);
            $wxopen=new Wxopen([
                'userid'=>$scene_id,
                'nickname'=>$user['nickname'],
                'openid'=>(string)$openid,
                'follow_time'=>date('Y-m-d H:i:s',time())
            ]);
            $one=Wxopen::getByUserid($scene_id);
            if($scene_id){
                if(!$one){
                    $res=$wxopen->save();
                }
            }
        }

    }


    //轮询是否已绑定
    public function followed()
    {
        //接受userid查询是否关注公众号
        $userid=input('post.userid');
        $result=Wxopen::getByUserid($userid);
        if($result){
            echo 1;
        }else{
            echo 2;
        }
    }
}



function looger($contents){
    file_put_contents('log.html',date('Y-m-d H:i:s',time()).$contents.'<br/>',FILE_APPEND);
}

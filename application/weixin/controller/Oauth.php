<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 17:31
 */
namespace app\weixin\controller;
use app\weixin\model\Pay;
use app\weixin\model\ProductPrice;
use app\weixin\model\Serverhost;
use app\weixin\model\VpsProduct;
use qpp\weixin\model\Vhostproduct;
use think\Controller;
use app\weixin\model\Wxopen;
use app\weixin\model\Userlist;
use think\Db;
use think\Session;

class Oauth extends Controller
{
    public function oauth2()
    {
        $pay=Pay::get(1);
        $code=input('code/s','htmlspecialchars');
        if($code){
            $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$pay->WxpayAPPID."&secret=".$pay->WxpayAPPSECRET."&code=".$code."&grant_type=authorization_code ";
            $resturl=httpGet($url);
            if(isset($resturl['openid'])){
                $openid=$resturl['openid'];
                //根据openid获取用户
                $userid=Wxopen::getByOpenid($openid);
                $user=Userlist::getByUserid($userid->userid);
                //获取当前客户即将到期的产品，查询所有产品的状态，最近要过期的一个
                $nowtime=date("Y-m-d H:i:s",time());
                $onemonth=date("Y-m-d H:i:s",strtotime('+1 month'));
                $array=['Domain','Vhostlist','Vps','Rentserver'];
                foreach ($array as $k=>$v){
                    $where=[
                        'userid'=>$user->id,
                        'last_time'=>['BETWEEN',[$nowtime,$onemonth]],
                    ];
                    $result=Db::name($v)->where($where)->find();
                    //判断是否有即将过期的产品
                    if($result){
                        $data['create_time']=date('YmdHis',time());
                        $data['status']=2;
                        $data['order_id']=date('YmdHis',time()).rand(10000,99999);
                        $data['userid']=$userid->userid;
                        if($v=='Domain'){
                            $data['product']='域名服务';
                            $data['title']=$result['domainname'];

                        }elseif($v=='Vhostlist'){
                            $data['product']='虚拟主机';
                            $product=Vhostproduct::get(['title'=>$result['pro_id']]);
                            $data['title']=$product->name;
                            $price=ProductPrice::get(['p_id'=>$product['pro_id'],'term'=>12]);
                            $data['price']=$price->price;
                        }elseif($v=='Vps'){
                            $data['product']='云主机';
                            $product=VpsProduct::get($result['productid']);
                            $data['title']=$product->name.$product->cputype;
                            $price=ProductPrice::get(['p_id'=>$product['title'],'term'=>12]);
                            $data['price']=$price->price;
                        }elseif($v=='Rentserver'){
                            $data['product']='服务器租用';
                            $product=Serverhost::get(['title'=>$result['name']]);
                            $data['title']=$product->$product->product.$product->cpu.$product->memory;
                            $price=ProductPrice::get(['p_id'=>$product['title'],'term'=>12]);
                            $data['price']=$price->price;
                        }
                        $data['user']=$user->username;
                        $data['userid']=$user->userid;
                        $data['time']=date('Y-m-d',strtotime($result['last_time']));
                        $data['openid']=$openid;
                        Session::set('order',$data);
                        $this->assign('data',$data);
                        return view();
                    }
                }
            }else{
                echo '系统错误，请返回重试！！';
            }
        }else{
            echo '系统错误，请返回重试！！';
        }
    }

    public function pay()
    {
        require_once VENDOR_PATH . 'wxpay/lib/WxPay.Api.php';
        require_once VENDOR_PATH . 'wxpay/example/WxPay.JsApiPay.php';
        $pay=Pay::get(1);
        $key=$pay->WxpayKEY;
        $res=Session::get('order');
        /*//写入订单，并调用微信js支付
        $user=Userlist::getByUserid($res['userid']);
        $array['userid']=$user->userid;
        $array['order_id']=$res['order_id'];
        $array['product']=$res['product'];
        $array['money']=$res['price'];
        $array['term']=1;
        $array['payment']=2;
        $array['status']=$res['status'];
        $array['create_time']=$res['create_time'];
        //*/
        $url="https://api.mch.weixin.qq.com/pay/unifiedorder";
        $post_data['appid']=$pay->WxpayAPPID;
        $post_data['mch_id']=$pay->WxpayMCHID;
        $post_data['device_info']='WEB';
        $post_data['nonce_str']=getString();
        $post_data['body']=$res['product'];
        $post_data['sign_type']='MD5';
        $post_data['out_trade_no']=$res['order_id'];
        $post_data['openid']=$res['openid'];
        $post_data['total_fee']=$res['price']*100;
        $post_data['spbill_create_ip']=request()->ip();
        $post_data['notify_url']="https://".$_SERVER['SERVER_NAME']."/weixin/oauth/payok";
        $post_data['trade_type']='JSAPI';
        $post_data['sign']=getSign($post_data,$key);
        $post_data=arrayToXml($post_data);
        $result=postCurlw($url,$post_data);
        $tools=new \JsApiPay();
        $jsApiParameters=$tools->GetJsApiParameters($result);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('data',$res);
        return view();
    }

    public function payok()
    {
        dump($_REQUEST);
    }
}

function getString( $length = 16 ) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $string = "";
    for ( $i = 0; $i < $length; $i++ )
    {
        $string .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
return $string;
}

function getSign($array,$key){
    $stringA="appid=".$array['appid']."&body=".$array['body']."&device_info=".$array['device_info']."&mch_id=".$array['mch_id']
        ."&nonce_str=".$array['nonce_str']."&notify_url=".$array['notify_url']."&openid=".$array['openid']."&out_trade_no=".$array['out_trade_no']
        ."&sign_type=MD5&spbill_create_ip=".$array['spbill_create_ip']."&total_fee=".$array['total_fee']."&trade_type=JSAPI";
    $stringSignTemp=$stringA."&key=".$key;
    $string=strtoupper(md5($stringSignTemp));
    return $string;
}

//数组转XML
function arrayToXml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key=>$val)
    {
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml;
}

//将XML转为array
function xmlToArray($xml)
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

function postCurlw($url,$param)
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_SSLVERSION, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, config('curl_http_timeout'));
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $param); // Post提交的数据包
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);
    $result=xmlToArray($response);
    return $result;
}

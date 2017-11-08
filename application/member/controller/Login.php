<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/25
 * Time: 16:29
 */
namespace app\member\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\member\controller\BaiduSmsClient;
use traits\controller\Jump;
use app\member\model\Settingsite;
class Login extends Controller  {

    public function index()
    {
        //二维码登录的appid
        $app=Settingsite::get(1);
        $url="https://".$_SERVER['SERVER_NAME']."/member/Login/access_token";
        $this->assign('url',$url);
        $this->assign('css_href',"https://".$_SERVER['SERVER_NAME']."/css.css");
        $this->assign('appid',$app->appid);
        //生成6为的随机数
        $sign=(string)mt_rand(100000,999999);
        Session::set('sign',$sign);
        //如果是代理商的链接
        $token=input('token');
        if($token){
            $this->assign('token',$token);
        }else{
            $this->assign('token',base64_encode(encode('0+0+0')));
        }
        return view();
    }


    //百度验证码
    public function bcesms()
    {
        $config = array(
            'endPoint' => 'sms.bj.baidubce.com',
            'accessKey' => '',
            'secretAccessKey' => '',
        );

        $smsClient = new BaiduSmsClient($config);
        $code=rand(100000,999999);
        Session::set('yzm',$code);
        Session::set('phone',input('post.phone'));
        $message = array(
            "invokeId" => "",
            "phoneNumber" => $_POST['phone'],
            "templateCode" => "smsTpl:",
            "contentVar" => array(
                "code" =>  (string)$code,
            ),
        );

       if(count(Session::get('yzm'))!=6 || Session::get('phone') !=input('post.phone') ){
           $ret = $smsClient->sendMessage($message);
           $resu = json_decode( json_encode($ret),true);
           if($resu['code']=='1000'){
               return $code;
           } else {
               return 1;
           }
       }else{
           return 2;
       }

    }

  

    //注册
    public function add()
    {
        unset($_POST['password1']);
        unset($_POST['yzm']);
        $username = $_POST['username'];
        //用户名验证
        if ($_POST['username'] == '') {
            $this->error('用户名不能为空');
        }


        //密码验证
        if ($_POST['password'] == '') {
            $this->error('密码不能为空');
        } else {
            $strlen = strlen($_POST['password']);
            if ($strlen < 6) {
                $this->error('密码少于6位数');
            }
        }

        //邮箱验证
        if ($_POST['email'] == '') {
            $this->error('邮箱不能为空');
        } else {
            $arr['email'] = $_POST['email'];
            $res = Db::name('userlist')->where($arr)->find();
            if (!empty($res)) {
                $this->error('该邮箱以注册');
            }
            $zhengze = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/';
            preg_match($zhengze, $_POST['email'], $result);
            if (!$result) {
                $this->error('邮箱不正确');
            }
        }

        //手机验证
        if ($_POST['phone'] == '') {
            $this->error('手机不能为空');
        } else {
            $ar['phone'] = '+86' . $_POST['phone'];
            $res = Db::name('userlist')->where($ar)->find();
            if (!empty($res)) {
                $this->error('该手机以注册');
            }
            $zhengze = '/^(1[3-9][0-9])\d{8}$/';
            preg_match($zhengze, $_POST['phone'], $result);
            if (!$result) {
                $this->error('手机号码不正确');
            }
        }

        $_POST['userid']=$this->rand();
        if(isset($_POST['token'])){
            $token=decode(base64_decode($_POST['token']));
            $token=explode('+',$token);
            if(is_array($token)){
                $_POST['a_id']=$token[0];
                unset($_POST['token']);
            }
        }
        $_POST['phone'] = '+86' . $_POST['phone'];
        $_POST['creat_ip'] = $_SERVER["REMOTE_ADDR"];
        $_POST['creat_time'] = date('Y-m-d H:i:s', time());
        $_POST['password'] = md5($_POST['password']);
        $_POST['sign']=Session::get('sign');
        $_POST['phoneva']=1;
        $res = Db::name('userlist')->insertGetId($_POST);
        $data['userid']=$res;
        $data['name']=$_POST['username'];
        $data['phone']=$_POST['phone'];
        $data['email']=$_POST['email'];
        $res=Db::name('linkman')->insertGetId($data);
        $_POST['id']=$res;
        Session::set('home',$_POST);
        if ($res) {
            echo 1;
        } else {
            echo 0;
        }
    }

    //登录
    public function go()
    {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        //是否是手机
        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze, $username, $result);
        if ($result) {
            $username = '+86' . $username;
            $res = Db::name('userlist')->where("phone='$username' AND password='$password'")->find();
        } else {
            $zhengze = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/';
            preg_match($zhengze, $username, $res);
            if ($res) {
                $res = Db::name('userlist')->where("email='$username' AND password='$password'")->find();

            } else {
                $res = Db::name('userlist')->where("userid='$username' AND password='$password'")->find();
            }
        }

        if ($res != null) {
           Session::set('home',$res);
            unset(Session::get('home')['password']);
            unset(Session::get('home')['hisamount']);
            unset(Session::get('home')['balance']);
            if ($res['state'] == 0) {
                $this->error('你已被拉黑');
            }
            echo 1;
        } else {
            $this->error('用户名或密码不正确');
        }
    }

    //判断手机号码有没有被注册
    public function phonetext()
    {
        $phone = '+86'.$_POST['phone'];
        $res = Db::name('userlist')->where("phone='$phone'")->find();

        if (!empty($res)) {
            echo 1;
        }else{
            echo 0;
        }
    }

   //修改密码 处理
    public function reset()
    {
        unset($_POST['repassword']);
        $where['phone'] ='+86' . $_POST['phone'];
        $arr['password'] = md5($_POST['password']);
        $res=Db::name('userlist')->where($where)->update($arr);
        if($res){
            echo 1;
        }else{
            echo 2;
        }
    }

    //获取access_token
    public function access_token()
    {
        $code=$_GET['code'];
        $site=Settingsite::get(1);
        $app_id=$site->appid;
        $app_secret=$site->appsecret;
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$app_id."&secret=".$app_secret."&code=".$code."&grant_type=authorization_code";
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $token_url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        $data=json_decode($data,true);
        $openid=$data['openid'];
        $access_token=$data['access_token'];
        $token_url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid;
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $token_url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        $data=json_decode($data,true);
        $rest['openid']=$data['openid'];
        $result=Db::name('userlist')->where($rest)->find();
        if(!$result){
            //第一次登录
            $rest['unionid']=$data['unionid'];
            $rest['username']=$data['nickname'];
            $rest['creat_time']=date('Y-m-d H:i:s',time());
            $rest['userid']=$this->rand();
            $rest['creat_ip']=$_SERVER["REMOTE_ADDR"];
            $res=Db::name('userlist')->insertGetId($rest);
            $this->redirect('member/Login/pvalidate',array('id'=>$res));

        }if($result['openid']!=''&&$result['phone']=='') {
            //有过登录，但是未绑定手机
            $this->redirect(url('member/Login/pvalidate',['id'=>$result['id']]));

        }else{
            if($result['phone']!=''&&$result['openid']!=''){
                Session::set('home',$result);
               $this->redirect(url('member/index/index'));
            }
        }

    }

    public function rand()
    {
        $rest['userid'] = '8011' . mt_rand(10000, 99999);
        $userid = $rest['userid'];
        $r = Db::name('userlist')->where("userid=$userid")->find();
        if ($r) {
            rand();
        }else{
            return $userid;
        }
    }

    //手机验证
    public function pvalidate()
    {
        //获取logo
        $logo=Settingsite::get(1);
        $this->assign('logo',$logo->pic);
        //接受id
        $id=input('id');
        $this->assign('id',$id);
        return view('login/pvalidate');
    }

    //验证微信登录的用户手机后是否已被验证
    public function phoneve()
    {
        if(count(Session::get('yzm'))!=6)
        {
            $where['phone']='+86'.$_POST['phone'];
            $res=Db::name('userlist')->where($where)->find();
            echo 0;
        }else{
            echo 3;
        }
    }


    //微信登录的用户验证手机号
    public function savephone()
    {
        $where['id']=input('post.id');
        $phone='+86'.input('post.phone');
        $res=Db::name('userlist')->where('phone',$phone)->find();
        if($res){
            $weixin=Db::name('userlist')->where($where)->field('openid,unionid')->find();
            $rest=Db::name('userlist')->where('id',$res['id'])->update($weixin);
            if($rest){
                $result=Db::name('userlist')->where('phone',$phone)->find();
                Session::set('home',$result);
                if(Db::name('userlist')->delete($where['id'])){
                    echo 1;
                }
            }
        }else{
            $data['phone']=$phone;
            $data['phoneva']=1;
            $res=Db::name('userlist')->where($where)->update($data);
            $rest['userid']=$where['id'];
            $rest['phone']=$data['phone'];
            $res=Db::name('linkman')->insert($rest);
            $result=Db::name('userlist')->where('phone',$phone)->find();
            Session::set('home',$result);
            if($res){
                echo 1;
            }else{
                echo 2;
            }
        }
    }




}
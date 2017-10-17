<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29
 * Time: 14:00
 */
use think\Controller;
use think\Db;
include 'sign.php';
class bce extends Controller
{
    //域名查询
    public function bce_select($data)
    {
        set_time_limit(0);
        //生成签名
        $domain=$data;
        $data=$this->sign_http_get($domain);
        $data=json_decode($data,true);
        return $data;
    }

    //签名，curl(post 方法)
    public function sign_http_post($data)
    {
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        //计算签名
        $json_data=json_encode($data['data']);
        $signer = new SampleSigner();
        $credentials = $res;
        $httpMethod = "POST";
        $path = $data['path'];
        $params = array($data['data']);
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers = array("Host" => 'bcd.baidubce.com');
        $str_sha256 = hash('sha256', $json_data);
        $headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp, SignOption::HEADERS_TO_SIGN => array('host', 'x-bce-content-sha256',),);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            "Accept-Encoding: gzip,deflate",
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Date:' . $datetime_gmt,
        );

        $url = 'http://bcd.baidubce.com'. $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        return $result;
    }

    //签名，curl(get 方法)
    public function sign_http_get($data)
    {
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);

        $signer = new SampleSigner();
        $credentials = $res;
        $params = array('domain'=>$data);
        $timestamp = new \DateTime();
        $httpMethod='GET';
        $path="/v1/domain/search";
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $headers['Host']="bcd.baidubce.com";
        $options = array(SignOption::TIMESTAMP => $timestamp);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        //拼接请求头
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Authorization:' . $ret,
        );
        $url='http://bcd.baidubce.com/v1/domain/search?domain='.$data;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        $a=curl_getinfo($curl);
        $errorno = curl_errno($curl);
        //关闭URL请求
        curl_close($curl);
        return $result;
    }

    //域名注册
    public function bce_buy($array)
    {
        set_time_limit(0);
        $years=$array['term'];
        $privacy=$array['privacy'];
        $tempid=$array['tempid'];
        $domain=$array['domain'];
        //根据id去查对应的模板ID
        $res=Db::name('domain_temp')->where('id',$tempid)->find();
        $data['years']=$years;
        if($res==1){
            $data['userType']='INDIVIDUAL';
        }else{
            $data['userType']='ENTERPRISE';
        }
        if($privacy){
            $data['privacy']=true;
        }
        $reg=explode(',',$res['region']);
        $region=Db::name('city')->where('id',$res['region'])->find();
        $data['data']['region']['province']=Db::name('city')->where('name',$reg[1])->find()['id'];
        $data['data']['region']['city']=Db::name('city')->where('name',$reg[2])->find()['id'];
        $data['data']['ownerChinese']=$res['owner_cn'];
        $data['data']['ownerEnglish']=$res['owner_en'];
        $data['data']['contactChinese']=$res['contacts_cn'];
        $data['data']['contactEnglish']=$res['contacts_en'];
        $data['data']['email']=$res['email'];
        $data['data']['addressChinese']=$res['address_cn'];
        $data['data']['addressEnglish']=$res['address_en'];
        $data['data']['mobilePhone']=$res['phone'];
        $data['data']['postalCode']=$res['postcode'];
        $data['data']['areaCode']=$res['areacode'];
        $data['data']['phoneNumber']=$res['tel'];
        $data['data']['domain']=$domain;
        $data['path']="/v1/domain/register";
        $code=$this->sign_http_post($data);
        return $code;
    }

    //域名解析列表
    public function bce_resolulist($array)
    {
        $data['domain']=$array['domain'];
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        //计算签名
        $json_data=json_encode($data);
        $signer = new SampleSigner();
        $credentials = $res;
        $httpMethod = "POST";
        $path = "/v1/domain/resolve/list";
        $params = [];
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers = array("Host" => 'bcd.baidubce.com');
        $str_sha256 = hash('sha256', $json_data);
        $headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            "Accept-Encoding: gzip,deflate",
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Date:' . $datetime_gmt,
        );

        $url = 'http://bcd.baidubce.com'. $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        $result=json_decode($result,true);
        foreach ($result['result'] as $k =>$v){
            $rest[$k]=[
                'type'=>$v['rdtype'],
                'id'=>(string)$v['recordId'],
                'ttl'=>(string)$v['ttl'],
                'name'=>$v['domain'],
                'value'=>$v['rdata'],
                'prio'=>'10',
                'ispause'=>'0',
            ];
        };
        return $result;
    }

    //添加域名解析
    public function bce_addresolu($array)
    {
        //查询配置
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        //组合数据
        $data['domain']=$array['name'];
        $data['rdType']=$array['type'];
        $data['rdata']=$array['value'];
        $data['zoneName']=$array['domain'];
        $data['ttl']=(int)$array['ttl'];
        $json_data=json_encode($data);
        $signer = new SampleSigner();
        $credentials = $res;
        $httpMethod = "POST";
        $path = "/v1/domain/resolve/add";
        $params = [];
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers = array("Host" => 'bcd.baidubce.com');
        $str_sha256 = hash('sha256', $json_data);
        $headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            "Accept-Encoding: gzip,deflate",
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Date:' . $datetime_gmt,
        );

        $url = 'http://bcd.baidubce.com'. $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        $result=json_decode($result,true);
        if(!$result){
            $rest=[
                'type'=>$data['rdType'],
                'name'=>$data['domain'],
                'value'=>$data['rdata'],
                'ttl'=>$data['ttl'],
                'ispause'=>'0',
            ];
        }else{
            $rest=[
                'code'=>'501',
                'msg'=>'添加失败'
            ];
        }
        return $rest;
    }

    //删除解析
    public function bce_delresolu($array)
    {
        //查询配置
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        //组合数据
        $data['zoneName']=$array['domain'];
        $data['recordId']=(int)$array['id'];
        $json_data=json_encode($data);
        $signer = new SampleSigner();
        $credentials = $res;
        $httpMethod = "POST";
        $path = "/v1/domain/resolve/delete";
        $params = [];
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers = array("Host" => 'bcd.baidubce.com');
        $str_sha256 = hash('sha256', $json_data);
        $headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            "Accept-Encoding: gzip,deflate",
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Date:' . $datetime_gmt,
        );

        $url = 'http://bcd.baidubce.com'. $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        $result=json_decode($result,true);
        if(!$result){
            $rest=[
                'code'=>'200',
                'msg'=>'删除成功'
            ];
        }else{
            $rest=[
                'code'=>'200',
                'msg'=>'删除成功'
            ];
        }
        return $rest;
    }

    //修改解析
    public function bce_editresolu($array)
    {

        //查询配置
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        //组合数据
        $data['recordId']=(int)$array['id'];
        $data['domain']=$array['name'];
        $data['rdType']=$array['type'];
        $data['ttl']=(int)$array['ttl'];
        $data['rdata']=$array['value'];
        $data['zoneName']=$array['domain'];
        $json_data=json_encode($data);
        $signer = new SampleSigner();
        $credentials = $res;
        $httpMethod = "POST";
        $path = "/v1/domain/resolve/edit";
        $params = [];
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("GMT"));
        $datetime = $timestamp->format("Y-m-d\TH:i:s\Z");
        $datetime_gmt = $timestamp->format("D, d M Y H:i:s T");
        $headers = array("Host" => 'bcd.baidubce.com');
        $str_sha256 = hash('sha256', $json_data);
        $headers['x-bce-content-sha256'] = $str_sha256;
        $headers['Content-Length'] = strlen($json_data);
        $headers['Content-Type'] = "application/json";
        $headers['x-bce-date'] = $datetime;
        $options = array(SignOption::TIMESTAMP => $timestamp);
        $ret = $signer->sign($credentials, $httpMethod, $path, $headers, $params, $options);
        $headers_curl = array(
            'Content-Type:application/json',
            'Host:bcd.baidubce.com',
            'x-bce-date:' . $datetime,
            'Content-Length:' . strlen($json_data),
            'x-bce-content-sha256:' . $str_sha256,
            'Authorization:' . $ret,
            "Accept-Encoding: gzip,deflate",
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Date:' . $datetime_gmt,
        );

        $url = 'http://bcd.baidubce.com'. $path;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $errorno = curl_errno($curl);
        curl_close($curl);
        $result=json_decode($result,true);

        return $data;
    }

    //创建云服务器
    public function create_server()
    {
        require 'aes.class.php';

        $a=input('post.password');
        if($a){
            $key='e8679c8be4de44d29121d99cb16213b7';
            $m = new \Xcrypt($key, 'cbc', 'auto');
            //加密
            $encode = $m->encrypt($a, 'base64');
            //转换为16进制
            $adminpass=bin2hex($encode);
        }
        $token=mt_rand(1000000,9999999);
        $clientoken='';
        for($i=0;$i<strlen($token);$i++){
            $clientoken.=ord(substr($token,$i,1));
        }
        //获取规格类型
        $type=input('post.type');
        if(!$type){
            $cpucount=input('post.cpu');
            $gb=input('post.gb');
        }
        $imgid=input('post.imgid');


    }




}
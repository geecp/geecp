<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 14:12
 */
namespace addons\west263\controller;
use think\addons\Controller;
use think\Db;
include 'sign.php';
class West263 extends Controller
{
    //西部数码链接基础信息
    public function Basic()
    {
        header("Content-type:text/html;charset=utf-8");
        //转换字符格式
        $GLOBALS['sys_api'] = "http://api.west263.com/API/";
        //API请求地址
        $GLOBALS['sys_user'] = "imatao";
        //用户名
        $GLOBALS['sys_pass'] = "qe8ncca2";
        //密码

        $where['state'] = 1;
        //常用域名
        $where_cy['flag'] = '1';
        $cy_domain = Db::name('Domain_price')->where($where)->where($where_cy)->field('type')->select();
        foreach ($cy_domain as $v) {
            $cy[] = '.' . $v['type'];
        }
        $GLOBALS['cy_houzhui'] = implode(',', $cy);
        $where_tj['flag'] = '2';
        //推荐域名
        $tj_domain = Db::name('Domain_price')->where($where)->where($where_tj)->field('type')->select();
        foreach ($tj_domain as $v) {
            $tj[] = '.' . $v['type'];
        }
        $GLOBALS['tj_houzhui'] = implode(',', $tj);
    }

    //域名查询
    public function domain_select()
    {
        $domain='qiduo';
        //生成签名

        $cy_houzui=Db::name('Domain_price')->where('flag','1')->field('type')->select();
        //循环查询常用域名的注册状态,以及价格
        foreach ($cy_houzui as $k=>$v){
            $signer = new SampleSigner();
            $credentials = array("ak" => '405426911bc747e8a50dcd4dbf95822d', "sk" => 'e8679c8be4de44d29121d99cb16213b7');
            $params = array('domain'=>$domain.'.'.$v['type']);
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
            $url='http://bcd.baidubce.com/v1/domain/search?domain='.$domain.'.'.$v['type'];
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            //设置头文件的信息作为数据流输出
            curl_setopt($curl, CURLOPT_HEADER, 0);
            //设置获取的信息以文件流的形式返回，而不是直接输出。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers_curl);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
            $v['status'] = curl_exec($curl);
            $a=curl_getinfo($curl);
            $errorno = curl_errno($curl);
            //关闭URL请求
            curl_close($curl);
            $data[$k]=$v;
        }
        dump($data);
    }

    //

}
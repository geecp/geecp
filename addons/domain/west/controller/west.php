<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/20
 * Time: 14:12
 */
use think\Controller;
use think\Db;
class west extends Controller
{
    //西部数码链接基础信息
    private static function Basic()
    {
        //转换字符格式
        header("Content-type:text/html;charset=utf-8");
        //API请求地址
        $where=[
            'range'=>'domain',
            'status'=>1
        ];
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        $res['sys_api'] = "http://api.west263.com/api/";
        return $res;


    }

    //域名查询
    public function west_select($data)
    {

        $domainname=explode('.',$data)[0];
        $suffix='.'.explode('.',$data)[1];
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];

        $cmdstrng = "domainname"."\r\n"."check"."\r\n"."entityname:domain-check"."\r\n";
        $cy_gongn="domainname:" . $domainname . "\r\n" ."suffix:". $suffix . "\r\n" . "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['returncode']=='200'){
            unset($xml['returncode']);
            unset($xml['returnmsg']);
            if($xml['info']['record']['allow']){
                $code['domainName']=$xml['info']['record']['allow'];
                $code['status']='UNREGISTERED';
            }else if($xml['info']['record']['registered']){
                $code['domainName']=$xml['info']['record']['registered'];
                $code['status']='REGISTERED';
            }else if($xml['info']['record']['error']){
                $code['domainName']=$xml['info']['record']['error'];
                $code['status']='UNKNOWN';
            }else{
                $code['domainName']=$xml['info']['record']['premium'];
                $code['status']='RESERVED';
            }
            $res['domainBasicInfoList']=array($code);
        }else if($xml['returncode']=='501'){
            $res=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }

        return $res;
    }

    //查询whois信息
    public function whois_select($data)
    {
        $domain=$data['domain'];
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];


        $cmdstrng = "other"."\r\n"."whois"."\r\n"."entityname:info"."\r\n";
        $cy_gongn="domain:" . $domain . "\r\n" . "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        $res=json_decode($xml['returnmsg']);
        $niubi=object2array($res);

        return $niubi;
    }

    //西部数码域名注册
    public function west_buy($data)
    {
        $domain=$data['domain'];
        $term=$data['term'];
        $where['id']=$data['tempid'];
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];

        $cmdstrng = "domainname" . "\r\n" . "add" . "\r\n" . "entityname:domain" . "\r\n";
        $res=Db::name('domain_temp')->where($where)->find();
        $dom_org=$res['owner_en'];
        $dom_fn=explode(' ',$res['owner_en'])[0];
        $dom_ln=substr($res['owner_en'],strpos($res['owner_en'],' '));
        $dom_adr1=$res['address_en'];
        $dom_ct=$res['city_en'];
        $dom_st=$res['province_en'];
        $dom_co='cn';
        $dom_pc=$res['postcode'];
        $dom_ph=$res['phone'];
        $dom_fax=$res['phone'];
        $dom_em=$res['email'];
        $dom_org_m=$res['owner_cn'];
        $dom_adr_m=$res['address_cn'];
        $dom_ct_m=$res['city_cn'];
        $dom_st_m=$res['province_cn'];
        $dom_ln_m=splitName($res['owner_cn'])[0];
        $dom_fn_m=splitName($res['owner_cn'])[1];
        $gongn = "domainname:" . "$domain" . "\r\n" . "term:" . "$term" . "\r\n" . "dom_org:" . "$dom_org" . "\r\n" . "dom_fn:" . "$dom_fn" . "\r\n" . "dom_ln:" . "$dom_ln" . "\r\n" . "dom_adr1:" . "$dom_adr1" . "\r\n" . "dom_ct:" . "$dom_ct" . "\r\n" . "dom_st:" . "$dom_st" . "\r\n" . "dom_co:" . "$dom_co" . "\r\n" . "dom_pc:" . "$dom_pc" . "\r\n" . "dom_ph:" . "$dom_ph" . "\r\n" . "dom_fax:" . "$dom_fax" . "\r\n" . "dom_em:" . "$dom_em" . "\r\n" . "dom_org_m:" . "$dom_org_m" . "\r\n" . "dom_fn_m:" . "$dom_fn_m" . "\r\n" . "dom_ln_m:" . "$dom_ln_m" . "\r\n" . "dom_adr_m:" . "$dom_adr_m" . "\r\n" . "dom_ct_m:" . "$dom_ct_m" . "\r\n" . "dom_st_m:" . "$dom_st_m" . "\r\n" . "domainpwd:" . "jtc11v" . "\r\n" . "ppriicetemp:" . "999" . "\r\n" . "." . "\r\n";
        $md5sing = md5($sys_user . $sys_pass . substr($cmdstrng, 0, 10));
        //请求地址
        $postdata = $sys_api . "?userid=" . $sys_user . "&versig=" . $md5sing . "&strCmd=" . urlencode($cmdstrng) . rawurlencode(iconv("utf-8", "gb2312//IGNORE", $gongn));

        $return = file_get_contents($postdata);
        //发送请求
        $xml = xmlToArray($return);
        if($xml['returncode']=='200'){
            $rest=[
                'code'=>200,
                'msg'=>'command success',
                'orderid'=>$xml['info']['orderid']
            ];
        }else{
            $rest=[
                'code'=>500,
                'msg'=>'command false'
            ];
        }

        return $rest;
    }

    //域名续费
    public function west_renew($data)
    {
        set_time_limit(0);
        $this->Basic();
        global $sys_api;
        //API请求地址
        global $sys_user;
        //用户名
        global $sys_pass;
        $domain=$data['domain'];
        //获取当前域名在未续费之前的过期时间
        $expiredate=Db::name('domain')->where('domainname',$domain)->field('last_time')->find();
        $term=$data['term'];
        $ppricetemp='9999';
        $cmdstrng = "domainname"."\r\n"."renew"."\r\n"."entityname:domain"."\r\n";
        $cy_gongn="domain:" . $domain . "\r\n" . "term:" . $term. "\r\n" ."expiredate:" . $expiredate . "\r\n" ."ppricetemp:" . $ppricetemp . "\r\n" ."." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        $res=json_decode($xml['returnmsg']);
        $niubi=object2array($res);

        return $niubi;

    }

    //获取所有的域名解析列表
    public function west_resolulist($data)
    {
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];
        $domain=$data['domain'];
        $cmdstrng = "dnsresolve"."\r\n"."list"."\r\n"."entityname:dnsrecord"."\r\n";
        $cy_gongn="domain:" . $domain .  "\r\n" ."." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['info']!=''&&$xml['info']!='["\n"]'){
            if($xml['returncode']=='200'){
                unset($data['uid']);
                $rest=$data;
                $rest=$xml['info'];
            }else if($xml['returncode']=='501'){
                $rest=[
                    'code'=>'501',
                    'msg'=>'不在API接口授权IP之内'
                ];
            }
        }else if($xml['info']=='["\n"]'){
            $rest=[
                'code'=>'404',
                'msg'=>'暂无解析记录'
            ];
        }else{
            $rest=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }
        return $rest;
    }

    //添加解析
    public function west_addresolu($data)
    {
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];
        $domain=$data['domain'];
        $ttl=$data['ttl'];
        $type=$data['type'];
        $value=$data['value'];
        $rr=$data['name'];
        $pri=$data['prio'];
        $cmdstrng = "dnsresolve"."\r\n"."add"."\r\n"."entityname:dnsrecord"."\r\n";
        $cy_gongn="domain:" . $domain .  "\r\n" ."rr:" . $rr .  "\r\n" ."type:" . $type .  "\r\n" . "value:" . $value .  "\r\n" . "pri:" . $pri .  "\r\n" . "ttl:" . $ttl .  "\r\n" .  "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['returncode']=='200'){
            $rest=[
                'type'=>$data['type'],
                'id'=>$xml['info']['id'],
                'name'=>$data['name'],
                'value'=>$data['value'],
                'ttl'=>$data['ttl'],
                'prio'=>$data['prio'],
                'ispause'=>'0',
            ];
        }else if($xml['returncode']=='501'){
            $rest=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }
        return $rest;
    }

    //删除解析
    public function west_delresolu($data)
    {
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];
        $rr_id=$data['id'];
        $domain=$data['domain'];
        $cmdstrng = "dnsresolve"."\r\n"."del"."\r\n"."entityname:dnsrecord"."\r\n";
        $cy_gongn="domain:" . $domain .  "\r\n" ."rr_id:" . $rr_id .  "\r\n" . "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['returnmsg']=='api连接打开失败'){
            $rest=[
                'code'=>'200',
                'msg'=>'删除成功'
            ];
        }else{
            $rest=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }
        return $rest;
    }

    //修改解析记录
    public function west_editresolu($data)
    {
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];
        $rr_id=$data['id'];
        $domain=$data['domain'];
        $ttl=$data['ttl'];
        $value=$data['value'];
        $cmdstrng = "dnsresolve"."\r\n"."mod"."\r\n"."entityname:dnsrecord"."\r\n";
        $cy_gongn="domain:" . $domain .  "\r\n" ."rr_id:" . $rr_id .  "\r\n" . "value:" . $value .  "\r\n" ."ttl:" . $ttl .  "\r\n" .  "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['returncode']=='200'){
            $rest=[
                'code'=>'200',
                'msg'=>'修改成功'
            ];
        }else{
            $rest=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }
        return $rest;
    }

    //修改状态
    public function west_status_resolu($data)
    {
        set_time_limit(0);
        $config=$this->Basic();
        $sys_api=$config['sys_api'];
        //API请求地址
        $sys_user=$config['sys_user'];
        //用户名
        $sys_pass=$config['sys_pass'];
        $domain=$data['domain'];
        $rr_id=$data['id'];
        $value=$data['value'];
        $cmdstrng = "dnsresolve"."\r\n"."status"."\r\n"."entityname:dnsrecord"."\r\n";
        $cy_gongn="domain:" . $domain .  "\r\n" ."rr_id:" . $rr_id .  "\r\n" . "value:" . $value .  "\r\n" . "." . "\r\n";
        $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
        $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
        $return =file_get_contents($postdata);
        $xml = xmlToArray($return);
        if($xml['returncode']=='200'){
            $rest=[
                'code'=>'200',
                'msg'=>'修改成功'
            ];
        }else{
            $rest=[
                'code'=>'501',
                'msg'=>'不在API接口授权IP之内'
            ];
        }
        return $rest;
    }

    

}
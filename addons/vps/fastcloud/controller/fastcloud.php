<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/25
 * Time: 17:05
 */
use think\Controller;
use think\Db;
class fastcloud extends Controller
{
    private function Basic()
    {
        //读取数据库jainet的配置
        $where=array(
            'range'=>'vhost',
            'name'=>'jainet'
        );
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        return $res;
    }

    function httpPost($data)
    {
        $url=$data['url'];
        $post_data=$data['post_data'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $resutl = curl_exec($ch);
        curl_close($ch);
        return $resutl;
    }

    function hmac_sha1($data)
    {
        $res=$this->Basic();
        $aid=$res['aid'];
        $ptype=$data['ptype'];
        $tid=$data['tid'];
        $date=date('YmdHis',time());
        $akey=$res['akey'];
        $aid=$aid.$ptype.$tid.$date.$akey;
        $aid=mb_convert_encoding($aid,"UTF-8");
        $gsig=strtoupper(hash_hmac('sha1',$aid,$akey));
        $rest=array(
            'aid'=>$res['aid'],
            'ptype'=>$ptype,
            'tid'=>$tid,
            'date'=>$date,
            'gsig'=>$gsig
        );
        return $rest;
    }

    //购买
    public function fastcloud_buy($data)
    {
        $where['id']=$data['allocation']['conf']['typeid'];
        $res=Db::name('vps_product')->where($where)->find();
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=1;
        $gsig=$this->hmac_sha1($code);
        $gsig['pname']=$res['title'];
        if(!strpos($data['allocation']['room'],'郑州')){
            $area=4001;
        }else if(!strpos($data['allocation']['room'],'北京')){
            $area=4003;
        }
        $gsig['area_code']=$area;
        $gsig['yid']=$data['allocation']['conf']['mouth'];
        //判断操作系统
        $os['id']=$data['allocation']['conf']['systemid'];
        $os_res=Db::name('system')->where($os)->find();
        if($os_res['name']=='Windows' && $os_res['version']=='2003'){
            $system=0;
        }else if($os_res['name']=='CentOS'){
            $system=1;
        }else if($os_res['name']=='Windows' && $os_res['version']=='2008'){
            $system=2;
        }
        $gsig['input_name']=$system;
        $gsig['usetype']=3;
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        //开通
        if($rest['code']=='0' && $rest['info']['did']!=''){
            unset($gsig);
            $url="http://api.zzidc.com/rest.api";
            $code['ptype']='fastcloudvps';
            $code['tid']=2;
            $gsig=$this->hmac_sha1($code);
            $whe['id']=$data['userid'];
            $user=Db::name('userlist')->where($whe)->field('id,phone,email,username')->find();
            $gsig['mobile']=substr($user['phone'],3);
            $gsig['email']=$user['email'];
            $gsig['applyname']=$user['username'];
            $gsig['did']=$rest['info']['did'];
            $post_data=array(
                'url'=>$url,
                'post_data'=>$gsig,
            );
            $restul=$this->httpPost($post_data);
            $restul=json_decode($restul,true);
            $allocation=[
                'did'=>$rest['info']['did'],
                'bid'=>$restul['info']['bid']
            ];
            $allocation=json_encode($allocation,JSON_UNESCAPED_UNICODE);
            if($restul['code']=='0' && $rest['info']['did']!=''){
                $fanhui=[
                    'did'=>$rest['info']['did'],
                    'bid'=>$restul['info']['bid'],
                    'userid'=>$user['id'],
                    'user'=>$restul['info']['user'],
                    'pwd'=>$restul['info']['pwd'],
                    'ip'=>$restul['info']['ip'],
                    'create_time'=>$restul['info']['createDate'],
                    'last_time'=>$restul['info']['overDate'],
                    'productid'=>$data['allocation']['conf']['typeid'],
                    'cpu'=>$res['cpu'],
                    'hardisk'=>$res['systemdisk']+$res['datadisk'],
                    'memory'=>$res['memory'],
                    'os'=>$os_res['name'].$os_res['version'],
                    'allocation'=>$allocation
                ];
            }
        }
        return $fanhui;
    }

    //开通
    public function fastcloud_open($data)
    {
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=2;
        $gsig=$this->hmac_sha1($code);
        $gsig['mobile']=$data['phone'];
        $gsig['email']=$data['email'];
        $gsig['applyname']=$data['name'];
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        return $rest;
    }

    //续费
    public function fastcloud_renew($data)
    {
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=3;
        $gsig=$this->hmac_sha1($code);
        $gsig['bid']=$data['bid'];
        $gsig['usetype']=3;
        $gsig['yid']=$data['term'];
        $gsig['keytype']=0;
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        return $rest;
    }

    //升级
    public function fastcloud_upgrade($data)
    {
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=4;
        $gsig=$this->hmac_sha1($code);
    }

    //开机、关机、重启
    public function fastcloud_operation($data)
    {
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=6;
        $gsig=$this->hmac_sha1($code);
        $gsig['bid']=$data['bid'];
        if($data['type']=='start'){
            $status=1;
        }else if($data['type']=='shutdown'){
            $status=2;
        } else if($data['type']=='restart'){
            $status=3;
        }
        $gsig['operation_type']=$status;
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        return $rest;
    }

    // 获取管理平台的链接地址
    public function fastcloud_manage($data)
    {
        //获取所需要的参数
        $id['id']=$data['productid'];
        $res=Db::name('vps_product')->find($id);
        $url="http://api.zzidc.com/rest.api";
        $code['ptype']='self';
        $code['tid']=17;
        $gsig=$this->hmac_sha1($code);
        $gsig['pname']=$res['title'];
        $gsig['input_name']='fastcloudvps';
        $gsig['bid']=$data['bid'];
        $gsig['bis_sign']=$data['ip'];
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        if($rest['code']=='0' && $rest['info']['passwd']!=''){
            unset($gsig);
            $service_code=$rest['info']['serviceCode'];
            $service_code_pwd=$rest['info']['passwd'];
            $url="http://api.zzidc.com/rest.api";
            $code['ptype']='self';
            $code['tid']=5;
            $gsig=$this->hmac_sha1($code);
            $clent_ip=get_client_ip();
            $gsig['service_code']=$service_code;
            $gsig['service_code_pwd']=$service_code_pwd;
            $gsig['input_name']=$clent_ip;
            $post_data=array(
                'url'=>$url,
                'post_data'=>$gsig,
            );
            $rest=$this->httpPost($post_data);
            $rest=json_decode($rest,true);
        }
        return $rest;
    }

    //获取列表
    public function jainet_list($data)
    {
        $url = "http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=18;
        $gsig=$this->hmac_sha1($code);
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        return $rest;
    }

    //获取指定vps的配置信息
    public function jainet_vps_info($data)
    {
        $url = "http://api.zzidc.com/rest.api";
        $code['ptype']='fastcloudvps';
        $code['tid']=46;
        $gsig=$this->hmac_sha1($code);
        $gsig['pname']=$data['data'];
        $post_data=array(
            'url'=>$url,
            'post_data'=>$gsig
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        return $rest;
    }
}

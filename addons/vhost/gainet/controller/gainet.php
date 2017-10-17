<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 21:51
 */
use think\Controller;
use think\Db;
class gainet extends Controller
{
    public function Basic()
    {
        //读取数据库jainet的配置
        $where=array(
            'range'=>'vhost',
            'name'=>'gainet'
        );
        $res=Db::name('addons')->where($where)->field('config')->find();
        $res=json_decode($res['config'],true);
        return $res;
    }

    //curl post 请求
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

    //签名
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

    //购买和开通,归纳为一个步骤
    public function gainet_buy($data)
    {
        //接收产品id，判断其ptype
        $res=Db::name('vhostproduct')->where('title',$data['allocation']['conf']['title'])->find();
        $user=Db::name('userlist')->where('id',$data['userid'])->field('username,phone,email')->find();
        //创造数组
        $host=['host.mf.I','host.gr.A','host.gr.B','host.qy.A','host.qy.B'];
        $usahost=['usa.host.I','usa.host.II','usa.host.III','usa.host.V','usa.host.G'];
        $hkhost=['hkhost.I','hkhost.II','hkhost.III','hkhost.V','hkhost.G'];
        $cloudVirtual=['cloudVirtual.JS.I','cloudVirtual.JS.II','cloudVirtual.JS.III'];
        $cloudspace=['cloudspace.I','cloudspace.II'];
        $dedehost=['dede.cn.host.I','dede.cn.host.II','dede.cn.host.III','dede.cn.host.G','dede.hk.host.I','dede.hk.host.II','dede.hk.host.III','dede.hk.host.G'];
        if(in_array($res['title'],$host)){
            $code['ptype']='host';
        }else if(in_array($res['title'],$usahost)){
            $code['ptype']='usahost';
        }else if(in_array($res['title'],$hkhost)){
            $code['ptype']='hkhost';
        }else if(in_array($res['title'],$cloudVirtual)){
            $code['ptype']='cloudVirtual';
        }else if(in_array($res['title'],$dedehost)){
            $code['ptype']='dedehost';
        }
        $code['tid']=1;
        $code=$this->hmac_sha1($code);
        $url="http://api.zzidc.com/rest.api";
        $code['pname']=$res['title'];
        $code['yid']=(int)$data['allocation']['mouth'];
        $id=$data['allocation']['conf']['systemid'];
        $system=Db::name('system')->where('id',$id)->find()['name'];
        if($system=='linux'){
            $code['systemType']=0;
        }else{
            $code['systemType']=1;
        }
        $post_data=array(
            'url'=>$url,
            'post_data'=>$code,
        );
        $rest=$this->httpPost($post_data);
        $rest=json_decode($rest,true);
        //开通
        if($rest['code']=='0' && $rest['info']['did']!=''){
            unset($code['yid']);
            $code['tid']=2;
            $code=$this->hmac_sha1($code);
            $code['did']=$rest['info']['did'];
            $post_data=array(
                'url'=>$url,
                'post_data'=>$code,
            );
            $rest=$this->httpPost($post_data);
            $restu=json_decode($rest,true);
            if($restu['code']=='0'){
                //获取主机大小
                $gsig['ptype']=$code['ptype'];
                $gsig['tid']=5;
                $gsig=$this->hmac_sha1($gsig);
                $gsig['bid']=$restu['info']['bid'];
                $post_data=array(
                    'url'=>$url,
                    'post_data'=>$gsig,
                );
                $restul=$this->httpPost($post_data);
                $restul=json_decode($rest,true);
                $allcation=[
                    'ftpname'=>$restu['info']['ftpName'],
                    'ftpaddress'=>$restu['info']['ftp_address'],
                    'ftppwd'=>$restu['info']['pwd'],
                ];
                $allcation=json_encode($allcation,JSON_UNESCAPED_UNICODE);
                $result=[
                    'pro_id'=>$res['title'],
                    'ip'=>$restu['info']['ip'],
                    'space'=>$restul['info']['spaceSize'],
                    'webnum'=>$res['webnum'],
                    'domainnum'=>$res['domainnum'],
                    'dbsize'=>$res['dbsize'],
                    'system'=>$restul['info']['system'],
                    'flow'=>$res['dbsize'],
                    'maxconent'=>$restul['info']['maxConnect'],
                    'email'=>$user['email'],
                    'phone'=>$user['phone'],
                    'cname'=>$restul['domainName'],
                    'create_time'=>$restu['info']['createDate'],
                    'last_time'=>$restu['info']['overDate'],
                    'bid'=>$restu['info']['bid'],
                    'did'=>$rest['info']['did'],
                    'allcation'=>$allcation,
                ];
                return $result;
            }else{
                return [$rest,$restu];
            }
        }

    }



}
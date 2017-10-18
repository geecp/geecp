<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;
use app\admin\model\Domain;
use app\admin\model\System;
use app\admin\model\Userlist;
use app\admin\model\Vhostbusiness;
use app\admin\model\Vhostgroup;
use app\admin\model\Vps;
use app\admin\model\VpsProduct;
use app\template\model\VhostProduct;
use think\Db;
use think\Session;


class Business extends Base
{
    public function index()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        //找到当前方法在权限表中的对应数据
        $res=Db::name('right')->where('url',$url)->find()['id'];

        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];

        //找到与一级菜单同名的方法
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];

        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else if (!in_array($res1,$adm_group)){
            //菜单id
            $id=input('id');
            $url=Db::name('right')->whereIn('id',$adm_group)->where('pid',$id)->order('num')->find()['url'];
            if ($url !=''){
                return $this->redirect($url);
            }else{
                return $this->redirect('error/index');
            }
        }else{
            $domain=new Domain();
            $res=$domain->where('status','<>',2)->paginate(10);

            foreach($res as $val){

                $val['userid']=Db::name("userlist")->where('id',$val['userid'])->find()['username'];
                //处理一下模板
                $val['domain_temp']=Db::name("domain_temp")->where('id',$val['domain_temp'])->find()['owner_cn'];
                //处理时间
                //当前时间
                $time=time();
                //到期时间
                $val['nlast_time']=strtotime($val['last_time']);
                if (($val['nlast_time']-$time) >= 2592000 ){
                    $arr['state']=1;
                    $this->update($val['id'],$arr);
                }elseif (($val['nlast_time']-$time) <= 0 ){
                    $arr['state']=3;
                    $this->update($val['id'],$arr);

                }else{
                    $arr['state']=2;
                    $this->update($val['id'],$arr);

                }
                unset($val['ncreate_time']);
                unset($val['nlast_time']);
            }
            $this->assign('res',$res);
            $this->assign('count',count($res));
            return view();
        }

    }


    public function adddomain()
    {
        $userlist=Userlist::all();
        $this->assign('userlist',$userlist);
        //插件表
        $addons=Db::name("addons")->where('range','domain')->select();
        $this->assign('addons',$addons);
        //域名模板
        $temp=Db::name("domain_temp")->select();
        $this->assign('templist',$temp);
        return view();
    }

    function update($id,$arr)
    {
        Db::name("domain")->where('id',$id)->update($arr);
    }

    //域名修改
    public function domain_edit()
    {

        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            if (input('id')){
                $where['id']=input('id');
                $res=Db::name("domain")->where($where)->find();
                $res['domain_temp']=Db::name("domain_temp")->where('id',$res['domain_temp'])->find()['owner_cn'];
                //组成模板数组
               $temp=Db::name("domain_temp")->select();
                $templist=[];
                foreach($temp as $key=>$val){
                    $templist[$val['id']]=$val['owner_cn'];
                }
                //插件表
                $addons=Db::name("addons")->where('range','domain')->select();
                $this->assign('addons',$addons);
                $this->assign('templist',$templist);
                $this->assign('res',$res);
                return view();
            }else{
                $this->error('非法操作');
            }
        }

    }

    public function editdomain()
    {
        $where['id']=input('post.id');
        if($where['id']!=''){
            if (input('post.password')){

                $domain=Db::name("domain")->where($where)->find();
                //旧密码
                $oldpassword=input('post.oldpassword');

                if ($oldpassword !=$domain['password']){
                    return json(['msg'=>2]);
                }

                $data['password']= input('post.password');
                if ($oldpassword == $data['password']){
                    return json(['msg'=>3]);
                }

                $res=Db::name("domain")->where($where)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                $arr=input('post.');
                $where['id']=$arr['id'];
                $data=Db::name("domain")->where($where)->find();

                $arr['update_time']=date('Y-m-d H:i:s',time());
                $res=Db::name("domain")->where($where)->update($arr);
                if ($res){
                    return json(['msg'=>1]);
                }
            }
        }else{
            $data=input('post.');
            $data['update_time']=date('Y-m-d H:i:s',time());
            $data['state']='1';
            $res=Db::name('domain')->insert($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }

    }

    //删除域名
    public function delete()
    {
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
       if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            if (input('post.id')){
                //删除员工id
                $id = input('post.id');
                $data['status']=2;
                $res=Db::name('domain')->where('id',$id)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

       }

    }

    //虚拟主机
    public function vhost()
    {
        Session::set('num',2);
        $res=Db::table('gee_vhostlist')
            ->alias('v')
            ->join('gee_userlist u','v.userid = u.id')
            ->join('gee_vhostproduct vh','v.pro_id = vh.title')
            ->field('u.username,v.root,v.ip,v.space,v.create_time,v.last_time,vh.name,v.ftpaddr,v.id')
            ->paginate(15);

        $date=date('Y-m-d H:i:s',time());
        $this->assign('date',$date);
        $this->assign('res',$res);
        $this->assign('count',count($res));
        return view();
    }

    function updatevhost($id,$arr)
    {
        Db::name("vhostbusiness")->where('id',$id)->update($arr);
    }

    //虚拟主机编辑
    public function vhost_edit()
    {
        if (input('id')){
            Session::set('num',2);
            $where['id']=input('id');
            $res=Db::name("vhostbusiness")->where($where)->find();
            //插件表
            $addons=Db::name("addons")->where('range','domain')->select();
            $this->assign('addons',$addons);
            //产品表
            $product=Db::name("vhostproduct")->where('status',1)->select();
            $this->assign('product',$product);

            //用户列表
            $userlist=Db::name("userlist")->select();
            $this->assign('userlist',$userlist);
            $this->assign('res',$res);
            return view();
        }else{
            $this->error('非法操作');
        }
    }

    //执行修改
    public function editvhost()
    {

        if (input('post.password')){
            $where['id']=input('post.id');
            $vhost=Db::name("vhostbusiness")->where($where)->find();
            //旧密码
            $oldpassword=md5(input('post.oldpassword'));

            if ($oldpassword !=$vhost['password']){
                return json(['msg'=>2]);
            }

            $data['password']= md5(input('post.password'));
            if ($oldpassword == $data['password']){
                return json(['msg'=>3]);
            }

            $res=Db::name("vhostbusiness")->where($where)->update($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }elseif (input('post.ftp_password')){

            $where['id']=input('post.id');
            $vhost=Db::name("vhostbusiness")->where($where)->find();
            //旧密码
            $oldpassword=md5(input('post.oldpassword'));

            if ($oldpassword !=$vhost['ftp_password']){
                return json(['msg'=>2]);
            }

            $data['ftp_password']= md5(input('post.ftp_password'));
            if ($oldpassword == $data['ftp_password']){
                return json(['msg'=>3]);
            }

            $res=Db::name("vhostbusiness")->where($where)->update($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }elseif (input('post.mysql_password')){

            $where['id']=input('post.id');
            $vhost=Db::name("vhostbusiness")->where($where)->find();
            //旧密码
            $oldpassword=md5(input('post.oldpassword'));

            if ($oldpassword !=$vhost['mysql_password']){
                return json(['msg'=>2]);
            }

            $data['mysql_password']= md5(input('post.mysql_password'));
            if ($oldpassword == $data['mysql_password']){
                return json(['msg'=>3]);
            }

            $res=Db::name("vhostbusiness")->where($where)->update($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }
        else{

            $arr=input('post.');
            $where['id']=$arr['id'];
            $data=Db::name("vhostbusiness")->where($where)->find();
            $arr['update_time']=date('Y-m-d H:i:s',time());
            $res=Db::name("vhostbusiness")->where($where)->update($arr);
            if ($res){
                return json(['msg'=>1]);
            }
        }

    }

    //删除域名
    public function vhostdelete()
    {
       /* $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{*/
            if (input('post.id')){
                //删除虚拟主机id
                $id = input('post.id');
                $data['state']=2;
                $res=Db::name('vhostbusiness')->where('id',$id)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }
/*
        }*/

    }

    //vps列表
    public function vps()
    {
        $date=date('Y-m-d H:i:s',time());
        $this->assign('date',$date);
        $res=Vps::vpsList();
        $this->assign('res',$res);
        return view();
    }

    //服务器托管
    public function serverhosting()
    {

    }

    //新增虚拟主机客户
    public function addVhost()
    {
        Session::set('num','2');
        $userlist=\app\admin\model\Vhostproduct::getUserList();
        $this->assign('userlist',$userlist);
        $product = \app\admin\model\Vhostproduct::all();
        $this->assign('product',$product);
        return view();

    }

    //vps新增
    public function addvps()
    {
        $userlist=Userlist::all();
        $this->assign('userlist',$userlist);
        $system=System::all(['type'=>'server']);
        $this->assign('system',$system);
        $product=VpsProduct::all();
        $this->assign('product',$product);
        return view();
    }

    //vps编辑
    public function editVps()
    {
        
    }

    //vps保存
    public function saveVps()
    {
        
    }



}
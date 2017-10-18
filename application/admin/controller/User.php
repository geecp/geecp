<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;
use app\admin\model\Userlist;
use think\Db;
use think\Session;

class User extends Base
{
    public function index()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        //找到当前方法在权限表中的对应数据
        $res=Db::name('right')->where('pid',0)->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        //找出权限表里面当前方法的数据
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

            $right=Db::name('right')->whereIn('id',$adm_group)->where('pid',4)->order('num')->select();
            $this->assign('right',$right);

            //$right=Db::name('right')->where('pid',$id)->order('num')->select();
            $user=new Userlist();
            $data=$user->paginate(15);
            //获取代理等级
            $agent=Db::name('agent')->where('status',1)->select();
            $this->assign('agent',$agent);
            $this->assign('data', $data);
            $this->assign('count', count($data));

            return view();
        }
    }

    public function adduser()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        //找到当前方法在权限表中的对应数据
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            return view();
        }

    }

    public function useradd()
    {
        $data=input('post.');
        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze, $data['phone'], $result);
        if ($data['password']!=$data['repassword']){
            return json(['msg'=>2]);
        }
        if (Db::name('userlist')->where('username',$data['username'])->find()){
            return json(['msg'=>3]);
        }
        if (strlen($data['password'])<6){
            return json(['msg'=>4]);
        }
        if (!$result){
            return json(['msg'=>5]);
        }
        if (Db::name('userlist')->where('phone',$data['phone'])->find()){
            return json(['msg'=>6]);
        }
        $data['phone']='+86'.$data['phone'];
        $data['state']=1;
        $data['userid']=$this->trand();
        $data['password']=md5($data['password']);
        $data['email']='';
        $data['creat_ip']='';
        $data['balance']='';
        $data['enterprise']='';
        $data['hisamount']='';
        $data['openid']='';
        $data['unionid']='';
        unset($data['repassword']);
        $data['creat_time']=date('Y-m-d H:i:s',time());
        $res=Db::name('userlist')->insert($data);
        if ($res){
            return json(['msg'=>1]);
        }
    }

    public function savestatus()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            return json(['msg'=>2]);
        }else{

            //修改客户状态
            $res = input('post.');
            $model = \app\admin\model\Userlist::get($res['id']);
            $model->state = $res['status'];
            if (false !== $model->save()) {
                return json(['id' => $model->id, 'status' => $model->state, 'msg' => 1]);
            }
        }

    }
    //下面的未完善
    //-----------------------------------------------------------------------------------------

    //随机生成uid
    public function trand()
    {
        $uid='8001'.mt_rand(0001,9999);
        $r=Db::name('userlist')->where('userid',$uid)->find();
        if ($r){
            $this->rand();
        }
        return $uid;
    }

    public function edituser()
    {
        $where['id'] = input('id');
        $list=Db::name('userlist')->where($where)->find();
        $list['phone']=substr($list['phone'],3);
        //获取代理等级
        $agent=Db::name('agent')->where('status',1)->select();
        $this->assign('agent',$agent);
        $this->assign('res',$list);
        return view();
    }


    public function useredit()
    {
        $where['id']=input("post.id");
        $data=input('post.');
        unset($data['id']);

        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze, $data['phone'], $result);
        if (!$result){
            return json(['msg'=>5]);
        }
        if($data['password']==''){
            unset($data['password']);
        }else{
            if (strlen($data['password'])<6){
                return json(['msg'=>4]);
            }
            $data['password']=md5($data['password']);
        }
        $data['phone']='+86'.$data['phone'];
        unset($data['repassword']);
        $r=Db::name('userlist')->where($where)->update($data);
        if ($r){
            return json(['msg'=>1]);
        }
    }

    public function delete()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            return json(['msg'=>2]);
        }else{
            //删除用户id
            $id = input('post.id');
            $arr=explode(',',$id);

            $data['state']=3;
            $res=Db::name('userlist')->whereIn('id',$arr)->update($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }

    }

}
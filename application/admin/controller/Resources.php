<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;

use app\admin\model\Vhostgroup;
use app\admin\model\Vhostserver;
use think\Db;
use think\Session;

class Resources extends Base
{
    public  function index()
    {
        return view();
    }
   //虚拟主机
    public function vhost()
    {

        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('pid',0)->where('url',$url)->find()['id'];

        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
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
        }else {
            Session::set('num', 1);

            $server = new Vhostserver();
            $data = $server->where('state', '<>', '1')->paginate(10);

            foreach ($data as $val) {
                $val['cate_id'] = Db::name("vhostgroup")->where('id', $val['cate_id'])->find()['name'];
            }

            $this->assign('data', $data);
            $this->assign('count', count($data));

            return view();
        }
    }
    //修改虚拟主机状态
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
            $model =Vhostserver::get($res['id']);
            $model->status = $res['status'];
            if (false !== $model->save()) {
                return json(['id' => $model->id, 'status' => $model->status, 'msg' => 1]);
            }
        }
    }

    //添加虚拟主机
    public function addvhost()
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
            $this->error('您还没有获取操作权限');
        }else{

            $group=Db::name("vhostgroup")->where('status',1)->select();
            $this->assign('group',$group);
            //接口
            $addons=Db::name("addons")->where('range','vhost')->select();
            $this->assign('addons',$addons);
            Session::set("num",1);
            return view();
        }

    }

    //执行添加
    public function vhostadd()
    {
        $data=input('post.');
        if (!input('post.pattern')){
            $data['pattern']=2;
            $data['hostname']='http://'.input('post.hostname');
        }else{
            $data['hostname']='https://'.input('post.hostname');
        }
        $data['state']=2;
        $res=Db::name('vhostserver')->insert($_POST);
        if ($res){
            return json(['msg'=>1]);
        }
    }
    //加载修改页面
    public function vhost_edit()
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
            $this->error('您还没有获取操作权限');
        }else{
            //处理左侧菜单
            $name=Db::name('right')->where('id',10)->field('munu')->find()['munu'];
            $this->assign('name',$name);
            Session::set('id',10);
            $right=Session::get("right");
            $this->assign('right',$right);
            Session::set("num",1);
            if (input('id')){
                $res=Db::name("vhostserver")->where('id',input('id'))->find();

               /* if ($res['pattern'] == 1){
                    if(!preg_match("/^(https:\/\/).*$/",$res['hostname'])){
                        $res['hostname']='https://'.$res['hostname'];
                    }

                }else{
                    if(!preg_match("/^(http:\/\/).*$/",$res['hostname'])){
                        $res['hostname']='http://'.$res['hostname'];
                    }
                }*/
                $this->assign('res',$res);
                $group=Db::name("vhostgroup")->select();
                $this->assign('group',$group);
                //接口
                $addons=Db::name("addons")->where('range','vhost')->select();
                $this->assign('addons',$addons);
            }else{
                $this->error('非法操作');
            }
            return view();
        }

    }
    //执行修改
    public function editvhost()
    {

        $where['id']=input('post.id');
        $data=input('post.');

        //如果改变了状态
        if (!input('post.pattern')){
            $data['pattern'] =2;
        }

            if(preg_match("/^(https:\/\/).*$/",$data['hostname'])){
                $data['hostname']=str_replace("https://","",$data['hostname']);
            }
            //如果是https状态
            if(preg_match("/^(http:\/\/).*$/",$data['hostname'])){
                $data['hostname']=str_replace("http://","",$data['hostname']);

            }
        unset($data['id']);
        $data['updat_time']=time();
        if (!input('post.status')){
            $data['status']=0;
        }
        if (!input('post.pattern')){
            $data['pattern']=2;
        }

        $res=Db::name("vhostserver")->where($where)->update($data);
        if ($res){
            return json(['msg'=>1]);
        }
    }
    //删除虚拟主机
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
                //删除服务器
                $id = input('post.id');
                $data['state']=1;
                $res=Db::name('vhostserver')->where('id',$id)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

       }

    }
    //服务器组展示
    public function group()
    {
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取查看权限');
        }else{
            Session::set('num',1);
            $group=new Vhostgroup();
            $data=$group->paginate(10);
            $this->assign('data',$data);
            $this->assign('count',count($data));
            return view();
        }

    }
    //添加服务器组页面
    public function addgroup()
    {
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
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
    //执行添加
    public function groupadd()
    {
        $_POST['status']=1;
        $_POST['creat_time']=time();
        $res=Db::name('vhostgroup')->insert($_POST);
        if ($res){
            return json(['msg'=>1]);
        }
    }
    //加载修改页面
    public function group_edit()
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
            $this->error('您还没有获取操作权限');
        }else{
            if (input('id')){
                //处理左侧菜单
                $name=Db::name('right')->where('id',10)->field('munu')->find()['munu'];
                $this->assign('name',$name);
                Session::set('id',10);
                $right=Db::name('right')->where('pid',10)->order('num')->select();
                $this->assign('right',$right);
                Session::set("num",1);

                $where['id']=input('id');
                $res=Db::name("vhostgroup")->where($where)->find();

                $this->assign('res',$res);
                return view();
            }else{
                $this->error('非法操作');
            }

        }

    }
    //执行修改
    public function edit_group()
    {
        $where['id']=input('post.id');
        $data=input('post.');
        unset($data['id']);
        $data['updat_time']=time();
        $res=Db::name("vhostgroup")->where($where)->update($data);
        if ($res){
            return json(['msg'=>1]);
        }
    }

    //修改服务器组状态
    public function savegrostatus()
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
         }else {

             //修改客户状态
             $res = input('post.');
             $model = Vhostgroup::get($res['id']);
             $model->status = $res['status'];
             if (false !== $model->save()) {
                 return json(['id' => $model->id, 'status' => $model->status, 'msg' => 1]);
             }
         }
    }

    //删除虚拟主机
    public function deletegroup()
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
         }else {
             if (input('post.id')){
                 //删除服务器
                 $id = input('post.id');
                 $data['state'] = 1;
                 $res = Db::name('vhostgroup')->where('id', $id)->update($data);
                 //查询有没有子级产品
                 $server = new Vhostserver();
                 $vhost = $server->where('state', '<>', '1')->paginate(10);
                 foreach ($vhost as $val) {
                     if ($val['cate_id'] == $id) {
                         return json(['msg' => 4]);
                     }
                 }
                 if ($res) {
                     return json(['msg' => 1]);
                 }
             }

         }
    }

    //云服务器
    public function vcloud()
    {
        return 1;
    }
}


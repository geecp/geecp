<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;
use app\admin\model\Admgroup;
use app\admin\model\Admmember;
use app\admin\model\Attachment;
use app\admin\model\Group;
use think\Db;
use think\Request;
use think\Session;

class Member extends Base
{
    public function index()
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
        }else{
            Session::set('num',1);
            Session::set('id',8);
            $right=Db::name('right')->whereIn('id',$adm_group)->where('pid',8)->order('num')->select();
            $this->assign('right',$right);
            $name=Db::name('right')->where('id',8)->field('munu')->find()['munu'];
            $this->assign('name',$name);
            $member=new Admmember();
            $data=$member->where('status',1)->paginate(10);
            //$member=Db::name('admmember')->select();
            foreach ($data as $key=>$val){
                $data[$key]['adm_group']=Db::name('admgroup')->where('id',$val['adm_group'])->find()['author'];
            }
            $this->assign('data', $data);
            $this->assign('count', count($data));
            return view();
        }
    }

    // 文件上传提交
    public function up(Request $request)
    {
        // 获取表单上传文件
        $file = $request->file('videoid');
        // 上传文件验证
        $result = $this->validate(['file' => $file], ['file' => 'require|image'], ['file.require' => '请选择上传文件', 'file.image' => '非法图像文件']);
        if (true !== $result) {
            $this->error($result);
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if ($info) {
            $this->success('文件上传成功：' . $info->getRealPath());
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    //添加员工页面
    public function memberadd()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        Session::set("num",1);
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            $group=Db::name('admgroup')->select();
            $this->assign('group',$group);
            return view();
        }
    }
    //添加员工
    public function addmember()
    {
        $data=input('post.');
        $data['status']=1;
        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze, $data['mobile'], $result);
        if (!$result) {

        }
        if(strlen($data['password']) <6){
            return json(['msg'=>4]);
        }
        //查询手机号是否重复
        $resu=Db::name('admmember')->where('mobile',$data['mobile'])->find();
        if ($resu) {
            return json(['msg'=>6]);
        }
        $data['password']=md5(md5($data['password'].'qiduo'));;
        $data['creat_time']=date('Y/m/d H:i:s',time());

        $result=Db::name('attachment')->find();

        if($result['bucket'] =='' && $result['status'] ==2){
            $arr['info'] = 'BOS 配置错误！';
            return json(['msg'=>2]);
        }

        $file=$_FILES;

        if(!$file){
            return json(['msg'=>11]);
        }
        $BOS_TEST_CONFIG =
            array(
                'credentials' => array(
                    'ak' => $result['ak'],
                    'sk' => $result['sk'],
                ),
                'endpoint' => $result['domain'],
            );

        if($result['status'] ==2)
        {
            $files=request()->file('file');
            // 移动到框架应用根目录/uploads/ 目录下
            $info = $files->move(ROOT_PATH . '/public'. DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息

                $code= str_replace('\\','/',$info->getSaveName());
                $data['img']="http://".$_SERVER["HTTP_HOST"]."/uploads/". $code;

                $res=Db::name('admmember')->insert($data);
                if ($res) {
                    return json(['msg'=>1]);
                }
            }
        }else{
            //选择BOS上传，查询是否配置BOS
            $img=date('YmdHis',time()).rand(100,999).'.jpg';
            $data['img']=bos($result['bucket'],$img,$file['file']['tmp_name'],$BOS_TEST_CONFIG);
            $data['img']=$result['domain'].'/'.$result['bucket'].'/'.$img;

            $res=Db::name('admmember')->insert($data);

            if ($res) {
                return json(['msg'=>1]);
            }
        }

/*
        $data=input('post.');
        $data['status']=1;

        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze, $data['mobile'], $result);
        if (!$result) {
            return json(['msg'=>5]);
        }
        if(strlen($data['password']) <6){
            return json(['msg'=>4]);
        }

        $resu=Db::name('admmember')->where('mobile',$data['mobile'])->find();
        if ($resu) {
            return json(['msg'=>6]);
        }
        $data['password']=md5($data['password']);
        $data['creat_time']=date('Y/m/d H:i:s',time());
        $res=Db::name("admmember")->insert($data);
        if ($res){
            return json(['msg'=>1]);
        }*/
    }
    //加载修改页面
    public function memberedit()
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
            if (input('id')){
                $name=Db::name('right')->where('id',8)->field('munu')->find()['munu'];
                $this->assign('name',$name);
                Session::set('id',8);
                $right=Session::get("right");

                $this->assign('right',$right);
                $where['id']=input('id');
                $res=Db::name('admmember')->where($where)->find();
                $this->assign('res',$res);
                $group=Db::name('admgroup')->select();
                $this->assign('group',$group);
                return view();
            }else{
                $this->error('非法操作');
            }

        }
    }
    //修改员工
    public  function  editmember()
    {

        $arr=input('post.');
        $where['id']=input('post.id');
        $res=Db::name('admmember')->where($where)->find();
        unset($arr['id']);
        $arr['updat_time']=date('Y/m/d H:i:s',time());
        if ($res['password'] != $arr['password']){
            $arr['password']=md5($arr['password']);
        }

        //bos配置
        $result=Db::name('attachment')->find();

        if($result['bucket'] =='' && $result['status'] ==2){
            $arr['info'] = 'BOS 配置错误！';
            return json(['msg'=>2]);
        }

        if(!empty($_FILES['file']['tmp_name'])){

            $BOS_TEST_CONFIG =
                array(
                    'credentials' => array(
                        'ak' => $result['ak'],
                        'sk' => $result['sk'],
                    ),
                    'endpoint' => $result['domain'],
                );

            if($result['status'] ==2)
            {
                $files=request()->file('file');
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $files->move(ROOT_PATH . '/public'. DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息

                    $code= str_replace('\\','/',$info->getSaveName());
                    $arr['img']="http://".$_SERVER["HTTP_HOST"]."/uploads/". $code;

                    $resu=Db::name('admmember')->where($where)->update($arr);
                    if ($resu) {
                        return json(['msg'=>1]);
                    }
                }
            }else{
                //选择BOS上传，查询是否配置BOS
                $img=date('YmdHis',time()).rand(100,999).'.jpg';
                $arr['img']=bos($result['bucket'],$img,$_FILES['file']['tmp_name'],$BOS_TEST_CONFIG);
                $arr['img']=$result['domain'].'/'.$result['bucket'].'/'.$img;

                $resu=Db::name('admmember')->where($where)->update($arr);

                if ($resu) {
                    return json(['msg'=>1]);
                }
            }
        }else{

            $resu=Db::name('admmember')->where($where)->update($arr);
            if ($resu){
                return json(['msg'=>1]);
            }
        }


/*
        $where['id']=input('post.id');
        $arr=input('post.');
        $result=Db::name('admmember')->where($where)->find();
        if ($result['password'] != $arr['password']){
            $arr['password']=md5($arr['password']);
        }
        unset($arr['id']);
        $arr['updat_time']=date('Y/m/d H:i:s',time());
        $res=Db::name('admmember')->where($where)->update($arr);
        if ($res){
            return json(['msg'=>1]);
        }*/
    }
    //删除员工
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
                $arr=explode(',',$id);
                $data['status']=2;
                $res=Db::name('admmember')->whereIn('id',$arr)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

        }

    }
    //用户组列表
    public function group()
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
            //权限
            $adm_group=Db::name('admgroup')->where('id',Session::get('admin')['adm_group'])->find()['authority'];
            //菜单处理
            Session::set('id',8);
            $right=Db::name('right')->whereIn('id',$adm_group)->where('pid',8)->order('num')->select();
            $this->assign('right',$right);
            $name=Db::name('right')->where('id',8)->field('munu')->find()['munu'];
            $this->assign('name',$name);

            Session::set('num',2);

            $group=new Admgroup();
            $data=$group->where('status',1)->paginate(10);
            $this->assign('data',$data);
            $this->assign('count',count($data));
            $this->assign('count',count($data));
            return view();
        }

    }
    //添加用户组
    public function groupadd()
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
            Session::set("num",2);
            $data=[];
            $res=Db::name('right')->where('pid',0)->select();
            foreach($res as $key =>$val){
                $res[$key]['son']=Db::name('right')->where('pid',$val['id'])->select();
                $res[$key]['count']=Db::name('right')->where('pid',$val['id'])->count();
                foreach($res[$key]['son'] as $k =>$v){
                    $res[$key]['son'][$k]['small']=Db::name('right')->where('pid',$v['id'])->select();
                }
            }
            $this->assign('res',$res);
            return view();
        }
    }

    //添加用户组
    public function addgroup()
    {
            $data=input('post.');
            if($data['author']==''){
                return json(['msg'=>'用户组名不得为空','code'=>2]);
            }
            $arr1['author']=$data['author'];
            unset($data['author']);
            $arr='';
            $i=0;
            foreach($data as $k=> $val){
                if ($val != ''){
                    $arr[$i]=implode(',',array_merge($val));
                }
                $i++;
            }
                $arr1['authority']=implode(',',array_merge($arr));


            $arr1['status']=1;
            $arr1['creat_time']=date('Y/m/d H:i:s',time());

            unset($data);
            $res=Db::name('admgroup')->insert($arr1);
            if ($res){
                return json(['msg'=>'添加成功','code'=>1]);
            }
    }
    //编辑用户组
    public function groupedit()
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
            if (input('id')){
                //左侧菜单的手动修改
                Session::set('id',8);
                $right=Session::get("right");
                $this->assign('right',$right);
                $name=Db::name('right')->where('id',8)->field('munu')->find()['munu'];
                $this->assign('name',$name);
                Session::set('num',2);
                //该用户的权限查询
                $where['id']=input('id');
                $res=Db::name("admgroup")->where($where)->find();
                $res['authority']=explode(',',$res['authority']);
                $this->assign('res',$res);

                //权限列表

                $group=Db::name('right')->where('pid',0)->order('num')->select();
                foreach($group as $key =>$val){

                    $group[$key]['son']=Db::name('right')->where('pid',$val['id'])->select();
                    $group[$key]['count']=Db::name('right')->where('pid',$val['id'])->count();
                    foreach($group[$key]['son'] as $k =>$v){

                        $group[$key]['son'][$k]['small']=Db::name('right')->where('pid',$v['id'])->select();

                        foreach($group[$key]['son'][$k]['small'] as $kk=>$s){
                            if (in_array($s['id'],$res['authority'])){
                                $group[$key]['son'][$k]['small'][$kk]['data']=1;
                            }else{
                                $group[$key]['son'][$k]['small'][$kk]['data']=2;
                            }
                        }

                        if (in_array($v['id'],$res['authority'])){
                            $group[$key]['son'][$k]['data']=1;
                        }else{
                            $group[$key]['son'][$k]['data']=2;
                        }
                    }
                    //一级
                    if (in_array($val['id'],$res['authority'])){
                        $group[$key]['data']=1;
                    }else{
                        $group[$key]['data']=2;
                    }
                }
                $this->assign('author',$res);
                $this->assign('group',$group);
                return view();
            }else{
                $this->error('非法操作');
            }

        }

    }
    //编辑用户组
    public function editgroup()
    {
        $where['id']=input('post.id');
        $data=input('post.');
        $arr1['author']=$data['author'];
        if($data['author']==''){
            return json(['msg'=>'用户组名不得为空','code'=>2]);
        }
        unset($data['author']);
        unset($data['id']);
        $arr1['authority']=implode(',',$data['authority']);
        $arr1['status']=1;
        $arr1['updat_time']=date('Y/m/d H:i:s',time());

        unset($data);
        $res=Db::name('admgroup')->where($where)->update($arr1);
        if ($res){
            return json(['msg'=>'修改成功','code'=>1]);
        }else{
            return json(['msg'=>'修改失败','code'=>2]);
        }
    }
    //删除用户组
    public function deleteGroup()
    {
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')->where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            if (input('post.id')){
                //删除管理组
                $id = input('post.id');
                $arr=explode(',',$id);
                $data['status']=0;
                $res=Db::name('admgroup')->whereIn('id',$arr)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

        }

    }
}
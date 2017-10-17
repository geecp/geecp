<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;
use think\Db;
use think\Session;

class Setting extends Base
{
    //首页站点配置
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

            if (input('id')){
                $id=input('id');
                Session::set('id',$id);
            }
            //从数据库中查询所需数据
            $res=Db::name('settingsite')->find();
            $this->assign('res',$res);
            return view();
        }

    }


    //保存站点配置
    public function save_site()
    {
        //接受数据，存入数据库
        $where['id']=1;
        $_POST['update_time']=time();
        //bos配置
        $result=Db::name('attachment')->find();
        $arr=$_POST;
        if($result['bucket'] =='' && $result['status'] ==2){
            $arr['status'] = 2;
            echo json_encode($arr);die;
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
                    $arr['pic']="http://".$_SERVER["HTTP_HOST"]."/uploads/". $code;

                    $resu=Db::name('settingsite')->where($where)->update($arr);
                    if ($resu) {
                        $arr['status'] = 1;
                        echo json_encode($arr);die;
                    }
                }
            }else{
                //选择BOS上传，查询是否配置BOS
                $img=date('YmdHis',time()).rand(100,999).'.jpg';
                $arr['pic']=bos($result['bucket'],$img,$_FILES['file']['tmp_name'],$BOS_TEST_CONFIG);
                $arr['pic']=$result['domain'].'/'.$result['bucket'].'/'.$img;

                $resu=Db::name('settingsite')->where($where)->update($arr);

                if ($resu) {
                    $arr['status'] = 1;
                    echo json_encode($arr);die;
                }
            }
        }else{
            unset($arr['file']);
            $res=Db::name('settingsite')->where($where)->update($arr);
            if ($res) {
                $arr['status'] = 1;
                echo json_encode($arr);die;
            }
        }

    }

    public function pay()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            $name=Db::name('right')->where('id',7)->field('munu')->find()['munu'];
            $this->assign('name',$name);
            $right=Db::name('right')->whereIn('id',$adm_group)->where('pid',7)->order('num')->select();
            $this->assign('right',$right);
            Session::set('id',7);
            Session::set('num',2);
            $res=Db::name('pay')->find();
            $this->assign('res',$res);
            return view();
        }
    }

    public function save_pay()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $arr['status'] = 2;
            echo json_encode($arr);
        }else{
            $where['id']=input('post.id');
            $data=input('post.');

            unset($data['id']);
            $data['update_time']=time();
            $res=Db::name('pay')->where($where)->update($data);

            if ($res){
                $arr['status'] = 1;
                echo json_encode($arr);
            }else{
                $ar['info'] = '修改失败';
                echo json_encode($ar);
            }
        }

    }
    public function attachment()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else {
            $name=Db::name('right')->where('id',7)->field('munu')->find()['munu'];
            $this->assign('name',$name);

            $right = Db::name('right')->whereIn('id', $adm_group)->where('pid',7)->order('num')->select();
            $this->assign('right',$right);
            Session::set('id',7);
            Session::set('num',3);
            $res=Db::name('attachment')->find();
            $this->assign('res',$res);
            return view();
        }

    }
    public function save_att(){

        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $arr['status'] = 2;
            echo json_encode($arr);
        }else{

            $where['id']=input('post.id');
            unset($_POST['id']);
            $arr=input('post.');
            $arr['update_time']=time();
            $cname=input('post.cname');
            $dname=input('post.dname');
            if ($cname ='' || $dname !=''){
                $arr['cname']=strtolower($cname);
                $arr['dname']=strtolower($dname);
            }

            $res=Db::name('attachment')->where($where)->update($arr);
            if ($res){
                $arr['status'] = 1;
                echo json_encode($arr);
                die;
            }else{
                $ar['info'] = '添加失败';
                echo json_encode($ar);
                die;
            }
        }

    }

    public function yzm()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else {
            $name=Db::name('right')->where('id',7)->field('munu')->find()['munu'];
            $this->assign('name',$name);
            $right = Db::name('right')->whereIn('id', $adm_group)->where('pid', 7)->order('num')->select();
            $this->assign('right',$right);
            Session::set('id',7);
            Session::set('num',4);
            $res=Db::name('yzm')->find();
            $this->assign('res',$res);
            return view();
        }

    }

    public function save_yzm()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $arr['status'] = 2;
            echo json_encode($arr);
        }else{

            $where['id']=input('post.id');
            unset($_POST['id']);
            $arr=input('post.');
            $arr['update_time']=time();
            $arr['status']=1;
            $res=Db::name('yzm')->where($where)->update($arr);
            if ($res){
                $arr['status'] = 1;
                echo json_encode($arr);
                die;
            }else{
                $ar['info'] = '添加失败';
                echo json_encode($ar);
                die;
            }
        }
    }

    public function email()
    {
        $res=Db::table('gee_email')
            ->alias('e')
            ->join('gee_product p','e.type = p.id')
            ->field('e.id,e.content,p.name,e.time')
            ->paginate(15);
        $this->assign('count',count($res));
        $this->assign('res',$res);
        return view();
    }

    //编辑email模板
    public function editemail()
    {
        $id=input('id');
        if($id!=''){
            //修改
            $res=Email::get($id);
            $this->assign('data',$res);
        }
        //获取所有的产品类型
        $product = Db::name('product')->select();
        $this->assign('product',$product);
        return view();
    }

    //保存email模板
    public function saveemail()
    {
        $data=input('post.');
        $data['time']=date('Y-m-d H:i:s',time());
        $email=new Email;
        if($data['id']!=''){
            //上一步为修改
            $result=$email->save($data,$data['id']);
            if($result){
                return json(['msg'=>'修改成功','code'=>'1']);
            }else{
                return json(['msg'=>'修改失败','code'=>'2']);
            }
        }else{
            //上一步是新增
            $result=$email->save($data);
            if($result){
                return json(['msg'=>'新增成功','code'=>'1']);
            }else{
                return json(['msg'=>'新增失败','code'=>'2']);
            }
        }

    }

    //删除游客
    public function delemail()
    {
        $id=input('post.id');
        $res=Email::destroy($id);
        if($res){
            return json(['msg'=>'1']);
        }else{
            return json(['msg'=>'2']);
        }
    }
}

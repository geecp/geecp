<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/27
 * Time: 17:25
 */
namespace app\admin\controller;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use think\Db;
use think\Request;
use think\Controller;
use think\Session;
class Login extends Controller
{
    public function __construct(Request $request = null)
    {
        /*//查看站点的状态
        $status=Db::name('settingsite')->select();
        if($status==2){
            $this->redirect('error/index');
        }*/
        parent::__construct($request);
        $this->view->replace(['__PUBLIC__' => '/static',]);
    }

    public function index()
    {
        return view();
    }
    public function login()
    {
        $_POST['password'] = md5(md5($_POST['password'].'qiduo'));
        // dump($_POST);die;
        $res = Db::name('admmember')->where($_POST)->find();

        $user=Db::name("admmember")->where("username",$_POST['username'])->find();
        //用户名不存在
        if (!$user){
            return json(['msg'=>2]);
        }
        //被封禁
        if ($res['status']==2){
            return json(['msg'=>3]);
        }

        if ($res) {
            Session::set('admin',$res);
            return json(['msg'=>1]);
           $this->redirect('Setting/index');
        }else{
            //密码不正确
            return json(['msg'=>4]);
        }
    }
    //注销
    public function zhuxiao()
    {
        Session::delete('admin');
        $this->redirect('Login/index');
    }
    //账号设置
    public function userset()
    {
        //处理继承数据
        $adm_group=Db::name('admgroup')->where('id',Session::get('admin')['adm_group'])->find()['authority'];
        //根据用户当前的用户组，获得左侧菜单栏
        $menu=Db::name('right')->whereIn('id',$adm_group)->where('pid',0)->order('num')->select();
        //将目录传到前台
        $this->assign('menu',$menu);
        $name='账号设置';
        $this->assign('name',$name);
        Session::delete('id');

        $user=Session::get('admin');
        $this->assign('user',$user);

        //当前登录人信息

        return view();
    }
    //修改账号信息
    public function user_save()
    {
        //拿到当前登录人的资料
        $user=Session::get('admin');
        $where['id']=$user['id'];

        //修改密码
        if (input('post.password')){
            //旧密码
            $oldpassword=md5(md5(input('post.oldpassword').'qiduo'));

            if ($oldpassword!=$user['password']){
                return json(['msg'=>2]);
            }

            $data['password']= md5(md5(input('post.password').'qiduo'));
            if ($oldpassword == $data['password']){
                return json(['msg'=>3]);
            }

            $res=Db::name("admmember")->where($where)->update($data);
            if ($res){
                return json(['msg'=>1]);
            }
        }else{
            //修改其他信息(对信息进行审核)
            $data=input('post.');

            $zhengze = '/^(1[3-9][0-9])\d{8}$/';
            preg_match($zhengze, $data['mobile'], $result);
            if (!$result) {
                return json(['msg'=>6]);
            }
            if ($data['mobile']!= $user['mobile']){
                //查询手机号是否重复
                $resu=Db::name('admmember')->where('mobile',$data['mobile'])->find();
                if ($resu) {
                    return json(['msg'=>4]);
                }

            }
            if($data['username']!=$user['username']){
                //查询用户名是否重复
                $res=Db::name('admmember')->where('username',$data['username'])->find();
                if ($res) {
                    return json(['msg'=>5]);
                }
            }

            //bos配置
            $result=Db::name('attachment')->find();

            if($result['bucket'] =='' && $result['status'] ==2){
                $arr['info'] = 'BOS 配置错误！';
                return json(['msg'=>2]);
            }


            //文件上传(如果有头像上传)

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
                        $data['img']="http://".$_SERVER["HTTP_HOST"]."/uploads/". $code;
                        unset($data['file']);
                        $resu=Db::name('admmember')->where($where)->update($data);
                        if ($resu) {
                            return json(['msg'=>1]);
                        }
                    }
                }else{
                    //选择BOS上传，查询是否配置BOS
                    $img=date('YmdHis',time()).rand(100,999).'.jpg';
                    $data['img']=bos($result['bucket'],$img,$_FILES['file']['tmp_name'],$BOS_TEST_CONFIG);
                    $data['img']=$result['domain'].'/'.$result['bucket'].'/'.$img;
                    unset($data['file']);
                    $resu=Db::name('admmember')->where($where)->update($data);

                    if ($resu) {
                        return json(['msg'=>1]);
                    }
                }
            }else{
                unset($data['file']);
                $resu=Db::name('admmember')->where($where)->update($data);
                if ($resu){
                    return json(['msg'=>1]);
                }
            }


        }
    }

}
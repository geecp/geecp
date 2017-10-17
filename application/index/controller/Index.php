<?php
namespace app\index\controller;
use think\Db;
use think\Controller;

class Index extends Controller
{
    public function active()
    {
        $data=input('code');
        $data=base64_decode($data);
        $data=explode('/',$data);
        $nowtime=time();
        $oldtime=$data[1];
        $yutime=$nowtime-$oldtime;
        if($yutime<$data[2]){
            $where['userid']=$data[1];
            $cod=['emailva'=>1];
            $res=Db::name('userlist')->where($where)->update($cod);
            if($res){
                return "<script>alert('您的帐户已成功绑定邮箱！！');window.open('','_self');window.close();</script>";
            }else{
                return "<script>alert('帐户绑定邮箱失败，请稍后重试！！')</script>";
            }
        }else{
            return "<script>alert('激活邮件已过期，请重新发送！！')</script>";
        }

    }
}

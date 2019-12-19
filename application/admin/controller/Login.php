<?php
namespace app\admin\controller;

use app\admin\model\GeeAdminuser;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeStaffgroup; // 员工表
use app\admin\model\GeeWebbasic; // 员工组表
use think\Controller;
// 基本信息表

class Login extends Controller
{
    //登录页面
    public function index()
    {

        $basic = new GeeWebbasic();
        //网站基本数据
        $basicInfo = $basic->where('id = 1')->find();
        $this->_basicInfo = $basicInfo;
        $this->assign("basicInfo", $basicInfo);
        session('_basicInfo', $basicInfo);
        if (isset($_COOKIE['token']) && !empty($_COOKIE['token']) && jwt_decode($_COOKIE['token'])) {
            return $this->redirect('admin/Index/index');
        }
        return $this->fetch('Login/login');
    }
    //登录验证
    public function auth()
    {
        $data = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功！',
        ];
        $user = new GeeAdminuser();

        if (!isset($data['username']) || empty($data['username'])) {
            $ret['status'] = 422;
            $ret['msg'] = '用户名或密码错误！';
            return $ret;
        }
        if (!isset($data['password']) || empty($data['password'])) {
            $ret['status'] = 422;
            $ret['msg'] = '用户名或密码错误！';
            return $ret;
        }
        $userinfo = $user->where('`username` = "' . $data['username'] . '"')->find();
        if (!$userinfo) {
            $ret['status'] = 422;
            $ret['msg'] = '用户名或密码错误！';
            return $ret;
        }
        if ($userinfo['status'] == '1') {
            $ret['status'] = 422;
            $ret['msg'] = '员工账号已被禁用，请联系管理员！';
            return $ret;
        }
        if (password_verify($data['password'], $userinfo['password'])) {
            $log = new GeeLog();
            $saveInfo = [
                'content' => $userinfo['name'] . ' 登录了，登录IP为：' . get_ip(),
                'ip' => get_ip(),
            ];
            $logres = $log->save($saveInfo);
            $userres = $user->save(['last_login_time' => time()], ['id' => $userinfo['id']]);
            if ($logres && $userres) {
                $to_info = [
                    'user_id' => $userinfo['id'],
                    'username' => $userinfo['username'],
                    'name' => $userinfo['name'],
                    'email' => $userinfo['email'],
                    'phone' => $userinfo['phone'],
                    'ip' => $userinfo['ip'],
                    'status' => $userinfo['status'],
                    'group' => db('gee_staffgroup')->where('id = ' . $userinfo['group_id'])->find()['name'],
                    'rend' => rand(0, 100),
                ];
                $sign = $this->sign($to_info);
                $to_info['sign'] = $sign;
                $token = jwt_encode($to_info);
                // setcookie('token',$token,time() + 3600 * 2,'/');
                $ret['token'] = $token;
                return $ret;
            }
        }
        $ret['status'] = 422;
        $ret['msg'] = '用户名或密码错误！';
        return $ret;
    }
    /**
     * 签名
     */
    public function sign($to_info)
    {
        // 生成签名
        ksort($to_info);
        $sign = '';
        foreach ($to_info as $key => $var) {
            $sign .= $key . '=' . $var . '&';
        }
        $sign = trim($sign, '&');
        return md5($sign);
    }
    /**
     * 退出
     */
    public function logout()
    {
        setcookie('token', '', time() - 3600, '/');
        return $this->redirect('admin/Login/index');
        die;
    }
}

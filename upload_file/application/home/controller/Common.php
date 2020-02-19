<?php
namespace app\home\controller;

use app\admin\model\GeeAnnexconfig;
use app\admin\model\GeeEmailconfig; // 请求类
use app\admin\model\GeeLog; //验证函数类库
use app\admin\model\GeeMsgmodel; //SMTP邮件类库
use app\admin\model\GeePicture; //七牛云存储类库
use app\admin\model\GeeProductType; //百度云存储类库
use app\admin\model\GeeUser; // 日志表
use app\admin\model\GeeWebbasic; // 文件表
use app\index\model\GeeWebroute; // 系统SMTP邮件配置表
use BaiduBce\Services\Bos\BosClient; // 基本信息表
use phpmailer\phpmailer; // 消息模板表
use tegic\qiniu\Qiniu; // 附件设置表
use think\Controller; // 用户表
use think\Request; // 前台路由表
// 产品类型表

/**
 * 前置操作
 */
class Common extends Controller
{
    protected $_userInfo;
    /**
     * 前置操作
     */
    public function _initialize()
    {
        $basic = new GeeWebbasic();
        //网站基本数据
        $basicInfo = $basic->where('id = 1')->find();
        $this->_basicInfo = $basicInfo;
        $this->assign("basicInfo", $basicInfo);
        session('_basicInfo', $basicInfo);
        if (isset($_COOKIE['token']) && !empty($_COOKIE['token']) && jwt_decode($_COOKIE['token'])) {
            $user = new GeeUser();
            //JWT 数据
            $res = jwt_decode($_COOKIE['token']);
            $res = object_toArray($res);
            $res = $user->where('id = ' . $res['jti']['user_id'])->find();
            $this->_userInfo = $res;
            $this->assign("userinfo", $res);
            session('_userInfo', $res);
        } else {
            // return $this->redirect('index/Login/index');
        }
    }

    /**
     * 获取当前路由
     */
    public function nowRoute($startRoute1, $redirectUrl)
    {
        $route = new GeeWebroute();
        foreach ($startRoute1 as $key => $var) {
            if (vali_data('url', $var['code'])) {
                $varCode = $var['code'];
            } else {
                $varCode = '/' . $var['code'];
            }
            if (strstr($redirectUrl, $varCode . '/') !== false) {
                $nowStart = $var;
                $routeRes = routeAnalysis($var['id']);
                foreach ($routeRes as $k => $v) {
                    if ($redirectUrl === $varCode . '/' . $v['code'] || $redirectUrl === $varCode . '/' . $v['code'] . '.html') {
                        $nowStart['child'] = $v;
                    }
                }
            }
        }
        return $nowStart;
    }
    /**
     * @name 公共单图上传
     */
    public function uploadimg()
    {
        $file = request()->file('file');
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $annex = new GeeAnnexconfig();
        $annexConfig = $annex->where('id = 1')->find();
        $picture = new GeePicture();
        $filetypes = array('png', 'jpg', 'jpeg', 'gif');
        if (!in_array(trim(strrchr($file->getInfo()['type'], '/'), '/'), $filetypes) || filesize($file->getInfo()['tmp_name']) > 1024 * 1024 * 2) {
            $ret['status'] = 422;
            $ret['msg'] = '上传失败；文件格式或文件大小不符合规范';
            return json($ret);
        }
        if ($annexConfig) {
            if ($annexConfig['type'] == 'ftp') {

                return;
            } elseif ($annexConfig['type'] == 'bos') {
                $BOS_TEST_CONFIG = [
                    'credentials' => [
                        'accessKeyId' => $annexConfig['bos_ak'],
                        'secretAccessKey' => $annexConfig['bos_sk'],
                    ],
                    'endpoint' => $annexConfig['bos_domain'],
                ];
                $client = new BosClient($BOS_TEST_CONFIG);
                $bucketName = '';

                $fileurl = file_get_contents($file->getInfo()['tmp_name']);
                $filenames = time() . rand(0, 999999) . '.' . trim(strrchr($file->getInfo()['type'], '/'), '/');
                $url = $client->putObjectFromString($bucketName, $filenames, $fileurl);
                $resurl = $annexConfig['bos_domain'] . '/' . $filenames;
                $data['url'] = $resurl;
                $data['sha1'] = '';
                $data['md5'] = '';
                $picture->save($data);
                $ret['data'] = $resurl;

                return json($ret);
            } elseif ($annexConfig['type'] == 'qiniu') {
                $qiniu = new Qiniu($annexConfig['qiniu_ak'], $annexConfig['qiniu_sk'], $annexConfig['qiniu_bucket']);
                $res = $qiniu->upload();
                if ($res) {
                    $resurl = $annexConfig['qiniu_domain'] . '/' . $res;
                    $data['url'] = $resurl;
                    $data['sha1'] = '';
                    $data['md5'] = '';
                    $picture->save($data);
                    $ret['data'] = $resurl;
                } else {
                    $ret['status'] = 422;
                    $ret['msg'] = '上传失败；' . $res;
                }
                // dump($resurl);
                return json($ret);
            } elseif ($annexConfig['type'] == 'oss') {

                return;
            }
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->rule('uniqid')->move(ROOT_PATH . 'public/uploads/' . date("Ymd"));
        if ($info) {
            $data['url'] = '/uploads/' . date("Ymd") . '/' . $info->getSaveName();
            $data['sha1'] = $info->hash('sha1');
            $data['md5'] = $info->hash('md5');
            $picture->save($data);
            // 成功上传后 获取上传信息
            $ret['data'] = '/uploads/' . date("Ymd") . '/' . $info->getSaveName();
        } else {
            $ret['status'] = 422;
            $ret['msg'] = $file->getError();
        }
        return json($ret);
    }
    /**
     * @name 解析邮件模板
     */
    public function sendEmail()
    {
        $id = $_GET['tempId'];
        //获取消息模板
        $temp = new GeeMsgmodel();
        $tempInfo = $temp->where('id = ' . $id)->find();
        $title = $tempInfo['name'];
        $content = $tempInfo['content'];

        //获取基本信息可使用变量配置
        $basic = new GeeWebbasic();
        $basicInfo = $basic->where('id = 1')->field('name,email,url,logo,icp,beian,idc,isp')->find();
        $basicInfo = $basicInfo->toArray();
        //转义变量内容
        foreach ($basicInfo as $k => $v) {
            if ($k == 'logo') {
                $content = str_replace("{basic_" . $k . "}", $v, $content);
            } else {
                $content = str_replace("{basic_" . $k . "}", $v, $content);
            }
        }

        //邮件随机验证码
        $emailCode = mt_rand(100000, 999999);
        //转义邮件随机验证码
        $content = str_replace("{email_code}", $emailCode, $content);
        session('emailCode', $emailCode);

        //短信随机验证码
        $smsCode = mt_rand(100000, 999999);
        //转义短信随机验证码
        $content = str_replace("{sms_code}", $smsCode, $content);
        session('smsCode', $smsCode);

        // dump($basicInfo);
        // dump($content);
        // exit;
        $res = sendEmail(['email' => 'xiao.song@qiduo.net', 'title' => $title, 'content' => $content]);
        // dump($res);
        return $res;
    }

}

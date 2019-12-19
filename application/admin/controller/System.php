<?php
namespace app\admin\controller;
use app\admin\controller\Common; // 前置操作
use think\Request; // 请求类
use think\Controller;
use app\admin\model\GeeLog; // 日志表
use app\admin\model\GeeWebbasic; // 系统信息表
use app\admin\model\GeeEmailconfig; // 系统SMTP邮件配置表
use app\admin\model\GeeMsgmodel; // 消息模板表
use app\admin\model\GeeAnnexconfig; // 附件设置表


class System extends Common
{
    public function index()
    {
        return $this->redirect('admin/System/basic');
    }
    public function basic()
    {
    	$basic = new GeeWebbasic();
    	$basicInfo = $basic->where('id = 1')->find();
        if($basicInfo){
            $this->assign('info',$basicInfo);
        }
        // dump($this->_adminInfo);
        return $this->fetch('System/basic');
    }
    //基础信息数据提交操作
    public function basicAuth(){
    	$basic = new GeeWebbasic();
		$log = new GeeLog();
    	$data = $_POST;
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
    	];
    	//如果是修改基础信息
    	if(isset($data['id']) && !empty($data['id'])){
    		$id = $data['id'];
	        unset($data['id']);
	        foreach($data as $key=>$var){
	            if(empty($var) && $var != '0'){
	                unset($data[$key]);
	            }
	        }
    	}
		if(!isset($data['name']) || empty($data['name'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '网站名称提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['email']) || !vali_data('email',$data['email'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '联系邮箱提交有误！';
    		return json_encode($ret);
    	}
    	if(!isset($data['url']) ||!vali_data('url',$data['url'])){
    		$ret['status'] = 422;
    		$ret['msg'] = '网站首页域名提交有误！';
    		return json_encode($ret);
    	}
        if(!isset($data['logo']) || empty($data['logo'])){
            $ret['status'] = 422;
            $ret['msg'] = 'Logo提交有误！';
            return json_encode($ret);
        }
        unset($data['file']);
    	if(isset($id) && !empty($id)){
    		$w['id'] = $id;
    		$basicres = $basic->save($data,$w);
    		if($basicres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 修改了基本信息 ',
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	} else {
    		$basicres = $basic->save($data);
    		if($basicres){
    			$saveInfo = [
					'content' => $this->_adminInfo['name'].' 添加了基本信息 ',
					'ip' => get_ip()
				];
				$logres = $log->save($saveInfo);
	    		return json_encode($ret);
    		}
    	}
    }
    public function email()
    {
        $email = new GeeEmailconfig();
        $emailInfo = $email->where('id = 1')->find();
        if($emailInfo){
            $this->assign('info',$emailInfo);
        }
        return $this->fetch('System/email');
    }
    //邮件配置数据提交操作
    public function emailAuth(){
        $email = new GeeEmailconfig();
        $log = new GeeLog();
        $data = $_POST;
        $ret = [
            'status'=> 200,
            'msg'=> '操作成功',
            'data'=> ''
        ];
        //如果是修改邮件配置
        if(isset($data['id']) && !empty($data['id'])){
            $id = $data['id'];
            unset($data['id']);
            foreach($data as $key=>$var){
                if(empty($var) && $var != '0'){
                    unset($data[$key]);
                }
            }
        }
        if(!isset($data['host']) || empty($data['host'])){
            $ret['status'] = 422;
            $ret['msg'] = 'SMTP服务器提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['port']) || !vali_data('port',$data['port'])){
            $ret['status'] = 422;
            $ret['msg'] = 'SMTP端口提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['username']) || empty($data['username'])){
            $ret['status'] = 422;
            $ret['msg'] = 'SMTP用户名提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['password']) || empty($data['password'])){
            $ret['status'] = 422;
            $ret['msg'] = 'SMTP密码提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['email']) || !vali_data('email',$data['email'])){
            $ret['status'] = 422;
            $ret['msg'] = '发件人信箱提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['emailname']) || empty($data['emailname'])){
            $ret['status'] = 422;
            $ret['msg'] = '发件人姓名提交有误！';
            return json_encode($ret);
        }
        unset($data['file']);
        if(isset($id) && !empty($id)){
            $w['id'] = $id;
            $emailres = $email->save($data,$w);
            if($emailres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 修改了邮件配置 ',
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        } else {
            $emailres = $email->save($data);
            if($emailres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 添加了邮件配置 ',
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        }
    }
    //消息模板列表
    public function template(){
        $template = new GeeMsgmodel();
        $templateList = $template->order('id desc')->select();
        $this->assign('list',$templateList);
        // dump($templateList);
        return $this->fetch('System/templatelist');
    }
    //消息模板添加页面
    public function addtemplate(){
        if($_GET['id']){
            $id = $_GET['id'];
            $template = new GeeMsgmodel();
            $templateInfo = $template->where('id = '.$id)->find();
            $this->assign('info',$templateInfo);
        }
        return $this->fetch('System/addtemplate');
    }
    
    //消息模板提交操作
    public function addtemplateAuth(){
        $template = new GeeMsgmodel();
        $log = new GeeLog();
        $data = $_POST;
        $ret = [
            'status'=> 200,
            'msg'=> '操作成功',
            'data'=> ''
        ];
        //如果是修改消息模板
        if(isset($data['id']) && !empty($data['id'])){
            $id = $data['id'];
            unset($data['id']);
            foreach($data as $key=>$var){
                if(empty($var) && $var != '0'){
                    unset($data[$key]);
                }
            }
        } else {
            //如果是新增消息模板
            $hasTemplate = $template->where('mark = "'.$data['mark'].'"')->find();
            if($hasTemplate){
                $ret['status'] = 422;
                $ret['msg'] = '模板标识已存在！';
                return json_encode($ret);
            }
        }
        if(!isset($data['mark']) || empty($data['mark'])){
            $ret['status'] = 422;
            $ret['msg'] = '模板标识提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['name']) || empty($data['name'])){
            $ret['status'] = 422;
            $ret['msg'] = '名称提交有误！';
            return json_encode($ret);
        }
        if(!isset($data['content']) || empty($data['content'])){
            $ret['status'] = 422;
            $ret['msg'] = '模板内容提交有误！';
            return json_encode($ret);
        }
    
        if(isset($id) && !empty($id)){
            $w['id'] = $id;
            $templateres = $template->save($data,$w);
            if($templateres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 修改了消息模板 '.$data['name'],
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        } else {
            $templateres = $template->save($data);
            if($templateres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 添加了消息模板 '.$data['name'],
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        }
    }
    //删除模板
    public function deltemplate(){
        $ret = [
            'status'=> 200,
            'msg'=> '操作成功',
            'data'=> ''
        ];
        if(request()->isDelete()){
            $template = new GeeMsgmodel();
            $log = new GeeLog();
            $id = request()->param()['id'];
            $delTemplate = $template->where('id = '.$id)->delete();
            if($delTemplate){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 删除了消息模板ID '.$id,
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
            }
        } else {
            $ret['status'] = 500;
            $ret['msg'] = '操作超时';
        }
        return json_encode($ret);
    }
    //禁用模板
    public function disatemplate(){
        $ret = [
            'status'=> 200,
            'msg'=> '操作成功',
            'data'=> ''
        ];
        if(request()->isPut()){
            $template = new GeeMsgmodel();
            $log = new GeeLog();
            $id['id'] = request()->param()['id'];
            $disabledTemplate = $template->save(['status'=>request()->param()['status']],$id);
            if($disabledTemplate){
                $saveInfo = [
                    'content' => request()->param()['status'] == '2' ? $this->_adminInfo['name'].' 禁用了消息模板ID '.$id['id'] :  $this->_adminInfo['name'].' 解禁了消息模板ID '.$id['id'],
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
            }
        } else {
            $ret['status'] = 500;
            $ret['msg'] = '操作超时';
        }
        return json_encode($ret);
    }
    public function pay(){
      
      return $this->fetch('System/pay');
    }
    //附件设置
    public function annex(){
        $annex = new GeeAnnexconfig();
        $annexInfo = $annex->where('id = 1')->find();
        if($annexInfo){
            $this->assign('info',$annexInfo);
        }
        return $this->fetch('System/annex');
    }
    //附件设置数据提交操作
    public function annexAuth(){
        $annex = new GeeAnnexconfig();
        $log = new GeeLog();
        $data = $_POST;
        $ret = [
            'status'=> 200,
            'msg'=> '操作成功',
            'data'=> ''
        ];
        //如果是修改附件设置
        if(isset($data['id']) && !empty($data['id'])){
            $id = $data['id'];
            unset($data['id']);
            foreach($data as $key=>$var){
                if(empty($var) && $var != '0'){
                    unset($data[$key]);
                }
            }
        }
        if($data['type'] == 'ftp'){
            if(!isset($data['ftp_sever']) || !vali_data('url',$data['ftp_sever'])){
                $ret['status'] = 422;
                $ret['msg'] = 'FTP服务器域名提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['ftp_name']) || empty($data['ftp_name'])){
                $ret['status'] = 422;
                $ret['msg'] = 'FTP账号提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['ftp_pwd']) || empty($data['ftp_pwd'])){
                $ret['status'] = 422;
                $ret['msg'] = 'FTP密码提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['ftp_port']) || empty($data['ftp_port'])){
                $ret['status'] = 422;
                $ret['msg'] = 'FTP端口号提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['ftp_timeout']) || empty($data['ftp_timeout'])){
                $ret['status'] = 422;
                $ret['msg'] = '超时时间提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['ftp_remoteroor']) || empty($data['ftp_remoteroor'])){
                $ret['status'] = 422;
                $ret['msg'] = '图片服务器根目录提交有误！';
                return json_encode($ret);
            }
        } elseif ($data['type'] == 'bos'){
            if(!isset($data['bos_ak']) || empty($data['bos_ak'])){
                $ret['status'] = 422;
                $ret['msg'] = 'AK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['bos_sk']) || empty($data['bos_sk'])){
                $ret['status'] = 422;
                $ret['msg'] = 'SK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['bos_bucket']) || empty($data['bos_bucket'])){
                $ret['status'] = 422;
                $ret['msg'] = 'Bucket提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['bos_domain']) || !vali_data('url',$data['bos_domain'])){
                $ret['status'] = 422;
                $ret['msg'] = '绑定域名提交有误！';
                return json_encode($ret);
            }
        } elseif ($data['type'] == 'qiniu'){
            if(!isset($data['qiniu_ak']) || empty($data['qiniu_ak'])){
                $ret['status'] = 422;
                $ret['msg'] = 'AK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['qiniu_sk']) || empty($data['qiniu_sk'])){
                $ret['status'] = 422;
                $ret['msg'] = 'SK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['qiniu_bucket']) || empty($data['qiniu_bucket'])){
                $ret['status'] = 422;
                $ret['msg'] = 'Bucket提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['qiniu_domain']) || !vali_data('url',$data['qiniu_domain'])){
                $ret['status'] = 422;
                $ret['msg'] = '绑定域名提交有误！';
                return json_encode($ret);
            }

        } elseif ($data['type'] == 'oss'){
            if(!isset($data['oss_ak']) || empty($data['oss_ak'])){
                $ret['status'] = 422;
                $ret['msg'] = 'AK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['oss_sk']) || empty($data['oss_sk'])){
                $ret['status'] = 422;
                $ret['msg'] = 'SK提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['oss_bucket']) || empty($data['oss_bucket'])){
                $ret['status'] = 422;
                $ret['msg'] = 'Bucket提交有误！';
                return json_encode($ret);
            }
            if(!isset($data['oss_domain']) || !vali_data('url',$data['oss_domain'])){
                $ret['status'] = 422;
                $ret['msg'] = '绑定域名提交有误！';
                return json_encode($ret);
            }
        }
        if(isset($id) && !empty($id)){
            $w['id'] = $id;
            $annexres = $annex->save($data,$w);
            if($annexres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 修改了附件设置 ',
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        } else {
            $annexres = $annex->save($data);
            if($annexres){
                $saveInfo = [
                    'content' => $this->_adminInfo['name'].' 添加了附件设置 ',
                    'ip' => get_ip()
                ];
                $logres = $log->save($saveInfo);
                return json_encode($ret);
            }
        }
    }
}

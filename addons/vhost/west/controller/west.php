<?php
namespace addons\vhost\west\controller;

use think\Controller;
use addons\vhost\west\service;

class west extends Controller
{
    /**
     * 通用接口
     * @param $sControl：操作命令
     * @param $aUserData ：用户信息 ['uid', 'username', 'password']
     * @param $aPram : 接口所需要的参数
     * @return json
     */
    public function controlVhost($sControl, $aUserData, $aPram){

        if (empty($sControl)){
            return json_encode(['code'=>300, 'msg'=> '操作命令不能为空']);
        }

        if (empty($aUserData)){
            return json_encode(['code'=>301, 'msg'=> '用户信息不能为空']);
        }

        $sWest = new service\west($aUserData);
        if (isset($sWest['code']) && in_array($sWest['code'], [302, 303, 304])){
            return json_encode($sWest);
        }

        $aCmd = [];
        switch ($sControl){
            // 开通正式/试用虚拟主机
            case 'addVhost':
                $aCmd = ['vhost', 'add', 'entityname:vhost'];
                break;
            // 修改FTP密码
            case 'modFtpPwd':
                $aCmd = ['vhost', 'mod', 'entityname: ftppassword'];
                break;
            // 主机续费
            case 'renewalVhost':
                $aCmd = ['vhost', 'renewal', 'entityname:vhost'];
                break;
            // 试用主机转正
            case 'payTest':
                $aCmd = ['vhost', 'paytest', 'entityname:vhost'];
                break;
            // 主机升级
            case 'upVhost':
                $aCmd = ['vhost', 'set', 'entityname:upvhost'];
                break;
            // 获取ftp密码
            case 'getFtpPwd':
                $aCmd = ['other', 'get', 'entityname:ftppassword'];
                break;
            // 判断FTP是否已被使用
            case 'getFtpExists':
                $aCmd = ['other', 'get', 'entityname:vhostexists'];
                break;
            // 获取主机相关信息
            case 'getVhostInfo':
                $aCmd = ['vhost', 'get', 'entityname:vhostinfo'];
                break;
            //  虚拟主机绑定域名
            case 'addDomain':
                $aCmd = ['vhost', 'mod', 'entityname:adddomain '];
                break;
            // 删除绑定域名
            case 'removeDomain':
                $aCmd = ['vhost', 'mod', 'entityname:removedomain'];
                break;
            // 虚拟主机流量充值
            case 'trafficVhost':
                $aCmd = ['vhost', 'traffic', 'entityname:add'];
                break;
            // 开通赠送mysql数据库
            case 'openMysql':
                $aCmd = ['vhost', 'add', 'entityname:openmysql'];
                break;
            case  '':
                return json_encode(['code'=>400, 'msg'=> '没有对应操作，请核对参数后重试']);
                break;
        }

        $aReturn = $sWest->doit($aCmd, $aPram);

        return json_encode($aReturn);
    }






}
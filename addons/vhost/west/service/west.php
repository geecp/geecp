<?php
namespace addons\vhost\west\service;

use think\Config;

class west{

    private $sApiUrl;
    private $sUid;
    private $sUserName;
    private $sPassword;

    /**
     * west constructor.
     * @param array $aData
     */
    function __construct($aData = []) {
        if (!isset($aData['uid']) || empty($aData['uid'])){
            return ['code'=>302, 'msg' => 'uid不能为空'];
        }
        $this->sUid = $aData['uid'];

        if (!isset($aData['username']) || empty($aData['username'])){
            return ['code'=>303, 'msg' => 'username不能为空'];
        }
        $this->sUserName = $aData['username'];

        if (!isset($aData['password']) || empty($aData['password'])){
            return ['code'=>304, 'msg' => 'password不能为空'];
        }
        $this->sPassword = $aData['password'];

        $this->sApiUrl = Config::get('api_url');
    }

    /**
     * 整合传参，调用接口
     * @param $aCmd
     * @param $aParm
     * @return \SimpleXMLElement
     */
    public function doit($aCmd, $aParm) {
        $sCmdStr = $this->formatData($aCmd, $aParm);
        $sMd5Str = $this->encryptPram($sCmdStr);

        $aPostData = [
            'userid' => $this->sUid,
            'versig' => $sMd5Str,
            'strCmd' => $sCmdStr
        ];
        return $this->send($aPostData);
    }

    /**
     * curl 调用接口
     * @param $aData
     * @return \SimpleXMLElement
     */
    private function send($aData){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->sApiUrl );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $aData);
        $return =  curl_exec($curl);
        return simplexml_load_string($return);
    }

    /**
     * 整理参数
     * @param $aCmdStr
     * @param $aData
     * @return string
     */
    private function formatData($aCmdStr, $aData) {
        $sReturnStr = '';
        if (is_array($aCmdStr) && !empty($aCmdStr)){
            foreach ($aCmdStr as $v){
                $sReturnStr = $v."\r\n";
            }
        }

        if (is_array($aData) && !empty($aData)){
            foreach ($aData as $kk => $vv){
                $sReturnStr .= $kk.':'.$vv."\r\n";
            }
            $sReturnStr .= ".\r\n";
        }
        return $sReturnStr;
    }

    /**
     * 处理加密验签字段
     * @param $sCmdstring
     * @return string
     */
    private function encryptPram($sCmdstring){
        $sUsername = $this->sUserName;
        $sPassword = $this->sPassword;
        return md5( $sUsername . $sPassword . substr($sCmdstring,0,10) );
    }


}
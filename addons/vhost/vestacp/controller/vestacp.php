<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/10
 * Time: 15:28
 */
use think\Controller;
use think\Db;
class vestacp extends Controller
{

    public function vestacp_create($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user'=> $res['config']['user'],
            'password'=> $res['config']['password'],
            'returncode'=> 'yes',
            'cmd'=> 'v-add-user',
            'arg1'=> $data['username'],
            'arg2'=> $data['password'],
            'arg3'=> $data['email'],
            'arg4'=> $data['default'],
            'arg5'=> $data['fist_name'],
            'arg6'=> $data['last_name'],
        );
        $postdata = http_build_query($postvars);
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,'https://'.$res['config']['hostname'].':8083/api/');
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
        $answer = curl_exec($curl);

        return $answer;
    }

    public function vestacp_suspend($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user' => $res['config']['user'],
            'password' => $res['config']['password'],
            'returncode'=> 'yes',
            'cmd' => 'v-suspend-user',
            'arg1' => $data['username']
        );
        $postdata = http_build_query($postvars);

        // Delete user account
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $res['config']['hostname'].':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);
        return $answer;
    }

    public function vestacp_unsuspend($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user' => $res['config']['user'],
            'password' => $res['config']['password'],
            'returncode'=> 'yes',
            'cmd' => 'v-unsuspend-user',
            'arg1' => $data['username']
        );
        $postdata = http_build_query($postvars);

        // Delete user account
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $res['config']['hostname'].':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);
        return $answer;
    }

    public function vestacp_db_create($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user' => $res['config']['user'],
            'password' => $res['config']['password'],
            'returncode'=> 'yes',
            'cmd' => 'v-add-database',
            'arg1' => $data['username'],
            'arg2' => $data['name'],
            'arg3' => $data['user'],
            'arg4' => $data['pass'],
        );
        $postdata = http_build_query($postvars);

        // Delete user account
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $res['config']['hostname'].':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);
        return $answer;
    }


    public function vestacp_list($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user' => $res['config']['user'],
            'password' => $res['config']['password'],
            'cmd' => 'v-list-user',
            'arg1' => $data['username'],
            'arg2' => $data['format'],
        );
        $postdata = http_build_query($postvars);

        // Delete user account
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $res['config']['hostname'].':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);
        return json_decode($answer,true);
    }

    public function vestacp_del_user($data)
    {
        $where['name']='vestacp';
        $res=Db::name('addons')->where($where)->find();
        $res['config']=json_decode($res['config'],true);
        $postvars = array(
            'user' => $res['config']['user'],
            'password' => $res['config']['password'],
            'returncode'=> 'yes',
            'cmd' => 'v-delete-user',
            'arg1' => $data['username'],
        );
        $postdata = http_build_query($postvars);

        // Delete user account
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $res['config']['hostname'].':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $answer = curl_exec($curl);
        return $answer;
    }

}
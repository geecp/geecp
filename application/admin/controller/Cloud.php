<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/4
 * Time: 19:18
 */
namespace app\admin\controller;
use think\Db;
use think\Session;
use app\common\model;

class Cloud extends Base
{
    public function Index()
    {

        if (input('id')) {
            $id = input('id');
            Session::set('id', $id);
        }
        //从数据库中查询所需数据

        return view();
    }

    public function alread()
    {
        //根据接受的插件类型，获取插件目录下的配置文件
        $name=input('pname');
        if(!$name){
            $name='sms';
        }
        //查询所有插件的使用情况
        $old_res=Db::name('addons')->where('range',$name)->select();
        $pname=get_addons_name($name);
        $data=new $pname;
        $addon['path']=$data->addons_path;
        $new_res=scanFile($addon['path']);
        if(count($old_res)==count($new_res)){
            $this->assign('data',$old_res);
        }else{
            //新安装插件之后，读取新插件的配置文件并写入到数据库
            $array=array_column($old_res,'name');
            foreach ($new_res as $k =>$v)
            {
                $new='';
                if(in_array($k,$array)) {
                    unset($new_res[$k]);
                }else{
                    $new=array(
                        'title'=>$v['title'],
                        'name'=>$k,
                        'author'=>$v['author'],
                        'range'=>$name,
                    );
                    if(isset($v['options'])){
                        foreach ($v['options'] as $key =>$val)
                        {
                            $config[$key]=$val['value'];
                        }
                    }else{
                        $config='';
                    }
                    $new['config']=json_encode($config,JSON_UNESCAPED_UNICODE);
                    unset($config);
                    $new['status']=0;
                    Db::name('addons')->insert($new);
                    unset($new);
                }

            }
            $result=Db::name('addons')->where('range',$name)->select();
            $this->assign('data',$result);
        }
        $this->assign('pname',$name);
        return view();
    }

    public function setting()
    {
        //接受类型,并读取该目录下的所有配置文件
        $result=Db::name('addons')->where('id',input('appid'))->find();
        $this->assign('id',$result['id']);
        $result['config']=json_decode($result['config'],true);
        $pname=$result['range'];
        $aname=$result['name'];
        $name=get_addons_name($pname);
        $data=new $name;
        $addon['path']=$data->addons_path;
        $addon['path']=$addon['path'].$aname.DS.'config.php';
        $res=require $addon['path'];
        if (count($result['config'])>1) {
            foreach ($result['config'] as $key =>$val){
                foreach ($res['options'] as $k =>$v){
                    if($k==$key){
                        $res['options'][$k]['value']=$result['config'][$key];
                    }
                }
            }
        }
        $this->assign('pname',$pname);
        $this->assign('aname',$aname);
        $this->assign('result',$res);
        return view();
    }

    public function saveconfig()
    {
        //接受值
        $data=input('post.');
        //将当前配置写入到数据库
        $where['id']=$data['id'];
        unset($data['id']);
        $array['config']=json_encode($data,JSON_UNESCAPED_UNICODE);
        $res=Db::name('addons')->where($where)->update($array);
        if($res){
            echo 1;
        }else{
            echo 2;
        }
    }

    public function savestatus()
    {
        $res = input('post.');
        $where['id']=$res['id'];
        unset($res['id']);
        $result=Db::name("addons")->where($where)->update($res);
        if ($result) {
            return json(['state' => $res['status'], 'msg' => 1]);
        }
    }

    /**
     * [update 更新插件]
     * @return [type] [description]
     */
    public function update()
    {
        $appid=input('appid/d','','htmlspecialchars');

        $pm=model('addons')->getAddonsInfo($appid);

        $p=controller('app\common\service\Plugins');
        $pluginsname=[$pm['name']];
        $pluginsname=base64_encode(json_encode($pluginsname));
        $postdata=['plugins'=>$pluginsname];
        $res=$p->getPluginsCloudVersion($postdata);

        if ($pm['version']!=$res['data'][0]['version']) {
            //版本不一致，下载最新版本
            $postdata=['plugins'=>$pm['name']];
            $getres=$p->getNewPluginsFile($postdata);
            if (json_decode($getres,true) ){
               die($getres);
            }
            $ress=qd_unzip($getres,ROOT_PATH.'addons'.DS.$pm['range'].DS);
            if($ress)
            {
                return json(['code' => 0, 'msg' => "更新成功！"]);
            }else{
                echo $ress;
            }
        }else{
                return json(['code' => 0, 'msg' => "已是最新版本！"]);
        }
        die;

    }

    /**
     * [updatePluginAauth 更新插件授权文件]
     * @return [type] [description]
     */
    public function updatePluginAauth()
    {
        // do it
        return;
    }

    public function test()
    {
        $zip=new ZipArchive;
        echo $zip->ER_READ;
        die;
        // echo DS;
        // $p=model('plugins');
        // $res=$p->getPluginsVersion(['bce','west']);
        // var_dump($res);die;
        // 
        $p=controller('app\common\service\Plugins');
        $pluginsname=['west','ali'];
        $pluginsname="bce";
        // $pluginsname=base64_encode(json_encode($pluginsname));
        $postdata=['plugins'=>$pluginsname];
        // $res=$p->getPluginsCloudVersion($postdata);
        // $pm=model('Plugins');
        // $res= $pm->getPluginsFile('bce');
        // echo(json_encode($res));
        // 
        // 
        // echo ROOT_PATH;die;
        $res=$p->getNewPluginsFile($postdata);

        $ress=qd_unzip($res,ROOT_PATH.'addons'.DS.'tmp');

        // echo(json_encode($res['data']));
        die;
        return;
    }

}
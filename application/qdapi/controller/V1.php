<?php
namespace app\qdapi\controller;
use app\common\model;

class V1
{
    public function getPluginsVersion()
    {
        $plugins=input('plugins/s','','htmlspecialchars');
        $plugins=base64_decode($plugins);
        $plugins=json_decode($plugins,true);
        if (!$plugins) {
            error(1, '插件为空！');
        }
        $p=model('plugins');
        $res=$p->getPluginsVersion($plugins);
        success($res);
        return;

    }

    public function getPluginsfile()
    {
        $plugins=input('plugins/s','','htmlspecialchars');
        $plugins=base64_decode($plugins);
        $plugins=json_decode($plugins,true);
        if (!$plugins) {
            error(1, '插件为空！');
        }
        $pm=model('Plugins');
        $res= $pm->getPluginsFile('bce');
        unset($res['filepath']);
        success($res);
        return;
    }

    public function downloadPlugins()
    {
        $plugins=input('plugins/s','','htmlspecialchars');
        // $plugins=base64_decode($plugins);
        // $plugins=json_decode($plugins,true);
        if (!$plugins) {
            error(1, '插件为空！');
        }
        $pm=model('Plugins');
        $res= $pm->getPluginsFile($plugins);
       header("Content-type:text/html;charset=utf-8");
        if (!$res) {
            error(1, '为未查询到该插件！');
        }
        $file_name=$res[0]['filepath'];
        //用以解决中文不能显示出来的问题
        $file_name=iconv("utf-8","gb2312",$file_name);
        $file_sub_path=ROOT_PATH . 'plugin_files' . DS . 'uploads'. DS ;
        $file_path=$file_sub_path.$file_name;
        //首先要判断给定的文件存在与否
        if(!file_exists($file_path)){
            error(1, '没有该文件');
        return ;
        }
        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
        $file_con=fread($fp,$buffer);
        $file_count+=$buffer;
        echo $file_con;
        }
        fclose($fp);
        // success($res);


    }
}
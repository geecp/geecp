<?php
namespace app\admin\controller;
use app\admin\model\GeeAddons; // 插件表
use app\admin\model\GeeLog; // 日志表
use ZipArchive; // PHP自带zip解析
class Addons extends Common
{
  public  $ret = [
        'status'=>200,
        'msg'=>'操作成功',
        'data'=>''
      ];
    public function index()
    {
        return $this->redirect('admin/Addons/list');
    }
    public function list(){
    	$addons = new GeeAddons();
    	$list = $addons->select();
    	$this->assign('list',$list);
        $this->assign('count', count($list));
        return $this->fetch('Addons/index');
    }
    public function install(){
      $data = $_GET;
      $addons = new GeeAddons();
    	$log = new GeeLog();
      $item = $addons->where('id = '.$data['id'])->find();
      $way = $item['name'];
      $range = $item['range'];
      //下载插件
      $public = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDP4YsIG5QxQHW0B9yspGOeytkfsqFjRrK3pzLqOtuVJ+k5/Slx44Q1dd3XWudSSZCGxcaKlICF2+AnAomcVzEtdVOYQdDCKQlngwdxoGAAlwqGiIBPd0MkeK0UGLnzFiXCtpXerLU/EP1ZbR+kQw0I20R1Uoo74QtPCPkTlAIvUQIDAQAB';

      // $params = [
      //   'time' => time(),
      //   'plugin_id' => (int)$data['id'],
      //   'uid' => session('_adminInfo.user_id')
      // ];
      // ksort($params);
      // $eccrypt = pubkeyEncrypt(http_build_query($params), formatPukey($public));
      // // dump($eccrypt);
      // $url = 'http://demo.qiduo.net:7002/api/geestack/plugin/download?eccrypt='.urlencode($eccrypt);
      // dump($downres);
      // $downres = json_decode(file_get_contents($url),true)?json_decode(file_get_contents($url),true):file_get_contents($url);
      // if(isset($downres['code']) && $downres['code'] != 0){
      //   $ret['status'] = 500;
      //   $ret['msg'] = $downres['msg'];
      //   return $ret;
      // }
      // $file = file_get_contents($url);
      // file_put_contents(ROOT_PATH.'public/uploads/plugs/'.$way.'.zip',$file);
      //解压插件
      $zip = new ZipArchive;
      $zipres = $zip->open(ROOT_PATH.'public/uploads/plugs/'.$way.'.zip');
      if($zipres===TRUE){
        //解压缩到test文件夹
        $zip->extractTo(ROOT_PATH);
        $zip->close();
      }else{
        $ret['status'] = 500;
        $ret['msg'] = 'failed, code:' . $zipres;
        return $ret;
      }
      // dump(ROOT_PATH);
      // exit;
      //引入addons 相关 range 的插件主控
      $path = ROOT_PATH.'/addons/'.$range.'/'.$range.'.php';
      include_once $path;
      $className = '\addons\\'.$range.'\\'.$range;
      $plug= new $className();
      //执行插件中的安装操作
      $res = $plug->install($way);
      $addons->where('id = '.$data['id'])->update(['status'=>2]);
      
      $saveInfo = [
      'content' => $this->_adminInfo['name'].' 安装了插件 '.$way,
      'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
      return $res;
    }
    public function uninstall(){
      $data = $_GET;
      $addons = new GeeAddons();
    	$log = new GeeLog();
      $item = $addons->where('id = '.$data['id'])->find();
      $way = $item['name'];
      $range = $item['range'];
      // dump($way);
      $path = ROOT_PATH.'/addons/'.$range.'/'.$range.'.php';
      include_once $path;
      $className = '\addons\\'.$range.'\\'.$range;
      $plug= new $className();
      //执行插件中的安装操作
      $res = $plug->uninstall($way);
      $addons->where('id = '.$data['id'])->update(['status'=>0]);
      $saveInfo = [
      'content' => $this->_adminInfo['name'].' 卸载了插件 '.$way,
      'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
      return $res;
    }
    public function on(){
      $data = $_GET;
      $addons = new GeeAddons();
    	$log = new GeeLog();
      $addons->where('id = '.$data['id'])->update(['status'=>'2']);
      $saveInfo = [
      'content' => $this->_adminInfo['name'].' 启用了插件 '.$way,
      'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
      $this->redirect('admin/Addons/index');
    }
    public function off(){
      $data = $_GET;
      $addons = new GeeAddons();
    	$log = new GeeLog();
      $addons->where('id = '.$data['id'])->update(['status'=>'1']);
      $saveInfo = [
      'content' => $this->_adminInfo['name'].' 禁用了插件 '.$way,
      'ip' => get_ip()
      ];
      $logres = $log->save($saveInfo);
      $this->redirect('admin/Addons/index');
    }
}

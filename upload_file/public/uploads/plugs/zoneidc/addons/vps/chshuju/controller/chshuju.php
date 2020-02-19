<?php
namespace addons\vps\chshuju\controller;
use think\Controller;
use think\Db;
use think\Config;
class chshuju extends Controller{
    /**
     * 主控端接口
     * @param $data  所传参数
     * @param $data['plug_id']  传入的接口产品id(将根据产品id去获取相关配置)
     * @param $data['data']  访问接口时的其余附带参数
     * @param $data['action']  即将访问的接口名称
     * @param $data['attach']  访问接口时附带的请求备注
     * @param $data['openX']   访问接口时附带的?
     * @return json
     */
    public function control($data=[]){
      $ret = [
        'status'=>200,
        'msg'=>'操作成功！',
        'data'=>''
      ];
      // dump($data);
      if(empty($data['action']) || !isset($data['action'])){
        $ret['status'] = 422;
        $ret['msg'] = '请求接口异常！接口不存在。';
        return $ret;
      }
      $_URI = 'https://chshuju.com/api/cloudapi.asp';
      if($data['action'] != 'manager'){
        $product = Db::name('gee_product')->where('id = '.$data['plug_id'])->find();
        $config = json_decode($product['plug_config'],true);
        $data['userid'] = $config['user'];
        //([原密码+150cn.com] md5 32位)
        $data['userstr'] = md5($config['password'].'7i24.com');
      }

      switch($data['action']){
        case 'getinfo':
        //查询VPS
        $url = $_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&vpsname='.(isset($data['data']['vpsname'])?$data['data']['vpsname']:'').'&action='.$data['action'].'&attach='.$data['attach'].'openX='.$data['openX'];
        break; 
        case 'activate':
        //开通VPS
        $url = $_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&year='.$data['data']['year'].'&idc='.(isset($data['data']['idc'])?$data['data']['idc']:'').'&productid='.$config['product_id'].'&action='.$data['action'].'&attach='.$data['attach'];
        break;
        case 'renew':
        //续费VPS
        $url = $_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&vpsname='.$data['data']['vpsname'].'&year='.$data['data']['year'].'&action='.$data['action'].'&attach='.$data['attach'];
        // dump($url);
        break;
        case 'update':
        //升级VPS
        $url = $_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&vpsname='.$data['data']['vpsname'].'&productid='.$config['product_id'].'&action='.$data['action'].'&attach='.$data['attach'];
        break;
        case 'changepw':
        $_URI = 'https://chshuju.com/api/cloudapi.asp';
        //修改密码
        $url = $_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&action='.$data['action'].'&vpsname='.$data['data']['vpsname'].'&newpassword='.$data['data']['password'];
        break;
        case 'manager':
          $post = '<form id="sub" action="http://yunvps.info/vpsadm/login.asp" method="post"><input type="hidden" name="vpsname" value="'.$data['data']['vpsname'].'"/><input type="hidden" name="vpspassword" value="'.$data['data']['password'].'"/></form>';
          $post = $post.'<script>document.forms["sub"].submit()</script>';
          return $post;
        break;
      }
      $res = $this->send($url);
      // dump($res);

      $res = convertUrlQuery($res);
      if($res['ret'] == '0'){
        $ret['status'] = 401;
        $ret['msg'] = $res['freehosinfo'];
        return json_encode($ret);
      }
      if($data['action'] == 'activate'){
        $getinfo = $this->send($_URI.'?userid='.$data["userid"].'&userstr='.$data['userstr'].'&vpsname='.$res['vpsname'].'&action=getinfo');
        $getinfo = convertUrlQuery($getinfo);
        $saveData = [
          'user_id'=> $data['user_id'],
          'product_id'=> $data['plug_id'],
          'name'=> $res['vpsname'],
          'password'=>$res['vpspassword'],
          'attach'=>$res['attach'],
          'end_time'=>strtotime($res['endtime']),
          'ip'=> $getinfo['ip'],
          'status'=> $getinfo['status'],
          'create_time'=>time(),
          'update_time'=>time(),
        ];
        $saveres = Db::name('gee_vps_chshuju')->insert($saveData);
        if(!$saveres){
          $ret['status'] = 500;
          $ret['msg'] = '操作超时！';
        }
        $savevpsres = Db::name('gee_vps')->insert(['user_id'=>$data['user_id'],'plug_id'=>$data['plug_id'],'plug_type'=>'gee_vps_chshuju','plug_name'=>'chshuju','pro_id'=>Db::name('gee_vps_chshuju')->where('name = "'.$res['vpsname'].'"')->find()['id'],'create_time'=>time(),'update_time'=>time()]);
      } else if($data['action'] == 'getinfo'){
        $ret['data'] = $res;
      } else if($data['action'] == 'changepw'){
        $ret['data'] = '';
        $up = Db::name('gee_vps_chshuju')->where('name = "'.$data['data']['vpsname'].'"')->update(['password'=>$data['data']['password']]);
      } else if($data['action'] == 'renew'){
        $ret['data'] = '';
        $up = Db::name('gee_vps_chshuju')->where('name = "'.$data['data']['vpsname'].'"')->update(['end_time'=>$res['endtime']]);
      } else if($data['action'] == 'update'){
        $ret['data'] = '';
        $up = Db::name('gee_vps_chshuju')->where('name = "'.$data['data']['vpsname'].'"')->update(['end_time'=>$res['endtime']]);
      }
      // dump(json_encode($ret));
      return json_encode($ret);
    }
    /**
     * 更新vps列表数据
     * @param $data  所传参数
     * @param $data['user_id']  所属用户ID
     * @return json
     */
    public function updateList($data=[]){
      $v = Db::name('gee_vps');
      $vps = Db::name('gee_vps_chshuju');
      // $plug= $this->control();
      if($data['data']['user_id'] == '-1'){
        $vpsList = $vps->select();
      } else {
        $vpsList = $vps->where('user_id = '.$data['data']['user_id'])->select();
      }
      foreach($vpsList as $k=>$v){
        $id['id'] = $v['id'];
        $putData = [
          'plug_id'=>$v['product_id'],
          'user_id'=> $data['user_id'],
          'action'=>'getinfo',
          'data'=>[
            'vpsname'=> $v['name'],
          ],
          'attach'=>'',
          'openX'=>""
        ];
        $res = $this->control($putData);
        $res = json_decode($res,true);
        if($res['msg'] == '云服务器名不存在！'){
          $vps->where('id = '.$v['id'])->delete();
          Db::name('gee_vps')->where('pro_id = '.$v['id'])->delete();
        }else{
          $vps->where('id = '.$v['id'])->update(['password'=>$res['data']['vpspassword'],'status'=>$res['data']['status'],'ip'=>$res['data']['ip'],'end_time'=>strtotime($res['data']['endtime']),'update_time'=>time()]);
          Db::name('gee_vps')->where('pro_id = '.$v['id'])->update(['status'=>$res['data']['status'],'end_time'=>strtotime($res['data']['endtime']),'update_time'=>time()]);
        }
      }
    }

    /**
     * curl 调用接口
     * @param $aData
     * @return \SimpleXMLElement
     */
    private function send($_URI='',$Data=''){
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $_URI);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $Data);
      $return =  curl_exec($curl);
      // dump(mb_convert_encoding($return,'utf-8','GB2312'));
      return mb_convert_encoding($return,'utf-8','GB2312');
  }
}
<?php
namespace app\index\controller;
use app\admin\model\GeeProductGroup; //产品组表
use app\admin\model\GeeProduct; //产品表
use app\index\model\GeeZhVps; //产品表
use addons\vps\vps; //vps插件
class Vpss extends Common
{
    public function index()
    {
        $vps = new GeeZhVps();
        $plug= new vps();
        $vpsList = $vps->where('user_id = '.$this->_userInfo['id'])->select();
        foreach($vpsList as $k=>$v){
          $id['id'] = $v['id'];
          $putData = [
            'function'=>'control',
            'data'=>[
              'plug_id'=>$v['product_id'],
              'user_id'=> $this->_userInfo['id'],
              'action'=>'getinfo',
              'data'=>[
                'vpsname'=> $v['name'],
              ],
              'attach'=>'',
              'openX'=>""
            ]
          ];
          $res = $plug->vps($putData);
          $res = json_decode($res,true);
          if($res['msg'] == '云服务器名不存在！'){
            $vps->where('id = '.$v['id'])->delete();
          }else{
            $vps->where('id = '.$v['id'])->update(['password'=>$res['data']['vpspassword'],'status'=>$res['data']['status'],'ip'=>$res['data']['ip'],'end_time'=>strtotime($res['data']['endtime']),'update_time'=>time()]);
          }
        }
        $vpsList = $vps->where('user_id = '.$this->_userInfo['id'])->order('create_time desc')->paginate(10);
        $this->assign('list',$vpsList);

        return $this->fetch('Vps/index');
    }
    public function add()
    {
      $group = new GeeProductGroup();
      $pro = new GeeProduct();
      if($_GET['id']){
        $item = $pro->where('id = '.$_GET['pro_id'])->find();
        $itemgroup = $group->where('id',$item['group_id'])->find();
        $this->assign('progroup',$itemgroup['name']);
        $this->assign('proname',$item['name']);
        $items = explode(',',$item['update_list']);
        foreach($items as $k=>$v){
          $proItems[$k] = $pro->where('id = '.$v)->find();
        }
      } else {
        $proItems = $pro->where('type=2')->select();
      }
      $groups = [];
      $groupList = [];
      // dump($proItems);
      foreach($proItems as $k=>$v){
        $groups[$k] = $v['group_id'];
      }
      $groups = array_unique($groups);
      $num = 0;
      foreach($groups as $k=>$v){
        $groupList[$num] = $group->where('id = '.$v)->find();
        $num++;
      }
      $this->assign('group',$groupList);
      if($_GET['id']){
        foreach($items as $k=>$v){
          $defualtPro[$k] = $pro->where('id = '.$v.' and group_id = '.$groupList[0]['id'])->find();
        }
        // $defualtPro = $pro->where('group_id = '.$groupList[0]['id'])->select();
      } else {
        $defualtPro = $pro->where('group_id = '.$groupList[0]['id'])->select();
      }
      $this->assign('prolist',$defualtPro);
      return $this->fetch('Vps/add');
    }
    /**
     * 创建VPS
     */
    public function addAuth(){
      $data = $_POST;
      $pro = new GeeProduct();
      $plug = new vps();
      $item = $pro->where('id = '.$data['pro_id'])->find();
      $pro_id = json_decode($item['plug_config'],true)['product_id'];
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      if(!$data['type']){
        if((int)$data['pay_length'] / 12 >= 1){
          // dump($data['pay_length']);
          $years = number_format((int)$data['pay_length'] / 12,0);
        } else{
          $years = '0.'.$data['pay_length'];
        }
        $action = 'activate';
        $datas = [
          'year'=> $years,
          'idc'=> '',
          'productid'=> $pro_id
        ];
      } else if($data['type'] == 'renew'){
        $action = 'renew';
        $datas = [
          'year'=> $years,
          'vpsname'=> $data['name']
        ];
      } else if($data['type'] == 'update'){
        $action = 'update';
        $datas = [
          'vpsname'=> $data['name']
        ];

      } else {
        $ret['status'] = 422;
        $ret['msg'] = '非法操作！';
        return $ret;
      }
      $putData = [
        'function'=>'control',
        'data'=>[
          'plug_id'=>$data['pro_id'],
          'user_id'=> $this->_userInfo['id'],
          'action'=>$action,
          'data'=>$datas,
          'attach'=>'',
          'openX'=>""
        ]
      ];
      // dump($data);
      // dump($putData);
      // exit;
      $res = $plug->vps($putData);
      return $res;
    }
    /**
     * 修改密码
     */
    public function changepass(){
      $data = $_POST;
      $vps = new GeeZhVps();
      $plug = new vps();
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      if(!isset($data['password']) || !vali_data('vpspw',$data['password'])){
        $ret['status'] = 401;
        $ret['msg'] = '新密码有误！';
      }
      $putData = [
        'function'=>'control',
        'data'=>[
          'plug_id'=>$data['pro_id'],
          'action'=>'changepw',
          'data'=>[
            'vpsname'=> $data['vpsname'],
            'password'=> $data['password']
          ],
          'attach'=>'',
          'openX'=>""
        ]
      ];
      $res = $plug->vps($putData);
      // dump($res);
      return $res;
    }
    /**
     * 前往控制面板
     */
    public function manager(){
      $data = $_GET;
      $post = '<form id="sub" action="http://www.ipcomserver.com/vpsadm/zndatalogin.asp" method="post"><input type="hidden" name="vpsname" value="'.$data['vpsname'].'"/><input type="hidden" name="vpspassword" value="'.$data['password'].'"/></form>';
      $post = $post.'<script>document.forms["sub"].submit()</script>';
      echo $post;
    }
    /**
     * 获取分组下的产品类型
     */
    public function getProItem(){
      $id = $_POST['id'];
      $pro = new GeeProduct();
      if($_POST['type'] == 'update'){
        $pro = new GeeProduct();
        $item = $pro->where('id = '.$_POST['pro_id'])->find();
        $items = explode(',',$item['update_list']);
        $num = 0;
        foreach($items as $k=>$v){
          $proitem = $pro->where('id = '.$v.' and group_id = '.$_POST['id'])->find();
          if($proitem){
            $proList[$num] = $proitem;
          } else {
            continue;
          }
          $num++;
        }
        // $proList=array_filter($proList);
        // $proList = $pro->where('group_id = '.$id)->select();
      } else {
        $proList = $pro->where('group_id = '.$id)->select();
      }
      return json_encode($proList);
    }
    /**
     * 获取价格
     */
    public function getPrice(){
      $data = $_POST;
      $pro = new GeeProduct();
    	$ret = [
    		'status'=> 200,
    		'msg'=> '操作成功',
    		'data'=> ''
      ];
      if($data['type'] != 'renew'){
        
      }
      $item = $pro->where('id = '.$data['pro_id'])->find();
      $pro_id = json_decode($item['plug_config'],true)['product_id'];
      if($data['type'] != 'update'){
        //固定换算价格
        switch($data['pay_length']){
          case 1:
          $lengthPrice = $item['month'] * 1;
          break;
          case 2:
          $lengthPrice = $item['month'] * 2;
          break;
          case 3:
          $lengthPrice = $item['quarter'] * 1;
          break;
          case 4:
          $lengthPrice = $item['quarter'] * 1 + $item['month'] * 1;
          break;
          case 5:
          $lengthPrice = $item['quarter'] * 1 + $item['month'] * 2;
          break;
          case 6:
          $lengthPrice = $item['semestrale'] * 1;
          break;
          case 7:
          $lengthPrice = $item['semestrale'] * 1 + $item['month'] * 1;
          break;
          case 8:
          $lengthPrice = $item['semestrale'] * 1 + $item['month'] * 2;
          break;
          case 9:
          $lengthPrice = $item['semestrale'] * 1 + $item['quarter'] * 1;
          break;
          case 10:
          $lengthPrice = $item['years'];
          break;
          case 11:
          $lengthPrice = $item['years'];
          break;
          case 12:
          $lengthPrice = $item['years'];
          break;
          case 24:
          $lengthPrice = $item['biennium'];
          break;
          case 36:
          $lengthPrice = $item['triennium'];
          break;
          default:
          $ret['status'] = 422;
          $ret['msg'] = '非法操作！';
          return json_encode($ret);
          break;
        }
      } else {
        $lengthPrice = $item['month'] * 1;
      }
      
      $ret['data'] = ['price'=>number_format($lengthPrice,2)];
      return json_encode($ret);
    }
}

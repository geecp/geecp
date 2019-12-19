<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\GeeUser;
use app\admin\model\GeeProduct;
use app\admin\model\GeeProductGroup;
use app\admin\model\GeeServer;
use app\admin\model\GeeServerAdded;
use app\admin\model\GeeServerAddedItems;
use app\admin\model\GeeOsgroup;
use app\admin\model\GeeOstype;


/**
 * 物理服务器表
 */
class GeeServer extends Model
{
	public function getUserAttr($var, $data)
    {
      $user = new GeeUser();
      return $user->where('id = '.$data['user_id'])->find()['username'];
    }
    
	public function getConfigAttr($var, $data)
  {
    $p = new GeeProduct();
    $group = new GeeProductGroup();
    $osg = new GeeOsgroup();
    $ost = new GeeOstype();
    $config = $p->where('id = '.$data['pro_id'])->find();
    $put_config[0][0] = '产品名称';
    $put_config[0][1] = $config['name'];
    $put_config[1][0] = '产品类型';
    $put_config[1][1] = $group->where('id = '.$data['pro_group_id'])->find()['name'];
    $put_config[2][0] = '产品配置';
    $put_config[2][1] = $config['describe'];
    $put_config[3][0] = '操作系统';
    // dump($data['osgroup']);
    $put_config[3][1] = $data['osgroup']?$osg->where('id = '.$data['osgroup'])->find()['title']:'暂无';
    $put_config[4][0] = '系统版本';
    $put_config[4][1] = $data['osgroup']?$ost->where('id = '.$data['ostype'])->find()['title']:'暂无';
    return json_encode($put_config);
  }
	public function getAddedsAttr($var, $data)
  {
    // var_dump(json_decode($data['server_added'],false));
    $sg = new GeeServerAdded();
    $sgi = new GeeServerAddedItems();
    foreach(json_decode($data['server_added'],false) as $k=>$v){
      $name = $sg->where('name = "'.$k.'"')->find();
      $added[$k][0] = $name['title'];
      if($name['type'] == 3){
        $addedid = explode(',', $v);
        $sgiinfo = $sgi->where('id = '.$addedid[0])->find();
        $added[$k][1] = $addedid[1].$sgiinfo['title'];
      } else {
        $sgiinfo = $sgi->where('id = '.$v)->find();
        $added[$k][1] = $v == 0?'未使用该服务':$sgiinfo['title'];
      }
    }
    return json_encode($added);
  }
  public function getIspassAttr($var,$data){
    if($data['password']){
      return '<a href="javascript:;" data-pass="'.$data['password'].'">查看</a>';
    } else {
      return '';
    }
  }
	public function getGroupnameAttr($var, $data)
    {
      $group = new GeeProductGroup();
      // dump($group->where('id = '.$data['pro_group_id'])->find());
      return $group->where('id = '.$data['pro_group_id'])->find()['name'];
    }
    public function getStatussAttr($var, $data)
      {
          switch ($data['status']) {
              case '0':
                  return '开通中';
                  break;
              case '1':
                  return '已到期';
                  break;
              case '2':
                  return '正在重装系统';
                  break;
              case '3':
                  return '正在运行';
                  break;
              case '4':
                  return '服务器异常';
                  break;
          }
      }
}

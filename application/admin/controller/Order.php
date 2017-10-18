<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;

use app\admin\model\Finance_order;
use think\Db;
use think\Session;

class Order extends Base
{
    public  function index()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('pid',0)->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];
        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');

        } else if (!in_array($res1, $adm_group)) {

            //菜单id
            $id = input('id');
            $url = Db::name('right')->whereIn('id', $adm_group)->where('pid', $id)->order('num')->find()['url'];
            if ($url != '') {
                return $this->redirect($url);
            } else {
                return $this->redirect('error/index');
            }
        } else {

            Session('status',null);
            $order=new Finance_order();
            $where = [];
            if (input('status')){
                $res=$order->where('status',input('status'))->order('create_time desc')->paginate(10);

                Session::set('status',input('status'));
            }else{
                $res=$order->order('create_time desc')->paginate(10);

            }
            foreach ($res as $val) {
                $val['userid'] = Db::name("userlist")->where('id', $val['userid'])->find()['username'];
                $val['product']=explode('-',$val['product'])[0];

            }
            $this->assign('res',$res);
            $this->assign('count',count($res));
            return view();
        }
    }
    //订单详情
    public function orderInfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];

        //拿到路径
        $res1 = Db::name('right')->where('url', $url)->where('pid', 'neq', 0)->find()['id'];

        $adm_group = explode(',', $adm_group);

        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            if (input('id')) {
                $name = Db::name('right')->where('id', 1)->field('munu')->find()['munu'];
                $this->assign('name', $name);
                Session::set('id', 1);
                $right=Session::get("right");
                $this->assign('right',$right);
                $where['id'] = input('id');
                $res = Db::name("finance_order")->where($where)->find();
                //时长
                $res['life']=$res['term'];
                //产品
                $res['allocation']=json_decode($res['allocation'],true);;
                //对配置进行处理
                if($res['allocation']['type']=='regDomain'){
                    $allocation=$res['allocation']['domain'];
                    foreach($allocation as $k=>$v)
                    {
                        $product[$k]=[
                            'name'=>$v['suffix'],
                            'allocation'=>$v['domain'],
                            'price'=>$v['price'],
                            'term'=>$v['year'].'年',
                            'pays'=>$v['pays']
                        ];
                    }
                }else{
                    $product[0]=[
                        'name'=>$res['product'],
                        'allocation'=>$res['allocation']['text'],
                        'price'=>$res['allocation']['price'],
                        'term'=>'',
                        'pays'=>''
                    ];
                }


                $res['userid'] = Db::name("userlist")->where('id', $res['userid'])->find()['username'];
                $this->assign('product',$product);
                $this->assign('res', $res);
                return view();
            } else {
                $this->error('非法操作');
            }

        }

    }

}
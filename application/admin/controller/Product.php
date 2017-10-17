<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;
use app\admin\model\Domain_price;
use app\admin\model\Productgroup;
use app\admin\model\Vhostproduct;
use app\admin\model\VpsProduct;
use app\admin\model\ProductPrice;
use app\admin\model\System;
use app\admin\model\Serverhost;
use think\Db;
use think\Session;

class Product extends Base
{
    public function index()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('pid',0)->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $res1=Db::name('right')->where('url',$url)->where('pid','neq',0)->find()['id'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else if (!in_array($res1,$adm_group)){
            //菜单id
            $id=input('id');
            $url=Db::name('right')->whereIn('id',$adm_group)->where('pid',$id)->order('num')->find()['url'];
            if ($url !=''){
                return $this->redirect($url);
            }else{
                return $this->redirect('error/index');
            }
        }else{
            Session::set('id',3);

            $product=new Domain_price();
            $domain=$product->where('state',1)->order('number')->paginate(10);
            foreach($domain as $key =>$val){
                $domain[$key]['price']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('first_price,second_price,third_price,fourth_price,fifth_price')->find(); //购买价
                $domain[$key]['price']=implode(',',$domain[$key]['price']);
                $domain[$key]['renew']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('renew_first_price,renew_second_price,renew_third_price,renew_fourth_price,renew_fifth_price')->find();//续费价
                $domain[$key]['renew']=implode(',',$domain[$key]['renew']);
                $domain[$key]['agent']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('agent_price,agent_second_price,agent_third_price,agent_fourth_price,agent_fifth_price')->find();//代理价
                $domain[$key]['agent']=implode(',',$domain[$key]['agent']);
                $domain[$key]['agerenew']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('agerenew_first_price,agerenew_second_price,agerenew_third_price,agerenew_fourth_price,agerenew_fifth_price')->find();//代理续费价
                $domain[$key]['agerenew']=implode(',',$domain[$key]['agerenew']);
                $domain[$key]['pro']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('promotion_price,pro_second_price,pro_third_price,pro_fourth_price,pro_fifth_price')->find();//促销价
                $domain[$key]['pro']=implode(',',$domain[$key]['pro']);
                $domain[$key]['into']=Db::name('domain_price')->where('id',$domain[$key]['id'])->field('into_price,into_second_price,into_third_price,into_fourth_price,into_fifth_price')->find();//转入价
                $domain[$key]['into']=implode(',',$domain[$key]['into']);
            }

            $this->assign('domain',$domain);

            $this->assign('count', count($domain));
            return view();
        }

    }

    //新增数据
    public function add_domain()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            foreach ($arr as $val){
                if ($val==''){
                    return json([ 'msg' => 3]);
                }
            }
            $data['number']=$arr[0];
            $data['suffix']=$arr[1];
            $data['text']=$arr[2];
            $data['tag']=$arr[3];
            $data['APIID']=$arr[4];

            unset($arr);
            $price=explode(',',input('price'));
            $renew=explode(',',input('renew'));
            $agent=explode(',',input('agent'));
            $agerenew=explode(',',input('agerenew'));
            $pro=explode(',',input('pro'));
            $into=explode(',',input('into'));
            //购买价
            $data['first_price']=$price[0];
            $data['second_price']=$price[1];
            $data['third_price']=$price[2];
            $data['fourth_price']=$price[3];
            $data['fifth_price']=$price[4];
            //续费价
            $data['renew_first_price']=$renew[0];
            $data['renew_second_price']=$renew[1];
            $data['renew_third_price']=$renew[2];
            $data['renew_fourth_price']=$renew[3];
            $data['renew_fifth_price']=$renew[4];
            //代理价
            $data['agent_price']=$agent[0];
            $data['agent_second_price']=$agent[1];
            $data['agent_third_price']=$agent[2];
            $data['agent_fourth_price']=$agent[3];
            $data['agent_fifth_price']=$agent[4];
            //代理续费价
            $data['agerenew_first_price']=$agerenew[0];
            $data['agerenew_second_price']=$agerenew[1];
            $data['agerenew_third_price']=$agerenew[2];
            $data['agerenew_fourth_price']=$agerenew[3];
            $data['agerenew_fifth_price']=$agerenew[4];

            //促销价
            $data['promotion_price']=$pro[0];
            $data['pro_second_price']=$pro[1];
            $data['pro_third_price']=$pro[2];
            $data['pro_fourth_price']=$pro[3];
            $data['pro_fifth_price']=$pro[4];

            //转入价
            $data['into_price']=$into[0];
            $data['into_second_price']=$into[1];
            $data['into_third_price']=$into[2];
            $data['into_fourth_price']=$into[3];
            $data['into_fifth_price']=$into[4];

            $data['create_time']=time();
            $data['state']=1;
            unset($price);
            unset($renew);
            unset($agent);
            unset($agerenew);
            unset($pro);
            unset($into);
            $res=Db::name("domain_price")->insert($data);
            if ($res) {
                return json([ 'msg' => 1]);
            }
            return $data;
        }
    }

    //修改数据
    public function save_domain()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            if (input('id')){
                $data=[];
                $where['id']=input('id');
                $arr=explode(',',input('data'));

                $data['number']=$arr[0];
                $data['suffix']=$arr[1];
                $data['text']=$arr[2];
                $data['tag']=$arr[3];
                $data['APIID']=$arr[4];
                $data['update_time']=time();
                //价格
                $price=explode(',',input('price'));
                $renew=explode(',',input('renew'));
                $agent=explode(',',input('agent'));
                $agerenew=explode(',',input('agerenew'));
                $pro=explode(',',input('pro'));
                $into=explode(',',input('into'));

                //购买价
                $data['first_price']=$price[0];
                $data['second_price']=$price[1];
                $data['third_price']=$price[2];
                $data['fourth_price']=$price[3];
                $data['fifth_price']=$price[4];
                //续费价
                $data['renew_first_price']=$renew[0];
                $data['renew_second_price']=$renew[1];
                $data['renew_third_price']=$renew[2];
                $data['renew_fourth_price']=$renew[3];
                $data['renew_fifth_price']=$renew[4];
                //代理价
                $data['agent_price']=$agent[0];
                $data['agent_second_price']=$agent[1];
                $data['agent_third_price']=$agent[2];
                $data['agent_fourth_price']=$agent[3];
                $data['agent_fifth_price']=$price[4];
                //代理续费价
                $data['agerenew_first_price']=$agerenew[0];
                $data['agerenew_second_price']=$agerenew[1];
                $data['agerenew_third_price']=$agerenew[2];
                $data['agerenew_fourth_price']=$agerenew[3];
                $data['agerenew_fifth_price']=$agerenew[4];

                //促销价
                $data['promotion_price']=$pro[0];
                $data['pro_second_price']=$pro[1];
                $data['pro_third_price']=$pro[2];
                $data['pro_fourth_price']=$pro[3];
                $data['pro_fifth_price']=$pro[4];

                //转入价
                $data['into_price']=$into[0];
                $data['into_second_price']=$into[1];
                $data['into_third_price']=$into[2];
                $data['into_fourth_price']=$into[3];
                $data['into_fifth_price']=$into[4];

                unset($price);
                unset($renew);
                unset($agent);
                unset($agerenew);
                unset($pro);
                unset($into);
                $res=Db::name('domain_price')->where($where)->update($data);
                if ($res) {
                    return json([ 'msg' => 1]);
                }
            }else{
                $this->error('非法操作');
            }

        }

    }

    //保存状态
    public function savestatus()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            //修改文章禁用状态
            $res = input('post.');
            $where['id']=$res['id'];
            unset($res['id']);
            dump($res);
            $result=Db::name("domain_price")->where($where)->update($res);
            if ($result) {
                return json(['state' => $res['state'], 'msg' => 1]);
            }
        }
    }

    //删除数据
    public function delete()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            if (input('post.id')){
                //删除域名
                $id = input('post.id');

                $arr=explode(',',$id);
                $data['state']=2;
                $res=Db::name('domain_price')->whereIn('id',$arr)->update($data);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

        }

    }

    //产品列表
    public function product()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取查看权限');
        }else{
            Session::set('id',3);
            Session::set('num',3);
            $product=new Vhostproduct();
            $data = $product->paginate(10);
            foreach($data as $val){
                $val['pro_id']=Db::name("productgroup")->where('id',$val['pro_id'])->find()['name'];
            }
            $this->assign('data',$data);
            $this->assign('count',count($data));
            return view();
        }

    }

    //添加产品
    public function addproduct()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            Session::set('num',3);
            //产品分组
            $whe=[
                'identify'=>'vhost',
                'status '=>1
            ];
            $group=Db::name("productgroup")->where($whe)->select();
            $this->assign('group',$group);
            //获取所有的操作系统
            $whe=[
                'type'=>'serverhost'
            ];
            $system=System::all($whe);
            $this->assign('system',$system);
            return view();
        }

    }

    //产品添加
    public function productadd()
    {

        $data=input('post.');
        $data['creat_time']=date('Y/m/d H:i:s');
        //处理价格
        if($data['month']!=''){
            $price[0]['time']='month';
            $price[0]['term']='1';
            $price[0]['price']=$data['month'];
        }
        if($data['half']!=''){
            $price[1]['time']='half';
            $price[1]['term']='6';
            $price[1]['price']=$data['half'];
        }
        if($data['year']!=''){
            $price[2]['time']='year';
            $price[2]['term']='12';
            $price[2]['price']=$data['year'];
        }
        if($data['two']!=''){
            $price[3]['time']='two';
            $price[3]['term']='24';
            $price[3]['price']=$data['two'];
        }
        if($data['three']!=''){
            $price[4]['time']='three';
            $price[4]['term']='36';
            $price[4]['price']=$data['three'];
        }
        if($data['five']!=''){
            $price[5]['time']='five';
            $price[5]['term']='60';
            $price[5]['price']=$data['five'];
        }
        unset($data['month']);
        unset($data['half']);
        unset($data['year']);
        unset($data['two']);
        unset($data['three']);
        unset($data['five']);
        $data['system']=json_encode($data['system'],JSON_UNESCAPED_UNICODE);
        $data['language']=json_encode($data['language'],JSON_UNESCAPED_UNICODE);
        $res=Vhostproduct::create($data);
        if($res){
            foreach ($price as $k=>$v){
                $price[$k]['p_id']=$res->title;
            }
            $code=new ProductPrice();
            $res=$code->saveAll($price);
            if ($res){
                return json(['msg'=>1]);
            }
        }

    }

    //修改产品状态
    public function saveprostatus()
    {
        //获取当前控制器和方法
         $controller=strtolower(substr(__CLASS__,'21'));
         $method=__FUNCTION__;
         $url=$controller.'/'.$method;
         $res=Db::name('right')->where('url',$url)->find()['id'];
         //拿到当前登录人的权限
         $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
         $adm_group=explode(',',$adm_group);

         if (!in_array($res,$adm_group)){
             return json(['msg'=>2]);
         }else {

             //修改产品状态
             $res = input('post.');
             $model = Vhostproduct::get($res['id']);
             $model->status = $res['status'];
             if (false !== $model->save()) {
                 return json(['id' => $model->id, 'status' => $model->status, 'msg' => 1]);
             }
         }
    }

    //编辑产品
    public function product_edit()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            //处理左侧菜单
            $name=Db::name('right')->where('id',3)->field('munu')->find()['munu'];
            $this->assign('name',$name);
            Session::set('id',3);
            $right=Session::get("right");

            $this->assign('right',$right);
            Session::set("num",3);
            if (input('id')){
                //产品分组
                $group=Db::name("productgroup")->where('state','<>',1)->select();
                $this->assign('group',$group);
                //服务器列表查出来
                $vhost=Db::name("vhostserver")->where('state','<>','1')->select();

                //该产品信息
                $res=Vhostproduct::get(input('id'));
                $res['system']=json_decode($res['system'],JSON_UNESCAPED_UNICODE);
                $res['language']=json_decode($res['language'],JSON_UNESCAPED_UNICODE);
                //获取该产品的价格
                $price=ProductPrice::all(['p_id'=>$res->title]);

                //把选中的服务器列表拆出来
                $data=[];
                $data=explode(',',$res['vhostlist']);
                //已经选好的
                $vhostlist=Db::name("vhostserver")->whereIn('id',$data)->select();

                foreach($vhost as $key => $val){
                    foreach($vhostlist as $v){
                        if ($v['id']==$val['id']){
                            unset($vhost[$key]);
                        }
                    }
                }
                //获取所有的操作系统
                $whe=[
                    'type'=>'serverhost'
                ];
                $system=System::all($whe);
                $this->assign('system',$system);
                $this->assign('price',$price);
                $this->assign('data',$res['vhostlist']);
                $this->assign('vhostlist',$vhostlist);
                $this->assign('vhost',$vhost);
                $this->assign('res',$res);
                return view();
            }else{
                $this->error('非法操作');
            }

        }

    }

    //进行修改产品
    public function edit_product()
    {
        $where['id']=input('post.id');
        $data=input('post.');
        $old=Vhostproduct::get($where['id']);
        /*if (strlen($data['vhostlist'])==0){
            return json(['msg'=>2]);
        }*/
        //处理价格
        $code=[
            ['time'=>'month','term'=>'1','price'=>$data['month']],
            ['time'=>'half','term'=>'6','price'=>$data['half']],
            ['time'=>'year','term'=>'12','price'=>$data['year']],
            ['time'=>'two','term'=>'24','price'=>$data['two']],
            ['time'=>'three','term'=>'36','price'=>$data['three']],
            ['time'=>'five','term'=>'60','price'=>$data['five']]
        ];
        unset($data['month']);
        unset($data['half']);
        unset($data['year']);
        unset($data['two']);
        unset($data['three']);
        unset($data['five']);
        $whe['p_id']=$old->title;
        ProductPrice::destroy($whe);
        foreach ($code as $k=>$v){
            $code[$k]['p_id']=$old->title;
        }
        $price=new ProductPrice;
        $price->saveAll($code);
        unset($data['id']);
        $data['updat_time']=time();
        $res=Db::name("vhostproduct")->where($where)->update($data);
        if ($res){
            return json(['msg'=>1]);
        }
    }

    //产品删除
    public function deletepro()
    {
       //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
            return json([ 'msg' =>2]);
        }else{
            if (input('post.id')){
                //删除域名
                $id = input('post.id');
                $res=Db::name('vhostproduct')->delete($id);
                if ($res){
                    return json(['msg'=>1]);
                }
            }else{
                return json(['msg'=>3]);
            }

        }

    }

    //产品分组
    public function progroup()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            Session::set('num',6);
            $progroup=new Productgroup();
            $data=$progroup->where('state','<>','1')->paginate(10);
            $this->assign('data',$data);
            $this->assign('count',count($data));

            Session::set('id',3);
            return view();
        }

    }

    //添加产品分组页面
    public function addgroup()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            return view();
        }

    }

    //执行添加
    public function groupadd()
    {

        //处理左侧菜单
        $name=Db::name('right')->where('id',10)->field('munu')->find()['munu'];
        $this->assign('name',$name);
        Session::set('id',3);
        $right=Db::name('right')->where('pid',10)->order('num')->select();
        $this->assign('right',$right);
        Session::set("num",6);


        $_POST['status']=1;
        $_POST['state']=2;
        $_POST['creat_time']=time();
        $res=Db::name('productgroup')->insert($_POST);
        if ($res){
            return json(['msg'=>1]);
        }
    }

    //加载修改页面
    public function group_edit()
    {
        //获取当前控制器和方法
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);

        if (!in_array($res,$adm_group)){
            $this->error('您还没有获取操作权限');
        }else{
            if (input('id')){
                //处理左侧菜单
                $name=Db::name('right')->where('id',10)->field('munu')->find()['munu'];
                $this->assign('name',$name);
                Session::set('id',3);
                $right=Session::get("right");
                $this->assign('right',$right);
                Session::set("num",6);

                $where['id']=input('id');
                $res=Db::name("productgroup")->where($where)->find();

                $this->assign('res',$res);
                return view();
            }else{
                $this->error('非法操作!');
            }

        }

    }

    //执行修改
    public function edit_group()
    {
        $where['id']=input('post.id');
        $data=input('post.');
        unset($data['id']);
        $data['updat_time']=time();
        $res=Db::name("productgroup")->where($where)->update($data);
        if ($res){
            return json(['msg'=>1]);
        }
    }

    //修改产品分组状态
    public function savegrostatus()
    {
        //获取当前控制器和方法
         $controller=strtolower(substr(__CLASS__,'21'));
         $method=__FUNCTION__;
         $url=$controller.'/'.$method;
         $res=Db::name('right')->where('url',$url)->find()['id'];
         //拿到当前登录人的权限
         $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
         $adm_group=explode(',',$adm_group);

         if (!in_array($res,$adm_group)){
             return json(['msg'=>2]);
         }else {

             //修改客户状态
             $res = input('post.');
             $model = Productgroup::get($res['id']);
             $model->status = $res['status'];
             if (false !== $model->save()) {
                 return json(['id' => $model->id, 'status' => $model->status, 'msg' => 1]);
             }
         }
    }

    //删除产品分组
    public function deletegroup()
    {
        $controller=strtolower(substr(__CLASS__,'21'));
        $method=__FUNCTION__;
        $url=$controller.'/'.$method;
        $res=Db::name('right')->where('url',$url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group=Db::name('admgroup')-> where('id',Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group=explode(',',$adm_group);
        $arr=explode(',',input('data'));
        if (!in_array($res,$adm_group)){
             return json([ 'msg' =>2]);
         }else {
             if (input('post.id')){
                 //删除服务器
                 $id = input('post.id');
                 $data['state'] = 1;


                 //查询有没有子级产品
                 $server = new Vhostproduct();
                 $product = $server->where('status', '<>', '1')->paginate(10);
                 if ($product) {
                     foreach ($product as $val) {
                         if ($val['pro_id'] == $id) {
                             return json(['msg' => 4]);
                         }
                     }
                 }

                 $res = Db::name('productgroup')->delete($id);

                 if ($res) {
                     return json(['msg' => 1]);
                 }
             }else{
                 return json(['msg' => 1]);
             }

         }
    }

    //vps列表
    public function fastcloud()
    {
        //查询列表页
        $res=VpsProduct::all();
        foreach ($res as $k=>$v){
            $res[$k]['room']=Db::name('productgroup')->where('id',$v['room'])->find()['name'];
        }
        $this->assign('data',$res);
        return view();
    }

    //编辑vps
    public function editfastcloud()
    {
        //判断是否有传参
        $where=input('id');
        if($where){
            //如果存在就是编辑
            $res=VpsProduct::get($where);
            $res['system']=json_decode($res['system'],true);
            $whe['p_id']=$where;
            $price=Db::name('product_price')->where($whe)->order('id')->select();
            if($price){
                foreach($price as $k=>$v){
                    $price_z[$k]['time']=$v['time'];
                    $price_z[$k]['price']=$v['price'];
                }
                $this->assign('price',$price_z);
            }
            $this->assign('id',$where);
            $this->assign('data',$res);
        }
        //获取所有的机房
        $whe=array(
            'identify'=>'vps',
            'status'=>1
        );
        $room=Productgroup::all($whe);
        $this->assign('room',$room);
        unset($whe);
        //获取所有的操作系统
        $whe=[
            'type'=>'server'
        ];
        $system=System::all($whe);
        $this->assign('system',$system);
        return view();
    }

    //保存vps
    public function save_fastcloud()
    {
        $data=input('post.');
        //处理系统和机房的选择
        $data['system']=json_encode($data['system'],JSON_UNESCAPED_UNICODE);
        //判断是否含有参数
        $where['id']=input('post.id');
        //处理价格参数
        $code=[
            ['time'=>'month','term'=>'1','price'=>$data['month']],
            ['time'=>'half','term'=>'6','price'=>$data['half']],
            ['time'=>'year','term'=>'12','price'=>$data['year']],
            ['time'=>'two','term'=>'24','price'=>$data['two']],
            ['time'=>'three','term'=>'36','price'=>$data['three']],
            ['time'=>'five','term'=>'60','price'=>$data['five']]
        ];
        $data['status']='2';
        unset($data['month']);
        unset($data['half']);
        unset($data['year']);
        unset($data['two']);
        unset($data['three']);
        unset($data['five']);
        if($where['id']){
            //如果存在就是修改
            $result=VpsProduct::update($data);
            $whe['p_id']=$where['id'];
            ProductPrice::destroy($whe);
            foreach ($code as $k=>$v){
                $code[$k]['p_id']=$where['id'];
            }
            $price=new ProductPrice;
            $price->saveAll($code);
            if($result){
                return json(['msg'=>'修改成功','code'=>'1']);
            }else{
                return json(['msg'=>'修改失败','code'=>'2']);
            }
        }else{
            //新增
            $result=VpsProduct::create($data);
            foreach ($code as $k=>$v){
                $code[$k]['p_id']=$result->id;
            }
            $price=new ProductPrice;
            $price->saveAll($code);
            if($result){
                return json(['msg'=>'添加成功','code'=>'1']);
            }else{
                return json(['msg'=>'添加失败','code'=>'2']);
            }
        }
    }

    //修改vps状态
    public function statusFastCloud()
    {
        //接收id
        $where['id']=input('post.id');
        $data['status']=input('post.status');
        $res=Db::name('fast_cloud')->where($where)->update($data);
        if($res){
            return json(['msg'=>'1','status'=>$data['status']]);
        }else{
            return $data['status'];
        }
    }

    //删除vps
    public function delvps()
    {
        $where['id']=input('post.id');
        $res=Db::name('vps')->delete($where);
        if($res){
            return json(['msg'=>'1']);
        }else{
            return json(['msg'=>'2']);
        }
    }

    //短信
    public function sms()
    {
        $res=Db::name('sms')->paginate(15);
        $this->assign('res',$res);
        return view();
    }

    //编辑短信
    public function editsms()
    {
        $where['id']=input('id');
        if($where['id']!=""){
            //修改
            $res=Db::name('sms')->where($where)->find();
            $this->assign('data',$res);
        }
        return view();
    }

    //保存短信
    public function savesms()
    {
        $where['id']=input('id');
        $data=input('post.');
        unset($data['id']);
        if($where['id']!=""){
            //修改
            $res=Db::name('sms')->where($where)->update($data);
            if($res){
                return json(['msg'=>'修改成功','code'=>'1']);
            }else{
                return json(['msg'=>'修改失败','code'=>'2']);
            }
        }else{
            //新增
            $data['create_time']=date('Y-m-d H:i:s',time());
            $res=Db::name('sms')->insert($data);
            if($res){
                return json(['msg'=>'添加成功','code'=>'1']);
            }else{
                return json(['msg'=>'添加失败','code'=>'2']);
            }
        }
    }

    //短信状态
    public function statussms()
    {
        //接收id
        $where['id']=input('post.id');
        $data['status']=input('post.status');
        $res=Db::name('sms')->where($where)->update($data);
        if($res){
            return json(['msg'=>'1','status'=>$data['status']]);
        }else{
            return json(['msg'=>'2']);
        }
    }

    //删除短信
    public function deletesms()
    {
        $where['id']=input('post.id');
        $res=Db::name('sms')->delete($where);
        if($res){
            return json(['msg'=>'1']);
        }else{
            return json(['msg'=>'2']);
        }
    }
    
    //服务器租用
    public function serverhost()
    {
        //获取所有可租用的服务器信息
        $res=Db::table('gee_serverhost')
            ->alias('s')
            ->join('gee_productgroup p','s.room=p.id')
            ->field('s.id,s.product,s.create_time,s.status,p.name')
            ->paginate(15);
        $this->assign('data',$res);
        return view();
    }

    //保存租用服务器
    public function saveserverhost()
    {
        //接受id，判断操作
        $id['id']=input('post.id');
        $data=input('post.');
        //处理价格
        if($data['month']!=''){
            $price[0]['time']='month';
            $price[0]['term']='1';
            $price[0]['price']=$data['month'];
            $price[0]['p_id']=$data['title'];
        }
        if($data['half']!=''){
            $price[1]['time']='half';
            $price[1]['term']='6';
            $price[1]['price']=$data['half'];
            $price[1]['p_id']=$data['title'];
        }
        if($data['year']!=''){
            $price[2]['time']='year';
            $price[2]['term']='12';
            $price[2]['price']=$data['year'];
            $price[2]['p_id']=$data['title'];
        }
        if($data['two']!=''){
            $price[3]['time']='two';
            $price[3]['term']='24';
            $price[3]['price']=$data['two'];
            $price[3]['p_id']=$data['title'];
        }
        if($data['three']!=''){
            $price[4]['time']='three';
            $price[4]['term']='36';
            $price[4]['price']=$data['three'];
            $price[4]['p_id']=$data['title'];
        }
        if($data['five']!=''){
            $price[5]['time']='five';
            $price[5]['term']='60';
            $price[5]['price']=$data['five'];
            $price[5]['p_id']=$data['title'];
        }
        unset($data['month']);
        unset($data['half']);
        unset($data['year']);
        unset($data['two']);
        unset($data['three']);
        unset($data['five']);
        $data['system']=json_encode($data['system'],JSON_UNESCAPED_UNICODE);
        if($id['id']==''){
            unset($data['id']);
            $data['create_time']=date('Y-m-d H:i:s',time());
            $data['status']=1;
            $res=Db::name('serverhost')->insert($data);
            $price1=new ProductPrice;
            $price1->saveAll($price);
            if($res){
                return json(['msg'=>'添加成功','code'=>'1']);
            }else{
                return json(['msg'=>'添加失败','code'=>'2']);
            }
        }else{

            $result=Serverhost::update($data);
            $whe['p_id']=$id['id'];
            ProductPrice::destroy($whe);
            foreach ($price as $k=>$v){
                $code[$k]['p_id']=$id['id'];
            }
            $code=new ProductPrice;
            $code->saveAll($price);
            if($result){
                return json(['msg'=>'修改成功','code'=>'1']);
            }else{
                return json(['msg'=>'修改失败','code'=>'2']);
            }
        }
    }

    //编辑租用服务器
    public function editserverhost()
    {
        $where['id']=input('id');
        $whe=array(
            'identify'=>'server',
            'status'=>1
        );
        $room=Productgroup::all($whe);
        $this->assign('room',$room);
        if($where['id']!=''){
            //修改
            $res=Db::name('serverhost')->where($where)->find();
            $res['system']=json_decode($res['system'],true);
            $this->assign('data',$res);
            unset($whe);
            $whe['p_id']=$res['title'];
            $price=Db::name('product_price')->where($whe)->order('id')->select();

            if($price){
                foreach($price as $k=>$v){
                    $price_z[$k]['time']=$v['time'];
                    $price_z[$k]['price']=$v['price'];
                }
                $this->assign('price',$price_z);
            }
        }
        $this->assign('id',$where['id']);
        $system=System::all(['type'=>'server']);
        $this->assign('system',$system);
        return view();
    }

    //修改租用服务器状态
    public function statusserver()
    {

    }


}
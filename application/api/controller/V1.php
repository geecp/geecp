<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/25
 * Time: 16:03
 */
namespace app\api\controller;
use app\api\model\Access;
use app\api\model\Attachment;
use app\api\model\Messagetemp;
use app\api\model\Product;
use app\api\model\Questiontype;
use app\api\model\Settingsite;
use app\api\model\SmsAppname;
use app\api\model\SmsTemplate;
use app\api\model\Smstime;
use app\api\model\System;
use app\api\model\Workorder;
use think\Controller;
use think\Db;
use think\Session;
use addons\domain\domain;
use addons\vhost\vhost;
use addons\vps\vps;
class V1 extends Controller  {

    //个人信息
    public function userinfo()
    {
        /*$res=Db::name('userlist')->find();
        Session::set('home',$res);*/
        if(Session::get('home')){
            $success=true;
        }else{
            $success=false;
        }
        //未处理工单数量
        $whe['coustom']=Session::get('home')['id'];
        $data['worder']['num']=Db::name('workorder')->where($whe)->where('status','in',[1,2,3,4])->count();

        //消息未读数量
        $where['is_read']=0;
        $where['sendee']=Session::get('home')['id'];
        $data['sms']['msgnum']=Db::name('messagetemp')->where($where)->count();

        //短信应用数量
        $where2['userid']=Session::get('home')['id'];
        $data['sms']['appnum']=Db::name('sms_appname')->where($where2)->count();

        //模板数量
        $data['sms']['tempnum']=Db::name('sms_template')->where($where2)->count();

        //短信剩余数量
        $resu=Db::name('sms_count')->where($where2)->find();
        if($resu['smscount']){
            $data['sms']['smsnum']=$resu['smscount'];
        }else{
            $data['sms']['smsnum']=0;
        }


        //域名总数
        $data['domain']['domain_count']=Db::name('domain')->where($where2)->count();

        //等待实名
        $where2['suffix']=array('in',['.com','.cn','.net']);
        $where2['realname']=array('in',[1,2]);
        $data['domain']['unreal_domain']=Db::name('domain')->where($where2)->count();

        //即将到期
        unset($where2['suffix']);
        unset($where2['realname']);
        $last_time=date('Y-m-d H:i:s',  strtotime("+1 month"));
        $where2['last_time']=array('<',$last_time);
        $data['domain']['expire']=Db::name('domain')->where($where2)->count();

        //有效域名
        $where2['last_time']=array('>',date('Y-m-d H:i:s',time()));
        $data['domain']['valid_domain']=Db::name('domain')->where($where2)->count();

        $user['success']='success';
        $user['data']['user']=Session::get('home');
        $user['data']['count']=$data;
        unset($user['data']['user']['password']);
        echo json_encode($user,JSON_UNESCAPED_UNICODE);
    }

    //退出
    public function sign_out()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            Session::clear('home');
            $msg['data']=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取百度bos配置
    public function getBosinfo()
    {
        $msg=returnJson(Attachment::bosInfo());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取产品类型
    public function getProduct()
    {
        $msg=Product::alls();
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //用户中心logo   801111491
    public function getLogo()
    {
        $msg=Settingsite::getLogo();
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //短信验证
    public function getSms()
    {
        $msg=returnJson(Smstime::sendSms());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //工单
    public function wordertype()
    {
        $msg=returnJson(Questiontype::WorkorderType());

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //查询所有工单
    public function worder()
    {
        $msg=returnJson(Workorder::workOrder());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //工单详情
    public function woderinfo()
    {
        $msg=returnJson(Workorder::worderInfo());

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //生成工单
    public function creatWorder()
    {
        $msg=returnJson(Workorder::createWorder());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

   /* //删除工单
    public function delWorder()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询当前用户的所有消息
            $whe['workorderid']=input('post.id');
            $message=Db::name('workorder')->where($whe)->delete();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }*/

    //修改工单状态  801111491
    public function edit_worder()
    {
        $msg=returnJson(Workorder::editWorderStatus());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //回复工单的聊天内容
    public function saveChalist()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data=input('post.');
            unset($data['uid']);
            $data['reply_time']=date('Y-m-d H:i:s',time());
            $data['userid']=$result['id'];
            $data['status']='1';
            $res=Db::name('chatlist')->insertGetId($data);
            $dat['id']=$res;
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //消息中心 801111491
    public function message()
    {
        $msg=returnJson(Messagetemp::messageView());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //读取消息
    public function readmsg()
    {
        $msg=returnJson(Messagetemp::readMsg());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除消息
    public function delmsg()
    {
        $display=input('post.display');
        $current=input('post.current');
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询当前用户的所有消息
            $whe['sendee']=$result['id'];
            if(!empty($type)){
                $whe['temp']=$type;
            }
            $data=input('post.msgid');
            $message=Db::name('messagetemp')->where('id','in',$data)->delete();
            $offerset=$display*($current-1);
            $msgnum=Db::name('messagetemp')->where($whe)->count();
            $msg['msgnum']=$msgnum;
            $message=Db::name('messagetemp')->where($whe)->limit($offerset,$display)->select();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
    }

    //AK SK列表  801111491
    public function access()
    {
        $msg=returnJson(Access::access());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //创建Ak，sk 801111491
    public function createace()
    {
        $msg=returnJson(Access::createAccess());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改note
    public function saveenote()
    {
        $msg=returnJson(Access::saveNote());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除数据
    public function delace()
    {
        $msg=returnJson(Access::delAcess());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //短信剩余条数
    public function sms11()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询当前用户的所有消息
            $whe['userid']=$result['id'];
            $smscount=Db::name('sms_count')->where($whe)->find();
            $msg['data']=$smscount['smscount'];
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //应用列表
    public function applist()
    {
        $msg=returnJson(SmsAppname::appList());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //模板列表
    public function templist()
    {
        $msg=returnJson(SmsTemplate::tempList());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //创建应用
    public function createapp()
    {
        $msg=returnJson(SmsAppname::createApp());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //创建模板
    public function createsmstemp()
    {
        $msg=returnJson(SmsTemplate::createTemplate());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除应用
    public function delsmsapp()
    {
       $msg=returnJson(SmsAppname::delSmsApp());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除模板
    public function delsmstemp()
    {
        $msg=returnJson(SmsTemplate::delSmsTemp());
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //增加数据脚本
    public function sql()
    {
        for ($i=0;$i<200;$i++){
            $sql="";
            Db::execute($sql);
        }
    }

    //最近一周短信的使用条数
    public function smsusenum()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询当前用户一周内的短信使用情况

            $message=[
                'name'=>'短信使用情况日统计',
                'stack'=>'总量',
                'data'=>[20, 12, 19, 34, 20, 33, 31],
                'xset'=>['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期天']
            ];
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //发票地址列表
    public function invoiceaddress()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['userid']=$result['id'];
            $message=Db::name('finance_invoiceaddress')->where($whe)->order('id desc')->select();
            foreach ($message as $k=> $v){
                $v['open']=false;
                $v['active']=false;
                $message[$k]=$v;
            }
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //新增，修改发票地址
    public function editinvoiceaddress()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $data['userid']=$result['id'];
            $data['name']=input('post.name');
            $data['phone']=input('post.phone');
            $data['address']=input('post.address');
            $data['postcode']=input('post.postcode');
            $data['create_time']=date('Y-m-d H:i:s',time());
            if($whe['id']!=0){
                $message=Db::name('finance_invoiceaddress')->where($whe)->update($data);
            }else{
                $message=Db::name('finance_invoiceaddress')->insertGetId($data);
                $data['id']=$message;
                $message=$data;
            }
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除发票地址
    public function delinvoiceaddress()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $where['userid']=$result['id'];
            $where['default']=1;
            $res=Db::name('finance_invoiceaddress')->where($where)->count();
            if($res['id']==$whe['id']){
                $message=999;
            }else{
                $message=Db::name('finance_invoiceaddress')->where($whe)->delete();
            }
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //默认的发票地址
    public function defaddress()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $where['userid']=$result['id'];
            $where['default']=1;
            $data['default']=0;
            $message=Db::name('finance_invoiceaddress')->where($where)->update($data);
            $data['default']=1;
            $message=Db::name('finance_invoiceaddress')->where($whe)->update($data);
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //收支明细  801111491
    public function finance_detailed()
    {
        $where['userid']=input('post.uid');
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            $where['status']=1;
            $data=input('post.type');
            $time=input('post.time');
            if($data){
                $where['transaction_type']=$data;
            }
            if($time){
                $time=explode(',',$time);
                $time['1']=$time['1'].' 23:59:59';
                $where['creat_time']=['between',$time];
            }
            $msgnum=Db::name('finance_detailed')->where($where)->count();
            $msg['total']=$msgnum;
            $message=Db::name('finance_detailed')->where($where)->order('id desc')->limit($offerset,$display)->select();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);

    }

    //订单管理  801111491
    public function finance_order()
    {
        $where['userid']=input('post.uid');
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];

            $data=input('post.type');
            $time=input('post.time');
            if($data){
                $where['transaction_type']=$data;
            }
            if($time){
                $time=explode(',',$time);
                $time['1']=$time['1'].' 23:59:59';
                $where['create_time']=['between',$time];
            }
            $msgnum=Db::name('finance_order')->where($where)->count();
            $msg['total']=$msgnum;
            $message=Db::name('finance_order')->where($where)->order('id desc')->limit($offerset,$display)->select();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }
        $this->dingDan();
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //全局订单处理
    public function dingDan()
    {
        //查询所有未付款和超时的订单，更改其订单状态
        $where=[
            'status'=>2,
            'create_time'=>['<',date("Y-m-d H:i:s",strtotime("-1 day"))],
        ];
        $data['status']=3;
        $res=Db::name('finance_order')->where($where)->update($data);

    }

    //拉取一条订单
    public function get_finorderinfo()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['order_id']=input('post.id');
            $data=Db::name('finance_order')->where($whe)->find();
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //发票列表  801111491
    public function finance_invoicelist()
    {
        $where['userid']=input('post.uid');
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            $data=input('post.title');
            if($data){
                $where['invoice_header']=array('like',$data);
            }
            $msgnum=Db::name('finance_invoicelist')->where($where)->count();
            $msg['total']=$msgnum;
            $message=Db::name('finance_invoicelist')->where($where)->order('id desc')->limit($offerset,$display)->field('auditor',true)->select();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //可开发票金额
    public function ok_finance()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            //查看当前用户的所有消费金额
            $money=Db::name('finance_order')->where($where)->sum('money');
            //查询当前用户已开发票金额
            $where['status']=['in','1,2,3,5'];
            $finance=Db::name('finance_invoicelist')->where($where)->sum('money');
            //得到未开发票的金额
            $message=round($money-$finance,2);
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //城市
    public function city()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $pid['pid']=input('post.cid');
            if(!$pid['pid']){
                //查询一级城市
                $data=Db::name('city')->where('level = 1')->field('id,name')->select();
            }else{
                $data=Db::name('city')->where($pid)->field('id,name')->select();
            }
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //发票信息管理
    public function invoice_info()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            $data=input('post.');
            $data['userid']=$result['id'];
            unset($data['uid']);
            if($data){
                $data['time']=date('Y-m-d H:i:s',time());
                $res=Db::name('invoice_info')->where($where)->find();
                if($res){
                    //修改
                    Db::name('invoice_info')->where($where)->update($data);
                }else{
                    //新增
                    Db::name('invoice_info')->insert($data);
                }
                $msg['data']=$data;
            }
            $res=Db::name('invoice_info')->where($where)->find();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //申请发票
    public function apply_invoice()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data['userid']=$result['id'];
            $data['create_time']=date('Y-m-d H:i:s',time());
            $data['money']=input('post.money');
            $type=input('post.type');
            if($type==1){
                $data['content']='技术服务费';
            }else{
                $data['content']='网络服务费';
            }
            $address=input('post.address');
            if($address){
                $data['address']=$address;
            }
            $data['status']=1;
            $data['id']=Db::name('finance_invoicelist')->insertGetId($data);
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //查询当前用户的联系人列表
    public function linkman()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['userid']=$result['id'];
            $message=Db::name('linkman')->where($whe)->order('id desc')->select();
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //增加联系人
    public function addlinkman()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data['userid']=$result['id'];
            $data['name']=input('post.name');
            $data['tel']=input('post.tel');
            $data['phone']=input('post.phone');
            $data['email']=input('post.email');
            $data['cardtype']=input('post.cardtype');
            $data['cardcode']=input('post.cardcode');
            $data['url']=input('post.url');
            $data['id']=Db::name('linkman')->insertGetId($data);
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改联系人
    public function savelinkman()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $data['name']=input('post.name');
            $data['tel']=input('post.tel');
            $data['phone']=input('post.phone');
            $data['email']=input('post.email');
            $data['cardtype']=input('post.cardtype');
            $data['cardcode']=input('post.cardcode');
            $data['url']=input('post.url');
            $message=Db::name('linkman')->where($whe)->update($data);
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除联系人
    public function dellinkman()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $where['userid']=$result['id'];
            $res=Db::name('linkman')->where($where)->count();
            if($res==1){
                $message=999;
            }else{
                $message=Db::name('linkman')->where($whe)->delete();
            }
            $msg['data']=$message;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //根据不同的付款方式选择不同的方法,并生成订单
    public function pay()
    {
        //接受数据生成订单号
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type=input('post.type');
            //生成订单
            $data['userid']=$result['id'];
            $data['order_id']=date('YmdHis',time()).rand(10000,99999);
            Session::set('order',$data['order_id']);
            $data['product']=input('post.product');
            $data['money']=input('post.money');
            $data['allocation']=input('post.allocation');
            $data['term']=input('post.term');
            if(!$data['product']){
                $data['product']='充值';
                $data['status']=2;
                $data['transaction_type']=1;
                $data['creat_time']=date('Y-m-d H:i:s',time());
                $data['channel_type']=$type;
                Db::name('finance_detailed')->insert($data);
                if ($type == 1) {//支付宝支付
                    $msg['data'] = $_SERVER['SERVER_NAME'].'/api/Alipay/doalipay?did=' . $data['order_id'] . '&money=' . $data['money'] . '&goods=' . $data['product'] . '&body=' . $data['product'];
                    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                } else if ($type == 2) {//微信支付
                    $url = $this->WxPay($data);
                    $order=Session::get('orderid');
                    $msg['data'] = $_SERVER['SERVER_NAME'].'/api/Wxpay/index?url=' . $url . '&order=' . $order . '&userid=' . $data['userid'] . '&money=' . $data['money'] . '&goods=' . $data['product'] . '&orderid=' . $data['order_id'] . '&createtime=' . $data['creat_time'];
                    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                }
            }else{
                $data['money']=input('post.money');
                $data['payment']=$type;
                $data['status']=2;
                $data['create_time']=date('Y-m-d H:i:s',time());
                $res=Db::name('finance_order')->insertGetId($data);
                $data['id']=$res;
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
        }

    }

    //支付回调执行对应的注册购买操作   测试成功之后写入$data
    public function WxPay($data)
    {
        ini_set('date.timezone','Asia/Shanghai');
        require_once VENDOR_PATH . 'wxpay/lib/WxPay.Api.php';
        require_once VENDOR_PATH . 'wxpay/example/WxPay.NativePay.php';
        require_once VENDOR_PATH . 'wxpay/example/log.php';
        $host='http://'.$_SERVER['HTTP_HOST'];
        $orderid=\WxPayConfig::MCHID.date("YmdHis");
        $data['orderid']=$orderid;
        Session::set('orderid',$orderid);
        $notify = new \NativePay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($data['product']);
        $input->SetAttach($data['product']);
        $input->SetOut_trade_no($orderid);
        $input->SetTotal_fee($data['money']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 6000));
        $input->SetGoods_tag($data['product']);
        $input->SetNotify_url("http://v1.qiduo.net/index.php?s=api/V1/wxpayok");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($orderid);
        $result = $notify->GetPayUrl($input);
        $url2 = $result["code_url"];
        include_once VENDOR_PATH .'wxpay/example/qrcode.php';
        $url="https://v1.qiduo.net/api/code/code.html?data=".urlencode($url2);
        return $url;
    }

    //支付回调
    public function payCallBack()
    {
        //接收传过来的订单号，读取用户的购买配置，对应的去调用接口
        $where['order_id']=input('post.order_id');
        /*$where['order_id']=$data['order_id'];*/
        $where['status']=1;
        $res=Db::name('finance_order')->where($where)->find();
        $allocation=json_decode($res['allocation'],true);
        $res['allocation']=$allocation;
        $function=$allocation['type'];
        $res=$this->$function($res);
        //生成返点
        $this->rebate($res);
    }

    //短信购买
    public function sms($res)
    {

    }


    //返点
    public function rebate($data)
    {
        //查询当前购买人的详细信息
        $res=Db::name('userlist')->where('id',$data['userid'])->find();
        //判断是代理商还是代理商所属用户,和代理等级
        if($res['agent']!=0&&$res['a_id']==''){
            $level=$res['agent'];
            //购买人为代理商,判断该商品,获得在当前类型产品中的返点比例
            if($res['agent']!=0){
                $product=$data['product'];
                switch ($product){
                    case "短信服务":
                        $pro=json_decode($data['allocation'],JSON_UNESCAPED_UNICODE)['text'];
                        $ratio='agent_'.$level;
                        $ratio=Db::name('sms')->where('name',$pro)->field($ratio)->find()[$ratio];
                        $rebate=$data['money']*$ratio;
                        //将返点存入数据库
                        $code['money']=$rebate;
                        $code['userid']=$res['id'];
                        $code['create_time']=date('Y-m-d H:i:s',time());
                        $code['pro_type']=$product;
                        $code['order_id']=$data['order_id'];
                        $code['a_id']=$res['id'];
                        Db::name('rebate')->insert($code);
                        $rest['enterprise']=$res['enterprise']+$rebate;
                        Db::name('userlist')->where('id',$res['id'])->update($rest);
                        break;
                    case "域名服务":

                        break;
                    case "vps主机":
                        $pro=json_decode($data['allocation'],JSON_UNESCAPED_UNICODE)['conf']['typeid'];
                        $ratio='agent_'.$level;
                        $ratio=Db::name('vps_product')->where('id',$pro)->field($ratio)->find()[$ratio];
                        $rebate=$data['money']*$ratio;
                        //将返点存入数据库
                        $code['money']=$rebate;
                        $code['userid']=$res['id'];
                        $code['create_time']=date('Y-m-d H:i:s',time());
                        $code['pro_type']=$product;
                        $code['order_id']=$data['order_id'];
                        Db::name('rebate')->insert($code);
                        $rest['enterprise']=$res['enterprise']+$rebate;
                        Db::name('userlist')->where('id',$res['id'])->update($rest);
                        break;
                    case "云虚拟主机":
                        echo $product;
                        break;
                }
            }
        }elseif ($res['agent']==0 && $res['a_id']!=''){
            //购买人有上级代理商,获取上级代理商的等级
            $agent=Db::name('userlist')->where('id',$res['a_id'])->find();
            $level=$agent['agent'];
            $product=$data['product'];
            switch ($product){
                case "短信服务":
                    $pro=json_decode($data['allocation'],JSON_UNESCAPED_UNICODE)['text'];
                    $ratio='agent_'.$level;
                    $ratio=Db::name('sms')->where('name',$pro)->field($ratio)->find()[$ratio];
                    $rebate=$data['money']*$ratio;
                    //将返点存入数据库
                    $code['money']=$rebate;
                    $code['userid']=$res['id'];
                    $code['create_time']=date('Y-m-d H:i:s',time());
                    $code['pro_type']=$product;
                    $code['order_id']=$data['order_id'];
                    $code['a_id']=$agent['id'];
                    Db::name('rebate')->insert($code);
                    $rest['enterprise']=$agent['enterprise']+$rebate;
                    Db::name('userlist')->where('id',$agent['id'])->update($rest);
                    break;
                case "域名服务":

                    break;
                case "vps主机":
                    $pro=json_decode($data['allocation'],JSON_UNESCAPED_UNICODE)['conf']['typeid'];
                    $ratio='agent_'.$level;
                    $ratio=Db::name('vps_product')->where('id',$pro)->field($ratio)->find()[$ratio];
                    $rebate=$data['money']*$ratio;
                    //将返点存入数据库
                    $code['money']=$rebate;
                    $code['userid']=$res['id'];
                    $code['create_time']=date('Y-m-d H:i:s',time());
                    $code['pro_type']=$product;
                    $code['order_id']=$data['order_id'];
                    $code['a_id']=$agent['id'];
                    Db::name('rebate')->insert($code);
                    $rest['enterprise']=$agent['enterprise']+$rebate;
                    Db::name('userlist')->where('id',$agent['id'])->update($rest);
                    break;
                case "云虚拟主机":
                    echo $product;
                    break;
            }
        }

    }

    //付款
    public function payment()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result) {
            $type=input('post.type');
            $where['userid']=$result['id'];
            $where['order_id']=input('post.orderid');
            $data=Db::name('finance_order')->where($where)->find();
            if($data['status']==2){
                if ($type == 1) {//支付宝支付
                    $msg['data'] = $_SERVER['SERVER_NAME'].'/api/Alipay/doalipay?did=' . $data['order_id'] . '&money=' . $data['money'] . '&goods=' . $data['product'] . '&body=' . $data['product'];
                    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                } else if ($type == 2) {//微信支付
                    $url = $this->WxPay($data);
                    $msg['data'] = $_SERVER['SERVER_NAME'].'/api/Wxpay/index?url=' . $url . '&order=' . $data['order_id'] . '&userid=' . $data['userid'] . '&money=' . $data['money'] . '&goods=' . $data['product'] . '&orderid=' . $data['order_id'] . '&createtime=' . $data['create_time'];
                    echo json_encode($msg, JSON_UNESCAPED_UNICODE);
                } else {//余额支付，获取当前用户的余额
                    $balance = $result['balance'];
                    if($balance<$data['money']){
                        $res='ERROR';
                    }else{
                        $resu['balance'] = $balance-$data['money'];
                        if ($resu) {
                            $whe['order_id'] = $where['order_id'];
                            $code['payment'] = 4;
                            $code['status'] = 1;
                            Db::name('finance_order')->where($whe)->update($code);
                            $wh['id']=$where['userid'];
                            Db::name('userlist')->where($wh)->update($resu);
                            Session::set('home.balance', $resu['balance']);
                            //写入收支明细
                            unset($code['payment']);
                            $code['order_id'] = $where['order_id'];
                            $code['userid'] = $result['id'];
                            $code['product'] = $data['product'];
                            $code['transaction_type'] = 2;
                            $code['channel_type'] = 4;
                            $code['money'] = $data['money'];
                            $code['creat_time'] = date('Y-m-d H:i:s', time());
                            $res=Db::name('finance_detailed')->insert($code);
                        }
                    }
                    echo json_encode($res,JSON_UNESCAPED_UNICODE);
                }
            }else{
                echo json_encode('RPEAT',JSON_UNESCAPED_UNICODE);
            }
        }
    }

    //域名注册
    public function regDomain($res)
    {

        $where['id']=$res['userid'];
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $server = new domain();
            $k=count($res['allocation']['domain']);
            for($i=0;$i<$k;$i++){
                $data['data']['domain']=$res['allocation']['domain'][$i]['suffix'];
                $data['data']['term']=$res['allocation']['domain'][$i]['year'];
                $data['data']['tempid']=$res['allocation']['tempId'];
                $data['data']['privacy']=$res['allocation']['safe'];
                $data['function']='buy';
                $rest=$server->domain($data);
                $domainarray=['.com','.cn','.net'];
                $suffix=$res['allocation']['domain'][$i]['domain'];
                $usertype=Db::name('domain_temp')->where('id',$data['data']['tempid'])->field('type')->find();
                if(in_array($suffix,$domainarray)){
                    $realname=1;
                }else{
                    $realname=2;
                }
                if($rest['code']==200&&$rest['msg']=='command success'){
                    //注册成功
                    $code=[
                        'domainname'=>$data['data']['domain'],
                        'term'=>$data['data']['term'],
                        'suffix'=>$res['allocation']['domain'][$i]['domain'],
                        'type'=>$usertype['type'],
                        'userid'=>$result['id'],
                        'create_time'=>date('Y-m-d H:i:s',time()),
                        'last_time'=>date('Y-m-d H:i:s',strtotime('+'.$data['data']['term'].' year')),
                        'domain_temp'=>$data['data']['tempid'],
                        'username'=>$result['username'],
                        'realname'=>$realname,
                        'update_time'=>date('Y-m-d H:i:s',time()),
                        'did'=>$res['order_id']
                    ];
                    Db::name('domain')->insert($code);
                }
            }
        }

    }

    //域名模板列表
    public function domain_temp()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            $res=Db::name('domain_temp')->where($where)->select();
            foreach ($res as $k=> $v){
                $res[$k]['domain_num']=Db::name('domain')->where('tempid',$v['id'])->count();
                $res[$k]['tel']=$res[$k]['areacode'].'-'.$res[$k]['tel'];
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //拉取一条域名模板信息
    public function editdomain_temp()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $res=Db::name('domain_temp')->where($whe)->find();
            $res['tel']=$res['areacode'].'-'.$res['tel'];
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //编辑域名模板
    public function savedomain_temp()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $data=input('post.');
            $data['owner']=explode('|',$data['owner']);
            $data['owner_cn']=$data['owner']['0'];
            $data['owner_en']=$data['owner']['1'];
            $data['contacts']=explode('|',$data['contacts']);
            $data['contacts_cn']=$data['contacts']['0'];
            $data['contacts_en']=$data['contacts']['1'];
            $data['address']=explode('|',$data['address']);
            $data['address_cn']=$data['address']['0'];
            $data['address_en']=$data['address']['1'];
            //中文区域分割为省，市
            $d=explode(',',explode('|',$data['region'])[0]);
            $data['province_cn']=$d[1];
            $data['city_cn']=$d[2];
            //英文区域分割为省，市
            $p=explode(',',explode('|',$data['region'])[1]);
            $data['province_en']=$p['1'];
            $data['city_en']=$p['2'];
            $data['region']=explode('|',$data['region'])[0];
            $data['areacode']=explode('-',$data['tel'])[0];
            $data['tel']=explode('-',$data['tel'])[1];
            $data['status']=1;
            unset($data['ctype']);
            unset($data['id']);
            unset($data['uid']);
            unset($data['owner']);
            unset($data['contacts']);
            unset($data['address']);
            if($whe['id']==''){
                //新增
                $data['userid']=$result['id'];
                $data['create_time']=date('Y-m-d H:i:s',time());
                $res=Db::name('domain_temp')->insertGetId($data);
                $data['id']=$res;
            }else{
                //修改
                $res=Db::name('domain_temp')->where($whe)->update($data);
            }

            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除域名模板 801111491
    public function deldomain_temp()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['id']=input('post.id');
            $res=Db::name('domain_temp')->delete($whe);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //域名列表
    public function domain_list()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['userid']=$result['id'];
            $res=Db::name('domain')->where($whe)->order('id desc')->select();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取所有域名后缀
    public function domain_suffix()
    {

    }

    //域名价格列表
    public function domain_price()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['suffix']=input('post.suffix');
            if($where['suffix']){
                $years=input('post.years');
                if($years=='1'){
                    $field='first_price';
                }else if($years=='2'){
                    $field='second_price';
                }else if($years=='3'){
                    $field='third_price';
                }else if($years=='4'){
                    $field='fourth_price';
                }else if($years=='5'){
                    $field='fifth_price';
                }
                unset($where['userid']);
                $res=Db::name('domain_price')->where($where)->field($field)->select();
            }else{
                $res=Db::name('domain_price')->field('suffix,tag,promotion_price,first_price,text,renew_first_price,into_price')->select();
                foreach($res as $k =>$v){
                    $res[$k]['price']=[$res[$k]['first_price'],$res[$k]['promotion_price'],$res[$k]['renew_first_price'],$res[$k]['into_price']];
                    $res[$k]['reg']='';
                    $res[$k]['loading']=false;
                    $res[$k]['chose']=false;
                    unset($res[$k]['first_price']);
                    unset($res[$k]['promotion_price']);
                }
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //域名查询
    public function domain_select()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']=input('post.domain');
            $data['function']='select';
            $res=$server->domain($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //查询whois信息 801111491
    public function select_whois()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';

            $domain=input('post.domain');;
            $sys_api = "";
            $sys_user= "";
            $sys_pass= "";
            $cmdstrng = "other"."\r\n"."whois"."\r\n"."entityname:info"."\r\n";
            $cy_gongn="domain:" . $domain . "\r\n" . "." . "\r\n";
            $md5sing=md5( $sys_user.$sys_pass .substr($cmdstrng,0,10));
            $postdata=$sys_api."?userid=".$sys_user."&versig=".$md5sing."&strCmd=".urlencode($cmdstrng). rawurlencode(iconv("utf-8", "gb2312//IGNORE", $cy_gongn));
            $return =file_get_contents($postdata);
            $xml = xmlToArray($return);
            $res=json_decode($xml['returnmsg']);
            $niubi=object2array($res);
            $msg['data']=$niubi;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg);
    }

    //域名续费价格  801111491
    public function renew_domain()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe['suffix']=input('post.name');
            $years=input('post.year');
            if($years=='1'){
                $field='renew_first_price';
            }else if($years=='2'){
                $field='renew_second_price';
            }else if($years=='3'){
                $field='renew_third_price';
            }else if($years=='4'){
                $field='renew_fourth_price';
            }else if($years=='5'){
                $field='renew_fifth_price';
            }
            $res=Db::name('domain_price')->where($whe)->field($field)->find();
            $msg['data']=$res[$field];
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取域名解析列表
    public function domain_resolu_list()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']['domain']=input('post.domain');
            $data['function']='resolulist';
            $whe=[
                'status'=>1,
                'range'=>'domain'
            ];
            $type=Db::name('addons')->where($whe)->field('name')->find();
            $res=$server->domain($data);
            $msg['data']=$res;
            $msg['type']=$type['name'];
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg);
    }

    //添加域名解析
    public function add_domain_resolu()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']=input('post.');
            $data['function']='addresolu';
            $res=$server->domain($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //删除解析
    public function del_domain_resolu()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']=input('post.');
            $data['function']='delresolu';
            $res=$server->domain($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改解析
    public function edit_domain_resolu()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']=input('post.');
            $data['function']='editresolu';
            $res=$server->domain($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //解析状态更改
    public function status_domain_resolu()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $server = new domain();
            $data['data']=input('post.');
            $data['function']='status_resolu';
            $res=$server->domain($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取操作系统
    public function getOs()
    {

        $where['userid']=input('post.uid/s','','htmlspecialchars');
        $type=input('post.type/s','','htmlspecialchars');
        $name=input('post.name/s','','htmlspecialchars');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $res=System::getServerOs($type);
            if($name){
                $res=System::getServerVersion($name);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //vps产品列表
    public function vps_product_list()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $whe=[
                'room'=>input('post.room'),
                'status'=>1,
            ];
            $res=Db::name('fast_cloud')->where($whe)->order('id')->select();
            $msg['data']['product']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //购买vps
    public function vps($ress)
    {
        $where['id']=$ress['userid'];
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            // 生成机器名
            $root=getRandomString(12);
            $result=new vps();
            $data['data']=$ress;
            $data['function']='buy';
            $res=$result->vps($data);
            //接收数据
            $term=$ress['term'];
            if($term == 1){
                $payment='月付';
            }elseif ($term == 6){
                $payment='季付';
            }else{
                $payment='年付';
            }
            //将已开通的vps信息存入到数据库
            $code=[
                'userid'=>$res['userid'],
                'did'=>$res['did'],
                'user'=>$res['user'],
                'pwd'=>$res['pwd'],
                'ip'=>$res['ip'],
                'create_time'=>$res['create_time'],
                'last_time'=>$res['last_time'],
                'os'=>$res['os'],
                'productid'=>$res['productid'],
                'cpu'=>$res['cpu'],
                'bid'=>$res['bid'],
                'payment'=>$payment,
                'hardisk'=>$res['hardisk'],
                'memory'=>$res['memory'],
                'vpsname'=>'',
                'root'=>$root,
                'allocation'=>$res['allocation'],
                'bandwidth'=>$res['bandwidth'],
                'room'=>$res['room']
            ];
            Db::name('vps')->insert($code);

        }

    }

    // 获取已购买的vps列表
    public function get_vps_list()
    {
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['userid']=$result['id'];
            $msg['vpsnum']=Db::name('vps')->where($type)->count();
            $res=Db::name('vps')->where($type)->limit($offerset,$display)->select();
            $last_time=date('Y-m-d H:i:s',  strtotime("+1 month"));
            foreach ($res as $k=>$v){
                if($v['last_time']<$last_time){
                    $res[$k]['expire']=1;
                }else{
                    $res[$k]['expire']=0;
                }
                unset($res[$k]['pwd']);
                unset($res[$k]['did']);
                unset($res[$k]['bid']);
                unset($res[$k]['bid']);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获得所有路线
    public function getRoom()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['status']='1';
            //$type['identify']=input('post.type');
            $res=Db::name('productgroup')->where($type)->field('id,name,identify')->select();
            foreach($res as $k=>$v){
                $res[$k]['value']=$v['id'];
                $res[$k]['type']=$v['identify'];
                unset($res[$k]['id']);
                unset($res[$k]['identify']);

            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //vps价格选择
    public function get_vps_price()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.id');
            $type['term']=input('post.term');
            $res=Db::name('product_price')->where($type)->field('price')->find();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取当前所选择的产品可购买的时间长短
    public function buy_vps_length()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.id');
            $res=Db::name('product_price')->where($type)->order('id')->field('term')->select();
            foreach($res as $k=>$v){
                if($v['term']=='1'){
                    $res[$k]['name']='1个月';
                    $res[$k]['tag']='';
                }
                if($v['term']=='6'){
                    $res[$k]['name']='6个月';
                    $res[$k]['tag']='';
                }
                if($v['term']=='12'){
                    $res[$k]['name']='1年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='24'){
                    $res[$k]['name']='2年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='36'){
                    $res[$k]['name']='3年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='60'){
                    $res[$k]['name']='5年';
                    $res[$k]['tag']='sale';
                }
                $res[$k]['value']=(int)$v['term'];
                unset($res[$k]['term']);

            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改vpsname
    public function editVpsName()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['id']=input('post.id');
            $data['vpsname']=input('post.vpsname');
            $res=Db::name('vps')->where($type)->update($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取快云的跳转链接
    public function manage()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['id']=input('post.id');
            $res=Db::name('vps')->find($type);
            $vps=new vps();
            $data['data']=$res;
            $data['function']='manage';
            $res=$vps->vps($data);

            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //查询当前用户是否已经实名
    public function getAuthinfo()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $res=Db::name('auth')->where('userid',$result['id'])->find();
            $res['cardnum']=substr($res['cardnum'],0,8).'***********';
            if($res){
                $msg['data']=$res;
            }else{
                $msg['data']=null;
            }
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //发起实名
    public function getAuth()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data=input('post.');
            $data['userid']=$result['id'];
            $data['status']=2;
            $data['create_time']=date('Y-m-d H:i:s',time());
            unset($data['uid']);
            Db::name('auth')->where('userid',$result['id'])->delete();
            $res=Db::name('auth')->insert($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //文件上传接口
    public function fileUpload()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询后台配置
            $status=Db::name('attachment')->find();
            $pictures=[];
            $files=$this->request->file('file');
            if($status['status']=='1'){
                //bos
                $BOS_TEST_CONFIG =
                    array(
                        'credentials' => array(
                            'ak' => $status['ak'],
                            'sk' => $status['sk'],
                        ),
                        'endpoint' => $status['domain'],
                    );
                $filename = date('YmdHis', time()) . rand(100, 999) . '.jpg';
                $path = bos($status['bucket'], $filename, $files->getinfo()['tmp_name'], $BOS_TEST_CONFIG);
                $path=$status['domain'].'/'.$status['bucket'].'/'.$filename;

            }else{
                //服务器
                $info = $files->move(ROOT_PATH . '/public'. DS . 'uploads');
                if($info) {
                    $code= str_replace('\\','/',$info->getSaveName());
                    $path=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME']."/uploads/". $code;
                }
            }
            $msg['data']=$path;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //短信产品列表
    public function getSmsList()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data=Db::name('sms')->where('status',1)->field('id,name')->select();
            foreach ($data as $k=>$v){
                $data[$k]['value']=$v['id'];
                unset($data[$k]['id']);
            }
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取短信价格
    public function getSmsPrice()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $data=Db::name('sms')->where('id',input('post.id'))->field('price')->find();
            $msg['data']=$data['price'];
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //查询代理商的信息
    public function getAgentInfo()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $res=Db::name('auth')->where('userid',$result['id'])->find();
            $data['tel']=substr($result['phone'],3);;
            $data['level']=Db::name('agent')->where('id',$result['agent'])->find()['name'];
            $data['name']=$res['name'];
            $data['email']=$result['email'];
            $data['customnum']=Db::name('userlist')->where('a_id',$result['id'])->count();
            $msg['data']=$data;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //返点收益分布
    public function rebateSurvey()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //默认时间为1周
            $time=input('post.time');
            if($time){
                $time=explode(',',$time);
                $time['1']=$time['1'].' 23:59:59';
            }else{
                $time=[date("Y-m-d",strtotime("-1 week")),date('Y-m-d H:i:s',time())];
            }
            $whe['create_time']=['between',$time];
            $whe['a_id']=$result['id'];
            $res=Db::table('gee_rebate')
                ->where($whe)
                ->field('pro_type')
                ->group('pro_type')
                ->select();
            $array=[];
            foreach ($res as $k=>$v) {
                $whe['pro_type']=$v['pro_type'];
                $array[$k]['name']=$v['pro_type'];
                $array[$k]['value']=Db::name('rebate')->where($whe)->sum('money');
            }
            $msg['data']=[
                'name'=>"近期返点收益分布",
                'type'=>"pie",
                'data'=>$array,

            ];
            $msg['data']=array($msg['data']);

        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //返点收益趋势
    public function rebateTrend()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';

            $time=input('post.time');
            if($time){
                $time=explode(',',$time);
                $time['1']=$time['1'].' 23:59:59';
            }else{
                $time=[date("Y-m-d H:i:s",strtotime("-6 month")),date('Y-m-d H:i:s',time())];
            }
            $whe['create_time']=['between',$time];
            $whe['a_id']=$result['id'];
            $res=Db::table('gee_rebate')
                ->where($whe)
                ->field('pro_type')
                ->group('pro_type')
                ->select();
            $array=[];
            $time1=strtotime($whe['create_time'][1][0]);
            $time2=strtotime($whe['create_time'][1][1]);
            foreach ($res as $k=>$v) {
                $whe['pro_type']=$v['pro_type'];
                $array[$k]['name']=$v['pro_type'];
                $array[$k]['stack']="总量";
                $timec=round(($time2-$time1)/3600/24);
                for($i=0;$i<=$timec;$i++){
                    $whe['create_time'][1][0]=date('Y-m-d H:i:s',$time1+(3600*24*$i));
                    $whe['create_time'][1][1]=date('Y-m-d H:i:s',$time1+(3600*24*($i+1)));
                    $rest=Db::name('rebate')->where($whe)->sum('money');
                    if($rest!=null){
                        $array[$k]['data'][$i]=$rest;
                    }else{
                        $array[$k]['data'][$i]=0;
                    }
                }

            }
            $array[0]['xset']=['周一','周二','周三','周四','周五','周六','周日'];
            $msg['data']=$array;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //客户返点top10
    public function rankTopTen()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //降序排列查询十位返点客户

            $time=input('post.time');
            if($time){
                $time=explode(',',$time);
                $time['1']=$time['1'].' 23:59:59';
            }else{
                $time=[date("Y-m-d H:i:s",strtotime("-6 month")),date('Y-m-d H:i:s',time())];
            }
            $whe['create_time']=['between',$time];
            $whe['a_id']=$result['id'];
            $res=Db::name('rebate')->where($whe)->group('userid')->limit(0,10)->select();
            unset($whe['a_id']);

            $array=[];
            foreach ($res as $k=>$v){
                $user=Db::name('userlist')->where('id',$v['userid'])->find();
                $array[$k]['id']=$user['userid'];
                $array[$k]['name']=$user['username'];
                $array[$k]['linkname']=Db::name('linkman')->where('userid',$v['userid'])->find()['name'];
                $whe['userid']=$v['userid'];
                $array[$k]['rebate']=Db::name('rebate')->where($whe)->sum('money');
            }
            foreach ($array as $k=>$v){
                $rebate[$k]=$v['rebate'];
            }
            $data=array_multisort($rebate,SORT_DESC,$array);
            $msg['data']=$array;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //代理管理列表
    public function agentMange()
    {
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询当前代理商下的所有客户
            $array=Db::name('userlist')->where('a_id',$result['id'])->limit($offerset,$display)->field('id,username,userid,phone,email,balance,remark')->select();
            $arrcount=Db::name('userlist')->where('a_id',$result['id'])->count();
            foreach ($array as $k=>$v){
                $array[$k]['phone']=substr($v['phone'],3);
                $array[$k]['rebate']=Db::name('rebate')->where('userid',$v['id'])->sum('money');
            }
            $msg['data']=$array;
            $msg['count']=$arrcount;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改客户备注
    public function editRemark()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $id=input('post.id');
            $data['remark']=input('post.remark');
            $array=Db::name('userlist')->where('id',$id)->update($data);
            $msg['data']=$array;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //生成分销链接
    public function agentLink()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //生成包含当前用户信息的链接
            $sign=base64_encode(encode($result['id'].'+'.$result['userid'].'+'.$result['username']));
            $res=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/login/index/index/token/'.$sign;
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //消费概览
    public function financeOverview()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //查询最近六个月的消费情况
            $whe['userid']=$result['id'];
            $whe['status']='1';
            $res=Db::name('finance_detailed')->where('product','<>','充值')->where($whe)->group('product')->field('id,product')->select();
            $time=[date("Y-m-d H:i:s",strtotime("-6 month")),date('Y-m-d H:i:s',time())];
            $whe['creat_time']=['between',$time];
            $array=[];
            foreach ($res as $k =>$v){
                $array[$k]['name']=$v['product'];
                $array[$k]['stack']='总量';
                $array[$k]['type']='bar';
                $whe['product']=$v['product'];
                //获取6月前月份
                $month=date('Y-m-d',strtotime('-6 month'));
                for($i=0;$i<=6;$i++){
                    $time=getthemonth(date('Y-m-d',strtotime(-$i.' month')));
                    $whe['creat_time'][1][0]=$time[0];
                    $whe['creat_time'][1][1]=$time[1].' 23:59:59';
                    $rest=Db::name('finance_detailed')->where($whe)->sum('money');
                    if($rest!=null){
                        $array[$k]['data'][$i]=$rest;
                    }else{
                        $array[$k]['data'][$i]=0;
                    }
                    if($k=='0'){
                        $array[$k]['xset'][$i]=substr($time[0],0,7);
                    }

                }
                if($k=='0'){
                    $array[$k]['xset']=array_reverse($array[$k]['xset']);
                }
                $array[$k]['data']=array_reverse($array[$k]['data']);
            }
            $msg['data']['loading']='false';
            $msg['data']['data']=$array;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //虚拟主机列表
    public function getVhostList()
    {
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['userid']=$result['id'];
            $find=input('post.find');
            if($find){
                $type['vhostname']=['like','%'.$find.'%'];
            }
            $msg['vhostnum']=Db::name('vhostlist')->where($type)->count();
            $res=Db::name('vhostlist')->where($type)->limit($offerset,$display)->select();
            $last_time=date('Y-m-d H:i:s',  strtotime("+1 month"));
            foreach ($res as $k=>$v){
                if($v['last_time']<$last_time){
                    $res[$k]['expire']=1;
                }else{
                    $res[$k]['expire']=0;
                }
                unset($res[$k]['did']);
                unset($res[$k]['bid']);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //虚拟主机产品列表
    public function getProductVhost()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $room=input('post.id');
            $whe=[
                'pro_id'=>$room,
                'status'=>1,
            ];
            $res=Db::name('vhostproduct')->where($whe)->field('agent_1,agent_2,agent_3,agent_4,agent_5,creat_time,updat_time,language',true)->order('id')->select();
            $msg['data']['product']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //虚拟主机可购买时长
    public function buyVhostLength()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.title');
            $res=Db::name('product_price')->where($type)->field('term')->select();
            foreach($res as $k=>$v){
                if($v['term']=='12'){
                    $res[$k]['name']='1年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='24'){
                    $res[$k]['name']='2年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='36'){
                    $res[$k]['name']='3年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='48'){
                    $res[$k]['name']='4年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='60'){
                    $res[$k]['name']='5年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='120'){
                    $res[$k]['name']='10年';
                    $res[$k]['tag']='sale';
                }
                $res[$k]['value']=(int)$v['term'];
                unset($res[$k]['term']);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //虚拟主机价格选择
    public function getVhostPrice()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.title');
            $type['term']=input('post.term');
            $res=Db::name('product_price')->where($type)->field('price')->find();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //虚拟主机购买
    public function vhost($ress)
    {
        $where['id']=$ress['userid'];
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            // 生成机器名
            $root=getRandomString(12);
            $result=new vhost();
            $data['data']=$ress;
            $data['function']='buy';
            $res=$result->vhost($data);
            //接收数据
            $code=[
                'userid'=>$result['id'],
                'did'=>$res['did'],
                'pro_id'=>$res['pro_id'],
                'space'=>$res['space'],
                'webnum'=>$res['webnum'],
                'domainnum'=>$res['domainnum'],
                'ip'=>$res['ip'],
                'create_time'=>$res['create_time'],
                'last_time'=>$res['last_time'],
                'system'=>$res['system'],
                'dbsize'=>$res['dbsize'],
                'flow'=>$res['flow'],
                'maxconnect'=>$res['maxconnect'],
                'email'=>$res['email'],
                'phone'=>$res['phone'],
                'bid'=>$res['bid'],
                'payment'=>'年付',
                'root'=>$root,
                'cname'=>$res['domainName'],
                'allocation'=>$res['allocation'],
            ];
            Db::name('vhostlist')->insert($code);

        }
    }

    //修改虚拟主机name
    public function editVhostName()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['id']=input('post.id');
            $data['vhostname']=input('post.vhostname');
            $res=Db::name('vhostlist')->where($type)->update($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取快云的跳转链接
    public function vhostmanage()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['id']=input('post.id');
            $res=Db::name('vhostlist')->find($type);
            $vhost=new vhost();
            $data['data']=$res;
            $data['function']='manage';
            $res=$vhost->vhost($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //当月消费详情
    public function consumeDetails()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];

            $time=getthemonth(date('Y-m-d'));
            $time[1]=$time[1].' 23:59:59';
            $where['creat_time']=['between',$time];
            $res=Db::name('finance_detailed')->where($where)->field('product,creat_time,money,order_id')->select();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }

        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取服务器租用列表
    public function getServerHostList()
    {
        $display=input('post.display');
        $current=input('post.current');
        $offerset=$display*($current-1);
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['userid']=$result['id'];
            $msg['vhostnum']=Db::name('rentserver')->where($type)->count();
            $res=Db::name('rentserver')->where($type)->limit($offerset,$display)->select();
            $last_time=date('Y-m-d H:i:s',  strtotime("+1 month"));
            foreach ($res as $k=>$v){
                if($v['last_time']<$last_time){
                    $res[$k]['expire']=1;
                }else{
                    $res[$k]['expire']=0;
                }
                unset($res[$k]['did']);
                unset($res[$k]['bid']);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //服务器租用产品列表
    public function getServerHost()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $room=input('post.id');
            $whe=[
                'room'=>$room,
                'status'=>1,
            ];
            $res=Db::name('serverhost')->where($whe)->field('agent_1,agent_2,agent_3,agent_4,agent_5,creat_time,updat_time,language',true)->order('id')->select();
            foreach ($res as $k => $v){
                $res[$k]['system']=json_decode($v['system'],JSON_UNESCAPED_UNICODE);
            }
            $msg['data']['product']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //服务器租用时长
    public function getServerHostLength()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.title');
            $res=Db::name('product_price')->where($type)->field('term')->select();
            foreach($res as $k=>$v){
                if($v['term']=='1'){
                    $res[$k]['name']='1个月';
                    $res[$k]['tag']='';
                }
                if($v['term']=='6'){
                    $res[$k]['name']='半年';
                    $res[$k]['tag']='';
                }
                if($v['term']=='12'){
                    $res[$k]['name']='1年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='24'){
                    $res[$k]['name']='2年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='36'){
                    $res[$k]['name']='3年';
                    $res[$k]['tag']='sale';
                }
                if($v['term']=='60'){
                    $res[$k]['name']='5年';
                    $res[$k]['tag']='sale';
                }

                $res[$k]['value']=(int)$v['term'];
                unset($res[$k]['term']);
            }
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //服务器租用价格选择
    public function getServerHostPrice()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['p_id']=input('post.title');
            $type['term']=input('post.term');
            $res=Db::name('product_price')->where($type)->field('price')->find();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改虚拟主机name
    public function editServerHostName()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $type['id']=input('post.id');
            $data['servername']=input('post.servername');
            $res=Db::name('rentserver')->where($type)->update($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取展品服务中的所有数据
    public function DataOverview()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            //分别查出短信，vhost，vps，domain，sms的使用情况
            $smscount=Db::name('sms_appname')->where($where)->count();
            $domain_count=Db::name('domain')->where($where)->count();
            $where['suffix']=array('in',['.com','.cn','.net']);
            $where['realname']=array('in',[1,2]);
            $unreal_domain=Db::name('domain')->where($where)->count();
            unset($where['suffix']);
            unset($where['realname']);
            $vhostnum=Db::name('vhostlist')->where($where)->count();
            $vpsnum=Db::name('vps')->where($where)->count();
            $res=[
                'sms'=>[
                    'name'=>'短信',
                    'icon'=>'product',
                    'router'=>'smsall',
                    'data'=>[['name'=>'剩余短信数量','count'=>$smscount]]
                ],
                'domain'=>[
                    'name'=>'域名',
                    'icon'=>'product',
                    'router'=>'domainall',
                    'data'=>[['name'=>'购买域名总数','count'=>$domain_count],['name'=>'未完成实名域名数','count'=>$unreal_domain]]
                ],
                'vhost'=>[
                    'name'=>'虚拟主机',
                    'icon'=>'product',
                    'router'=>'vhostall',
                    'data'=>[['name'=>'购买虚拟主机总数','count'=>$vhostnum]]
                ],
                'vps'=>[
                    'name'=>'vps',
                    'icon'=>'product',
                    'router'=>'vpsall',
                    'data'=>[['name'=>'购买vps数量','count'=>$vpsnum]]
                ],
            ];
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //修改密码
    public function editPassword()
    {
        $res=Db::name('smstime')->where('code',input('post.smscode'))->find();
        if($res){
            $where['userid']=input('post.uid');
            $result=Db::name('userlist')->where($where)->find();
            if($result){
                $msg['success']='true';
                $whe['id']=$result['id'];
                $data['password']=md5(input('post.password'));
                $res=Db::name('userlist')->where($whe)->update($data);
                $msg['data']=$res;
            }else{
                $msg['success']='false';
            }
        }else{
            $msg['data']='code error';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //发送邮件
    public function sendMail()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            //获取邮件模板
            $emailtpl=Db::name('email')->where('type',998)->find()['content'];
            //生成激活链接
            $code=base64_encode($result['userid'].'/'.time().'/86400');
            $url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/index.php?s=index/index/active/code/'.$code;
            $data['body']=str_replace('{**}',$result['username'],$emailtpl);
            $data['body']=str_replace('{%%}',$url,$data['body']);
            $data['subject']='绑定邮箱';
            $data['toemail']=input('post.email');
            $res=send_mail($data);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取类型
    public function getCouponType ()
    {
        $where['userid']=input('post.uid');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $res=Db::name('product')->order('id')->select();
            $arr=['id'=>0,'name'=>'通用','type'=>'ALL'];
            array_unshift($res,$arr);
            array_pop($res);
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }

    //获取当前用户正常的代金券
    public function getCoupon()
    {
        $where['userid']=input('post.uid/s','','htmlspecialchars');
        $result=Db::name('userlist')->where($where)->find();
        if($result){
            $msg['success']='true';
            $where['userid']=$result['id'];
            $type=input('post.type/s','','htmlspecialchars');
            $mix=input('post.mix/s','','htmlspecialchars');
            $money=input('post.money/s','','htmlspecialchars');

            if($type!=''){
                if($mix=='1'){
                    $where['type']=[['=',$type],['=','ALL'],'or'];
                }else{
                    $where['type']=$type;
                }
            }

            $where['status']=1;
            $where['endtime']=['>',date('Y-m-d H:i:s',time())];
            if($money){
                $where['money']=['<=',$money];
            }

            $res=Db::name('coupon')->where($where)->order('endtime')->select();
            $msg['data']=$res;
        }else{
            $msg['success']='false';
        }
        echo json_encode($msg,JSON_UNESCAPED_UNICODE);
    }




}
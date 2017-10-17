<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23
 * Time: 17:15
 */
namespace app\api\controller;
use think\Controller;
use think\Session;
use think\Db;
class WxPay extends Controller
{
    public function index()
    {
        $data['url']=input('url');
        $data['money']=input('money');
        $data['goods']=input('goods');
        $data['orderid']=input('orderid');
        $data['order']=input('order');
        $data['userid']=input('userid');
        Session::set('order',$data['order']);
        Session::set('order_id',$data['orderid']);
        $data['create_time']=input('createtime');
        $this->assign('data',$data);
        return view();
    }

    //订单状态返回
    public function wxpayok()
    {
        $_REQUEST['out_trade_no']=input('post.order');
        ini_set('date.timezone','Asia/shanghai');
        error_reporting(E_ERROR);
        require_once VENDOR_PATH . 'wxpay/lib/WxPay.Api.php';
        require_once VENDOR_PATH.  'wxpay/example/log.php';
        $logHandler= new \CLogFileHandler(VENDOR_PATH ."./logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

        function printf_info($data)
        {
            /*if($data['trade_state']=='SUCCESS'){
                $where['order_id']=Session::get('order_id');
                $result=Db::name('finance_detailed')->where($where)->find();
                $userid['id']=$result['userid'];
                $status['status']=1;
                if($result['transaction_type']=='1'){
                    $rest=Db::name('userlist')->where($userid)->find();
                    $balance['balance']=$result['money']+$rest['balance'];
                    Db::name('userlist')->where($userid)->update($balance);
                    $res=Db::name('finance_detailed')->where($where)->update($status);

                }else{
                    Db::name('finance_order')->where($where)->update($status);
                    $res=Db::name('finance_detailed')->where($where)->update($status);
                }
            }else{
                $res=0;
            }*/
            if($data['trade_state']=='SUCCESS'){
                $where['order_id']=Session::get('order_id');
                $status['status']=1;
                Db::name('finance_order')->where($where)->update($status);
                $code['userid']=Session::get('order')['userid'];
                $code['order_id']=Session::get('order')['order'];
                $code['product']=Session::get('order')['goods'];
                if($code['product']=='充值'){
                    $code['transaction_type']=1;
                }else{
                    $code['transaction_type']=2;
                }
                $code['channel_type']=2;
                $code['money']=Session::get('order')['money'];
                $code['creat_time']=date('Y-m-d H:i:s',time());
                $res=Db::name('finance_detailed')->insert($code);
            }else{
                $res=0;
            }
                echo json_encode($res,JSON_UNESCAPED_UNICODE);
        }

        if (isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != "") {
            $transaction_id = $_REQUEST["transaction_id"];
            $input = new \WxPayOrderQuery();
            $input->SetTransaction_id($transaction_id);
            printf_info(\WxPayApi::orderQuery($input));
            exit();
        }

        if (isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != "") {
            $out_trade_no = $_REQUEST["out_trade_no"];
            $input = new \WxPayOrderQuery();
            $input->SetOut_trade_no($out_trade_no);
            $data=\WxPayApi::orderQuery($input);

            printf_info($data);
            exit();
        }
    }
}
<?php
namespace app\index\controller;

use app\index\controller\Common; // 前置操作
use app\index\model\GeeBilling; //用户表
// use app\index\model\GeeDeal; //收支明细表
use app\index\model\GeeOrder; //交易记录表
use app\index\model\GeeProConfig; //订单表
use app\index\model\GeeUser; //订单表
use app\admin\model\GeeDomainContact; //产品分类表
use app\admin\model\GeeDomainPrice; //产品分类表
use app\admin\model\GeeAddons; // 
use app\admin\model\GeeProduct; //产品组表
use app\admin\model\GeeProductClass; //产品表
use app\admin\model\GeeProductGroup; //插件表
use app\index\model\GeeDomain; //产品购买配置表
use think\Controller;
use think\Request;
// 请求类

class Api extends Controller
{
    /**
     * 支付宝同步接口
     */
    public function return_url(Request $request)
    {
        // $deal = new GeeDeal();
        $u = new GeeUser();
        $o = new GeeOrder();
        $b = new GeeBilling();
        $pc = new GeeProConfig();

        $post = input();
        // $post = $request->param();
        //写在文本里看一下参数
        $data = json_encode($post);
        file_put_contents("alipaytext.txt", $data);
        
		vendor('alipay.AlipayTradeService');
		$alipaySevice = new \AlipayTradeService(Config('alipay'));
        $alipaySevice->writeLog(var_export($post,true));
        $result = $alipaySevice->check($post);
        
        // $deal->where('num = ' . $post['out_trade_no'])->update(['dealnum' => $post['trade_no']]);
        if ($post['trade_status'] == "TRADE_SUCCESS") {
            //操作数据库 修改状态
            $dinfo = $b->where('order_number = ' . $post['out_trade_no'])->find();
            if ($dinfo['order_type'] == '0') {
                $uinfo = $u->where('id = ' . $dinfo['user_id'])->find();
                $u->where('id = ' . $dinfo['user_id'])->update(['balance' => ((double) $uinfo['balance'] + (double) $dinfo['money'])]);
            } else if ($dinfo['order_type'] == '4') {
                $pcs = $pc->where('order_number = "' . $data['order'] . '"')->find();
                $configs = json_decode($pcs['config'], true);
                if($configs['_create_putData']){
                  if($configs['_create_putData']['class'] == 'domain'){
                    $d = new GeeDomain();
                    $dc = new GeeDomainContact();
                    $dp = new GeeDomainPrice();
                    $pro = new GeeProduct();
                    $addons = new GeeAddons();
                    $plug = new $configs['_create_putData']['plug']();
                    $func = $configs['_create_putData']['class'];
                    // dump($configs['_create_putData']);
                    foreach ($configs['_create_putData']['data'] as $k => $v) {
                        // dump($v);
                        $suffix = '.' . explode(".", $v['domainname'])[1];
                        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
                        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
                        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
                        if ($configs['_create_putData']['action'] != 'domainRenew') {
                            $dcinfo = $dc->where('id = ' . $v['userid'])->find();
                            $userid = json_decode($dcinfo['contact_id'], true)[$proinfo['name']]['value'];
                        }
                        // dump(json_decode($dcinfo['contact_id'],true));
                        if ($configs['_create_putData']['action'] == 'domainRenew') {
                            $putData = [
                                'way' => $adninfo['name'],
                                'pro_id' => $dpinfo['pro_id'],
                                'function' => 'control',
                                'action' => $configs['_create_putData']['action'],
                                'data' => [
                                    "domainname" => $v['domainname'],
                                    "years" => $v['years'],
                                    "exptme" => $v['exptme'],
                                ],
                            ];
                        } else {
                            $putData = [
                                'way' => $adninfo['name'],
                                'pro_id' => $dpinfo['pro_id'],
                                'function' => 'control',
                                'action' => 'createDom',
                                'data' => [
                                    'userid' => $userid,
                                    'domainname' => $v['domainname'],
                                    'years' => $v['years'],
                                    'domainpass' => $v['domainpass'],
                                    'dns1' => $v['dns1'],
                                    'dns2' => $v['dns2'],
                                ],
                            ];
                        }
                        // dump($v);
                        // dump($putData);

                        $res = $plug->$func($putData);
                        $res = json_decode($res, true);
                        if ($res['status'] == 'failed') {
                            $ret['status'] = 400;
                            $ret['msg'] = '请求超时！请联系管理员处理！错误码:' . explode(' ', $res['data'])[0];
                            $isfailed = true;
                            break;
                        } else {
                            if ($configs['_create_putData']['action'] == 'domainRenew') {
                                $save = [
                                    'user_id' => session('_userInfo')['id'],
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                ];
                                $d->where('domainname = "' . $v['domainname'] . '"')->update($save);
                            } else {
                                $save = [
                                    'user_id' => session('_userInfo')['id'],
                                    'userid' => $userid,
                                    'domainname' => $v['domainname'],
                                    'years' => $v['years'],
                                    'domainpass' => $v['domainpass'],
                                    'isname' => 0,
                                    'dns' => json_encode(['dns1' => $v['dns1'], 'dns2' => $v['dns2']]),
                                    'status' => 0,
                                    'r_status' => 0,
                                    'newstas' => 0,
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                ];
                                $d->save($save);
                            }
                        }
                    }
                    // return;

                    // dump($ret);
                    if ($isfailed) {
                        // dump(session('_userInfo')['balance']);
                        // dump($info['money']);
                        $userup = db('gee_user')->where('id = ' . session('_userInfo')['id'])->update(['balance' => ((double) session('_userInfo')['balance'] - (double) $info['money']) + (double) $info['money']]);
                        return json_encode($ret);
                    }
                  } else {
                    //vps 或 其他通用插件类购买
                    $plug = new $configs['_create_putData']['plug']();
                    $func = $configs['_create_putData']['class'];
                    $putData = $configs['_create_putData']['data'];
                    if (!$putData['function']) {
                        $putData['function'] = $configs['_create_putData']['function'];
                    }
                    $res = $plug->$func($putData);
                  }

                } else {
                  //租用物理服务器
                  $server = new GeeServer();
                  $selfPro = object_toArray($selfPro);
                  foreach($selfPro as $k=>$v){
                    if($v['pro_type'] == 'server') {
                      if($info['order_type'] == 'renew'){
                        $sinfo = $server->where('id',(int)$v['id'])->find();
                        $dt=date('Y-m-d H:i:s',$sinfo['end_time']);
                        $updata = [
                          'server_added' => $v['added'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime($dt."+".(int)$v['years']." month"))),
                        ];
                        if($sinfo['status'] == 1){
                          $updata['status'] = 3;
                        }
                        $server->where('id = '.$v['id'])->update($updata);
                      } else {
                        $sinfo = [
                          'pro_group_id'=> $v['group_id'],
                          'pro_id'=> $v['pro_id'],
                          'server_added'=> $v['added'],
                          'name'=> $v['hostname'],
                          'user_id'=> $v['user_id'],
                          'remake'=> $v['remake'],
                          'username'=> $v['username'],
                          'password'=> $v['password'],
                          'osgroup'=> (int)$v['osgroup'],
                          'ostype'=> (int)$v['ostype'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime("+".(int)$v['years']." month"))),
                          'status'=> '0'
                        ];
                        $server->save($sinfo);
                      }
                    }
                  }
                }
                
            }
            // $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 1]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 1]);
            echo "SUCCESS";
            $this->redirect('console/Billing/order');
        } else {
            // $deal->where('num = ' . $post['out_trade_no'])->update(['status' => 3]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 0]);
            $this->redirect('console/Billing/order');
            // echo "SUCCESS";
        }
        //写在文本里看一下参数
        $data = json_encode($post);
        file_put_contents("alipaytext.txt", $data);
        // $this->redirect('console/Index/index');
    }
    /**
     * 支付宝异步接口
     */
    public function notify_url()
    {
    	
        // $deal = new GeeDeal();
        $u = new GeeUser();
        $o = new GeeOrder();
        $b = new GeeBilling();
        $pc = new GeeProConfig();

        $post = input();
        // $post = $request->param();
        //写在文本里看一下参数
        $data = json_encode($post);
        file_put_contents("alipaytext.txt", $data);
        
		vendor('alipay.AlipayTradeService');
		$alipaySevice = new \AlipayTradeService(Config('alipay'));
        $alipaySevice->writeLog(var_export($post,true));
        $result = $alipaySevice->check($post);
        
        // $deal->where('num = ' . $post['out_trade_no'])->update(['dealnum' => $post['trade_no']]);
        if ($post['trade_status'] == "TRADE_SUCCESS") {
            //操作数据库 修改状态
            $dinfo = $b->where('order_number = ' . $post['out_trade_no'])->find();
            if ($dinfo['order_type'] == '0') {
                $uinfo = $u->where('id = ' . $dinfo['user_id'])->find();
                $u->where('id = ' . $dinfo['user_id'])->update(['balance' => ((double) $uinfo['balance'] + (double) $dinfo['money'])]);
            } else if ($dinfo['order_type'] == '4') {
                $pcs = $pc->where('order_number = "' . $data['order'] . '"')->find();
                $configs = json_decode($pcs['config'], true);
                if($configs['_create_putData']){
                  if($configs['_create_putData']['class'] == 'domain'){
                    $d = new GeeDomain();
                    $dc = new GeeDomainContact();
                    $dp = new GeeDomainPrice();
                    $pro = new GeeProduct();
                    $addons = new GeeAddons();
                    $plug = new $configs['_create_putData']['plug']();
                    $func = $configs['_create_putData']['class'];
                    // dump($configs['_create_putData']);
                    foreach ($configs['_create_putData']['data'] as $k => $v) {
                        // dump($v);
                        $suffix = '.' . explode(".", $v['domainname'])[1];
                        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
                        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
                        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
                        if ($configs['_create_putData']['action'] != 'domainRenew') {
                            $dcinfo = $dc->where('id = ' . $v['userid'])->find();
                            $userid = json_decode($dcinfo['contact_id'], true)[$proinfo['name']]['value'];
                        }
                        // dump(json_decode($dcinfo['contact_id'],true));
                        if ($configs['_create_putData']['action'] == 'domainRenew') {
                            $putData = [
                                'way' => $adninfo['name'],
                                'pro_id' => $dpinfo['pro_id'],
                                'function' => 'control',
                                'action' => $configs['_create_putData']['action'],
                                'data' => [
                                    "domainname" => $v['domainname'],
                                    "years" => $v['years'],
                                    "exptme" => $v['exptme'],
                                ],
                            ];
                        } else {
                            $putData = [
                                'way' => $adninfo['name'],
                                'pro_id' => $dpinfo['pro_id'],
                                'function' => 'control',
                                'action' => 'createDom',
                                'data' => [
                                    'userid' => $userid,
                                    'domainname' => $v['domainname'],
                                    'years' => $v['years'],
                                    'domainpass' => $v['domainpass'],
                                    'dns1' => $v['dns1'],
                                    'dns2' => $v['dns2'],
                                ],
                            ];
                        }
                        // dump($v);
                        // dump($putData);

                        $res = $plug->$func($putData);
                        $res = json_decode($res, true);
                        if ($res['status'] == 'failed') {
                            $ret['status'] = 400;
                            $ret['msg'] = '请求超时！请联系管理员处理！错误码:' . explode(' ', $res['data'])[0];
                            $isfailed = true;
                            break;
                        } else {
                            if ($configs['_create_putData']['action'] == 'domainRenew') {
                                $save = [
                                    'user_id' => session('_userInfo')['id'],
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                ];
                                $d->where('domainname = "' . $v['domainname'] . '"')->update($save);
                            } else {
                                $save = [
                                    'user_id' => session('_userInfo')['id'],
                                    'userid' => $userid,
                                    'domainname' => $v['domainname'],
                                    'years' => $v['years'],
                                    'domainpass' => $v['domainpass'],
                                    'isname' => 0,
                                    'dns' => json_encode(['dns1' => $v['dns1'], 'dns2' => $v['dns2']]),
                                    'status' => 0,
                                    'r_status' => 0,
                                    'newstas' => 0,
                                    'end_time' => strtotime(date("Y-m-d H:i:s", strtotime("+" . ((int) $v['years'] * 12) . " month"))),
                                ];
                                $d->save($save);
                            }
                        }
                    }
                    // return;

                    // dump($ret);
                    if ($isfailed) {
                        // dump(session('_userInfo')['balance']);
                        // dump($info['money']);
                        $userup = db('gee_user')->where('id = ' . session('_userInfo')['id'])->update(['balance' => ((double) session('_userInfo')['balance'] - (double) $info['money']) + (double) $info['money']]);
                        return json_encode($ret);
                    }
                  } else {
                    //vps 或 其他通用插件类购买
                    $plug = new $configs['_create_putData']['plug']();
                    $func = $configs['_create_putData']['class'];
                    $putData = $configs['_create_putData']['data'];
                    if (!$putData['function']) {
                        $putData['function'] = $configs['_create_putData']['function'];
                    }
                    $res = $plug->$func($putData);
                  }

                } else {
                  //租用物理服务器
                  $server = new GeeServer();
                  $selfPro = object_toArray($selfPro);
                  foreach($selfPro as $k=>$v){
                    if($v['pro_type'] == 'server') {
                      if($info['order_type'] == 'renew'){
                        $sinfo = $server->where('id',(int)$v['id'])->find();
                        $dt=date('Y-m-d H:i:s',$sinfo['end_time']);
                        $updata = [
                          'server_added' => $v['added'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime($dt."+".(int)$v['years']." month"))),
                        ];
                        if($sinfo['status'] == 1){
                          $updata['status'] = 3;
                        }
                        $server->where('id = '.$v['id'])->update($updata);
                      } else {
                        $sinfo = [
                          'pro_group_id'=> $v['group_id'],
                          'pro_id'=> $v['pro_id'],
                          'server_added'=> $v['added'],
                          'name'=> $v['hostname'],
                          'user_id'=> $v['user_id'],
                          'remake'=> $v['remake'],
                          'username'=> $v['username'],
                          'password'=> $v['password'],
                          'osgroup'=> (int)$v['osgroup'],
                          'ostype'=> (int)$v['ostype'],
                          'end_time'=> strtotime(date("Y-m-d H:i:s", strtotime("+".(int)$v['years']." month"))),
                          'status'=> '0'
                        ];
                        $server->save($sinfo);
                      }
                    }
                  }
                }
                
            }
            // $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 1]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 1]);
            echo "SUCCESS";
        } else {
            // $deal->where('num = ' . $post['out_trade_no'])->update(['status' => 3]);
            $o->where('number = ' . $post['out_trade_no'])->update(['status' => 2]);
            $b->where('order_number = ' . $post['out_trade_no'])->update(['status' => 0]);
            $this->redirect('console/Billing/order');
            // echo "SUCCESS";
        }
        //写在文本里看一下参数
        $data = json_encode($post);
        file_put_contents("alipaytext.txt", $data);
    }
}

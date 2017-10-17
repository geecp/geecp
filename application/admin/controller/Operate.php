<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/28
 * Time: 11:34
 */
namespace app\admin\controller;

use app\admin\model\Auth;
use app\admin\model\Domain_temp;
use app\admin\model\Finance_detailed;
use app\admin\model\Messagetemp;
use app\admin\model\Remarks;
use app\admin\model\Workorder;
use think\Db;
use think\Session;

class Operate extends Base
{
    public function index()
    {

        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        //找到当前方法在权限表中的对应数据
        $res = Db::name('right')->where('url', $url)->find()['id'];

        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];

        //找到与一级菜单同名的方法
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
            //控制工单分类
            Session('status', null);

            $workorder = new Workorder();

            if (input('status')) {
                $res = $workorder->where('status', input('status'))->paginate(10);

                Session::set('status', input('status'));
            } else {
                $res = $workorder->paginate(10);

            }

            foreach ($res as $key => $val) {
                $res[$key]['workid'] = Db::name("admmember")->where('id', $val['workid'])->find()['username'];
            }

            $where = ['6', '13'];
            //客服和技术支持员工列表
            $member = Db::name("admmember")->whereIn('adm_group', $where)->select();

            $this->assign('member', $member);
            $this->assign('res', $res);
            $this->assign('count', count($res));

            return view();
        }

    }

    //排除当前指派人员以后的可用人员名单
    public function saveWorkID()
    {
        $id = input('post.id');
        $username = input('post.username');
        //客服和技术
        $where = ['6', '13'];
        $newmember = Db::name("admmember")->whereIn('adm_group', $where)->select();
        foreach ($newmember as $key => $val) {
            $val['wid'] = $id;
            $newmember[$key] = $val;
            if ($val['username'] == $username) {
                unset($newmember[$key]);
            }

        }
        echo json_encode($newmember);
    }

    //委派人员的修改
    public function doSaveWork()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group = explode(',', $adm_group);
        $arr = explode(',', input('data'));
        if (!in_array($res, $adm_group)) {
            return json(['msg' => 2]);
        } else {
            $data = input('post.');
            $where['id'] = $data['changeId'];
            unset($data['changeId']);
            $data['workid'] = $data['username'];
            unset($data['username']);
            $res = Db::name('workorder')->where($where)->update($data);
            if ($res) {
                return json(['msg' => 1]);
            } else {
                return json(['msg' => 2]);
            }
        }
    }

    //工单详情
    public function workInfo()
    {
        //获取当前控制器和方法
        $controller = strtolower(substr(__CLASS__, '21'));
        $method = __FUNCTION__;
        $url = $controller . '/' . $method;
        $res = Db::name('right')->where('url', $url)->find()['id'];
        //拿到当前登录人的权限
        $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
        $adm_group = explode(',', $adm_group);
        $arr = explode(',', input('data'));
        if (!in_array($res, $adm_group)) {
            $this->error('您还没有获取操作权限');
        } else {
            if (input('id')) {

                //左侧菜单
                $name = Db::name('right')->where('id', 9)->field('munu')->find()['munu'];
                $this->assign('name', $name);
                Session::set('id', 9);
                $right = Db::name('right')->where('pid', 9)->order('num')->select();
                $this->assign('right', $right);
                Session::set('num', 2);

                $where['id'] = input('id');
                $res = Db::name("workorder")->where($where)->find();

                $res['workid'] = Db::name("admmember")->where('id', $res['workid'])->find()['username'];
                $workorderid = $res['workorderid'];
                //聊天内容
                $chatlist = Db::name("chatlist")->where('workorderid', $workorderid)->order('reply_time')->select();
                foreach ($chatlist as $key => $val) {
                    if ($val['status'] == 1) {
                        $val['writer'] = Db::name('userlist')->where('id', $val['userid'])->find()['username'];
                        $val['img'] = '';
                    } else {
                        $val['writer'] = Db::name('admmember')->where('id', $val['admuserid'])->find()['username'];
                        $val['img'] = Db::name('admmember')->where('id', $val['admuserid'])->find()['img'];
                    }
                    $chatlist[$key] = $val;
                }

                $this->assign('chatlist', $chatlist);
                $this->assign('res', $res);
                return view();
            } else {
                $this->error('非法操作');
            }

        }

    }

    //添加聊天内容
    public function addchat()
    {
        $where['id'] = input('post.wid');
        $res = Db::name("workorder")->where($where)->find();
        $data['userid'] = $res['coustom'];
        $data['admuserid'] = $res['workid'];
        $data['workorderid'] = $res['workorderid'];
        $data['status'] = 2;
        $data['reply_time'] = date('Y-m-d H:i:s', time());
        $data['content'] = input('post.content');

        //bos配置
        $result = Db::name('attachment')->find();


        if (!empty($_FILES['file']['tmp_name'])) {

            $BOS_TEST_CONFIG =
                array(
                    'credentials' => array(
                        'ak' => $result['ak'],
                        'sk' => $result['sk'],
                    ),
                    'endpoint' => $result['domain'],
                );

            if ($result['status'] == 2) {
                $files = request()->file('file');
                // 移动到框架应用根目录/uploads/ 目录下
                $info = $files->move(ROOT_PATH . '/public' . DS . 'uploads');
                if ($info) {
                    // 成功上传后 获取上传信息

                    $code = str_replace('\\', '/', $info->getSaveName());
                    $data['attac'] = "http://" . $_SERVER["HTTP_HOST"] . "/uploads/" . $code;

                    $res = Db::name('chatlist')->insert($data);

                }
            } else {
                //选择BOS上传，查询是否配置BOS
                $img = date('YmdHis', time()) . rand(100, 999) . '.jpg';
                $data['attac'] = bos($result['bucket'], $img, $_FILES['file']['tmp_name'], $BOS_TEST_CONFIG);
                $data['attac'] = $result['domain'] . '/' . $result['bucket'] . '/' . $img;

                $res = Db::name('chatlist')->insert($data);

            }
        } else {
            $res = Db::name('chatlist')->insert($data);
        }
        if ($result) {
            return json(['msg' => 1]);
        }
    }

    //工作平台
    public function userlist()
    {
        $userlist = [];
        if (input('userid')) {
            $userlist = Db::name("userlist")->where('userid', input('userid'))->find();
        }
        /*  if (input('post.userid')){
              $userlist=Db::name("userlist")->where('userid',input('post.userid'))->find();
              return json(['msg'=>1]);
          }*/

        $this->assign('userlist', $userlist);
        return view();
    }

    //工作平台( )
    public function platform()
    {
        if (!input('id')) {
            $this->error('非法操作');
        }
        //拿到客户信息
        $userid = input('id');
        $userInfo = Db::name("userlist")->where('id', $userid)->find();
        $this->assign('userInfo', $userInfo);
        //财务账单 1,已支付 2,未支付 3,已取消 4,已退款 5,收入 6,余额
        $finance1 = Db::name("finance_detailed")->where('status', '1')->where('userid', $userid)->sum('money');
        $finance2 = Db::name("finance_detailed")->where('status', '2')->where('userid', $userid)->sum('money');
        $finance3 = Db::name("finance_detailed")->where('status', '3')->where('userid', $userid)->sum('money');
        $finance4 = Db::name("finance_detailed")->where('transaction_type', '3')->where('status', '6')->where('userid', $userid)->sum('money');
        $finance5 = Db::name("finance_detailed")->where('transaction_type', '4')->where('status', '6')->where('userid', $userid)->sum('money');
        $finance1 = $this->is_kong($finance1);
        $finance2 = $this->is_kong($finance2);
        $finance3 = $this->is_kong($finance3);
        $finance4 = $this->is_kong($finance4);
        $finance5 = $this->is_kong($finance5);
        //消费
        $xf = Db::name('finance_detailed')->where('userid', $userid)->where('transaction_type', 2)->sum('money');
        //充值
        $cz = Db::name("finance_detailed")->where('userid', $userid)->where('transaction_type', 1)->sum('money');
        $this->assign('cz', $cz);
        //余额
        $ye = $cz - $xf;
        $this->assign('ye', $ye);
        $this->assign('finance1', $finance1);
        $this->assign('finance2', $finance2);
        $this->assign('finance3', $finance3);
        $this->assign('finance4', $finance4);
        $this->assign('finance5', $finance5);
        $this->assign('userid', $userid);
        return view();
    }

    //验证价格是否为空
    public function is_kong($data)
    {
        if ($data == '') {
            $data = 0;
        }
        return $data;
    }

    //域名模板审核
    public function domaincheck()
    {
        $where = [];
        if (input('testatus')) {
            $where['status'] = input('testatus');
            Session::set('testatus', input('testatus'));
        }
        /*  if (input('invoice_header')) {
              $where['invoice_header'] = array('like', input('invoice_header') . '%');;
              Session::set('invoice_header', input('invoice_header'));
          }*/
        $operate = new Domain_temp();
        $data = $operate->where($where)->paginate(10);
        foreach ($data as $val) {
            $val['userid'] = Db::name("userlist")->where('id', $val['userid'])->find()['username'];
        }
        $this->assign('data', $data);
        $this->assign('count', count($data));
        return view();
    }

    //修改发票状态
    public function savestatus()
    {
        /* //获取当前控制器和方法
         $controller = strtolower(substr(__CLASS__, '21'));
         $method = __FUNCTION__;
         $url = $controller . '/' . $method;
         $res = Db::name('right')->where('url', $url)->find()['id'];
         //拿到当前登录人的权限
         $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
         $adm_group = explode(',', $adm_group);
         $arr = explode(',', input('data'));
         if (!in_array($res, $adm_group)) {
             return json(['msg' => 2]);
         } else {*/
        $where = [];
        $data = [];
        $data['auditor'] = Session::get('admin')['id'];
        if (input('post.status') == 2) {
            $where['id'] = input('post.id');
            $data['status'] = 2;
        } elseif (input('post.status') == 3) {
            $where['id'] = input('post.id');
            $data['status'] = 3;
        }
        $res = Db::name("domain_temp")->where($where)->update($data);
        if ($res) {
            return json(['msg' => 1]);
        }
        /* }*/

    }

    //域名模板审核详情
    public function checkinfo()
    {
        /*   //获取当前控制器和方法
           $controller = strtolower(substr(__CLASS__, '21'));
           $method = __FUNCTION__;
           $url = $controller . '/' . $method;
           $res = Db::name('right')->where('url', $url)->find()['id'];
           //拿到当前登录人的权限
           $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];

           $adm_group = explode(',', $adm_group);

           if (!in_array($res, $adm_group)) {
               $this->error('您还没有获取操作权限');
           } else {*/
        if (input('id')) {
            $where = [];
            //左侧菜单
            $name = Db::name('right')->where('id', 9)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 9);
            $right = Session::get("right");
            $this->assign('right', $right);
            Session::set('num', 3);

            $where['id'] = input('id');
            $res = Db::name("domain_temp")->where($where)->find();
            $res['userid'] = Db::name("userlist")->where('id', $res['userid'])->find()['username'];
            $res['auditor'] = Db::name("admmember")->where("id", $res['auditor'])->find()['username'];
            $this->assign('res', $res);
        } else {
            $this->error('非法操作');
        }
        return view();
        /*  }*/
    }

    //客户资料
    public function userinfo()
    {
        if (!input('id')) {
            $this->error('非法操作');
        }
        $province = Db::name("city")->where('pid', 0)->select();
        $this->assign('province', $province);
        $userid = input('id');
        $this->assign('userid', $userid);
        return view();
    }

    //二级联动获取城市
    public function getCitysByParentId()
    {
        if (input('post.pid')) {
            $where['pid'] = input('post.pid');
            $res = Db::name("city")->where($where)->select();
            return json_encode($res);
        }
    }

    //产品服务
    public function product()
    {

    }

    //交易记录
    public function transaction()
    {
        $userid = input('id');
        $this->assign('userid', $userid);
        $transaction = new Finance_detailed();
        $data = $transaction->where('userid', $userid)->paginate(10);
        //总收入
        $arr = array(3, 4);
        $shouru = Db::name("finance_detailed")->where('userid', $userid)->whereIn('transaction_type', $arr)->sum('money');
        //总支出
        $arr1 = array(2, 7, 8);
        $zhichu = Db::name("finance_detailed")->where('userid', $userid)->whereIn('transaction_type', $arr1)->sum('money');
        //结余
        $jieyu = $shouru - $zhichu;
        $this->assign('zhichu', $zhichu);
        $this->assign('data', $data);
        $this->assign('shouru', $shouru);
        $this->assign('jieyu', $jieyu);
        return view();
    }

    //交易记录详情
    public function traninfo()
    {
        if (input('id')) {
            $where = [];
            //左侧菜单
            $name = Db::name('right')->where('id', 5)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 5);
            $right = Db::name('right')->where('pid', 5)->order('num')->select();
            $this->assign('right', $right);
            Session::set('num', 5);

            $where['id'] = input('id');
            $res = Db::name("finance_detailed")->where($where)->find();
            $res['auditor'] = Db::name("admmember")->where("id", $res['auditor'])->find()['username'];
            $userid = $res['userid'];
            $this->assign('userid', $userid);
            $res['userid'] = Db::name("userlist")->where("id", $res['userid'])->find()['username'];
            $this->assign('res', $res);
            return view();
        } else {
            $this->error('非法操作');
        }
    }

    //备注
    public function remarks()
    {
        $userid = input('id');
        $this->assign('userid', $userid);
        //备注
        $remarks = new Remarks();
        $data = $remarks->where('userid', $userid)->where('status', 1)->paginate(10);
        foreach ($data as $val) {
            $val['admid'] = Db::name("admmember")->where("id", $val['admid'])->find()['username'];
        }
        $this->assign('data', $data);
        $this->assign('count', count($data));
        return view();
    }

    //删除备注
    public function remarks_del()
    {
        if (input('post.id')) {
            //删除备注信息
            $id = input('post.id');
            $data['status'] = 2;
            $data['update_time'] = date('Y/m/d H:i:s', time());
            $res = Db::name('remarks')->where('id', $id)->update($data);
            if ($res) {
                return json(['msg' => 1]);
            }
        } else {
            return json(['msg' => 3]);
        }
    }

    //添加新的备注
    public function addremarks()
    {
        if (!input('id')) {
            $this->error('非法操作');
        } else {
            $userid = input('id');
            $this->assign('userid', $userid);
            return view();
        }
    }

    //执行添加
    public function remarksadd()
    {
        dump($_POST);
        exit;
    }

    //消息中心
    public function message()
    {
        Session::set('num', 4);
        Session('temp', null);
        $message = new Messagetemp();
        if (input('temp')) {
            $res = $message->where('temp', input('temp'))->paginate(10);
            Session::set('temp', input('temp'));
        } else {
            $res = $message->order('creat_time', 'desc')->paginate(10);

        }
        foreach ($res as $val) {
            $val['sendee'] = Db::name("userlist")->where('id', $val['sendee'])->find()['username'];
            $val['issuer'] = Db::name("admmember")->where('id', $val['issuer'])->find()['username'];
        }
        $this->assign('res', $res);
        $this->assign('count', count($res));
        return view();
    }

    //加载添加消息页面
    public function addmessage()
    {
        Session::set('num', 4);
        $userlist = Db::name("userlist")->select();
        $this->assign('userlist', $userlist);
        return view();
    }

    //执行添加
    public function messageadd()
    {
        $data = input('post.');
        //判断富文本编辑器是否有内容
        if (isset($data['editorValue'])) {
            $data['content'] = $data['editorValue'];
            unset($data['editorValue']);
        } else {
            return json(['msg' => 3]);
        }
        $data['creat_time'] = date('Y/m/d H:i:s', time());
        $data['is_published'] = 0;
        $data['is_read'] = 0;
        $data['issuer'] = Session::get('admin')['id'];
        $res = Db::name("messagetemp")->insert($data);
        if ($res) {
            return json(['msg' => 1]);
        }
    }

    //修改发票状态
    public function savemestatus()
    {
        /* //获取当前控制器和方法
         $controller = strtolower(substr(__CLASS__, '21'));
         $method = __FUNCTION__;
         $url = $controller . '/' . $method;
         $res = Db::name('right')->where('url', $url)->find()['id'];
         //拿到当前登录人的权限
         $adm_group = Db::name('admgroup')->where('id', Session::get('admin')['adm_group'])->find()['authority'];
         $adm_group = explode(',', $adm_group);
         $arr = explode(',', input('data'));
         if (!in_array($res, $adm_group)) {
             return json(['msg' => 2]);
         } else {*/
        $where['id'] = input('post.id');
        $data['is_published'] = 1;
        $data['issuer'] = Session::get('admin')['id'];
        $res = Db::name("messagetemp")->where($where)->update($data);
        if ($res) {
            return json(['msg' => 1]);
        }
    }

    public function messageinfo()
    {
        if (input('id')) {
            $where = [];
            //左侧菜单
            $name = Db::name('right')->where('id', 9)->field('munu')->find()['munu'];
            $this->assign('name', $name);
            Session::set('id', 9);
            $right = Session::get("right");
            $this->assign('right', $right);
            Session::set('num', 4);

            $where['id'] = input('id');
            $res = Db::name("messagetemp")->where($where)->find();
            $res['sendee'] = Db::name("userlist")->where('id', $res['sendee'])->find()['username'];
            $res['issuer'] = Db::name("admmember")->where('id', $res['issuer'])->find()['username'];
            $this->assign('res', $res);
        } else {
            $this->error('非法操作');
        }
        return view();
    }

    //实名认证
    public function authname()
    {
        Session::set('num', 5);
        Session('temp', null);
        //查询所有认证数据
        $res = Db::table('gee_auth')
            ->alias('a')
            ->join('gee_userlist u', 'a.userid=u.id')
            ->field('a.id,a.name,a.type,a.create_time,a.status,u.username,u.phone,u.username')
            ->paginate(15);
        $this->assign('res', $res);
        return view();
    }

    //实名认证状态
    public function saveauth()
    {
        $data = input('post.');
        $res = Auth::update($data);
        $id=$data['id'];
        $user=Db::name('auth')->where('id',$id)->field('userid')->find();
        if($data['status']== '3'){
            $da['idcardva']=1;
        }else{
            $da['idcardva']=0;
        }
        Db::name('userlist')->where('id',$user['userid'])->update($da);
        if ($res) {
            return json(['msg' => 1]);
        }
    }

    //实名认证详情
    public function authInfo()
    {
        Session::set('num', 5);
        Session('temp', null);
        $where['id']=input('id');
        //接收id查询数据
        $res=Db::name('auth')->where($where)->find();

        $this->assign('res',$res);
        return view();
    }
}
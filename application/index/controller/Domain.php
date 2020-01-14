<?php
namespace app\index\controller;

use app\admin\model\GeeAddons; // 前置操作
use app\admin\model\GeeDomainContact; //产品分类表
use app\admin\model\GeeDomainPrice; //产品分类表
use app\admin\model\GeeProduct; //产品组表
use app\admin\model\GeeProductClass; //产品表
use app\admin\model\GeeProductGroup; //插件表
use app\index\controller\Common; //域名表
use app\index\model\GeeBilling; //域名价格表
use app\index\model\GeeDomain; //产品购买配置表
use app\index\model\GeeProConfig; //订单表

class Domain extends Common
{
    public function index()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $dlist = $d->where('user_id = ' . session('_userInfo')['id'])->select();
        $plist = $dp->order('id')->limit(20)->select();
        $dcount = $d->where('user_id = ' . session('_userInfo')['id'])->count();
        $dvcount = $d->where('user_id = ' . session('_userInfo')['id'])->where('r_state = "run"')->count();
        $ddcount = $d->where('user_id = ' . session('_userInfo')['id'])->where('d_state = ""')->count();
        $decount = $d->where('user_id = ' . session('_userInfo')['id'])->where('end_time <= ' . (time() - 60 * 60 * 24 * 30))->count();
        // dump(time() - 60*60*24*30);

        $this->assign('dcount', $dcount);
        $this->assign('dvcount', $dvcount);
        $this->assign('ddcount', $ddcount);
        $this->assign('decount', $decount);
        $this->assign('plist', $plist);
        // dump($plist);
        return $this->fetch('Domain/index');
    }
    public function search()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        return $this->fetch('Domain/search');
    }
    /**
     * 搜索域名接口
     */
    public function searchdomain()
    {
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        // $plug= new \addons\domain\domain();
        $p = $_POST;
        if (empty($p['domain'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入您要查询的域名!';
            return json_encode($ret);
        }
        $dplist = $dp->order('id')->select();
        $list = [];
        foreach ($dplist as $k => $v) {
            // dump($v);
            $list[$k] = [
                'domain' => $p['domain'],
                'suffix' => $v['domain'],
                'description' => $v['description'],
                'tag' => $v['tag'],
                'status' => 'loading',
                'origin_price' => to_double($v['origin_price']),
                'price' => to_double($v['price']),
            ];
        }
        $ret['data'] = $list;
        return json_encode($ret);
    }
    /**
     * 域名信息查询
     */
    public function searchdomaininfo()
    {
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $plug = new \addons\domain\domain();
        $p = $_POST;
        if (empty($p['domain']) || empty($p['suffix'])) {
            $ret['status'] = '422';
            $ret['msg'] = '请传入正确的域名!';
            return json_encode($ret);
        }
        $dpinfo = $dp->where('domain = "' . $p['suffix'] . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'check',
            'data' => [
                'domain' => $p['domain'],
                'suffix' => $p['suffix'],
            ],
        ];
        $res = $plug->domain($putData);
        $ret['data'] = json_decode($res, true)['data'];
        return json_encode($ret);
    }
    /**
     * 域名管理
     */
    public function manage()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $list = $d->where('user_id = ' . session('_userInfo')['id'])->order('id')->paginate(20);
        foreach ($list as $k => $v) {
            $suffix = '.' . explode(".", $v['domainname'])[1];
            $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
            $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
            $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
            $putData = [
                'way' => $adninfo['name'],
                'pro_id' => $dpinfo['pro_id'],
                'function' => 'control',
                'action' => 'domainDetail',
                'data' => [
                    'domainname' => $v['domainname'],
                ],
            ];
            $adnres = $plug->domain($putData);
            $adnres = json_decode($adnres, true);
            // dump($adnres);
            $v['runstate'] = $adnres['data'][0]['runstate'];
            $v['dnvcstate'] = $adnres['data'][0]['dnvcstate'];
            $v['domaintype'] = $adnres['data'][0]['domaintype'];

            $d->where('id = ' . $v['id'])->update([
                'r_state' => $v['runstate'],
                'd_state' => $v['dnvcstate'],
                'domaintype' => $v['domaintype'],
                'domainpass' => $adnres['data'][0]['password'],
                'userid' => $adnres['data'][0]['userid'],
                'dns' => json_encode([
                    'dns1' => ['host' => $adnres['data'][0]['host1'], 'ip' => $adnres['data'][0]['hostip1']],
                    'dns2' => ['host' => $adnres['data'][0]['host2'], 'ip' => $adnres['data'][0]['hostip2']],
                    'dns3' => ['host' => $adnres['data'][0]['host3'], 'ip' => $adnres['data'][0]['hostip3']],
                    'dns4' => ['host' => $adnres['data'][0]['host4'], 'ip' => $adnres['data'][0]['hostip4']],
                    'dns5' => ['host' => $adnres['data'][0]['host5'], 'ip' => $adnres['data'][0]['hostip5']],
                    'dns6' => ['host' => $adnres['data'][0]['host6'], 'ip' => $adnres['data'][0]['hostip6']],
                ]),
                'end_time' => strtotime($adnres['data'][0]['ExpireTime']),
                'newstas' => $adnres['data'][0]['d_constt'],
                'isname' => $adnres['data'][0]['isNameDomain'],

            ]);
        }
        // domcer
        $this->assign('list', $list);
        return $this->fetch('Domain/manage');
    }
    /**
     * 生成域名证书
     */
    public function certification()
    {
        $data = $_GET;
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $info = $d->where('domainname = "' . $data['domain'] . '"')->find();
        if (!$info) {
            return $this->redirect('index/Domain/manage');
        }

        $suffix = '.' . explode(".", $data['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainDetail',
            'data' => [
                'domainname' => $data['domain'],
            ],
        ];
        $adnres = $plug->domain($putData);
        $adnres = json_decode($adnres, true)['data'][0];
        // dump($adnres);
        echo '<script>window.location.href="'.$adnres['domcer'].'"</script>';
    }
    /**
     * 域名续费
     */
    public function recharge()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $this->assign('plist', $plist);
        return $this->fetch('Domain/recharge');
    }
    /**
     * 域名详情
     */
    public function detail()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $data = $_GET;
        $info = $d->where('domainname = "' . $data['domain'] . '"')->find();
        if (!$info) {
            return $this->redirect('index/Domain/manage');
        }

        $suffix = '.' . explode(".", $data['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();

        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'contactList',
            'data' => [
                'userid' => $info['userid'],
            ],
        ];
        $adnres = $plug->domain($putData);
        $adnres = json_decode($adnres, true);

        $dputData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainDetail',
            'data' => [
                'domainname' => $data['domain'],
            ],
        ];
        $dinfo = $plug->domain($dputData);
        $dinfo = json_decode($dinfo, true);

        // dump($info);
        // dump($adnres);
        // dump($dinfo);
        $this->assign('info', $info);
        $this->assign('minfo', $adnres['data'][0]);
        $this->assign('dinfo', $dinfo['data'][0]);
        return $this->fetch('Domain/detail');
    }
    /**
     * 域名隐私保护
     */
    public function whoisProtect()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        $suffix = '.' . explode(".", $p['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainWhoisProtect',
            'data' => [
                'domainname' => $p['domain'],
                'newstas' => $p['newstas'],
            ],
        ];
        // dump($putData);
        $info = $plug->domain($putData);
        $info = json_decode($dinfo, true);
        // dump($info);
        $d->where('domainname = "' . $p['domain'] . '"')->update(['newstas' => $p['newstas']]);
        return json_encode($ret);
    }
    /**
     * 修改域名DNS
     */
    public function changens()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $data = $_GET;
        $info = $d->where('domainname = "' . $data['domain'] . '"')->find();
        if (!$info) {
            return $this->redirect('index/Domain/manage');
        }

        $suffix = '.' . explode(".", $data['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();

        $dputData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainDetail',
            'data' => [
                'domainname' => $data['domain'],
            ],
        ];
        $dinfo = $plug->domain($dputData);
        $dinfo = json_decode($dinfo, true);

        // dump($info);
        // dump($dinfo);
        $this->assign('info', $info);
        $this->assign('dinfo', $dinfo['data'][0]);
        return $this->fetch('Domain/changens');
    }
    /**
     * 修改域名DNS验证
     */
    public function changensAuth()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        $suffix = '.' . explode(".", $p['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $datas['domainname'] = $p['domain'];
        if ($p['type'] == 0) {
            //还原为默认DNS
            $ac = 'resetDNS';
        } else {
            //自定义设置
            $ac = 'modifyDNS';
            $datas['dns_hst1'] = $p['dns1'];
            $datas['dns_hst2'] = $p['dns2'];
            $datas['dns_hst3'] = $p['dns3'];
            $datas['dns_hst4'] = $p['dns4'];
            $datas['dns_hst5'] = $p['dns5'];
            $datas['dns_hst6'] = $p['dns6'];
        }

        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => $ac,
            'data' => $datas,
        ];
        // dump($putData);
        $info = $plug->domain($putData);
        $info = json_decode($dinfo, true);

        return json_encode($ret);
    }
    /**
     * 更变域名所有者
     */
    public function transform()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $dc = new GeeDomainContact();
        $plug = new \addons\domain\domain();
        $data = $_GET;

        $info = $d->where('domainname = "' . $data['domain'] . '"')->find();
        if (!$info) {
            return $this->redirect('index/Domain/manage');
        }

        $suffix = '.' . explode(".", $data['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'contactList',
            'data' => [
                'userid' => $info['userid'],
            ],
        ];
        $adnres = $plug->domain($putData);
        $adnres = json_decode($adnres, true);

        $dputData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainDetail',
            'data' => [
                'domainname' => $data['domain'],
            ],
        ];
        $dinfo = $plug->domain($dputData);
        $dinfo = json_decode($dinfo, true);

        $dclist = $dc->where('user_id = ' . session('_userInfo')['id'])->where('status = "1"')->where('ischecked = 2')->select();

        // dump($info);
        // dump($adnres);
        // dump($dinfo);
        $this->assign('info', $info);
        $this->assign('minfo', $adnres['data'][0]);
        $this->assign('dinfo', $dinfo['data'][0]);
        $this->assign('dclist', $dclist);
        return $this->fetch('Domain/transform');
    }
    /**
     * 更变域名所有者
     */
    public function transformAuth()
    {
        $d = new GeeDomain();
        $dc = new GeeDomainContact();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        $suffix = '.' . explode(".", $p['domainname'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();

        $dcinfo = $dc->where('id = ' . $p['userid'])->find();
        $dcids = json_decode($dcinfo['contact_id'], true);
        $p['userid'] = $dcids[$adninfo['name']]['value'];
        // dump($p);
        // return;
        $putData = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'domainguohu',
            'data' => [
                'domainname' => $p['domainname'],
                'userid' => $p['userid'],
            ],
        ];
        $adnres = $plug->domain($putData);
        $adnres = json_decode($adnres, true);

        return json_encode($ret);
    }
    /**
     * 前往控制面板
     */
    public function tomanager()
    {
        $data = $_GET;
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $suffix = '.' . explode(".", $data['domain'])[1];
        $dpinfo = $dp->where('domain = "' . $suffix . '"')->find();
        $proinfo = $pro->where('id = ' . $dpinfo['pro_id'])->find();
        $adninfo = $addons->where('id = ' . $proinfo['plug'])->find();
        $putDataurl = [
            'way' => $adninfo['name'],
            'pro_id' => $dpinfo['pro_id'],
            'function' => 'control',
            'action' => 'getLoginURL',
            'data' => [
                'domainname' => $data['domain'],
            ],
        ];
        $res = $plug->domain($putDataurl);
        $res = json_decode($res, true)['data'];
        echo '<script>window.location.href="' . $res . '"</script>';
    }
    public function price()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $plist = $dp->order('id')->paginate(20);
        $this->assign('plist', $plist);
        return $this->fetch('Domain/price');
    }
    public function model()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $dc = new GeeDomainContact();
        $list = $dc->where('user_id = ' . session('_userInfo')['id'])->paginate(10);

        //插件提交更新联系人信息
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        foreach ($list as $k => $v) {
            foreach (json_decode($v['contact_id'], true) as $key => $val) {
                $putData = [
                    'way' => $key,
                    'pro_id' => $val['pro_id'],
                    'function' => 'control',
                    'action' => 'contactList',
                    'data' => [
                        'userid' => $val['value'],
                    ],
                ];
                // dump($putData);
                $adnres = $plug->domain($putData);
                // dump(json_decode($adnres,true));
                $v['ischecked'] = json_decode($adnres, true)['data'][0]['ischecked'];
                $v['status'] = json_decode($adnres, true)['data'][0]['isforbidden'];
                $dc->where('id = ' . $v['id'])->update(['ischecked' => json_decode($adnres, true)['data'][0]['ischecked'], 'status' => json_decode($adnres, true)['data'][0]['isforbidden']]);
            }
        }

        $this->assign('list', $list);
        return $this->fetch('Domain/model');
    }
    /**
     * 提交信息模板
     */
    public function modelAuth()
    {
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $dc = new GeeDomainContact();

        if (empty($p['firstname_cn']) || empty($p['lastname_cn']) || empty($p['lastname_en']) || empty($p['firstname_en'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入管理联系人姓名！';
            return json_encode($ret);
        }
        //域名类型 企业
        if ($p['usertype'] == 'O') {
            if (empty($p['company_cn']) || empty($p['company_en'])) {
                $ret['status'] = 422;
                $ret['msg'] = '请输入域名所有者！';
                return json_encode($ret);
            }
        } else {
            $p['company_cn'] = $p['lastname_cn'] . $p['firstname_cn'];
            $p['company_en'] = $p['lastname_en'] . ' ' . $p['firstname_en'];
        }
        if (vali_data($p['email'], 'email')) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入正确的邮箱地址！';
            return json_encode($ret);
        }
        if (empty($p['country_cn']) || empty($p['state_cn']) || empty($p['city_cn']) || empty($p['country_en']) || empty($p['state_en']) || empty($p['city_en'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入所属地区！';
            return json_encode($ret);
        }
        if (empty($p['address_cn']) || empty($p['address_en'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入通讯地址！';
            return json_encode($ret);
        }
        if (empty($p['zipcode'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入邮政编码！';
            return json_encode($ret);
        }
        if (vali_data($p['phone'], 'phone')) {
            $ret['status'] = 422;
            $ret['msg'] = '请输入手机号码！';
            return json_encode($ret);
        }
        $p['user_id'] = session('_userInfo')['id'];
        $p['phone'] = '+86.' . $p['phone'];
        $p['fax'] = $p['phone'];
        $p['country_cn'] = '中国';
        $p['country_en'] = 'CN';
        //插件提交创建联系人信息
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        // dump($p);
        $prolist = $pro->where('type = 5')->select();
        $contact = [];
        foreach ($prolist as $k => $v) {
            $adninfo = $addons->where('id = ' . $v['plug'])->find();
            if ($p['id']) {
                $ac = 'modifyContact';
                // dump(!$p['domainname']);
                if (!$p['domainname']) {
                    $dcinfo = $dc->where('id = ' . $p['id'])->find();
                    // dump($dcinfo['contact_id']);
                    $p['userid'] = json_decode($dcinfo['contact_id'], true)[$adninfo['name']]['value'];
                }
            } else {
                $ac = 'createContact';
            }
            $putData = [
                'way' => $adninfo['name'],
                'pro_id' => $v['id'],
                'function' => 'control',
                'action' => $ac,
                'data' => $p,
            ];
            // dump($putData);
            $adnres = $plug->domain($putData);
            // dump($adnres);
            if (json_decode($adnres, true)['status'] == 'failed') {
                $ret['status'] = 422;
                $ret['msg'] = json_decode($adnres, true)['data'];
                return $ret;
                break;
            }
            $contact[$adninfo['name']] = [
                'pro_id' => $v['id'],
                'value' => json_decode($adnres, true)['data'],
            ];
            unset($p['userid']);
        }
        // return;
        // dump($contact);
        if (!$p['id']) {
            $p['contact_id'] = json_encode($contact);
        }
        //修改
        if ($p['id']) {
            $res = $dc->where('id = ' . $p['id'])->update($p);
        } else {
            $res = $dc->save($p);
        }
        if (!$res) {
            $ret['status'] = 422;
            $ret['msg'] = '网络错误!请稍后再试';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    /**
     * 删除联系人模板
     */
    public function modeldel()
    {
        $p = $_POST;
        $dc = new GeeDomainContact();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];

        if (empty($p['id'])) {
            $ret['status'] = 422;
            $ret['msg'] = '非法操作';
            return json_encode($ret);
        }
        $has = $dc->where('id = ' . $p['id'] . ' and user_id=' . session('_userInfo')['id'])->find();
        if (!$has) {
            $ret['status'] = 422;
            $ret['msg'] = '非法操作';
            return json_encode($ret);
        }

        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();

        $prolist = $pro->where('type = 5')->select();
        $contact = json_decode($has['contact_id'], true);
        foreach ($contact as $k => $v) {
            // dump($v);
            // return;
            $putData = [
                'way' => $k,
                'pro_id' => $v['pro_id'],
                'function' => 'control',
                'action' => 'deleteContact',
                'data' => ['userid' => $v['value']],
            ];
            // dump($putData);
            $adnres = $plug->domain($putData);

        }
        $res = $dc->where('id = ' . $p['id'] . ' and user_id=' . session('_userInfo')['id'])->delete();
        if (!$res) {
            $ret['status'] = 422;
            $ret['msg'] = '网络错误!请稍后再试';
            return json_encode($ret);
        }
        return json_encode($ret);
    }
    /**
     * 测试domain接口
     */
    public function checked()
    {
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $addons = new GeeAddons();
        $plug = new \addons\domain\domain();
        $way = $addons->where('`range` = "domain" and `status` = 2')->find();

        $putData = [
            'function' => 'control',
            'user_id' => session('_userInfo')['id'],
            'action' => 'checked',
            'data' => [
            ],
        ];
        $res = $plug->domain($putData);
        dump($res);
        return json_encode($ret);
    }
    public function add()
    {
        //域名联系人模板
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $dc = new GeeDomainContact();
        $dclist = $dc->where('user_id = ' . session('_userInfo')['id'])->where('status = "1"')->where('ischecked = 2')->select();
        $this->assign('dclist', $dclist);
        return $this->fetch('Domain/add');
    }
    /**
     * 创建域名
     */
    public function addAuth()
    {
        $d = new GeeDomain();
        $dp = new GeeDomainPrice();
        $dc = new GeeDomainContact();
        $dc = new GeeDomainContact();
        $pro = new GeeProduct();
        $addons = new GeeAddons();
        $pc = new GeeProConfig();
        $billing = new GeeBilling();
        $plug = new \addons\domain\domain();
        $p = $_POST;
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        $userinfo = session('_userInfo');
        if ($userinfo['realverify'] != 2) {
            $ret['status'] = 422;
            $ret['msg'] = '请先进行实名认证!';
            return json_encode($ret);
        }
        /** 域名创建接口所需参数
         * [
         *  'userid'=>'',  //联系人ID
         *  'domainname'=>'',  //域名
         *  'years'=> 1,  //年限
         *  'domainpass'=>'',  //域名密码
         *  'dns1'=> '',  //dns1
         *  'dns2'=>''
         * ]
         */
        $putData = [];
        $_proInfo = [];
        $dlist = json_decode($p['domainlist'], true);
        // dump($dlist);
        foreach ($dlist as $k => $v) {
            $domain = [
                'list' => json_encode([['domain' => $v['domain'], 'suffix' => $v['suffix'], 'years' => $v['years']]]),
            ];
            if ($p['type'] == 'recharge') {
                $putData[$k] = [
                    'domainname' => $v['domain'] . $v['suffix'], //域名
                    'years' => $v['years'], //年限
                    'exptme' => $v['exptme'], //域名到期时间
                ];
                $pinfo[$k] = [
                    'class' => '域名服务',
                    'config' => '域名:' . $v['domain'] . $v['suffix'],
                    'num' => 1,
                    'years' => $v['years'] * 12,
                    'price' => json_decode($this->getDomainListPrice($domain, 'recharge'), true)['data'],
                ];
            } else {
                $putData[$k] = [
                    'userid' => $p['contact_id'], //联系人ID
                    'domainname' => $v['domain'] . $v['suffix'], //域名
                    'years' => $v['years'], //年限
                    'domainpass' => $this->vali_name('number', rand_name(8), 8, 'rand_name'), //域名密码
                    'dns1' => $p['dns1'], //dns1
                    'dns2' => $p['dns2'],
                ];
                $pinfo[$k] = [
                    'class' => '域名服务',
                    'config' => '域名:' . $v['domain'] . $v['suffix'],
                    'num' => 1,
                    'years' => $v['years'] * 12,
                    'price' => json_decode($this->getDomainListPrice($domain), true)['data'],
                ];
            }
        }
        $_putData = [
            'plug' => '\addons\domain\domain',
            'class' => 'domain',
            'function' => 'control',
            'action' => $p['type'] == 'recharge' ? 'domainRenew' : 'createDom',
            'data' => $putData,
        ];
        // dump($_putData);
        // dump($pinfo);
        // return;
        $price = $p['type'] == 'recharge' ? json_decode($this->getDomainListPrice(['list' => $p['domainlist']], 'recharge'), true)['data'] : json_decode($this->getDomainListPrice(['list' => $p['domainlist']]), true)['data'];
        $_SESSION['_create_putData'] = $putData;
        $_SESSION['_pro_info'] = $pinfo;
        $number = $this->vali_name('number', rand_name(8), 8, 'rand_name');
        $order_number = date('Ymdhis', time()) . rand(10000, 99999);

        $pcConfig['order_number'] = $order_number;
        $pcConfig['config'] = json_encode([
            '_create_putData' => $_putData,
            '_pro_info' => $pinfo,
        ]);
        $pc->save($pcConfig);
        $billing_save = [
            'number' => $number,
            'order_number' => $order_number,
            'pro_list' => json_encode($_SESSION['_pro_info']),
            'user_id' => session('_userInfo')['id'],
            'type' => '0',
            'order_type' => $p['type'] == 'recharge' ? 'renew' : 'create',
            'money' => (double) str_replace(",", "", $price),
            'balance' => (double) session('_userInfo')['balance'] - (double) $_SESSION['_pro_info']['price'],
            'cash' => 0,
            'channel_type' => '0',
            'remarks' => '',
            'status' => '0',
            'order_status' => '2',
        ];
        // return;
        $_SESSION['_pro_order'] = $billing_save['order_number'];
        $billing->save($billing_save);
        // dump($putData);
        // dump($_proInfo);
        // dump($data);
        return json_encode($ret);
    }
    /**
     * 验证随机名称
     */
    public function vali_name($key, $val, $len, $func)
    {
        if (!is_int($val) && !is_bool($va)) {
            $w = '"' . $val . '"';
        }
        return $val;
    }
    /**
     * 计算域名清单价格
     */
    public function getDomainListPrice($dlist = [], $type = '')
    {
        $p = $dlist ? $dlist : $_POST;
        $p['type'] = $dlist ? $type : $_POST['type'];
        // dump($p);
        $dp = new GeeDomainPrice();
        $ret = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => '',
        ];
        if (empty($p['list'])) {
            $ret['status'] = 422;
            $ret['msg'] = '请提交域名清单!';
            return json_encode($ret);
        }
        $p['list'] = json_decode($p['list'], true);
        if (empty($p['list'][0])) {
            $ret['status'] = 422;
            $ret['msg'] = '请提交域名清单!';
            return json_encode($ret);
        }
        $total = 0;
        // dump($p['type']);
        if ($p['type'] && $p['type'] == 'recharge') {
            //计算续费价
            foreach ($p['list'] as $k => $v) {
                $dpinfo = $dp->where('domain = "' . $v['suffix'] . '"')->find();
                if (!$dpinfo) {
                    $ret['status'] = 422;
                    $ret['msg'] = '非法操作!';
                    return json_encode($ret);
                    break;
                }
                $total += $v['years'] * $dpinfo['recharge'];
            }
            // dump($total);
        } else {
            //计算创建价
            $type = ['twelvemonth', 'biennia', 'triennium', 'quadrennium', 'lustrum', 'decade'];
            foreach ($p['list'] as $k => $v) {
                $dpinfo = $dp->where('domain = "' . $v['suffix'] . '"')->find();
                if (!$dpinfo) {
                    $ret['status'] = 422;
                    $ret['msg'] = '非法操作!';
                    return json_encode($ret);
                    break;
                }
                if ($dpinfo[$type[$v['years'] - 1]] && $dpinfo[$type[$v['years'] - 1]] > 0) {
                    $total += $dpinfo[$type[$v['years'] - 1]];
                } else {
                    if ($v['years'] <= 1) {
                        $total += $dpinfo['price'];
                    } else {
                        $total += $dpinfo['price'] + (($v['years'] - 1) * $dpinfo['origin_price']);
                    }
                }
            }
        }
        $ret['data'] = to_double($total);
        return json_encode($ret);
    }
}

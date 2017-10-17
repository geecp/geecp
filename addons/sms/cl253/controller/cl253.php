<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/10
 * Time: 20:34
 */
use think\Controller;
use think\Db;
class cl253 extends Controller
{
    public static function cl253_sendSMS($data)
    {
        $uid = $data['userid'];
        $sk=$data['key'];
        $ak=$data['keyid'];
        $appid=$data['appid'];
        $phone=$data['phone'];
        $tempid=$data['tempid'];
        $code=(int)$data['code'];


        //此正则只是针对于6位验证码的，后续根据实际开发情况
//        $zhengze = '/^[0-9]{6}$/';
//        preg_match($zhengze,$code,$result);
//        if (!$result) {
//            //
//            echo '8';die;
//        }

        $zhengze = '/^(1[3-9][0-9])\d{8}$/';
        preg_match($zhengze,$phone,$result);
        if (!$result) {
            //手机号格式错误
            echo '6';die;
        }
        $res = Db::name('userlist')->where("userid", $uid)->find();
        if (!$res) {
            //未查询到此用户
            echo '3';die;
        }
        $userid = $res['id'];
        $res = Db::name('sms_count')->where("userid",$userid)->find();
        $smscount = $res['smscount'];
        if ($smscount < 1) {
            //短信数量不足
            echo '10';die;
        }

        $res = Db::name('sms_appname')->where("appid",$appid)->find();
        if (!$res) {
            //无此app
            echo '4';die;
        }
        /*//每天最大发送量
        $num = $res['num'];
        //当天发送数量
        $dangnum = $res['dangnum'];
        //最后一条发送的时间
        $timenum = $res['timenum'];
        if ($num != 0) {
            if ($num <= $dangnum) {
                //发送条数超出每天最大发送量
                echo '11';die;
            }
            if ($timenum == 0) {
                $where['dangnum'] = 1;
                $where['timenum'] = time();
                $ress = Db::name('sms_app')->where("appid",$appid)->update($where);
            }else{
                $jintian = date('Y-m-d',time());
                $zuihou = date('Y-m-d',(int)$timenum);
                if ($jintian == $zuihou) {
                    $where['dangnum'] = $dangnum+1;
                    $where['timenum'] = time();
                    $ress = Db::name('sms_app')->where("appid",$appid)->update($where);
                }else{
                    $where['dangnum'] = 1;
                    $where['timenum'] = time();
                    $ress = Db::name('sms_app')->where("appid",$appid)->update($where);
                }
            }
        }*/
        //签名
        $smsname = $res['smsname'];

        if ($res['status'] != 1) {
            //当前应用未启用
            echo '7';die;
        }

        $res = Db::name('sms_template')->where("tempid",$tempid)->find();
        if (!$res) {
            //无短信模板
            echo '5';die;
        }
        $res['content'] = str_replace('${time}',date("Y-m-d h:i:s",time()),$res['content']);
        $content = '【'.$smsname.'】'. str_replace('${code}',$code,$res['content']);

        if ($res['status'] != 1) {
            //当前模板未启用
            echo '9';die;
        }
        $j=[
            'userid'=>$userid ,
            'keyid'=>$ak,
            'key'=>$sk
        ];
        $res = Db::name('access')->where($j)->find();
        if (!$res) {
            //未通过ak，sk认证
            echo '2';die;
        }else{
            // echo '1:成功';
            require_once 'Smsapi.php';
            $clapi  = new Smsapi();
            $result = $clapi->sendSMS($phone, $content,'true');
            $result = $clapi->execResult($result);

            if($result[1]==0){
                $arr['smscount'] = $smscount-1;
                Db::name('sms_count')->where("userid",$userid)->update($arr);
                $arrs['creat_time'] = date("Y-m-d H:i:s",time());
                $arrs['userid'] = $userid;
                $arrs['phone'] = $phone;
                $arrs['appid'] = $appid;
                $arrs['smsname'] = $smsname;
                $arrs['tempid'] = $tempid;
                $arrs['content'] = $content;
                $arrs['status'] = 1;
                Db::name('pushlist')->insert($arrs);
                echo 1;die;
            }else{
                $arrs['creat_time'] = date("Y-m-d H:i:s",time());
                $arrs['userid'] = $userid;
                $arrs['phone'] = $phone;
                $arrs['appid'] = $appid;
                $arrs['smsname'] = $smsname;
                $arrs['tempid'] = $tempid;
                $arrs['content'] = $content;
                $arrs['status'] = 0;
                Db::name('pushlist')->insert($arrs);
                echo 0;die;
            }

        }
    }
}
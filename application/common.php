<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Config;
use think\Db;
use think\Url;
use dir\Dir;
use think\Route;
use think\Loader;
use think\Request;
use cmf\lib\Storage;

if(function_exists('saeAutoLoader')){// 自动识别SAE环境
    defined('APP_MODE')     or define('APP_MODE',      'sae');
    defined('STORAGE_TYPE') or define('STORAGE_TYPE',  'Sae');
}else{
    defined('APP_MODE')     or define('APP_MODE',       'common'); // 应用模式 默认为普通模式
    defined('STORAGE_TYPE') or define('STORAGE_TYPE',   'File'); // 存储类型 默认为File
}

define('IS_WRITE',APP_MODE !== 'sae');

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env(){
    $items = array(
        'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
        'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk'    => array('磁盘空间', '5M', '不限制', '未知', 'success'),
    );

    //PHP环境检测
    if($items['php'][3] < $items['php'][1]){
        $items['php'][4] = 'error';
        session('error', true);
    }

    //附件上传检测
    if(@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if(empty($tmp['GD Version'])){
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    //磁盘空间检测
    if(function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(INSTALL_APP_PATH) / (1024*1024)).'M';
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile(){
    $items = array(
        array('dir',  '可写', 'success', './public/uploads'),
        array('dir',  '可写', 'success', './runtime'),
        array('dir',  '可写', 'success', './application'),

    );

    foreach ($items as &$val) {
        $item =	INSTALL_APP_PATH . $val[3];
        if('dir' == $val[0]){
            if(is_writable($item)) {
                if(is_dir($items)) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        } else {
            if(file_exists($item)) {
                if(is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if(!is_writable(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
        clearstatcache();
    }

    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){
    $items = array(
        array('pdo','支持','success','类'),
        array('pdo_mysql','支持','success','模块'),
        array('file_get_contents', '支持', 'success','函数'),
        array('mb_strlen',		   '支持', 'success','函数'),
    );

    foreach ($items as &$val) {
        if(('类'==$val[3] && !class_exists($val[0]))
            || ('模块'==$val[3] && !extension_loaded($val[0]))
            || ('函数'==$val[3] && !function_exists($val[0]))
        ){
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db, $prefix = ''){
    //读取SQL文件
    $sql = file_get_contents(APP_PATH . 'install/install.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $orginal = config('ORIGINAL_TABLE_PREFIX');
    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
    //开始安装
    show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if(empty($value)) continue;
        if(substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg  = "创建数据表{$name}";
            if(false !== $db->execute($value)){
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            $db->execute($value);
        }

    }
}

function show_msg($msg, $class = ''){
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}

function register_administrator($db, $prefix, $admin){
    show_msg('开始注册创始人帐号...');
    $sql = "INSERT INTO `[PREFIX]admmember` VALUES " .
        "('1','10000',1, '[NAME]', '[PASS]', '','',1, '',1, '10000','[creat_time]','')";

    $password =md5(md5($admin['adminpwd'].'qiduo'));
    $sql = str_replace(
        array('[PREFIX]','[NAME]', '[PASS]' ,'[creat_time]'),
        array($prefix,$admin['adminuser'] , $password,date('Y-m-d H:i:s',time())),
        $sql);
    //执行sql
    $db->execute($sql);
    show_msg('创始人帐号注册完成！');
    $sql = "INSERT INTO `[PREFIX]admgroup` (`id`, `author`, `authority`, `status`, `creat_time`, `updat_time`) VALUES (1, '超级管理员', '9,52,59,60,77,107,108,109,110,1050,1,111,112,2,69,113,114,70,71,72,73,3,22,31,34,35,36,23,97,98,99,100,24,25,26,96,101,102,103,104,1046,1051,1049,8,27,37,38,39,29,41,40,42,4,16,43,44,45,46,5,63,88,64,74,75,65,89,90,66,91,92,67,76,68,1047,10,78,82,83,84,85,86,87,93,94,95,79,80,81,6,105,106,7,18,47,19,48,20,49,21,50', '1', '','')";
    $sql=str_replace(
        array('[PREFIX]'),
        array($prefix),
        $sql);
    $db->execute($sql);
    show_msg('基础权限加载完成！');
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents(APP_PATH . 'database.php');
        //替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }
        //写入应用配置文件
        if(!IS_WRITE){
            return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>'.realpath(APP_PATH).'/database.php</p>
            <textarea name="" style="width:650px;height:185px">'.$conf.'</textarea>';
        }else{
            if(file_put_contents(APP_PATH . 'database.php', $conf)){
                show_msg('数据库配置写入成功');
            } else {
                show_msg('数据库配置写入失败！', 'error');
                session('error', true);
            }
            return '';
        }



    }
}

//生成template下的config.php
function build_config()
{
//读取配置内容
    $conf = file_get_contents(APP_PATH . 'install/tmp_config.tpl');
//写入应用配置文件
    if(!IS_WRITE){
        return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>'.realpath(APP_PATH).'/template/config.php</p>
            <textarea name="" style="width:650px;height:185px">'.$conf.'</textarea>
            ';
    }else{
        if(file_put_contents(APP_PATH . 'template/config.php', $conf)){
            show_msg('配置文件写入成功');
        } else {
            show_msg('配置文件写入失败！', 'error');
            session('error', true);
        }
        return '';
    }
}


// 应用公共文件
function getTree($data, $pid = 0, $count = 0)
{
    //因为函数再每次调用时都会将之前的数据清空,所以要声明一个静态变量
    static $res = [];
    //对原数组进行遍历,一次去除每一个分类的记录
    foreach ($data as $v) {
        //保存一个计数
        if ($v['pid'] == $pid) {
            $v['count'] = $count;
            //将汉字类信息保存在新的数组里面
            $res[] = $v;
            //在继续执行,需要传递处理分类的数组,遍历的时候记录ID
            getTree($data,$v['id'],$count + 1);
        }
    }
    return $res;
}



function getFree($data, $fid = 0, $count = 0)
{
    //因为函数再每次调用时都会将之前的数据清空,所以要声明一个静态变量
    static $res = [];
    //对原数组进行遍历,一次去除每一个分类的记录
    foreach ($data as $v) {
        //保存一个计数
        if ($v['fid'] == $fid) {
            $v['count'] = $count;
            //将汉字类信息保存在新的数组里面
            $res[] = $v;
            //在继续执行,需要传递处理分类的数组,遍历的时候记录ID
            getFree($data,$v['id'],$count + 1);
        }
    }
    return $res;
}

function list_to_tree($list,$pk = 'id',$pid='pid',$child = 'child',$root = 0)
{
    //创建Tree
    $tree = [];
    if (is_array($list)) {
        //创建基于逐渐的数组引用
        $refer = [];
        foreach ($list as $k =>$v) {
            $refer[$v[$pk]] =& $list[$k];
        }
        foreach ($list as $k =>$v) {
            //判断是否存在parent
            $parentId = $v[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$k];
            } else {
                //如果存在父父类
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent['$child'][] = $list[$k];
                }
            }
        }
    }
    return $tree;
}

//百度BOS上传
/*  endpoint ===>domain
 *  $bucket  ===>bocket
 **/
function bos($bucket,$objectKey, $fileName,$BOS_TEST_CONFIG)
{
    require_once VENDOR_PATH . 'bos/BosClient.php';
    $bos = new \BaiduBos();



    /*$BOS_TEST_CONFIG =
        array(
            'credentials' => array(
                'ak' => '',
                'sk' => '',
            ),
            'endpoint' => '',
        );*/

    $bucketName = $bucket;//存放地址

    $bos->upload($bucketName, $objectKey, $fileName,$BOS_TEST_CONFIG);

    $download = $bos->download($bucketName, $objectKey,$BOS_TEST_CONFIG);

    return $download;

}

//百度BOS删除
function bos_del($bucket,$objectKey,$BOS_TEST_CONFIG)
{
    require_once VENDOR_PATH . 'bos/BosClient.php';
    $bos = new \BaiduBos();

    $bucketName = $bucket;//存放地址

    $objectKey = explode('qm.qiduo.net/', $objectKey);//处理返回的数据
    $objectKey = $objectKey[1];

    try {
        $bos->delete($bucketName, $objectKey);
        return 1;
    } catch (\Exception $e) {
        return 2;
    }
}
//将二维数组转换成一维数组
function array_mult($array) {
    static $result_array=array();
    foreach($array as $value) {
        if(is_array($value)) {
            array_mult($value);
        } else {
            $result_array[]=$value;
        }
    }
    return $result_array;
}




function get_addons_name($name)
{
    $class="addons\\$name\\$name";
    return $class;
}

function get_config_path($name)
{
    $class="addons".DS."$name";
    return $class;
}

function scanFile($path) {
    $result = '';
    $files = scandir($path);
    $k=count($files);
    unset($files[0]);
    unset($files[1]);
    for ($i=2;$i<$k;$i++){
            if(strpos($files[$i],'.php')){
                unset($files[$i]);
            }else{
                //读取$files目录下的文件
                $url=$path.$files[$i].DS.'config.php';
                $data[$files[$i]]=require $url;
            }

    }
    return $data;
}

function xmlToArray($xml)
{
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

function object2array($object) {
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    }
    else {
        $array = $object;
    }
    return $array;
}

function getSeparate()
{
    return  (strstr(PHP_OS, 'WIN'))?"\\":'/';
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return string
 */
function get_client_ip($type = 0, $adv = false)
{
    return request()->ip($type, $adv);
}

/**
 * 去掉数组中的空
 * @param unknown $v
 * @return boolean
 */
function delEmpty($v){
    if ($v===""||$v===null){
        return false;
    }
    return true;
}


/**
 * GET 请求
 * @param string $url
 * @param array $param ['key'=>'','time'=>600,'resjson'=>1]
 * 如果不缓存，不需要传入key
 */
function httpGet($url, $param = [])
{

    if(!isset($param['resjson']) || empty($param['resjson'])) $param['resjson'] = 0;
    // 判断是否需要缓存文件，缓存是否存在
    if(isset($param['key']) && !empty($param['key'])){
        $key = md5($param['key']);
        if(!isset($param['time']) || empty($param['time'])){
            $time = 600;
        }else{
            $time = $param['time'] + 0 > 0 ? $param['time'] : 600;
        }

        $cache = cache($key);
        if(!empty($cache)){
            if($param['resjson'] == 1){
                return json_encode($cache);
            }
            return $cache;
        }
    }

    $ch = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, config('curl_http_timeout'));
    $response = curl_exec($ch);

    $info = curl_getinfo($ch);
    curl_close($ch);
    if(intval($info["http_code"])==200){
        //当获取的数据为空的时候，不缓存
        $result = json_decode($response,true);
        if(!empty($result) && ($result['code'] == 0 || ($result['code'] == '00') && !empty($result['data']['content']) ) ){
            if(!empty($key)){
                cache($key,$response,$time);
            }
        }
        // 是否返回json格式数据
        if($param['resjson'] == 1){
            return $response;
        }else{
            return $result;
        }

    }else{
        return false;
    }
}


/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @param array $param {"media":'@Path\filename.jpg'} 上传文件时
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function httpPost($url,$param,$post_file=false)
{
    $ch = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach($param as $key=>$val){
            $aPOST[] = $key."=".urlencode($val);
        }
        $strPOST =  join("&", $aPOST);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, config('curl_http_timeout'));
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$strPOST);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if(intval($info["http_code"])==200){
        $result = json_decode($response,true);
        return $result;
    }else{
        return false;
    }
}

/**
 * [getFile 获取远程而年间]
 * @return [type] [description]
 */
function qd_getFile($url) {
    $token = '';
    $pars = array();
    $pars['host'] = $_SERVER['HTTP_HOST'];
    $pars['version'] = '1.0';
    $pars['type'] = 'install';
    $pars['method'] = 'application.install';
    // $url = 'http://cp.aniu.tv/qdapi/v1/downloadPlugins?plugins=bce';
    $urlset = parse_url($url);
    // var_dump(  $urlset);die;
    $cloudip = gethostbyname($urlset['host']);
    $headers[] = "Host: {$urlset['host']}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlset['scheme'] . '://' . $cloudip . $urlset['path']);
    curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($pars, '', '&'));
    curl_setopt($ch, CURLOPT_POSTFIELDS,$urlset['query']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $content = curl_exec($ch);
    curl_close($ch);
    if (empty($content)) {
        return die( '获取安装信息失败，可能是由于网络不稳定，请重试。');
    }

    return $content;
}


function qd_unzip($file,$path,$cover=false)
{
    $tmpfile = ROOT_PATH . 'qdsource.tmp';

    file_put_contents($tmpfile, $file);

    $zip = new ZipArchive;
    $res = $zip->open($tmpfile);

    unlink($tmpfile);

    if ($res === TRUE) {
        $zip->extractTo($path);
        $zip->close();
        return true;
    } else {
        return false;
        die('<script type="text/javascript">alert("安装失败，请确认当前目录是否有写入权限！'.$res.'");history.back();</script>');
    }
}


//姓名分割
function splitName($fullname){
    $hyphenated = array('欧阳','太史','端木','上官','司马','东方','独孤','南宫','万俟','闻人','夏侯','诸葛','尉迟','公羊','赫连','澹台','皇甫',
        '宗政','濮阳','公冶','太叔','申屠','公孙','慕容','仲孙','钟离','长孙','宇文','城池','司徒','鲜于','司空','汝嫣','闾丘','子车','亓官',
        '司寇','巫马','公西','颛孙','壤驷','公良','漆雕','乐正','宰父','谷梁','拓跋','夹谷','轩辕','令狐','段干','百里','呼延','东郭','南门',
        '羊舌','微生','公户','公玉','公仪','梁丘','公仲','公上','公门','公山','公坚','左丘','公伯','西门','公祖','第五','公乘','贯丘','公皙',
        '南荣','东里','东宫','仲长','子书','子桑','即墨','达奚','褚师');
    $vLength = mb_strlen($fullname, 'utf-8');
    $lastname = '';
    $firstname = '';//前为姓,后为名
    if($vLength > 2){
        $preTwoWords = mb_substr($fullname, 0, 2, 'utf-8');//取命名的前两个字,看是否在复姓库中
        if(in_array($preTwoWords, $hyphenated)){
            $lastname = $preTwoWords;
            $firstname = mb_substr($fullname, 2, 10, 'utf-8');
        }else{
            $lastname = mb_substr($fullname, 0, 1, 'utf-8');
            $firstname = mb_substr($fullname, 1, 10, 'utf-8');
        }
    }else if($vLength == 2){//全名只有两个字时,以前一个为姓,后一下为名
        $lastname = mb_substr($fullname ,0, 1, 'utf-8');
        $firstname = mb_substr($fullname, 1, 10, 'utf-8');
    }else{
        $lastname = $fullname;
    }
    return array($lastname, $firstname);
}

function getRandomString($len, $chars=null)
{
    if (is_null($chars)){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000*(double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

//对称加密加密
function encode($string) {
    $chars = '';
    $len = strlen($string = iconv('utf-8', 'gbk', $string));
    for ($i = 0; $i < $len; $i++) {
        $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
    }
    return strtoupper($chars);
}


//对称加密解密
function decode($string) {
    $chars = '';
    foreach (str_split($string, 2) as $char) {
        $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('gbk', 'utf-8', $chars);
}

//
function getLastTimeArea($year,$month,$legth,$page=1)
{
    if (!$page) {
        $page = 1;
    }
    $monthNum = $month + $legth - $page*$legth;
    $num = 1;
    if ($monthNum < -12) {
        $num = ceil($monthNum/(-12));
    }
    $timeAreaList = [];
    for($i=0;$i<$legth;$i++) {
        $temMonth = $monthNum - $i;
        $temYear = $year;
        if ($temMonth <= 0) {
            $temYear = $year - $num;
            $temMonth = $temMonth + 12*$num;
            if ($temMonth <= 0) {
                $temMonth += 12;
                $temYear -= 1;
            }
        }
        $startMonth = strtotime($temYear.'-'.$temMonth.'-01');//该月的月初时间戳
        $endMonth = strtotime($temYear.'-'.($temMonth + 1).'/01') - 86400;//该月的月末时间戳
        $res = [$temYear.'-'.$temMonth.'-01',date('Y-m-d',$endMonth)]; //该月的月初格式化时间
//        $res = date('Y-m-d',$endMonth);//该月的月末格式化时间
//        $res['timeArea'] = implode(',',[$startMonth, $endMonth]);//区间时间戳
        $timeAreaList[] = $res;
    }
    return $timeAreaList;
}

//获取上个月的第一天和最后一天的日期
function getthemonth($date)
{
    $firstday = date('Y-m-01', strtotime($date));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return array($firstday,$lastday);
}

//邮件发送
function send_mail($data)
{
    require VENDOR_PATH . 'phpmail/src/PHPMailer.php';
    $mail=new \PHPMailer\PHPMailer\PHPMailer();
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->SMTPAuth=true;
    $mail->Host = config('email_config.host');
    $mail->Port = config('email_config.port');
    $mail->Username =config('email_config.username');
    $mail->Password = config('email_config.psw');
    $mail->From = config('email_config.From');
    $mail->FromName = config('email_config.FromName');
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    if(is_array($data['toemail'])){
        foreach($data['toemail'] as $to_email){
            $mail->AddAddress($to_email);
        }
    }else{
        $mail->AddAddress($data['toemail']);
    }
    $mail->Subject = $data['subject'];
    $mail->Body = $data['body'];
    $status = $mail->send();

    //简单的判断与提示信息
    if($status) {
        return true;
    }else{
        return false;
    }
}

function mb_text($data)
{
    $data=mb_substr($data,0,45,'UTF-8');
    return $data;
}

function returnJson($data){
    $result=[];
    $result['success']='success';
    $result['data']=$data;
    return $result;
}

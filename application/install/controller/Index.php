<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 14:01
 */
namespace app\install\controller;
use think\Controller;
use think\Session;
use think\Db;
class Index extends Controller
{
    protected function _initialize()
    {
        if(is_file(APP_PATH . 'template/config.php')){
            $this->error('已经成功安装了GeeSystem，请不要重复安装!','template/index/index');
        }
    }

    public function index()
    {
        return view('index/index');
    }

    //检测安装所需的运行环境
    public function install_step1()
    {
        Session::set('error',false);
        //环境检测
        $env=check_env();
        //文件检测
        $file=check_dirfile();
        //函数检测
        $function=check_func();

        $this->assign('env',$env);
        $this->assign('file',$file);
        $this->assign('func',$function);
        return view('index/install-step1');
    }

    //验证站点ID
    public function verifyWebId()
    {
        //将数据发送到开发者中心
        $id=input('post.webid/s','','htmlspecialchars');
        $website=$_SERVER['SERVER_NAME'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.geecp.com/index.php?s=Api/WebApi/webid');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        $post_data = array(
            "key" => $id,
            "website" => 'https://www.jd.com'
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($curl);
        curl_close($curl);
        $data=json_decode($data,true);
        if($data['success']!=1){
            echo 0;
        }else{
            echo 1;
            /*header('location:install.php?s=/index/install_step3.html');*/
        }
    }

    public function install_step2()
    {
        return view('install-step2');
    }

    public function install_step3()
    {
        return view('install-step3');
    }

    public function install()
    {
        //接收数据
        $data=input('post.');
        if($data['adminuser']==''||$data['adminpwd']==''||$data['readminpwd']==''||$data['email']==''){
            $this->error('请填写完整管理员信息!','install_step3');
        }elseif ($data['adminpwd']!==$data['readminpwd']){
            $this->error('两次密码不一致!','install_step3');
        }else{
            $info=[
                'adminuser'=>$data['adminuser'],
                'adminpwd'=>$data['adminpwd'],
                'readminpwd'=>$data['readminpwd'],
                'email'=>$data['email']
            ];
            Session::set('admin',$info);
        }
        if($data['dbaddress']==''||$data['dbname']==''||$data['dbuser']==''||$data['dbpwd']==''||$data['dbpre']==''){
            $this->error('请填写完整的数据库信息!','install_step3');
        }else{
            $base=[
                'type'=>'mysql',
                'hostname'=>$data['dbaddress'],
                'database'=>$data['dbname'],
                'username'=>$data['dbuser'],
                'password'=>$data['dbpwd'],
                'dbpre'=>$data['dbpre'],
            ];
            Session::set('db',$base);
            //创建数据库
            $dbname=$data['dbname'];
            unset($base['database']);
            $db = Db::connect($base);
            $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8";
            $db->execute($sql) || $this->error($db->getError());
        }

        $this->redirect('install_step4');
    }

    public function install_step4()
    {
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];

        $html=<<<html
        {extend name="index/base"}
        {block name="content"}
<div class="install-step step4 mt15">
			<div class="thumbnail">
				<div class="header-box" style="background-image: url('__ROOT__/static/install/img/bj2.png');">
					<div class="amid text-center">
						<div class="media">
							<div class="media-left media-middle">
								<span>4.</span>
							</div>
							<div class="media-body media-middle">
								<h2>安装数据库</h2>
								<p>正在执行数据库安装</p>
							</div>
						</div>
					</div>
					<ul class="nav nav-pills nav-justified step step-round-wh unhover">
						<li class="active">
							<a>检查环境</a>
						</li>
						<li class="active">
							<a>设置运行环境</a>
						</li>
						<li class="active">
							<a>创建数据库</a>
						</li>
						<li class="active">
							<a>安装</a>
						</li>
					</ul>
				</div>
				<div class="caption body-box">
					<div id="show-list" class="main text-left">
					</div>
					<script type="text/javascript">
						var list   = document.getElementById('show-list');
						function showmsg(msg, classname){
							var li = document.createElement('p');
							li.innerHTML = msg;
							classname && li.setAttribute('class', classname);
							list.appendChild(li);
							document.scrollTop += 30;
						}
					</script>
					<div class="mt45">
						<a href="$url/index.php/template/index/index" class="btn btn-ces">登录前台</a>
						<a href="$url/index.php/admin/index/index" class="btn btn-ces">登录后台</a>
					</div>
				</div>
				<div class="copyright">
					Copyright&copy; 2016-2017 Geesystem
				</div>
			</div>
		</div>
{/block}
html;


        echo $this->display($html);
        $db_config=Session::get('db');
        $db=Db::connect($db_config);
        create_tables($db,$db_config['dbpre']);

        //写入后台管理员
        $admin=Session::get('admin');
        register_administrator($db, $db_config['dbpre'], $admin);
        //写入database.php文件
        $conf=write_config($db_config);
        //生成template下的config.php
        build_config();

    }

}

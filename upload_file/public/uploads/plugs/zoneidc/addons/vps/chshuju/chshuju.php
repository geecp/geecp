<?php
namespace addons\vps\chshuju;
use think\Addons;
use \think\Db;
use \think\Route;
use app\admin\model\GeeRoute; // 后台路由表
use app\index\model\GeeWebroute; // 前台路由表
use app\index\model\GeeVps; // 前台路由表
use app\admin\model\GeeAddons; // 插件表
use ZipArchive; // PHP自带zip解析
class chshuju extends Addons
{
    public  $ret = [
          'status'=>200,
          'msg'=>'操作成功',
          'data'=>''
        ];
    //安装
    public function install()
    {
        // TODO: Implement install() method.
        $ret['status'] = 200;
        $ret['msg'] = '操作成功';
      

        //创建关联数据表
        // 创建连接
        $dbinfo = config("database");
        $conn = mysqli_connect($dbinfo['hostname'], $dbinfo['username'], $dbinfo['password'], $dbinfo['database']);
        // 检测连接
        if (!$conn) {
            die("连接失败: " . mysqli_connect_error());
        }
        if(!Db::query('show tables like "gee_vps_chshuju"')){
          // 使用 sql 创建数据表
          $sql = "CREATE TABLE gee_vps_chshuju (`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,`user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',`product_id` int(11) NULL DEFAULT NULL COMMENT '产品ID', `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '服务器名称',`password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '接口回传vpspassword',`attach` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '接口回传备注',`ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '服务器内部IP',`status` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '状态',`create_time` int(11) NULL DEFAULT NULL COMMENT '开通时间',`update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',`end_time` int(11) NULL DEFAULT NULL COMMENT '结束时间',PRIMARY KEY (`id`) USING BTREE)";

          if (!mysqli_query($conn, $sql)) {
              $ret['status'] = 500;
              $ret['msg'] = "创建数据表错误: " . mysqli_error($conn);
              return $ret;
          }
          mysqli_close($conn);
        }
        
        return $ret;
    }
    //卸载
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
        $ret['status'] = 200;
        $ret['msg'] = '操作成功';
        //删除数据表 与 相关数据
        $dbinfo = config("database");
        $conn = mysqli_connect($dbinfo['hostname'], $dbinfo['username'], $dbinfo['password'], $dbinfo['database']);
        // 检测连接
        if (!$conn) {
            die("连接失败: " . mysqli_connect_error());
        }
        
        if(Db::query('show tables like "gee_vps_chshuju"')){
          $sql = "DROP TABLE gee_vps_chshuju";
          if (!mysqli_query($conn, $sql)) {
              $ret['status'] = 500;
              $ret['msg'] = "删除数据表错误: " . mysqli_error($conn);
              return $ret;
          }
          mysqli_close($conn);
        }
        $vps = new GeeVps();
        $vps->where('plug_name = "chshuju"')->delete();
        // return $ret;

        //先删除目录下的文件：
        $dir = './../addons/vps/chshuju';
        $this->deldir($dir);
        $dir = './../application/chshuju';
        $this->deldir($dir);
        return $ret;
    }

    //删除
    public function deldir($dir){
      if(is_dir($dir)){
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
          if($file!="." && $file!="..") {
              $fullpath=$dir."/".$file;
              if(!is_dir($fullpath)) {
                unlink($fullpath);
              } else {
                $this->deldir($fullpath);
              }
          }
        }
        // exit;
        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
          return true;
        } else {
          return false;
        }
    }
  }
}


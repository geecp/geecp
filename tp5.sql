/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : tp5

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 19/12/2019 16:04:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for gee_addons
-- ----------------------------
DROP TABLE IF EXISTS `gee_addons`;
CREATE TABLE `gee_addons`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '插件标识',
  `author` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '插件作者',
  `range` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '插件所属模块',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '插件配置',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '插件名称',
  `introduce` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '插件介绍',
  `version` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '版本号',
  `license` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '授权费',
  `is_list` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否包含列表 0:包含 1:不包含',
  `status` int(11) NULL DEFAULT 0 COMMENT '插件状态 0:未安装 1:未启用 2: 已启用',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 47 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_addons
-- ----------------------------
INSERT INTO `gee_addons` VALUES (3, 'zoneidc', '七朵云', 'vps', '{\"user\": {\"type\": \"text\",\"value\": \"\"},\"password\": {\"type\": \"password\",\"value\": \"\"},\"product_id\": {\"type\": \"text\",\"value\": \"\"},\"machine_room\": {\"type\": \"text\",\"value\": \"\"}}', '纵横IDC', NULL, '1.0', NULL, '0', 2, NULL, NULL);

-- ----------------------------
-- Table structure for gee_adminuser
-- ----------------------------
DROP TABLE IF EXISTS `gee_adminuser`;
CREATE TABLE `gee_adminuser`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码盐值',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ip',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `last_login_time` int(11) NOT NULL COMMENT '最后登录时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '员工状态 0:正常 1锁定',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '员工姓名',
  `group_id` int(11) NOT NULL COMMENT '员工组',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '员工表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_adminuser
-- ----------------------------
INSERT INTO `gee_adminuser` VALUES (1, 'admin', '', '', '$2y$11$cub.Y9NiD6OhSrWo/q.TsegCVglCDoP7Mg6GLuVMuE.mBA5xGBVPa', '', '::1', 1557120679, 1576741532, 1576741532, '0', '超级管理员', 8);

-- ----------------------------
-- Table structure for gee_annexconfig
-- ----------------------------
DROP TABLE IF EXISTS `gee_annexconfig`;
CREATE TABLE `gee_annexconfig`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('0','ftp','bos','qiniu','oss') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '附件管理类型',
  `ftp_sever` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'FTP服务器(文件读取地址)',
  `ftp_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'FTP账号',
  `ftp_pwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'FTP密码',
  `ftp_port` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'FTP端口号',
  `ftp_pasv` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否开启被动模式 0:不开启 1:开启',
  `ftp_ssl` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否开启ssl链接 0:不开启 1:开启',
  `ftp_timeout` int(11) NULL DEFAULT 60 COMMENT '超时时间 默认60 单位s',
  `ftp_remoteroor` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片服务器根目录',
  `bos_ak` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百度云存储AK',
  `bos_sk` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百度云存储sk',
  `bos_bucket` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百度云存储bucket',
  `bos_domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百度云存储绑定域名',
  `qiniu_ak` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '七牛云存储AK',
  `qiniu_sk` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '七牛云存储SK',
  `qiniu_bucket` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '七牛云存储bucket',
  `qiniu_domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '七牛云存储绑定域名',
  `oss_ak` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '阿里云OSS存储AK',
  `oss_sk` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '阿里云OSS存储SK',
  `oss_bucket` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '阿里云OSS存储bucket',
  `oss_domain` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '阿里云OSS存储绑定域名',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_annexconfig
-- ----------------------------
INSERT INTO `gee_annexconfig` VALUES (1, 'bos', '', '', '', '', '0', '0', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 1558084404, 1558331556);

-- ----------------------------
-- Table structure for gee_billing
-- ----------------------------
DROP TABLE IF EXISTS `gee_billing`;
CREATE TABLE `gee_billing`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '编号',
  `order_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `pro_list` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '购买产品合计 0:账户充值',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` enum('0','1','2','3','4') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '交易类型 0:消费 1:充值 2:提现 3:退款 4:产品交易',
  `order_type` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单类型 0:不是订单 1:购买 2:续费 3:升级',
  `money` double(255, 2) NOT NULL COMMENT '交易金额',
  `balance` double(255, 2) NOT NULL COMMENT '交易后余额',
  `cash` double(255, 2) NULL DEFAULT NULL COMMENT '现金',
  `channel_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '渠道类型 0:账户余额 1:第三方支付',
  `remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '订单备注',
  `status` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '交易状态 0:未支付 1:已支付 2:已取消',
  `order_status` enum('0','1','2','3','4') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '订单状态 0:不是订单 1:已支付 2:待支付 3:已取消 4:已作废',
  `is_invoice` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否可开发票 0:不可 1:可开',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 132 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_emailconfig
-- ----------------------------
DROP TABLE IF EXISTS `gee_emailconfig`;
CREATE TABLE `gee_emailconfig`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP服务器',
  `port` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP端口',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP密码',
  `secure` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP验证方式',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP发件人信箱',
  `emailname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP发件人姓名',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_emailconfig
-- ----------------------------
INSERT INTO `gee_emailconfig` VALUES (1, '', '', '', '', '', '', '', 1557975644, 1557991905);

-- ----------------------------
-- Table structure for gee_homeroute
-- ----------------------------
DROP TABLE IF EXISTS `gee_homeroute`;
CREATE TABLE `gee_homeroute`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由名称',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `level` int(11) NULL DEFAULT NULL COMMENT '路由等级',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '路由图标',
  `is_show` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '是否展示到列表中 0:不展示 1:展示',
  `is_customize` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否为自定义导航展示 0:不是 1:是',
  `is_pro` int(11) NULL DEFAULT 0 COMMENT '所属产品类型',
  `is_plug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属插件',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_homeroute
-- ----------------------------
INSERT INTO `gee_homeroute` VALUES (1, '首页', 'index', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_homeroute` VALUES (2, '产品列表', 'product', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_homeroute` VALUES (3, 'VPS产品', 'vps', 2, NULL, NULL, '1', '0', 0, NULL, NULL, 0, 0);

-- ----------------------------
-- Table structure for gee_invoice
-- ----------------------------
DROP TABLE IF EXISTS `gee_invoice`;
CREATE TABLE `gee_invoice`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL COMMENT '申请用户',
  `number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发票号',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发票抬头',
  `money` double(255, 2) NOT NULL COMMENT '发票金额',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '发票内容',
  `type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '发票类型 0:普通发票 1:增值税专用发票',
  `status` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '发票状态 0:审核中 1:已寄出 2:已取消 3:未通过',
  `express` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '快递单号',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '发票备注',
  `n_type` int(11) NULL DEFAULT NULL COMMENT '普票类型 0:个人普票 1:企业类普票',
  `taxpayerno` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纳税人识别号',
  `bank` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户银行名称',
  `bankuser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户账号',
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '开户银行地址',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所留电话',
  `addr_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收货人姓名',
  `addr_region` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收货人所在地区',
  `addr_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收货人街道地址',
  `addr_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收货人邮政编码',
  `addr_tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收货人联系电话',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_invoice_addr
-- ----------------------------
DROP TABLE IF EXISTS `gee_invoice_addr`;
CREATE TABLE `gee_invoice_addr`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '收件人姓名',
  `province` int(11) NULL DEFAULT NULL COMMENT '省份',
  `city` int(11) NULL DEFAULT NULL COMMENT '城市',
  `district` int(11) NULL DEFAULT NULL COMMENT '区域',
  `region` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '所在地区 省+市+区总合',
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '详细地址',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮政编码',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系电话',
  `is_defualt` int(11) NULL DEFAULT NULL COMMENT '是否为默认地址 0:不是 1:是',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_invoice_info
-- ----------------------------
DROP TABLE IF EXISTS `gee_invoice_info`;
CREATE TABLE `gee_invoice_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NULL DEFAULT NULL COMMENT '发票类型 0:增值税普通发票 1:增值税专用发票',
  `n_type` int(11) NULL DEFAULT NULL COMMENT '普票类型 0:个人普票 1:企业类普票',
  `title` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '发票抬头',
  `taxpayerno` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '纳税人识别号',
  `bank` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户银行名称',
  `bankuser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户账号',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地址',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '电话',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_log
-- ----------------------------
DROP TABLE IF EXISTS `gee_log`;
CREATE TABLE `gee_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日志内容',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作IP',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 661 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_msgmodel
-- ----------------------------
DROP TABLE IF EXISTS `gee_msgmodel`;
CREATE TABLE `gee_msgmodel`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '模板类型 0:短信验证码 1:短信通知  2:邮件验证码 3:邮件通知',
  `mark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板标识',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板名称',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模板内容',
  `status` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '模板状态 0:可用 1:禁用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_msgmodel
-- ----------------------------
INSERT INTO `gee_msgmodel` VALUES (1, '3', 'defaultEmail', '默认测试邮件', '这是一个默认邮件模板{basic_name}|{basic_email}|{basic_url}|{basic_logo}|{basic_icp}|{basic_beian}|{basic_idc}|{basic_isp}|{basic_qwejo}|{email_code}', '0', 1557988637, 1558062883);

-- ----------------------------
-- Table structure for gee_order
-- ----------------------------
DROP TABLE IF EXISTS `gee_order`;
CREATE TABLE `gee_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `pro_id` int(11) NOT NULL COMMENT '产品ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` enum('0','1','2','3','4') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '订单类型 0:消费 1:充值 2:提现 3:退款 4:产品交易',
  `remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '订单备注',
  `money` double(255, 2) NOT NULL COMMENT '交易金额',
  `balance` double(255, 2) NOT NULL COMMENT '交易后余额',
  `product` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '交易产品合计',
  `cash` double(255, 2) NULL DEFAULT NULL COMMENT '现金支付',
  `channel_type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '渠道类型 0:余额支付 1:第三方支付',
  `status` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '交易状态 0:未支付 1:已支付 2:支付失败',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_osgroup
-- ----------------------------
DROP TABLE IF EXISTS `gee_osgroup`;
CREATE TABLE `gee_osgroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作系统名称',
  `uname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_osgroup
-- ----------------------------
INSERT INTO `gee_osgroup` VALUES (1, 'CentOs', 'root', 9, 1574661455, 1574672282);
INSERT INTO `gee_osgroup` VALUES (2, 'Windows', 'Administrator', 0, 1574663512, 1574672295);
INSERT INTO `gee_osgroup` VALUES (3, 'Ubuntu', 'ubuntu', 0, 1574672319, 1574672319);

-- ----------------------------
-- Table structure for gee_ostype
-- ----------------------------
DROP TABLE IF EXISTS `gee_ostype`;
CREATE TABLE `gee_ostype`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作系统版本名称',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '所属操作系统',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_ostype
-- ----------------------------
INSERT INTO `gee_ostype` VALUES (1, 'CentOs 7.6 ', 1, 0, 1574661500, 1574661562);
INSERT INTO `gee_ostype` VALUES (2, 'CentOs 7.5 ', 1, 0, 1574661588, 1574661588);
INSERT INTO `gee_ostype` VALUES (3, 'Windows Server 2012 R2 数据中心版 64位英文版', 2, 0, 1574663546, 1574663582);
INSERT INTO `gee_ostype` VALUES (4, 'Windows Server 2012 R2 数据中心版 64位中文版', 2, 0, 1574663555, 1574663592);
INSERT INTO `gee_ostype` VALUES (5, 'Windows Server 2012 R2 数据中心版 64位', 2, 0, 1574663561, 1574732562);
INSERT INTO `gee_ostype` VALUES (6, 'Ubuntu Server 18.04.1 LTS 64位', 3, 0, 1574672328, 1574672328);
INSERT INTO `gee_ostype` VALUES (7, 'Ubuntu Server 16.04.1 LTS 64位', 3, 0, 1574672338, 1574672338);

-- ----------------------------
-- Table structure for gee_picture
-- ----------------------------
DROP TABLE IF EXISTS `gee_picture`;
CREATE TABLE `gee_picture`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图片链接',
  `sha1` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'sha1',
  `md5` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'md5',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 54 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_pro_config
-- ----------------------------
DROP TABLE IF EXISTS `gee_pro_config`;
CREATE TABLE `gee_pro_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单编号',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '产品配置',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_product
-- ----------------------------
DROP TABLE IF EXISTS `gee_product`;
CREATE TABLE `gee_product`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '产品类型 1:虚拟主机  2:VPS主机  3:云服务器  4:SSL证书  5:域名 6:其他',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品名称',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '产品分组',
  `describe` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品描述',
  `email_model` int(11) NOT NULL COMMENT '开通产品时会发送的邮件模板id',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '产品标签',
  `update_list` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '可用于升级的套餐',
  `month` double(255, 2) NULL DEFAULT NULL COMMENT '月价格',
  `quarter` double(255, 2) NULL DEFAULT NULL COMMENT '季度价格',
  `semestrale` double(255, 2) NULL DEFAULT NULL COMMENT '半年价格',
  `years` double(255, 2) NULL DEFAULT NULL COMMENT '年价格',
  `biennium` double(255, 2) NULL DEFAULT NULL COMMENT '两年价格',
  `triennium` double(255, 2) NULL DEFAULT NULL COMMENT '三年价格',
  `sort` int(11) NULL DEFAULT NULL COMMENT '产品排序',
  `plug` int(11) NULL DEFAULT NULL COMMENT '所用插件ID',
  `plug_config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '插件配置项',
  `added` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '产品增值服务',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 26 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_product_class
-- ----------------------------
DROP TABLE IF EXISTS `gee_product_class`;
CREATE TABLE `gee_product_class`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类名称',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类标识',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_product_class
-- ----------------------------
INSERT INTO `gee_product_class` VALUES (1, '虚拟主机', 'vhost', 0, 0);
INSERT INTO `gee_product_class` VALUES (2, 'VPS主机', 'vps', 0, 0);
INSERT INTO `gee_product_class` VALUES (3, '云服务器', 'chost', 0, 0);
INSERT INTO `gee_product_class` VALUES (4, 'SSL证书', 'ssl', 0, 0);
INSERT INTO `gee_product_class` VALUES (5, '域名', 'domain', 0, 0);
INSERT INTO `gee_product_class` VALUES (6, '其他', 'other', 0, 0);
INSERT INTO `gee_product_class` VALUES (8, '物理服务器租用', 'server', 1573543543, 1573543551);

-- ----------------------------
-- Table structure for gee_product_group
-- ----------------------------
DROP TABLE IF EXISTS `gee_product_group`;
CREATE TABLE `gee_product_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  `slogan` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '分组标语',
  `sort` int(11) NULL DEFAULT NULL COMMENT '组排序',
  `class_id` int(11) NULL DEFAULT NULL COMMENT '所属分类',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_product_type
-- ----------------------------
DROP TABLE IF EXISTS `gee_product_type`;
CREATE TABLE `gee_product_type`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类名称',
  `mark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类型标识',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 18 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_product_type
-- ----------------------------
INSERT INTO `gee_product_type` VALUES (1, '计算', '', 0, 1560152069);
INSERT INTO `gee_product_type` VALUES (2, '网络', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (3, '存储和CDN', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (5, '安全和管理', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (15, '其他', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (4, '数据库', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (7, '网站服务', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (6, '数据分析', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (8, '智能多媒体服务', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (9, '物联网服务', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (10, '人工智能', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (11, '数字营销云', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (12, '区块链', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (13, '应用服务', '', 0, 0);
INSERT INTO `gee_product_type` VALUES (14, '云市场', '', 0, 0);

-- ----------------------------
-- Table structure for gee_route
-- ----------------------------
DROP TABLE IF EXISTS `gee_route`;
CREATE TABLE `gee_route`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由名称',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `is_show` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '是否展示 0:不展示 1:展示',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '路由图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 93 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_route
-- ----------------------------
INSERT INTO `gee_route` VALUES (1, '服务管理', 'service', 0, '1', 1557194766, 1557194766, 'fa-industry');
INSERT INTO `gee_route` VALUES (2, '域名列表', 'domain', 1, '0', 1557194766, 1557194766, 'fa-globe');
INSERT INTO `gee_route` VALUES (3, '主机管理', 'host', 1, '0', 1557194766, 1557194766, 'fa-server');
INSERT INTO `gee_route` VALUES (4, '租用托管', 'server', 1, '1', 1557194766, 1557194766, 'fa-server');
INSERT INTO `gee_route` VALUES (5, '工单管理', 'ticket', 0, '1', 1557194766, 1557194766, ' fa-ticket');
INSERT INTO `gee_route` VALUES (6, '工单列表', 'list', 5, '1', 1557194766, 1557194766, 'fa-list-ul');
INSERT INTO `gee_route` VALUES (7, '工单分类', 'group', 5, '0', 1557194766, 1557194766, 'fa-tags');
INSERT INTO `gee_route` VALUES (8, '产品管理', 'product', 0, '1', 1557194766, 1557194766, 'fa-cube');
INSERT INTO `gee_route` VALUES (46, '产品类型', 'type', 8, '1', 1557194766, 1557194766, 'fa-cubes');
INSERT INTO `gee_route` VALUES (13, '区域管理', 'region', 12, '1', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (14, '线路管理', 'line', 12, '1', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (15, '系统管理', 'system', 12, '1', 1557194766, 1557194766, 'fa-linux');
INSERT INTO `gee_route` VALUES (16, '用户管理', 'user', 0, '1', 1557194766, 1557194766, 'fa-user');
INSERT INTO `gee_route` VALUES (17, '用户列表', 'list', 16, '1', 1557194766, 1557194766, 'fa-list-ul');
INSERT INTO `gee_route` VALUES (18, '用户分组', 'group', 16, '1', 1557194766, 1557194766, 'fa-tag');
INSERT INTO `gee_route` VALUES (19, '财务管理', 'finance', 0, '1', 1557194766, 1557194766, 'fa-database');
INSERT INTO `gee_route` VALUES (20, '总览', 'index', 19, '1', 1557194766, 1557194766, 'fa-pie-chart');
INSERT INTO `gee_route` VALUES (21, '财务明细', 'details', 19, '1', 1557194766, 1557194766, 'fa-newspaper-o');
INSERT INTO `gee_route` VALUES (24, '充值记录', 'recharge', 19, '1', 1557194766, 1557194766, 'fa-bar-chart');
INSERT INTO `gee_route` VALUES (25, '订单列表', 'order', 19, '1', 1557194766, 1557194766, 'fa-reorder');
INSERT INTO `gee_route` VALUES (26, '发票管理', 'invoice', 19, '1', 1557194766, 1557194766, 'fa-ticket');
INSERT INTO `gee_route` VALUES (27, '员工管理', 'staff', 0, '1', 1557194766, 1557194766, 'fa-user');
INSERT INTO `gee_route` VALUES (28, '员工列表', 'list', 27, '1', 1557194766, 1557194766, 'fa-users');
INSERT INTO `gee_route` VALUES (29, '员工分组', 'group', 27, '1', 1557194766, 1557194766, 'fa-user-secret');
INSERT INTO `gee_route` VALUES (30, '插件管理', 'addons', 0, '1', 1557194766, 1557194766, 'fa-th');
INSERT INTO `gee_route` VALUES (31, '插件列表', 'list', 30, '1', 1557194766, 1557194766, 'fa-list');
INSERT INTO `gee_route` VALUES (32, '详细', 'details', 30, '0', 1557194766, 1557194766, 'fa-newspaper-o');
INSERT INTO `gee_route` VALUES (33, '应用市场', '', 0, '0', 1557194766, 1557194766, 'fa-cube');
INSERT INTO `gee_route` VALUES (34, '系统设置', 'system', 0, '1', 1557194766, 1557194766, 'fa-cog');
INSERT INTO `gee_route` VALUES (35, '基本信息', 'basic', 34, '1', 1557194766, 1557194766, 'fa-info-circle');
INSERT INTO `gee_route` VALUES (36, '邮件设置', 'email', 34, '1', 1557194766, 1557194766, 'fa-envelope');
INSERT INTO `gee_route` VALUES (37, '短信设置', 'sms', 34, '0', 1557194766, 1557194766, 'fa-commenting');
INSERT INTO `gee_route` VALUES (38, '支付设置', 'pay', 34, '0', 1557194766, 1557194766, 'fa-credit-card');
INSERT INTO `gee_route` VALUES (39, '消息模板', 'template', 34, '1', 1557194766, 1557194766, 'fa-comments');
INSERT INTO `gee_route` VALUES (40, '附件设置', 'annex', 34, '1', 1557194766, 1557194766, 'fa-file');
INSERT INTO `gee_route` VALUES (43, '产品列表', 'list', 8, '1', 1557194766, 1557194766, 'fa-list-ul');
INSERT INTO `gee_route` VALUES (47, '导航管理', 'routes', 0, '0', 1557194766, 1557194766, 'fa-navicon');
INSERT INTO `gee_route` VALUES (48, '添加导航', 'add', 47, '0', 1557194766, 1557194766, 'fa-navicon');
INSERT INTO `gee_route` VALUES (49, '添加验证', 'addAuth', 47, '0', 1557194766, 1557194766, 'fa-street-view');
INSERT INTO `gee_route` VALUES (50, '删除路由', 'del', 47, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (51, '产品分类', 'class', 8, '1', 1557194766, 1557194766, 'fa-tag');
INSERT INTO `gee_route` VALUES (52, '添加分类', 'addclass', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (53, '添加分类验证', 'addclassAuth', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (54, '删除分类', 'delclass', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (59, '工单接入', 'join', 5, '0', 1557194766, 1557194766, 'fa-ticket');
INSERT INTO `gee_route` VALUES (57, '工单详情', 'details', 5, '0', 1557194766, 1557194766, 'fa-ticket');
INSERT INTO `gee_route` VALUES (58, '工单回复', 'reply', 5, '0', 1557194766, 1557194766, 'fa-ticket');
INSERT INTO `gee_route` VALUES (60, '增值服务', 'added', 8, '1', 1557194766, 1557194766, 'fa-thumbs-up');
INSERT INTO `gee_route` VALUES (61, '新增增值服务组', 'addaddedgroup', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (62, '新增增值服务', 'addadded', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (63, '新增增值服务组验证', 'addaddedgroupAuth', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (64, '删除增值服务组', 'deladdedgroup', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (65, '新增增值服务验证', 'addaddedAuth', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (66, '删除增值服务', 'deladded', 8, '0', 1557194766, 1557194766, NULL);
INSERT INTO `gee_route` VALUES (67, '交付物理服务器', 'delivery', 1, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (68, '交付物理服务器验证', 'deliveryauth', 1, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (69, '删除订单', 'delorder', 19, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (71, '操作系统', 'os', 8, '1', 0, 0, 'fa-linux');
INSERT INTO `gee_route` VALUES (79, '获取物理服务器信息', 'getserver', 1, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (73, '添加操作系统', 'addosgroup', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (74, '添加操作系统验证', 'addosgroupAuth', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (75, '删除操作系统', 'delosgroup', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (76, '添加操作系统版本', 'addostype', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (77, '添加操作系统验证', 'addostypeAuth', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (78, '删除操作系统版本', 'delostype', 8, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (80, '编辑物理服务器信息', 'editserver', 1, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (81, '实名认证', 'realverify', 16, '0', 0, 0, 'fa-check');
INSERT INTO `gee_route` VALUES (82, '通过认证', 'passreal', 16, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (83, '拒绝认证', 'rejectreal', 16, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (84, '企业认证', 'enterpriseverify', 16, '1', 0, 0, 'fa-black-tie');
INSERT INTO `gee_route` VALUES (85, '通过认证', 'passenterprise', 16, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (86, '拒绝认证', 'rejectenterprise', 16, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (9, '云主机产品管理', 'vps', 1, '1', 0, 0, 'fa-tasks');
INSERT INTO `gee_route` VALUES (87, '通过发票申请', 'passinvoice', 19, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (88, '拒绝发票申请', 'nopassinvoice', 19, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (89, '编辑发票信息', 'editinvoice', 19, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (91, '续费VPS', 'renewvps', 1, '0', 0, 0, NULL);
INSERT INTO `gee_route` VALUES (92, 'vps控制面板', 'vpsmanager', 1, '0', 0, 0, NULL);

-- ----------------------------
-- Table structure for gee_server
-- ----------------------------
DROP TABLE IF EXISTS `gee_server`;
CREATE TABLE `gee_server`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_group_id` int(11) NULL DEFAULT NULL COMMENT '产品分组ID',
  `pro_id` int(11) NULL DEFAULT NULL COMMENT '产品ID',
  `server_added` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '增值服务选项',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主机名称',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公网IP',
  `intranetip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '内网IP',
  `username` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '主机账号',
  `password` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '主机密码',
  `osgroup` int(11) NULL DEFAULT NULL COMMENT '操作系统类型',
  `ostype` int(11) NULL DEFAULT NULL COMMENT '操作系统版本',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `remake` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `status` int(11) NULL DEFAULT NULL COMMENT '主机状态 0:开通中 1:已到期 2:正在重装系统 3:正在运行 4:服务器异常',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) NULL DEFAULT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_server
-- ----------------------------
INSERT INTO `gee_server` VALUES (7, 14, 23, '{\"CC\":18,\"ddosfree\":20,\"ddospro\":22,\"dk\":\"54,10\"}', '测试', '192.168.0.2', '192.168.0.21', 'root', '123456789', 1, 1, 25, '测试用', 3, 1574128273, 1574128273, 1587261073);
INSERT INTO `gee_server` VALUES (10, 14, 23, '{\"CC\":18,\"ddosfree\":19,\"ddospro\":21,\"dk\":\"54,1\"}', 'bdpqt479', '192.168.0.133', '192.168.0.20', 'root', '1234567', 1, 1, 30, '', 3, 1574321429, 1574751527, 1577808000);
INSERT INTO `gee_server` VALUES (11, 14, 23, '{\"CC\":18,\"ddosfree\":20,\"ddospro\":22,\"dk\":\"54,1\"}', 'klmIOPS3', '192.168.0.123', '192.168.0.19', 'root', '123123', 1, 1, 25, '', 3, 1574326534, 1574326534, 1576918534);
INSERT INTO `gee_server` VALUES (17, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'dhoqIMS7', '64.54.166.24', '192.168.0.13', 'root', '123123', 1, 1, 25, '', 3, 1574923884, 1574925458, 1577515860);
INSERT INTO `gee_server` VALUES (12, 14, 23, '{\"CC\":18,\"ddosfree\":20,\"ddospro\":22,\"dk\":\"54,10\"}', 'aqwCJZ48', '192.168.1.151', '192.168.0.18', 'root', '123456', 1, 1, 25, '', 3, 1574836404, 1574836404, 1590561204);
INSERT INTO `gee_server` VALUES (13, 13, 22, '{\"HDD2\":46,\"cn2\":53}', 'muAJKLV2', '192.168.0.116', '192.168.0.17', 'root', '123456', 1, 1, 25, '', 3, 1574836915, 1574836915, 1577428915);
INSERT INTO `gee_server` VALUES (14, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'noBFKQU4', '192.168.0.112', '192.168.0.16', 'root', '123456', 1, 1, 25, '', 3, 1574837305, 1574837305, 1577429305);
INSERT INTO `gee_server` VALUES (15, 13, 22, '{\"HDD2\":46,\"cn2\":53}', 'test-v-1', '192.168.0.155', '192.168.0.15', 'root', '123456', 1, 1, 25, '', 3, 1574837632, 1574837632, 1577429632);
INSERT INTO `gee_server` VALUES (16, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'test-v-2', '192.168.0.124', '192.168.0.14', 'root', 'test123456', 1, 1, 25, '', 3, 1574837774, 1574925466, 1577429760);
INSERT INTO `gee_server` VALUES (18, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'oBCFHL39', '64.51.177.25', '192.168.0.12', 'root', '123456', 1, 1, 25, '', 3, 1574924677, 1574925450, 1577516640);
INSERT INTO `gee_server` VALUES (19, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'ciHORUX8', '64.51.177.26', '192.168.0.11', 'root', '123456', 1, 1, 25, '12321312321321321321', 3, 1574924926, 1574934835, 1577516880);
INSERT INTO `gee_server` VALUES (20, 13, 22, '{\"HDD2\":0,\"cn2\":0}', 'diluL149', '64.54.177.27', '192.168.0.33', 'root', '123456', 1, 1, 25, '', 3, 1574934732, 1574934732, 1577526732);

-- ----------------------------
-- Table structure for gee_server_added
-- ----------------------------
DROP TABLE IF EXISTS `gee_server_added`;
CREATE TABLE `gee_server_added`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '增值服务标识',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '增值服务名称',
  `slogan` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '增值服务描述',
  `type` int(11) NULL DEFAULT NULL COMMENT '增值服务类型 1:单选 2:下拉 3输入框',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_server_added
-- ----------------------------
INSERT INTO `gee_server_added` VALUES (3, 'CC', '免费CC防护服务', '', 1, NULL, 1573635011, 1573635011);
INSERT INTO `gee_server_added` VALUES (4, 'ddosfree', 'IP DDOS 防护服务（10G/共享）', '', 2, NULL, 1573635054, 1573635054);
INSERT INTO `gee_server_added` VALUES (5, 'ddospro', 'DDOS防护升级服务', '', 2, NULL, 1573635083, 1573635083);
INSERT INTO `gee_server_added` VALUES (6, 'ip', '增加IP地址数量', '', 2, NULL, 1573635130, 1573635130);
INSERT INTO `gee_server_added` VALUES (7, 'bandwidth', '网络带宽升级服务', '', 2, NULL, 1573635167, 1573635167);
INSERT INTO `gee_server_added` VALUES (8, 'memory', '内存升级服务', '', 2, NULL, 1573635191, 1573635191);
INSERT INTO `gee_server_added` VALUES (9, 'HDD1', '默认硬盘一', '', 2, NULL, 1573635214, 1573635214);
INSERT INTO `gee_server_added` VALUES (10, 'HDD2', '默认硬盘二', '', 1, 2, 1573635229, 1573723620);
INSERT INTO `gee_server_added` VALUES (11, 'cn2', '中国直连CN2线路优化服务', '', 1, 1, 1573635263, 1573723613);
INSERT INTO `gee_server_added` VALUES (12, 'dk', '带宽选择', '', 3, 4, 1573787507, 1573787507);

-- ----------------------------
-- Table structure for gee_server_added_items
-- ----------------------------
DROP TABLE IF EXISTS `gee_server_added_items`;
CREATE TABLE `gee_server_added_items`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '增值服务名称',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '所属服务ID',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `month` double(255, 2) NULL DEFAULT NULL COMMENT '月价格',
  `quarter` double(255, 0) NULL DEFAULT NULL COMMENT '季度价格',
  `semestrale` double(255, 0) NULL DEFAULT NULL COMMENT '半年价格',
  `years` double(255, 0) NULL DEFAULT NULL COMMENT '年价格',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '值',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 55 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_server_added_items
-- ----------------------------
INSERT INTO `gee_server_added_items` VALUES (17, '不开启', 3, 1, 0.00, 0, 0, 0, '', 1573635655, 1573635655);
INSERT INTO `gee_server_added_items` VALUES (18, '开启', 3, 2, 0.00, 0, 0, 0, '', 1573635676, 1573635676);
INSERT INTO `gee_server_added_items` VALUES (19, '默认 5 IP DDOS 防护', 4, 1, 0.00, 0, 0, 0, '', 1573635708, 1573635708);
INSERT INTO `gee_server_added_items` VALUES (20, '升级至 /29 防护服务', 4, 2, 175.00, 380, 1240, 2590, '', 1573635749, 1573635757);
INSERT INTO `gee_server_added_items` VALUES (21, '免费 10G DDOS 防护服务', 5, 1, 0.00, 0, 0, 0, '', 1573635789, 1573635789);
INSERT INTO `gee_server_added_items` VALUES (22, '升级至 20G DDOS 防护服务', 5, 2, 2233.00, 4466, 8822, 12580, '', 1573635825, 1573635825);
INSERT INTO `gee_server_added_items` VALUES (23, '升级至 40G DDOS 防护服务', 5, 3, 6573.00, 18750, 34850, 94880, '', 1573635865, 1573635865);
INSERT INTO `gee_server_added_items` VALUES (24, '升级至 60G DDOS 防护服务', 5, 4, 8680.00, 24870, 67810, 187650, '', 1573635890, 1573635890);
INSERT INTO `gee_server_added_items` VALUES (25, '升级至 90G DDOS 防护服务', 5, 5, 15197.00, 38540, 97580, 27850, '', 1573635918, 1573635918);
INSERT INTO `gee_server_added_items` VALUES (26, '升级至 150G DDOS 防护服务', 5, 6, 30387.00, 90975, 278940, 678970, '', 1573635946, 1573635946);
INSERT INTO `gee_server_added_items` VALUES (28, '升级至 200G DDOS 防护服务', 5, 7, 52097.00, 157800, 399999, 999999, '', 1573635992, 1573635992);
INSERT INTO `gee_server_added_items` VALUES (29, '升级至 500G DDOS 防护服务', 5, 8, 86821.00, 248760, 678540, 1875460, '', 1573636019, 1573636019);
INSERT INTO `gee_server_added_items` VALUES (30, '升级至 /29 5个可用IP ', 6, 1, 175.00, 345, 970, 2750, '', 1573636087, 1573636087);
INSERT INTO `gee_server_added_items` VALUES (31, '升级至 /28 13个可用IP', 6, 2, 280.00, 680, 1880, 3880, '', 1573636110, 1573636110);
INSERT INTO `gee_server_added_items` VALUES (32, '升级至 /27 29个可用IP', 6, 3, 483.00, 1280, 3680, 9280, '', 1573636137, 1573636137);
INSERT INTO `gee_server_added_items` VALUES (33, '升级至 /26 61个可用IP', 6, 4, 693.00, 1890, 3890, 9890, '', 1573636162, 1573636162);
INSERT INTO `gee_server_added_items` VALUES (34, '升级至 /25 125个可用IP', 6, 5, 833.00, 1633, 3833, 9833, '', 1573636192, 1573636192);
INSERT INTO `gee_server_added_items` VALUES (35, '升级至 /24 253个可用IP', 6, 6, 1323.00, 3323, 9323, 27333, '', 1573636233, 1573636233);
INSERT INTO `gee_server_added_items` VALUES (36, '免费100M（直连带宽）服务', 7, 1, 0.00, 0, 0, 0, '', 1573636263, 1573636263);
INSERT INTO `gee_server_added_items` VALUES (37, '升级至30M CN2 GIA 线路带宽', 7, 2, 140.00, 340, 940, 2740, '', 1573636294, 1573636294);
INSERT INTO `gee_server_added_items` VALUES (38, '默认 16GB 内存', 8, 1, 0.00, 0, 0, 0, '', 1573636333, 1573636333);
INSERT INTO `gee_server_added_items` VALUES (39, '升级至 256GB 内存', 8, 2, 9999.00, 27999, 67999, 187999, '', 1573636356, 1573636356);
INSERT INTO `gee_server_added_items` VALUES (40, '默认 1TB SATA HDD 硬盘', 9, 1, 0.00, 0, 0, 0, '', 1573636424, 1574317397);
INSERT INTO `gee_server_added_items` VALUES (41, '升级为 2TB SATA HDD 硬盘', 9, 2, 140.00, 248, 580, 1280, '', 1573636450, 1574317389);
INSERT INTO `gee_server_added_items` VALUES (42, '升级为 4TB SATA HDD 硬盘', 9, 3, 245.00, 480, 880, 1880, '', 1573636481, 1574317374);
INSERT INTO `gee_server_added_items` VALUES (43, '升级为 6TB SATA HDD 硬盘', 9, 4, 420.00, 880, 1880, 2880, '', 1573636501, 1574317351);
INSERT INTO `gee_server_added_items` VALUES (44, '升级为 480G SSD 硬盘', 9, 5, 280.00, 480, 880, 1880, '', 1573636527, 1574317338);
INSERT INTO `gee_server_added_items` VALUES (45, '无', 10, 1, 0.00, 0, 0, 0, '', 1573636558, 1574317323);
INSERT INTO `gee_server_added_items` VALUES (46, '增加 1TB SATA HDD 硬盘', 10, 2, 112.00, 332, 992, 2792, '', 1573636585, 1573636592);
INSERT INTO `gee_server_added_items` VALUES (47, '增加 2TB SATA HDD 硬盘', 10, 3, 140.00, 280, 458, 1400, '', 1573636608, 1574317315);
INSERT INTO `gee_server_added_items` VALUES (48, '增加 4TB SATA HDD 硬盘', 10, 4, 245.00, 540, 1240, 2450, '', 1573636628, 1574317292);
INSERT INTO `gee_server_added_items` VALUES (49, '增加 6TB SATA HDD 硬盘', 10, 5, 420.00, 840, 1500, 2460, '', 1573636653, 1574317267);
INSERT INTO `gee_server_added_items` VALUES (50, '增加 480G SSD 硬盘', 10, 6, 280.00, 480, 880, 1080, '', 1573636678, 1574317246);
INSERT INTO `gee_server_added_items` VALUES (51, '增加 2TB SSD 硬盘', 10, 7, 525.00, 1050, 2100, 4200, '', 1573636717, 1574317230);
INSERT INTO `gee_server_added_items` VALUES (52, '中国优质（CN2/CU/CM ）线路升级选项 35元/M', 11, 1, 700.00, 1400, 2800, 5600, '', 1573636828, 1574317195);
INSERT INTO `gee_server_added_items` VALUES (53, 'CN2/GIA 升级选项 42元/M', 11, 2, 840.00, 1200, 2400, 4200, '', 1573636866, 1574317178);
INSERT INTO `gee_server_added_items` VALUES (54, 'M带宽', 12, 1, 10.00, 20, 30, 40, '1', 1573787829, 1574307085);

-- ----------------------------
-- Table structure for gee_staffgroup
-- ----------------------------
DROP TABLE IF EXISTS `gee_staffgroup`;
CREATE TABLE `gee_staffgroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组名称',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_staffgroup
-- ----------------------------
INSERT INTO `gee_staffgroup` VALUES (8, '超级管理员', 1557380521, 1557380521);
INSERT INTO `gee_staffgroup` VALUES (9, '财务审核', 1557380533, 1557380533);
INSERT INTO `gee_staffgroup` VALUES (10, '销售', 1557380542, 1557380549);

-- ----------------------------
-- Table structure for gee_ticket
-- ----------------------------
DROP TABLE IF EXISTS `gee_ticket`;
CREATE TABLE `gee_ticket`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromid` int(11) NULL DEFAULT NULL COMMENT '提交用户id',
  `replierid` int(11) NULL DEFAULT NULL COMMENT '接收人id',
  `num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单编号',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '工单描述',
  `imgs` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '相关截图',
  `status` int(11) NULL DEFAULT 0 COMMENT '工单状态 0:待接入 1:处理中 2:待回复 3:待您确认 4:已撤销 5:已完成',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单类型 ',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_ticket_details
-- ----------------------------
DROP TABLE IF EXISTS `gee_ticket_details`;
CREATE TABLE `gee_ticket_details`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NULL DEFAULT NULL COMMENT '所属工单id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '回复内容',
  `imgs` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '相关截图',
  `fromid` int(11) NULL DEFAULT NULL COMMENT '发言人ID',
  `replierid` int(11) NULL DEFAULT NULL COMMENT '接收人ID',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_user
-- ----------------------------
DROP TABLE IF EXISTS `gee_user`;
CREATE TABLE `gee_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名(英文)',
  `password` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `salt` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '加密盐值',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '姓名',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `is_email` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '邮箱验证 0:未认证 1:已认证',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机号',
  `is_phone` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '手机验证 0:未认证 1:已认证',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '固话',
  `type` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '用户类型 0:个人 1:企业',
  `balance` double(255, 2) NOT NULL DEFAULT 0.00 COMMENT '用户余额',
  `invoice_money` double(11, 0) NULL DEFAULT 0 COMMENT '已开票金额',
  `free_money` double(255, 0) NULL DEFAULT 0 COMMENT '开票冻结金额',
  `create_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '注册IP',
  `group_id` int(11) NOT NULL COMMENT '用户组ID',
  `approve` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '用户认证 0:未认证 1:已认证',
  `realname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `idcard` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '身份证号',
  `realverify` int(11) NULL DEFAULT NULL COMMENT '认证审核 0:未提交申请 1:审核中 2:审核成功 3: 审核失败',
  `status` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '用户状态 0:正常 1:欠费 2:锁定',
  `last_login_time` int(11) NULL DEFAULT NULL COMMENT '最后登录时间',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 32 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_user_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `gee_user_enterprise`;
CREATE TABLE `gee_user_enterprise`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NULL DEFAULT NULL COMMENT '组织类型 0:企业 1:其他组织',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '企业名称/字号名称',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '营业执照注册号/组织机构代码',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '营业执照扫描件/组织机构代码证扫描件',
  `is_individual` int(11) NULL DEFAULT NULL COMMENT '是否为个体工商户  0:否 1:是',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `status` int(11) NULL DEFAULT NULL COMMENT '审核状态  0:审核中 1:审核成功 2:审核失败',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_user_realnames
-- ----------------------------
DROP TABLE IF EXISTS `gee_user_realnames`;
CREATE TABLE `gee_user_realnames`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `rname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `idcard` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '身份证',
  `verify` int(255) NULL DEFAULT NULL COMMENT '审核状态 0:审核中 1:审核通过',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_usergroup
-- ----------------------------
DROP TABLE IF EXISTS `gee_usergroup`;
CREATE TABLE `gee_usergroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组名称',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_usergroup
-- ----------------------------
INSERT INTO `gee_usergroup` VALUES (2, '青铜代理', 1557364810, 1557364810);
INSERT INTO `gee_usergroup` VALUES (3, '白银代理', 1557364818, 1557364818);
INSERT INTO `gee_usergroup` VALUES (4, '黄金代理', 1557364828, 1557364828);
INSERT INTO `gee_usergroup` VALUES (5, '铂金代理', 1557364839, 1557364839);
INSERT INTO `gee_usergroup` VALUES (6, '钻石代理', 1557364845, 1557364845);
INSERT INTO `gee_usergroup` VALUES (1, '普通用户', 1557364855, 1557364855);

-- ----------------------------
-- Table structure for gee_vhost
-- ----------------------------
DROP TABLE IF EXISTS `gee_vhost`;
CREATE TABLE `gee_vhost`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `service_id` int(11) NOT NULL COMMENT '所属服务',
  `status` enum('0','1','2','-1','-2','-4') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '主机状态 0:运行 1:暂停 2:管理员停止 -1:未开设 -2:已经删除 -4:状态未确定',
  `paytype` enum('0','1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '支付方式 0:余额支付 1:支付宝支付 2:微信支付 3:现金支付',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) NULL DEFAULT NULL COMMENT '结束时间',
  `product_id` int(11) NULL DEFAULT NULL COMMENT '产品id',
  `domain` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '绑定域名',
  `ftp_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ftp用户名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_vps
-- ----------------------------
DROP TABLE IF EXISTS `gee_vps`;
CREATE TABLE `gee_vps`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `plug_id` int(11) NULL DEFAULT NULL COMMENT '所属插件id',
  `plug_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属插件数据表',
  `plug_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '插件名称',
  `pro_id` int(11) NULL DEFAULT NULL COMMENT '产品id',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_vps_zoneidc
-- ----------------------------
DROP TABLE IF EXISTS `gee_vps_zoneidc`;
CREATE TABLE `gee_vps_zoneidc`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL COMMENT '所属用户',
  `product_id` int(11) NULL DEFAULT NULL COMMENT '产品ID',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '服务器名称',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '接口回传vpspassword',
  `attach` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '接口回传备注',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '服务器内部IP',
  `status` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '状态',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '开通时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) NULL DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gee_webbasic
-- ----------------------------
DROP TABLE IF EXISTS `gee_webbasic`;
CREATE TABLE `gee_webbasic`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网站名称',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系邮箱',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网站首页域名',
  `logo` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'logo url地址',
  `logo1` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '深色LOGO',
  `favicon` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'favicon',
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '网站描述',
  `keywords` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '网站SEO关键字',
  `icp` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ICP备案',
  `beian` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '网安备案',
  `idc` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'IDC备案',
  `isp` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ISP备案',
  `maintain` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '维护模式 0:关闭 1:开启',
  `maintaininfo` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '维护模式下首页展示内容(可为html)',
  `code` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '第三方统计代码(内容为html)',
  `account_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户名称(线下汇款用)',
  `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开户银行(线下汇款用)',
  `account_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '汇款账号(线下汇款用)',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_webbasic
-- ----------------------------
INSERT INTO `gee_webbasic` VALUES (1, 'test', '123@123.cc', 'https://3', 'https://ylsq.cdn.bcebos.com/1576663497381689.png', 'https://ylsq.cdn.bcebos.com/157673985627459.png', 'https://ylsq.cdn.bcebos.com/1576739607123266.png', '1', '1', '1', '2', '3', '4', '0', '1', '1', '北京百度网讯科技有限公司', '招商银行北京分行上地支行', '1109021606104020100157799', 1557910511, 1576739861);

-- ----------------------------
-- Table structure for gee_webroute
-- ----------------------------
DROP TABLE IF EXISTS `gee_webroute`;
CREATE TABLE `gee_webroute`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由名称',
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `level` int(11) NULL DEFAULT NULL COMMENT '路由等级',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '路由图标',
  `is_show` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '是否展示到列表中 0:不展示 1:展示',
  `is_customize` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否为自定义导航展示 0:不是 1:是',
  `is_pro` int(11) NULL DEFAULT 0 COMMENT '所属产品类型',
  `is_plug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '所属插件',
  `remark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 198 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gee_webroute
-- ----------------------------
INSERT INTO `gee_webroute` VALUES (1, '基本信息', 'basic', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (2, '账号信息', 'info', 1, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (3, '安全设置', 'safety', 1, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (4, '实名认证', 'auth', 1, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (6, '云虚拟主机', 'host', 0, NULL, 'icon-cvh', '0', '0', 1, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (7, '云主机VPS', 'vps', 0, NULL, 'icon-ecs', '1', '0', 1, NULL, NULL, 1557194766, 1560335621);
INSERT INTO `gee_webroute` VALUES (8, '云服务器', 'server', 0, NULL, 'icon-ecs', '0', '0', 1, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (9, '域名管理', 'domain', 0, NULL, 'icon-cdm', '0', '0', 1, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (10, '云解析', 'analysis', 0, NULL, 'icon-dns', '0', '0', 1, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (11, '工单管理', 'ticket', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (12, '工单列表', 'list', 11, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (13, '创建工单', 'add', 11, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (14, '消息管理', 'message', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (132, '财务总览', 'overview', 131, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (16, '安全认证', 'safetyauth', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 1557194766, 1557194766);
INSERT INTO `gee_webroute` VALUES (136, '收支明细', 'dealrecord', 131, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (134, '消费总览', 'overview', 133, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (135, '账单明细', 'list', 133, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (133, '消费中心', 'consumption', 131, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (131, '财务中心', 'billing', 0, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (130, '支付状态', 'paystatus', 127, NULL, '', '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (129, '线上支付', 'onlinepay', 127, NULL, '', '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (128, '确认订单', 'confirm', 127, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (127, '购买产品', 'buy', 0, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (126, '实例', 'index', 7, NULL, NULL, '1', '0', 0, NULL, 'VPS实例列表', 1559807152, 1560335597);
INSERT INTO `gee_webroute` VALUES (138, '发票管理', 'invoice', 131, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (139, '发票列表', 'list', 138, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (140, '发票信息管理', 'template', 138, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (141, '寄送地址', 'addresslist', 138, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (142, '订单管理', 'order', 131, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (143, '续费管理', 'renew', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (144, '退订管理', 'refund', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (145, '可退款订单', 'list', 144, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (146, '退订记录', 'record', 144, NULL, NULL, '1', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (147, '合同管理', 'contract', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (148, '资源账单', 'resource', 133, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (182, '账号信息', 'iam', 0, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (150, '充值', 'recharge', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (151, '提现', 'withdraw', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (152, '购买验证', 'pay', 127, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (153, '工单详情', 'details', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (154, '创建工单验证', 'addauth', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (155, '工单回复', 'reply', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (156, '工单确认', 'confirm', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (157, '撤销工单', 'cancel', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (158, '删除工单', 'del', 11, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (159, '充值验证', 'rechargeauth', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (160, '接口API', 'api', 0, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (161, '支付宝同步', 'return_url', 160, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (162, '支付宝异步', 'notify_url', 160, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (163, '账单详情', 'dealrecorddetails', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (166, '物理服务器租用', 'server', 0, NULL, 'icon-dcc', '1', '0', 1, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (165, '取消订单', 'cancelorder', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 1560335597, 1560335597);
INSERT INTO `gee_webroute` VALUES (167, '租用物理服务器', 'add', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (168, '物理服务器管理', 'index', 166, NULL, NULL, '1', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (169, '获取物理服务器产品', 'getAddedItems', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (170, '获取价格', 'getPrice', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (171, '获取物理服务器增值服务', 'getAdded', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (172, '租用物理服务器验证', 'addAuth', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (173, '获取物理服务器密码', 'getpass', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (174, '重装操作系统', 'resetos', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (176, '删除订单', 'delorder', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (177, '获取操作系统版本', 'getOstypes', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (178, '订单详情', 'orderdetails', 131, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (179, '物理服务器详情', 'detail', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (180, '编辑服务器信息', 'edits', 166, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (181, '获取历史消费趋势', 'gethistory', 133, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (183, '用户中心', 'overview', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (184, '安全认证', 'accesslist', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (185, '个人认证', 'cpersonal', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (186, '企业认证', 'ccompany', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (187, '账号信息编辑', 'baseinfoedit', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (189, '提交实名认证信息', 'subauth', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (190, '修改基本信息', 'baseinfoedit', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (191, '提交企业认证信息', 'subcompany', 182, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (192, '提交发票信息', 'subtemp', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (193, '提交发票地址', 'subaddress', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (194, '删除发票地址', 'deladdress', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (195, '发票申请', 'applyinvoice', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (196, '提交发票申请', 'subapplyinvoice', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);
INSERT INTO `gee_webroute` VALUES (197, '取消发票申请', 'cancelInvoice', 138, NULL, NULL, '0', '0', 0, NULL, NULL, 0, 0);

SET FOREIGN_KEY_CHECKS = 1;

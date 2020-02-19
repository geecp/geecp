# Host: localhost  (Version: 5.5.53)
# Date: 2020-01-18 12:08:27
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "gee_accesskey"
#

DROP TABLE IF EXISTS `gee_accesskey`;
CREATE TABLE `gee_accesskey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ak` text COMMENT 'AccessKey',
  `sk` text COMMENT 'SecretKey',
  `intro` text COMMENT '说明',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_accesskey"
#

/*!40000 ALTER TABLE `gee_accesskey` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_accesskey` ENABLE KEYS */;

#
# Structure for table "gee_addons"
#

DROP TABLE IF EXISTS `gee_addons`;
CREATE TABLE `gee_addons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `author` varchar(50) DEFAULT NULL COMMENT '插件作者',
  `range` varchar(255) DEFAULT NULL COMMENT '插件所属模块',
  `config` text COMMENT '插件配置',
  `title` varchar(50) DEFAULT NULL COMMENT '插件名称',
  `introduce` text COMMENT '插件介绍',
  `version` varchar(10) DEFAULT NULL COMMENT '版本号',
  `license` varchar(255) DEFAULT NULL COMMENT '授权费',
  `is_list` enum('0','1') DEFAULT '0' COMMENT '是否包含列表 0:包含 1:不包含',
  `status` int(11) DEFAULT '0' COMMENT '插件状态 0:未安装 1:未启用 2: 已启用',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_addons"
#

/*!40000 ALTER TABLE `gee_addons` DISABLE KEYS */;
INSERT INTO `gee_addons` VALUES (3,'zoneidc','七朵云','vps','{\"user\": {\"type\": \"text\",\"value\": \"\"},\"password\": {\"type\": \"password\",\"value\": \"\"},\"product_id\": {\"type\": \"text\",\"value\": \"\"},\"machine_room\": {\"type\": \"text\",\"value\": \"\"}}','纵横IDC',NULL,'1.0',NULL,'0',0,NULL,NULL);
/*!40000 ALTER TABLE `gee_addons` ENABLE KEYS */;

#
# Structure for table "gee_adminuser"
#

DROP TABLE IF EXISTS `gee_adminuser`;
CREATE TABLE `gee_adminuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `phone` varchar(255) NOT NULL COMMENT '手机号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `salt` varchar(255) DEFAULT NULL COMMENT '密码盐值',
  `ip` varchar(255) NOT NULL COMMENT 'ip',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `last_login_time` int(11) NOT NULL COMMENT '最后登录时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '员工状态 0:正常 1锁定',
  `name` varchar(255) NOT NULL COMMENT '员工姓名',
  `group_id` int(11) NOT NULL COMMENT '员工组',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='员工表';

#
# Data for table "gee_adminuser"
#

/*!40000 ALTER TABLE `gee_adminuser` DISABLE KEYS */;
INSERT INTO `gee_adminuser` VALUES (1,'admin','xiao.song@qiduo.net','13789398868','$2y$11$cub.Y9NiD6OhSrWo/q.TsegCVglCDoP7Mg6GLuVMuE.mBA5xGBVPa','','::1',1557120679,1578979052,1578979052,'0','超级管理员',8),(2,'ylsq','975827527@qq.com','13789398868','$2y$11$VRC6vfIdRv0FsJNnC80sJOKrttqnnMJQH.U2/5fI4lpT7eVAqbV.W',NULL,'::1',1557385353,1558062805,1558062805,'0','詹孝松',8);
/*!40000 ALTER TABLE `gee_adminuser` ENABLE KEYS */;

#
# Structure for table "gee_annexconfig"
#

DROP TABLE IF EXISTS `gee_annexconfig`;
CREATE TABLE `gee_annexconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('0','ftp','bos','qiniu','oss') NOT NULL DEFAULT '0' COMMENT '附件管理类型',
  `ftp_sever` varchar(255) DEFAULT NULL COMMENT 'FTP服务器(文件读取地址)',
  `ftp_name` varchar(255) DEFAULT NULL COMMENT 'FTP账号',
  `ftp_pwd` varchar(255) DEFAULT NULL COMMENT 'FTP密码',
  `ftp_port` varchar(255) DEFAULT NULL COMMENT 'FTP端口号',
  `ftp_pasv` enum('0','1') DEFAULT '0' COMMENT '是否开启被动模式 0:不开启 1:开启',
  `ftp_ssl` enum('0','1') DEFAULT '0' COMMENT '是否开启ssl链接 0:不开启 1:开启',
  `ftp_timeout` int(11) DEFAULT '60' COMMENT '超时时间 默认60 单位s',
  `ftp_remoteroor` varchar(255) DEFAULT NULL COMMENT '图片服务器根目录',
  `bos_ak` varchar(255) DEFAULT NULL COMMENT '百度云存储AK',
  `bos_sk` varchar(255) DEFAULT NULL COMMENT '百度云存储sk',
  `bos_bucket` varchar(255) DEFAULT NULL COMMENT '百度云存储bucket',
  `bos_domain` varchar(255) DEFAULT NULL COMMENT '百度云存储绑定域名',
  `qiniu_ak` varchar(255) DEFAULT NULL COMMENT '七牛云存储AK',
  `qiniu_sk` varchar(255) DEFAULT NULL COMMENT '七牛云存储SK',
  `qiniu_bucket` varchar(255) DEFAULT NULL COMMENT '七牛云存储bucket',
  `qiniu_domain` varchar(255) DEFAULT NULL COMMENT '七牛云存储绑定域名',
  `oss_ak` varchar(255) DEFAULT NULL COMMENT '阿里云OSS存储AK',
  `oss_sk` varchar(255) DEFAULT NULL COMMENT '阿里云OSS存储SK',
  `oss_bucket` varchar(255) DEFAULT NULL COMMENT '阿里云OSS存储bucket',
  `oss_domain` varchar(255) DEFAULT NULL COMMENT '阿里云OSS存储绑定域名',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_annexconfig"
#

/*!40000 ALTER TABLE `gee_annexconfig` DISABLE KEYS */;
INSERT INTO `gee_annexconfig` VALUES (1,'bos','','','','','0','0',0,'','','','','','','','','','','','','',1558084404,1558331556);
/*!40000 ALTER TABLE `gee_annexconfig` ENABLE KEYS */;

#
# Structure for table "gee_billing"
#

DROP TABLE IF EXISTS `gee_billing`;
CREATE TABLE `gee_billing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL COMMENT '编号',
  `order_number` varchar(255) NOT NULL COMMENT '订单号',
  `pro_list` text NOT NULL COMMENT '购买产品合计 0:账户充值',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` enum('0','1','2','3','4') NOT NULL DEFAULT '0' COMMENT '交易类型 0:消费 1:充值 2:提现 3:退款 4:产品交易',
  `order_type` text NOT NULL COMMENT '订单类型 0:不是订单 1:购买 2:续费 3:升级',
  `money` double(255,2) NOT NULL COMMENT '交易金额',
  `balance` double(255,2) NOT NULL COMMENT '交易后余额',
  `cash` double(255,2) DEFAULT NULL COMMENT '现金',
  `channel_type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '渠道类型 0:账户余额 1:第三方支付',
  `remarks` text COMMENT '订单备注',
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '交易状态 0:未支付 1:已支付 2:已取消',
  `order_status` enum('0','1','2','3','4') NOT NULL DEFAULT '0' COMMENT '订单状态 0:不是订单 1:已支付 2:待支付 3:已取消 4:已作废',
  `is_invoice` enum('0','1') DEFAULT '0' COMMENT '是否可开发票 0:不可 1:可开',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=142 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_billing"
#

/*!40000 ALTER TABLE `gee_billing` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_billing` ENABLE KEYS */;

#
# Structure for table "gee_domain"
#

DROP TABLE IF EXISTS `gee_domain`;
CREATE TABLE `gee_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `domainname` varchar(255) DEFAULT NULL COMMENT '域名',
  `userid` text COMMENT '联系人ID',
  `domainpass` varchar(255) DEFAULT NULL COMMENT '域名密码',
  `isname` varchar(255) DEFAULT NULL COMMENT '是否为姓名域名 0:普通域名 1:姓名域名',
  `dns` text COMMENT 'DNS服务器 [{dns1: ""},{dns2:""}]',
  `status` int(11) DEFAULT NULL COMMENT '域名状态 0:待审核 1:审核中 2:正常 3:审核未通过',
  `r_status` int(11) DEFAULT NULL COMMENT '备案状态 0:未备案 1:备案审核中 2:正常 3:审核未通过',
  `r_state` varchar(255) DEFAULT NULL COMMENT '域名状态 具体看美橙文档',
  `d_state` varchar(255) DEFAULT NULL COMMENT '域名命名状态 具体看美橙文档',
  `domaintype` varchar(255) DEFAULT NULL COMMENT '域名状态 具体看美橙文档',
  `newstas` int(11) DEFAULT NULL COMMENT '域名隐私保护 0:关闭 1:开启',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_domain"
#

/*!40000 ALTER TABLE `gee_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_domain` ENABLE KEYS */;

#
# Structure for table "gee_domain_cndns"
#

DROP TABLE IF EXISTS `gee_domain_cndns`;
CREATE TABLE `gee_domain_cndns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `userid` int(11) DEFAULT NULL COMMENT '联系人ID',
  `domainname` text COMMENT '域名',
  `years` int(11) DEFAULT NULL COMMENT '注册年限',
  `domainpass` text COMMENT '域名密码',
  `isname` int(11) DEFAULT NULL COMMENT '是否为姓名域名 0:普通域名 1:姓名域名',
  `dns` text COMMENT 'DNS服务器 [{dns1: ""},{dns2:""}]',
  `status` int(11) DEFAULT NULL COMMENT '域名状态 0:待审核 1:审核中 2:正常 3:审核未通过',
  `r_status` int(11) DEFAULT NULL COMMENT '备案状态 0:未备案 1:备案审核中 2:正常 3:审核未通过',
  `newstas` int(11) DEFAULT NULL COMMENT '域名隐私保护 0:关闭 1:开启',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_domain_cndns"
#

/*!40000 ALTER TABLE `gee_domain_cndns` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_domain_cndns` ENABLE KEYS */;

#
# Structure for table "gee_domain_contact"
#

DROP TABLE IF EXISTS `gee_domain_contact`;
CREATE TABLE `gee_domain_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_en` varchar(255) DEFAULT NULL COMMENT '域名所有者(英文) 至少包含一个空格',
  `lastname_en` varchar(255) DEFAULT NULL COMMENT '姓(英文)',
  `firstname_en` varchar(255) DEFAULT NULL COMMENT '名(英文)',
  `country_en` varchar(255) DEFAULT NULL COMMENT '国家代码(英文)  中国 CN',
  `state_en` varchar(255) DEFAULT NULL COMMENT '省份(英文)',
  `city_en` varchar(255) DEFAULT NULL COMMENT '城市(英文)',
  `address_en` varchar(255) DEFAULT NULL COMMENT '地址(英文)',
  `company_cn` varchar(255) DEFAULT NULL COMMENT '域名所有者(中文) 企业长度至少6位 个人长度至少2位  (1汉字=2位)',
  `lastname_cn` varchar(255) DEFAULT NULL COMMENT '姓(中文)',
  `firstname_cn` varchar(255) DEFAULT NULL COMMENT '名(中文)',
  `country_cn` varchar(255) DEFAULT NULL COMMENT '国家代码(中文)',
  `state_cn` varchar(255) DEFAULT NULL COMMENT '省份(中文)',
  `city_cn` varchar(255) DEFAULT NULL COMMENT '城市(中文)',
  `address_cn` varchar(255) DEFAULT NULL COMMENT '联系地址(中文)',
  `zipcode` varchar(255) DEFAULT NULL COMMENT '邮编',
  `phone` varchar(255) DEFAULT NULL COMMENT '电话',
  `fax` varchar(255) DEFAULT NULL COMMENT '传真',
  `email` varchar(255) DEFAULT NULL COMMENT '电子邮箱',
  `usertype` varchar(255) DEFAULT NULL COMMENT '域名类型 (O:企业  I:个人)',
  `idtype` varchar(255) DEFAULT NULL COMMENT '证件类型',
  `idnumber` varchar(255) DEFAULT NULL COMMENT '证件号码',
  `ischecked` int(11) DEFAULT NULL COMMENT '审核状态 0:未审核 1:待审核 2:通过 3:失败 5:未上传资料 6:黑名单 8:上传中',
  `status` varchar(255) DEFAULT NULL COMMENT '状态 1:正常 2:禁用',
  `contact_id` text COMMENT '联系人模板ID',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_domain_contact"
#

/*!40000 ALTER TABLE `gee_domain_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_domain_contact` ENABLE KEYS */;

#
# Structure for table "gee_domain_price"
#

DROP TABLE IF EXISTS `gee_domain_price`;
CREATE TABLE `gee_domain_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` text COMMENT '域名后缀',
  `origin_price` double(11,2) DEFAULT NULL COMMENT '原价(元/年)',
  `price` double(11,2) DEFAULT NULL COMMENT '促销价(元/年)',
  `recharge` double(11,2) DEFAULT NULL COMMENT '续费价(元/年)',
  `transferin` double(11,2) DEFAULT NULL COMMENT '转入价(元/年)',
  `twelvemonth` double(11,2) DEFAULT NULL COMMENT '一年价',
  `biennia` double(11,2) DEFAULT NULL COMMENT '两年价',
  `triennium` double(11,2) DEFAULT NULL COMMENT '三年价',
  `quadrennium` double(11,2) DEFAULT NULL COMMENT '四年价',
  `lustrum` double(11,2) DEFAULT NULL COMMENT '五年价',
  `decade` double(11,2) DEFAULT NULL COMMENT '十年价',
  `tag` varchar(255) DEFAULT NULL COMMENT '标签',
  `description` text COMMENT '描述',
  `pro_id` int(11) DEFAULT NULL COMMENT '所使用产品ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_domain_price"
#

/*!40000 ALTER TABLE `gee_domain_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_domain_price` ENABLE KEYS */;

#
# Structure for table "gee_emailconfig"
#

DROP TABLE IF EXISTS `gee_emailconfig`;
CREATE TABLE `gee_emailconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL COMMENT 'SMTP服务器',
  `port` varchar(255) NOT NULL COMMENT 'SMTP端口',
  `username` varchar(255) NOT NULL COMMENT 'SMTP用户名',
  `password` varchar(255) NOT NULL COMMENT 'SMTP密码',
  `secure` varchar(255) NOT NULL COMMENT 'SMTP验证方式',
  `email` varchar(255) NOT NULL COMMENT 'SMTP发件人信箱',
  `emailname` varchar(255) NOT NULL COMMENT 'SMTP发件人姓名',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_emailconfig"
#

/*!40000 ALTER TABLE `gee_emailconfig` DISABLE KEYS */;
INSERT INTO `gee_emailconfig` VALUES (1,'','','','','','','',1557975644,1557991905);
/*!40000 ALTER TABLE `gee_emailconfig` ENABLE KEYS */;

#
# Structure for table "gee_homeroute"
#

DROP TABLE IF EXISTS `gee_homeroute`;
CREATE TABLE `gee_homeroute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '路由名称',
  `code` varchar(255) NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `level` int(11) DEFAULT NULL COMMENT '路由等级',
  `icon` varchar(255) DEFAULT NULL COMMENT '路由图标',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否展示到列表中 0:不展示 1:展示',
  `is_customize` enum('0','1') DEFAULT '0' COMMENT '是否为自定义导航展示 0:不是 1:是',
  `is_pro` int(11) DEFAULT '0' COMMENT '所属产品类型',
  `is_plug` varchar(255) DEFAULT NULL COMMENT '所属插件',
  `remark` text COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_homeroute"
#

/*!40000 ALTER TABLE `gee_homeroute` DISABLE KEYS */;
INSERT INTO `gee_homeroute` VALUES (1,'首页','index',0,NULL,NULL,'1','0',0,NULL,NULL,0,0),(2,'产品列表','product',0,NULL,NULL,'1','0',0,NULL,NULL,0,0),(3,'VPS产品','vps',2,NULL,NULL,'1','0',0,NULL,NULL,0,0);
/*!40000 ALTER TABLE `gee_homeroute` ENABLE KEYS */;

#
# Structure for table "gee_invoice"
#

DROP TABLE IF EXISTS `gee_invoice`;
CREATE TABLE `gee_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '申请用户',
  `number` varchar(255) NOT NULL COMMENT '发票号',
  `title` varchar(255) NOT NULL COMMENT '发票抬头',
  `money` double(255,2) NOT NULL COMMENT '发票金额',
  `content` text COMMENT '发票内容',
  `type` enum('0','1') DEFAULT '0' COMMENT '发票类型 0:普通发票 1:增值税专用发票',
  `status` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '发票状态 0:审核中 1:已寄出 2:已取消 3:未通过',
  `express` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `remark` text COMMENT '发票备注',
  `n_type` int(11) DEFAULT NULL COMMENT '普票类型 0:个人普票 1:企业类普票',
  `taxpayerno` varchar(255) DEFAULT NULL COMMENT '纳税人识别号',
  `bank` varchar(255) DEFAULT NULL COMMENT '开户银行名称',
  `bankuser` varchar(255) DEFAULT NULL COMMENT '开户账号',
  `address` text COMMENT '开户银行地址',
  `tel` varchar(255) DEFAULT NULL COMMENT '所留电话',
  `addr_name` varchar(255) DEFAULT NULL COMMENT '收货人姓名',
  `addr_region` varchar(255) DEFAULT NULL COMMENT '收货人所在地区',
  `addr_address` varchar(255) DEFAULT NULL COMMENT '收货人街道地址',
  `addr_code` varchar(255) DEFAULT NULL COMMENT '收货人邮政编码',
  `addr_tel` varchar(255) DEFAULT NULL COMMENT '收货人联系电话',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_invoice"
#

/*!40000 ALTER TABLE `gee_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_invoice` ENABLE KEYS */;

#
# Structure for table "gee_invoice_addr"
#

DROP TABLE IF EXISTS `gee_invoice_addr`;
CREATE TABLE `gee_invoice_addr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '收件人姓名',
  `province` int(11) DEFAULT NULL COMMENT '省份',
  `city` int(11) DEFAULT NULL COMMENT '城市',
  `district` int(11) DEFAULT NULL COMMENT '区域',
  `region` text COMMENT '所在地区 省+市+区总合',
  `address` text COMMENT '详细地址',
  `code` varchar(255) DEFAULT NULL COMMENT '邮政编码',
  `tel` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `is_defualt` int(11) DEFAULT NULL COMMENT '是否为默认地址 0:不是 1:是',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_invoice_addr"
#

/*!40000 ALTER TABLE `gee_invoice_addr` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_invoice_addr` ENABLE KEYS */;

#
# Structure for table "gee_invoice_info"
#

DROP TABLE IF EXISTS `gee_invoice_info`;
CREATE TABLE `gee_invoice_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '发票类型 0:增值税普通发票 1:增值税专用发票',
  `n_type` int(11) DEFAULT NULL COMMENT '普票类型 0:个人普票 1:企业类普票',
  `title` text COMMENT '发票抬头',
  `taxpayerno` text COMMENT '纳税人识别号',
  `bank` varchar(255) DEFAULT NULL COMMENT '开户银行名称',
  `bankuser` varchar(255) DEFAULT NULL COMMENT '开户账号',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `tel` varchar(255) DEFAULT NULL COMMENT '电话',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_invoice_info"
#

/*!40000 ALTER TABLE `gee_invoice_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_invoice_info` ENABLE KEYS */;

#
# Structure for table "gee_log"
#

DROP TABLE IF EXISTS `gee_log`;
CREATE TABLE `gee_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '日志内容',
  `ip` varchar(255) NOT NULL COMMENT '操作IP',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=703 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_log"
#

/*!40000 ALTER TABLE `gee_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_log` ENABLE KEYS */;

#
# Structure for table "gee_msgmodel"
#

DROP TABLE IF EXISTS `gee_msgmodel`;
CREATE TABLE `gee_msgmodel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('0','1','2','3') NOT NULL DEFAULT '0' COMMENT '模板类型 0:短信验证码 1:短信通知  2:邮件验证码 3:邮件通知',
  `mark` varchar(255) NOT NULL COMMENT '模板标识',
  `name` varchar(255) NOT NULL COMMENT '模板名称',
  `content` text NOT NULL COMMENT '模板内容',
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '模板状态 0:可用 1:禁用',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_msgmodel"
#

/*!40000 ALTER TABLE `gee_msgmodel` DISABLE KEYS */;
INSERT INTO `gee_msgmodel` VALUES (1,'3','defaultEmail','默认测试邮件','这是一个默认邮件模板{basic_name}|{basic_email}|{basic_url}|{basic_logo}|{basic_icp}|{basic_beian}|{basic_idc}|{basic_isp}|{basic_qwejo}|{email_code}','0',1557988637,1558062883);
/*!40000 ALTER TABLE `gee_msgmodel` ENABLE KEYS */;

#
# Structure for table "gee_order"
#

DROP TABLE IF EXISTS `gee_order`;
CREATE TABLE `gee_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL COMMENT '订单号',
  `pro_id` int(11) NOT NULL COMMENT '产品ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` enum('0','1','2','3','4') NOT NULL DEFAULT '0' COMMENT '订单类型 0:消费 1:充值 2:提现 3:退款 4:产品交易',
  `remarks` text COMMENT '订单备注',
  `money` double(255,2) NOT NULL COMMENT '交易金额',
  `balance` double(255,2) NOT NULL COMMENT '交易后余额',
  `product` text COMMENT '交易产品合计',
  `cash` double(255,2) DEFAULT NULL COMMENT '现金支付',
  `channel_type` enum('0','1') DEFAULT '0' COMMENT '渠道类型 0:余额支付 1:第三方支付',
  `status` enum('0','1','2') DEFAULT '0' COMMENT '交易状态 0:未支付 1:已支付 2:支付失败',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_order"
#

/*!40000 ALTER TABLE `gee_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_order` ENABLE KEYS */;

#
# Structure for table "gee_osgroup"
#

DROP TABLE IF EXISTS `gee_osgroup`;
CREATE TABLE `gee_osgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '操作系统名称',
  `uname` varchar(255) DEFAULT NULL COMMENT '用户名',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_osgroup"
#

/*!40000 ALTER TABLE `gee_osgroup` DISABLE KEYS */;
INSERT INTO `gee_osgroup` VALUES (1,'CentOs','root',9,1574661455,1574672282),(2,'Windows','Administrator',0,1574663512,1574672295),(3,'Ubuntu','ubuntu',0,1574672319,1574672319);
/*!40000 ALTER TABLE `gee_osgroup` ENABLE KEYS */;

#
# Structure for table "gee_ostype"
#

DROP TABLE IF EXISTS `gee_ostype`;
CREATE TABLE `gee_ostype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '操作系统版本名称',
  `group_id` int(11) DEFAULT NULL COMMENT '所属操作系统',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_ostype"
#

/*!40000 ALTER TABLE `gee_ostype` DISABLE KEYS */;
INSERT INTO `gee_ostype` VALUES (1,'CentOs 7.6 ',1,0,1574661500,1574661562),(2,'CentOs 7.5 ',1,0,1574661588,1574661588),(3,'Windows Server 2012 R2 数据中心版 64位英文版',2,0,1574663546,1574663582),(4,'Windows Server 2012 R2 数据中心版 64位中文版',2,0,1574663555,1574663592),(5,'Windows Server 2012 R2 数据中心版 64位',2,0,1574663561,1574732562),(6,'Ubuntu Server 18.04.1 LTS 64位',3,0,1574672328,1574672328),(7,'Ubuntu Server 16.04.1 LTS 64位',3,0,1574672338,1574672338);
/*!40000 ALTER TABLE `gee_ostype` ENABLE KEYS */;

#
# Structure for table "gee_picture"
#

DROP TABLE IF EXISTS `gee_picture`;
CREATE TABLE `gee_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text COMMENT '图片链接',
  `sha1` text COMMENT 'sha1',
  `md5` text COMMENT 'md5',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_picture"
#

/*!40000 ALTER TABLE `gee_picture` DISABLE KEYS */;
INSERT INTO `gee_picture` VALUES (1,'https://ylsq.cdn.bcebos.com/1558341347744682.png','','',1558341347,1558341347),(2,'https://ylsq.cdn.bcebos.com/1558341399879898.png','','',1558341399,1558341399),(3,'https://ylsq.cdn.bcebos.com/1558341751672715.jpeg','','',1558341752,1558341752),(4,'https://ylsq.cdn.bcebos.com/1572935071179500.png','','',1572935071,1572935071),(5,'https://ylsq.cdn.bcebos.com/1572937554456840.png','','',1572937554,1572937554),(6,'https://ylsq.cdn.bcebos.com/1572937669828394.png','','',1572937670,1572937670),(7,'https://ylsq.cdn.bcebos.com/1572937697818693.png','','',1572937698,1572937698),(8,'https://ylsq.cdn.bcebos.com/1572941554904025.png','','',1572941554,1572941554),(9,'https://ylsq.cdn.bcebos.com/1572941555174641.png','','',1572941555,1572941555),(10,'https://ylsq.cdn.bcebos.com/1572942455565577.png','','',1572942455,1572942455),(11,'https://ylsq.cdn.bcebos.com/1572942479574173.png','','',1572942479,1572942479),(12,'https://ylsq.cdn.bcebos.com/1572942805484442.png','','',1572942805,1572942805),(13,'https://ylsq.cdn.bcebos.com/1572943078292983.png','','',1572943078,1572943078),(14,'https://ylsq.cdn.bcebos.com/157294373239425.png','','',1572943732,1572943732),(15,'https://ylsq.cdn.bcebos.com/1572944416711090.png','','',1572944416,1572944416),(16,'https://ylsq.cdn.bcebos.com/1572944519180179.png','','',1572944520,1572944520),(17,'https://ylsq.cdn.bcebos.com/1572944606199115.png','','',1572944606,1572944606),(18,'https://ylsq.cdn.bcebos.com/1572945257160972.png','','',1572945257,1572945257),(19,'https://ylsq.cdn.bcebos.com/157294630642593.png','','',1572946307,1572946307),(20,'https://ylsq.cdn.bcebos.com/157294631198743.png','','',1572946311,1572946311),(21,'https://ylsq.cdn.bcebos.com/1572948455678960.png','','',1572948455,1572948455),(22,'https://ylsq.cdn.bcebos.com/1572948458902518.png','','',1572948458,1572948458),(23,'https://ylsq.cdn.bcebos.com/1573004829365866.png','','',1573004830,1573004830),(24,'https://ylsq.cdn.bcebos.com/1573008335591469.png','','',1573008336,1573008336),(25,'https://ylsq.cdn.bcebos.com/1573020883357834.png','','',1573020883,1573020883),(26,'https://ylsq.cdn.bcebos.com/1573025860842735.png','','',1573025861,1573025861),(27,'https://ylsq.cdn.bcebos.com/1573026041952018.png','','',1573026042,1573026042),(28,'https://ylsq.cdn.bcebos.com/1573027041893797.png','','',1573027041,1573027041),(29,'https://ylsq.cdn.bcebos.com/1573027378568645.png','','',1573027378,1573027378),(30,'https://ylsq.cdn.bcebos.com/1575278057769276.jpeg','','',1575278058,1575278058),(31,'https://ylsq.cdn.bcebos.com/1575278102808462.jpeg','','',1575278103,1575278103),(32,'https://ylsq.cdn.bcebos.com/1575278271764929.jpeg','','',1575278271,1575278271),(33,'https://ylsq.cdn.bcebos.com/1575278347739953.jpeg','','',1575278348,1575278348),(34,'https://ylsq.cdn.bcebos.com/1575280241595859.jpeg','','',1575280241,1575280241),(35,'https://ylsq.cdn.bcebos.com/1575280553395250.jpeg','','',1575280554,1575280554),(36,'https://ylsq.cdn.bcebos.com/1575342139531096.jpeg','','',1575342140,1575342140),(37,'https://ylsq.cdn.bcebos.com/1575343976713522.jpeg','','',1575343976,1575343976),(38,'https://ylsq.cdn.bcebos.com/1575343989337132.jpeg','','',1575343989,1575343989),(39,'https://ylsq.cdn.bcebos.com/1575344019110553.jpeg','','',1575344019,1575344019),(40,'https://ylsq.cdn.bcebos.com/1575352240744859.jpeg','','',1575352241,1575352241),(41,'https://ylsq.cdn.bcebos.com/1575352390530427.jpeg','','',1575352391,1575352391),(42,'https://ylsq.cdn.bcebos.com/1575361864209774.jpeg','','',1575361864,1575361864),(43,'https://ylsq.cdn.bcebos.com/157542603642559.jpeg','','',1575426037,1575426037),(44,'https://ylsq.cdn.bcebos.com/1575427586803617.jpeg','','',1575427586,1575427586),(45,'https://ylsq.cdn.bcebos.com/1575427616136494.jpeg','','',1575427618,1575427618),(46,'https://ylsq.cdn.bcebos.com/1575428071976714.jpeg','','',1575428072,1575428072),(47,'https://ylsq.cdn.bcebos.com/1575428074350282.jpeg','','',1575428075,1575428075),(48,'https://ylsq.cdn.bcebos.com/1576663497381689.png','','',1576663498,1576663498),(49,'https://ylsq.cdn.bcebos.com/1576739359122674.png','','',1576739360,1576739360),(50,'https://ylsq.cdn.bcebos.com/1576739547493077.png','','',1576739547,1576739547),(51,'https://ylsq.cdn.bcebos.com/1576739562488948.png','','',1576739562,1576739562),(52,'https://ylsq.cdn.bcebos.com/1576739607123266.png','','',1576739607,1576739607),(53,'https://ylsq.cdn.bcebos.com/157673985627459.png','','',1576739857,1576739857);
/*!40000 ALTER TABLE `gee_picture` ENABLE KEYS */;

#
# Structure for table "gee_pro_config"
#

DROP TABLE IF EXISTS `gee_pro_config`;
CREATE TABLE `gee_pro_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `config` text COMMENT '产品配置',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_pro_config"
#

/*!40000 ALTER TABLE `gee_pro_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_pro_config` ENABLE KEYS */;

#
# Structure for table "gee_product"
#

DROP TABLE IF EXISTS `gee_product`;
CREATE TABLE `gee_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '产品类型 1:虚拟主机  2:VPS主机  3:云服务器  4:SSL证书  5:域名 6:其他',
  `name` varchar(255) NOT NULL COMMENT '产品名称',
  `group_id` int(11) DEFAULT NULL COMMENT '产品分组',
  `describe` text NOT NULL COMMENT '产品描述',
  `email_model` int(11) NOT NULL COMMENT '开通产品时会发送的邮件模板id',
  `label` varchar(255) NOT NULL COMMENT '产品标签',
  `update_list` text COMMENT '可用于升级的套餐',
  `month` double(255,2) DEFAULT NULL COMMENT '月价格',
  `quarter` double(255,2) DEFAULT NULL COMMENT '季度价格',
  `semestrale` double(255,2) DEFAULT NULL COMMENT '半年价格',
  `years` double(255,2) DEFAULT NULL COMMENT '年价格',
  `biennium` double(255,2) DEFAULT NULL COMMENT '两年价格',
  `triennium` double(255,2) DEFAULT NULL COMMENT '三年价格',
  `sort` int(11) DEFAULT NULL COMMENT '产品排序',
  `plug` int(11) DEFAULT NULL COMMENT '所用插件ID',
  `plug_config` text COMMENT '插件配置项',
  `added` text COMMENT '产品增值服务',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_product"
#

/*!40000 ALTER TABLE `gee_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_product` ENABLE KEYS */;

#
# Structure for table "gee_product_class"
#

DROP TABLE IF EXISTS `gee_product_class`;
CREATE TABLE `gee_product_class` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `name` varchar(255) NOT NULL COMMENT '分类标识',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_product_class"
#

/*!40000 ALTER TABLE `gee_product_class` DISABLE KEYS */;
INSERT INTO `gee_product_class` VALUES (1,'虚拟主机','vhost',0,0),(2,'VPS主机','vps',0,0),(3,'云服务器','chost',0,0),(4,'SSL证书','ssl',0,0),(5,'域名','domain',0,0),(8,'物理服务器租用','server',1573543543,1573543551),(9,'云主机','cloudserver',0,0),(999,'其他','other',0,0);
/*!40000 ALTER TABLE `gee_product_class` ENABLE KEYS */;

#
# Structure for table "gee_product_group"
#

DROP TABLE IF EXISTS `gee_product_group`;
CREATE TABLE `gee_product_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '分组名称',
  `slogan` text COMMENT '分组标语',
  `sort` int(11) DEFAULT NULL COMMENT '组排序',
  `class_id` int(11) DEFAULT NULL COMMENT '所属分类',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_product_group"
#

/*!40000 ALTER TABLE `gee_product_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_product_group` ENABLE KEYS */;

#
# Structure for table "gee_product_type"
#

DROP TABLE IF EXISTS `gee_product_type`;
CREATE TABLE `gee_product_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `mark` varchar(255) NOT NULL COMMENT '类型标识',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_product_type"
#

/*!40000 ALTER TABLE `gee_product_type` DISABLE KEYS */;
INSERT INTO `gee_product_type` VALUES (1,'计算','',0,1560152069),(2,'网络','',0,0),(3,'存储和CDN','',0,0),(4,'数据库','',0,0),(5,'安全和管理','',0,0),(6,'数据分析','',0,0),(7,'网站服务','',0,0),(8,'智能多媒体服务','',0,0),(9,'物联网服务','',0,0),(10,'人工智能','',0,0),(11,'数字营销云','',0,0),(12,'区块链','',0,0),(13,'应用服务','',0,0),(14,'云市场','',0,0),(15,'其他','',0,0);
/*!40000 ALTER TABLE `gee_product_type` ENABLE KEYS */;

#
# Structure for table "gee_route"
#

DROP TABLE IF EXISTS `gee_route`;
CREATE TABLE `gee_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '路由名称',
  `code` varchar(255) NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否展示 0:不展示 1:展示',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `icon` varchar(255) DEFAULT NULL COMMENT '路由图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_route"
#

/*!40000 ALTER TABLE `gee_route` DISABLE KEYS */;
INSERT INTO `gee_route` VALUES (1,'服务管理','service',0,'1',1557194766,1557194766,'fa-industry'),(2,'域名管理','domain',1,'1',1557194766,1557194766,'fa-globe'),(3,'主机管理','host',1,'0',1557194766,1557194766,'fa-server'),(4,'租用托管','server',1,'1',1557194766,1557194766,'fa-server'),(5,'工单管理','ticket',0,'1',1557194766,1557194766,' fa-ticket'),(6,'工单列表','list',5,'1',1557194766,1557194766,'fa-list-ul'),(7,'工单分类','group',5,'0',1557194766,1557194766,'fa-tags'),(8,'产品管理','product',0,'1',1557194766,1557194766,'fa-cube'),(9,'云主机产品管理','vps',1,'1',0,0,'fa-tasks'),(13,'区域管理','region',12,'1',1557194766,1557194766,NULL),(14,'线路管理','line',12,'1',1557194766,1557194766,NULL),(15,'系统管理','system',12,'1',1557194766,1557194766,'fa-linux'),(16,'用户管理','user',0,'1',1557194766,1557194766,'fa-user'),(17,'用户列表','list',16,'1',1557194766,1557194766,'fa-list-ul'),(18,'用户分组','group',16,'1',1557194766,1557194766,'fa-tag'),(19,'财务管理','finance',0,'1',1557194766,1557194766,'fa-database'),(20,'总览','index',19,'1',1557194766,1557194766,'fa-pie-chart'),(21,'财务明细','details',19,'1',1557194766,1557194766,'fa-newspaper-o'),(24,'充值记录','recharge',19,'1',1557194766,1557194766,'fa-bar-chart'),(25,'订单列表','order',19,'1',1557194766,1557194766,'fa-reorder'),(26,'发票管理','invoice',19,'1',1557194766,1557194766,'fa-ticket'),(27,'员工管理','staff',0,'1',1557194766,1557194766,'fa-user'),(28,'员工列表','list',27,'1',1557194766,1557194766,'fa-users'),(29,'员工分组','group',27,'1',1557194766,1557194766,'fa-user-secret'),(30,'插件管理','addons',0,'1',1557194766,1557194766,'fa-th'),(31,'插件列表','list',30,'1',1557194766,1557194766,'fa-list'),(32,'详细','details',30,'0',1557194766,1557194766,'fa-newspaper-o'),(33,'应用市场','https://addon.geecp.com',0,'0',1557194766,1557194766,'fa-cube'),(35,'基本信息','basic',999,'1',1557194766,1557194766,'fa-info-circle'),(36,'邮件设置','email',999,'1',1557194766,1557194766,'fa-envelope'),(37,'短信设置','sms',999,'0',1557194766,1557194766,'fa-commenting'),(38,'支付设置','pay',999,'0',1557194766,1557194766,'fa-credit-card'),(39,'消息模板','template',999,'1',1557194766,1557194766,'fa-comments'),(40,'附件设置','annex',999,'1',1557194766,1557194766,'fa-file'),(43,'产品列表','list',8,'1',1557194766,1557194766,'fa-list-ul'),(46,'产品类型','type',8,'1',1557194766,1557194766,'fa-cubes'),(47,'导航管理','routes',0,'0',1557194766,1557194766,'fa-navicon'),(48,'添加导航','add',47,'0',1557194766,1557194766,'fa-navicon'),(49,'添加验证','addAuth',47,'0',1557194766,1557194766,'fa-street-view'),(50,'删除路由','del',47,'0',1557194766,1557194766,NULL),(51,'产品分类','class',8,'1',1557194766,1557194766,'fa-tag'),(52,'添加分类','addclass',8,'0',1557194766,1557194766,NULL),(53,'添加分类验证','addclassAuth',8,'0',1557194766,1557194766,NULL),(54,'删除分类','delclass',8,'0',1557194766,1557194766,NULL),(57,'工单详情','details',5,'0',1557194766,1557194766,'fa-ticket'),(58,'工单回复','reply',5,'0',1557194766,1557194766,'fa-ticket'),(59,'工单接入','join',5,'0',1557194766,1557194766,'fa-ticket'),(60,'增值服务','added',8,'1',1557194766,1557194766,'fa-thumbs-up'),(61,'新增增值服务组','addaddedgroup',8,'0',1557194766,1557194766,NULL),(62,'新增增值服务','addadded',8,'0',1557194766,1557194766,NULL),(63,'新增增值服务组验证','addaddedgroupAuth',8,'0',1557194766,1557194766,NULL),(64,'删除增值服务组','deladdedgroup',8,'0',1557194766,1557194766,NULL),(65,'新增增值服务验证','addaddedAuth',8,'0',1557194766,1557194766,NULL),(66,'删除增值服务','deladded',8,'0',1557194766,1557194766,NULL),(67,'交付物理服务器','delivery',1,'0',0,0,NULL),(68,'交付物理服务器验证','deliveryauth',1,'0',0,0,NULL),(69,'删除订单','delorder',19,'0',0,0,NULL),(71,'操作系统','os',8,'1',0,0,'fa-linux'),(73,'添加操作系统','addosgroup',8,'0',0,0,NULL),(74,'添加操作系统验证','addosgroupAuth',8,'0',0,0,NULL),(75,'删除操作系统','delosgroup',8,'0',0,0,NULL),(76,'添加操作系统版本','addostype',8,'0',0,0,NULL),(77,'添加操作系统验证','addostypeAuth',8,'0',0,0,NULL),(78,'删除操作系统版本','delostype',8,'0',0,0,NULL),(79,'获取物理服务器信息','getserver',1,'0',0,0,NULL),(80,'编辑物理服务器信息','editserver',1,'0',0,0,NULL),(81,'实名认证','realverify',16,'1',0,0,'fa-check'),(82,'通过认证','passreal',16,'0',0,0,NULL),(83,'拒绝认证','rejectreal',16,'0',0,0,NULL),(84,'企业认证','enterpriseverify',16,'1',0,0,'fa-black-tie'),(85,'通过认证','passenterprise',16,'0',0,0,NULL),(86,'拒绝认证','rejectenterprise',16,'0',0,0,NULL),(87,'通过发票申请','passinvoice',19,'0',0,0,NULL),(88,'拒绝发票申请','nopassinvoice',19,'0',0,0,NULL),(89,'编辑发票信息','editinvoice',19,'0',0,0,NULL),(91,'续费VPS','renewvps',1,'0',0,0,NULL),(92,'vps控制面板','vpsmanager',1,'0',0,0,NULL),(95,'域名价格','domainprice',1,'0',0,0,NULL),(96,'信息模板审核','domaintempaudit',1,'0',0,0,NULL),(97,'提交域名价格','adddomainpriceauth',1,'0',0,0,NULL),(98,'域名接口测试','domainchecked',1,'0',0,0,NULL),(99,'云平台','cloudservice',0,'1',0,0,'fa-cloud'),(100,'系统升级','update',99,'1',0,0,'fa-cloud-download'),(101,'注册站点','regsite',99,'0',0,0,NULL),(102,'云服务诊断','diagnose',99,'0',0,0,NULL),(999,'系统设置','system',0,'1',1557194766,1557194766,'fa-cog');
/*!40000 ALTER TABLE `gee_route` ENABLE KEYS */;

#
# Structure for table "gee_server"
#

DROP TABLE IF EXISTS `gee_server`;
CREATE TABLE `gee_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_group_id` int(11) DEFAULT NULL COMMENT '产品分组ID',
  `pro_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `server_added` text COMMENT '增值服务选项',
  `name` varchar(255) DEFAULT NULL COMMENT '主机名称',
  `ip` varchar(255) DEFAULT NULL COMMENT '公网IP',
  `intranetip` varchar(255) DEFAULT NULL COMMENT '内网IP',
  `username` text COMMENT '主机账号',
  `password` text COMMENT '主机密码',
  `osgroup` int(11) DEFAULT NULL COMMENT '操作系统类型',
  `ostype` int(11) DEFAULT NULL COMMENT '操作系统版本',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `remake` text COMMENT '备注',
  `status` int(11) DEFAULT NULL COMMENT '主机状态 0:开通中 1:已到期 2:正在重装系统 3:正在运行 4:服务器异常',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) DEFAULT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_server"
#

/*!40000 ALTER TABLE `gee_server` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_server` ENABLE KEYS */;

#
# Structure for table "gee_server_added"
#

DROP TABLE IF EXISTS `gee_server_added`;
CREATE TABLE `gee_server_added` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '增值服务标识',
  `title` varchar(255) DEFAULT NULL COMMENT '增值服务名称',
  `slogan` text COMMENT '增值服务描述',
  `type` int(11) DEFAULT NULL COMMENT '增值服务类型 1:单选 2:下拉 3输入框',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_server_added"
#

/*!40000 ALTER TABLE `gee_server_added` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_server_added` ENABLE KEYS */;

#
# Structure for table "gee_server_added_items"
#

DROP TABLE IF EXISTS `gee_server_added_items`;
CREATE TABLE `gee_server_added_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '增值服务名称',
  `group_id` int(11) DEFAULT NULL COMMENT '所属服务ID',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `month` double(255,2) DEFAULT NULL COMMENT '月价格',
  `quarter` double(255,0) DEFAULT NULL COMMENT '季度价格',
  `semestrale` double(255,0) DEFAULT NULL COMMENT '半年价格',
  `years` double(255,0) DEFAULT NULL COMMENT '年价格',
  `value` text COMMENT '值',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_server_added_items"
#

/*!40000 ALTER TABLE `gee_server_added_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_server_added_items` ENABLE KEYS */;

#
# Structure for table "gee_staffgroup"
#

DROP TABLE IF EXISTS `gee_staffgroup`;
CREATE TABLE `gee_staffgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '组名称',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_staffgroup"
#

/*!40000 ALTER TABLE `gee_staffgroup` DISABLE KEYS */;
INSERT INTO `gee_staffgroup` VALUES (8,'超级管理员',1557380521,1557380521),(9,'财务审核',1557380533,1557380533),(10,'销售',1557380542,1557380549);
/*!40000 ALTER TABLE `gee_staffgroup` ENABLE KEYS */;

#
# Structure for table "gee_system_info"
#

DROP TABLE IF EXISTS `gee_system_info`;
CREATE TABLE `gee_system_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `versions` varchar(255) DEFAULT NULL COMMENT '系统版本',
  `charset` text COMMENT '字符集',
  `devinfo` text COMMENT '开发商信息',
  `officesite` text COMMENT '官方网站',
  `qq` varchar(255) DEFAULT NULL COMMENT 'QQ群',
  `tel` varchar(255) DEFAULT NULL COMMENT '客服电话',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间  可当作更新时间用',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_system_info"
#

/*!40000 ALTER TABLE `gee_system_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_system_info` ENABLE KEYS */;

#
# Structure for table "gee_ticket"
#

DROP TABLE IF EXISTS `gee_ticket`;
CREATE TABLE `gee_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromid` int(11) DEFAULT NULL COMMENT '提交用户id',
  `replierid` int(11) DEFAULT NULL COMMENT '接收人id',
  `num` varchar(255) DEFAULT NULL COMMENT '工单编号',
  `title` varchar(255) DEFAULT NULL COMMENT '工单标题',
  `content` text COMMENT '工单描述',
  `imgs` text COMMENT '相关截图',
  `status` int(11) DEFAULT '0' COMMENT '工单状态 0:待接入 1:处理中 2:待回复 3:待您确认 4:已撤销 5:已完成',
  `type` varchar(255) DEFAULT NULL COMMENT '工单类型 ',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_ticket"
#

/*!40000 ALTER TABLE `gee_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_ticket` ENABLE KEYS */;

#
# Structure for table "gee_ticket_details"
#

DROP TABLE IF EXISTS `gee_ticket_details`;
CREATE TABLE `gee_ticket_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) DEFAULT NULL COMMENT '所属工单id',
  `title` varchar(255) DEFAULT NULL COMMENT '工单标题',
  `content` text COMMENT '回复内容',
  `imgs` text COMMENT '相关截图',
  `fromid` int(11) DEFAULT NULL COMMENT '发言人ID',
  `replierid` int(11) DEFAULT NULL COMMENT '接收人ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_ticket_details"
#

/*!40000 ALTER TABLE `gee_ticket_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_ticket_details` ENABLE KEYS */;

#
# Structure for table "gee_user"
#

DROP TABLE IF EXISTS `gee_user`;
CREATE TABLE `gee_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '用户名(英文)',
  `password` text COMMENT '密码',
  `salt` text COMMENT '加密盐值',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `is_email` enum('0','1') DEFAULT '0' COMMENT '邮箱验证 0:未认证 1:已认证',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `is_phone` enum('0','1') DEFAULT '0' COMMENT '手机验证 0:未认证 1:已认证',
  `tel` varchar(255) DEFAULT NULL COMMENT '固话',
  `type` enum('0','1') NOT NULL DEFAULT '0' COMMENT '用户类型 0:个人 1:企业',
  `balance` double(255,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `invoice_money` double(11,0) DEFAULT '0' COMMENT '已开票金额',
  `free_money` double(255,0) DEFAULT '0' COMMENT '开票冻结金额',
  `create_ip` varchar(255) DEFAULT NULL COMMENT '注册IP',
  `group_id` int(11) DEFAULT NULL COMMENT '用户组ID',
  `approve` enum('0','1') DEFAULT '0' COMMENT '用户认证 0:未认证 1:已认证',
  `realname` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `idcard` varchar(255) DEFAULT NULL COMMENT '身份证号',
  `realverify` int(11) DEFAULT NULL COMMENT '认证审核 0:未提交申请 1:审核中 2:审核成功 3: 审核失败',
  `status` enum('0','1','2') DEFAULT '0' COMMENT '用户状态 0:正常 1:欠费 2:锁定',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `create_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户表';

#
# Data for table "gee_user"
#

/*!40000 ALTER TABLE `gee_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_user` ENABLE KEYS */;

#
# Structure for table "gee_user_enterprise"
#

DROP TABLE IF EXISTS `gee_user_enterprise`;
CREATE TABLE `gee_user_enterprise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '组织类型 0:企业 1:其他组织',
  `name` varchar(255) DEFAULT NULL COMMENT '企业名称/字号名称',
  `code` varchar(255) DEFAULT NULL COMMENT '营业执照注册号/组织机构代码',
  `pic` varchar(255) DEFAULT NULL COMMENT '营业执照扫描件/组织机构代码证扫描件',
  `is_individual` int(11) DEFAULT NULL COMMENT '是否为个体工商户  0:否 1:是',
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `status` int(11) DEFAULT NULL COMMENT '审核状态  0:审核中 1:审核成功 2:审核失败',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_user_enterprise"
#

/*!40000 ALTER TABLE `gee_user_enterprise` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_user_enterprise` ENABLE KEYS */;

#
# Structure for table "gee_user_realnames"
#

DROP TABLE IF EXISTS `gee_user_realnames`;
CREATE TABLE `gee_user_realnames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `rname` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `idcard` varchar(255) DEFAULT NULL COMMENT '身份证',
  `verify` int(255) DEFAULT NULL COMMENT '审核状态 0:审核中 1:审核通过',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_user_realnames"
#

/*!40000 ALTER TABLE `gee_user_realnames` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_user_realnames` ENABLE KEYS */;

#
# Structure for table "gee_usergroup"
#

DROP TABLE IF EXISTS `gee_usergroup`;
CREATE TABLE `gee_usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '组名称',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_usergroup"
#

/*!40000 ALTER TABLE `gee_usergroup` DISABLE KEYS */;
INSERT INTO `gee_usergroup` VALUES (1,'普通用户',1557364855,1557364855),(2,'青铜代理',1557364810,1557364810),(3,'白银代理',1557364818,1557364818),(4,'黄金代理',1557364828,1557364828),(5,'铂金代理',1557364839,1557364839),(6,'钻石代理',1557364845,1557364845);
/*!40000 ALTER TABLE `gee_usergroup` ENABLE KEYS */;

#
# Structure for table "gee_vhost"
#

DROP TABLE IF EXISTS `gee_vhost`;
CREATE TABLE `gee_vhost` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `service_id` int(11) NOT NULL COMMENT '所属服务',
  `status` enum('0','1','2','-1','-2','-4') DEFAULT '0' COMMENT '主机状态 0:运行 1:暂停 2:管理员停止 -1:未开设 -2:已经删除 -4:状态未确定',
  `paytype` enum('0','1','2','3') DEFAULT '0' COMMENT '支付方式 0:余额支付 1:支付宝支付 2:微信支付 3:现金支付',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  `product_id` int(11) DEFAULT NULL COMMENT '产品id',
  `domain` text COMMENT '绑定域名',
  `ftp_name` varchar(255) DEFAULT NULL COMMENT 'ftp用户名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_vhost"
#

/*!40000 ALTER TABLE `gee_vhost` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_vhost` ENABLE KEYS */;

#
# Structure for table "gee_vps"
#

DROP TABLE IF EXISTS `gee_vps`;
CREATE TABLE `gee_vps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '所属用户',
  `plug_id` int(11) DEFAULT NULL COMMENT '所属插件id',
  `plug_type` varchar(255) DEFAULT NULL COMMENT '所属插件数据表',
  `plug_name` varchar(255) DEFAULT NULL COMMENT '插件名称',
  `pro_id` int(11) DEFAULT NULL COMMENT '产品id',
  `status` varchar(255) DEFAULT NULL COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_vps"
#

/*!40000 ALTER TABLE `gee_vps` DISABLE KEYS */;
/*!40000 ALTER TABLE `gee_vps` ENABLE KEYS */;

#
# Structure for table "gee_webbasic"
#

DROP TABLE IF EXISTS `gee_webbasic`;
CREATE TABLE `gee_webbasic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '网站名称',
  `email` varchar(255) NOT NULL COMMENT '联系邮箱',
  `url` text NOT NULL COMMENT '网站首页域名',
  `logo` text NOT NULL COMMENT 'logo url地址',
  `logo1` text COMMENT '深色LOGO',
  `favicon` text COMMENT 'favicon',
  `description` text COMMENT '网站描述',
  `keywords` text COMMENT '网站SEO关键字',
  `icp` text COMMENT 'ICP备案',
  `beian` text COMMENT '网安备案',
  `idc` text COMMENT 'IDC备案',
  `isp` text COMMENT 'ISP备案',
  `maintain` enum('0','1') DEFAULT '0' COMMENT '维护模式 0:关闭 1:开启',
  `maintaininfo` text COMMENT '维护模式下首页展示内容(可为html)',
  `code` text COMMENT '第三方统计代码(内容为html)',
  `account_name` varchar(255) DEFAULT NULL COMMENT '开户名称(线下汇款用)',
  `bank_name` varchar(255) DEFAULT NULL COMMENT '开户银行(线下汇款用)',
  `account_number` varchar(255) DEFAULT NULL COMMENT '汇款账号(线下汇款用)',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_webbasic"
#

/*!40000 ALTER TABLE `gee_webbasic` DISABLE KEYS */;
INSERT INTO `gee_webbasic` VALUES (1,'GEECP管理系统','123@123.cc','https://3','https://ylsq.cdn.bcebos.com/1576663497381689.png','https://ylsq.cdn.bcebos.com/157673985627459.png','https://ylsq.cdn.bcebos.com/1576739607123266.png','1','1','1','2','3','4','0','1','1','北京百度网讯科技有限公司','招商银行北京分行上地支行','1109021606104020100157799',1557910511,1576739861);
/*!40000 ALTER TABLE `gee_webbasic` ENABLE KEYS */;

#
# Structure for table "gee_webroute"
#

DROP TABLE IF EXISTS `gee_webroute`;
CREATE TABLE `gee_webroute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '路由名称',
  `code` varchar(255) NOT NULL COMMENT '路由标识',
  `f_id` int(11) NOT NULL COMMENT '上级ID',
  `s_id` int(11) DEFAULT '0' COMMENT '从属ID',
  `level` int(11) DEFAULT NULL COMMENT '路由等级',
  `icon` varchar(255) DEFAULT NULL COMMENT '路由图标',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否展示到列表中 0:不展示 1:展示',
  `is_customize` enum('0','1') DEFAULT '0' COMMENT '是否为自定义导航展示 0:不是 1:是',
  `is_pro` int(11) DEFAULT '0' COMMENT '所属产品类型',
  `is_plug` varchar(255) DEFAULT NULL COMMENT '所属插件',
  `remark` text COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=237 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "gee_webroute"
#

/*!40000 ALTER TABLE `gee_webroute` DISABLE KEYS */;
INSERT INTO `gee_webroute` VALUES (1,'基本信息','basic',0,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(2,'账号信息','info',1,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(3,'安全设置','safety',1,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(4,'实名认证','auth',1,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(6,'云虚拟主机','host',0,NULL,NULL,'icon-cvh','0','0',1,NULL,NULL,1557194766,1557194766),(7,'云主机VPS','vps',0,NULL,NULL,'icon-ecs','1','0',1,NULL,NULL,1557194766,1560335621),(8,'云主机','cloudserver',0,NULL,NULL,'icon-ecs','0','0',1,NULL,NULL,1557194766,1557194766),(9,'域名管理','domain',0,NULL,NULL,'icon-cdm','1','0',1,NULL,NULL,1557194766,1557194766),(10,'云解析','analysis',0,NULL,NULL,'icon-dns','0','0',1,NULL,NULL,1557194766,1557194766),(11,'工单管理','ticket',0,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(12,'工单列表','list',11,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(13,'创建工单','add',11,12,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(14,'消息管理','message',0,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(16,'安全认证','safetyauth',0,NULL,NULL,NULL,'1','0',0,NULL,NULL,1557194766,1557194766),(126,'实例','index',7,NULL,NULL,NULL,'1','0',0,NULL,'VPS实例列表',1559807152,1560335597),(127,'购买产品','buy',0,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(128,'确认订单','confirm',127,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(129,'线上支付','onlinepay',127,NULL,NULL,'','0','0',0,NULL,NULL,1560335597,1560335597),(130,'支付状态','paystatus',127,NULL,NULL,'','0','0',0,NULL,NULL,1560335597,1560335597),(131,'财务中心','billing',0,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(132,'财务总览','overview',131,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(133,'消费中心','consumption',131,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(134,'消费总览','overview',133,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(135,'账单明细','list',133,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(136,'收支明细','dealrecord',131,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(138,'发票管理','invoice',131,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(139,'发票列表','list',138,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(140,'发票信息管理','template',138,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(141,'寄送地址','addresslist',138,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(142,'订单管理','order',131,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(143,'续费管理','renew',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(144,'退订管理','refund',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(145,'可退款订单','list',144,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(146,'退订记录','record',144,NULL,NULL,NULL,'1','0',0,NULL,NULL,1560335597,1560335597),(147,'合同管理','contract',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(148,'资源账单','resource',133,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(150,'充值','recharge',131,132,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(151,'提现','withdraw',131,132,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(152,'购买验证','pay',127,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(153,'工单详情','details',11,12,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(154,'创建工单验证','addauth',11,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(155,'工单回复','reply',11,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(156,'工单确认','confirm',11,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(157,'撤销工单','cancel',11,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(158,'删除工单','del',11,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(159,'充值验证','rechargeauth',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(160,'接口API','api',0,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(161,'支付宝同步','return_url',160,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(162,'支付宝异步','notify_url',160,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(163,'账单详情','dealrecorddetails',131,136,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(165,'取消订单','cancelorder',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,1560335597,1560335597),(166,'物理服务器租用','server',0,NULL,NULL,'icon-dcc','1','0',1,NULL,NULL,1560335597,1560335597),(167,'租用物理服务器','add',166,168,NULL,NULL,'0','0',0,NULL,NULL,0,0),(168,'物理服务器管理','index',166,NULL,NULL,NULL,'1','0',0,NULL,NULL,0,0),(169,'获取物理服务器产品','getAddedItems',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(170,'获取价格','getPrice',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(171,'获取物理服务器增值服务','getAdded',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(172,'租用物理服务器验证','addAuth',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(173,'获取物理服务器密码','getpass',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(174,'重装操作系统','resetos',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(176,'删除订单','delorder',131,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(177,'获取操作系统版本','getOstypes',166,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(178,'订单详情','orderdetails',131,142,NULL,NULL,'0','0',0,NULL,NULL,0,0),(179,'物理服务器详情','detail',166,168,NULL,NULL,'0','0',0,NULL,NULL,0,0),(180,'编辑服务器信息','edits',166,168,NULL,NULL,'0','0',0,NULL,NULL,0,0),(181,'获取历史消费趋势','gethistory',133,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(182,'账号信息','iam',0,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(183,'用户中心','overview',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(184,'安全认证','accesslist',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(185,'个人认证','cpersonal',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(186,'企业认证','ccompany',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(187,'账号信息编辑','baseinfoedit',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(189,'提交实名认证信息','subauth',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(190,'修改基本信息','baseinfoedit',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(191,'提交企业认证信息','subcompany',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(192,'提交发票信息','subtemp',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(193,'提交发票地址','subaddress',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(194,'删除发票地址','deladdress',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(195,'发票申请','applyinvoice',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(196,'提交发票申请','subapplyinvoice',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(197,'取消发票申请','cancelInvoice',138,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(198,'域名概览','index',9,NULL,NULL,NULL,'1','0',0,NULL,NULL,0,0),(199,'域名搜索','search',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(200,'域名管理','manage',9,NULL,NULL,NULL,'1','0',0,NULL,NULL,0,0),(201,'域名价格','price',9,NULL,NULL,NULL,'1','0',0,NULL,NULL,0,0),(202,'信息模板','model',9,NULL,NULL,NULL,'1','0',0,NULL,NULL,0,0),(203,'域名搜索接口','searchdomain',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(204,'域名查询接口','searchdomaininfo',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(205,'获取域名清单价格','getDomainListPrice',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(206,'创建域名订单','add',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(207,'创建联系人信息验证','modelAuth',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(208,'删除联系人信息','modeldel',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(209,'创建域名验证','addAuth',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(210,'域名续费','recharge',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(211,'域名详情','detail',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(212,'前往域名控制台','tomanager',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(213,'更变域名隐私保护','whoisProtect',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(214,'更变域名所有者','transform',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(215,'更变域名所有者验证','transformauth',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(216,'更变域名DNS','changens',9,200,NULL,NULL,'0','0',0,NULL,NULL,0,0),(217,'更变域名DNS验证','changensAuth',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(218,'生成域名证书','certification',9,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(219,'新增AKSK','addaccessAuth',0,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(220,'新增AKSK','addaccessAuth',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(221,'修改AKSK说明','editaccessIntro',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(222,'删除AKSK','delaccess',182,NULL,NULL,NULL,'0','0',0,NULL,NULL,0,0),(223,'主机服务','clientarea',8,224,NULL,NULL,'0','1',1,NULL,NULL,0,0),(224,'实例列表','list',8,NULL,NULL,NULL,'0','1',1,NULL,NULL,0,0),(226,'创建VPS','add',7,126,NULL,NULL,'0','0',0,NULL,NULL,0,0),(227,'创建VPS验证','addAuth',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(228,'更新VPS','up',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(229,'释放VPS','del',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(230,'续费VPS','renew',7,126,NULL,NULL,'0','0',0,NULL,NULL,0,0),(231,'VPS开启','on',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(232,'VPS关闭','off',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(233,'修改VPS密码','changepass',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(234,'获取VPS产品信息','getProItem',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(235,'获取VPS价格','getPrice',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0),(236,'VPS控制台','manager',7,0,NULL,NULL,'0','0',0,NULL,NULL,0,0);
/*!40000 ALTER TABLE `gee_webroute` ENABLE KEYS */;

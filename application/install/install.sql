-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-10-11 23:56:21
-- 服务器版本： 5.5.56-log
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `console`
--

-- --------------------------------------------------------

--
-- 表的结构 `gee_yzm`
--

CREATE TABLE `gee_yzm` (
  `id` int(11) NOT NULL,
  `AK` varchar(255) DEFAULT NULL COMMENT 'AK',
  `SK` varchar(255) DEFAULT NULL COMMENT 'SK',
  `APPID` varchar(255) DEFAULT NULL COMMENT 'APPID',
  `domainReg` varchar(255) DEFAULT NULL COMMENT '域名注册成功模板',
  `domainRenew` varchar(255) DEFAULT NULL COMMENT '域名续费通知模板',
  `domainSucc` varchar(255) DEFAULT NULL COMMENT '域名续费成功模板',
  `host` varchar(255) DEFAULT NULL COMMENT '虚拟主机开通模板',
  `hostRenew` varchar(255) DEFAULT NULL COMMENT '虚拟主机续费模板',
  `hostExpire` varchar(255) DEFAULT NULL COMMENT '虚拟主机到期模板',
  `mail` varchar(255) DEFAULT NULL COMMENT '企业邮箱开通模板',
  `server` varchar(255) DEFAULT NULL COMMENT '云服务器开通模板',
  `serverExpire` varchar(255) DEFAULT NULL COMMENT '云服务器到期模板',
  `serverRenew` varchar(255) DEFAULT NULL COMMENT '云服务器续费模板',
  `update_time` varchar(255) DEFAULT NULL COMMENT '修改时间',
  `status` varchar(255) DEFAULT NULL COMMENT '状态 1 启用 2 禁止'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `gee_yzm`
--

INSERT INTO `gee_yzm` (`id`, `AK`, `SK`, `APPID`, `domainReg`, `domainRenew`, `domainSucc`, `host`, `hostRenew`, `hostExpire`, `mail`, `server`, `serverExpire`, `serverRenew`, `update_time`, `status`) VALUES
(1, 'fb5fcfadf35142d0a6631f7a6e42d1fc', 'fb5fcfadf35142d0a6631f7a6e42d1fc', '10027', '12195', '', '', '', '', '', '', '', '', '', '1498644911', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gee_yzm`
--
ALTER TABLE `gee_yzm`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

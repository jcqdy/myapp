-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 7 月 15 日 23:18
-- 服务器版本: Apache/2.4.2
-- PHP 版本: PHP/5.4.3


--
-- 数据库: `demos_core`
--

CREATE DATABASE IF NOT EXISTS `myapp` DEFAULT CHARACTER SET utf8;

USE `myapp`;

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(100) NOT NULL DEFAULT '',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`id`, `name`, `pass`, `uptime`) VALUES
(1, 'admin', 'admin', '2013-7-15 12:52:49');

-- --------------------------------------------------------

--
-- 表的结构 `consumer`
--

CREATE TABLE IF NOT EXISTS `consumer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(100) NOT NULL DEFAULT '',
  `face` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `encrypt` varchar(1000) NOT NULL DEFAULT '',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- 转存表中的数据 `consumer`
--

INSERT INTO `consumer` (`id`, `name`, `pass`, `uptime`) VALUES
(1, 'consumer', '12345', '2013-7-15 12:52:49');

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- 表的结构 `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_num` varchar(100) NOT NULL DEFAULT '0',
  `watch` int(11) NOT NULL DEFAULT '0',
  `shopname` varchar(100) NOT NULL DEFAULT '',
  `pass` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `sertype` varchar(100) NOT NULL DEFAULT '',
  `face` varchar(100) NOT NULL DEFAULT '',
  `intro` varchar(500) NOT NULL DEFAULT '',  
  `encrypt` varchar(1000) NOT NULL DEFAULT '',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;





-- --------------------------------------------------------

--
-- 表的结构 `serviceinfo`
--

CREATE TABLE IF NOT EXISTS `serviceinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serviceid` int(11) NOT NULL DEFAULT '0',
  `foodmenu` varchar(100) NOT NULL DEFAULT '',
  `favorable` varchar(100) NOT NULL DEFAULT '',
  `site` tinyint(1) NOT NULL DEFAULT '0',
  `photoinfo` varchar(100) NOT NULL DEFAULT '',
  `information` varchar(1000) NOT NULL DEFAULT '',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `attention`
--

CREATE TABLE IF NOT EXISTS `attention` (
  `serviceid` int(11) NOT NULL DEFAULT '0',
  `consumerid` int(11) NOT NULL DEFAULT '0',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
--
-- 转存表中的数据 `service`
--

INSERT INTO `attention` (`serviceid`, `consumerid`, `uptime`) VALUES
('1', '2', '2013-7-16 12:52:49'),
('2', '1', '2013-7-17 12:52:49'),
('1', '3', '2013-7-18 12:52:49');
-- --------------------------------------------------------

--
-- 表的结构 `notice`
--

CREATE TABLE IF NOT EXISTS `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumerid` int(11) NOT NULL DEFAULT '0',
  `fanscount` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `serviceid` int(11) NOT NULL DEFAULT '0',
  `imgurl1` varchar(100) NOT NULL DEFAULT '0',
  `imgurl2` varchar(100) NOT NULL DEFAULT '0',
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
--
-- 转存表中的数据 `image`
--

INSERT INTO `image` (`serviceid`, `imgurl1`) VALUES
('1', '192.168.1.100/myapp/Uploads/image_mix/1.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/2.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/3.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/4.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/5.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/6.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/7.jpg'),
('1', '192.168.1.100/myapp/Uploads/image_mix/8.jpg');
/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50615
Source Host           : localhost:3306
Source Database       : metdb

Target Server Type    : MYSQL
Target Server Version : 50615
File Encoding         : 65001

Date: 2014-07-29 21:52:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for identity_credential
-- ----------------------------
DROP TABLE IF EXISTS `identity_credential`;
CREATE TABLE `identity_credential` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '登录凭证类型  4oauth.cmcc 3手机 1nickname,2email',
  `name` varchar(100) NOT NULL COMMENT '登录凭证名称',
  PRIMARY KEY (`id`),
  KEY `fk_credential_uid_idx` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='凭证';

-- ----------------------------
-- Records of identity_credential
-- ----------------------------

-- ----------------------------
-- Table structure for identity_password
-- ----------------------------
DROP TABLE IF EXISTS `identity_password`;
CREATE TABLE `identity_password` (
  `uid` bigint(20) NOT NULL,
  `pwd` varchar(100) NOT NULL,
  `protection_question` tinyint(4) DEFAULT NULL,
  `protection_answer` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `fk_password_uid_idx` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='密码表';

-- ----------------------------
-- Records of identity_password
-- ----------------------------

-- ----------------------------
-- Table structure for identity_session
-- ----------------------------
DROP TABLE IF EXISTS `identity_session`;
CREATE TABLE `identity_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '标识',
  `token` varchar(100) NOT NULL COMMENT 'SSOToken',
  `uid` bigint(20) NOT NULL COMMENT '用户标识',
  `create_time` int(11) NOT NULL,
  `ttl` int(20) NOT NULL COMMENT '存活时间',
  `tti` int(20) NOT NULL COMMENT '空闲时间',
  `client_ip` varchar(60) DEFAULT NULL COMMENT '客户端IP',
  `last_active_time` int(11) NOT NULL DEFAULT '0' COMMENT '最近活动时间',
  `client_type` int(2) NOT NULL DEFAULT '1' COMMENT '1web,2移动客户端',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  KEY `fk_session_uid_idx` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会话';

-- ----------------------------
-- Records of identity_session
-- ----------------------------

-- ----------------------------
-- Table structure for identity_user
-- ----------------------------
DROP TABLE IF EXISTS `identity_user`;
CREATE TABLE `identity_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `realname` varchar(100) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1启用，2禁用',
  `last_login_ip` varchar(20) DEFAULT NULL,
  `auth` int(11) DEFAULT '1' COMMENT '用户权限，1普通用户，2普通管理员，3，禁止上传，999超级管理员',
  `tno` varchar(255) DEFAULT NULL COMMENT 'oa编号',
  `last_login_time` int(255) DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户基本信息';

-- ----------------------------
-- Records of identity_user
-- ----------------------------

-- ----------------------------
-- Table structure for material_attatch
-- ----------------------------
DROP TABLE IF EXISTS `material_attatch`;
CREATE TABLE `material_attatch` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `sname` text NOT NULL COMMENT '附件名称，外部展现用',
  `rname` varchar(255) DEFAULT NULL COMMENT '附件名词，内部存储用，随机生成',
  `mid` int(11) NOT NULL COMMENT '素材ID',
  `mvid` int(11) NOT NULL COMMENT '素材版本ID',
  `pfix` varchar(255) DEFAULT NULL COMMENT '后缀名',
  `uptime` int(255) DEFAULT NULL COMMENT '上传时间',
  `upuser` int(255) DEFAULT NULL COMMENT '上传者',
  `stat` int(1) DEFAULT '1' COMMENT '状态，1正常，0：删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of material_attatch
-- ----------------------------

-- ----------------------------
-- Table structure for material_cate
-- ----------------------------
DROP TABLE IF EXISTS `material_cate`;
CREATE TABLE `material_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类Id',
  `cname` varchar(255) NOT NULL COMMENT '分类名称',
  `clogo` varchar(255) NOT NULL COMMENT '分类d代表图片（大中小三套）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材分类表';

-- ----------------------------
-- Records of material_cate
-- ----------------------------

-- ----------------------------
-- Table structure for material_info
-- ----------------------------
DROP TABLE IF EXISTS `material_info`;
CREATE TABLE `material_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '素材ID',
  `mname` varchar(255) DEFAULT NULL COMMENT '素材名称',
  `vernum` int(11) DEFAULT NULL COMMENT '版本数',
  `cid` int(11) DEFAULT NULL COMMENT '分类ID',
  `create_at` int(11) DEFAULT NULL COMMENT '上传时间',
  `update_at` int(11) DEFAULT NULL COMMENT '最后更新时间',
  `uid` int(11) DEFAULT NULL COMMENT '所有者ID（区别于上传者）',
  `state` int(11) DEFAULT '1' COMMENT '0草稿，1发布',
  `cversion` int(11) DEFAULT NULL COMMENT '当前版本ID',
  `logo` varchar(255) DEFAULT NULL COMMENT '代表图片',
  `vright` int(11) DEFAULT '1' COMMENT '访问类型 1：全部可访问，2登录用户可访问，3指定用户访问',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材基本信息表';

-- ----------------------------
-- Records of material_info
-- ----------------------------

-- ----------------------------
-- Table structure for material_version
-- ----------------------------
DROP TABLE IF EXISTS `material_version`;
CREATE TABLE `material_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '版本自增ID',
  `mid` int(11) DEFAULT NULL COMMENT '素材ID',
  `content` longtext COMMENT '素材描述',
  `nohtml` longtext COMMENT '去除html标签的信息',
  `depict` varchar(255) DEFAULT NULL COMMENT '版本描述信息',
  `vnum` int(11) DEFAULT NULL COMMENT '版本号（从1逐次递增）',
  `anum` int(11) DEFAULT NULL COMMENT '附件数',
  `uid` int(11) DEFAULT NULL COMMENT '上传者id',
  `cat` int(11) DEFAULT NULL COMMENT '上传时间',
  `upat` int(11) DEFAULT NULL COMMENT '版本更新时间',
  `shownum` int(11) DEFAULT NULL COMMENT '查看次数',
  `downnum` int(11) DEFAULT NULL COMMENT '下载数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材版本信息表';

-- ----------------------------
-- Records of material_version
-- ----------------------------

-- ----------------------------
-- Table structure for material_visit_right
-- ----------------------------
DROP TABLE IF EXISTS `material_visit_right`;
CREATE TABLE `material_visit_right` (
  `mid` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `vr` int(11) DEFAULT '2' COMMENT '访问权限类型，1查看，2可查看并下载',
  PRIMARY KEY (`mid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材访问权限表';

-- ----------------------------
-- Records of material_visit_right
-- ----------------------------

-- ----------------------------
-- Table structure for site_config
-- ----------------------------
DROP TABLE IF EXISTS `site_config`;
CREATE TABLE `site_config` (
  `skey` varchar(255) NOT NULL DEFAULT '' COMMENT '配置key',
  `svalue` varchar(255) DEFAULT NULL COMMENT '配置值',
  `content` text COMMENT '配置说明',
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站配置表';

-- ----------------------------
-- Records of site_config
-- ----------------------------
INSERT INTO `site_config` VALUES ('IS_NOTICE', '0', '是否显示通知');
INSERT INTO `site_config` VALUES ('IS_STOP', '0', '是否临时关站');
INSERT INTO `site_config` VALUES ('SHOW_MATERIAL', '1', '上传素材默认是否显示');
INSERT INTO `site_config` VALUES ('SITE_NOTICE', '网站开了', '网站通知');

-- ----------------------------
-- Table structure for visit_log
-- ----------------------------
DROP TABLE IF EXISTS `visit_log`;
CREATE TABLE `visit_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户Id',
  `platform` char(20) DEFAULT NULL COMMENT '操作系统',
  `browser` char(50) DEFAULT NULL COMMENT '浏览器',
  `browserAll` char(100) DEFAULT NULL,
  `browserVer` char(50) DEFAULT NULL,
  `time` int(20) DEFAULT NULL COMMENT '请求时间',
  `reference` text COMMENT '来源地址',
  `ip` char(100) DEFAULT NULL,
  `curl` text COMMENT '当前访问地址',
  `isrobot` tinyint(1) DEFAULT NULL COMMENT '是否蜘蛛',
  `ctitle` varchar(255) DEFAULT NULL COMMENT '页面标题',
  `robot` char(100) DEFAULT NULL COMMENT '蜘蛛名称(统计搜索引擎)',
  `agent` varchar(255) DEFAULT NULL,
  `usign` char(100) DEFAULT NULL COMMENT '用户标识，记录在cookie中的一个字符串',
  PRIMARY KEY (`id`),
  KEY `timeindex` (`time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='访问日志表';

-- ----------------------------
-- Records of visit_log
-- ----------------------------
INSERT INTO `visit_log` VALUES ('1', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406639712', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('2', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406639808', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('3', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406639937', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('4', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640019', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('5', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640239', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('6', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640368', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('7', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640525', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('8', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640620', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('9', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640702', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('10', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640740', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('11', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640772', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('12', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640866', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('13', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640900', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('14', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640924', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('15', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406640983', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('16', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641156', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('17', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641245', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('18', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641299', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('19', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641324', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('20', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641403', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('21', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641419', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('22', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641443', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('23', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641475', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('24', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641585', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('25', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641632', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('26', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641749', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('27', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641791', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('28', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641841', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');
INSERT INTO `visit_log` VALUES ('29', '0', 'Unknown Windows OS', 'Chrome', 'Chrome 36.0.1985.125', '36.0.1985.125', '1406641872', '', '127.0.0.1', 'http://mate.jiaoyu365.net/', '0', '首页-统一素材库', '', 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36', '0');

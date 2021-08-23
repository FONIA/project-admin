项目管理系统，个人开发项目，原公司没用上
===============

tp5框架，多数采用原生技术。

表设计：

```sql
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(20) DEFAULT NULL,
  `step` int(11) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `ext` varchar(255) DEFAULT NULL,
  `chtime` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(20) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `custom` varchar(20) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `stime` int(11) DEFAULT NULL,
  `etime` int(11) DEFAULT NULL,
  `worker` varchar(12) DEFAULT NULL,
  `ext` varchar(255) DEFAULT NULL,
  `ask` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(20) DEFAULT NULL,
  `rate` text,
  `chtime` varchar(255) DEFAULT NULL,
  `time` text,
  `diff` text,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` char(10) DEFAULT NULL,
  `name` varchar(10) DEFAULT NULL,
  `sex` blob,
  `depa` varchar(10) DEFAULT NULL,
  `job` varchar(10) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `statu` varchar(20) DEFAULT NULL,
  `leader` varchar(10) DEFAULT NULL,
  `ext` varchar(50) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `acount` varchar(12) DEFAULT NULL,
  `passw` varchar(255) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `pw` char(2) NOT NULL DEFAULT '0' COMMENT '用户信息重置权限',
  `pidpw` char(2) NOT NULL DEFAULT '1' COMMENT '项目新建权限',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

ps：注释被吃掉了 ------ 没错，就是懒。
```



截图：

![1](.\1.png)

![2](.\2.png)

![3](.\3.png)

![4](.\4.png)

![5](.\5.png)

![6](.\6.png)

![7](.\7.png)

![8](.\8.png)

![9](.\9.png)

![10](.\10.png)

![11](.\11.png)



置于前端为什么不用vue-element-admin框架？

官方回答是：杀鸡用不着牛刀。

后来发现还是用牛刀香~~




































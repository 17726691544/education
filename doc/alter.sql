 ALTER TABLE `user` ADD `is_ej_qd` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为耳机区代 0、不是  1、是';
 ALTER TABLE `user` ADD `is_ej_gd` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '是否为耳机个代 0、不是  1、是';

 ALTER TABLE `config` ADD `goods_rate` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '商品税费百分比';



 ALTER TABLE `course` ADD `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '产品类型 0：课程 1：耳机';
 ALTER TABLE `course` ADD `gd_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '个代价格';
 ALTER TABLE `course` ADD `gd_num` int(10) unsigned NOT NULL DEFAULT '10' COMMENT '个代数量';
 ALTER TABLE `course` ADD `qd_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '区代价格';
 ALTER TABLE `course` ADD `qd_num` int(10) unsigned NOT NULL DEFAULT '20' COMMENT '区代数量';
 ALTER TABLE `course` MODIFY `grades` varchar(1024) NOT NULL DEFAULT '' COMMENT '可选年级，JSON数组';


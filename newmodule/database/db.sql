CREATE TABLE `idc_room` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) NOT NULL COMMENT '����ȫ��',
  `room_shortname` varchar(50) DEFAULT '' COMMENT '����������д',
  `room_addr` varchar(255) NOT NULL DEFAULT '' COMMENT '����������ַ',
  `room_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '����״̬��0���Ѿ��رգ�1����������',
  `room_starttime` varchar(20) DEFAULT NULL COMMENT '������ʼ����ʱ��',
  `room_endtime` varchar(20) DEFAULT NULL COMMENT '����Ԥ�ƹر�ʱ��',
  `room_closedtime` varchar(20) DEFAULT NULL COMMENT '������ʽ�ر�ʱ��',
  `room_comment` varchar(255) DEFAULT '' COMMENT '��������˵��',
  `room_userid` bigint(20) unsigned NOT NULL COMMENT '���������ID',
  `room_createtime` varchar(20) DEFAULT NULL COMMENT '�������ʱ��',
  `room_userip` varchar(255) NOT NULL COMMENT '���������IP��ַ',
  `room_lasttime` varchar(20) DEFAULT NULL COMMENT '����޸�ʱ��',
  `room_lastuserid` bigint(20) unsigned DEFAULT NULL COMMENT '����޸���ID',
  `room_lastuserip` varchar(255) DEFAULT '' COMMENT '����޸���IP��ַ',
  `room_deltime` varchar(20) DEFAULT NULL COMMENT '���鵵ʱ��',
  `room_deluserid` bigint(20) unsigned DEFAULT NULL COMMENT '���鵵��ID',
  `room_deluserip` varchar(255) DEFAULT '' COMMENT '���鵵��IP��ַ',
  PRIMARY KEY (`id`),
  KEY `c_room_userid` (`room_userid`),
  KEY `c_room_lastuserid` (`room_lastuserid`),
  KEY `c_room_deluserid` (`room_deluserid`),
  CONSTRAINT `c_room_deluserid` FOREIGN KEY (`room_deluserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `c_room_lastuserid` FOREIGN KEY (`room_lastuserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `c_room_userid` FOREIGN KEY (`room_userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 


create table `idc_box` (
	`id` int(5) NOT NULL AUTO_INCREMENT primary key ,
	`room_id` int(5) not null, 
	`box_no` varchar(50) not null comment '������',
	`box_secno` varchar(50) comment '����ڶ������',
	`box_starttime` varchar(20) comment '��������ʱ��',
	`box_endtime`  varchar(20) comment '����Ԥ�ƹر�ʱ��',
  `box_closedtime`  varchar(20) comment '������ʽ�ر�ʱ��',
  `box_desc`	varchar(255) comment '����������Ϣ',
  `box_status` tinyint(1) not null default 1 comment '����״̬��0���Ѿ��رգ�1����������',
  `box_outbandwidth` int(10) not null default '0' comment '������ڴ����С����λ��Mbps',
  `box_iplist` varchar(255) comment '�����Ӧ�Ĺ���IP��ַ�б�',
  `box_height` int(3)	not null default '48' comment '����߶ȣ�������U',
  `box_userid`	bigint(20) unsigned NOT NULL comment '���������ID',
  `box_createtime` varchar(20) comment '�������ʱ��',
  `box_userip`	varchar(255)  NOT NULL comment '���������IP��ַ',
  `box_lasttime` varchar(20) comment '����޸�ʱ��',
	`box_lastuserid`	bigint(20) unsigned comment '����޸���ID',
	`box_lastuserip`	varchar(255) comment '����޸���IP��ַ',
	`box_deltime` varchar(20) comment '���鵵ʱ��',
	`box_deluserid`	bigint(20) unsigned comment '���鵵��ID',
	`box_deluserip`	varchar(255)  comment '���鵵��IP��ַ',
	CONSTRAINT `box_userid` FOREIGN KEY (`box_userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
	CONSTRAINT `box_lastuserid` FOREIGN KEY (`box_lastuserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
	CONSTRAINT `box_deluserid` FOREIGN KEY (`box_deluserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `c_room_id` FOREIGN KEY (`room_id`) REFERENCES `idc_room` (`id`) ON DELETE CASCADE
) engine=innodb default charset=utf8;

CREATE TABLE `contact` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(20) NOT NULL COMMENT '����',
  `contact_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ա� 0Ů1��',
  `contact_position` varchar(50) DEFAULT '' COMMENT '��ϵ�˵�ְλ',
  `contact_company` varchar(50) DEFAULT '' COMMENT '��˾ȫ��',
  `contact_url` varchar(255) DEFAULT '' COMMENT '��ϵ�˹�˾��ַ',
  `contact_addr` varchar(255) DEFAULT '' COMMENT '��ϵͳ�˹�˾��ַ',
  `contact_tel` varchar(255) DEFAULT '' COMMENT '��ϵ�˵绰',
  `contact_mp` varchar(15) DEFAULT '' COMMENT '��ϵ���ֻ�',
  `contact_email` varchar(255) DEFAULT '' COMMENT '��ϵ�˵�������',
  `contact_qq` varchar(50) DEFAULT '' COMMENT '��ϵ��QQ��',
  `contact_wx` varchar(255) DEFAULT '' COMMENT '��ϵ��΢�ź�',
  `contact_desc` varchar(255) DEFAULT '' COMMENT '������ϵ�˵�˵��',
  `contact_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '��ϵ��״̬��0���Ѿ�ʧ�����ٺ�������ϵ�ˣ�1������',
  `contact_userid` bigint(20) unsigned NOT NULL COMMENT '���������ID',
  `contact_createtime` varchar(20) DEFAULT '' COMMENT '�������ʱ��',
  `contact_userip` varchar(255) DEFAULT '' COMMENT '���������IP��ַ',
  `contact_lasttime` varchar(20) DEFAULT '' COMMENT '����޸�ʱ��',
  `contact_lastuserid` bigint(20) unsigned DEFAULT NULL COMMENT '����޸���ID',
  `contact_lastuserip` varchar(255) DEFAULT '' COMMENT '����޸���IP��ַ',
  `contact_deltime` varchar(20) DEFAULT '' COMMENT '���鵵ʱ��',
  `contact_deluserid` bigint(20) unsigned DEFAULT NULL COMMENT '���鵵��ID',
  `contact_deluserip` varchar(255) DEFAULT '' COMMENT '���鵵��IP��ַ',
  `contact_fax` varchar(255) DEFAULT '' COMMENT '��ϵ�˴���',
  PRIMARY KEY (`id`),
  KEY `contact_userid` (`contact_userid`),
  KEY `contact_lastuserid` (`contact_lastuserid`),
  KEY `contact_deluserid` (`contact_deluserid`),
  CONSTRAINT `contact_deluserid` FOREIGN KEY (`contact_deluserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `contact_lastuserid` FOREIGN KEY (`contact_lastuserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `contact_userid` FOREIGN KEY (`contact_userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 

CREATE TABLE `file` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_title` varchar(50) NOT NULL COMMENT '��������',
  `file_name` varchar(50) NOT NULL COMMENT '�����ļ���',
  `file_uploadtime` varchar(20) DEFAULT NULL COMMENT '��������ϴ�ʱ��',
  `file_userid` bigint(20) unsigned NOT NULL COMMENT '��������ϴ���ID',
  `file_userip` varchar(255) NOT NULL COMMENT '��������ϴ���IP��ַ',
  `file_lasttime` varchar(20) DEFAULT NULL COMMENT '��������ϴ�ʱ��',
  `file_lastuserid` bigint(20) unsigned DEFAULT NULL COMMENT '��������ϴ���ID',
  `file_lastuserip` varchar(255) DEFAULT NULL COMMENT '��������ϴ���IP��ַ',
  `file_desc` varchar(255) DEFAULT '' COMMENT '���ڸ�����˵��',
  `file_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '����״̬��0��ɾ���鵵״̬��1������',
  `file_deltime` varchar(20) DEFAULT '' COMMENT '�����ʱ��',
  `file_deluserid` bigint(20) unsigned DEFAULT NULL COMMENT '�����鵵��ID',
  `file_deluserip` varchar(255) DEFAULT '' COMMENT '�����鵵��IP��ַ',
  PRIMARY KEY (`id`),
  KEY `file_userid` (`file_userid`),
  KEY `file_lastuserid` (`file_lastuserid`),
  KEY `file_deluserid` (`file_deluserid`),
  CONSTRAINT `file_deluserid` FOREIGN KEY (`file_deluserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `file_lastuserid` FOREIGN KEY (`file_lastuserid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `file_userid` FOREIGN KEY (`file_userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8


create table `system_settingtype` (
	`id` int(5) NOT NULL AUTO_INCREMENT primary key ,
	`settingtype_name` varchar(50) not null comment '��������',
	`settingtype_shortname` varchar(50) comment '��������Ӣ����д',
	`settingtype_prefix` varchar(50) comment '���Ͷ�Ӧϵͳ����ǰ׺',
	`settingtype_desc` varchar(255) comment '��������'
) engine=innodb default charset=utf8;
insert into system_settingtype (id,settingtype_name,settingtype_shortname,settingtype_prefix,settingtype_desc,setting_status) values (1,'ȫ������','global_settings','SYS_GLOBAL','��Ӧ�ڲ����ڹ��࣬�Զ��ģ�鶼��Ӱ�������','1');
insert into system_settingtype (id,settingtype_name,settingtype_shortname,settingtype_prefix,settingtype_desc) values (3,'Monitoring Settings','monitor_settings','SYS_MONITOR','监控系统的相关配置');


CREATE TABLE `system_setting` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `settingtype_id` int(5) NOT NULL COMMENT '设置项所属于的大类ID 关联system_settingtype表',
  `setting_name` varchar(50) NOT NULL COMMENT '设置项名称，英文。作为系统变量的后部分',
  `setting_title` varchar(50) NOT NULL COMMENT '设置项的中文描述，作为表单项内容',
  `setting_helpmsg` varchar(255) DEFAULT NULL COMMENT '设置项帮助性的说明',
  `setting_value` varchar(255) DEFAULT NULL COMMENT '设置项的值',
  `input_type` varchar(50) NOT NULL DEFAULT 'TEXTBOX' COMMENT '配置项显示类型 PHP定义的常量，在include\defines.inc.php中定义的',
  `data_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '数据类型，在include\defines.inc.php中定义',
  `js_check_method` varchar(255) NOT NULL DEFAULT 'none',
  `php_check_method` varchar(255) NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  KEY `settingtype_id` (`settingtype_id`),
  CONSTRAINT `settingtype_id` FOREIGN KEY (`settingtype_id`) REFERENCES `system_settingtype` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8; 

insert into system_setting(settingtype_id,setting_name,setting_title,setting_helpmsg,setting_value,input_type) values(1,'allow_upload_file_size','Allow Upload File Size','',10,0);
insert into system_setting(settingtype_id,setting_name,setting_title,setting_helpmsg,setting_value,input_type) values(1,'allow_upload_file_type','Allow UploadFile Type','',1,1);
insert into system_setting(settingtype_id,setting_name,setting_title,setting_helpmsg,setting_value,input_type) values(1,'upload_file_path','Upload File Save Path','','/uploadfiles',0);
insert into system_setting(id,settingtype_id,setting_name,setting_title,setting_helpmsg,setting_value,input_type,data_type,js_check_method,php_check_method) values (
10,3,'default_inventory_mode','Default Inventory Mode',NULL,0,2,0,'','');

create table `system_setting_item` (
	`id` int(5) NOT NULL AUTO_INCREMENT primary key ,
	`item_name` varchar(50) not null comment '���������ƣ�Ӣ�ġ���Ϊϵͳ�����ĺ󲿷�',
	`item_title` varchar(50) not null comment '�������������������Ϊ��������',
	`item_helpmsg` varchar(255) comment '����������Ե�˵��',
	`item_img` varchar(255) comment 'ָ���������ͼƬ������еĻ�',
	`belong_setting` varchar(50) comment '�����������ڵ��������Ϊsystem_setting���setting_name���'
) engine=innodb default charset=utf8;
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('jpg','JPG','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('GIF','GIF','','','allow_upload_file_type'); 
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('BMP','BMP','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('TXT','TXT','','','allow_upload_file_type'); 
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('PDF','PDF','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('DOC','DOC','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('DOCX','DOCX','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('XLS','XLS','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('XLSX','XLSX','','','allow_upload_file_type');
insert into system_setting_item(item_name,item_title,item_helpmsg,item_img,belong_setting) values('PNG','PNG','','','allow_upload_file_type');
	
	
CREATE TABLE `fileobjectrelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(5) NOT NULL,
  `object_table` varchar(50) DEFAULT NULL COMMENT '���������ı���',
  `object_field` varchar(50) DEFAULT NULL COMMENT '�����������ֶ���',
  `object_value` varchar(50) DEFAULT NULL COMMENT '�����������ֶ�ֵ',
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  CONSTRAINT `file_id` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE `contactobjectrelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(5) NOT NULL,
  `object_table` varchar(50) DEFAULT NULL COMMENT '���������ı���',
  `object_field` varchar(50) DEFAULT NULL COMMENT '�����������ֶ���',
  `object_value` varchar(50) DEFAULT NULL COMMENT '�����������ֶ�ֵ',
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bzhy_hosttype` (
  `typeid` int(2) NOT NULL AUTO_INCREMENT,
  `typename` varchar(50) NOT NULL,
  `typedesc` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0:disabled 1:normal',
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ;
insert into `bzhy_hosttype`(typeid,typename,typedesc,status) values (1,'Router','router',1),
(2,'Switch','Included switch hub',1),(3,'Firewall','Included Firewall,Gateway',1),(4,'Server','Hareware Server',1),
(5,'Vmware VM','Include Esxi vmware ',1),(6,'KVM VM','Include KVM VM ',1),(7,'Docker','Docker container ',1),(8,'Xen','Xen VM',1);

CREATE TABLE `bzhy_os` (
  `osid` tinyint(2) NOT NULL AUTO_INCREMENT,
  `osname` varchar(20) NOT NULL COMMENT '操作系统名称',
  `osbit` int(4) NOT NULL DEFAULT '64' COMMENT '操作系统位数，默认为64位',
  `version` varchar(20) NOT NULL COMMENT '操作系统的的版本号',
  `desc` varchar(255) DEFAULT NULL COMMENT '操作系统描述',
  PRIMARY KEY (`osid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ;
insert into bzhy_os(osid,osname,osbit,version,`desc`) values (1,'CentOS',64,'6.4','CentOS 6.4'),
    (2,'CentOS',64,'6.5','CentOS 6.5'),(3,'ESXI',64,'5.0','VMware ESXI 5.0'),
    (4,'ESXI',64,'5.5','VMware ESXI 5.5'),(5,'Windows',64,'2003','Microsoft Windows Server 2003'),
    (6,'Windows',64,'2008','Microsoft Windows Server 2008');

CREATE TABLE `bzhy_brand` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `local_name` varchar(255) DEFAULT NULL COMMENT '品牌本地语言名称，如中文',
  `english_name` varchar(255) DEFAULT NULL COMMENT '品牌英文名称',
  `offical_site` varchar(255) DEFAULT NULL COMMENT '官方网站地址',
  `support_site` varchar(255) DEFAULT NULL COMMENT '技术支持网站地址',
  `tel1` varchar(20) DEFAULT NULL COMMENT '服务电话号码1',
  `tel1_mem` varchar(50) DEFAULT NULL COMMENT '标注服务电话号码1的作用',
  `tel2` varchar(20) DEFAULT NULL COMMENT '服务电话号码2',
  `tel2_mem` varchar(50) DEFAULT NULL COMMENT '标注服务电话号码2的作用',
  `tel3` varchar(20) DEFAULT NULL COMMENT '服务电话号码3',
  `tel3_mem` varchar(50) DEFAULT NULL COMMENT '标注服务电话号码3的作用',
  `desc` varchar(255) DEFAULT NULL COMMENT '品牌描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
insert into `bzhy_brand`(`id`,`local_name`,`english_name`,`offical_site`,`support_site`,`tel1`,`tel1_mem`,`tel2`,`tel2_mem`,`tel3`,`tel3_mem`,`desc`) values 
    (1,'戴尔','Dell','https://www.dell.com','https://www.dell.com','8008580888','售前咨询',NULL,NULL,NULL,NULL,NULL),
    (2,'惠普','HP','http://www.hp.com/','http://www.hp.com/',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (3,'联想','Lenovo','https://www.lenovo.com.cn','https://www.lenovo.com.cn',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (4,'恩科','Cisco','https://www.cisco.com/','https://www.cisco.com/',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (5,'华为','HUAWEI','https://www.huawei.com','https://www.huawei.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (6,'华三','H3C','http://www.h3c.com','http://www.h3c.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (7,'VMware','VMware','https://www.vmware.com','https://www.vmware.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (8,'KVM','KVM','https://www.linux-kvm.org','https://www.linux-kvm.org',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (9,'QEMU','QEMU','https://wiki.qemu.org','https://wiki.qemu.org',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (10,'XEN','XEN','https://www.xenproject.org/','https://www.xenproject.org/',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
    (11,'Docker','Docker','https://www.docker.com','https://www.docker.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL);

create table `deviceinfo` (
    `deviceid`  int(7) not null auto_increment,
    `typeid` int(2) not null COMMENT '类型ID，与devicetype表关联' ,
    `size` tinyint(1) DEFAULT 0 comment '设备大小，单位为U，非标设备或虚拟机为0',
    `model` varchar(255) DEFAULT '' comment  '设备型号',
    `serialno` varchar(255) DEFAULT '' comment '设备序列号',
    `serviceno` varchar(255) DEFAULT '' comment '设备服务代码',
    `hardinfo` varchar(255) default '' comment '设备硬件信息',
    `createdate` varchar(20) default '' COMMENT '设备购买或创建日期',
    `warrantystartdate` varchar(20) default '' comment '设备保修开始日期',
    `warrantyenddate` varchar(20) default '' comment '设备保修结束日期',
    `agent` varchar(255) default '' comment '设备采购的代理商名称',
    `hostname` varchar(255) default '' comment '设备的主机名',
    `ips` varchar(255) default '' comment '设备所配置的IP地址列表，多个IP则用；号分隔',
    `dns` varchar(255) default '' comment '设备所配置的DNS地址列表，多个IP则用；号分隔',
    `gw` varchar(255) default '' comment '设备所配置的网关地址列表，多个IP则用；号分隔',
    `roomid` int(5) not null comment '设备所在机房的ID号，虚拟机、容器等该值与突宿主机相同',
    `boxid` int(5) not null comment '设备所存放的机柜ID号,虚拟机、容器等该值与突宿主机相同',
    `position` varchar(20) default '' comment '设备在机柜里的位置',
    `belongdeviceid` int(7) not null default 0 comment '本设备所属于的父设备的ID号，为0则表示是独立设备',
    `userid` bigint(20) unsigned NOT NULL COMMENT '设备维护者ID',
    `isruning` tinyint(1) not null default 1 comment '设备的运行状态0表示关机，1表示正常运行',
    `status` tinyint(1) not null default 1 comment '设备的维护状态0表示下架、1表示正常、2表示维护',
    `osid` tinyint(2) not null default 0 comment '设备操作系统信息ID,0表示此设置没有操作系统或未知',
    `desc` varchar(255) default '' comment '设备描述',
    `brandid` int(4) DEFAULT '0' COMMENT '品牌信息ID号 0 为无品牌',
    primary key(`deviceid`),
    CONSTRAINT `typeid` FOREIGN KEY (`typeid`) REFERENCES `devicetype` (`typeid`) ON DELETE CASCADE,
    CONSTRAINT `brandid` FOREIGN KEY (`brandid`) REFERENCES `brandinfo` (`id`) ON DELETE CASCADE,
    CONSTRAINT `boxid` FOREIGN KEY (`boxid`) REFERENCES `idc_box` (`id`) ON DELETE CASCADE,
    CONSTRAINT `userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
    CONSTRAINT `roomid` FOREIGN KEY (`roomid`) REFERENCES `idc_room` (`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

create table `bzhy_host_inventory` (
    `id` bigint(20) NOT NULL primary key auto_increment comment '资产ID号',
    `hostid`  bigint(20) unsigned not null comment '主机ID，与hosts表中hostid关联',
    `typeid` int(2) not null COMMENT '类型ID，与devicetype表关联' ,
    `size` tinyint(1) DEFAULT 0 comment '设备大小，单位为U，非标设备或虚拟机为0',
    `model` varchar(255) DEFAULT '' comment  '设备型号',
    `serialno` varchar(255) DEFAULT '' comment '设备序列号',
    `serviceno` varchar(255) DEFAULT '' comment '设备服务代码',
    `tag` varchar(255) default '' comment '主机标签',
    `inventory_tag` varchar(255) default '' comment '主机资产标签',
    `hardinfo` varchar(255) default '' comment '设备硬件信息',
    `createdate` varchar(20) default '' COMMENT '设备购买或创建日期',
    `warrantystartdate` varchar(20) default '' comment '设备保修开始日期',
    `warrantyenddate` varchar(20) default '' comment '设备保修结束日期',
    `roomid` int(5) not null comment '设备所在机房的ID号，虚拟机、容器等该值与突宿主机相同',
    `boxid` int(5) not null comment '设备所存放的机柜ID号,虚拟机、容器等该值与突宿主机相同',
    `position` varchar(20) default '' comment '设备在机柜里的位置',
    `belongdeviceid` int(7) not null default 0 comment '本设备所属于的父设备的ID号，为0则表示是独立设备',
    `userid` bigint(20) unsigned NOT NULL COMMENT '设备维护者ID',
    `isruning` tinyint(1) not null default 1 comment '设备的运行状态0表示关机，1表示正常运行',
    `status` tinyint(1) not null default 1 comment '设备的维护状态0表示下架、1表示正常、2表示维护',
    `osid` tinyint(2) not null default 0 comment '设备操作系统信息ID,0表示此设置没有操作系统或未知',
    `desc` varchar(255) default '' comment '设备描述',
    `brandid` int(4) DEFAULT '0' COMMENT '品牌信息ID号 0 为无品牌',
    CONSTRAINT `c_typeid` FOREIGN KEY (`typeid`) REFERENCES `devicetype` (`typeid`) ON DELETE CASCADE,
    CONSTRAINT `c_brandid` FOREIGN KEY (`brandid`) REFERENCES `brandinfo` (`id`) ON DELETE CASCADE,
    CONSTRAINT `c_boxid` FOREIGN KEY (`boxid`) REFERENCES `idc_box` (`id`) ON DELETE CASCADE,
    CONSTRAINT `c_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
    CONSTRAINT `c_roomid` FOREIGN KEY (`roomid`) REFERENCES `idc_room` (`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
	
CREATE TABLE `bzhy_interfaces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'MAC 地址的ID号',
  `name` varchar(50) DEFAULT '' COMMENT '接口名',
  `mac` varchar(17) DEFAULT '' COMMENT '接口的MAC地址',
  `type` tinyint(1) DEFAULT '0' COMMENT '接口类型 0 为虚拟接口 1为实体接口',
  `hostid` bigint(20) unsigned NOT NULL COMMENT '主机ID，与hosts表中hostid关联',
  `status` tinyint(1) DEFAULT '1' COMMENT '接口的状态1启用 0为禁用',
  `description` varchar(255) DEFAULT '' COMMENT '接口描述',
  `useip` int(11) NOT NULL DEFAULT '1',
  `dns` varchar(64) NOT NULL DEFAULT '',
  `ip` varchar(64) NOT NULL DEFAULT '127.0.0.1',
  `mask` varchar(64) NOT NULL DEFAULT '255.255.255.0' COMMENT 'IP的子网掩码',
  PRIMARY KEY (`id`),
  KEY `c_hostid` (`hostid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

 CREATE TABLE `bzhy_interface_port` (
  `portid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '端口编号',
  `bzhy_interfaceid` bigint(20) unsigned NOT NULL,
  `zbx_interfaceid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `port` varchar(64) NOT NULL DEFAULT '10050',
  `bulk` int(11) NOT NULL DEFAULT '1',
  `main` int(11) NOT NULL DEFAULT '0',
  `zbx_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`portid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

REATE TABLE `bzhy_ips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'IP地址ID ',
  `hostid` bigint(20) unsigned NOT NULL COMMENT '主机ID，与hosts表中hostid关联',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'IP地址的类型，1表示接口的IP地址 2表示主机的网关，3表示主机的DNS地址',
  `family` tinyint(1) NOT NULL DEFAULT '4' COMMENT 'IP地址协议族 4表示IPv4地址 6表示IPv6地址',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP地址',
  PRIMARY KEY (`id`),
  KEY `bzhy_ips_c_hostid` (`hostid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ;


  
  
  
  
	 

  
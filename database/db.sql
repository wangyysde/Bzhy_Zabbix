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

	
	


  
  
  
  
	 

  
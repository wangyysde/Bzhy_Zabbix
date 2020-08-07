<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

//Bzhy module version
define('BZHY_VERSION',10000);                                                   //10000 1:Main ver 00 Sec ver 00 Modify ver  

//Error code
define('BZHY_ERROR_INTERNAL', 100);
define('BZHY_ERROR_NOOBJECT', 101);
define('BZHY_ERROR_OBJECTNOTLOAD', 102);
define('BZHY_ERROR_PARAMETERS',103);

//Object type
define('BZHY_OBJECT_CLASS_COMMON', 1);
define('BZHY_OBJECT_CLASS_EXTRA', 2);

//Ports
define('BZHY_AGENT_DEFAULT_PORT', 10050);
define('BZHY_SNMP_DEFAULT_PORT', 161);
define('BZHY_JMX_DEFAULT_PORT', 12345);
define('BZHY_IPMI_DEFAULT_PORT', 623);

//Device runing status
define('BZHY_STATUS_OFFLINE', -4);
define('BZHY_STATUS_SHUTDOWN', -3);
define('BZHY_STATUS_MAINTAINCE',-2);
define('BZHY_STATUS_ERROR', -1);
define('BZHY_STATUS_DISABLED', 0);
define('BZHY_STATUS_RUNING',1);

//Database Field type
define('DATABASE_FIELD_TYPE_STRING', 0);
define('DATABASE_FIELD_TYPE_NUMBER', 1);

define('BZHY_SEX_MAN',1);
define('BZHY_SEX_WOMAN',0);

//input type 
define('BZHYINPUT_TYPE_TEXTBOX',0);
define('BZHYINPUT_TYPE_CHECKBOX',1);
define('BZHYINPUT_TYPE_RADIO',2);

//Data type
define('BZHYDATA_TYPE_NORMAL',0);
define('BZHYDATA_TYPE_BYE',1);
define('BZHYDATA_TYPE_BIT',2);
define('BZHYDATA_TYPE_DATE',3);
define('BZHYDATA_TYPE_TIME',4);
define('BZHYDATA_TYPE_UNIXTIME',5);

//Direct of date SQL
define('BZHY_DIRECTDATE_LESS', 0);
define('BZHY_DIRECTDATE_MORE', 1);

//Host ip type
define('BZHYHOST_IP_TYPE_IP', 1);
define('BZHYHOST_IP_TYPE_GW', 2);
define('BZHYHOST_IP_TYPE_DNS', 3);

//Host IP family
define('BZHYHOST_IP_FAMILY_VER4', 4);
define('BZHYHOST_IP_FAMILY_VER6', 6);

//Interface Type
define('BZHYINTERFACE_TYPE_ENTITY', 1);
define('BZHYINTERFACE_TYPE_DUMMY', 0);
<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

//idc information status
define('IDC_ROOM_NORMAL',1);
define('IDC_ROOM_CLOSED',0);

//file and contact status
define('STATUS_NORMAL',1);
define('STATUS_DISABLED',0);

//input type 
define('INPUT_TYPE_TEXTBOX',0);
define('INPUT_TYPE_CHECKBOX',1);

define('SEX_MAN',1);
define('SEX_WOMAN',0);

//Data type
define('DATA_TYPE_NORMAL',0);
define('DATA_TYPE_BYE',1);
define('DATA_TYPE_BIT',2);
define('DATA_TYPE_DATE',3);
define('DATA_TYPE_TIME',4);
define('DATA_TYPE_UNIXTIME',5);

//Device status
define('DEVICE_STATUS_OFFLINE', 0);
define('DEVICE_STATUS_NORMAL', 1);
define('DEVICE_STATUS_MANTAINCE', 2);

//Device runing status
define('DEVICE_RUNING_STATUS_SHUTDOWN', 0);
define('DEVICE_RUNING_STATUS_RUNING', 1);

//Database Field type
define('DATABASE_FIELD_TYPE_STRING', 0);
define('DATABASE_FIELD_TYPE_NUMBER', 1);

//Number
define('BZHY_NUM_ZERO', 0);
define('BZHY_NUM_ONE', 1);

//Default port of data getting
define('BZHY_AGENT_DEFAULT_PORT', 10050);
define('BZHY_SNMP_DEFAULT_PORT', 161);
define('BZHY_JMX_DEFAULT_PORT', 12345);
define('BZHY_IPMI_DEFAULT_PORT', 623);

//Host ip type
define('HOST_IP_TYPE_IP', 1);
define('HOST_IP_TYPE_GW', 2);
define('HOST_IP_TYPE_DNS', 3);

//Host IP family
define('HOST_IP_FAMILY_VER4', 4);
define('HOST_IP_FAMILY_VER6', 6);
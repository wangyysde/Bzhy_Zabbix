<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

$objects =[
    'base' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyCBase',
        'tablename' => NULL,
        'filename'=>'bzhyCBase',  
        'isinit'=>FALSE,
        'tableAlias'=> NULL,
        'objecttitle'=>NULL,
        'objecttitlefield'=>NULL,
        'uri'=>NULL
    ],
    'objectcommon' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyObjectCommon',
        'tablename' => NULL,
        'filename'=>'bzhyObjectCommon',  
        'isinit'=>FALSE,
        'tableAlias'=> NULL,
        'objecttitle'=>NULL,
        'objecttitlefield'=>NULL,
        'uri'=>NULL
    ],
    'db' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyCDB',
        'tablename' => NULL,
        'filename'=>'bzhyCDB',  
        'isinit'=>FALSE,
        'tableAlias'=> NULL,
        'objecttitle'=>NULL,
        'objecttitlefield'=>NULL,
        'uri'=>NULL
    ],
    'sysset' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyCSysSet',
        'tablename' => 'system_setting',
        'filename'=>'bzhyCSysSet',  
        'isinit'=>TRUE,
        'tableAlias'=> 'sy',
        'objecttitle'=>_('Item Name'),
        'objecttitlefield'=>'setting_title',
        'uri'=> 'bzhySystemSetting.php'
    ],
    'contact' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCContact',
        'tablename' => 'contact',
        'filename'=>['bzhyObjectCommon','bzhyCContact'],
        'isinit'=>TRUE,
        'tableAlias'=> 'co',
        'objecttitle'=>_('Name'),
        'objecttitlefield'=>'contact_name',
        'uri'=> 'bzhycontacts.php'
    ],
    'file' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCFiles',
        'tablename' => 'file',
        'filename'=>['bzhyObjectCommon', 'bzhyCFiles'],
        'isinit'=>TRUE,
        'tableAlias'=> 'fi',
        'objecttitle'=>_('Title'),
        'objecttitlefield'=>'file_title',
        'uri'=> 'bzhyfiles.php'
    ],
    'idc_room' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCIdc',
        'tablename' => 'idc_room',
        'filename'=>['bzhyObjectCommon','bzhyCIdc'],
        'isinit'=>TRUE,
        'tableAlias'=> 'ir',
        'objecttitle'=>_('Name'),
        'objecttitlefield'=>'room_name',
        'uri'=> 'bzhyIDCs.php'
    ],
    
    'idc_box' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCIdcbox',
        'tablename' => 'idc_box',
        'filename'=>['bzhyObjectCommon','bzhyCIdcbox'], 
        'isinit'=>TRUE,
        'tableAlias'=> 'ib',
        'objecttitle'=>_('No'),
        'objecttitlefield'=>'box_no',
        'uri'=> 'bzhyidcboxes.php'
    ],
    'host' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCHost',
        'tablename' => 'hosts',
        'filename'=>'bzhyCHost',
        'isinit'=>TRUE,
        'tableAlias'=> 'h',
        'objecttitle'=>_('Host'),
        'objecttitlefield'=>'host',
        'uri'=> 'bzhyhosts.php'
    ],
    
    'user' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCUser',
        'tablename' => 'users',
        'filename'=>'bzhyCUser',
        'isinit'=>TRUE,
        'tableAlias'=> 'u',
        'objecttitle'=>_('User'),
        'objecttitlefield'=>'name',
        'uri'=> 'bzhyusers.php'
    ],
    'group' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCGroup',
        'tablename' => 'groups',
        'filename'=>'bzhyCGroup',
        'isinit'=>FALSE,
        'tableAlias'=> 'g',
        'objecttitle'=>_('Group'),
        'objecttitlefield'=>'name',
        'uri'=> null
    ],
    
    'relationmap' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyCRelationMap',
        'tablename' => NULL,
        'filename'=>'bzhyCRelationMap',
        'isinit'=>FALSE,
        'tableAlias'=> NULL,
        'objecttitle'=> NULL,
        'objecttitlefield'=>NULL,
        'uri'=> null
    ],
    'api' => [
        'class' => BZHY_OBJECT_CLASS_COMMON,
        'classname' =>'bzhyAPI',
        'tablename' => NULL,
        'filename'=>'bzhyAPI',
        'isinit'=>FALSE,
        'tableAlias'=> NULL,
        'objecttitle'=> NULL,
        'objecttitlefield'=>NULL,
        'uri'=> null
    ],
    'interface' => [
        'class' => BZHY_OBJECT_CLASS_EXTRA,
        'classname' =>'bzhyCHostInterface',
        'tablename' => 'bzhy_interfaces',
        'filename'=>'bzhyCHostInterface',
        'isinit'=>FALSE,
        'tableAlias'=> 'bhi',
        'objecttitle'=> _('Name'),
        'objecttitlefield'=>'name',
        'uri'=> null
    ],
    
];

return $objects;
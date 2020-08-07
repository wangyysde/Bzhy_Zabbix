<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

$FieldLabel['contact'] = ['id' => _('ID'),'contact_name' => _('Name'),'contact_sex'=> _('Sex'),'contact_position' => _('Duty'),
    'contact_company' => _('Company'),'contact_url' => _('Website'), 'contact_addr' => _('Address'),'contact_tel' => _('Tel'),
    'contact_mp' =>_('MP'),'contact_email' =>_('Email'),'contact_qq' =>_('QQ'),'contact_wx' =>_('Weixin'),'contact_desc' =>_('Memory'),
    'contact_status' =>_('Status'), 'contact_createtime' =>_('CreateTime'),'contact_userid' =>_('CreateByUser'),'contact_userip' =>_('CreateFromIP'),
    'contact_lasttime' =>_('ModifyTime'),'contact_lastuserid' =>_('ModifyByUser'),'contact_lastuserip' => _('ModifyFromIP'),'contact_deltime' => _('DisableTime'),
    'contact_deluserid' =>_('DisableByUser'),'contact_deluserip' =>_('DisableFromIP'),'contact_fax' =>_('Fax')
];

$FieldLabel['file'] = ['id' => _('ID'),'file_title' => _('FileTitle'),'file_name' =>  _('FileName'),'file_uploadtime' =>_('CreateTime'),
    'file_userid' => _('CreateByUser'),'file_userip' => _('CreateFromIP'), 'file_lasttime' => _('ModifyTime'),'file_lastuserid' =>  _('ModifyByUser'),
    'file_lastuserip' =>_('ModifyFromIP'),'file_desc' =>_('Description'),'file_status' =>_('Status'),'file_deltime' => _('DisableTime'), 'file_deluserid' => _('DisableByUser'),
    'file_deluserip' =>_('DisableFromIP')
];

$FieldLabel['idc_box'] = ['id' => _('ID'),'room_id' => _('Room Name'),                  
    'box_no'=>  _('Box No'),                   
    'box_secno' => _('box_secno'),                  
    'box_starttime' =>  _('Active since'),                    
    'box_endtime' =>  _('Active till'),                    
    'box_closedtime' => _('Closed At'),                   
    'box_desc' =>  _('Description'),                   
    'box_status' =>  _('Status'),                    
    'box_outbandwidth' =>  _('Bandwidth'),                 
    'box_iplist' => _('IP List'),                   
    'box_height' => _('Height'),                   
    'box_userid' => _('CreateByUser'),                  
    'box_createtime' => _('Create At'),                   
    'box_userip' => _('CreateFromIP'),                 
    'box_lasttime' => _('Modify At'),                  
    'box_lastuserid' => _('ModifyByUser'),               
    'box_lastuserip' =>_('ModifyFromIP'),                  
    'box_deltime' => _('Closed At'),               
    'box_deluserid' => _('ClosedByUser'),                  
    'box_deluserip' =>_('ClosedFromIP')            
];

$FieldLabel['idc_room'] = [
    'id' =>  _('ID'),
    'room_name' => _('Name'),
    'room_shortname' => _('Short Name'),
    'room_addr' =>  _('Address'),
    'room_status' =>_('Status'),
    'room_starttime' =>_('Enable At'),
    'room_endtime' =>('Still To'),
    'room_closedtime' => _('Closed At'),
    'room_comment' =>_('Description'),
    'room_userid' =>_('CreateByUser'),
    'room_createtime' =>_('Create At'),
    'room_userip' =>_('CreateFromIP'),
    'room_lasttime' => _('Modify At'),
    'room_lastuserid' =>_('ModifyByUser'),
    'room_lastuserip' => _('ModifyFromIP'),
    'room_deltime' =>_('Closed At'),
    'room_deluserid' =>_('ClosedByUser'),
    'room_deluserip' =>_('ClosedFromIP')
];

$FieldLabel['group'] = [
    'groupid' => _('ID'),
    'name' =>_('Name')
];
return $FieldLabel;
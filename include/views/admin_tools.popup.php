<?php
/*
** Zabbix
** Copyright (C) 2001-2016 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

require_once dirname(__FILE__).'/../js.inc.php';
global $System_Settings;
if(strtolower($data['objectName']) == "contact"){
    $data['DetailsData']['contact_sex'] = $data['DetailsData']['contact_sex'] == SEX_MAN ?_('Sir'):_('Lady');
    $data['DetailsData']['contact_url'] = new CLink($data['DetailsData']['contact_url'],$data['DetailsData']['contact_url']);
    $data['DetailsData']['contact_status'] = $data['DetailsData']['contact_status'] == STATUS_NORMAL ?_('Normal'):_('Disabled');
    $data['title'] = _('Contact Details');
    $data['DetailsData']['contact_userid'] = isset($data['DetailsData']['CreateUserInfo']['name'])?$data['DetailsData']['CreateUserInfo']['name']:SPACE;
    $data['DetailsData']['contact_createtime'] = zbx_empty($data['DetailsData']['contact_createtime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['contact_createtime']);
    $data['DetailsData']['contact_lastuserid'] = isset($data['DetailsData']['LastUserInfo']['name'])?$data['DetailsData']['LastUserInfo']['name']:SPACE;
    $data['DetailsData']['contact_lasttime'] = zbx_empty($data['DetailsData']['contact_lasttime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['contact_lasttime']);
    $data['DetailsData']['contact_deluserid'] = isset($data['DetailsData']['DelUserInfo']['name'])?$data['DetailsData']['DelUserInfo']['name']:SPACE;
    $data['DetailsData']['contact_deltime'] = zbx_empty($data['DetailsData']['contact_deltime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['contact_deltime']);
    $selectedfiles = [];
    if(is_array($data['selectedfiles'])){
        foreach ($data['selectedfiles'] as $id => $row){
            $selectedfiles[] = new CLink($row['file_title'],'file_maintence.php?id='.$id.'&action=details.posted');
            $selectedfiles[] = SPACE;
        }
    }
    $data['labels']['selectedfiles'] = _('Related Files');
    $data['DetailsData']['selectedfiles'] = $selectedfiles;
    $relatedObjects = [];
    if(isset($data['DetailsData']['relatedObjects']) && @is_array($data['DetailsData']['relatedObjects'])){
        foreach ($data['DetailsData']['relatedObjects'] as $object_table => $object){
            if(isset($object['object_info']) && is_array($object['object_info'])){
                $relatedObjects[] = $object['object_type'].":";
                $objectUrl = DB::getObjectUrl($object_table);
                foreach ($object['object_info'] as $object_pk_value => $value){
                    $url= $objectUrl.'?action=details.posted&id='.$object_pk_value;
                    $detailurl = (new CLink($value['object_item_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $relatedObjects[] = !zbx_empty($objectUrl)?$detailurl:$value['object_item_name'];
                    $relatedObjects[]=SPACE;
                }
            }
        }
    }
    $data['labels']['relatedObjects'] = _('Related Objects');
    $data['DetailsData']['relatedObjects'] = $relatedObjects;
}
elseif(strtolower($data['objectName']) == "file"){
    $data['DetailsData']['file_uploadtime'] = zbx_empty($data['DetailsData']['file_uploadtime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['file_uploadtime']);
    $data['DetailsData']['file_userid'] = isset($data['DetailsData']['CreateUserInfo']['name'])?$data['DetailsData']['CreateUserInfo']['name']:SPACE;
    $data['DetailsData']['file_lasttime'] = zbx_empty($data['DetailsData']['file_lasttime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['file_lasttime']);
    $data['DetailsData']['file_lastuserid'] = isset($data['DetailsData']['LastUserInfo']['name'])?$data['DetailsData']['LastUserInfo']['name']:SPACE;
    $data['DetailsData']['file_status'] = $data['DetailsData']['file_status'] == STATUS_NORMAL ?_('Normal'):_('Disabled');
    $data['title'] = _('File Details');
    $data['DetailsData']['file_deluserid'] = isset($data['DetailsData']['DelUserInfo']['name'])?$data['DetailsData']['DelUserInfo']['name']:SPACE;
    $data['DetailsData']['file_deltime'] = zbx_empty($data['DetailsData']['file_deltime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['file_deltime']);
    $relatedObjects = [];
    if(isset($data['DetailsData']['relatedObjects']) && @is_array($data['DetailsData']['relatedObjects'])){
        foreach ($data['DetailsData']['relatedObjects'] as $object_table => $object){
            if(isset($object['object_info']) && is_array($object['object_info'])){
                $relatedObjects[] = $object['object_type'].":";
                $objectUrl = DB::getObjectUrl($object_table);
                foreach ($object['object_info'] as $object_pk_value => $value){
                    $url= $objectUrl.'?action=details.posted&id='.$object_pk_value;
                    $detailurl = (new CLink($value['object_item_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $relatedObjects[] = !zbx_empty($objectUrl)?$detailurl:$value['object_item_name'];
                    $relatedObjects[]= SPACE;
                }
            }
        }
    }
    $data['labels']['relatedObjects'] = _('Related Objects');
    $data['DetailsData']['relatedObjects'] = $relatedObjects;    
}
elseif (strtolower($data['objectName']) == "idc_room" || strtolower($data['objectName']) == "idc_box") {
    $objectName = $data['objectName'];
    $data['title'] = ($objectName === "idc_room")?_('IDC Room Details'):_('IDC Box Details');
    if($objectName === "idc_box"){
        $idcRoomStr="";
        if(isset($data['DetailsData']['selectIdcRoom'])){
            foreach ($data['DetailsData']['selectIdcRoom'] as $idcroom_id => $idc_room){
                if(isset($idc_room['room_name'])){
                    $url= 'idc_information_maintence.php?action=details.posted&id='.$idc_room['id'];
                    $idcRoomStr[]= (new CLink($idc_room['room_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $idcRoomStr[] = SPACE;
                }
            }
        }
        $data['DetailsData']['room_id'] = $idcRoomStr;
    }
    $obj = ($objectName === "idc_room")?"room":"box";
    $data['DetailsData'][$obj.'_status'] = $data['DetailsData'][$obj.'_status'] == STATUS_NORMAL ?_('Normal'):_('Closed');
    $data['DetailsData'][$obj.'_starttime'] = zbx_empty($data['DetailsData'][$obj.'_starttime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_starttime']);
    $data['DetailsData'][$obj.'_endtime'] = zbx_empty($data['DetailsData'][$obj.'_endtime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_endtime']);
    $data['DetailsData'][$obj.'_closedtime'] = zbx_empty($data['DetailsData'][$obj.'_closedtime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_closedtime']);
    $data['DetailsData'][$obj.'_userid'] = isset($data['DetailsData']['CreateUserInfo']['name'])?$data['DetailsData']['CreateUserInfo']['name']:SPACE;
    $data['DetailsData'][$obj.'_createtime'] = zbx_empty($data['DetailsData'][$obj.'_createtime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_createtime']);
    $data['DetailsData'][$obj.'_lasttime'] = zbx_empty($data['DetailsData'][$obj.'_lasttime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_lasttime']);
    $data['DetailsData'][$obj.'_deltime'] = zbx_empty($data['DetailsData'][$obj.'_deltime'])?SPACE:date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData'][$obj.'_deltime']);
    $data['DetailsData'][$obj.'_lastuserid'] = isset($data['DetailsData']['LastUserInfo']['name'])?$data['DetailsData']['LastUserInfo']['name']:SPACE;
    $data['DetailsData'][$obj.'_deluserid'] = isset($data['DetailsData']['DelUserInfo']['name'])?$data['DetailsData']['DelUserInfo']['name']:SPACE;
    $selectedfiles = [];
    if(is_array($data['selectedfiles'])){
        foreach ($data['selectedfiles'] as $id => $row){
            $selectedfiles[] = new CLink($row['file_title'],'file_maintence.php?id='.$id.'&action=details.posted');
            $selectedfiles[] = SPACE;
        }
    }
    $data['labels']['selectedfiles'] = _('Relating Files');
    $data['DetailsData']['selectedfiles'] = $selectedfiles;
    $selectedContacts = [];
    if(is_array($data['selectedContact'])){
        foreach ($data['selectedContact'] as $id => $row){
            $selectedContacts[] = new CLink($row['contact_name'],'contact_maintence.php?id='.$id.'&action=details.posted');
            $selectedContacts[] = SPACE;
        }
    }
    $data['labels']['selectedContacts'] = _('Relating Contacts');
    $data['DetailsData']['selectedContacts'] = $selectedContacts;
    
}

$widget = (new CWidget())
	->setTitle($data['title']);

$table = (new CTableInfo())
	->setHeader([
		_('Name'),
                _('Content')
	]);

foreach ($data['labels'] as $field=>$value) {       
    $table->addRow([
        (new CCol($data['labels'][$field])),
        (new CCol(zbx_empty($data['DetailsData'][$field])?SPACE:$data['DetailsData'][$field]))
    ]);
}
$closeButton = new CButton('close', _('Close'));
$closeButton->onClick("Javascript:close_window();");
$closeRow=(new CCol($closeButton))
        ->setColSpan(2)
        ->setAttribute('align', 'center');
$table->addRow([$closeRow]);
$widget->addItem($table);

return $widget;

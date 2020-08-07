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
require_once dirname(__FILE__).'/../js/common.js.php';

$widget = (new CWidget())
	->setTitle(_('IDC Box Maintence'))
	->setControls((new CForm('get'))
		->cleanItems()
                ->addItem(new CSubmit('form', _('Create IdcBox')))
                ->addVar('action', 'add', 'action')
	);


// table idcroom list
$form = (new CForm())->setName('idcbox');

$table = (new CTableInfo())
	->setHeader([
            make_sorting_header(_('Box No'), 'box_no', $data['sortField'], $data['sortOrder']),
		_('Box Sec No'),
            make_sorting_header(_('Status'), 'box_status', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('StartTime'),'box_starttime', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('EndTime'),'box_endtime', $data['sortField'], $data['sortOrder']),
		_('IP List'),
		_('IdcRoom'),
		_('Contactor'),
		_('Files'),
                _('Action')
	]);

$current_time = time();

foreach ($data['idc_box'] as $idc_box) {
        $parasClose = "action=close.posted&id=".$idc_box['id'];
        $closeUrl = (new CLink(_('Close'),'#'))->onClick('Javascript:confirmAndRefresh(\'idcbox_maintence.php\',\''._('Are sure close this IDC Box ?').'\',\''.$parasClose.'\')');
        $parasOpen = "action=open.posted&id=".$idc_box['id'];
        $openUrl = (new CLink(_('Open'),'#'))->onClick('Javascript:confirmAndRefresh(\'idcbox_maintence.php\',\''._('Are sure open this IDC Box?').'\',\''.$parasOpen.'\')');
        $CloseOrOpen = ($idc_box['box_status'] == IDC_ROOM_NORMAL) ? $closeUrl:$openUrl;
        $parasDel = "action=del.posted&id=".$idc_box['id'];
        $DelUrl = ($idc_box['box_status'] == IDC_ROOM_CLOSED)?(new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'idcbox_maintence.php\',\''._('Are sure DELETE this IDC Box infromation?').'\',\''.$parasDel.'\')'):_('Delete');
        $modifyidcbox = ($idc_box['box_status'] == IDC_ROOM_NORMAL) ? (new CLink(_('Modify'),
		'idcbox_maintence.php?action=modify&id='.$idc_box['id'])):_('Modify');
        $starttime = is_null($idc_box['box_starttime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_box['box_starttime']);
	$endtime = is_null($idc_box['box_endtime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_box['box_endtime']);
     //   $createuser = isset($idc_room['CreateUserInfo']['name']) ? $idc_room['CreateUserInfo']['name']:SPACE;
     //   $lastuser = isset($idc_room['LastUserInfo']['name']) ? $idc_room['LastUserInfo']['name']:SPACE;
     //   $deluser = isset($idc_room['LastUserInfo']['name']) ? $idc_room['DelUserInfo']['name']:SPACE;
        $url= 'idcbox_maintence.php?action=details.posted&id='.$idc_box['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        
        $idcRoomStr = null;
        if(isset($idc_box['selectIdcRoom']) && is_array($idc_box['selectIdcRoom'])){
            foreach ($idc_box['selectIdcRoom'] as $idcroom_id => $idc_room){
                if(isset($idc_room['room_name'])){
                    $url= 'idc_information_maintence.php?action=details.posted&id='.$idc_room['id'];
                    $idcRoomStr[]= (new CLink($idc_room['room_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $idcRoomStr[] = SPACE;
                }
            }
        }
        $contactStr = null; 
        if(isset($idc_box['selectContact']) && is_array($idc_box['selectContact'])){
            foreach ($idc_box['selectContact'] as $contact){
                if(isset($contact['contact_name'])){
                    $url= 'contact_maintence.php?action=details.posted&id='.$contact['id'];
                    $contactStr[]= (new CLink($contact['contact_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $contactStr[] = SPACE;
                }
            }
        }
        $attachStr = null; 
        if(isset($idc_box['SelectFile']) && is_array($idc_box['SelectFile'])){
            foreach ($idc_box['SelectFile'] as $file){
                if(isset($file['file_title'])){
                    $url= 'file_maintence.php?action=details.posted&id='.$file['id'];
                    $attachStr[] = (new CLink($file['file_title'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $attachStr[] =  SPACE;                     
                }
            }
        }
        $table->addRow([
		(new CCol($idc_box['box_no'])),
                (new CCol($idc_box['box_secno'])),
                (new CCol($idc_box['box_status'] == IDC_ROOM_NORMAL ? _('Normal'):_('Closed'))),
                (new CCol($starttime)),
                (new CCol($endtime)),
                (new CCol($idc_box['box_iplist'])),
                (new CCol($idcRoomStr)),
                (new CCol($contactStr)),
                (new CCol($attachStr)),
                (new CCol([$detailurl,SPACE,$modifyidcbox,SPACE,$CloseOrOpen,SPACE,$DelUrl]))
	]);
}
$form->addItem([
	$table,
]);


$widget->addItem($form);

return $widget;

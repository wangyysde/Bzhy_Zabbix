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
	->setTitle(_('IDC Room Maintence'))
	->setControls((new CForm('get'))
		->cleanItems()
                ->addItem(new CSubmit('form', _('Create IdcRoom')))
                ->addVar('action', 'add', 'action')
	);


// table idcroom list
$form = (new CForm())->setName('idcroom');

$table = (new CTableInfo())
	->setHeader([
            make_sorting_header(_('Name'), 'room_name', $data['sortField'], $data['sortOrder']),
		_('Address'),
            make_sorting_header(_('Status'), 'room_status', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('StartTime'),'room_starttime', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('EndTime'),'room_endtime', $data['sortField'], $data['sortOrder']),
		_('CreateByUser'),
		_('IdcBox'),
		_('Contactor'),
		_('Files'),
                _('Action')
	]);

$current_time = time();

foreach ($data['idc_room'] as $idc_room) {
        $parasClose = "action=close.posted&id=".$idc_room['id'];
        $closeUrl = (new CLink(_('Close'),'#'))->onClick('Javascript:confirmAndRefresh(\'idc_information_maintence.php\',\''._('Are sure close this IDC room?').'\',\''.$parasClose.'\')');
        $parasOpen = "action=open.posted&id=".$idc_room['id'];
        $openUrl = (new CLink(_('Open'),'#'))->onClick('Javascript:confirmAndRefresh(\'idc_information_maintence.php\',\''._('Are sure open this IDC room?').'\',\''.$parasOpen.'\')');
        $CloseOrOpen = ($idc_room['room_status'] == IDC_ROOM_NORMAL) ? $closeUrl:$openUrl;
        $parasDel = "action=del.posted&id=".$idc_room['id'];
        $DelUrl = ($idc_room['room_status'] == IDC_ROOM_CLOSED)?(new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'idc_information_maintence.php\',\''._('Are sure DELETE this IDC room infromation?').'\',\''.$parasDel.'\')'):_('Delete');
        $modifyidcroom = ($idc_room['room_status'] == IDC_ROOM_NORMAL) ? (new CLink(_('Modify'),
		'idc_information_maintence.php?action=modify&id='.$idc_room['id'])):_('Modify');
        $starttime = is_null($idc_room['room_starttime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_room['room_starttime']);
	$endtime = is_null($idc_room['room_endtime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_room['room_endtime']);
        $createuser = isset($idc_room['CreateUserInfo']['name']) ? $idc_room['CreateUserInfo']['name']:SPACE;
     //   $lastuser = isset($idc_room['LastUserInfo']['name']) ? $idc_room['LastUserInfo']['name']:SPACE;
     //   $deluser = isset($idc_room['LastUserInfo']['name']) ? $idc_room['DelUserInfo']['name']:SPACE;
        $url= 'idc_information_maintence.php?action=details.posted&id='.$idc_room['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        
        $idcBoxStr = null;
        if(isset($idc_room['selectIdcBox']) && is_array($idc_room['selectIdcBox'])){
            foreach ($idc_room['selectIdcBox'] as $key => $idcbox){
                if(isset($idcbox['box_no'])){
                    $url= 'idcbox_maintence.php?action=details.posted&id='.$idcbox['id'];
                    $idcBoxStr[]= (new CLink($idcbox['box_no'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $idcBoxStr[] = SPACE;
                }
            }
        }
        $contactStr = null; 
        if(isset($idc_room['selectContact']) && is_array($idc_room['selectContact'])){
            foreach ($idc_room['selectContact'] as $contact){
                if(isset($contact['contact_name'])){
                    $url= 'contact_maintence.php?action=details.posted&id='.$contact['id'];
                    $contactStr[]= (new CLink($contact['contact_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $contactStr[] = SPACE;
                }
            }
        }
        $attachStr = null; 
        if(isset($idc_room['SelectFile']) && is_array($idc_room['SelectFile'])){
            foreach ($idc_room['SelectFile'] as $file){
                if(isset($file['file_title'])){
                    $url= 'file_maintence.php?action=details.posted&id='.$file['id'];
                    $attachStr[] = (new CLink($file['file_title'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $attachStr[] =  SPACE;                     
                }
            }
        }
        $table->addRow([
		(new CCol($idc_room['room_name'])),
                (new CCol($idc_room['room_addr'])),
                (new CCol($idc_room['room_status'] == IDC_ROOM_NORMAL ? _('Normal'):_('Closed'))),
                (new CCol($starttime)),
                (new CCol($endtime)),
                (new CCol($createuser)),
                (new CCol($idcBoxStr)),
                (new CCol($contactStr)),
                (new CCol($attachStr)),
                (new CCol([$detailurl,SPACE,$modifyidcroom,SPACE,$CloseOrOpen,SPACE,$DelUrl]))
	]);
}
$form->addItem([
	$table,
//	$data['paging']
]);


$widget->addItem($form);

return $widget;

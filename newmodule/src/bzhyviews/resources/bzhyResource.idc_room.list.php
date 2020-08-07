<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


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
        $closeUrl = (new CLink(_('Close'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyIDCs.php\',\''._('Are sure close this IDC room?').'\',\''.$parasClose.'\')');
        $parasOpen = "action=open.posted&id=".$idc_room['id'];
        $openUrl = (new CLink(_('Open'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyIDCs.php\',\''._('Are sure open this IDC room?').'\',\''.$parasOpen.'\')');
        $CloseOrOpen = ($idc_room['room_status'] == BZHY_STATUS_RUNING) ? $closeUrl:$openUrl;
        $parasDel = "action=del.posted&id=".$idc_room['id'];
        $DelUrl = ($idc_room['room_status'] == BZHY_STATUS_DISABLED)?(new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyIDCs.php\',\''._('Are sure DELETE this IDC room infromation?').'\',\''.$parasDel.'\')'):_('Delete');
        $modifyidcroom = ($idc_room['room_status'] == BZHY_STATUS_RUNING) ? (new CLink(_('Modify'),
		'bzhyIDCs.php?action=modify&id='.$idc_room['id'])):_('Modify');
        $starttime = is_null($idc_room['room_starttime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_room['room_starttime']);
	$endtime = is_null($idc_room['room_endtime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_room['room_endtime']);
        $createuser = isset($idc_room['CreateUserInfo']['name']) ? $idc_room['CreateUserInfo']['name']:SPACE;
     
        $url= 'bzhyIDCs.php?action=details.posted&id='.$idc_room['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        
        $idcBoxStr = null;
        if(isset($idc_room['selectIdcBox']) && is_array($idc_room['selectIdcBox'])){
            foreach ($idc_room['selectIdcBox'] as $key => $idcbox){
                if(isset($idcbox['box_no'])){
                    $url= 'bzhyIDCs.php?action=details.posted&id='.$idcbox['id'];
                    $idcBoxStr[]= (new CLink($idcbox['box_no'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $idcBoxStr[] = SPACE;
                }
            }
        }
        $contactStr = null; 
        if(isset($idc_room['selectContact']) && is_array($idc_room['selectContact'])){
            foreach ($idc_room['selectContact'] as $contact){
                if(isset($contact['contact_name'])){
                    $url= 'bzhyIDCs.php?action=details.posted&id='.$contact['id'];
                    $contactStr[]= (new CLink($contact['contact_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $contactStr[] = SPACE;
                }
            }
        }
        $attachStr = null; 
        if(isset($idc_room['SelectFile']) && is_array($idc_room['SelectFile'])){
            foreach ($idc_room['SelectFile'] as $file){
                if(isset($file['file_title'])){
                    $url= 'bzhyIDCs.php?action=details.posted&id='.$file['id'];
                    $attachStr[] = (new CLink($file['file_title'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $attachStr[] =  SPACE;                     
                }
            }
        }
        $table->addRow([
		(new CCol($idc_room['room_name'])),
                (new CCol($idc_room['room_addr'])),
                (new CCol($idc_room['room_status'] == BZHY_STATUS_RUNING ? _('Normal'):_('Closed'))),
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

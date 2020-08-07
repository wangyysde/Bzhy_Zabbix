<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


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
        $closeUrl = (new CLink(_('Close'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyidcboxes.php\',\''._('Are sure close this IDC Box ?').'\',\''.$parasClose.'\')');
        $parasOpen = "action=open.posted&id=".$idc_box['id'];
        $openUrl = (new CLink(_('Open'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyidcboxes.php\',\''._('Are sure open this IDC Box?').'\',\''.$parasOpen.'\')');
        $CloseOrOpen = ($idc_box['box_status'] == BZHY_STATUS_RUNING) ? $closeUrl:$openUrl;
        $parasDel = "action=del.posted&id=".$idc_box['id'];
        $DelUrl = ($idc_box['box_status'] == BZHY_STATUS_DISABLED)?(new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyidcboxes.php\',\''._('Are sure DELETE this IDC Box infromation?').'\',\''.$parasDel.'\')'):_('Delete');
        $modifyidcbox = ($idc_box['box_status'] == BZHY_STATUS_RUNING) ? (new CLink(_('Modify'),
		'bzhyidcboxes.php?action=modify&id='.$idc_box['id'])):_('Modify');
        $starttime = is_null($idc_box['box_starttime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_box['box_starttime']);
	$endtime = is_null($idc_box['box_endtime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$idc_box['box_endtime']);
        $url= 'bzhyidcboxes.php?action=details.posted&id='.$idc_box['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        
        $idcRoomStr = null;
        if(isset($idc_box['selectIdcRoom']) && is_array($idc_box['selectIdcRoom'])){
            foreach ($idc_box['selectIdcRoom'] as $idcroom_id => $idc_room){
                if(isset($idc_room['room_name'])){
                    $url= 'bzhyIDCs.php?action=details.posted&id='.$idc_room['id'];
                    $idcRoomStr[]= (new CLink($idc_room['room_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $idcRoomStr[] = SPACE;
                }
            }
        }
        $contactStr = null; 
        if(isset($idc_box['selectContact']) && is_array($idc_box['selectContact'])){
            foreach ($idc_box['selectContact'] as $contact){
                if(isset($contact['contact_name'])){
                    $url= 'bzhycontacts.php?action=details.posted&id='.$contact['id'];
                    $contactStr[]= (new CLink($contact['contact_name'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $contactStr[] = SPACE;
                }
            }
        }
        $attachStr = null; 
        if(isset($idc_box['SelectFile']) && is_array($idc_box['SelectFile'])){
            foreach ($idc_box['SelectFile'] as $file){
                if(isset($file['file_title'])){
                    $url= 'bzhyfiles.php?action=details.posted&id='.$file['id'];
                    $attachStr[] = (new CLink($file['file_title'],'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
                    $attachStr[] =  SPACE;                     
                }
            }
        }
        $table->addRow([
		(new CCol($idc_box['box_no'])),
                (new CCol($idc_box['box_secno'])),
                (new CCol($idc_box['box_status'] == BZHY_STATUS_RUNING ? _('Normal'):_('Closed'))),
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

<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

require_once dirname(__FILE__).'/../../bzhyjs/bzhyResource.idc_room.js.php';

$widget = (new CWidget())->setTitle(_('IDC Information Maintence'));

$idcroomForm = (new CForm())
	->setName($this->data['form'])
        ->setId($this->data['form'])
	->addVar('action', $this->data['action']);

if($this->data['action'] === 'modify.posted'){
    $idcroomForm->addVar('id', $this->data['idc_room']['id']);
}

$idcroomFormList = (new CFormList())
	->addRow(_('Name'),
		(new CTextBox('name', isset($this->data['idc_room']['room_name']) ? $this->data['idc_room']['room_name']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
			->setAttribute('autofocus', 'autofocus')
	);
$idcroomFormList->addRow(_('Shortname'),
                (new CTextBox('shortname',isset($this->data['idc_room']['room_shortname']) ? $this->data['idc_room']['room_shortname']:""))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$idcroomFormList->addRow(_('Active since'), createDateMenu('room_starttime', isset($this->data['room_starttime'])?$this->data['room_starttime']:time(),null,FALSE));
$idcroomFormList->addRow(_('Active till'), createDateMenu('room_endtime', isset($this->data['room_endtime'])?$this->data['room_endtime']:time(),null,FALSE ));

$idcroomFormList->addRow(_('Address'),
		(new CTextBox('room_addr', isset($this->data['idc_room']['room_addr']) ? $this->data['idc_room']['room_addr']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$idcroomFormList->addRow(_('Description'),
	(new CTextArea('room_comment',isset($this->data['idc_room']['room_comment']) ? $this->data['idc_room']['room_comment']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);
$contactTB = new CTweenBox($idcroomForm, 'contacts', NULL, 10);
foreach ($this->data['allContacts'] as $id => $row){
    $contactTB->addItem($row['id'], $row['contact_name'], in_array($row['id'], $this->data['selectedContactIds'])?'yes':'no' ,TRUE);
}
$idcroomFormList->addRow(_('Contacts'), $contactTB->get(_('In Contacts'), _('Other Contacts')));
$fileTB = new CTweenBox($idcroomForm, 'files', NULL, 10);
foreach ($this->data['allFiles'] as $id => $row){
    $fileTB->addItem($row['id'], $row['file_title'], in_array($row['id'], $this->data['selectedFileIds'])?'yes':'no' ,TRUE);
}
$idcroomFormList->addRow(_('Files'), $fileTB->get(_('In Files'), _('Other Files')));

$idcroomTabs = (new CTabView())->addTab('idcroomTab', _('idc_information_maintence'), $idcroomFormList);

if ($this->data['action'] == 'modify.posted')
{
    $submitbutton = new CButton('update', _('Update'));
    $submitbutton->onClick("Javascript:idc_submit();");
    $idcroomTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
    
}
else {
        $submitbutton = new CButton('add', _('Add'));
        $submitbutton->onClick("Javascript:idc_submit();");
	$idcroomTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));

}

$idcroomForm->addItem($idcroomTabs);
$widget->addItem($idcroomForm);

return $widget;


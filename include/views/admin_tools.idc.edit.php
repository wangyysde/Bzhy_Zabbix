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
require_once dirname(__FILE__).'/js/admin_tools.idc.js.php';

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
$idcroomFormList->addRow(_('Active since'), createDateSelector('room_starttime', $this->data['room_starttime'], 'room_endtime'));
$idcroomFormList->addRow(_('Active till'), createDateSelector('room_endtime', $this->data['room_endtime'], 'room_starttime'));
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


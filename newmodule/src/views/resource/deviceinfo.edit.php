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
require_once dirname(__FILE__).'/../js/deviceinfo.js.php';
require_once dirname(__FILE__).'/../js/common.js.php';

$formTitle = ($this->data['action'] == 'add.posted')?_('Add Device'):_('Modify Device Info');
$widget = (new CWidget())->setTitle($formTitle);

$deviceForm = (new CForm())
	->setName($this->data['form'])
        ->setId($this->data['form'])
	->addVar('action', $this->data['action']);

if($this->data['action'] === 'modify.posted'){
    $deviceForm->addVar('id', $this->data['deviceinfo']['id']);
}

$deviceFormList =new CFormList();

foreach($this->data['allType'] as $id => $line){
    $items[$id] = $line['typename'];
}
$deviceFormList->addRow(_('Type'),
    (new CComboBox('typeid',isset($this->data['deviceinfo']['typeid'])?$this->data['deviceinfo']['typeid']:"","",$items))
        ->setAttribute('autofocus', 'autofocus')
);

unset($items);
foreach($this->data['allSize'] as $id => $value){
    $items[$id] = $value;
}
$deviceFormList->addRow(_('Size'),
    (new CComboBox('size',isset($this->data['deviceinfo']['size'])?$this->data['deviceinfo']['size']:"","",$items))
);

$deviceFormList->addRow(_('Model'),
        (new CTextBox('model', isset($this->data['deviceinfo']['model']) ? $this->data['deviceinfo']['model']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('SerialNo'),
        (new CTextBox('serialno', isset($this->data['deviceinfo']['serialno']) ? $this->data['deviceinfo']['serialno']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('ServiceNo'),
        (new CTextBox('serviceno', isset($this->data['deviceinfo']['serviceno']) ? $this->data['deviceinfo']['serviceno']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );
$deviceFormList->addRow(_('HardInfo'),
        (new CTextBox('serviceno', isset($this->data['deviceinfo']['hardinfo']) ? $this->data['deviceinfo']['hardinfo']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('PurchaseTime'), createDateMenu('createdate', isset($this->data['createdate'])?$this->data['createdate']:time()));

/*
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

*/

$deviceTabs = (new CTabView())->addTab('idcroomTab', _('idc_information_maintence'), $deviceFormList);


if ($this->data['action'] == 'modify.posted')
{
    $submitbutton = new CButton('update', _('Update'));
    $submitbutton->onClick("Javascript:idc_submit();");
    $deviceTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
    
}
else {
        $submitbutton = new CButton('add', _('Add'));
        $submitbutton->onClick("Javascript:idc_submit();");
	$deviceTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));

}

$deviceForm->addItem($deviceTabs);
$widget->addItem($deviceForm);

return $widget;


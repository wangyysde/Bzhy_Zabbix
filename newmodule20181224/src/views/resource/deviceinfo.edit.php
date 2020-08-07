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
foreach($this->data['allBrand'] as $id => $line){
    $items[$id] = $line['local_name'];
}
$deviceFormList->addRow(_('Brand'),
    (new CComboBox('brandid',isset($this->data['deviceinfo']['brandid'])?$this->data['deviceinfo']['brandid']:"","",$items))
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
        (new CTextBox('hardinfo', isset($this->data['deviceinfo']['hardinfo']) ? $this->data['deviceinfo']['hardinfo']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('PurchaseTime'), createDateMenu('createdate', isset($this->data['deviceinfo']['createdate'])?$this->data['deviceinfo']['createdate']:time()));

$deviceFormList->addRow(_('WarrantySince'), createDateMenu('warrantystartdate', isset($this->data['deviceinfo']['warrantystartdate'])?$this->data['deviceinfo']['warrantystartdate']:time()));

$deviceFormList->addRow(_('WarrantyTo'), createDateMenu('warrantyenddate', isset($this->data['deviceinfo']['warrantyenddate'])?$this->data['deviceinfo']['warrantyenddate']:time()));

$deviceFormList->addRow(_('BuyFrom'),
        (new CTextBox('agent', isset($this->data['deviceinfo']['agent']) ? $this->data['deviceinfo']['agent']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('HostName'),
        (new CTextBox('hostname', isset($this->data['deviceinfo']['hostname']) ? $this->data['deviceinfo']['hostname']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('IPList'),
        (new CTextBox('ips', isset($this->data['deviceinfo']['ips']) ? $this->data['deviceinfo']['ips']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('DNSList'),
        (new CTextBox('dns', isset($this->data['deviceinfo']['dns']) ? $this->data['deviceinfo']['dns']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$deviceFormList->addRow(_('GateWay'),
        (new CTextBox('gw', isset($this->data['deviceinfo']['gw']) ? $this->data['deviceinfo']['gw']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

unset($items);
$i=0;
foreach($this->data['allRooms'] as $id => $line){
    if($i == 0){
        $firstRoomid = $id;
    }
    $i++;
    $items[$id] = $line['room_name'];
}
$deviceFormList->addRow(_('Room'),
    (new CComboBox('roomid',isset($this->data['deviceinfo']['roomid'])?$this->data['deviceinfo']['roomid']:"","",$items))
        ->setId('roomid')
        ->onChange("chgSubSel('roomid','subboxid',null,'/device_list.php','action=formajax.getboxes')")
);

$FirstBox = $this->data['idcbox'][$firstRoomid];

unset($items);
foreach($FirstBox as $id => $line){
    $items[$id] = $line['box_no'];
}

$deviceFormList->addRow(_('Box'),
    (new CComboBox('boxid',isset($this->data['deviceinfo']['boxid'])?$this->data['deviceinfo']['boxid']:"","",$items))
        ->setId('subboxid')
);

$deviceFormList->addRow(_('Position'),
        (new CTextBox('position', isset($this->data['deviceinfo']['position']) ? $this->data['deviceinfo']['position']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

unset($items);
foreach($this->data['allIndepend'] as $id => $line){
    $items[$id] = $line['hostname'];
}
if(!isset($items)){
    $items = [];
}
$deviceFormList->addRow(_('BelongFrom'),
    (new CComboBox('belongdeviceid',isset($this->data['deviceinfo']['belongdeviceid'])?$this->data['deviceinfo']['belongdeviceid']:"","",$items))
);

unset($items);
foreach($this->data['allOS'] as $id => $line){
    $items[$id] = $line['osname'];
}
$deviceFormList->addRow(_('OS'),
    (new CComboBox('osid',isset($this->data['deviceinfo']['osid'])?$this->data['deviceinfo']['osid']:"","",$items))
);

$deviceFormList->addRow(_('Description'),
	(new CTextArea('desc',isset($this->data['deviceinfo']['desc']) ? $this->data['deviceinfo']['desc']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);

$this->data['selectedContactIds'] = isset($this->data['deviceinfo']['selectContact'])?array_keys($this->data['deviceinfo']['selectContact']):"";
$contactTB = new CTweenBox($deviceForm, 'contacts', NULL, 10);

foreach ($this->data['allContacts'] as $id => $row){
    $contactTB->addItem($row['id'], $row['contact_name'], is_array($this->data['selectedContactIds'])?(in_array($row['id'], $this->data['selectedContactIds'])?'yes':'no'):'no' ,TRUE);
}
$deviceFormList->addRow(_('Contacts'), $contactTB->get(_('In Contacts'), _('Other Contacts')));

$this->data['selectedFileIds'] = isset($this->data['deviceinfo']['SelectFile'])?array_keys($this->data['deviceinfo']['SelectFile']):"";
$fileTB = new CTweenBox($deviceForm, 'files', NULL, 10);
foreach ($this->data['allFiles'] as $id => $row){
    $fileTB->addItem($row['id'], $row['file_title'], is_array($this->data['selectedFileIds'])?(in_array($row['id'], $this->data['selectedFileIds'])?'yes':'no'):'no' ,TRUE);
}
$deviceFormList->addRow(_('Files'), $fileTB->get(_('In Files'), _('Other Files')));

$deviceTabs = (new CTabView())->addTab('deviceTab', _('Device Information'), $deviceFormList);


if ($this->data['action'] == 'modify.posted')
{
    $submitbutton = new CButton('update', _('Update'));
}
else {
        $submitbutton = new CButton('add', _('Add'));
}
$submitbutton->onClick("Javascript:".$this->data['form']."_submit();");
$deviceTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
    
$deviceForm->addItem($deviceTabs);
$widget->addItem($deviceForm);

return $widget;


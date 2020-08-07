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
require_once dirname(__FILE__).'/../js/system_setting.js.php';
require_once dirname(__FILE__).'/../js/common.js.php';

$systemSettingComboBox = (new CComboBox('settingtype_id', $data['settingtype_id'], 'submit()'));
foreach ($data['settingType'] as $key => $row){
    $systemSettingComboBox->addItem($key, $row["settingtype_name"]);
}
$widget = (new CWidget())
	->setTitle(_('SystemSetting'))
        ->setControls((new CForm('get'))
		->cleanItems()
		->addItem((new CList())
			->addItem([_('System Setting'), SPACE, $systemSettingComboBox])
		)
	);
        
$frmSystemSetting = (new CForm())
	->setName($data['form'])
        ->setId($data['form'])
        ->addVar('form', $data['form'])
        ->addVar('update', $data['update'])
	->addVar('settingtype_id', $data['settingtype_id']);
$settingFormList = new CFormList('settingFormList');
foreach ($data['settingItem'] as $key => $item){
    $inputParas['type'] = $item['input_type'];
    $inputParas['name'] = $item['setting_name'];
    $inputParas['js_check'] = strtolower(trim($item['js_check_method'])) === 'none'?FALSE:$item['js_check_method'];
    $inputParas['id'] = $item['id'];
    if($inputParas['type'] == INPUT_TYPE_CHECKBOX){    //For CheckBox
        $enableValues = [];
        foreach ($item['items'] as $id => $fieldvalue){
            $enableValues[$id]['value'] = $fieldvalue['id'];
            $enableValues[$id]['label'] = $fieldvalue['item_title'];
        }
        $selectedItems = explode(',', $item['setting_value']);
        foreach ($selectedItems as $value){
            $selectedValues[$value] = $value; 
        }
        $inputLine = buildInput($inputParas,$selectedValues,$enableValues);
    }
    elseif ($inputParas['type'] == INPUT_TYPE_TEXTBOX) {          //For TextBox
        if($item['data_type'] == DATA_TYPE_BYE || $item['data_type'] == DATA_TYPE_BIT){
            $selectedValues[0] = byes2str($item['setting_value']);
        }
        else{
            $selectedValues[0] = $item['setting_value'];
        }
        $inputLine = buildInput($inputParas,$selectedValues);
    }
    else{
        $inputLine = NULL;
    }
    if(!empty($inputLine)){
        $settingFormList->addRow($item['setting_title'],$inputLine);
    }
}

$systemSettingTabs = (new CTabView())->addTab('systemSettingTab', _('SystemSetting'), $settingFormList);
//$submitbutton =  new CSubmit('update', _('Update'));
$submitbutton =  new CButton('update', _('Update'));
$submitbutton ->onClick('javascript:system_setting_submit();');
$systemSettingTabs ->setFooter(makeFormFooter(
       $submitbutton,[new CButtonCancel()]
));
$frmSystemSetting->addItem($systemSettingTabs);

$widget->addItem($frmSystemSetting);
return $widget;

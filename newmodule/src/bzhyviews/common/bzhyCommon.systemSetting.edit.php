<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


require_once dirname(__FILE__).'/../../bzhyjs/bzhyCommon.systemSetting.js.php';

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
    if($inputParas['type'] == BZHYINPUT_TYPE_CHECKBOX){    //For CheckBox
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
    elseif ($inputParas['type'] == BZHYINPUT_TYPE_TEXTBOX) {          //For TextBox
        if($item['data_type'] == BZHYDATA_TYPE_BYE || $item['data_type'] == BZHYDATA_TYPE_BIT){
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

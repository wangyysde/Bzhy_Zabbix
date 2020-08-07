<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

require_once dirname(__FILE__).'/../../bzhyjs/bzhyResource.idc_box.js.php';


$widget = (new CWidget())->setTitle(_('IDC Box Maintence'));

$IdcBoxForm = (new CForm())
	->setName($this->data['form'])
        ->setId($this->data['form'])
	->addVar('action', $this->data['action']);

if($this->data['action'] === 'modify.posted'){
    $IdcBoxForm->addVar('id', $this->data['idc_box']['id']);
}

$IdcBoxFormList = (new CFormList())
	->addRow(_('Box No'),
		(new CTextBox('box_no', isset($this->data['idc_box']['box_no']) ? $this->data['idc_box']['box_no']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
			->setAttribute('autofocus', 'autofocus')
	);
$IdcBoxFormList->addRow(_('Box Sec No'),
                (new CTextBox('box_secno',isset($this->data['idc_box']['box_secno']) ? $this->data['idc_box']['box_secno']:""))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );

foreach($this->data['allIdcs'] as $id => $idcroom){
    $items[$id] = $idcroom['room_name'];
}
$IdcBoxFormList->addRow(_('IDC Room'),
                (new CComboBox('room_id',isset($this->data['idc_box']['room_id'])?$this->data['idc_box']['room_id']:"","",$items)));

$IdcBoxFormList->addRow(_('Active since'), createDateMenu('box_starttime', isset($this->data['box_starttime'])?$this->data['box_starttime']:time(),null,FALSE));
$IdcBoxFormList->addRow(_('Active till'), createDateMenu('box_endtime', isset($this->data['box_endtime'])?$this->data['box_endtime']:time(), null, false));
$IdcBoxFormList->addRow(_('Bandwidth'),
                (new CTextBox('box_outbandwidth',isset($this->data['idc_box']['box_outbandwidth']) ? $this->data['idc_box']['box_outbandwidth']:""))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$IdcBoxFormList->addRow(_('IP List'),
                (new CTextBox('box_iplist',isset($this->data['idc_box']['box_iplist']) ? $this->data['idc_box']['box_iplist']:""))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$IdcBoxFormList->addRow(_('Height'),
                (new CTextBox('box_height',isset($this->data['idc_box']['box_height']) ? $this->data['idc_box']['box_height']:""))
                        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$IdcBoxFormList->addRow(_('Description'),
	(new CTextArea('box_desc',isset($this->data['idc_box']['box_desc']) ? $this->data['idc_box']['box_desc']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);
$this->data['selectedContactIds'] = isset($this->data['idc_box']['selectContact'])?array_keys($this->data['idc_box']['selectContact']):"";
$contactTB = new CTweenBox($IdcBoxForm, 'contacts', NULL, 10);

foreach ($this->data['allContacts'] as $id => $row){
    $contactTB->addItem($row['id'], $row['contact_name'], is_array($this->data['selectedContactIds'])?(in_array($row['id'], $this->data['selectedContactIds'])?'yes':'no'):'no' ,TRUE);
}
$IdcBoxFormList->addRow(_('Contacts'), $contactTB->get(_('In Contacts'), _('Other Contacts')));
$this->data['selectedFileIds'] = isset($this->data['idc_box']['SelectFile'])?array_keys($this->data['idc_box']['SelectFile']):"";
$fileTB = new CTweenBox($IdcBoxForm, 'files', NULL, 10);
foreach ($this->data['allFiles'] as $id => $row){
    $fileTB->addItem($row['id'], $row['file_title'], is_array($this->data['selectedFileIds'])?(in_array($row['id'], $this->data['selectedFileIds'])?'yes':'no'):'no' ,TRUE);
}
$IdcBoxFormList->addRow(_('Files'), $fileTB->get(_('In Files'), _('Other Files')));

$idcboxTabs = (new CTabView())->addTab('idcboxTab', _('IDC Box maintence'), $IdcBoxFormList);

if ($this->data['action'] == 'modify.posted')
{
    $submitbutton = new CButton('update', _('Update'));
    $submitbutton->onClick("Javascript:idcbox_submit();");
    $idcboxTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
    
}
else {
        $submitbutton = new CButton('add', _('Add'));
        $submitbutton->onClick("Javascript:idcbox_submit();");
	$idcboxTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));

}

$IdcBoxForm->addItem($idcboxTabs);
$widget->addItem($IdcBoxForm);

return $widget;


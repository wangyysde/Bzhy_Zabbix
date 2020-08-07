<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

require_once dirname(__FILE__).'/../../bzhyjs/bzhyResource.contact.js.php';


$widget = (new CWidget())->setTitle(_('Contact Maintence'));

$contactForm = (new CForm())
	->setName($this->data['form'])
        ->setId($this->data['form'])
	->addVar('action', $this->data['action']);

if($this->data['action'] === 'modify.posted'){
    $contactForm->addVar('contactid', $this->data['contact']['id']);
}

$contactFormList = (new CFormList())
	->addRow(_('Name'),
		(new CTextBox('contact_name', isset($this->data['contact']['contact_name']) ? $this->data['contact']['contact_name']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
			->setAttribute('autofocus', 'autofocus')
	);
//$contact_sex = new CRadioButtonList('contact_sex', isset($this->data['contact']['contact_sex'])?trim($this->data['contact']['contact_sex']):SEX_MAN);
if(isset($this->data['contact']['contact_sex'])){
    $sex=$this->data['contact']['contact_sex']==BZHY_SEX_MAN?BZHY_SEX_MAN:BZHY_SEX_WOMAN;
}
else{
    $sex = BZHY_SEX_MAN;
}

$contact_sex = new CRadioButtonList('contact_sex', $sex);
$contact_sex->setModern(true);
$contact_sex->addValue(_('MAN'), BZHY_SEX_MAN);
$contact_sex->addValue(_('WOMAN'), BZHY_SEX_WOMAN);
$contactFormList->addRow(_('Sex'),$contact_sex);
$contactFormList->addRow(_('Duty'),
                (new CTextBox('contact_position', isset($this->data['contact']['contact_position']) ? $this->data['contact']['contact_position']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('Company'),
                (new CTextBox('contact_company', isset($this->data['contact']['contact_company']) ? $this->data['contact']['contact_company']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('Website'),
                (new CTextBox('contact_url', isset($this->data['contact']['contact_url']) ? $this->data['contact']['contact_url']:"http://"))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('Address'),
                (new CTextBox('contact_addr', isset($this->data['contact']['contact_addr']) ? $this->data['contact']['contact_addr']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('Tel'),
                (new CTextBox('contact_tel', isset($this->data['contact']['contact_tel']) ? $this->data['contact']['contact_tel']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('Fax'),
                (new CTextBox('contact_fax', isset($this->data['contact']['contact_fax']) ? $this->data['contact']['contact_fax']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('MP'),
                (new CTextBox('contact_mp', isset($this->data['contact']['contact_mp']) ? $this->data['contact']['contact_mp']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('EMAIL'),
                (new CTextBox('contact_email', isset($this->data['contact']['contact_email']) ? $this->data['contact']['contact_email']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('QQ'),
                (new CTextBox('contact_qq', isset($this->data['contact']['contact_qq']) ? $this->data['contact']['contact_qq']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$contactFormList->addRow(_('WEIXIN'),
                (new CTextBox('contact_wx', isset($this->data['contact']['contact_wx']) ? $this->data['contact']['contact_wx']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );

$contactFormList->addRow(_('Description'),
	(new CTextArea('contact_desc',isset($this->data['contact']['contact_desc']) ? $this->data['contact']['contact_desc']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);
$contactTB = new CTweenBox($contactForm, 'files', NULL, 10);
foreach ($this->data['allfiles'] as $id => $row){
    $contactTB->addItem($row['id'], $row['file_title'], in_array($row['id'], $this->data['selectedFileIds'])?'yes':'no' ,TRUE);
}
$contactFormList->addRow(_('Files'), $contactTB->get(_('In files'), _('Other files')));

$contactTabs = (new CTabView())->addTab('contactTab', _('Contact Maintence'), $contactFormList);

if ($this->data['action'] == 'modify.posted')
{
	$submitbutton = new CButton('update', _('Update'));
        $submitbutton->onClick("Javascript:contact_submit();");
        $contactTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
}
else {
        $submitbutton = new CButton('add', _('Add'));
        $submitbutton->onClick("Javascript:contact_submit();");
	$contactTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
}

$contactForm->addItem($contactTabs);


$widget->addItem($contactForm);

return $widget;


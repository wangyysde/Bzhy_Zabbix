<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

require_once dirname(__FILE__).'/../../bzhyjs/bzhyResource.file.js.php';


$widget = (new CWidget())->setTitle(_('File Maintence'));

$fileForm = (new CForm())
	->setName($this->data['form'])
        ->setEnctype("multipart/form-data")
        ->setId($this->data['form'])
	->addVar('action', $this->data['action']);

if($this->data['action'] === 'modify.posted'){
    $fileForm->addVar('fileid', $this->data['file']['id']);
    $fileForm->addVar('oldfile_name',$this->data['file']['file_name']);
}

$fileFormList = (new CFormList())
	->addRow(_('Title:'),
		(new CTextBox('title', isset($this->data['file']['file_title']) ? $this->data['file']['file_title']:""))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
			->setAttribute('autofocus', 'autofocus')
	);
$fileFormList->addRow(_('Select File:'),
        (new CFile('file_name', isset($this->data['file']['file_name']) ? $this->data['file']['file_name']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        );
$fileFormList->addRow(_('Description:'),
	(new CTextArea('file_desc',isset($this->data['file']['file_desc']) ? $this->data['file']['file_desc']:""))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);

$fileTabs = (new CTabView())->addTab('fileTab', _('File Maintence'), $fileFormList);

if ($this->data['action'] == 'modify.posted')
{
	$submitbutton = new CButton('update', _('Update'));
        $submitbutton->onClick("Javascript:submit_file();");
        $fileTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
}
else {
        $submitbutton = new CButton('add', _('Add'));
        $submitbutton->onClick("Javascript:submit_file();");
	$fileTabs->setFooter(makeFormFooter($submitbutton,[new CButtonCancel()]));
}

$fileForm->addItem($fileTabs);

//$idcroomForm->addItem($idcroomFormList);

$widget->addItem($fileForm);

return $widget;


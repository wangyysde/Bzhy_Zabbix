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
require_once dirname(__FILE__).'/../js/admin_tools.file.edit.js.php';
require_once dirname(__FILE__).'/../js/common.js.php';

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


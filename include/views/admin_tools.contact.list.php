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
global $System_Settings;

$widget = (new CWidget())
	->setTitle(_('Contact Maintence'))
	->setControls((new CForm('get'))
		->cleanItems()
                ->addItem(new CSubmit('form', _('Create Contact')))
                ->addVar('action', 'add', 'action')
	);


// table file list
$form = (new CForm())->setName('contact');

$table = (new CTableInfo())
	->setHeader([
            make_sorting_header(_('Name'), 'contact_name', $data['sortField'], $data['sortOrder']),
		_('Sex'),
                _('Duty'),
            make_sorting_header(_('Company'), 'contact_company', $data['sortField'], $data['sortOrder']),
                _('Tel'),
                _('Email'),
            make_sorting_header(_('Status'), 'contact_status', $data['sortField'], $data['sortOrder']),
                _('Related Objects'),
                _('Description'),
                _('Action')
               
	]);

foreach ($data['contacts'] as $contact) {
        $paras = "action=disable.posted&id=".$contact['id'];
        $linkurl = (new CLink(_('Disable'),'#'))->onClick('Javascript:confirmAndRefresh(\'contact_maintence.php\',\''._('Are sure disable this contact?').'\',\''.$paras.'\')');
        $parasen = "action=enable.posted&id=".$contact['id'];
        $linkenurl = (new CLink(_('Enable'),'#'))->onClick('Javascript:confirmAndRefresh(\'contact_maintence.php\',\''._('Are sure enable this contact?').'\',\''.$parasen.'\')');
        $disableContact = ($contact['contact_status'] == STATUS_NORMAL) ? $linkurl:$linkenurl;
        $parasDel = "action=del.posted&id=".$contact['id'];
        $linkDelurl = (new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'contact_maintence.php\',\''._('Are sure delete this contact?').'\',\''.$parasDel.'\')');
        $delContact = ($contact['contact_status'] == STATUS_DISABLED) ?$linkDelurl :_('Delete') ;
        $modifyContact = ($contact['contact_status'] == STATUS_NORMAL) ? (new CLink(_('Update'),
		'contact_maintence.php?action=modify&id='.$contact['id'])):_('Updated');
        $url= 'contact_maintence.php?action=details.posted&id='.$contact['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
       
        $relatedObjects = "";
        if(isset($contact['relatedObjects']) && @is_array($contact['relatedObjects'])){
            foreach ($contact['relatedObjects'] as $object_table => $object){
                if(isset($object['object_info']) && is_array($object['object_info'])){
                    $relatedObjects .= "[".$object['object_type'].":";
                    foreach ($object['object_info'] as $object_pk_value => $value){
                        $relatedObjects .= $value['object_item_name'].SPACE;
                    }
                    $relatedObjects .= "]";
                }
            }
        }
        $table->addRow([
		(new CCol($contact['contact_name'])),
                (new CCol($contact['contact_sex']==SEX_MAN?_('Sir'):_('Lady'))),
                (new CCol($contact['contact_position'])),
                (new CCol($contact['contact_company'])),
                (new CCol($contact['contact_tel'])),
                (new CCol($contact['contact_email'])),                
                (new CCol($contact['contact_status'] == STATUS_NORMAL ? _('Normal'):_('Disabled'))),
                (new CCol($relatedObjects)),
                (new CCol(!$contact['contact_desc']?SPACE:$contact['contact_desc'])),
                (new CCol([$detailurl,SPACE,$modifyContact,SPACE,$disableContact,SPACE,$delContact]))
	]);
}
$form->addItem([
	$table,
]);


$widget->addItem($form);

return $widget;

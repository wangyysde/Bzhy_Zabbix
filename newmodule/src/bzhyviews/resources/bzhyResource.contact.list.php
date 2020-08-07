<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

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
        $linkurl = (new CLink(_('Disable'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhycontacts.php\',\''._('Are sure disable this contact?').'\',\''.$paras.'\')');
        $parasen = "action=enable.posted&id=".$contact['id'];
        $linkenurl = (new CLink(_('Enable'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhycontacts.php\',\''._('Are sure enable this contact?').'\',\''.$parasen.'\')');
        $disableContact = ($contact['contact_status'] == BZHY_STATUS_RUNING) ? $linkurl:$linkenurl;
        $parasDel = "action=del.posted&id=".$contact['id'];
        $linkDelurl = (new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhycontacts.php\',\''._('Are sure delete this contact?').'\',\''.$parasDel.'\')');
        $delContact = ($contact['contact_status'] == BZHY_STATUS_DISABLED) ?$linkDelurl :_('Delete') ;
        $modifyContact = ($contact['contact_status'] == BZHY_STATUS_RUNING) ? (new CLink(_('Update'),
		'bzhycontacts.php?action=modify&id='.$contact['id'])):_('Updated');
        $url= 'bzhycontacts.php?action=details.posted&id='.$contact['id'];
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
                (new CCol($contact['contact_sex']==BZHY_SEX_MAN?_('Sir'):_('Lady'))),
                (new CCol($contact['contact_position'])),
                (new CCol($contact['contact_company'])),
                (new CCol($contact['contact_tel'])),
                (new CCol($contact['contact_email'])),                
                (new CCol($contact['contact_status'] == BZHY_STATUS_RUNING ? _('Normal'):_('Disabled'))),
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

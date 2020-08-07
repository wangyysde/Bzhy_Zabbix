<?php
/*
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


global $System_Settings;

$widget = (new CWidget())
	->setTitle(_('file_maintence'))
	->setControls((new CForm('get'))
		->cleanItems()
                ->addItem(new CSubmit('form', _('Upload File')))
                ->addVar('action', 'add', 'action')
	);


// table file list
$form = (new CForm())->setName('file');

$table = (new CTableInfo())
	->setHeader([
            make_sorting_header(_('File Title'), 'file_title', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('CreateUser'), 'file_userid', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('First Uploadtime'), 'file_uploadtime', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('Status'), 'file_status', $data['sortField'], $data['sortOrder']),
            _('Related Objects'),
            _('Description'),
            _('Action')
               
	]);

foreach ($data['files'] as $file) {
        $paras = "action=disable.posted&fileid=".$file['id'];
        $linkurl = (new CLink(_('Disable'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyfiles.php\',\''._('Are sure disable this file?').'\',\''.$paras.'\')');
        $parasen = "action=enable.posted&fileid=".$file['id'];
        $linkenurl = (new CLink(_('Enable'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyfiles.php\',\''._('Are sure enable this file?').'\',\''.$parasen.'\')');
        $disablefile = ($file['file_status'] == BZHY_STATUS_RUNING) ? $linkurl:$linkenurl;
        $parasDel = "action=del.posted&fileid=".$file['id'];
        $linkDelurl = (new CLink(_('Delete'),'#'))->onClick('Javascript:confirmAndRefresh(\'bzhyfiles.php\',\''._('Are sure delete this file?').'\',\''.$parasDel.'\')');
        $delfile = ($file['file_status'] == BZHY_STATUS_DISABLED) ?$linkDelurl :_('Delete') ;
        $modifyfile = ($file['file_status'] == BZHY_STATUS_RUNING) ? (new CLink(_('Update'),
		'bzhyfiles.php?action=modify&fileid='.$file['id'])):_('Updated');
        $linkurl = $System_Settings['upload_file_path']."/".urlencode($file['file_name']);
        $open = (new CLink(_('Download'),$linkurl))
                ->setAttribute('target', '_blank');
        $uploadtime = is_null($file['file_uploadtime']) ? "":date("Y"._('Year')."m"._('Month')."d"._('Day'),$file['file_uploadtime']);
        $url= 'bzhyfiles.php?action=details.posted&id='.$file['id'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        $createuser = isset($file['CreateUserInfo']['name']) ? $file['CreateUserInfo']['name']:SPACE;
        $relatedObjects = "";
        if(isset($file['relatedObjects']) && @is_array($file['relatedObjects'])){
            foreach ($file['relatedObjects'] as $object_table => $object){
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
		(new CCol($file['file_title'])),
                (new CCol($createuser)),
                (new CCol($uploadtime)),
                (new CCol($file['file_status'] == BZHY_STATUS_RUNING ? _('Normal'):_('Disabled'))),
                (new CCol($relatedObjects)),
                (new CCol(!$file['file_desc']?SPACE:$file['file_desc'])),
                (new CCol([$detailurl,SPACE,$open,SPACE,$modifyfile,SPACE,$disablefile,SPACE,$delfile]))
	]);
}
$form->addItem([
	$table,
]);


$widget->addItem($form);

return $widget;

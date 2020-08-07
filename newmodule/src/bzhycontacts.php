<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/bzhyclasses/common/bzhyCBase.php';
bzhyCBase::run(bzhyCBase::BZHY_RUN_MODE_DEFAULT);

global $ZBX_MESSAGES;
$page['title'] = _('Contacts');
$page['file'] = 'bzhycontacts.php';
$page['hist_arg'] = array();
$page['bzhyscripts'] = ['bzhycommon.js'];

$themes = array_keys(Z::getThemes());
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
$data = []; 
$ViewFile="";
if(hasRequest('action') && getRequest('action') == 'disable.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No contact has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
        $cContact = bzhyCBase::getInstanceByObject('contact', []);
        $data['contacts'] = $cContact->get($options);
        if(count($data['contacts'])<1){
            $response = _('The contact you specified has not found!');
        }
        else{
            if(count($data['contacts'][$id]['relatedObjects']) >0){
                $response = _('Have some objects related to the contact you disabling!');
            }
        }
    }
    if(is_null($response)){
        $dbdata['contact_deluserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['contact_deluserip'] = substr($ip, 0, 39);
        $dbdata['contact_deltime'] = time();
        $dbdata['contact_status'] = BZHY_STATUS_DISABLED;
        $cContact->data['dbdata'] =  $dbdata;
        $cContact->data['id'] = getRequest('id');
        $cContact->data['action'] = 'disable.posted';
        if($cContact->update()){
            $response = _('The contact has be disabled!');
        }
        else{
            $response = _('The contact can not be disabled!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'enable.posted'){
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No contact has specified!');
    }
    else{
        $dbdata['contact_lastuserid'] = CWebUser::$data['userid'];
        $cContact = bzhyCBase::getInstanceByObject('contact', []);
        $cContact->data['id'] = getRequest('id');
        $cContact->data['action'] = 'enable.posted';
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['contact_lastuserip'] = substr($ip, 0, 39);
        $dbdata['contact_lasttime'] = time();
        $dbdata['contact_status'] = BZHY_STATUS_RUNING;
        $dbdata['contact_deluserid'] = NULL;
        $dbdata['contact_deluserip'] = '';
        $dbdata['contact_deltime'] = '';
        $cContact->data['dbdata'] =  $dbdata;
        if($cContact->update()){
            $response = _('The contact has be enabled!');
        }
        else{
            $response = _('The contact can not be enabled!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'del.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No contact has specified!');
    }
    else{
        $cContact = bzhyCBase::getInstanceByObject('contact', []);
        $cContact->data['action'] = 'del.posted';
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('id');
        $result =  $cContact->get($options);
        if(is_null($result) || count($result)<1){
            $response = _('Could not get contact!');
        }
        else{
            $cContact->data['id'] = getRequest('id');
            if($cContact->update()){
                $response = _('The contact has be deleted!');
            }
            else{
                $response = _('The contact can not be deleted!');
            }
        }
    }
    echo  json_encode($response);
    exit;
}

if (hasRequest('action') && getRequest('action') == 'details.posted') {
    define('ZBX_PAGE_NO_MENU', 0);
}
require_once dirname(__FILE__).'/bzhyinclude/bzhypage_header.php';

if(hasRequest('action') && (getRequest('action') == 'add.posted' || getRequest('action') == 'modify.posted')){       //finished 
    check_input();
    if(count($ZBX_MESSAGES) > 0){
        show_error_message(_('Input Error'));
    }
    else{
        $dbdata['contact_name'] = trim(getRequest('contact_name'));
        $dbdata['contact_sex'] = trim(getRequest('contact_sex'));
        $dbdata['contact_position'] = trim(getRequest('contact_position'));
        $dbdata['contact_company'] = trim(getRequest('contact_company'));
        $dbdata['contact_url'] = trim(getRequest('contact_url'));
        $dbdata['contact_addr'] = trim(getRequest('contact_addr'));
        $dbdata['contact_tel'] = trim(getRequest('contact_tel'));
        $dbdata['contact_fax'] = trim(getRequest('contact_fax'));
        $dbdata['contact_mp'] = trim(getRequest('contact_mp'));
        $dbdata['contact_email'] = trim(getRequest('contact_email'));
        $dbdata['contact_qq'] = trim(getRequest('contact_qq'));
        $dbdata['contact_wx'] = trim(getRequest('contact_wx'));
        $dbdata['contact_desc'] = trim(getRequest('contact_desc'));
        $dbdata['contact_status'] = BZHY_STATUS_RUNING;
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $cContact = bzhyCBase::getInstanceByObject('contact', []); 
        if(getRequest('action') == 'add.posted'){
            $dbdata['contact_userid'] = $dbdata['contact_lastuserid'] = CWebUser::$data['userid'];
            $dbdata['contact_userip'] = $dbdata['contact_lastuserip'] = substr($ip, 0, 39);
            $dbdata['contact_createtime'] = $dbdata['contact_lasttime'] = time();
            $cContact->data['action'] = 'add.posted';
            $successMessage = _('Add contact successful!');
            $errorMessage = _('Add contact error!');
            $messageTitle = _('Add Contact');
        }
        else{
             $cContact->data['id'] = getRequest('contactid');
             $dbdata['contact_lastuserid'] = CWebUser::$data['userid'];
             $dbdata['contact_lastuserip'] = substr($ip, 0, 39);
             $dbdata['contact_lasttime'] = time();
             $cContact->data['action'] = 'modify.posted';
             $successMessage = _('Modify contact successful!');
             $errorMessage = _('Modify contact error!');
             $messageTitle = _('Modify Contact');
        }
        $cContact->data['dbdata'] =  $dbdata;
        $cContact->data['files'] = getRequest('files');
        if($cContact->update($data)){
            $ZBX_MESSAGES[] = ['type'=>'info','message'=>$successMessage];
            show_message($messageTitle);
        }
        else{
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>$errorMessage];
            show_error_message($messageTitle);
        }
    }
}
elseif(hasRequest('action') && (getRequest('action') == 'add' || getRequest('action') == 'modify')){
    $options['output'] = ['id','file_title'];
    $options['status'] = BZHY_STATUS_RUNING;
    $cfile = bzhyCBase::getInstanceByObject('file', []);
    $data['allfiles'] = $cfile->get($options);
    $data['selectedFileIds'] = [];
    unset($options);
    if(getRequest('action') == 'modify'){
        $data['action'] = 'modify.posted';
        if(!hasRequest('id')){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Parameters error!')];
        }
        else{
            $data['form'] = 'contact';
            $options['output'] = API_OUTPUT_EXTEND;
            $options['ids'] = getRequest('id');
            $cContact = bzhyCBase::getInstanceByObject('contact', []);
            $result =  $cContact->get($options);
            if(is_null($result) || count($result)<1){
                $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get contact infomation!')];
            }
            $data['contact'] = $result[getRequest('id')]; 
            unset($options);
            $options['output'] = ['id','file_title'];
            $options['status'] = BZHY_STATUS_RUNING;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'contact';
            $cfile = bzhyCBase::getInstanceByObject('file', []);
            $data['selectedfiles'] = $cfile->get($options);
            $data['selectedFileIds'] = array_keys($data['selectedfiles']);
            
        }
    }
    else{
        $data['action'] = 'add.posted';
        $data['form'] = 'contact';
    }
    if(count($ZBX_MESSAGES)>0){
        show_error_message(_('Ocurred a error'));
    }
    else{
       $ViewFile = 'bzhyResource.contact.edit';
    }
}
elseif (hasRequest('action') && getRequest('action') == 'details.posted') {
    $labels = [];
    if(!hasRequest('id')){
        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Parameters error!')];
    }
    else{
        $withOutFields = ['id'];
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('id');
        $options['selectCreateUser'] = ['userid','alias','name'];
        $options['selectLastUser'] = ['userid','alias','name'];
        $options['selectDelUser'] = ['userid','alias','name'];
        $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
        $cContact = bzhyCBase::getInstanceByObject('contact', []);
        $result =  $cContact->get($options);
        if(is_null($result) || count($result)<1){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get contact infomation!')];
        }
        else{
            $data['labels'] = bzhyCBase::getFieldsLabelByObject('contact',[],$withOutFields);
            $data['DetailsData'] = $result[getRequest('id')];
            unset($options);
            $options['output'] = ['id','file_title'];
            $options['status'] = BZHY_STATUS_RUNING;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'contact';
            $cfile = bzhyCBase::getInstanceByObject('file', []);
            $data['selectedfiles'] = $cfile->get($options);
            $data['objectName'] ='contact';
        }
    }
    if(count($ZBX_MESSAGES)>0){
        show_error_message(_('Ocurred a error'));
    }
    else{
        $ViewFile = 'bzhyResource.popup';
    }
}
elseif(!hasRequest('action'))
{
    if(hasRequest('sort')){
        $data['sortField'] = getRequest('sort');
    }
    else{
        $data['sortField'] = 'contact_name';
    }
    if(hasRequest('sortorder')){
        $data['sortOrder'] = getRequest('sortorder');
    }
    else{
        $data['sortOrder'] = ZBX_SORT_DOWN;
    }
    if(hasRequest('paging'))
        $data['paging'] = getRequest ('paging');
    else
        $data['paging'] = 1;
    $options['output'] = API_OUTPUT_EXTEND;
    $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
    $options['sortfield'] = $data['sortField'];
    $options['sortorder'] = $data['sortOrder'];
    $cContact = bzhyCBase::getInstanceByObject('contact', []); 
    $data['contacts'] = $cContact->get($options);
    $ViewFile = 'bzhyResource.contact.list';

}
if(!zbx_empty($ViewFile)){
    $ContactView = new CView($ViewFile, $data);
    $ContactView->render();
    $ContactView->show();
}
function check_input(){
    global $ZBX_MESSAGES;
    if(!hasRequest('contact_name') || strlen(getRequest('contact_name'))<3 || strlen(getRequest('contact_name')) >20  ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Name you input is invalid!')];
    }
    if(!hasRequest('contact_position') || strlen(getRequest('contact_position'))<3 || strlen(getRequest('contact_position')) >50  ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Duty you input is invalid!')];
    }
    if(!hasRequest('contact_company') || strlen(getRequest('contact_company'))<3 || strlen(getRequest('contact_company')) >50  ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Company you input is invalid!')];
    }
    if(!is_tel(getRequest('contact_tel')) && !is_mp(getRequest('contact_mp')) && !is_email(getRequest('contact_email'))){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('You should input Tel or MP or EMAIL!')];
    }
}
require_once dirname(__FILE__).'/include/page_footer.php';


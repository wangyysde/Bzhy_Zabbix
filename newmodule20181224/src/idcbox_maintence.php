<?php
require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/classes/CBase.php';
$objectFiles=['classes/CContact.php','classes/CAttachment.php','classes/CIdc.php','classes/CIdcbox.php'];
$objects = ['idc_room' => 'CIdc',
             'file'  => 'CAttachment',
             'contact' => 'CContact',
             'idc_box' => 'CIdcbox'];

CBase::addObjects($objects);
$CBase = new CBase();
$CBase->addFiles($objectFiles);
$CBase->loadFiles();

$page['title'] = _('IDC Box Maintence');
$page['file'] = 'idcbox_maintence.php';
$page['hist_arg'] = array();
$page['scripts'] = ['class.calendar.js'];

$themes = array_keys(Z::getThemes());
$INSTALL_SCRIPT="install_scripts.bat";
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
$data = []; 
$ViewFile="";
if(hasRequest('action') && getRequest('action') == 'close.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No IDC Room has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $cIdcBox = new CIdcbox();
        $data['ids'] = $cIdcBox->get($options);
        if(count($data['ids'])<1){
            $response = _('The IDC Box you specified has not found!');
        }
    }
    if(is_null($response)){
        $dbdata['box_deluserid'] =  $dbdata['box_lastuserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['box_deluserip'] = $dbdata['box_lastuserip'] = substr($ip, 0, 39);
        $dbdata['box_deltime'] = $dbdata['box_lasttime'] = $dbdata['box_closedtime'] = time();
        $dbdata['box_status'] = IDC_ROOM_CLOSED;
        $cIdcBox->data['dbdata'] =  $dbdata;
        $cIdcBox->data['id'] = getRequest('id');
        $cIdcBox->data['action'] = 'close.posted';
        if($cIdcBox->update()){
            $response = _('The IDC Box has be closed!');
        }
        else{
            $response = _('The IDC Box can not be closed!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'open.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No IDC Box has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $cIdcBox = new CIdcbox();
        $data['ids'] = $cIdcBox->get($options);
        if(count($data['ids'])<1){
            $response = _('The IDC Box you specified has not found!');
        }
    }
     if(is_null($response)){
        $dbdata['box_deluserid'] = NULL;
        $dbdata['box_deluserip'] = '';
        $dbdata['box_deltime'] =  NULL;
        $dbdata['box_lastuserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['box_lastuserip'] = substr($ip, 0, 39);
        $dbdata['box_lasttime'] = time();
        $dbdata['box_closedtime'] = NULL;
        $dbdata['box_status'] = IDC_ROOM_NORMAL;
        $cIdcBox->data['dbdata'] =  $dbdata;
        $cIdcBox->data['id'] = getRequest('id');
        $cIdcBox->data['action'] = 'open.posted';
        if($cIdcBox->update()){
            $response = _('The IDC Box has be reopened!');
        }
        else{
            $response = _('The IDC Box can not be reopened!');
        }
     }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'del.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No IDC Box has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $cIdcBox = new CIdcbox();
        $data['ids'] = $cIdcBox->get($options);
        if(count($data['ids'])<1){
            $response = _('The IDC Box you specified has not found!');
        }
    }
    if(is_null($response)){
        $cIdcBox->data['id'] = getRequest('id');
        $cIdcBox->data['action'] = 'del.posted';
        if($cIdcBox->update()){
            $response = _('The IDC Box has be deleted!');
        }
        else{
            $response = _('The IDC Box can not be deleted!');
        }
    }
    echo  json_encode($response);
    exit;
}
if (hasRequest('action') && getRequest('action') == 'details.posted') {
    define('ZBX_PAGE_NO_MENU', 0);
}
require_once dirname(__FILE__).'/include/page_header.php';

if(hasRequest('action') && (getRequest('action') == 'add.posted' || getRequest('action') == 'modify.posted') ){
    check_input();
    if(count($ZBX_MESSAGES) > 0){
        show_error_message(_('Input Error'));
    }
    else{
        $dbdata['box_no'] = trim(getRequest('box_no'));
        $dbdata['box_secno'] = trim(getRequest('box_secno'));
        $dbdata['room_id'] = trim(getRequest('room_id'));
        $idcbox_starttime = zbxDateToTime(getRequest('box_starttime_year').getRequest('box_starttime_month').getRequest('box_starttime_day').getRequest('box_starttime_hour').getRequest('box_starttime_minute'));
        $idcbox_endtime = zbxDateToTime(getRequest('box_endtime_year').getRequest('box_endtime_month').getRequest('box_endtime_day').getRequest('box_endtime_hour').getRequest('box_endtime_minute'));
        $dbdata['box_starttime'] = $idcbox_starttime;
        $dbdata['box_endtime'] = $idcbox_endtime;
        $dbdata['box_closedtime'] = "";
        $dbdata['box_desc'] = trim(getRequest('box_desc'));
        $dbdata['room_status'] = IDC_ROOM_NORMAL;
        $dbdata['box_outbandwidth'] = trim(getRequest('box_outbandwidth'));
        $dbdata['box_iplist'] = trim(getRequest('box_iplist'));
        $dbdata['box_height'] = trim(getRequest('box_height'));
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $cIdcBox = new CIdcbox();
        if(getRequest('action') == 'add.posted'){
            $dbdata['box_userid'] = $dbdata['box_lastuserid'] = CWebUser::$data['userid'];
            $dbdata['box_userip'] = $dbdata['box_lastuserip'] = substr($ip, 0, 39);
            $dbdata['box_createtime'] = $dbdata['box_lasttime'] = time();
            $cIdcBox->data['action'] = 'add.posted';
            $successMessage = _('Add IDC Box information successful!');
            $errorMessage = _('Add IDC Box information error!');
            $messageTitle = _('Add IDC Box infromation');
            
        }
        else{
             $cIdcBox->data['id'] = getRequest('id');
             $dbdata['box_lastuserid'] = CWebUser::$data['userid'];
             $dbdata['box_lastuserip'] = substr($ip, 0, 39);
             $dbdata['box_lasttime'] = time();
             $cIdcBox->data['action'] = 'modify.posted';
             $successMessage = _('Modify IDC Box infromation successful!');
             $errorMessage = _('Modify IDC Box infromation error!');
             $messageTitle = _('Modify IDC Box infromation');
        }
        $cIdcBox->data['dbdata'] =  $dbdata;
        $cIdcBox->data['files'] = getRequest('files');
        $cIdcBox->data['contacts'] = getRequest('contacts');
        if($cIdcBox->update($data)){
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
    $options['status'] = STATUS_NORMAL;
    $cFile = new CAttachment();
    $data['allFiles'] = $cFile->get($options);
    $data['selectedFileIds'] = [];
    unset($options);
    $options['output'] = ['id','contact_name'];
    $options['status'] = STATUS_NORMAL;
    $cContact = new CContact();
    $data['allContacts'] = $cContact->get($options);
    $data['selectedContactIds'] = [];
    unset($options);
    $options['output'] = ['room_name'];
    $options['status'] = STATUS_NORMAL;
    $cIdc = new CIdc();
    $data['allIdcs'] = $cIdc->get($options);
    $data['selectedIdcIds'] = [];
    $data['form'] = 'IdcBoxForm';
    if(getRequest('action') == 'modify'){
        $data['action'] = 'modify.posted';
        if(!hasRequest('id')){
            show_error_message(_('Parameters error!'));
        }
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('id');
        $options['selectContact'] = ['id','contact_name'];
        $options['selectFile'] = ['id','file_title'];
        $options['selectIdcRoom'] = ['id','room_name'];
        $cIdcBox = new CIdcbox();
        $data['idc_box'] = $cIdcBox->get($options);
        if(is_null($data['idc_box'])){
            show_error_message(_('Parameters error!'));
        }
        $data['idc_box'] =  $data['idc_box'][getRequest('id')];
        $data['box_starttime'] = $data['idc_box']['box_starttime'];
        $data['box_endtime'] = $data['idc_box']['box_endtime'];
    }
    else{
        $data['action'] = 'add.posted';
        $data['box_starttime'] = time();
        $data['box_endtime'] = time() + 31536000;
    }
    $IdcBoxView = new CNewView('admin_tools.idcbox.edit', $data);
    $IdcBoxView->render();
    $IdcBoxView->show();
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
        $options['selectIdcRoom'] = ['id','room_name'];
        $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
        $cIdcBox = new CIdcbox();
        $result =  $cIdcBox->get($options);
        if(is_null($result) || count($result)<1){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get idc box infomation!')];
        }
        else{
            $data['labels'] = $cIdcBox->getLabels([],$withOutFields);
            $data['DetailsData'] = $result[getRequest('id')];
            unset($options);
            $options['output'] = ['id','file_title'];
            $options['status'] = STATUS_NORMAL;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'idc_box';
            $cfile = new CAttachment();
            $data['selectedfiles'] = $cfile->get($options);
            $data['objectName'] ='idc_box';
            unset($options);
            $options['output'] = ['id','contact_name'];
            $options['status'] = STATUS_NORMAL;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'idc_box';
            $cContact = new CContact();
            $data['selectedContact'] = $cContact->get($options);
        }
    }
    if(count($ZBX_MESSAGES)>0){
        show_error_message(_('Ocurred a error'));
    }
    else{
        $ViewFile = 'admin_tools.popup';
    }
}
elseif(!hasRequest('action'))
{
    if(hasRequest('sort')){
        $data['sortField'] = getRequest('sort');
    }
    else{
        $data['sortField'] = 'box_no';
    }
    if(hasRequest('sortorder')){
        $data['sortOrder'] = getRequest('sortorder');; 
    }
    else{
        $data['sortOrder'] = ZBX_SORT_DOWN;
    }
    if(hasRequest('paging'))
        $data['paging'] = getRequest ('paging');
    else
        $data['paging'] = 1;
    $options['output'] = API_OUTPUT_EXTEND;
    $options['selectCreateUser'] = ['userid','alias','name'];
    $options['selectLastUser'] = ['userid','alias','name'];
    $options['selectDelUser'] = ['userid','alias','name'];
    $options['selectIdcRoom'] = ['id','room_name','room_shortname'];
    $options['sortfield'] = $data['sortField'];
    $options['sortorder'] = $data['sortOrder'];
    $options['selectFile'] = ['id','file_title'];
    $options['selectContact'] = ['id','contact_name'];
    $cIdcBox = new CIdcbox();
    $data['idc_box'] = $cIdcBox->get($options);
    $ViewFile = 'admin_tools.idcbox.list';
}
if(!zbx_empty($ViewFile)){
    $ContactView = new CNewView($ViewFile, $data);
    $ContactView->render();
    $ContactView->show();
}

function check_input(){
    global $System_Settings,$ZBX_MESSAGES;
    if(!hasRequest('box_no') || strlen(getRequest('box_no'))<2 || strlen(getRequest('box_no'))>255 ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Box No you input is invalid!')];
    }
    $idcbox_starttime = zbxDateToTime(getRequest('box_starttime_year').getRequest('box_starttime_month').getRequest('box_starttime_day').getRequest('box_starttime_hour').getRequest('box_starttime_minute'));
    $idcbox_endtime = zbxDateToTime(getRequest('box_endtime_year').getRequest('box_endtime_month').getRequest('room_endtime_day').getRequest('box_endtime_hour').getRequest('box_endtime_minute'));
    if($idcbox_starttime >= $idcbox_endtime ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Active Since Or Active Till you input is invalid!')];
    }
}
require_once dirname(__FILE__).'/include/page_footer.php';


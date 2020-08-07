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

$page['title'] = _('idc_information_maintence');
$page['file'] = 'idc_information_maintence.php';
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
        $options['selectIdcBox'] = ['countOutput' => null,'groupCount'=>null ];
        $cIdc = new CIdc();
        $data['idcs'] = $cIdc->get($options);
        if(count($data['idcs'])<1){
            $response = _('The IDC Room you specified has not found!');
        }
        else{
            if(count($data['idcs'][$id]['selectIdcBox']) >0){
                $response = _('Have some IDC Box  related to the IDC Room you closing!');
            }
        }
    }
    if(is_null($response)){
        $dbdata['room_deluserid'] =  $dbdata['room_lastuserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['room_deluserip'] = $dbdata['room_lastuserip'] = substr($ip, 0, 39);
        $dbdata['room_deltime'] = $dbdata['room_lasttime'] = $dbdata['room_closedtime'] = time();
        $dbdata['room_status'] = IDC_ROOM_CLOSED;
        $cIdc->data['dbdata'] =  $dbdata;
        $cIdc->data['id'] = getRequest('id');
        $cIdc->data['action'] = 'close.posted';
        if($cIdc->update()){
            $response = _('The IDC Room has be closed!');
        }
        else{
            $response = _('The IDC Room can not be closed!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'open.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No IDC Room has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $cIdc = new CIdc();
        $data['idcs'] = $cIdc->get($options);
        if(count($data['idcs'])<1){
            $response = _('The IDC Room you specified has not found!');
        }
    }
     if(is_null($response)){
        $dbdata['room_deluserid'] = NULL;
        $dbdata['room_deluserip'] = '';
        $dbdata['room_deltime'] =  NULL;
        $dbdata['room_lastuserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['room_lastuserip'] = substr($ip, 0, 39);
        $dbdata['room_lasttime'] = time();
        $dbdata['room_closedtime'] = NULL;
        $dbdata['room_status'] = IDC_ROOM_NORMAL;
        $cIdc->data['dbdata'] =  $dbdata;
        $cIdc->data['id'] = getRequest('id');
        $cIdc->data['action'] = 'open.posted';
        if($cIdc->update()){
            $response = _('The IDC Room has be reopened!');
        }
        else{
            $response = _('The IDC Room can not be reopened!');
        }
     }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'del.posted'){
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No IDC Room has specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = API_OUTPUT_EXTEND;
        $cIdc = new CIdc();
        $data['idcs'] = $cIdc->get($options);
        if(count($data['idcs'])<1){
            $response = _('The IDC Room you specified has not found!');
        }
    }
    if(is_null($response)){
        $cIdc->data['id'] = getRequest('id');
        $cIdc->data['action'] = 'del.posted';
        if($cIdc->update()){
            $response = _('The IDC Room has be deleted!');
        }
        else{
            $response = _('The IDC Room can not be deleted!');
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
        $dbdata['room_name'] = trim(getRequest('name'));
        $dbdata['room_shortname'] = trim(getRequest('shortname'));
        $room_starttime = zbxDateToTime(getRequest('room_starttime_year').getRequest('room_starttime_month').getRequest('room_starttime_day').getRequest('room_starttime_hour').getRequest('room_starttime_minute'));
        $room_endtime = zbxDateToTime(getRequest('room_endtime_year').getRequest('room_endtime_month').getRequest('room_endtime_day').getRequest('room_endtime_hour').getRequest('room_endtime_minute'));
        $dbdata['room_starttime'] = $room_starttime;
        $dbdata['room_endtime'] = $room_endtime;
        $dbdata['room_comment'] = trim(getRequest('room_comment'));
        $dbdata['room_addr'] = trim(getRequest('room_addr'));
        $dbdata['room_status'] = IDC_ROOM_NORMAL;
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $cIdc = new CIdc();
        if(getRequest('action') == 'add.posted'){
            $dbdata['room_userid'] = $dbdata['room_lastuserid'] = CWebUser::$data['userid'];
            $dbdata['room_userip'] = $dbdata['room_lastuserip'] = substr($ip, 0, 39);
            $dbdata['room_createtime'] = $dbdata['room_lasttime'] = time();
            $cIdc->data['action'] = 'add.posted';
            $successMessage = _('Add IDC information successful!');
            $errorMessage = _('Add IDC information error!');
            $messageTitle = _('Add IDC infromation');
            
        }
        else{
             $cIdc->data['id'] = getRequest('id');
             $dbdata['room_lastuserid'] = CWebUser::$data['userid'];
             $dbdata['room_lastuserip'] = substr($ip, 0, 39);
             $dbdata['room_lasttime'] = time();
             $cIdc->data['action'] = 'modify.posted';
             $successMessage = _('Modify IDC infromation successful!');
             $errorMessage = _('Modify IDC infromation error!');
             $messageTitle = _('Modify IDC infromation');
        }
        $cIdc->data['dbdata'] =  $dbdata;
        $cIdc->data['files'] = getRequest('files');
        $cIdc->data['contacts'] = getRequest('contacts');
        if($cIdc->update($data)){
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
    $data['form'] = 'idcroomForm';
    if(getRequest('action') == 'modify'){
        $data['action'] = 'modify.posted';
        if(!hasRequest('id')){
            show_error_message(_('Parameters error!'));
        }
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('id');
        $options['selectContact'] = ['id','contact_name'];
        $options['selectFile'] = ['id','file_title'];
        $cidc = new CIdc();
        $data['idc_room'] = $cidc->get($options);
        if(is_null($data['idc_room'])){
            show_error_message(_('Parameters error!'));
        }
        $data['idc_room'] =  $data['idc_room'][getRequest('id')];
        $data['room_starttime'] = $data['idc_room']['room_starttime'];
        $data['room_endtime'] = $data['idc_room']['room_endtime'];
    }
    else{
        $data['action'] = 'add.posted';
        $data['room_starttime'] = time();
        $data['room_endtime'] = time() + 31536000;
    }
    $idcroomView = new CNewView('admin_tools.idc.edit', $data);
    $idcroomView->render();
    $idcroomView->show();
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
        $cIdc = new CIdc();
        $result =  $cIdc->get($options);
        if(is_null($result) || count($result)<1){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get idc room infomation!')];
        }
        else{
            $data['labels'] = $cIdc->getLabels([],$withOutFields);
            $data['DetailsData'] = $result[getRequest('id')];
            unset($options);
            $options['output'] = ['id','file_title'];
            $options['status'] = STATUS_NORMAL;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'idc_room';
            $cfile = new CAttachment();
            $data['selectedfiles'] = $cfile->get($options);
            $data['objectName'] ='idc_room';
            unset($options);
            $options['output'] = ['id','contact_name'];
            $options['status'] = STATUS_NORMAL;
            $options['object_ids'] = getRequest('id');
            $options['object_table'] = 'idc_room';
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
        $data['sortField'] = 'room_name';
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
    $options['sortfield'] = $data['sortField'];
    $options['sortorder'] = $data['sortOrder'];
    $options['selectFile'] = ['id','file_title'];
    $options['selectContact'] = ['id','contact_name'];
    $options['selectIdcBox'] = ['id','box_no'];
    $cidc = new CIdc();
    $data['idc_room'] = $cidc->get($options);
    $ViewFile = 'admin_tools.idc.list';
}
if(!zbx_empty($ViewFile)){
    $ContactView = new CNewView($ViewFile, $data);
    $ContactView->render();
    $ContactView->show();
}

function check_input(){
    global $System_Settings,$ZBX_MESSAGES;
    if(!hasRequest('name') || strlen(getRequest('name'))<3 || strlen(getRequest('name'))>255 ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Name you input is invalid!')];
    }
    if(hasRequest('shortname') &&(strlen(getRequest('shortname'))<0 || strlen(getRequest('shortname'))>50) ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('ShortName you input is invalid!')];
    }
    $room_starttime = zbxDateToTime(getRequest('room_starttime_year').getRequest('room_starttime_month').getRequest('room_starttime_day').getRequest('room_starttime_hour').getRequest('room_starttime_minute'));
    $room_endtime = zbxDateToTime(getRequest('room_endtime_year').getRequest('room_endtime_month').getRequest('room_endtime_day').getRequest('room_endtime_hour').getRequest('room_endtime_minute'));
    if($room_starttime >= $room_endtime ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Active Since Or Active Till you input is invalid!')];
    }
    if(!hasRequest('room_addr') || strlen(getRequest('room_addr'))<3 || strlen(getRequest('room_addr'))>255 ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Address you input is invalid!')];
    }
}
require_once dirname(__FILE__).'/include/page_footer.php';


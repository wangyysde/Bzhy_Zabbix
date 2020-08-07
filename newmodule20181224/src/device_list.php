<?php
require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/classes/CBase.php';
$objectFiles=['classes/CContact.php','classes/CAttachment.php','classes/CIdc.php','classes/CIdcbox.php',
    'classes/CDevice.php','classes/bzhyDB.php'];
$objects = ['idc_room' => 'CIdc',
             'file'  => 'CAttachment',
             'contact' => 'CContact',
             'idc_box' => 'CIdcbox',
             'deviceinfo' => 'CDevice'];

CBase::addObjects($objects);
$CBase = new CBase();
$CBase->addFiles($objectFiles);
$CBase->loadFiles();

$page['title'] = _('Device List');
$page['file'] = 'device_list.php';
$page['scripts'] = ['class.calendar.js'];
$page['bzhyscripts'] = ["bzhyjs/bzhycommon.js","/bzhyjs/jquery-3.3.1.min.js","/bzhyjs/multiselect.js"];
$page['hist_arg'] = array();

$themes = array_keys(Z::getThemes());
$INSTALL_SCRIPT="install_scripts.bat";
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
$data = []; 
$ViewFile="";
if(hasRequest('action') && (getRequest('action') == 'mantaince.posted' || getRequest('action') == 'offline.posted'
                 || getRequest('action') == 'online.posted')){
    $action  = getRequest('action');
    $response = null ; 
    if(!hasRequest('id') || trim(getRequest('id')) == ""){
        $response = _('No Device has been specified!');
    }
    else{
        $id = trim(getRequest('id'));
        $options['ids'] = $id;
        $options['output'] = ['deviceid','hostname'];
        $options['selectBelong'] = ['deviceid','hostname' ];
        $CDeviceInfo = new CDevice();
        $data['deviceinfo'] = $CDeviceInfo->get($options);
        if(count($data['deviceinfo'])<1){
            $response = _('The device you specified has not found!');
        }
        else{
            if($action === "mantaince.posted"){
                if(count($data['deviceinfo'][$id]['selectBelong']) >0){
                    $response = _('Have some device belong to the device you mantaincing!');
                }
            }
        }
    }
    if(is_null($response)){
        $CDeviceInfo->data['id'] = getRequest('id');
        $CDeviceInfo->data['action'] = $action;
        if($action === "mantaince.posted"){
            $dbdata['status'] = DEVICE_STATUS_MANTAINCE;
            $successmsg=  _('The device has be mantainced!');
            $errormsg = _('The device can not be mantainced!');
        }
        elseif($action === "offline.posted"){
            $dbdata['status'] = DEVICE_STATUS_OFFLINE;
            $successmsg=  _('The device has be offline!');
            $errormsg = _('The device can not be offline!');
        }
        elseif($action === "online.posted"){
            $dbdata['status'] = DEVICE_STATUS_NORMAL;
            $successmsg=  _('The device has be online!');
            $errormsg = _('The device can not be online!');
        }
        $CDeviceInfo->data['dbdata'] = $dbdata;
        if($CDeviceInfo->update()){
            $response = $successmsg;
        }
        else{
            $response = $errormsg;
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'formajax.getboxes'){
    $response = null ; 
    if(!hasRequest('ParenKey') || trim(getRequest('ParenKey')) == ""){
        $response = "<option value='0'>"._("Nothing")."</option>\n";
    }
    else{
        $ParenKey = trim(getRequest('ParenKey'));
        $CIdcbox = new CIdcbox();
        $data = $CIdcbox->get(['box_status' =>STATUS_NORMAL,'idcroom_ids'=>$ParenKey,'output' => ['id','box_no']]);
        foreach($data as $id=>$line){
            $response .="<option value='".$line["id"]."'>".$line["box_no"]."</option>\n";
        }
    }
    if($response == null){
        $response = "<option value='0'>"._("Nothing")."</option>\n";
    }
    echo $response;
    exit;
     
}
if (hasRequest('action') && getRequest('action') == 'details.posted') {
    define('ZBX_PAGE_NO_MENU', 0);
}
require_once dirname(__FILE__).'/bzhyinclude/bzhypage_header.php';

if(hasRequest('action') && (getRequest('action') == 'add.posted' || getRequest('action') == 'modify.posted') ){
    check_input();
    if(count($ZBX_MESSAGES) > 0){
        show_error_message(_('Input Error'));
    }
    else{
        $dbdata=[
            'typeid' =>getRequest('typeid'),
            'brandid' =>getRequest('brandid'),
            'size' =>getRequest('size'),
            'model' =>getRequest('model'),
            'serialno' =>getRequest('serialno'),
            'serviceno' =>getRequest('serviceno'),
            'hardinfo' =>getRequest('hardinfo'),
            'agent' =>getRequest('agent'),
            'hostname' =>getRequest('hostname'),
            'ips' =>getRequest('ips'),
            'dns' =>getRequest('dns'),
            'gw' =>getRequest('gw'),
            'roomid' =>getRequest('roomid'),
            'boxid' =>getRequest('boxid'),
            'position' =>getRequest('position'),
            'belongdeviceid' =>getRequest('belongdeviceid'),
            'osid' =>getRequest('osid'),
            'desc' =>getRequest('desc'),
            'contacts' =>getRequest('contacts'),
            'files' =>getRequest('files')
        ];
        
        $dbdata['belongdeviceid']= zbx_empty($dbdata['belongdeviceid'])?0:$dbdata['belongdeviceid'];
        
        
        $dbdata['createdate'] = zbxDateToTime(getRequest('createdate_year').getRequest('createdate_month').getRequest('createdate_day').getRequest('createdate_hour').getRequest('createdate_minute'));
        $dbdata['warrantystartdate'] = zbxDateToTime(getRequest('warrantystartdate_year').getRequest('warrantystartdate_month').getRequest('warrantystartdate_day').getRequest('warrantystartdate_hour').getRequest('warrantystartdate_minute'));
        $dbdata['warrantyenddate'] = zbxDateToTime(getRequest('warrantyenddate_year').getRequest('warrantyenddate_month').getRequest('warrantyenddate_day').getRequest('warrantyenddate_hour').getRequest('warrantyenddate_minute'));
    
        $dbdata['userid'] = CWebUser::$data['userid'];
        $dbdata['isruning'] = DEVICE_RUNING_STATUS_RUNING;
        $dbdata['status'] = DEVICE_STATUS_NORMAL;
        
        $CDeviceInfo = new CDevice();
        if(getRequest('action') == 'add.posted'){
            $CDeviceInfo->data['action'] = 'add.posted';
            $successMessage = _('Add Device Successful!');
            $errorMessage = _('Add Device error!');
            $messageTitle = _('Add Device infromation');
            
        }
        else{
             $CDeviceInfo->data['id'] = getRequest('id');
             $CDeviceInfo->data['action'] = 'modify.posted';
             $successMessage = _('Modify Device successful!');
             $errorMessage = _('Modify Device error!');
             $messageTitle = _('Modify Device infromation');
        }
        $CDeviceInfo->data['dbdata'] =  $dbdata;
        if($CDeviceInfo->update($data)){
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
    $CDeviceInfo = new CDevice();
    $data['allType'] = $CDeviceInfo->getDeviceType(['status' =>STATUS_NORMAL,'output' =>['typeid','typename']]);
    $data['allSize'] = $CDeviceInfo->deviceSize;
    
    $CIdc = new CIdc();
    $data['allRooms'] = $CIdc->get(['status' =>STATUS_NORMAL,'output' => ['id','room_name']]);
    
    $CIdcbox = new CIdcbox();
    foreach ($data['allRooms'] as $key => $line){
        $data['idcbox'][$key] = $CIdcbox->get(['box_status' =>STATUS_NORMAL,'idcroom_ids'=>$key,'output' => ['id','box_no']]);
    }
    
    $data['allOS'] = $CDeviceInfo->getOS(['output' =>['osid','osname']]);
    
    $data['allBrand'] = $CDeviceInfo->getBrand(['output' =>['id','local_name']]);

    $DeviceInfo = new CDevice();
    $data['allIndepend'][0] = ["deviceid"=>0,"hostname"=>_("Independ")];
    $dependDevices = $DeviceInfo->get(['status' =>DEVICE_STATUS_NORMAL,'belongdeviceid'=>0,'output' => ['deviceid','hostname']]);
    foreach ($dependDevices as $deviceid => $row){
        $data['allIndepend'][$row['deviceid']] = $row;
    }
               
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
    
    
    $data['form'] = 'deviceForm';
    if(getRequest('action') == 'modify'){
        $data['action'] = 'modify.posted';
        if(!hasRequest('id')){
            show_error_message(_('Parameters error!'));
        }
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('id');
        $options['selectFile'] = ['id','file_title','file_name'];
        $options['selectContact'] = ['id','contact_name'];
        $data['deviceinfo'] = $CDeviceInfo->get($options);
        if(is_null($data['deviceinfo'])){
            show_error_message(_('Parameters error!'));
        }
        $data['deviceinfo'] =  $data['deviceinfo'][getRequest('id')];
        $data['deviceinfo']['id'] = getRequest('id');
    }
    else{
        $data['action'] = 'add.posted';
    }
    $idcroomView = new CNewView('deviceinfo.edit', $data);
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
        $options['selectType'] = ['typeid','typename'];
        $options['selectRoom'] = ['id','room_name'];
        $options['selectIdcBox'] = ['id','box_no'];
        $options['selectBelong'] = ['deviceid','hostname'];
        $options['selectOS'] = ['osid','osname'];
        $options['selectBrand'] = ['id','local_name'];
        $options['selectCreateUser'] = ['userid','alias','name'];
        $options['selectFile'] = ['id','file_title','file_name'];
        $options['selectContact'] = ['id','contact_name'];
        
        $DeviceInfo = new CDevice();
        $result =  $DeviceInfo->get($options);
        if(is_null($result) || count($result)<1){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get device infomation!')];
        }
        else{
            $id = getRequest('id');
            $data['labels'] = $DeviceInfo->getLabels([],$withOutFields);
            $data['DetailsData'] = $result[$id];
            $data['objectName'] ='deviceinfo';
            
            $data['title'] = _("Device Information");
            $data['DetailsData']['typeid'] = $data['DetailsData']['selectType'][$data['DetailsData']['typeid']]['typename'];
            $data['DetailsData']['brandid'] = $data['DetailsData']['selectBrand'][$data['DetailsData']['brandid']]['local_name'];
            $deviceSize = $DeviceInfo->deviceSize;
            $data['DetailsData']['size'] = $deviceSize[$data['DetailsData']['size']];
            $data['DetailsData']['createdate'] = isset($data['DetailsData']['createdate'])?date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['createdate']):SPACE; 
            $data['DetailsData']['warrantystartdate'] = isset($data['DetailsData']['warrantystartdate'])?date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['warrantystartdate']):SPACE; 
            $data['DetailsData']['warrantyenddate'] = isset($data['DetailsData']['warrantyenddate'])?date("Y"._('Year')."m"._('Month')."d"._('Day'),$data['DetailsData']['warrantyenddate']):SPACE; 
            $data['roomid'] = $data['DetailsData']['roomid'];
            $data['boxid'] = $data['DetailsData']['boxid'];
            $data['DetailsData']['roomid'] = $data['DetailsData']['selectRoom'][$data['DetailsData']['roomid']]['room_name'];
            $data['DetailsData']['boxid'] = $data['DetailsData']['selectIdcBox'][$data['DetailsData']['boxid']]['box_no'];
            $data['DetailsData']['belongdeviceid'] = $data['DetailsData']['selectBelong'][$data['DetailsData']['belongdeviceid']]['hostname'];
            $data['DetailsData']['userid'] = $data['DetailsData']['selectCreateUser']['name'];
            $data['DetailsData']['isruning'] = ($data['DetailsData']['isruning'] == DEVICE_RUNING_STATUS_RUNING)?_('Runing'):_('Shutdown');
            switch ($data['DetailsData']['status']){
                case DEVICE_STATUS_OFFLINE:
                    $status = _('Offline');
                    break;
                case DEVICE_STATUS_NORMAL:
                    $status = _('Normal');
                    break;
                default:
                    $status = _('Mantaince');
                    break;
            }
            $data['DetailsData']['status'] = $status;
            $data['DetailsData']['osid'] = $data['DetailsData']['selectOS'][$data['DetailsData']['osid']]['osname'];
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
        $data['sortField'] = 'hostname';
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
    $options['output'] = ["deviceid","typeid","size","model","hostname","ips","roomid","boxid","position","belongdeviceid","isruning","status","osid","brandid"];
    $options['selectType'] = ['typeid','typename'];
    $options['selectRoom'] = ['id','room_name'];
    $options['selectIdcBox'] = ['id','box_no'];
    $options['selectBelong'] = ['deviceid','hostname'];
    $options['selectOS'] = ['osid','osname'];
    $options['selectBrand'] = ['id','local_name'];
    
    $CDeviceInfo = new CDevice();
    $data['deviceinfo'] = $CDeviceInfo->get($options);
    
    $ViewFile = 'device.list';
}
if(!zbx_empty($ViewFile)){
    $ContactView = new CNewView($ViewFile, $data);
    $ContactView->render();
    $ContactView->show();
}

function check_input(){
    global $System_Settings,$ZBX_MESSAGES;
    if(!hasRequest('hardinfo') || strlen(getRequest('hardinfo'))<3 || strlen(getRequest('name'))>255 ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('HardInfo is invalid!')];
    }
    if(hasRequest('shortname') &&(strlen(getRequest('hostname'))<0 || strlen(getRequest('hostname'))>50) ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('Host Name is invalid!')];
    }
    $createdate = zbxDateToTime(getRequest('createdate_year').getRequest('createdate_month').getRequest('createdate_day').getRequest('createdate_hour').getRequest('createdate_minute'));
    $warrantystartdate = zbxDateToTime(getRequest('warrantystartdate_year').getRequest('warrantystartdate_month').getRequest('warrantystartdate_day').getRequest('warrantystartdate_hour').getRequest('warrantystartdate_minute'));
    $warrantyenddate = zbxDateToTime(getRequest('warrantyenddate_year').getRequest('warrantyenddate_month').getRequest('warrantyenddate_day').getRequest('warrantyenddate_hour').getRequest('warrantyenddate_minute'));
    
    if($createdate > $warrantystartdate ){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('PurchaseTime was later than WarrantySince!')];
    }
    if($warrantystartdate > $warrantyenddate){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('WarrantySince was later than WarrantyTo!')];
    }
}
require_once dirname(__FILE__).'/include/page_footer.php';


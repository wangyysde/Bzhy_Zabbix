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
$page['title'] = _('File');
$page['file'] = 'bzhyfiles.php';
$page['hist_arg'] = array();
$page['bzhyscripts'] = ['bzhycommon.js'];


$themes = array_keys(Z::getThemes());
//$INSTALL_SCRIPT="install_scripts.bat";
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
$data = [];
if(hasRequest('action') && getRequest('action') == 'disable.posted'){
    $response = null ; 
    if(!hasRequest('fileid') || trim(getRequest('fileid')) === ""){
        $response = _('No file has specified!');
    }
    else{
        $fileid = trim(getRequest('fileid'));
        $options['ids'] = $fileid;
        $options['output'] = API_OUTPUT_EXTEND;
        $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
        $cfile = bzhyCBase::getInstanceByObject('file', []);
        $data['files'] = $cfile->get($options);
        if(count($data['files'])<1){
            $response = _('The file you specified has not found!');
        }
        else{
            if(count($data['files'][$fileid]['relatedObjects']) >0){
                $response = _('Have some objects related to the file you disabling!');
            }
        }
    }
    if(is_null($response)){
        $dbdata['file_deluserid'] = CWebUser::$data['userid'];
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['file_deluserip'] = substr($ip, 0, 39);
        $dbdata['file_deltime'] = time();
        $dbdata['file_status'] = BZHY_STATUS_DISABLED;
        $cfile->data['dbdata'] =  $dbdata;
        $cfile->data['fileid'] = getRequest('fileid');
        $cfile->data['action'] = 'disable.posted';
        if($cfile->update()){
            $response = _('The file has be disabled!');
        }
        else{
            $response = _('The file can not be disabled!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'enable.posted'){
    $response = null ; 
    if(!hasRequest('fileid') || trim(getRequest('fileid')) == ""){
        $response = _('No file has specified!');
    }
    else{
        $dbdata['file_lastuserid'] = CWebUser::$data['userid'];
        $cfile = bzhyCBase::getInstanceByObject('file', []);
        $cfile->data['fileid'] = getRequest('fileid');
        $cfile->data['action'] = 'enable.posted';
        $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $dbdata['file_lastuserip'] = substr($ip, 0, 39);
        $dbdata['file_lasttime'] = time();
        $dbdata['file_status'] = BZHY_STATUS_RUNING;
        $dbdata['file_deluserid'] = NULL;
        $dbdata['file_deluserip'] = '';
        $dbdata['file_deltime'] = '';
        $cfile->data['dbdata'] =  $dbdata;
        if($cfile->update()){
            $response = _('The file has be enabled!');
        }
        else{
            $response = _('The file can not be enabled!');
        }
    }
    echo  json_encode($response);
    exit;
}
elseif(hasRequest('action') && getRequest('action') == 'del.posted'){
    $response = null ; 
    if(!hasRequest('fileid') || trim(getRequest('fileid')) == ""){
        $response = _('No file has specified!');
    }
    else{
        $cfile = bzhyCBase::getInstanceByObject('file', []);
        $cfile->data['action'] = 'del.posted';
        $options['output'] = API_OUTPUT_EXTEND;
        $options['ids'] = getRequest('fileid');
        $result =  $cfile->get($options);
        if(is_null($result) || count($result)<1){
            $response = _('Could not get file!');
        }
        else{
            $cfile->data['fileid'] = getRequest('fileid');
            $data['file'] = $result[getRequest('fileid')];
            $filename = $data['file']['file_name'];
            if($cfile->update()){
                $destination = bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path']."/".$filename;
                @unlink($destination);
                $response = _('The file has be deleted!');
            }
            else{
                $response = _('The file can not be deleted!');
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

if(hasRequest('action') && getRequest('action') == 'add.posted' ){
    check_input(true);
    if(count($ZBX_MESSAGES) > 0){
        show_error_message(_('Input Error'));
    }
    else{
        $ext = strtolower(substr($_FILES['file_name']['name'], (strrpos($_FILES['file_name']['name'], ".")+1)));
        $name = strtolower(substr($_FILES['file_name']['name'],0,strrpos($_FILES['file_name']['name'], "."))).time();
        $destination = bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path']."/".$name.".".$ext;
        if(!file_exists( bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path'])){
            if(!mkdir( bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path'],0777)){
                $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Upload file error!')];
                show_error_message(_('Directory for saving upload files is not exists,and can not create it.Please check permissions!'));
            }
        }
        if(count($ZBX_MESSAGES)<1){
            if(!move_uploaded_file ($_FILES['file_name']['tmp_name'], $destination)){
                $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Upload file error!')];
                show_error_message(_('upload file'));
            }
            else{
                $cfile = bzhyCBase::getInstanceByObject('file', []);
                $dbdata = ['file_title' => getRequest('title'),'file_name' => $name.".".$ext,'file_uploadtime' =>time()];
                $dbdata['file_userid'] = $dbdata['file_lastuserid'] = CWebUser::$data['userid'];
                $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
                $dbdata['file_userip'] = $dbdata['file_lastuserip'] = substr($ip, 0, 39);
                $dbdata['file_desc'] = getRequest('file_desc');
                $dbdata['file_lasttime'] = time();
                $dbdata['file_value'] = BZHY_STATUS_RUNING;
                $cfile->data['dbdata'] =  $dbdata;
                $cfile->data['action'] = 'add.posted';
                if($cfile->update($data)){
                    $ZBX_MESSAGES[] = ['type'=>'info','message'=>_('Upload file successful!')];
                    show_message(_('upload file'));
                }
                else{
                    @unlink($destination);
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Upload file error!')];
                    show_error_message(_('upload file'));
                }
            }
        }
    }
}
elseif (hasRequest('action') && getRequest('action') == 'modify.posted') {
    $isreloaded = trim($_FILES['file_name']['name'])==''?FALSE:TRUE;
    check_input($isreloaded);
    if(count($ZBX_MESSAGES) > 0){
        show_error_message(_('Input Error'));
    }
    else{
        if($isreloaded){
            $ext = strtolower(substr($_FILES['file_name']['name'], (strrpos($_FILES['file_name']['name'], ".")+1)));
            $name = strtolower(substr($_FILES['file_name']['name'],0,strrpos($_FILES['file_name']['name'], "."))).time();
            $destination = bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path']."/".$name.".".$ext;
            $file_name = $name.".".$ext;
            if(!file_exists(bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path'])){
                if(!mkdir(bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path'],0777)){
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Upload file error!')];
                    show_error_message(_('Directory for saving upload files is not exists,and can not create it.Please check permissions!'));
                }
            }
            if(count($ZBX_MESSAGES)<1){
                if(!move_uploaded_file ($_FILES['file_name']['tmp_name'], $destination)){
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Upload file error!')];
                    show_error_message(_('upload file'));
                }
            }
        }
        else{
            $file_name = getRequest('oldfile_name');
        }
        if(count($ZBX_MESSAGES)<1){
            $dbdata = ['file_title' => getRequest('title'),'file_name' =>$file_name,'file_lasttime' =>time(),'file_desc'=>getRequest('file_desc') ];
            $dbdata['file_lastuserid'] = CWebUser::$data['userid'];
            $ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $dbdata['file_lastuserip'] = substr($ip, 0, 39);
            $cfile = bzhyCBase::getInstanceByObject('file', []);
            $cfile->data['dbdata'] =  $dbdata;
            $cfile->data['fileid'] = getRequest('fileid');
            $cfile->data['action'] = 'modify.posted';
            if($cfile->update()){
                $ZBX_MESSAGES[] = ['type'=>'info','message'=>_('Modify file successful!')];
                if($isreloaded){
                    @unlink( bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path']."/".getRequest('oldfile_name'));
                }
                show_message(_('upload file'));
            }
            else{
                if($isreloaded){
                    $destination = bzhyCBase::getRootDir()."/".$System_Settings['upload_file_path']."/".$file_name;
                    @unlink($destination);
                }
                $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify file error!')];
                show_error_message(_('Modify file'));
            }
        }
    }
    
}
elseif(hasRequest('action') && (getRequest('action') == 'add' || getRequest('action') == 'modify')){
    if(getRequest('action') == 'modify'){
        $data['action'] = 'modify.posted';
        if(!hasRequest('fileid')){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Parameters error!')];
        }
        else{
            $data['form'] = 'uploadfile';
            $options['output'] = API_OUTPUT_EXTEND;
            $options['ids'] = getRequest('fileid');
            $cfile = bzhyCBase::getInstanceByObject('file', []);
            $result =  $cfile->get($options);
            if(is_null($result) || count($result)<1){
                $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get file!')];
            }
            $data['file'] = $result[getRequest('fileid')]; 
        }
    }
    else{
        $data['action'] = 'add.posted';
        $data['form'] = 'uploadfile';
    }
    if(count($ZBX_MESSAGES)>0){
        show_error_message(_('Ocurred a error'));
    }
    else{
        $fileView = new CView('bzhyResource.file.edit', $data);
        $fileView->render();
        $fileView->show();
    }
}
elseif (hasRequest('action') && getRequest('action') == 'details.posted'){
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
        $cFile = bzhyCBase::getInstanceByObject('file', []);
        $result =  $cFile->get($options);
        if(is_null($result) || count($result)<1){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Could not get file infomation!')];
        }
        else{
            $data['labels'] = bzhyCBase::getFieldsLabelByObject('file');
            $data['DetailsData'] = $result[getRequest('id')];
            unset($options);
            $data['objectName'] ='file';
        }
    }
    if(count($ZBX_MESSAGES)>0){
        show_error_message(_('Ocurred a error'));
    }
    else{
        $fileView = new CView('bzhyResource.popup', $data);
        $fileView->render();
        $fileView->show();
    }
}
else
{
    if(hasRequest('sort')){
        $data['sortField'] = getRequest('sort');
    }
    else{
        $data['sortField'] = 'file_title';
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
    $options['selectObject'] = ['countOutput' => null,'groupCount'=>null ];
    $options['sortfield'] = $data['sortField'];
    $options['sortorder'] = $data['sortOrder'];
    $CFile = bzhyCBase::getInstanceByObject('file', []);
    $data['files'] = $CFile->get($options);
    $fileView = new CView('bzhyResource.file.list', $data);
    $fileView->render();
    $fileView->show();
}

function check_input($checknewfile=true){
    global $System_Settings,$ZBX_MESSAGES;
    if(!hasRequest('title') || strlen(getRequest('title'))<2){
        $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('You should input file title')];
    }
    if($checknewfile){
        if(!is_uploaded_file($_FILES['file_name']['tmp_name'])){
            $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('You should select a file to upload!')];
        }
        if(!zbx_is_allowupload($_FILES['file_name'])){
            $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('The type of the file you selected is not allowed upload!')];
        }
        if($_FILES['file_name']['size'] > $System_Settings['allow_upload_file_size']){
            $ZBX_MESSAGES[] = ["type"=>"error","message"=>_('The MAX value of file size you allowed to upload is: '.str2mem($System_Settings['allow_upload_file_size']))];
        }
    }
}
require_once dirname(__FILE__).'/include/page_footer.php';


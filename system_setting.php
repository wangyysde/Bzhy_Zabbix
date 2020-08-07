<?php
require_once dirname(__FILE__).'/include/config.inc.php';
global $ZBX_MESSAGES;
$page['title'] = _('System Set');
$page['file'] = 'system_setting.php';
$page['hist_arg'] = array();

$themes = array_keys(Z::getThemes());
$INSTALL_SCRIPT="install_scripts.bat";
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
require_once dirname(__FILE__).'/include/page_header.php';
if (hasRequest('settingtype_id') && hasRequest('update')){
    $errmsg=NULL;
    $csystemsetting = new CSysSet();
    $options['output'] = ['settingtype_id','setting_name','php_check_method'];
    $options['byType'] = $settingtype_id;
    $data['form'] = 'SystemSetting';
    $data['settingtype_id'] =  $settingtype_id;
    $data['settingItem'] = $csystemsetting->get($options);
    if(count($data['settingItem'])<1){                  //Checking data valid
        $errmsg=_('System error.');
    }
    else { 
        foreach ($data['settingItem'] as $key => $item){
            if(trim($item['php_check_method']) != "none"){
                $inputname = $item['setting_name'];
                $inputvalue = getRequest($inputname);
                if(!chkInputData($item['php_check_method'],$inputname,$inputvalue)){
                    $errmsg = _('At least one data you just input is wrong!');
                    break;
                }
            }
        }
    }
    foreach ($data['settingItem'] as $key => $item){
        $inputname = $item['setting_name'];
        $inputvalue = getRequest($inputname);
        
    }
    $allow_upload_file_size = getRequest('allow_upload_file_size');
    $upload_file_path = getRequest('upload_file_path');
    if(zbx_empty($allow_upload_file_size)){
        $ZBX_MESSAGES[] = ['type'=>'error','message'=> _('Allow upload file size must be a number!')];
    }
    if(!is_bye_size($allow_upload_file_size)){
        $ZBX_MESSAGES[] = ['type'=>'error','message'=> _('Allow upload file size must be a number!')];
    }
    
    if(zbx_empty($upload_file_path)){
        $ZBX_MESSAGES[] = ['type'=>'error','message'=> _('Allow upload file size must be a number!')];
    }
    if(!file_exists(dirname(__FILE__).$upload_file_path) || !is_writable(dirname(__FILE__).$upload_file_path)){
        $ZBX_MESSAGES[] = ['type'=>'error','message'=> _('Upload path NOT exist or unwriteable!')];
    }
    if(count($ZBX_MESSAGES) > 0 ){
        show_error_message(_('Input Error'));
    }
    else{
        $allow_upload_file_size = str2mem($allow_upload_file_size);
        if(!hasRequest('allow_upload_file_type')){
            $allow_upload_file_type = 0;
        }
        else{
            $allow_upload_file_type = implode(',', getRequest('allow_upload_file_type'));
        }
        $options['allow_upload_file_type'] = $allow_upload_file_type;
        $options['allow_upload_file_size'] = $allow_upload_file_size;
        $options['upload_file_path'] = $upload_file_path;
        $csystemsetting = new CSysSet();
        if(!$csystemsetting->update($options)){
            show_error_message(_('Update system settins error!'));
        }
        else{
            show_message(_('Update system settins successful!'));
        }
    }
    jsRedirect($_SERVER["PHP_SELF"]."?settingtype_id=".getRequest('settingtype_id'),SCREEN_REFRESH_TIMEOUT);
}
else{
    if(!hasRequest('settingtype_id') || !is_numeric($_REQUEST['settingtype_id']))
        $settingtype_id = 1;          
    else
        $settingtype_id = getRequest('settingtype_id');
    $csystemsetting = new CSysSet();
    $typeOptions["output"] = ['settingtype_name','settingtype_shortname'];
    $data['settingType'] = $csystemsetting->getType($typeOptions);
    $options['output'] = ['settingtype_id','setting_name','setting_title','setting_helpmsg','setting_value',
        'input_type','data_type','js_check_method'];
    $options['byType'] = $settingtype_id;
    $options['selectItems'] =TRUE;
    $data['form'] = 'SystemSetting';
    $data['settingtype_id'] =  $settingtype_id;
    $data['update'] =  TRUE; 
    if((count($data['settingItem'] = $csystemsetting->get($options)))<1 || count($data['settingType'])<1){
        show_error_message(_('System error.'));
    }
    else { 
        $systemsettingView = new CView('configuration.systemsetting', $data);
        $systemsettingView->render();
        $systemsettingView->show();
    }
}

require_once dirname(__FILE__).'/include/page_footer.php';


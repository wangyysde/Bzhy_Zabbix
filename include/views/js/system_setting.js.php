<script type="text/javascript">
var sysSettingFlag = "";


function chgSettingFlag(obj,value){
    if(value){
        sysSettingFlag=rmNameFromStr(sysSettingFlag,obj);
    }
    else{
        sysSettingFlag=addNameToStr(sysSettingFlag,obj);
    }
}

function chkFileType(){ 
    alert(sysSettingFlag);
    var chkObjs = document.getElementsByName('allow_upload_file_type[]');
    if(getCheckedBoxNum('allow_upload_file_type[]') <1){ 
	if(!confirm('Are you sure disabled upload?')){ 
		chgSettingFlag(chkObjs[0],false); 
		return false;
	} 
        else{
		chgSettingFlag(chkObjs[0],true); 
		return true;
        }
    }
    else{ 
	chgSettingFlag(chkObjs[0],true); 
	return true;
    } 
    
}
function system_setting_submit(){
    alert(sysSettingFlag);
   if(sysSettingFlag.length <1){
       var formid = document.getElementById('<?=$data['form']?>');
       formid.submit();
       return true; 
   }
   else{
       return false;
   }
}
</script>


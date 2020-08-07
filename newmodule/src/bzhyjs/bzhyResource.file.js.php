 <script type="text/javascript">

function submit_file(){
    <?php
    global $System_Settings;
    echo "var allow_upload_file_size = ".$System_Settings['allow_upload_file_size'].";\n";
    echo build_js_var(explode(",",$System_Settings['allow_upload_file_type']),"allow_upload_file_type");
    ?>
    var title = document.getElementById('title');
    if(title.value.length < 2){
        <?="alert('"._('You should input file title')."');\n\r" ?>
        return false; 
    }
    var oldfilename = document.getElementById("oldfile_name");
    var filename_obj = document.getElementById("file_name");
    var filename = filename_obj.value;
    if(oldfilename){
        oldfilename = oldfilename.value;
        if(filename.length<3){
            <?="var reloadfile=confirm('"._('Are you want not reload file?')."');\r" ?> 
            if(!reloadfile){
                return false;
            }
        }
    }
    else{    
        if(filename.length<3){
            <?="alert('"._('You should select a file to upload!')."');\n\r" ?>
            return false; 
        }
    }
    if(filename.length>=3){
        var fileext = filename.substr(filename.lastIndexOf(".")+1).toLowerCase();
        var is_allow_upload = false;
        for(var i=0; i<(allow_upload_file_type.length);i++){
            var allow_ext = allow_upload_file_type[i].toLowerCase();
            is_allow_upload = (fileext === allow_ext) ? true : is_allow_upload;
        }
        if(!is_allow_upload){
            <?="alert('"._('The type of the file you selected is not allowed upload!')."');\n\r" ?>
            return false; 
        }
        var fileSize = 0;
        var isIE = /msie/i.test(navigator.userAgent) && !window.opera; 
        if (isIE && !filename_obj.files) {          
            var fileSystem = new ActiveXObject("Scripting.FileSystemObject");   
            var file = fileSystem.GetFile(filename);               
            fileSize = file.Size;         
        }else {  
            fileSize = filename_obj.files[0].size;     
        } 
        if(fileSize>allow_upload_file_size){
            <?="alert('"._('The MAX value of file size you allowed to upload is: '.str2mem($System_Settings['allow_upload_file_size']).'. The size of the file you seleced is bigger !')."');\r" ?>
            return false; 
        }
    }
    var formid = document.getElementById('<?=$data['form']?>');
    formid.submit();
    return true; 
}
</script>
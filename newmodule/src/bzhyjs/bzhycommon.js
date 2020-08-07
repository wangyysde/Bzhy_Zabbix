function confirmAndRefresh(url,msg,paras){
    if(Confirm(msg)){
        jQuery.ajax({
            cache:false,
            url:url,
            type:'GET',
            dataType:'json',
            data:paras,
            timeout:10000,
        //    error:function(data){ alert(data.);},
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(XMLHttpRequest.status);
                        alert(XMLHttpRequest.readyState);
                        alert(textStatus);
                    },
            success:function(data){
                alert(data);
                window.location.reload();
            }
        });
        return true;
    }
    else{
        return false; 
    }
}

function is_bye_size(obj, allowempty) {
    var bye_size_units = new Array("b","k","m","g","t");
    var value_num = null; 
    if(!obj){
        return false;
    }
   var value = obj.value; 
   if(value === null ){
        if(!allowempty){
            return false;
        }
        else{
            obj.value = 0;
            return true;
        }
   }
   if(value.length>1){
        var lastchar = value.charAt((value.length-1));
        if(isNaN(lastchar)){
            var isunit = false;
            lastchar = lastchar.toString().toUpperCase();
            for(var i=0; i<bye_size_units.length;i++){
                var unit_item = bye_size_units[i].toString().toUpperCase();
                if(unit_item == lastchar){
                    isunit = true; 
                    value_num = value.substr(0,(value.length-1));
                }
            }
            if(!isunit){
                return false;
            }
            else{
                return true;
            }
        }
        else{
            value_num =  value; 
        }
        return !isNaN(value_num);
   }
   else{
       if(value.length>0){
           return !isNaN(value);
       }
       else{
            retrun (!allowempty) ? false : true;
        }
   }
}


function checkStrLength(str,min,max){
    if(!is_string(str))
    {
        return false;
    }
    if(min<0 && empty(str)){
        return false;
    }
    if(str.length<min || str.length>max){
        return false;
    }
    return true;
}

function is_tel(tel){
    var teleReg = /^((0\d{2,3})-)(\d{7,8})$/;
    if(!teleReg.test(tel)) return false;
    return true;
}

function is_mp(mp){
    var mobileReg =/^1[345678]\d{9}$/;
    if(!mobileReg.test(mp)) return false;
    return true;
}

function is_email(email){
    var regExp = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
    if(!regExp.test(email)) return false;
    return true;
}


function Bye2Num(value){
    var bye_size_units = new Array("b","k","m","g","t");
    var ret = 0;
    if(value.length>1){
        var lastchar = value.charAt((value.length-1));
        if(isNaN(lastchar)){
            var isunit = false;
            lastchar = lastchar.toString().toUpperCase();
            value_num = value.substr(0,(value.length-1));
            if(isNaN(value_num)){
               ret = -1;
            }
            else{
                lastchar = lastchar.toString().toUpperCase();
                ret = value_num;
                switch (lastchar){
                    case "T":
                        ret = ret * 1024;
                    case "G":
                        ret = ret * 1024;
                    case "M":
                        ret = ret * 1024;
                    case "K":
                        ret = ret * 1024;
                        break;
                    default:
                        ret = -1;
                }
            }
        }
        else{
            value_num = value; 
            if(isNaN(value_num)){
               ret = -1;
            }
            else{
                ret = value_num;
            }
        }
    }
    else{
        if(value.length>0){
            if(isNaN(value)){
                ret = -1;
            }
            else{
                ret = value;
            }
        }
        else{
            ret = -1;
        }
    }
    return ret;        
        
}

function validateByeSize(obj,min,max,msg){
    if(min === 0){
        if(!is_bye_size(obj,true)){
            if(is_string(msg)){
                alert(msg);
            }
            return false;
        }
    }
    else{
        if(!is_bye_size(obj,false)){
            if(is_string(msg)){
                alert(msg);
            }
            return false;
        }
    }
    var value = obj.value;
    value = Bye2Num(value);
    if(value === -1){
        if(is_string(msg)){
            alert(msg);
        }
        return false;
    }
    min = Bye2Num(min);
    if(min>0){
        if(value < min){
            if(is_string(msg)){
                alert(msg);
            }
            return false;
        }
    }
    max = Bye2Num(min);
    if(max>0){
        if(value > max){
            if(is_string(msg)){
                alert(msg);
            }
            return false;
        }
    }
    return true;
}


function getCheckedBoxNum(chkName){
    var chkObjs = document.getElementsByName(chkName);
    var checkedNum=0; 
    for(var i=0; i<chkObjs.length;i++){
        if(chkObjs[i].checked){
            checkedNum++;
        }
    }
    return checkedNum;
}

function addNameToStr(str,obj){
    var Flag = false;
    var str_Array = new Array();
    if(!obj){
        return str;
    }
    if(str.length>0){
        str_Array = str.split(",");
        for(var i=0;i<str_Array.length;i++){
            if(str_Array[i].toString().toUpperCase() == obj.name.toString().toUpperCase()){
                Flag=true;
            }
        }
    }
    if(!Flag){
        if(str.length>0){
            str = str + "," + obj.name.toString();
        }
        else{
            str = obj.name.toString();
        }
    }
    return str; 
}

function rmNameFromStr(str,obj){
    var tmpStr = "";
    var str_Array = new Array();
    if(!obj){
        return str;
    }
    if(str.length>0){
        str_Array = str.split(",");
        for(var i=0;i<str_Array.length;i++){
            if(str_Array[i].toString().toUpperCase() != obj.name.toString().toUpperCase()){
                if(tmpStr.length == 0){
                    tmpStr = str_Array[i];
                }
                else{
                    tmpStr = tmpStr + ',' + str_Array[i];
                }
            }
        }
    }
    str = tmpStr;
    return str;
}

function getDateFromCalendar(CalendarId){
    var retdatatime,field_name,year,month,day,hour,minute;
    if(empty(CalendarId)){
        return null;
    }
    
    field_name = CalendarId + '_year';
    if(!document.getElementById(field_name)){
        return null;
    }
    year = document.getElementById(field_name).value;
    
    field_name = CalendarId + '_month';
    if(!document.getElementById(field_name)){
        return null;
    }
    month = document.getElementById(field_name).value;
    
    field_name = CalendarId + '_day';
    if(!document.getElementById(field_name)){
        return null;
    }
    day = document.getElementById(field_name).value;
    
    field_name = CalendarId + '_hour';
    if(!document.getElementById(field_name)){
        hour = null;
    }
    else{
        hour = document.getElementById(field_name).value;
    }
    
    field_name = CalendarId + '_minute';
    if(!document.getElementById(field_name)){
        minute = null;
    }
    else{
        minute = document.getElementById(field_name).value;
    }
    
    if(hour == null || minute == null){
        retdatatime = year + month + day;
    }
    else{
        retdatatime = year + month + day + hour + minute;
    }
    
    return retdatatime;
}

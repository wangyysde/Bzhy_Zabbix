

<script type="text/javascript">

function idc_submit(){
    if(!checkStrLength(document.getElementById("name").value,3,255)){
        <?="alert('"._('Name you input is invalid!')."');\n\r" ?>
        return false;
    }
    if(!checkStrLength(document.getElementById("shortname").value,0,50)){
        <?="alert('"._('ShortName you input is invalid!')."');\n\r" ?>
        return false;
    }
    var starttime_year=document.getElementById("room_starttime_year").value;
    var starttime_month=document.getElementById("room_starttime_month").value;
    var starttime_day=document.getElementById("room_starttime_day").value;
    var starttime_hour=document.getElementById("room_starttime_hour").value;
    var starttime_minute=document.getElementById("room_starttime_minute").value;
    var starttime = starttime_year + starttime_month + starttime_day + starttime_hour + starttime_minute;
    var room_endtime_year=document.getElementById("room_endtime_year").value;
    var room_endtime_month=document.getElementById("room_endtime_month").value;
    var room_endtime_day=document.getElementById("room_endtime_day").value;
    var room_endtime_hour=document.getElementById("room_endtime_hour").value;
    var room_endtime_minute=document.getElementById("room_endtime_minute").value;
    var endtime = room_endtime_year + room_endtime_month + room_endtime_day + room_endtime_hour + room_endtime_minute;
    if(starttime >= endtime){
        <?="alert('"._('Active Since Or Active Till you input is invalid!')."');\n\r" ?>
        return false;
    }
    if(!checkStrLength(document.getElementById("room_addr").value,3,255)){
        <?="alert('"._('Address you input is invalid!')."');\n\r" ?>
        return false;
    }
    var formid = document.getElementById('<?=$data['form']?>');
    formid.submit();
    return true; 
}
</script>


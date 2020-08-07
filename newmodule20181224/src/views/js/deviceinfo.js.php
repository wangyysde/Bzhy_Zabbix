<script type="text/javascript">

function <?=$this->data['form']?>_submit(){
    if(!checkStrLength(document.getElementById("hardinfo").value,3,255)){
        <?="alert('"._('HardInfo is invalid!')."');\n\r" ?>
        return false;
    }
    
    var createdate = getDateFromCalendar("createdate");
    var warrantystartdate = getDateFromCalendar("warrantystartdate");
    var warrantyenddate = getDateFromCalendar("warrantyenddate");
    
    if((warrantystartdate != null && warrantystartdate != null) && warrantystartdate >warrantystartdate){
        <?="alert('"._('PurchaseTime was later than WarrantySince!')."');\n\r" ?>
    }
    
    if((createdate != null && warrantyenddate != null) && createdate >warrantyenddate){
        <?="alert('"._('WarrantySince was later than WarrantyTo!')."');\n\r" ?>
    }
    
    if(!checkStrLength(document.getElementById("hostname").value,3,255)){
        <?="alert('"._('Host Name is invalid!')."');\n\r" ?>
        return false;
    }
    
    var formid = document.getElementById('<?=$data['form']?>');
    formid.submit();
    return true; 
}
</script>


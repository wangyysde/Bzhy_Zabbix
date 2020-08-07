

<script type="text/javascript">

function contact_submit(){
    if(!checkStrLength(document.getElementById("contact_name").value,3,20)){
        <?="alert('"._('Name you input is invalid!')."');\n\r" ?>
        return false;
    }
    if(!checkStrLength(document.getElementById("contact_position").value,3,50)){
        <?="alert('"._('Duty you input is invalid!')."');\n\r" ?>
        return false;
    }
    if(!checkStrLength(document.getElementById("contact_company").value,3,50)){
        <?="alert('"._('Company you input is invalid!')."');\n\r" ?>
        return false;
    }
    var contact_tel = document.getElementById("contact_tel").value
    var contact_mp = document.getElementById("contact_mp").value
    var contact_email = document.getElementById("contact_email").value
    if(!is_tel(contact_tel) && !is_mp(contact_mp) && !is_email(contact_email)){
        <?="alert('"._('You should input Tel or MP or EMAIL!')."');\n\r" ?>
        return false;
    }
    var formid = document.getElementById('<?=$data['form']?>');
    formid.submit();
    return true; 
}
</script>


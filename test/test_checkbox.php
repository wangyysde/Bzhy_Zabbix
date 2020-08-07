<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var_dump($_REQUEST);
?>
<script lang="javascript">
    function checkvalue(){
        var objcheckbox = document.getElementsByName("checkbox[]");
        var checkednum=0; 
        for(i=0; i<objcheckbox.length;i++){
          //  obj = document.getElementsByName("checkbox[]");
          //  alert(obj.)
            if(objcheckbox[i].checked){
                alert('checked!!!!');
                checkednum++;
            }
            else{
                alert('unchecked!!!');
            }
        }
    }
 </script>
<form name='test_checkbox' action="./test_checkbox.php" target="_self">
    <input type="checkbox" id="checkbox1" name="checkbox[]" value="111111">aaaaaaaaa
    <input type="checkbox" id="checkbox2" name="checkbox[]" value="222222">bbbb
    <input type="checkbox" id="checkbox3" name="checkbox[]" value="333333">ccccc
    <input type="button" onclick="Javascript:checkvalue();">
</form>


<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(isset($_REQUEST['action'])){
    var_dump($_FILES);
}
?>
<form method="post" action="test_upload.php" accept-charset="utf-8" name="uploadfile" enctype="multipart/form-data" id="uploadfile">
    <input type="file" id="file_name" name="file" value="" style="width: 453px;">
    <input type="hidden" name="action" value="1">
    <input type="submit">
</form>

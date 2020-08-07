<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */
function zbx_menu() {
    $zbx_menu = [
                'aaa' =>"aaaaaa",
                'bbb' => "bbbb",
                'ccc' => "cccc",
                
            ];
    
    include'test_include.inc.php';        
       
    foreach($zbx_menu as $key=>$value){
        echo "key:".$key."value:".$value."<br>";
    }
}

zbx_menu();
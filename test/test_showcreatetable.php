<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

$db = mysqli_connect("localhost","zabbixnew","Zaq!@wsX","zabbixnew");
if(!$db){
    die('Could not connect: '.mysqli_connect_error());
}
$result= mysqli_query($db,"SHOW COLUMNS FROM deviceinfo");
if($result){
    while( $result_array = mysqli_fetch_assoc($result)){
        var_dump($result_array);
        echo "<br>";
    }
    
}
else{
    echo mysql_error();
}
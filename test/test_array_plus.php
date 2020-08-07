<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

$a = ['aaaa','bbbb','cccc'];
$b[0] = 'AAAAAA';
$b[0] = $b[0] + $a;
foreach ($b[0] as $key => $value){
    echo "Key:".$key." value: ".$value." <br>";
}
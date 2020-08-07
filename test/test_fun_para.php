<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

function display($name,...$args){
    echo "count:".count($args)."<br>";
    echo 'name:'.$name."<br>";
    echo 'args:'."<br>";
    print_r($args);
    echo "<br>";
}
display('AAAAAAA');
display('fdipzone', 'programmer');
display('terry', 'designer', 1, 2);
display('aoao', 'tester', array('a','b'), array('c'), array('d'));

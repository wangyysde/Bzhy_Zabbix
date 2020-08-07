<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

class myclass{
    public function __construct() {
        echo $this->objectName."<br>";
    }
}

class myclass2 extends myclass{
    protected $objectName ="test object name";
}

class myclass3 extends myclass{
    protected $objectName ="this is myclass3 object name";
}
$myclass =  new myclass2();
$mysclass1 = new myclass3();

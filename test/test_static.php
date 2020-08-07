<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

class api{
    protected static $instances = [];
    
    protected static $num ;
    
    public function __construct(){
        echo 'construct is runing<br>';
        if(!isset(self::$instances['aaa']) || !(self::$instances['aaa'] instanceof self)){
            echo 'initation is runing<br>';
            self::$instances['aaa'] = $this;
            self::$num = 0;
        }
    }
    
    public static function getobject($name){
        if((self::$instances['aaa'] instanceof self)){  
            return self::$instances['aaa']; 
        }
        else{
            return null;
        }
    }
    
    public function addNum(){
        self::$num++;
    }
    
    public function getNum(){
        return self::$num;
    }
    
    public function test_construct(){
        echo "The test construct is runing<br>";
    }
    
    public static function test_static_fun(){
        echo "The test_static_fun is runing<br>";
    }
    
    public static function static_getNum(){
        return self::$num;
    }
    
    public static function set_num(){
        self::$num = 10;
        return TRUE;
    }
}

api::test_static_fun();
echo "the num is".api::static_getNum()."<br>";
$testapi = new api();
echo "the num is".$testapi->getNum()."<br>";
$testapi->addNum();
echo "the num is".$testapi->getNum()."<br>";
echo "the num is".api::static_getNum()."<br>";

$inst = api::getobject('aaa');
if(!$inst){
    echo "can not get instance<br>";
}
 else {
    echo "Before add the num is".$inst->getNum()."<br>";
    $inst->addNum();
    echo "After add the num is".$inst->getNum()."<br>";
}

echo "By API Before add the num is".api::static_getNum()."<br>";
echo "By INSTANCE Before add the num is".$inst->getNum()."<br>";
api::set_num();
echo "By API After add the num is".api::static_getNum()."<br>";
echo "By INSTANCE After add the num is".$inst->getNum()."<br>";
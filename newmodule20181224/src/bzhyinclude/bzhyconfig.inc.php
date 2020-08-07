<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


// reset the LC_CTYPE locale so that case transformation functions would work correctly
// it is also required for PHP to work with the Turkish locale (https://bugs.php.net/bug.php?id=18556)
// WARNING: this must be done before executing any other code, otherwise code execution could fail!
// this will be unnecessary in PHP 5.5
setlocale(LC_CTYPE, [
	'C', 'POSIX', 'en', 'en_US', 'en_US.UTF-8', 'English_United States.1252', 'en_GB', 'en_GB.UTF-8'
]);

require_once dirname(__FILE__).'/../classes/common/bzhyCBase.php';

try {
    if(!isset($runmode) || empty($runmode)){
        $runmode = bzhyCBase::EXEC_MODE_DEFAULT;
    }
    $cbase = new bzhyCBase(true,$runmode);
    
    
}

catch (Exception $e) {
    (new bzhyCView('general.warning', [
		'header' => 'Configure error',
		'messages' => [$e->getMessage()],
		'theme' => BZHY_DEFAULT_THEME
	]))->render();

	exit;
}

CProfiler::getInstance()->start();

global $ZBX_SERVER, $ZBX_SERVER_PORT, $page;

$page = [
	'title' => null,
	'file' => null,
	'scripts' => null,
	'type' => null,
	'menu' => null
];

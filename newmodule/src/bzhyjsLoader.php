<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


// get language translations
require_once dirname(__FILE__).'/include/gettextwrapper.inc.php';
require_once dirname(__FILE__).'/include/js.inc.php';
require_once dirname(__FILE__).'/include/locales.inc.php';

// if we must provide language constants on language different from English
if (isset($_GET['lang'])) {
	if (function_exists('bindtextdomain')) {
		// initializing gettext translations depending on language selected by user
		$locales = zbx_locale_variants($_GET['lang']);
		foreach ($locales as $locale) {
			putenv('LC_ALL='.$locale);
			putenv('LANG='.$locale);
			putenv('LANGUAGE='.$locale);
			if (setlocale(LC_ALL, $locale)) {
				break;
			}
		}
		bindtextdomain('frontend', 'locale');
		bind_textdomain_codeset('frontend', 'UTF-8');
		textdomain('frontend');
	}
	// numeric Locale to default
	setlocale(LC_NUMERIC, ['C', 'POSIX', 'en', 'en_US', 'en_US.UTF-8', 'English_United States.1252', 'en_GB', 'en_GB.UTF-8']);
}

require_once dirname(__FILE__).'/include/translateDefines.inc.php';

// available scripts 'scriptFileName' => 'path relative to js/'
$availableJScripts = [
    'bzhyclass.calendar.js' => '',
    'bzhycommon.js' =>'',
    'bzhyjquery-3.3.1.min.js'=>''
];

$tranStrings = [

	'bzhyclass.calendar.js' => [
		'S_JANUARY' => _('January'),
		'S_FEBRUARY' => _('February'),
		'S_MARCH' => _('March'),
		'S_APRIL' => _('April'),
		'S_MAY' => _('May'),
		'S_JUNE' => _('June'),
		'S_JULY' => _('July'),
		'S_AUGUST' => _('August'),
		'S_SEPTEMBER' => _('September'),
		'S_OCTOBER' => _('October'),
		'S_NOVEMBER' => _('November'),
		'S_DECEMBER' => _('December'),
		'S_MONDAY_SHORT_BIG' => _x('M', 'Monday short'),
		'S_TUESDAY_SHORT_BIG' => _x('T', 'Tuesday short'),
		'S_WEDNESDAY_SHORT_BIG' => _x('W', 'Wednesday short'),
		'S_THURSDAY_SHORT_BIG' => _x('T', 'Thursday short'),
		'S_FRIDAY_SHORT_BIG' => _x('F', 'Friday short'),
		'S_SATURDAY_SHORT_BIG' => _x('S', 'Saturday short'),
		'S_SUNDAY_SHORT_BIG' => _x('S', 'Sunday short'),
		'S_NOW' => _('Now'),
		'S_DONE' => _('Done'),
		'S_TIME' => _('Time')
	]
];

if (empty($_GET['files'])) {
    $files = [];
}
else{
    $files = $_GET['files'];
}

$js = 'if (typeof(locale) == "undefined") { var locale = {}; }'."\n";
foreach ($files as $file) {
	if (isset($tranStrings[$file])) {
		foreach ($tranStrings[$file] as $origStr => $str) {
			$js .= "locale['".$origStr."'] = ".zbx_jsvalue($str).";";
		}
	}
}

foreach ($files as $file) {
	if (isset($availableJScripts[$file])) {
		$js .= file_get_contents('bzhyjs/'.$availableJScripts[$file].$file)."\n";
	}
}

$jsLength = strlen($js);
$etag = md5($jsLength);
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
	header('HTTP/1.1 304 Not Modified');
	header('ETag: '.$etag);
	exit;
}

header('Content-type: text/javascript; charset=UTF-8');
header('Cache-Control: public, must-revalidate');
header('ETag: '.$etag);

echo $js;

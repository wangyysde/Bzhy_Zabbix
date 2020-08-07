<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

bzhyCSession::start();

if (!isset($page['type'])) {
	$page['type'] = PAGE_TYPE_HTML;
}
if (!isset($page['file'])) {
	$page['file'] = basename($_SERVER['PHP_SELF']);
}
$_REQUEST['fullscreen'] = getRequest('fullscreen', 0);
if ($_REQUEST['fullscreen'] === '1') {
	if (!defined('ZBX_PAGE_NO_MENU')) {
		define('ZBX_PAGE_NO_MENU', 1);
	}
	define('ZBX_PAGE_FULLSCREEN', 1);
}

require_once dirname(__FILE__).'/../include/menu.inc.php';

if (!defined('ZBX_PAGE_NO_THEME')) {
	define('ZBX_PAGE_NO_THEME', false);
}

switch ($page['type']) {
	case PAGE_TYPE_IMAGE:
		set_image_header();
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_XML:
		header('Content-Type: text/xml');
		header('Content-Disposition: attachment; filename="'.$page['file'].'"');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_JS:
		header('Content-Type: application/javascript; charset=UTF-8');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_JSON:
		header('Content-Type: application/json');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_JSON_RPC:
		header('Content-Type: application/json-rpc');
		if(!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_CSS:
		header('Content-Type: text/css; charset=UTF-8');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_TEXT:
	case PAGE_TYPE_TEXT_RETURN_JSON:
	case PAGE_TYPE_HTML_BLOCK:
		header('Content-Type: text/plain; charset=UTF-8');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_TEXT_FILE:
		header('Content-Type: text/plain; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$page['file'].'"');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_CSV:
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$page['file'].'"');
		if (!defined('ZBX_PAGE_NO_MENU')) {
			define('ZBX_PAGE_NO_MENU', 1);
		}
		break;
	case PAGE_TYPE_HTML:
	default:
		header('Content-Type: text/html; charset=UTF-8');

		global $ZBX_SERVER_NAME;

		// page title
		$pageTitle = '';
		if (isset($ZBX_SERVER_NAME) && $ZBX_SERVER_NAME !== '') {
			$pageTitle = $ZBX_SERVER_NAME.NAME_DELIMITER;
		}
		$pageTitle .= isset($page['title']) ? $page['title'] : _('Zabbix');

		if ((defined('ZBX_PAGE_DO_REFRESH') || defined('ZBX_PAGE_DO_JS_REFRESH')) && bzhyCWebUser::$data['refresh']) {
			$pageTitle .= ' ['._s('refreshed every %1$s sec.', bzhyCWebUser::$data['refresh']).']';
		}
		break;
}

// construct menu
$main_menu = [];
$sub_menus = [];

//Cancel menu permission
$denied_page_requested = zbx_construct_menu($main_menu, $sub_menus, $page);

// render the "Deny access" page
//if ($denied_page_requested) {
//	access_deny(ACCESS_DENY_PAGE);
//}

if ($page['type'] == PAGE_TYPE_HTML) {
    $pageHeader = new bzhyCPageHeader($pageTitle);
    $theme = ZBX_DEFAULT_THEME;
    if (!ZBX_PAGE_NO_THEME) {
        global $DB;
        if (!empty($DB['DB'])) {
            $config = bzhyselect_config();
            $theme = bzhygetUserTheme(bzhyCWebUser::$data);

            $pageHeader->addStyle(bzhygetTriggerSeverityCss($config));

            // perform Zabbix server check only for standard pages
            if ((!defined('ZBX_PAGE_NO_MENU') || defined('ZBX_PAGE_FULLSCREEN')) && $config['server_check_interval']
                    && !empty($ZBX_SERVER) && !empty($ZBX_SERVER_PORT)) {
                    $page['scripts'][] = 'servercheck.js';
		}
            }
    }
    $pageHeader->addCssFile('styles/'.bzhyCHtml::encode($theme).'.css');

    if ($page['file'] == 'sysmap.php') {
        $pageHeader->addCssFile('imgstore.php?css=1&output=css');
    }
    $pageHeader->addJsFile('js/browsers.js');
    $pageHeader->addJsBeforeScripts('var PHP_TZ_OFFSET = '.date('Z').';');

    // show GUI messages in pages with menus and in fullscreen mode
    $showGuiMessaging = (!defined('ZBX_PAGE_NO_MENU') || $_REQUEST['fullscreen'] == 1) ? 1 : 0;
    $path = 'jsLoader.php?ver='.ZABBIX_VERSION.'&amp;lang='.bzhyCWebUser::$data['lang'].'&showGuiMessaging='.$showGuiMessaging;
    $pageHeader->addJsFile($path);

        
    if (!empty($page['scripts']) && is_array($page['scripts'])) {
            foreach ($page['scripts'] as $script) {
		$path .= '&amp;files[]='.$script;
            }
            $pageHeader->addJsFile($path);
	}
        
        
    //The following has been added by wayne 
        
    $path = 'bzhyjsLoader.php?ver='.ZABBIX_VERSION.'&amp;lang='.bzhyCWebUser::$data['lang'].'&showGuiMessaging='.$showGuiMessaging;
    if (!empty($page['bzhyscripts']) && is_array($page['bzhyscripts'])) {
            foreach ($page['bzhyscripts'] as $script) {
		$path .= '&amp;files[]='.$script;
            }
            $pageHeader->addJsFile($path);
	}
        
        
        
    $pageHeader->display();
?>
<body>
<div class="<?= ZBX_STYLE_MSG_BAD_GLOBAL ?>" id="msg-bad-global"></div>
<?php
}

define('PAGE_HEADER_LOADED', 1);

if (defined('ZBX_PAGE_NO_HEADER')) {
	return null;
}

// checking messages from MVC pages
$message_good = null;
$message_ok = null;
$message_error = null;
$messages = [];

// this code show messages generated by MVC pages
if (bzhyCSession::keyExists('messageOk') || bzhyCSession::keyExists('messageError')) {
    if (bzhyCSession::keyExists('messages')) {
	$messages = bzhyCSession::getValue('messages');
	bzhyCSession::unsetValue(['messages']);
    }

    if (bzhyCSession::keyExists('messageOk')) {
	$message_good = true;
	$message_ok = bzhyCSession::getValue('messageOk');
    }
    else {
	$message_good = false;
	$message_error = bzhyCSession::getValue('messageError');
    }

    bzhyCSession::unsetValue(['messageOk', 'messageError']);
}

if (!defined('ZBX_PAGE_NO_MENU')) {
    $pageMenu = new bzhyCView('bzhylayout.htmlpage.menu', [
	'server_name' => isset($ZBX_SERVER_NAME) ? $ZBX_SERVER_NAME : '',
	'menu' => [
            'main_menu' => $main_menu,
            'sub_menus' => $sub_menus,
            'selected' => $page['menu']
	],
	'user' => [
            'is_guest' => bzhyCWebUser::isGuest(),
            'alias' => bzhyCWebUser::$data['alias'],
            'name' => bzhyCWebUser::$data['name'],
            'surname' => bzhyCWebUser::$data['surname']
        ]
    ]);
    echo $pageMenu->getOutput();
}

if ($page['type'] == PAGE_TYPE_HTML) {
	echo '<div class="'.ZBX_STYLE_ARTICLE.'">';
}

// unset multiple variables
unset($table, $top_page_row, $menu_table, $main_menu_row, $sub_menu_table, $sub_menu_rows);

if ($page['type'] == PAGE_TYPE_HTML && $showGuiMessaging) {
	zbx_add_post_js('initMessages({});');
}

// if a user logs in after several unsuccessful attempts, display a warning
if ($failedAttempts = bzhyCProfile::get('web.login.attempt.failed', 0)) {
    $attempip = bzhyCProfile::get('web.login.attempt.ip', '');
    $attempdate = bzhyCProfile::get('web.login.attempt.clock', 0);

    $error_msg = _n('%4$s failed login attempt logged. Last failed attempt was from %1$s on %2$s at %3$s.',
	'%4$s failed login attempts logged. Last failed attempt was from %1$s on %2$s at %3$s.',
	$attempip,
	bzhy_date2str(DATE_FORMAT, $attempdate),
	bzhy_date2str(TIME_FORMAT, $attempdate),
	$failedAttempts
    );
    error($error_msg);

    bzhyCProfile::update('web.login.attempt.failed', 0, PROFILE_TYPE_INT);
}
show_messages();

// this code show messages generated by MVC pages
if ($message_good !== null) {
    global $ZBX_MESSAGES;

    $ZBX_MESSAGES = $messages;
    show_messages($message_good, $message_ok, $message_error);
}

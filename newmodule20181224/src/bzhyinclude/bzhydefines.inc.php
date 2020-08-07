<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

//idc information status
define('IDC_ROOM_NORMAL',1);
define('IDC_ROOM_CLOSED',0);

//file and contact status
define('STATUS_NORMAL',1);
define('STATUS_DISABLED',0);

//input type 
define('INPUT_TYPE_TEXTBOX',0);
define('INPUT_TYPE_CHECKBOX',1);

define('SEX_MAN',1);
define('SEX_WOMAN',0);

//Data type
define('DATA_TYPE_NORMAL',0);
define('DATA_TYPE_BYE',1);
define('DATA_TYPE_BIT',2);
define('DATA_TYPE_DATE',3);
define('DATA_TYPE_TIME',4);
define('DATA_TYPE_UNIXTIME',5);

//Device status
define('DEVICE_STATUS_OFFLINE', 0);
define('DEVICE_STATUS_NORMAL', 1);
define('DEVICE_STATUS_MANTAINCE', 2);

//Device runing status
define('DEVICE_RUNING_STATUS_SHUTDOWN', 0);
define('DEVICE_RUNING_STATUS_RUNING', 1);

//Database Field type
define('DATABASE_FIELD_TYPE_STRING', 0);
define('DATABASE_FIELD_TYPE_NUMBER', 1);

//Number
define('BZHY_NUM_ZERO', 0);
define('BZHY_NUM_ONE', 1);

//Default port of data getting
define('BZHY_AGENT_DEFAULT_PORT', 10050);
define('BZHY_SNMP_DEFAULT_PORT', 161);
define('BZHY_JMX_DEFAULT_PORT', 12345);
define('BZHY_IPMI_DEFAULT_PORT', 623);

//Host ip type
define('HOST_IP_TYPE_IP', 1);
define('HOST_IP_TYPE_GW', 2);
define('HOST_IP_TYPE_DNS', 3);

//Host IP family
define('HOST_IP_FAMILY_VER4', 4);
define('HOST_IP_FAMILY_VER6', 6);

//Bzhy runing mode 
define('BZHY_RUNING_MODE_DEFAULT', 'default');
define('BZHY_RUNING_MODE_SETUP', 'setup');
define('BZHY_RUNING_MODE_API', 'api');

//Object type
define('BZHY_OBJECT_CLASS_COMMON', 1);
define('BZHY_OBJECT_CLASS_EXTRA', 2);

//Sort direction
define('BZHY_SORT_UP',	'ASC');
define('BZHY_SORT_DOWN','DESC');

// API errors
define('BZHY_API_ERROR_INTERNAL',	111);
define('BZHY_API_ERROR_PARAMETERS',	100);
define('BZHY_API_ERROR_PERMISSIONS',	120);
define('BZHY_API_ERROR_NO_AUTH',	200);
define('BZHY_API_ERROR_NO_METHOD',	300);
define('BZHY_DB_CONNECT_ERROR',	499);
define('BZHY_DB_DBEXECUTE_ERROR',	500);
define('BZHY_DB_RESERVEIDS_ERROR',	501);
define('BZHY_DB_SCHEMA_ERROR',	502);
define('BZHY_DB_INPUT_ERROR',	503);
define('BZHYCONFIG_NOT_FOUND',	505);
define('BZHYCONFIG_ERROR',	506);

// suffixes
define('BZHY_BYTE_SUFFIXES', 'KMGT');
define('BZHY_TIME_SUFFIXES', 'smhdw');

// preg
define('BZHY_PREG_PRINT', '^\x00-\x1F');
define('BZHY_PREG_MACRO_NAME', '([A-Z0-9\._]+)');
define('BZHY_PREG_MACRO_NAME_LLD', '([A-Z0-9\._]+)');
define('BZHY_PREG_INTERNAL_NAMES', '([0-9a-zA-Z_\. \-]+)'); // !!! Don't forget sync code with C !!!
define('BZHY_PREG_NUMBER', '([\-+]?[0-9]+[.]?[0-9]*['.BZHY_BYTE_SUFFIXES.BZHY_TIME_SUFFIXES.']?)');
define('BZHY_PREG_DEF_FONT_STRING', '/^[0-9\.:% ]+$/');
define('BZHY_PREG_DNS_FORMAT', '([0-9a-zA-Z_\.\-$]|\{\$?'.BZHY_PREG_MACRO_NAME.'\})*');
define('BZHY_PREG_HOST_FORMAT', BZHY_PREG_INTERNAL_NAMES);
define('BZHY_PREG_MACRO_NAME_FORMAT', '(\{[A-Z\.]+\})');
define('BZHY_PREG_EXPRESSION_LLD_MACROS', '(\{\#'.BZHY_PREG_MACRO_NAME_LLD.'\})');

//profile type
define('BZHYPROFILE_TYPE_ID',			1);
define('BZHYPROFILE_TYPE_INT',			2);
define('BZHYPROFILE_TYPE_STR',			3);

//user type
define('BZHYUSER_TYPE_USER',		1);
define('BZHYUSER_TYPE_ADMIN',	2);
define('BZHYUSER_TYPE_SUPER_ADMIN',		3);

define('BZHYAPI_OUTPUT_EXTEND',		'extend');
define('BZHYAPI_OUTPUT_COUNT',		'count');

// CSS styles
define('BZHY_STYLE_ACTION_BUTTONS', 'action-buttons');
define('BZHY_STYLE_ACTIVE_INDIC', 'active-indic');
define('BZHY_STYLE_ACTIVE_BG', 'active-bg');
define('BZHY_STYLE_ADM_IMG', 'adm-img');
define('BZHY_STYLE_ARTICLE', 'article');
define('BZHY_STYLE_AVERAGE_BG', 'average-bg');
define('BZHY_STYLE_ARROW_DOWN', 'arrow-down');
define('BZHY_STYLE_ARROW_LEFT', 'arrow-left');
define('BZHY_STYLE_ARROW_RIGHT', 'arrow-right');
define('BZHY_STYLE_ARROW_UP', 'arrow-up');
define('BZHY_STYLE_BLUE', 'blue');
define('BZHY_STYLE_BTN_ADD_FAV', 'btn-add-fav');
define('BZHY_STYLE_BTN_ALT', 'btn-alt');
define('BZHY_STYLE_BTN_CONF', 'btn-conf');
define('BZHY_STYLE_BTN_DEBUG', 'btn-debug');
define('BZHY_STYLE_BTN_GREY', 'btn-grey');
define('BZHY_STYLE_BTN_INFO', 'btn-info');
define('BZHY_STYLE_BTN_LINK', 'btn-link');
define('BZHY_STYLE_BTN_MAX', 'btn-max');
define('BZHY_STYLE_BTN_MIN', 'btn-min');
define('BZHY_STYLE_BTN_REMOVE_FAV', 'btn-remove-fav');
define('BZHY_STYLE_BTN_RESET', 'btn-reset');
define('BZHY_STYLE_BTN_SEARCH', 'btn-search');
define('BZHY_STYLE_BTN_WIDGET_ACTION', 'btn-widget-action');
define('BZHY_STYLE_BTN_WIDGET_COLLAPSE', 'btn-widget-collapse');
define('BZHY_STYLE_BTN_WIDGET_EXPAND', 'btn-widget-expand');
define('BZHY_STYLE_BOTTOM', 'bottom');
define('BZHY_STYLE_BROWSER_LOGO_CHROME', 'browser-logo-chrome');
define('BZHY_STYLE_BROWSER_LOGO_FF', 'browser-logo-ff');
define('BZHY_STYLE_BROWSER_LOGO_IE', 'browser-logo-ie');
define('BZHY_STYLE_BROWSER_LOGO_OPERA', 'browser-logo-opera');
define('BZHY_STYLE_BROWSER_LOGO_SAFARI', 'browser-logo-safari');
define('BZHY_STYLE_BROWSER_WARNING_CONTAINER', 'browser-warning-container');
define('BZHY_STYLE_BROWSER_WARNING_FOOTER', 'browser-warning-footer');
define('BZHY_STYLE_CELL', 'cell');
define('BZHY_STYLE_CELL_WIDTH', 'cell-width');
define('BZHY_STYLE_CENTER', 'center');
define('BZHY_STYLE_CLOCK', 'clock');
define('BZHY_STYLE_CLOCK_FACE', 'clock-face');
define('BZHY_STYLE_CLOCK_HAND', 'clock-hand');
define('BZHY_STYLE_CLOCK_HAND_SEC', 'clock-hand-sec');
define('BZHY_STYLE_CLOCK_LINES', 'clock-lines');
define('BZHY_STYLE_COLOR_PICKER', 'color-picker');
define('BZHY_STYLE_CURSOR_MOVE', 'cursor-move');
define('BZHY_STYLE_CURSOR_POINTER', 'cursor-pointer');
define('BZHY_STYLE_DASHBRD_WIDGET_HEAD', 'dashbrd-widget-head');
define('BZHY_STYLE_DASHBRD_WIDGET_FOOT', 'dashbrd-widget-foot');
define('BZHY_STYLE_DASHED_BORDER', 'dashed-border');
define('BZHY_STYLE_DEBUG_OUTPUT', 'debug-output');
define('BZHY_STYLE_DISABLED', 'disabled');
define('BZHY_STYLE_DISASTER_BG', 'disaster-bg');
define('BZHY_STYLE_DRAG_ICON', 'drag-icon');
define('BZHY_STYLE_DRAG_DROP_AREA', 'drag-drop-area');
define('BZHY_STYLE_TABLE_FORMS_SEPARATOR', 'table-forms-separator');
define('BZHY_STYLE_FILTER_CONTAINER', 'filter-container');
define('BZHY_STYLE_FILTER_BTN_CONTAINER', 'filter-btn-container');
define('BZHY_STYLE_FILTER_FORMS', 'filter-forms');
define('BZHY_STYLE_FILTER_TRIGGER', 'filter-trigger');
define('BZHY_STYLE_FILTER_ACTIVE', 'filter-active');
define('BZHY_STYLE_FLOAT_LEFT', 'float-left');
define('BZHY_STYLE_FORM_INPUT_MARGIN', 'form-input-margin');
define('BZHY_STYLE_FORM_NEW_GROUP', 'form-new-group');
define('BZHY_STYLE_FOOTER', 'footer');
define('BZHY_STYLE_GREEN', 'green');
define('BZHY_STYLE_GREEN_BG', 'green-bg');
define('BZHY_STYLE_GREY', 'grey');
define('BZHY_STYLE_HEADER_LOGO', 'header-logo');
define('BZHY_STYLE_HEADER_TITLE', 'header-title');
define('BZHY_STYLE_HIDDEN', 'hidden');
define('BZHY_STYLE_HIGH_BG', 'high-bg');
define('BZHY_STYLE_HOR_LIST', 'hor-list');
define('BZHY_STYLE_HOVER_NOBG', 'hover-nobg');
define('BZHY_STYLE_ICON_ACKN', 'icon-ackn');
define('BZHY_STYLE_ICON_CAL', 'icon-cal');
define('BZHY_STYLE_ICON_DEPEND_DOWN', 'icon-depend-down');
define('BZHY_STYLE_ICON_DEPEND_UP', 'icon-depend-up');
define('BZHY_STYLE_ICON_INFO', 'icon-info');
define('BZHY_STYLE_ICON_MAINT', 'icon-maint');
define('BZHY_STYLE_ICON_WZRD_ACTION', 'icon-wzrd-action');
define('BZHY_STYLE_INACTIVE_BG', 'inactive-bg');
define('BZHY_STYLE_INFO_BG', 'info-bg');
define('BZHY_STYLE_INPUT_COLOR_PICKER', 'input-color-picker');
define('BZHY_STYLE_LEFT', 'left');
define('BZHY_STYLE_LINK_ACTION', 'link-action');
define('BZHY_STYLE_LINK_ALT', 'link-alt');
define('BZHY_STYLE_LIST_HOR_CHECK_RADIO', 'list-hor-check-radio');
define('BZHY_STYLE_LIST_CHECK_RADIO', 'list-check-radio');
define('BZHY_STYLE_LIST_TABLE', 'list-table');
define('BZHY_STYLE_LOCAL_CLOCK', 'local-clock');
define('BZHY_STYLE_LOG_NA_BG', 'log-na-bg');
define('BZHY_STYLE_LOG_INFO_BG', 'log-info-bg');
define('BZHY_STYLE_LOG_WARNING_BG', 'log-warning-bg');
define('BZHY_STYLE_LOG_HIGH_BG', 'log-high-bg');
define('BZHY_STYLE_LOG_DISASTER_BG', 'log-disaster-bg');
define('BZHY_STYLE_LOGO', 'logo');
define('BZHY_STYLE_MAP_AREA', 'map-area');
define('BZHY_STYLE_MIDDLE', 'middle');
define('BZHY_STYLE_MSG_GOOD', 'msg-good');
define('BZHY_STYLE_MSG_BAD', 'msg-bad');
define('BZHY_STYLE_MSG_BAD_GLOBAL', 'msg-bad-global');
define('BZHY_STYLE_MSG_DETAILS', 'msg-details');
define('BZHY_STYLE_MSG_DETAILS_BORDER', 'msg-details-border');
define('BZHY_STYLE_NA_BG', 'na-bg');
define('BZHY_STYLE_NAV', 'nav');
define('BZHY_STYLE_NORMAL_BG', 'normal-bg');
define('BZHY_STYLE_NOTIF_BODY', 'notif-body');
define('BZHY_STYLE_NOTIF_INDIC', 'notif-indic');
define('BZHY_STYLE_NOTIF_INDIC_CONTAINER', 'notif-indic-container');
define('BZHY_STYLE_NOTHING_TO_SHOW', 'nothing-to-show');
define('BZHY_STYLE_NOWRAP', 'nowrap');
define('BZHY_STYLE_ORANGE', 'orange');
define('BZHY_STYLE_OVERLAY_CLOSE_BTN', 'overlay-close-btn');
define('BZHY_STYLE_OVERLAY_DESCR', 'overlay-descr');
define('BZHY_STYLE_OVERLAY_DESCR_URL', 'overlay-descr-url');
define('BZHY_STYLE_OVERFLOW_ELLIPSIS', 'overflow-ellipsis');
define('BZHY_STYLE_OBJECT_GROUP', 'object-group');
define('BZHY_STYLE_PAGING_BTN_CONTAINER', 'paging-btn-container');
define('BZHY_STYLE_PAGING_SELECTED', 'paging-selected');
define('BZHY_STYLE_PRELOADER', 'preloader');
define('BZHY_STYLE_PROGRESS_BAR_BG', 'progress-bar-bg');
define('BZHY_STYLE_PROGRESS_BAR_CONTAINER', 'progress-bar-container');
define('BZHY_STYLE_PROGRESS_BAR_LABEL', 'progress-bar-label');
define('BZHY_STYLE_RADIO_SEGMENTED', 'radio-segmented');
define('BZHY_STYLE_RED', 'red');
define('BZHY_STYLE_RED_BG', 'red-bg');
define('BZHY_STYLE_REL_CONTAINER', 'rel-container');
define('BZHY_STYLE_RIGHT', 'right');
define('BZHY_STYLE_ROW', 'row');
define('BZHY_STYLE_SCREEN_TABLE', 'screen-table');
define('BZHY_STYLE_SEARCH', 'search');
define('BZHY_STYLE_SELECTED', 'selected');
define('BZHY_STYLE_SELECTED_ITEM_COUNT', 'selected-item-count');
define('BZHY_STYLE_SERVER_NAME', 'server-name');
define('BZHY_STYLE_SETUP_CONTAINER', 'setup-container');
define('BZHY_STYLE_SETUP_FOOTER', 'setup-footer');
define('BZHY_STYLE_SETUP_LEFT', 'setup-left');
define('BZHY_STYLE_SETUP_LEFT_CURRENT', 'setup-left-current');
define('BZHY_STYLE_SETUP_RIGHT', 'setup-right');
define('BZHY_STYLE_SETUP_RIGHT_BODY', 'setup-right-body');
define('BZHY_STYLE_SETUP_TITLE', 'setup-title');
define('BZHY_STYLE_SIGNIN_CONTAINER', 'signin-container');
define('BZHY_STYLE_SIGNIN_LINKS', 'signin-links');
define('BZHY_STYLE_SIGNIN_LOGO', 'signin-logo');
define('BZHY_STYLE_SIGN_IN_TXT', 'sign-in-txt');
define('BZHY_STYLE_STATUS_CONTAINER', 'status-container');
define('BZHY_STYLE_STATUS_DARK_GREY', 'status-dark-grey');
define('BZHY_STYLE_STATUS_GREEN', 'status-green');
define('BZHY_STYLE_STATUS_GREY', 'status-grey');
define('BZHY_STYLE_STATUS_RED', 'status-red');
define('BZHY_STYLE_STATUS_YELLOW', 'status-yellow');
define('BZHY_STYLE_SUBFILTER_ENABLED', 'subfilter-enabled');
define('BZHY_STYLE_TABLE', 'table');
define('BZHY_STYLE_TABLE_FORMS', 'table-forms');
define('BZHY_STYLE_TABLE_FORMS_CONTAINER', 'table-forms-container');
define('BZHY_STYLE_TABLE_FORMS_TD_LEFT', 'table-forms-td-left');
define('BZHY_STYLE_TABLE_FORMS_TD_RIGHT', 'table-forms-td-right');
define('BZHY_STYLE_TABLE_PAGING', 'table-paging');
define('BZHY_STYLE_TABLE_STATS', 'table-stats');
define('BZHY_STYLE_TABS_NAV', 'tabs-nav');
define('BZHY_STYLE_TAG', 'tag');
define('BZHY_STYLE_TFOOT_BUTTONS', 'tfoot-buttons');
define('BZHY_STYLE_TD_DRAG_ICON', 'td-drag-icon');
define('BZHY_STYLE_TIME_ZONE', 'time-zone');
define('BZHY_STYLE_TIMELINE_AXIS', 'timeline-axis');
define('BZHY_STYLE_TIMELINE_DATE', 'timeline-date');
define('BZHY_STYLE_TIMELINE_DOT', 'timeline-dot');
define('BZHY_STYLE_TIMELINE_DOT_BIG', 'timeline-dot-big');
define('BZHY_STYLE_TIMELINE_TD', 'timeline-td');
define('BZHY_STYLE_TIMELINE_TH', 'timeline-th');
define('BZHY_STYLE_TOP', 'top');
define('BZHY_STYLE_TOP_NAV', 'top-nav');
define('BZHY_STYLE_TOP_NAV_CONTAINER', 'top-nav-container');
define('BZHY_STYLE_TOP_NAV_HELP', 'top-nav-help');
define('BZHY_STYLE_TOP_NAV_ICONS', 'top-nav-icons');
define('BZHY_STYLE_TOP_NAV_PROFILE', 'top-nav-profile');
define('BZHY_STYLE_TOP_NAV_SIGNOUT', 'top-nav-signout');
define('BZHY_STYLE_TOP_NAV_ZBBSHARE', 'top-nav-zbbshare');
define('BZHY_STYLE_TOP_SUBNAV', 'top-subnav');
define('BZHY_STYLE_TOP_SUBNAV_CONTAINER', 'top-subnav-container');
define('BZHY_STYLE_TREEVIEW', 'treeview');
define('BZHY_STYLE_TREEVIEW_PLUS', 'treeview-plus');
define('BZHY_STYLE_UPPERCASE', 'uppercase');
define('BZHY_STYLE_WARNING_BG', 'warning-bg');
define('BZHY_STYLE_YELLOW', 'yellow');

// user default theme
define('BZHYTHEME_DEFAULT', 'default');

// the default theme
define('BZHY_DEFAULT_THEME', 'blue-theme');

define('BZHY_AUTH_INTERNAL',	0);
define('BZHY_AUTH_LDAP',		1);
define('BZHY_AUTH_HTTP',		2);

// IMPORTANT!!! by priority DESC
define('BZHYGROUP_GUI_ACCESS_SYSTEM',	0);
define('BZHYGROUP_GUI_ACCESS_INTERNAL', 1);
define('BZHYGROUP_GUI_ACCESS_DISABLED', 2);

define('BZHYHOST_STATUS_MONITORED',		0);
define('BZHYHOST_STATUS_NOT_MONITORED',	1);
define('BZHYHOST_STATUS_TEMPLATE',		3);
define('BZHYHOST_STATUS_PROXY_ACTIVE',	5);
define('BZHYHOST_STATUS_PROXY_PASSIVE',	6);

define('BZHY_FLAG_DISCOVERY_NORMAL',		0x0);
define('BZHY_FLAG_DISCOVERY_RULE',		0x1);
define('BZHY_FLAG_DISCOVERY_PROTOTYPE',	0x2);
define('BZHY_FLAG_DISCOVERY_CREATED',	0x4);

define('BZHYPERM_READ_WRITE',	3);
define('BZHYPERM_READ',			2);
define('BZHYPERM_DENY',			0);
define('BZHYPERM_NONE',			-1);

define('BZHY_MIN_PERIOD',		60); // 1 minute
define('BZHY_MAX_PERIOD',		63072000); // the maximum period for the time bar control, ~2 years (2 * 365 * 86400)
define('BZHY_MAX_DATE',			2147483647); // 19 Jan 2038 05:14:07
define('BZHY_PERIOD_DEFAULT',	3600); // 1 hour

define('BZHY_DB_DB2',		'IBM_DB2');
define('BZHY_DB_MYSQL',		'MYSQL');
define('BZHY_DB_ORACLE',		'ORACLE');
define('BZHY_DB_POSTGRESQL',	'POSTGRESQL');
define('BZHY_DB_SQLITE3',	'SQLITE3');

define('BZHYSPACE',	'&nbsp;');

define('BZHYPAGE_TYPE_HTML',				0);
define('BZHYPAGE_TYPE_IMAGE',				1);
define('BZHYPAGE_TYPE_XML',					2);
define('BZHYPAGE_TYPE_JS',					3); // javascript
define('BZHYPAGE_TYPE_CSS',					4);
define('BZHYPAGE_TYPE_HTML_BLOCK',			5); // simple block of html (as text)
define('BZHYPAGE_TYPE_JSON',				6); // simple JSON
define('BZHYPAGE_TYPE_JSON_RPC',			7); // api call
define('BZHYPAGE_TYPE_TEXT_FILE',			8); // api call
define('BZHYPAGE_TYPE_TEXT',				9); // simple text
define('BZHYPAGE_TYPE_CSV',					10); // CSV format
define('BZHYPAGE_TYPE_TEXT_RETURN_JSON',	11); // input plaintext output json

define('BZHYGRAPH_TYPE_NORMAL',			0);
define('BZHYGRAPH_TYPE_STACKED',		1);
define('BZHYGRAPH_TYPE_PIE',			2);
define('BZHYGRAPH_TYPE_EXPLODED',		3);
define('BZHYGRAPH_TYPE_3D',				4);
define('BZHYGRAPH_TYPE_3D_EXPLODED',	5);
define('BZHYGRAPH_TYPE_BAR',			6);
define('BZHYGRAPH_TYPE_COLUMN',			7);
define('BZHYGRAPH_TYPE_BAR_STACKED',	8);
define('BZHYGRAPH_TYPE_COLUMN_STACKED',	9);

define('BZHYGRAPH_ITEM_DRAWTYPE_LINE',			0);
define('BZHYGRAPH_ITEM_DRAWTYPE_FILLED_REGION',	1);
define('BZHYGRAPH_ITEM_DRAWTYPE_BOLD_LINE',		2);
define('BZHYGRAPH_ITEM_DRAWTYPE_DOT',			3);
define('BZHYGRAPH_ITEM_DRAWTYPE_DASHED_LINE',	4);
define('BZHYGRAPH_ITEM_DRAWTYPE_GRADIENT_LINE',	5);
define('BZHYGRAPH_ITEM_DRAWTYPE_BOLD_DOT',		6);

define('BZHYCALC_FNC_MIN', 1);
define('BZHYCALC_FNC_AVG', 2);
define('BZHYCALC_FNC_MAX', 4);
define('BZHYCALC_FNC_ALL', 7);
define('BZHYCALC_FNC_LST', 9);

define('BZHYGRAPH_YAXIS_SIDE_DEFAULT', 0); // 0 - LEFT SIDE, 1 - RIGHT SIDE

define('BZHYITEM_VALUE_TYPE_FLOAT',		0);
define('BZHYITEM_VALUE_TYPE_STR',		1); // aka Character
define('BZHYITEM_VALUE_TYPE_LOG',		2);
define('BZHYITEM_VALUE_TYPE_UINT64',	3);
define('BZHYITEM_VALUE_TYPE_TEXT',		4);

define('BZHYSEC_PER_MIN',			60);
define('BZHYSEC_PER_HOUR',			3600);
define('BZHYSEC_PER_DAY',			86400);
define('BZHYSEC_PER_WEEK',			604800);
define('BZHYSEC_PER_MONTH',			2592000);
define('BZHYSEC_PER_YEAR',			31536000);
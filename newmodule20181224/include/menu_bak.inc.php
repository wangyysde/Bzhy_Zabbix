<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

$menu_path = [
    'resource' => "newmodule/resource/",
];


$zbx_menu['resource'] = [
    'label' => _('Resource Management'),
        'user_type' => USER_TYPE_ZABBIX_USER,
        'default_page_id' => 0,
	'pages' => [
            [
		'url' => $menu_path['resource'].'contact_maintence.php',
		'label' => _('Contacts')
            ],
		[
                    'url' => $menu_path['resource'].'file_maintence.php',
                    'label' => _('Files')
		]
            ]
];

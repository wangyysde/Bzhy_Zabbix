<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

$pageHeader = (new bzhyCPageHeader(_('Warning').' ['._s('refreshed every %1$s sec.', 30).']'))
	->addCssFile('styles/'.bzhyCHtml::encode($data['theme']).'.css')
	->display();

$buttons = array_key_exists('buttons', $data)
	? $data['buttons']
	: [(new bzhyCButton(null, _('Retry')))->onClick('document.location.reload();')];

echo '<body>';

(new bzhyCDiv(new bzhyCWarning($data['header'], $data['messages'], $buttons)))
	->addClass(BZHY_STYLE_ARTICLE)
	->show();

echo bzhyget_js("setTimeout('document.location.reload();', 30000);");
echo '</body>';
echo '</html>';

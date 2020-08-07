<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */



class bzhyCWarning extends bzhyCDiv {

	public function __construct($header, $messages = [], $buttons = []) {
		parent::__construct($header);
		$this->addClass(BZHY_STYLE_MSG_BAD);
		$this->addClass('msg-global');
		if ($messages) {
			parent::addItem(
				(new bzhyCDiv(
					(new bzhyCList($messages))->addClass(BZHY_STYLE_MSG_DETAILS_BORDER)
				))->addClass(BZHY_STYLE_MSG_DETAILS)
			);
		}
		parent::addItem((new bzhyCDiv($buttons))->addClass('msg-buttons'));
	}
}

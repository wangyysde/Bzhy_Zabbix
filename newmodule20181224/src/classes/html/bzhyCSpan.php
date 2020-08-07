<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


class bzhyCSpan extends bzhyCTag {

	public function __construct($items = null) {
		parent::__construct('span', true);
		$this->addItem($items);
	}
}

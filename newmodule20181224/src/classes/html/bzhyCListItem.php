<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */



class bzhyCListItem extends bzhyCTag {

	public function __construct($value) {
		parent::__construct('li', true);
		$this->addItem($value);
	}
}

<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

class bzhyCDiv extends bzhyCTag {

    public function __construct($items = null) {
	parent::__construct('div', true);
	$this->addItem($items);

	return $this;
    }

    public function setWidth($value) {
	$this->addStyle('width: '.$value.'px;');

	return $this;
    }
}

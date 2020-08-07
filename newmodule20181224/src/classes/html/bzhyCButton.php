<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

class bzhyCButton extends bzhyCTag implements bzhyCButtonInterface {

	public function __construct($name = 'button', $caption = '') {
		parent::__construct('button', true, $caption);
		$this->setAttribute('type', 'button');

		if ($name !== null) {
			$this->setId(bzhy_formatDomId($name));
			$this->setAttribute('name', $name);
		}
	}

	/**
	 * Enable or disable the element.
	 *
	 * @param bool $value
	 */
	public function setEnabled($value) {
		if ($value) {
			$this->removeAttribute('disabled');
		}
		else {
			$this->setAttribute('disabled', 'disabled');
		}
		return $this;
	}
}

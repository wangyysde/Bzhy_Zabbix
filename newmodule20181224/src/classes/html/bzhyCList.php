<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


class bzhyCList extends bzhyCTag {

	private $emptyList;

	/**
	 * Creates a UL list.
	 *
	 * @param array $values			an array of items to add to the list
	 */
	public function __construct(array $values = []) {
		parent::__construct('ul', true);

		foreach ($values as $value) {
			$this->addItem($value);
		}

		if (!$values) {
			$this->addItem(_('List is empty'), 'empty');
			$this->emptyList = true;
		}
	}

	public function prepareItem($value = null, $class = null, $id = null) {
		if ($value !== null) {
			$value = new bzhyCListItem($value);
			if ($class !== null) {
				$value->addClass($class);
			}
			if ($id !== null) {
				$value->setId($id);
			}
		}

		return $value;
	}

	public function addItem($value, $class = null, $id = null) {
		if (!is_null($value) && $this->emptyList) {
			$this->emptyList = false;
			$this->items = [];
		}

		if ($value instanceof bzhyCListItem) {
			parent::addItem($value);
		}
		else {
			parent::addItem($this->prepareItem($value, $class, $id));
		}

		return $this;
	}

}

<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

/**
 * Database backend class for MySQL.
 */
class bzhyMysqlDbBackend extends bzhyDbBackend {

	/**
	 * Check if 'dbversion' table exists.
	 *
	 * @return bool
	 */
	protected function checkDbVersionTable() { 
		$tableExists = DBfetch(DBselect("SHOW TABLES LIKE 'dbversion'"));

		if (!$tableExists) {
			$this->setError(_('The frontend does not match Zabbix database.'));
			return false;
		}

		return true;
	}

}

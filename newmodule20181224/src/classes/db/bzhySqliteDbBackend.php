<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


/**
 * Database backend class for SQLite.
 */
class bzhySqliteDbBackend extends bzhyDbBackend {

    /**
    * Check if 'dbversion' table exists.
    *
    * @return boolean
    */
    protected function checkDbVersionTable() {
        if (!bzhyDBfetch(bzhyDBselect("SELECT name FROM sqlite_master WHERE type='table' AND name='dbversion';"))) {
            $this->setError(_('The frontend does not match Zabbix database.'));
            return false;
	}
	return true;
    }
}

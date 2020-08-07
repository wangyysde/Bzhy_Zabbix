<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */



/**
 * Database backend class for DB2.
 */
class bzhyDb2DbBackend extends bzhhDbBackend {

    /**
    * Check if 'dbversion' table exists.
    *
    * @return boolean
    */  
    protected function checkDbVersionTable() {
        global $DB;
	$tabSchema = bzhy_dbstr(!empty($DB['SCHEMA']) ? $DB['SCHEMA'] : strtoupper($DB['USER']));
	$tableExists = bzhyDBfetch(DBselect('SELECT 1 FROM SYSCAT.TABLES'.
            " WHERE TABNAME='DBVERSION'".
            " AND TABSCHEMA=".$tabSchema));

        if (!$tableExists) {
            $this->setError(_('The frontend does not match Zabbix database.'));
            return false;
	}

	return true;
    }
}

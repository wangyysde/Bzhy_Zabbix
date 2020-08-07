<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * Database backend class for PostgreSQL.
 */
class bzhyPostgresqlDbBackend extends bzhyDbBackend {

    /**
    * Check if 'dbversion' table exists.
    *
    * @return bool
    */
    protected function checkDbVersionTable() {
	global $DB;

	$schema = bzhy_dbstr($DB['SCHEMA'] ? $DB['SCHEMA'] : 'public');

	$tableExists = bzhyDBfetch(DBselect('SELECT 1 FROM information_schema.tables'.
            ' WHERE table_catalog='.bzhy_dbstr($DB['DATABASE']).
            ' AND table_schema='.$schema.
            " AND table_name='dbversion'"
	));

	if (!$tableExists) {
            $this->setError(_('The frontend does not match Zabbix database.'));
            return false;
        }

	return true;
    }
}

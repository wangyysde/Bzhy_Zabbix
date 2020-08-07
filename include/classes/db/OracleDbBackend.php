<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */



/**
 * Database backend class for Oracle.
 */
class bzhyOracleDbBackend extends bzhyDbBackend {

	/**
	 * Check if 'dbversion' table exists.
	 *
	 * @return boolean
	 */
	protected function checkDbVersionTable() {
		$tableExists = bzhyDBfetch(bzhyDBselect("SELECT table_name FROM user_tables WHERE table_name='DBVERSION'"));

		if (!$tableExists) {
			$this->setError(_('The frontend does not match Zabbix database.'));
			return false;
		}

		return true;
	}

	/**
	 * Create INSERT SQL query.
	 * Creation example:
	 *	BEGIN
	 *	INSERT INTO usrgrp (usrgrpid, name, gui_access, users_status, debug_mode)
	 *		VALUES ('20', 'admins', '1', '0', '1');
	 *	INSERT INTO usrgrp (usrgrpid, name, gui_access, users_status, debug_mode)
	 *		VALUES ('21', 'users', '0', '0', '0');
	 *  END;
	 */
	public function createInsertQuery($table, array $fields, array $values) {
		$sql = 'BEGIN';
		$fields = '('.implode(',', $fields).')';
		foreach ($values as $row) {
			$sql .= ' INSERT INTO '.$table.' '.$fields.' VALUES ('.implode(',', array_values($row)).');';
		}
		$sql .= ' END;';

		return $sql;
	}
}

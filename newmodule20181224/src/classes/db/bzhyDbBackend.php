<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * Abstract database backend class.
 */
abstract class bzhyDbBackend {

    protected $error;

    /**
    * Check if 'dbversion' table exists.
    *
    * @return boolean
    */
    abstract protected function checkDbVersionTable();

    /**
    * Check if connected database version matches with frontend version.
    *
    * @return bool
    */
    public function checkDbVersion() {
        if (!$this->checkDbVersionTable()) {
            return false;
        }

        return true;
    }

    /**
    * Check the integrity of the table "config".
    *
    * @return bool
    */
    public function checkConfig() {
	if (!bzhyDBfetch(bzhyDBselect('SELECT NULL FROM config c'))) {
            $this->setError(_('Unable to select configuration.'));
            return false;
	}
	return true;
    }

    /**
    * Create INSERT SQL query for MySQL, PostgreSQL and IBM DB2.
    * Creation example:
    *	INSERT INTO applications (name,hostid,templateid,applicationid)
    *	VALUES ('CPU','10113','13','868'),('Filesystems','10113','5','869'),('General','10113','21','870');
    *
    * @param string $table
    * @param array $fields
    * @param array $values
	 *
	 * @return string
	 */
	public function createInsertQuery($table, array $fields, array $values) {
		$sql = 'INSERT INTO '.$table.' ('.implode(',', $fields).') VALUES ';

		foreach ($values as $row) {
			$sql .= '('.implode(',', array_values($row)).'),';
		}

		$sql = substr($sql, 0, -1);

		return $sql;
	}

	/**
	 * Set error string.
	 *
	 * @param string $error
	 */
	public function setError($error) {
		$this->error = $error;
	}

	/**
	 * Return error or null if no error occurred.
	 *
	 * @return mixed
	 */
	public function getError() {
		return $this->error;
	}
}

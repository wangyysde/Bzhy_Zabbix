<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

/**
 * Creates global database connection.
 *
 * @param string $error returns a message in case of an error
 * @param bool   $debug turns On or Off trace calls when making connections. Suggested debug mode Off during Zabbix setup
 *
 * @return bool
 */
function bzhyDBconnect(&$error) {
    global $DB;
    if (isset($DB['DB'])) {
	return true;
    }
    $result = true;
    $DB['DB'] = null; // global db handler
    $DB['TRANSACTIONS'] = 0; // level of a nested transation
    $DB['TRANSACTION_NO_FAILED_SQLS'] = true; // true - if no statements failed in transaction, false - there are failed statements
    $DB['SELECT_COUNT'] = 0; // stats
    $DB['EXECUTE_COUNT'] = 0;

    if (!isset($DB['TYPE'])) {
	$error = 'Unknown database type.';
	$result = false;
    }
    else {
	switch ($DB['TYPE']) {
            case BZHY_DB_MYSQL:
		$DB['DB'] = @mysqli_connect($DB['SERVER'], $DB['USER'], $DB['PASSWORD'], $DB['DATABASE'], $DB['PORT']);
		if (!$DB['DB']) {
                    $error = 'Error connecting to database: '.trim(mysqli_connect_error());
                    $result = false;
		}
                else {
                    bzhyDBexecute('SET NAMES utf8');
                }

		if ($result) {
                    $dbBackend = new bzhyMysqlDbBackend();
		}
                break;
            case BZHY_DB_POSTGRESQL:
		$pg_connection_string =
                    (!empty($DB['SERVER']) ? 'host=\''.pg_connect_escape($DB['SERVER']).'\' ' : '').
                    'dbname=\''.pg_connect_escape($DB['DATABASE']).'\' '.
                    (!empty($DB['USER']) ? 'user=\''.pg_connect_escape($DB['USER']).'\' ' : '').
                    (!empty($DB['PASSWORD']) ? 'password=\''.pg_connect_escape($DB['PASSWORD']).'\' ' : '').
                    (!empty($DB['PORT']) ? 'port='.pg_connect_escape($DB['PORT']) : '');
		$DB['DB']= @pg_connect($pg_connection_string);
		if (!$DB['DB']) {
                    $error = 'Error connecting to database.';
                    $result = false;
		}
		else {
                    $schemaSet = bzhyDBexecute('SET search_path = '.bzhy_dbstr($DB['SCHEMA'] ? $DB['SCHEMA'] : 'public'), true);
                    if(!$schemaSet) {
			bzhyclear_messages();
			$error = pg_last_error();
			$result = false;
                    }
                    else {
			if (false !== ($pgsql_version = pg_parameter_status('server_version'))) {
                            if ((int) $pgsql_version >= 9) {
				// change the output format for values of type bytea from hex (the default) to escape
				bzhyDBexecute('SET bytea_output = escape');
                            }
			}
                    }
		}

		if ($result) {
                    $dbBackend = new bzhyPostgresqlDbBackend();
		}
		break;
            case BZHY_DB_ORACLE:
		$connect = '';
		if (!empty($DB['SERVER'])) {
                    $connect = '//'.$DB['SERVER'];

                    if ($DB['PORT'] != '0') {
			$connect .= ':'.$DB['PORT'];
                    }
                    if ($DB['DATABASE']) {
			$connect .= '/'.$DB['DATABASE'];
                    }
		}

		$DB['DB'] = @oci_connect($DB['USER'], $DB['PASSWORD'], $connect);
                if ($DB['DB']) {
                    bzhyDBexecute('ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.bzhy_dbstr('. '));
                }
                else {
                    $ociError = oci_error();
                    $error = 'Error connecting to database: '.$ociError['message'];
                    $result = false;
                }

                if ($result) {
                    $dbBackend = new bzhyOracleDbBackend();
                }
		break;
            case BZHY_DB_DB2:
		$connect = '';
		$connect .= 'DATABASE='.$DB['DATABASE'].';';
		$connect .= 'HOSTNAME='.$DB['SERVER'].';';
		$connect .= 'PORT='.$DB['PORT'].';';
		$connect .= 'PROTOCOL=TCPIP;';
		$connect .= 'UID='.$DB['USER'].';';
		$connect .= 'PWD='.$DB['PASSWORD'].';';

		$unicodeprefixes = ['C', 'en_US', 'en_GB'];
		foreach ($unicodeprefixes as $prefix) {
                    $result = setlocale(LC_ALL, [$prefix.'.utf8', $prefix.'.UTF-8']);
			if ($result) {
                            break;
			}
                    }
                    if ($result) {
                        $DB['DB'] = @db2_connect($connect, $DB['USER'], $DB['PASSWORD']);
			if (!$DB['DB']) {
                            $error = 'Error connecting to database: '.db2_conn_errormsg();
                            $result = false;
			}
			else {
                            $options = [
				'db2_attr_case' => DB2_CASE_LOWER
                            ];
                            db2_set_option($DB['DB'], $options, 1);
                            if (isset($DB['SCHEMA']) && $DB['SCHEMA'] != '') {
                                bzhyDBexecute('SET CURRENT SCHEMA='.bzhy_dbstr($DB['SCHEMA']));
                            }
			}
                    }
                    else {
			$error = 'Cannot set UTF-8 locale for web server.';
                    }

                    if ($result) {
			$dbBackend = new bzhyDb2DbBackend();
                    }
                    break;
            case ZBX_DB_SQLITE3:
		if (file_exists($DB['DATABASE'])) {
                    init_sqlite3_access();
                    lock_sqlite3_access();
                    try{
			$DB['DB'] = @new SQLite3($DB['DATABASE'], SQLITE3_OPEN_READWRITE);
                    }
                    catch (Exception $e) {
			$error = 'Error connecting to database.';
                        $result = false;
                    }
                    unlock_sqlite3_access();
		}
                else {
                    $error = 'Missing database';
                    $result = false;
		}

		if ($result) {
                    $dbBackend = new bzhySqliteDbBackend();
		}
                break;
            default:
		$error = 'Unsupported database';
		$result = false;
        }
    }

    if ($result && (!$dbBackend->checkDbVersion() || !$dbBackend->checkConfig())) {
        $error = $dbBackend->getError();
        $result = false;
    }

    if (false == $result) {
	$DB['DB'] = null;
    }

    return $result;
}

function bzhyDBclose() {
    global $DB;
    $result = false;
    if (isset($DB['DB']) && !empty($DB['DB'])) {
	switch ($DB['TYPE']) {
            case BZHY_DB_MYSQL:
                $result = mysqli_close($DB['DB']);
		break;
            case BZHY_DB_POSTGRESQL:
		$result = pg_close($DB['DB']);
		break;
            case BZHY_DB_ORACLE:
		$result = oci_close($DB['DB']);
		break;
            case BZHY_DB_DB2:
		$result = db2_close($DB['DB']);
		break;
            case BZHY_DB_SQLITE3:
		lock_sqlite3_access();
		$DB['DB']->close();
		unlock_sqlite3_access();
		$result = true;
		break;
        }
    }
    unset($DB['DB']);
    return $result;
}

function bzhyDBstart() {
    global $DB;
    $result = false;
    if ($DB['TRANSACTIONS'] != 0) {
	bzhyinfo('POSSIBLE ERROR: Used incorrect logic in database processing, started subtransaction!');
	return $result;
    }

    $DB['TRANSACTIONS']++;
    $DB['TRANSACTION_NO_FAILED_SQLS'] = true;

    if (!isset($DB['DB']) || empty($DB['DB'])) {
	return $result;
    }
    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            $result = bzhyDBexecute('BEGIN');
            break;
	case BZHY_DB_POSTGRESQL:
            $result = bzhyDBexecute('BEGIN');
            break;
	case BZHY_DB_ORACLE:
            $result = true;
            break;
	case BZHY_DB_DB2:
            $result = db2_autocommit($DB['DB'], DB2_AUTOCOMMIT_OFF);
            break;
	case BZHY_DB_SQLITE3:
            lock_sqlite3_access();
            $result = bzhyDBexecute('BEGIN');
            break;
    }
    return $result;
}

/**
 * Closes transaction.
 *
 * @param string $doCommit True - do commit, rollback otherwise. Rollback is also always performed if a sql failed within this transaction.
 *
 * @return bool True - successful commit, False - otherwise
 */
function bzhyDBend($doCommit = true) {
    global $DB;
    $result = false;
    if (!isset($DB['DB']) || empty($DB['DB'])) {
	return $result;
    }

    if ($DB['TRANSACTIONS'] == 0) {
	bzhyinfo('POSSIBLE ERROR: Used incorrect logic in database processing, transaction not started!');
	return $result;
    }

    $DBresult = $doCommit && $DB['TRANSACTION_NO_FAILED_SQLS'];

    if ($DBresult) {
	$DBresult = bzhyDBcommit();
    }
    else {
	bzhyDBrollback();
    }

    $DB['TRANSACTIONS'] = 0;

    return (!is_null($doCommit) && $DBresult) ? $doCommit : $DBresult;
}

function bzhyDBcommit() {
    global $DB;

    $result = false;

    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            $result = bzhyDBexecute('COMMIT');
            break;
        case BZHY_DB_POSTGRESQL:
            $result = bzhyDBexecute('COMMIT');
            break;
	case BZHY_DB_ORACLE:
            $result = oci_commit($DB['DB']);
            break;
	case BZHY_DB_DB2:
            $result = db2_commit($DB['DB']);
            if ($result) {
		db2_autocommit($DB['DB'], DB2_AUTOCOMMIT_ON);
            }
            break;
	case BZHY_DB_SQLITE3:
            $result = bzhyDBexecute('COMMIT');
            unlock_sqlite3_access();
            break;
    }
    return $result;
}

function bzhyDBrollback() {
    global $DB;

    $result = false;

    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            $result = bzhyDBexecute('ROLLBACK');
            break;
	case BZHY_DB_POSTGRESQL:
            $result = bzhyDBexecute('ROLLBACK');
            break;
	case BZHY_DB_ORACLE:
            $result = oci_rollback($DB['DB']);
            break;
	case BZHY_DB_DB2:
            $result = db2_rollback($DB['DB']);
            db2_autocommit($DB['DB'], DB2_AUTOCOMMIT_ON);
            break;
	case BZHY_DB_SQLITE3:
            $result = bzhyDBexecute('ROLLBACK');
            unlock_sqlite3_access();
            break;
    }
    return $result;
}

/**
 * Select data from DB. Use function DBexecute for non-selects.
 *
 * Example:
 * DBselect('select * from users')
 * DBselect('select * from users',50,200)
 *
 * @param string $query
 * @param int $limit    max number of record to return
 * @param int $offset   return starting from $offset record
 *
 * @return resource or object, False if failed
 */
function bzhyDBselect($query, $limit = null, $offset = 0) {
    global $DB;

    $result = false;

    if (!isset($DB['DB']) || empty($DB['DB'])) {
	return $result;
    }

    // add the LIMIT clause
   if(!$query = bzhyDBaddLimit($query, $limit, $offset)) {
	return false;
    }

    $time_start = microtime(true);
    $DB['SELECT_COUNT']++;

    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            if (!$result = mysqli_query($DB['DB'], $query)) {
		error('Error in query ['.$query.'] ['.mysqli_error($DB['DB']).']');
            }
            break;
	case BZHY_DB_POSTGRESQL:
            if (!$result = pg_query($DB['DB'], $query)) {
		bzhyerror('Error in query ['.$query.'] ['.pg_last_error().']');
            }
            break;
        case BZHY_DB_ORACLE:
            if (!$result = oci_parse($DB['DB'], $query)) {
		$e = @oci_error();
		bzhyerror('SQL error ['.$e['message'].'] in ['.$e['sqltext'].']');
            }
            elseif (!@oci_execute($result, ($DB['TRANSACTIONS'] ? OCI_DEFAULT : OCI_COMMIT_ON_SUCCESS))) {
		$e = oci_error($result);
		bzhyerror('SQL error ['.$e['message'].'] in ['.$e['sqltext'].']');
            }
            break;
	case BZHY_DB_DB2:
            $options = [];
            if ($DB['TRANSACTIONS']) {
		$options['autocommit'] = DB2_AUTOCOMMIT_OFF;
            }

            if (!$result = db2_prepare($DB['DB'], $query)) {
		$e = @db2_stmt_errormsg($result);
		bzhyerror('SQL error ['.$query.'] in ['.$e.']');
            }
            elseif (true !== @db2_execute($result, $options)) {
		$e = @db2_stmt_errormsg($result);
		bzhyerror('SQL error ['.$query.'] in ['.$e.']');
		$result = false;
            }
            break;
	case BZHY_DB_SQLITE3:
            if ($DB['TRANSACTIONS'] == 0) {
		lock_sqlite3_access();
            }
            if (false === ($result = $DB['DB']->query($query))) {
		bzhyerror('Error in query ['.$query.'] Error code ['.$DB['DB']->lastErrorCode().'] Message ['.$DB['DB']->lastErrorMsg().']');
            }
            if ($DB['TRANSACTIONS'] == 0) {
		unlock_sqlite3_access();
            }
            break;
    }

    // $result is false only if an error occurred
    if ($DB['TRANSACTION_NO_FAILED_SQLS'] && !$result) {
	$DB['TRANSACTION_NO_FAILED_SQLS'] = false;
    }

    bzhyCProfiler::getInstance()->profileSql(microtime(true) - $time_start, $query);
    return $result;
}

/**
 * Add the LIMIT clause to the given query.
 *
 * NOTE:
 * LIMIT and OFFSET records
 *
 * Example: select 6-15 row.
 *
 * MySQL:
 * SELECT a FROM tbl LIMIT 5,10
 * SELECT a FROM tbl LIMIT 10 OFFSET 5
 *
 * PostgreSQL:
 * SELECT a FROM tbl LIMIT 10 OFFSET 5
 *
 * Oracle, DB2:
 * SELECT a FROM tbe WHERE rownum < 15 // ONLY < 15
 * SELECT * FROM (SELECT * FROM tbl) WHERE rownum BETWEEN 6 AND 15
 *
 * @param $query
 * @param int $limit    max number of record to return
 * @param int $offset   return starting from $offset record
 *
 * @return bool|string
 */
function bzhyDBaddLimit($query, $limit = 0, $offset = 0) {
    global $DB;

    if ((isset($limit) && ($limit < 0 || !bzhy_ctype_digit($limit))) || $offset < 0 || !bzhy_ctype_digit($offset)) {
	$moreDetails = isset($limit) ? ' Limit ['.$limit.'] Offset ['.$offset.']' : ' Offset ['.$offset.']';
	bzhyerror('Incorrect parameters for limit and/or offset. Query ['.$query.']'.$moreDetails);
        return false;
    }

    // Process limit and offset
    if (isset($limit)) {
	switch ($DB['TYPE']) {
            case BZHY_DB_MYSQL:
            case BZHY_DB_POSTGRESQL:
            case BZHY_DB_SQLITE3:
		$query .= ' LIMIT '.intval($limit).' OFFSET '.intval($offset);
		break;
            case BZHY_DB_ORACLE:
            case BZHY_DB_DB2:
		$till = $offset + $limit;
		$query = 'SELECT * FROM ('.$query.') WHERE rownum BETWEEN '.intval($offset).' AND '.intval($till);
		break;
        }
    }

    return $query;
}

function bzhyDBexecute($query, $skip_error_messages = 0) {
    global $DB;

    if (!isset($DB['DB']) || empty($DB['DB'])) {
	return false;
    }

    $result = false;
    $time_start = microtime(true);

    $DB['EXECUTE_COUNT']++;

    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            if (!$result = mysqli_query($DB['DB'], $query)) {
		bzhyerror('Error in query ['.$query.'] ['.mysqli_error($DB['DB']).']');
            }
            break;
	case BZHY_DB_POSTGRESQL:
            if (!$result = (bool) pg_query($DB['DB'], $query)) {
                bzhyerror('Error in query ['.$query.'] ['.pg_last_error().']');
            }
            break;
            case BZHY_DB_ORACLE:
		if (!$result = oci_parse($DB['DB'], $query)) {
                    $e = @oci_error();
                    bzhyerror('SQL error ['.$e['message'].'] in ['.$e['sqltext'].']');
		}
		elseif (!@oci_execute($result, ($DB['TRANSACTIONS'] ? OCI_DEFAULT : OCI_COMMIT_ON_SUCCESS))) {
                    $e = oci_error($result);
                    bzhyerror('SQL error ['.$e['message'].'] in ['.$e['sqltext'].']');
		}
		else {
                    $result = true; // function must return boolean
		}
		break;
            case BZHY_DB_DB2:
		if (!$result = db2_prepare($DB['DB'], $query)) {
                    $e = @db2_stmt_errormsg($result);
                    bzhyerror('SQL error ['.$query.'] in ['.$e.']');
                }
		elseif (true !== @db2_execute($result)) {
                    $e = @db2_stmt_errormsg($result);
                    bzhyerror('SQL error ['.$query.'] in ['.$e.']');
		}
		else {
                    $result = true; // function must return boolean
		}
		break;
            case BZHY_DB_SQLITE3:
                if ($DB['TRANSACTIONS'] == 0) {
                    lock_sqlite3_access();
		}
		if (!$result = $DB['DB']->exec($query)) {
                    bzhyerror('Error in query ['.$query.'] Error code ['.$DB['DB']->lastErrorCode().'] Message ['.$DB['DB']->lastErrorMsg().']');
		}
		if ($DB['TRANSACTIONS'] == 0) {
                    unlock_sqlite3_access();
		}
		break;
    }
    if ($DB['TRANSACTIONS'] != 0 && !$result) {
        $DB['TRANSACTION_NO_FAILED_SQLS'] = false;
    }

    bzhyCProfiler::getInstance()->profileSql(microtime(true) - $time_start, $query);
    return (bool) $result;
}

/**
 * Returns the next data set from a DB resource or false if there are no more results.
 *
 * @param resource $cursor
 * @param bool $convertNulls	convert all null values to string zeroes
 *
 * @return array|bool
 */
function bzhyDBfetch($cursor, $convertNulls = true) {
    global $DB;

    $result = false;

    if (!isset($DB['DB']) || empty($DB['DB']) || is_bool($cursor)) {
        return $result;
    }

    switch ($DB['TYPE']) {
	case BZHY_DB_MYSQL:
            $result = mysqli_fetch_assoc($cursor);
            if (!$result) {
		mysqli_free_result($cursor);
            }
            break;
	case BZHY_DB_POSTGRESQL:
            if (!$result = pg_fetch_assoc($cursor)) {
		pg_free_result($cursor);
            }
            break;
        case BZHY_DB_ORACLE:
            if ($row = oci_fetch_assoc($cursor)) {
		$result = [];
		foreach ($row as $key => $value) {
                    $field_type = strtolower(oci_field_type($cursor, $key));
                    // Oracle does not support NULL values for string fields, so if the string is empty, it will return NULL
                    // convert it to an empty string to be consistent with other databases
                    $value = (str_in_array($field_type, ['varchar', 'varchar2', 'blob', 'clob']) && is_null($value)) ? '' : $value;
                    if (is_object($value) && (strpos($field_type, 'lob') !== false)) {
			$value = $value->load();
                    }
                    $result[strtolower($key)] = $value;
		}
            }
            break;
        case BZHY_DB_DB2:
            if (!$result = db2_fetch_assoc($cursor)) {
		db2_free_result($cursor);
            }
            else {
		// cast all of the values to string to be consistent with other DB drivers: all of them return
		// only strings.
		foreach ($result as &$value) {
                    if ($value !== null) {
                        $value = (string) $value;
                    }
                }
		unset($value);
            }
            break;
	case BZHY_DB_SQLITE3:
            if ($DB['TRANSACTIONS'] == 0) {
		lock_sqlite3_access();
            }
            if (!$result = $cursor->fetchArray(SQLITE3_ASSOC)) {
		unset($cursor);
            }
            else {
		// cast all of the values to string to be consistent with other DB drivers: all of them return
		// only strings.
		foreach ($result as &$value) {
                    $value = (string) $value;
		}
		unset($value);
            }
            if ($DB['TRANSACTIONS'] == 0) {
                unlock_sqlite3_access();
            }
            break;
    }

    if ($result) {
	if ($convertNulls) {
            foreach ($result as $key => $val) {
		if (is_null($val)) {
                    $result[$key] = '0';
		}
            }
	}

        return $result;
    }

    return false;
}


/**
 * Takes an initial part of SQL query and appends a generated WHERE condition.
 * The WHERE condition is generated from the given list of values as a mix of
 * <fieldname> BETWEEN <id1> AND <idN>" and "<fieldname> IN (<id1>,<id2>,...,<idN>)" elements.
 *
 * In some frontend places we can get array with bool as input values parameter. This is fail!
 * Therefore we need check it and return 1=0 as temporary solution to not break the frontend.
 *
 * @param string $fieldName		field name to be used in SQL WHERE condition
 * @param array  $values		array of numerical values sorted in ascending order to be included in WHERE
 * @param bool   $notIn			builds inverted condition
 * @param bool   $sort			values mandatory must be sorted
 *
 * @return string
 */
function bzhydbConditionInt($fieldName, array $values, $notIn = false, $sort = true) {
	$MAX_EXPRESSIONS = 950; // maximum  number of values for using "IN (id1>,<id2>,...,<idN>)"
	$MIN_NUM_BETWEEN = 4; // minimum number of consecutive values for using "BETWEEN <id1> AND <idN>"

	if (is_bool(reset($values))) {
		return '1=0';
	}

	$values = array_keys(array_flip($values));

	if ($sort) {
		natsort($values);

		$values = array_values($values);
	}

	$betweens = [];
	$data = [];

	for ($i = 0, $size = count($values); $i < $size; $i++) {
		$between = [];

		// analyze by chunk
		if (isset($values[$i + $MIN_NUM_BETWEEN])
				&& bccomp(bcadd($values[$i], $MIN_NUM_BETWEEN), $values[$i + $MIN_NUM_BETWEEN]) == 0) {
			for ($sizeMinBetween = $i + $MIN_NUM_BETWEEN; $i < $sizeMinBetween; $i++) {
				$between[] = $values[$i];
			}

			$i--; // shift 1 back

			// analyze by one
			for (; $i < $size; $i++) {
				if (isset($values[$i + 1]) && bccomp(bcadd($values[$i], 1), $values[$i + 1]) == 0) {
					$between[] = $values[$i + 1];
				}
				else {
					break;
				}
			}

			$betweens[] = $between;
		}
		else {
			$data[] = $values[$i];
		}
	}

	// concatenate conditions
	$dataSize = count($data);
	$betweenSize = count($betweens);

	$condition = '';
	$operatorAnd = $notIn ? ' AND ' : ' OR ';

	if ($betweens) {
		$operatorNot = $notIn ? 'NOT ' : '';

		foreach ($betweens as $between) {
			$between = $operatorNot.$fieldName.' BETWEEN '.bzhy_dbstr($between[0]).' AND '.bzhy_dbstr(end($between));

			$condition .= $condition ? $operatorAnd.$between : $between;
		}
	}

	if ($dataSize == 1) {
		$operator = $notIn ? '!=' : '=';

		$condition .= ($condition ? $operatorAnd : '').$fieldName.$operator.bzhy_dbstr($data[0]);
	}
	else {
		$operatorNot = $notIn ? ' NOT' : '';
		$data = array_chunk($data, $MAX_EXPRESSIONS);

		foreach ($data as $chunk) {
			$chunkIns = '';

			foreach ($chunk as $value) {
				$chunkIns .= ','.bzhy_dbstr($value);
			}

			$chunkIns = $fieldName.$operatorNot.' IN ('.substr($chunkIns, 1).')';

			$condition .= $condition ? $operatorAnd.$chunkIns : $chunkIns;
		}
	}

	return (($dataSize && $betweenSize) || $betweenSize > 1 || $dataSize > $MAX_EXPRESSIONS) ? '('.$condition.')' : $condition;
}

/**
 * Escape string for safe usage in SQL queries.
 * Works for ibmdb2, mysql, oracle, postgresql, sqlite.
 *
 * @param array|string $var
 *
 * @return array|bool|string
 */
function bzhy_dbstr($var) {
    global $DB;

    if (!isset($DB['TYPE'])) {
	return false;
    }

    switch ($DB['TYPE']) {
	case ZBX_DB_DB2:
            if (is_array($var)) {
		foreach ($var as $vnum => $value) {
                    $var[$vnum] = "'".db2_escape_string($value)."'";
		}
		return $var;
            }
            return "'".db2_escape_string($var)."'";

	case ZBX_DB_MYSQL:
            if (is_array($var)) {
                foreach ($var as $vnum => $value) {
                    $var[$vnum] = "'".mysqli_real_escape_string($DB['DB'], $value)."'";
		}
		return $var;
            }
            return "'".mysqli_real_escape_string($DB['DB'], $var)."'";

        case ZBX_DB_ORACLE:
            if (is_array($var)) {
		foreach ($var as $vnum => $value) {
                    $var[$vnum] = "'".preg_replace('/\'/', '\'\'', $value)."'";
		}
                return $var;
            }
            return "'".preg_replace('/\'/','\'\'',$var)."'";

	case ZBX_DB_POSTGRESQL:
            if (is_array($var)) {
		foreach ($var as $vnum => $value) {
                    $var[$vnum] = "'".pg_escape_string($value)."'";
		}
		return $var;
            }
            return "'".pg_escape_string($var)."'";

	case ZBX_DB_SQLITE3:
            if (is_array($var)) {
		foreach ($var as $vnum => $value) {
                    $var[$vnum] = "'".$DB['DB']->escapeString($value)."'";
		}
		return $var;
            }
            return "'".$DB['DB']->escapeString($var)."'";

	default:
            return false;
    }
}

/**
 * Takes an initial part of SQL query and appends a generated WHERE condition.
 *
 * @param string $fieldName		field name to be used in SQL WHERE condition
 * @param array  $values		array of string values sorted in ascending order to be included in WHERE
 * @param bool   $notIn			builds inverted condition
 *
 * @return string
 */
function bzhydbConditionString($fieldName, array $values, $notIn = false) {
    switch (count($values)) {
	case 0:
            return '1=0';
	case 1:
            return $notIn
		? $fieldName.'!='.bzhy_dbstr(reset($values))
		: $fieldName.'='.bzhy_dbstr(reset($values));
    }

    $in = $notIn ? ' NOT IN ' : ' IN ';
    $concat = $notIn ? ' AND ' : ' OR ';
    $items = array_chunk($values, 950);

    $condition = '';
    foreach ($items as $values) {
	$condition .= !empty($condition) ? ')'.$concat.$fieldName.$in.'(' : '';
	$condition .= implode(',', bzhy_dbstr($values));
    }

    return '('.$fieldName.$in.'('.$condition.'))';
}

function bzhy_db_search($table, $options, &$sql_parts) {
    list($table, $tableShort) = explode(' ', $table);

    $Fields = bzhyDB::getFields($table);
    
    $start = is_null($options['startSearch']) ? '%' : '';
    $exclude = is_null($options['excludeSearch']) ? '' : ' NOT ';
    $glue = (!$options['searchByAny']) ? ' AND ' : ' OR ';

    $search = [];
    foreach ($options['search'] as $field => $patterns) {
        if (!isset($Fields[$field]) || zbx_empty($patterns)) {
            continue;
	}
	if ($Fields[$field]['type'] != DATABASE_FIELD_TYPE_STRING){
            continue;
        }

	$fieldSearch = [];
	foreach ((array) $patterns as $pattern) {
            if (bzhy_empty($pattern)) {
		continue;
            }

            // escaping parameter that is about to be used in LIKE statement
            $pattern = str_replace("!", "!!", $pattern);
            $pattern = str_replace("%", "!%", $pattern);
            $pattern = str_replace("_", "!_", $pattern);

            if (!$options['searchWildcardsEnabled']) {
		$fieldSearch[] =
                    ' UPPER('.$tableShort.'.'.$field.') '.
                    $exclude.' LIKE '.
                    bzhy_dbstr($start.mb_strtoupper($pattern).'%').
                    " ESCAPE '!'";
            }
            else {
		$pattern = str_replace("*", "%", $pattern);
		$fieldSearch[] =
                    ' UPPER('.$tableShort.'.'.$field.') '.
                    $exclude.' LIKE '.
                    bzhy_dbstr(mb_strtoupper($pattern)).
                    " ESCAPE '!'";
            }
	}

	$search[$field] = '( '.implode($glue, $fieldSearch).' )';
    }

    if (!empty($search)) {
	if (isset($sql_parts['where']['search'])) {
            $search[] = $sql_parts['where']['search'];
	}

	$sql_parts['where']['search'] = '( '.implode($glue, $search).' )';
	return true;
    }

    return false;
}

/**
* Returns true if the table has the given field. If no $tableName is given,
* the current table will be used.
*
* @param string $fieldName
* @param string $tableName
*
* @return boolean
*/
function hasField($fieldName, $tableName = null) {
    $Fields = bzhyDB::getFields($tableName);
    return isset($Fields[$fieldName]);
}

function bzhy_db_distinct($sql_parts) {
    if (count($sql_parts['from']) > 1) {
	return ' DISTINCT ';
    }
    else {
	return ' ';
    }
}
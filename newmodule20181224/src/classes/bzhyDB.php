<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

class bzhyDB {
    
    const DBEXECUTE_ERROR = 1;
    const RESERVEIDS_ERROR = 2;
    const SCHEMA_ERROR = 3;
    const INPUT_ERROR = 4;
    const PARAMETER_ERROR = 5;
    
    /*
     * Table fields is a array using to recording fields to tables map
     * The key of the array are tables name and value are fields 
     */
    protected static $tableFields = [];
    
     /*
     * Object fields is a array using to recording fields to object map
     * The key of the array are object name and value are fields 
     */
    protected static $objectFields = [];

    /**
    * Insert data into DB.
    *
    * @param string $table
    * @param array  $values pair of fieldname => fieldvalue
    * @param bool   $getids
    *
    * @return array    an array of ids with the keys preserved
    */
    public static function insert($table, $values,$getids = true) {
       $resultIds = [];
        if (empty($values) || bzhy_empty($table)) {
            self::exception(BZHY_API_ERROR_PARAMETERS, _('Table name or values is empty.'));
	}
        if(($Fields = self::getTableFields($table)) == FALSE){
            self::exception(self::DBEXECUTE_ERROR, _s('Get table fields error "%1$s".', $table));
        }
        
        $idField = $Fields['id'];
        if($getids){
            $query ="select max(".$idField.") as id From ".$table;
            $ret = DBselect($query);
            if($ret){
                $row = DBfetch($ret);
                $id = ++$row['id'];
            }
            else{
                self::exception(self::DBEXECUTE_ERROR, _s('SQL statement execution has failed "%1$s".', $query));
            }
        }  
        foreach ($values as $key => $row) {
            if(!isset($row[$idField])){
                $row[$idField] = $id;
            }
            self::unSetUnusefulFields($row,$Fields['Fields']);
            if(bzhy_empty($row)){
                continue;
            }
            if(self::checkFieldValues($row, $Fields['Fields']) == FALSE){
                self::exception(self::INPUT_ERROR, _s('Input Error'));
            }
            $sql = 'INSERT INTO '.$table.' ('.implode(',', array_keys($row)).')'.
            ' VALUES ('.implode(',', array_values($row)).')';

            if (!DBexecute($sql)) {
                self::exception(self::DBEXECUTE_ERROR, _s('SQL statement execution has failed "%1$s".', $sql));
            }
            if($getids){
                $resultIds[] = $id++;
                $id = bcadd($id, 1, 0);
            }
	}
        return $resultIds;
    }
    
    public static function checkFieldValues(&$values,$Fields){
        $newvalue = [];

        if(!is_array($values) || !is_array($Fields)){
            self::exception(self::INPUT_ERROR, _s('Input Error'));
            return false;
        }
             
        foreach ($values as $field => $value){
            foreach ($Fields as $key =>$fieldData){
                if(strcasecmp($field, $fieldData["name"]) ==0 ){
                    if(zbx_empty($value) && ($fieldData['null'] == BZHY_NUM_ZERO)){
                        if(!zbx_empty($fieldData['default'])){
                            $value = $fieldData['default'];
                        }
                        else{
                            self::exception(self::INPUT_ERROR, _s('Input Error'));
                        }
                    }
                    if(zbx_empty($value) && ($fieldData['null'] == BZHY_NUM_ONE)){
                        $value = $fieldData['default'];
                    }
                    $newkey = "`".$fieldData["name"]."`";
                    $newvalue[$newkey] = ($fieldData["type"] == DATABASE_FIELD_TYPE_STRING)?"'".$value."'":$value;
                }
            }
        }
        $values = $newvalue;
        return TRUE;
    }
    
    private static function exception($code, $error) {
        throw new DBException($error, $code);
    }
    
    public static function getTableFields($table,$options=[]){
        global  $DB;
        
        if(isset(self::$tableFields[$table]) && !bzhy_empty(self::$tableFields[$table])){
            return self::$tableFields[$table];
        }
        $ret ="";
        
        $defOptions = [
            'Fields' => TRUE,
            'PriKey' => TRUE,
            'Auto_increment' =>TRUE
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        
        if(zbx_empty($table)){
            return FALSE;
        }
        switch ($DB['TYPE']){
            case ZBX_DB_DB2:
                $query = "";                                                    // We will added     
                break;
            case ZBX_DB_MYSQL:
                $query = "SHOW  COLUMNS FROM ".$table;                                               
                break;
            case ZBX_DB_ORACLE:
                $query = "";                                                    // We will added     
                break;
            case ZBX_DB_POSTGRESQL:
                $query = "";                                                    // We will added     
                break;
            case ZBX_DB_SQLITE3:
                $query = "";                                                    // We will added     
                break;
            default:
                return FALSE;
                break;
        }
        
        if(!zbx_empty($query)){
            $res = DBselect($query);
            switch ($DB['TYPE']){
                case ZBX_DB_DB2:                                                // We will added     
                case ZBX_DB_ORACLE:
                case ZBX_DB_POSTGRESQL:
                case ZBX_DB_SQLITE3:
                    return FALSE;
                    break;
                case ZBX_DB_MYSQL:
                    $ret['id'] = NULL;
                    while ($row = DBfetch($res)){
                        $line["name"]= $row["Field"];
                        $fieldType= $row["Type"];
                        $bzhyType= self::getFieldTypeAndLen($fieldType,$type,$len);
                        $bzhyType = ($bzhyType == FALSE)?DATABASE_FIELD_TYPE_STRING:$type;
                        $fieldLen = ($bzhyType == FALSE)?0:$len;
                        $line["type"] = $bzhyType;
                        $line["length"] = $fieldLen;
                        $fieldNull = $row["Null"];
                        if(strcasecmp($fieldNull,"no") == 0){
                            $line["null"] = BZHY_NUM_ZERO;
                        }
                        else{
                            $line["null"] = BZHY_NUM_ONE;
                        }
                        $fieldKey = $row["Key"];
                        $fieldDefault = $row["Default"];
                        $fieldExtra = $row["Extra"];
                        if((strcasecmp($fieldKey, "pri") == 0) || (strcasecmp($fieldExtra, "auto_increment") == 0)){
                           $ret['id'] = $row["Field"];
                        }
                        $line["default"] = ($fieldDefault == NULL)?"NULL":$fieldDefault;
                        $ret['Fields'][] = $line;
                    }
                    break;
                default:
                    return FALSE;
                    break;
            }
        }
        else{
            return FALSE;
        }
        if(!isset(self::$tableFields[$table])){
            self::$tableFields[$table] = $ret;
        }
        return $ret;
    }
    
    public static function getFieldTypeAndLen($type,&$bzhytype,&$len){
        if(zbx_empty($type)){
            return FALSE;
        }
        $left = strpos("(",$type);
        if($left == FALSE){
            return FALSE;
        }
        $typeStr = substr($type,0,$left);
        if(strcasecmp($typeStr,"tinyint") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"smallint") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"mediumint") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"int") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"integer") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"bigint") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"bit") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"real") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"double") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"float") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"decimal") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"numeric") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_NUMBER;
        }
        elseif(strcasecmp($typeStr,"char") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"varchar") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"date") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"time") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"year") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"timestamp") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"datetime") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"tinyblob") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"blob") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"mediumblob") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        elseif(strcasecmp($typeStr,"longblob") == 0){
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        else{
            $bzhytype = DATABASE_FIELD_TYPE_STRING;
        }
        
        $right = strpos(")",$type);
        $length = $right - $left;
        $num =  substr($type,$left,$length);
        if($num == FALSE){
            $len = 0;
        }
        else {
            $len = $num;
        }
        
        return TRUE;
    }
    
    /**
    * Update data in DB.
    *
    * @param string $table
    * @param array $data
    * @param array $data[...]['values'] pair of fieldname => fieldvalue for SET clause
    * @param array $data[...]['where'] pair of fieldname => fieldvalue for WHERE clause
    *
    * @return array of ids
    */
    public static function update($table, $data) {
        if (empty($data)) {
            return true;
        }

        //$tableSchema = self::getSchema($table);

        $data = bzhy_toArray($data);
        if(($Fields = self::getTableFields($table)) == FALSE){
            self::exception(self::DBEXECUTE_ERROR, _s('Get table fields error "%1$s".', $table));
        }
    
        foreach ($data as $row) {
        // check
            if(self::checkFieldValues($row['values'], $Fields['Fields']) == FALSE){
                self::exception(self::INPUT_ERROR, _s('Input Error'));
            }
            //		self::checkValueTypes($table, $row['values']);
            if (empty($row['values'])) {
                self::exception(self::DBEXECUTE_ERROR, _s('Cannot perform update statement on table "%1$s" without values.', $table));
            }

            // set creation
            $sqlSet = '';
            foreach ($row['values'] as $field => $value) {
                if ($sqlSet !== '') {
                    $sqlSet .= ',';
                }
                $sqlSet .= $field.'='.$value;
            }

            if (!isset($row['where']) || empty($row['where']) || !is_array($row['where'])) {
                self::exception(self::DBEXECUTE_ERROR, _s('Cannot perform update statement on table "%1$s" without where condition.', $table));
            }

            // where condition processing
            $sqlWhere = [];
            foreach ($row['where'] as $field => $values) {
                if ((self::checkFieldValues($row['where'], $Fields['Fields]']) == FALSE) || is_null($values)) {
                    self::exception(self::DBEXECUTE_ERROR, _s('Incorrect field "%1$s" name or value in where statement for table "%2$s".', $field, $table));
                }
                $values = bzhy_toArray($values);
                sort($values); // sorting ids to prevent deadlocks when two transactions depend on each other

                $sqlWhere[] = dbConditionString($field, $values);
            }   

            // sql execution
            $sql = 'UPDATE '.$table.' SET '.$sqlSet.' WHERE '.implode(' AND ', $sqlWhere);
            if (!DBexecute($sql)) {
                self::exception(self::DBEXECUTE_ERROR, _s('SQL statement execution has failed "%1$s".', $sql));
            }
        }
    return true;
    }
    
    /**
    * Delete data from DB.
    *
    * Example:
    * DB::delete('applications', array('applicationid'=>array(1, 8, 6)));
    * DELETE FROM applications WHERE applicationid IN (1, 8, 6)
    *
    * bzhyDB::delete('applications', array('applicationid'=>array(1), 'templateid'=array(10)));
    * DELETE FROM applications WHERE applicationid IN (1) AND templateid IN (10)
    *
    * @param string $table
    * @param array  $wheres pair of fieldname => fieldvalues
    * @param bool   $use_or
    *
    * @return bool
    */
    public static function delete($table, $wheres, $use_or = false) {
        if (empty($wheres) || !is_array($wheres)) {
            bzhyCBase::exception(BZHY_DB_DBEXECUTE_ERROR, _s('Cannot perform delete statement on table "%1$s" without where condition.', $table),__FILE__,__LINE__);
	}
        $Fields = bzhyDB::getFields($table);
	$sqlWhere = [];
	foreach ($wheres as $field => $values) {
            if (!isset($Fields[$field]) || is_null($values)) {
		bzhyCBase::exception(BZHY_DB_DBEXECUTE_ERROR, _s('Incorrect field "%1$s" name or value in where statement for table "%2$s".', $field, $table),__FILE__,__LINE__);
            }
            $values = bzhy_toArray($values);
            sort($values); // sorting ids to prevent deadlocks when two transactions depends from each other
		$sqlWhere[] = bzhydbConditionString($field, $values);
        }
        $sql = 'DELETE FROM '.$table.' WHERE '.implode(($use_or ? ' OR ' : ' AND '), $sqlWhere);
        if (!DBexecute($sql)) {
            bzhyCBase::exception(BZHY_DB_DBEXECUTE_ERROR,_s('SQL statement execution has failed "%1$s"', $sql),__FILE__,__LINE__);
        }
        return true;
    }

    
    /**
     * Unset unuseful Fields for insert into DB;
     * @param array $values pair of fieldname => fieldvalue
     * @param array $Fields Fields of a table or null
     * @param string $tablename table name or null
     * If $fields is null then get table fields by this class
     * @return bool return True if successful Or False
     */
    
    public static function unSetUnusefulFields(&$values,$Fields = NULL,$table = NULL){
        if(!is_array($values)){
            return TRUE;
        }
        
        if((zbx_empty($Fields) || !is_array($Fields)) && $table == NULL){ 
            self::exception(self::PARAMETER_ERROR, _s('Parameters error.')); 
        }
        
        if(is_array($Fields)){
            foreach ($values as $key =>$value){
                $found = 0;
                foreach($Fields as $row){
                    if(strcasecmp($row['name'], $key) == 0){
                        $found = 1;
                        break;
                    }
                }
                
                if(!$found){
                    unset($values[$key]);
                }
            }
            return True;
        }
        
        if(!zbx_empty($table)){
            if(!($Fields = self::getTableFields($table))){
                return FALSE; 
            }
            
            $Fields = $Fields['Fields'];
            foreach ($values as $key =>$value){
                $found = 0;
                foreach($Fields as $row){
                    if(strcasecmp($row['name'], $key) == 0){
                        $found = 1;
                        break;
                    }
                }
                
                if(!$found){
                    unset($values[$key]);
                }
            }
            return True;
        }
        
        return FALSE;
    }
    
    /**
    * Constructs an SQL SELECT query for a specific table from the given API options, executes it and returns
    * the result.
    *
    * TODO: add global 'countOutput' support
    *
    * @param string $tableName
    * @param array  $options
    *
    * @return array
    */
   public static function select($tableName, array $options) {
        $limit = isset($options['limit']) ? $options['limit'] : null;

        $sql =  self::createSelectQuery($tableName, $options);

		$objects = DBfetchArray(DBSelect($sql, $limit));

		if (isset($options['preservekeys'])) {
			$rs = [];
			foreach ($objects as $object) {
				$rs[$object[self::getPkByTable($tableName)]] = $object;
			}

			return $rs;
		}
		else {
			return $objects;
		}
	}
    
        
    /**
    * Creates an SQL SELECT query from the given options.
    *
    * @param string $tableName
    * @param array  $options
    *
    * @return array
    */
   public static function createSelectQuery($tableName, array $options) {
        $sqlParts = self::createSelectQueryParts($tableName, $options);

        return self::createSelectQueryFromParts($sqlParts);
    }
    
    /**
    * Builds an SQL parts array from the given options.
    *
    * @param string $tableName
    * @param string $tableAlias
    * @param array  $options
    *
    * @return array		The resulting SQL parts array
    */
   public static function createSelectQueryParts($tableName, array $options) {
    // extend default options
	$Fields = self::getTableFields($tableName);	
        $tablePk = $Fields['id'];
        $tableAlias = bzhyCBase::getTableAliasByTable($tableName);
        $select = zbx_empty($tablePk)?"":(zbx_empty($tableAlias)?$tablePk:$tableAlias.".".$tablePk);
        $from = zbx_empty($tableAlias)?$tableName:$tableName." ".$tableAlias;
	$sqlParts = [
            'select' => [$select],
            'from' => [$from],
            'where' => [],
            'group' => [],
            'order' => [],
            'limit' => null
	];

	// add filter options
	$sqlParts = self::applyQueryFilterOptions($tableName, $tableAlias, $options, $sqlParts);

	// add output options
	$sqlParts = self::applyQueryOutputOptions($tableName, $tableAlias, $options, $sqlParts);

	// add sort options
	$sqlParts = self::applyQuerySortOptions($tableName, $tableAlias, $options, $sqlParts);

	return $sqlParts;
    }
    
        
    /**
    * Modifies the SQL parts to implement all of the filter related options.
    *
    * @param string $tableName
    * @param string $tableAlias
    * @param array $options
    * @param array $sqlParts
    *
    * @return array		The resulting SQL parts array
    */
   public static function applyQueryFilterOptions($tableName, $tableAlias, array $options, array $sqlParts) {
        $Fields = self::getTableFields($tableName);
        $tablePk = $Fields['id'];
        $pkOption = bzhy_empty($tablePk)?NULL:$tablePk.'s';
        $tableId = bzhy_empty($tableAlias)?$tableName:$tableName." ".$tableAlias;
	
        if (!zbx_empty($pkOption) && isset($options[$pkOption])) {
            zbx_value2array($options[$pkOption]);
            $select = zbx_empty($tablePk)?"":(zbx_empty($tableAlias)?$tablePk:$tableAlias.".".$tablePk);
            $fieldName = zbx_empty($tableAlias)?$tablePk:$tableAlias.".".$tablePk;  
            $sqlParts['where'][] = bzhydbConditionString($fieldName, $options[$pkOption]);
	}

	// filters
	if (isset($options['filter']) && is_array($options['filter'])) {
            self::dbFilter($tableId, $options, $sqlParts);
	}

	// search
	if (isset($options['search']) && is_array($options['search'])) {
            bzhy_db_search($tableId, $options, $sqlParts);
	}

	return $sqlParts;
    }
    
        
    /**
    * Apply filter conditions to sql built query.
    *
    * @param string $table
    * @param array  $options
    * @param array  $sqlParts
    *
    * @return bool
    */
   public static function dbFilter($table, $options, &$sqlParts) {
        list($table, $tableShort) = explode(' ', $table);
        
        $fields = bzhyDB::getFields($table);
	$filter = [];
	foreach ($options['filter'] as $field => $value) {
            // skip missing fields and text fields (not supported by Oracle)
            // skip empty values
            if (!isset($fields[$field]) || bzhy_empty($value)) {
                continue;
            }

            zbx_value2array($value);

            $fieldName = zbx_empty($tableShort)?$field:$tableShort.".".$field;  
            $filter[$field] = ($fields[$field]['type'] === DATABASE_FIELD_TYPE_NUMBER)
		? bzhydbConditionInt($fieldName, $value)
		: bzhydbConditionString($fieldName, $value);
            }

            if ($filter) {
		if (isset($sqlParts['where']['filter'])) {
                    $filter[] = $sqlParts['where']['filter'];
		}

                if (!isset($options['searchByAny']) || is_null($options['searchByAny']) || $options['searchByAny'] === false || count($filter) == 1) {
                    $sqlParts['where']['filter'] = implode(' AND ', $filter);
		}
		else {
                    $sqlParts['where']['filter'] = '('.implode(' OR ', $filter).')';
		}

		return true;
            }

	return false;
    }
    
    /*
     * The key of Fields which we got from database is numberic.
     * This function change the key of the fields to filed name 
     * @param $table string table name 
     * @return return a array with field name as key if OK, Or retrun a null Array. 
     */
    public static function getFields($table){
        $ret = [];
        if(bzhy_empty($table)){
            return $ret;
        }
        if(!isset(self::$tableFields[$table]) || bzhy_empty(self::$tableFields[$table])){
            $Fields = self::getTableFields($table);
        }
        else{
            $Fields = self::$tableFields[$table];
        }
        
        $ret = [];
        foreach ($Fields['Fields'] as $field){
            $ret[$field['name']] = $field;
        }
        
        return $ret;
    }
    
    /**
    * Modifies the SQL parts to implement all of the output related options.
    *
    * @param string $tableName
    * @param string $tableAlias
    * @param array  $options
    * @param array  $sqlParts
    *
    * @return array		The resulting SQL parts array
    */
   public static function applyQueryOutputOptions($tableName, $tableAlias, array $options, array $sqlParts) {
	$Fields =  self::getTableFields($tableName);
        $pkFieldId = bzhy_empty($tableAlias)?$Fields['id']:$tableAlias.".".$Fields['id'];

	// count
	if (isset($options['countOutput'])) {
            $sqlParts['select'] = ['COUNT(DISTINCT '.$pkFieldId.') AS rowscount'];

            // select columns used by group count
            if (isset($options['groupCount'])) {
		foreach ($sqlParts['group'] as $fields) {
                    $sqlParts['select'][] = $fields;
		}
            }
	}
	// custom output
	elseif (is_array($options['output'])) {
            // the pk field must always be included for the API to work properly
            $sqlParts['select'] = [$pkFieldId];
            foreach ($options['output'] as $field) {
		if (hasField($field, $tableName)) {
                    $sqlParts['select'][] = bzhy_empty($tableAlias)?$field:$tableAlias.".".$field; 
		}
            }

            $sqlParts['select'] = array_unique($sqlParts['select']);
	}
	// extended output
	elseif ($options['output'] == API_OUTPUT_EXTEND) {
            // TODO: API_OUTPUT_EXTEND must return ONLY the fields from the base table
            $sqlParts = self::addQuerySelect((bzhy_empty($tableAlias)?'*':$tableAlias.".*"), $sqlParts);
	}

	return $sqlParts;
    }

    /**
    * Adds the given field to the SELECT part of the $sqlParts array if it's not already present.
    * If $sqlParts['select'] not present it is created and field appended.
    *
    * @param string $fieldId
    * @param array  $sqlParts
    *
    * @return array
    */
   public static function addQuerySelect($fieldId, array $sqlParts) {
        if (!isset($sqlParts['select'])) {
            return ['select' => [$fieldId]];
	}

	list($tableAlias, $field) = explode('.', $fieldId);

	if (!in_array($fieldId, $sqlParts['select']) && !in_array((bzhy_empty($tableAlias)?'*':$tableAlias.".*"), $sqlParts['select'])) {
            // if we want to select all of the columns, other columns from this table can be removed
            if ($field == '*') {
		foreach ($sqlParts['select'] as $key => $selectFieldId) {
                    list($selectTableAlias,) = explode('.', $selectFieldId);

			if ($selectTableAlias == $tableAlias) {
                            unset($sqlParts['select'][$key]);
			}
                    }
		}

		$sqlParts['select'][] = $fieldId;
            }

	return $sqlParts;
    }
    
    /**
    * Modifies the SQL parts to implement all of the sorting related options.
    * Sorting is currently only supported for CApiService::get() methods.
    *
    * @param string $tableName
    * @param string $tableAlias
    * @param array  $options
    * @param array  $sqlParts
    *
    * @return array
    */
   public static function applyQuerySortOptions($tableName, $tableAlias, array $options, array $sqlParts) {
	if (isset($options['sortfield']) && !bzhy_empty($options['sortfield'])) {
            $options['sortfield'] = is_array($options['sortfield'])
		? array_unique($options['sortfield'])
		: [$options['sortfield']];

            foreach ($options['sortfield'] as $i => $sortfield) {			
                // add sort field to order
		$sortorder = '';
		if (is_array($options['sortorder'])) {
                    if (!empty($options['sortorder'][$i])) {
			$sortorder = ($options['sortorder'][$i] == BZHY_SORT_DOWN) ? ' '.BZHY_SORT_DOWN : '';
                    }
		}
		else {
                    $sortorder = ($options['sortorder'] == BZHY_SORT_DOWN) ? ' '.BZHY_SORT_DOWN : '';
		}

		$sqlParts = self::applyQuerySortField($sortfield, $sortorder, $tableAlias, $sqlParts);
            }
	}

	return $sqlParts;
    }
        
    /**
    * Adds a specific property from the 'sortfield' parameter to the $sqlParts array.
    *
    * @param string $sortfield
    * @param string $sortorder
    * @param string $alias
    * @param array  $sqlParts
    *
    * @return array
    */
   public static function applyQuerySortField($sortfield, $sortorder, $alias, array $sqlParts) {
        // add sort field to select if distinct is used
        if (count($sqlParts['from']) > 1
            && !str_in_array((bzhy_empty($alias)?$sortfield:$alias.'.'.$sortfield), $sqlParts['select'])
            && !str_in_array((bzhy_empty($alias)?'*':$alias.'.*'), $sqlParts['select'])) {

            $sqlParts['select'][$sortfield] = bzhy_empty($alias)?$sortfield:$alias.'.'.$sortfield;
	}
        
        if(bzhy_empty($alias)){
            $sqlParts['order'][$sortfield] = $sortfield.$sortorder;
        }
        else{
            $sqlParts['order'][$alias.'.'.$sortfield] = $alias.'.'.$sortfield.$sortorder;
        }
	return $sqlParts;
    }
    
    /**
    * Creates a SELECT SQL query from the given SQL parts array.
    *
    * @param array $sqlParts	An SQL parts array
    *
    * @return string			The resulting SQL query
    */
   public static function createSelectQueryFromParts(array $sqlParts) {
        // build query
	$sqlSelect = implode(',', array_unique($sqlParts['select']));
	$sqlFrom = implode(',', array_unique($sqlParts['from']));

	$sql_left_join = '';
	if (array_key_exists('left_join', $sqlParts)) {
            foreach ($sqlParts['left_join'] as $join) {
                $sql_left_join .= ' LEFT JOIN '.$join['from'].' ON '.$join['on'];
            }
	}

	$sqlWhere = empty($sqlParts['where']) ? '' : ' WHERE '.implode(' AND ', array_unique($sqlParts['where']));
	$sqlGroup = empty($sqlParts['group']) ? '' : ' GROUP BY '.implode(',', array_unique($sqlParts['group']));
	$sqlOrder = empty($sqlParts['order']) ? '' : ' ORDER BY '.implode(',', array_unique($sqlParts['order']));

	return 'SELECT '.bzhy_db_distinct($sqlParts).' '.$sqlSelect.
            ' FROM '.$sqlFrom.
            $sql_left_join.
            $sqlWhere.
            $sqlGroup.
            $sqlOrder;
    }
    
   /*
    * Get a field ID string(added alias of a table) for SQL query by $object
    * @param $object string object name
    * @param $field string field name 
    * @return If $object or $field is empty ,then return null
    *        else return alias.$field string
    */
    public static function getFieldIdByObject($object,$field){
        if(bzhy_empty($object) || bzhy_empty($field)){
            return null;
        }
        
        $Alias = bzhyCBase::getTableAliasByObject($object);
        if(bzhy_empty($Alias)){
            return $field;
        }
        else{
            return $Alias.".".$field;
        }   
    }
    
    /*
    * Get a field ID string(added alias of a table) for SQL query by $table 
    * @param $table string table name
    * @param $field string field name 
    * @return If $table or $field is empty ,then return null
    *        else return alias.$field string
    */
    public static function getFieldIdByTable($table,$field){
        if(bzhy_empty($table) || bzhy_empty($field)){
            return null;
        }
        
        $Alias = bzhyCBase::getTableAliasByTable($table);
        if(bzhy_empty($Alias)){
            return $field;
        }
        else{
            return $Alias.".".$field;
        }   
    }
    
    /*
     * Get the PRIMARY KEY Field name for object
     *  @param $object string object name
     * @return return NULL if object or table name for object is empty
     *    or a string
     */
    public static function getPkByObject($object){
        if(bzhy_empty($object)){
            return null;
        }
        
        if(isset(self::$objectFields[$object]) && !bzhy_empty(self::$objectFields[$object])){
            return self::$objectFields[$object]['id'];
        }
        
        $Fields = self::getTableFieldsByObject($object);
        
        return bzhy_empty($Fields)?NULL:$Fields['id'];       
    }
    
    /*
     * Get the PRIMARY KEY Field name for table
     *  @param $tableName string table name
     * @return return NULL if table name for object is empty
     *    or a string
     */
    public static function getPkByTable($tableName){
        if(bzhy_empty($tableName)){
            return null;
        }
        
        if(isset(self::$tableFields[$tableName]) && !bzhy_empty(self::$tableFields[$tableName])){
            return self::$tableFields[$tableName]['id'];
        }
        
        $Fields = self::getTableFields($tableName);
        
        return bzhy_empty($Fields)?NULL:$Fields['id'];  
    }
    
    /*
     * Get the table fields of a object. 
     * @param $object string object name
     * @return return NULL if object or table name for object is empty
     *          Or a array 
     */
    public static function getTableFieldsByObject($object){
        if(bzhy_empty($object)){
            return null;
        }
        
        if(isset(self::$objectFields[$object]) && !bzhy_empty(self::$objectFields[$object])){
            return self::$objectFields[$object];
        }
        
        $tableName = bzhyCBase::getObjectTable($object);
        if(bzhy_empty($tableName)){
            return NULL;
        }
        
        $Fields = self::getTableFields($tableName);
        self::$objectFields[$object] = $Fields;
        
        return $Fields;
    }
    
    /*
     * Get from SQL for object by object 
     * @param $object string object name
     * @return return NULL if object or table name for object is empty
     *     or return tablename alias
     */
    public static function getFromByObject($object){
        if(bzhy_empty($object)){
            return null;
        }
        
        $tableName = bzhyCBase::getObjectTable($object);
        $Alias = bzhyCBase::getTableAliasByObject($object);
        return bzhy_empty($Alias)?$tableName:$tableName." ".$Alias;
    }
    
    /*
     * Get from SQL for a table by table 
     * @param $table string table name
     * @return return NULL if table name for table is empty
     *     or return tablename alias
     */
    public static function getFromByTable($table){
        if(bzhy_empty($table)){
            return null;
        }
        
        $Alias = bzhyCBase::getTableAliasByTable($table);
        return bzhy_empty($Alias)?$table:$table." ".$Alias;
    }
    
    public static function dbSearch($table, $options, &$sql_parts) {
	list($table, $tableShort) = explode(' ', $table);
        
        $Fields =  self::getFields($table);
	$start = is_null($options['startSearch']) ? '%' : '';
	$exclude = is_null($options['excludeSearch']) ? '' : ' NOT ';
	$glue = (!$options['searchByAny']) ? ' AND ' : ' OR ';

	$search = [];
	foreach ($options['search'] as $field => $patterns) {
            if (!isset($Fields[$field]) || zbx_empty($patterns)) {
                continue;
            }
            if ($Fields[$field]['type'] != DATABASE_FIELD_TYPE_STRING) {
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
                    zbx_dbstr($start.mb_strtoupper($pattern).'%').
                    " ESCAPE '!'";
            }
            else {
		$pattern = str_replace("*", "%", $pattern);
		$fieldSearch[] =
                    ' UPPER('.$tableShort.'.'.$field.') '.
                    $exclude.' LIKE '.
                    zbx_dbstr(mb_strtoupper($pattern)).
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
    * Fetches the fields given in $fields from the database and extends the objects with the loaded data.
    *
    * @param string $tableName
    * @param array  $objects
    * @param array  $fields
    *
    * @return array
    */
   public function extendObjects($tableName, array $objects, array $fields) {
	if ($objects) {
            $dbObjects = self::select($tableName, [
		'output' => $fields,
                self::getPkByTable($tableName).'s' => bzhy_objectValues($objects,self::getPkByTable($tableName)),
		'preservekeys' => true
            ]);

            foreach ($objects as &$object) {
		$pk = $object[self::getPkByTable($tableName)];
		if (isset($dbObjects[$pk])) {
                    check_db_fields($dbObjects[$pk], $object);
		}
            }
            unset($object);
	}

	return $objects;
    }
   

}
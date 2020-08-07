<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * Class containing methods for operations with users media.
 */
class bzhyCUserMedia {
    protected static $objectName ='usermedia';
    
    protected $tableName = 'media';
    protected $tableAlias = 'm';
    protected $sortColumns = ['mediaid', 'userid', 'mediatypeid'];

    /**
    * Get users data.
    *
    * @param array  $options
    * @param array  $options['usrgrpids']	filter by UserGroup IDs
    * @param array  $options['userids']	filter by User IDs
    * @param bool   $options['type']		filter by User type [USER_TYPE_ZABBIX_USER: 1, USER_TYPE_ZABBIX_ADMIN: 2, USER_TYPE_SUPER_ADMIN: 3]
    * @param bool   $options['getAccess']	extend with access data for each User
    * @param bool   $options['count']		output only count of objects in result. (result returned in property 'rowscount')
    * @param string $options['pattern']	filter by Host name containing only give pattern
    * @param int    $options['limit']		output will be limited to given number
    * @param string $options['sortfield']	output will be sorted by given property ['userid', 'alias']
    * @param string $options['sortorder']	output will be sorted in given order ['ASC', 'DESC']
    *
    * @return array
    */
    public function get($options = []) {
	$result = [];
	$sqlParts = [
            'select'=> [bzhyCBase::getObjectTable(self::$objectName) => bzhyDB::getFieldIdByObject(self::$objectName, 'mediaid')],
            'from'  => [bzhyCBase::getObjectTable(self::$objectName) => bzhyDB::getFromByObject(self::$objectName)],
            'where'		=> [],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];

        $defOptions = [
            'usrgrpids'					=> null,
            'userids'					=> null,
            'mediaids'					=> null,
            'mediatypeids'				=> null,
            // filter
            'filter'					=> null,
            'search'					=> null,
            'searchByAny'				=> null,
            'startSearch'				=> null,
            'excludeSearch'				=> null,
            'searchWildcardsEnabled'	=> null,
            // output
            'output'					=> BZHYAPI_OUTPUT_EXTEND,
            'editable'					=> null,
            'countOutput'				=> null,
            'groupCount'				=> null,
            'preservekeys'				=> null,
            'sortfield'					=> '',
            'sortorder'					=> '',
            'limit'						=> null
	];
	$options = bzhy_array_merge($defOptions, $options);
		
	// mediaids
	if ($options['mediaids'] !== null) {
            bzhy_value2array($options['mediaids']);
            $sqlParts['where'][] = bzhydbConditionInt(bzhyDB::getFieldIdByObject(self::$objectName, 'mediaid'), $options['mediaids']);
	}
	// userids
	if ($options['userids'] !== null) {
            bzhy_value2array($options['userids']);
            $sqlParts['from']['users'] = bzhyDB::getFromByObject('user');
            $sqlParts['where'][] = bzhydbConditionInt(bzhyDB::getFieldIdByObject('user','userid'), $options['userids']);
            $sqlParts['where']['mu'] = bzhyDB::getFieldIdByObject(self::$objectName,'userid').'='.bzhyDB::getFieldIdByObject('user','userid');
            if ($options['groupCount'] !== null) {
                $sqlParts['group']['userid'] = bzhyDB::getFieldIdByObject('user','userid');
            }
	}
	// usrgrpids
	if ($options['usrgrpids'] !== null) {
            bzhy_value2array($options['usrgrpids']);
            $sqlParts['from']['users_groups'] = 'users_groups ug';
            $sqlParts['where'][] = bzhydbConditionInt('ug.usrgrpid', $options['usrgrpids']);
            $sqlParts['where']['mug'] = bzhyDB::getFieldIdByObject(self::$objectName,'userid').'=ug.userid';
            if ($options['groupCount'] !== null) {
		$sqlParts['group']['usrgrpid'] = 'ug.usrgrpid';
            }
	}
	// mediatypeids
	if ($options['mediatypeids'] !== null) {
            bzhy_value2array($options['mediatypeids']);
            $sqlParts['where'][] = bzhydbConditionInt(bzhyDB::getFieldIdByObject(self::$objectName,'mediatypeid'), $options['mediatypeids']);
            if ($options['groupCount'] !== null) {
		$sqlParts['group']['mediatypeid'] = bzhyDB::getFieldIdByObject(self::$objectName,'mediatypeid');
            }
	}
	// filter
	if (is_array($options['filter'])) {
            bzhyDB::dbFilter(bzhyDB::getFromByObject(self::$objectName), $options, $sqlParts);
	}
	// search
	if (is_array($options['search'])) {
            bzhy_db_search(bzhyDB::getFromByObject(self::$objectName), $options, $sqlParts);
	}
	// limit
	if (bzhy_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        $sqlParts = bzhyDB::applyQueryOutputOptions(bzhyCBase::getObjectTable(self::$objectName), bzhyCBase::getTableAliasByObject(self::$objectName), $options, $sqlParts);
	$sqlParts = bzhyDB::applyQuerySortOptions(bzhyCBase::getObjectTable(self::$objectName), bzhyCBase::getTableAliasByObject(self::$objectName), $options, $sqlParts);
	$res = DBselect(bzhyDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
	while ($media = DBfetch($res)) {
            if ($options['countOutput'] !== null) {
		if ($options['groupCount'] !== null) {
                    $result[] = $media;
		}
		else {
                    $result = $media['rowscount'];
		}
            }
            else {
		$result[$media['mediaid']] = $media;
            }
	}
	if ($options['countOutput'] !== null) {
            return $result;
	}
        // removing keys
	if ($options['preservekeys'] === null) {
            $result = bzhy_cleanHashes($result);
	}
	return $result;
    }
}

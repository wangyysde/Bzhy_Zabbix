<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

//Refer: include/api/CApiService.php

class bzhyObjectCommon {
    
    protected $tableName = '';
    
    protected $tableAlias = '';
    
    protected $pkField = '';
    
    public  $data = null;
        
    public function __construct(){
        if(!bzhy_empty($this->ObjectName)){
            $this->tableName = bzhyCBase::getTableByObject($this->ObjectName);
            $this->tableAlias = bzhyCBase::getTableAliasByObject($this->ObjectName);
            $this->pkField = bzhyCDB::getPkByObject($this->ObjectName);
        }
    }
    
    protected function getRelationObjects($objectId=null, $objectField=null, $table,array $options){
        $result = null; 
        $sqlParts['from'] = [$table];
        if (!is_null($options['countOutput'])) {
            if (!is_null($options['groupCount'])) {
                $sqlParts['group'] = ['object_table'];
                $sqlParts['select'] = [$this->pkField,$objectField,'object_table','object_field','object_value','count('.$this->pkField.') as objectcount'];
            }
            else{
                $sqlParts['select'] = ['COUNT(object_table) AS rowscount'];
            }
        }
        if(!isset( $sqlParts['select'])){
            $sqlParts['select'] = [$this->pkField,$objectField,'object_table','object_field','object_value'];
        }
        $sqlParts['where'][] =  dbConditionInt($objectField, $objectId);
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts));
        if(!is_null($options['countOutput']) && is_null($options['groupCount'])){
            $object = DBfetch($res);
            $result['objectcounts'] = $object['rowscount'];
        }
        else{
            $null_output =  FALSE;
            while ($object = DBfetch($res)){
                if(!is_null(($object_table=$object['object_table']))){
                    $object_type = bzhyCBase::getObjectTitleByTable($object_table);
                    $object_type_field = bzhyCBase::getObjectTitleFieldByTable($object_table);
                    if(!isset($options['output']) || is_null($options['output']) || $null_output){
                        $options['output'] = [$object_type_field];
                        $null_output = TRUE;
                    }
                    $object_type_pk= bzhyCDB::getPkByTable($object_table);
                    $object_type_value = $object['object_value'];
                    if(is_null(trim($object_type))){
                        bzhyCBase::exception(ZBX_API_ERROR_INTERNAL, _('Can not found object type!'),__FILE__,__LINE__,TRUE);
                    }
                }
                else{
                    bzhyCBase::exception(ZBX_API_ERROR_INTERNAL, _('Can not found objects!'),__FILE__,__LINE__,TRUE);
                }
                
                $objectInstance = bzhyCBase::getInstanceByTable($object_table);
                if(!bzhy_empty($objectInstance) && count(($ObjectInfos= $objectInstance->get(['ids'=>$object_type_value,"output"=>[implode(',',$options['output'])]])))>0){
                    if($options['output'] === [$object_type_field] ){
                        foreach($ObjectInfos as $ObjectInfo){
                            $result[$object_table]['object_info'][$object_type_value]['object_pk_value'] = $object_type_value;
                            $result[$object_table]['object_info'][$object_type_value]['object_item_name'] = $ObjectInfo[$object_type_field];
                        }
                    }
                    else{
                        $result[$object_table]['object_info'] = $ObjectInfos;
                    }
                }
                $result[$object_table]['object_type'] = $object_type;
                $result[$object_table]['object_field'] = $object_type_field;
                $result[$object_table]['object_pk_name'] = $object_type_pk;

                if(!is_null($options['countOutput']) && !is_null($options['groupCount'])){
                    $result[$object_table]['object_group_count'] = $object['objectcount'];
                }
            }
        }
        return $result; 
    }
    
    /**
    * Creates a relation map for the given objects.
    *
    * If the $table parameter is set, the relations will be loaded from a database table, otherwise the map will be
    * built from two base object properties.
    *
    * @param array  $objects			a hash of base objects
    * @param string $baseField			the base object ID field
    * @param string $foreignField		the related objects ID field
    * @param string $table				table to load the relation from
    *
    * @return CRelationMap
    */
    protected function createRelationMap(array $objects, $baseField, $foreignField, $table = null) {
	$relationMap = bzhyCBase::getInstanceByObject('relationmap', []);
	// create the map from a database table
	if ($table) {
            $res = DBselect(bzhyCDB::createSelectQuery($table, [
                'output' => [$baseField, $foreignField],
                'filter' => [$baseField => array_keys($objects)]
            ]));
            while ($relation = DBfetch($res)) {
		$relationMap->addRelation($relation[$baseField], $relation[$foreignField]);
            }
	}
	// create a map from the base objects
	else {
        foreach ($objects as $object) {
            $relationMap->addRelation($object[$baseField], $object[$foreignField]);
        }
	}

	return $relationMap;
    }
    
    /**
    * Checks if an object contains any of the given parameters.
    *
    * Example:
    * checkNoParameters($item, array('templateid', 'state'), _('Cannot set "%1$s" for item "%2$s".'), $item['name']);
    * If any of the parameters 'templateid' or 'state' are present in the object, it will be placed in "%1$s"
    * and $item['name'] will be placed in "%2$s".
    *
    * @throws APIException			if any of the parameters are present in the object
    *
    * @param array  $object
    * @param array  $params		array of parameters to check
    * @param string $error
    * @param string $objectName
    */
    protected function checkNoParameters(array $object, array $params, $error, $objectName) {
	foreach ($params as $param) {
            if (array_key_exists($param, $object)) {
		$error = _params($error, [$param, $objectName]);
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, $error,__FILE__,__LINE__,TRUE);
            }
	}
    }
    
    /**
    * Checks that each object has a valid ID.
    *
    * @param array $objects
    * @param $idField			name of the field that contains the id
    * @param $messageRequired	error message if no ID is given
    * @param $messageEmpty		error message if the ID is empty
    * @param $messageInvalid	error message if the ID is invalid
    */
    protected function checkObjectIds(array $objects, $idField, $messageRequired, $messageEmpty, $messageInvalid) {
        $idValidator = new CIdValidator([
            'messageEmpty' => $messageEmpty,
            'messageInvalid' => $messageInvalid
	]);
	foreach ($objects as $object) {
            if (!isset($object[$idField])) {
		bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _params($messageRequired, [$idField]),__FILE__,__LINE__,TRUE);
            }
            $this->checkValidator($object[$idField], $idValidator);
	}
    }
    
    /**
    * Runs the given validator and throws an exception if it fails.
    *
    * @param $value
    * @param CValidator $validator
    */
    protected function checkValidator($value, CValidator $validator) {
        if (!$validator->validate($value)) {
            bzhyCBase::exception(ZBX_API_ERROR_INTERNAL, $validator->getError(),__FILE__,__LINE__,TRUE);
	}
    }
    
    /**
    * Runs the given partial validator and throws an exception if it fails.
    *
    * @param array $array
    * @param CPartialValidatorInterface $validator
    * @param array $fullArray
    */
    protected function checkPartialValidator(array $array, CPartialValidatorInterface $validator, $fullArray = []) {
	if (!$validator->validatePartial($array, $fullArray)) {
            bzhyCBase::exception(ZBX_API_ERROR_INTERNAL, $validator->getError(),__FILE__,__LINE__,TRUE);
	}
    }
    
    /**
    * Adds the given fields to the "output" option if it's not already present.
    *
    * @param string $output
    * @param array $fields        either a single field name, or an array of fields
    *
    * @return mixed
    */
    protected function outputExtend($output, array $fields) {
        if ($output === null) {
            return $fields;
	}
	// if output is set to extend, it already contains that field; return it as is
	elseif ($output === API_OUTPUT_EXTEND) {
            return $output;
	}
	// if output is an array, add the additional fields
	return array_keys(array_flip(array_merge($output, $fields)));
    }
}

<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */



class bzhyCFiles extends bzhyObjectCommon{

    protected $relatedTableName = 'fileobjectrelation';
        
    protected $sortColumns = ['file_title','file_uploadtime','file_userid','file_status'];
    
    public  $data = null;
        
    protected $ObjectName='file';

    /**
    * Tries to get system settings value 
    * 
    * @param string $settingtype   Type of settins. 0 for all settings
    * @param bool   $buildvars     Whether build and modify(or create) ../../conf/system_settings.ini.php file
    * 
    * @throws Exception if any errors ocured
    * 
    * @return Array                An Array of result 
    */
        
    public function get($options = []) {
        $result = [];
        $sqlParts = [
            'select'	=> [$this->tableName => bzhyCDB::getFieldIdByObject($this->ObjectName,'id')],
            'from'	=> [$this->tableName => bzhyCDB::getFromByObject($this->ObjectName)],
            'group'	=> [],
            'order'	=> [],
            'limit'	=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'object_ids'                               => NULL, 
            'status'                                   => NULL,
            'object_table'                              => NULL,  
            'sortfield'                                => '',
            'sortorder'				       => '',
            'limit'                                    => null,
            'limitSelects'			       => null,
            'selectCreateUser'                         => null, 
            'selectLastUser'                           => null, 
            'selectDelUser'                            => null,
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'selectObject'                             => null,
            'groupCount'			       => null
            
        ];
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where'][bzhyCBase::getTableByObject($this->ObjectName)] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'id'), $options['ids']);
	}
        if(!is_null($options['status'])){
            zbx_value2array($options['status']);
            $sqlParts['where']['file_status'] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'file_status'), $options['status']);
        }
        if (!is_null($options['object_ids']) && !is_null($options['object_table'])) {
            $sqlParts['from']['fileobjectrelation'] = 'fileobjectrelation fo';
            zbx_value2array($options['object_ids']);
            $sqlParts['where'][] = dbConditionInt('fo.object_value', $options['object_ids']);
            $sqlParts['where']['object_file'] = 'fo.file_id = '.bzhyCDB::getFieldIdByObject($this->ObjectName,'id');
            $sqlParts['where']['object_table'] = 'fo.object_table =\''.$options['object_table'].'\'';
	}
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
	$sqlParts = bzhyCDB::applyQuerySortOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($file = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $file;
		}
		else {
                    $result = $file['rowscount'];
		}
            }
            else {
		$result[$file['id']] = $file;
            }
        }
        if (!is_null($options['countOutput'])) {
		return $result;
	}
        if ($result) {
            $result = $this->addRelatedObjects($options, $result);
	}
        return $result;
	
    }
    
    protected function addRelatedObjects(array $options, array $result){
        $fileids = array_keys($result);
        //Add Create user related 
        if ($options['selectCreateUser'] !== null) {
            foreach ($result as $fileid => $file){
                if(isset($file['file_userid']) && trim($file['file_userid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$file['file_userid'],"output"=>$options['selectCreateUser']])))){ 
                        $result[$fileid]['CreateUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add modified user related 
        if ($options['selectLastUser'] !== null) {
            foreach ($result as $fileid => $file){
                if(isset($file['file_lastuserid']) && trim($file['file_lastuserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$file['file_lastuserid'],"output"=>$options['selectLastUser']])))){ 
                        $result[$fileid]['LastUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add deleted user related 
        if ($options['selectDelUser'] !== null) {
            foreach ($result as $fileid => $file){
                if(isset($file['file_deluserid']) && trim($file['file_deluserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$file['file_deluserid'],"output"=>$options['selectDelUser']])))){ 
                        $result[$fileid]['DelUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        if ($options['selectObject'] !== null) {
            foreach ($result as $fileid => $file){
                $objects=$this->getRelationObjects(zbx_toArray($fileid), 'file_id','fileobjectrelation',$options['selectObject']);
                $result[$fileid]['relatedObjects'] = $objects; 
            }
        }
        
        return($result);
    }

    public function update(){
        global $ZBX_MESSAGES;
        if(is_null($this->data)){
            return FALSE;
        }
       if(!isset($this->data['action'])){
           return FALSE;
       }
       switch (strtolower($this->data['action'])){
            case"add.posted":
               try{
                    $fileid = bzhyCDB::insert($this->tableName, [$this->data['dbdata']]);
                    return TRUE;
               } catch (Exception $ex) {
                   $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                   return FALSE;
               }
               break;
            case"modify.posted":
                try{
                    if(!isset($this->data['fileid'])){
                         $ZBX_MESSAGES[] = ['type'=>'error','message'=>'File ID not found!'];
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['fileid'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                    return FALSE;
                }
                break;
            case"disable.posted":
                try{
                    if(!isset($this->data['fileid'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['fileid'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
            case"enable.posted":
                try{
                    if(!isset($this->data['fileid'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['fileid'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
            case"del.posted":
                try{
                    if(!isset($this->data['fileid'])){
                         return FALSE;
                    }
                    $updatedb['where']['id'] = $this->data['fileid'];
                    $result = bzhyCDB::delete($this->tableName, $updatedb['where']);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
           default :
               return FALSE;
       }
    }
    
    public function insertRelatedObjects(){
        
        if(zbx_empty($this->data['FileIds'])) return FALSE;
        $RelatedTable =$this->data['RelatedTable'];
        $RelatedField = $this->data['RelatedField'];
        $RelatedValue = $this->data['RelatedValue'];
        if(zbx_empty($RelatedTable) || zbx_empty($RelatedField) || zbx_empty($RelatedValue)) return FALSE;
        $values = [];
        foreach ($this->data['FileIds'] as $key =>$id){
            $row['file_id'] = $id;
            $row['object_table'] = $RelatedTable;
            $row['object_field'] = $RelatedField;
            $row['object_value'] = $RelatedValue;
            $values[] = $row;
        }
        try{
            $relatedId = bzhyCDB::insert($this->relatedTableName, $values);
            return TRUE;
        } catch (Exception $ex) {
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
            return FALSE;
        }
    }
    
    public function deleteRelatedObjects(){
  
        $RelatedTable =$this->data['RelatedTable'];
        $RelatedField = $this->data['RelatedField'];
        $RelatedValue = $this->data['RelatedValue'];
        if(zbx_empty($RelatedTable) || zbx_empty($RelatedField) || zbx_empty($RelatedValue)) return FALSE;
        $updatedb['where']['object_table'] = $RelatedTable;
        $updatedb['where']['object_field'] = $RelatedField;
        $updatedb['where']['object_value'] = $RelatedValue;
        try{
            $relatedId = bzhyCDB::delete($this->relatedTableName, $updatedb['where']);
            return TRUE;
        } catch (Exception $ex) {
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
            return FALSE;
        }
    }
    
}


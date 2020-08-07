<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

class bzhyCContact {

    protected $tableName = 'contact';
    protected $tableAlias = 'c';
    protected $pkField = 'id';
    protected $sortColumns = ['contact_name','contact_company','contact_status'];
    
    protected $relatedTableName = 'contactobjectrelation';
    protected $objectName = 'contact';
    
    public  $data = null;
        
        
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
            'select'	=> [bzhyCBase::getObjectTable($this->objectName) => bzhyDB::getFieldIdByObject($this->objectName, 'id')],
            'from'		=> [bzhyCBase::getObjectTable($this->objectName) => bzhyDB::getFromByObject($this->objectName)],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'object_ids'                               => NULL, 
            'status'                                   => NULL,
            'object_table'                              => NULL,  
            'sortfield'                                => NULL,
            'sortorder'				       => NULL,
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
            $sqlParts['where']['contact'] = bzhydbConditionInt(bzhyDB::getFieldIdByObject($this->objectName,'id'), $options['ids']);
	}
        if(!is_null($options['status'])){
            zbx_value2array($options['status']);
            $sqlParts['where']['contact_status'] = bzhydbConditionInt( bzhyDB::getFieldIdByObject($this->objectName,'contact_status'), $options['status']);
        }
        if (!is_null($options['object_ids']) && !is_null($options['object_table'])) {
            $sqlParts['from']['contactobjectrelation'] = 'contactobjectrelation co';
            zbx_value2array($options['object_ids']);
            $sqlParts['where'][] = bzhydbConditionInt('co.object_value', $options['object_ids']);
            $sqlParts['where']['object_contact'] = 'co.contact_id = '.bzhyDB::getFieldIdByObject($this->objectName,'id');
            $sqlParts['where']['object_table'] = 'co.object_table =\''.$options['object_table'].'\'';
	}
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        $sqlParts = bzhyDB::applyQueryOutputOptions(bzhyCBase::getObjectTable($this->objectName), bzhyCBase::getTableAliasByObject($this->objectName), $options, $sqlParts);
	$sqlParts = bzhyDB::applyQuerySortOptions(bzhyCBase::getObjectTable($this->objectName), bzhyCBase::getTableAliasByObject($this->objectName), $options, $sqlParts);
        $res = DBselect(bzhyDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($contact = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $contact;
		}
		else {
                    $result = $contact['rowscount'];
		}
            }
            else {
		$result[$contact['id']] = $contact;
            }
        }
        if (!is_null($options['countOutput'])) {
		return $result;
	}
        if ($result) {
            $result = $this->addRelatedObjects($options, $result);
	}
        return $result;
	//return $this->createSelectQueryFromParts($sqlParts);
        //$res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
    }
    
    protected function addRelatedObjects(array $options, array $result){
        $fileids = array_keys($result);
        //Add Create user related 
        if ($options['selectCreateUser'] !== null) {
            foreach ($result as $contactid => $contact){
                if(isset($contact['contact_userid']) && trim($contact['contact_userid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$contact['contact_userid'],"output"=>$options['selectCreateUser']])))){ 
                        $result[$contactid]['CreateUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add modified user related 
        if ($options['selectLastUser'] !== null) {
            foreach ($result as $contactid => $contact){
                if(isset($contact['contact_lastuserid']) && trim($contact['contact_lastuserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$contact['contact_lastuserid'],"output"=>$options['selectLastUser']])))){ 
                        $result[$contactid]['LastUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add deleted user related 
        if ($options['selectDelUser'] !== null) {
            foreach ($result as $contactid => $contact){
                if(isset($contact['contact_deluserid']) && trim($contact['contact_deluserid']) != NULL){
                    //if(!is_null(($UserInfo=getUserInfobyUserid($idcroom['room_deluserid'],$options['selectDelUser'])))){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$contact['contact_deluserid'],"output"=>$options['selectDelUser']])))){ 
                        $result[$contactid]['DelUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        if ($options['selectObject'] !== null) {
            foreach ($result as $contactid => $contact){
                $objects=$this->getRelationObjects(zbx_toArray($contactid), 'contact_id','contactobjectrelation',$options['selectObject']);
                $result[$contactid]['relatedObjects'] = $objects; 
            }
        }
        return($result);
    }

    public function update(){
        global $ZBX_MESSAGES;
        if(is_null($this->data)){
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>'Internal error!'];
            return FALSE;
        }
       if(!isset($this->data['action'])){
           $ZBX_MESSAGES[] = ['type'=>'error','message'=>'Internal error(No action)!'];
           return FALSE;
       }
       switch (strtolower($this->data['action'])){
            case"add.posted":
               try{
                    $contactId = DB::insert($this->tableName, [$this->data['dbdata']]);
                    $relatedFile['FileIds'] = $this->data['files'];
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $contactId[0];
                    if(zbx_empty($relatedFile['FileIds'])){
                        return TRUE;
                    }
                    $cFile = new CAttachment();
                    $cFile->data = $relatedFile;
                    if($cFile->insertRelatedObjects()){
                        return TRUE;
                    }
                    else{
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Add related files error!')];
                        return FALSE;
                    }
               } catch (Exception $ex) {
                   $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                   return FALSE;
               }
               break;
            case"modify.posted":
                try{
                    if(!isset($this->data['id'])){
                         $ZBX_MESSAGES[] = ['type'=>'error','message'=>'Contact ID not found!'];
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = DB::update($this->tableName, $updatedb);
                    $relatedFile['FileIds'] = $this->data['files'];
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $this->data['id'];
                    $cFile = new CAttachment();
                    $cFile->data = $relatedFile;
                    if(!$cFile->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                        return FALSE;
                    }
                    if(zbx_empty($relatedFile['FileIds'])){
                        return TRUE;
                    }
                    if($cFile->insertRelatedObjects()){
                        return TRUE;
                    }
                    else{
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                        return FALSE;
                    }                    
                    return TRUE;
                } catch (Exception $ex) {
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                    return FALSE;
                }
                break;
            case"disable.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = DB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
            case"enable.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = DB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
            case"del.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = DB::delete($this->tableName, $updatedb['where']);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
           default :
               $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Occured UNKNOWN error!')];
               return FALSE;
       }
    }
    
    public function getLabels($fields = [],$withOutFields = array()){
        return NewDB::getFieldsLabel($this->tableName, $fields,$withOutFields);
    }
    
    public function insertRelatedObjects(){
        
        if(zbx_empty($this->data['ContactIds'])) return FALSE;
        $RelatedTable =$this->data['RelatedTable'];
        $RelatedField = $this->data['RelatedField'];
        $RelatedValue = $this->data['RelatedValue'];
        if(zbx_empty($RelatedTable) || zbx_empty($RelatedField) || zbx_empty($RelatedValue)) return FALSE;
        $values = [];
        foreach ($this->data['ContactIds'] as $key =>$id){
            $row['contact_id'] = $id;
            $row['object_table'] = $RelatedTable;
            $row['object_field'] = $RelatedField;
            $row['object_value'] = $RelatedValue;
            $values[] = $row;
        }
        try{
            $relatedId = bzhyDB::insert($this->relatedTableName, $values);
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
            $relatedId = DB::delete($this->relatedTableName, $updatedb['where']);
            return TRUE;
        } catch (Exception $ex) {
            $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
            return FALSE;
        }
    }
    
   }


<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */
class bzhyCIdcbox extends bzhyObjectCommon  {
    
    protected $ObjectName='idc_box';
     
    protected $sortColumns = ['box_no','box_status','box_starttime','box_endtime'];


    public  $data = null;
    
    public function get($options = []) {
        $result = [];
        $sqlParts = [
            'select'	=> [$this->tableName => bzhyCDB::getFieldIdByObject($this->ObjectName,'id')],
            'from'	=> [$this->tableName => bzhyCDB::getFromByObject($this->ObjectName)],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        $defOptions = [
            'ids'                                      => NULL,
            'idcroom_ids'                               => NULL, 
            'sortfield'                                => '',
            'sortorder'				       => '',
            'limit'                                    => null,
            'limitSelects'			       => null,
            'selectCreateUser'                         => null, 
            'selectLastUser'                           => null, 
            'selectDelUser'                            => null,
            'selectFile'                               => null,
            'selectContact'                            => null, 
            'selectIdcRoom'                            => null,
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null
            
        ];
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['idc_boxid'] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'id'), $options['ids']);
	}
        if (!is_null($options['idcroom_ids'])) {
            zbx_value2array($options['idcroom_ids']);
            $sqlParts['where']['idcroom_ids'] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'room_id'), $options['idcroom_ids']);
	}
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
	$sqlParts = bzhyCDB::applyQuerySortOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($IdcBox = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $IdcBox;
		}
		else {
                    $result = $IdcBox['rowscount'];
		}
            }
            else {
		$result[$IdcBox['id']] = $IdcBox;
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
        $IdcBoxids = array_keys($result);
        if ($options['selectCreateUser'] !== null) {
            foreach ($result as $IdcBoxId => $IdcBox){
                if(isset($IdcBox['box_userid']) && trim($IdcBox['box_userid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$IdcBox['box_userid'],"output"=>$options['selectCreateUser']])))){ 
                        $result[$IdcBoxId]['CreateUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add modified user related 
        if ($options['selectLastUser'] !== null) {
            foreach ($result as $IdcBoxId => $IdcBox){
                if(isset($IdcBox['box_lastuserid']) && trim($IdcBox['box_lastuserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$IdcBox['box_lastuserid'],"output"=>$options['selectLastUser']])))){ 
                        $result[$IdcBoxId]['LastUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        //get files
        if($options['selectFile'] !== null) {
            foreach ($result as $IdcBoxId => $IdcBox){
                $FileOptions['output'] = $options['selectFile'];
                $FileOptions['object_ids'] = $IdcBoxId;
                $FileOptions['object_table'] = $this->tableName;
                $cFile =bzhyCBase::getInstanceByObject('file', []);
                $selectedfile = $cFile->get($FileOptions);
                $result[$IdcBoxId]['SelectFile'] = $selectedfile;
            }
        }
        
        if($options['selectContact'] !== null) {
            foreach ($result as $IdcBoxId => $IdcBox){
                $ContactOptions['output'] = $options['selectContact'];
                $ContactOptions['object_ids'] = $IdcBoxId;
                $ContactOptions['object_table'] = $this->tableName;
                $cContact = bzhyCBase::getInstanceByObject('contact', []);
                $selectContact = $cContact->get($ContactOptions);
                $result[$IdcBoxId]['selectContact'] = $selectContact;
            }
        }
        
        //Add deleted user related 
        if ($options['selectDelUser'] !== null) {
            foreach ($result as $IdcBoxId => $IdcBox){
                if(isset($IdcBox['box_deluserid']) && trim($IdcBox['box_deluserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$IdcBox['box_deluserid'],"output"=>$options['selectDelUser']])))){ 
                        $result[$IdcBoxId]['DelUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        //Get IDC BOX information by idcroom id 
        if ($options['selectIdcRoom'] !== null) {
            $idcroomInstance = bzhyCBase::getInstanceByObject('idc_room', []);
            foreach ($result as $IdcBoxId => $IdcBox){
                if(!bzhy_empty($idcroomInstance) && !is_null(($IdcRoomInfo= $idcroomInstance->get(["ids"=>$IdcBox['room_id'],"output"=>$options['selectIdcRoom']])))){ 
                    $result[$IdcBoxId]['selectIdcRoom'] = $IdcRoomInfo;
                }
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
                    $IdcBoxId = bzhyCDB::insert($this->tableName, [$this->data['dbdata']]);
                    $relatedFile['FileIds'] = $this->data['files'];
                    if(!zbx_empty($relatedFile['FileIds'])){
                        $relatedFile['RelatedTable'] = $this->tableName;
                        $relatedFile['RelatedField'] = $this->pkField;
                        $relatedFile['RelatedValue'] = $IdcBoxId[0];
                        $cFile = bzhyCBase::getInstanceByObject('file', []);
                        $cFile->data = $relatedFile;
                        if(!$cFile->insertRelatedObjects()){
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Add related files error!')];
                            return FALSE;
                        }
                        unset($relatedFile);
                    }
                    
                    $relatedContact['ContactIds'] = $this->data['contacts'];
                    if(!zbx_empty($relatedContact['ContactIds'])){
                        $relatedContact['RelatedTable'] = $this->tableName;
                        $relatedContact['RelatedField'] = $this->pkField;
                        $relatedContact['RelatedValue'] = $IdcBoxId[0];
                        $cContact = bzhyCBase::getInstanceByObject('contact', []);
                        $cContact->data = $relatedContact;
                        if($cContact->insertRelatedObjects()){
                            return TRUE;
                        }
                        else{
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Add related contacts error!')];
                            return FALSE;
                        }
                    }
                    return TRUE;
               } catch (Exception $ex) {
                   $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                   return FALSE; 
               }
               break;
            case"modify.posted":
                try {
                    if(!isset($this->data['id'])){
                         $ZBX_MESSAGES[] = ['type'=>'error','message'=>'IdcBox ID not found!'];
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
                    $relatedFile['FileIds'] = $this->data['files'];
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $this->data['id'];
                    $cFile = bzhyCBase::getInstanceByObject('file', []);
                    $cFile->data = $relatedFile;
                    if(!$cFile->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                        return FALSE;
                    }
                    if(!zbx_empty($relatedFile['FileIds'])){
                        if(!$cFile->insertRelatedObjects()){
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                            return FALSE;
                        }
                    }
                    
                    $relatedContact['ContactIds'] = $this->data['contacts'];
                    $relatedContact['RelatedTable'] = $this->tableName;
                    $relatedContact['RelatedField'] = $this->pkField;
                    $relatedContact['RelatedValue'] = $this->data['id'];
                    $cContact = bzhyCBase::getInstanceByObject('contact', []);
                    $cContact->data = $relatedContact;
                    if(!$cContact->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related contacts error!')];
                        return FALSE;
                    }
                    if(!zbx_empty($relatedContact['ContactIds'])){
                        if($cContact->insertRelatedObjects()){
                            return TRUE;
                        }
                        else{
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related contacts error!')];
                            return FALSE;
                        }
                    }
                    return TRUE;
                } catch (Exception $ex) {
                    $ZBX_MESSAGES[] = ['type'=>'error','message'=>$ex->getMessage()];
                    return FALSE;
                }
                break;
            case"close.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
                    return TRUE;
                } catch (Exception $ex) {
                    return FALSE;
                }
                break;
            case"open.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = bzhyCDB::update($this->tableName, $updatedb);
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
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $this->data['id'];
                    $cFile = bzhyCBase::getInstanceByObject('file', []);
                    $cFile->data = $relatedFile;
                    if(!$cFile->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Delete related files error!')];
                        return FALSE;
                    }
                    $relatedContact['RelatedTable'] = $this->tableName;
                    $relatedContact['RelatedField'] = $this->pkField;
                    $relatedContact['RelatedValue'] = $this->data['id'];
                    $cContact = bzhyCBase::getInstanceByObject('contact', []);
                    $cContact->data = $relatedContact;
                    if(!$cContact->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related contacts error!')];
                        return FALSE;
                    }
                    $updatedb['where']['id'] = $this->data['id'];
                    $result = bzhyCDB::delete($this->tableName, $updatedb['where']);
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
}

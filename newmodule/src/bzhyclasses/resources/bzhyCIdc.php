<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


class bzhyCIdc extends bzhyObjectCommon  {
    
    protected $ObjectName='idc_room';
     
    protected $sortColumns = ['room_name','room_status','room_starttime','room_endtime'];


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
            'select'	=> [$this->tableName => bzhyCDB::getFieldIdByObject($this->ObjectName,'id')],
            'from'	=> [$this->tableName => bzhyCDB::getFromByObject($this->ObjectName)],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'idcbox_ids'                               => NULL, 
            'sortfield'                                => '',
            'sortorder'				       => '',
            'limit'                                    => null,
            'limitSelects'			       => null,
            'selectCreateUser'                         => null, 
            'selectLastUser'                           => null, 
            'selectDelUser'                            => null,
            'selectFile'                               => null,
            'selectContact'                            => null, 
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null,
            'selectIdcBox'                             => null
            
        ];
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['idc_roomid'] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'id'), $options['ids']);
	}
        if (!is_null($options['idcbox_ids'])) {
            $sqlParts['from']['idc_box'] =  bzhyCDB::getFromByObject('idc_box');
            zbx_value2array($options['idcbox_ids']);
            $sqlParts['where'][] = dbConditionInt(bzhyCDB::getFieldIdByObject('idc_box','id'), $options['idcbox_ids']);
            $sqlParts['where']['idcbox_ids'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'id').'= '.bzhyCDB::getFieldIdByObject('idcbox_ids','room_id');
	}
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
	$sqlParts = bzhyCDB::applyQuerySortOptions($this->tableName, $this->tableAlias, $options, $sqlParts);

        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($idc = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $idc;
		}
		else {
                    $result = $idc['rowscount'];
		}
            }
            else {
		$result[$idc['id']] = $idc;
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
        $idcroomids = array_keys($result);
        //Add Create user related 
        if ($options['selectCreateUser'] !== null) {
            foreach ($result as $idcroomid => $idcroom){
                if(isset($idcroom['room_userid']) && trim($idcroom['room_userid']) != NULL){
                    if(!is_null(($UserInfo=  API::getApi('user')->get(["userids"=>$idcroom['room_userid'],"output"=>$options['selectCreateUser']])))){ 
                        $result[$idcroomid]['CreateUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        //Add modified user related 
        if ($options['selectLastUser'] !== null) {
            foreach ($result as $idcroomid => $idcroom){
                if(isset($idcroom['room_lastuserid']) && trim($idcroom['room_lastuserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$idcroom['room_lastuserid'],"output"=>$options['selectLastUser']])))){ 
                        $result[$idcroomid]['LastUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        //get files
        if($options['selectFile'] !== null) {
            foreach ($result as $idcroomid => $idcroom){
                $FileOptions['output'] = $options['selectFile'];
                $FileOptions['object_ids'] = $idcroomid;
                $FileOptions['object_table'] = $this->tableName;
                $cFile = bzhyCBase::getInstanceByObject('file', []);
                $selectedfile = $cFile->get($FileOptions);
                $result[$idcroomid]['SelectFile'] = $selectedfile;
            }
        }
        
        if($options['selectContact'] !== null) {
            foreach ($result as $idcroomid => $idcroom){
                $ContactOptions['output'] = $options['selectContact'];
                $ContactOptions['object_ids'] = $idcroomid;
                $ContactOptions['object_table'] = $this->tableName;
                $cContact = bzhyCBase::getInstanceByObject('contact', []);
                $selectContact = $cContact->get($ContactOptions);
                $result[$idcroomid]['selectContact'] = $selectContact;
            }
        }
        
        //Add deleted user related 
        if ($options['selectDelUser'] !== null) {
            foreach ($result as $idcroomid => $idcroom){
                if(isset($idcroom['room_deluserid']) && trim($idcroom['room_deluserid']) != NULL){
                    if(!is_null(($UserInfo= API::getApi('user')->get(["userids"=>$idcroom['room_deluserid'],"output"=>$options['selectDelUser']])))){ 
                        $result[$idcroomid]['DelUserInfo'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                }
            }
        }
        
        //Get IDC BOX information by idcroom id 
        if ($options['selectIdcBox'] !== null) {
            $idcboxInstance = bzhyCBase::getInstanceByObject('idc_box', []);
            foreach ($result as $idcroomid => $idcroom){
                if(!bzhy_empty($idcboxInstance) && count(($IdcBoxInfo=$idcboxInstance->get(["idcroom_ids"=>$idcroomid,"output"=>$options['selectIdcBox']])))>0){ 
                    
                    $result[$idcroomid]['selectIdcBox'] = $IdcBoxInfo;
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
                    $IDCId = bzhyCDB::insert($this->tableName, [$this->data['dbdata']]);
                    if(!zbx_empty($this->data['files'])){
                        $relatedFile['FileIds'] = $this->data['files'];
                        $relatedFile['RelatedTable'] = $this->tableName;
                        $relatedFile['RelatedField'] = $this->pkField;
                        $relatedFile['RelatedValue'] = $IDCId[0];
                        $cFile = bzhyCBase::getInstanceByObject('file', []);
                        $cFile->data = $relatedFile;
                        if(!$cFile->insertRelatedObjects()){
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Add related files error!')];
                            return FALSE;
                        }
                        unset($relatedFile);
                    }
                    if(!zbx_empty($this->data['contacts'])){
                        $relatedContact['ContactIds'] = $this->data['contacts'];
                        $relatedContact['RelatedTable'] = $this->tableName;
                        $relatedContact['RelatedField'] = $this->pkField;
                        $relatedContact['RelatedValue'] = $IDCId[0];
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
                    if(!zbx_empty($this->data['files'])){
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
                    if(!zbx_empty($this->data['contacts'])){
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
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                        return FALSE;
                    }
                    $relatedContact['RelatedTable'] = $this->tableName;
                    $relatedContact['RelatedField'] = $this->pkField;
                    $relatedContact['RelatedValue'] = $this->data['id'];
                    $cContact =bzhyCBase::getInstanceByObject('contact', []);
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
       return TRUE;
    }
    
}


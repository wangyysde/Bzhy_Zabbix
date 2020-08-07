<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


class bzhyCDevice {

    protected $tableName = 'deviceinfo';
    protected $tableAlias = 'di';
    protected $pkField = 'deviceid';
    protected $sortColumns = [];
    
    protected $typeTable ='devicetype';
    protected $typeAlias ='dt';

    protected $osTable ='os';
    protected $osAlias ='os';
    
    protected $brandTable ='brandinfo';
    protected $brandAlias ='bd';
    
    
    public $deviceSize = ['1'=>'1U','2'=>'2U','3'=>'3U','4'=>'4U','6'=>'6U','8'=>'8U','0'=>'Other'];
    public  $data = null;

    public function getBrand($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> ['brandinfo' => 'brandinfo.id'],
            'from'		=> ['brandinfo' => 'brandinfo bd'],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'local_name'                               => NULL,
            'english_name'                             => NULL,
            'sortfield'                                => 'local_name',
            'sortorder'				       => '',
            'limit'                                    => null,
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['id'] = dbConditionInt('bd.id', $options['ids']);
	}
        
        if (!is_null($options['local_name'])) {
            zbx_value2array($options['local_name']);
            $sqlParts['where']['local_name'] = dbConditionString('bd.local_name', $options['local_name']);
	}
               
        if (!is_null($options['english_name'])) {
            zbx_value2array($options['english_name']);
            $sqlParts['where']['english_name'] = dbConditionString('bd.english_name', $options['english_name']);
	}
        
        $sqlParts = $this->applyQueryOutputOptions($this->brandTable, $this->brandAlias, $options, $sqlParts);
	$sqlParts = $this->applyQuerySortOptions($this->brandTable, $this->brandAlias, $options, $sqlParts);
        
        $res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($brand = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $brand;
		}
		else {
                    $result = $brand['rowscount'];
		}
            }
            else {
		$result[$brand['id']] = $brand;
            }
        }
        
        return $result;
    }
    
    public function getOS($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> ['os' => 'os.osid'],
            'from'		=> ['os' => 'os os'],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'osname'                                   => NULL,
            'osbit'                                    => NULL,
            'version'                                  => NULL,
            'sortfield'                                => 'osname',
            'sortorder'				       => '',
            'limit'                                    => null,
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['osid'] = dbConditionInt('os.osid', $options['ids']);
	}
        
        if (!is_null($options['osname'])) {
            zbx_value2array($options['osname']);
            $sqlParts['where']['osname'] = dbConditionString('os.osname', $options['osname']);
	}
        
        if (!is_null($options['osbit'])) {
            zbx_value2array($options['osbit']);
            $sqlParts['where']['osbit'] = dbConditionInt('os.osbit', $options['osbit']);
	}
        
        if (!is_null($options['version'])) {
            zbx_value2array($options['version']);
            $sqlParts['where']['version'] = dbConditionString('os.version', $options['version']);
	}
        
        $sqlParts = $this->applyQueryOutputOptions($this->osTable, $this->osAlias, $options, $sqlParts);
	$sqlParts = $this->applyQuerySortOptions($this->osTable, $this->osAlias, $options, $sqlParts);
        
        $res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($os = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $os;
		}
		else {
                    $result = $os['rowscount'];
		}
            }
            else {
		$result[$os['osid']] = $os;
            }
        }
        
        return $result;
    }

    public function getDeviceType($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> ['devicetype' => 'dt.typeid'],
            'from'		=> ['devicetype' => 'devicetype dt'],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'typename'                                 => NULL,
            'status'                                   => NULL,
            'sortfield'                                => 'typename',
            'sortorder'				       => '',
            'limit'                                    => null,
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['typeid'] = dbConditionInt('dt.typeid', $options['ids']);
	}
        
        if (!is_null($options['typename'])) {
            zbx_value2array($options['typename']);
            $sqlParts['where']['typename'] = dbConditionString('dt.typename', $options['typename']);
	}
        
        if (!is_null($options['status'])) {
            zbx_value2array($options['status']);
            $sqlParts['where']['status'] = dbConditionInt('dt.status', $options['status']);
	}
        
        $sqlParts = $this->applyQueryOutputOptions($this->typeTable, $this->typeAlias, $options, $sqlParts);
	$sqlParts = $this->applyQuerySortOptions($this->typeTable, $this->typeAlias, $options, $sqlParts);
        
        $res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($deviceInfo = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $deviceInfo;
		}
		else {
                    $result = $deviceInfo['rowscount'];
		}
            }
            else {
		$result[$deviceInfo['typeid']] = $deviceInfo;
            }
        }
        
        return $result;
    }
    
    
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
            'select'	=> ['deviceinfo' => 'di.deviceid'],
            'from'		=> ['deviceinfo' => 'deviceinfo di'],
            'group'		=> [],
            'order'		=> []
	];
        
        $defOptions = [
            'ids'                                      => NULL,
            'typeid'                                   => NULL,
            'roomid'                                   => NULL, 
            'idcbox_ids'                               => NULL, 
            'belongdeviceid'                           => NULL,
            'isruning'                                 =>NULL,
            'status'                                   =>NULL,
            'osid'                                     =>NULL,
            'brandid'                                  =>NULL,
            'userid'                                   =>NULL,
            'sortfield'                                => '',
            'sortorder'				       => '',
            'limit'                                    => null,
            'limitSelects'			       => null,
            'selectType'                               => NULL,
            'selectRoom'                               => NULL,
            'selectIdcBox'                             => NULL,
            'selectBelong'                             => NULL,
            'selectOS'                                 => NULL,
            'selectBrand'                              => NULL,
            'selectCreateUser'                         => NULL,
            'selectFile'                               => null,
            'selectContact'                            => null, 
            'output'                                   => API_OUTPUT_EXTEND,
            'countOutput'			       => null,
            'groupCount'			       => null
            
        ];
        $options = zbx_array_merge($defOptions, $options);
        if (!is_null($options['ids'])) {
            zbx_value2array($options['ids']);
            $sqlParts['where']['deviceid'] = dbConditionInt('di.deviceid', $options['ids']);
	}
        
        if (!is_null($options['typeid'])) {
            zbx_value2array($options['typeid']);
            $sqlParts['where']['typeid'] = dbConditionInt('di.typeid', $options['typeid']);
	}
        
        if (!is_null($options['roomid'])) {
            zbx_value2array($options['roomid']);
            $sqlParts['where']['roomid'] = dbConditionInt('di.roomid', $options['roomid']);
	}
        
        if (!is_null($options['idcbox_ids'])) {
            zbx_value2array($options['idcbox_ids']);
            $sqlParts['where']['boxid'] = dbConditionInt('di.boxid', $options['idcbox_ids']);
	}
        
        if (!is_null($options['belongdeviceid'])) {
            zbx_value2array($options['belongdeviceid']);
            $sqlParts['where']['belongdeviceid'] = dbConditionInt('di.belongdeviceid', $options['belongdeviceid']);
	}
        
        if (!is_null($options['isruning'])) {
            zbx_value2array($options['isruning']);
            $sqlParts['where']['isruning'] = dbConditionInt('di.isruning', $options['isruning']);
	}
        
        if (!is_null($options['status'])) {
            zbx_value2array($options['status']);
            $sqlParts['where']['status'] = dbConditionInt('di.status', $options['status']);
	}
        
        if (!is_null($options['osid'])) {
            zbx_value2array($options['osid']);
            $sqlParts['where']['osid'] = dbConditionInt('di.osid', $options['osid']);
	}
        
        if (!is_null($options['brandid'])) {
            zbx_value2array($options['brandid']);
            $sqlParts['where']['brandid'] = dbConditionInt('di.brandid', $options['brandid']);
	}
        
        if (!is_null($options['userid'])) {
            zbx_value2array($options['userid']);
            $sqlParts['where']['userid'] = dbConditionInt('di.userid', $options['userid']);
	}
        
        
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}
        else{
            $sqlParts['limit'] = null;
        }
        $sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
	$sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
        $res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($deviceinfo = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $deviceinfo;
		}
		else {
                    $result = $deviceinfo['rowscount'];
		}
            }
            else {
		$result[$deviceinfo['deviceid']] = $deviceinfo;
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
        $result = parent::addRelatedObjects($options, $result);
        $deviceids = array_keys($result);
        
        if ($options['selectType'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['typeid']) && trim($line['typeid']) != NULL){
                    if(!is_null($TypeInfo = $this->getDeviceType(['ids'=>$line['typeid'],'output'=>$options['selectType']]))){
                        $result[$deviceid]['selectType'] = $TypeInfo;
                    }
                    else{
                        $result[$deviceid]['selectType'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectType'] = NULL;
                }
            }
        }
        
        if ($options['selectRoom'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['roomid']) && trim($line['roomid']) != NULL){
                    if(count(($IdcInfo= CBase::getObject('idc_room')->get(["ids"=>$line['roomid'],"output"=>$options['selectRoom']])))>0){ 
                        $result[$deviceid]['selectRoom'] = $IdcInfo;
                    }
                    else{
                        $result[$deviceid]['selectRoom'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectRoom'] = NULL;
                }
            }
        }
        
        if ($options['selectIdcBox'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['boxid']) && trim($line['boxid']) != NULL){
                    if(count(($IdcBoxInfo= CBase::getObject('idc_box')->get(["ids"=>$line['boxid'],"output"=>$options['selectIdcBox']])))>0){ 
                        $result[$deviceid]['selectIdcBox'] = $IdcBoxInfo;
                    }
                    else{
                        $result[$deviceid]['selectIdcBox'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectIdcBox'] = NULL;
                }
            }
        }
        
        if ($options['selectBelong'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['belongdeviceid']) && trim($line['belongdeviceid']) != NULL && $line['belongdeviceid'] !== 0){
                    if(count(($BelognDevice= $this->get(['ids'=>$line['belongdeviceid'],'output'=>$options['selectBelong']] )))>0){ 
                        $result[$deviceid]['selectBelong'] = $BelognDevice;
                    }
                    else{
                        $result[$deviceid]['selectBelong'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectBelong'] = NULL;
                }
            }
        }
        
        if ($options['selectOS'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['osid']) && trim($line['osid']) != NULL && $line['osid'] != 0){
                    if(!is_null($OSInfo = $this->getOS(['ids'=>$line['osid'],'output'=>$options['selectOS']]))){
                        $result[$deviceid]['selectOS'] = $OSInfo;
                    }
                    else{
                        $result[$deviceid]['selectOS'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectOS'] = NULL;
                }
            }
        }
        
        if ($options['selectBrand'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['brandid']) && trim($line['brandid']) != NULL && $line['brandid'] != 0){
                    if(!is_null($BrandInfo = $this->getBrand(['ids'=>$line['brandid'],'output'=>$options['selectBrand']]))){
                        $result[$deviceid]['selectBrand'] = $BrandInfo;
                    }
                    else{
                        $result[$deviceid]['selectBrand'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectBrand'] = NULL;
                }
            }
        }
        
        if ($options['selectCreateUser'] !== null) {
            foreach ($result as $deviceid => $line){
                if(isset($line['userid']) && trim($line['userid']) != NULL){
                    if(!is_null(($UserInfo=  API::getApi('user')->get(["userids"=>$line['userid'],"output"=>$options['selectCreateUser']])))){ 
                        $result[$deviceid]['selectCreateUser'] = isset($UserInfo[0])?$UserInfo[0]:NULL;
                    }
                    else{
                        $result[$deviceid]['selectCreateUser'] = NULL;
                    }
                }
                else{
                    $result[$deviceid]['selectCreateUser'] = NULL;
                }
            }
        }

        //get files
        if($options['selectFile'] !== null) {
            foreach ($result as $deviceid => $line){
                $FileOptions['output'] = $options['selectFile'];
                $FileOptions['object_ids'] = $deviceid;
                $FileOptions['object_table'] = $this->tableName;
                $cFile = new CAttachment();
                $selectedfile = $cFile->get($FileOptions);
                $result[$deviceid]['SelectFile'] = $selectedfile;
            }
        }
        
        if($options['selectContact'] !== null) {
            foreach ($result as $deviceid => $line){
                $ContactOptions['output'] = $options['selectContact'];
                $ContactOptions['object_ids'] = $deviceid;
                $ContactOptions['object_table'] = $this->tableName;
                $cContact = new CContact();
                $selectContact = $cContact->get($ContactOptions);
                $result[$deviceid]['selectContact'] = $selectContact;
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
                    $files = $this->data['dbdata']['files'];
                    $contacts = $this->data['dbdata']['contacts'];
                    $DeviceId = bzhyDB::insert($this->tableName, [$this->data['dbdata']]);
                    if(!zbx_empty($files)){
                        $relatedFile['FileIds'] = $files;
                        $relatedFile['RelatedTable'] = $this->tableName;
                        $relatedFile['RelatedField'] = $this->pkField;
                        $relatedFile['RelatedValue'] = $DeviceId[0];
                        $cFile = new CAttachment();
                        $cFile->data = $relatedFile;
                        if(!$cFile->insertRelatedObjects()){
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Add related files error!')];
                            return FALSE;
                        }
                        unset($relatedFile);
                    }
                    if(!zbx_empty($contacts)){
                        $relatedContact['ContactIds'] = $contacts;
                        $relatedContact['RelatedTable'] = $this->tableName;
                        $relatedContact['RelatedField'] = $this->pkField;
                        $relatedContact['RelatedValue'] = $DeviceId[0];
                        $cContact = new CContact();
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
                         $ZBX_MESSAGES[] = ['type'=>'error','message'=>'Device ID not found!'];
                         return FALSE;
                    }
                    $files = isset($this->data['dbdata']['files'])?$this->data['dbdata']['files']:[];
                    $contacts = isset($this->data['dbdata']['contacts'])?$this->data['dbdata']['contacts']:[];
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['deviceid'] = $this->data['id'];
                    $result = bzhyDB::update($this->tableName, $updatedb);
                    $relatedFile['FileIds'] = $files;
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $this->data['id'];
                    $cFile = new CAttachment();
                    $cFile->data = $relatedFile;
                    if(!$cFile->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                        return FALSE;
                    }
                    if(!zbx_empty($files)){
                        if(!$cFile->insertRelatedObjects()){
                            $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related files error!')];
                            return FALSE;
                        }
                    }
                    $relatedContact['ContactIds'] = $contacts;
                    $relatedContact['RelatedTable'] = $this->tableName;
                    $relatedContact['RelatedField'] = $this->pkField;
                    $relatedContact['RelatedValue'] = $this->data['id'];
                    $cContact = new CContact();
                    $cContact->data = $relatedContact;
                    if(!$cContact->deleteRelatedObjects()){
                        $ZBX_MESSAGES[] = ['type'=>'error','message'=>_('Modify related contacts error!')];
                        return FALSE;
                    }
                    if(!zbx_empty($contacts)){
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
            case"mantaince.posted":
            case"offline.posted":
            case"online.posted":
                try{
                    if(!isset($this->data['id'])){
                         return FALSE;
                    }
                    $updatedb['values'] = $this->data['dbdata'];
                    $updatedb['where']['deviceid'] = $this->data['id'];
                    $result = bzhyDB::update($this->tableName, $updatedb);
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
    
    public function getLabels($fields = [],$withOutFields = array()){
        return NewDB::getFieldsLabel($this->tableName, $fields,$withOutFields);
    }
    
}


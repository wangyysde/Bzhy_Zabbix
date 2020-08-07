<?php
/*
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


class bzhyCSysSet extends bzhyObjectCommon{

	public  $data = null;
        
        protected $setting_file = '/conf/system_settings.ini.php';
        
        protected  $fp = NULL; 
        
        protected $ObjectName='sysset';
        
        /**
         * Initated the object data 
         * 
         */
       
        function __construct(){
            parent::__construct();
            $this->setting_file = bzhyCBase::getRootDir()."/".$this->setting_file;
            if(!file_exists($this->setting_file)){
                $this->updateSettingsFile();
            }
            
            require_once $this->setting_file;
        }
        public function get($options = []) {
            $result = [];
            
            $defOptions = [
                'byIds'                                 => NULL,
                'byNames'                               => NULL,
                'byType'                                => NULL,
                'output'                                => API_OUTPUT_EXTEND,
                'selectItems'                           => NULL
            ];
            $sqlParts = [
                 'select'	=> ['id' => bzhyCDB::getFieldIdByObject($this->ObjectName,'id')],
                 'from'		=> [bzhyCBase::getTableByObject($this->ObjectName) => bzhyCDB::getFromByObject($this->ObjectName)]
            ];
            
             
            $options = zbx_array_merge($defOptions, $options);
            if (!is_null($options['byIds'])) {
                zbx_value2array($options['byIds']);
                $sqlParts['where']['ids'] = dbConditionInt( bzhyCDB::getFieldIdByObject($this->ObjectName,'id'), $options['byIds']);
            }
            if (!is_null($options['byNames'])) {
                zbx_value2array($options['byNames']);
                $sqlParts['where']['setting_names'] = dbConditionString (bzhyCDB::getFieldIdByObject($this->ObjectName,'setting_name'), $options['byNames']);
            }
            if (!is_null($options['byType'])) {
                $sqlParts['from'][] = 'system_settingtype st';
                $sqlParts['where'][] = bzhyCDB::getFieldIdByObject($this->ObjectName,'settingtype_id').'=st.id ';
                zbx_value2array($options['byType']);
                $sqlParts['where']['system_type'] = dbConditionInt('st.id', $options['byType']);
            }
            
            $sqlParts = bzhyCDB::applyQueryOutputOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
            $sqlParts = bzhyCDB::applyQuerySortOptions($this->tableName, $this->tableAlias, $options, $sqlParts);
            $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts));
            
            while ($system_setting = DBfetch($res)) {
                $result[$system_setting['id']] = $system_setting;
                if(!is_null($options['selectItems'])){
                    if($system_setting['input_type'] != BZHYINPUT_TYPE_TEXTBOX){
                        $result[$system_setting['id']]['items'] = $this->getEnableItems($system_setting['setting_name']);
                    }
                    else{
                        $result[$system_setting['id']]['items'] = null; 
                    }
                }
            }
            return $result;
        }
        
        protected function getEnableItems($settingName = null){
            $result = [];
            if(is_null($settingName)){
                return FALSE;
            }

            $sqlParts = [
                 'select'	=> ['id','item_name','item_title','item_helpmsg','item_img','belong_setting'],
                 'from'		=> ['system_setting_item' => 'system_setting_item']
            ];
            zbx_value2array($settingName);
            $sqlParts['where']['belong_setting'] = dbConditionString ('belong_setting', $settingName);
            $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts));
            while ($setting_item = DBfetch($res)) {
                $result[$setting_item['id']] = $setting_item;
            }
            return $result;  
        }
       
        /**
         * Tries to create or update system_settings.ini.php file
         * @param Array  $SystemSettings An Array will be update to settings file 
         * 
         * @throws Exception if any errors ocured
         * 
         * @return bool              return true if update sucessful , or return false 
         */
        
        public function updateSettingsFile() {
            $options['selectItems'] = true; 
            $options['output'] =  ['setting_name','setting_value','input_type'];
            $fpcontent =  "<?php\n  global \$System_Settings;\n";
            $system_settings = $this->get($options);
            foreach ($system_settings as $setting_name=> $itemline){
                if(!isset($itemline['items']) || is_null($itemline['items'])){
                    $fpcontent .= "\$System_Settings['".$itemline['setting_name']."'] = '".$itemline['setting_value']."';\n";
                }
                else{
                    $itemname = [];
                    foreach ($itemline['items'] as $item){
                        $itemname[] = $item['item_name'];
                    }
                    $fpcontent .= "\$System_Settings['".$itemline['setting_name']."'] = '". implode(',', $itemname)."';\n";
                }
                
            }
            if(!$this->fp){
                if(!$this->fp=fopen($this->setting_file,"w+t")){
                    error(_('Open configuration file error,Please check permissions!'));
                    return FALSE;
                }
            }
            if(!fwrite($this->fp,$fpcontent)){
                error(_('Write to configuration file error'));
                return FALSE;
            }
            return TRUE;
        }
        
        public function update($options = []){
            if(empty($options))
                return FALSE;
            DBstart();
            foreach ($options as $key => $value){
                DBexecute("update ".$this->tableName." set setting_value='".$value."' where setting_name='".$key."';");
            }
            if(!DBend(true)){
                return FALSE;
            }
            if(!$this->updateSettingsFile()){
                return FALSE;
            }
            return TRUE;
        }
        
        
        public function getType($options = []){
            $defOptions = [
                "tableName"                             => "system_settingtype",
                "tableAlias"                            => "st",
                'output'                                => null,
                'byIds'                                 => NULL,
                'byNames'                               => NULL,
                'byShortName'                           => NULL,
                'countOutput'                           => NULL,
                'groupCount'                           => NULL,
            ];
            $sqlParts = [
                 'select'	=> ['id' => 'st.id'],
                 'from'		=> ['system_settingtype' => 'system_settingtype st']
            ];
            $options = zbx_array_merge($defOptions, $options);
            if (!is_null($options['byIds'])) {
                zbx_value2array($options['byIds']);
                $sqlParts['where']['ids'] = dbConditionInt('st.id', $options['byIds']);
            }
            if (!is_null($options['byNames'])) {
                zbx_value2array($options['byNames']);
                $sqlParts['where']['settingtype_name'] = dbConditionString ('st.settingtype_name', $options['settingtype_name']);
            }
            if (!is_null($options['byShortName'])) {
                zbx_value2array($options['byShortName']);
                $sqlParts['where']['settingtype_shortname'] = dbConditionString ('st.settingtype_shortname', $options['byShortName']);
            }
            $sqlParts = bzhyCDB::applyQueryOutputOptions($options["tableName"], $options["tableAlias"], $options, $sqlParts);
            $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts));
            while ($setting_type = DBfetch($res)) {
                if (!is_null($options['countOutput'])) {
                    if (!is_null($options['groupCount'])) {
                        $result[] = $setting_type;
                    }
                    else {
                        $result = $setting_type['rowscount'];
                    }
                }
                else {
                    $result[$setting_type['id']] = $setting_type;
                }
            }
            return $result;
        }
        
        public function existSettingfile(){
            return file_exists($this->setting_file)?TRUE:FALSE;        
        }
        
        public function getSettingfile(){
            return $this->setting_file;
        }
}

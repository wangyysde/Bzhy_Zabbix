<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

class bzhyCBase {
    
    const EXEC_MODE_DEFAULT = 'default';
    const EXEC_MODE_SETUP = 'setup';
    const EXEC_MODE_API = 'api';
    
    protected static $is_debug = TRUE;
    /* 
     * bzhyCBase will require the common files from 
     * the comm_dir when it is initating 
     *
     */
    protected static $comm_dirs = ['bzhyinclude','classes','classes/views'];
    
    /* 
     * bzhyCBase will require the customer files from 
     * the $require_dirs when it is initating 
     *
     */
    protected static $require_dirs = ['classes','classes/resource','classes/views','classes/common','classes/html'];
        
    protected static $runmode;
    
    /*
     * This variable is for new version 
     */
    
    protected static $comm_files = [
        'bzhydefines.inc','bzhyfunc.inc','bzhydb.inc','bzhyDB','bzhyusers.inc','bzhyperm.inc','bzhyhtml.inc'
    ];
     
    protected static $rootDir;
    
    /*
     * objectdefine:load from WEBSITEROOT/bzhyinclude/objectdefine.inc.php
     * This file recorded the relations of object ,object file and object table
     */
    
    protected static $objectdefine;
    
    /* 
     * This variable is using to record objects to instances map
     * This variable is a array, keys are object name and values 
     * are instances of the class.
     */
    protected static $instances = [];
    
    protected static $objctdefinefile = "objectdefine.inc";


    public function __construct($initobject=false, $runmode= self::EXEC_MODE_DEFAULT) {
        /* adding common files into require_files */
        if(!isset(self::$instances['cbase']) || !self::$instances['cbase']){
            self::$instances['cbase'] = $this;
            self::$rootDir = self::findRootDir();
            self::$runmode = $runmode;
            
            /* require once common files from common dir */
            foreach (self::$comm_files as $commfile){
                foreach (self::$comm_dirs as $dir){
                    $file = self::$rootDir.'/'.$dir."/".$commfile.".php";
                    if(file_exists($file)){
                        require_once $file;
                    }
                }
            }
            
            /* Load object defines from WEBSITEROOT/bzhyinclude/objectdefine.inc.php */
            $file = self::$rootDir.'/bzhyinclude/'.self::$objctdefinefile.".php";
            if(file_exists($file)){
                self::$objectdefine = include($file);
            }
            
            /* Load common class file */
            foreach (self::$objectdefine as $object){
                if($object['class'] === BZHY_OBJECT_CLASS_COMMON){
                    if(is_array($object['filename'])){                          // One object can have more than one files
                        foreach ($object['filename'] as $objectfile){
                            foreach (self::$require_dirs as $dir){
                                $file = self::$rootDir."/".$dir."/".$objectfile.".php";
                                if(file_exists($file)){
                                    require_once $file;
                                }
                            }
                        }
                    }
                    else{
                        foreach (self::$require_dirs as $dir){
                            $file = self::$rootDir."/".$dir."/".$object['filename'].".php";
                            if(file_exists($file)){
                                require_once $file;
                            }
                        }
                    }
                }
            }
            
            /* Initating all common objects */
            foreach (self::$objectdefine as $object =>$value){
                if($value['class'] === BZHY_OBJECT_CLASS_COMMON){
                    if((!isset(self::$instances[$object]) || !self::$instances[$object]) && $value['isinit']){
                        self::$instances[$object] = new $value['classname'];
                    }
                    if($object === 'sysset'){
                        $inst = self::$instances[$object];
                        if(!$inst->existSettingfile()){
                            $inst->updateSettingsFile();
                            $settingfile = $inst->getSettingfile();
                            require_once $settingfile;
                        }
                    }
                }
            }
            
            /* Load extra object files and initating extra object class */
            if($initobject){
                if(is_array($initobject)){                                      //If we should load more than one extra files and initate more than objects
                    foreach ($initobject as $object){
                        if(isset(self::$objectdefine[$object]) && 
                            self::$objectdefine[$object]['class'] === BZHY_OBJECT_CLASS_EXTRA){
                            $objectfiles =  self::$objectdefine[$object]['filename'];
                            if(is_array($objectfiles)){
                                foreach ($objectfiles as $files){
                                    foreach (self::$require_dirs as $dir){
                                        $file = self::$rootDir."/".$dir."/".$files.".php";
                                        if(file_exists($file)){
                                            require_once $file;
                                        }
                                    }
                                }
                            }
                            else{
                                $files = $objectfiles;
                                foreach (self::$require_dirs as $dir){
                                    $file = self::$rootDir."/".$dir."/".$files.".php";
                                    if(file_exists($file)){
                                        require_once $file;
                                    }
                                }
                            }
                            if(!isset(self::$instances[$object]) || !self::$instances[$object]){
                                self::$instances[$object] = new self::$objectdefine[$object]['classname'];
                            }
                        }
                    }
                }
                else{
                    $object = $initobject;
                    if(isset(self::$objectdefine[$object]) && 
                        self::$objectdefine[$object]['class'] === BZHY_OBJECT_CLASS_EXTRA){
                        $objectfiles =  self::$objectdefine[$object]['filename'];
                        if(is_array($objectfiles)){
                            foreach ($objectfiles as $files){
                                foreach (self::$require_dirs as $dir){
                                    $file = self::$rootDir."/".$dir."/".$files.".php";
                                    if(file_exists($file)){
                                        require_once $file;
                                    }
                                }
                            }
                        }
                        else{
                            $files = $objectfiles;
                            foreach (self::$require_dirs as $dir){
                                $file = self::$rootDir."/".$dir."/".$files.".php";
                                if(file_exists($file)){
                                    require_once $file;
                                }
                            }
                        }
                        if(!isset(self::$instances[$object]) || !self::$instances[$object]){
                            self::$instances[$object] = new self::$objectdefine[$object]['classname'];
                        }
                    }
                }
            }
            $this->run($runmode);
            
        }
        return self::$instances['cbase'];
    }
    
    private function findRootDir() {
        return realpath(dirname(__FILE__).'/..');        
    }
    
   /* 
    * Get the instance of a object. If there is no instance of a object ,
    * then new a new instance for the object and return it  
    * @param $object string object name
    * @return the instance of a object or false
    */ 
    public static function getObject($object,...$params) {
        if (!isset(self::$instances[$object])) {
            if(isset(self::$objectdefine[$object])){
                $definition = self::$objectdefine[$object]['classname'];
                if($definition instanceof Closure){
                    $ret = $definition;
                }
                else{
                   switch (count($params)){
                        case 0:
                            $ret = new $definition();
                            break;
                        case 1:
                            $ret = new $definition($params[0]);
                            break;
                        case 2:
                            $ret = new $definition($params[0],$params[1]);
                            break;
                        case 3:
                            $ret = new $definition($params[0],$params[1],$params[2]);
                            break;
                        case 4:
                            $ret = new $definition($params[0],$params[1],$params[2],$params[3]);
                            break;
                        case 5:
                           $ret = new $definition($params[0],$params[1],$params[2],$params[3],$params[4]);
                            break;
                        default :
                            $ret = FALSE;
                            break;
                    }
                        
                }
                //$ret = self::$instances[$object] = ($definition instanceof Closure) ? $definition: new $definition();
            }
            else{
                $ret  = FALSE;
            }
        }
        else{
            $ret = self::$instances[$object];
        }

        return $ret;
    }
    
    /*
     * Require extra object files and new new instance for them 
     * @param $object: Array  object names.
     * @return true or False
     */
    public static function addObjects(array $objects){
        foreach ($objects as $object){
            if(isset(self::$objectdefine[$object]) && 
                self::$objectdefine[$object]['class'] === BZHY_OBJECT_CLASS_EXTRA){  //Onle extra object can be add
                $files = self::$objectdefine[$object]['filename'];
                if(is_array($files)){
                    foreach ($files as $objectfile){
                        foreach (self::$require_dirs as $dir){
                            $file = self::$rootDir."/".$dir."/".$objectfile.".php";
                            if(file_exists($file)){
                                require_once $file;
                            }
                        }
                    }
                }
                else{
                    $objectfile = $files;
                    foreach (self::$require_dirs as $dir){
                        $file = self::$rootDir."/".$dir."/".$objectfile.".php";
                        if(file_exists($file)){
                            require_once $file;
                        }
                    }
                    
                }
                if (!isset(self::$instances[$object])) {
                    $definition = self::$objectdefine[$object]['classname'];
                    self::$instances[$object] = ($definition instanceof Closure) ? $definition() : new $definition();
                }
            }
        }
        
        return TRUE;
    }
    
    /* 
     * Get object table name by object name 
     * @param:object string object name
     * @return table name of a object or null
     */
    public static function getObjectTable($object){
        if(!zbx_empty($object)){
            if(isset(self::$objectdefine[$object]['tablename'])){
                $ret = self::$objectdefine[$object]['tablename'];
            }
            else{
                $ret = null;
            }
        }
        else{
            $ret = null;
        }
        
        return $ret;
    }
    
    /*
     * Get TableAlias by TableName.
     * @Param $tableName string 
     * @Return If there is a table alias defination for $tableName in objectdefine.inc.php,
     *     then return the table alias; or if there is not a table alias defination for 
     *     $tableName in objectdefine.inc.php and $tableName is string,then return tableName;
     *    Otherwise, return null    
     */
    public static function getTableAliasByTable($tableName){
        if(is_string($tableName)){
            $ret = $tableName;
        }
        else{
            $ret = null;
        }
        
        if(!zbx_empty($tableName) && is_string($tableName)){
            foreach (self::$objectdefine as $object => $values){
                if(isset($values['tablename']) && strcasecmp($values['tablename'],$tableName) == 0){
                    if(!isset($values['tableAlias'])){
                        $ret = $values['tableAlias'];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    /*
     * Get TableAlias by ObjectName.
     * @Param $object string 
     * @Return If there is a table alias defination for $object in objectdefine.inc.php,
     *     then return the table alias; or if there is not a table alias defination for 
     *     $object in objectdefine.inc.php and $tableName is string,then return tableName;
     *    Otherwise, return null    
     */
    public static function getTableAliasByObject($object){
        if(bzhy_empty($object)){
            $ret = null;
        }
        else{
            if(isset(self::$objectdefine[$object]['tableAlias']) 
                    && !bzhy_empty(self::$objectdefine[$object]['tableAlias'])){
                $ret = self::$objectdefine[$object]['tableAlias'];
            }
            else{
                if(isset(self::$objectdefine[$object]['tablename']) && 
                    !bzhy_empty(self::$objectdefine[$object]['tablename'])){
                   $ret = self::$objectdefine[$object]['tablename'];
                }
                else{
                    $ret = null;
                }
            }
        }  
        return $ret;
    }
    
    /*
     * Get website root dir
     */
    public static function getRootDir(){
        if(bzhy_empty(self::$rootDir)){
            self::findRootDir();
        }
        
        return self::$rootDir;
    }
    
    /**
    * Throws an API exception.
    *
    * @static
    *
    * @param int    $code
    * @param string $error
    */
   public static function exception($code = BZHY_API_ERROR_INTERNAL, $error = '',$file,$line) {
       if(self::$is_debug){
           if(!bzhy_empty($file)){
               $error .= ":".$file;
           }
           if(!bzhy_empty($line)){
               $error .= $line;
           }
       }
        throw new Exception($error,$code);
    }
    
    /**
    * Unsets fields $fields from the given objects if they are not requested in $output.
    *
    * @param array        $objects
    * @param array        $fields
    * @param string|array $output		desired output
    *
    * @return array
    */
    public static  function unsetExtraFields(array $objects, array $fields, $output) {
        // find the fields that have not been requested
	$extraFields = [];
	foreach ($fields as $field) {
            if (!self::outputIsRequested($field, $output)) {
		$extraFields[] = $field;
            }
	}

	// unset these fields
	if ($extraFields) {
            foreach ($objects as &$object) {
		foreach ($extraFields as $field) {
                    unset($object[$field]);
		}
            }
            unset($object);
	}

	return $objects;
    }
        
    /**
    * Returns true if the given field is requested in the output parameter.
    *
    * @param $field
    * @param $output
    *
    * @return bool
    */
   public static function outputIsRequested($field, $output) {
        switch ($output) {
            // if all fields are requested, just return true
            case API_OUTPUT_EXTEND:
		return true;

            // return false if nothing or an object count is requested
            case API_OUTPUT_COUNT:
            case null:
		return false;

            // if an array of fields is passed, check if the field is present in the array
            default:
		return in_array($field, $output);
	}
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
   public static function createRelationMap(array $objects, $baseField, $foreignField, $table = null) {
	$relationMap = bzhyAPI::RelationMap();
        // create the map from a database table
	if ($table) {
            $res = DBselect(self::createSelectQuery($table, [
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
    
    protected function run($runmode =self::EXEC_MODE_DEFAULT){
        switch ($runmode){
            case self::EXEC_MODE_DEFAULT:
                $this->authenticateUser();
                $this->initLocales();
                break;
            case self::EXEC_MODE_API:
                $this->initLocales();
                break;
            case self::EXEC_MODE_SETUP:
                $this->authenticateUser();
		$this->initLocales();
                break;
        }
    }
    
    /**
    * Authenticate user.
    */
    protected function authenticateUser() {
        $sessionId = bzhyCWebUser::checkAuthentication(bzhyCWebUser::getSessionCookie());
        if (!$sessionId) {
            bzhyCWebUser::setDefault();
        }
    }
    
    /**
    * Initialize translations.
    */
    protected function initLocales() {
	init_mbstrings();

		$defaultLocales = [
			'C', 'POSIX', 'en', 'en_US', 'en_US.UTF-8', 'English_United States.1252', 'en_GB', 'en_GB.UTF-8'
		];

		if (function_exists('bindtextdomain')) {
			// initializing gettext translations depending on language selected by user
			$locales = zbx_locale_variants(CWebUser::$data['lang']);
			$locale_found = false;
			foreach ($locales as $locale) {
				// since LC_MESSAGES may be unavailable on some systems, try to set all of the locales
				// and then revert some of them back
				putenv('LC_ALL='.$locale);
				putenv('LANG='.$locale);
				putenv('LANGUAGE='.$locale);
				setlocale(LC_TIME, $locale);

				if (setlocale(LC_ALL, $locale)) {
					$locale_found = true;
					CWebUser::$data['locale'] = $locale;
					break;
				}
			}

			// reset the LC_CTYPE locale so that case transformation functions would work correctly
			// it is also required for PHP to work with the Turkish locale (https://bugs.php.net/bug.php?id=18556)
			// WARNING: this must be done before executing any other code, otherwise code execution could fail!
			// this will be unnecessary in PHP 5.5
			setlocale(LC_CTYPE, $defaultLocales);

			if (!$locale_found && CWebUser::$data['lang'] != 'en_GB' && CWebUser::$data['lang'] != 'en_gb') {
				error('Locale for language "'.CWebUser::$data['lang'].'" is not found on the web server. Tried to set: '.implode(', ', $locales).'. Unable to translate Zabbix interface.');
			}
			bindtextdomain('frontend', 'locale');
			bind_textdomain_codeset('frontend', 'UTF-8');
			textdomain('frontend');
		}

		// reset the LC_NUMERIC locale so that PHP would always use a point instead of a comma for decimal numbers
		setlocale(LC_NUMERIC, $defaultLocales);

		// should be after locale initialization
		require_once $this->getRootDir().'/include/translateDefines.inc.php';
	}

}
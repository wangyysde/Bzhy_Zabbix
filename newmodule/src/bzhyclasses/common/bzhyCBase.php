<?php

/*
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */

/**
 * Description of bzhyCBase
 *
 * @author wangyuying
 */
class bzhyCBase {
    //put your code here
    
    const BZHY_RUN_MODE_DEFAULT='default';
    const BZHY_RUN_MODE_API='api';
    const BZHY_RUN_MODE_SETUP='setup';

    protected static $rootDir=NULL;
    
    protected static $objectFile = '/bzhyinclude/bzhyObjectDefineFile.inc.php';
    protected static $ObjectLabelDefined = '/bzhyinclude/bzhyObjectLabels.inc.php';
    
    protected static $commonDir = ['bzhyinclude'];
    
    protected static $commonFiles = ['bzhydefine.inc','bzhyconfig.inc','bzhyfun.inc','bzhyhtml.inc'];
    
    protected static $classesDir = ['bzhyclasses/common','bzhyclasses/resources','bzhyclasses/html','bzhyclasses/configure'];
    
    protected static $definedObjects = []; 

    protected static $instances = [];
    
    protected static $runMode = NULL;


    protected static function loadCommonFiles(){
        if(self::$rootDir === NULL){
            self::$rootDir = self::findRootDir();
        }
        
        foreach (self::$commonFiles as $commonFile){
            foreach (self::$commonDir as $dir){
                $file = self::$rootDir.'/'.$dir."/".$commonFile.".php";
                if(file_exists($file)){
                    require_once $file;
                }
            }
        }
    }
    
    protected static function loadCommonClassFiles(){
        $Objects = self::getAllObjects();
        foreach ($Objects as $object){
            if($object['class'] === BZHY_OBJECT_CLASS_COMMON){
                if(is_array($object['filename'])){
                    foreach (is_array($object['filename']) as $file){
                        foreach (self::$classesDir as $dir){
                            $tmpfile = self::$rootDir."/".$dir."/".$file.".php";
                            if(file_exists($tmpfile)){
                                require_once $tmpfile;
                            }
                        }
                    }
                }
                else{
                    foreach (self::$classesDir as $dir){
                        $tmpfile = self::$rootDir."/".$dir."/".$object['filename'].".php";
                        if(file_exists($tmpfile)){
                            require_once $tmpfile;
                        }
                    }
                }
            }
        }
    }

    protected static function initCommonClass(){
        $DefinedObjects = self::getAllObjects();
        foreach ($DefinedObjects as $objectName=>$object){
            if(!isset(self::$instances[$objectName])){
                if($object['class'] === BZHY_OBJECT_CLASS_COMMON && $object['isinit']){
                    self::$instances[$objectName] = new $object['classname'];
                }
            }
        }
    }

    public static function initExtraClass($objects,$paras){
        if(bzhy_empty($objects)){
            return TRUE;
        }
        
        if(!is_array($objects)){
            $objects = [$objects];
        }
        
        $DefinedObjects = self::getAllObjects();
        foreach ($objects as $object){
            if(!isset(self::$instances[$object])){
                if(!isset($DefinedObjects[$object])){
                    self::exception(BZHY_ERROR_NOOBJECT,"Object:".$object." was not found",__FILE__,__LINE__,TRUE);
                    return FALSE;
                }
                $definition = $DefinedObjects[$object]['classname'];
                
                if(!class_exists($definition)){
                    self::loadExtraClassFiles($object);
                }
                
                if(isset($paras[$object])){
                    $para = $paras[$object];
                    switch (count($para)){
                        case 0:
                            self::$instances[$object] =  new $definition();
                            break;
                        case 1:
                            self::$instances[$object] =  new $definition($para[0]);
                            break;
                        case 2:
                            self::$instances[$object] =  new $definition($para[0],$para[1]);
                            break;
                        case 3:
                            self::$instances[$object] =  new $definition($para[0],$para[1],$para[2]);
                            break;
                        case 4:
                            self::$instances[$object] =  new $definition($para[0],$para[1],$para[2],$para[3]);
                            break;
                        case 5:
                            self::$instances[$object] =  new $definition($para[0],$para[1],$para[2],$para[3],$para[4]);
                            break;
                        default:
                            self::exception(BZHY_ERROR_INTERNAL,"Object:".$object." parameters error:more than 5 parameters",__FILE__,__LINE__,TRUE);
                            return FALSE;
                    }
                }
                else{
                    self::$instances[$object] =  new $definition();
                }
            }
        }
        
        return TRUE;
    }

    public static function loadExtraClassFiles($objects){
                
        if(bzhy_empty($objects)){
            return true;
        }
        $DefinedObjects = self::getAllObjects();
        if(is_array($objects)){
            foreach ($objects as $objectName){
                if(!isset($DefinedObjects[$objectName])){
                    self::exception(BZHY_ERROR_NOOBJECT,"Object:".$objectName." was not found",__FILE__,__LINE__,TRUE);
                }
                
                $DefinedObject = $DefinedObjects[$objectName];
                if($DefinedObject['class'] !== BZHY_OBJECT_CLASS_EXTRA){
                    self::exception(BZHY_ERROR_OBJECTNOTLOAD,"Object:".$objectName." can not load in here",__FILE__,__LINE__,TRUE);
                }
                
                $objectFiles = $DefinedObject['filename'];
                if(is_array($objectFiles)){
                    foreach ($objectFiles as $file){
                        foreach (self::$classesDir as $dir){
                            $tmpfile = self::$rootDir."/".$dir."/".$file.".php";
                            if(file_exists($tmpfile)){
                                require_once $tmpfile;
                            }
                        }
                    }
                }
                else{
                    foreach (self::$classesDir as $dir){
                        $tmpfile = self::$rootDir."/".$dir."/".$objectFiles.".php";
                        if(file_exists($tmpfile)){
                            require_once $tmpfile;
                        }
                    }
                }
            }
        }
        else{
            $objectName = $objects;
            if(!isset($DefinedObjects[$objectName])){
                self::exception(BZHY_ERROR_NOOBJECT,"Object:".$objectName." was not found",__FILE__,__LINE__,TRUE);
            }
            
            $DefinedObject = $DefinedObjects[$objectName];
            if($DefinedObject['class'] !== BZHY_OBJECT_CLASS_EXTRA){
                self::exception(BZHY_ERROR_OBJECTNOTLOAD,"Object:".$objectName." can not load in here",__FILE__,__LINE__,TRUE);
            }
            
            $objectFiles = $DefinedObject['filename'];
            if(is_array($objectFiles)){
                foreach ($objectFiles as $file){
                    foreach (self::$classesDir as $dir){
                        $tmpfile = self::$rootDir."/".$dir."/".$file.".php";
                        if(file_exists($tmpfile)){
                            require_once $tmpfile;
                        }
                    }
                }
            }
            else{
                foreach (self::$classesDir as $dir){
                    $tmpfile = self::$rootDir."/".$dir."/".$objectFiles.".php";
                    if(file_exists($tmpfile)){
                        require_once $tmpfile;
                    }
                }
            }
        }
        
        return TRUE;
    }

    private static function findRootDir() {
        return realpath(dirname(__FILE__).'/../..');        
    }
    
    public static function getRootDir(){
        if(self::$rootDir === NULL){
            self::$rootDir = self::findRootDir();
        }
        return self::$rootDir;
    }
    
    public static function run($runMode = BZHY_RUN_MODE_DEFAULT){
        self::$rootDir = self::findRootDir();
        self::loadCommonFiles();
        self::loadCommonClassFiles();
        self::initCommonClass();
        self::$instances['base'] = new bzhyCBase($runMode);
    }

    public static function getAllObjects(){
        if(!bzhy_empty(self::$definedObjects)){
            return self::$definedObjects;
        }
        
        if(self::$rootDir === NULL){
            self::$rootDir = self::findRootDir();
        }
        
        $objectFile = self::$rootDir.self::$objectFile;
        if(file_exists($objectFile)){
            self::$definedObjects = include $objectFile;
            return self::$definedObjects;
        }
        else {
            self::exception(BZHY_ERROR_INTERNAL,"The file defined objects in was not found",__FILE__,__LINE__,TRUE);
            return FALSE;
        }
    }

    public static function exception($code=BZHY_ERROR_INTERNAL,$error='',$file='',$line='',$is_message=TRUE){
        global $BZHY_MESSAGES,$BZHY_CONFIG;
        
        if($BZHY_CONFIG['is_debug']){
            if(!bzhy_empty($file)){
                $error .= ":".$file;
            }
            if(!bzhy_empty($line)){
                $error .= $line;
            }
        }
        if($is_message){
            $BZHY_MESSAGES[] = ['code'=>$code,"message"=>$error];
        }
        throw new Exception($error,$code);
    }
    
    /* 
    * Get the instance of a object. If there is no instance of a object ,
    * then new a new instance for the object and return it  
    * @param $object string object name
    * @return the instance of a object or false
    */ 
    public static function getInstanceByObject($object,$params) {
        
        if(isset(self::$instances[$object])){
            return self::$instances[$object];
        }
        
        if(!self::initExtraClass($object, $params)){
            return FALSE;
        }
        
        return self::$instances[$object];
    }
    
    /* 
    * Get the instance of a object. If there is no instance of a object ,
    * then new a new instance for the object and return it  
    * @param $tableName string table name for a object
    * @return the instance of a object or false
    */ 
   public static function getInstanceByTable($tableName,$params=[]){
       if(bzhy_empty($tableName)){
           return NULL;
       }
       
       $AllObjects = self::getAllObjects();
       
       $foundObject ="";
       foreach ($AllObjects as $object => $definition){
           if(!bzhy_empty($definition['tablename']) && strcasecmp($tableName,$definition['tablename']) == 0){
               $foundObject = $object;
               break;
           }
       }
       
       if(bzhy_empty($foundObject)){
           return NULL;
       }
       
       return self::getInstanceByObject($foundObject, $params);
   }

   /* 
     * Get object table name by object name 
     * @param:object string object name
     * @return table name of a object or null
     */
    public static function getTableByObject($object){
        if(!bzhy_empty($object)){
            if(isset(self::$definedObjects[$object]['tablename'])){
                $ret = self::$definedObjects[$object]['tablename'];
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
        if(bzhy_empty($tableName)){
            return NULL;
        }
        
        $ret = NULL;
        foreach (self::$definedObjects as $object => $values){
            if(!bzhy_empty($values['tablename']) && strcasecmp($values['tablename'],$tableName) == 0){
                if(!bzhy_empty($values['tableAlias'])){
                    $ret = $values['tableAlias'];
                }
            }
        }
        
        $ret = bzhy_empty($ret)?$tableName:$ret;
        
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
            return NULL;
        }
        
        if(isset(self::$definedObjects[$object]['tableAlias']) 
            && !bzhy_empty(self::$definedObjects[$object]['tableAlias'])){
            $ret = self::$definedObjects[$object]['tableAlias'];
        }
        else{
            if(isset(self::$definedObjects[$object]['tablename']) && 
                !bzhy_empty(self::$definedObjects[$object]['tablename'])){
                $ret = self::$definedObjects[$object]['tablename'];
            }
            else{
                $ret = null;
            }
        } 
        
        $ret = bzhy_empty($ret)?$object:$ret;
        return $ret;
    }
    
    /*
     * Get Object title by TableName.
     * @Param $tableName string 
     * @Return If there is a object title defination for $tableName in bzhyObjectDefineFile.inc.php,
     *     then return the tobject title; or if there is not a object title defination for 
     *     $tableName in bzhyObjectDefineFile.inc.php and $tableName is string,then return NULL;
     *    Otherwise, return null    
     */
    public static function getObjectTitleByTable($tableName){
        if(bzhy_empty($tableName)){
            return NULL;
        }
        
        $ret = NULL;
        foreach (self::$definedObjects as $values){
            if(!bzhy_empty($values['tablename']) && strcasecmp($values['tablename'],$tableName) == 0){
                if(!bzhy_empty($values['objecttitle'])){
                    $ret = $values['objecttitle'];
                }
            }
        }
        
        return $ret;
    }
    
    public static function getObjectTitleByObject($ObjectName){
        if(bzhy_empty($ObjectName)){
            return NULL;
        }
        
        return isset(self::$definedObjects[$ObjectName])?self::$definedObjects[$ObjectName]['objecttitle']:NULL;

    }
    
    public static function getObjectTitleFieldByTable($tableName){
        if(bzhy_empty($tableName)){
            return NULL;
        }
        
        $ret = NULL;
        foreach (self::$definedObjects as $values){
            if(!bzhy_empty($values['tablename']) && strcasecmp($values['tablename'],$tableName) == 0){
                if(!bzhy_empty($values['objecttitlefield'])){
                    $ret = $values['objecttitlefield'];
                }
            }
        }
        
        return $ret;
    }
    
    public static function getObjectTitleFieldByObject($ObjectName){
        if(bzhy_empty($ObjectName)){
            return NULL;
        }
        
        return isset(self::$definedObjects[$ObjectName])?self::$definedObjects[$ObjectName]['objecttitlefield']:NULL;

    }
    
    public static function getFieldsLabelByObject($ObjectName=null,$fields=array(),$withOutFields=array()){
        $labels = [];
        if(zbx_empty($ObjectName)){
            return $labels;
        }
        
        $LabelsDefinedFile = self::findRootDir()."/".self::$ObjectLabelDefined;
        
        if(file_exists($LabelsDefinedFile)){
            $DefinedLabels = include $LabelsDefinedFile;
        }
        else {
            return $labels;
        }
        
        if(!isset($DefinedLabels[$ObjectName])){
            return $labels;
        }
        $DefinedLabels = $DefinedLabels[$ObjectName];
        
        if(zbx_empty($fields)){
            $fields = bzhyCDB::getFields(self::getTableByObject($ObjectName));
            if(bzhy_empty($fields)){
                return $labels;
            }
        }
        
        $fieldKeys = array_keys($fields);
        
        if(!zbx_empty($withOutFields)){
            $fieldKeys = array_diff($fieldKeys, $withOutFields);
        }

        foreach ($fieldKeys as $field){
           if(isset($DefinedLabels[$field]) && !bzhy_empty($DefinedLabels[$field])){
                $labels[$field] = $DefinedLabels[$field];
            }
        }
        
        return $labels;
    }
    
    public static function getFieldsLabelByTable($TableName=null,$fields=array(),$withOutFields=array()){
        $labels = [];
        if(zbx_empty($TableName)){
            return $labels;
        }
        
        $objectName = null;
        foreach (self::$definedObjects as $object => $values){
            if(!bzhy_empty($values['tablename']) && strcasecmp($values['tablename'],$TableName) == 0){
                $objectName = $object;
            }
        }
        
        if(bzhy_empty($objectName)){
            return $labels;
        }
        
        return self::getFieldsLabelByObject($objectName,$fields,$withOutFields);
        
    }
    
    public static function getUriByObject($ObjectName=null){
        $uri = null;
        if(zbx_empty($ObjectName)){
            return $uri;
        }
        
        $definedObjects = self::$definedObjects;
        if(isset($definedObjects[$ObjectName]) && isset($definedObjects[$ObjectName]['uri'])){
            $uri = $definedObjects[$ObjectName]['uri'];
        }
        
        return $uri;
    }
    
    public static function getUriByTable($TableName=null){
        $uri = null;
        
        if(zbx_empty($TableName)){
            return $uri;
        }
        
        $objectName = null;
        foreach (self::$definedObjects as $object => $values){
            if(!bzhy_empty($values['tablename']) && strcasecmp($values['tablename'],$TableName) == 0){
                $objectName = $object;
            }
        }
        
        return self::getUriByObject($objectName);
    }
}

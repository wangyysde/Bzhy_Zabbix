<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

class CBase {
    protected $require_files = [];
    private  $common_files = ['views/CNewView.php',
           'newinclude/defines.inc.php',
           'newinclude/func.inc.php',
           'classes/NewDB.php',
           'classes/CSysSet.php',
           'newapi/CApiService.php'];
    
    protected $rootDir;
    
    protected static $objects = [];
    protected static $instances = [];
    
    public function __construct() {
        $this->rootDir = $this->findRootDir();
        $i=0;
        foreach ($this->common_files as $file){
            $file = $this->rootDir.'/'.$file;
            $this->require_files[$i] = $file; 
            $i++;
        }
        
    }
    
    private function findRootDir() {
        return realpath(dirname(__FILE__).'/..');        
    }
    
    public function addFiles(array $files){
        $num = count($this->require_files);
        foreach ($files as $file){
            $file = $this->rootDir.'/'.$file;
            $this->require_files[$num] = $file; 
            $num++;
        }
    }
    
    public function loadFiles(){
        foreach ($this->require_files as $file){
            require_once $file;
        }
        $csysSet = new CSysSet();
        if(!$csysSet->existSettingfile()){
            $csysSet->updateSettingsFile();
        }
        
        require_once $csysSet->getSettingfile();
    }
    
    public static function getObject($object) {
        if (!isset(self::$instances[$object])) {
            $definition = self::$objects[$object];
            self::$instances[$object] = ($definition instanceof Closure) ? $definition() : new $definition();
        }

        return self::$instances[$object];
  
    }
    
    public static function addObjects(array $objects){
        self::$objects = array_merge(self::$objects,$objects);
    }
}
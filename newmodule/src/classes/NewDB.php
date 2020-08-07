<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

class NewDB extends DB{
    /**
         * 
         * The following has been added by Wayne Wang
         */
        
        public static function getFieldsLabel($table=null,$fields=array(),$withOutFields=array()){
            $labels = [];
            if(zbx_empty($table)){
                return $labels;
            }
            $table_schema = self::getSchema($table);
            if(zbx_empty($fields)){
                foreach ($table_schema['fields'] as $key => $value){
                    $fields[$key] = $key;
                }
            }
            if(!zbx_empty($withOutFields)){
                $fields = array_diff($fields, $withOutFields);
            }
            foreach ($fields as $key=>$field){
                if(isset($table_schema['fields'][$field]['label']) && !is_null($table_schema['fields'][$field]['label'])){
                    $labels[$field] = $table_schema['fields'][$field]['label'];
                }
            }
            return $labels;
        }
        
        public static function getObjectUrl($table=null){
            if(zbx_empty($table)){
                return NULL;
            }
            $table_schema = self::getSchema($table);
            if(isset($table_schema['object_url'])){
                return $table_schema['object_url'].".php";
            }
            return NULL;
        }
        
        public static function getObjectName($table=null){
            if(zbx_empty($table)){
                return NULL;
            }
            $table_schema = self::getSchema($table);
            if(isset($table_schema['object_url'])){
                return $table_schema['object_url'].".php";
            }
            return NULL;
        }
}
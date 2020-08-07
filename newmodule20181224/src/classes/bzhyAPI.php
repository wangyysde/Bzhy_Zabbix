<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


class bzhyAPI {
    
     /*
     * Record the object name to its table files  map
     * This variable is a array, keys are object name and 
     * values(is a array) are the table fields of the object.Format of value are:
     * value['id']: table primary field name
     * value['Fields'][]: table fileds list.And the formation of its are:
     * Fields['name']: filed name
     * Fields['type']: Field type DATABASE_FIELD_TYPE_STRING or DATABASE_FIELD_TYPE_NUMBER 
     * Fields['length']:Field lenght
     * Fields['Null']: Flag of directing the filed allowed to fill with null.
     * Fields['default']: The default value of the field.
     * 
     */
    protected static $Object2Fields =  [];
    
    /*
     * Record the object name to its table
     * This variable is a array, keys are object name and
     * values are the table name
     */
    protected static $Object2Table = ['host'=>"hosts"];
    
    
    /*
     * Get the host group instance;
     */
    public static function HostGroup(){
        return bzhyCBase::getObject('hostgroup');
    }
    
    /*
     * Get the host instance;
     */
    public static function Host(){
        return bzhyCBase::getObject('host');
    }
    
    /**
    * @return CTemplate
    */
    public static function Template() {
        return bzhyCBase::getObject('template');
    }
    
    /**
    * @return bzhyCDevice
    */
    public static function Device() {
        return bzhyCBase::getObject('device');
    }
    
    /**
    * @return bzhyCIdc
    */
    public static function Idc_room() {
        return bzhyCBase::getObject('idc_room');
    }
    
    public static function Idc_box() {
        return bzhyCBase::getObject('idc_box');
    }
    
    public static function File() {
        return bzhyCBase::getObject('file');
    }
    
    public static function Contact() {
        return bzhyCBase::getObject('contact');
    }
    
    public static function View($template_file,$data) {
        return bzhyCBase::getObject('view',$template_file,$data);
    }
    
    public static function Proxy() {
        return bzhyCBase::getObject('proxy');
    }
    
    public static function HostInterface() {
        return bzhyCBase::getObject('hostinterface');
    }
    
    public static function UserGroup() {
        return bzhyCBase::getObject('usergroup');
    }
    
    public static function RelationMap() {
        return bzhyCBase::getObject('relationmap');
    }
    
    public static function UserMedia() {
        return bzhyCBase::getObject('usermedia');
    }
    
    public static function Mediatype() {
        return bzhyCBase::getObject('mediatype');
    }
    
    public static function PageFilter($options) {
        return bzhyCBase::getObject('pagefilter',$options);
    }
    
    public static function Url() {
        return bzhyCBase::getObject('url');
    }
    
    public static function Graph() {
        return bzhyCBase::getObject('graph');
    }
}

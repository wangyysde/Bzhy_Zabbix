<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
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
    
    /*
     * Get the host group instance;
     */
    public static function HostGroup(){
        return bzhyCBase::getInstanceByObject('hostgroup',[]);
    }
    
    public static function HostGroupUri(){
        return bzhyCBase::getUriByObject('hostgroup');
    }
    
    /*
     * Get the host instance;
     */
    public static function Host(){
        return bzhyCBase::getInstanceByObject('host',[]);
    }
    
    public static function HostUri(){
        return bzhyCBase::getUriByObject('host');
    }
    
    /**
    * @return CTemplate
    */
    public static function Template() {
        return bzhyCBase::getInstanceByObject('template',[]);
    }
    
    public static function TemplateUri(){
        return bzhyCBase::getUriByObject('template');
    }
    
    /**
    * @return bzhyCIdc
    */
    public static function IdcRoom() {
        return bzhyCBase::getInstanceByObject('idc_room',[]);
    }
    
    public static function IdcRoomUri(){
        return bzhyCBase::getUriByObject('idc_room');
    }
    
    public static function IdcBox() {
        return bzhyCBase::getInstanceByObject('idc_box',[]);
    }
    
    public static function IdcBoxUri(){
        return bzhyCBase::getUriByObject('idc_box');
    }
    
    public static function File() {
        return bzhyCBase::getInstanceByObject('file',[]);
    }
    
    public static function FileUri(){
        return bzhyCBase::getUriByObject('file');
    }
    
    public static function Contact() {
        return bzhyCBase::getInstanceByObject('contact',[]);
    }
     
    public static function ContactUri(){
        return bzhyCBase::getUriByObject('contact');
    }
    
    public static function Proxy() {
        return bzhyCBase::getInstanceByObject('proxy',[]);
    }
    
    public static function ProxyUri(){
        return bzhyCBase::getUriByObject('proxy');
    }
    
    public static function HostInterface() {
        return bzhyCBase::getInstanceByObject('interface',[]);
    }
    
    public static function HostInterfaceUri(){
        return bzhyCBase::getUriByObject('interface');
    }
    
    public static function UserGroup() {
        return bzhyCBase::getInstanceByObject('usergroup',[]);
    }
    
    public static function UserGroupUri(){
        return bzhyCBase::getUriByObject('usergroup');
    }
    
    public static function RelationMap() {
        return bzhyCBase::getInstanceByObject('relationmap',[]);
    }
    
    public static function RelationMapUri(){
        return bzhyCBase::getUriByObject('relationmap');
    }
    
    public static function UserMedia() {
        return bzhyCBase::getInstanceByObject('usermedia',[]);
    }
    
    public static function UserMediaUri(){
        return bzhyCBase::getUriByObject('usermedia');
    }
    
    public static function Mediatype() {
        return bzhyCBase::getInstanceByObject('mediatype',[]);
    }
    
    public static function MediatypeUri(){
        return bzhyCBase::getUriByObject('mediatype');
    }
    
    public static function PageFilter($options) {
        return bzhyCBase::getInstanceByObject('pagefilter',$options);
    }

    public static function PageFilterUri(){
        return bzhyCBase::getUriByObject('pagefilter');
    }
    
    public static function Graph() {
        return bzhyCBase::getInstanceByObject('graph',[]);
    }
    
    public static function GraphUri(){
        return bzhyCBase::getUriByObject('graph');
    }
    
    public static function Group() {
        return bzhyCBase::getInstanceByObject('group',[]);
    }
    
    public static function GroupUri(){
        return bzhyCBase::getUriByObject('group');
    }
}

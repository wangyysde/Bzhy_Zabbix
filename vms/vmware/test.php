<?php
require_once "Bootstrap.php";
$vhost = new \Vmwarephp\Vhost('172.16.0.253:443', 'root', '&fZ6M5qZcW#nnhH2');
//$vhost = new Vhost('192.168.201.222:443', 'root', 'password');
//$virtualMachines = $vhost->findAllManagedObjects('VirtualMachine', array('configStatus'));
$virtualMachines = $vhost->findAllManagedObjects('VirtualMachine', 'all');
//$virtualMachines = $vhost->findManagedObjectByName('VirtualMachine', '201.25-ZABBIX-xin', array('configStatus'));
//  $virtualMachine = $vhost->findOneManagedObject('VirtualMachine', '201.25-ZABBIX-xin', array());
//  $configStatus = $virtualMachine->configStatus;
//    $virtualMachine = $vhost->findOneManagedObject('VirtualMachine', '201.25-ZABBIX-xin', array('configStatus'));
echo "<pre>";
//echo $configStatus;
var_dump($virtualMachines);

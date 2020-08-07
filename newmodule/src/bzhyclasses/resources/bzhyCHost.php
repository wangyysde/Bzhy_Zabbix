<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


/**
 * Class containing methods for operations with hosts.
 */                         
class bzhyCHost extends bzhyObjectCommon {
          
    protected $ObjectName='host';
    
    protected $HostTypeTable = 'bzhy_hosttype';
    
    protected $HostTypeTableAlias = 'ht';
    
    protected $OsTable = 'bzhy_os';
    
    protected $OsTableAlias = 'os';

    protected $brandTable ='bzhy_brand';
    
    protected $brandTableAlias ='bd';

    protected $inventoryTable = 'bzhy_host_inventory';
    
    protected $inventoryTableAlias = 'bhi';
    
    protected $interfaceTable = 'bzhy_interfaces';
    
    protected $interfaceTableAlias = 'bif';
    
    protected $hostgroupTable = 'hosts_groups';
    
    protected $hostgroupTableAlias= 'hg';
    
    protected $ipTable = 'bzhy_ips';
    
    protected $ipTableAlias = 'bip';

    protected $sortColumns = ['hostid', 'host', 'name', 'status'];

    public $HostSize = ['1'=>'1U','2'=>'2U','3'=>'3U','4'=>'4U','6'=>'6U','8'=>'8U','0'=>'Other'];
    
    /**
    * Get host data.
    *
    * @param array         $options
    * @param array         $options['groupids']                 HostGroup IDs
    * @param array         $options['hostids']                  Host IDs
    * @param boolean       $options['monitored_hosts']          only monitored Hosts
    * @param boolean       $options['templated_hosts']          include templates in result
    * @param boolean       $options['with_items']               only with items
    * @param boolean       $options['with_monitored_items']     only with monitored items
    * @param boolean       $options['with_triggers']            only with triggers
    * @param boolean       $options['with_monitored_triggers']  only with monitored triggers
    * @param boolean       $options['with_httptests']           only with http tests
    * @param boolean       $options['with_monitored_httptests'] only with monitored http tests
    * @param boolean       $options['with_graphs']              only with graphs
    * @param boolean       $options['editable']                 only with read-write permission. Ignored for SuperAdmins
    * @param boolean       $options['selectGroups']             select HostGroups
    * @param boolean       $options['selectItems']              select Items
    * @param boolean       $options['selectTriggers']           select Triggers
    * @param boolean       $options['selectGraphs']             select Graphs
    * @param boolean       $options['selectApplications']       select Applications
    * @param boolean       $options['selectMacros']             select Macros
    * @param boolean|array $options['selectInventory']          select Inventory
    * @param boolean       $options['withInventory']            select only hosts with inventory
    * @param int           $options['count']                    count Hosts, returned column name is rowscount
    * @param string        $options['pattern']                  search hosts by pattern in Host name
    * @param string        $options['extendPattern']            search hosts by pattern in Host name, ip and DNS
    * @param int           $options['limit']                    limit selection
    * @param string        $options['sortfield']                field to sort by
    * @param string        $options['sortorder']                sort order
    *
    * @return array|boolean Host data as array or false if error
    */
    public function get($options = []) {
	$result = [];
                        
	$sqlParts = [
            'select'	=> [ bzhyCBase::getTableByObject($this->ObjectName) => bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid')],
            'from'		=> [bzhyCDB::getFromByObject($this->ObjectName)],
            'where'		=> ['flags' =>  bzhyCDB::getFieldIdByObject($this->ObjectName,'flags').' IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'],
            'group'		=> [],
            'order'		=> [],
            'limit'		=> null
	];

	$defOptions = [
            'groupids'					=> null,
            'hostids'					=> null,
            'proxyids'					=> null,
            'templateids'				=> null,
            'interfaceids'				=> null,
            'itemids'					=> null,
            'triggerids'				=> null,
            'maintenanceids'			=> null,
            'graphids'					=> null,
            'applicationids'			=> null,
            'dserviceids'				=> null,
            'httptestids'				=> null,
            'monitored_hosts'			=> null,
            'templated_hosts'			=> null,
            'proxy_hosts'				=> null,
            'with_items'				=> null,
            'with_monitored_items'		=> null,
            'with_simple_graph_items'	=> null,
            'with_triggers'				=> null,
            'with_monitored_triggers'	=> null,
            'with_httptests'			=> null,
            'with_monitored_httptests'	=> null,
            'with_graphs'				=> null,
            'with_applications'			=> null,
            'withInventory'				=> null,
            'editable'					=> null,
            'nopermissions'				=> null,
            // filter
            'filter'					=> null,
            'search'					=> null,
            'searchInventory'			=> null,
            'searchByAny'				=> null,
            'startSearch'				=> null,
            'excludeSearch'				=> null,
            'searchWildcardsEnabled'	=> null,
            // output
            'output'					=> API_OUTPUT_EXTEND,
            'selectGroups'				=> null,
            'selectParentTemplates'		=> null,
            'selectItems'				=> null,
            'selectDiscoveries'			=> null,
            'selectTriggers'			=> null,
            'selectGraphs'				=> null,
            'selectApplications'		=> null,
            'selectMacros'				=> null,
            'selectScreens'				=> null,
            'selectInterfaces'			=> null,
            'selectInventory'			=> null,
            'selectHttpTests'           => null,
            'selectDiscoveryRule'		=> null,
            'selectHostDiscovery'		=> null,
            'countOutput'				=> null,
            'groupCount'				=> null,
            'preservekeys'				=> null,
            'sortfield'					=> '',
            'sortorder'					=> '',
            'limit'						=> null,
            'limitSelects'				=> null
	];
	$options = zbx_array_merge($defOptions, $options);

        // hostids
	if (!is_null($options['hostids'])) {
            zbx_value2array($options['hostids']);
            $sqlParts['where']['hostid'] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid'), $options['hostids']);
	}

	// groupids
	if (!is_null($options['groupids'])) {
            zbx_value2array($options['groupids']);

            $sqlParts['from'][$this->hostgroupTable] = $this->hostgroupTable.' '.$this->hostgroupTableAlias;
            $sqlParts['where'][] = dbConditionInt('hg.groupid', $options['groupids']);
            $sqlParts['where']['hgh'] = bzhyCDB::buildFieldId($this->hostgroupTableAlias,'hostid').'='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');

            if (!is_null($options['groupCount'])) {
		$sqlParts['group']['groupid'] = bzhyCDB::buildFieldId($this->hostgroupTableAlias,'groupid');
            }
	}

	// proxyids
	if (!is_null($options['proxyids'])) {
            zbx_value2array($options['proxyids']);
            $sqlParts['where'][] = dbConditionInt(bzhyCDB::getFieldIdByObject($this->ObjectName, 'proxy_hostid'), $options['proxyids']);
	}

	// templateids
	if (!is_null($options['templateids'])) {
            zbx_value2array($options['templateids']);
            $sqlParts['from']['hosts_templates'] = 'hosts_templates ht';
            $sqlParts['where'][] = dbConditionInt('ht.templateid', $options['templateids']);
            $sqlParts['where']['hht'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=ht.hostid';
            if (!is_null($options['groupCount'])) {
		$sqlParts['group']['templateid'] = 'ht.templateid';
            }
	}

	// interfaceids
	if (!is_null($options['interfaceids'])) {
            zbx_value2array($options['interfaceids']);
            $sqlParts['from']['interface'] = 'interface hi';
            $sqlParts['where'][] = dbConditionInt('hi.interfaceid', $options['interfaceids']);
            $sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=hi.hostid';
	}

	// itemids
	if (!is_null($options['itemids'])) {
            zbx_value2array($options['itemids']);
            $sqlParts['from']['items'] = 'items i';
            $sqlParts['where'][] = dbConditionInt('i.itemid', $options['itemids']);
            $sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid';
	}

	// triggerids
	if (!is_null($options['triggerids'])) {
            zbx_value2array($options['triggerids']);

            $sqlParts['from']['functions'] = 'functions f';
            $sqlParts['from']['items'] = 'items i';
            $sqlParts['where'][] = dbConditionInt('f.triggerid', $options['triggerids']);
            $sqlParts['where']['hi'] = 'h.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');
            $sqlParts['where']['fi'] = 'f.itemid=i.itemid';
	}

	// httptestids
	if (!is_null($options['httptestids'])) {
            zbx_value2array($options['httptestids']);

            $sqlParts['from']['httptest'] = 'httptest ht';
            $sqlParts['where'][] = dbConditionInt('ht.httptestid', $options['httptestids']);
            $sqlParts['where']['aht'] = 'ht.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');
	}

	// graphids
	if (!is_null($options['graphids'])) {
            zbx_value2array($options['graphids']);

            $sqlParts['from']['graphs_items'] = 'graphs_items gi';
            $sqlParts['from']['items'] = 'items i';
            $sqlParts['where'][] = dbConditionInt('gi.graphid', $options['graphids']);
            $sqlParts['where']['igi'] = 'i.itemid=gi.itemid';
            $sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid';
	}

	// applicationids
	if (!is_null($options['applicationids'])) {
            zbx_value2array($options['applicationids']);

            $sqlParts['from']['applications'] = 'applications a';
            $sqlParts['where'][] = dbConditionInt('a.applicationid', $options['applicationids']);
            $sqlParts['where']['ah'] = 'a.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');
	}

	// dserviceids
	if (!is_null($options['dserviceids'])) {
            zbx_value2array($options['dserviceids']);

            $sqlParts['from']['dservices'] = 'dservices ds';
            $sqlParts['from']['interface'] = 'interface i';
            $sqlParts['where'][] = dbConditionInt('ds.dserviceid', $options['dserviceids']);
            $sqlParts['where']['dsh'] = 'ds.ip=i.ip';
            $sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid';

            if (!is_null($options['groupCount'])) {
		$sqlParts['group']['dserviceid'] = 'ds.dserviceid';
            }
	}

	// maintenanceids
	if (!is_null($options['maintenanceids'])) {
            zbx_value2array($options['maintenanceids']);

            $sqlParts['from']['maintenances_hosts'] = 'maintenances_hosts mh';
            $sqlParts['where'][] = dbConditionInt('mh.maintenanceid', $options['maintenanceids']);
            $sqlParts['where']['hmh'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=mh.hostid';

            if (!is_null($options['groupCount'])) {
		$sqlParts['group']['maintenanceid'] = 'mh.maintenanceid';
            }
	}

	// monitored_hosts, templated_hosts
	if (!is_null($options['monitored_hosts'])) {
            $sqlParts['where']['status'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'status').'='.HOST_STATUS_MONITORED;
	}
	elseif (!is_null($options['templated_hosts'])) {
            $sqlParts['where']['status'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'status').' IN ('.HOST_STATUS_MONITORED.','.HOST_STATUS_NOT_MONITORED.','.HOST_STATUS_TEMPLATE.')';
	}
	elseif (!is_null($options['proxy_hosts'])) {
            $sqlParts['where']['status'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'status').' IN ('.HOST_STATUS_PROXY_ACTIVE.','.HOST_STATUS_PROXY_PASSIVE.')';
	}
	else {
            $sqlParts['where']['status'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'status').' IN ('.HOST_STATUS_MONITORED.','.HOST_STATUS_NOT_MONITORED.')';
	}

	// with_items, with_monitored_items, with_simple_graph_items
	if (!is_null($options['with_items'])) {
        $sqlParts['where'][] = 'EXISTS ('.
		'SELECT NULL'.
		' FROM items i'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid'.
		' AND i.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
		')';
	}
	elseif (!is_null($options['with_monitored_items'])) {
        $sqlParts['where'][] = 'EXISTS ('.
		'SELECT NULL'.
		' FROM items i'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid'.
		' AND i.status='.ITEM_STATUS_ACTIVE.
		' AND i.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
                ')';
            }
    elseif (!is_null($options['with_simple_graph_items'])) {
            $sqlParts['where'][] = 'EXISTS ('.
		'SELECT NULL'.
		' FROM items i'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid'.
		' AND i.value_type IN ('.ITEM_VALUE_TYPE_FLOAT.','.ITEM_VALUE_TYPE_UINT64.')'.
		' AND i.status='.ITEM_STATUS_ACTIVE.
		' AND i.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
                ')';
	}

	// with_triggers, with_monitored_triggers
	if (!is_null($options['with_triggers'])) {
            $sqlParts['where'][] = 'EXISTS ('.
                'SELECT NULL'.
		' FROM items i,functions f,triggers t'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid'.
		' AND i.itemid=f.itemid'.
		' AND f.triggerid=t.triggerid'.
		' AND t.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
		')';
	}
	elseif (!is_null($options['with_monitored_triggers'])) {
            $sqlParts['where'][] = 'EXISTS ('.
                'SELECT NULL'.
		' FROM items i,functions f,triggers t'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=i.hostid'.
		' AND i.itemid=f.itemid'.
		' AND f.triggerid=t.triggerid'.
		' AND i.status='.ITEM_STATUS_ACTIVE.
		' AND t.status='.TRIGGER_STATUS_ENABLED.
		' AND t.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
		')';
	}

	// with_httptests, with_monitored_httptests
	if (!empty($options['with_httptests'])) {
            $sqlParts['where'][] = 'EXISTS (SELECT NULL FROM httptest ht WHERE ht.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').')';
	}
	elseif (!empty($options['with_monitored_httptests'])) {
            $sqlParts['where'][] = 'EXISTS ('.
		'SELECT NULL'.
		' FROM httptest ht'.
		' WHERE '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=ht.hostid'.
		' AND ht.status='.HTTPTEST_STATUS_ACTIVE.
		')';
	}

	// with_graphs
	if (!is_null($options['with_graphs'])) {
            $sqlParts['where'][] = 'EXISTS ('.
		'SELECT NULL'.
		' FROM items i,graphs_items gi,graphs g'.
		' WHERE i.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').
		' AND i.itemid=gi.itemid '.
		' AND gi.graphid=g.graphid'.
		' AND g.flags IN ('.ZBX_FLAG_DISCOVERY_NORMAL.','.ZBX_FLAG_DISCOVERY_CREATED.')'.
		')';
	}

	// with applications
	if (!is_null($options['with_applications'])) {
            $sqlParts['from']['applications'] = 'applications a';
            $sqlParts['where'][] = 'a.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');
	}

	// withInventory
	if (!is_null($options['withInventory']) && $options['withInventory']) {
            $sqlParts['where'][] = ' '.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').' IN ('.
		' SELECT hin.hostid'.
		' FROM host_inventory hin'.
            ')';
	}

	// search
	if (is_array($options['search'])) {
            zbx_db_search(bzhyCDB::getFromByObject($this->ObjectName), $options, $sqlParts);
            if (zbx_db_search('interface hi', $options, $sqlParts)) {
				$sqlParts['from']['interface'] = 'interface hi';
				$sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=hi.hostid';
			}
	}

	// search inventory
	if ($options['searchInventory'] !== null) {
            $sqlParts['from']['host_inventory'] = 'host_inventory hii';
            $sqlParts['where']['hii'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=hii.hostid';
            zbx_db_search('host_inventory hii',
                [
                    'search' => $options['searchInventory'],
                    'startSearch' => $options['startSearch'],
                    'excludeSearch' => $options['excludeSearch'],
                    'searchWildcardsEnabled' => $options['searchWildcardsEnabled'],
                    'searchByAny' => $options['searchByAny']
                ],
                $sqlParts
            );
	}

	// filter
	if (is_array($options['filter'])) {
            bzhyCDB::dbFilter(bzhyCDB::getFromByObject($this->ObjectName), $options, $sqlParts);

            if (bzhyCDB::dbFilter('interface hi', $options, $sqlParts)) {
				$sqlParts['from']['interface'] = 'interface hi';
				$sqlParts['where']['hi'] = bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid').'=hi.hostid';
			}
	}

	// limit
	if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
	}

	$sqlParts = bzhyCDB::applyQueryOutputOptions(bzhyCBase::getTableByObject($this->ObjectName), bzhyCBase::getTableAliasByObject($this->ObjectName), $options, $sqlParts);
	$sqlParts = bzhyCDB::applyQuerySortOptions(bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid'), bzhyCBase::getTableAliasByObject($this->ObjectName), $options, $sqlParts);
	$res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
	while ($host = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $host;
		}
		else {
                    $result = $host['rowscount'];
		}
            }
            else {
		$result[$host['hostid']] = $host;
            }
	}

	if (!is_null($options['countOutput'])) {
            return $result;
	}

	if ($result) {
            $result = $this->addRelatedObjects($options, $result);
	}

	// removing keys (hash -> array)
	if (is_null($options['preservekeys'])) {
            $result = zbx_cleanHashes($result);
	}

	return $result;
    }

    /**
    * Add host.
    *
    * @param array  $hosts									An array with hosts data.
    * @param string $hosts[]['host']						Host technical name.
    * @param string $hosts[]['name']						Host visible name (optional).
    * @param array  $hosts[]['groups']						An array of host group objects with IDs that host will be added to.
    * @param int    $hosts[]['status']						Status of the host (optional).
    * @param array  $hosts[]['interfaces']					An array of host interfaces data.
    * @param int    $hosts[]['interfaces']['type']			Interface type.
    * @param int    $hosts[]['interfaces']['main']			Is this the default interface to use.
    * @param string $hosts[]['interfaces']['ip']			Interface IP (optional).
    * @param int    $hosts[]['interfaces']['port']			Interface port (optional).
    * @param int    $hosts[]['interfaces']['useip']		Interface shoud use IP (optional).
    * @param string $hosts[]['interfaces']['dns']			Interface shoud use DNS (optional).
    * @param int    $hosts[]['interfaces']['bulk']			Use bulk requests for interface (optional).
    * @param int    $hosts[]['proxy_hostid']				ID of the proxy that is used to monitor the host (optional).
    * @param int    $hosts[]['ipmi_authtype']				IPMI authentication type (optional).
    * @param int    $hosts[]['ipmi_privilege']				IPMI privilege (optional).
    * @param string $hosts[]['ipmi_username']				IPMI username (optional).
    * @param string $hosts[]['ipmi_password']				IPMI password (optional).
    * @param array  $hosts[]['inventory']					An array of host inventory data (optional).
    * @param array  $hosts[]['macros']						An array of host macros (optional).
    * @param string $hosts[]['macros'][]['macro']			Host macro (required if "macros" is set).
    * @param array  $hosts[]['templates']					An array of template objects with IDs that will be linked to host (optional).
    * @param string $hosts[]['templates'][]['templateid']	Template ID (required if "templates" is set).
    * @param string $hosts[]['tls_connect']				Connections to host (optional).
    * @param string $hosts[]['tls_accept']					Connections from host (optional).
    * @param string $hosts[]['tls_psk_identity']			PSK identity (required if "PSK" type is set).
    * @param string $hosts[]['tls_psk']					PSK (required if "PSK" type is set).
    * @param string $hosts[]['tls_issuer']					Certificate issuer (optional).
    * @param string $hosts[]['tls_subject']				Certificate subject (optional).
    *
    * @return array
    */
    public function create($hosts) {
        try {
            $hosts = zbx_toArray($hosts);   
            $this->validateCreate($hosts);
            $hostids = [];

            foreach ($hosts as $host) {
                // If visible name is not given or empty it should be set to host name.
                if (!array_key_exists('name', $host) || !trim($host['name'])) {
                    $host['name'] = $host['host'];
                }

                // Clean PSK fields.
                if ((array_key_exists('tls_connect', $host) && $host['tls_connect'] != HOST_ENCRYPTION_PSK)
                        && (array_key_exists('tls_accept', $host)
                        && ($host['tls_accept'] & HOST_ENCRYPTION_PSK) != HOST_ENCRYPTION_PSK)) {
                    $host['tls_psk_identity'] = '';
                    $host['tls_psk'] = '';
                }

                // Clean certificate fields.
                if ((array_key_exists('tls_connect', $host) && $host['tls_connect'] != HOST_ENCRYPTION_CERTIFICATE)
                        && (array_key_exists('tls_accept', $host)
                        && ($host['tls_accept'] & HOST_ENCRYPTION_CERTIFICATE) != HOST_ENCRYPTION_CERTIFICATE)) {
                    $host['tls_issuer'] = '';
                    $host['tls_subject'] = '';
                }
                $hostid = bzhyCDB::insert(bzhyCBase::getTableByObject($this->ObjectName), [$host]);
                $hostid = reset($hostid);
                $host['hostid'] = $hostid;
                $hostids[] = $hostid;
                // Save groups. Groups must be added before calling massAdd() for permission validation to work.
                $groupsToAdd = [];
                foreach ($host['groups'] as $group) {
                    $groupsToAdd[] = [
                        'hostid' => $hostid,
                        'groupid' => $group['groupid']
                    ];
                }
                bzhyCDB::insert('hosts_groups', $groupsToAdd);
                $options = [
                    'hosts' => $host
                ];

                if (isset($host['templates']) && !is_null($host['templates'])) {
                    $options['templates'] = $host['templates'];
                }

                if (isset($host['macros']) && !is_null($host['macros'])) {
                    $options['macros'] = $host['macros'];
                }

                if (isset($host['bzhyinterfaces']) && !is_null($host['bzhyinterfaces'])) {
                    $options['bzhyinterfaces'] = $host['bzhyinterfaces'];
                }   
                $result = $this->massAdd($options);
                if (!$result) {
                    bzhyCBase::exception(ZBX_API_ERROR_INTERNAL,_('Create host error'),__FILE__,__LINE__,TRUE);
                }
                
                $bzhyInventory = $host['bzhyinventory'];
                if(!zbx_empty($bzhyInventory['gw'])){
                    $gws = explode(';',$bzhyInventory['gw'] );
                    foreach ($gws as $gw){
                        $ips['hostid'] =  $hostid;
                        $ips['type'] = BZHYHOST_IP_TYPE_GW;
                        $ips['family'] = BZHYHOST_IP_FAMILY_VER4;
                        $ips['ip'] = $gw;
                        $ipid = bzhyCDB::insert($this->ipTable, [$ips]);
                    }
                }
                        
                if(!zbx_empty($inventoryip['dns'])){
                    $dnss = explode(';',$inventoryip['dns'] );
                    foreach ($dnss as $dns){
                        $ips['hostid'] =  $hostid;
                        $ips['type'] = BZHYHOST_IP_TYPE_DNS;
                        $ips['family'] = BZHYHOST_IP_FAMILY_VER4;
                        $ips['ip'] = $dns;
                        $ips['description'] = '';
                        $ipid = bzhyCDB::insert($this->ipTable, [$ips]);
                    }
                }
                        
                $bzhyinventory = $host['bzhyinventory'];
                $bzhyinventory['hostid'] = $hostid;
                $bzhyinventoryid = bzhyCDB::insert($this->inventoryTable, [$bzhyinventory]);
                $bzhyinventoryid = reset($bzhyinventoryid);
               
                $files = $bzhyinventory['files'];
                if(!zbx_empty($files)){
                    $relatedFile['FileIds'] = $files;
                    $relatedFile['RelatedTable'] = $this->tableName;
                    $relatedFile['RelatedField'] = $this->pkField;
                    $relatedFile['RelatedValue'] = $hostid;
                    $cFile = bzhyApi::File();
                    $cFile->data = $relatedFile;
                    if(!$cFile->insertRelatedObjects()){
                        bzhyCBase::exception(ZBX_API_ERROR_INTERNAL,_('Add related files error!'),__FILE__,__LINE__,TRUE);
                    }
                }
                        
                unset($relatedFile);
                $contacts = $bzhyinventory['contacts'];
                if(!zbx_empty($contacts)){
                    $relatedContact['ContactIds'] = $contacts;
                    $relatedContact['RelatedTable'] = $this->tableName;
                    $relatedContact['RelatedField'] = $this->pkField;
                    $relatedContact['RelatedValue'] = $hostid;
                    $cContact = bzhyApi::Contact();
                    $cContact->data = $relatedContact;
                    if(!$cContact->insertRelatedObjects()){
                        bzhyCBase::exception(ZBX_API_ERROR_INTERNAL,_('Add related contacts error!'),__FILE__,__LINE__,TRUE);
                    }
                }
            }
             
            return ['hostids' => $hostids];
        } catch (Exception $ex) {
            bzhyCBase::exception($ex->getCode(),$ex->getMessage(),__FILE__,__LINE__,TRUE);
        }
       
    }

	/**
	 * Update host.
	 *
	 * @param array  $hosts											An array with hosts data.
	 * @param string $hosts[]['hostid']								Host ID.
	 * @param string $hosts[]['host']								Host technical name (optional).
	 * @param string $hosts[]['name']								Host visible name (optional).
	 * @param array  $hosts[]['groups']								An array of host group objects with IDs that host will be replaced to.
	 * @param int    $hosts[]['status']								Status of the host (optional).
	 * @param array  $hosts[]['interfaces']							An array of host interfaces data to be replaced.
	 * @param int    $hosts[]['interfaces']['type']					Interface type.
	 * @param int    $hosts[]['interfaces']['main']					Is this the default interface to use.
	 * @param string $hosts[]['interfaces']['ip']					Interface IP (optional).
	 * @param int    $hosts[]['interfaces']['port']					Interface port (optional).
	 * @param int    $hosts[]['interfaces']['useip']				Interface shoud use IP (optional).
	 * @param string $hosts[]['interfaces']['dns']					Interface shoud use DNS (optional).
	 * @param int    $hosts[]['interfaces']['bulk']					Use bulk requests for interface (optional).
	 * @param int    $hosts[]['proxy_hostid']						ID of the proxy that is used to monitor the host (optional).
	 * @param int    $hosts[]['ipmi_authtype']						IPMI authentication type (optional).
	 * @param int    $hosts[]['ipmi_privilege']						IPMI privilege (optional).
	 * @param string $hosts[]['ipmi_username']						IPMI username (optional).
	 * @param string $hosts[]['ipmi_password']						IPMI password (optional).
	 * @param array  $hosts[]['inventory']							An array of host inventory data (optional).
	 * @param array  $hosts[]['macros']								An array of host macros (optional).
	 * @param string $hosts[]['macros'][]['macro']					Host macro (required if "macros" is set).
	 * @param array  $hosts[]['templates']							An array of template objects with IDs that will be linked to host (optional).
	 * @param string $hosts[]['templates'][]['templateid']			Template ID (required if "templates" is set).
	 * @param array  $hosts[]['templates_clear']					Templates to unlink and clear from the host (optional).
	 * @param string $hosts[]['templates_clear'][]['templateid']	Template ID (required if "templates" is set).
	 * @param string $hosts[]['tls_connect']						Connections to host (optional).
	 * @param string $hosts[]['tls_accept']							Connections from host (optional).
	 * @param string $hosts[]['tls_psk_identity']					PSK identity (required if "PSK" type is set).
	 * @param string $hosts[]['tls_psk']							PSK (required if "PSK" type is set).
	 * @param string $hosts[]['tls_issuer']							Certificate issuer (optional).
	 * @param string $hosts[]['tls_subject']						Certificate subject (optional).
	 *
	 * @return array
	 */
	public function update($hosts) {
		$hosts = zbx_toArray($hosts);
		$hostids = zbx_objectValues($hosts, 'hostid');

		$db_hosts = $this->get([
			'output' => ['hostid', 'host', 'flags', 'tls_connect', 'tls_accept', 'tls_issuer', 'tls_subject',
				'tls_psk_identity', 'tls_psk'
			],
			'hostids' => $hostids,
			'editable' => true,
			'preservekeys' => true
		]);

		$this->validateUpdate($hosts, $db_hosts);

		$inventories = [];
		foreach ($hosts as &$host) {
			// If visible name is not given or empty it should be set to host name.
			if (array_key_exists('host', $host) && (!array_key_exists('name', $host) || !trim($host['name']))) {
				$host['name'] = $host['host'];
			}

			$host['tls_connect'] = array_key_exists('tls_connect', $host)
				? $host['tls_connect']
				: $db_hosts[$host['hostid']]['tls_connect'];

			$host['tls_accept'] = array_key_exists('tls_accept', $host)
				? $host['tls_accept']
				: $db_hosts[$host['hostid']]['tls_accept'];

			// Clean PSK fields.
			if ($host['tls_connect'] != HOST_ENCRYPTION_PSK
					&& ($host['tls_accept'] & HOST_ENCRYPTION_PSK) != HOST_ENCRYPTION_PSK) {
				$host['tls_psk_identity'] = '';
				$host['tls_psk'] = '';
			}

			// Clean certificate fields.
			if ($host['tls_connect'] != HOST_ENCRYPTION_CERTIFICATE
					&& ($host['tls_accept'] & HOST_ENCRYPTION_CERTIFICATE) != HOST_ENCRYPTION_CERTIFICATE) {
				$host['tls_issuer'] = '';
				$host['tls_subject'] = '';
			}

			// Fetch fields required to update host inventory.
            /*
			if (array_key_exists('bzhyinventory', $host)) {
				$inventory = $host['bzhyinventory'];
				$inventory['hostid'] = $host['hostid'];

				$inventories[] = $inventory;
			}
             * 
             */
		}
		unset($host);

        /*
		$inventories = $this->extendObjects('host_inventory', $inventories, ['inventory_mode']);
		$inventories = zbx_toHash($inventories, 'hostid');
*/
		$macros = [];
		foreach ($hosts as &$host) {
			if (isset($host['macros'])) {
				$macros[$host['hostid']] = $host['macros'];

				unset($host['macros']);
			}
		}
		unset($host);

		if ($macros) {
			API::UserMacro()->replaceMacros($macros);
		}

		foreach ($hosts as $host) {
			// extend host inventory with the required data
            /*
			if (isset($host['inventory']) && $host['inventory']) {
				$inventory = $inventories[$host['hostid']];

				// if no host inventory record exists in the DB, it's disabled
				if (!isset($inventory['inventory_mode'])) {
					$inventory['inventory_mode'] = HOST_INVENTORY_DISABLED;
				}

				$host['inventory'] = $inventory;
			}
            */
			$data = $host;
			$data['hosts'] = $host;
			$result = $this->massUpdate($data);

			if (!$result) {
				bzhyCBase::exception(ZBX_API_ERROR_INTERNAL, _('Host update failed.'),__FILE__,__LINE__,TRUE);
			}
		}

		return ['hostids' => $hostids];
	}
    
    /**
    * Additionally allows to create new interfaces on hosts.
    *
    * Checks write permissions for hosts.
    *
    * Additional supported $data parameters are:
    * - interfaces - an array of interfaces to create on the hosts
    * - templates  - an array of templates to link to the hosts, overrides the CHostGeneral::massAdd()
    *                'templates' parameter
    *
    * @param array $data
    *
    * @return array
    */
    public function massAdd(array $data) {
        try{    
            $hosts = isset($data['hosts']) ? zbx_toArray($data['hosts']) : [];
            $hostIds = zbx_objectValues($hosts, 'hostid');

            // add new interfaces
            if (!empty($data['bzhyinterfaces'])) {
                bzhyAPI::HostInterface()->massAdd([
                    'hosts' => $data['hosts'],
                    'interfaces' => zbx_toArray($data['bzhyinterfaces'])
                ]);
            }
                
            // rename the "templates" parameter to the common "templates_link"
            if (isset($data['templates'])) {
                $data['templates_link'] = $data['templates'];
                unset($data['templates']);
            }
            $data['templates'] = [];
            
            $hostIds = zbx_objectValues($data['hosts'], 'hostid');
            $templateIds = zbx_objectValues($data['templates'], 'templateid');
            
            $allHostIds = array_merge($hostIds, $templateIds);
            
            // add groups
            if (!empty($data['groups'])) {
                API::HostGroup()->massADD([
                    'hosts' => $data['hosts'],
                    'templates' => $data['templates'],
                    'groups' => $data['groups']
		]);
            }
            
            // link templates
            if (!empty($data['templates_link'])) {           
                if (!API::Host()->isWritable($allHostIds)) {
                    bzhyCBase::exception(ZBX_API_ERROR_PERMISSIONS,
                        _('No permissions to referred object or it does not exist!')
                    ,__FILE__,__LINE__,TRUE);
		}
                $this->link(zbx_objectValues(zbx_toArray($data['templates_link']), 'templateid'), $allHostIds);
            }
            
            // create macros
            if (!empty($data['macros'])) {
                $data['macros'] = zbx_toArray($data['macros']);
                $hostMacrosToAdd = [];
                foreach ($data['macros'] as $hostMacro) {
                    foreach ($allHostIds as $hostid) {
                        $hostMacro['hostid'] = $hostid;
                        $hostMacrosToAdd[] = $hostMacro;
                    }
                }

                API::UserMacro()->create($hostMacrosToAdd);
            }

            $ids = ['hostids' => $hostIds, 'templateids' => $templateIds];
            $Fields = bzhyCDB::getTableFieldsByObject($this->ObjectName);
            if(bzhy_empty($Fields['id'])){
                $pk = "ids";
            }
            else{
                $pk = $Fields['id']."s";
            }
            
            return [$pk => $ids[$pk]];
        }
        catch (Exception $ex){
            bzhyCBase::exception(ZBX_API_ERROR_INTERNAL,$ex->getMessage(),__FILE__,__LINE__,TRUE);
        }
    }

	/**
	 * Mass update hosts.
	 *
	 * @param array  $hosts								multidimensional array with Hosts data
	 * @param array  $hosts['hosts']					Array of Host objects to update
	 * @param string $hosts['fields']['host']			Host name.
	 * @param array  $hosts['fields']['groupids']		HostGroup IDs add Host to.
	 * @param int    $hosts['fields']['port']			Port. OPTIONAL
	 * @param int    $hosts['fields']['status']			Host Status. OPTIONAL
	 * @param int    $hosts['fields']['useip']			Use IP. OPTIONAL
	 * @param string $hosts['fields']['dns']			DNS. OPTIONAL
	 * @param string $hosts['fields']['ip']				IP. OPTIONAL
	 * @param int    $hosts['fields']['bulk']			bulk. OPTIONAL
	 * @param int    $hosts['fields']['proxy_hostid']	Proxy Host ID. OPTIONAL
	 * @param int    $hosts['fields']['ipmi_authtype']	IPMI authentication type. OPTIONAL
	 * @param int    $hosts['fields']['ipmi_privilege']	IPMI privilege. OPTIONAL
	 * @param string $hosts['fields']['ipmi_username']	IPMI username. OPTIONAL
	 * @param string $hosts['fields']['ipmi_password']	IPMI password. OPTIONAL
	 *
	 * @return boolean
	 */
	public function massUpdate($data) {
		$hosts = zbx_toArray($data['hosts']);
		$inputHostIds = zbx_objectValues($hosts, 'hostid');
		$hostids = array_unique($inputHostIds);

		sort($hostids);

		$db_hosts = $this->get([
			'output' => ['hostid', 'tls_connect', 'tls_accept', 'tls_issuer', 'tls_subject', 'tls_psk_identity',
				'tls_psk'
			],
			'hostids' => $hostids,
			'editable' => true,
			'preservekeys' => true
		]);

		foreach ($hosts as $host) {
			if (!array_key_exists($host['hostid'], $db_hosts)) {
				bzhyCDB::exception(ZBX_API_ERROR_PERMISSIONS, _('You do not have permission to perform this operation.')
                ,__FILE__,__LINE__,true);
			}
		}

		// Check connection fields only for massupdate action.
		if ((array_key_exists('tls_connect', $data) || array_key_exists('tls_accept', $data)
				|| array_key_exists('tls_psk_identity', $data) || array_key_exists('tls_psk', $data)
				|| array_key_exists('tls_issuer', $data) || array_key_exists('tls_subject', $data))
					&& (!array_key_exists('tls_connect', $data) || !array_key_exists('tls_accept', $data))) {
			bzhyCBase::exception(ZBX_API_ERROR_PERMISSIONS, _(
				'Cannot update host encryption settings. Connection settings for both directions should be specified.'
			),__FILE__,__LINE__,true);
		}

		$this->validateEncryption([$data]);

		// Clean PSK fields.
		if ((array_key_exists('tls_connect', $data) && $data['tls_connect'] != HOST_ENCRYPTION_PSK)
				&& (array_key_exists('tls_accept', $data)
					&& ($data['tls_accept'] & HOST_ENCRYPTION_PSK) != HOST_ENCRYPTION_PSK)) {
			$data['tls_psk_identity'] = '';
			$data['tls_psk'] = '';
		}

		// Clean certificate fields.
		if ((array_key_exists('tls_connect', $data) && $data['tls_connect'] != HOST_ENCRYPTION_CERTIFICATE)
				&& (array_key_exists('tls_accept', $data)
					&& ($data['tls_accept'] & HOST_ENCRYPTION_CERTIFICATE) != HOST_ENCRYPTION_CERTIFICATE)) {
			$data['tls_issuer'] = '';
			$data['tls_subject'] = '';
		}

		// check if hosts have at least 1 group
		if (isset($data['groups']) && empty($data['groups'])) {
			bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('No groups for hosts.'),__FILE__,__LINE__,TRUE);
		}

		/*
		 * Update hosts properties
		 */
		if (isset($data['name'])) {
			if (count($hosts) > 1) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot mass update visible host name.'),__FILE__,__LINE__,TRUE);
			}
		}

		if (isset($data['host'])) {
			if (!preg_match('/^'.ZBX_PREG_HOST_FORMAT.'$/', $data['host'])) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Incorrect characters used for host name "%s".', $data['host']),__FILE__,__LINE__,TRUE);
			}

			if (count($hosts) > 1) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot mass update host name.'),__FILE__,__LINE__,TRUE);
			}

			$curHost = reset($hosts);

			$sameHostnameHost = $this->get([
				'output' => ['hostid'],
				'filter' => ['host' => $data['host']],
				'nopermissions' => true,
				'limit' => 1
			]);
			$sameHostnameHost = reset($sameHostnameHost);
			if ($sameHostnameHost && (bccomp($sameHostnameHost['hostid'], $curHost['hostid']) != 0)) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Host "%1$s" already exists.', $data['host']),__FILE__,__LINE__,true);
			}

			// can't add host with the same name as existing template
			$sameHostnameTemplate = API::Template()->get([
				'output' => ['templateid'],
				'filter' => ['host' => $data['host']],
				'nopermissions' => true,
				'limit' => 1
			]);
			if ($sameHostnameTemplate) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Template "%1$s" already exists.', $data['host']),__FILE__,__LINE__,TRUE);
			}
		}

		if (isset($data['groups'])) {
			$updateGroups = $data['groups'];
		}

		if (isset($data['bzhyinterfaces'])) {
			$updateInterfaces = $data['bzhyinterfaces'];
		}

		if (array_key_exists('templates_clear', $data)) {
			$updateTemplatesClear = zbx_toArray($data['templates_clear']);
		}

		if (isset($data['templates'])) {
			$updateTemplates = $data['templates'];
		}

		if (isset($data['macros'])) {
			$updateMacros = $data['macros'];
		}

		// second check is necessary, because import incorrectly inputs unset 'inventory' as empty string rather than null
		
        if (isset($data['bzhyinventory']) && $data['bzhyinventory']) {
			if (isset($data['inventory_mode']) && $data['inventory_mode'] == HOST_INVENTORY_DISABLED) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot set inventory fields for disabled inventory.'),__FILE__,__LINE__,TRUE);
			}

			$updateInventory = $data['bzhyinventory'];
			$updateInventory['inventory_mode'] = null;
		}

		if (isset($data['inventory_mode'])) {
			if (!isset($updateInventory)) {
				$updateInventory = [];
			}
			$updateInventory['inventory_mode'] = $data['inventory_mode'];
		}
        
		if (isset($data['status'])) {
			$updateStatus = $data['status'];
		}

		unset($data['hosts'], $data['groups'], $data['interfaces'], $data['templates_clear'], $data['templates'],
			$data['macros'], $data['inventory'], $data['inventory_mode'], $data['status']);

		if (!zbx_empty($data)) {
			bzhyCDB::update('hosts', [
				'values' => $data,
				'where' => ['hostid' => $hostids]
			]);
		}

		if (isset($updateStatus)) {
			$this->updateHostStatus($hostids, $updateStatus);
		}

		/*
		 * Update template linkage
		 */
		if (isset($updateTemplatesClear)) {
			$templateIdsClear = zbx_objectValues($updateTemplatesClear, 'templateid');

			if ($updateTemplatesClear) {
				$this->massRemove(['hostids' => $hostids, 'templateids_clear' => $templateIdsClear]);
			}
		}
		else {
			$templateIdsClear = [];
		}

		// unlink templates
		if (isset($updateTemplates)) {
			$hostTemplates = API::Template()->get([
				'hostids' => $hostids,
				'output' => ['templateid'],
				'preservekeys' => true
			]);

			$hostTemplateids = array_keys($hostTemplates);
			$newTemplateids = zbx_objectValues($updateTemplates, 'templateid');

			$templatesToDel = array_diff($hostTemplateids, $newTemplateids);
			$templatesToDel = array_diff($templatesToDel, $templateIdsClear);

			if ($templatesToDel) {
				$result = $this->massRemove([
					'hostids' => $hostids,
					'templateids' => $templatesToDel
				]);
				if (!$result) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot unlink template'),__FILE__,__LINE__,TRUE);
				}
			}
		}

		/*
		 * update interfaces
		 */
		if (isset($updateInterfaces)) {
			foreach($hostids as $hostid) {
				bzhyAPI::HostInterface()->replaceHostInterfaces([
					'hostid' => $hostid,
					'interfaces' => $updateInterfaces
				]);
			}
		}

		// link new templates
		if (isset($updateTemplates)) {
			$result = $this->massAdd([
				'hosts' => $hosts,
				'templates' => $updateTemplates
			]);

			if (!$result) {
				self::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot link template'));
			}
		}

		// macros
		if (isset($updateMacros)) {
			DB::delete('hostmacro', ['hostid' => $hostids]);

			$this->massAdd([
				'hosts' => $hosts,
				'macros' => $updateMacros
			]);
		}

		/*
		 * Inventory
		 */
		if (isset($updateInventory)) {
			// disabling inventory
			if ($updateInventory['inventory_mode'] == HOST_INVENTORY_DISABLED) {
				$sql = 'DELETE FROM host_inventory WHERE '.dbConditionInt('hostid', $hostids);
				if (!DBexecute($sql)) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot delete inventory.'));
				}
			}
			// changing inventory mode or setting inventory fields
			else {
				$existingInventoriesDb = DBfetchArrayAssoc(DBselect(
					'SELECT hostid,inventory_mode'.
					' FROM host_inventory'.
					' WHERE '.dbConditionInt('hostid', $hostids)
				), 'hostid');

				// check existing host inventory data
				$automaticHostIds = [];
				if ($updateInventory['inventory_mode'] === null) {
					foreach ($hostids as $hostid) {
						// if inventory is disabled for one of the updated hosts, throw an exception
						if (!isset($existingInventoriesDb[$hostid])) {
							$host = get_host_by_hostid($hostid);
							self::exception(ZBX_API_ERROR_PARAMETERS, _s(
								'Inventory disabled for host "%1$s".', $host['host']
							));
						}
						// if inventory mode is set to automatic, save its ID for later usage
						elseif ($existingInventoriesDb[$hostid]['inventory_mode'] == HOST_INVENTORY_AUTOMATIC) {
							$automaticHostIds[] = $hostid;
						}
					}
				}

				$inventoriesToSave = [];
				foreach ($hostids as $hostid) {
					$hostInventory = $updateInventory;
					$hostInventory['hostid'] = $hostid;

					// if no 'inventory_mode' has been passed, set inventory 'inventory_mode' from DB
					if ($updateInventory['inventory_mode'] === null) {
						$hostInventory['inventory_mode'] = $existingInventoriesDb[$hostid]['inventory_mode'];
					}

					$inventoriesToSave[$hostid] = $hostInventory;
				}

				// when updating automatic inventory, ignore fields that have items linked to them
				if ($updateInventory['inventory_mode'] == HOST_INVENTORY_AUTOMATIC
						|| ($updateInventory['inventory_mode'] === null && $automaticHostIds)) {

					$itemsToInventories = API::item()->get([
						'output' => ['inventory_link', 'hostid'],
						'hostids' => $automaticHostIds ? $automaticHostIds : $hostids,
						'nopermissions' => true
					]);

					$inventoryFields = getHostInventories();
					foreach ($itemsToInventories as $hinv) {
						// 0 means 'no link'
						if ($hinv['inventory_link'] != 0) {
							$inventoryName = $inventoryFields[$hinv['inventory_link']]['db_field'];
							unset($inventoriesToSave[$hinv['hostid']][$inventoryName]);
						}
					}
				}

				// save inventory data
				foreach ($inventoriesToSave as $inventory) {
					$hostid = $inventory['hostid'];
					if (isset($existingInventoriesDb[$hostid])) {
						DB::update('host_inventory', [
							'values' => $inventory,
							'where' => ['hostid' => $hostid]
						]);
					}
					else {
						DB::insert('host_inventory', [$inventory], false);
					}
				}
			}
		}

		/*
		 * Update host and host group linkage. This procedure should be done the last because user can unlink
		 * him self from a group with write permissions leaving only read premissions. Thus other procedures, like
		 * host-template linkage, inventory update, macros update, must be done before this.
		 */
		if (isset($updateGroups)) {
			$updateGroups = zbx_toArray($updateGroups);

			$hostGroups = API::HostGroup()->get([
				'output' => ['groupid'],
				'hostids' => $hostids
			]);
			$hostGroupIds = zbx_objectValues($hostGroups, 'groupid');
			$newGroupIds = zbx_objectValues($updateGroups, 'groupid');

			$groupsToAdd = array_diff($newGroupIds, $hostGroupIds);
			if ($groupsToAdd) {
				$this->massAdd([
					'hosts' => $hosts,
					'groups' => zbx_toObject($groupsToAdd, 'groupid')
				]);
			}

			$groupIdsToDelete = array_diff($hostGroupIds, $newGroupIds);
			if ($groupIdsToDelete) {
				$this->massRemove([
					'hostids' => $hostids,
					'groupids' => $groupIdsToDelete
				]);
			}
		}

		return ['hostids' => $inputHostIds];
	}

	/**
	 * Additionally allows to remove interfaces from hosts.
	 *
	 * Checks write permissions for hosts.
	 *
	 * Additional supported $data parameters are:
	 * - interfaces  - an array of interfaces to delete from the hosts
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function massRemove(array $data) {
		$hostids = zbx_toArray($data['hostids']);

		$this->checkPermissions($hostids);

		if (isset($data['interfaces'])) {
			$options = [
				'hostids' => $hostids,
				'interfaces' => zbx_toArray($data['interfaces'])
			];
			API::HostInterface()->massRemove($options);
		}

		// rename the "templates" parameter to the common "templates_link"
		if (isset($data['templateids'])) {
			$data['templateids_link'] = $data['templateids'];
			unset($data['templateids']);
		}

		$data['templateids'] = [];

		return parent::massRemove($data);
	}

	/**
	 * Validates the input parameters for the delete() method.
	 *
	 * @throws APIException if the input is invalid
	 *
	 * @param array $hostIds
	 * @param bool 	$nopermissions
	 */
	protected function validateDelete(array $hostIds, $nopermissions = false) {
		if (!$hostIds) {
			self::exception(ZBX_API_ERROR_PARAMETERS, _('Empty input parameter.'));
		}

		if (!$nopermissions) {
			$this->checkPermissions($hostIds);
		}
	}

	/**
	 * Delete Host.
	 *
	 * @param array	$hostIds
	 * @param bool	$nopermissions
	 *
	 * @return array
	 */
	public function delete(array $hostIds, $nopermissions = false) {
		$this->validateDelete($hostIds, $nopermissions);

		// delete the discovery rules first
		$delRules = API::DiscoveryRule()->get([
			'output' => ['itemid'],
			'hostids' => $hostIds,
			'nopermissions' => true,
			'preservekeys' => true
		]);
		if ($delRules) {
			API::DiscoveryRule()->delete(array_keys($delRules), true);
		}

		// delete the items
		$delItems = API::Item()->get([
			'templateids' => $hostIds,
			'output' => ['itemid'],
			'nopermissions' => true,
			'preservekeys' => true
		]);
		if ($delItems) {
			API::Item()->delete(array_keys($delItems), true);
		}

		// delete web tests
		$delHttptests = [];
		$dbHttptests = get_httptests_by_hostid($hostIds);
		while ($dbHttptest = DBfetch($dbHttptests)) {
			$delHttptests[$dbHttptest['httptestid']] = $dbHttptest['httptestid'];
		}
		if (!empty($delHttptests)) {
			API::HttpTest()->delete($delHttptests, true);
		}


		// delete screen items
		DB::delete('screens_items', [
			'resourceid' => $hostIds,
			'resourcetype' => SCREEN_RESOURCE_HOST_TRIGGERS
		]);

		// delete host from maps
		if (!empty($hostIds)) {
			DB::delete('sysmaps_elements', [
				'elementtype' => SYSMAP_ELEMENT_TYPE_HOST,
				'elementid' => $hostIds
			]);
		}

		// disable actions
		// actions from conditions
		$actionids = [];
		$sql = 'SELECT DISTINCT actionid'.
				' FROM conditions'.
				' WHERE conditiontype='.CONDITION_TYPE_HOST.
				' AND '.dbConditionString('value', $hostIds);
		$dbActions = DBselect($sql);
		while ($dbAction = DBfetch($dbActions)) {
			$actionids[$dbAction['actionid']] = $dbAction['actionid'];
		}

		// actions from operations
		$sql = 'SELECT DISTINCT o.actionid'.
				' FROM operations o, opcommand_hst oh'.
				' WHERE o.operationid=oh.operationid'.
				' AND '.dbConditionInt('oh.hostid', $hostIds);
		$dbActions = DBselect($sql);
		while ($dbAction = DBfetch($dbActions)) {
			$actionids[$dbAction['actionid']] = $dbAction['actionid'];
		}

		if (!empty($actionids)) {
			$update = [];
			$update[] = [
				'values' => ['status' => ACTION_STATUS_DISABLED],
				'where' => ['actionid' => $actionids]
			];
			DB::update('actions', $update);
		}

		// delete action conditions
		DB::delete('conditions', [
			'conditiontype' => CONDITION_TYPE_HOST,
			'value' => $hostIds
		]);

		// delete action operation commands
		$operationids = [];
		$sql = 'SELECT DISTINCT oh.operationid'.
				' FROM opcommand_hst oh'.
				' WHERE '.dbConditionInt('oh.hostid', $hostIds);
		$dbOperations = DBselect($sql);
		while ($dbOperation = DBfetch($dbOperations)) {
			$operationids[$dbOperation['operationid']] = $dbOperation['operationid'];
		}

		DB::delete('opcommand_hst', [
			'hostid' => $hostIds,
		]);

		// delete empty operations
		$delOperationids = [];
		$sql = 'SELECT DISTINCT o.operationid'.
				' FROM operations o'.
				' WHERE '.dbConditionInt('o.operationid', $operationids).
				' AND NOT EXISTS(SELECT oh.opcommand_hstid FROM opcommand_hst oh WHERE oh.operationid=o.operationid)';
		$dbOperations = DBselect($sql);
		while ($dbOperation = DBfetch($dbOperations)) {
			$delOperationids[$dbOperation['operationid']] = $dbOperation['operationid'];
		}

		DB::delete('operations', [
			'operationid' => $delOperationids,
		]);

		$hosts = API::Host()->get([
			'output' => [
				'hostid',
				'name'
			],
			'hostids' => $hostIds,
			'nopermissions' => true
		]);

		// delete host inventory
		DB::delete('host_inventory', ['hostid' => $hostIds]);

		// delete host applications
		DB::delete('applications', ['hostid' => $hostIds]);

		// delete host
		DB::delete('hosts', ['hostid' => $hostIds]);

		// TODO: remove info from API
		foreach ($hosts as $host) {
			info(_s('Deleted: Host "%1$s".', $host['name']));
			add_audit_ext(AUDIT_ACTION_DELETE, AUDIT_RESOURCE_HOST, $host['hostid'], $host['name'], 'hosts', NULL, NULL);
		}

		// remove Monitoring > Latest data toggle profile values related to given hosts
		DB::delete('profiles', ['idx' => 'web.latest.toggle_other', 'idx2' => $hostIds]);

		return ['hostids' => $hostIds];
	}

	/**
	 * Check if user has read permissions for host.
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function isReadable(array $ids) {
		if (!is_array($ids)) {
			return false;
		}
		if (empty($ids)) {
			return true;
		}

		$ids = array_unique($ids);

		$count = $this->get([
			'hostids' => $ids,
			'templated_hosts' => true,
			'countOutput' => true
		]);

		return (count($ids) == $count);
	}

	/**
	 * Check if user has write permissions for host.
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function isWritable(array $ids) {
		if (!is_array($ids)) {
			return false;
		}
		if (empty($ids)) {
			return true;
		}

		$ids = array_unique($ids);

		$count = $this->get([
			'hostids' => $ids,
			'editable' => true,
			'templated_hosts' => true,
			'countOutput' => true
		]);

		return (count($ids) == $count);
	}

    protected function addRelatedObjects(array $options, array $result) {		
        $hostids = array_keys($result);
        
        // adding groups
		if ($options['selectGroups'] !== null) {
			$relationMap = $this->createRelationMap($result, 'hostid', 'groupid', 'hosts_groups');
			$groups = API::HostGroup()->get([
				'output' => $options['selectGroups'],
				'groupids' => $relationMap->getRelatedIds(),
				'preservekeys' => true
			]);
			$result = $relationMap->mapMany($result, $groups, 'groups');
		}

        // adding templates
		if ($options['selectParentTemplates'] !== null) {
			if ($options['selectParentTemplates'] != API_OUTPUT_COUNT) {
				$relationMap = $this->createRelationMap($result, 'hostid', 'templateid', 'hosts_templates');
				$templates = API::Template()->get([
					'output' => $options['selectParentTemplates'],
					'templateids' => $relationMap->getRelatedIds(),
					'preservekeys' => true
				]);
				if (!is_null($options['limitSelects'])) {
					order_result($templates, 'host');
				}
				$result = $relationMap->mapMany($result, $templates, 'parentTemplates', $options['limitSelects']);
			}
			else {
				$templates = API::Template()->get([
					'hostids' => $hostids,
					'countOutput' => true,
					'groupCount' => true
				]);
				$templates = zbx_toHash($templates, 'hostid');
				foreach ($result as $hostid => $host) {
					$result[$hostid]['parentTemplates'] = isset($templates[$hostid]) ? $templates[$hostid]['rowscount'] : 0;
				}
			}
		}
        
        // adding items
		if ($options['selectItems'] !== null) {
			if ($options['selectItems'] != API_OUTPUT_COUNT) {
				$items = API::Item()->get([
					'output' => $this->outputExtend($options['selectItems'], ['hostid', 'itemid']),
					'hostids' => $hostids,
					'nopermissions' => true,
					'preservekeys' => true
				]);

				if (!is_null($options['limitSelects'])) {
					order_result($items, 'name');
				}

				$relationMap = $this->createRelationMap($items, 'hostid', 'itemid');

				$items = bzhyCDB::unsetExtraFields($items, ['hostid', 'itemid'], $options['selectItems']);
				$result = $relationMap->mapMany($result, $items, 'items', $options['limitSelects']);
			}
			else {
				$items = API::Item()->get([
					'hostids' => $hostids,
					'nopermissions' => true,
					'countOutput' => true,
					'groupCount' => true
				]);
				$items = zbx_toHash($items, 'hostid');
				foreach ($result as $hostid => $host) {
					$result[$hostid]['items'] = isset($items[$hostid]) ? $items[$hostid]['rowscount'] : 0;
				}
			}
		}
        
        // adding discoveries
		if ($options['selectDiscoveries'] !== null) {
			if ($options['selectDiscoveries'] != API_OUTPUT_COUNT) {
				$items = API::DiscoveryRule()->get([
					'output' => $this->outputExtend($options['selectDiscoveries'], ['hostid', 'itemid']),
					'hostids' => $hostids,
					'nopermissions' => true,
					'preservekeys' => true
				]);

				if (!is_null($options['limitSelects'])) {
					order_result($items, 'name');
				}

				$relationMap = $this->createRelationMap($items, 'hostid', 'itemid');

				$items = bzhyCDB::unsetExtraFields($items, ['hostid', 'itemid'], $options['selectDiscoveries']);
				$result = $relationMap->mapMany($result, $items, 'discoveries', $options['limitSelects']);
			}
			else {
				$items = API::DiscoveryRule()->get([
					'hostids' => $hostids,
					'nopermissions' => true,
					'countOutput' => true,
					'groupCount' => true
				]);
				$items = zbx_toHash($items, 'hostid');
				foreach ($result as $hostid => $host) {
					$result[$hostid]['discoveries'] = isset($items[$hostid]) ? $items[$hostid]['rowscount'] : 0;
				}
			}
		}

        // adding triggers
		if ($options['selectTriggers'] !== null) {
			if ($options['selectTriggers'] != API_OUTPUT_COUNT) {
				// discovered items
				$res = DBselect(
					'SELECT i.hostid,f.triggerid'.
						' FROM items i,functions f'.
						' WHERE '.dbConditionInt('i.hostid', $hostids).
						' AND i.itemid=f.itemid'
				);
				$relationMap = new CRelationMap();
				while ($relation = DBfetch($res)) {
					$relationMap->addRelation($relation['hostid'], $relation['triggerid']);
				}

				$triggers = API::Trigger()->get([
					'output' => $options['selectTriggers'],
					'triggerids' => $relationMap->getRelatedIds(),
					'preservekeys' => true
				]);
				if (!is_null($options['limitSelects'])) {
					order_result($triggers, 'description');
				}
				$result = $relationMap->mapMany($result, $triggers, 'triggers', $options['limitSelects']);
			}
			else {
				$triggers = API::Trigger()->get([
					'hostids' => $hostids,
					'countOutput' => true,
					'groupCount' => true
				]);
				$triggers = zbx_toHash($triggers, 'hostid');

				foreach ($result as $hostid => $host) {
					$result[$hostid]['triggers'] = isset($triggers[$hostid]) ? $triggers[$hostid]['rowscount'] : 0;
				}
			}
		}
        
        // adding graphs
		if ($options['selectGraphs'] !== null) {
			if ($options['selectGraphs'] != API_OUTPUT_COUNT) {
				// discovered items
				$res = DBselect(
					'SELECT i.hostid,gi.graphid'.
						' FROM items i,graphs_items gi'.
						' WHERE '.dbConditionInt('i.hostid', $hostids).
						' AND i.itemid=gi.itemid'
				);
				$relationMap = new CRelationMap();
				while ($relation = DBfetch($res)) {
					$relationMap->addRelation($relation['hostid'], $relation['graphid']);
				}

				$graphs = API::Graph()->get([
					'output' => $options['selectGraphs'],
					'graphids' => $relationMap->getRelatedIds(),
					'preservekeys' => true
				]);
				if (!is_null($options['limitSelects'])) {
					order_result($graphs, 'name');
				}
				$result = $relationMap->mapMany($result, $graphs, 'graphs', $options['limitSelects']);
			}
			else {
				$graphs = API::Graph()->get([
					'hostids' => $hostids,
					'countOutput' => true,
					'groupCount' => true
				]);
				$graphs = zbx_toHash($graphs, 'hostid');
				foreach ($result as $hostid => $host) {
					$result[$hostid]['graphs'] = isset($graphs[$hostid]) ? $graphs[$hostid]['rowscount'] : 0;
				}
			}
		}

        // adding http tests
		if ($options['selectHttpTests'] !== null) {
			if ($options['selectHttpTests'] != API_OUTPUT_COUNT) {
				$httpTests = API::HttpTest()->get([
					'output' => $this->outputExtend($options['selectHttpTests'], ['hostid', 'httptestid']),
					'hostids' => $hostids,
					'nopermissions' => true,
					'preservekeys' => true
				]);

				if (!is_null($options['limitSelects'])) {
					order_result($httpTests, 'name');
				}

				$relationMap = $this->createRelationMap($httpTests, 'hostid', 'httptestid');

				$httpTests = bzhyCDB::unsetExtraFields($httpTests, ['hostid', 'httptestid'], $options['selectHttpTests']);
				$result = $relationMap->mapMany($result, $httpTests, 'httpTests', $options['limitSelects']);
			}
			else {
				$httpTests = API::HttpTest()->get([
					'hostids' => $hostids,
					'nopermissions' => true,
					'countOutput' => true,
					'groupCount' => true
				]);
				$httpTests = zbx_toHash($httpTests, 'hostid');
				foreach ($result as $hostId => $host) {
					$result[$hostId]['httpTests'] = isset($httpTests[$hostId]) ? $httpTests[$hostId]['rowscount'] : 0;
				}
			}
		}
        
        // adding applications
		if ($options['selectApplications'] !== null) {
			if ($options['selectApplications'] != API_OUTPUT_COUNT) {
				$applications = API::Application()->get([
					'output' => $this->outputExtend($options['selectApplications'], ['hostid', 'applicationid']),
					'hostids' => $hostids,
					'nopermissions' => true,
					'preservekeys' => true
				]);

				if (!is_null($options['limitSelects'])) {
					order_result($applications, 'name');
				}

				$relationMap = $this->createRelationMap($applications, 'hostid', 'applicationid');

				$applications = bzhyCDB::unsetExtraFields($applications, ['hostid', 'applicationid'],
					$options['selectApplications']
				);
				$result = $relationMap->mapMany($result, $applications, 'applications', $options['limitSelects']);
			}
			else {
				$applications = API::Application()->get([
					'output' => $options['selectApplications'],
					'hostids' => $hostids,
					'nopermissions' => true,
					'countOutput' => true,
					'groupCount' => true
				]);

				$applications = zbx_toHash($applications, 'hostid');
				foreach ($result as $hostid => $host) {
					$result[$hostid]['applications'] = isset($applications[$hostid]) ? $applications[$hostid]['rowscount'] : 0;
				}
			}
		}

        // adding macros
		if ($options['selectMacros'] !== null && $options['selectMacros'] != API_OUTPUT_COUNT) {
			$macros = API::UserMacro()->get([
				'output' => $this->outputExtend($options['selectMacros'], ['hostid', 'hostmacroid']),
				'hostids' => $hostids,
				'preservekeys' => true
			]);

			$relationMap = $this->createRelationMap($macros, 'hostid', 'hostmacroid');

			$macros = bzhyCDB::unsetExtraFields($macros, ['hostid', 'hostmacroid'], $options['selectMacros']);
			$result = $relationMap->mapMany($result, $macros, 'macros', $options['limitSelects']);
		}
        
        // adding inventories
        if (!bzhy_empty($options['selectInventory'])) {
            $InventoryOutput = isset($options['selectInventory']['output'])?$options['selectInventory']['output']
               :$options['selectInventory'];
            foreach ($result as $id => $value){
                $inventorys = bzhyCDB::select($this->inventoryTable, [
                    'output' => $InventoryOutput,
                    'filter' => ['hostid' => $value['hostid']]
                ]);
                
                $inventory = reset($inventorys);
                
                if(isset($options['selectInventory']['dns']) && !bzhy_empty($options['selectInventory']['dns'])){
                    if(isset($options['selectInventory']['dns']['output'])){
                        $dnsOption = $options['selectInventory']['dns'];
                    }
                    else{
                        $dnsOption['output'] = is_array($options['selectInventory']['dns'])?$options['selectInventory']['dns']:[$options['selectInventory']['dns']];
                    }
                    $dnsOption['filter'] = ['hostid' => $value['hostid'],'type' =>BZHYHOST_IP_TYPE_DNS];
                    $dnsOption['preservekeys'] = TRUE;
                    $dnsObjects = bzhyCDB::select($this->ipTable, $dnsOption);
                    $inventory['dns'] = $dnsObjects;
                }
            
                if(isset($options['selectInventory']['gw']) && !bzhy_empty($options['selectInventory']['gw'])){
                    if(isset($options['selectInventory']['gw']['output'])){
                        $gwOption = $options['selectInventory']['gw'];
                    }
                    else{
                        $gwOption['output'] = is_array($options['selectInventory']['gw'])?$options['selectInventory']['gw']:[$options['selectInventory']['gw']];
                    }
                    $gwOption['filter'] = ['hostid' => $value['hostid'],'type' =>BZHYHOST_IP_TYPE_GW];
                    $gwOption['preservekeys'] = TRUE;
                    
                    $gwObjects = bzhyCDB::select($this->ipTable, $gwOption);
                    $inventory['gw'] = $gwObjects;
                }
            
                if(isset($options['selectInventory']['contact']) && !bzhy_empty($options['selectInventory']['contact'])){
                    $ContactOptions = $options['selectInventory']['contact'];
                    $ContactOptions['object_ids'] = $value['hostid'];
                    $ContactOptions['object_table'] = $this->tableName;
                    $Contacts = bzhyAPI::Contact()->get($ContactOptions);
                    $inventory['contact'] = $Contacts;
                }
            
                if(isset($options['selectInventory']['file']) && !bzhy_empty($options['selectInventory']['file'])){
                    $FileOptions = $options['selectInventory']['file'];
                    $FileOptions['object_ids'] = $value['hostid'];
                    $FileOptions['object_table'] = $this->tableName;
                    $Files = bzhyAPI::File()->get($FileOptions);
                    $inventory['file'] = $Files;
                }
                
                $result[$id]['inventory'] = $inventory;
            }
        }

        // adding hostinterfaces
        if ($options['selectInterfaces'] !== null) {
            if ($options['selectInterfaces'] != API_OUTPUT_COUNT) {
                $interfaces = bzhyAPI::HostInterface()->get([
                    'output' => $this->outputExtend($options['selectInterfaces'], ['hostid', 'id']),
                    'hostids' => $hostids,
                    'nopermissions' => true,
                    'preservekeys' => true
                ]);
                // we need to order interfaces for proper linkage and viewing
                order_result($interfaces, 'id', ZBX_SORT_UP);
                $relationMap = $this->createRelationMap($interfaces, 'hostid', 'id');
                $interfaces = bzhyCDB::unsetExtraFields($interfaces, ['hostid', 'id'], $options['selectInterfaces']);
                $result = $relationMap->mapMany($result, $interfaces, 'interfaces', $options['limitSelects']);
            }
            else {
                $interfaces = bzhyAPI::HostInterface()->get([
                    'hostids' => $hostids,
                    'nopermissions' => true,
                    'countOutput' => true,
                    'groupCount' => true
                ]);
                $interfaces = zbx_toHash($interfaces, 'hostid');
                foreach ($result as $hostid => $host) {
                    $result[$hostid]['interfaces'] = isset($interfaces['rowscount']) ? $interfaces['rowscount'] : 0;
                }
            }
        }

	// adding screens
        if ($options['selectScreens'] !== null) {
            if ($options['selectScreens'] != API_OUTPUT_COUNT) {
                $screens = API::TemplateScreen()->get([
                    'output' => $this->outputExtend($options['selectScreens'], ['hostid']),
                    'hostids' => $hostids,
                    'nopermissions' => true
                ]);
                if (!is_null($options['limitSelects'])) {
                    order_result($screens, 'name');
                }
                // inherited screens do not have a unique screenid, so we're building a map using array keys
                $relationMap = new CRelationMap();
                foreach ($screens as $key => $screen) {
                    $relationMap->addRelation($screen['hostid'], $key);
                }
                $screens = $this->unsetExtraFields($screens, ['hostid'], $options['selectScreens']);
                $result = $relationMap->mapMany($result, $screens, 'screens', $options['limitSelects']);
            }
            else {
                $screens = API::TemplateScreen()->get([
                    'hostids' => $hostids,
                    'nopermissions' => true,
                    'countOutput' => true,
                    'groupCount' => true
                ]);
                $screens = zbx_toHash($screens, 'hostid');
                foreach ($result as $hostid => $host) {
                    $result[$hostid]['screens'] = isset($screens[$hostid]) ? $screens[$hostid]['rowscount'] : 0;
                }
            }
        }

        // adding discovery rule
        if ($options['selectDiscoveryRule'] !== null && $options['selectDiscoveryRule'] != API_OUTPUT_COUNT) {
            // discovered items
            $discoveryRules = DBFetchArray(DBselect(
                'SELECT hd.hostid,hd2.parent_itemid'.
                ' FROM host_discovery hd,host_discovery hd2'.
                ' WHERE '.dbConditionInt('hd.hostid', $hostids).
                ' AND hd.parent_hostid=hd2.hostid'
            ));
            $relationMap = $this->createRelationMap($discoveryRules, 'hostid', 'parent_itemid');

            $discoveryRules = API::DiscoveryRule()->get([
                'output' => $options['selectDiscoveryRule'],
                'itemids' => $relationMap->getRelatedIds(),
                'preservekeys' => true
            ]);
            $result = $relationMap->mapOne($result, $discoveryRules, 'discoveryRule');
        }

        // adding host discovery
        if ($options['selectHostDiscovery'] !== null) {
            $hostDiscoveries = bzhyCDB::select('host_discovery', [
                'output' => $this->outputExtend($options['selectHostDiscovery'], ['hostid']),
                'filter' => ['hostid' => $hostids],
                'preservekeys' => true
            ]);
            $relationMap = $this->createRelationMap($hostDiscoveries, 'hostid', 'hostid');
            $hostDiscoveries = bzhyCDB::unsetExtraFields($hostDiscoveries, ['hostid'],
                $options['selectHostDiscovery']
            );
            $result = $relationMap->mapOne($result, $hostDiscoveries, 'hostDiscovery');
        }
        return $result;
    }

	/**
	 * Checks if all of the given hosts are available for writing.
	 *
	 * @throws APIException     if a host is not writable or does not exist
	 *
	 * @param array $hostIds
	 */
	protected function checkPermissions(array $hostIds) {
		if (!$this->isWritable($hostIds)) {
			bzhyCBase::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'),__FILE__,__LINE__,TRUE);
		}
	}

    /**
    * Validate connections from/to host and PSK fields.
    *
    * @param array $hosts		hosts data array
    *
    * @throws APIException if incorrect encryption options.
    */
    protected function validateEncryption(array $hosts) {
        foreach ($hosts as $host) {
            $available_connect_types = [HOST_ENCRYPTION_NONE, HOST_ENCRYPTION_PSK, HOST_ENCRYPTION_CERTIFICATE];
            $available_accept_types = [
		HOST_ENCRYPTION_NONE, HOST_ENCRYPTION_PSK, (HOST_ENCRYPTION_NONE | HOST_ENCRYPTION_PSK),
		HOST_ENCRYPTION_CERTIFICATE, (HOST_ENCRYPTION_NONE | HOST_ENCRYPTION_CERTIFICATE),
		(HOST_ENCRYPTION_PSK | HOST_ENCRYPTION_CERTIFICATE),
		(HOST_ENCRYPTION_NONE | HOST_ENCRYPTION_PSK | HOST_ENCRYPTION_CERTIFICATE)
            ];

            if (array_key_exists('tls_connect', $host) && !in_array($host['tls_connect'], $available_connect_types)) {
		bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Incorrect value for field "%1$s": %2$s.', 'tls_connect',
                    _s('unexpected value "%1$s"', $host['tls_connect'])
		),__FILE__,__LINE__,TRUE);
            }

            if (array_key_exists('tls_accept', $host) && !in_array($host['tls_accept'], $available_accept_types)) {
		bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Incorrect value for field "%1$s": %2$s.', 'tls_accept',
                    _s('unexpected value "%1$s"', $host['tls_accept'])
		),__FILE__,__LINE__,TRUE);
            }

            // PSK validation.
            if ((array_key_exists('tls_connect', $host) && $host['tls_connect'] == HOST_ENCRYPTION_PSK)
		|| (array_key_exists('tls_accept', $host)
		&& ($host['tls_accept'] & HOST_ENCRYPTION_PSK) == HOST_ENCRYPTION_PSK)) {
		if (!array_key_exists('tls_psk_identity', $host) || bzhy_empty($host['tls_psk_identity'])) {
                    bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
			_s('Incorrect value for field "%1$s": %2$s.', 'tls_psk_identity', _('cannot be empty'))
                    ,__FILE__,__LINE__,TRUE);
		}

		if (!array_key_exists('tls_psk', $host) || bzhy_empty($host['tls_psk'])) {
                    bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
			_s('Incorrect value for field "%1$s": %2$s.', 'tls_psk', _('cannot be empty'))
                    ,__FILE__,__LINE__,TRUE);
		}

		if (!preg_match('/^([0-9a-f]{2})+$/i', $host['tls_psk'])) {
                    bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _(
			'Incorrect value used for PSK field. It should consist of an even number of hexadecimal characters.'
                    ),__FILE__,__LINE__,TRUE);
		}

		if (strlen($host['tls_psk']) < PSK_MIN_LEN) {
                    bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
			_s('PSK is too short. Minimum is %1$s hex-digits.', PSK_MIN_LEN)
                    ,__FILE__,__LINE__,TRUE);
		}
            }
	}
    }

    /**
    * Validates the input parameters for the create() method.
    *
    * @param array $hosts		hosts data array
    *
    * @throws APIException if the input is invalid.
    */
    protected function validateCreate(array $hosts) {
        $host_db_fields = ['host' => null];
        $groupids = [];

        foreach ($hosts as &$host) {
            // Validate "host" field.
            if (!preg_match('/^'.ZBX_PREG_HOST_FORMAT.'$/', $host['host'])) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                    _s('Incorrect characters used for host name "%s".', $host['host'])
                    ,__FILE__,__LINE__,TRUE);
            }

            // If visible name is not given or empty it should be set to host name. Required for duplicate checks.
            if (!array_key_exists('name', $host) || !trim($host['name'])) {
                $host['name'] = $host['host'];
            }

            // Validate "groups" field.
            if (!array_key_exists('groups', $host) || !is_array($host['groups']) || !$host['groups']) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('No groups for host "%1$s".', $host['host']),__FILE__,__LINE__,TRUE);
            }

            $groupids = array_merge($groupids, zbx_objectValues($host['groups'], 'groupid'));
        }
        unset($host);

        // Check for duplicate "host" and "name" fields.
        $duplicate = bzhyfindDuplicate($hosts, 'host');
        if ($duplicate) {
            bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                _s('Duplicate host. Host with the same host name "%s" already exists in data.', $duplicate['host'])
            ,__FILE__,__LINE__,TRUE);
        }

        $duplicate = bzhyfindDuplicate($hosts, 'name');
        if ($duplicate) {
            bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                _s('Duplicate host. Host with the same visible name "%s" already exists in data.', $duplicate['name'])
                ,__FILE__,__LINE__,TRUE);
        }

        // Validate permissions to host groups.
        if ($groupids) {
            $db_groups = API::HostGroup()->get([
                'output' => ['groupid'],
                'groupids' => $groupids,
                'editable' => true,
                'preservekeys' => true
            ]);
        }

        foreach ($hosts as $host) {
            foreach ($host['groups'] as $group) {
                if (!array_key_exists($group['groupid'], $db_groups)) {
                    bzhyCBase::exception(BZHY_API_ERROR_PERMISSIONS,
                    _('No permissions to referred object or it does not exist!')
                    ,__FILE__,__LINE__,TRUE);
                }
            }
        }
        $host_names = [];

        foreach ($hosts as $host) {
            if (array_key_exists('status', $host)) {
                if (!is_string($host['status']) && !is_int($host['status'])) {
                   bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,  _s('Incorrect status for host "%s".',$host['host']),__FILE__,__LINE__,TRUE);
                }
                if (($host['status'] != HOST_STATUS_MONITORED) && ($host['status'] != HOST_STATUS_NOT_MONITORED)) {
                    bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('Incorrect status for host "%s".', $host['host']),__FILE__,__LINE__,TRUE);
                }
            }

            // Collect technical and visible names to check if they exist in hosts and templates.
            $host_names['host'][$host['host']] = true;
            $host_names['name'][$host['name']] = true;
        }

        $filter = [
            'host' => array_keys($host_names['host']),
            'name' => array_keys($host_names['name'])
        ];

        $hosts_exists = $this->get([
            'output' => ['host', 'name'],
            'filter' => $filter,
            'searchByAny' => true,
            'nopermissions' => true
        ]);

        foreach ($hosts_exists as $host_exists) {
            if (array_key_exists($host_exists['host'], $host_names['host'])) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                    _s('Host with the same name "%s" already exists.', $host_exists['host']),__FILE__,__LINE__,TRUE
            );
        }

        if (array_key_exists($host_exists['name'], $host_names['name'])) {
            bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                _s('Host with the same visible name "%s" already exists.', $host_exists['name'])
                ,__FILE__,__LINE__,__FILE__,__LINE__);
            }
        }
                
        $bzhyTemplate = API::Template(); 
    	$templates_exists = $bzhyTemplate->get([
            'output' => ['host', 'name'],
            'filter' => $filter,
            'searchByAny' => true,
            'nopermissions' => true
        ]);

        foreach ($templates_exists as $template_exists) {
            if (array_key_exists($template_exists['host'], $host_names['host'])) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                    _s('Template with the same name "%s" already exists.', $template_exists['host'])
                    ,__FILE__,__LINE__,TRUE);
            }

            if (array_key_exists($template_exists['name'], $host_names['name'])) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                    _s('Template with the same visible name "%s" already exists.', $template_exists['name'],__FILE__,__LINE__,TRUE)
                );
            }
        }

        $this->validateEncryption($hosts);
    }

	/**
	 * Validates the input parameters for the update() method.
	 *
	 * @param array $hosts			hosts data array
	 * @param array $db_hosts		db hosts data array
	 *
	 * @throws APIException if the input is invalid.
	 */
	protected function validateUpdate(array $hosts, array $db_hosts) {
		$host_db_fields = ['hostid' => null];
		$hosts_full = [];

		foreach ($hosts as $host) {
			// Validate mandatory fields.
			if (!check_db_fields($host_db_fields, $host)) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
					_s('Wrong fields for host "%1$s".', array_key_exists('host', $host) ? $host['host'] : '')
				,__FILE__,__LINE__,TRUE);
			}

			// Validate host permissions.
			if (!array_key_exists($host['hostid'], $db_hosts)) {
				bzhyCDB::exception(ZBX_API_ERROR_PARAMETERS, _(
					'No permissions to referred object or it does not exist!'
				),__FILE__,__LINE__,TRUE);
			}

			// Validate "groups" field.
			if (array_key_exists('groups', $host) && (!is_array($host['groups']) || !$host['groups'])) {
				bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('No groups for host "%1$s".',
					$db_hosts[$host['hostid']]['host'])
				,__FILE__,__LINE__,TRUE);
			}

			// Permissions to host groups is validated in massUpdate().
		}

		//$inventory_fields = zbx_objectValues(getHostInventories(), 'db_field');

        
		$status_validator = new CLimitedSetValidator([
			'values' => [HOST_STATUS_MONITORED, HOST_STATUS_NOT_MONITORED],
			'messageInvalid' => _('Incorrect status for host "%1$s".')
		]);

		$update_discovered_validator = new CUpdateDiscoveredValidator([
			'allowed' => ['hostid', 'status', 'inventory', 'description'],
			'messageAllowedField' => _('Cannot update "%2$s" for a discovered host "%1$s".')
		]);
        
		$host_names = [];

		foreach ($hosts as &$host) {
			$db_host = $db_hosts[$host['hostid']];
			$host_name = array_key_exists('host', $host) ? $host['host'] : $db_host['host'];

			if (array_key_exists('status', $host)) {
				$status_validator->setObjectName($host_name);
				$this->checkValidator($host['status'], $status_validator);
			}

            /*
			if (array_key_exists('inventory', $host) && $host['inventory']) {
				if (array_key_exists('inventory_mode', $host) && $host['inventory_mode'] == HOST_INVENTORY_DISABLED) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _('Cannot set inventory fields for disabled inventory.'));
				}

				$fields = array_keys($host['inventory']);
				foreach ($fields as $field) {
					if (!in_array($field, $inventory_fields)) {
						self::exception(ZBX_API_ERROR_PARAMETERS, _s('Incorrect inventory field "%s".', $field));
					}
				}
			}
            */
            
			// cannot update certain fields for discovered hosts
			$update_discovered_validator->setObjectName($host_name);
			$this->checkPartialValidator($host, $update_discovered_validator, $db_host);
            
           
			if (array_key_exists('bzhyinterfaces', $host) || bzhy_empty($host['bzhyinterfaces'])) {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s('No interfaces for host "%s".', $host['host'])
                    ,__FILE__,__LINE__,TRUE);
			}
            
            
			if (array_key_exists('host', $host)) {
				if (!preg_match('/^'.ZBX_PREG_HOST_FORMAT.'$/', $host['host'])) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Incorrect characters used for host name "%s".', $host['host'])
					,__FILE__,__LINE__,TRUE);
				}

				if (array_key_exists('host', $host_names) && array_key_exists($host['host'], $host_names['host'])) {
					self::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Duplicate host. Host with the same host name "%s" already exists in data.', $host['host'])
					,__FILE__,__LINE__,TRUE);
				}

				$host_names['host'][$host['host']] = $host['hostid'];
			}

			if (array_key_exists('name', $host)) {
				// if visible name is empty replace it with host name
				if (zbx_empty(trim($host['name']))) {
					if (!array_key_exists('host', $host)) {
						bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
							_s('Visible name cannot be empty if host name is missing.')
						,__FILE__,__LINE__,TRUE);
					}
					$host['name'] = $host['host'];
				}

				if (array_key_exists('name', $host_names) && array_key_exists($host['name'], $host_names['name'])) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _s(
						'Duplicate host. Host with the same visible name "%s" already exists in data.', $host['name'])
					,__FILE__,__LINE__,true);
				}
				$host_names['name'][$host['name']] = $host['hostid'];
			}

			$hosts_full[] = zbx_array_merge($db_host, $host);
		}
		unset($host);

		if (array_key_exists('host', $host_names) || array_key_exists('name', $host_names)) {
			$filter = [];

			if (array_key_exists('host', $host_names)) {
				$filter['host'] = array_keys($host_names['host']);
			}

			if (array_key_exists('name', $host_names)) {
				$filter['name'] = array_keys($host_names['name']);
			}

			$hosts_exists = $this->get([
				'output' => ['hostid', 'host', 'name'],
				'filter' => $filter,
				'searchByAny' => true,
				'nopermissions' => true,
				'preservekeys' => true
			]);

			foreach ($hosts_exists as $host_exists) {
				if (array_key_exists('host', $host_names) && array_key_exists($host_exists['host'], $host_names['host'])
						&& bccomp($host_exists['hostid'], $host_names['host'][$host_exists['host']]) != 0) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Host with the same name "%s" already exists.', $host_exists['host'])
					,__FILE__,__LINE__,TRUE);
				}

				if (array_key_exists('name', $host_names) && array_key_exists($host_exists['name'], $host_names['name'])
						&& bccomp($host_exists['hostid'], $host_names['name'][$host_exists['name']]) != 0) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Host with the same visible name "%s" already exists.', $host_exists['name'])
					,__FILE__,__LINE__,TRUE);
				}
			}

			$templates_exists = API::Template()->get([
				'output' => ['hostid', 'host', 'name'],
				'filter' => $filter,
				'searchByAny' => true,
				'nopermissions' => true,
				'preservekeys' => true
			]);

			foreach ($templates_exists as $template_exists) {
				if (array_key_exists('host', $host_names)
						&& array_key_exists($template_exists['host'], $host_names['host'])
						&& bccomp($template_exists['templateid'], $host_names['host'][$template_exists['host']]) != 0) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Template with the same name "%s" already exists.', $template_exists['host'])
					,__FILE__,__LINE__,TRUE);
				}

				if (array_key_exists('name', $host_names)
						&& array_key_exists($template_exists['name'], $host_names['name'])
						&& bccomp($template_exists['templateid'], $host_names['name'][$template_exists['name']]) != 0) {
					bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Template with the same visible name "%s" already exists.', $template_exists['name'])
					,__FILE__,__LINE__,TRUE);
				}
			}
		}

		$this->validateEncryption($hosts_full);
	}
        
    
    public function getHostType($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> [ $this->HostTypeTable => $this->HostTypeTableAlias.".typeid"],
            'from'		=> [ $this->HostTypeTable => $this->HostTypeTable." ".$this->HostTypeTableAlias ],
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
            $sqlParts['where']['typeid'] = dbConditionInt($this->HostTypeTableAlias.'.typeid', $options['ids']);
	}
        
        if (!is_null($options['typename'])) {
            zbx_value2array($options['typename']);
            $sqlParts['where']['typename'] = dbConditionString($this->HostTypeTableAlias.'.typename', $options['typename']);
	}
        
        if (!is_null($options['status'])) {
            zbx_value2array($options['status']);
            $sqlParts['where']['status'] = dbConditionInt($this->HostTypeTableAlias.'.status', $options['status']);
	}
        
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->HostTypeTable, $this->HostTypeTableAlias, $options, $sqlParts);
        $sqlParts = bzhyCDB::applyQuerySortOptions($this->HostTypeTable,$this->HostTypeTableAlias, $options, $sqlParts);
        
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($HostType = DBfetch($res)) {
            if (!is_null($options['countOutput'])) {
		if (!is_null($options['groupCount'])) {
                    $result[] = $HostType;
		}
		else {
                    $result = $HostType['rowscount'];
		}
            }
            else {
		$result[$HostType['typeid']] = $HostType;
            }
        }
        
        return $result;
    }
    
    public function getOS($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> [$this->OsTable => $this->OsTableAlias.'.osid'],
            'from'		=> [$this->OsTable => $this->OsTable.' '.$this->OsTableAlias],
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
            $sqlParts['where']['osid'] = dbConditionInt($this->OsTableAlias.'.osid', $options['ids']);
	}
        
        if (!is_null($options['osname'])) {
            zbx_value2array($options['osname']);
            $sqlParts['where']['osname'] = dbConditionString($this->OsTableAlias.'.osname', $options['osname']);
	}
        
        if (!is_null($options['osbit'])) {
            zbx_value2array($options['osbit']);
            $sqlParts['where']['osbit'] = dbConditionInt($this->OsTableAlias.'.osbit', $options['osbit']);
	}
        
        if (!is_null($options['version'])) {
            zbx_value2array($options['version']);
            $sqlParts['where']['version'] = dbConditionString($this->OsTableAlias.'.version', $options['version']);
	}
        
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->OsTable, $this->OsTableAlias, $options, $sqlParts);
        $sqlParts = bzhyCDB::applyQuerySortOptions($this->OsTable, $this->OsTableAlias, $options, $sqlParts);
        
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
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
    
    public function getBrand($options=[]){
        $result = [];
        
        $sqlParts = [
            'select'	=> [$this->brandTable => $this->brandTableAlias.'.id'],
            'from'		=> [$this->brandTable => $this->brandTable.' '.$this->brandTableAlias ],
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
            $sqlParts['where']['id'] = dbConditionInt($this->brandTableAlias.'.id', $options['ids']);
	}
        
        if (!is_null($options['local_name'])) {
            zbx_value2array($options['local_name']);
            $sqlParts['where']['local_name'] = dbConditionString($this->brandTableAlias.'.local_name', $options['local_name']);
	}
               
        if (!is_null($options['english_name'])) {
            zbx_value2array($options['english_name']);
            $sqlParts['where']['english_name'] = dbConditionString($this->brandTableAlias.'.english_name', $options['english_name']);
	}
        
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->brandTable, $this->brandTableAlias, $options, $sqlParts);
        $sqlParts = bzhyCDB::applyQuerySortOptions($this->brandTable, $this->brandTableAlias, $options, $sqlParts);
        
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
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
    
    public function getHostByinventory($options=[]){
        $result = [];
                        
        $sqlParts = [                                          
            'select'	=> [bzhyCBase::getTableByObject($this->ObjectName) => bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid')],
            'from'		=> [$this->tableName=>bzhyCDB::getFromByObject($this->ObjectName), $this->inventoryTable => $this->inventoryTable.' '.$this->inventoryTableAlias],
            'group'		=> [],
            'order'		=> [],
            'where'             => ['hv' => $this->inventoryTableAlias.'.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid')],
            'limit'		=> null
        ];
        
        $defOptions = [
            'groupids'					=> null,
            'typeids'					=> null,
            'sizeids'					=> null,
            'model'                                     => null,
            'serialno'                                  => null,
            'serviceno'                                 => null,
            'tag'                                       => null,
            'inventory_tag'                             => null,
            'hardinfo'                                  => null,
            'createdate'                                => null,
            'directcreatedata'                          => null,
            'warrantystartdate'                         =>null,
            'directwarrantystartdate'                   =>null,
            'warrantyenddate'                           =>null,
            'directwarrantyenddate'                     =>null,
            'roomids'                                   =>null,
            'boxids'                                    =>null,
            'belongdeviceids'                           =>null,
            'userids'                                   =>null,
            'status'                                    =>null,
            'osids'                                     =>null,
            'brandids'                                   =>null,
            'hostoutput'                                =>API_OUTPUT_EXTEND,
            'typeoutput'                                =>null,
            'roomoutput'                                =>null,
            'boxoutput'                                 =>null,
            'useroutput'                                =>null,
            'osoutput'                                  =>null,
            'brandout'                                  =>null,
            'sortorder'					=> '',
            'limit'					=> null,
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        
        if(!bzhy_empty($options['typeids'])){
            zbx_value2array($options['typeids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.typeid', $options['typeids']);
            if(!bzhy_empty($options['typeoutput'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions($this->HostTypeTable,$this->HostTypeTableAlias, $options['typeoutput'], $sqlParts);
                $sqlParts['where']['vt'] = $this->inventoryTableAlias.'.typeid='.$this->HostTypeTableAlias.'.typeid'; 
            }
        }
        
        if(!bzhy_empty($options['sizeids'])){
            zbx_value2array($options['typeids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.size', $options['typeids']);
        }
        
        if(!bzhy_empty($options['model'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['model'].'%\'';
        }
        
        if(!bzhy_empty($options['serialno'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['serialno'].'%\'';
        }
        
        if(!bzhy_empty($options['serviceno'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['serviceno'].'%\'';
        }
        
        if(!bzhy_empty($options['tag'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['tag'].'%\'';
        }
        
        if(!bzhy_empty($options['inventory_tag'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['inventory_tag'].'%\'';
        }
        
        if(!bzhy_empty($options['hardinfo'])){
            $sqlParts['where'][] = $this->inventoryTableAlias.'model like \'%'.$options['hardinfo'].'%\'';
        }
        
        if(!bzhy_empty($options['createdate']) && !bzhy_empty($options['directcreatedata'])){
            if($options['directcreatedata'] == BZHY_DIRECTDATE_LESS){
                $sqlParts['where'][] = $this->inventoryTableAlias.'createdate <= \''.$options['createdate'].'\'';
            }
            if($options['directcreatedata'] == BZHY_DIRECTDATE_MORE){
                $sqlParts['where'][] = $this->inventoryTableAlias.'createdate >= \''.$options['createdate'].'\'';
            }
        }
        
        if(!bzhy_empty($options['warrantystartdate']) && !bzhy_empty($options['directwarrantystartdate'])){
            if($options['directwarrantystartdate'] == BZHY_DIRECTDATE_LESS){
                $sqlParts['where'][] = $this->inventoryTableAlias.'warrantystartdate <= \''.$options['warrantystartdate'].'\'';
            }  
            if($options['directwarrantystartdate'] == BZHY_DIRECTDATE_MORE){
                $sqlParts['where'][] = $this->inventoryTableAlias.'warrantystartdate >= \''.$options['warrantystartdate'].'\'';
            }
        }
        
        if(!bzhy_empty($options['warrantyenddate']) && !bzhy_empty($options['directwarrantyenddate'])){
            if($options['directwarrantyenddate'] == BZHY_DIRECTDATE_LESS){
                $sqlParts['where'][] = $this->inventoryTableAlias.'warrantyenddate <= \''.$options['warrantyenddate'].'\'';
            }            
            if($options['directwarrantyenddate'] == BZHY_DIRECTDATE_MORE){
                $sqlParts['where'][] = $this->inventoryTableAlias.'warrantyenddate >= \''.$options['warrantyenddate'].'\'';
            }
        }
        
        if(!bzhy_empty($options['roomids'])){
            zbx_value2array($options['roomids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.roomid', $options['roomids']);
            
            if(!bzhy_empty($options['roomoutput'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions(bzhyCBase::getTableByObject('idc_room'), bzhyCBase::getTableAliasByObject('idc_room'), $options['roomoutput'], $sqlParts);
                $sqlParts['where']['vr'] = $this->inventoryTableAlias.'.roomid='.bzhyCDB::getFieldIdByObject('idc_room', 'id'); 
            }
        }
        
        if(!bzhy_empty($options['boxids'])){
            zbx_value2array($options['boxids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.boxid', $options['boxids']);
                        
            if(!bzhy_empty($options['boxoutput'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions(bzhyCBase::getTableByObject('idc_box'), bzhyCBase::getTableAliasByObject('idc_box'), $options['boxoutput'], $sqlParts);
                $sqlParts['where']['vb'] = $this->inventoryTableAlias.'.roomid='.bzhyCDB::getFieldIdByObject('idc_box', 'id'); 
            }
        }
        
        if(!bzhy_empty($options['belongdeviceids'])){
            zbx_value2array($options['belongdeviceids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.belongdeviceid', $options['belongdeviceids']);
        }
        
        if(!bzhy_empty($options['userids'])){
            zbx_value2array($options['userids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.userid', $options['userids']);
            $sqlParts['where']['hv'] = $this->inventoryTableAlias.'.hostid='.bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid');
            
            if(!bzhy_empty($options['useroutput'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions(bzhyCBase::getTableByObject('user'), bzhyCBase::getTableAliasByObject('user'), $options['useroutput'], $sqlParts);
                $sqlParts['where']['vu'] = $this->inventoryTableAlias.'.userid='.bzhyCDB::getFieldIdByObject('user','userid');
            }
        }
        
        if(!bzhy_empty($options['status'])){
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.status', $options['status']);
        }
        
        if(!bzhy_empty($options['osids'])){
            zbx_value2array($options['osids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.osid', $options['osids']);
            if(!bzhy_empty($options['osoutput'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions($this->OsTable,$this->OsTableAlias, $options['osoutput'], $sqlParts);
                $sqlParts['where']['vos'] = $this->inventoryTableAlias.'.osid='.$this->OsTableAlias.'.osid'; 
            }
        }
        
        if(!bzhy_empty($options['brandids'])){
            zbx_value2array($options['brandids']);
            $sqlParts['where'][] = dbConditionInt($this->inventoryTableAlias.'.brandid', $options['brandids']);
            if(!bzhy_empty($options['brandout'])){
                $sqlParts = bzhyCDB::applyQueryOutputOptions($this->brandTable,$this->brandTableAlias, $options['brandout'], $sqlParts);
                $sqlParts['where']['vb'] = $this->inventoryTableAlias.'.brandid='.$this->brandTableAlias.'.id'; 
            }
        }
        
        // limit
        if (zbx_ctype_digit($options['limit']) && $options['limit']) {
            $sqlParts['limit'] = $options['limit'];
        }
        
        $sqlParts = bzhyCDB::applyQueryOutputOptions(bzhyCBase::getTableByObject($this->ObjectName), bzhyCBase::getTableAliasByObject($this->ObjectName), $options['hostoutput'], $sqlParts);
        $sqlParts = bzhyCDB::applyQuerySortOptions(bzhyCDB::getFieldIdByObject($this->ObjectName,'hostid'), bzhyCBase::getTableAliasByObject($this->ObjectName), $options['hostoutput'], $sqlParts);
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($host = DBfetch($res)) {
            $result[$host['hostid']] = $host;
        }
        
        return $result;
    }
    
    /*
    public function getInterface($options=[]){
        $result = [];
        
        $sqlParts = [                                          
            'select'	=> [$this->interfaceTable => bzhyCDB::buildFieldId($this->interfaceTableAlias, 'id')],
            'from'		=> [$this->interfaceTable => bzhyCDB::buildFromId($this->interfaceTable, $this->interfaceTableAlias)],
            'group'		=> [],
            'order'		=> [],
            'where'     => [],
            'limit'		=> null
        ];
        
        $defOptions = [
            'ids'					=> null,
            'mac'					=> null,
            'hostids'               => null,
            'output'                =>API_OUTPUT_EXTEND,
            'hostoutput'            =>NULL,
            'limit'					=> null
        ];
        
        $options = zbx_array_merge($defOptions, $options);
        
        if(!bzhy_empty($options['ids'])){
            zbx_value2array($options['ids']);
            $sqlParts['where'][] = dbConditionInt(bzhyCDB::buildFieldId($this->interfaceTableAlias, 'id'), $options['ids']);
        }
        
        if(!bzhy_empty($options['mac'])){
            zbx_value2array($options['mac']);
            $sqlParts['where'][] = dbConditionString(bzhyCDB::buildFieldId($this->interfaceTableAlias, 'mac'), $options['mac']);
        }
        
        if(!bzhy_empty($options['hostids'])){
            zbx_value2array($options['hostids']);
            $sqlParts['where'][] = dbConditionInt(bzhyCDB::buildFieldId($this->interfaceTableAlias, 'hostid'), $options['hostids']);
        }
        
        $sqlParts = bzhyCDB::applyQueryOutputOptions($this->interfaceTable,$this->interfaceTableAlias, $options['output'], $sqlParts);
        $res = DBselect(bzhyCDB::createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
        while ($interface = DBfetch($res)) {
            $result[$interface['id']] = $interface;
            if(!bzhy_empty($options['hostoutput'])){
                $HostOptions['hostids'] = $interface['hostid'];
                $HostOptions['output'] = $options['hostoutput'];
                $Host = $this->get($HostOptions);
                $result[$interface['id']]['host'] = $Host;
                unset($HostOptions);
            }
        }
        
        return $result;
    }
*/

    protected function link(array $templateIds, array $targetIds) {
        if (empty($templateIds)) {
            return;
	}
        
        // permission check
	if (!API::Template()->isReadable($templateIds)) {
            self::exception(ZBX_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
	}
        
        // check if someone passed duplicate templates in the same query
	$templateIdDuplicates = zbx_arrayFindDuplicates($templateIds);
	if (!zbx_empty($templateIdDuplicates)) {
            $duplicatesFound = [];
            foreach ($templateIdDuplicates as $value => $count) {
		$duplicatesFound[] = _s('template ID "%1$s" is passed %2$s times', $value, $count);
            }
            bzhyCBase::exception(
		ZBX_API_ERROR_PARAMETERS,
                 _s('Cannot pass duplicate template IDs for the linkage: %s.', implode(', ', $duplicatesFound))
		,__FILE__,__LINE__,TRUE);
	}
        
        // get DB templates which exists in all targets
	$res = DBselect('SELECT * FROM hosts_templates WHERE '.dbConditionInt('hostid', $targetIds));
	$mas = [];
	while ($row = DBfetch($res)) {
            if (!isset($mas[$row['templateid']])) {
		$mas[$row['templateid']] = [];
            }
            $mas[$row['templateid']][$row['hostid']] = 1;
	}
	$targetIdCount = count($targetIds);
	$commonDBTemplateIds = [];
	foreach ($mas as $templateId => $targetList) {
            if (count($targetList) == $targetIdCount) {
		$commonDBTemplateIds[] = $templateId;
            }
	}
        
        // check if there are any template with triggers which depends on triggers in templates which will be not linked
	$commonTemplateIds = array_unique(array_merge($commonDBTemplateIds, $templateIds));
	foreach ($templateIds as $templateid) {
            $triggerids = [];
            $dbTriggers = get_triggers_by_hostid($templateid);
            while ($trigger = DBfetch($dbTriggers)) {
                $triggerids[$trigger['triggerid']] = $trigger['triggerid'];
            }

            $sql = 'SELECT DISTINCT h.host'.
                ' FROM trigger_depends td,functions f,items i,hosts h'.
		' WHERE ('.
		dbConditionInt('td.triggerid_down', $triggerids).
		' AND f.triggerid=td.triggerid_up'.
		' )'.
		' AND i.itemid=f.itemid'.
		' AND h.hostid=i.hostid'.
		' AND '.dbConditionInt('h.hostid', $commonTemplateIds, true).
		' AND h.status='.HOST_STATUS_TEMPLATE;
            if ($dbDepHost = DBfetch(DBselect($sql))) {
		$tmpTpls = API::Template()->get([
                    'templateids' => $templateid,
                    'output'=> API_OUTPUT_EXTEND
		]);
		$tmpTpl = reset($tmpTpls);
		bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS,
                    _s('Trigger in template "%1$s" has dependency with trigger in template "%2$s".', $tmpTpl['host'], $dbDepHost['host'])
                    ,__FILE__,__LINE__,TRUE);
            }
	}
        
        $res = DBselect(
            'SELECT ht.hostid,ht.templateid'.
            ' FROM hosts_templates ht'.
            ' WHERE '.dbConditionInt('ht.hostid', $targetIds).
            ' AND '.dbConditionInt('ht.templateid', $templateIds)
	);
	$linked = [];
	while ($row = DBfetch($res)) {
            if (!isset($linked[$row['hostid']])) {
		$linked[$row['hostid']] = [];
            }
            $linked[$row['hostid']][$row['templateid']] = 1;
	}

	// add template linkages, if problems rollback later
	$hostsLinkageInserts = [];
	foreach ($targetIds as $targetid) {
            foreach ($templateIds as $templateid) {
		if (isset($linked[$targetid]) && isset($linked[$targetid][$templateid])) {
                    continue;
		}
		$hostsLinkageInserts[] = ['hostid' => $targetid, 'templateid' => $templateid];
            }
	}
	bzhyCDB::insert('hosts_templates', $hostsLinkageInserts);
        
        // check if all trigger templates are linked to host.
	// we try to find template that is not linked to hosts ($targetids)
	// and exists trigger which reference that template and template from ($templateids)
	$sql = 'SELECT DISTINCT h.host'.
            ' FROM functions f,items i,triggers t,hosts h'.
            ' WHERE f.itemid=i.itemid'.
            ' AND f.triggerid=t.triggerid'.
            ' AND i.hostid=h.hostid'.
            ' AND h.status='.HOST_STATUS_TEMPLATE.
            ' AND NOT EXISTS (SELECT 1 FROM hosts_templates ht WHERE ht.templateid=i.hostid AND '.dbConditionInt('ht.hostid', $targetIds).')'.
            ' AND EXISTS (SELECT 1 FROM functions ff,items ii WHERE ff.itemid=ii.itemid AND ff.triggerid=t.triggerid AND '.dbConditionInt('ii.hostid', $templateIds). ')';
	if ($dbNotLinkedTpl = DBfetch(DBSelect($sql, 1))) {
            bzhyCBase::exception(
		ZBX_API_ERROR_PARAMETERS,
		_s('Trigger has items from template "%1$s" that is not linked to host.', $dbNotLinkedTpl['host'])
		,__FILE__,__LINE__,TRUE);
	}

	// check template linkage circularity
	$res = DBselect(
            'SELECT ht.hostid,ht.templateid'.
            ' FROM hosts_templates ht,hosts h'.
            ' WHERE ht.hostid=h.hostid '.
            ' AND h.status IN('.HOST_STATUS_MONITORED.','.HOST_STATUS_NOT_MONITORED.','.HOST_STATUS_TEMPLATE.')'
	);
        
        // build linkage graph and prepare list for $rootList generation
	$graph = [];
	$hasParentList = [];
	$hasChildList = [];
	$all = [];
	while ($row = DBfetch($res)) {
            if (!isset($graph[$row['hostid']])) {
		$graph[$row['hostid']] = [];
            }
            $graph[$row['hostid']][] = $row['templateid'];
            $hasParentList[$row['templateid']] = $row['templateid'];
            $hasChildList[$row['hostid']] = $row['hostid'];
            $all[$row['templateid']] = $row['templateid'];
            $all[$row['hostid']] = $row['hostid'];
	}
        
        // get list of templates without parents
	$rootList = [];
	foreach ($hasChildList as $parentId) {
            if (!isset($hasParentList[$parentId])) {
		$rootList[] = $parentId;
            }
	}
        
        // search cycles and double linkages in rooted parts of graph
	$visited = [];
	foreach ($rootList as $root) {
            $path = [];
            // raise exception on cycle or double linkage
            $this->checkCircularAndDoubleLinkage($graph, $root, $path, $visited);
	}

	// there is still possible cycles without root
	if (count($visited) < count($all)) {
            bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Circular template linkage is not allowed.'),__FILE__,__LINE__,TRUE);
	}

	foreach ($hostsLinkageInserts as $hostTplIds){
            Manager::Application()->link($hostTplIds['templateid'], $hostTplIds['hostid']);

            API::DiscoveryRule()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::ItemPrototype()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::HostPrototype()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::Item()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            Manager::HttpTest()->link($hostTplIds['templateid'], $hostTplIds['hostid']);
	}

	// we do linkage in two separate loops because for triggers you need all items already created on host
	foreach ($hostsLinkageInserts as $hostTplIds){
            API::Trigger()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::TriggerPrototype()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::GraphPrototype()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);

            API::Graph()->syncTemplates([
		'hostids' => $hostTplIds['hostid'],
		'templateids' => $hostTplIds['templateid']
            ]);
	}

	foreach ($hostsLinkageInserts as $hostTplIds){
            API::Trigger()->syncTemplateDependencies([
		'templateids' => $hostTplIds['templateid'],
		'hostids' => $hostTplIds['hostid']
            ]);
            
            API::TriggerPrototype()->syncTemplateDependencies([
		'templateids' => $hostTplIds['templateid'],
		'hostids' => $hostTplIds['hostid']
            ]);
	}
	return $hostsLinkageInserts;
    }
    
    /**
    * Searches for cycles and double linkages in graph.
    *
    * @throw APIException rises exception if cycle or double linkage is found
    *
    * @param array $graph - array with keys as parent ids and values as arrays with child ids
    * @param int $current - cursor for recursive DFS traversal, starting point for algorithm
    * @param array $path - should be passed empty array for DFS
    * @param array $visited - there will be stored visited graph node ids
    *
    * @return boolean
    */
    protected function checkCircularAndDoubleLinkage($graph, $current, &$path, &$visited) {
	if (isset($path[$current])) {
            if ($path[$current] == 1) {
		bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Circular template linkage is not allowed.'),__FILE__,__LINE__,TRUE);
            }
            else {
                bzhyCBase::exception(ZBX_API_ERROR_PARAMETERS, _('Template cannot be linked to another template more than once even through other templates.'),__FILE__,__LINE__,TRUE);
            }
	}
	$path[$current] = 1;
	$visited[$current] = 1;

	if (isset($graph[$current])) {
            foreach ($graph[$current] as $next) {
		$this->checkCircularAndDoubleLinkage($graph, $next, $path, $visited);
            }
	}

	$path[$current] = 2;

	return false;
    }
    
    protected function updateHostStatus($hostids, $status) {
        zbx_value2array($hostids);

        $hostIds = [];
        $oldStatus = ($status == HOST_STATUS_MONITORED ? HOST_STATUS_NOT_MONITORED : HOST_STATUS_MONITORED);
        
        $options = ["output"=>['hostid',"host","status"],"filter"=>['hostid'=>$hostids,'status'=>$oldStatus],
            'preservekeys' =>TRUE];
        
        $db_hosts = bzhyCDB::select($this->tableName, $options);
        /*
        $db_hosts = DBselect(
		'SELECT h.hostid,h.host,h.status'.
		' FROM hosts h'.
		' WHERE '.dbConditionInt('h.hostid', $hostids).
			' AND h.status='.zbx_dbstr($oldStatus)
        );
         * 
         */
        //while ($host = DBfetch($db_hosts)) {
		foreach ($db_hosts as $host){
            $hostIds[] = $host['hostid'];
            $host_new = $host;
            $host_new['status'] = $status;
            add_audit_ext(AUDIT_ACTION_UPDATE, AUDIT_RESOURCE_HOST, $host['hostid'], $host['host'], 'hosts', $host, $host_new);
            info(_s('Updated status of host "%1$s".', $host['host']));
        }

        return bzhyCDB::update($this->tableName, [
            'values' => ['status' => $status],
            'where' => ['hostid' => $hostIds]
        ]);
    }
}

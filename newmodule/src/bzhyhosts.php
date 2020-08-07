<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/include/forms.inc.php';
require_once dirname(__FILE__).'/bzhyclasses/common/bzhyCBase.php';
bzhyCBase::run(bzhyCBase::BZHY_RUN_MODE_DEFAULT);

if (hasRequest('action') && getRequest('action') == 'host.export' && hasRequest('hosts')) {
	$page['file'] = 'zbx_export_hosts.xml';
	$page['type'] = detect_page_type(PAGE_TYPE_XML);

	$exportData = true;
}
else {
    $page['title'] =  _('Configuration of hosts');
    $page['file'] = 'bzhyhosts.php';
    $page['hist_arg'] = array();
    $page['scripts'] = ["multiselect.js"];
    $page['bzhyscripts'] = ["bzhycommon.js","bzhyjquery-3.3.1.min.js","bzhyclass.calendar.js"];
    $page['type'] = detect_page_type(PAGE_TYPE_HTML);
    $exportData = false;
}

require_once dirname(__FILE__).'/bzhyinclude/bzhypage_header.php';
$defaultports = [INTERFACE_TYPE_AGENT =>BZHY_AGENT_DEFAULT_PORT,INTERFACE_TYPE_SNMP=>BZHY_SNMP_DEFAULT_PORT,
                    INTERFACE_TYPE_JMX=>BZHY_JMX_DEFAULT_PORT,INTERFACE_TYPE_IPMI=>BZHY_IPMI_DEFAULT_PORT];

/*
 * Permissions
 */
if (getRequest('groupid') && !API::HostGroup()->isWritable([$_REQUEST['groupid']])) {
	access_deny();
}
if (getRequest('hostid') && !API::Host()->isWritable([$_REQUEST['hostid']])) {
	access_deny();
}

$hostIds = getRequest('hosts', []);

/*
 * Export
 */
if ($exportData) {
	$export = new CConfigurationExport(['hosts' => $hostIds]);
	$export->setBuilder(new CConfigurationExportBuilder());
	$export->setWriter(CExportWriterFactory::getWriter(CExportWriterFactory::XML));
	$exportData = $export->export();

	if (hasErrorMesssages()) {
		show_messages();
	}
	else {
		print($exportData);
	}

	exit;
}

/*
 * Filter
 */
if (hasRequest('filter_set')) {
	CProfile::update('web.hosts.filter_ip', getRequest('filter_ip', ''), PROFILE_TYPE_STR);
	CProfile::update('web.hosts.filter_dns', getRequest('filter_dns', ''), PROFILE_TYPE_STR);
	CProfile::update('web.hosts.filter_host', getRequest('filter_host', ''), PROFILE_TYPE_STR);
	CProfile::update('web.hosts.filter_port', getRequest('filter_port', ''), PROFILE_TYPE_STR);
}
elseif (hasRequest('filter_rst')) {
	DBStart();
	CProfile::delete('web.hosts.filter_ip');
	CProfile::delete('web.hosts.filter_dns');
	CProfile::delete('web.hosts.filter_host');
	CProfile::delete('web.hosts.filter_port');
	DBend();
}

$filter['ip'] = CProfile::get('web.hosts.filter_ip', '');
$filter['dns'] = CProfile::get('web.hosts.filter_dns', '');
$filter['host'] = CProfile::get('web.hosts.filter_host', '');
$filter['port'] = CProfile::get('web.hosts.filter_port', '');

// remove inherited macros data (actions: 'add', 'update' and 'form')
$macros = cleanInheritedMacros(getRequest('macros', []));

// remove empty new macro lines
foreach ($macros as $idx => $macro) {
	if (!array_key_exists('hostmacroid', $macro) && $macro['macro'] === '' && $macro['value'] === '') {
		unset($macros[$idx]);
	}
}

/*
 * Actions
 */
if (isset($_REQUEST['add_template']) && isset($_REQUEST['add_templates'])) {
	$_REQUEST['templates'] = getRequest('templates', []);
	$_REQUEST['templates'] = array_merge($_REQUEST['templates'], $_REQUEST['add_templates']);
}
if (isset($_REQUEST['unlink']) || isset($_REQUEST['unlink_and_clear'])) {
	$_REQUEST['clear_templates'] = getRequest('clear_templates', []);

	$unlinkTemplates = [];

	if (isset($_REQUEST['unlink'])) {
		// templates_rem for old style removal in massupdate form
		if (isset($_REQUEST['templates_rem'])) {
			$unlinkTemplates = array_keys($_REQUEST['templates_rem']);
		}
		elseif (is_array($_REQUEST['unlink'])) {
			$unlinkTemplates = array_keys($_REQUEST['unlink']);
		}
	}
	else {
		$unlinkTemplates = array_keys($_REQUEST['unlink_and_clear']);

		$_REQUEST['clear_templates'] = array_merge($_REQUEST['clear_templates'], $unlinkTemplates);
	}

	foreach ($unlinkTemplates as $templateId) {
		unset($_REQUEST['templates'][array_search($templateId, $_REQUEST['templates'])]);
	}
}
elseif ((hasRequest('clone') || hasRequest('full_clone')) && hasRequest('hostid')) {
	$_REQUEST['form'] = hasRequest('clone') ? 'clone' : 'full_clone';

	$groupids = getRequest('groups', []);
	if ($groupids) {
		// leave only writable groups
		$_REQUEST['groups'] = array_keys(API::HostGroup()->get([
			'output' => [],
			'groupids' => $groupids,
			'editable' => true,
			'preservekeys' => true
		]));
	}

	if (hasRequest('interfaces')) {
		$interfaceid = 1;
		foreach ($_REQUEST['interfaces'] as &$interface) {
			$interface['interfaceid'] = (string) $interfaceid++;
			unset($interface['locked'], $interface['items']);
		}
		unset($interface);
	}

	if (hasRequest('full_clone')) {
		$_REQUEST['clone_hostid'] = $_REQUEST['hostid'];
	}

	unset($_REQUEST['hostid'], $_REQUEST['flags']);
}
elseif (hasRequest('action') && getRequest('action') == 'host.massupdate' && hasRequest('masssave')) {
	$hostIds = getRequest('hosts', []);
	$visible = getRequest('visible', []);
	$_REQUEST['proxy_hostid'] = getRequest('proxy_hostid', 0);
	$_REQUEST['templates'] = getRequest('templates', []);

	try {
		DBstart();

		// filter only normal and discovery created hosts
		$hosts = API::Host()->get([
			'output' => ['hostid'],
			'hostids' => $hostIds,
			'filter' => ['flags' => [ZBX_FLAG_DISCOVERY_NORMAL, ZBX_FLAG_DISCOVERY_CREATED]]
		]);
		$hosts = ['hosts' => $hosts];

		$properties = [
			'proxy_hostid', 'ipmi_authtype', 'ipmi_privilege', 'ipmi_username', 'ipmi_password', 'description'
		];

		$newValues = [];
		foreach ($properties as $property) {
			if (isset($visible[$property])) {
				$newValues[$property] = $_REQUEST[$property];
			}
		}

		if (isset($visible['status'])) {
			$newValues['status'] = getRequest('status', HOST_STATUS_NOT_MONITORED);
		}

		if (isset($visible['inventory_mode'])) {
			$newValues['inventory_mode'] = getRequest('inventory_mode', HOST_INVENTORY_DISABLED);
			$newValues['inventory'] = ($newValues['inventory_mode'] == HOST_INVENTORY_DISABLED)
				? []
				: getRequest('host_inventory', []);
		}

		if (array_key_exists('encryption', $visible)) {
			$newValues['tls_connect'] = getRequest('tls_connect', HOST_ENCRYPTION_NONE);
			$newValues['tls_accept'] = getRequest('tls_accept', HOST_ENCRYPTION_NONE);
			$newValues['tls_issuer'] = getRequest('tls_issuer', '');
			$newValues['tls_subject'] = getRequest('tls_subject', '');
			$newValues['tls_psk_identity'] = getRequest('tls_psk_identity', '');
			$newValues['tls_psk'] = getRequest('tls_psk', '');
		}

		$templateIds = [];
		if (isset($visible['templates'])) {
			$templateIds = $_REQUEST['templates'];
		}

		// add new or existing host groups
		$newHostGroupIds = [];
		if (isset($visible['new_groups']) && !empty($_REQUEST['new_groups'])) {
			if (CWebUser::getType() == USER_TYPE_SUPER_ADMIN) {
				foreach ($_REQUEST['new_groups'] as $newGroup) {
					if (is_array($newGroup) && isset($newGroup['new'])) {
						$newGroups[] = ['name' => $newGroup['new']];
					}
					else {
						$newHostGroupIds[] = $newGroup;
					}
				}

				if (isset($newGroups)) {
					if (!$createdGroups = API::HostGroup()->create($newGroups)) {
						throw new Exception();
					}

					$newHostGroupIds = $newHostGroupIds
						? array_merge($newHostGroupIds, $createdGroups['groupids'])
						: $createdGroups['groupids'];
				}
			}
			else {
				$newHostGroupIds = getRequest('new_groups');
			}
		}

		if (isset($visible['groups'])) {
			if (isset($_REQUEST['groups'])) {
				$replaceHostGroupsIds = $newHostGroupIds
					? array_unique(array_merge(getRequest('groups'), $newHostGroupIds))
					: $_REQUEST['groups'];
			}
			elseif ($newHostGroupIds) {
				$replaceHostGroupsIds = $newHostGroupIds;
			}

			if (isset($replaceHostGroupsIds)) {
				$hosts['groups'] = API::HostGroup()->get([
					'groupids' => $replaceHostGroupsIds,
					'editable' => true,
					'output' => ['groupid']
				]);
			}
			else {
				$hosts['groups'] = [];
			}
		}
		elseif ($newHostGroupIds) {
			$newHostGroups = API::HostGroup()->get([
				'groupids' => $newHostGroupIds,
				'editable' => true,
				'output' => ['groupid']
			]);
		}

		if (isset($_REQUEST['mass_replace_tpls'])) {
			if (isset($_REQUEST['mass_clear_tpls'])) {
				$hostTemplates = API::Template()->get([
					'output' => ['templateid'],
					'hostids' => $hostIds
				]);

				$hostTemplateIds = zbx_objectValues($hostTemplates, 'templateid');
				$templatesToDelete = array_diff($hostTemplateIds, $templateIds);

				$hosts['templates_clear'] = zbx_toObject($templatesToDelete, 'templateid');
			}

			$hosts['templates'] = $templateIds;
		}

		$result = API::Host()->massUpdate(array_merge($hosts, $newValues));
		if ($result === false) {
			throw new Exception();
		}

		$add = [];
		if ($templateIds && isset($visible['templates'])) {
			$add['templates'] = $templateIds;
		}

		// add new host groups
		if ($newHostGroupIds && (!isset($visible['groups']) || !isset($replaceHostGroups))) {
			$add['groups'] = zbx_toObject($newHostGroupIds, 'groupid');
		}

		if ($add) {
			$add['hosts'] = $hosts['hosts'];

			$result = API::Host()->massAdd($add);

			if ($result === false) {
				throw new Exception();
			}
		}

		DBend(true);

		uncheckTableRows();
		show_message(_('Hosts updated'));

		unset($_REQUEST['massupdate'], $_REQUEST['form'], $_REQUEST['hosts']);
	}
	catch (Exception $e) {
		DBend(false);
		show_error_message(_('Cannot update hosts'));
	}
}
elseif (hasRequest('add') || hasRequest('update')) {
    try {
        DBstart();
        $hostId = getRequest('hostid', 0);
        if ($hostId != 0) {
            $create = false;
            $msgOk = _('Host updated');
            $msgFail = _('Cannot update host');
            $dbHost = bzhyAPI::Host()->get([
                'output' => API_OUTPUT_EXTEND,
                'hostids' => $hostId,
                'editable' => true
            ]);
            $dbHost = reset($dbHost);
        }
        else {
            $create = true;
            $msgOk = _('Host added');
            $msgFail = _('Cannot add host');
        }
        // host data
        if (!$create && $dbHost['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
            $host = [
                'hostid' => $hostId,
                'status' => getRequest('status', HOST_STATUS_NOT_MONITORED),
                'description' => getRequest('description', ''),
                'inventory' => (getRequest('inventory_mode') == HOST_INVENTORY_DISABLED)
                ? []
                : getRequest('host_inventory', [])
            ];
        }
        else {
            // templates
            $templates = [];
            foreach (getRequest('templates', []) as $templateId) {
                $templates[] = ['templateid' => $templateId];
            }

            // interfaces
            $interfaces = getRequest('interfaces', []);
            $enable = getRequest('enable',[]);
            $port = getRequest('port',[]);
            $bulk = getRequest('bulk',[]);
            $zabbixInterfaces = [];
            $bzhyInterfaces = [];
            foreach ($interfaces as $key => $interface) {   
                if ( bzhy_empty($interface['name'])|| bzhy_empty($interface['ip'])) {
                    unset($interface[$key]);
                    continue;
                }
                                                                
                $bzhyInterfaces[$key]['name'] = $interface['name'];
                $bzhyInterfaces[$key]['ip'] = $interface['ip'];
                $bzhyInterfaces[$key]['mask'] = $interface['mask'];
                $bzhyInterfaces[$key]['dns'] = $interface['dns'];
                $bzhyInterfaces[$key]['useip'] = ($interface['useip'] == INTERFACE_USE_IP)?INTERFACE_USE_IP:INTERFACE_USE_DNS;
                $bzhyInterfaces[$key]['type'] = ($interface['type'] == BZHYINTERFACE_TYPE_ENTITY)?BZHYINTERFACE_TYPE_ENTITY:BZHYINTERFACE_TYPE_DUMMY;
                $bzhyInterfaces[$key]['mac'] = '';
                $bzhyInterfaces[$key]['port'] = [];
                $bzhyInterfaces[$key]['status'] = BZHY_STATUS_RUNING;
                $bzhyInterfaces[$key]['description'] = '';
            }
            
            foreach ([INTERFACE_TYPE_AGENT,INTERFACE_TYPE_SNMP,INTERFACE_TYPE_JMX,INTERFACE_TYPE_IPMI] as $kind){
                if(isset($enable[$kind])){
                    $interfaceid = $enable[$kind];
                    if(isset($bzhyInterfaces[$interfaceid])){
                        $bzhyInterfaces[$interfaceid]['port'][$kind] = isset($port[$interfaceid][$kind])?$port[$interfaceid][$kind]
                            :$defaultports[$kind];
                        if($kind == INTERFACE_TYPE_SNMP){
                            $bzhyInterfaces[$interfaceid]['bulk'] = isset($bulk[$interfaceid]['bulk'])?SNMP_BULK_ENABLED:SNMP_BULK_DISABLED;
                        }
                    }
                }                                                                                                                                
            }
			
            // new group
            $groups = getRequest('groups', []);
            $newGroup = getRequest('newgroup');

            if (!bzhy_empty($newGroup)) {
                $newGroup = bzhyAPI::Group()->create(['name' => $newGroup]);
                if (!$newGroup) {
                    throw new Exception();
                }
                $groups[] = reset($newGroup['groupids']);
            }
            $groups = zbx_toObject($groups, 'groupid');
            
            //bzhy inventory
            $bzhyinventory=[
                'typeid' =>getRequest('typeid'),
                'brandid' =>getRequest('brandid'),
                'size' =>getRequest('size'),
                'model' =>getRequest('model'),
                'serialno' =>getRequest('serialno'),
                'serviceno' =>getRequest('serviceno'),
                'hardinfo' =>getRequest('hardinfo'),
                'dns' =>getRequest('dns'),
                'gw' =>getRequest('gw'),
                'roomid' =>getRequest('roomid'),
                'boxid' =>getRequest('boxid'),
                'position' =>getRequest('position'),
                'belongdeviceid' =>getRequest('belongdeviceid'),
                'osid' =>getRequest('osid'),
                'contacts' =>getRequest('contacts'),
                'files' =>getRequest('files'),
                'tag' => getRequest('tag'),
                'inventory_tag' => getRequest('inventory_tag')
            ];
            
            $bzhyinventory['belongdeviceid']= bzhy_empty($bzhyinventory['belongdeviceid'])?0:$bzhyinventory['belongdeviceid'];
            $bzhyinventory['createdate'] = zbxDateToTime(getRequest('createdate_year').getRequest('createdate_month').getRequest('createdate_day')."0000");
            $bzhyinventory['warrantystartdate'] = zbxDateToTime(getRequest('warrantystartdate_year').getRequest('warrantystartdate_month').getRequest('warrantystartdate_day')."0000");
            $bzhyinventory['warrantyenddate'] = zbxDateToTime(getRequest('warrantyenddate_year').getRequest('warrantyenddate_month').getRequest('warrantyenddate_day')."0000");
            $bzhyinventory['desc'] = getRequest('description','');
            $bzhyinventory['userid'] = CWebUser::$data['userid'];
            $bzhyinventory['isruning'] = BZHY_STATUS_RUNING;
            $bzhyinventory['status'] = BZHY_STATUS_RUNING;         
            // host data
            $host = [
                'host' => getRequest('host'),
                'name' => getRequest('visiblename'),
                'status' => getRequest('status', HOST_STATUS_NOT_MONITORED),
                'description' => getRequest('description'),
                'proxy_hostid' => getRequest('proxy_hostid', 0),
                'ipmi_authtype' => getRequest('ipmi_authtype'),
                'ipmi_privilege' => getRequest('ipmi_privilege'),
                'ipmi_username' => getRequest('ipmi_username'),
                'ipmi_password' => getRequest('ipmi_password'),
                'tls_connect' => getRequest('tls_connect', HOST_ENCRYPTION_NONE),
                'tls_accept' => getRequest('tls_accept', HOST_ENCRYPTION_NONE),
                'tls_issuer' => getRequest('tls_issuer'),
                'tls_subject' => getRequest('tls_subject'),
                'tls_psk_identity' => getRequest('tls_psk_identity'),
                'tls_psk' => getRequest('tls_psk'),
                'groups' => $groups,
                'templates' => $templates,
                'bzhyinterfaces' => $bzhyInterfaces,
                'macros' => $macros,
                'inventory_mode' => getRequest('inventory_mode'),
                'inventory' =>[], 
                'bzhyinventory' => $bzhyinventory
            ];

            if (!$create) {
                $host['templates_clear'] = zbx_toObject(getRequest('clear_templates', []), 'templateid');
            }
        }
        if ($create) {
            $hostsIds = bzhyAPI::Host()->create($host);
            if (!bzhy_empty($hostsIds)) {
                $hostId = reset($hostsIds['hostids']);
            }
            else {
                throw new Exception();
            }

            add_audit_ext(AUDIT_ACTION_ADD, AUDIT_RESOURCE_HOST, $hostId, $host['host'], null, null, null);
        }
        else {
            $host['hostid'] = $hostId;
            if (!bzhyAPI::Host()->update($host)) {
                throw new Exception();
            }
            $dbHostNew = bzhyAPI::Host()->get([
                'output' => API_OUTPUT_EXTEND,
                'hostids' => $hostId,
                'editable' => true
            ]);
            $dbHostNew = reset($dbHostNew);
            add_audit_ext(AUDIT_ACTION_UPDATE, AUDIT_RESOURCE_HOST, $dbHostNew['hostid'], $dbHostNew['host'], 'hosts',
                $dbHost, $dbHostNew);
        }

        // full clone
        if (getRequest('form', '') === 'full_clone' && getRequest('clone_hostid', 0) != 0) {
            $srcHostId = getRequest('clone_hostid');
            // copy applications
            if (!copyApplications($srcHostId, $hostId)) {
                throw new Exception();
            }
            // copy items
            if (!copyItems($srcHostId, $hostId)) {
                throw new Exception();
            }

            // copy web scenarios
            if (!copyHttpTests($srcHostId, $hostId)) {
                throw new Exception();
            }

            // copy triggers
            $dbTriggers = API::Trigger()->get([
                'output' => ['triggerid'],
                'hostids' => $srcHostId,
                'inherited' => false,
                'filter' => ['flags' => ZBX_FLAG_DISCOVERY_NORMAL]
            ]);

            if ($dbTriggers) {
                if (!copyTriggersToHosts(zbx_objectValues($dbTriggers, 'triggerid'), $hostId, $srcHostId)) {
                    throw new Exception();
                }
            }

            // copy discovery rules
            $dbDiscoveryRules = API::DiscoveryRule()->get([
                'output' => ['itemid'],
                'hostids' => $srcHostId,
                'inherited' => false
            ]);

            if ($dbDiscoveryRules) {
                $copyDiscoveryRules = API::DiscoveryRule()->copy([
                    'discoveryids' => zbx_objectValues($dbDiscoveryRules, 'itemid'),
                    'hostids' => [$hostId]
                ]);
                if (!$copyDiscoveryRules) {
                    throw new Exception();
                }
            }

            // copy graphs
            $dbGraphs = API::Graph()->get([
                'output' => API_OUTPUT_EXTEND,
                'selectHosts' => ['hostid'],
                'selectItems' => ['type'],
                'hostids' => $srcHostId,
                'filter' => ['flags' => ZBX_FLAG_DISCOVERY_NORMAL],
                'inherited' => false
            ]);

            foreach ($dbGraphs as $dbGraph) {
                if (count($dbGraph['hosts']) > 1) {
                    continue;
                }
                if (httpItemExists($dbGraph['items'])) {
                    continue;
                }
                if (!copyGraphToHost($dbGraph['graphid'], $hostId)) {
                    throw new Exception();
                }
            }
        }

        $result = DBend(true);

        if ($result) {
            uncheckTableRows();
        }
        show_messages($result, $msgOk, $msgFail);

        unset($_REQUEST['form'], $_REQUEST['hostid']);
    }
    catch (Exception $e) {
        DBend(false);
        //show_messages(false, $msgOk, $msgFail);
        show_messages(false,null, $e->getMessage());
    }
}
elseif (hasRequest('delete') && hasRequest('hostid')) {
	DBstart();

	$result = API::Host()->delete([getRequest('hostid')]);
	$result = DBend($result);

	if ($result) {
		unset($_REQUEST['form'], $_REQUEST['hostid']);
		uncheckTableRows();
	}
	show_messages($result, _('Host deleted'), _('Cannot delete host'));

	unset($_REQUEST['delete']);
}
elseif (hasRequest('action') && getRequest('action') == 'host.massdelete' && hasRequest('hosts')) {
	DBstart();

	$result = API::Host()->delete(getRequest('hosts'));
	$result = DBend($result);

	if ($result) {
		uncheckTableRows();
	}
	show_messages($result, _('Host deleted'), _('Cannot delete host'));
}
elseif (hasRequest('action') && str_in_array(getRequest('action'), ['host.massenable', 'host.massdisable']) && hasRequest('hosts')) {
	$enable =(getRequest('action') == 'host.massenable');
	$status = $enable ? TRIGGER_STATUS_ENABLED : TRIGGER_STATUS_DISABLED;

	$actHosts = API::Host()->get([
		'hostids' => getRequest('hosts'),
		'editable' => true,
		'templated_hosts' => true,
		'output' => ['hostid']
	]);
	$actHosts = zbx_objectValues($actHosts, 'hostid');

	if ($actHosts) {
		DBstart();

		$result = updateHostStatus($actHosts, $status);
		$result = DBend($result);

		if ($result) {
			uncheckTableRows();
		}

		$updated = count($actHosts);

		$messageSuccess = $enable
			? _n('Host enabled', 'Hosts enabled', $updated)
			: _n('Host disabled', 'Hosts disabled', $updated);
		$messageFailed = $enable
			? _n('Cannot enable host', 'Cannot enable hosts', $updated)
			: _n('Cannot disable host', 'Cannot disable hosts', $updated);

		show_messages($result, $messageSuccess, $messageFailed);
	}
}

/*
 * Display
 */
$pageFilter = new CPageFilter([
	'groups' => [
		'real_hosts' => true,
		'editable' => true
	],
	'groupid' => getRequest('groupid')
]);

$_REQUEST['groupid'] = $pageFilter->groupid;
$_REQUEST['hostid'] = getRequest('hostid', 0);

$config = select_config();
if(isset($System_Settings['default_inventory_mode'])){
    $config['default_inventory_mode'] = $System_Settings['default_inventory_mode'];
}

if (hasRequest('action') && getRequest('action') === 'host.massupdateform' && hasRequest('hosts')) {
    $data = [
	'hosts' => getRequest('hosts'),
	'visible' => getRequest('visible', []),
	'mass_replace_tpls' => getRequest('mass_replace_tpls'),
	'mass_clear_tpls' => getRequest('mass_clear_tpls'),
	'groups' => getRequest('groups', []),
	'newgroup' => getRequest('newgroup', ''),
	'status' => getRequest('status', HOST_STATUS_MONITORED),
	'description' => getRequest('description'),
	'proxy_hostid' => getRequest('proxy_hostid', ''),
	'ipmi_authtype' => getRequest('ipmi_authtype', IPMI_AUTHTYPE_DEFAULT),
	'ipmi_privilege' => getRequest('ipmi_privilege', IPMI_PRIVILEGE_USER),
	'ipmi_username' => getRequest('ipmi_username', ''),
	'ipmi_password' => getRequest('ipmi_password', ''),
	'inventory_mode' => getRequest('inventory_mode', HOST_INVENTORY_DISABLED),
	'host_inventory' => getRequest('host_inventory', []),
	'templates' => getRequest('templates', []),
	'inventories' => zbx_toHash(getHostInventories(), 'db_field'),
	'tls_connect' => getRequest('tls_connect', HOST_ENCRYPTION_NONE),
	'tls_accept' => getRequest('tls_accept', HOST_ENCRYPTION_NONE),
	'tls_issuer' => getRequest('tls_issuer', ''),
	'tls_subject' => getRequest('tls_subject', ''),
	'tls_psk_identity' => getRequest('tls_psk_identity', ''),
	'tls_psk' => getRequest('tls_psk', '')
    ];

    // sort templates
    natsort($data['templates']);

    // get groups
    $data['all_groups'] = API::HostGroup()->get([
	'output' => API_OUTPUT_EXTEND,
	'editable' => true
    ]);
    order_result($data['all_groups'], 'name');

    // get proxies
    $data['proxies'] = DBfetchArray(DBselect(
	'SELECT h.hostid,h.host'.
	' FROM hosts h'.
	' WHERE h.status IN ('.HOST_STATUS_PROXY_ACTIVE.','.HOST_STATUS_PROXY_PASSIVE.')'
    ));
    order_result($data['proxies'], 'host');

    // get templates data
    $data['linkedTemplates'] = null;
    if (!empty($data['templates'])) {
	$getLinkedTemplates = API::Template()->get([
            'templateids' => $data['templates'],
            'output' => ['templateid', 'name']
	]);

	foreach ($getLinkedTemplates as $getLinkedTemplate) {
            $data['linkedTemplates'][] = [
		'id' => $getLinkedTemplate['templateid'],
		'name' => $getLinkedTemplate['name']
            ];
	}
    }

    $hostView = new CView('configuration.host.massupdate', $data);
}
elseif (hasRequest('form')) {
    $createdate = zbxDateToTime(getRequest('createdate_year',date("Y")).getRequest('createdate_month',date("m")).getRequest('createdate_day',date("d"))."0000");
    $warrantystartdate = zbxDateToTime(getRequest('warrantystartdate_year',date("Y")).getRequest('warrantystartdate_month',date("m")).getRequest('warrantystartdate_day',date("d"))."0000");
    $warrantyenddate = zbxDateToTime(getRequest('warrantyenddate_year',date("Y")).getRequest('warrantyenddate_month',date("m")).getRequest('warrantyenddate_day',date("d"))."0000");
    $data = [
        // Common & auxiliary
        'form' => getRequest('form', ''),
        'hostid' => getRequest('hostid', 0),
        'clone_hostid' => getRequest('clone_hostid', 0),
        'groupid' => getRequest('groupid', 0),
        'flags' => getRequest('flags', ZBX_FLAG_DISCOVERY_NORMAL),

        // Host
        'host' => getRequest('host', ''),
        'visiblename' => getRequest('visiblename', ''),
        'groups' => getRequest('groups', []),
        'newgroup' => getRequest('newgroup', ''),
        'interfaces' => getRequest('interfaces', []),
        'enable' => getRequest('enable', []),
        'port' => getRequest('port', []),
        'description' => getRequest('description', ''),
        'mainInterfaces' => getRequest('mainInterfaces', []),
        'description' => getRequest('description', ''),
        'proxy_hostid' => getRequest('proxy_hostid', 0),
        'status' => getRequest('status', HOST_STATUS_NOT_MONITORED),

         // Templates
        'templates' => getRequest('templates', []),
        'clear_templates' => getRequest('clear_templates', []),
        'original_templates' => [],
        'linked_templates' => [],

        // IPMI
        'ipmi_authtype' => getRequest('ipmi_authtype', IPMI_AUTHTYPE_DEFAULT),
        'ipmi_privilege' => getRequest('ipmi_privilege', IPMI_PRIVILEGE_USER),
        'ipmi_username' => getRequest('ipmi_username', ''),
        'ipmi_password' => getRequest('ipmi_password', ''),

        // Macros
        'macros' => $macros,
        'show_inherited_macros' => getRequest('show_inherited_macros', 0),

        // Host inventory
        'typeid' => getRequest('typeid',''),
        'brandid' => getRequest('brandid',''),
        'size' => getRequest('size',''),
        'model' => getRequest('model',''),
        'tag' => getRequest('tag',''),
        'inventory_tag' => getRequest('inventory_tag',''),
        'serialno' => getRequest('serialno',''),
        'serviceno' => getRequest('serviceno',''),
        'hardinfo' => getRequest('hardinfo',''),
        'createdate' => $createdate,
        'warrantystartdate' =>$warrantystartdate,
        'warrantyenddate' =>$warrantyenddate,
        'dns' => getRequest('dns',''),
        'gw' => getRequest('gw',''),
        'roomid' => getRequest('roomid',''),
        'boxid' => getRequest('boxid',''),
        'position' => getRequest('position',''),
        'belongdeviceid' => getRequest('belongdeviceid',''),
        'osid' => getRequest('osid',''),
        'selectedContact' => getRequest('contacts',''),
        'selectedFile' => getRequest('files',''),

        
        // Encryption
        'tls_connect' => getRequest('tls_connect', HOST_ENCRYPTION_NONE),
        'tls_accept' => getRequest('tls_accept', HOST_ENCRYPTION_NONE),
        'tls_issuer' => getRequest('tls_issuer', ''),
        'tls_subject' => getRequest('tls_subject', ''),
        'tls_psk_identity' => getRequest('tls_psk_identity', ''),
        'tls_psk' => getRequest('tls_psk', '')
    ];

    if (!hasRequest('form_refresh')) {
        //Get data from database when modifing a host
        if ($data['hostid'] != 0) {                                        
            $dbHosts = bzhyAPI::Host()->get([
                'output' => ['hostid', 'proxy_hostid', 'host', 'name', 'status', 'ipmi_authtype', 'ipmi_privilege',
                'ipmi_username', 'ipmi_password', 'flags', 'description', 'tls_connect', 'tls_accept', 'tls_issuer',
                'tls_subject', 'tls_psk_identity', 'tls_psk'
                ],
                'selectInventory' => ['output'=>['typeid','size','model','serialno','serviceno','tag','inventory_tag',
                    'hardinfo','createdate','warrantystartdate','warrantyenddate','roomid','boxid','position',
                    'belongdeviceid','isruning','status','osid','brandid','id'],
                    'contact' =>['output' =>['id','contact_name'],'status' =>BZHY_STATUS_RUNING],
                    'file' =>['output' =>['id','file_title'],'status' =>BZHY_STATUS_RUNING],
                    'dns' =>['id','type','family','ip'],
                    'gw' => ['id','type','family','ip']
                    ],
                'selectGroups' => ['groupid'],
                'selectParentTemplates' => ['templateid'],
                'selectMacros' => ['hostmacroid', 'macro', 'value'],
                'selectDiscoveryRule' => ['itemid', 'name'],
                'hostids' => [$data['hostid']]
            ]);
            $dbHost = reset($dbHosts);
            $data['flags'] = $dbHost['flags'];
            if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
                $data['discoveryRule'] = $dbHost['discoveryRule'];
            }

            // Host
            $data['host'] = $dbHost['host'];
            $data['visiblename'] = $dbHost['name'];
            $data['groups'] = zbx_objectValues($dbHost['groups'], 'groupid');
            $interfaces = bzhyAPI::HostInterface()->get([
                'output' => ['id','name', 'type', 'useip', 'ip','mask', 'dns'],
                'selectItems' => ['type'],
                'selectZbxInterface' => ['interfaceid','main','type','useip','ip','dns','port','bulk'],
                'hostids' => [$data['hostid']],
                'sortfield' => 'id'
            ]);
            
            unset($data['interfaces']);
            foreach ($interfaces as $interfaceid => $interface){
                $data['interfaces'][$interfaceid] = $interface;
                $data['interfaces'][$interfaceid]['interfaceid'] = $interfaceid;
                foreach ($defaultports as $zbx_type => $defaultport){
                    $data['interfaces'][$interfaceid]['enable'][$zbx_type] = 0;
                    $data['interfaces'][$interfaceid]['port'][$zbx_type] = $defaultport;
                    if($zbx_type == INTERFACE_TYPE_SNMP){
                        $data['interfaces'][$interfaceid]['bulk'] = SNMP_BULK_DISABLED;
                    }
                    
                    if(is_array($interface['zbx_interface'])){
                        foreach ($interface['zbx_interface'] as $zbx_interfaceid => $zbx_interface){
                            if($zbx_interface['type'] == $zbx_type){
                                $data['interfaces'][$interfaceid]['enable'][$zbx_type] = 1;
                                $data['interfaces'][$interfaceid]['port'][$zbx_type] = $zbx_interface['port'];
                                if($zbx_type == INTERFACE_TYPE_SNMP){
                                    $data['interfaces'][$interfaceid]['bulk'] = $zbx_interface['bulk'];
                                }
                            }
                        }
                    }
                }
            }
            
           
            
            $data['description'] = $dbHost['description'];
            $data['proxy_hostid'] = $dbHost['proxy_hostid'];
            $data['status'] = $dbHost['status'];

            // Templates
            $data['templates'] = zbx_objectValues($dbHost['parentTemplates'], 'templateid');
            $data['original_templates'] = array_combine($data['templates'], $data['templates']);

            // IPMI
            $data['ipmi_authtype'] = $dbHost['ipmi_authtype'];
            $data['ipmi_privilege'] = $dbHost['ipmi_privilege'];
            $data['ipmi_username'] = $dbHost['ipmi_username'];
            $data['ipmi_password'] = $dbHost['ipmi_password'];

            // Macros
            $data['macros'] = $dbHost['macros'];

            // Interfaces
            foreach ($data['interfaces'] as &$interface) {
                if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
                    $interface['locked'] = true;
                }
                else {
                    // check if interface has items that require specific interface type, if so type cannot be changed
                    $interface['locked'] = false;
                    foreach ($interface['items'] as $item) {
                        $type = itemTypeInterface($item['type']);
                        if ($type !== false && $type != INTERFACE_TYPE_ANY) {
                            $interface['locked'] = true;
                            break;
                        }
                    }
                }
                $interface['items'] = (bool) $interface['items'];
            }
            unset($interface);

           // $data['inventory_mode'] =HOST_INVENTORY_MANUAL;

            // Encryption
            $data['tls_connect'] = $dbHost['tls_connect'];
            $data['tls_accept'] = $dbHost['tls_accept'];
            $data['tls_issuer'] = $dbHost['tls_issuer'];
            $data['tls_subject'] = $dbHost['tls_subject'];
            $data['tls_psk_identity'] = $dbHost['tls_psk_identity'];
            $data['tls_psk'] = $dbHost['tls_psk'];
           
            // Host inventory
            $data['typeid'] = isset($dbHost['inventory']['typeid'])?$dbHost['inventory']['typeid']:NULL; 
            $data['brandid'] = isset($dbHost['inventory']['brandid'])?$dbHost['inventory']['brandid']:NULL; 
            $data['size'] = isset($dbHost['inventory']['size'])?$dbHost['inventory']['size']:NULL; 
            $data['model'] = isset($dbHost['inventory']['model'])?$dbHost['inventory']['model']:NULL; 
            $data['tag'] = isset($dbHost['inventory']['tag'])?$dbHost['inventory']['tag']:NULL; 
            $data['inventory_tag'] = isset($dbHost['inventory']['inventory_tag'])?$dbHost['inventory']['inventory_tag']:NULL; 
            $data['serialno'] = isset($dbHost['inventory']['serialno'])?$dbHost['inventory']['serialno']:NULL; 
            $data['serviceno'] = isset($dbHost['inventory']['serviceno'])?$dbHost['inventory']['serviceno']:NULL; 
            $data['hardinfo'] = isset($dbHost['inventory']['hardinfo'])?$dbHost['inventory']['hardinfo']:NULL; 
            $data['createdate'] = isset($dbHost['inventory']['createdate'])?$dbHost['inventory']['createdate']:time();
            $data['warrantystartdate'] = isset($dbHost['inventory']['warrantystartdate'])?$dbHost['inventory']['warrantystartdate']:time();
            $data['warrantyenddate'] = isset($dbHost['inventory']['warrantyenddate'])?$dbHost['inventory']['warrantyenddate']:time();
            $dnsips = [];
            if(isset($dbHost['inventory']['dns'])){
                foreach($dbHost['inventory']['dns'] as $dns){
                    $dnsips[] = $dns['ip'];
                }
            }
            $gwips = [];
            if(isset($dbHost['inventory']['gw'])){
                foreach($dbHost['inventory']['gw'] as $gw){
                    $gwips[] = $gw['ip'];
                }
            }
            $data['dns'] = !bzhy_empty($dnsips)?implode(';', $dnsips):NULL;
            $data['gw'] = !bzhy_empty($gwips)?implode(';', $gwips):NULL;
            $data['roomid'] = isset($dbHost['inventory']['roomid'])?$dbHost['inventory']['roomid']:NULL;
            $data['boxid'] = isset($dbHost['inventory']['boxid'])?$dbHost['inventory']['boxid']:NULL;
            $data['position'] = isset($dbHost['inventory']['position'])?$dbHost['inventory']['position']:NULL;
            $data['belongdeviceid'] = isset($dbHost['inventory']['belongdeviceid'])?$dbHost['inventory']['belongdeviceid']:0;
            $data['osid'] = isset($dbHost['inventory']['osid'])?$dbHost['inventory']['osid']:NULL;
            $data['selectedContact'] = isset($dbHost['inventory']['contact'])?$dbHost['inventory']['contact']:[];
            $data['selectedFile'] = isset($dbHost['inventory']['file'])? $dbHost['inventory']['file']:[];
            
            
            // display empty visible name if equal to host name
            if ($data['host'] === $data['visiblename']) {
                $data['visiblename'] = '';
            }
        }
        else {
            $data['status'] = HOST_STATUS_MONITORED;
        }

        if (!$data['groups'] && $data['groupid'] != 0) {
            $data['groups'][] = $data['groupid'];
        }
    }
    else {                         //If errors have occured when submit a edit form,displaying the form again
        if ($data['hostid'] != 0) {
            $dbHosts = bzhyAPI::Host()->get([
                'output' => ['flags'],
                'selectParentTemplates' => ['templateid'],
                'selectDiscoveryRule' => ['itemid', 'name'],
                'hostids' => [$data['hostid']]
            ]);
            $dbHost = reset($dbHosts);

            $data['flags'] = $dbHost['flags'];
            if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
                $data['discoveryRule'] = $dbHost['discoveryRule'];
            }

            $templateids = zbx_objectValues($dbHost['parentTemplates'], 'templateid');
            $data['original_templates'] = array_combine($templateids, $templateids);
        }
        
        if(!bzhy_empty($data['interfaces'])){
            foreach ($data['interfaces'] as $interfaceid => $interface){
                foreach ([INTERFACE_TYPE_AGENT, INTERFACE_TYPE_SNMP, INTERFACE_TYPE_JMX, INTERFACE_TYPE_IPMI] as $type){
                    if(isset($data['enable'][$type]) && $data['enable'][$type] == $interfaceid){
                        $data['interfaces'][$interfaceid]['enable'][$type] = 1;
                        $data['interfaces'][$interfaceid]['port'][$type] = bzhy_empty($data['port'][$interfaceid][$type])?$defaultports[$type]:$data['port'][$interfaceid][$type];
                    }
                    else{
                        $data['interfaces'][$interfaceid]['enable'][$type] = 0;
                        $data['interfaces'][$interfaceid]['port'][$type] = $defaultports[$type];
                    }
                    if(isset($interface['bulk']) && !bzhy_empty($interface['bulk'])){
                        $data['interfaces'][$interfaceid]['bulk'] = SNMP_BULK_ENABLED;
                    }
                    else{
                        $data['interfaces'][$interfaceid]['bulk'] = SNMP_BULK_DISABLED;
                    }
                }
            }
        }
        $data['interfaces'] = array_values($data['interfaces']);
    }

    if ($data['hostid'] != 0) {
       // get items that populate host inventory fields
        $data['inventory_items'] = API::Item()->get([
            'output' => ['inventory_link', 'itemid', 'hostid', 'name', 'key_'],
            'hostids' => [$dbHost['hostid']],
            'filter' => ['inventory_link' => array_keys(getHostInventories())]
        ]);
        $data['inventory_items'] = zbx_toHash($data['inventory_items'], 'inventory_link');
        $data['inventory_items'] = CMacrosResolverHelper::resolveItemNames($data['inventory_items']);
    }

    if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
        if ($data['proxy_hostid'] != 0) {
            $data['proxies'] = API::Proxy()->get([
                'output' => ['host'],
                'proxyids' => [$data['proxy_hostid']],
                'preservekeys' => true
            ]);
        }
        else {
            $data['proxies'] = [];
        }
    }
    else {
        $data['proxies'] = API::Proxy()->get([
            'output' => ['host'],
            'preservekeys' => true
        ]);
        order_result($data['proxies'], 'host');
    }

    foreach ($data['proxies'] as &$proxy) {
        $proxy = $proxy['host'];
    }
    unset($proxy);

    if ($data['show_inherited_macros']) {
        $data['macros'] = mergeInheritedMacros($data['macros'], getInheritedMacros($data['templates']));
    }
    $data['macros'] = array_values(order_macros($data['macros'], 'macro'));

    if (!$data['macros'] && $data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
        $macro = ['macro' => '', 'value' => ''];
        if ($data['show_inherited_macros']) {
            $macro['type'] = MACRO_TYPE_HOSTMACRO;
        }
        $data['macros'][] = $macro;
    }

    // groups with RW permissions
    $data['groupsAllowed'] = API::HostGroup()->get([
       'output' => [],
       'editable' => true,
        'preservekeys' => true
    ]);

    // all available groups
    $data['groupsAll'] = API::HostGroup()->get(['output' => ['groupid', 'name']]);
    CArrayHelper::sort($data['groupsAll'], ['name']);

    if ($data['templates']) {
        $data['linked_templates'] = API::Template()->get([
           'output' => ['templateid', 'name'],
            'templateids' => $data['templates']
        ]);
        CArrayHelper::sort($data['linked_templates'], ['name']);
    }
    
    $data['inventory_mode'] = HOST_INVENTORY_MANUAL;
    
    $HostInstance = bzhyCBase::getInstanceByObject('host', []);
    $data['allType'] = $HostInstance->getHostType(['status' =>BZHY_STATUS_RUNING,'output' =>['typeid','typename']]);
    $data['allSize'] = $HostInstance->HostSize;
    
    $IdcRoomInstance = bzhyCBase::getInstanceByObject('idc_room', []);
    $data['allRooms'] = $IdcRoomInstance->get(['status' =>BZHY_STATUS_RUNING,'output' => ['id','room_name']]);
    
    $IdcBoxInstance = bzhyCBase::getInstanceByObject('idc_box', []);
    foreach ($data['allRooms'] as $key => $line){
        $data['idcbox'][$key] = $IdcBoxInstance->get(['box_status' =>BZHY_STATUS_RUNING,'idcroom_ids'=>$key,'output' => ['id','box_no']]);
    }
    
    $data['allOS'] = $HostInstance->getOS(['output' =>['osid','osname']]);
    
    $data['allBrand'] = $HostInstance->getBrand(['output' =>['id','local_name']]);
    
    $data['allIndepend'][0] = ["hostid"=>0,"host"=>_("Independ"),"name"=>_("Independ")];
    
    $Options = ['belongdeviceids'=>0,'hostoutput' => ['output'=>['hostid','host','name'],'status' =>BZHY_STATUS_RUNING]];
    $dependHosts = $HostInstance->getHostByinventory($Options);
    foreach ($dependHosts as $hostid => $row){
        $data['allIndepend'][$row['hostid']] = $row;
    }
    unset($Options);
    
    $Options['output'] = ['id','file_title'];
    $Options['status'] = BZHY_STATUS_RUNING;
    $FileInstance = bzhyCBase::getInstanceByObject('file', []);
    $data['allFiles'] = $FileInstance->get($Options);
    unset($Options);
    
    $Options['output'] = ['id','contact_name'];
    $Options['status'] = BZHY_STATUS_RUNING;
    $ContactInstance = bzhyCBase::getInstanceByObject('contact', []);
    $data['allContacts'] = $ContactInstance->get($Options);
    unset($Options);
    
    $hostView = new CView('bzhyResource.host.edit', $data);
}
else {
	$sortField = getRequest('sort', CProfile::get('web.'.$page['file'].'.sort', 'name'));
	$sortOrder = getRequest('sortorder', CProfile::get('web.'.$page['file'].'.sortorder', ZBX_SORT_UP));

	CProfile::update('web.'.$page['file'].'.sort', $sortField, PROFILE_TYPE_STR);
	CProfile::update('web.'.$page['file'].'.sortorder', $sortOrder, PROFILE_TYPE_STR);

	// get Hosts
	$hosts = [];
	if ($pageFilter->groupsSelected) {
		$hosts = API::Host()->get([
			'output' => ['hostid', $sortField],
			'groupids' => ($pageFilter->groupid > 0) ? $pageFilter->groupid : null,
			'editable' => true,
			'sortfield' => $sortField,
			'limit' => $config['search_limit'] + 1,
			'search' => [
				'name' => ($filter['host'] === '') ? null : $filter['host'],
				'ip' => ($filter['ip'] === '') ? null : $filter['ip'],
				'dns' => ($filter['dns'] === '') ? null : $filter['dns']
			],
			'filter' => [
				'port' => ($filter['port'] === '') ? null : $filter['port']
			]
		]);
	}
	order_result($hosts, $sortField, $sortOrder);

	$url = (new CUrl(bzhyCBase::getUriByObject('host')))
		->setArgument('groupid', $pageFilter->groupid);

	$pagingLine = getPagingLine($hosts, $sortOrder, $url);

	$hosts = API::Host()->get([
		'hostids' => zbx_objectValues($hosts, 'hostid'),
		'output' => API_OUTPUT_EXTEND,
		'selectParentTemplates' => ['hostid', 'name'],
		'selectInterfaces' => API_OUTPUT_EXTEND,
		'selectItems' => API_OUTPUT_COUNT,
		'selectDiscoveries' => API_OUTPUT_COUNT,
		'selectTriggers' => API_OUTPUT_COUNT,
		'selectGraphs' => API_OUTPUT_COUNT,
		'selectApplications' => API_OUTPUT_COUNT,
		'selectHttpTests' => API_OUTPUT_COUNT,
		'selectDiscoveryRule' => ['itemid', 'name'],
		'selectHostDiscovery' => ['ts_delete']
	]);
	order_result($hosts, $sortField, $sortOrder);

	// selecting linked templates to templates linked to hosts
	$templateIds = [];
	foreach ($hosts as $host) {
		$templateIds = array_merge($templateIds, zbx_objectValues($host['parentTemplates'], 'templateid'));
	}
	$templateIds = array_unique($templateIds);

	$templates = API::Template()->get([
		'output' => ['templateid', 'name'],
		'templateids' => $templateIds,
		'selectParentTemplates' => ['hostid', 'name'],
		'preservekeys' => true
	]);

	// get proxy host IDs that that are not 0
	$proxyHostIds = [];
	foreach ($hosts as $host) {
		if ($host['proxy_hostid']) {
			$proxyHostIds[$host['proxy_hostid']] = $host['proxy_hostid'];
		}
	}
	$proxies = [];
	if ($proxyHostIds) {
		$proxies = API::Proxy()->get([
			'proxyids' => $proxyHostIds,
			'output' => ['host'],
			'preservekeys' => true
		]);
	}

	$data = [
		'pageFilter' => $pageFilter,
		'hosts' => $hosts,
		'paging' => $pagingLine,
		'filter' => $filter,
		'sortField' => $sortField,
		'sortOrder' => $sortOrder,
		'groupId' => $pageFilter->groupid,
		'config' => $config,
		'templates' => $templates,
		'proxies' => $proxies
	];

	$hostView = new CView('bzhyResource.host.list', $data);
}

$hostView->render();
$hostView->show();

require_once dirname(__FILE__).'/include/page_footer.php';

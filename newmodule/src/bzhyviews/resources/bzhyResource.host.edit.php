<?php
/*
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2019 http://www.bzhy.com
 */


require_once dirname(__FILE__).'/../../bzhyjs/bzhyResource.host.js.php';

$widget = (new CWidget())
	->setTitle(_('Hosts'))
	->addItem(bzhyget_header_host_table('', $data['hostid']));

$divTabs = new CTabView();
if (!hasRequest('form_refresh')) {
	$divTabs->setSelected(0);
}

$frmHost = (new CForm())
	->setName('hostsForm')
	->addVar('form', $data['form'])
	->addVar('clear_templates', $data['clear_templates'])
	->addVar('flags', $data['flags'])
	->addVar('tls_connect', $data['tls_connect'])
	->addVar('tls_accept', $data['tls_accept'])
	->setAttribute('id', 'hostForm');

if ($data['hostid'] != 0) {
	$frmHost->addVar('hostid', $data['hostid']);
}
if ($data['clone_hostid'] != 0) {
	$frmHost->addVar('clone_hostid', $data['clone_hostid']);
}
if ($data['groupid'] != 0) {
	$frmHost->addVar('groupid', $data['groupid']);
}

$hostList = new CFormList('hostlist');

// LLD rule link
if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
	$hostList->addRow(_('Discovered by'),
		new CLink($data['discoveryRule']['name'],
			'host_prototypes.php?parent_discoveryid='.$data['discoveryRule']['itemid']
		)
	);
}

$hostList->addRow(_('Host name'),
	(new CTextBox('host', $data['host'], ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED), 128))
		->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
		->setAttribute('autofocus', 'autofocus')
);

$hostList->addRow(_('Visible name'),
	(new CTextBox('visiblename', $data['visiblename'], ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED), 128))
		->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);

if ($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
	// groups for normal hosts
	$groupsTB = new CTweenBox($frmHost, 'groups', $data['groups'], 10);

	foreach ($data['groupsAll'] as $group) {
		if (in_array($group['groupid'], $data['groups'])) {
			$groupsTB->addItem($group['groupid'], $group['name'], null,
				array_key_exists($group['groupid'], $data['groupsAllowed'])
			);
		}
		elseif (array_key_exists($group['groupid'], $data['groupsAllowed'])) {
			$groupsTB->addItem($group['groupid'], $group['name']);
		}
	}

	$hostList->addRow(_('Groups'), $groupsTB->get(_('In groups'), _('Other groups')));

	$new_group = (new CTextBox('newgroup', $data['newgroup']))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH);
	$new_group_label = _('New group');
	if (CWebUser::$data['type'] != USER_TYPE_SUPER_ADMIN) {
		$new_group_label .= ' '._('(Only super admins can create groups)');
		$new_group->setReadonly(true);
	}
	$hostList->addRow(new CLabel($new_group_label, 'newgroup'),
		(new CSpan($new_group))->addClass(ZBX_STYLE_FORM_NEW_GROUP)
	);
}
else {
	// groups for discovered hosts
	$groupBox = new CListBox(null, null, 10);
	$groupBox->setEnabled(false);
	foreach ($data['groupsAll'] as $group) {
		if (in_array($group['groupid'], $data['groups'])) {
			$groupBox->addItem($group['groupid'], $group['name'], null,
				array_key_exists($group['groupid'], $data['groupsAllowed'])
			);
		}
	}
	$hostList->addRow(_('Groups'), $groupBox);
	$hostList->addVar('groups', $data['groups']);
}

// interfaces for normal hosts
if ($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
	zbx_add_post_js($data['interfaces']
		? 'hostInterfacesManager.add('.CJs::encodeJson($data['interfaces']).');'
		: 'hostInterfacesManager.addNew("agent");');     
	// Zabbix agent interfaces
	$ifTab = (new CTable())
		->setId('agentInterfaces')
        ->setHeader([
			new CColHeader(),
			new CColHeader(_('Name')),
			new CColHeader(_('IP')),
			new CColHeader(_('Mask')),
			new CColHeader(_('DNS')),
			new CColHeader(_('Connect to'))
		])  
		->addRow((new CRow([
			(new CCol(
				(new CButton('addAgentInterface', _('Add')))->addClass(ZBX_STYLE_BTN_LINK)
			))->setColSpan(6)
		]))->setId('agentInterfacesFooter'));
                
	$hostList->addRow(_('Interfaces'),
		(new CDiv($ifTab))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('data-type', 'agent')
			->setWidth(ZBX_HOST_INTERFACE_WIDTH)
	);

}
// interfaces for discovered hosts
else {
	$existingInterfaceTypes = [];
	foreach ($data['interfaces'] as $interface) {
		$existingInterfaceTypes[$interface['type']] = true;
	}
	zbx_add_post_js('hostInterfacesManager.add('.CJs::encodeJson($data['interfaces']).');');
	zbx_add_post_js('hostInterfacesManager.disable();');

	$hostList->addVar('interfaces', $data['interfaces']);

	// Zabbix agent interfaces
	$ifTab = (new CTable())
		->setId('agentInterfaces')
		->setHeader([
			new CColHeader(),
			new CColHeader(_('IP address')),
			new CColHeader(_('DNS name')),
			new CColHeader(_('Connect to')),
			new CColHeader(_('Port')),
			(new CColHeader(_('Default')))->setColSpan(2)
		]);

	$row = (new CRow())->setId('agentInterfacesFooter');
	if (!array_key_exists(INTERFACE_TYPE_AGENT, $existingInterfaceTypes)) {
		$row->addItem(new CCol());
		$row->addItem((new CCol(_('No agent interfaces found.')))->setColSpan(6));
	}
	$ifTab->addRow($row);

	$hostList->addRow(_('Agent interfaces'),
		(new CDiv($ifTab))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('data-type', 'agent')
			->setWidth(ZBX_HOST_INTERFACE_WIDTH)
	);

	// SNMP interfaces
	$ifTab = (new CTable())->setId('SNMPInterfaces');

	$row = (new CRow())->setId('SNMPInterfacesFooter');
	if (!array_key_exists(INTERFACE_TYPE_SNMP, $existingInterfaceTypes)) {
		$row->addItem(new CCol());
		$row->addItem((new CCol(_('No SNMP interfaces found.')))->setColSpan(6));
	}
	$ifTab->addRow($row);

	$hostList->addRow(_('SNMP interfaces'),
		(new CDiv($ifTab))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('data-type', 'snmp')
			->setWidth(ZBX_HOST_INTERFACE_WIDTH)
	);

	// JMX interfaces
	$ifTab = (new CTable())->setId('JMXInterfaces');

	$row = (new CRow())->setId('JMXInterfacesFooter');
	if (!array_key_exists(INTERFACE_TYPE_JMX, $existingInterfaceTypes)) {
		$row->addItem(new CCol());
		$row->addItem((new CCol(_('No JMX interfaces found.')))->setColSpan(6));
	}
	$ifTab->addRow($row);

	$hostList->addRow(_('JMX interfaces'),
		(new CDiv($ifTab))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('data-type', 'jmx')
			->setWidth(ZBX_HOST_INTERFACE_WIDTH)
	);

	// IPMI interfaces
	$ifTab = (new CTable())->setId('IPMIInterfaces');

	$row = (new CRow())->setId('IPMIInterfacesFooter');
	if (!array_key_exists(INTERFACE_TYPE_IPMI, $existingInterfaceTypes)) {
		$row->addItem(new CCol());
		$row->addItem((new CCol(_('No IPMI interfaces found.')))->setColSpan(6));
	}
	$ifTab->addRow($row);

	$hostList->addRow(_('IPMI interfaces'),
		(new CDiv($ifTab))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('data-type', 'ipmi')
			->setWidth(ZBX_HOST_INTERFACE_WIDTH)
	);
}

$hostList->addRow(_('Description'),
	(new CTextArea('description', $data['description']))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
);

// Proxy
if ($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
	$proxy = new CComboBox('proxy_hostid', $data['proxy_hostid'], null, [0 => _('(no proxy)')] + $data['proxies']);
	$proxy->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED);
}
else {
	$proxy = (new CTextBox(null, $data['proxy_hostid'] != 0 ? $data['proxies'][$data['proxy_hostid']] : _('(no proxy)'), true))
		->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH);
	$hostList->addVar('proxy_hostid', $data['proxy_hostid']);
}
$hostList->addRow(_('Monitored by proxy'), $proxy);

$hostList->addRow(_('Enabled'),
	(new CCheckBox('status', HOST_STATUS_MONITORED))->setChecked($data['status'] == HOST_STATUS_MONITORED)
);

if ($data['clone_hostid'] != 0) {
	// host applications
	$hostApps = API::Application()->get([
		'output' => ['name'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false,
		'preservekeys' => true,
		'filter' => ['flags' => ZBX_FLAG_DISCOVERY_NORMAL]
	]);

	if ($hostApps) {
		$applicationsList = [];
		foreach ($hostApps as $hostAppId => $hostApp) {
			$applicationsList[$hostAppId] = $hostApp['name'];
		}
		order_result($applicationsList);

		$listBox = new CListBox('applications', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($applicationsList);
		$hostList->addRow(_('Applications'), $listBox);
	}

	// host items
	$hostItems = API::Item()->get([
		'output' => ['itemid', 'hostid', 'key_', 'name'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false,
		'filter' => ['flags' => ZBX_FLAG_DISCOVERY_NORMAL]
	]);

	if ($hostItems) {
		$hostItems = CMacrosResolverHelper::resolveItemNames($hostItems);

		$itemsList = [];
		foreach ($hostItems as $hostItem) {
			$itemsList[$hostItem['itemid']] = $hostItem['name_expanded'];
		}
		order_result($itemsList);

		$listBox = new CListBox('items', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($itemsList);
		$hostList->addRow(_('Items'), $listBox);
	}

	// host triggers
	$hostTriggers = API::Trigger()->get([
		'output' => ['triggerid', 'description'],
		'selectItems' => ['type'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false,
		'filter' => ['flags' => [ZBX_FLAG_DISCOVERY_NORMAL]]
	]);

	if ($hostTriggers) {
		$triggersList = [];

		foreach ($hostTriggers as $hostTrigger) {
			if (httpItemExists($hostTrigger['items'])) {
				continue;
			}
			$triggersList[$hostTrigger['triggerid']] = $hostTrigger['description'];
		}

		if ($triggersList) {
			order_result($triggersList);

			$listBox = new CListBox('triggers', null, 8);
			$listBox->setAttribute('disabled', 'disabled');
			$listBox->addItems($triggersList);
			$hostList->addRow(_('Triggers'), $listBox);
		}
	}

	// host graphs
	$hostGraphs = API::Graph()->get([
		'output' => ['graphid', 'name'],
		'selectHosts' => ['hostid'],
		'selectItems' => ['type'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false,
		'filter' => ['flags' => [ZBX_FLAG_DISCOVERY_NORMAL]]
	]);

	if ($hostGraphs) {
		$graphsList = [];
		foreach ($hostGraphs as $hostGraph) {
			if (count($hostGraph['hosts']) > 1) {
				continue;
			}
			if (httpItemExists($hostGraph['items'])) {
				continue;
			}
			$graphsList[$hostGraph['graphid']] = $hostGraph['name'];
		}

		if ($graphsList) {
			order_result($graphsList);

			$listBox = new CListBox('graphs', null, 8);
			$listBox->setAttribute('disabled', 'disabled');
			$listBox->addItems($graphsList);
			$hostList->addRow(_('Graphs'), $listBox);
		}
	}

	// discovery rules
	$hostDiscoveryRuleIds = [];

	$hostDiscoveryRules = API::DiscoveryRule()->get([
		'output' => ['itemid', 'hostid', 'key_', 'name'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false
	]);

	if ($hostDiscoveryRules) {
		$hostDiscoveryRules = CMacrosResolverHelper::resolveItemNames($hostDiscoveryRules);

		$discoveryRuleList = [];
		foreach ($hostDiscoveryRules as $discoveryRule) {
			$discoveryRuleList[$discoveryRule['itemid']] = $discoveryRule['name_expanded'];
		}
		order_result($discoveryRuleList);
		$hostDiscoveryRuleIds = array_keys($discoveryRuleList);

		$listBox = new CListBox('discoveryRules', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($discoveryRuleList);
		$hostList->addRow(_('Discovery rules'), $listBox);
	}

	// item prototypes
	$hostItemPrototypes = API::ItemPrototype()->get([
		'output' => ['itemid', 'hostid', 'key_', 'name'],
		'hostids' => [$data['clone_hostid']],
		'discoveryids' => $hostDiscoveryRuleIds,
		'inherited' => false
	]);

	if ($hostItemPrototypes) {
		$hostItemPrototypes = CMacrosResolverHelper::resolveItemNames($hostItemPrototypes);

		$prototypeList = [];
		foreach ($hostItemPrototypes as $itemPrototype) {
			$prototypeList[$itemPrototype['itemid']] = $itemPrototype['name_expanded'];
		}
		order_result($prototypeList);

		$listBox = new CListBox('itemsPrototypes', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($prototypeList);
		$hostList->addRow(_('Item prototypes'), $listBox);
	}

	// Trigger prototypes
	$hostTriggerPrototypes = API::TriggerPrototype()->get([
		'output' => ['triggerid', 'description'],
		'selectItems' => ['type'],
		'hostids' => [$data['clone_hostid']],
		'discoveryids' => $hostDiscoveryRuleIds,
		'inherited' => false
	]);

	if ($hostTriggerPrototypes) {
		$prototypeList = [];
		foreach ($hostTriggerPrototypes as $triggerPrototype) {
			// skip trigger prototypes with web items
			if (httpItemExists($triggerPrototype['items'])) {
				continue;
			}
			$prototypeList[$triggerPrototype['triggerid']] = $triggerPrototype['description'];
		}

		if ($prototypeList) {
			order_result($prototypeList);

			$listBox = new CListBox('triggerprototypes', null, 8);
			$listBox->setAttribute('disabled', 'disabled');
			$listBox->addItems($prototypeList);
			$hostList->addRow(_('Trigger prototypes'), $listBox);
		}
	}

	// Graph prototypes
	$hostGraphPrototypes = API::GraphPrototype()->get([
		'output' => ['graphid', 'name'],
		'selectHosts' => ['hostid'],
		'hostids' => [$data['clone_hostid']],
		'discoveryids' => $hostDiscoveryRuleIds,
		'inherited' => false
	]);

	if ($hostGraphPrototypes) {
		$prototypeList = [];
		foreach ($hostGraphPrototypes as $graphPrototype) {
			if (count($graphPrototype['hosts']) == 1) {
				$prototypeList[$graphPrototype['graphid']] = $graphPrototype['name'];
			}
		}
		order_result($prototypeList);

		$listBox = new CListBox('graphPrototypes', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($prototypeList);
		$hostList->addRow(_('Graph prototypes'), $listBox);
	}

	// host prototypes
	$hostPrototypes = API::HostPrototype()->get([
		'output' => ['hostid', 'name'],
		'discoveryids' => $hostDiscoveryRuleIds,
		'inherited' => false
	]);

	if ($hostPrototypes) {
		$prototypeList = [];
		foreach ($hostPrototypes as $hostPrototype) {
			$prototypeList[$hostPrototype['hostid']] = $hostPrototype['name'];
		}
		order_result($prototypeList);

		$listBox = new CListBox('hostPrototypes', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($prototypeList);
		$hostList->addRow(_('Host prototypes'), $listBox);
	}

	// web scenarios
	$httpTests = API::HttpTest()->get([
		'output' => ['httptestid', 'name'],
		'hostids' => [$data['clone_hostid']],
		'inherited' => false
	]);

	if ($httpTests) {
		$httpTestList = [];

		foreach ($httpTests as $httpTest) {
			$httpTestList[$httpTest['httptestid']] = $httpTest['name'];
		}

		order_result($httpTestList);

		$listBox = new CListBox('httpTests', null, 8);
		$listBox->setAttribute('disabled', 'disabled');
		$listBox->addItems($httpTestList);
		$hostList->addRow(_('Web scenarios'), $listBox);
	}
}

$divTabs->addTab('hostTab', _('Host'), $hostList);

// templates
$tmplList = new CFormList();

// templates for normal hosts
if ($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
	$ignoredTemplates = [];

	$linkedTemplateTable = (new CTable())
		->setAttribute('style', 'width: 100%;')
		->setHeader([_('Name'), _('Action')]);

	foreach ($data['linked_templates'] as $template) {
		$tmplList->addVar('templates[]', $template['templateid']);
		$templateLink = (new CLink($template['name'], 'templates.php?form=update&templateid='.$template['templateid']))
			->setTarget('_blank');

		$linkedTemplateTable->addRow([
			$templateLink,
			(new CCol(
				new CHorList([
					(new CSimpleButton(_('Unlink')))
						->onClick('javascript: submitFormWithParam('.
							'"'.$frmHost->getName().'", "unlink['.$template['templateid'].']", "1"'.
						');')
						->addClass(ZBX_STYLE_BTN_LINK),
					array_key_exists($template['templateid'], $data['original_templates'])
						? (new CSimpleButton(_('Unlink and clear')))
							->onClick('javascript: submitFormWithParam('.
								'"'.$frmHost->getName().'", "unlink_and_clear['.$template['templateid'].']", "1"'.
							');')
							->addClass(ZBX_STYLE_BTN_LINK)
						: null
				])
			))->addClass(ZBX_STYLE_NOWRAP)
		], null, 'conditions_'.$template['templateid']);

		$ignoredTemplates[$template['templateid']] = $template['name'];
	}

	$tmplList->addRow(_('Linked templates'),
		(new CDiv($linkedTemplateTable))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('style', 'min-width: '.ZBX_TEXTAREA_BIG_WIDTH.'px;')
	);

	// create new linked template table
	$newTemplateTable = (new CTable())
		->addRow([
			(new CMultiSelect([
				'name' => 'add_templates[]',
				'objectName' => 'templates',
				'ignored' => $ignoredTemplates,
				'popup' => [
					'parameters' => 'srctbl=templates&srcfld1=hostid&srcfld2=host&dstfrm='.$frmHost->getName().
						'&dstfld1=add_templates_&templated_hosts=1&multiselect=1'
				]
			]))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
		])
		->addRow([
			(new CSimpleButton(_('Add')))
				->onClick('javascript: submitFormWithParam("'.$frmHost->getName().'", "add_template", "1");')
				->addClass(ZBX_STYLE_BTN_LINK)
		]);

	$tmplList->addRow(_('Link new templates'),
		(new CDiv($newTemplateTable))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('style', 'min-width: '.ZBX_TEXTAREA_BIG_WIDTH.'px;')
	);
}
// templates for discovered hosts
else {
	$linkedTemplateTable = (new CTable())
		->setAttribute('style', 'width: 100%;')
		->setHeader([_('Name')]);

	foreach ($data['linked_templates'] as $template) {
		$tmplList->addVar('templates[]', $template['templateid']);
		$templateLink = (new CLink($template['name'], 'templates.php?form=update&templateid='.$template['templateid']))
			->setTarget('_blank');

		$linkedTemplateTable->addRow($templateLink, null, 'conditions_'.$template['templateid']);
	}

	$tmplList->addRow(_('Linked templates'),
		(new CDiv($linkedTemplateTable))
			->addClass(ZBX_STYLE_TABLE_FORMS_SEPARATOR)
			->setAttribute('style', 'min-width: '.ZBX_TEXTAREA_BIG_WIDTH.'px;')
	);
}

$divTabs->addTab('templateTab', _('Templates'), $tmplList);

/*
 * IPMI
 */
if ($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED) {
	$cmbIPMIAuthtype = new CListBox('ipmi_authtype', $data['ipmi_authtype'], 7, null, ipmiAuthTypes());
	$cmbIPMIPrivilege = new CListBox('ipmi_privilege', $data['ipmi_privilege'], 5, null, ipmiPrivileges());
}
else {
	$cmbIPMIAuthtype = [
		(new CTextBox('ipmi_authtype_name', ipmiAuthTypes($data['ipmi_authtype']), true))
			->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
		new CVar('ipmi_authtype', $data['ipmi_authtype'])
	];
	$cmbIPMIPrivilege = [
		(new CTextBox('ipmi_privilege_name', ipmiPrivileges($data['ipmi_privilege']), true))
			->setWidth(ZBX_TEXTAREA_SMALL_WIDTH),
		new CVar('ipmi_privilege', $data['ipmi_privilege'])
	];
}

$divTabs->addTab('ipmiTab', _('IPMI'),
	(new CFormList())
		->addRow(_('Authentication algorithm'), $cmbIPMIAuthtype)
		->addRow(_('Privilege level'), $cmbIPMIPrivilege)
		->addRow(_('Username'),
			(new CTextBox('ipmi_username', $data['ipmi_username'], ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED)))
				->setWidth(ZBX_TEXTAREA_SMALL_WIDTH)
		)
		->addRow(_('Password'),
			(new CTextBox('ipmi_password', $data['ipmi_password'], ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED)))
				->setWidth(ZBX_TEXTAREA_SMALL_WIDTH)
		)
);

/*
 * Macros
 */
$macrosView = new CView('hostmacros', [
	'macros' => $data['macros'],
	'show_inherited_macros' => $data['show_inherited_macros'],
	'is_template' => false,
	'readonly' => ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED)
]);
$divTabs->addTab('macroTab', _('Macros'), $macrosView->render());

$inventoryFormList = new CFormList('inventorylist');

$inventoryFormList->addRow(null,
	(new CRadioButtonList('inventory_mode', (int) $data['inventory_mode']))
		->addValue(_('Disabled'), HOST_INVENTORY_DISABLED)
		->addValue(_('Manual'), HOST_INVENTORY_MANUAL)
		->addValue(_('Automatic'), HOST_INVENTORY_AUTOMATIC)
		->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED)
		->setModern(true)
);
if ($data['flags'] == ZBX_FLAG_DISCOVERY_CREATED) {
	$inventoryFormList->addVar('inventory_mode', $data['inventory_mode']);
}

foreach($data['allType'] as $id => $line){
    $items[$id] = $line['typename'];
}
$inventoryFormList->addRow(_('Type'),
    (new CComboBox('typeid',isset($data['typeid'])?$data['typeid']:"","",$items))
        ->setAttribute('autofocus', 'autofocus')
);

unset($items);
foreach($data['allBrand'] as $id => $line){
    $items[$id] = $line['local_name'];
}
$inventoryFormList->addRow(_('Brand'),
    (new CComboBox('brandid',isset($data['brandid'])?$data['brandid']:"","",$items))
);

unset($items);
foreach($data['allSize'] as $id => $value){
    $items[$id] = $value;
}
$inventoryFormList->addRow(_('Size'),
    (new CComboBox('size',isset($data['size'])?$data['size']:"","",$items))
);

$inventoryFormList->addRow(_('Model'),
        (new CTextBox('model', isset($data['model']) ? $data['model']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$inventoryFormList->addRow(_('Host Tag'),
        (new CTextBox('tag', isset($data['tag']) ? $data['tag']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );
$inventoryFormList->addRow(_('Inventory Tag'),
        (new CTextBox('inventory_tag', isset($data['inventory_tag']) ? $data['inventory_tag']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$inventoryFormList->addRow(_('SerialNo'),
        (new CTextBox('serialno', isset($data['serialno']) ? $data['serialno']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$inventoryFormList->addRow(_('ServiceNo'),
        (new CTextBox('serviceno', isset($data['serviceno']) ? $data['serviceno']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );
$inventoryFormList->addRow(_('HardInfo'),
        (new CTextBox('hardinfo', isset($data['hardinfo']) ? $data['hardinfo']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$inventoryFormList->addRow(_('PurchaseTime'), createDateMenu('createdate', isset($data['createdate'])?$data['createdate']:time(),null,false));

$inventoryFormList->addRow(_('WarrantySince'), createDateMenu('warrantystartdate', isset($data['warrantystartdate'])?$data['warrantystartdate']:time(),null,false));

$inventoryFormList->addRow(_('WarrantyTo'), createDateMenu('warrantyenddate', isset($data['warrantyenddate'])?$data['warrantyenddate']:time(),null,false));

$inventoryFormList->addRow(_('DNSList'),
        (new CTextBox('dns', isset($data['dns']) ? $data['dns']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

$inventoryFormList->addRow(_('GateWay'),
        (new CTextBox('gw', isset($data['gw']) ? $data['gw']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

unset($items);
$i=0;
foreach($data['allRooms'] as $id => $line){
    if($i == 0){
        $firstRoomid = $id;
    }
    $i++;
    $items[$id] = $line['room_name'];
}
$inventoryFormList->addRow(_('Room'),
    (new CComboBox('roomid',isset($data['roomid'])?$data['roomid']:"","",$items))
        ->setId('roomid')
        ->onChange("chgSubSel('roomid','subboxid',null,'/device_list.php','action=formajax.getboxes')")
);

$FirstBox = $data['idcbox'][$firstRoomid];

unset($items);
foreach($FirstBox as $id => $line){
    $items[$id] = $line['box_no'];
}

$inventoryFormList->addRow(_('Box'),
    (new CComboBox('boxid',isset($data['boxid'])?$data['boxid']:"","",$items))
        ->setId('subboxid')
);

$inventoryFormList->addRow(_('Position'),
        (new CTextBox('position', isset($data['position']) ? $data['position']:""))
         ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
    );

unset($items);
foreach($this->data['allIndepend'] as $id => $line){
    $items[$id] = $line['host'];
}
if(!isset($items)){
    $items = [];
}
$inventoryFormList->addRow(_('BelongFrom'),
    (new CComboBox('belongdeviceid',isset($data['belongdeviceid'])?$data['belongdeviceid']:"","",$items))
);

unset($items);
foreach($data['allOS'] as $id => $line){
    $items[$id] = $line['osname'];
}
$inventoryFormList->addRow(_('OS'),
    (new CComboBox('osid',isset($data['osid'])?$data['osid']:"","",$items))
);

$selectedContactIds = is_array($data['selectedContact'])?array_keys($data['selectedContact']):[];
$contactTB = new CTweenBox($frmHost, 'contacts', NULL, 10);

foreach ($data['allContacts'] as $id => $row){
    $contactTB->addItem($row['id'], $row['contact_name'], in_array($id, $selectedContactIds)?'yes':'no',TRUE);
}
$inventoryFormList->addRow(_('Contacts'), $contactTB->get(_('In Contacts'), _('Other Contacts')));

$selectedFileIds = is_array($data['selectedFile'])?array_keys($data['selectedFile']):[];
$fileTB = new CTweenBox($frmHost, 'files', NULL, 10);
foreach ($this->data['allFiles'] as $id => $row){
    $fileTB->addItem($row['id'], $row['file_title'],in_array($id, $selectedFileIds)?'yes':'no',TRUE);
}
$inventoryFormList->addRow(_('Files'), $fileTB->get(_('In Files'), _('Other Files')));



$divTabs->addTab('inventoryTab', _('Host inventory'), $inventoryFormList);

// Encryption form list.
$encryption_form_list = (new CFormList('encryption'))
	->addRow(_('Connections to host'),
		(new CRadioButtonList('tls_connect', (int) $data['tls_connect']))
			->addValue(_('No encryption'), HOST_ENCRYPTION_NONE)
			->addValue(_('PSK'), HOST_ENCRYPTION_PSK)
			->addValue(_('Certificate'), HOST_ENCRYPTION_CERTIFICATE)
			->setModern(true)
			->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED)
	)
	->addRow(_('Connections from host'), [
		new CLabel([(new CCheckBox('tls_in_none'))->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED),
			_('No encryption')
		]),
		BR(),
		new CLabel([(new CCheckBox('tls_in_psk'))->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED), _('PSK')]),
		BR(),
		new CLabel([(new CCheckBox('tls_in_cert'))->setEnabled($data['flags'] != ZBX_FLAG_DISCOVERY_CREATED),
			_('Certificate')
		])
	])
	->addRow(_('PSK identity'),
		(new CTextBox('tls_psk_identity', $data['tls_psk_identity'], $data['flags'] == ZBX_FLAG_DISCOVERY_CREATED, 128))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
	)
	->addRow(_('PSK'),
		(new CTextBox('tls_psk', $data['tls_psk'], $data['flags'] == ZBX_FLAG_DISCOVERY_CREATED, 512))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
	)
	->addRow(_('Issuer'),
		(new CTextBox('tls_issuer', $data['tls_issuer'], $data['flags'] == ZBX_FLAG_DISCOVERY_CREATED, 1024))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
	)
	->addRow(_x('Subject', 'encryption certificate'),
		(new CTextBox('tls_subject', $data['tls_subject'], $data['flags'] == ZBX_FLAG_DISCOVERY_CREATED, 1024))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
	);

$divTabs->addTab('encryptionTab', _('Encryption'), $encryption_form_list);

/*
 * footer
 */
// Do not display the clone and delete buttons for clone forms and new host forms.
if ($data['hostid'] != 0) {
	$divTabs->setFooter(makeFormFooter(
		new CSubmit('update', _('Update')),
		[
			new CSubmit('clone', _('Clone')),
			new CSubmit('full_clone', _('Full clone')),
			new CButtonDelete(_('Delete selected host?'), url_param('form').url_param('hostid').url_param('groupid')),
			new CButtonCancel(url_param('groupid'))
		]
	));
}
else {
	$divTabs->setFooter(makeFormFooter(
		new CSubmit('add', _('Add')),
		[new CButtonCancel(url_param('groupid'))]
	));
}

$frmHost->addItem($divTabs);

$widget->addItem($frmHost);

return $widget;
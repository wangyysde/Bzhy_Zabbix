<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */



/**
 * Find user theme or get default theme.
 *
 * @param array $userData
 *
 * @return string
 */
function bzhygetUserTheme($userData) {
    $config = bzhyselect_config();

    if (isset($config['default_theme'])) {
	$css = $config['default_theme'];
    }
    if (isset($userData['theme']) && $userData['theme'] != BZHYTHEME_DEFAULT) {
	$css = $userData['theme'];
    }
    if (!isset($css)) {
	$css = BZHY_DEFAULT_THEME;
    }

    return $css;
}

/**
 * Get user type name.
 *
 * @param int $userType
 *
 * @return string
 */
function bzhyuser_type2str($userType = null) {
    $userTypes = [
	BZHYUSER_TYPE_ZABBIX_USER => _('User'),
        BZHYUSER_TYPE_ZABBIX_ADMIN => _('Admin'),
	BZHYUSER_TYPE_SUPER_ADMIN => _('Super Admin')
    ];

    if ($userType === null) {
	return $userTypes;
    }
    elseif (isset($userTypes[$userType])) {
        return $userTypes[$userType];
    }
    else {
	return _('Unknown');
    }
}

/**
 * Get user authentication name.
 *
 * @param int $authType
 *
 * @return string
 */
function bzhyuser_auth_type2str($authType) {
    if ($authType === null) {
	$authType = bzhygetUserGuiAccess(bzhyCWebUser::$data['userid']);
    }

    $authUserType = [
        BZHYGROUP_GUI_ACCESS_SYSTEM => _('System default'),
        BZHYGROUP_GUI_ACCESS_INTERNAL => _x('Internal', 'user type'),
        BZHYGROUP_GUI_ACCESS_DISABLED => _('Disabled')
    ];

    return isset($authUserType[$authType]) ? $authUserType[$authType] : _('Unknown');
}

/**
 * Unblock user account.
 *
 * @param array $userIds
 *
 * @return bool
 */
function bzhyunblock_user_login($userIds) {
    bzhy_value2array($userIds);

    return DBexecute('UPDATE users SET attempt_failed=0 WHERE '.bzhydbConditionInt('userid', $userIds));
}

/**
 * Get users ids by groups ids.
 *
 * @param array $userGroupIds
 *
 * @return array
 */
function bzhyget_userid_by_usrgrpid($userGroupIds) {
    bzhy_value2array($userGroupIds);

    $userIds = [];

    $dbUsers = DBselect(
	'SELECT DISTINCT u.userid'.
	' FROM users u,users_groups ug'.
	' WHERE u.userid=ug.userid'.
	' AND '.bzhydbConditionInt('ug.usrgrpid', $userGroupIds)
    );
    while ($user = DBFetch($dbUsers)) {
	$userIds[$user['userid']] = $user['userid'];
    }

    return $userIds;
}

/**
 * Check if group has permissions for update.
 *
 * @param array $userGroupIds
 *
 * @return bool
 */
function bzhygranted2update_group($userGroupIds) {
    bzhy_value2array($userGroupIds);

    $users = bzhyget_userid_by_usrgrpid($userGroupIds);

    return !isset($users[bzhyCWebUser::$data['userid']]);
}

/**
 * Change group status.
 *
 * @param array $userGroupIds
 * @param int   $usersStatus
 *
 * @return bool
 */
function bzhychange_group_status($userGroupIds, $usersStatus) {
    bzhy_value2array($userGroupIds);

    $grant = ($usersStatus == STATUS_DISABLED) ? bzhygranted2update_group($userGroupIds) : true;

    if ($grant) {
	return DBexecute(
            'UPDATE usrgrp'.
            ' SET users_status='.bzhy_dbstr($usersStatus).
            ' WHERE '.bzhydbConditionInt('usrgrpid', $userGroupIds)
	);
    }
    else {
	error(_('User cannot change status of himself.'));
    }

    return false;
}

/**
 * Change gui access for group.
 *
 * @param array $userGroupIds
 * @param int   $guiAccess
 *
 * @return bool
 */
function bzhychange_group_gui_access($userGroupIds, $guiAccess) {
    bzhy_value2array($userGroupIds);

    $grant = ($guiAccess == BZHYGROUP_GUI_ACCESS_DISABLED) ? bzhygranted2update_group($userGroupIds) : true;

    if ($grant) {
	return DBexecute(
            'UPDATE usrgrp SET gui_access='.bzhy_dbstr($guiAccess).' WHERE '.bzhydbConditionInt('usrgrpid', $userGroupIds)
	);
    }
    else {
	error(_('User cannot change GUI access for himself.'));
    }

    return false;
}

/**
 * Change debug mode for group.
 *
 * @param array $userGroupIds
 * @param int   $debugMode
 *
 * @return bool
 */
function bzhychange_group_debug_mode($userGroupIds, $debugMode) {
    bzhy_value2array($userGroupIds);

    return DBexecute(
	'UPDATE usrgrp SET debug_mode='.bzhy_dbstr($debugMode).' WHERE '.bzhydbConditionInt('usrgrpid', $userGroupIds)
    );
}

/**
 * Gets user full name in format "alias (name surname)". If both name and surname exist, returns translated string.
 *
 * @param array  $userData
 * @param string $userData['alias']
 * @param string $userData['name']
 * @param string $userData['surname']
 *
 * @return string
 */
function bzhygetUserFullname($userData) {
    if (!bzhy_empty($userData['surname'])) {
	if (!bzhy_empty($userData['name'])) {
            return $userData['alias'].' '._x('(%1$s %2$s)', 'user fullname', $userData['name'], $userData['surname']);
	}

	$fullname = $userData['surname'];
    }
    else {
	$fullname = bzhy_empty($userData['name']) ? '' : $userData['name'];
    }

    return bzhy_empty($fullname) ? $userData['alias'] : $userData['alias'].' ('.$fullname.')';
}

/**
 * Returns the list of permissions to the host groups for selected user groups.
 *
 * @param string $usrgrpid
 *
 * @return array
 */
function bzhygetHostGroupsRights(array $usrgrpids = []) {
    $groups_rights = [
        '0' => [
        'permission' => BZHYPERM_NONE,
        'name' => '',
        'grouped' => '1'
        ]
    ];

    $host_groups = bzhyAPI::HostGroup()->get(['groupid', 'name']);

    foreach ($host_groups as $host_group) {
	$groups_rights[$host_group['groupid']] = [
            'permission' => BZHYPERM_NONE,
            'name' => $host_group['name']
	];
    }

    if ($usrgrpids) {
	$db_rights = DBselect(
            'SELECT r.id AS groupid,'.
            'CASE WHEN MIN(r.permission)='.BZHYPERM_DENY.' THEN '.BZHYPERM_DENY.' ELSE MAX(r.permission) END AS permission'.
            ' FROM rights r'.
            ' WHERE '.bzhydbConditionInt('r.groupid', $usrgrpids).
            ' GROUP BY r.id'
	);

	while ($db_right = DBfetch($db_rights)) {
            $groups_rights[$db_right['groupid']]['permission'] = $db_right['permission'];
	}
    }

    return $groups_rights;
}

/**
 * Returns the sorted list of permissions to the host groups in collapsed form.
 *
 * @param array  $groups_rights
 * @param string $groups_rights[<groupid>]['name']
 * @param int    $groups_rights[<groupid>]['permission']
 *
 * @return array
 */
function bzhycollapseHostGroupRights(array $groups_rights) {
    $groups = [];

    foreach ($groups_rights as $groupid => $group_rights) {
	$groups[$group_rights['name']] = $groupid;
    }

    bzhyCArrayHelper::sort($groups_rights, [['field' => 'name', 'order' => BZHY_SORT_DOWN]]);

    $permissions = [];

    foreach ($groups_rights as $groupid => $group_rights) {
	if ($groupid == 0) {
            continue;
	}

	$permissions[$group_rights['permission']] = true;

	$parent_group_name = $group_rights['name'];

	do {
            $pos = strrpos($parent_group_name, '/');
            $parent_group_name = ($pos === false) ? '' : substr($parent_group_name, 0, $pos);

            if (array_key_exists($parent_group_name, $groups)) {
		$parent_group_rights = &$groups_rights[$groups[$parent_group_name]];

		if ($parent_group_rights['permission'] == $group_rights['permission']) {
                    $parent_group_rights['grouped'] = '1';
                    unset($groups_rights[$groupid]);
		}
		unset($parent_group_rights);

		break;
            }
	}
	while ($parent_group_name !== '');
    }

    if (count($permissions) == 1) {
	$groups_rights = array_slice($groups_rights, -1);
	$groups_rights[0]['permission'] = key($permissions);
    }

    bzhyCArrayHelper::sort($groups_rights, [['field' => 'name', 'order' => ZBX_SORT_UP]]);

    return $groups_rights;
}

/**
 * Applies new permissions to the host groups.
 *
 * @param array  $groups_rights
 * @param string $groups_rights[<groupid>]['name']
 * @param int    $groups_rights[<groupid>]['permission']
 * @param int    $groups_rights[<groupid>]['grouped']    (optional)
 * @param array  $groupids
 * @param array  $groupids_subgroupids
 * @param int    $new_permission
 *
 * @return array
 */
function bzhyapplyHostGroupRights(array $groups_rights, array $groupids = [], array $groupids_subgroupids = [],
    $new_permission = BZHYPERM_NONE) {
    // get list of host groups
    $ex_groups_rights = bzhygetHostGroupsRights();
    $ex_groups = [];

    foreach ($ex_groups_rights as $groupid => $ex_group_rights) {
	$ex_groups[$ex_group_rights['name']] = $groupid;
    }

    // convert $groupids_subgroupids into $groupids
    foreach ($groupids_subgroupids as $groupid) {
	if (!array_key_exists($groupid, $ex_groups_rights)) {
            continue;
	}

        $groupids[] = $groupid;

	$parent_group_name = $ex_groups_rights[$groupid]['name'].'/';
	$parent_group_name_len = strlen($parent_group_name);

	foreach ($ex_groups_rights as $groupid => $ex_group_rights) {
            if (substr($ex_group_rights['name'], 0, $parent_group_name_len) === $parent_group_name) {
		$groupids[] = $groupid;
            }
	}
    }

    $groupids = array_fill_keys($groupids, true);

    // apply new permissions to all groups
    foreach ($ex_groups_rights as $groupid => &$ex_group_rights) {
	if ($groupid == 0) {
            continue;
	}
	if (array_key_exists($groupid, $groupids)) {
            $ex_group_rights['permission'] = $new_permission;
            continue;
	}
	if (array_key_exists($groupid, $groups_rights)) {
            $ex_group_rights['permission'] = $groups_rights[$groupid]['permission'];
            continue;
	}

	$parent_group_name = $ex_group_rights['name'];

	do {
            $pos = strrpos($parent_group_name, '/');
            $parent_group_name = ($pos === false) ? '' : substr($parent_group_name, 0, $pos);

            if (array_key_exists($parent_group_name, $ex_groups)
                && array_key_exists($ex_groups[$parent_group_name], $groups_rights)) {
                $parent_group_rights = $groups_rights[$ex_groups[$parent_group_name]];

		if (array_key_exists('grouped', $parent_group_rights) && $parent_group_rights['grouped']) {
                    $ex_group_rights['permission'] = $parent_group_rights['permission'];
                    break;
		}
            }
	}
	while ($parent_group_name !== '');
    }
    unset($ex_group_rights);

    bzhyCArrayHelper::sort($ex_groups_rights, [['field' => 'name', 'order' => BZHY_SORT_UP]]);

    return $ex_groups_rights;
}

/**
 * Get textual representation of given permission.
 *
 * @param string $perm			Numerical value of permission.
 *									Possible values are:
 *									 3 - PERM_READ_WRITE,
 *									 2 - PERM_READ,
 *									 0 - PERM_DENY,
 *									-1 - PERM_NONE;
 *
 * @return string
 */
function bzhypermissionText($perm) {
    switch ($perm) {
	case BZHYPERM_READ_WRITE: return _('Read-write');
	case BZHYPERM_READ: return _('Read');
	case BZHYPERM_DENY: return _('Deny');
	case BZHYPERM_NONE: return _('None');
    }
}


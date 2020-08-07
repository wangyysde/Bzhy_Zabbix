<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */



/**
 * Get permission label.
 *
 * @param int $permission
 *
 * @return string
 */
function bzhypermission2str($permission) {
    $permissions = [
	BZHYPERM_READ_WRITE => _('Read-write'),
	PERM_READ => _('Read only'),
	BZHYPERM_DENY => _('Deny')
    ];

    return $permissions[$permission];
}

/**
 * Get authentication label.
 *
 * @param int $type
 *
 * @return string
 */
function bzhyauthentication2str($type) {
    $authentications = [
        BZHY_AUTH_INTERNAL => _('Zabbix internal authentication'),
	BZHY_AUTH_LDAP => _('LDAP authentication'),
	BZHY_AUTH_HTTP => _('HTTP authentication')
    ];

    return $authentications[$type];
}

/***********************************************
	CHECK USER ACCESS TO SYSTEM STATUS
************************************************/
/* Function: check_perm2system()
 *
 * Description:
 * Checking user permissions to access system (affects server side: no notification will be sent)
 *
 * Comments:
 *return true if permission is positive
 *
 * Author: Aly
 */
function bzhycheck_perm2system($userid) {
    $sql = 'SELECT '.bzhyDB::getFieldIdByObject('usergroup','usrgrpid').
	' FROM '.bzhyDB::getFromByObject('usergroup').','.bzhyDB::getFromByObject('users_groups').
        ' WHERE '.bzhyDB::getFieldIdByObject('users_groups','userid').'='.bzhy_dbstr($userid).
	' AND '.bzhyDB::getFieldIdByObject('usergroup','usrgrpid').'='.bzhyDB::getFieldIdByObject('users_groups','usrgrpid').
	' AND '.bzhyDB::getFieldIdByObject('usergroup','users_status').'='.STATUS_DISABLED;
    if ($res = DBfetch(DBselect($sql, 1))) {
	return false;
    }
    return true;
}

/**
 * Checking user permissions to login in frontend.
 *
 * @param string $userId
 *
 * @return bool
 */
function bzhycheck_perm2login($userId) {
    return (bzhygetUserGuiAccess($userId) != BZHYGROUP_GUI_ACCESS_DISABLED);
}

/**
 * Get user gui access.
 *
 * @param string $userId
 * @param int    $maxGuiAccess
 *
 * @return int
 */
function bzhygetUserGuiAccess($userId, $maxGuiAccess = null) {
    if (bccomp($userId,bzhyCWebUser::$data['userid']) == 0 && isset(bzhyCWebUser::$data['gui_access'])) {
	return bzhyCWebUser::$data['gui_access'];
    }

    $guiAccess = DBfetch(DBselect(
	'SELECT MAX('.bzhyDB::getFieldIdByObject('usergroup','gui_access').') AS gui_access'.
	' FROM '.bzhyDB::getFromByObject('usergroup').','.bzhyDB::getFromByObject('users_groups').
	' WHERE '.bzhyDB::getFieldIdByObject('users_groups','userid').'='.bzhy_dbstr($userId).
	' AND '.bzhyDB::getFieldIdByObject('usergroup','usrgrpid').'='.bzhyDB::getFieldIdByObject('users_groups','usrgrpid').
	(($maxGuiAccess === null) ? '' : ' AND '.bzhyDB::getFieldIdByObject('usergroup','gui_access').'<='.bzhy_dbstr($maxGuiAccess))
	));

    return $guiAccess ? $guiAccess['gui_access'] : BZHYGROUP_GUI_ACCESS_SYSTEM;
}

/**
 * Get user authentication type.
 *
 * @param string $userId
 * @param int    $maxGuiAccess
 *
 * @return int
 */
function bzhygetUserAuthenticationType($userId, $maxGuiAccess = null) {
    $config = bzhyselect_config();

    switch (bzhygetUserGuiAccess($userId, $maxGuiAccess)) {
	case BZHYGROUP_GUI_ACCESS_SYSTEM:
            return $config['authentication_type'];

	case BZHYGROUP_GUI_ACCESS_INTERNAL:
            return ($config['authentication_type'] == BZHY_AUTH_HTTP) ? BZHY_AUTH_HTTP : BZHY_AUTH_INTERNAL;

	default:
            return $config['authentication_type'];
    }
}

/**
 * Get groups gui access.
 *
 * @param array $groupIds
 * @param int   $maxGuiAccess
 *
 * @return int
 */
function bzhygetGroupsGuiAccess($groupIds, $maxGuiAccess = null) {
    $guiAccess = DBfetch(DBselect(
	'SELECT MAX('.bzhyDB::getFieldIdByObject('usergroup','gui_access').') AS gui_access'.
	' FROM '.bzhyDB::getFromByObject('usergroup').
	' WHERE '.bzhydbConditionInt(bzhyDB::getFieldIdByObject('usergroup','usrgrpid'), $groupIds).
	(($maxGuiAccess === null) ? '' : ' AND '.bzhyDB::getFieldIdByObject('usergroup','gui_access').'<='.bzhy_dbstr($maxGuiAccess))
    ));
    return $guiAccess ? $guiAccess['gui_access'] : BZHYGROUP_GUI_ACCESS_SYSTEM;
}

/**
 * Get group authentication type.
 *
 * @param array $groupIds
 * @param int   $maxGuiAccess
 *
 * @return int
 */
function bzhygetGroupAuthenticationType($groupIds, $maxGuiAccess = null) {
    $config = bzhyselect_config();

    switch (bzhygetGroupsGuiAccess($groupIds, $maxGuiAccess)) {
	case BZHYGROUP_GUI_ACCESS_SYSTEM:
            return $config['authentication_type'];

	case BZHYGROUP_GUI_ACCESS_INTERNAL:
            return ($config['authentication_type'] == BZHY_AUTH_HTTP) ? BZHY_AUTH_HTTP : BZHY_AUTH_INTERNAL;

	default:
            return $config['authentication_type'];
    }
}

/***********************************************
	GET ACCESSIBLE RESOURCES BY RIGHTS
************************************************/
/* NOTE: right structure is
	$rights[i]['type']	= type of resource
	$rights[i]['permission']= permission for resource
	$rights[i]['id']	= resource id
*/
function bzhyget_accessible_hosts_by_rights(&$rights, $user_type, $perm) {
    $result = [];
    $res_perm = [];

    foreach ($rights as $id => $right) {
	$res_perm[$right['id']] = $right['permission'];
    }

    $host_perm = [];
    $where = [];

    array_push($where, bzhyDB::getFieldIdByObject('host', 'status').'in ('.BZHYHOST_STATUS_MONITORED.','.BZHYHOST_STATUS_NOT_MONITORED.','.BZHYHOST_STATUS_TEMPLATE.')');
    array_push($where, bzhydbConditionInt( bzhyDB::getFieldIdByObject('host', 'flags'), [BZHY_FLAG_DISCOVERY_NORMAL, BZHY_FLAG_DISCOVERY_CREATED]));
    $where = count($where) ? $where = ' WHERE '.implode(' AND ', $where) : '';
    $perm_by_host = [];

    $dbHosts = DBselect(
	'SELECT '.bzhyDB::getFieldIdByObject('users_groups','groupid').' AS groupid,'.
        bzhyDB::getFieldIdByObject('host','hostid').','.bzhyDB::getFieldIdByObject('host','host').
        ','.bzhyDB::getFieldIdByObject('host','name').' AS host_name,'.bzhyDB::getFieldIdByObject('host','status').
	' FROM '.bzhyDB::getFromByObject('host').
	' LEFT JOIN '.bzhyDB::getFromByObject('hosts_groups').' ON '.bzhyDB::getFieldIdByObject('hosts_groups', 'hostid').
        '='.bzhyDB::getFieldIdByObject('host', 'hostid').$where
    );
    while ($dbHost = DBfetch($dbHosts)) {
	if (isset($dbHost['groupid']) && isset($res_perm[$dbHost['groupid']])) {
            if (!isset($perm_by_host[$dbHost['hostid']])) {
		$perm_by_host[$dbHost['hostid']] = [];
            }
            $perm_by_host[$dbHost['hostid']][] = $res_perm[$dbHost['groupid']];
            $host_perm[$dbHost['hostid']][$dbHost['groupid']] = $res_perm[$dbHost['groupid']];
	}
	$host_perm[$dbHost['hostid']]['data'] = $dbHost;
    }

    foreach ($host_perm as $hostid => $dbHost) {
	$dbHost = $dbHost['data'];

	// select min rights from groups
	if (BZHYUSER_TYPE_SUPER_ADMIN == $user_type) {
            $dbHost['permission'] = BZHYPERM_READ_WRITE;
	}
	else {
            if (isset($perm_by_host[$hostid])) {
		$dbHost['permission'] = (min($perm_by_host[$hostid]) == BZHYPERM_DENY)
                    ? BZHYPERM_DENY
                    : max($perm_by_host[$hostid]);
            }
            else {
		$dbHost['permission'] = BZHYPERM_DENY;
            }
	}

	if ($dbHost['permission'] < $perm) {
            continue;
	}

	$result[$dbHost['hostid']] = $dbHost;
    }

    bzhyCArrayHelper::sort($result, [
	['field' => 'host_name', 'order' => BZHY_SORT_UP]
    ]);

    return $result;
}

function bzhyget_accessible_groups_by_rights(&$rights, $user_type, $perm) {
    $result = [];

    $group_perm = [];
    foreach ($rights as $right) {
	$group_perm[$right['id']] = $right['permission'];
    }

    $dbHostGroups = DBselect('SELECT '.bzhyDB::getFieldIdByObject('hostgroup', '*').','.BZHYPERM_DENY.' AS permission FROM '.bzhyDB::getFromByObject('hostgroup'));

    while ($dbHostGroup = DBfetch($dbHostGroups)) {
	if ($user_type == BZHYUSER_TYPE_SUPER_ADMIN) {
            $dbHostGroup['permission'] = BZHYPERM_READ_WRITE;
	}
	elseif (isset($group_perm[$dbHostGroup['groupid']])) {
            $dbHostGroup['permission'] = $group_perm[$dbHostGroup['groupid']];
	}
	else {
            $dbHostGroup['permission'] = BZHYPERM_DENY;
	}

	if ($dbHostGroup['permission'] < $perm) {
            continue;
	}

	$result[$dbHostGroup['groupid']] = $dbHostGroup;
    }

    bzhyCArrayHelper::sort($result, [
	['field' => 'name', 'order' => BZHY_SORT_UP]
    ]);

    return $result;
}

/**
 * Returns array of user groups by $userId
 *
 * @param int $userId
 *
 * @return array
 */
function bzhygetUserGroupsByUserId($userId) {
    static $userGroups;

    if (!isset($userGroups[$userId])) {
       $userGroups[$userId] = [];

        $result = DBselect('SELECT '.bzhyDB::getFieldIdByObject('users_groups','usrgrpid').
                ' FROM '.bzhyDB::getFromByObject('users_groups').' WHERE '.bzhyDB::getFieldIdByObject('users_groups', 'userid').
                '='.bzhy_dbstr($userId));
	while ($row = DBfetch($result)) {
            $userGroups[$userId][] = $row['usrgrpid'];
	}
    }

    return $userGroups[$userId];
}

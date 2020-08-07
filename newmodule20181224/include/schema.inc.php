<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
 */

$newschema = include(dirname(__FILE__).'/schema_org.inc.php');
$newschema['contact'] = [
        'key' => 'id',
        'object_name' => _('contact'),
        'object_name_field' => 'contact_name',
        'object_url' => 'contact_maintence',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 5,
                'label' => _('ID'),
            ],
            'contact_name' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('Name'),
            ],
            'contact_sex' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_INT,
                'length' => 1,
                'default' => 1,
                'label' => _('Sex'),
            ],
            'contact_position' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>50,
                'label' => _('Duty'),
            ],
            'contact_company' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>50,
                'label' => _('Company'),
            ],
            'contact_url' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>255,
                'label' => _('Website'),
            ],
            'contact_addr' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>255,
                'label' => _('Address'),
            ],
            'contact_tel' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>255,
                'label' => _('Tel'),
            ],
            'contact_mp' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>15,
                'label' => _('MP'),
            ],
            'contact_email' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>255,
                'label' => _('Email'),
            ],
            'contact_qq' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' => 50,
                'label' => _('QQ'),
            ],
            'contact_wx' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' => 255,
                'label' => _('Weixin'),
            ],
            'contact_desc' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' => 255,
                'label' => _('Description'),
            ],
            'contact_status' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_INT,
                'length' => 1,
                'default' => 1,
                'label' => _('Status'),
            ],
            'contact_createtime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('CreateTime'),
            ],
            'contact_userid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('CreateByUser'),
            ],
            'contact_userip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('CreateFromIP'),
            ],
            'contact_lasttime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('ModifyTime'),
            ],
            'contact_lastuserid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('ModifyByUser'),
            ],
            'contact_lastuserip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('ModifyFromIP'),
            ],
            'contact_deltime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('DisableTime'),
            ],
            'contact_deluserid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('DisableByUser'),
            ],
            'contact_deluserip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('DisableFromIP'),
            ],
            'contact_fax' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('Fax')
            ]
        ]
];

$newschema['idc_box'] = [
    'key' => 'id',
            'object_name' => _('idbox'),
            'object_name_field' => 'box_no',
            'fields' => [
                    'id' => [
                        'null' => false, 
                        'type' => DB::FIELD_TYPE_ID,
                        'length' => 5,
                        'label' => _('ID'),
                    ],
                    'room_id' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_ID,
                        'length' =>5,
                        'ref_table' => 'idc_room',
                        'ref_field' => 'id',
                        'label' => _('Room Name'),
                    ],
                    'box_no' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>50,
                        'label' => _('Box No'),
                    ],
                    'box_secno' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>50,
                        'label' => _('box_secno'),
                    ],
                    'box_starttime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Active since'),
                    ],
                    'box_endtime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Active till'),
                    ],
                    'box_closedtime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Closed At'),
                    ],
                    'box_desc' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>255,
                        'label' => _('Description'),
                    ],
                    'box_status' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_INT,
                        'length' => 1,
                        'default' => 1,
                        'label' => _('Status'),
                    ],
                    'box_outbandwidth' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_INT,
                        'length' => 10,
                        'default' => 0,
                        'label' => _('Bandwidth'),
                    ],
                    'box_iplist' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>255,
                        'label' => _('IP List'),
                    ],
                    'box_height' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_INT,
                        'length' =>3,
                        'label' => _('Height'),
                    ],
                    'box_userid' => [
                        'null' => FALSE,
                        'type' => DB::FIELD_TYPE_ID,
                        'length' =>20,
                        'ref_table' => 'users',
                        'ref_field' => 'userid',
                        'label' => _('CreateByUser'),
                    ],
                    'box_createtime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Create At'),
                    ],
                    'box_userip' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>255,
                        'label' => _('CreateFromIP'),
                    ],
                    'box_lasttime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Modify At'),
                    ],
                    'box_lastuserid' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_ID,
                        'length' =>20,
                        'ref_table' => 'users',
                        'ref_field' => 'userid',
                        'label' => _('ModifyByUser'),
                    ],
                    'box_lastuserip' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>255,
                        'label' => _('ModifyFromIP'),
                    ],
                    'box_deltime' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>20,
                        'label' => _('Closed At'),
                    ],
                    'box_deluserid' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_ID,
                        'length' =>20,
                        'ref_table' => 'users',
                        'ref_field' => 'userid',
                        'label' => _('ClosedByUser'),
                    ],
                    'box_deluserip' => [
                        'null' => TRUE,
                        'type' => DB::FIELD_TYPE_CHAR,
                        'length' =>255,
                        'label' => _('ClosedFromIP'),
                    ],
            ]
];

$newschema['idc_room'] = [
    'key' => 'id',
                'object_name' => _('idcroom'),
                'object_name_field' => 'room_name',
                'fields' => [
                        'id' => [
                            'null' => false, 
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 5,
                            'label' => _('ID'),
                        ],
                        'room_name' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' =>_('Name'),
                        ],
                        'room_shortname' => [
                            'null' => true,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 50,
                            'label'=>_('Short Name'),
                        ],
                        'room_addr' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('Address'),
                        ],
                        'room_status' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_INT,
                            'default' => 1,
                            'length' => 1,
                            'label' => _('Status'),
                        ],
                        'room_starttime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Enable At'),
                        ],
                        'room_endtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => ('Still To'),
                        ],
                        'room_closedtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Closed At'),
                        ],
                        'room_comment' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('Description'),
                        ],
                        'room_userid' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('CreateByUser'),
                        ],
                        'room_createtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Create At'),
                        ],
                        'room_userip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('CreateFromIP'),
                        ],
                        'room_lasttime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' =>20,
                            'label' => _('Modify At'),
                        ],
                        'room_lastuserid' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('ModifyByUser'),
                        ],
                        'room_lastuserip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('ModifyFromIP'),
                        ],
                         'room_deltime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' =>20,
                             'label' => _('Closed At'),
                        ],
                        'room_deluserid' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('ClosedByUser'),
                        ],
                        'room_deluserip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('ClosedFromIP'),
                        ],
                ]
];

$newschema['file'] = [
    'key' => 'id',
        'object_name' => _('file'),
        'object_name_field' => 'file_title',
        'object_url' => 'file_maintence',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 5,
                'label' => _('ID'),
            ],
            'file_title' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>50,
                'label' => _('FileTitle'),
            ],
            'file_name' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>50,
                'label' => _('FileName'),
            ],
            'file_uploadtime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('CreateTime'),
                
            ],
            'file_userid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('CreateByUser'),
            ],
            'file_userip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('CreateFromIP'),
            ],
            'file_lasttime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('ModifyTime'),
            ],
            'file_lastuserid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('ModifyByUser'),
            ],
            'file_lastuserip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('ModifyFromIP'),
            ],
            'file_desc' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('Description'),
            ],
            'file_status' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_INT,
                'default' => 1,
                'length' =>1,
                'label' => _('Status'),
            ],
            'file_deltime' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
                'length' =>20,
                'label' => _('DisableTime'),
            ],
            'file_deluserid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
                'length' =>20,
                'ref_table' => 'users',
                'ref_field' => 'userid',
                'label' => _('DisableByUser'),
                
            ],
            'file_deluserip' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
                'label' => _('DisableFromIP'),
            ],
        ]
    ];

$newschema['system_setting'] = [
        'key' => 'id',
        'object_name' => _('System Setting'),
        'object_name_field' => 'setting_name',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 5,
            ],
            'settingtype_id' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>5,
            ],
            'setting_name' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'setting_title' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'setting_helpmsg' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'setting_value' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'input_type' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'js_check_method' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'php_check_method' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'data_type' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_INT,
                'default' => 0,
                'length' =>1,
                'label' => _('Data Type'),
            ]   
        ]
];

$newschema['system_setting_item'] = [
    'key' => 'id',
        'object_name' => _('Setting Items'),
        'object_name_field' => 'item_name',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 5,
            ],
            'item_name' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'item_title' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'item_helpmsg' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'item_img' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'belong_setting' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ]
        ]
];

$newschema['fileobjectrelation'] = [
    'key' => 'id',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 11,
            ],
            'file_id' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>5,
            ],
            'object_table' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'object_field' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'object_value' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>5,
            ],
        ]
];

$newschema['contactobjectrelation'] = [
    'key' => 'id',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 11,
            ],
            'contact_id' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>5,
            ],
            'object_table' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'object_field' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'object_value' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>5,
            ],
        ]
];

$newschema['system_settingtype'] = [
    'key' => 'id',
        'object_name' => _('Setting Type'),
        'object_name_field' => 'settingtype_name',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 11,
            ],
            'settingtype_name' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'settingtype_shortname' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'settingtype_prefix' => [
                'null' => false,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'settingtype_desc' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ]
        ]
];

$newschema['idc_room'] = [
    'key' => 'id',
                'object_name' => _('idcroom'),
                'object_name_field' => 'room_name',
                'fields' => [
                        'id' => [
                            'null' => false, 
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 5,
                            'label' => _('ID'),
                        ],
                        'room_name' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' =>_('Name'),
                        ],
                        'room_shortname' => [
                            'null' => true,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 50,
                            'label'=>_('Short Name'),
                        ],
                        'room_addr' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('Address'),
                        ],
                        'room_status' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_INT,
                            'default' => 1,
                            'length' => 1,
                            'label' => _('Status'),
                        ],
                        'room_starttime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Enable At'),
                        ],
                        'room_endtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => ('Still To'),
                        ],
                        'room_closedtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Closed At'),
                        ],
                        'room_comment' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('Description'),
                        ],
                        'room_userid' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('CreateByUser'),
                        ],
                        'room_createtime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 20,
                            'label' => _('Create At'),
                        ],
                        'room_userip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('CreateFromIP'),
                        ],
                        'room_lasttime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' =>20,
                            'label' => _('Modify At'),
                        ],
                        'room_lastuserid' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('ModifyByUser'),
                        ],
                        'room_lastuserip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('ModifyFromIP'),
                        ],
                         'room_deltime' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' =>20,
                             'label' => _('Closed At'),
                        ],
                        'room_deluserid' => [
                            'null' => TRUE,
                            'type' => DB::FIELD_TYPE_ID,
                            'length' => 20,
                            'ref_table' => 'users',
                            'ref_field' => 'userid',
                            'label' => _('ClosedByUser'),
                        ],
                        'room_deluserip' => [
                            'null' => FALSE,
                            'type' => DB::FIELD_TYPE_CHAR,
                            'length' => 255,
                            'label' => _('ClosedFromIP'),
                        ],
                ]
];

$newschema['deviceinfo'] = [
    'key' => 'deviceid',
        'fields' => [
            'deviceid' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 7,
                'label' => _('ID'),
            ],
            'typeid' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>2,
               'label' => _('Type'),
            ],
            'size' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>1,
                'label' => _('Size'),
            ],
            'model' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('Model'),
            ],
            'serialno' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('SerialNo'),
            ],
            'serviceno' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('ServiceNo'),
            ],
            'hardinfo' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('HardInfo'),
            ],
            'createdate' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
               'label' => _('PurchaseTime'),
            ],
            'warrantystartdate' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
               'label' => _('WarrantySince'),
            ],
            'warrantyenddate' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
               'label' => _('WarrantyTo'),
            ],
            'agent' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('BuyFrom'),
            ],
            'hostname' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('Hostname'),
            ],
            'ips' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('IPList'),
            ],
            'dns' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('DNSList'),
            ],
            'gw' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('GateWay'),
            ],
            'roomid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>5,
               'label' => _('Room'),
            ],
            'boxid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>5,
               'label' => _('Box'),
            ],
            'position' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
               'label' => _('Position'),
            ],
            'belongdeviceid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>7,
               'label' => _('BelongFrom'),
            ],
            'userid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>20,
               'label' => _('ModifyUser'),
            ],
            'isruning' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>1,
               'label' => _('RuningStatus'),
            ],
            'status' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>1,
               'label' => _('Status'),
            ],
            'osid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>4,
               'label' => _('OS'),
            ],
            'desc' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
               'label' => _('Description'),
            ],
            'brandid' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>4,
               'label' => _('Brand'),
            ],
        ]
];

$newschema['devicetype'] = [
    'key' => 'typeid',
        'fields' => [
            'typeid' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 2,
            ],
            'typename' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'typedesc' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'status' => [
                'null' => false,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>1,
            ]
        ]
];

$newschema['os'] = [
    'key' => 'osid',
        'fields' => [
            'osid' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' => 2,
            ],
            'osname' => [
                'null' => FALSE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
            ],
            'osbit' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_ID,
               'length' =>4,
            ],
            'version' => [
                'null' => false,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
            ],
            'desc' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ]
        ]
];

$newschema['brandinfo'] = [
    'key' => 'id',
        'fields' => [
            'id' => [
                'null' => false, 
                'type' => DB::FIELD_TYPE_ID,
                'length' =>4,
            ],
            'local_name' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'english_name' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'offical_site' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'support_site' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ],
            'tel1' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
            ],
            'tel1_mem' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'tel2' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
            ],
            'tel2_mem' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'tel3' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>20,
            ],
            'tel3_mem' => [
                'null' => TRUE,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>50,
            ],
            'desc' => [
                'null' => true,
                'type' => DB::FIELD_TYPE_CHAR,
               'length' =>255,
            ]
        ]
];

return $newschema;
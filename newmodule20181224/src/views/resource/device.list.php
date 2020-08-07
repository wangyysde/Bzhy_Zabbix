<?php

$widget = (new CWidget())
	->setTitle(_('Device List'))
	->setControls((new CForm('post'))
		->cleanItems()
                ->addItem(new CSubmit('form', _('Add Device')))
                ->addVar('action', 'add', 'action')
	);


$form = (new CForm())->setName('deviceinfo');

$table = (new CTableInfo())
	->setHeader([
            make_sorting_header(_('Hostname'), 'hostname', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('IPs'), 'ips', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('Type'), 'typeid', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('Brand'), 'brandid', $data['sortField'], $data['sortOrder']),
            _('Model'),
            make_sorting_header(_('Room'), 'roomid', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('IDCBox'), 'boxid', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('OS'), 'osid', $data['sortField'], $data['sortOrder']),
            _('Size'),
            _('Position'),
            make_sorting_header(_('BelongTo'), 'belongdeviceid', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('IsRuning'), 'isruning', $data['sortField'], $data['sortOrder']),
            make_sorting_header(_('Status'), 'status', $data['sortField'], $data['sortOrder']),
            _('Action')
	]);

$current_time = time();

foreach ($data['deviceinfo'] as $deviceinfo) {
    
        $url= 'device_list.php?action=details.posted&id='.$deviceinfo['deviceid'];
        $detailurl = (new CLink(_('Detail'),'#'))->onClick('Javascript:PopUp(\''.$url.'\')');
        
        $modifyurl = ($deviceinfo['status'] == DEVICE_STATUS_NORMAL || $deviceinfo['status'] == DEVICE_STATUS_MANTAINCE) ? (new CLink(_('Modify'),
		'device_list.php?action=modify&id='.$deviceinfo['deviceid'])):_('Modify');
        
        if($deviceinfo['status'] == DEVICE_STATUS_NORMAL){
            $paras ='action=mantaince.posted&id='.$deviceinfo['deviceid'];
            $mantainceurl = (new CLink(_('Mantaince'),'#'))->onClick('Javascript:confirmAndRefresh(\'device_list.php\',\''._('Are sure mantaince this device?').'\',\''.$paras.'\')');
            $offlineurl = _('Offline');
        }
        elseif ($deviceinfo['status'] == DEVICE_STATUS_MANTAINCE) {
            $paras ='action=online.posted&id='.$deviceinfo['deviceid'];
            $mantainceurl = (new CLink(_('Online'),'#'))->onClick('Javascript:confirmAndRefresh(\'device_list.php\',\''._('Are sure online this device?').'\',\''.$paras.'\')');
            $paras ='action=offline.posted&id='.$deviceinfo['deviceid'];
            $offlineurl = (new CLink(_('Offline'),'#'))->onClick('Javascript:confirmAndRefresh(\'device_list.php\',\''._('Are sure offline this device?').'\',\''.$paras.'\')');
        }
        else{
            $paras ='action=mantaince.posted&id='.$deviceinfo['deviceid'];
            $mantainceurl = (new CLink(_('Mantaince'),'#'))->onClick('Javascript:confirmAndRefresh(\'device_list.php\',\''._('Are sure mantaince this device?').'\',\''.$paras.'\')');
            $offlineurl = _('Offline');
        }
               
        switch ($deviceinfo['status']){
            case DEVICE_STATUS_OFFLINE:
                $status = _('Offline');
                break;
            case DEVICE_STATUS_NORMAL:
                $status = _('Normal');
                break;
            default:
                $status = _('Mantaince');
                break;
        }
        
        $typename ="";
        if(is_array($deviceinfo['selectType'])){
            foreach ($deviceinfo['selectType'] as $key => $row){
                $typename .= $row['typename'];
            }
        }
        else{
            $typename = "";
        }
        
        $brandstr="";
        if(is_array($deviceinfo['selectBrand'])){
            foreach ($deviceinfo['selectBrand'] as $key => $row){
                $brandstr .= $row['local_name'];
            }
        }
               
        $roomstr="";
        if(is_array($deviceinfo['selectRoom'])){
            foreach ($deviceinfo['selectRoom'] as $key => $row){
                $roomstr .= $row['room_name'];
            }
        }
        
        $idcboxstr="";
        if(is_array($deviceinfo['selectIdcBox'])){
            foreach ($deviceinfo['selectIdcBox'] as $key => $row){
                $idcboxstr .= $row['box_no'];
            }
        }
        
        $osstr="";
        if(is_array($deviceinfo['selectOS'])){
            foreach ($deviceinfo['selectOS'] as $key => $row){
                $osstr .= $row['osname'];
            }
        }
        
        $belongstr="";
        if(is_array($deviceinfo['selectBelong'])){
            foreach ($deviceinfo['selectBelong'] as $key => $row){
                $belongstr .= $row['hostname'];
            }
        }
        
        $table->addRow([
            (new CCol($deviceinfo['hostname'])),
            (new CCol($deviceinfo['ips'])),
            (new CCol($typename)),
            (new CCol($brandstr)),
            (new CCol($deviceinfo['model'])),
            (new CCol($roomstr)),
            (new CCol($idcboxstr)),
            (new CCol($osstr)),
            (new CCol($deviceinfo['size'])),
            (new CCol($deviceinfo['position'])),
            (new CCol($belongstr)),
            (new CCol($deviceinfo['isruning'] == DEVICE_RUNING_STATUS_RUNING?_('Runing'):_('Shutdown'))),
            (new CCol($status)),
            (new CCol([$detailurl,SPACE,$modifyurl,SPACE,$mantainceurl,SPACE,$offlineurl]))
	]);
}
$form->addItem([
	$table,
]);


$widget->addItem($form);

return $widget;

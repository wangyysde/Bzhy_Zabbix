<?php

/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */

function buildInput($inputParas = array(), $selectedValues = array(),$enableValues = array()) {
    $ret = [];
    if(empty($inputParas) || !is_array($inputParas)){
        return $ret;
    }
    switch ($inputParas['type']){
        case INPUT_TYPE_TEXTBOX:
            $txtbox = new CTextBox($inputParas['name']);
            $txtbox->setAttribute('value',!empty($selectedValues)?$selectedValues[0]:"");
            $txtbox->setWidth(isset($inputParas['width'])?$inputParas['width']:ZBX_TEXTAREA_STANDARD_WIDTH);
            $txtbox->setReadonly(isset($inputParas['readonly'])?TRUE:FALSE);
            if($inputParas['js_check']){
                $txtbox->onChange("Javascript:".$inputParas['js_check'].";");
            }
            $ret[]=$txtbox;
            if(isset($inputParas['helpmsg'])){   //计划显示一个图片，但是CSS不工作，暂时修改成文字的
                $helpicons = (//new CList())
                   // ->addClass(ZBX_STYLE_TOP_NAV_ICONS)
                  //  ->addItem(
                        (new CLink(_('Help'), $inputParas['helpmsg']['url']))
		//	->addClass(ZBX_STYLE_TOP_NAV_HELP)
			->setAttribute('target', '_blank')
			->setAttribute('title', _('Help'))
                );
                $ret[] = $helpicons;
            }
            break;
        case INPUT_TYPE_CHECKBOX:
            $chkboxs = [];
            if(!empty($enableValues) && is_array($enableValues)){
                foreach ($enableValues as $var => $value){
                    $chkbox = new CCheckBox($inputParas['name'].'[]');
                    $chkbox->setId($inputParas['name']."_".$var);
                    $chkbox->setAttribute('value', $value['value']);
                    $chkbox->setChecked(isset($selectedValues[$var])?TRUE:FALSE);
                    $chkbox->setEnabled(isset($selectedValues[$var]['disabled'])?FALSE:TRUE);
                    if($inputParas['js_check']){
                        $chkbox->onClick("Javascript:".$inputParas['js_check'].";");
                    }
                    $chkboxs[] = [$chkbox,SPACE,(new CLabel($value['label'])),SPACE,SPACE];
                }
            }
            $ret = $chkboxs;
            break; 
        default:
            break;   
    }
    return $ret;
}

function is_bye_size($value, $allowempty = FALSE ){
    $units = ["b","k","m","g","t"];
    if(zbx_empty($value) && $allowempty){
        return true; 
    }
    if(zbx_empty($value) && !$allowempty){
        return false; 
    }
    $suffix = $value[strlen($value) - 1];
    $is_suffix = FALSE;
    if (!ctype_digit($suffix)) {
        foreach ($units as $unit){
           if(strtolower($unit) === strtolower($suffix)){
               $is_suffix = true; 
               $value = substr($value, 0, strlen($value) - 1);
           }
        }
    }
    if($is_suffix && is_numeric($value)){
        return true;
    }
    return is_numeric($value)?true : FALSE;
}

function zbx_is_allowupload(array $file){
    global $System_Settings;
    if(isset($file['name'])){
        $ext = strtolower(substr($file['name'], (strrpos($file['name'], ".")+1)));
        $allow_upload_file_type = explode(",", strtolower($System_Settings['allow_upload_file_type']));
        if(!in_array($ext,$allow_upload_file_type)){
            return FALSE;
        }
        else{
            return TRUE;
        }
    }
    else {
        return FALSE;
    }
}

function is_tel($str)
{
  if(zbx_empty($str)){
      return false;
  }
  return (preg_match("/^((0\d{2,3})-)(\d{7,8})$/",$str))?true:false;
  // return (preg_match("/^(((d{3}))|(d{3}-))?((0d{2,3})|0d{2,3}-)?[1-9]d{6,8}$/",$str))?true:false;
 } 

 function is_mp($str){
   if(zbx_empty($str)){
      return false;
    }
    if (!is_numeric($str)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $str) ? true : false;
 }
 
 function is_email($str){
    if(zbx_empty($str)){
       return false;
    }
     $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
     return preg_match( $pattern, $str )?true:false;
 }
 
 function chkInputData($fun,$input_name,$input_value){
     if(!function_exists($fun))
         return FALSE;
     return(call_user_func($fun,$input_name,$input_value));
 }
 
 function handleInputData($data,$data_type){
     switch ($data_type){                             //Defined in defines.inc.php
        case DATA_TYPE_NORMAL:
            return $data;
            break;
        case DATA_TYPE_BYE:
        case DATA_TYPE_BIT:
            $data = str2mem($allow_upload_file_size);
            return $data; 
            break; 
        case DATA_TYPE_DATE:
        case DATA_TYPE_TIME:
            $data=zbxDateToTime($data);   //YYYYMMDDHHMMSS', $year, $month, $date, $hours, $minutes, $seconds))
            return $data; 
            break; 
        case DATA_TYPE_UNIXTIME:
        default:
            return $data; 
            break; 
     }
 }
 
 function build_opt_js($main,$sub,$third){
        $js_main_str="";
        $js_sub_str = "";
        $js_third_str = "";
        $i = 0;
        foreach($main as $key => $value){
                        $js_main_str .=$i === 0 ? "var js_main = [\"".$value."\"":",\"".$value."\"";
                        $j = 0;
                        if($i > 0 ){
                                $js_sub_str .=",\n";
                                $js_third_str .=",\n";
                  }
                  if($i === 0 ){
                                        $js_sub_str .="var js_sub = [\n";
                                        $js_third_str .="var js_third = [\n";
                        }
                        foreach($sub[$key] as $key_sub => $value_sub){
                                $js_sub_str .= $j == 0 ? "[\"".$value_sub."\"":",\"".$value_sub."\"";
                                if($j>0)
                                        $js_third_str .=",\n";
                                $k = 0;
                                if($j === 0 )
                                           $js_third_str .="  [";
                                foreach($third[$key][$key_sub] as $key_third => $value_third){
                                        $js_third_str .= $k == 0 ? "    [\"".$value_third."\"":",\"".$value_third."\"";
                                        $k++;
                                }
                                $js_third_str .="]";
                                $j++;
                        }
                        $js_third_str .="  ]";
                        $js_sub_str .="]";
                        $i++;
        }
        $js_main_str .= "];\n";
        $js_sub_str .="\n  ];\n";
        $js_third_str .="\n ];\n";
        return( $js_main_str.$js_sub_str.$js_third_str);
}


function build_js_var($arr,$var_name){
        $js_var="var ".$var_name." = new Array();\n";
        foreach($arr as $key => $value){
                $js_var .=$var_name."[\"".$key."\"]=\"".$value."\";\n";
        }
        return($js_var);
}

function build_js_array($arr,$var_name){
        $js_var="var ".$var_name." = new Array();\n";
        $i =0 ;
        foreach($arr as $key => $value ){
     $j = 0;
     $js_var .= $var_name."[".$i."] = new Array(); \n";
     foreach($value as $sub_key => $sub_value){
     $k = 0;
        $js_var .= $var_name."[".$i."][".$j."] = new Array(); \n";
                foreach($sub_value as $third_key => $third_value){
                $js_var .=$var_name."[".$i."][".$j."][".$k."] = \"".$third_value."\";\n";
                $k++;
          }
        $j++;
     }
    $i++;
  }
        return($js_var);
}

function byes2str($size) {
	//$prefix = 'B';
        if($size >1099511627776){
            $size = $size / 1099511627776; 
            $prefix = 'T';
        }
        elseif ($size > 1073741824){
            $size = $size / 1073741824; 
            $prefix = 'G';
        }
	elseif ($size > 1048576) {
		$size = $size / 1048576;
		$prefix = 'M';
	}
	elseif ($size > 1024) {
		$size = $size / 1024;
		$prefix = 'K';
	}

	return round($size, 2).$prefix;
}

function str2byes($val) {
	$val = trim($val);
	$last = strtolower(substr($val, -1));

	switch ($last) {
                case 't':
                        $val *= 1024;
		case 'g':
			$val *= 1024;
			/* falls through */
		case 'm':
			$val *= 1024;
			/* falls through */
		case 'k':
			$val *= 1024;
	}

	return $val;
}

/**
 * Create array with all inputs required for date selection and calendar.
 *
 * @param string      $name
 * @param int|array   $date unix timestamp/date array(Y,m,d,H,i)
 * @param string|null $relatedCalendar name of the calendar which must be closed when this calendar opens
 *
 * @return array
 */
function createDateMenu($name, $date, $relatedCalendar = null,$isHour = TRUE) {
	$onClick = 'var pos = getPosition(this); pos.top += 10; pos.left += 16; CLNDR["'.$name.
		'_calendar"].clndr.clndrshow(pos.top, pos.left);';
	if ($relatedCalendar) {
		$onClick .= ' CLNDR["'.$relatedCalendar.'_calendar"].clndr.clndrhide();';
	}

	if (is_array($date)) {
		$y = $date['y'];
		$m = $date['m'];
		$d = $date['d'];
                if($isHour){
                    $h = $date['h'];
                    $i = $date['i'];
                }
	}
	else {
		$y = date('Y', $date);
		$m = date('m', $date);
		$d = date('d', $date);
                if($isHour){
                    $h = date('H', $date);
                    $i = date('i', $date);
                }
	}
        if($isHour){
           $fields = [
		(new CNumericBox($name.'_year', $y, 4))
                    ->setWidth(ZBX_TEXTAREA_4DIGITS_WIDTH)
                    ->setAttribute('placeholder', _('yyyy')),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		'-',
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CTextBox($name.'_month', $m, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('mm'))
                    ->onChange('validateDatePartBox(this, 1, 12, 2);'),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		'-',
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CTextBox($name.'_day', $d, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('dd'))
                    ->onChange('validateDatePartBox(this, 1, 31, 2);'), 
               	(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
            
                (new CTextBox($name.'_hour', $h, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('hh'))
                    ->onChange('validateDatePartBox(this, 0, 23, 2);'),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		':',
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CTextBox($name.'_minute', $i, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('mm'))
                    ->onChange('validateDatePartBox(this, 0, 59, 2);'),
               (new CButton())
                    ->addClass(ZBX_STYLE_ICON_CAL)
                    ->onClick($onClick)
            ];
        }
        else{
            $fields = [
		(new CNumericBox($name.'_year', $y, 4))
                    ->setWidth(ZBX_TEXTAREA_4DIGITS_WIDTH)
                    ->setAttribute('placeholder', _('yyyy')),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		'-',
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CTextBox($name.'_month', $m, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('mm'))
                    ->onChange('validateDatePartBox(this, 1, 12, 2);'),
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		'-',
		(new CDiv())->addClass(ZBX_STYLE_FORM_INPUT_MARGIN),
		(new CTextBox($name.'_day', $d, false, 2))
                    ->setWidth(ZBX_TEXTAREA_2DIGITS_WIDTH)
                    ->addStyle('text-align: right;')
                    ->setAttribute('placeholder', _('dd'))
                    ->onChange('validateDatePartBox(this, 1, 31, 2);'), 
               (new CButton())
                    ->addClass(ZBX_STYLE_ICON_CAL)
                    ->onClick($onClick)
            ];
        }
        
        $IsHourStr = $isHour?"true":"false";
        
        if($isHour){
            zbx_add_post_js('create_calendar(null,'.
		'["'.$name.'_day","'.$name.'_month","'.$name.'_year","'.$name.'_hour","'.$name.'_minute"],'.
		'"'.$name.'_calendar",'.
		'"'.$name.'",'.$IsHourStr.');'
            );
        }
        else{
            zbx_add_post_js('create_calendar(null,'.
		'["'.$name.'_day","'.$name.'_month","'.$name.'_year"],'.
		'"'.$name.'_calendar",'.
		'"'.$name.'",'.$IsHourStr.');'
            );
        }

	return $fields;
}

/**
* Checks if array $arrays contains arrays with duplicate values under the $uniqueField key. If a duplicate exists,
* returns the first duplicate, otherwise returns null.
*
* Example 1:
* $data = array(
*     array('name' => 'CPU load'),
* 	   array('name' => 'CPU load'),
* 	   array('name' => 'Free memory')
* );
* var_dump(CArrayHelper::findDuplicate($data, 'name')); // returns array with index 1
*
* Example 2:
* $data = array(
*     array('host' => 'Zabbix server', 'name' => 'CPU load'),
* 	   array('host' => 'Zabbix server', 'name' => 'Free memory'),
* 	   array('host' => 'Linux server', 'name' => 'CPU load'),
* 	   array('host' => 'Zabbix server', 'name' => 'CPU load')
* );
* var_dump(CArrayHelper::findDuplicate($data, 'name', 'host')); // returns array with index 3
*
* @param array $arrays         an array of arrays
* @param string $uniqueField   key to be used as unique criteria
* @param string $uniqueField2	second key to be used as unique criteria
*
* @return null|array           the first duplicate found or null if there are no duplicates
*/
function findDuplicate(array $arrays, $uniqueField, $uniqueField2 = null) {
    $uniqueValues = [];

    foreach ($arrays as $array) {
	$value = $array[$uniqueField];

	if ($uniqueField2 !== null) {
            $uniqueByValue = $array[$uniqueField2];

            if (isset($uniqueValues[$uniqueByValue]) && isset($uniqueValues[$uniqueByValue][$value])) {
		return $array;
            }
            $uniqueValues[$uniqueByValue][$value] = $value;
	}
	else {
            if (isset($uniqueValues[$value])) {
		return $array;
            }
            $uniqueValues[$value] = $value;
	}
    }
}

/**
 * Returns true if the value is an empty string, empty array or null.
 *
 * @deprecated use strict comparison instead
 *
 * @param $value
 *
 * @return bool
 */
function bzhy_empty($value) {
	if ($value === null) {
		return true;
	}
	if (is_array($value) && empty($value)) {
		return true;
	}
	if (is_string($value) && $value === '') {
		return true;
	}

	return false;
}

// value OR object OR array of objects TO an array
function bzhy_objectValues($value, $field) {
    if (is_null($value)) {
	return $value;
    }

    if (!is_array($value)) {
	$result = [$value];
    }
    elseif (isset($value[$field])) {
	$result = [$value[$field]];
    }
    else {
	$result = [];
	foreach ($value as $val) {
            if (!is_array($val)) {
		$result[] = $val;
            }
            elseif (isset($val[$field])) {
		$result[] = $val[$field];
            }
	}
    }
    return $result;
}

function bzhyBuildTraceMessage($e){
    $retmsg = "";
    if(bzhy_empty($e)){
        return "";
    }
    if(is_string($e)){
        return $e;
    }
    if(is_array($e)){
        foreach ($e as $msg){
            if(is_array($msg)){
                $retmsg .=bzhyBuildTraceMessage($msg);
            }
            else{
                $retmsg .=$msg;
            }
        }
    }
    else{
        $retmsg .= $e;
    }
    
    return $retmsg;
}

function bzhy_value2array(&$values) {
    if (!is_array($values) && !is_null($values)) {
        $tmp = [];
        if (is_object($values)) {
            $tmp[] = $values;
        }
        else {
            $tmp[$values] = $values;
        }
        $values = $tmp;
    }
}

function bzhy_array_merge() {
    $args = func_get_args();
    $result = [];
    foreach ($args as &$array) {
	if (!is_array($array)) {
            return false;
        }
	foreach ($array as $key => $value) {
            $result[$key] = $value;
        }
    }
    unset($array);

    return $result;
}

/**
 * Converts the given value to a numeric array:
 * - a scalar value will be converted to an array and added as the only element;
 * - an array with first element key containing only numeric characters will be converted to plain zero-based numeric array.
 * This is used for reseting nonsequential numeric arrays;
 * - an associative array will be returned in an array as the only element, except if first element key contains only numeric characters.
 *
 * @param mixed $value
 *
 * @return array
 */
function bzhy_toArray($value) {
    if ($value === null) {
	return $value;
    }

    if (is_array($value)) {
	// reset() is needed to move internal array pointer to the beginning of the array
	reset($value);
	if (ctype_digit(strval(key($value)))) {
            $result = array_values($value);
	}
	elseif (!empty($value)) {
            $result = [$value];
	}
	else {
            $result = [];
	}
    }
    else {
	$result = [$value];
    }

    return $result;
}

function bzhy_cleanHashes(&$value) {
    if (is_array($value)) {
        // reset() is needed to move internal array pointer to the beginning of the array
	reset($value);
        if (bzhy_ctype_digit(key($value))) {
            $value = array_values($value);
	}
    }
    return $value;
}

// accepts parameter as integer either
function bzhy_ctype_digit($x) {
    return ctype_digit(strval($x));
}

/**
* Adds the given fields to the "output" option if it's not already present.
*
* @param string $output
* @param array $fields        either a single field name, or an array of fields
*
* @return mixed
*/
function outputExtend($output, array $fields) {
    if ($output === null) {
	return $fields;
    }
    // if output is set to extend, it already contains that field; return it as is
    elseif ($output === BZHYAPI_OUTPUT_EXTEND) {
	return $output;
    }
    // if output is an array, add the additional fields
    return array_keys(array_flip(array_merge($output, $fields)));
}

/**
 * Sorts the data using a natural sort algorithm.
 *
 * Not suitable for sorting macros, use order_macros() instead.
 *
 * @param $data
 * @param null $sortfield
 * @param string $sortorder
 *
 * @return bool
 *
 * @see order_macros()
 */
function bzhyorder_result(&$data, $sortfield = null, $sortorder = ZBX_SORT_UP) {
    if (empty($data)) {
	return false;
    }
    if (is_null($sortfield)) {
	natcasesort($data);
	if ($sortorder != BZHY_SORT_UP) {
            $data = array_reverse($data, true);
	}
	return true;
    }
    $sort = [];
    foreach ($data as $key => $arr) {
	if (!isset($arr[$sortfield])) {
            return false;
	}
	$sort[$key] = $arr[$sortfield];
    }
    natcasesort($sort);
    if ($sortorder != BZHY_SORT_UP) {
        $sort = array_reverse($sort, true);
    }
    $tmp = $data;
    $data = [];
    foreach ($sort as $key => $val) {
	$data[$key] = $tmp[$key];
    }
    return true;
}

/**
 * Returns paging line and recursively slice $items of current page.
 *
 * @param array  $items				list of elements
 * @param string $sortorder			the order in which items are sorted ASC or DESC
 * @param CUrl $url					URL object containing arguments and query
 *
 * @return CDiv
 */
function bzhygetPagingLine(&$items, $sortorder, bzhyCUrl $url) {
    global $page;
    $rowsPerPage = (int) bzhyCWebUser::$data['rows_per_page'];
    $config = bzhyselect_config();
    $itemsCount = count($items);
    $limit_exceeded = ($config['search_limit'] < $itemsCount);
    $offset = 0;

    if ($limit_exceeded) {
	if ($sortorder == BZHY_SORT_DOWN) {
            $offset = $itemsCount - $config['search_limit'];
        }
	$itemsCount = $config['search_limit'];
    }

    $pagesCount = ($itemsCount > 0) ? ceil($itemsCount / $rowsPerPage) : 1;
    $currentPage = bzhygetPageNumber();

    if ($currentPage < 1) {
	$currentPage = 1;
    }
    elseif ($currentPage > $pagesCount) {
	$currentPage = $pagesCount;
    }

    $tags = [];

    if ($pagesCount > 1) {
	// For MVC pages $page is not set
	if (isset($page['file'])) {
            bzhyCProfile::update('web.paging.lastpage', $page['file'], BZHYPROFILE_TYPE_STR);
            bzhyCProfile::update('web.paging.page', $currentPage, BZHYPROFILE_TYPE_INT);
	}
	elseif (isset($_REQUEST['action'])) {
            bzhyCProfile::update('web.paging.lastpage', $_REQUEST['action'], BZHYPROFILE_TYPE_STR);
            bzhyCProfile::update('web.paging.page', $currentPage, BZHYPROFILE_TYPE_INT);
	}
	// viewed pages (better to use odd)
	$pagingNavRange = 11;

	$endPage = $currentPage + floor($pagingNavRange / 2);
	if ($endPage < $pagingNavRange) {
            $endPage = $pagingNavRange;
	}
        if ($endPage > $pagesCount) {
            $endPage = $pagesCount;
	}

	$startPage = ($endPage > $pagingNavRange) ? $endPage - $pagingNavRange + 1 : 1;

	if ($startPage > 1) {
            $url->setArgument('page', 1);
            $tags[] = new CLink(_x('First', 'page navigation'), $url->getUrl());
	}

	if ($currentPage > 1) {
            $url->setArgument('page', $currentPage - 1);
            $tags[] = new bzhyCLink(
                (new bzhyCSpan())->addClass(ZBX_STYLE_ARROW_LEFT), $url->getUrl()
            );
	}
	for ($p = $startPage; $p <= $endPage; $p++) {
            $url->setArgument('page', $p);
            $link = new bzhyCLink($p, $url->getUrl());
            if ($p == $currentPage) {
                $link->addClass(BZHY_STYLE_PAGING_SELECTED);
            }
            $tags[] = $link;
	}

	if ($currentPage < $pagesCount) {
            $url->setArgument('page', $currentPage + 1);
            $tags[] = new bzhyCLink((new CSpan())->addClass(BZHY_STYLE_ARROW_RIGHT), $url->getUrl());
	}

	if ($p < $pagesCount) {
            $url->setArgument('page', $pagesCount);
            $tags[] = new bzhyCLink(_x('Last', 'page navigation'), $url->getUrl());
	}
    }

    $total = $limit_exceeded ? $itemsCount.'+' : $itemsCount;
    $start = ($currentPage - 1) * $rowsPerPage;
    $end = $start + $rowsPerPage;

    if ($end > $itemsCount) {
	$end = $itemsCount;
    }

    if ($pagesCount == 1) {
	$table_stats = _s('Displaying %1$s of %2$s found', $itemsCount, $total);
    }
    else {
	$table_stats = _s('Displaying %1$s to %2$s of %3$s found', $start + 1, $end, $total);
    }

    // Trim array with elements to contain elements for current page.
    $items = array_slice($items, $start + $offset, $end - $start, true);

    return (new bzhyCDiv())
	->addClass(BZHY_STYLE_TABLE_PAGING)
	->addItem(
            (new bzhyCDiv())
		->addClass(BZHY_STYLE_PAGING_BTN_CONTAINER)
		->addItem($tags)
		->addItem(
            (new bzhyCDiv())
                ->addClass(BZHY_STYLE_TABLE_STATS)
                ->addItem($table_stats)
            )
	);
}

/**
 * Select configuration parameters.
 *
 * @static array $config	Array containing configuration parameters.
 *
 * @return array
 */
function bzhyselect_config() {
    static $config;
    if (!isset($config)) {
	$config = DBfetch(DBselect('SELECT c.* FROM config c'));
    }

    return $config;
}

/**
 * Returns the list page number for the current page.
 *
 * The functions first looks for a page number in the HTTP request. If no number is given, falls back to the profile.
 * Defaults to 1.
 *
 * @return int
 */
function bzhygetPageNumber() {
    global $page;

    $pageNumber = getRequest('page');
    if (!$pageNumber) {
	$lastPage = bzhyCProfile::get('web.paging.lastpage');
	// For MVC pages $page is not set so we use action instead
	if (isset($page['file']) && $lastPage == $page['file']) {
            $pageNumber = bzhyCProfile::get('web.paging.page', 1);
	}
	elseif (isset($_REQUEST['action']) && $lastPage == $_REQUEST['action']) {
            $pageNumber = bzhyCProfile::get('web.paging.page', 1);
	}
	else {
            $pageNumber = 1;
	}
    }

    return $pageNumber;
}

/**
 * Sorts the data using a natural sort algorithm.
 *
 * Not suitable for sorting macros, use order_macros() instead.
 *
 * @param $data
 * @param null $sortfield
 * @param string $sortorder
 *
 * @return bool
 *
 * @see order_macros()
 */
function bzjuorder_result(&$data, $sortfield = null, $sortorder = ZBX_SORT_UP) {
    if (empty($data)) {
	return false;
    }
    if (is_null($sortfield)) {
	natcasesort($data);
	if ($sortorder != ZBX_SORT_UP) {
            $data = array_reverse($data, true);
	}
	return true;
    }

    $sort = [];
    foreach ($data as $key => $arr) {
	if (!isset($arr[$sortfield])) {
            return false;
	}
	$sort[$key] = $arr[$sortfield];
    }
    natcasesort($sort);

    if ($sortorder != ZBX_SORT_UP) {
	$sort = array_reverse($sort, true);
    }

    $tmp = $data;
    $data = [];
    foreach ($sort as $key => $val) {
	$data[$key] = $tmp[$key];
    }

    return true;
}

function bzhyunpack_object(&$item) {
	$res = '';
	if (is_object($item)) {
		$res = $item->toString(false);
	}
	elseif (is_array($item)) {
		foreach ($item as $id => $dat) {
			$res .= unpack_object($item[$id]); // attention, recursion !!!
		}
	}
	elseif (!is_null($item)) {
		$res = strval($item);
		unset($item);
	}
	return $res;
}

// Convert timestamp to string representation. Return 'Never' if 0.
function bzhy_date2str($format, $value = null) {
    static $weekdaynames, $weekdaynameslong, $months, $monthslong;

    $prefix = '';

    if ($value === null) {
	$value = time();
    }
    elseif ($value > BZHY_MAX_DATE) {
	$prefix = '> ';
	$value = BZHY_MAX_DATE;
    }
    elseif (!$value) {
	return _('Never');
    }

    if (!is_array($weekdaynames)) {
	$weekdaynames = [
	0 => _('Sun'),
	1 => _('Mon'),
	2 => _('Tue'),
	3 => _('Wed'),
	4 => _('Thu'),
	5 => _('Fri'),
	6 => _('Sat')
	];
    }

    if (!is_array($weekdaynameslong)) {
	$weekdaynameslong = [
        0 => _('Sunday'),
	1 => _('Monday'),
	2 => _('Tuesday'),
	3 => _('Wednesday'),
	4 => _('Thursday'),
	5 => _('Friday'),
	6 => _('Saturday')
        ];
    }

    if (!is_array($months)) {
	$months = [
            1 => _('Jan'),
            2 => _('Feb'),
            3 => _('Mar'),
            4 => _('Apr'),
            5 => _x('May', 'May short'),
            6 => _('Jun'),
            7 => _('Jul'),
            8 => _('Aug'),
            9 => _('Sep'),
            10 => _('Oct'),
            11 => _('Nov'),
            12 => _('Dec')
	];
    }

    if (!is_array($monthslong)) {
	$monthslong = [
            1 => _('January'),
            2 => _('February'),
            3 => _('March'),
            4 => _('April'),
            5 => _('May'),
            6 => _('June'),
            7 => _('July'),
            8 => _('August'),
            9 => _('September'),
            10 => _('October'),
            11 => _('November'),
            12 => _('December')
	];
    }

    $rplcs = [
        'l' => $weekdaynameslong[date('w', $value)],
	'F' => $monthslong[date('n', $value)],
	'D' => $weekdaynames[date('w', $value)],
	'M' => $months[date('n', $value)]
    ];

    $output = $part = '';
    $length = strlen($format);

    for ($i = 0; $i < $length; $i++) {
	$pchar = ($i > 0) ? substr($format, $i - 1, 1) : '';
	$char = substr($format, $i, 1);

	if ($pchar != '\\' && isset($rplcs[$char])) {
            $output .= (strlen($part) ? date($part, $value) : '').$rplcs[$char];
            $part = '';
	}
	else {
            $part .= $char;
	}
    }

    $output .= (strlen($part) > 0) ? date($part, $value) : '';

    return $prefix.$output;
}

function bzhyclear_messages($count = null) {
    global $BZHY_MESSAGES;
    if ($count != null) {
	$result = [];
        while ($count-- > 0) {
            array_unshift($result, array_pop($BZHY_MESSAGES));
	}
    }
    else {
	$result = $BZHY_MESSAGES;
	$BZHY_MESSAGES = [];
    }

    return $result;
}

function bzhyinfo($msgs) {
    global $BZHY_MESSAGES;
    if (!isset($BZHY_MESSAGES)) {
        $BZHY_MESSAGES = [];
    }

    bzhy_value2array($msgs);
    foreach ($msgs as $msg) {
	$BZHY_MESSAGES[] = ['type' => 'info', 'message' => $msg];
    }
}
function bzhyerror($msgs) {
	global $BZHY_MESSAGES;

	if (!isset($BZHY_MESSAGES)) {
		$BZHY_MESSAGES = [];
	}

	$msgs = bzhy_toArray($msgs);

	foreach ($msgs as $msg) {
		$BZHY_MESSAGES[] = ['type' => 'error', 'message' => $msg];
	}
}

function bzhy_formatDomId($value) {
	return str_replace(['[', ']'], ['_', ''], $value);
}

function bzhyget_js($script, $jQueryDocumentReady = false) {
	return $jQueryDocumentReady
		? '<script type="text/javascript">'."\n".'jQuery(document).ready(function() { '.$script.' });'."\n".'</script>'
		: '<script type="text/javascript">'."\n".$script."\n".'</script>';
}

function bzhyfatal_error($msg) {
	require_once dirname(__FILE__).'/bzhypage_header.php';
	bzhyshow_error_message($msg);
	require_once dirname(__FILE__).'/bzhypage_footer.php';
}

function bzhyshow_error_message($msg) {
    bzhyshow_messages(false, '', $msg);
}

function bzhyshow_messages($good = false, $okmsg = null, $errmsg = null) {
	global $page, $BZHY_MESSAGES;

    if (!defined('PAGE_HEADER_LOADED')) {
//		return null;
	}
	if (defined('ZBX_API_REQUEST')) {
		return null;
	}
	if (!isset($page['type'])) {
		$page['type'] = BZHYPAGE_TYPE_HTML;
	}

	$imageMessages = [];

	$title = $good ? $okmsg : $errmsg;
	$messages = isset($BZHY_MESSAGES) ? $BZHY_MESSAGES : [];

	$BZHY_MESSAGES = [];

	switch ($page['type']) {
            case BZHYPAGE_TYPE_IMAGE:
                if ($title !== null) {
			$imageMessages[] = [
                            'text' => $title,
					'color' => (!$good) ? ['R' => 255, 'G' => 0, 'B' => 0] : ['R' => 34, 'G' => 51, 'B' => 68]
				];
			}

			foreach ($messages as $message) {
				$imageMessages[] = [
					'text' => $message['message'],
					'color' => $message['type'] == 'error'
						? ['R' => 255, 'G' => 55, 'B' => 55]
						: ['R' => 155, 'G' => 155, 'B' => 55]
				];
			}
			break;
		case BZHYPAGE_TYPE_XML:
			if ($title !== null) {
				echo htmlspecialchars($title)."\n";
			}

			foreach ($messages as $message) {
				echo '['.$message['type'].'] '.$message['message']."\n";
			}
			break;
		case BZHYPAGE_TYPE_HTML:
		default:
                    if ($title || $messages) {
			bzhymakeMessageBox($good, $messages, $title, true, !$good)->show();
                    }
                    break;
	}

	// draw an image with the messages
	if ($imageMessages) {
		$imageFontSize = 8;

		// calculate the size of the text
		$imageWidth = 0;
		$imageHeight = 0;
		foreach ($imageMessages as &$msg) {
			$size = imageTextSize($imageFontSize, 0, $msg['text']);
			$msg['height'] = $size['height'] - $size['baseline'];

			// calculate the total size of the image
			$imageWidth = max($imageWidth, $size['width']);
			$imageHeight += $size['height'] + 1;
		}
		unset($msg);

		// additional padding
		$imageWidth += 2;
		$imageHeight += 2;

		// create the image
		$canvas = imagecreate($imageWidth, $imageHeight);
		imagefilledrectangle($canvas, 0, 0, $imageWidth, $imageHeight, imagecolorallocate($canvas, 255, 255, 255));

		// draw each message
		$y = 1;
		foreach ($imageMessages as $msg) {
			$y += $msg['height'];
			imageText($canvas, $imageFontSize, 0, 1, $y,
				imagecolorallocate($canvas, $msg['color']['R'], $msg['color']['G'], $msg['color']['B']),
				$msg['text']
			);
		}
		imageOut($canvas);
		imagedestroy($canvas);
	}
}

function bzhymakeMessageBox($good, array $messages, $title = null, $show_close_box = true, $show_details = false)
{
	$class = $good ? ZBX_STYLE_MSG_GOOD : ZBX_STYLE_MSG_BAD;
	$msg_box = (new CDiv($title))->addClass($class);

	if ($messages) {
		$msg_details = (new CDiv())->addClass(ZBX_STYLE_MSG_DETAILS);

		if ($title !== null) {
			$link = (new CSpan(_('Details')))
				->addClass(ZBX_STYLE_LINK_ACTION)
				->onClick('javascript: showHide($(this).next(\'.'.ZBX_STYLE_MSG_DETAILS_BORDER.'\'));');
			$msg_details->addItem($link);
		}

		$list = new CList();
		if ($title !== null) {
			$list->addClass(ZBX_STYLE_MSG_DETAILS_BORDER);

			if (!$show_details) {
				$list->setAttribute('style', 'display: none;');
			}
		}
		foreach ($messages as $message) {
			foreach (explode("\n", $message['message']) as $message_part) {
				$list->addItem($message_part);
			}
		}
		$msg_details->addItem($list);

		$msg_box->addItem($msg_details);
	}

	if ($show_close_box) {
		$msg_box->addItem((new CSimpleButton())
			->addClass(ZBX_STYLE_OVERLAY_CLOSE_BTN)
			->onClick('jQuery(this).closest(\'.'.$class.'\').remove();')
			->setAttribute('title', _('Close')));
	}

	return $msg_box;
}
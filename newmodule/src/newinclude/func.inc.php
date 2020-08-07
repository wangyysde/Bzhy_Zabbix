<?php

/* 
 * Author: Wayne Wang
 * Website: http://www.bzhy.com
 * Date: Aug 28 2017
 * Each line should be prefixed with  * 
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
function createDateMenu($name, $date, $relatedCalendar = null) {
	$onClick = 'var pos = getPosition(this); pos.top += 10; pos.left += 16; CLNDR["'.$name.
		'_calendar"].clndr.clndrshow(pos.top, pos.left);';
	if ($relatedCalendar) {
		$onClick .= ' CLNDR["'.$relatedCalendar.'_calendar"].clndr.clndrhide();';
	}

	if (is_array($date)) {
		$y = $date['y'];
		$m = $date['m'];
		$d = $date['d'];
		$h = $date['h'];
		$i = $date['i'];
	}
	else {
		$y = date('Y', $date);
		$m = date('m', $date);
		$d = date('d', $date);
		$h = date('H', $date);
		$i = date('i', $date);
	}

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

	zbx_add_post_js('create_calendar(null,'.
		'["'.$name.'_day","'.$name.'_month","'.$name.'_year","'.$name.'_hour","'.$name.'_minute"],'.
		'"'.$name.'_calendar",'.
		'"'.$name.'");'
	);

	return $fields;
}
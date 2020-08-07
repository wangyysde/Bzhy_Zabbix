<?php
require_once dirname(__FILE__).'/include/config.inc.php';
require_once dirname(__FILE__).'/classes/CBase.php';
$objectFiles=['classes/CContact.php','classes/CAttachment.php','classes/CIdc.php','classes/CIdcbox.php'];
$objects = ['idc_room' => 'CIdc',
             'file'  => 'CAttachment',
             'contact' => 'CContact',
             'idc_box' => 'CIdcbox'];

CBase::addObjects($objects);
$CBase = new CBase();
$CBase->addFiles($objectFiles);
$CBase->loadFiles();

$page['title'] = _('Install OS');
$page['file'] = 'auto_install.php';
$page['hist_arg'] = array();

$themes = array_keys(Z::getThemes());
$INSTALL_SCRIPT="install_scripts.bat";
$MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());

function handle_append_file($install_mac,$filename,$macro_array,$pxecfgpath){
	$pos = strrpos($filename,".");
	$fpforce=substr($filename,0,$pos );
	$fpext = substr($filename,($pos + 1));
	$newfilename = strtolower($fpforce."_".$install_mac.".".$fpext);
	if(!$fp=@fopen($pxecfgpath.$filename,"r")){
		echo "打开指定的模板文件出错，请确认系统所配置的路径是否正确，并且系统具有读取权限";
 		exit;
 	}
	$newcontent = "";
 	while (($buffer = fgets($fp, 4096)) !== false) {
 		foreach($macro_array as $key => $value){
				$buffer = str_replace($key,$value,$buffer);
		}
		$newcontent .= $buffer;
	}
	fclose($fp);
	$pos = strrpos($filename,"/");
	$tmpfilename = substr($newfilename,($pos + 1));
	$newfilename = "start_cfg/".$tmpfilename;
	if(!$fp=@fopen($pxecfgpath.$newfilename,"w")){
		echo "创建启动配置文件出错，请确认系统懿配置的路径是否正确，并且系统具有写入权限";
 		exit;
 	}
 fwrite($fp, $newcontent);
 fclose($fp);
 return("pxelinux.cfg/".$newfilename);
}	
			
function handle_macro_file($install_mac,$line_str,$macro_str,$macro_array,$pxecfgpath){
	$ret_str="";
	$end_str=$line_str;
	$pos = strpos($end_str,$macro_str);
	while($pos !== false) {
		if($pos > 0)
			$ret_str .= substr($end_str,0,$pos);
		$end_str =  substr($end_str,($pos+strlen($macro_str)));
		$end_macro_pos = strpos($end_str,"}");
		if($end_macro_pos !== false){
			$tmp_content = 	substr($end_str,0,$end_macro_pos);
			$eq_sign_pos = strpos($tmp_content,"=");
			$filename =  substr($tmp_content,($eq_sign_pos+1));
			$newfilename = "/".handle_append_file($install_mac,$filename,$macro_array,$pxecfgpath);
			$end_str = substr($end_str,($end_macro_pos + 1));
			$ret_str .= $newfilename;
		}
		else{
				return false;
		}
		$pos = strpos($end_str,$macro_str);
	}
	foreach($macro_array as $key => $value){
		$ret_str = str_replace($key,$value,$ret_str);
	}
	return 	$ret_str;
}
/*
if(!$DB_CONN=mysql_connect($DB["PORT"]?$DB["SERVER"].":".$DB["PORT"]:$DB["SERVER"],$DB["USER"],$DB["PASSWORD"])){
	die('Could not connect MySQL Server: ' . mysql_error());
}
 */
//mysql_query("set names 'utf8'"); 

require_once dirname(__FILE__).'/include/page_header.php';
DBstart();
$query="select pxenetname,pxedefaultdns1,pxedefaultdns2,pxedefaultgw,pxedefaultpasswd,pxedefaultnet1,pxedefaultmask1,pxedefaultnet2,pxedefaultmask2 from pxeconfig where pxestatus=0 group by pxenetname;";
//$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
$result = DBselect($query);
$netname = "";
$ostype = "";
$osver = "";
$i=0;
//	while($result_array=mysql_fetch_array($result)){
	  while($result_array = DBfetch($result)) {
		$netname[$result_array["pxenetname"]]=$result_array["pxenetname"];
		$defaultdns1[$i]=$result_array["pxedefaultdns1"];
		$defaultdns2[$i]=$result_array["pxedefaultdns2"];
		$defaultgw[$i]=$result_array["pxedefaultgw"];
		$defaultpasswd[$i]=$result_array["pxedefaultpasswd"];
		$defaultnet1[$i]=$result_array["pxedefaultnet1"];
		$defaultmask1[$i]=$result_array["pxedefaultmask1"];
		$defaultnet2[$i]=$result_array["pxedefaultnet2"];
		$defaultmask2[$i]=$result_array["pxedefaultmask2"];
		$i++;
	}
	$DEFAULTDNS1=build_js_var($defaultdns1,"defaultdns1");
	$DEFAULTDNS2=build_js_var($defaultdns2,"defaultdns2");
	$DEFAULTGW=build_js_var($defaultgw,"defaultgw");
	$DEFAULTPASSWD=build_js_var($defaultpasswd,"defaultpasswd");
	$DEFAULTNET1=build_js_var($defaultnet1,"defaultnet1");
	$DEFAULTMASK1=build_js_var($defaultmask1,"defaultmask1");	
	$DEFAULTNET2=build_js_var($defaultnet2,"defaultnet2");
	$DEFAULTMASK2=build_js_var($defaultmask2,"defaultmask2");		
	foreach($netname as $key => $value){
		$query="select pxeostype from pxeos where pxenetname='".$value."' and pxestatus=0 group by pxeostype;";
		$result = DBselect($query);
		//$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
		while($result_array=DBfetch($result)){
			$ostype[$key][$result_array["pxeostype"]] = $result_array["pxeostype"];
			$query_ver="select pxeosver,pxebootsize,pxeswapsize,pxerootsize from pxeos where pxenetname='".$value."' and pxeostype = '".$result_array["pxeostype"]."' and pxestatus=0  group by pxeosver;";
			//$result_ver=mysql_db_query($DB["DATABASE"],$query_ver,$DB_CONN);
			$result_ver = DBselect($query_ver);
			while($result_ver_array=DBfetch($result_ver)){
				$osver[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] = $result_ver_array["pxeosver"];
				$bootsize[$key][$result_array["pxeostype"]][$result_ver_array["pxebootsize"]] = $result_ver_array["pxebootsize"];
				$swapsize[$key][$result_array["pxeostype"]][$result_ver_array["pxeswapsize"]] = $result_ver_array["pxeswapsize"];
				$rootsize[$key][$result_array["pxeostype"]][$result_ver_array["pxerootsize"]] = $result_ver_array["pxerootsize"];
				$query_command = "select pxecommandid,pxecommandtitle,pxecommandmust from pxecommand where pxenetname='".$value."' and pxeostype = '".$result_array["pxeostype"]."' and pxeosver = '".$result_ver_array["pxeosver"]."' and pxecommandstatus=0 order by pxecommandmust desc;";
				//$result_command=mysql_db_query($DB["DATABASE"],$query_command,$DB_CONN);
				$result_command = DBselect($query_command);
				$command[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] = "";
				$i=0;
				while($result_command_array=DBfetch($result_command)){
						$command[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] .="<input type='checkbox' name='commands[]' value='".$result_command_array["pxecommandid"]."' id='commands_".$i."' ";
						if($result_command_array["pxecommandmust"] > 0)
							$command[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] .=" checked >";
						else
						  $command[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] .=" >";
						$command[$key][$result_array["pxeostype"]][$result_ver_array["pxeosver"]] .="&nbsp;".$result_command_array["pxecommandtitle"]."&nbsp;&nbsp;";
						$i++;
				}
			}
			
		}
		
	}
  $JS_VAS = build_opt_js($netname,$ostype,$osver);
  $JS_ARRAY_VAS = build_js_array($bootsize,"bootsize");
  $JS_ARRAY_VAS .= build_js_array($swapsize,"swapsize");
  $JS_ARRAY_VAS .= build_js_array($rootsize,"rootsize");
  $JS_ARRAY_VAS .= build_js_array($command,"commands");
  require_once("views/os/new_install_form.html");
  DBend();
require_once dirname(__FILE__).'/include/page_footer.php';


<?php
require_once("../conf/zabbix.conf.php");
$INSTALL_SCRIPT="install_scripts.bat";
$KSREPLATE_ARRAY["{DATATIME}"] = @date("YmdHis",time());
$COMMANDREPLACE_ARRAY = "";
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
	return(	$js_main_str.$js_sub_str.$js_third_str);
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

function check_vars_ok($var_list, $check_method,$check_paras,$message,$is_exit){
	$will_exit = false;
	$errormsg="";
	foreach($var_list as $var_key => $var_name){
		if(!isset($_REQUEST[$var_name])){
			if($is_exit[$var_name]) 
				$will_exit = true;
			$errormsg .= $message[$var_name]."<br>\n";
		}else{
			if(is_array($_REQUEST[$var_name])){
				foreach($_REQUEST[$var_name] as $key => $value){
					if(trim($value) ==  ""){
						if($is_exit[$var_name])
							$will_exit = true;
						$errormsg .= $message[$var_name].":".$var_name."[".$key."]<br>\n";
					}
					$check_paras_array = @split("||",$check_paras[$var_name]);
					$check_method_array = @split("||",$check_method[$var_name]);
					$i = 0;
					foreach($check_method_array as $method_key => $method_value){
						if(isset($check_method_array[$i])){
							if(@isset($check_paras_array[$i]))
								$para_value = $check_paras_array[$i];
							else
								$para_value = "";
							if(!check_var($value,$check_method_array[$i],$para_value)){
								if($is_exit[$var_name])
									$will_exit = true;
								$errormsg .= $message[$var_name].":".$var_name."[".$key."]<br>\n";
							}
						}
						$i++;
					}
				}
			}
			else{
				if(trim($_REQUEST[$var_name]) ==  ""){
						if($is_exit[$var_name])
							$will_exit = true;
						$errormsg .= $message[$var_name].":".$var_name."<br>\n";
				}
				else{
						$check_paras_array = @split("||",$check_paras[$var_name]);
						$check_method_array = @split("||",$check_method[$var_name]);
						$i = 0;
						foreach($check_method_array as $method_key => $method_value){
							if(isset($check_method_array[$i])){
								if(@isset($check_paras_array[$i]))
								$para_value = $check_paras_array[$i];
								else
								$para_value = "";
								if(!check_var(trim($_REQUEST[$var_name]),$check_method_array[$i],$para_value)){
									if($is_exit[$var_name])
										$will_exit = true;
									$errormsg .= $message[$var_name].":".$var_name."[".$key."]<br>\n";
								}
							}
						$i++;
					}
				}
			}
		}
	}
	$ret["is_exited"] =  $will_exit;
	$ret["errormsg"] =  $errormsg;
/*	if($will_exit){
		echo 	$errormsg;
		exit;
	}
*/
  return $ret;
}

function check_var($var,$check_method,$check_paras){
	switch($check_method){
		case"stringlen":
			if(!is_string($var)) return false;
			$para_array=split(",",$check_paras);
			if(strlen($var) < $para_array[0] || strlen($var) > $para_array[1])
				return false;
			return true;
			break;
		case"mac":
			return (preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $var) == 1);	
			break;
		case"checkipv4":
			retrun (filter_var($var, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
			break;
		case"checkstrequal":
			$para_array=split(",",$check_paras);
			foreach($para_array as $para_key => $para_value){
				if(@!isset($_REQUEST[$para_value])) return false;
				if($var != trim($_REQUEST[$para_value])) return false;
			}
			break;
		case"numsize":
			 $para_array=split(",",$check_paras);
			 if(!@isset($para_array[1])) return false;
			 if($var < $para_array[0] || $var > $para_array[1] )
			 	return false;
			 break;
		default:
			return false;
			break;
	}
}

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
		echo "创建启动配置文件出错，请确认系统所配置的路径是否正确，并且系统具有写入权限";
 		exit;
 	}
 fwrite($fp, $newcontent);
 fclose($fp);
 return($newfilename);
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

function str_relace_array($str,$replace_array){
	if(!is_array($replace_array))
		return false;
//	if(trim($str) == "")
//		return false;
	foreach($replace_array as $key => $value){
			$str =  str_replace( $key , $value , $str );
	}
	return($str);
}

if(!$DB_CONN=mysql_connect($DB["PORT"]?$DB["SERVER"].":".$DB["PORT"]:$DB["SERVER"],$DB["USER"],$DB["PASSWORD"])){
	die('Could not connect MySQL Server: ' . mysql_error());
}
if(isset($_REQUEST["action"])){
	if(strtolower(trim($_REQUEST["action"])) === "newhostinstall"){    //新服务器安装操作操作系统表单处理
 			$var_list = array("hostmac","hostname","netip", "maskip","hostdns1", "hostdns2", "hostgw","rootpasswd", "rerootpasswd","bootsize");
 			$check_method = array("hostmac" => "mac","hostname" => "stringlen","netip" => "checkipv4","maskip" => "checkipv4", "hostdns1" => "checkipv4", "hostdns2" => "checkipv4", "hostgw" => "checkipv4","rootpasswd" => "stringlen", "rerootpasswd" => "checkstrequal", "bootsize" => "numsize"); 
 			$check_paras = array("hostmac" => "", "hostname" => "3,15","netip" => "", "maskip" => "", "hostdns1" => "", "hostdns2" => "", "hostgw" => "", "rootpasswd" => "6,15", "rerootpasswd" => "rerootpasswd", "bootsize" => "100,200"); 
 			$message = array("hostmac" => "服务器被用于安装操作系统的网卡所对应的MAC地址必须输入！","hostname" => "主机名必须是3到15个字符", "netip" => "所输入的IP地址必须合法", "maskip" => "所输入的掩码地址必须合法", "hostdns1" => "所输入的DNSIP地址必须合法", "hostdns2" => "所输入的DNSIP地址必须合法", "hostgw" => "所输入的网关IP地址必须合法", "rootpasswd" => "管理员密码必须是6到15个数字和字母组合", "rerootpasswd" => "两次输入的密码不一致", "bootsize" => "/boot分区大小为100到200MB");
 			$is_exit = array("hostmac" => true, "hostname" => false, "netip" => true,"maskip" => true, "hostdns1" => true, "hostdns2" => true, "hostgw" => true, "rootpasswd" => true, "rerootpasswd" => true, "bootsize" => true); 
 			$check_res = check_vars_ok($var_list, $check_method,$check_paras,$message,$is_exit);
 			if( $check_res["is_exited"]){
 			/*		echo "<script> \n";
 					echo "alert(\"".$check_res["errormsg"]."\");\n";
 					echo "window.history.go(-1);\n";
 					echo "</script>\n";
			*/
					echo $check_res["errormsg"];
					exit;
 			}
 			if(!$check_res["is_exited"] && trim($check_res["errormsg"]) != ""){
 		/*		echo "<script> \n";
 				echo "alert(\"".$check_res["errormsg"]."\");\n";
 				echo "</script>\n";
 		*/
 			echo $check_res["errormsg"];		
 			}
 			$query = "select pxe.pxecfgpath,pxe.pxeksuri,os.pxecfgtemplate  from pxeconfig pxe, pxeos os where pxe.pxenetname = os.pxenetname and pxe.pxenetname='".trim($_REQUEST["netname"])."' and os.pxeostype='".trim($_REQUEST["ostype"])."' and os.pxeosver='".trim($_REQUEST["ostver"])."';";
 			$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
 			$result_array=mysql_fetch_array($result);
 			$pxecfgpath = $result_array[0];
 			$pxeksuri = $result_array[1];
 			$pxecfgtemplate = $result_array[2];
 			$cfgcontent="";
 			//$cfgcontent="default ".trim($_REQUEST["ostver"])."\n";
 			$macro_array["{KSURL}"] = $pxeksuri."ks/".trim($_REQUEST["ostver"])."/".strtolower(str_replace(':',"-",trim($_REQUEST["hostmac"])));
 			$macro_array["{OSVER}"] = trim($_REQUEST["ostver"]);
 			if(!$fp=@fopen($pxecfgpath.$pxecfgtemplate,"r")){
 				echo "打开指定的PXE启动模板文件出错，请确认系统所配置的路径是否正确，并且系统具有读取权限";
 				exit;
 			}
 			while (($buffer = fgets($fp, 4096)) !== false) {
 				$pos = strpos($buffer, "{APPENDFILE");
 				if ($pos !== false) {
 					$cfgcontent .= handle_macro_file(str_replace(':',"-",trim($_REQUEST["hostmac"])),$buffer,"{APPENDFILE",$macro_array,$pxecfgpath);
 				}
 				else{
 					foreach($macro_array as $key => $value){
							$buffer = str_replace($key,$value,$buffer);
					}		
 					$cfgcontent .= $buffer;
 				}
 			}
 			fclose($fp);
 			if(!$fp=@fopen($pxecfgpath."01-".strtolower(str_replace(':',"-",trim($_REQUEST["hostmac"]))),"w")){
 				echo "创建PXE启动配置文件失败，请确认系统所配置的目录是否存在，且系统具写权限！";
 				exit;
 			}
 			fwrite($fp, $cfgcontent);
 			fclose($fp);
 			$hostmac = strtolower(str_replace(':',"-",trim($_REQUEST["hostmac"])));
 			$netname = trim($_REQUEST["netname"]); 
 			$ostype = trim($_REQUEST["ostype"]); 
 			$ostver = trim($_REQUEST["ostver"]); 
 			$hostname = trim($_REQUEST["hostname"]); 
 			$hosttype = trim($_REQUEST["hosttype"]); 
 			$netip = $_REQUEST["netip"]; 
 			$maskip = $_REQUEST["maskip"];
 			$hostdns1 = trim($_REQUEST["hostdns1"]); 
 			$hostdns2 = trim($_REQUEST["hostdns2"]); 
 			$hostgw = trim($_REQUEST["hostgw"]); 
 			$rootpasswd = trim($_REQUEST["rootpasswd"]);
 	 		$bootsize = trim($_REQUEST["bootsize"]); 
 			$swapsize = trim($_REQUEST["swapsize"]);		 		
 			$rootsize = trim($_REQUEST["rootsize"]); 
 			$commands = $_REQUEST["commands"];	
 			$query = "select pxehostid, count(pxehostid) from pxehost where pxenetname='".trim($_REQUEST["netname"])."' and pxeinstallmac =  '".$hostmac."' and pxehoststatus=0";
 			//echo $query;
 			//exit;
 			$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
 			$result_array=mysql_fetch_array($result);
 			if($result_array[1] > 0 ){
 				$pxehostid = $result_array[0];
 				$query = "update pxehost set  pxeosver='".$ostver."', pxehostname='".$hostname."', pxehosttype=".$hosttype.", pxedns1='".$hostdns1."', pxedns2='".$hostdns2."'";
 				$query .= ",pxegateway='".$hostgw."',pxerootpasswd='".$rootpasswd."'  where pxehostid=".$pxehostid;
 			}
 			else{
 				$query  = "insert into pxehost(pxenetname,pxeosver,pxehostname,pxehosttype,pxedns1,pxedns2,pxegateway,pxeinstallmac,pxerootpasswd) values ('";
 				$query .= $netname."','".$ostver."','".$hostname."',".$hosttype.",'".$hostdns1."','".$hostdns2."','".$hostgw."','".$hostmac."','".$rootpasswd."')";
 			}
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			if(!@isset($pxehostid) || trim($pxehostid) == ""){
 				$query = "select  max(pxehostid) from pxehost ";
 				if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				   echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				   exit;
 				}
 				$result_array=mysql_fetch_array($result);
 				$pxehostid = $result_array[0];
 			}
 			$query  = "delete from pxenics where pxehostid=".$pxehostid;
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query = "";
 			$i = 0;
 			foreach($netip as $key => $value){
 				if( $i == 0){
 					$query =  "insert into pxenics(pxenicno,pxehostid,pxenicip,pxenicmask,pxenicenbled) values (";
 					$query .= $key.",".$pxehostid.",'".$value."','".$maskip[$key]."',0)";
 				}
 				else{
 					$query .= ", (".$key.",".$pxehostid.",'".$value."','".$maskip[$key]."',0)";
 				}
 				$i++;
 			}
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query  = "delete from pxedisk where pxehostid=".$pxehostid;
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query = "insert into pxedisk(pxehostid,pxedisktype,pxediskfstype,pxediskmount,pxedisksize) values (";
 			$query .= $pxehostid.",0,'ext4','/boot',".$bootsize."),";
 			$query .= " (".$pxehostid.",1,'ext4','swap',".$swapsize."),";
 			$query .= " (".$pxehostid.",0,'ext4','/',".$rootsize.")";
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query  = "delete from pxehostcommand where pxehostid=".$pxehostid;
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query = "";
 			$i = 0;
 			foreach($commands as $key => $value){
 				if( $i == 0){
 					$query = "insert into pxehostcommand(pxehostid,pxecommandid) values (".$pxehostid.",".$value.")";
 				}
 				else
 					$query .= ",(".$pxehostid.",".$value.")";
 				$i++;
 			}
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				  echo "查询数据库出错，错误信息为22222：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			echo "现在请重启所需要安装操作系统的机器，并确保0网卡是连接到网络上了<br>";
 			echo $cfgcontent;
 			exit;
 			
	}
	if(strtolower(trim($_REQUEST["action"])) === "ks"){    //KS信息处理
		  $osver=trim($_REQUEST["osver"]);
		  $hostmac=trim($_REQUEST["hostmac"]);
		  $query = "select pxehostid, pxenetname,pxeosver,pxehostname,pxedns1,pxedns2,pxegateway, pxerootpasswd from pxehost where pxeinstallmac='".$hostmac."' and pxehoststatus=0";
		  if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
 			$result_array=mysql_fetch_array($result);
 			$pxehostid = $result_array[0];
 			$pxenetname = $result_array[1];
 			$pxehostname = $result_array[3];
 			$pxerootpasswd  = $result_array[7];
 			$COMMANDREPLACE_ARRAY["{OSVER}"] = $osver;
 			$COMMANDREPLACE_ARRAY["{INSTALLMAC}"] =strtolower(str_replace(':',"-",trim($hostmac)));
 			$COMMANDREPLACE_ARRAY["{HOSTID}"] = $pxehostid;
 			$COMMANDREPLACE_ARRAY["{HOSTNAME}"] = $pxehostname;
 			$COMMANDREPLACE_ARRAY["{INSTALLPASSWD}"] = $pxerootpasswd;
 			$COMMANDREPLACE_ARRAY["{DNS1}"] = $result_array[4];
 			$COMMANDREPLACE_ARRAY["{DNS2}"] = $result_array[5];
 			$COMMANDREPLACE_ARRAY["{GATEWAY}"] = $result_array[6];
 			$query = "select pxeoshomeuri,pxekstemplatepath,pxeksuri from pxeconfig where pxenetname='".$pxenetname."' and pxestatus=0";
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 				echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
			}
		  $result_array=mysql_fetch_array($result);
		  $pxeoshomeuri = $result_array[0];
		  $pxeksuri = $result_array[2];
		  $COMMANDREPLACE_ARRAY["{OSHOMEURL}"] = $pxerootpasswd;
		  $COMMANDREPLACE_ARRAY["{KSURL}"] = $$result_array[2];
		  $pxekstemplatepath = $result_array[1];
		  $query =  "select pxeosuri, pxecommandsuri,pxewgetcommand,pxeinstallscriptpath,pxechmodcommand,pxermcommand from pxeos where pxenetname='".$pxenetname."' and pxeosver='".$osver."' and pxestatus=0";
		  if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $result_array=mysql_fetch_array($result);
		  $pxeosuri = $result_array[0];
		  $pxecommandsuri = $result_array[1];
		  $pxewgetcommand = $result_array[2];
		  $pxeinstallscriptpath = $result_array[3];
		  $pxechmodcommand = $result_array[4];
		  $pxermcommand = $result_array[5];
		  $query = "select pxedisktype,pxediskfstype,pxediskmount,pxedisksize from pxedisk where pxehostid=".$pxehostid;
		  if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $i = 0;
		  $diskcontent="";
		 	while($result_array=mysql_fetch_array($result)){
		 		if(($result_array[1] == 1) && $result_array[3] != 0){
		 				$diskcontent .="part swap --size=".$result_array[3]."\n";
		 		}
		 		else{
		 				if($result_array[3] == 0)
		 					$diskcontent .= "part  ".$result_array[2]."  --fstype=".$result_array[1]."  --size=1 --grow\n";
		 				else
		 					$diskcontent .= "part  ".$result_array[2]."  --fstype=".$result_array[1]."  --size=".$result_array[3]."\n";
		 		}
		 				
				$disk[$i]["pxedisktype"] = $result_array[0];
				$disk[$i]["pxediskfstype"] = $result_array[1];
				$disk[$i]["pxediskmount"] = $result_array[2];
				$disk[$i]["pxedisksize"] = $result_array[3];
				$i++;
			}
			$filename = $pxekstemplatepath."/ks_template_".$osver.".tpl";
			if(!$fp=@fopen($filename,"r")){
				echo "open file error";
 				exit;
 			}
 			$KSREPLATE_ARRAY["{OSIMGURL}"] = $pxeoshomeuri.$pxeosuri;
 			$KSREPLATE_ARRAY["{INSTALLPASSWD}"] = $pxerootpasswd;
 			$KSREPLATE_ARRAY["{DISKPARTS}"] = $diskcontent;
 			$COMMANDREPLACE_ARRAY["{OSIMGURL}"] = $pxeoshomeuri.$pxeosuri;
 			$query ="select pxenicno,pxenicip,pxenicmask,pxenicenbled from pxenics where pxehostid=".$pxehostid." and pxenicstatus=0 order by pxenicno";
 			if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	 echo $query."<br>";
		  	 echo mysql_error()."<br>";
		  	 exit;
		  }
 			for($i=0;$i<4;$i++){
 				if(($result_array=mysql_fetch_array($result))){
 					 $KSREPLATE_ARRAY["{IP".($i+1)."}"] = $result_array[1];
 					 $KSREPLATE_ARRAY["{MASK".($i+1)."}"] = $result_array[2];
 					 $KSREPLATE_ARRAY["{EANBLED".($i+1)."}"] = $result_array[3];
 				}
 				else{
 					 $KSREPLATE_ARRAY["{IP".($i+1)."}"] = "";
 					 $KSREPLATE_ARRAY["{MASK".($i+1)."}"] = "";
 					 $KSREPLATE_ARRAY["{EANBLED".($i+1)."}"] = 1;
 				}
 			}
 			
 			$query = "select c.pxecommandtype,c.pxecommand,c.pxecommandstats from pxecommand c,pxehostcommand hc where c.pxecommandstatus=0 and c.pxecommandid=hc.pxecommandid and hc.pxehostid=".$pxehostid." and c.pxecommandstats=0 and c.pxecommandstatus=0 order by pxecommandexcuno ";
		  if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $i=0;
		  $precommands="";
		  $SCRIPT_REPLACE["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$INSTALL_SCRIPT;
		  while($result_array=mysql_fetch_array($result)){
		  	if($i == 0)
		  		$precommands="%pre\n";
		  	if($result_array[0] == 0){
		  		$precommands .= str_relace_array($result_array[1],$KSREPLATE_ARRAY)."\n";
		  	}
		  	else{
		    	$command_array = split("/",$result_array[1]);
		    	$command_tpl = 	$command_array[0];
		    	$command_para = $command_array[1];
		    	$command_para = str_relace_array($command_para,$KSREPLATE_ARRAY);
		    	$command_para = str_replace("||"," ",$command_para);
		    	$SCRIPT_REPLACE["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/".$command_tpl."/".$pxehostid;
		    	$precommands .=  str_relace_array($pxewgetcommand,$SCRIPT_REPLACE)."\n";
		    	if(trim($pxechmodcommand) !==""){
		    			$precommands .= str_relace_array($pxechmodcommand,$SCRIPT_REPLACE)."\n";
		    	}
		    	$precommands .=$pxeinstallscriptpath.$INSTALL_SCRIPT." ".$command_para."\n";
		    	$precommands .= str_relace_array($pxermcommand,$SCRIPT_REPLACE)."\n";
		    }
		    $precommands .= "\n";
		    $i++;
		  }
 			if($i > 0 ){
 				$precommands .= "%end\n\n";
 			}
 			$KSREPLATE_ARRAY["{PRE_SCRIPT}"] = $precommands;
 			
 			$query = "select c.pxecommandtype,c.pxecommand,c.pxecommandstats from pxecommand c,pxehostcommand hc where c.pxecommandstatus=0 and c.pxecommandid=hc.pxecommandid and hc.pxehostid=".$pxehostid." and c.pxecommandstats=1 and c.pxecommandstatus=0 order by pxecommandexcuno ";
		  if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $i=0;
		  $postcommands="%post\n";
		  $SCRIPT_REPLACE["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$INSTALL_SCRIPT;
		  while($result_array=mysql_fetch_array($result)){
		  	if($result_array[0] == 0){
		  		$postcommands .= str_relace_array($result_array[1],$KSREPLATE_ARRAY)."\n";
		  	}
		  	else{
		    	$command_array = split("/",$result_array[1]);
		    	$command_tpl = 	$command_array[0];
		    	$command_para = $command_array[1];
		    	$command_para = str_relace_array($command_para,$KSREPLATE_ARRAY);
		    	$command_para = str_replace("||"," ",$command_para);
		    	$SCRIPT_REPLACE["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/".$command_tpl."/".$pxehostid;
		    	$postcommands .=  str_relace_array($pxewgetcommand,$SCRIPT_REPLACE)."\n";
		    	if(trim($pxechmodcommand) !==""){
		    			$postcommands .= str_relace_array($pxechmodcommand,$SCRIPT_REPLACE)."\n";
		    	}
		    	$postcommands .=$pxeinstallscriptpath.$INSTALL_SCRIPT." ".$command_para."\n";
		    	$postcommands .= str_relace_array($pxermcommand,$SCRIPT_REPLACE)."\n";
		    }
		    $postcommands .= "\n";
		    $i++;
		  }
		  $SCRIPT_REPLACE["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/finshed_install/".$pxehostid;
		  $postcommands .=  str_relace_array($pxewgetcommand,$SCRIPT_REPLACE)."\n";
		  $postcommands .= str_relace_array($pxermcommand,$SCRIPT_REPLACE)."\n";
		  $postcommands .= "%end\n\n";
		  $KSREPLATE_ARRAY["{POST_SCRIPT}"] = $postcommands;
 			$ks_content="";
 			while (($line_str = fgets($fp, 4096)) !== false) {
 				if(($line_str = str_relace_array($line_str,$KSREPLATE_ARRAY)) !== false)
 					$ks_content .= $line_str;
 				else
 					 exit;
 		  }
 		  echo $ks_content;
 		  exit;
	}
   echo "aaaaa";
	exit;
}
else{
	$query="select pxenetname,pxedefaultdns1,pxedefaultdns2,pxedefaultgw,pxedefaultpasswd,pxedefaultnet1,pxedefaultmask1,pxedefaultnet2,pxedefaultmask2 from pxeconfig where pxestatus=0 group by pxenetname;";
	$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
	$netname = "";
	$ostype = "";
	$osver = "";
	$i=0;
	while($result_array=mysql_fetch_array($result)){
		$netname[$result_array[0]]=$result_array[0];
		$defaultdns1[$i]=$result_array[1];
		$defaultdns2[$i]=$result_array[2];
		$defaultgw[$i]=$result_array[3];
		$defaultpasswd[$i]=$result_array[4];
		$defaultnet1[$i]=$result_array[5];
		$defaultmask1[$i]=$result_array[6];
		$defaultnet2[$i]=$result_array[7];
		$defaultmask2[$i]=$result_array[8];
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
		$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
		while($result_array=mysql_fetch_array($result)){
			$ostype[$key][$result_array[0]] = $result_array[0];
			$query_ver="select pxeosver,pxebootsize,pxeswapsize,pxerootsize from pxeos where pxenetname='".$value."' and pxeostype = '".$result_array[0]."' and pxestatus=0  group by pxeosver;";
			$result_ver=mysql_db_query($DB["DATABASE"],$query_ver,$DB_CONN);
			while($result_ver_array=mysql_fetch_array($result_ver)){
				$osver[$key][$result_array[0]][$result_ver_array[0]] = $result_ver_array[0];
				$bootsize[$key][$result_array[0]][$result_ver_array[1]] = $result_ver_array[1];
				$swapsize[$key][$result_array[0]][$result_ver_array[2]] = $result_ver_array[2];
				$rootsize[$key][$result_array[0]][$result_ver_array[3]] = $result_ver_array[3];
				$query_command = "select pxecommandid,pxecommandtitle,pxecommandmust from pxecommand where pxenetname='".$value."' and pxeostype = '".$result_array[0]."' and pxeosver = '".$result_ver_array[0]."' and pxecommandstatus=0 order by pxecommandmust desc;";
				$result_command=mysql_db_query($DB["DATABASE"],$query_command,$DB_CONN);
				$command[$key][$result_array[0]][$result_ver_array[0]] = "";
				$i=0;
				while($result_command_array=mysql_fetch_array($result_command)){
						$command[$key][$result_array[0]][$result_ver_array[0]] .="<input type='checkbox' name='commands[]' value='".$result_command_array[0]."' id='commands_".$i."' ";
						if($result_command_array[2] > 0)
							$command[$key][$result_array[0]][$result_ver_array[0]] .=" checked >";
						else
						  $command[$key][$result_array[0]][$result_ver_array[0]] .=" >";
						$command[$key][$result_array[0]][$result_ver_array[0]] .="&nbsp;".$result_command_array[1]."&nbsp;&nbsp;";
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
  require_once("template/new_install_form.html");
}


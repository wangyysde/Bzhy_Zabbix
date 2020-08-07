<?php

require_once dirname(__FILE__).'/include/config.inc.php';
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

 DBstart();
	if(strtolower(trim($_REQUEST["action"])) === "newhostinstall"){    //新服务器安装操作操作系统表单处理
 			$var_list = array("hostmac","hostname","netip", "maskip","hostdns1", "hostdns2", "hostgw","rootpasswd", "rerootpasswd","bootsize");
 			$check_method = array("hostmac" => "mac","hostname" => "stringlen","netip" => "checkipv4","maskip" => "checkipv4", "hostdns1" => "checkipv4", "hostdns2" => "checkipv4", "hostgw" => "checkipv4","rootpasswd" => "stringlen", "rerootpasswd" => "checkstrequal", "bootsize" => "numsize"); 
 			$check_paras = array("hostmac" => "", "hostname" => "3,15","netip" => "", "maskip" => "", "hostdns1" => "", "hostdns2" => "", "hostgw" => "", "rootpasswd" => "6,15", "rerootpasswd" => "rerootpasswd", "bootsize" => "100,200"); 
 			$message = array("hostmac" => "服务器被用于安装操作系统的网卡所对应的MAC地址必须输入?","hostname" => "主机名必须是3冿15个字窿", "netip" => "懿输入的IP地址必须合法", "maskip" => "懿输入的掩码地囿必须合法", "hostdns1" => "懿输入的DNSIP地址必须合法", "hostdns2" => "懿输入的DNSIP地址必须合法", "hostgw" => "懿输入的网关IP地址必须合法", "rootpasswd" => "管理员密码必须是6冿15个数字和字母组合", "rerootpasswd" => "两次输入的密码不?腿", "bootsize" => "/boot分区大小?100冿200MB");
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
 			//$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
 			$result =  DBselect($query);
 			$result_array=DBfetch($result);
 			$pxecfgpath = $result_array["pxecfgpath"];
 			$pxeksuri = $result_array["pxeksuri"];
 			$pxecfgtemplate = $result_array["pxecfgtemplate"];
 			$cfgcontent="";
 			//$cfgcontent="default ".trim($_REQUEST["ostver"])."\n";
 			$macro_array["{KSURL}"] = $pxeksuri."ks/".trim($_REQUEST["ostver"])."/".strtolower(str_replace(':',"-",trim($_REQUEST["hostmac"])));
 			$macro_array["{OSVER}"] = trim($_REQUEST["ostver"]);
 			if(!$fp=@fopen($pxecfgpath.$pxecfgtemplate,"r")){
 				echo "打开指定的PXE启动模板文件出错，请确认系统懿配置的路径是否正确，并且系统具有读取权限";
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
 				echo "创建PXE启动配置文件失败，请确认系统懿配置的目录是否存在，且系统具写权限！";
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
 			$query = "select pxehostid, count(pxehostid) as hostnum  from pxehost where pxenetname='".trim($_REQUEST["netname"])."' and pxeinstallmac =  '".$hostmac."' and pxehoststatus=0";
 			//echo $query;
 			//exit;
 			//$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN);
 			$result = DBselect($query);
 		  //	$result_array=mysql_fetch_array($result);
 			$result_array =  DBfetch($result);
 			if($result_array["hostnum"] > 0 ){
 				$pxehostid = $result_array["pxehostid"];
 				$query = "update pxehost set  pxeosver='".$ostver."', pxehostname='".$hostname."', pxehosttype=".$hosttype.", pxedns1='".$hostdns1."', pxedns2='".$hostdns2."'";
 				$query .= ",pxegateway='".$hostgw."',pxerootpasswd='".$rootpasswd."'  where pxehostid=".$pxehostid;
 			}
 			else{
 				$query  = "insert into pxehost(pxenetname,pxeosver,pxehostname,pxehosttype,pxedns1,pxedns2,pxegateway,pxeinstallmac,pxerootpasswd) values ('";
 				$query .= $netname."','".$ostver."','".$hostname."',".$hosttype.",'".$hostdns1."','".$hostdns2."','".$hostgw."','".$hostmac."','".$rootpasswd."')";
 			}
 		//	if(!$result=mysql_db_query($DB["DATABASE"],$query,$DB_CONN)){
 		  if(!$result=DBselect($query)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			if(!@isset($pxehostid) || trim($pxehostid) == ""){
 				$query = "select  max(pxehostid) as hostnum from pxehost ";
 				if(!$result=DBselect($query)){
 				   echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				   exit;
 				}
 				$result_array=DBfetch($result);
 				$pxehostid = $result_array["hostnum"];
 			}
 			$query  = "delete from pxenics where pxehostid=".$pxehostid;
 			if(!$result=DBexecute($query)){
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
 			if(!$result=DBexecute($query)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query  = "delete from pxedisk where pxehostid=".$pxehostid;
 			if(!$result=DBexecute($query)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query = "insert into pxedisk(pxehostid,pxedisktype,pxediskfstype,pxediskmount,pxedisksize) values (";
 			$query .= $pxehostid.",0,'ext4','/boot',".$bootsize."),";
 			$query .= " (".$pxehostid.",1,'ext4','swap',".$swapsize."),";
 			$query .= " (".$pxehostid.",0,'ext4','/',".$rootsize.")";
 			if(!$result=DBexecute($query)){
 				  echo "查询数据库出错，错误信息为：".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			$query  = "delete from pxehostcommand where pxehostid=".$pxehostid;
 			if(!$result=DBexecute($query)){
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
 			if(!$result=DBexecute($query)){
 				  echo "查询数据库出错，错误信息?22222?".mysql_error()."<br>查询语句为：".$query."<br>";
 				  exit;
 			}
 			echo "现在请重启所要安装操作系统的机器，并确保0网卡是连接到网络上了<br>";
 			//echo $cfgcontent;
 			DBend();
 			exit;
 			
	}
	if(strtolower(trim($_REQUEST["action"])) === "ks"){    //KS信息处理
		  $osver=trim($_REQUEST["osver"]);
		  $hostmac=trim($_REQUEST["hostmac"]);
		  $query = "select pxehostid, pxenetname,pxeosver,pxehostname,pxedns1,pxedns2,pxegateway, pxerootpasswd from pxehost where pxeinstallmac='".$hostmac."' and pxehoststatus=0";
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
 			$result_array=DBfetch($result);
 			$pxehostid = $result_array["pxehostid"];
 			$pxenetname = $result_array["pxenetname"];
 			$pxehostname = $result_array["pxehostname"];
 			$pxerootpasswd  = $result_array["pxerootpasswd"];
 			$MACRO_ARRAY["{OSVER}"] = $osver;
 			$MACRO_ARRAY["{INSTALLMAC}"] =strtolower(str_replace(':',"-",trim($hostmac)));
 			$MACRO_ARRAY["{HOSTID}"] = $pxehostid;
 			$MACRO_ARRAY["{HOSTNAME}"] = $pxehostname;
 			$MACRO_ARRAY["{INSTALLPASSWD}"] = $pxerootpasswd;
 			$MACRO_ARRAY["{DNS1}"] = $result_array["pxedns1"];
 			$MACRO_ARRAY["{DNS2}"] = $result_array["pxedns2"];
 			$MACRO_ARRAY["{GATEWAY}"] = $result_array["pxegateway"]; 			
 			$query = "select pxeoshomeuri,pxekstemplatepath,pxeksuri,pxecfgpath from pxeconfig where pxenetname='".$pxenetname."' and pxestatus=0";
 			if(!$result=DBselect($query)){
 				echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
			}
		  $result_array=DBfetch($result);
		  $pxeoshomeuri = $result_array["pxeoshomeuri"];
		  $pxeksuri = $result_array["pxeksuri"];
		  $MACRO_ARRAY["{OSHOMEURL}"] = $pxeoshomeuri;
		  $MACRO_ARRAY["{KSURL}"] = $result_array["pxeksuri"];
		  $pxekstemplatepath = $result_array["pxekstemplatepath"];
		  $MACRO_ARRAY["{CFGPATH}"] = $result_array["pxecfgpath"];
		  $query =  "select pxeosuri, pxecommandsuri,pxewgetcommand,pxeinstallscriptpath,pxechmodcommand,pxermcommand from pxeos where pxenetname='".$pxenetname."' and pxeosver='".$osver."' and pxestatus=0";
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $result_array=DBfetch($result);
		  $pxeosuri = $result_array["pxeosuri"];
		  $pxecommandsuri = $result_array["pxecommandsuri"];
		  $pxewgetcommand = $result_array["pxewgetcommand"];
		  $pxeinstallscriptpath = $result_array["pxeinstallscriptpath"];
		  $pxechmodcommand = $result_array["pxechmodcommand"];
		  $pxermcommand = $result_array["pxermcommand"];
		  $query = "select pxedisktype,pxediskfstype,pxediskmount,pxedisksize from pxedisk where pxehostid=".$pxehostid;
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $i = 0;
		  $diskcontent="";
		 	while($result_array=DBfetch($result)){
		 		if(($result_array["pxediskfstype"] == 1) && $result_array["pxedisksize"] != 0){
		 				$diskcontent .="part swap --size=".$result_array["pxedisksize"]."\n";
		 		}
		 		else{
		 				if($result_array["pxedisksize"] == 0)
		 					$diskcontent .= "part  ".$result_array["pxediskmount"]."  --fstype=".$result_array["pxediskfstype"]."  --size=1 --grow\n";
		 				else
		 					$diskcontent .= "part  ".$result_array["pxediskmount"]."  --fstype=".$result_array["pxediskfstype"]."  --size=".$result_array["pxedisksize"]."\n";
		 		}
		 				
				$disk[$i]["pxedisktype"] = $result_array["pxedisktype"];
				$disk[$i]["pxediskfstype"] = $result_array["pxediskfstype"];
				$disk[$i]["pxediskmount"] = $result_array["pxediskmount"];
				$disk[$i]["pxedisksize"] = $result_array["pxedisksize"];
				$i++;
			}
			$filename = $pxekstemplatepath."/ks_template_".$osver.".tpl";
			if(!$fp=@fopen($filename,"r")){
				echo "open file error";
 				exit;
 			}
 			$MACRO_ARRAY["{OSIMGURL}"] = $pxeoshomeuri.$pxeosuri;
 			$MACRO_ARRAY["{INSTALLPASSWD}"] = $pxerootpasswd;
 			$MACRO_ARRAY["{DISKPARTS}"] = $diskcontent;
 			$query ="select pxenicno,pxenicip,pxenicmask,pxenicenbled from pxenics where pxehostid=".$pxehostid." and pxenicstatus=0 order by pxenicno";
 			if(!$result=DBselect($query)){
		  	 echo $query."<br>";
		  	 echo mysql_error()."<br>";
		  	 exit;
		  }
 			for($i=0;$i<4;$i++){
 				if(($result_array=DBfetch($result))){
 					 $MACRO_ARRAY["{IP".($i+1)."}"] = $result_array["pxenicip"];
 					 $MACRO_ARRAY["{MASK".($i+1)."}"] = $result_array["pxenicmask"];
 					 $MACRO_ARRAY["{EANBLED".($i+1)."}"] = $result_array["pxenicenbled"];
 				}
 				else{
 					 $MACRO_ARRAY["{IP".($i+1)."}"] = "";
 					 $MACRO_ARRAY["{MASK".($i+1)."}"] = "";
 					 $MACRO_ARRAY["{EANBLED".($i+1)."}"] = 1;
 				}
 			}
 			
 			$query = "select c.pxecommandtype,c.pxecommand,c.pxecommandstats from pxecommand c,pxehostcommand hc where c.pxecommandstatus=0 and c.pxecommandid=hc.pxecommandid and hc.pxehostid=".$pxehostid." and c.pxecommandstats=0 and c.pxecommandstatus=0 order by pxecommandexcuno ";
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $i=0;
		  $precommands="";
		  $command_execute_array = "";
		  $MACRO_ARRAY["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$INSTALL_SCRIPT;
		  while($result_array=DBfetch($result)){
		  	//if($i == 0)
		  		//$precommands="%pre\n";
		  	if($result_array["pxecommandtype"] == 0){
		  		$precommands .= str_replace_array($result_array["pxecommand"],$MACRO_ARRAY)."\n";
		  	}
		  	else{
		    	$command_array = split("/",$result_array["pxecommand"]);
		    	$command_tpl = 	$command_array[0];
		    	$command_para = $command_array[1];
		    	$MACRO_ARRAY["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"];
		    	$command_para = str_replace_array($command_para,$MACRO_ARRAY);
		    	$command_para = str_replace("||"," ",$command_para);
		    	$MACRO_ARRAY["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/".$command_tpl."/".$pxehostid;
		    	$precommands .=  str_replace_array($pxewgetcommand,$MACRO_ARRAY)."\n";
		    	if(trim($pxechmodcommand) !==""){
		    			$precommands .= str_replace_array($pxechmodcommand,$MACRO_ARRAY)."\n";
		    	}
		    	//$precommands .= $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"]." ".$command_para."\n";
		    	//$precommands .= str_replace_array($pxermcommand,$MACRO_ARRAY)."\n";
		    	$command_execute_array[$i] = $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"]." ".$command_para."\n";
		    	 $i++;
		    }
		    $precommands .= "\n";
		   
		  }
		   foreach($command_execute_array as $key => $value ){
		  	  $precommands .= $value;
		  }
 			//if($i > 0 ){
 				  $precommands .= "\n\n";
 				//$precommands .= "%end\n\n";
 			//}
 			$MACRO_ARRAY["{PRE_SCRIPT}"] = $precommands;
 			
 			$query = "select c.pxecommandtype,c.pxecommand,c.pxecommandstats from pxecommand c,pxehostcommand hc where c.pxecommandstatus=0 and c.pxecommandid=hc.pxecommandid and hc.pxehostid=".$pxehostid." and c.pxecommandstats=1 and c.pxecommandstatus=0 order by pxecommandexcuno ";
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		 // $postcommands="%post\n";
		  $postcommands="";
		  $i=0;
		  $command_execute_array = "";
		  $MACRO_ARRAY["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$INSTALL_SCRIPT;
		  while($result_array=DBfetch($result)){
		  	if($result_array["pxecommandtype"] == 0){
		  		$postcommands .= str_replace_array($result_array["pxecommand"],$MACRO_ARRAY)."\n";
		  	}
		  	else{
		    	$command_array = split("/",$result_array["pxecommand"]);
		    	$command_tpl = 	$command_array[0];
		    	$command_para = $command_array[1];
		    	$MACRO_ARRAY["{INSTALL_SCRIPT}"] = $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"];
		    	$command_para = str_replace_array($command_para,$MACRO_ARRAY);
		    	$command_para = str_replace("||"," ",$command_para);
		    	$MACRO_ARRAY["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/".$command_tpl."/".$pxehostid;
		    	$postcommands .=  str_replace_array($pxewgetcommand,$MACRO_ARRAY)."\n";
		    	if(trim($pxechmodcommand) !==""){
		    			$postcommands .= str_replace_array($pxechmodcommand,$MACRO_ARRAY)."\n";
		    	}
		    	//$postcommands .= $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"]." ".$command_para."\n";
		    	//$postcommands .= str_replace_array($pxermcommand,$MACRO_ARRAY)."\n";
					$command_execute_array[$i] = $pxeinstallscriptpath.$command_tpl.$MACRO_ARRAY["{DATATIME}"]." ".$command_para."\n";
					$i++;
		    }
		    $postcommands .= "\n";
		  }
		  foreach($command_execute_array as $key => $value ){
		  	  $postcommands .= $value;
		  }
		 /* $MACRO_ARRAY["{SCRIPT_FILE_URL}"] = $pxeksuri."commands/".$osver."/finshed_install/".$pxehostid;
		  $postcommands .=  str_replace_array($pxewgetcommand,$MACRO_ARRAY)."\n";
		  $postcommands .= str_replace_array($pxermcommand,$MACRO_ARRAY)."\n";
		 */ 
		  $postcommands .= "\n\n";
		  //$postcommands .= "%end\n\n";
		  $MACRO_ARRAY["{POST_SCRIPT}"] = $postcommands;
 			$ks_content="";
 			while (($line_str = fgets($fp, 4096)) !== false) {
 				if(($line_str = str_replace_array($line_str,$MACRO_ARRAY)) !== false)
 					$ks_content .= $line_str;
 				else
 					 exit;
 		  }
 		  fclose($fp);
 		  $query = "update pxehost set pxeosinstall='".@date("YmdHis",time())."', pxehoststatus=0 where  pxehostid=".$MACRO_ARRAY["{HOSTID}"];
		  if(!$result=DBexecute($query)){
		  			echo $query."<br>";
		  			echo mysql_error()."<br>";
		  			exit;
		  }
		   if(!@unlink($MACRO_ARRAY["{CFGPATH}"]."01-".$MACRO_ARRAY["{INSTALLMAC}"])){
		  	 		echo "删除PXE配置文件出错，请确认系统是否具有相应目录和文件的删除权限!";
		  	 		exit;
		   }
 		  echo $ks_content;
 		  exit;
	}
	if(strtolower(trim($_REQUEST["action"])) === "commands"){     //处理命令请求
			$command_type =strtolower(trim($_REQUEST["template"]));
			$hostid = strtolower(trim($_REQUEST["hostid"]));
			$osver = strtolower(trim($_REQUEST["osver"]));
			/*
			if($command_type === "finshed_install"){
				 $query = "select pxenetname,pxeinstallmac from pxehost where pxehostid=".$hostid;
				 if(!$result=DBselect($query)){
		  			echo $query."<br>";
		  			echo mysql_error()."<br>";
		  			exit;
		  	 }
		  	 $result_array = DBfetch($result);
		  	 $pxenetname = $result_array["pxenetname"];
		  	 $pxeinstallmac = $result_array["pxeinstallmac"];
		  	 $query = "select pxecfgpath from pxeconfig where pxenetname='".$pxenetname."' and pxestatus=0";
		  	  if(!$result=DBselect($query)){
		  			echo $query."<br>";
		  			echo mysql_error()."<br>";
		  			exit;
		  	 }
		  	 $result_array = DBfetch($result);
		  	 $pxecfgpath = $result_array["pxecfgpath"];
		  	 $pxecfgfile = $pxecfgpath."01-".strtolower(str_replace(':',"-",trim($pxeinstallmac)));
		  	 if(!@unlink($pxecfgfile)){
		  	 		echo "删除PXE配置文件出错，请确认系统是否具有相应目录和文件的删除权限!";
		  	 		exit;
		  	 }
		  	 $query = "update pxehost set pxeosinstall='".@date("YmdHis",time())."', pxehoststatus=0 where  pxehostid=".$hostid;
		  	 if(!$result=DBexecute($query)){
		  			echo $query."<br>";
		  			echo mysql_error()."<br>";
		  			exit;
		  	 }
		  	 exit;
			}
	    
	    */
	    
	   	//处理基于模板的命乿
			$query = "select pxenetname,pxehostname,pxedns1,pxedns2,pxegateway,pxeinstallmac,pxerootpasswd from pxehost where pxehoststatus=0 and pxehostid=".$hostid;
		  if(!$result=DBselect($query)){
		  			echo $query."<br>";
		  			echo mysql_error()."<br>";
		  			exit;
		  }
		  
		  $result_array = DBfetch($result);
		  $MACRO_ARRAY["{DATATIME}"] = @date("YmdHis",time());
			$MACRO_ARRAY["{NETNAME}"] = $result_array["pxenetname"];
			$MACRO_ARRAY["{HOSTNAME}"] = $result_array["pxehostname"];
			$MACRO_ARRAY["{DNS1}"] = $result_array["pxedns1"];
			$MACRO_ARRAY["{DNS2}"] = $result_array["pxedns2"];
			$MACRO_ARRAY["{GATEWAY}"] = $result_array["pxegateway"];
			$MACRO_ARRAY["{OSVER}"] = $osver;
 			$MACRO_ARRAY["{INSTALLMAC}"] =strtolower(str_replace(':',"-",trim($result_array["pxeinstallmac"])));
 			$MACRO_ARRAY["{HOSTID}"] = $hostid;
 			$MACRO_ARRAY["{INSTALLPASSWD}"] = $result_array["pxerootpasswd"];
			$query = "select pxeoshomeuri,pxekstemplatepath,pxeksuri,pxecommandtemplatepath from pxeconfig where pxenetname='".$MACRO_ARRAY["{NETNAME}"]."' and pxestatus=0";
 			if(!$result=DBselect($query)){
 				echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
			}
			$result_array = DBfetch($result);
			$MACRO_ARRAY["{OSHOMEURL}"] = $result_array["pxeoshomeuri"];
		  $MACRO_ARRAY["{KSURL}"] = $result_array["pxeksuri"];
		  $pxecommandtemplatepath = $result_array["pxecommandtemplatepath"];
		  $query =  "select pxeostype, pxeosuri, pxecommandsuri,pxeinstallscriptpath from pxeos where pxenetname='".$MACRO_ARRAY["{NETNAME}"]."' and pxeosver='".$MACRO_ARRAY["{OSVER}"]."' and pxestatus=0";
		  if(!$result=DBselect($query)){
		  	echo $query."<br>";
		  	echo mysql_error()."<br>";
		  	exit;
		  }
		  $result_array=DBfetch($result);
		  $MACRO_ARRAY["{OSTYPE}"] = $result_array["pxeostype"];
		  $MACRO_ARRAY["{OSIMGURL}"] = $MACRO_ARRAY["{OSHOMEURL}"].$result_array["pxeosuri"];
		  $MACRO_ARRAY["{INSTALL_SCRIPT}"] = $result_array["pxeinstallscriptpath"].$INSTALL_SCRIPT;
		  $query ="select pxenicno,pxenicip,pxenicmask,pxenicenbled from pxenics where pxehostid=".$MACRO_ARRAY["{HOSTID}"]." and pxenicstatus=0 order by pxenicno";
 			if(!$result=DBselect($query)){
		  	 echo $query."<br>";
		  	 echo mysql_error()."<br>";
		  	 exit;
		  }
		  $MACRO_ARRAY["{BINDGATEWAYNICNO}"] = -1;
 			for($i=0;$i<4;$i++){
 				if(($result_array=DBfetch($result))){
 					 $MACRO_ARRAY["{IP".($i+1)."}"] = $result_array["pxenicip"];
 					 $MACRO_ARRAY["{MASK".($i+1)."}"] = $result_array["pxenicmask"];
 					 $MACRO_ARRAY["{EANBLED".($i+1)."}"] = $result_array["pxenicenbled"];
 					 if((calc_net_address($result_array["pxenicip"],$result_array["pxenicmask"]) == calc_net_address($MACRO_ARRAY["{GATEWAY}"],$result_array["pxenicmask"])) && ($result_array["pxenicenbled"] == 0)){
 					 		$MACRO_ARRAY["{BINDGATEWAYNICNO}"] = $i;
 					 }
 				}
 				else{
 					 $MACRO_ARRAY["{IP".($i+1)."}"] = "";
 					 $MACRO_ARRAY["{MASK".($i+1)."}"] = "";
 					 $MACRO_ARRAY["{EANBLED".($i+1)."}"] = 1;
 				}
 			}
		  $commandtplfile = $pxecommandtemplatepath.$MACRO_ARRAY["{OSVER}"]."/".$command_type.".tpl";
		  $scriptcontent = "";
		//  echo $commandtplfile."<br>";
		  if(!$fp=fopen($commandtplfile,"r")){
				echo "打开命令模板文件错误，请确认命令模板文件存在，且系统具有读取该文件的权限";
 				exit;
 			}
			while (($line_str = fgets($fp, 4096)) !== false) {
 				if(($line_str = str_replace_array($line_str,$MACRO_ARRAY)) !== false)
 					$scriptcontent .= $line_str;
 				else
 					 exit;
 		  }
 		  fclose($fp);
			echo $scriptcontent;
			exit;
  }
   echo "aaaaa";
   	DBend();
	exit;
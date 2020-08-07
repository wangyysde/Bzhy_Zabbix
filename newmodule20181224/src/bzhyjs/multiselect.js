var subopts = new Array();
subopts['main1'] = new Array();
subopts['main1'][0] = "00000";
subopts['main1'][1] = "11111";
subopts['main1'][2] = "22222";

subopts['main2'] = new Array();
subopts['main2'][0] = "AAAAA";
subopts['main2'][1] = "BBBBB";
subopts['main2'][2] = "CCCCC";

subopts['main3'] = new Array();
subopts['main3'][0] = "aaaaaa";
subopts['main3'][1] = "bbbbbb";
subopts['main3'][2] = "cccccc";

function chgSubSel(pVar,subVar,SubOpts,url,urlParas){
    var pObj,subObj,selectedKey,Opts,i,akey,optValue;	
    
    pObj = null;
    if(typeof(pVar) == 'object'){
	pObj = pVar;
    }
    if(typeof(pVar) == 'string'){
	pObj = document.getElementById(pVar);
    }
    if(pObj == null){
	return false;
    }
		
    if(typeof(subVar) == 'object'){
	subObj = subVar;
    }
    if(typeof(subVar) == 'string'){
        subObj = document.getElementById(subVar);
    }
    if(subObj == null){
        return false;
    }
        
    selectedKey = pObj.selectedIndex;	
    if(selectedKey == null){
	return false;
    }		
    
    Opts = null;
    if((SubOpts != null) && (typeof SubOpts == 'object') && ('splice' in SubOpts) && ('join' in SubOpts)){
        i = 0;
	for (akey in SubOpts){
            if(i ==  selectedKey){
		Opts = SubOpts[akey];
            }
            i++;
	}
    }
    if(Opts == null){
        if((url == null) || (typeof url != 'string')){
            return false;
	}
	//optValue = getSelValue(pObj,selectedKey);
        optValue = pObj.value;
	if(optValue == null){
            return false;
	}
	if(typeof urlParas == 'string'){
            urlParas = urlParas + '&ParenKey=' + optValue;
	}
	if((urlParas != null) && (typeof urlParas == 'object') && ('splice' in urlParas) && ('join' in urlParas)){
            urlParas['ParenKey'] = optValue;
	}
	if(urlParas == null){
            urlParas = new Array();
            urlParas['ParenKey'] = optValue;
	}
        			
	if((typeof urlParas != 'string') && urlParas['ParenKey'] == null){
            return false;
	}
        jq331($(subObj)).load(url,urlParas,function(responseTxt,statusTxt,xhr){
            if(statusTxt == "success")
                return true;;
            if(statusTxt == "error"){
                alert("Maybe Network or server error,we can not got data");
                return false;
            }
        });
    }
    else{
	if(chgOptions(subObj,Opts) == false){
            return false;
	}
	else{
            return true;
	}
    }
    		
}	

function chgOptions(subSel,SubOpts){
    if((SubOpts != null) && (typeof SubOpts == 'object') && ('splice' in SubOpts) && ('join' in SubOpts)){
        subSel.options.length = 0;
        for (key in SubOpts){
            var op = new Option(SubOpts[key],key);
            subSel.options.add(op);
        }
    }
    else{
    	return false;
    }
    
   return true;
}

function getSelValue(obj,SelId){
	var i,OptValue,OptKey,Options;
	
        return obj.value;
	OptValue = null;
	Options = obj.options;
	i = 0;
	for (OptKey in Options){
		if(i == SelId){
			OptValue = Options[i].value;
		}
		i++;
	}
	
	return OptValue;
}
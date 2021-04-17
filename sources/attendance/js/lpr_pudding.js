function gData(getPar){			
	tSearch=document.getElementById("tSearch").value;	
	pSearch=document.getElementById("pSearch").value;	
	fSearch=document.getElementById("fSearch").value;	
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){
				document.getElementById("tCount").innerHTML = response;
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&tSearch=" + tSearch + "&pSearch=" + pSearch + "&fSearch=" + fSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
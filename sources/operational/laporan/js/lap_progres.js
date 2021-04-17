function gData(getPar){	
	mSearch=document.getElementById("mSearch").value;		
	tSearch=document.getElementById("tSearch").value;		
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){				
				var dta = response.split("\t");
				document.getElementById("tPelatihan").innerHTML = dta[0];				
				document.getElementById("tPeserta").innerHTML = dta[1];				
				document.getElementById("tHadir").innerHTML = dta[2];				
				document.getElementById("tTidak").innerHTML = dta[3];				
				document.getElementById("tWaktu").innerHTML = dta[4];				
				document.getElementById("aPelatihan").innerHTML = dta[5];
				document.getElementById("aPresent").innerHTML = dta[6];		
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&mSearch=" + mSearch + "&tSearch=" + tSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
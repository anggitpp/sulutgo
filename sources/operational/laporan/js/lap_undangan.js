function gData(getPar){	
	pSearch=document.getElementById("pSearch").value;	
	mSearch=document.getElementById("mSearch").value;	
	tSearch=document.getElementById("tSearch").value;	
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){				
				var dta = response.split("\t");
				document.getElementById("tPelatihan").innerHTML = dta[0] == "0 Jam" ? "" : dta[0];
				document.getElementById("tPelatih").innerHTML = dta[0] == "0 Jam" ? "" : dta[1];
				document.getElementById("tJumlah").innerHTML = dta[0] == "0 Jam" ? "" : dta[2];				
				document.getElementById("tHadir").innerHTML = dta[0] == "0 Jam" ? "" : dta[3];				
				document.getElementById("pAbsen").innerHTML = dta[0] == "0 Jam" ? "" : dta[4];				
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&pSearch=" + pSearch + "&mSearch=" + mSearch + "&tSearch=" + tSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
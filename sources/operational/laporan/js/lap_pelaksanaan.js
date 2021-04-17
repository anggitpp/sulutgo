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
				document.getElementById("tPelatihan").innerHTML = response == "0 Jam" ? "" : dta[0] + " Kali";
				document.getElementById("tPertemuan").innerHTML = response == "0 Jam" ? "" : dta[1];
				document.getElementById("tPeserta").innerHTML = response == "0 Jam" ? "" : dta[2] + " Orang";
				
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&pSearch=" + pSearch + "&mSearch=" + mSearch + "&tSearch=" + tSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
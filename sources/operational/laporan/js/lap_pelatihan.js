function gData(getPar){	
	pSearch=document.getElementById("pSearch").value;		
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){				
				var dta = response.split("\t");
				document.getElementById("tanggalPelatihan").innerHTML = dta[0];				
				document.getElementById("namaTrainer").innerHTML = dta[1];				
				document.getElementById("lokasiPelatihan").innerHTML = dta[2];				
				document.getElementById("tHadir").innerHTML = dta[3];				
				document.getElementById("tAbsen").innerHTML = dta[4];				
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&pSearch=" + pSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
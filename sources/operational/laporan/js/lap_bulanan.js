function gData(getPar){		
	mSearch=document.getElementById("mSearch").value;	
	tSearch=document.getElementById("tSearch").value;	
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				//alert(response);
				
				var dta = response.split("\t");
				document.getElementById("tParticipant").innerHTML = dta[0];
				document.getElementById("tPresent").innerHTML = dta[1];	
				document.getElementById("tAbsen").innerHTML = dta[2];
				
				var pPresent = dta[0] > 0 ? (dta[1] / dta[0] * 100).toFixed(2) : 0;
				var pAbsen = dta[0] > 0 ? (dta[2] / dta[0] * 100).toFixed(2) : 0;
				
				document.getElementById("pParticipant").innerHTML = "100%";
				document.getElementById("pPresent").innerHTML = pPresent + "%";	
				document.getElementById("pAbsen").innerHTML = pAbsen + "%";
				
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&mSearch=" + mSearch + "&tSearch=" + tSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
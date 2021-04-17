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
				document.getElementById("tPelatihan").innerHTML = dta[0] + " Kali";
				document.getElementById("tPeserta").innerHTML = dta[1] + " Orang";	
				document.getElementById("tHadir").innerHTML = dta[2];	
				document.getElementById("pHadir").innerHTML = dta[3];	
				document.getElementById("tAbsen").innerHTML = dta[4];	
				document.getElementById("pAbsen").innerHTML = dta[5];	
				document.getElementById("tWaktu").innerHTML = dta[6];
				document.getElementById("aWaktu").innerHTML = dta[7];			
			}else{
				document.getElementById("tPelatihan").innerHTML = "";
				document.getElementById("tPeserta").innerHTML = "";	
				document.getElementById("tHadir").innerHTML = "";	
				document.getElementById("pHadir").innerHTML = "";	
				document.getElementById("tAbsen").innerHTML = "";	
				document.getElementById("pAbsen").innerHTML = "";	
				document.getElementById("tWaktu").innerHTML = "";
				document.getElementById("aWaktu").innerHTML = "";	
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&mSearch=" + mSearch + "&tSearch=" + tSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}
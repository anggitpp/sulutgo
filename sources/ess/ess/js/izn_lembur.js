function getNomor(getPar){
	tanggalLembur=document.getElementById("inp[tanggalLembur]");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorLembur]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalLembur=" + tanggalLembur.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
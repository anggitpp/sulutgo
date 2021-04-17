function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);	
	closeBox();
}

function getPegawai(getPar){
	nikPegawai = document.getElementById("inp[nikPegawai]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("inp[idPegawai]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaPegawai]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}
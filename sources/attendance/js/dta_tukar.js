function getNomor(getPar){
	tanggalShift=document.getElementById("tanggalShift");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorShift]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalShift=" + tanggalShift.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);	
	closeBox();
}

function setPengganti(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPengganti]").value = nikPegawai;	
	parent.getPengganti(getPar);	
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
				document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];
				
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getPengganti(getPar){
	nikPengganti = document.getElementById("inp[nikPengganti]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("inp[idPengganti]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikPengganti]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaPengganti]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				document.getElementById("inp[_namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[_namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];
				
				if(data["idPegawai"] == null && nikPengganti.length > 0)
				alert("maaf, nik : \""+ nikPengganti + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPengganti + getPar, true);
	xmlHttp.send(null);
	return false;
}
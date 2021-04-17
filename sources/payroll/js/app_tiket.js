function save(getPar){	
	if(validation(document.form)){
		document.getElementById("form").submit();
		return true;
	}
	return false;
}

function setPulang(getPar){
	idTipe=document.getElementById("inp[idTipe]");	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				document.getElementById("pulangTiket").style.display = response;
				if(response == "none"){
					document.getElementById("txtPulang").innerHTML = "&nbsp;";
					document.getElementById("pulangTiket_tanggal").value = "";
					document.getElementById("pulangTiket_waktu").value = "";
					document.getElementById("inp[pulangTiket_asal]").value = "";
					document.getElementById("inp[pulangTiket_tujuan]").value = "";
				}else{
					document.getElementById("txtPulang").innerHTML = "PULANG";
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=tpe&par[idTipe]=" + idTipe.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getNomor(getPar){
	tanggalTiket=document.getElementById("tanggalTiket");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorTiket]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalTiket=" + tanggalTiket.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

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
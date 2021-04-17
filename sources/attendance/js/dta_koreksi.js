function getNomor(getPar){
	tanggalKoreksi=document.getElementById("tanggalKoreksi");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorKoreksi]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalKoreksi=" + tanggalKoreksi.value + getPar, true);
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
				getAbsen(getPar);
				
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getAbsen(getPar){
	idPegawai = document.getElementById("inp[idPegawai]");
	nikPegawai = document.getElementById("inp[nikPegawai]");
	mulaiKoreksi_tanggal = document.getElementById("mulaiKoreksi_tanggal");
	
	if(idPegawai.value.length < 1){		
		alert("anda harus mengisi nik");	
		return false;
	}
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){
			response = xmlHttp.responseText.trim();
			if(response){
				var data = JSON.parse(response);
				
				document.getElementById("mulaiKoreksi").value = data["masukAbsen"] == false ? "" : data["masukAbsen"];
				document.getElementById("selesaiKoreksi").value = data["pulangAbsen"] == false ? "" : data["pulangAbsen"];
				document.getElementById("masukKoreksi").value = data["masukAbsen"] == false ? "" : data["masukAbsen"];
				document.getElementById("pulangKoreksi").value = data["pulangAbsen"] == false ? "" : data["pulangAbsen"];
				
				if(data["idPegawai"] == undefined && mulaiKoreksi_tanggal.value.length > 0)
				alert("data absen tanggal " + mulaiKoreksi_tanggal.value + " untuk nik : " + nikPegawai.value + " tidak ada");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=abs&par[idPegawai]=" + idPegawai.value + "&par[tanggalAbsen]=" + mulaiKoreksi_tanggal.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function cekMasuk(){
	masukKoreksi_jam = document.getElementById("inp[masukKoreksi_jam]");
	if(masukKoreksi_jam.checked == false)
	document.getElementById("masukKoreksi").value = document.getElementById("mulaiKoreksi").value
}

function cekPulang(){
	pulangKoreksi_jam = document.getElementById("inp[pulangKoreksi_jam]");
	if(pulangKoreksi_jam.checked == false)
	document.getElementById("pulangKoreksi").value = document.getElementById("selesaiKoreksi").value
}
function getNomor(getPar){
	tanggalKoreksi=document.getElementById("inp[tanggalKoreksi]");
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

function getPegawai(getPar){
	idPegawai = document.getElementById("inp[idPegawai]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();	
			// alert(response);		
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("inp[namaLokasi]").value = data["location"] == undefined ? "" : data["location"];
				document.getElementById("inp[namaJabatan]").value = data["pos_name"] == undefined ? "" : data["pos_name"];
				document.getElementById("inp[namaDivisi]").value = data["div_id"] == undefined ? "" : data["div_id"];
				getAbsen(getPar);
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[idPegawai]=" + idPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getAbsen(getPar){
	idPegawai = document.getElementById("inp[idPegawai]");
	mulaiKoreksi_tanggal = document.getElementById("inp[mulaiKoreksi_tanggal]");
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){
			response = xmlHttp.responseText.trim();
			// alert(response);
			if(response){
				var data = JSON.parse(response);
				
				document.getElementById("mulaiKoreksi").value = data["masukAbsen"] == false ? "" : data["masukAbsen"];
				document.getElementById("selesaiKoreksi").value = data["pulangAbsen"] == false ? "" : data["pulangAbsen"];
				document.getElementById("masukKoreksi").value = data["masukAbsen"] == false ? "" : data["masukAbsen"];
				document.getElementById("pulangKoreksi").value = data["pulangAbsen"] == false ? "" : data["pulangAbsen"];
								
				if(data["idPegawai"] == undefined && mulaiKoreksi_tanggal.value.length > 0)
				alert("data absen tanggal " + mulaiKoreksi_tanggal.value + " tidak ada");
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
	document.getElementById("masukKoreksi").value = document.getElementById("inp[mulaiKoreksi]").value
}

function cekPulang(){
	pulangKoreksi_jam = document.getElementById("inp[pulangKoreksi_jam]");
	if(pulangKoreksi_jam.checked == false)
	document.getElementById("pulangKoreksi").value = document.getElementById("selesaiKoreksi").value
}
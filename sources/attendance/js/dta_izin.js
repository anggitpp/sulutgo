function getNomor(getPar){
	tanggalHadir=document.getElementById("tanggalHadir");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorHadir]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalHadir=" + tanggalHadir.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setHide(){
	mulaiHadir_tanggal = document.getElementById("mulaiHadir_tanggal");
	selesaiHadir_tanggal = document.getElementById("selesaiHadir_tanggal");
	mulaiHadir_waktu = document.getElementById("mulaiHadir_waktu");
	selesaiHadir_waktu = document.getElementById("selesaiHadir_waktu");
	
	allTrue = document.getElementById("true");
	allFalse = document.getElementById("false");
	
	jamMulai = document.getElementById("jamMulai");
	jamSelesai = document.getElementById("jamSelesai");
	allDay = document.getElementById("allDay");
	
	if(mulaiHadir_tanggal.value != selesaiHadir_tanggal.value){						
		jamMulai.style.display = "none";
		jamSelesai.style.display = "none";
		allDay.style.display = "none";
		
		mulaiHadir_waktu.value = "";
		selesaiHadir_waktu.value = "";				
	}else{
		if(allTrue.checked == true){
			jamMulai.style.display = "none";
			jamSelesai.style.display = "none";	
			
			mulaiHadir_waktu.value = "";
			selesaiHadir_waktu.value = "";		
		}else{
			jamMulai.style.display = "block";
			jamSelesai.style.display = "block";			
		}
		allDay.style.display = "block";
	}
}

function setTipe(getPar){
	idKategori=document.getElementById("inp[idKategori]");	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				document.getElementById("tipeIzin").style.display = response;
				if(response == "none") document.getElementById("inp[idTipe]").value = "";
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kat&par[idKategori]=" + idKategori.value + getPar, true);
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
								
				document.getElementById("inp[idPengganti]").value = data["idPengganti"] == undefined ? "" : data["idPengganti"];
				document.getElementById("inp[nikPengganti]").value = data["nikPengganti"] == undefined ? "" : data["nikPengganti"];
				document.getElementById("inp[namaPengganti]").value = data["namaPengganti"] == undefined ? "" : data["namaPengganti"];
				document.getElementById("inp[idAtasan]").value = data["idAtasan"] == undefined ? "" : data["idAtasan"];
				document.getElementById("inp[nikAtasan]").value = data["nikAtasan"] == undefined ? "" : data["nikAtasan"];
				document.getElementById("inp[namaAtasan]").value = data["namaAtasan"] == undefined ? "" : data["namaAtasan"];
				
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
				if(data["idPegawai"] == null && nikPengganti.length > 0)
				alert("maaf, nik : \""+ nikPengganti + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPengganti + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getAtasan(getPar){
	nikAtasan = document.getElementById("inp[nikAtasan]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				var data = JSON.parse(response);
				document.getElementById("inp[idAtasan]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikAtasan]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaAtasan]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				if(data["idPegawai"] == null && nikAtasan.length > 0)
				alert("maaf, nik : \""+ nikAtasan + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikAtasan + getPar, true);
	xmlHttp.send(null);
	return false;
}
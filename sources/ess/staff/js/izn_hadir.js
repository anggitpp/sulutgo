function getNomor(getPar){
	tanggalHadir=document.getElementById("inp[tanggalHadir]");
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
	mulaiHadir_tanggal = document.getElementById("inp[mulaiHadir_tanggal]");
	selesaiHadir_tanggal = document.getElementById("inp[selesaiHadir_tanggal]");
	mulaiHadir_waktu = document.getElementById("mulaiHadir_waktu");
	selesaiHadir_waktu = document.getElementById("selesaiHadir_waktu");
	
	allTrue = document.getElementById("inp[hariHadir]t");
	allFalse = document.getElementById("inp[hariHadir]f");
	// alert(allTrue.value);
	
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


function getPegawai(getPar) {
	idPegawai = document.getElementById("inp[idPegawai]").value;

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			response = xmlHttp.responseText.trim();
			if (response) {
				var data = JSON.parse(response);
				document.getElementById("inp[namaLokasi]").value = data["location"] == undefined ? "" : data["location"];
				document.getElementById("inp[namaJabatan]").value = data["pos_name"] == undefined ? "" : data["pos_name"];
				document.getElementById("inp[namaDivisi]").value = data["div_id"] == undefined ? "" : data["div_id"];
				document.getElementById("inp[idAtasan]").value = data["leader_id"] == undefined ? "" : data["leader_id"];
				document.getElementById("inp[idPengganti]").value = data["replacement_id"] == undefined ? "" : data["replacement_id"];
				jQuery("#inp\\[idPengganti\\]").trigger("chosen:updated");
				jQuery("#inp\\[idAtasan\\]").trigger("chosen:updated");				
			}
		}
	}

	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[idPegawai]=" + idPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getNomor(getPar){
	tanggalDuka=document.getElementById("tanggalDuka");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorDuka]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalDuka=" + tanggalDuka.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getJumlah(){		
	mulaiDuka=document.getElementById("mulaiDuka");
	selesaiDuka=document.getElementById("selesaiDuka");
	jatahDuka=document.getElementById("inp[jatahDuka]");
	sisaDuka=document.getElementById("inp[sisaDuka]");
	
	jumlahDuka = dateDiff(mulaiDuka.value, selesaiDuka.value, 'days');
	
	if(jumlahDuka * 1 > jatahDuka.value * 1){
		alert("Pengambilan cuti tidak boleh lebih dari jatah cuti");
		selesaiDuka.value = "";
	}
	
	if(jumlahDuka  < 1 || mulaiDuka.value.length < 1 || selesaiDuka.value.length < 1 || jumlahDuka * 1 > jatahDuka.value * 1){
		jumlahDuka = 0;
	}
	
	sisaDuka.value = jatahDuka.value - jumlahDuka;
	
	document.getElementById("inp[jumlahDuka]").value = jumlahDuka;
}

function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);
	closeBox();
}

function getPegawai(getPar){
	nikPegawai = document.getElementById("inp[nikPegawai]").value;
	tanggalDuka = document.getElementById("tanggalDuka").value;
	
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
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + "&par[tanggalDuka]=" + tanggalDuka + getPar, true);
	xmlHttp.send(null);
	return false;
}
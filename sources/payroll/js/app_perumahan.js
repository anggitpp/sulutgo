function getNomor(getPar){
	tanggalRumah=document.getElementById("tanggalRumah");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorRumah]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalRumah=" + tanggalRumah.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getJumlah(){		
	mulaiRumah=document.getElementById("mulaiRumah");
	selesaiRumah=document.getElementById("selesaiRumah");
	jatahRumah=document.getElementById("inp[jatahRumah]");
	sisaRumah=document.getElementById("inp[sisaRumah]");
	
	jumlahRumah = dateDiff(mulaiRumah.value, selesaiRumah.value, 'days');
	
	if(jumlahRumah * 1 > jatahRumah.value * 1){
		alert("Pengambilan cuti tidak boleh lebih dari jatah cuti");
		selesaiRumah.value = "";
	}
	
	if(jumlahRumah  < 1 || mulaiRumah.value.length < 1 || selesaiRumah.value.length < 1 || jumlahRumah * 1 > jatahRumah.value * 1){
		jumlahRumah = 0;
	}
	
	sisaRumah.value = jatahRumah.value - jumlahRumah;
	
	document.getElementById("inp[jumlahRumah]").value = jumlahRumah;
}

function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);
	closeBox();
}

function getPegawai(getPar){
	nikPegawai = document.getElementById("inp[nikPegawai]").value;
	tanggalRumah = document.getElementById("tanggalRumah").value;
	
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
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + "&par[tanggalRumah]=" + tanggalRumah + getPar, true);
	xmlHttp.send(null);
	return false;
}
function getNomor(getPar){
	tanggalCuti=document.getElementById("tanggalCuti");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorCuti]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalCuti=" + tanggalCuti.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getJumlah(){		
	mulaiCuti=document.getElementById("mulaiCuti");
	selesaiCuti=document.getElementById("selesaiCuti");
	jatahCuti=document.getElementById("inp[jatahCuti]");
	sisaCuti=document.getElementById("inp[sisaCuti]");
	
	jumlahCuti = dateDiff(mulaiCuti.value, selesaiCuti.value, 'days');
	
	if(jumlahCuti * 1 > jatahCuti.value * 1){
		alert("Pengambilan cuti tidak boleh lebih dari jatah cuti");
		selesaiCuti.value = "";
	}
	
	if(jumlahCuti  < 1 || mulaiCuti.value.length < 1 || selesaiCuti.value.length < 1 || jumlahCuti * 1 > jatahCuti.value * 1){
		jumlahCuti = 0;
	}
	
	sisaCuti.value = jatahCuti.value - jumlahCuti;
	
	document.getElementById("inp[jumlahCuti]").value = jumlahCuti;
}

function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);
	closeBox();
}

function getPegawai(getPar){
	nikPegawai = document.getElementById("inp[nikPegawai]").value;
	tanggalCuti = document.getElementById("tanggalCuti").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("inp[idPegawai]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaPegawai]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				document.getElementById("tanggalPegawai").value = data["tanggalPegawai"] == undefined ? "" : data["tanggalPegawai"];
				document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];				
				
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + "&par[tanggalCuti]=" + tanggalCuti + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setNilai(){	
	kaliCuti = document.getElementById("inp[kaliCuti]");
	jumlahCuti = document.getElementById("inp[jumlahCuti]");
	nilaiCuti = document.getElementById("inp[nilaiCuti]");
	
	nilaiCuti.value = convert(kaliCuti.value) * convert(jumlahCuti.value);
	
	kaliCuti.value = formatAngka(kaliCuti.value);
	jumlahCuti.value = formatAngka(jumlahCuti.value);
	nilaiCuti.value = formatAngka(nilaiCuti.value);
	
}
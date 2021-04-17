function getNomor(getPar){
	tanggalCuti=document.getElementById("tanggalCuti");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorCuti]").value = xmlHttp.responseText;
			}
			getJumlah(getPar);
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalCuti=" + tanggalCuti.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getJumlah(getPar){		
	nikPegawai = document.getElementById("inp[nikPegawai]");
	idTipe=document.getElementById("inp[idTipe]");
	tanggalCuti=document.getElementById("tanggalCuti");
	mulaiCuti=document.getElementById("mulaiCuti");
	selesaiCuti=document.getElementById("selesaiCuti");
	jatahCuti=document.getElementById("inp[jatahCuti]");
	jumlahCuti=document.getElementById("inp[jumlahCuti]");
	sisaCuti=document.getElementById("inp[sisaCuti]");

	// alert(mulaiCuti.value);
	// alert(selesaiCuti.value);
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			 var arr = xmlHttp.responseText.split("\t");
			 alert(response);
			if(response){
				jatahCuti.value = arr[0];
				pengambilanCuti = arr[1];
				
				if(pengambilanCuti * 1 > jatahCuti.value * 1){
					alert("Pengambilan cuti tidak boleh lebih dari jatah cuti");
					selesaiCuti.value = "";
				}
				
				if(pengambilanCuti  < 1 || mulaiCuti.value.length < 1 || selesaiCuti.value.length < 1 || pengambilanCuti * 1 > jatahCuti.value * 1) pengambilanCuti = 0;
				
				jumlahCuti.value = pengambilanCuti;
				sisaCuti.value = jatahCuti.value - pengambilanCuti;
				
			}else{
				jatahCuti.value = 0;
				jumlahCuti.value = 0;
				sisaCuti.value = 0;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=cut&idTipe=" + idTipe.value + "&tanggalCuti=" + tanggalCuti.value + "&nikPegawai=" + nikPegawai.value + 
	"&par[mulaiCuti]=" + mulaiCuti.value + "&par[selesaiCuti]=" + selesaiCuti.value + getPar, true);
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
				document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];
				document.getElementById("inp[jatahCuti]").value = data["jatahCuti"] == undefined ? "" : data["jatahCuti"];
				
				document.getElementById("inp[idPengganti]").value = data["idPengganti"] == undefined ? "" : data["idPengganti"];
				document.getElementById("inp[nikPengganti]").value = data["nikPengganti"] == undefined ? "" : data["nikPengganti"];
				document.getElementById("inp[namaPengganti]").value = data["namaPengganti"] == undefined ? "" : data["namaPengganti"];
				document.getElementById("inp[idAtasan]").value = data["idAtasan"] == undefined ? "" : data["idAtasan"];
				document.getElementById("inp[nikAtasan]").value = data["nikAtasan"] == undefined ? "" : data["nikAtasan"];
				document.getElementById("inp[namaAtasan]").value = data["namaAtasan"] == undefined ? "" : data["namaAtasan"];
				
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
			getJumlah(getPar);
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + "&par[tanggalCuti]=" + tanggalCuti + getPar, true);
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
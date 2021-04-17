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
	tanggalCuti=document.getElementById("inp[tanggalCuti]");
	mulaiCuti=document.getElementById("inp[mulaiCuti]");
	selesaiCuti=document.getElementById("inp[selesaiCuti]");
	jatahCuti=document.getElementById("inp[jatahCuti]");
	jumlahCuti=document.getElementById("inp[jumlahCuti]");
	sisaCuti=document.getElementById("inp[sisaCuti]");

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			 var arr = xmlHttp.responseText.split("\t");
			//  alert(response);
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
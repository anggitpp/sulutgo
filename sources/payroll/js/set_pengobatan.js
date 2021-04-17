// function getGrade(getPar, flag){
// 	idPangkat=document.getElementById("inp[idPangkat]");
// 	idGrade=document.getElementById("inp[idGrade]");
	
// 	var xmlHttp = getXMLHttp();
// 	xmlHttp.onreadystatechange = function(){	
// 		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
// 			response = xmlHttp.responseText.trim();
// 			if(response){
// 				var data = JSON.parse(response);
				
// 				for(var i=idGrade.options.length-1;i>=0;i--){
// 					idGrade.remove(i);
// 				}
			
// 				var opt = document.createElement("OPTION");
// 				opt.value = "";
// 				opt.text = "";
// 				idGrade.options.add(opt);				
				
// 				for (i = 0; i < data.length; ++i){					
// 					var opt = document.createElement("OPTION");
// 					var cat = data[i]["namaData"];
// 					opt.value = data[i]["kodeData"]
// 					opt.text = data[i]["namaData"];		
// 					if(opt.value) idGrade.options.add(opt);
// 				}
// 			}
// 		}
// 	}
// 	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[idPangkat]=" + idPangkat.value + getPar, true);
// 	xmlHttp.send(null);
// 	return false;
// }

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
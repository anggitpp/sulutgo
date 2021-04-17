function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	getPegawai(getPar, nikPegawai);
}

function getHubungan(element,getPar) {
	hubunganPasien = document.getElementById("hubunganPasien");
	bukti = document.getElementById("bukti");

	// if(empty(element)){
		// hubunganPasien.value = "";
	// }
	// alert(element);

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			// alert(xmlHttp.responseText);
			response = xmlHttp.responseText.trim();
			var arr = xmlHttp.responseText.split("\t");
			
			if (xmlHttp.responseText != "") {
				hubunganPasien.value = arr[0];
				if(arr[0] == "Anak"){
				if(arr[1] > 23){
				bukti.style.display = "block";
				}else{
				bukti.style.display = "none";
				}
				}else{
				bukti.style.display = "none";
				}
			}else{
				hubunganPasien.value = "";
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=hubungan&par[idPasien]=" + element +getPar, true);
	// console.log("ajax.php?par[mode]=jobposisi&par[kodePosisi]=" + element +getPar);
	xmlHttp.send(null);
	return false;
}

function hitungSisa(element){
	batas = document.getElementById("inp[batasNilai]");
	pengambilan = document.getElementById("inp[pengambilan]");
	sisaNilai = document.getElementById("inp[sisaNilai]");

	sisaNilai.value = batas.value - pengambilan.value;

	if(sisaNilai.value < 0){
		alert("Pengambilan tidak boleh lebih dari Batas Nilai");
		pengambilan.value = 0;
		sisaNilai.value = batas.value - pengambilan.value;
	}
}

function getPegawai(getPar, nikPegawai){
	// alert("a");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200){			
			response = xmlHttp.responseText.trim();			
			// alert(response);
			if(response){				
				var data = JSON.parse(response);
				parent.document.getElementById("inp[idPegawai]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				parent.document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				parent.document.getElementById("inp[namaPegawai]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				parent.document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				parent.document.getElementById("inp[namaGolongan]").value = data["namaGolongan"] == undefined ? "" : data["namaGolongan"];
				parent.document.getElementById("inp[batasNilai]").value = data["batasNilai"] == undefined ? "" : data["batasNilai"];




				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");+

				closeBox();
			}else{
				alert('Gagal Mengambil data');
			}
		}
	}
	
	// console.log("ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar);
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}
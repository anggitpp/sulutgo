function chk(){
	nilaiPinjaman=document.getElementById("inp[nilaiPinjaman]");
	waktuPinjaman=document.getElementById("inp[waktuPinjaman]");
	
	if(validation(document.form)){
		if(nilaiPinjaman.value * 1 < 1){
			alert("anda harus mengisi pinjaman");
			nilaiPinjaman.focus();
			return false;
		}
		
		if(waktuPinjaman.value * 1 < 1){
			alert("anda harus mengisi waktu");
			waktuPinjaman.focus();
			return false;
		}
		return true;
	}
	return false;
}

function getNomor(getPar){
	tanggalSakit=document.getElementById("tanggalSakit");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[nomorSakit]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalSakit=" + tanggalSakit.value + getPar, true);
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
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

// function setAngsuran(){	

		
// 	nilaiPinjaman = document.getElementById("inp[nilaiPinjaman]");
// 	waktuPinjaman = document.getElementById("inp[waktuPinjaman]");
// 	angsuranPinjaman = document.getElementById("inp[angsuranPinjaman]");
// 	angsuranPinjaman2 = document.getElementById("inp[angsuranPinjaman2]");
// 	bungaPinjaman = document.getElementById("inp[bungaPinjaman]");
// 	marginPinjaman = document.getElementById("inp[marginPinjaman]");

// 	bunga = (bungaPinjaman.value * convert(nilaiPinjaman.value)) / 100;

// 	marginPinjaman.value = bunga;



// 	// alert(bunga);

// 	nilaiPinjam = convert(nilaiPinjaman.value);
// 	nilaiPinjam = parseInt(nilaiPinjam) + parseInt(bunga);
// 	// alert(nilaiPinjam);

// 	if(bungaPinjaman.value != 0){
// 		angsuranPinjamanBelumFix = convert(waktuPinjaman.value) > 0 ? nilaiPinjam / convert(waktuPinjaman.value) : 0;

// 	}else{
// 		angsuranPinjamanBelumFix = convert(waktuPinjaman.value) > 0 ? convert(nilaiPinjaman.value) / convert(waktuPinjaman.value) : 0;	
// 	}

// 	angsuranPinjaman.value = angsuranPinjamanBelumFix;
// 	angsuranPinjaman2.value = angsuranPinjamanBelumFix;
	
// 	nilaiPinjaman.value = formatAngka(nilaiPinjaman.value);
// 	waktuPinjaman.value = formatAngka(waktuPinjaman.value);
// 	angsuranPinjaman.value = formatAngka(angsuranPinjaman.value);
// 	angsuranPinjaman2.value = formatAngka(angsuranPinjaman2.value);
	
// }

function setAngsuran(){	
	nilaiPinjaman = document.getElementById("inp[nilaiPinjaman]");
	waktuPinjaman = document.getElementById("inp[waktuPinjaman]");
	angsuranPinjaman = document.getElementById("inp[angsuranPinjaman]");
	angsuranPinjaman2 = document.getElementById("inp[angsuranPinjaman2]");
	bungaPinjaman = document.getElementById("inp[bungaPinjaman]");
	marginPinjaman = document.getElementById("inp[marginPinjaman]");

	bunga = convert(nilaiPinjaman.value) * bungaPinjaman.value / 100;

	marginPinjaman.value = bunga;
	
	angsuranPinjaman.value = convert(waktuPinjaman.value) > 0 ? convert(nilaiPinjaman.value) / convert(waktuPinjaman.value) + bunga : 0;
	angsuranPinjaman2.value = convert(waktuPinjaman.value) > 0 ? convert(nilaiPinjaman.value) / convert(waktuPinjaman.value) : 0;


	
	angsuranPinjaman2.value = formatAngka(angsuranPinjaman2.value);
	bungaPinjaman.value = formatAngka(bungaPinjaman.value);
	marginPinjaman.value = formatAngka(marginPinjaman.value);
	nilaiPinjaman.value = formatAngka(nilaiPinjaman.value);
	waktuPinjaman.value = formatAngka(waktuPinjaman.value);
	angsuranPinjaman.value = formatAngka(angsuranPinjaman.value);
	
}
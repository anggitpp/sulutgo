function getTotal(){
	nilaiTransport = document.getElementById('inp[nilaiTransport]').value;
	nilaiTransport = Number(nilaiTransport.replace(/[^0-9\.]+/g,""));
	nilaiAkomodasi = document.getElementById('inp[nilaiAkomodasi]').value;
	nilaiAkomodasi = Number(nilaiAkomodasi.replace(/[^0-9\.]+/g,""));
	nilaiUang = document.getElementById('inp[nilaiUang]').value;
	nilaiUang = Number(nilaiUang.replace(/[^0-9\.]+/g,""));
	nilaiPulsa = document.getElementById('inp[nilaiPulsa]').value;
	nilaiPulsa = Number(nilaiPulsa.replace(/[^0-9\.]+/g,""));
	nilaiTaxi = document.getElementById('inp[nilaiTaxi]').value;
	nilaiTaxi = Number(nilaiTaxi.replace(/[^0-9\.]+/g,""));
	// total = document.getElementById('inp[total]').value;
	// alert(total);

	total = nilaiTransport + nilaiAkomodasi + nilaiUang + nilaiPulsa + nilaiTaxi;
	totals = document.getElementById('inp[total]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;

}

function getJumlah(getPar){		
	// alert("hehe");
	mulaiDinas=document.getElementById("mulaiDinas");
	selesaiDinas=document.getElementById("selesaiDinas");
	
	hariTransport=document.getElementById("hariTransport");
	hariAkomodasi=document.getElementById("hariAkomodasi");
	hariUang=document.getElementById("hariUang");
	hariTaxi=document.getElementById("hariTaxi");

	jumlahTransport=document.getElementById("jumlahTransport");
	jumlahAkomodasi=document.getElementById("jumlahAkomodasi");
	jumlahUang=document.getElementById("jumlahUang");
	jumlahTaxi=document.getElementById("jumlahTaxi");

	nilaiTransport = document.getElementById('inp[nilaiTransport]').value;
	nilaiTransport = Number(nilaiTransport.replace(/[^0-9\.]+/g,""));
	nilaiAkomodasi = document.getElementById('inp[nilaiAkomodasi]').value;
	nilaiAkomodasi = Number(nilaiAkomodasi.replace(/[^0-9\.]+/g,""));
	nilaiUang = document.getElementById('inp[nilaiUang]').value;
	nilaiUang = Number(nilaiUang.replace(/[^0-9\.]+/g,""));
	nilaiPulsa = document.getElementById('inp[nilaiPulsa]').value;
	nilaiPulsa = Number(nilaiPulsa.replace(/[^0-9\.]+/g,""));
	nilaiTaxi = document.getElementById('inp[nilaiTaxi]').value;
	nilaiTaxi = Number(nilaiTaxi.replace(/[^0-9\.]+/g,""));


	// alert(mulaiCuti.value);
	// alert(selesaiCuti.value);
	
	var xmlHttp = getXMLHttp();
	// alert(xmlHttp);
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			 var arr = xmlHttp.responseText.split("\t");

			 hasilTransport = nilaiTransport * response;
			 hasilAkomodasi = nilaiAkomodasi * response;
			 hasilUang = nilaiUang * response;

			 hariTransport.value=response;

			 // alert(hasilTransport);
			 hariAkomodasi.value=response;
			 hariUang.value=response;
			 // hariTaxi.value=response;

			 total = hasilTransport + hasilAkomodasi + hasilUang + nilaiPulsa + nilaiTaxi;
			 totals = document.getElementById('inp[total]');
			 totals.value = total;
			 replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(replace.length == 0) replace = 0;
			 totals.value = replace;

			 // jumlahTransport.value = hasilTransport;

			 jumlahTransport.value = hasilTransport;
			 hasilTransport = formatCurrency(jumlahTransport.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilTransport.length == 0) hasilTransport = 0;
			 jumlahTransport.value = hasilTransport;

			 jumlahAkomodasi.value = hasilAkomodasi;
			 hasilAkomodasi = formatCurrency(jumlahAkomodasi.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilAkomodasi.length == 0) hasilAkomodasi = 0;
			 jumlahAkomodasi.value = hasilAkomodasi;

			 jumlahUang.value = hasilUang;
			 hasilUang = formatCurrency(jumlahUang.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilUang.length == 0) hasilUang = 0;
			 jumlahUang.value = hasilUang;

			

		}
	}


	xmlHttp.open("GET", "ajax.php?par[mode]=dinas&par[mulaiDinas]=" + mulaiDinas.value + "&par[selesaiDinas]=" + selesaiDinas.value + getPar, true);
	xmlHttp.send(null);
	return false;

}
function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	getPegawai(getPar, nikPegawai);
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
				parent.document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];

				parent.document.getElementById("inp[nilaiTransport]").value = data["nilaiTransport"] == undefined ? "" : data["nilaiTransport"];
				parent.document.getElementById("inp[nilaiAkomodasi]").value = data["nilaiAkomodasi"] == undefined ? "" : data["nilaiAkomodasi"];
				parent.document.getElementById("inp[nilaiUang]").value = data["nilaiUang"] == undefined ? "" : data["nilaiUang"];
				parent.document.getElementById("inp[nilaiPulsa]").value = data["nilaiPulsa"] == undefined ? "" : data["nilaiPulsa"];
				parent.document.getElementById("inp[nilaiTaxi]").value = data["nilaiTaxi"] == undefined ? "" : data["nilaiTaxi"];




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
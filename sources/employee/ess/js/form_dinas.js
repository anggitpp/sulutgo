function cekKendaraan(element){
	// alert(element);
	fieldsetKendaraan = document.getElementById('fieldsetKendaraan');
	// kendaraanDinas = document.getElementById('inp[kendaraanDinas]');
	if(element == "t"){
		fieldsetKendaraan.style.display = "none";
		// alert("none");
	}else{
		fieldsetKendaraan.style.display = "block";
	}
}

function getTotal(){
	nilaiSaku = document.getElementById('inp[nilaiSaku]').value;
	nilaiSaku = Number(nilaiSaku.replace(/[^0-9\.]+/g,""));
	nilaiMakan = document.getElementById('inp[nilaiMakan]').value;
	nilaiMakan = Number(nilaiMakan.replace(/[^0-9\.]+/g,""));
	nilaiPelengkap = document.getElementById('inp[nilaiPelengkap]').value;
	nilaiPelengkap = Number(nilaiPelengkap.replace(/[^0-9\.]+/g,""));

	total = nilaiSaku + nilaiMakan + nilaiPelengkap;
	totals = document.getElementById('inp[total]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;

}

function totalBerangkat(){
	jumlahBerangkat = document.getElementById('jumlahBerangkat').value;
	jumlahBerangkat = Number(jumlahBerangkat.replace(/[^0-9\.]+/g,""));
	hariBerangkat = document.getElementById('inp[hariBerangkat]').value;

	total = jumlahBerangkat * hariBerangkat;
	totals = document.getElementById('inp[nilaiBerangkat]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;

	jumlahPulang = document.getElementById('jumlahPulang').value;
	jumlahPulang = Number(jumlahPulang.replace(/[^0-9\.]+/g,""));
	hariPulang = document.getElementById('inp[hariPulang]').value;

	total = jumlahPulang * hariPulang;
	totals = document.getElementById('inp[nilaiPulang]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;

	nilaiBerangkat = document.getElementById('inp[nilaiBerangkat]').value;
	nilaiBerangkat = Number(nilaiBerangkat.replace(/[^0-9\.]+/g,""));
	nilaiPulang = document.getElementById('inp[nilaiPulang]').value;
	nilaiPulang = Number(nilaiPulang.replace(/[^0-9\.]+/g,""));

	total = nilaiBerangkat + nilaiPulang;
	totals = document.getElementById('inp[totalTaxi]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;

	totalJatah = document.getElementById('inp[total]').value;
	totalJatah = Number(totalJatah.replace(/[^0-9\.]+/g,""));
	totalTaxi = document.getElementById('inp[totalTaxi]').value;
	totalTaxi = Number(totalTaxi.replace(/[^0-9\.]+/g,""));

	total = totalJatah + totalTaxi;
	totals = document.getElementById('inp[totalAll]');
	totals.value = total;
	replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
	if(replace.length == 0) replace = 0;
	totals.value = replace;


}

function getJumlah(getPar){		
	// alert("hehe");
	mulaiDinas=document.getElementById("mulaiDinas");
	selesaiDinas=document.getElementById("selesaiDinas");
	
	hariSaku=document.getElementById("hariSaku");
	hariMakan=document.getElementById("hariMakan");
	hariPelengkap=document.getElementById("hariPelengkap");
	// hariTaxi=document.getElementById("hariTaxi");

	jumlahSaku=document.getElementById("jumlahSaku");
	jumlahMakan=document.getElementById("jumlahMakan");
	jumlahPelengkap=document.getElementById("jumlahPelengkap");
	// jumlahTaxi=document.getElementById("jumlahTaxi");

	nilaiSaku = document.getElementById('inp[nilaiSaku]').value;
	nilaiSaku = Number(nilaiSaku.replace(/[^0-9\.]+/g,""));
	nilaiMakan = document.getElementById('inp[nilaiMakan]').value;
	nilaiMakan = Number(nilaiMakan.replace(/[^0-9\.]+/g,""));
	nilaiPelengkap = document.getElementById('inp[nilaiPelengkap]').value;
	nilaiPelengkap = Number(nilaiPelengkap.replace(/[^0-9\.]+/g,""));
	// nilaiPulsa = document.getElementById('inp[nilaiPulsa]').value;
	// nilaiPulsa = Number(nilaiPulsa.replace(/[^0-9\.]+/g,""));
	// nilaiTaxi = document.getElementById('inp[nilaiTaxi]').value;
	// nilaiTaxi = Number(nilaiTaxi.replace(/[^0-9\.]+/g,""));


	// alert(mulaiCuti.value);
	// alert(selesaiCuti.value);
	
	var xmlHttp = getXMLHttp();
	// alert(xmlHttp);
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			 var arr = xmlHttp.responseText.split("\t");

			 hasilSaku = nilaiSaku * response;
			 hasilMakan = nilaiMakan * response;
			 hasilPelengkap = nilaiPelengkap * response;

			 hariSaku.value=response;

			 // alert(hasilSaku);
			 hariMakan.value=response;
			 hariPelengkap.value=response;
			 // hariTaxi.value=response;

			 total = hasilSaku + hasilMakan + hasilPelengkap;
			 totals = document.getElementById('inp[total]');
			 totals.value = total;
			 replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(replace.length == 0) replace = 0;
			 totals.value = replace;

			 totalJatah = document.getElementById('inp[total]').value;
			 totalJatah = Number(totalJatah.replace(/[^0-9\.]+/g,""));
			 totalTaxi = document.getElementById('inp[totalTaxi]').value;
			 totalTaxi = Number(totalTaxi.replace(/[^0-9\.]+/g,""));

			 total = totalJatah + totalTaxi;
			 totals = document.getElementById('inp[totalAll]');
			 totals.value = total;
			 replace = formatCurrency(totals.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(replace.length == 0) replace = 0;
			 totals.value = replace;

			 // jumlahSaku.value = hasilSaku;

			 jumlahSaku.value = hasilSaku;
			 hasilSaku = formatCurrency(jumlahSaku.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilSaku.length == 0) hasilSaku = 0;
			 jumlahSaku.value = hasilSaku;

			 jumlahMakan.value = hasilMakan;
			 hasilMakan = formatCurrency(jumlahMakan.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilMakan.length == 0) hasilMakan = 0;
			 jumlahMakan.value = hasilMakan;

			 jumlahPelengkap.value = hasilPelengkap;
			 hasilPelengkap = formatCurrency(jumlahPelengkap.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
			 if(hasilPelengkap.length == 0) hasilPelengkap = 0;
			 jumlahPelengkap.value = hasilPelengkap;

			

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


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
function getJumlah(kodeData, dataKeberapa){
  nilai = document.getElementById('det[nilai_'+dataKeberapa+']');
  satuan = document.getElementById('det[satuan_'+dataKeberapa+']');
  total = document.getElementById('det[total_'+dataKeberapa+']');
  sisa = document.getElementById('det[sisa_'+dataKeberapa+']');

  // alert("haha");

  nilaiText = Number(nilai.value.replace(/[^0-9\.-]+/g,""));

  total.value = nilaiText * satuan.value;

  // alert("hehe "+sisa.innerHTML);

  replace = formatCurrency(total.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g,''));
  if(replace.length == 0) replace = 0;
  total.value = replace;

  totalNum = Number(total.value.replace(/[^0-9\.-]+/g,""));
  sisaNum = Number(sisa.innerHTML.replace(/[^0-9\.-]+/g,""));

  if(totalNum > sisaNum){
  	alert("Total biaya tidak boleh melebihi batas maksimum");
  	nilai.value = 0;
  	total.value = 0;

  }

}

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
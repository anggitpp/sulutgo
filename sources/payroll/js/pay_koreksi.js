function nextDate(getPar){
	bulanKoreksi=document.getElementById("par[bulanKoreksi]");
	tahunKoreksi=document.getElementById("par[tahunKoreksi]");
	
	bulan = bulanKoreksi.value == 12 ? 01 : bulanKoreksi.value * 1 + 1;	
	tahun = bulanKoreksi.value == 12 ? tahunKoreksi.value * 1 + 1 : tahunKoreksi.value;
	
	bulanKoreksi.value = bulan > 9 ? bulan : "0" + bulan;
	tahunKoreksi.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanKoreksi=document.getElementById("par[bulanKoreksi]");
	tahunKoreksi=document.getElementById("par[tahunKoreksi]");
	
	bulan = bulanKoreksi.value == 01 ? 12 : bulanKoreksi.value * 1 - 1;	
	tahun = bulanKoreksi.value == 01 ? tahunKoreksi.value * 1 - 1 : tahunKoreksi.value;	
	
	bulanKoreksi.value = bulan > 9 ? bulan : "0" + bulan;
	tahunKoreksi.value = tahun;
	
	document.getElementById('form').submit();
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
				alert("maaf, nik : \""+ nikPegawai + "\" tidak ada pada proses gaji periode : " + document.getElementById("periodeKoreksi").innerHTML);
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setKoreksi(obj){
	cntPenerimaan = document.getElementById("cntPenerimaan").value;
	cntPotongan = document.getElementById("cntPotongan").value;
	
	nilaiTerbilang = document.getElementById("nilaiTerbilang");
	totalPenerimaan = document.getElementById("totalPenerimaan");
	totalPotongan = document.getElementById("totalPotongan");
	totalGaji = document.getElementById("totalGaji");
	
	cekAngka(obj);
	
	nilaiPenerimaan = 0;
	for(i=1; i<=cntPenerimaan; i++){
		nilaiProses = document.getElementById("inp[t]["+ i +"][nilaiProses]").value;
		nilaiPenerimaan = nilaiPenerimaan * 1 + convert(nilaiProses) * 1;		
	}	
	totalPenerimaan.value = formatNumber(nilaiPenerimaan);
	
	nilaiPotongan = 0;
	for(i=1; i<=cntPotongan; i++){
		nilaiProses = document.getElementById("inp[p]["+ i +"][nilaiProses]").value;
		nilaiPotongan = nilaiPotongan * 1 + convert(nilaiProses) * 1;		
	}	
	totalPotongan.value = formatNumber(nilaiPotongan);
	
	totalGaji.value = formatNumber(convert(totalPenerimaan.value) - convert(totalPotongan.value));
	
	nilaiTerbilang.innerHTML = terbilang(convert(totalGaji.value));
}
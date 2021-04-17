function nextDate(getPar){
	bulanPinjaman=document.getElementById("par[bulanPinjaman]");
	tahunPinjaman=document.getElementById("par[tahunPinjaman]");
	
	bulan = bulanPinjaman.value == 12 ? 01 : bulanPinjaman.value * 1 + 1;	
	tahun = bulanPinjaman.value == 12 ? tahunPinjaman.value * 1 + 1 : tahunPinjaman.value;
	
	bulanPinjaman.value = bulan > 9 ? bulan : "0" + bulan;
	tahunPinjaman.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanPinjaman=document.getElementById("par[bulanPinjaman]");
	tahunPinjaman=document.getElementById("par[tahunPinjaman]");
	
	bulan = bulanPinjaman.value == 01 ? 12 : bulanPinjaman.value * 1 - 1;	
	tahun = bulanPinjaman.value == 01 ? tahunPinjaman.value * 1 - 1 : tahunPinjaman.value;	
	
	bulanPinjaman.value = bulan > 9 ? bulan : "0" + bulan;
	tahunPinjaman.value = tahun;
	
	document.getElementById('form').submit();
}
function nextDate(getPar){
	bulanProses=document.getElementById("par[bulanProses]");
	tahunProses=document.getElementById("par[tahunProses]");
	
	bulan = bulanProses.value == 12 ? 01 : bulanProses.value * 1 + 1;	
	tahun = bulanProses.value == 12 ? tahunProses.value * 1 + 1 : tahunProses.value;
	
	bulanProses.value = bulan > 9 ? bulan : "0" + bulan;
	tahunProses.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanProses=document.getElementById("par[bulanProses]");
	tahunProses=document.getElementById("par[tahunProses]");
	
	bulan = bulanProses.value == 01 ? 12 : bulanProses.value * 1 - 1;	
	tahun = bulanProses.value == 01 ? tahunProses.value * 1 - 1 : tahunProses.value;	
	
	bulanProses.value = bulan > 9 ? bulan : "0" + bulan;
	tahunProses.value = tahun;
	
	document.getElementById('form').submit();
}
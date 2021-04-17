function nextDate(getPar){
	bulanLembur=document.getElementById("par[bulanLembur]");
	tahunLembur=document.getElementById("par[tahunLembur]");
	
	bulan = bulanLembur.value == 12 ? 01 : bulanLembur.value * 1 + 1;	
	tahun = bulanLembur.value == 12 ? tahunLembur.value * 1 + 1 : tahunLembur.value;
	
	bulanLembur.value = bulan > 9 ? bulan : "0" + bulan;
	tahunLembur.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanLembur=document.getElementById("par[bulanLembur]");
	tahunLembur=document.getElementById("par[tahunLembur]");
	
	bulan = bulanLembur.value == 01 ? 12 : bulanLembur.value * 1 - 1;	
	tahun = bulanLembur.value == 01 ? tahunLembur.value * 1 - 1 : tahunLembur.value;	
	
	bulanLembur.value = bulan > 9 ? bulan : "0" + bulan;
	tahunLembur.value = tahun;
	
	document.getElementById('form').submit();
}
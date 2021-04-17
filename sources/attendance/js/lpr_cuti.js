function nextDate(getPar){
	bulanCuti=document.getElementById("par[bulanCuti]");
	tahunCuti=document.getElementById("par[tahunCuti]");
	
	bulan = bulanCuti.value == 12 ? 01 : bulanCuti.value * 1 + 1;	
	tahun = bulanCuti.value == 12 ? tahunCuti.value * 1 + 1 : tahunCuti.value;
	
	bulanCuti.value = bulan > 9 ? bulan : "0" + bulan;
	tahunCuti.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanCuti=document.getElementById("par[bulanCuti]");
	tahunCuti=document.getElementById("par[tahunCuti]");
	
	bulan = bulanCuti.value == 01 ? 12 : bulanCuti.value * 1 - 1;	
	tahun = bulanCuti.value == 01 ? tahunCuti.value * 1 - 1 : tahunCuti.value;	
	
	bulanCuti.value = bulan > 9 ? bulan : "0" + bulan;
	tahunCuti.value = tahun;
	
	document.getElementById('form').submit();
}
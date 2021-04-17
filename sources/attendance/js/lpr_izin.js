function nextDate(getPar){
	bulanHadir=document.getElementById("par[bulanHadir]");
	tahunHadir=document.getElementById("par[tahunHadir]");
	
	bulan = bulanHadir.value == 12 ? 01 : bulanHadir.value * 1 + 1;	
	tahun = bulanHadir.value == 12 ? tahunHadir.value * 1 + 1 : tahunHadir.value;
	
	bulanHadir.value = bulan > 9 ? bulan : "0" + bulan;
	tahunHadir.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanHadir=document.getElementById("par[bulanHadir]");
	tahunHadir=document.getElementById("par[tahunHadir]");
	
	bulan = bulanHadir.value == 01 ? 12 : bulanHadir.value * 1 - 1;	
	tahun = bulanHadir.value == 01 ? tahunHadir.value * 1 - 1 : tahunHadir.value;	
	
	bulanHadir.value = bulan > 9 ? bulan : "0" + bulan;
	tahunHadir.value = tahun;
	
	document.getElementById('form').submit();
}
function nextDate(getPar){
	bulanData=document.getElementById("par[bulan]");
	tahunData=document.getElementById("par[tahun]");
	
	bulan = bulanData.value == 12 ? 01 : bulanData.value * 1 + 1;	
	tahun = bulanData.value == 12 ? tahunData.value * 1 + 1 : tahunData.value;
	
	bulanData.value = bulan > 9 ? bulan : "0" + bulan;
	tahunData.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanData=document.getElementById("par[bulan]");
	tahunData=document.getElementById("par[tahun]");
	
	bulan = bulanData.value == 01 ? 12 : bulanData.value * 1 - 1;	
	tahun = bulanData.value == 01 ? tahunData.value * 1 - 1 : tahunData.value;	
	
	bulanData.value = bulan > 9 ? bulan : "0" + bulan;
	tahunData.value = tahun;
	
	document.getElementById('form').submit();
}
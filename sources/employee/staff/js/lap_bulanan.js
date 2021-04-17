function nextDate(getPar){
	bulanAbsen=document.getElementById("par[bulanAbsen]");
	tahunAbsen=document.getElementById("par[tahunAbsen]");
	
	bulan = bulanAbsen.value == 12 ? 01 : bulanAbsen.value * 1 + 1;	
	tahun = bulanAbsen.value == 12 ? tahunAbsen.value * 1 + 1 : tahunAbsen.value;
	
	bulanAbsen.value = bulan > 9 ? bulan : "0" + bulan;
	tahunAbsen.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanAbsen=document.getElementById("par[bulanAbsen]");
	tahunAbsen=document.getElementById("par[tahunAbsen]");
	
	bulan = bulanAbsen.value == 01 ? 12 : bulanAbsen.value * 1 - 1;	
	tahun = bulanAbsen.value == 01 ? tahunAbsen.value * 1 - 1 : tahunAbsen.value;	
	
	bulanAbsen.value = bulan > 9 ? bulan : "0" + bulan;
	tahunAbsen.value = tahun;
	
	document.getElementById('form').submit();
}
function nextDate(getPar){
	bulanJadwal=document.getElementById("par[bulanJadwal]");
	tahunJadwal=document.getElementById("par[tahunJadwal]");
	
	bulan = bulanJadwal.value == 12 ? 01 : bulanJadwal.value * 1 + 1;	
	tahun = bulanJadwal.value == 12 ? tahunJadwal.value * 1 + 1 : tahunJadwal.value;
	
	bulanJadwal.value = bulan > 9 ? bulan : "0" + bulan;
	tahunJadwal.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanJadwal=document.getElementById("par[bulanJadwal]");
	tahunJadwal=document.getElementById("par[tahunJadwal]");
	
	bulan = bulanJadwal.value == 01 ? 12 : bulanJadwal.value * 1 - 1;	
	tahun = bulanJadwal.value == 01 ? tahunJadwal.value * 1 - 1 : tahunJadwal.value;	
	
	bulanJadwal.value = bulan > 9 ? bulan : "0" + bulan;
	tahunJadwal.value = tahun;
	
	document.getElementById('form').submit();
}
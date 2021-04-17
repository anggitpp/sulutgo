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
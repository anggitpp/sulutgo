function nextDate(getPar){
	bulanPerubahan=document.getElementById("par[bulanPerubahan]");
	tahunPerubahan=document.getElementById("par[tahunPerubahan]");
	
	bulan = bulanPerubahan.value == 12 ? 01 : bulanPerubahan.value * 1 + 1;	
	tahun = bulanPerubahan.value == 12 ? tahunPerubahan.value * 1 + 1 : tahunPerubahan.value;
	
	bulanPerubahan.value = bulan > 9 ? bulan : "0" + bulan;
	tahunPerubahan.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanPerubahan=document.getElementById("par[bulanPerubahan]");
	tahunPerubahan=document.getElementById("par[tahunPerubahan]");
	
	bulan = bulanPerubahan.value == 01 ? 12 : bulanPerubahan.value * 1 - 1;	
	tahun = bulanPerubahan.value == 01 ? tahunPerubahan.value * 1 - 1 : tahunPerubahan.value;	
	
	bulanPerubahan.value = bulan > 9 ? bulan : "0" + bulan;
	tahunPerubahan.value = tahun;
	
	document.getElementById('form').submit();
}
function realisasiRab(){
	jumlahRab = document.getElementById("inp[jumlahRab]");
	hargaRab = document.getElementById("inp[hargaRab]");
	totalRab = convert(jumlahRab.value) * convert(hargaRab.value);
		
	jumlahRab.value = formatNumber(jumlahRab.value);
	hargaRab.value = formatNumber(hargaRab.value);	
	document.getElementById("inp[realisasiRab]").value = formatNumber(totalRab);
}

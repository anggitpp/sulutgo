function nilaiRab(){
	jumlahRab = document.getElementById("inp[jumlahRab]");
	hargaRab = document.getElementById("inp[hargaRab]");
	hargaPengalih = document.getElementById("inp[hargaPengalih]");
	totalRab = convert(jumlahRab.value) * convert(hargaRab.value) * convert(hargaPengalih.value);
		
	jumlahRab.value = formatNumber(jumlahRab.value);
	hargaRab.value = formatNumber(hargaRab.value);
	hargaPengalih.value = formatNumber(hargaPengalih.value);
	document.getElementById("inp[nilaiRab]").value = formatNumber(totalRab);
}

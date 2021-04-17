function setDasar(){
	formula=document.getElementById("formula");
	proses=document.getElementById("proses");
	fixed=document.getElementById("fixed");
	
	nilaiKomponen=document.getElementById("inp[nilaiKomponen]");
	maxKomponen=document.getElementById("inp[maxKomponen]");
	idPengali=document.getElementById("inp[idPengali]");
	
	nilaiDisplay=document.getElementById("nilaiKomponen");
	maxDisplay=document.getElementById("maxKomponen");
		
	if(formula.checked){
		nilaiDisplay.style.display = "block";
		maxDisplay.style.display = "block";
	}else if(fixed.checked){
		nilaiDisplay.style.display = "block";
		maxDisplay.style.display = "none";
		
		maxKomponen.value = "0.00";
		idPengali.value = "";
	}else if(proses.checked){
		nilaiDisplay.style.display = "none";
		maxDisplay.style.display = "none";
	}else{
		nilaiDisplay.style.display = "none";
		maxDisplay.style.display = "none";
		
		nilaiKomponen.value = "0.00";
		maxKomponen.value = "0.00";
		idPengali.value = "";
	}
}
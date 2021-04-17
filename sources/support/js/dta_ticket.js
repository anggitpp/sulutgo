function setNilai(){
	if(document.getElementById('tidak').checked == true){
		document.getElementById('nilaiAnalisa').style.display = "none";
		document.getElementById('inp[nilaiAnalisa]').value = 0;
	}else{
		document.getElementById('nilaiAnalisa').style.display = "block";
	}
}
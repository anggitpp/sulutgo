function chk(getPar){
	if(validation(document.form)){
		document.getElementById("_submit").value = "t";
		document.getElementById("form").submit();
	}
	return false;
}

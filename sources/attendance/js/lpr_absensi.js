function nextDate(getPar){
	tanggalAbsen=document.getElementById("tanggalAbsen");
	tanggalAbsen.value = dateAdd(tanggalAbsen.value,1);	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	tanggalAbsen=document.getElementById("tanggalAbsen");
	tanggalAbsen.value = dateAdd(tanggalAbsen.value, -1);
	document.getElementById('form').submit();
}
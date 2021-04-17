function save(getPar){
	kodeSetting=document.getElementById("inp[kodeSetting]").value;	
	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{
				if(validation(document.form)){
					document.getElementById("form").submit();
				}
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[kodeSetting]=" + kodeSetting + getPar, true);
	xmlHttp.send(null);
	return false;
}
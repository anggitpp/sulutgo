function save(getPar){	
	namaCuti=document.getElementById("inp[namaCuti]").value;	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{
				if(validation(document.form)){
					document.getElementById("prosesImg").style.display = "block";
					document.getElementById("form").submit();
				}
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[namaCuti]=" + namaCuti + getPar, true);
	xmlHttp.send(null);
	return false;
}
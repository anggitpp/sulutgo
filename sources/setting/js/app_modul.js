function del(kodeModul, getPar){	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{ 
				if(confirm('are you sure to delete data ?')){  
					window.location = "?par[mode]=del&par[kodeModul]="+ kodeModul + getPar;
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=chk&par[kodeModul]="+ kodeModul + getPar, true);
	xmlHttp.send(null);
	return false;
}

function sts(){
	if(document.getElementById('count').value > 0 && document.getElementById('false').checked == true){
		alert("sorry, data has been use");
		document.getElementById('true').checked = true;
	}
}
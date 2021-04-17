function del(kodeParameter, getPar){	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{ 
				if(confirm('are you sure to delete data ?')){  
					window.location = "?par[mode]=del&par[kodeParameter]="+ kodeParameter + getPar;
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=chk&par[kodeParameter]="+ kodeParameter + getPar, true);
	xmlHttp.send(null);
	return false;
}
function save(getPar){
	kodeCategory=document.getElementById("inp[kodeCategory]").value;	
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
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[kodeCategory]=" + kodeCategory + getPar, true);
	xmlHttp.send(null);
	return false;
}

function delDet(kodeData, getPar){	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{ 
				if(confirm('are you sure to delete data ?')){  
					window.location = "?par[mode]=delDet&par[kodeData]="+ kodeData + getPar;
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=chkDet&par[kodeData]="+ kodeData + getPar, true);
	xmlHttp.send(null);
	return false;
}

function del(kodeCategory, getPar){	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{ 
				if(confirm('are you sure to delete data ?')){  
					window.location = "?par[mode]=del&par[kodeCategory]="+ kodeCategory + getPar;
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=chk&par[kodeCategory]="+ kodeCategory + getPar, true);
	xmlHttp.send(null);
	return false;
}

function sts(){
	if(document.getElementById('count').value > 0 && document.getElementById('false').checked == true){
		alert("sorry, data has been use");
		document.getElementById('true').checked = true;
	}
}

function order(getPar){
	kodeInduk=document.getElementById("inp[kodeInduk]").value;	
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				document.getElementById("inp[urutanData]").value = xmlHttp.responseText;										
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=order&inp[kodeInduk]=" + kodeInduk + getPar, true);
	xmlHttp.send(null);
	return false;
}
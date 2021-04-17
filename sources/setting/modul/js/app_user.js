function save(getPar){
	username=document.getElementById("inp[username]").value;	
	
	if(document.getElementById("inp[mode]").value == "add"){
		password=document.getElementById("inp[password]");
		repassword=document.getElementById("inp[repassword]");
		if(password.value && repassword.value && password.value != repassword.value){
			alert("password must be the same");
			repassword.focus();
			return false;
		}
	}
	
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
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[username]=" + username + getPar, true);
	xmlHttp.send(null);
	return false;
}

function pas(){
	password=document.getElementById("inp[password]");
	repassword=document.getElementById("inp[repassword]");
	if(password.value && repassword.value && password.value != repassword.value){
		alert("password must be the same");
		repassword.focus();
		return false;
	}
	
	if(validation(document.form)){
		document.getElementById("form").submit();
	}
	return false;
}

function del(username, getPar){	
	if(confirm('are you sure to delete data ?')){  
		window.location = "?par[mode]=del&par[username]="+ username + getPar;
	}	
}

function setPegawai(nikPegawai, getPar){	
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;	
	parent.getPegawai(getPar);
	closeBox();
}

function getPegawai(getPar){
	nikPegawai = document.getElementById("inp[nikPegawai]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("inp[idPegawai]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaUser]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				if(data["idPegawai"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}
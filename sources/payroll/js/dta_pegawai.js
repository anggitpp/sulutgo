function setKomponen(){
	document.getElementById("nilaiKomponen").style.display = document.getElementById("khusus").checked == true ? "block" : "none";
}

function setPokok(){
	document.getElementById("nilaiPokok").style.display = document.getElementById("khusus").checked == true ? "block" : "none";
}

function setUmr(getPar){
	document.getElementById("inp[nilaiPokok]").readOnly = document.getElementById("ya").checked == true ? true : false;
	
	if(document.getElementById("ya").checked == true){
		document.getElementById("inp[nilaiPokok]").readOnly = true;
		
		var xmlHttp = getXMLHttp();
		xmlHttp.onreadystatechange = function(){	
			if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
				response = xmlHttp.responseText.trim();
				if(response){
					document.getElementById("inp[nilaiPokok]").value = response;
				}
			}
		}
		xmlHttp.open("GET", "ajax.php?par[mode]=umr" + getPar, true);
		xmlHttp.send(null);
		return false;
	}else{
		document.getElementById("inp[nilaiPokok]").readOnly = false;
	}
}


function setProses(getPar){	
	formInput=document.getElementById("formInput");
	prosesImg=document.getElementById("prosesImg");
	progresBar=document.getElementById("progresBar");	
	
	
	tanggalPokok = document.getElementById('tanggalPokok').value;
	nomorPokok = document.getElementById('nomorPokok').value;
	fileData = document.getElementById('fileData').files[0];
	
	if(tanggalPokok.length < 1){
		alert("anda harus mengisi tanggal sk");
		document.getElementById('tanggalPokok').focus();
		return false;
	}else if(nomorPokok.length < 1){
		alert("anda harus mengisi no. sk");
		document.getElementById('nomorPokok').focus();
		return false;
	}else{
		prosesImg.style.display = "block";	
	
		var xmlHttp=new XMLHttpRequest();
		xmlHttp.onreadystatechange=function(){
			if (xmlHttp.readyState==4 && xmlHttp.status==200){
				response = xmlHttp.responseText.trim();
				if(response){				
					if(response.substring(0,8) == "fileData"){
						fileData = response.replace("fileData","");					
						setData(fileData, getPar, 1);
						formInput.style.display = "none";
						prosesImg.style.display = "none";
						progresBar.style.display = "block";					
					}else{
						prosesImg.style.display = "none";			
						alert(response);
					}
				}else{
					prosesImg.style.display = "none";
					alert("file harus dalam format .csv");
				}
			}
		}
		
		xmlHttp.open("POST","ajax.php?par[mode]=tab" + getPar,true);
		xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
		var formData = new FormData();
		
		formData.append("inp[nomorPokok]", nomorPokok);
		formData.append("inp[tanggalPokok]", tanggalPokok);
		formData.append("fileData", fileData);  
		
		xmlHttp.send(formData);
	}
}

function setData(fileData, getPar, rowData){
	persenBar=document.getElementById("persenBar");
	progresCnt=document.getElementById("progresCnt");	
	progresRes=document.getElementById("progresRes");
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){				
				dta=response.split("\t");
				persenBar.style.width = dta[0] + "%";
				progresCnt.innerHTML = dta[1];				
				//progresRes.innerHTML = progresRes.innerHTML + "<br>" + dta[2];
				setData(fileData, getPar, (rowData * 1 + 1));
			}else{
				endProses(getPar);
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=dta&par[fileData]=" + fileData + "&par[rowData]=" + rowData + getPar, true);
	xmlHttp.send(null);
	return false;
}

function endProses(getPar){	
	progresEnd=document.getElementById("progresEnd");
	progresRes=document.getElementById("progresRes");
	persenBar=document.getElementById("persenBar");	
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){				
				persenBar.className = "value bluebar";
				progresRes.innerHTML = response;				
				progresEnd.style.display = "block";
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=end" + getPar, true);
	xmlHttp.send(null);
	return false;
}
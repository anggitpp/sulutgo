function gData(getPar){		
	mSearch=document.getElementById("mSearch").value;	
	tSearch=document.getElementById("tSearch").value;	
	pSearch=document.getElementById("pSearch").value;	
	fSearch=document.getElementById("fSearch").value;	
	var xmlHttp = getXMLHttp();			
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var dta = response.split("\t");
				document.getElementById("tHari").innerHTML = dta[0];
				document.getElementById("tLibur").innerHTML = dta[1];
				document.getElementById("tKerja").innerHTML = dta[2];
				document.getElementById("tAlpha").innerHTML = dta[3];
				document.getElementById("tSakit").innerHTML = dta[4];
				document.getElementById("tIjin").innerHTML = dta[5];
				document.getElementById("tEo").innerHTML = dta[6];
				document.getElementById("tCuti").innerHTML = dta[7];
				document.getElementById("tService").innerHTML = dta[8];
				document.getElementById("tPeriode").innerHTML = dta[9];
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&mSearch=" + mSearch + "&tSearch=" + tSearch + "&pSearch=" + pSearch + "&fSearch=" + fSearch + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setProses(getPar){	
	formInput=document.getElementById("formInput");
	prosesImg=document.getElementById("prosesImg");
	progresBar=document.getElementById("progresBar");	
	
	prosesImg.style.display = "block";	
	fileData = document.getElementById('fileData').files[0];
		
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
	
	formData.append("fileData", fileData);  
	
	xmlHttp.send(formData);
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
	xmlHttp.open("GET", "ajax.php?par[mode]=dat&par[fileData]=" + fileData + "&par[rowData]=" + rowData + getPar, true);
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
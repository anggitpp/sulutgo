jQuery(document).ready(function(){
	_page = parseInt(document.getElementById("_page").value);
	_len = parseInt(document.getElementById("_len").value);
	
	var dTable = jQuery('#dynfilter').dataTable({		
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,		
		"bFilter": true,		
		"sDom": "frt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);	
	
	var dTable = jQuery('#dynpegawai').dataTable({		
		"sPaginationType": "full_numbers",		
		"iDisplayLength": _len,		
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,		
		"bFilter": true,		
		"sDom": "frt<'bottom'lip><'clear'>",		
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);	
});

function getPegawai(dta){
	dtaPegawai = document.getElementById('inp[dtaPegawai]');
	if(dta.checked == true)
		dtaPegawai.value = dtaPegawai.value + '\n' + dta.value;
	else
		dtaPegawai.value = dtaPegawai.value.replace('\n' + dta.value, '');
}

function setPegawai(){
	parent.document.getElementById('inp[txtPegawai]').value = document.getElementById('inp[dtaPegawai]').value;
	parent.tiny.box.hide();
	parent.fnClickAddRow()
}

function fnClickDelRow(obj){
	if(confirm("anda yakin akan menghapus data ini ?")){
		var row = jQuery(obj).closest('tr');
		var nRow = row[0];
		jQuery('#dynpegawai').dataTable().fnDeleteRow(nRow);
	}
}

function fnClickAddRow() {
	arr = document.getElementById('inp[txtPegawai]').value.split("\n");		
	for(i=1; i<arr.length; i++){		
		dta = arr[i].split("\t");						
		jQuery('#dynpegawai').dataTable().fnAddData( [						
			"<input type=\"hidden\" id=\"det[" + dta[0] +"]\" name=\"det[" + dta[0] +"]\" value=\"det[" + dta[0] +"]\">" + dta[1],
			dta[2],
			dta[3],
			dta[4],
			"<div align=\"center\" style=\"cursor:pointer\" onclick=\"fnClickDelRow(this);\"><img src=\"styles/images/icons/delete.png\"></div>",
		]);
	}
	
	document.getElementById('inp[txtPegawai]').value = "";
}


function setProses(getPar){	
	formInput=document.getElementById("formInput");
	prosesImg=document.getElementById("prosesImg");
	progresBar=document.getElementById("progresBar");	
	templateInfo=document.getElementById("templateInfo");	
	
	fileData = document.getElementById('fileData').files[0];
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
					templateInfo.style.display = "none";				
				}else{
					prosesImg.style.display = "none";			
					alert(response);
				}
			}else{
				prosesImg.style.display = "none";
				alert("file harus dalam format .xls");
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
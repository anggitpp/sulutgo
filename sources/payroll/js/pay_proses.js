function setProses(getPar){	
	prosesBtn=document.getElementById("prosesBtn");
	prosesImg=document.getElementById("prosesImg");
	progresBar=document.getElementById("progresBar");
	
	prosesBtn.style.display = "none";
	prosesImg.style.display = "block";
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				alert(response);
			}else{
				setData(getPar, 1);				
				prosesImg.style.display = "none";
				progresBar.style.display = "block";
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=tab" + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setData(getPar, idDetail){
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
				setData(getPar, (idDetail * 1 + 1));
			}else{
				endProses(getPar);
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=dat&par[idDetail]=" + idDetail + getPar, true);
	xmlHttp.send(null);
	return false;
}

function endProses(getPar){	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				alert(response);
				window.parent.location = "index.php?" + getPar;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=end" + getPar, true);
	xmlHttp.send(null);
	return false;
}

jQuery(document).ready(function() {
	_page = parseInt(document.getElementById("_page").value);
	_len = parseInt(document.getElementById("_len").value);
	
	jQuery("#subtable").dataTable( {
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);			
		},
		
		"fnFooterCallback": function ( nFoot, aData, iStart, iEnd, aiDisplay ){
			subTotal = 0;
			for(i=iStart ; i<iEnd ; i++) {
				subTotal = subTotal * 1 + convert(aData[aiDisplay[i]][5]) * 1;
			}
			document.getElementById("subTotal").innerHTML = formatNumber(subTotal);
		}
	} );
} );	

function save(getPar){
	rmb_val=document.getElementById("inp[rmb_val]");
	rmb_balance=document.getElementById("inp[rmb_balance]");
		
	if(convert(rmb_val.value) * 1 > convert(rmb_balance.value) * 1 ){
		alert("nilai tidak boleh lebih besar dari balance");
		rmb_val.focus();
		return false;
	}
		
	if(validation(document.form)){
		document.getElementById("form").submit();
		return true;
	}
	return false;
}

function getNomor(getPar){
	rmb_date=document.getElementById("rmb_date");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){
				document.getElementById("inp[rmb_no]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&rmb_date=" + rmb_date.value + getPar, true);
	xmlHttp.send(null);
	return false;
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
				document.getElementById("inp[parent_id]").value = data["parent_id"] == undefined ? "" : data["parent_id"];
				document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaPegawai]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];
				if(data["parent_id"] == null && nikPegawai.length > 0)
				alert("maaf, nik : \""+ nikPegawai + "\" belum terdaftar");
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getPlafon(getPar){	
	rmb_date=document.getElementById("rmb_date").value;	
	parent_id=document.getElementById("inp[parent_id]").value;
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				
				document.getElementById("inp[rmb_balance]").value = data["rmb_balance"] == undefined ? "" : data["rmb_balance"];
				document.getElementById("inp[rmb_limit]").value = data["rmb_limit"] == undefined ? "" : data["rmb_limit"];
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=plf&par[parent_id]=" + parent_id + "&par[rmb_date]=" + rmb_date + getPar, true);
	xmlHttp.send(null);
	return false;
	
}
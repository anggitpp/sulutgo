function nextDate(getPar){
	bulanJadwal=document.getElementById("par[bulanJadwal]");
	tahunJadwal=document.getElementById("par[tahunJadwal]");
	
	bulan = bulanJadwal.value == 12 ? 01 : bulanJadwal.value * 1 + 1;	
	tahun = bulanJadwal.value == 12 ? tahunJadwal.value * 1 + 1 : tahunJadwal.value;
	
	bulanJadwal.value = bulan > 9 ? bulan : "0" + bulan;
	tahunJadwal.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanJadwal=document.getElementById("par[bulanJadwal]");
	tahunJadwal=document.getElementById("par[tahunJadwal]");
	
	bulan = bulanJadwal.value == 01 ? 12 : bulanJadwal.value * 1 - 1;	
	tahun = bulanJadwal.value == 01 ? tahunJadwal.value * 1 - 1 : tahunJadwal.value;	
	
	bulanJadwal.value = bulan > 9 ? bulan : "0" + bulan;
	tahunJadwal.value = tahun;
	
	document.getElementById('form').submit();
}

function getJadwal(row, id, getPar){
	idShift=document.getElementById("det_[idShift][" + row + "][" + id + "]");		
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();			
			if(response){				
				var data = JSON.parse(response);
				document.getElementById("mulaiJadwal_" + row + "_" + id).value = data["mulaiShift"] == false ? "" : data["mulaiShift"];
				document.getElementById("selesaiJadwal_" + row + "_" + id).value = data["selesaiShift"] == false ? "" : data["selesaiShift"];
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[idShift]=" + idShift.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
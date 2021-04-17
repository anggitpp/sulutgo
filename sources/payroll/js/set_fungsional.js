function getGrade(getPar, flag){
	idPangkat=document.getElementById("inp[idPangkat]");
	idGrade=document.getElementById("inp[idGrade]");
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				var data = JSON.parse(response);
				
				for(var i=idGrade.options.length-1;i>=0;i--){
					idGrade.remove(i);
				}
			
				var opt = document.createElement("OPTION");
				opt.value = "";
				opt.text = "";
				idGrade.options.add(opt);				
				
				for (i = 0; i < data.length; ++i){					
					var opt = document.createElement("OPTION");
					var cat = data[i]["namaData"];
					opt.value = data[i]["kodeData"]
					opt.text = data[i]["namaData"];		
					if(opt.value) idGrade.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[idPangkat]=" + idPangkat.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
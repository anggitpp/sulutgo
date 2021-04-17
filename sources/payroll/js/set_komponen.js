function getKomponen(getPar){	
	tipeKomponen = document.getElementById("penerimaan").checked == true ? "t" : "p";
	idKomponen=document.getElementById("inp[idKomponen]");
	
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			response = xmlHttp.responseText.trim();
			if(response){
				var data = JSON.parse(response);
				
				for(var i=idKomponen.options.length-1;i>=0;i--){
					idKomponen.remove(i);
				}
			
				
				for (i = 0; i < data.length; ++i){					
					var opt = document.createElement("OPTION");					
					opt.value = data[i]["idKomponen"]
					opt.text = data[i]["namaKomponen"];		
					if(opt.value) idKomponen.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[tipeKomponen]=" + tipeKomponen + getPar, true);
	xmlHttp.send(null);
	return false;
}
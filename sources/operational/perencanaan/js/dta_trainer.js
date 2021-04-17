function getKota(getPar){
	idPropinsi = document.getElementById('inp[idPropinsi]');
	idKota = document.getElementById('inp[idKota]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=idKota.options.length-1; i>=0; i--){
				idKota.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				idKota.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) idKota.options.add(opt);
				}
				jQuery(".chosen-select").trigger("chosen:updated");
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kta&par[idPropinsi]="+ idPropinsi.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
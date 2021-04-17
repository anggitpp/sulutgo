function getAktifitas(getPar){
	idKategori = document.getElementById('inp[kategori_dokumen]');
	idRencana = document.getElementById('inp[kegiatan_dokumen]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=idRencana.options.length-1; i>=0; i--){
				idRencana.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				idRencana.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) idRencana.options.add(opt);
					  // jQuery("#inp\\[idRencana\\]").trigger("chosen:updated");
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=aktifitas&par[kategori_dokumen]="+ idKategori.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
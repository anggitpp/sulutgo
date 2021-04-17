function getAnakan(idInduk, idAnak, mode, getPar){
	kodeInduk = document.getElementById('inp['+idInduk+']');
	kodeAnak = document.getElementById('inp['+idAnak+']');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeAnak.options.length-1; i>=0; i--){
				kodeAnak.remove(i);
            }
            // alert(xmlHttp.responseText);
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeAnak.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeAnak.options.add(opt);
					jQuery("#inp\\["+idAnak+"\\]").trigger("chosen:updated");
				}
			}else{
                jQuery("#inp\\["+idAnak+"\\]").trigger("chosen:updated");
            }
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]="+mode+"&par[kodeInduk]="+ kodeInduk.value + getPar, true);
	xmlHttp.send(null);
	return false;
}
// function setPerguruan(data, getPar){
//     jQuery(".chosen-container").attr("style", "width:360px")
//     if(data > 614){
//         jQuery("#divPerguruan").fadeIn(500)
//     }else{
//         jQuery("#divPerguruan").fadeOut()
//     }
// }
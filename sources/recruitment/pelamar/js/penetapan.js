function getAnakan(idInduk, idAnak, mode, getPar){
	kodeInduk = document.getElementById('inp['+idInduk+']');
	kodeAnak = document.getElementById('inp['+idAnak+']');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeAnak.options.length-1; i>=0; i--){
				kodeAnak.remove(i);
			}
			// alert(mode);
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
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]="+mode+"&par[kodeInduk]="+ kodeInduk.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function changePegawai(element,getPar) {
    nik = document.getElementById("nik");
    mulaiKerja = document.getElementById("mulaiKerja");
    jabatan = document.getElementById("jabatan");
    pangkat = document.getElementById("pangkat");
    cat = document.getElementById("cat");

    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            if (xmlHttp.responseText) {
            var arr = xmlHttp.responseText.split("\t");
				nik.innerHTML = arr[0];
				mulaiKerja.innerHTML = arr[1];
				jabatan.innerHTML = arr[2];
				pangkat.innerHTML = arr[3];
				cat.innerHTML = arr[4];				
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=pegawai&par[idPegawai]=" + element +getPar, true);
    xmlHttp.send(null);
    return false;
}
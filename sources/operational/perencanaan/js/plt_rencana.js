function chk(getPar){
	if(validation(document.form)){
		if(document.getElementById("_total").value != "0"){
			document.getElementById("_submit").value = "t";
			document.getElementById("form").submit();
		}else{
			alert("Mohon untuk mengisi data pelaksanaan");
		}
	}
	return false;
}

function pelaksanaan(kodeVendor){	
	// if(document.getElementById("true").checked == true){
	// 	document.getElementById("iPegawai").style.visibility = "collapse";
	// 	document.getElementById("iVendor").style.visibility = "collapse";
	// 	document.getElementById("iTrainer").style.visibility = "visible";
	// 	document.getElementById("inp[idVendor]").value = kodeVendor;
	// 	jQuery("#inp\\[idVendor\\]").val(kodeVendor);	
	// 	document.getElementById("inp[idTrainer]").value = "";		
	// 	jQuery(".chosen-select").trigger("change");
	// }else{
	// 	document.getElementById("iPegawai").style.visibility = "collapse";
	// 	document.getElementById("iVendor").style.visibility = "visible";
	// 	document.getElementById("iTrainer").style.visibility = "visible";	
	// 	jQuery("#inp\\[idVendor\\]").val(kodeVendor);
	// 	document.getElementById("inp[idPegawai]").value = "";
	// }	
	
	// jQuery(".chosen-select").trigger("chosen:updated");
}

function getTrainer(par) {

    vendor_id       = jQuery("#inp\\[idVendor\\]").val()
    field_trainer   = jQuery("#inp\\[idTrainer\\]")
    
    jQuery.get('ajax.php?' + par + '&par[mode]=trainers&par[vendor_id]=' + vendor_id, function(datas) {

        data = JSON.parse(datas);

        field_trainer.empty();

        for (let key in data) {
            field_trainer.append('<option value="' + key + '">' + data[key] + '</option>')
        }

        jQuery(".chosen-select").trigger("chosen:updated")
    })
	
}

function getKodeSite(getPar){
    modul = document.getElementById('inp[modul_pelatihan]');
    kategori_level = document.getElementById('inp[kategori_level_pelatihan]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=kategori_level.options.length-1; i>=0; i--){
                kategori_level.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");                     
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "All Kategori Level";
                kategori_level.options.add(opt);
                for(var i=0; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];
                    if(opt.value) kategori_level.options.add(opt);
                    jQuery("#inp\\[kategori_level_pelatihan\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id&par[modul]="+ modul.value + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getKodeMenu(getPar){
    kategori_level = document.getElementById('inp[kategori_level_pelatihan]');
    menu = document.getElementById('inp[program_pelatihan]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=menu.options.length-1; i>=0; i--){
                menu.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");  
                console.log(arr);                   
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "All Program";
                menu.options.add(opt);
                for(var i=0; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];

                    if(opt.value) menu.options.add(opt);
                    jQuery("#inp\\[program_pelatihan\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id2&par[kategori_level]="+ kategori_level.value + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getIdProgram(getPar){
    program = document.getElementById('inp[program]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");  
                console.log(arr);                   
                var str = arr[0];
                //jQuery('#keterangan_program').val("Kode:"+ arr[0]);
                document.getElementById('keterangan_program').innerHTML = "<b style='font-weight:bolder; color:blue; cursor:pointer;'>Kode : " + str.replace("<br>", "</b><br>");;
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id3&par[program]="+ program.value + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getAtasan(getPar){
    atasan = document.getElementById('inp[atasan]');
    peserta = document.getElementById('inp[peserta]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=peserta.options.length-1; i>=0; i--){
                peserta.remove(i);
            }
            if(xmlHttp.responseText){
                var arr = xmlHttp.responseText.split("\n");  
                console.log(arr);                   
                var opt = document.createElement("OPTION");
                opt.value = "";     
                opt.text = "Pilih Pegawai";
                peserta.options.add(opt);
                for(var i=0; i<arr.length; i++){                            
                    var opt = document.createElement("OPTION");
                    var val = arr[i].split("\t");
                    opt.value = val[0];      
                    opt.text = val[1];

                    if(opt.value) peserta.options.add(opt);
                    jQuery("#inp\\[peserta\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id4&par[atasan]="+ atasan.value + getPar, true);
    xmlHttp.send(null);
    return false;
}
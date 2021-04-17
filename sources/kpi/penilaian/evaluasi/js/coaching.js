function getKodeSite(getPar){
    modul = document.getElementById('inp[modul]');
    kategori_level = document.getElementById('inp[kategori_level]');
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
                    jQuery("#inp\\[kategori_level\\]").trigger("chosen:updated");
                }
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id&par[modul]="+ modul.value + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getKodeMenu(getPar){
    kategori_level = document.getElementById('inp[kategori_level]');
    menu = document.getElementById('inp[program]');
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
                    jQuery("#inp\\[program\\]").trigger("chosen:updated");
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
    atasan = document.getElementById('inp[atasan]').value;
    peserta = document.getElementById('inp[peserta]');
    var xmlHttp = getXMLHttp();     
    xmlHttp.onreadystatechange = function(){    
        if(xmlHttp.readyState == 4 && xmlHttp.status==200){         
            for(var i=peserta.options.length-1; i>=0; i--){
                peserta.remove(i);
                jQuery("#inp\\[peserta\\]").trigger("chosen:updated");

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
                    jQuery("#inp\\[peserta\\]").trigger("chosen:updated");       }
                              
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=id4&par[atasan]="+ atasan + getPar, true);
    xmlHttp.send(null);
    return false;
}
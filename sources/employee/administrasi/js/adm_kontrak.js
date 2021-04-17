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
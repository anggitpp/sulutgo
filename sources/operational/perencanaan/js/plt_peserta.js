function getField(getPar) {
    id = document.getElementById("inp[idPegawai]").value;
    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            response = xmlHttp.responseText.trim();
            if (response) {
                var dta = response.split("\t");
                document.getElementById("inp[jabatanPeserta]").value = dta[0];
                document.getElementById("inp[posisiPeserta]").value = dta[1];
                document.getElementById("inp[umurPeserta]").value = dta[2];
            }
        }
    }

    xmlHttp.open("GET", "ajax.php?par[mode]=get&id=" + id + getPar, true);
    xmlHttp.send(null);
    return false;
}

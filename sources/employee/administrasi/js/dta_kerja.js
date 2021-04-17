function getData(element, getPar) {
    nik = document.getElementById("nik");
    tanggal = document.getElementById("tanggal");
    jabatan = document.getElementById("jabatan");
    pangkat = document.getElementById("pangkat");
    statusPegawai = document.getElementById("status");

    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            if (xmlHttp.responseText) {
                var arr = JSON.parse(xmlHttp.responseText);
                nik.innerHTML = arr[0];
                statusPegawai.innerHTML = arr[1];
                tanggal.innerHTML = arr[2];
                pangkat.innerHTML = arr[3];
                jabatan.innerHTML = arr[4];
            }
        }
    }
    
    xmlHttp.open("GET", "ajax.php?par[mode]=getData&par[idPegawai]=" + element + getPar, true);
    xmlHttp.send(null);
    return false;
}

function setProses(getPar) {
    formInput = document.getElementById("formInput");
    prosesImg = document.getElementById("prosesImg");
    progresBar = document.getElementById("progresBar");

    prosesImg.style.display = "block";
    fileData = document.getElementById('fileData').files[0];

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            response = xmlHttp.responseText.trim();
            if (response) {
                if (response.substring(0, 8) == "fileData") {
                    fileData = response.replace("fileData", "");
                    setData(fileData, getPar, 6);
                    formInput.style.display = "none";
                    prosesImg.style.display = "none";
                    progresBar.style.display = "block";
                } else {
                    prosesImg.style.display = "none";
                    alert(response);
                }
            } else {
                prosesImg.style.display = "none";
                alert("file harus dalam format .xls atau .xlsx");
            }
        }
    }

    xmlHttp.open("POST", "ajax.php?par[mode]=tab" + getPar, true);
    xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
    var formData = new FormData();

    formData.append("fileData", fileData);

    xmlHttp.send(formData);
}

function setData(fileData, getPar, rowData) {
    persenBar = document.getElementById("persenBar");
    progresCnt = document.getElementById("progresCnt");
    progresRes = document.getElementById("progresRes");

    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            response = xmlHttp.responseText.trim();
            if (response) {
                dta = response.split("\t");
                persenBar.style.width = dta[0] + "%";
                progresCnt.innerHTML = dta[1];
                setData(fileData, getPar, (rowData * 1 + 1));
            } else {
                endProses(getPar);
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=dat&par[fileData]=" + fileData + "&par[rowData]=" + rowData + getPar, true);
    xmlHttp.send(null);
    return false;
}

function endProses(getPar) {
    progresEnd = document.getElementById("progresEnd");
    progresRes = document.getElementById("progresRes");
    persenBar = document.getElementById("persenBar");

    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            response = xmlHttp.responseText.trim();
            if (response) {
                persenBar.className = "value bluebar";
                progresRes.innerHTML = response;
                progresEnd.style.display = "block";
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=end" + getPar, true);
    xmlHttp.send(null);
    return false;
}
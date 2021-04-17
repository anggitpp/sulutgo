function setPendidikan(data) {
    if (data > 614) {
        jQuery("#divFakultas").fadeIn(500)
        jQuery("#divJurusan").fadeIn(500)
    } else {
        jQuery("#divFakultas").fadeOut()
        jQuery("#divJurusan").fadeOut()
    }
}

function getPosisi(element, getPar) {
    edu = document.getElementById("inp[edu_id]");
    edu2 = document.getElementById("inp[edu_id2]");
    fac1 = document.getElementById("inp[edu_fac_id]");
    dept1 = document.getElementById("inp[edu_dept_id]");
    fac2 = document.getElementById("inp[edu_fac_id2]");
    dept2 = document.getElementById("inp[edu_dept_id2]");
    fac3 = document.getElementById("inp[edu_fac_id3]");
    dept3 = document.getElementById("inp[edu_dept_id3]");
    usia = document.getElementById("inp[age_from]");
    usia2 = document.getElementById("inp[age_to]");
    gender = document.getElementById("inp[male]");
    gender2 = document.getElementById("inp[female]");
    empStat = document.getElementById("inp[emp_sta]");
    characters = document.getElementById("inp[characters]");
    expertise = document.getElementById("inp[expertise]");
    job_desk = document.getElementById("inp[job_desk]");
    abilities = document.getElementById("inp[abilities]");
    comliterates = document.getElementById("inp[comliterates]");
    language = document.getElementById("inp[language]");
    jobdesc = document.getElementById("jobdesc");

    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            if (xmlHttp.responseText) {
                arr = JSON.parse(xmlHttp.responseText);
                if (arr.edu_id2 > 614) {
                    jQuery("#divFakultas").fadeIn(500)
                    jQuery("#divJurusan").fadeIn(500)
                } else {
                    jQuery("#divFakultas").fadeOut()
                    jQuery("#divJurusan").fadeOut()
                }
                jQuery(".chosen-container").attr("style", "width:300px");
                edu.value = arr.edu_id;
                // edu2.value = arr.edu_id2;
                fac1.value = arr.edu_fac_id;
                fac2.value = arr.edu_fac_id2;
                fac3.value = arr.edu_fac_id3;
                dept1.value = arr.edu_dept_id;
                dept2.value = arr.edu_dept_id2;
                dept3.value = arr.edu_dept_id3;
                jQuery("#inp\\[edu_fac_id\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_fac_id2\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_fac_id3\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_dept_id\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_dept_id2\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_dept_id3\\]").trigger("chosen:updated");
                // document.getElementById("male").checked = true;
                // document.getElementById("female").checked = true;
                // usia.value = arr.age_from;
                // usia2.value = arr.age_to;
                empStat.value = arr.emp_sta;
                characters.value = arr.characters;
                expertise.value = arr.expertise;
                job_desk.value = arr.job_desk;
                abilities.value = arr.abilities;
                comliterates.value = arr.comliterates;
                language.value = arr.language;
                jQuery("#inp\\[emp_sta\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_id\\]").trigger("chosen:updated");
                jQuery("#inp\\[edu_id2\\]").trigger("chosen:updated");
                jobdesc.innerHTML = arr.jobdesc;
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=getPosisi&par[idPosisi]=" + element + getPar, true);
    xmlHttp.send(null);
    return false;
}

function getPegawai(id, getPar) {
    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            if (xmlHttp.responseText) {
                var arr = xmlHttp.responseText.split("\t");
                document.getElementById('jabatan').value = arr[0];
                document.getElementById('divisi').value = arr[1];
                document.getElementById('departemen').value = arr[2];
            }
        }
    }
    xmlHttp.open("GET", "ajax.php?par[mode]=getPegawai&par[idPegawai]=" + id + getPar, true);
    xmlHttp.send(null);
    return false;
}

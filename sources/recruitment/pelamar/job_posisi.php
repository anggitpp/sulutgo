<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]' order by namaData");

    return implode("\n", $data);
}

function hapus()
{
    global $par;

    $sql = "delete from rec_job_posisi where id_posisi='$par[id_posisi]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,id_posisi") . "';</script>";
}

function tambah()
{
    global $inp, $par, $cUsername;

    repField();

    $id_posisi = getField("select id_posisi from rec_job_posisi order by id_posisi desc limit 1") + 1;

    $inp[male] = empty($inp[male]) ? 0 : 1;
    $inp[female] = empty($inp[female]) ? 0 : 1;

    $sql = "insert into rec_job_posisi set id_posisi = '$id_posisi', subject = '$inp[subject]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', edu_id = '$inp[edu_id]', edu_id2 = '$inp[edu_id2]', edu_fac_id = '$inp[edu_fac_id]', edu_fac_id2 = '$inp[edu_fac_id2]', edu_fac_id3 = '$inp[edu_fac_id3]', edu_dept_id = '$inp[edu_dept_id]', edu_dept_id2 = '$inp[edu_dept_id2]', edu_dept_id3 = '$inp[edu_dept_id3]', emp_sta = '$inp[emp_sta]', characters = '$inp[characters]', expertise = '$inp[expertise]', job_desk = '$inp[job_desk]', abilites = '$inp[abilites]', comliterates = '$inp[comliterates]', language = '$inp[language]', remark = '$inp[remark]', jobdesc = '$inp[jobdesc]', create_by = '$cUsername', create_date = '" . date('Y-m-d H:i:s') . "'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id_posisi]=$id_posisi" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUsername;

    repField();

    $inp[male] = empty($inp[male]) ? 0 : 1;
    $inp[female] = empty($inp[female]) ? 0 : 1;

    $sql = "update rec_job_posisi set subject = '$inp[subject]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', edu_id = '$inp[edu_id]', edu_id2 = '$inp[edu_id2]', edu_fac_id = '$inp[edu_fac_id]', edu_fac_id2 = '$inp[edu_fac_id2]', edu_fac_id3 = '$inp[edu_fac_id3]', edu_dept_id = '$inp[edu_dept_id]', edu_dept_id2 = '$inp[edu_dept_id2]', edu_dept_id3 = '$inp[edu_dept_id3]', emp_sta = '$inp[emp_sta]', characters = '$inp[characters]', expertise = '$inp[expertise]', job_desk = '$inp[job_desk]', abilites = '$inp[abilites]', comliterates = '$inp[comliterates]', language = '$inp[language]', remark = '$inp[remark]', jobdesc = '$inp[jobdesc]', create_by = '$cUsername', create_date = '" . date('Y-m-d H:i:s') . "' where id_posisi = '$par[id_posisi]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('R11', 'S04')");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" style="width:250px;" class="mediuminput" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="pos_r" style="float:right;">
                <a href="?par[mode]=add<?= getPar($par, "mode,kodeAktifitas") ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
        </form>

        <br clear="all" />

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Posisi</th>
                    <th width="250">Min. Pendidikan</th>
                    <th width="150">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($par[filterData]))
                        $filter = " where subject LIKE '%$par[filterData]%' ";

                    $sql = "SELECT id_posisi, subject, edu_id, edu_id2, emp_sta from rec_job_posisi $filter";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_array($res)) {
                        $no++;

                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"?par[mode]=edit&par[id_posisi]=$r[id_posisi]" . getPar($par, "mode,id_posisi") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id_posisi]=$r[id_posisi]" . getPar($par, "mode,id_posisi") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td align="right"><?= $no ?>.</td>
                        <td><?= $r[subject] ?></td>
                        <td><?= $arrMaster[$r[edu_id]] . " s/d " . $arrMaster[$r[edu_id2]] ?></td>
                        <td><?= $arrMaster[$r[emp_sta]] ?></td>
                        <td align="center"><?= $control ?></td>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
<?php
}

function form()
{
    global $s, $par, $arrTitle, $arrParameter, $ui;

    $sql = "SELECT * FROM rec_job_posisi WHERE id_posisi='$par[id_posisi]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $stylePerguruan = $r[edu_id2] > 614 ? "style=\"display:block\"" : "style=\"display:none\"";

    setValidation("is_null", "inp[subject]", "you must fill Job Posisi");
    setValidation("is_null", "inp[edu_id]", "you must fill Pendidikan dari");
    setValidation("is_null", "inp[edu_id2]", "you must fill Pendidikan sampai");
    setValidation("is_null", "inp[emp_sta]", "you must fill Status Pegawai");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id=" name=" form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p style="position:absolute;top:5px;right:5px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
            </p>
            <div class="widgetbox">
                <div class="title" style="margin-bottom:0px;">
                    <h3>POSISI & PERSYARATAN</h3>
                </div>
            </div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%">
                        <p>
                            <?= $ui->createComboData("Min. Pendidikan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R11' and statusData = 't' order by urutanData", "id", "description", "inp[edu_id]", $r[edu_id], "onchange=\"setPendidikan(this.value);\"", "300px", "t", "t", "t") ?>
                        </p>
                        <p>
                            <?= $ui->createComboData("Status Pegawai", "select kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='" . $arrParameter[5] . "' and statusData = 't' order by urutanData", "id", "description", "inp[emp_sta]", $r[emp_sta], "", "300px", "t", "t", "t") ?>
                        </p>
                        <div id="divFakultas" <?= $stylePerguruan ?>>
                            <p>
                                <?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id]", $r[edu_fac_id], "onchange=\"getSub('edu_fac_id', 'edu_dept_id', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
                            </p>
                            <p>
                                <?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id2]", $r[edu_fac_id2], "onchange=\"getSub('edu_fac_id2', 'edu_dept_id2', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
                            </p>
                            <p>
                                <?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id3]", $r[edu_fac_id3], "onchange=\"getSub('edu_fac_id3', 'edu_dept_id3', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
                            </p>
                        </div>
                    </td>
                    <td style="width: 50%">
                        <p><?= $ui->createField("Job Posisi", "inp[subject]", $r[subject]) ?> </p>
                        <div id="divJurusan" <?= $stylePerguruan ?>>
                            <p>
                                <?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id]", $r[edu_dept_id], "", "300px", "t") ?>
                            </p>
                            <p>
                                <?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id2]", $r[edu_dept_id2], "", "300px", "t") ?>
                            </p>
                            <p>
                                <?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id3]", $r[edu_dept_id3], "", "300px", "t") ?>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
            
            <br clear="all"/>
            
            <div class="widgetbox">
                <div class="title" style="margin-bottom:0px;"><h3>TASK/RESPONSIBILITIES & JOB DESCRIPTION</h3></div>
            </div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 100%">
                        <label style="width: 25%;text-align: left;">RESPONSIBILITIES & JOB DESCRIPTION</label>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%">
                        <textarea id="inp[jobdesc]" name="inp[jobdesc]" rows="3" cols="50" class="longinput" style="height:150px; width:100%;"><?= $r[jobdesc] ?></textarea>
                    </td>
                </tr>
            </table>

            <br clear="all"/>
            
            <div class="widgetbox">
                <div class="title">
                    <h3>INFO TAMBAHAN</h3>
                </div>
            </div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 50%">
                        <p>
                            <label style="width: 50%;text-align: left;">Karakter Pribadi</label>
                            <textarea id="inp[characters]" name="inp[characters]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[characters] ?></textarea>
                        </p>
                        <p>
                            <label style="width: 50%;text-align: left;">Uraian Tugas Utama</label>
                            <textarea id="inp[job_desk]" name="inp[job_desk]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[job_desk] ?></textarea>
                        </p>
                        <p>
                            <label style="width: 50%;text-align: left;">Keahilan Komputer</label>
                            <textarea id="inp[comliterates]" name="inp[comliterates]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[comliterates] ?></textarea>
                        </p>
                    </td>
                    <td style="width: 50%">
                        <p>    
                            <label style="width: 50%;text-align: left;">Keahlian Khusus</label>
                            <textarea id="inp[expertise]" name="inp[expertise]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[expertise] ?></textarea>
                        </p>                        
                        <p>
                            <label style="width: 50%;text-align: left;">Kemampuan Tambahan</label>
                            <textarea id="inp[abilities]" name="inp[abilities]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[abilites] ?></textarea>
                        </p>
                        <p>
                            <label style="width: 50%;text-align: left;">Kemampuan Bahasa</label>
                            <textarea id="inp[language]" name="inp[language]" rows="3" cols="50" class="longinput" style="height:50px; width:500px;"><?= $r[language] ?></textarea>
                        </p>
                    </td>
                </tr>
            </table>
            <div class="widgetbox">
                <div class="title" style="margin-bottom:0px;"><h3>NOTE</h3></div>
            </div>
            <table style="width: 100%">
                <tr>
                    <td style="width: 100%">
                        <label style="width: 25%;text-align: left;">BOD's NOTE</label>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%">
                        <textarea id="inp[remark]" name="inp[remark]" rows="3" cols="50" class="longinput" style="height:50px; width:100%;"><?= $r[remark] ?></textarea>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;

    switch ($par[mode]) {
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "del":
            $text = hapus();
            break;
        case "subData":
            $text = subData();
            break;
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        default:
            $text = lihat();
            break;
    }

    return $text;
}
?>
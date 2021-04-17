<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/recruit/mapping/";

function tambah()
{
    global $par, $det, $cUser;

    repField();

    if (is_array($det)) {
        while (list($idPosisi) = each($det)) {
            $id = getLastId("rec_job_mapping", "id");
            $sql = "INSERT INTO rec_job_mapping set id = '$id', id_posisi = '$idPosisi', id_pelamar = '$par[id]', create_date = '" . date('Y-m-d H:i:s') . "', create_by = '$cUser'";
            db($sql);
        }
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function hapus()
{
    global $par;

    $sql = "delete from rec_job_mapping where id='$par[idPosisi]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?par[mode]=listMapping" . getPar($par, "mode") . "';</script>";
}


function lihat()
{
    global $s, $par, $arrTitle;

    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];
    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");
    $arrMapping = arrayQuery("SELECT id_pelamar, count(id_posisi) FROM rec_job_mapping group by id_pelamar");

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
                <?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"") ?>
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">NAMA</th>
                    <th width="100">Gender</th>
                    <th width="100">Umur</th>
                    <th width="150">Pendidikan</th>
                    <th width="150">Jurusan</th>
                    <th width="150">Job Posisi</th>
                    <th width="50">Mapping</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "WHERE year(t1.tgl_input) = '$par[tahunData]'";
                    if (!empty($par[filterData]))
                        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' or lower(t2.subject) LIKE '%" . strtolower($par[filterData]) . "%') ";

                    $sql = "SELECT t1.id, t1.name, t1.gender, t2.subject, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT concat(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 WHERE x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduInfo FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi $filter order by name";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;

                        $jumlahMapping = empty($arrMapping[$r[id]]) ? 0 : $arrMapping[$r[id]];

                        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";
                        list($r[eduType], $r[eduFac]) = explode("\t", $r[eduInfo]);
                        $mapping = "<a href=\"?par[mode]=listMapping&par[id]=$r[id]" . getPar($par, "mode,id") . "\">$jumlahMapping</a>";
                        ?>
                    <tr>
                        <td align="right"><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td><?= $r[gender] ?></td>
                        <td><?= $r[umur] ?></td>
                        <td><?= $arrMaster[$r[eduType]] ?></td>
                        <td><?= $arrMaster[$r[eduFac]] ?></td>
                        <td><?= $r[subject] ?></td>
                        <td align="center"><?= $mapping ?></td>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <?php
        if ($par[mode] == "xls") {
            xls();
            echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
        }
        ?>
<?php
}

function listMapping()
{
    global $s, $par, $arrTitle, $menuAccess;

    $par[tahunKebutuhan] = empty($par[tahunKebutuhan]) ? date('Y') : $par[tahunKebutuhan];

    $_SESSION["curr_rec_id"] = $par[id];

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'S04') and statusData = 't'");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <?php include 'tmpl/rec_header_basic.php'; ?>

        <form action="" method="post" id="form" class="stdform">
            <div id="pos_r" style="float:right;">

            </div>
        </form>

        <br clear="all" />

        <div style="display: flex; align-items: end; margin-bottom: 1rem; padding-bottom: .5rem; border-bottom: 1px solid #c0c0c0;">
            <div style="flex: 1;">
                <h4>DATA PELAMAR</h4>
            </div>
            <div>
                <a href="#" class="btn btn1 btn_document" onclick="openBox('popup.php?par[mode]=listPosisi<?= getPar($par, 'mode') ?>', 800, 500)"><span>Tambah Data</span></a>
            </div>
        </div>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Job Posisi</th>
                    <th width="200">Min. Pendidikan</th>
                    <th width="150">Status Pegawai</th>
                    <?php if (!empty($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT t1.id, t2.subject, t2.edu_id, t2.emp_sta FROM rec_job_mapping t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi WHERE t1.id_pelamar = '$par[id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_array($res)) {
                        $no++;

                        if (!empty($menuAccess[$s]["delete"]))
                            $control = "<td align=\"center\"><a href=\"?par[mode]=del&par[idPosisi]=$r[id]" . getPar($par, "mode,idPosisi") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";
                        ?>
                    <tr>
                        <td align="right"><?= $no ?>.</td>
                        <td><?= $r[subject] ?></td>
                        <td><?= $arrMaster[$r[edu_id]] ?></td>
                        <td><?= $arrMaster[$r[emp_sta]] ?></td>
                        <?= $control ?>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
<?php
}

function listPosisi()
{
    global $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'S04') and statusData = 't'");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Data Posisi</h1>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            
            <p style="position: absolute; top: .5rem; right: 1rem;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
            </p>

            <br clear="all" />
            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dynscroll_x">
                <thead>
                    <tr>
                        <th width="20">No.</th>
                        <th width="*">Job Posisi</th>
                        <th width="200">Min. Pendidikan</th>
                        <th width="150">Status Pegawai</th>
                        <th width="50">Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $cekExist = getField("SELECT GROUP_CONCAT(id_posisi) FROM rec_job_mapping WHERE id_pelamar = '$par[id]'");

                        if (!empty($cekExist))
                            $filter = " WHERE id_posisi NOT IN ($cekExist)";

                        $sql = "SELECT id_posisi, subject, edu_id, emp_sta FROM rec_job_posisi $filter order by subject";
                        $res = db($sql);
                        $no = 0;
                        while ($r = mysql_fetch_array($res)) {
                            $no++;
                            $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";
                            ?>
                        <tr>
                            <td align="right"><?= $no ?>.</td>
                            <td><?= $r[subject] ?></td>
                            <td><?= $arrMaster[$r[edu_id]] ?></td>
                            <td><?= $arrMaster[$r[emp_sta]] ?></td>
                            <td align="center"><input type="checkbox" id="det[<?= $r[id_posisi] ?>]" name="det[<?= $r[id_posisi] ?>]" value="<?= $r[id_posisi] ?>" /></td>
                        </tr>
                    <?php
                        }
                        ?>
                </tbody>
            </table>
    </div>
<?php
}

function xls()
{
    global $s, $arrTitle, $fExport, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");
    $arrMapping = arrayQuery("SELECT id_pelamar, count(id_posisi) FROM rec_job_mapping group by id_pelamar");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no",  "nama", "gender", "umur", "pendidikan", "jurusan", "job posisi", "mapping");

    $filter = "WHERE year(t1.tgl_input) = '$par[tahunData]'";
    if (!empty($par[filterData]))
        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' or lower(t2.subject) LIKE '%" . strtolower($par[filterData]) . "%') ";

    $sql = "SELECT t1.id, t1.name, t1.gender, t2.subject, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT concat(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 WHERE x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduInfo FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi $filter order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;

        $jumlahMapping = empty($arrMapping[$r[id]]) ? 0 : $arrMapping[$r[id]];

        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";
        list($r[eduType], $r[eduFac]) = explode("\t", $r[eduInfo]);
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[gender] . "\t left",
            $r[umur] . "\t left",
            $arrMaster[$r[eduType]] . "\t left",
            $arrMaster[$r[eduFac]] . "\t left",
            $r[subject] . "\t left",
            $jumlahMapping . "\t center"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par)
{
    global $_submit;

    switch ($par[mode]) {
        case "del":
            $text = hapus();
            break;
        case "listMapping":
            $text =  listMapping();
            break;
        case "listPosisi":
            $text = empty($_submit) ? listPosisi() : tambah();
            break;
        default:
            $text = lihat();
            break;
    }

    return $text;
}
?>
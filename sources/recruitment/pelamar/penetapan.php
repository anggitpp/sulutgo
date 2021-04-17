<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fSK = "files/recruit/sk/";

function upload($id)
{
    global $fSK;
    $fileUpload = $_FILES["sk_file"]["tmp_name"];
    $fileUpload_name = $_FILES["sk_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fSK);
        $sk_file = "sk-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fSK, $fileUpload_name, $sk_file);
    }
    if (empty($sk_file)) $sk_file = getField("select sk_file from rec_applicant_placement where id='$id'");

    return $sk_file;
}

function hapusFile()
{
    global $par, $fSK;

    $eduFilename = getField("select sk_file from rec_applicant_placement where id='$par[id]'");
    if (file_exists($fSK . $eduFilename) and $eduFilename != "") unlink($fSK . $eduFilename);

    $sql = "update rec_applicant_placement set sk_file = '' where id='$par[id]'";
    db($sql);

    echo "<script>alert('FILE BERHASIL DIHAPUS');window.location='?par[mode]=id" . getPar($par, "mode,id") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $id = getLastId("rec_applicant_placement", "id");

    $cekExist = getField("SELECT id FROM rec_applicant_placement where parent_id = '$par[id]'");
    if ($cekExist) {
        $id = $cekExist;
        $inp[sk_file] = upload($id);
        $sql = "UPDATE rec_applicant_placement set sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', sk_file = '$inp[sk_file]', cat = '$inp[cat]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$cekExist'";
        db($sql);
    } else {
        $inp[sk_file] = upload($id);
        $sql = "INSERT INTO rec_applicant_placement set id = '$id', parent_id = '$par[id]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', sk_file = '$inp[sk_file]', cat = '$inp[cat]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function form()
{
    global $s, $par, $arrTitle, $ui;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $arrPosisi = arrayQuery("select t1.id, t2.subject from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi");

    $sql_ = "select name, id_posisi, birth_date, birth_place, religion, id_pelamar from rec_applicant where id='$par[id]'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_array($res_);

    $sql = "select * from rec_applicant_placement where parent_id = '$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    //QUERY COMBO DATA
    $queryCat = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S04' order by urutanData";

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p style="position:absolute;top:5px;right:5px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA PELAMAR </legend>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createSpan("Nama Pegawai", $r_[name]) ?></p>
                            <p><?= $ui->createSpan("Posisi", $arrPosisi[$r_[id_posisi]]) ?></p>
                            <p><?= $ui->createSpan("Tanggal Lahir", getTanggal($r_[birth_date])) ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createSpan("ID Pelamar", $r_[id_pelamar]) ?></p>
                            <p><?= $ui->createSpan("Agama", $arrMaster[$r_[religion]]) ?></p>
                            <p><?= $ui->createSpan("Tempat Lahir", $arrMaster[$r_[birth_place]]) ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br clear="all" />

            <fieldset>
                <legend> DATA SK </legend>
                <p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no]) ?></p>
                <p><?= $ui->createField("Tanggal SK", "inp[sk_date]", getTanggal($r[sk_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createComboData("Status Pegawai", $queryCat, "id", "description", "inp[cat]", $r[cat], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Selesai", "inp[end_date]", getTanggal($r[end_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createFile("File", "sk_file", $r[sk_file], "", "", "recSK", $r[id], "delFile") ?></p>
                <p><?= $ui->createField("Keterangan", "inp[remark]", $r[remark]) ?></p>
            </fieldset>
        </form>
    </div>
    </fieldset>
    </div>
    <style>
        .chosen-container {
            min-width: 250px;
        }
    </style>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle;

    $par[tahunData] = empty($par[tahunData]) ? date("Y") : $par[tahunData];

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN('R11', 'R13')");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" action="" method="post" class="stdform">
            <p style="position:absolute;top:5px;right:15px"><?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"") ?></p>
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" name="par[filterData]" placeholder="Search.." value="<?= $par[filterData] ?>" style="width:200px;" />
                    <?= comboData("SELECT t1.id id, concat(t1.subject, ' - ', t2.subject) description from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi order by t1.subject", "id", "description", "par[idRencana]", "All", $par[idRencana], "onchange=\"document.getElementById('form').submit();\"", "310px;", "chosen-select") ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="pos_r" style="float:right;">
                <a href="?par[mode]=xls<?= getPar($par, "mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">NAMA</th>
                    <th width="80">ID Pelamar</th>
                    <th width="80">Umur</th>
                    <th width="200">Posisi</th>
                    <th width="100">Pendidikan</th>
                    <th width="200">Jurusan</th>
                    <th width="100">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "where year(t1.tgl_input) = '$par[tahunData]'";
                    if (!empty($par[filterData]))
                        $filter .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($par[filterData])) . "%'";
                    if (!empty($par[idRencana]))
                        $filter .= " and t1.id_posisi = '" . $par[idRencana] . "'";
                    $sql = "SELECT t1.id, t1.name, t1.id_pelamar, t2.subject, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT CONCAT(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 where x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) dataPendidikan, t1.emp_id, t2.subject, t4.id as idPenetapan FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi join rec_selection_appl t3 on (t1.id = t3.parent_id AND t3.phase_id = '607' AND t3.sel_status = '601') left join rec_applicant_placement t4 on t1.id = t4.parent_id $filter";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        list($r[eduType], $r[eduDept]) = explode("\t", $r[dataPendidikan]);
                        $no++;
                        $tahapan = empty($r[idPenetapan]) ? "<img src=\"styles/images/f.png\">" : "<img src=\"styles/images/t.png\">";
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td align="center"><?= $r[id_pelamar] ?></td>
                        <td align="right"><?= $r[umur] ?></td>
                        <td><?= $r[subject] ?></td>
                        <td><?= $arrMaster[$r[eduType]] ?></td>
                        <td><?= $arrMaster[$r[eduDept]] ?></td>
                        <td align="center"><a href="?par[mode]=edit&par[id]=<?= $r[id] . getPar($par, "mode, id") ?>"><?= $tahapan ?></a></td>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <script>
        function showFilter() {
            jQuery('#form_filter').show('slow');
            jQuery('#sFilter').hide();
            jQuery('#hFilter').show();
        }

        function hideFilter() {
            jQuery('#form_filter').hide('slow');
            jQuery('#sFilter').show();
            jQuery('#hFilter').hide();
        }
    </script>
    <?php
        if ($par[mode] == "xls") {
            xls();
            echo "<iframe src=\"download.php?d=exp&f=exp-PENETAPAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
        }
        ?>
<?php
}

function xls()
{
    global $fExport, $par;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");

    $direktori = $fExport;
    $namaFile = "exp-PENETAPAN.xls";
    $judul = "PENETAPAN";

    $field = array("no", "nama", "id pelamar", "umur", "posisi", "pendidikan", "jurusan", "tahapan");

    $filter = "where year(t1.tgl_input) = '$par[tahunData]'";
    if (!empty($par[filterData]))
        $filter .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($par[filterData])) . "%'";
    if (!empty($par[idRencana]))
        $filter .= " and t1.id_rencana = '" . $par[idRencana] . "'";

    $sql = "SELECT t1.id, t1.name, t1.id_pelamar, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT CONCAT(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 where x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) dataPendidikan, t1.emp_id, t2.subject FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi join rec_selection_appl t3 on (t1.id = t3.parent_id AND t3.phase_id = '607' AND t3.sel_status = '601') $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        list($r[eduType], $r[eduDept]) = explode("\t", $r[dataPendidikan]);
        $no++;
        $tahapan = empty($r[emp_id]) ? "Tidak Lulus" : "Lulus";
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[id_pelamar] . "\t center",
            $r[umur] . "\t left",
            $r[subject] . "\t left",
            $arrMaster[$r[eduType]] . "\t left",
            $arrMaster[$r[eduDept]] . "\t left",
            $tahapan . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "subData":
            $text = subData();
            break;
        case "divisi":
            $text = divisi();
            break;
        case "departemen":
            $text = departemen();
            break;
        case "delFile":
            $text = hapusFile();
            break;
        case "unit":
            $text = unit();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
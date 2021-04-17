<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/recruit/selection/";

function upload($id)
{
    global $fFile, $par;
    $fileUpload = $_FILES["sel_filename"]["tmp_name"];
    $fileUpload_name = $_FILES["sel_filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $sel_filename = "sel-" . $par[idPhase] . " - " . $id . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $sel_filename);
    }
    if (empty($sel_filename)) $sel_filename = getField("select sel_filename from rec_selection_appl where parent_id='$id' AND phase_id = '$par[idPhase]'");

    return $sel_filename;
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[sel_date] = setTanggal($inp[sel_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $file = upload($par[id]);

    $sql = "update rec_selection_appl set sel_date = '$inp[sel_date]', sel_time = '$inp[sel_time]', sel_result = '$inp[sel_result]', sel_cancel = '$inp[sel_cancel]', sel_status = '$inp[sel_status]', sel_remark = '$inp[sel_remark]', sel_filename = '$file', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$par[id]' AND phase_id = '$par[idPhase]'";
    db($sql);

    if($inp[sel_status] == 600){
        $sql = "UPDATE rec_selection_appl SET sel_status = '600' WHERE parent_id = '$par[id]' AND phase_id > '$par[idPhase]'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode, id, idPosisi, idPhase") . "';</script>";
}


function hapusFile()
{
    global $par, $fFile;
    $sel_filename = getField("select sel_filename from rec_selection_appl where parent_id='$par[id]' AND phase_id = '$par[idPhase]'");
    if (file_exists($fFile . $sel_filename) and $sel_filename != "") unlink($fFile . $sel_filename);

    $sql = "update rec_selection_appl set sel_filename='' where parent_id='$par[id]' AND phase_id = '$par[idPhase]'";
    db($sql);
    echo "<script>alert('FILE BERHASIL DIHAPUS');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function lihat()
{
    global $s, $par, $arrTitle, $arrUrutan;

    $par[tahunData] = empty($par[tahunData]) ? date("Y") : $par[tahunData];
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(); ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="" method="post" id="form" class="stdform">
            <p style="position:absolute;top:5px;right:15px"><?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"") ?></p>
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <?= comboData("SELECT t1.id id, concat(t1.subject, ' - ', t2.subject) description from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi order by t1.subject", "id", "description", "par[idRencana]", "All", $par[idRencana], "onchange=\"document.getElementById('form').submit();\"", "310px;", "chosen-select") ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="pos_r" style="float:right;">
                <?= comboData("SELECT kodeData id, namaData description from mst_data where statusData='t' and kodeCategory='R07' order by urutanData", "id", "description", "par[idStatus]", "----", $par[idStatus], "onchange=\"document.getElementById('form').submit();\"", "110px") ?>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Nama</th>
                    <th width="100">Gender</th>
                    <th width="100">Umur</th>
                    <th width="100">Pendidikan</th>
                    <th width="100">No.Telp</th>
                    <th width="100">Tanggal</th>
                    <!-- <th width="50">Hasil</th> -->
                    <th width="50">Tahapan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $kodeMaster = getField("select kodeData from mst_data where kodeCategory = 'R09' and urutanData = '" . $arrUrutan[$s] . "'-1");
                    $tahapanSebelumnya = $kodeMaster - 1;

                    $filter = "WHERE t1.administrasi = 't'";
                    if ($kodeMaster != 602)
                        $filter = " AND (SELECT id FROM rec_selection_appl WHERE phase_id = '$tahapanSebelumnya' AND sel_status = '601' AND parent_id = t1.id)";
                    if (!empty($par[tahunData]))
                        $filter .= " and year(t1.tgl_input)= '$par[tahunData]'";
                    if (!empty($par[idStatus]))
                        $filter .= " and t2.sel_status = '" . $par[idStatus] . "'";
                    if (!empty($par[idRencana]))
                        $filter .= " and t1.id_rencana = '" . $par[idRencana] . "'";
                    if (!empty($par[filterData]))
                        $filter .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($par[filterData])) . "%'";

                    $sql = "SELECT t1.id, t1.id_posisi, t1.name, t1.gender, t1.cell_no, t2.sel_date, t2.sel_result, t2.sel_status, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn ') umur, (SELECT x2.namaData FROM rec_applicant_edu x1 JOIN mst_data x2 ON x1.edu_type=x2.kodeData WHERE x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduName from rec_applicant t1 join rec_selection_appl t2 on (t1.id = t2.parent_id AND t2.phase_id = $kodeMaster) $filter";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Wanita";
                        switch ($r[sel_status]) {
                            case '601':
                                $tahapan = "<img src=\"styles/images/t.png\" title=\"Disarankan\">";
                                break;
                            case '600':
                                $tahapan = "<img src=\"styles/images/f.png\" title=\"Tidak Disarankan\">";
                                break;
                            default:
                                $tahapan = "<img src=\"styles/images/p.png\" title=\"Belum di Proses\">";
                                break;
                        }
                        ?>
                        <tr>
                            <td><?= $no ?>.</td>
                            <td><?= $r[name] ?></td>
                            <td><?= $r[gender] ?></td>
                            <td><?= $r[umur] ?></td>
                            <td><?= $r[eduName] ?></td>
                            <td><?= $r[cell_no] ?></td>
                            <td align="center"><?= getTanggal($r[sel_date]) ?></td>
                            <!-- <td><?= $r[sel_result] ?></td> -->
                            <td align="center"><a href="?par[mode]=edit&par[id]=<?= $r[id] ?>&par[idPosisi]=<?= $r[id_posisi] ?>&par[idPhase]=<?= $kodeMaster . getPar($par, "mode, id") ?>"><?= $tahapan ?></a></td>
                        </tr>
                    <?php
                        }
                        ?>
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:right" colspan="9">
                        <img src="styles/images/t.png"> : Disarankan
                        <img src="styles/images/f.png"> : Tidak disarankan
                        <img src="styles/images/p.png"> : Belum di proses
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
<?php
}

function form()
{
    global $s, $par, $arrTitle, $arrUrutan, $ui;

    $sql = "SELECT t1.subject, t1.propose_date, t1.need_date, t2.subject as namaPosisi, t1.pos_function FROM rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi where t1.id = '$par[idPosisi]' ";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryStatus = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'SB' and statusData = 't' order by namaData";
    $queryHasil = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R07' and statusData = 't' order by namaData";

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN('R09', 'R11', 'R12', 'R13')");
    $kodeMaster = getField("select kodeData from mst_data where kodeCategory = 'R09' and urutanData = '" . $arrUrutan[$s] . "'-1");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="formseleksi" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p style="position:absolute;top:5px;right:5px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
            </p>
            <fieldset>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createSpan("Judul", $r[subject]) ?></p>
                            <p><?= $ui->createSpan("Tgl. Pengajuan", getTanggal($r[propose_date])) ?></p>
                            <p><?= $ui->createSpan("Tgl. Kebutuhan", getTanggal($r[need_date])) ?></p>
                        </td>
                        <td style="width:50%">
                            <p>&nbsp;</p>
                            <p><?= $ui->createSpan("Posisi", $r[namaPosisi]) ?></p>
                            <p><?= $ui->createSpan("Job Function", $r[pos_function]) ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br clear="all" />

            <fieldset>
                <legend>Pelamar</legend>
                <?php
                    $sql = "SELECT t1.name, t1.gender, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn ') umur, (SELECT CONCAT(x1.edu_type, '\t', x1.edu_fac, '\t', x1.edu_dept) FROM rec_applicant_edu x1 where x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) dataPendidikan from rec_applicant t1 where t1.id = '$par[id]' order by t1.name";
                    $res = db($sql);
                    $r = mysql_fetch_assoc($res);
                    $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Wanita";
                    list($r[eduType], $r[eduFac], $r[eduDept]) = explode("\t", $r[dataPendidikan]);
                    ?>
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%">
                            <p><?= $ui->createSpan("Nama", $r[name]) ?></p>
                            <p><?= $ui->createSpan("Pendidikan", $arrMaster[$r[eduType]]) ?></p>
                            <p><?= $ui->createSpan("Fakultas", $arrMaster[$r[eduFac]]) ?></p>
                        </td>
                        <td style="width: 50%">
                            <p><?= $ui->createSpan("Usia", $r[umur]) ?></p>
                            <p><?= $ui->createSpan("Gender", $r[gender]) ?></p>
                            <p><?= $ui->createSpan("Jurusan", $arrMaster[$r[eduDept]]) ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br clear="all" />
            <ul class="hornav">
                <?php
                    $sql = "select * from rec_selection_appl where phase_id = '" . $par[idPhase] . "' AND parent_id = '$par[id]'";
                    $res = db($sql);
                    $r = mysql_fetch_array($res);
                    $style = $par[idPhase] == 607 ? "style=\"display:none;\"" : "";
                    $arrMaster[$par[idPhase]] = $arrMaster[$par[idPhase]] == "Hasil" ? "Akhir" : $arrMaster[$par[idPhase]];
                    ?>
                <li class="current"><a href="#tab_1"><?= $arrMaster[$par[idPhase]] ?></a></li>
            </ul>

            <div id="tab_1" class="subcontent">
                <fieldset>
                    <legend>PELAKSANAAN</legend>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 50%">
                                <p><?= $ui->createField("Tanggal", "inp[sel_date]", getTanggal($r[sel_date]), "t", "t", "", "", "", "", "t") ?></p>
                                <div <?= $style ?>>
                                    <p>
                                        <label class="l-input-small2">Waktu</label>
                                        <div class="field">
                                            <input type="text" value="<?= $r[sel_time] ?>" id="sel_time" name="inp[sel_time]" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" />
                                        </div>
                                    </p>
                                </div>
                                <!-- <p><?= $ui->createField("Hasil", "inp[sel_result]", $r[sel_result], "", "t") ?></p> -->
                                <div <?= $style ?>>
                                    <p><?= $ui->createComboData("Status Penolakan", $queryStatus, "id", "description", "inp[sel_cancel]", $r[sel_cancel], "", "", "", "", "t") ?></p>
                                </div>
                                <p><?= $ui->createComboData("Hasil " . $arrMaster[$kodeMaster], $queryHasil, "id", "description", "inp[sel_status]", $r[sel_status], "", "", "", "", "t") ?></p>
                            </td>
                            <td style="width: 50%">
                                <p><?= $ui->createTextArea("Keterangan", "inp[sel_remark]", $r[sel_remark]) ?></p>
                                <p><?= $ui->createFile("File Pendukung", "sel_filename", $r[sel_filename], "", "", "recSelection", $r[id], "delFileSelection") ?></p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
        </form>
    </div>
<?php
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "delFileSelection":
            $text = hapusFile();
            break;
        case "edit":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
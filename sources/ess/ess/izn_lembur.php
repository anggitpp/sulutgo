<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
if (empty($cID)) {
    echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
    exit();
}

$fFile = "files/ess/lembur/";

function upload($idLembur)
{
    global $fFile;

    $fileUpload = $_FILES["fileLembur"]["tmp_name"];
    $fileUpload_name = $_FILES["fileLembur"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $fileLembur = "lembur-" . $idLembur . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $fileLembur);
    }
    if (empty($fileLembur)) $fileLembur = getField("select fileLembur from att_lembur where idLembur='$idLembur'");

    return $fileLembur;
}
function hapusFile()
{
    global $par, $fFile;

    $fileLembur = getField("select fileLembur from att_lembur where idLembur='$par[idLembur]'");
    if (file_exists($fFile . $fileLembur) and $fileLembur != "") unlink($fFile . $fileLembur);

    $sql = "update att_lembur set fileLembur='' where idLembur='$par[idLembur]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function gNomor()
{
    global $inp;

    $prefix = "SPL";
    $date = empty($_GET[tanggalLembur]) ? $inp[tanggalLembur] : $_GET[tanggalLembur];
    $date = empty($date) ? date('d/m/Y') : $date;
    list($tanggal, $bulan, $tahun) = explode("/", $date);

    $nomor = getField("select nomorLembur from att_lembur where month(tanggalLembur)='$bulan' and year(tanggalLembur)='$tahun' order by nomorLembur desc limit 1");
    list($count) = explode("/", $nomor);

    return str_pad(($count + 1), 3, "0", STR_PAD_LEFT) . "/" . $prefix . "-" . getRomawi($bulan) . "/" . $tahun;
}

function hapus()
{
    global $par;

    $sql = "delete from att_lembur where idLembur='$par[idLembur]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,idLembur") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[fileLembur] = upload($par[idLembur]);

    $inp[mulaiLembur] = setTanggal($inp[mulaiLembur_tanggal]) . " " . $inp[mulaiLembur];
    $inp[selesaiLembur] = setTanggal($inp[mulaiLembur_tanggal]) . " " . $inp[selesaiLembur];
    $inp[tanggalLembur] = setTanggal($inp[tanggalLembur]);
    $inp[updateBy] = $cUser;
    $inp[updateTime] = date('Y-m-d H:i:s');

    $sql = "update att_lembur set fileLembur = '$inp[fileLembur]', idPegawai = '$inp[idPegawai]', idAtasan = '$inp[idAtasan]', nomorLembur = '$inp[nomorLembur]', tanggalLembur = '$inp[tanggalLembur]', mulaiLembur = '$inp[mulaiLembur]', selesaiLembur = '$inp[selesaiLembur]', keteranganLembur = '$inp[keteranganLembur]', updateBy = '$inp[updateBy]', updateTime = '$inp[updateTime]' where idLembur='$par[idLembur]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,idLembur") . "';</script>";
}

function tambah()
{
    global $inp, $par, $cUser;

    repField();

    $idLembur = getLastId("att_lembur", "idLembur");

    $inp[fileLembur] = upload($idLembur);

    $inp[mulaiLembur] = setTanggal($inp[mulaiLembur_tanggal]) . " " . $inp[mulaiLembur];
    $inp[selesaiLembur] = setTanggal($inp[mulaiLembur_tanggal]) . " " . $inp[selesaiLembur];
    $inp[tanggalLembur] = setTanggal($inp[tanggalLembur]);
    $inp[createBy] = $cUser;
    $inp[createTime] = date('Y-m-d H:i:s');

    $sql = "insert into att_lembur set idLembur = '$idLembur', fileLembur = '$inp[fileLembur]', idPegawai = '$inp[idPegawai]', idAtasan = '$inp[idAtasan]', nomorLembur = '$inp[nomorLembur]', tanggalLembur = '$inp[tanggalLembur]', mulaiLembur = '$inp[mulaiLembur]', selesaiLembur = '$inp[selesaiLembur]', keteranganLembur = '$inp[keteranganLembur]', createBy = '$inp[createBy]', createTime = '$inp[createTime]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,idLembur") . "';</script>";
}

function form()
{
    global $s, $par, $arrTitle, $cID, $ui;

    $sql = "select * from att_lembur where idLembur='$par[idLembur]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
    if (empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
    list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
    list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);

    setValidation("is_null", "inp[nomorLembur]", "anda harus mengisi nomor");
    setValidation("is_null", "inp[idPegawai]", "anda harus mengisi nik");
    setValidation("is_null", "tanggalLembur", "anda harus mengisi tanggal");
    setValidation("is_null", "mulaiLembur_tanggal", "anda harus mengisi tanggal");
    setValidation("is_null", "mulaiLembur", "anda harus mengisi waktu");
    setValidation("is_null", "selesaiLembur", "anda harus mengisi waktu");
    setValidation("is_null", "inp[idAtasan]", "anda harus mengisi atasan");
    echo getValidation();

    if (!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
    if (empty($r[idAtasan])) $r[idAtasan] = getField("select leader_id from emp_phist where parent_id='" . $r[idPegawai] . "' AND status ='1'");

    $sql_ = "select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='" . $r[idPegawai] . "'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_array($res_);

    $sql__ = "select * from emp_phist where parent_id='" . $r_[idPegawai] . "' and status='1'";
    $res__ = db($sql__);
    $r__ = mysql_fetch_array($res__);
    $r_[namaJabatan] = $r__[pos_name];
    $r_[namaDivisi] = getField("select namaData from mst_data where kodeData='" . $r__[div_id] . "'");

    $queryEmp = "SELECT id, name description FROM emp where status = '535'";
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" />
            </p>
            <input type="hidden" id="inp[idPegawai]" name="inp[idPegawai]" value="<?= $r[idPegawai] ?>" readonly="readonly" />
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createField("Nomor", "inp[nomorLembur]", $r[nomorLembur]) ?></p>
                            <p><?= $ui->createField("NPP", "inp[nikPegawai]", $r_[nikPegawai]) ?></p>
                            <p><?= $ui->createField("Nama", "inp[namaPegawai]", $r_[namaPegawai]) ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createField("Tanggal", "inp[tanggalLembur]", getTanggal($r[tanggalLembur]), "", "", "", "onchange=\"getNomor('" . getPar($par, "mode") . "');\"", "", "", "t") ?></p>
                            <p><?= $ui->createField("Jabatan", "inp[namaJabatan]", $r_[namaJabatan]) ?></p>
                            <p><?= $ui->createField("Divisi", "inp[namaDivisi]", $r_[namaDivisi]) ?></p>
                        </td>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA IZIN LEMBUR</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createField("Tanggal", "inp[mulaiLembur_tanggal]", getTanggal($mulaiLembur_tanggal), "", "", "", "", "", "", "t") ?></p>
                            <table style="width:100%">
                                <tr>
                                    <td style="width:50%">
                                        <p><?= $ui->createTimePicker("Mulai", "mulaiLembur", substr($mulaiLembur, 0, 5), "", "t") ?></p>
                                    </td>
                                    <td style="width:50%">
                                        <p><?= $ui->createTimePicker("Selesai", "selesaiLembur", substr($selesaiLembur, 0, 5)) ?></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="55%">
                            <p><?= $ui->createComboData("Atasan", $queryEmp, "id", "description", "inp[idAtasan]", $r[idAtasan], "", "", "t") ?></p>
                            <p><?= $ui->createTextArea("Keterangan", "inp[keteranganLembur]", $r[keteranganLembur]) ?></p>
                            <p><?= $ui->createFile("Dokumen", "fileLembur", $r[fileLembur], "", "", "essLembur", $r[idLembur], "delFile") ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $cID;

    if (empty($par[tahunLembur])) $par[tahunLembur] = date('Y');
    $_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>

    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php require_once "tmpl/emp_header_basic.php"; ?>
        <br clear="all" />
        <form method="post" class="stdform">
            <div id="pos_r">
                <?php if (isset($menuAccess[$s]["add"])) ?><a href="?par[mode]=add<?= getPar($par, 'mode,idLembur') ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
            <div id="pos_l">
                <p>
                    <input type="text" id="par[filter]" name="par[filter]" style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" />
                    <?= comboYear("par[tahunLembur]", $par[tahunLembur]) ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th rowspan="2" width="20">No.</th>
                    <th rowspan="2" width="*">Nomor</th>
                    <th rowspan="2" width="75">Mulai</th>
                    <th rowspan="2" width="75">Selesai</th>
                    <th rowspan="2" width="75">Tanggal</th>
                    <th colspan="2" width="50">Approval</th>
                    <th rowspan="2" width="30">Bukti</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th rowspan="2" width="50">Kontrol</th>
                </tr>
                <tr>
                    <th width="50">Atasan</th>
                    <th width="50">MANAGER</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "where year(t1.tanggalLembur)='$par[tahunLembur]'";
                    if (!empty($cID)) $filter .= " and t1.idPegawai='" . $cID . "'";
                    if (!empty($par[filter]))
                        $filter .= " and (lower(t1.nomorLembur) like '%" . strtolower($par[filter]) . "%')";

                    $sql = "select t1.* from att_lembur t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
                    $res = db($sql);
                    while ($r = mysql_fetch_array($res)) {
                        $no++;

                        $persetujuanLembur = $r[persetujuanLembur] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                        $persetujuanLembur = $r[persetujuanLembur] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
                        $persetujuanLembur = $r[persetujuanLembur] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;

                        $sdmLembur = $r[sdmLembur] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                        $sdmLembur = $r[sdmLembur] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmLembur;
                        $sdmLembur = $r[sdmLembur] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmLembur;

                        list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
                        list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
                        $download = empty($r[fileLembur]) ? "" : "<a href=\"download.php?d=essLembur&f=$r[idLembur]\"><img src=\"" . getIcon($r[fileLembur]) . "\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[nomorLembur] ?></td>
                        <td align="center"><?= substr($mulaiLembur, 0, 5) ?></td>
                        <td align="center"><?= substr($selesaiLembur, 0, 5) ?></td>
                        <td align="center"><?= getTanggal($mulaiLembur_tanggal) ?></td>
                        <td align="center"><a href="#" onclick="openBox('popup.php?par[mode]=detAts&par[idLembur]=<?= $r[idLembur] . getPar($par, 'mode,idLembur') ?>',750,425);"><?= $persetujuanLembur ?></a></td>
                        <td align="center"><a href="#" onclick="openBox('popup.php?par[mode]=detSdm&par[idLembur]=<?= $r[idLembur] . getPar($par, 'mode,idLembur') ?>',750,425);"><?= $sdmLembur ?></a></td>
                        <td align="center"><?= $download ?></td>
                        <td align="center"><a href="#" class="print" title="Cetak Form" onclick="openBox('ajax.php?par[mode]=print&par[idLembur]=<?= $r[idLembur] . getPar($par, 'mode,idLembur') ?>',900,500);"><span>Cetak</span></a>
                            <a href="?par[mode]=det&par[idLembur]=<?= $r[idLembur] . getPar($par, 'mode,idLembur') ?>" title="Detail Data" class="detail"><span>Detail</span></a>
                            <?php
                                    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
                                        $control = "";
                                        if (in_array($r[persetujuanLembur], array("", "r")) || in_array($r[sdmLembur], array("", "r")))
                                            if (isset($menuAccess[$s]["edit"]))
                                                $control .= "<a href=\"?par[mode]=edit&par[idLembur]=$r[idLembur]" . getPar($par, "mode,idLembur") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";

                                        if (in_array($r[persetujuanLembur], array("")) || in_array($r[sdmLembur], array("")))
                                            if (isset($menuAccess[$s]["delete"]))
                                                $control .= "<a href=\"?par[mode]=del&par[idLembur]=$r[idLembur]" . getPar($par, "mode,idLembur") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                        $control .= "</td>";
                                    }
                                    ?>
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

function detailApproval()
{
    global $par, $ui;

    $sql = "select * from att_lembur where idLembur='$par[idLembur]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $persetujuanTitle = $par[mode] == "detSdm" ? "Approval Manager" : "Approval Atasan";
    $persetujuanField = $par[mode] == "detSdm" ? "sdmLembur" : "persetujuanLembur";
    $catatanField = $par[mode] == "detSdm" ? "noteLembur" : "catatanLembur";
    $timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
    $userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";

    $persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
    $persetujuanField = $par[mode] == "detPay" ? "pembayaranLembur" : $persetujuanField;
    $catatanField = $par[mode] == "detPay" ? "deskripsiLembur" : $catatanField;
    $timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
    $userField = $par[mode] == "detPay" ? "paidBy" : $userField;

    list($dateField) = explode(" ", $r[$timeField]);
    $persetujuanLembur = "Belum Diproses";
    $persetujuanLembur = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanLembur;
    $persetujuanLembur = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanLembur;
    $persetujuanLembur = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanLembur;
    ?>
    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle"><?= $persetujuanTitle ?></h1>
            <?= getBread(ucwords($par[mode] . " data")) ?>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form id="form" name="form" class="stdform">
                <div id="general" class="subcontent">
                    <p><?= $ui->createSpan("Tanggal", getTanggal($dateField, "t")) ?></p>
                    <p><?= $ui->createSpan("Nama", getField("select namaUser from app_user where username='" . $r[$userField] . "' ")) ?></p>
                    <p><?= $ui->createSpan("Status", $persetujuanLembur) ?></p>
                    <p><?= $ui->createSpan("Keterangan", nl2br($r[$catatanField])) ?></p>
                    <p class="btnSave">
                        <input type="button" class="cancel radius2" value="Close" onclick="closeBox();" />
                    </p>
                </div>
            </form>
        </div>
    </div>
<?php
}

function detail()
{
    global $db, $s, $par, $arrTitle, $ui;

    $sql = "select * from att_lembur where idLembur='$par[idLembur]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
    if (empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
    list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
    list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);

    $sql_ = "select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='" . $r[idPegawai] . "'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_array($res_);

    $sql__ = "select * from emp_phist where parent_id='" . $r_[idPegawai] . "' and status='1'";
    $res__ = db($sql__);
    $r__ = mysql_fetch_array($res__);
    $r_[namaJabatan] = $r__[pos_name];
    $r_[namaDivisi] = getField("select namaData from mst_data where kodeData='" . $r__[div_id] . "'");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div class="contentwrapper">
        <form id="form" name="form" class="stdform">
            <p class="btnSave">
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" style="float:right;" />
            </p>
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Nomor", $r[nomorLembur]) ?></p>
                            <p><?= $ui->createSpan("NPP", $r_[nikPegawai]) ?></p>
                            <p><?= $ui->createSpan("Nama", $r_[namaPegawai]) ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($r[tanggalLembur], "t")) ?></p>
                            <p><?= $ui->createSpan("Jabatan", $r_[namaJabatan]) ?></p>
                            <p><?= $ui->createSpan("Divisi", $r_[namaDivisi]) ?></p>
                        </td>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA IZIN LEMBUR</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($mulaiLembur_tanggal, "t")) ?></p>
                            <p><?= $ui->createSpan("Waktu", substr($mulaiLembur, 0, 5) . " <strong>s.d</strong> " . substr($selesaiLembur, 0, 5)) ?></p>
                        </td>
                        <td width="50%">
                            <?php
                                $sql_ = "select id as idAtasan, reg_no as nikAtasan, name as namaAtasan from emp where id='" . $r[idAtasan] . "'";
                                $res_ = db($sql_);
                                $r_ = mysql_fetch_array($res_);

                                $persetujuanLembur = $r[persetujuanLembur] == "t" ? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
                                $persetujuanLembur = $r[persetujuanLembur] == "f" ? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
                                $persetujuanLembur = $r[persetujuanLembur] == "r" ? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanLembur;

                                list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
                                $approveTime = getTanggal($approveTanggal) . " " . substr($approveWaktu, 0, 5);
                                $approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
                                ?>
                            <p><?= $ui->createSpan("Atasan", $r_[nikAtasan] . " - " . $r_[namaAtasan]) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[keteranganLembur])) ?></p>
                        </td>
                    </tr>
                </table>
                <?php
                    $persetujuanLembur = "Belum Diproses";
                    $persetujuanLembur = $r[persetujuanLembur] == "t" ? "Disetujui" : $persetujuanLembur;
                    $persetujuanLembur = $r[persetujuanLembur] == "f" ? "Ditolak" : $persetujuanLembur;
                    $persetujuanLembur = $r[persetujuanLembur] == "r" ? "Diperbaiki" : $persetujuanLembur;
                    list($approveDate) = explode(" ", $r[approveTime]);
                    ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL ATASAN</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($mulaiLembur_tanggal, "t")) ?></p>
                            <p><?= $ui->createSpan("Nama", getField("select namaUser from app_user where username='$r[approveBy]' ")) ?></p>
                            <p><?= $ui->createSpan("Status", $persetujuanLembur) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[catatanLembur])) ?></p>
                            <p><?= $ui->createSpan("Overtime", nl2br($r[catatanLembur])) ?></p>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                </table>
                <?php

                    $sdmLembur = "Belum Diproses";
                    $sdmLembur = $r[sdmLembur] == "t" ? "Disetujui" : $sdmLembur;
                    $sdmLembur = $r[sdmLembur] == "f" ? "Ditolak" : $sdmLembur;
                    $sdmLembur = $r[sdmLembur] == "r" ? "Diperbaiki" : $sdmLembur;
                    list($sdmDate) = explode(" ", $r[sdmTime]);
                    ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL MANAGER</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($sdmDate, "t")) ?></p>
                            <p><?= $ui->createSpan("Nama", getField("select namaUser from app_user where username='$r[sdmBy]' ")) ?></p>
                            <p><?= $ui->createSpan("Status", $sdmLembur) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[noteLembur])) ?></p>
                            <p><?= $ui->createSpan("Overtime", nl2br($r[overtimeLembur])) ?></p>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
<?php
}

function pdf()
{
    global $par;

    require_once 'plugins/PHPPdf.php';

    $sql = "select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur where idLembur='$par[idLembur]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
    list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
    list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
    list($Y, $m, $d) = explode("-", $mulaiLembur_tanggal);
    $hariLembur = $arrHari[date('w', mktime(0, 0, 0, $m, $d, $Y))];

    if ($selesaiLembur < $mulaiLembur) {
        $selisih = selisihMenit2(substr($mulaiLembur, 0, 5), substr($selesaiLembur, 0, 5)) / 60;
        $r[overtimeLembur] = (24 - substr($mulaiLembur, 0, 2)) + substr($selesaiLembur, 1, 1);
    } else {
        $r[overtimeLembur] = selisihMenit2(substr($mulaiLembur, 0, 5), substr($selesaiLembur, 0, 5)) / 60;
        if ($r[overtimeLembur] < 10) {
            $r[overtimeLembur] = substr($r[overtimeLembur], 0, 1);
        } else {
            $r[overtimeLembur] = substr($r[overtimeLembur], 0, 2);
        }
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(15);

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 6, 'PRATAMA MITRA SEJATI', 0, 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Arial', 'BU', 12);
    $pdf->Cell(180, 6, 'SURAT PERINTAH KERJA LEMBUR', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(180, 6, 'No. SPL : ' . $r[nomorLembur], 0, 0, 'C');
    $pdf->Ln(15);

    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(180, 2, 'Kepada karyawan yang namanya tersebut dibawah ini diperintahkan kerja lembur', 0, 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(180, 6, 'To the employees whose name is noted below working overtime', 0, 0, 'L');
    $pdf->Ln(10);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Untuk keperluan/Tugas', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'For/Duty', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, $r[keteranganLembur], 0, 'L');
    $pdf->Ln(3);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Pada Hari/Tanggal', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Day/Date', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, $hariLembur . ", " . getTanggal($mulaiLembur_tanggal, "t"), 0, 'L');
    $pdf->Ln();

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Dimulai Jam', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Start Form', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(15, 6, substr($mulaiLembur, 0, 5), 0, 'L');
    $pdf->SetXY($setX + 70, $setY);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(10, 3, 's/d', 0, 0, 'L');
    $pdf->SetXY($setX + 70, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(10, 6, 'Up to', 0, 0, 'L');
    $pdf->SetXY($setX + 85, $setY);
    $pdf->SetFont('Arial');
    $pdf->MultiCell(15, 6, substr($selesaiLembur, 0, 5), 0, 'L');
    $pdf->Ln();

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Jumlah Jam', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Number of hours', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(15, 6, $r[overtimeLembur], 0, 'L');
    $pdf->SetXY($setX + 70, $setY);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(10, 3, 'Jam', 0, 0, 'L');
    $pdf->SetXY($setX + 70, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(10, 6, 'Hours', 0, 0, 'L');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(10, 10, 'No', 1, 0, 'C');
    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->Cell(80, 10, '', 1, 0, 'C');
    $pdf->Cell(60, 10, '', 1, 0, 'C');
    $pdf->Cell(30, 10, '', 1, 0, 'C');
    $pdf->SetFont('Arial', 'BU');

    $pdf->SetXY($setX, $setY + 1);
    $pdf->Cell(80, 5, 'Nama', 0, 0, 'C');
    $pdf->Cell(60, 5, 'Jabatan', 0, 0, 'C');
    $pdf->Cell(30, 5, 'Tanda Tangan', 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetXY($setX, $setY + 4.5);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(80, 5, 'Name', 0, 0, 'C');
    $pdf->Cell(60, 5, 'Position', 0, 0, 'C');
    $pdf->Cell(30, 5, 'Signature', 0, 0, 'C');
    $pdf->Ln(5.5);

    $sql_ = "select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='" . $r[idPegawai] . "'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_array($res_);
    $pdf->SetFont('Arial');
    $pdf->SetAligns(array('C', 'L', 'L', 'L'));
    $pdf->SetWidths(array(10, 80, 60, 30));
    $pdf->Cols(array(
        array(
            "1.",
            getField("select name from emp where id='" . $r[idPegawai] . "'"),
            getField("select pos_name from emp_phist where parent_id='" . $r[idPegawai] . "' and status='1'"),
            ""
        )
    ), 10);

    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(90, 6, 'Requested by,', 0, 0, 'C');
    $pdf->Cell(90, 6, 'Approved by,', 0, 0, 'C');

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(90, 3, 'Diajukan Oleh,', 0, 0, 'C');
    $pdf->Cell(90, 3, 'Menyetujui,', 0, 0, 'C');

    $pdf->Ln(20);
    $pdf->Cell(90, 5, '                                           ', 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(90, 5, getField("select name from emp t1 join emp_phist t2 on (t1.id = t2.leader_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "), 0, 0, 'C');
    $pdf->Cell(90, 5, getField("select name from emp t1 join emp_phist t2 on (t1.id = t2.manager_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "), 0, 0, 'C');
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'U');
    $pdf->Cell(90, 3, '                                            ', 0, 0, 'C');
    $pdf->Cell(90, 3, '                                            ', 0, 0, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial');
    $pdf->Cell(90, 3, getField("select pos_name from emp t1 join emp_phist t2 on (t1.id = t2.leader_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "), 0, 0, 'C');
    $pdf->Cell(90, 3, getField("select pos_name from emp t1 join emp_phist t2 on (t1.id = t2.manager_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "), 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(90, 6, 'Date/Tgl :                           ', 0, 0, 'C');
    $pdf->Cell(90, 6, 'Date/Tgl :                           ', 0, 0, 'C');

    $pdf->Output();
}


function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "no":
            $text = gNomor();
            break;
        case "print":
            $text = pdf();
            break;
        case "det":
            $text = detail();
            break;
        case "detAts":
            $text = detailApproval();
            break;
        case "detSdm":
            $text = detailApproval();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
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
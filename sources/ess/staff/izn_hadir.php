<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/ess/hadir/";

function gNomor()
{
    global $inp;

    $prefix = "IK";
    $date = empty($_GET[tanggalHadir]) ? $inp[tanggalHadir] : $_GET[tanggalHadir];
    $date = empty($date) ? date('d/m/Y') : $date;
    list($tanggal, $bulan, $tahun) = explode("/", $date);

    $nomor = getField("select nomorHadir from att_hadir where month(tanggalHadir)='$bulan' and year(tanggalHadir)='$tahun' order by nomorHadir desc limit 1");
    list($count) = explode("/", $nomor);

    return str_pad(($count + 1), 3, "0", STR_PAD_LEFT) . "/" . $prefix . "-" . getRomawi($bulan) . "/" . $tahun;
}

function gPegawai()
{
    global $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('S06', 'X04', 'X05', 'X06')");

    $sql = "select pos_name, location, div_id, leader_id, replacement_id from emp_phist where parent_id='" . $par[idPegawai] . "' AND status = '1'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $r[location] = $arrMaster[$r[location]];
    $r[div_id] = $arrMaster[$r[div_id]];

    return json_encode($r);
}

function upload($idHadir)
{
    global $fFile;
    $fileUpload = $_FILES["fileHadir"]["tmp_name"];
    $fileUpload_name = $_FILES["fileHadir"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $fileHadir = "hadir-" . $idHadir . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $fileHadir);
    }
    if (empty($fileHadir)) $fileHadir = getField("select fileHadir from att_hadir where idHadir='$idHadir'");

    return $fileHadir;
}

function hapus()
{
    global $par, $fFile;

    $fileHadir = getField("select fileHadir from att_hadir where idHadir='$par[idHadir]'");
    if (file_exists($fFile . $fileHadir) and $fileHadir != "") unlink($fFile . $fileHadir);

    $sql = "delete from att_hadir where idHadir='$par[idHadir]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idHadir") . "';</script>";
}

function hapusFile()
{
    global $par, $fFile;

    $fileHadir = getField("select fileHadir from att_hadir where idHadir='$par[idHadir]'");
    if (file_exists($fFile . $fileHadir) and $fileHadir != "") unlink($fFile . $fileHadir);

    $sql = "update att_hadir set fileHadir='' where idHadir='$par[idHadir]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[fileHadir] = upload($par[idHadir]);

    $inp[hariHadir] = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
    $inp[mulaiHadir] = setTanggal($inp[mulaiHadir_tanggal]) . " " . $inp[mulaiHadir_waktu];
    $inp[selesaiHadir] = setTanggal($inp[selesaiHadir_tanggal]) . " " . $inp[selesaiHadir_waktu];
    $inp[tanggalHadir] = setTanggal($inp[tanggalHadir]);
    $inp[updateBy] = $cUser;
    $inp[updateTime] = date('Y-m-d H:i:s');

    $sql = "update att_hadir set idPegawai='$inp[idPegawai]', idPengganti = '$inp[idPengganti]', idAtasan = '$inp[idAtasan]', idKategori = '$inp[idKategori]', idTipe = '$inp[idTipe]', nomorHadir = '$inp[nomorHadir]', tanggalHadir = '$inp[tanggalHadir]', mulaiHadir = '$inp[mulaiHadir]', selesaiHadir = '$inp[selesaiHadir]', keteranganHadir = '$inp[keteranganHadir]', fileHadir = '$inp[fileHadir]', hariHadir = '$inp[hariHadir]', updateBy = '$inp[updateBy]', updateTime = '$inp[updateTime]' where idHadir='$par[idHadir]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idHadir") . "';</script>";
}

function tambah()
{
    global $inp, $cUser, $arrParameter;

    repField();

    $idHadir = getLastId("att_hadir", "idHadir");

    $inp[fileHadir] = upload($idHadir);
    $inp[hariHadir] = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
    $inp[mulaiHadir] = setTanggal($inp[mulaiHadir_tanggal]) . " " . $inp[mulaiHadir_waktu];
    $inp[selesaiHadir] = setTanggal($inp[selesaiHadir_tanggal]) . " " . $inp[selesaiHadir_waktu];
    $inp[tanggalHadir] = setTanggal($inp[tanggalHadir]);
    $inp[createBy] = $cUser;
    $inp[createTime] = date('Y-m-d H:i:s');

    $arrNama = arrayQuery("select id, name from emp");
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $sql = "insert into att_hadir set idHadir = '$idHadir', idPegawai = '$inp[idPegawai]', idPengganti = '$inp[idPengganti]', idAtasan = '$inp[idAtasan]', idKategori = '$inp[idKategori]', idTipe = '$inp[idTipe]', nomorHadir = '$inp[nomorHadir]', tanggalHadir = '$inp[tanggalHadir]', mulaiHadir = '$inp[mulaiHadir]', selesaiHadir = '$inp[selesaiHadir]', keteranganHadir = '$inp[keteranganHadir]', fileHadir = '$inp[fileHadir]', hariHadir = '$inp[hariHadir]', createBy = '$inp[createBy]', createTime = '$inp[createTime]'";
    db($sql);

    $subjek = "Pemberitahuan Rencana Ketidakhadiran $inp[tanggalHadir]";
    $link = "<a href=\"" . APP_URL . "/index.php?c=16&p=67&m=773&s=774\"><b>DISINI</b></a>";
    $isi1 = "
        <table width=\"100%\">
			<tr>
			    <td colspan=\"3\">Sebagai informasi bahwasannya rencana Izin Ketidakhadiran pada : </td> 
			</tr>
			<br>
			<tr>
                <td style=\"width:100px;\">Tanggal</td>
                <td style=\"width:10px;\">:</td>
                <td>" . getTanggal($inp[tanggalHadir], "t") . "</td>
			</tr>
			<tr>
                <td style=\"width:100px;\">Nomor</td>
                <td style=\"width:10px;\">:</td>
                <td><strong>" . $inp[nomorHadir] . "</strong></td>			
			</tr>
			<tr>
                <td style=\"width:100px;\">Kategori Izin</td>
                <td style=\"width:10px;\">:</td>
                <td>" . $arrMaster[$inp[idKategori]] . "</td>
			</tr>
			<tr>
                <td style=\"width:100px;\">Nama</td>
                <td style=\"width:10px;\">:</td>
                <td>" . $arrNama[$inp[idPegawai]] . "</td>
			</tr>
			<tr>
                <td style=\"width:100px;\">Pengganti</td>
                <td style=\"width:10px;\">:</td>
                <td>" . $arrNama[$inp[idPengganti]] . "</td>
			</tr>
			<tr>
                <td style=\"width:100px;\">Mulai Izin</td>
                <td style=\"width:10px;\">:</td>
                <td>" . $inp[mulaiHadir] . " s/d " . $inp[selesaiHadir] . "</td>
			</tr>					
			<tr>
                <td style=\"width:100px;\">Keterangan</td>
                <td style=\"width:10px;\">:</td>
                <td>$inp[keteranganHadir]</td>
			</tr>
        </table>
            
        <table>
			<br>
			<tr>
			    <td colspan=\"3\">Dimohon untuk melakukan Approval Atasan pada nomor hadir di atas, silahkan klik $link</td>
			</tr>
			<br>
			<tr>
			    <td colspan=\"3\">Jakarta, " . date('d M Y') . " 
			</tr>
			<tr>
			    <td></td>
			</tr>
			<br><br>
			<tr>
			    <td>TTD.</td>
			</tr>
			<tr>
			    <td>" . $arrParameter[86] . "</td>
			</tr>
		</table>";

    $email = getField("select email from dta_pegawai where id = '$inp[idAtasan]'");
    sendMail($email, $subjek, $isi1);

    echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?" . getPar($par, "mode,idHadir") . "';</script>";
}

function approve()
{
    global $cUser, $par, $inp;

    $inp[approveBy] = $cUser;
    $inp[approveTime] = date('Y-m-d H:i:s');
    $sql = "UPDATE att_hadir SET persetujuanHadir = '$inp[persetujuanHadir]', catatanHadir = '$inp[catatanHadir]', approveBy = '$inp[approveBy]', approveTime = '$inp[approveTime]' WHERE idHadir = '$par[idHadir]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DI APPROVE');window.location='?" . getPar($par, "mode,idHadir") . "';</script>";
}

function sdm()
{
    global $cUser, $par, $inp;

    $inp[sdmBy] = $cUser;
    $inp[sdmTime] = date('Y-m-d H:i:s');
    $sql = "UPDATE att_hadir SET sdmHadir = '$inp[sdmHadir]', noteHadir = '$inp[noteHadir]', sdmBy = '$inp[sdmBy]', sdmTime = '$inp[sdmTime]' WHERE idHadir = '$par[idHadir]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DI APPROVE');window.location='?" . getPar($par, "mode,idHadir") . "';</script>";
}

function detail()
{
    global $s, $par, $arrTitle, $ui;

    $sql = "select * from att_hadir where idHadir='$par[idHadir]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[nomorHadir])) $r[nomorHadir] = gNomor();
    if (empty($r[tanggalHadir])) $r[tanggalHadir] = date('Y-m-d');

    $hariHadir =  $r[hariHadir] == "t" ? "Ya" : "Tidak";

    list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
    list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);

    if ($mulaiHadir_tanggal != $selesaiHadir_tanggal) {
        $jamMulai = "none";
        $jamSelesai = "none";
        $allDay = "none";
    } else {
        if ($r[hariHadir] == "t") {
            $jamMulai = "none";
            $jamSelesai = "none";
        } else {
            $jamMulai = "block";
            $jamSelesai = "block";
        }
        $allDay = "block";
    }

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
        <h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div class="contentwrapper">
        <form id="form" name="form" class="stdform">
            <p class="btnSave">
                <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode,idPegawai") . "' ;\" style=\"float:right;\" />
            </p>
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Nomor", $r[nomorHadir]) ?></p>
                            <p><?= $ui->createSpan("NIK", $r_[nikPegawai]) ?></p>
                            <p><?= $ui->createSpan("Nama", $r_[namaPegawai]) ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($r[tanggalHadir], "t")) ?></p>
                            <p><?= $ui->createSpan("Jabatan", $r_[namaJabatan]) ?></p>
                            <p><?= $ui->createSpan("Divisi", $r_[namaDivisi]) ?></p>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA IZIN KETIDAKHADIRAN</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal Mulai", getTanggal($mulaiHadir_tanggal, "t")) ?></p>
                            <p><?= $ui->createSpan("Jam Mulai", substr($mulaiHadir_waktu, 0, 5)) ?></p>
                            <p><?= $ui->createSpan("Tanggal Mulai", getTanggal($selesaiHadir_tanggal, "t")) ?></p>
                            <p><?= $ui->createSpan("Jam Mulai", substr($selesaiHadir_waktu, 0, 5)) ?></p>
            </div>
            <div style="display:<?= $allDay ?>">
                <p><?= $ui->createSpan("All Day", $hariHadir) ?></p>
                <p><?= $ui->createSpan("Keterangan", nl2br($r[keteranganHadir])) ?></p>
                </td>
                <td width="50%">
                    <?php
                        $sql_ = "select id as idPengganti, reg_no as nikPengganti, name as namaPengganti from emp where id='" . $r[idPengganti] . "'";
                        $res_ = db($sql_);
                        $r_ = mysql_fetch_array($res_);
                        ?>
                    <p><?= $ui->createSpan("Pengganti", $r_[nikPengganti] . " - " . $r_[namaPengganti]) ?></p>
                    <?php
                        $sql_ = "select id as idAtasan, reg_no as nikAtasan, name as namaAtasan from emp where id='" . $r[idAtasan] . "'";
                        $res_ = db($sql_);
                        $r_ = mysql_fetch_array($res_);
                        ?>
                    <p><?= $ui->createSpan("Atasan", $r_[nikAtasan] . " - " . $r_[namaAtasan]) ?></p>
                    <p><?= $ui->createSpan("Kategori Izin", getField("select namaData from mst_data where kodeData='$r[idKategori]'")) ?></p>
                    <p><?= $ui->createSpan("Dokumen", "<a href=\"download.php?d=hadir&f=$r[idHadir]\"><img src=\"" . getIcon($r[fileHadir]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>") ?></p>
                </td>
                </tr>
                </table>

                <?php
                    $persetujuanHadir = "Belum Diproses";
                    $persetujuanHadir = $r[persetujuanHadir] == "t" ? "Disetujui" : $persetujuanHadir;
                    $persetujuanHadir = $r[persetujuanHadir] == "f" ? "Ditolak" : $persetujuanHadir;
                    $persetujuanHadir = $r[persetujuanHadir] == "r" ? "Diperbaiki" : $persetujuanHadir;
                    ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL ATASAN</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Status", $persetujuanHadir) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[catatanHadir])) ?></p>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                </table>
                <?php
                    $sdmHadir = "Belum Diproses";
                    $sdmHadir = $r[sdmHadir] == "t" ? "Disetujui" : $sdmHadir;
                    $sdmHadir = $r[sdmHadir] == "f" ? "Ditolak" : $sdmHadir;
                    $sdmHadir = $r[sdmHadir] == "r" ? "Diperbaiki" : $sdmHadir;
                    ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL SDM</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Status", $sdmHadir) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[noteHadir])) ?></p>
                        </td>
                        <td width="55%">&nbsp;</td>
                    </tr>
                </table>
            </div>

        </form>
    </div>
<?php
}

function detailApproval()
{
    global $par, $ui;

    $sql = "select * from att_hadir where idHadir='$par[idHadir]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $persetujuanTitle = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
    $persetujuanField = $par[mode] == "detSdm" ? "sdmHadir" : "persetujuanHadir";
    $catatanField = $par[mode] == "detSdm" ? "noteHadir" : "catatanHadir";
    $timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
    $userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";

    $persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
    $persetujuanField = $par[mode] == "detPay" ? "pembayaranHadir" : $persetujuanField;
    $catatanField = $par[mode] == "detPay" ? "deskripsiHadir" : $catatanField;
    $timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
    $userField = $par[mode] == "detPay" ? "paidBy" : $userField;

    list($dateField) = explode(" ", $r[$timeField]);
    $persetujuanHadir = "Belum Diproses";
    $persetujuanHadir = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanHadir;
    $persetujuanHadir = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanHadir;
    $persetujuanHadir = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanHadir;
    ?>
    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle"><?= $persetujuanTitle ?></h1>
            <?= getBread(ucwords($par[mode] . " data")) ?>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form id="form" name="form" class="stdform">
                <p class="btnSave">
                    <input type="button" class="cancel radius2" value="Close" onclick="closeBox();" />
                </p>
                <div id="general" class="subcontent">
                    <p><?= $ui->createSpan("Tanggal", getTanggal($dateField, "t")) ?></p>
                    <p><?= $ui->createSpan("Nama", getField("select namaUser from app_user where username='" . $r[$userField] . "' ")) ?></p>
                    <p><?= $ui->createSpan("Status", $persetujuanHadir) ?></p>
                    <p><?= $ui->createSpan("Keterangan", nl2br($r[$catatanField])) ?></p>

                </div>
            </form>
        </div>
    </div>
<?php
}

function form()
{
    global $s, $par, $arrTitle, $cID, $ui, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('S06', 'X04', 'X05', 'X06')");

    $sql = "select * from att_hadir where idHadir='$par[idHadir]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryEmp = "SELECT id, concat(reg_no, ' - ', name) description FROM emp where status = '535'";
    $queryIzin = "SELECT kodeData id, namaData description FROM mst_data where kodeCategory = 'A01' and statusData = 't'";

    if (empty($r[nomorHadir])) $r[nomorHadir] = gNomor();
    if (empty($r[tanggalHadir])) $r[tanggalHadir] = date('Y-m-d');

    if (!isset($menuAccess[$s]["apprlv2"]))
        $filter = " AND (leader_id = '$cID' OR administration_id = '$cID')";
    $queryPegawai = "SELECT t1.id, concat(reg_no, ' - ', name) description FROM EMP t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";
    echo "SELECT t1.id, concat(reg_no, ' - ', name) description FROM EMP t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";

    list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
    list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);

    if ($mulaiHadir_tanggal != $selesaiHadir_tanggal) {
        $jamMulai = "none";
        $jamSelesai = "none";
        $allDay = "none";
    } else {
        if ($r[hariHadir] == "t") {
            $jamMulai = "none";
            $jamSelesai = "none";
        } else {
            $jamMulai = "block";
            $jamSelesai = "block";
        }
        $allDay = "block";
    }

    setValidation("is_null", "inp[nomorHadir]", "anda harus mengisi nomor");
    setValidation("is_null", "inp[idPegawai]", "anda harus mengisi nik");
    setValidation("is_null", "tanggalHadir", "anda harus mengisi tanggal");
    setValidation("is_null", "mulaiHadir_tanggal", "anda harus mengisi tanggal mulai");
    setValidation("is_null", "selesaiHadir_tanggal", "anda harus mengisi tanggal selesai");
    setValidation("is_null", "inp[idAtasan]", "anda harus mengisi atasan");
    setValidation("is_null", "inp[idKategori]", "anda harus mengisi kategori izin");
    echo getValidation();

    $sql_ = "SELECT pos_name, div_id, location FROM emp_phist where parent_id = '$r[idPegawai]' AND status = '1'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_assoc($res_);

    $r_[namaLokasi] = $arrMaster[$r_[location]];
    $r_[namaDivisi] = $arrMaster[$r_[div_id]];
    $r_[namaJabatan] = $r_[pos_name];
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" />
            </p>
            <br clear="all" />
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createField("Nomor", "inp[nomorHadir]", $r[nomorHadir], "", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createComboData("NIK - Nama", $queryPegawai, "id", "description", "inp[idPegawai]", $r[idPegawai], "onchange=\"getPegawai('" . getPar($par, "mode") . "');\"", "305px", "t", "t") ?></p>
                            <p><?= $ui->createField("Lokasi Kerja", "inp[namaLokasi]", $r_[namaLokasi], "", "", "", "", "", "t") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createField("Tanggal", "inp[tanggalHadir]", getTanggal($r[tanggalHadir]), "", "", "", "onchange=\"getNomor('" . getPar($par, "mode") . "');\"", "", "", "t") ?></p>
                            <p><?= $ui->createField("Jabatan", "inp[namaJabatan]", $r_[namaJabatan], "", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Divisi", "inp[namaDivisi]", $r_[namaDivisi], "", "", "", "", "", "t") ?></p>
                        </td>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA IZIN KETIDAKHADIRAN</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <table style="width:100%">
                                <tr>
                                    <td style="width:50%">
                                        <p><?= $ui->createField("Tanggal Mulai", "inp[mulaiHadir_tanggal]", getTanggal($mulaiHadir_tanggal), "t", "t", "", "onchange=\"setHide();\"", "", "", "t") ?></p>
                                        <p><?= $ui->createField("Tanggal Selesai", "inp[selesaiHadir_tanggal]", getTanggal($selesaiHadir_tanggal), "t", "t", "", "onchange=\"setHide();\"", "", "", "t") ?></p>
                                    </td>
                                    <td style="width:50%">
                                        <div id="jamMulai" style="display:<?= $jamMulai ?>">
                                            <p><?= $ui->createTimePicker("Jam Mulai", "mulaiHadir_waktu", substr($mulaiHadir_waktu, 0, 5), "", "t") ?></p>
                                        </div>
                                        <div id="jamSelesai" style="display:<?= $jamSelesai ?>">
                                            <p><?= $ui->createTimePicker("Jam Selesai", "selesaiHadir_waktu", substr($selesaiHadir_waktu, 0, 5), "", "t") ?></p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div id="allDay" style="display:<?= $allDay ?>">
                                <p><?= $ui->createRadio("All Day", "inp[hariHadir]", array("t" => "Ya", "f" => "Tidak"), $r[hariHadir], "", "onclick=\"setHide();\"") ?></p>
                            </div>
                            <p><?= $ui->createTextArea("Keterangan", "inp[keteranganHadir]", $r[keteranganHadir], "") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createComboData("Pengganti", $queryEmp, "id", "description", "inp[idPengganti]", $r[idPengganti], "", "300px", "t") ?></p>
                            <p><?= $ui->createComboData("Atasan", $queryEmp, "id", "description", "inp[idAtasan]", $r[idAtasan], "", "300px", "t") ?></p>
                            <p><?= $ui->createComboData("Kategori Izin", $queryIzin, "id", "description", "inp[idKategori]", $r[idKategori], "onchange=\"setTipe('" . getPar($par, "mode, idKategori") . "');\"", "300px", "t") ?></p>
                            <p><?= $ui->createFile("Dokumen", "fileHadir", $r[fileHadir], "", "", "essHadir", $r[idHadir], "delFile") ?></p>
                        </td>
                    </tr>
                </table>
                <?php if ($par[mode] == "app") { ?>
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA APPROVAL ATASAN</h3>
                        </div>
                    </div>
                    <p><?= $ui->createRadio("Status", "inp[persetujuanHadir]", array("t" => "Disetujui", "f" => "Ditolak"), $r[persetujuanHadir]) ?></p>
                    <p><?= $ui->createTextArea("Keterangan", "inp[catatanHadir]", $r[catatanHadir]) ?></p>
                <?php }
                    if ($par[mode] == "sdm") { ?>
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA APPROVAL SDM</h3>
                        </div>
                    </div>
                    <p><?= $ui->createRadio("Status", "inp[sdmHadir]", array("t" => "Disetujui", "f" => "Ditolak"), $r[sdmHadir]) ?></p>
                    <p><?= $ui->createTextArea("Keterangan", "inp[noteHadir]", $r[noteHadir]) ?></p>
                <?php } ?>
            </div>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $cID;

    if (empty($par[tahunHadir])) $par[tahunHadir] = date('Y');
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form method="post" class="stdform">
            <div id="pos_l" style="float:left;">
                <input placeholder="Search.." type="text" id="par[filter]" name="par[filter]" style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" />
                <?= comboYear("par[tahunHadir]", $par[tahunHadir]) ?>
                <input type="submit" value="GO" class="btn btn_search btn-small" />
            </div>
            <div id="pos_r">
                <?php if (isset($menuAccess[$s]["add"])) ?> <a href="?par[mode]=add<?= getPar($par, 'mode,idHadir') ?> " class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th rowspan="2" width="20">No.</th>
                    <th rowspan="2" width="*">Nomor</th>
                    <th colspan="3" width="225">Tanggal</th>
                    <th colspan="2" width="100">Approval</th>
                    <th rowspan="2" width="30">Bukti</th>
                    <th rowspan="2" width="50">Detail</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th rowspan="2" width="50">Kontrol</th>
                </tr>
                <tr>
                    <th width="75">Dibuat</th>
                    <th width="75">Mulai</th>
                    <th width="75">Selesai</th>
                    <th width="50">Atasan</th>
                    <th width="50">Manager</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "where year(t1.tanggalHadir)='$par[tahunHadir]'";
                    if (!isset($menuAccess[$s]["apprlv2"]))
                        $filter .= " and (t3.leader_id ='$cID' or t3.administration_id='$cID')";
                    if (!empty($par[filter]))
                        $filter .= " and (lower(t1.nomorHadir) like '%" . strtolower($par[filter]) . "%' OR lower(t2.reg_no) like '%" . strtolower($par[filter]) . "%' OR lower(t2.name) like '%" . strtolower($par[filter]) . "%')";
                    $sql = "select t1.*, t2.name, t2.reg_no from att_hadir t1 left join emp t2 on (t1.idPegawai=t2.id) join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t1.nomorHadir";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_array($res)) {
                        $no++;

                        $persetujuanHadir = $r[persetujuanHadir] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                        $persetujuanHadir = $r[persetujuanHadir] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanHadir;
                        $persetujuanHadir = $r[persetujuanHadir] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanHadir;

                        $sdmHadir = $r[sdmHadir] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                        $sdmHadir = $r[sdmHadir] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmHadir;
                        $sdmHadir = $r[sdmHadir] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmHadir;

                        list($mulaiHadir) = explode(" ", $r[mulaiHadir]);
                        list($selesaiHadir) = explode(" ", $r[selesaiHadir]);

                        $download = empty($r[fileHadir]) ? "" : "<a href=\"download.php?d=essHadir&f=$r[idHadir]\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"><img src=\"" . getIcon($r[fileHadir]) . "\"></a>";

                        $linkAtasan = "<a href=\"?par[mode]=app&par[idHadir]=$r[idHadir]" . getPar($par, "mode,idHadir") . "\">$persetujuanHadir</a>";
                        $linkSdm = isset($menuAccess[$s]["apprlv2"]) ? "<a href=\"?par[mode]=sdm&par[idHadir]=$r[idHadir]" . getPar($par, "mode,idHadir") . "\">$sdmHadir</a>" : $sdmHadir;

                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[nomorHadir] ?></td>
                        <td align="center"><?= getTanggal($r[tanggalHadir]) ?></td>
                        <td align="center"><?= getTanggal($mulaiHadir) ?></td>
                        <td align="center"><?= getTanggal($selesaiHadir) ?></td>
                        <td align="center"><?= $linkAtasan ?></td>
                        <td align="center"><?= $linkSdm ?></td>
                        <td align="center"><?= $download ?></td>
                        <td align="center"><a href="?par[mode]=det&par[idHadir]=<?= $r[idHadir] . getPar($par, "mode,idHadir") ?>" title="Detail Data" class="detail"><span>Detail</span></a>
                        </td>
                        <?php
                                if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
                                    $control = "<td align=\"center\"><a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idHadir]=$r[idHadir]" . getPar($par, "mode,idHadir") . "',900,500);\" ><span>Cetak</span></a>";
                                    if (in_array($r[persetujuanHadir], array("", "r")) || in_array($r[sdmHadir], array("", "r")))
                                        if (isset($menuAccess[$s]["edit"]) && $r[persetujuanHadir] != 't') $control .= "<a href=\"?par[mode]=edit&par[idHadir]=$r[idHadir]" . getPar($par, "mode,idHadir") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
                                    if (in_array($r[persetujuanHadir], array("")) || in_array($r[sdmHadir], array("")))
                                        if (isset($menuAccess[$s]["delete"])) $control .= "<a href=\"?par[mode]=del&par[idHadir]=$r[idHadir]" . getPar($par, "mode,idHadir") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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

function pdf()
{
    global $par, $arrParameter;

    require_once 'plugins/PHPPdf.php';

    $sql = "select * from att_hadir where idHadir='$par[idHadir]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
    list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
    list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);

    list($Y, $m, $d) = explode("-", $mulaiHadir_tanggal);
    $mulaiHari = $arrHari[date('w', mktime(0, 0, 0, $m, $d, $Y))];
    $tanggalHadir = $mulaiHari . ", " . getTanggal($mulaiHadir_tanggal, "t");
    if ($mulaiHadir_tanggal != $selesaiHadir_tanggal) {
        list($Y, $m, $d) = explode("-", $selesaiHadir_tanggal);
        $selesaiHari = $arrHari[date('w', mktime(0, 0, 0, $m, $d, $Y))];
        $tanggalHadir .= " s.d " . $selesaiHari . ", " . getTanggal($selesaiHadir_tanggal, "t");
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(15);

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 6, $arrParameter[86], 0, 0, 'L');

    $pdf->Ln();

    $pdf->SetFont('Arial', 'BU', 12);
    $pdf->Cell(180, 6, 'SURAT IJIN KETIDAKHADIRAN', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(180, 6, 'Nomor : ' . $r[nomorHadir], 0, 0, 'C');
    $pdf->Ln(15);

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(180, 6, 'Kami berikan ijin kepada :', 0, 0, 'L');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Nama', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->Cell(140, 6, getField("select name from emp where id='" . $r[idPegawai] . "'"), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Departemen', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->Cell(140, 6, getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='" . $r[idPegawai] . "' and status='1'"), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Jabatan', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->Cell(140, 6, getField("select pos_name from emp_phist where parent_id='" . $r[idPegawai] . "' and status='1'"), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Hari, Tanggal', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->Cell(140, 6, $tanggalHadir, 0, 0, 'L');
    $pdf->Ln();

    if ($mulaiHadir_tanggal == $selesaiHadir_tanggal && $mulaiHadir_waktu != "00:00:00") {
        $pdf->SetFont('Arial', 'B');
        $pdf->Cell(35, 6, 'Waktu', 0, 0, 'L');
        $pdf->SetFont('Arial');
        $pdf->Cell(5, 6, ':', 0, 0, 'L');
        if ($selesaiHadir_waktu == "00:00:00")
            $pdf->Cell(140, 6, substr($mulaiHadir_waktu, 0, 5), 0, 0, 'L');
        else
            $pdf->Cell(140, 6, substr($mulaiHadir_waktu, 0, 5) . " s.d " . substr($selesaiHadir_waktu, 0, 5), 0, 0, 'L');

        $pdf->Ln();
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Kategori', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->Cell(140, 6, getField("select namaData from mst_data where kodeData='$r[idKategori]'"), 0, 0, 'L');
    $pdf->Ln();

    if (!empty($r[idTipe])) {
        $pdf->SetFont('Arial', 'B');
        $pdf->Cell(35, 6, 'Tipe', 0, 0, 'L');
        $pdf->SetFont('Arial');
        $pdf->Cell(5, 6, ':', 0, 0, 'L');
        $pdf->Cell(140, 6, getField("select namaData from mst_data where kodeData='$r[idTipe]'"), 0, 0, 'L');
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(35, 6, 'Keterangan', 0, 0, 'L');
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(140, 6, $r[keteranganHadir], 0, 'L');
    $pdf->Ln(10);

    $pdf->SetFont('Arial');
    $pdf->Cell(110, 3, getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'") . ', ' . getTanggal($r[tanggalHadir], "t"), 0, 0, 'L');
    $pdf->Ln(25);
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(60, 5, '   ' . getField("select name from emp where id='" . $r[idPegawai] . "'") . '   ', 0, 0, 'C');
    $pdf->Cell(60, 5, getField("SELECT NAME FROM emp_phist t1 JOIN emp t2 ON t1.`manager_id` = t2.id WHERE parent_id='$r[idPegawai]' AND t1.status = '1'"), 0, 0, 'C');
    $pdf->Cell(60, 5, getField("SELECT NAME FROM emp_phist t1 JOIN emp t2 ON t1.`parent_id` = t2.id WHERE parent_id='$r[idAtasan]' AND t1.status = '1'"), 0, 0, 'C');

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'U');
    $pdf->Cell(60, 3, '                                    ', 0, 0, 'C');
    $pdf->Cell(60, 3, '                                    ', 0, 0, 'C');
    $pdf->Cell(60, 3, '                                    ', 0, 0, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(60, 3, ' ', 0, 0, 'C');
    $pdf->Cell(60, 3, 'Manajer Ybs.', 0, 0, 'C');
    $pdf->Cell(60, 3, 'Atasan Langsung Ybs.', 0, 0, 'C');
    $pdf->Ln(15);


    $pdf->AutoPrint(true);

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
        case "get":
            $text = gPegawai();
            break;
        case "detAts":
            $text = detailApproval();
            break;
        case "detSdm":
            $text = detailApproval();
            break;
        case "det":
            $text = detail();
            break;
        case "delFile":
            if (isset($menuAccess[$s]["edit"])) $text = hapusFile();
            else $text = lihat();
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
        case "app":
            $text = empty($_submit) ? form() : approve();
            break;
        case "sdm":
            $text = empty($_submit) ? form() : sdm();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}

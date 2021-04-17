<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/ess/cuti/";

function upload($idCuti)
{
    global $fFile;

    $fileUpload = $_FILES["fileCuti"]["tmp_name"];
    $fileUpload_name = $_FILES["fileCuti"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $fileCuti = "cuti-" . $idCuti . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $fileCuti);
    }
    if (empty($fileCuti)) $fileCuti = getField("select fileCuti from att_cuti where idCuti='$idCuti'");

    return $fileCuti;
}

function hapusFile()
{
    global $par, $fFile;

    $fileCuti = getField("select fileCuti from att_cuti where idCuti='$par[idCuti]'");
    if (file_exists($fFile . $fileCuti) and $fileCuti != "") unlink($fFile . $fileCuti);

    $sql = "update att_cuti set fileCuti='' where idCuti='$par[idCuti]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function gNomor()
{
    global $inp;

    $prefix = "IC";
    $date = empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
    $date = empty($date) ? date('d/m/Y') : $date;
    list($tanggal, $bulan, $tahun) = explode("/", $date);

    $nomor = getField("select nomorCuti from att_cuti where month(tanggalCuti)='$bulan' and year(tanggalCuti)='$tahun' order by nomorCuti desc limit 1");
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

function gCuti()
{
    global $inp, $par;

    $idTipe = empty($_GET[idTipe]) ? $inp[idTipe] : $_GET[idTipe];
    $tanggalCuti = empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
    $idPegawai = $par[idPegawai];
    list($tahunCuti) = explode("-", setTanggal($tanggalCuti));

    $sql = "select * from dta_cuti where idCuti='" . $idTipe . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $getMasaKerja = getField("select timestampdiff(month, join_date, current_date) from emp where id = '$idPegawai'");
    $jumlahCuti = getField("select sum(jumlahCuti) from att_cuti where idPegawai='" . $idPegawai . "' and idTipe='" . $idTipe . "' and persetujuanCuti='t' and sdmCuti='t'");

    $jatahCuti = $r[jatahCuti] - $jumlahCuti;
    $jatahCuti = $r[masaCuti] < $getMasaKerja ? $jatahCuti : 0;
    if (!empty($par[mulaiCuti]) && !empty($par[selesaiCuti])) {
        $start = new DateTime(setTanggal($par[mulaiCuti]));
        $end = new DateTime(setTanggal($par[selesaiCuti]));
        $end->modify('+1 day');
        $interval = $end->diff($start);
        $days = $interval->days;

        for ($i = 0; $i < $days; $i++) {
            $no = new DateTime(setTanggal($par[mulaiCuti]));
            $no->modify('+' . $i . ' day');
            $no->format('Y-m-d');
            $arrTglPilih[] = $no->format('Y-m-d');
        }

        $sql = "select * from dta_libur";
        $res = db($sql);
        while ($b = mysql_fetch_array($res)) {
            $hariLibur = $b[mulaiLibur];
            while ($hariLibur <= $b[selesaiLibur]) {
                $holidays[] = $hariLibur;
                $hariLibur = date('Y-m-d', strtotime($hariLibur . "+1 days"));
            }
        }

        foreach ($arrTglPilih as $key) {
            $cekDay = date('w', strtotime($key));
            if ($cekDay == 6 || $cekDay == 0 || in_array($key, $holidays)) {
                $days--;
            }
        }
    }

    return $jatahCuti . "\t" . $days;
}

function hapus()
{
    global $par;

    $sql = "delete from att_cuti where idCuti='$par[idCuti]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUsername;

    repField();

    $fileCuti = upload($par[idCuti]);
    $sql = "update att_cuti set fileCuti='$fileCuti',idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='" . setTanggal($inp[tanggalCuti]) . "', mulaiCuti='" . setTanggal($inp[mulaiCuti]) . "', selesaiCuti='" . setTanggal($inp[selesaiCuti]) . "', jatahCuti='" . setAngka($inp[jatahCuti]) . "', jumlahCuti='" . setAngka($inp[jumlahCuti]) . "', sisaCuti='" . setAngka($inp[sisaCuti]) . "', keteranganCuti='$inp[keteranganCuti]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idCuti='$par[idCuti]'";
    db($sql);

    echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function tambah()
{
    global $inp, $par, $cUser, $arrParameter;

    repField();

    $idCuti = getLastId("att_cuti", "idCuti");

    $inp[fileCuti] = upload($idCuti);
    $inp[tanggalCuti] = setTanggal($inp[tanggalCuti]);
    $inp[mulaiCuti] = setTanggal($inp[mulaiCuti]);
    $inp[selesaiCuti] = setTanggal($inp[selesaiCuti]);
    $inp[createBy] = $cUser;
    $inp[createTime] = date('Y-m-d H:i:s');

    $arrNama = arrayQuery("select id, name from emp");
    $arrMaster = arrayQuery("select idCuti, namaCuti from dta_cuti");
    $sql = "insert into att_cuti set idCuti = '$idCuti', fileCuti = '$inp[fileCuti]', idTipe = '$inp[idTipe]', idPegawai = '$inp[idPegawai]', idPengganti = '$inp[idPengganti]', idAtasan = '$inp[idAtasan]', nomorCuti = '$inp[nomorCuti]', tanggalCuti = '$inp[tanggalCuti]', mulaiCuti = '$inp[mulaiCuti]', selesaiCuti = '$inp[selesaiCuti]', jatahCuti = '$inp[jatahCuti]', jumlahCuti = '$inp[jumlahCuti]', sisaCuti = '$inp[sisaCuti]', keteranganCuti = '$inp[keteranganCuti]', createBy = '$inp[createBy]', createTime = '$inp[createTime]'";
    db($sql);

    $subjek = "Pemberitahuan Rencana Cuti $inp[tanggalCuti]";
    $link = "<a href=\"" . APP_URL . "/index.php?c=16&p=67&m=773&s=775\"><b>DISINI</b></a>";
    $isi1 = "
		<table width=\"100%\">
			<tr>
				<td colspan=\"3\">Sebagai informasi bahwasannya rencana Izin Cuti pada : </td> 
			</tr>
			<br>
			<tr>
				<td style=\"width:100px;\">Tanggal</td>
				<td style=\"width:10px;\">:</td>
				<td>" . getTanggal($inp[tanggalCuti], "t") . "</td>
			</tr>
			<tr>
				<td style=\"width:100px;\">Nomor</td>
				<td style=\"width:10px;\">:</td>
				<td><strong>" . $inp[nomorCuti] . "</strong></td>			
			</tr>
			<tr>
				<td style=\"width:100px;\">Tipe Cuti</td>
				<td style=\"width:10px;\">:</td>
				<td>" . $arrMaster[$inp[idTipe]] . "</td>
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
				<td>" . getTanggal($inp[mulaiCuti], "t") . " s/d " . getTanggal($inp[selesaiCuti], "t") . "</td>
			</tr>					
			<tr>
				<td style=\"width:100px;\">Keterangan</td>
				<td style=\"width:10px;\">:</td>
				<td>$inp[keteranganCuti]</td>
			</tr>
		</table>
		<table style=\"width:100%\">
			<br>
			<tr>
				<td colspan=\"3\">Dimohon untuk melakukan Approval Atasan pada nomor cuti di atas, silahkan klik $link</td>
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
//	sendMail($email, $subjek, $isi1);

    echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function approve()
{
    global $cUser, $par, $inp;

    $inp[approveBy] = $cUser;
    $inp[approveTime] = date('Y-m-d H:i:s');
    $sql = "UPDATE att_cuti SET persetujuanCuti = '$inp[persetujuanCuti]', catatanCuti = '$inp[catatanCuti]', approveBy = '$inp[approveBy]', approveTime = '$inp[approveTime]' WHERE idCuti = '$par[idCuti]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DI APPROVE');window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function sdm()
{
    global $cUser, $par, $inp;

    $inp[sdmBy] = $cUser;
    $inp[sdmTime] = date('Y-m-d H:i:s');
    $sql = "UPDATE att_cuti SET sdmCuti = '$inp[sdmCuti]', noteCuti = '$inp[noteCuti]', sdmBy = '$inp[sdmBy]', sdmTime = '$inp[sdmTime]' WHERE idCuti = '$par[idCuti]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DI APPROVE');window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function form()
{
    global $s, $par, $arrTitle, $cID, $ui, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('S06', 'X04', 'X05', 'X06')");

    $sql = "select * from att_cuti where idCuti='$par[idCuti]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
    if (empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');

    if (!isset($menuAccess[$s]["apprlv2"]))
        $filter = " AND (leader_id = '$cID' OR administration_id = '$cID')";
    $queryPegawai = "SELECT t1.id, concat(reg_no, ' - ', name) description FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";

    setValidation("is_null", "inp[nomorCuti]", "anda harus mengisi nomor");
    setValidation("is_null", "inp[idPegawai]", "anda harus mengisi nik");
    setValidation("is_null", "tanggalCuti", "anda harus mengisi tanggal");
    setValidation("is_null", "inp[idTipe]", "anda harus mengisi tipe cuti");
    setValidation("is_null", "mulaiCuti", "anda harus mengisi mulai");
    setValidation("is_null", "selesaiCuti", "anda harus mengisi selesai");
    setValidation("is_null", "inp[idAtasan]", "anda harus mengisi atasan");
    echo getValidation();

    $sql_ = "SELECT pos_name, div_id, location FROM emp_phist where parent_id = '$r[idPegawai]' AND status = '1'";
    $res_ = db($sql_);
    $r_ = mysql_fetch_assoc($res_);

    $r_[namaLokasi] = $arrMaster[$r_[location]];
    $r_[namaDivisi] = $arrMaster[$r_[div_id]];
    $r_[namaJabatan] = $r_[pos_name];

//	$queryCuti = "SELECT idCuti id, namaCuti description from dta_cuti where jatahCuti > 0 and (idLokasi='" . $r__[location] . "' or idLokasi='') and statusCuti='t' and '$r[tanggalCuti]' between mulaiCuti and selesaiCuti order by idCuti";
    $queryCuti = "SELECT idCuti id, namaCuti description from dta_cuti where statusCuti='t' and '$r[tanggalCuti]' between mulaiCuti and selesaiCuti order by idCuti";
    $queryEmp = "SELECT id, name as description FROM emp where status = '535'";
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div class="contentwrapper">
        <br clear="all" />
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" />
            </p>
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createField("Nomor", "inp[nomorCuti]", $r[nomorCuti], "", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createComboData("NIK - Nama", $queryPegawai, "id", "description", "inp[idPegawai]", $r[idPegawai], "onchange=\"getPegawai('" . getPar($par, "mode") . "');\"", "305px", "t", "t") ?></p>
                            <p><?= $ui->createField("Lokasi Kerja", "inp[namaLokasi]", $r_[namaLokasi], "", "", "", "", "", "t") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createField("Tanggal", "inp[tanggalCuti]", getTanggal($r[tanggalCuti]), "", "", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Jabatan", "inp[namaJabatan]", $r_[namaJabatan], "", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Divisi", "inp[namaDivisi]", $r_[namaDivisi], "", "", "", "", "", "t") ?></p>
                        </td>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA CUTI</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createComboData("Tipe Cuti", $queryCuti, "id", "description", "inp[idTipe]", $r[idTipe], "onchange=\"getJumlah('" . getPar($par, "mode, idCuti") . "');\"", "", "t", "t") ?></p>
                            <table style="width:100%">
                                <tr>
                                    <td style="width:50%">
                                        <p><?= $ui->createField("Mulai Cuti", "inp[mulaiCuti]", getTanggal($r[mulaiCuti]), "t", "t", "", "onchange=\"getJumlah('" . getPar($par, "mode, idCuti") . "');\"", "", "", "t") ?></p>
                                        <p><?= $ui->createField("Jatah Cuti", "inp[jatahCuti]", $r[jatahCuti], "", "t", "style=\"width:100px;\"", "", "", "t") ?></p>
                                        <p><?= $ui->createField("Sisa Cuti", "inp[sisaCuti]", $r[sisaCuti], "", "t", "style=\"width:100px;\"", "", "", "t") ?></p>
                                    </td>
                                    <td style="width:50%">
                                        <p><?= $ui->createField("Selesai Cuti", "inp[selesaiCuti]", getTanggal($r[selesaiCuti]), "t", "t", "", "onchange=\"getJumlah('" . getPar($par, "mode, idCuti") . "');\"", "", "", "t") ?></p>
                                        <p><?= $ui->createField("Pengambilan", "inp[jumlahCuti]", $r[jumlahCuti], "", "t", "style=\"width:100px;\"", "", "", "t") ?></p>
                                    </td>
                            </table>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createComboData("Pengganti", $queryEmp, "id", "description", "inp[idPengganti]", $r[idPengganti], "", "", "t", "") ?></p>
                            <p><?= $ui->createComboData("Atasan", $queryEmp, "id", "description", "inp[idAtasan]", $r[idAtasan], "", "", "t", "") ?></p>
                            <p><?= $ui->createTextArea("Keterangan", "inp[keteranganCuti]", $r[keteranganCuti], "") ?></p>
                            <p><?= $ui->createFile("Dokumen", "fileCuti", $r[fileCuti], "", "", "essCuti", $r[idCuti], "delFile") ?></p>
                        </td>
                    </tr>
                </table>
                <?php if ($par[mode] == "app") { ?>
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA APPROVAL ATASAN</h3>
                        </div>
                    </div>
                    <p><?= $ui->createRadio("Status", "inp[persetujuanCuti]", array("t" => "Disetujui", "f" => "Ditolak"), $r[persetujuanCuti]) ?></p>
                    <p><?= $ui->createTextArea("Keterangan", "inp[catatanCuti]", $r[catatanCuti]) ?></p>
                <?php }
                if ($par[mode] == "sdm") { ?>
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA APPROVAL SDM</h3>
                        </div>
                    </div>
                    <p><?= $ui->createRadio("Status", "inp[sdmCuti]", array("t" => "Disetujui", "f" => "Ditolak"), $r[sdmCuti]) ?></p>
                    <p><?= $ui->createTextArea("Keterangan", "inp[noteCuti]", $r[noteCuti]) ?></p>
                <?php } ?>
            </div>
        </form>
    </div>
    <?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $cID;

    if (empty($par[tahunCuti])) $par[tahunCuti] = date('Y');

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form method="post" class="stdform">
            <div id="pos_r">
                <?php if (isset($menuAccess[$s]["add"])) ?><a href="?par[mode]=add<?= getPar($par, 'mode,idCuti') ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
            <div id="pos_l">
                <p>
                    <input placeholder="Search.." type="text" id="par[filter]" name="par[filter]" style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" />
                    <?= comboYear("par[tahunCuti]", $par[tahunCuti]) ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>

        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th rowspan="2" width="20">No.</th>
                <th rowspan="2" width="100">Nomor</th>
                <th rowspan="2">Tipe Cuti</th>
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
            $filter = "where year(t1.tanggalCuti)='$par[tahunCuti]'";
            if (!isset($menuAccess[$s]["apprlv2"]))
                $filter .= " and (t3.leader_id ='$cID' or t3.administration_id='$cID')";
            if (!empty($par[filter]))
                $filter .= " and (lower(t1.nomorCuti) like '%" . strtolower($par[filter]) . "%' OR lower(t2.reg_no) like '%" . strtolower($par[filter]) . "%' OR lower(t2.name) like '%" . strtolower($par[filter]) . "%')";

            $arrTipe = arrayQuery("select idCuti, namaCuti from dta_cuti order by idCuti");
            $sql = "select t1.*, t2.name, t2.reg_no from att_cuti t1 left join emp t2 on (t1.idPegawai=t2.id) join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t1.nomorCuti";
            $no = 0;
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                $no++;
                $persetujuanCuti = $r[persetujuanCuti] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                $persetujuanCuti = $r[persetujuanCuti] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanCuti;
                $persetujuanCuti = $r[persetujuanCuti] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanCuti;

                $sdmCuti = $r[sdmCuti] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                $sdmCuti = $r[sdmCuti] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmCuti;
                $sdmCuti = $r[sdmCuti] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmCuti;

                $download = empty($r[fileCuti]) ? "" : "<a href=\"download.php?d=essCuti&f=$r[idCuti]\"><img src=\"" . getIcon($r[fileCuti]) . "\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";

                $linkAtasan = "<a href=\"?par[mode]=app&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "\">$persetujuanCuti</a>";
                $linkSdm = isset($menuAccess[$s]["apprlv2"]) ? "<a href=\"?par[mode]=sdm&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "\">$sdmCuti</a>" : $sdmCuti;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[nomorCuti] ?></td>
                    <td><?= $arrTipe["$r[idTipe]"] ?></td>
                    <td align="center"><?= getTanggal($r[tanggalCuti]) ?></td>
                    <td align="center"><?= getTanggal($r[mulaiCuti]) ?></td>
                    <td align="center"><?= getTanggal($r[selesaiCuti]) ?></td>
                    <td align="center"><?= $linkAtasan ?></td>
                    <td align="center"><?= $linkSdm ?></td>
                    <td align="center"><?= $download ?></td>
                    <td align="center"><a href="?par[mode]=det&par[idCuti]=<?= $r[idCuti] . getPar($par, 'mode,idCuti') ?>" title="Detail Data" class="detail"><span>Detail</span></a></td>
                    <?php
                    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
                        $control = "<td align=\"center\"><a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "',900,500);\" ><span>Cetak</span></a>";
                        if (in_array($r[persetujuanCuti], array("", "r")) || in_array($r[sdmCuti], array("", "r")))
                            if (isset($menuAccess[$s]["edit"]) && $r[persetujuanCuti] != 't') $control .= "<a href=\"?par[mode]=edit&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
                        if (in_array($r[persetujuanCuti], array("")) || in_array($r[sdmCuti], array("")))
                            if (isset($menuAccess[$s]["delete"])) $control .= "<a href=\"?par[mode]=del&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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

function detail()
{
    global $s, $par, $arrTitle, $cID, $ui;

    if (empty($par[tahunPinjaman])) $par[tahunPinjaman] = date('Y');
    $_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;

    $sql = "select * from att_cuti where idCuti='$par[idCuti]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
    if (empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');

    $sql_ = "select	id as idPegawai, reg_no as nikPegawai, name as namaPegawai
	from emp where id='" . $r[idPegawai] . "'";
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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" />
            </p>
            <div id="general">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Nomor", $r[nomorCuti]) ?></p>
                            <p><?= $ui->createSpan("NIK", $r_[nikPegawai]) ?></p>
                            <p><?= $ui->createSpan("Nama", $r_[namaPegawai]) ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tanggal", getTanggal($r[tanggalCuti], "t")) ?></p>
                            <p><?= $ui->createSpan("Jabatan", $r_[namaJabatan]) ?></p>
                            <p><?= $ui->createSpan("Divisi", $r_[namaDivisi]) ?></p>
                        </td>
                    </tr>
                </table>
                <div class="widgetbox">
                    <div class="title">
                        <h3>DATA CUTI</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Tipe Cuti", getField("select namaCuti from dta_cuti where idCuti='" . $r[idTipe] . "'")) ?></p>
                            <p><?= $ui->createSpan("Tanggal Cuti", getTanggal($r[mulaiCuti], "t") . " <strong>s.d</strong> " . getTanggal($r[selesaiCuti], "t")) ?></p>
                            <p><?= $ui->createSpan("Jatah Cuti", getAngka($r[jatahCuti])) ?></p>
                            <p><?= $ui->createSpan("Pengambilan", getAngka($r[jumlahCuti])) ?></p>
                            <p><?= $ui->createSpan("Sisa Cuti", getAngka($r[sisaCuti])) ?></p>
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
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[keteranganCuti])) ?></p>
                        </td>
                    </tr>
                </table>
                <?php
                $persetujuanCuti = "Belum Diproses";
                $persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
                $persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;
                $persetujuanCuti = $r[persetujuanCuti] == "r" ? "Diperbaiki" : $persetujuanCuti;
                ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL ATASAN</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Status", $persetujuanCuti) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[catatanCuti])) ?></p>
                        </td>
                        <td width="50%">&nbsp;</td>
                    </tr>
                </table>
                <?php

                $sdmCuti = "Belum Diproses";
                $sdmCuti = $r[sdmCuti] == "t" ? "Disetujui" : $sdmCuti;
                $sdmCuti = $r[sdmCuti] == "f" ? "Ditolak" : $sdmCuti;
                $sdmCuti = $r[sdmCuti] == "r" ? "Diperbaiki" : $sdmCuti;

                ?>
                <div class="widgetbox">
                    <div class="title">
                        <h3>APPROVAL SDM</h3>
                    </div>
                </div>
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createSpan("Status", $sdmCuti) ?></p>
                            <p><?= $ui->createSpan("Keterangan", nl2br($r[noteCuti])) ?></p>
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

    $sql = "select * from att_cuti where idCuti='$par[idCuti]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $tanggalCuti = getTanggal($r[mulaiCuti], "t");
    if ($r[mulaiCuti] != $r[selesaiCuti]) $tanggalCuti .= " - " . getTanggal($r[selesaiCuti], "t");

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(15);

    $pdf->Ln();
    $pdf->Image("images/info/logo-1.png", $pdf->GetX() + 0, $pdf->GetY() + 0, 45, 10);
    $pdf->Ln();

    $pdf->SetFont('Arial', 'BU', 14);
    $pdf->Cell(180, 6, 'PERMOHONAN CUTI / IZIN', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'BI', 10);
    $pdf->Cell(180, 6, 'LEAVE APPLICATION / PERMISSION', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(180, 6, 'Nomor : ' . $r[nomorCuti], 0, 0, 'C');
    $pdf->Ln(15);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Nama Karyawan', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Employees Name', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, getField("select name from emp where id='" . $r[idPegawai] . "'"), 0, 'L');
    $pdf->Ln(3.5);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Jabatan', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Position', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, getField("select pos_name from emp_phist where parent_id='" . $r[idPegawai] . "' and status='1'"), 0, 'L');
    $pdf->Ln(3.5);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Departemen', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Department', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='" . $r[idPegawai] . "' and status='1'"), 0, 'L');
    $pdf->Ln(3.5);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Tipe Cuti', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Type of leave', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, getField("select namaCuti from dta_cuti where idCuti='" . $r[idTipe] . "'"), 0, 'L');
    $pdf->Ln(3.5);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Jumlah Hak Cuti', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Sum of the leave rights', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(10, 6, getAngka($r[jatahCuti]), 0, 'R');
    $pdf->SetXY($setX + 70, $setY);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(10, 3, 'Hari', 0, 0, 'L');
    $pdf->SetXY($setX + 70, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(10, 6, 'Day', 0, 0, 'L');
    $pdf->Ln(8);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Jumlah Cuti Diambil', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Sum of leave in taken', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(10, 6, getAngka($r[jumlahCuti]), 0, 'R');
    $pdf->SetXY($setX + 70, $setY);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(10, 3, 'Hari', 0, 0, 'L');
    $pdf->SetXY($setX + 70, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(10, 6, 'Day', 0, 0, 'L');
    $pdf->Ln(8);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Sisa Cuti', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Rest of leave', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(10, 6, getAngka($r[sisaCuti]), 0, 'R');
    $pdf->SetXY($setX + 70, $setY);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(10, 3, 'Hari', 0, 0, 'L');
    $pdf->SetXY($setX + 70, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(10, 6, 'Day', 0, 0, 'L');
    $pdf->Ln(8);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Tanggal Awal Cuti', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Date of early leave', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, $tanggalCuti, 0, 'L');
    $pdf->Ln(3.5);

    $setX = $pdf->GetX();
    $setY = $pdf->GetY();
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(50, 3, 'Alasan Cuti', 0, 0, 'L');
    $pdf->SetXY($setX, $setY + 2);
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(50, 6, 'Reason of leave', 0, 0, 'L');
    $pdf->SetXY($setX + 50, $setY);
    $pdf->SetFont('Arial');
    $pdf->Cell(5, 6, ':', 0, 0, 'L');
    $pdf->MultiCell(125, 6, $r[keteranganCuti], 0, 'L');
    $pdf->Ln(20);

    $pdf->SetFont('Arial', 'B');
    $pdf->Cell(60, 3, 'Menyetujui,', 0, 0, 'C');
    $pdf->Cell(60, 3, 'Menyetujui,', 0, 0, 'C');
    $pdf->Cell(60, 3, 'Pemohon Cuti,', 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'I');
    $pdf->Cell(60, 6, 'Approved by,', 0, 0, 'C');
    $pdf->Cell(60, 6, 'Approved by,', 0, 0, 'C');
    $pdf->Cell(60, 6, 'Leave applicant,', 0, 0, 'C');
    $pdf->Ln(20);
    $pdf->SetFont('Arial', 'BU');
    $pdf->Cell(60, 5, getField("select name from emp where id='" . $r[idAtasan] . "'"), 0, 0, 'C');
    $pdf->Cell(60, 5, getField("select name from emp t1 join app_user t2 on t1.id = t2.idPegawai where t2.username='" . $r[sdmBy] . "'"), 0, 0, 'C');
    $pdf->Cell(60, 5, getField("select name from emp where id='" . $r[idPegawai] . "'"), 0, 0, 'C');
    $pdf->Ln();
    $pdf->SetFont('Arial');
    $pdf->Cell(60, 3, getField("select pos_name from emp_phist where parent_id='" . $r[idAtasan] . "' AND status = '1'"), 0, 0, 'C');
    $pdf->Cell(60, 3, getField("select pos_name from emp_phist t1 join app_user t2 on t1.parent_id = t2.idPegawai where t2.username='" . $r[sdmBy] . "' AND t1.status = '1'"), 0, 0, 'C');
    $pdf->Cell(60, 3, getField("select pos_name from emp_phist where parent_id='" . $r[idPegawai] . "' AND status = '1'"), 0, 0, 'C');

    $pdf->Ln();
    $pdf->Cell(60, 6, 'Tgl/Date :                            ', 0, 0, 'C');
    $pdf->Cell(60, 6, 'Tgl/Date :                 ', 0, 0, 'C');
    $pdf->Cell(60, 6, 'Tgl/Date :  ' . getTanggal($r[tanggalCuti], "t"), 0, 0, 'C');
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
        case "get":
            $text = gPegawai();
            break;
        case "cut":
            $text = gCuti();
            break;
        case "print":
            $text = pdf();
            break;
        case "det":
            $text = detail();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "delFile":
            if (isset($menuAccess[$s]["edit"])) $text = hapusFile();
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

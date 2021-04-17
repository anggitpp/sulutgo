<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
if (empty($cID)) {
	echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
	exit();
}

$fFile = "files/ess/koreksi/";

function upload($idKoreksi)
{
	global $fFile;

	$fileUpload = $_FILES["fileKoreksi"]["tmp_name"];
	$fileUpload_name = $_FILES["fileKoreksi"]["name"];
	if (($fileUpload != "") and ($fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$fileKoreksi = "koreksi-" . $idKoreksi . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileKoreksi);
	}
	if (empty($fileKoreksi)) $fileKoreksi = getField("select fileKoreksi from att_koreksi where idKoreksi='$idKoreksi'");

	return $fileKoreksi;
}
function hapusFile()
{
	global $par, $fFile;

	$fileKoreksi = getField("select fileKoreksi from att_koreksi where idKoreksi='$par[idKoreksi]'");
	if (file_exists($fFile . $fileKoreksi) and $fileKoreksi != "") unlink($fFile . $fileKoreksi);

	$sql = "update att_koreksi set fileKoreksi='' where idKoreksi='$par[idKoreksi]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function gNomor()
{
	global $inp;

	$prefix = "KA";
	$date = empty($_GET[tanggalKoreksi]) ? $inp[tanggalKoreksi] : $_GET[tanggalKoreksi];
	$date = empty($date) ? date('d/m/Y') : $date;
	list($tanggal, $bulan, $tahun) = explode("/", $date);

	$nomor = getField("select nomorKoreksi from att_koreksi where month(tanggalKoreksi)='$bulan' and year(tanggalKoreksi)='$tahun' order by nomorKoreksi desc limit 1");
	list($count) = explode("/", $nomor);

	return str_pad(($count + 1), 3, "0", STR_PAD_LEFT) . "/" . $prefix . "-" . getRomawi($bulan) . "/" . $tahun;
}

function gAbsen()
{
	global $par;

	$sql = "select * from att_absen where idPegawai='" . $par[idPegawai] . "' and tanggalAbsen='" . setTanggal($par[tanggalAbsen]) . "'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$r[masukAbsen] = substr($r[masukAbsen], 0, 5);
	$r[pulangAbsen] = substr($r[pulangAbsen], 0, 5);

	return json_encode($r);
}

function hapus()
{
	global $par;

	$sql = "delete from att_koreksi where idKoreksi='$par[idKoreksi]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode,idKoreksi") . "';</script>";
}

function ubah()
{
	global $inp, $par, $cUser;

	repField();

	$inp[fileKoreksi] = upload($par[idKoreksi]);
	$inp[tanggalKoreksi] = setTanggal($inp[tanggalKoreksi]);
	$inp[mulaiKoreksi] = setTanggal($inp[mulaiKoreksi_tanggal]) . " " . $inp[mulaiKoreksi];
	$inp[selesaiKoreksi] = setTanggal($inp[mulaiKoreksi_tanggal]) . " " . $inp[selesaiKoreksi];
	$inp[durasiKoreksi] = "CASE WHEN pulangKoreksi >= masukKoreksi THEN TIMEDIFF(pulangKoreksi, masukKoreksi) ELSE ADDTIME(TIMEDIFF('24:00:00', masukKoreksi), TIMEDIFF(pulangKoreksi, '00:00:00')) END";
	$inp[update_by] = $cUser;
	$inp[update_date] = date('Y-m-d H:i:s');

	$sql = "update att_koreksi set fileKoreksi = '$inp[fileKoreksi]', idPegawai = '$inp[idPegawai]', nomorKoreksi = '$inp[nomorKoreksi]', tanggalKoreksi = '$inp[tanggalKoreksi]', mulaiKoreksi = '$inp[mulaiKoreksi]', selesaiKoreksi = '$inp[selesaiKoreksi]', masukKoreksi = '$inp[masukKoreksi]', masukKoreksi_jam = '$inp[masukKoreksi_jam]', pulangKoreksi = '$inp[pulangKoreksi]', pulangKoreksi_jam = '$inp[pulangKoreksi_jam]', durasiKoreksi = $inp[durasiKoreksi], keteranganKoreksi = '$inp[keteranganKoreksi]', updateBy = '$inp[create_by]', updateTime = '$inp[create_date]' where idKoreksi='$par[idKoreksi]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,idKoreksi") . "';</script>";
}

function tambah()
{
	global $inp, $par, $cUser, $arrParameter;

	repField();

	$idKoreksi = getLastId("att_koreksi", "idKoreksi");
	$inp[fileKoreksi] = upload($idKoreksi);
	$inp[tanggalKoreksi] = setTanggal($inp[tanggalKoreksi]);
	$inp[mulaiKoreksi] = setTanggal($inp[mulaiKoreksi_tanggal]) . " " . $inp[mulaiKoreksi];
	$inp[selesaiKoreksi] = setTanggal($inp[mulaiKoreksi_tanggal]) . " " . $inp[selesaiKoreksi];
	$inp[durasiKoreksi] = "CASE WHEN pulangKoreksi >= masukKoreksi THEN TIMEDIFF(pulangKoreksi, masukKoreksi) ELSE ADDTIME(TIMEDIFF('24:00:00', masukKoreksi), TIMEDIFF(pulangKoreksi, '00:00:00')) END";
	$inp[create_by] = $cUser;
	$inp[create_date] = date('Y-m-d H:i:s');

	$arrNama = arrayQuery("select id, name from emp");

	$sql = "insert into att_koreksi set idKoreksi = '$idKoreksi', fileKoreksi = '$inp[fileKoreksi]', idPegawai = '$inp[idPegawai]', nomorKoreksi = '$inp[nomorKoreksi]', tanggalKoreksi = '$inp[tanggalKoreksi]', mulaiKoreksi = '$inp[mulaiKoreksi]', selesaiKoreksi = '$inp[selesaiKoreksi]', masukKoreksi = '$inp[masukKoreksi]', masukKoreksi_jam = '$inp[masukKoreksi_jam]', pulangKoreksi = '$inp[pulangKoreksi]', pulangKoreksi_jam = '$inp[pulangKoreksi_jam]', durasiKoreksi = $inp[durasiKoreksi], keteranganKoreksi = '$inp[keteranganKoreksi]', createBy = '$inp[create_by]', createTime = '$inp[create_date]'";
	db($sql);

	$subjek = "Pemberitahuan Rencana Koreksi Absen $inp[tanggalKoreksi]";
	$link = "<a href=\"".APP_URL."/index.php?c=16&p=67&m=770&s=772\"><b>DISINI</b></a>";
	$isi = "
	<table width=\"100%\">
		<tr>
			<td colspan=\"3\">Sebagai informasi bahwasannya Koreksi Absen pada : </td>
		</tr>
		<br>
		<tr>
			<td style=\"width:200px;\">Tanggal</td>
			<td style=\"width:10px;\">:</td>
			<td>" . getTanggal($inp[tanggalKoreksi], "t") . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Nomor</td>
			<td style=\"width:10px;\">:</td>
			<td><strong>" . $inp[nomorKoreksi] . "</strong></td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Nama</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $arrNama[$inp[idPegawai]] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Tanggal</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $inp[mulaiKoreksi_tanggal] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Mulai Koreksi</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $inp[mulaiKoreksi] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Selesai Koreksi</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $inp[selesaiKoreksi] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Perubahan Mulai</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $inp[masukKoreksi] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Perubahan Selesai</td>
			<td style=\"width:10px;\">:</td>
			<td>" . $inp[pulangKoreksi] . "</td>
		</tr>
		<tr>
			<td style=\"width:200px;\">Keterangan</td>
			<td style=\"width:10px;\">:</td>
			<td>$inp[keteranganKoreksi]</td>
		</tr>
	</table>

	<table>
		<br>
		<tr>
			<td colspan=\"3\">Dimohon untuk melakukan Approval Atasan pada nomor koreksi di atas, silahkan klik $link</td>
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
			<td>".$arrParameter[86]."</td>
		</tr>
	</table>";

	$inp[idAtasan] = getField("select leader_id from emp_phist where parent_id = '$inp[idPegawai]'");
	$email = getField("select email from dta_pegawai where id = '$inp[idAtasan]'");
	sendMail($email, $subjek, $isi);

	echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?" . getPar($par, "mode,idKoreksi") . "';</script>";
}

function form()
{
	global $s, $par, $arrTitle, $cID, $ui;

	$sql = "select * from att_koreksi where idKoreksi='$par[idKoreksi]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	if (empty($r[nomorKoreksi])) $r[nomorKoreksi] = gNomor();
	if (empty($r[tanggalKoreksi])) $r[tanggalKoreksi] = date('Y-m-d');
	list($mulaiKoreksi_tanggal, $mulaiKoreksi) = explode(" ", $r[mulaiKoreksi]);
	list($selesaiKoreksi_tanggal, $selesaiKoreksi) = explode(" ", $r[selesaiKoreksi]);
	$masukKoreksi_jam = $r[masukKoreksi_jam] == "t" ? "checked" : "";
	$pulangKoreksi_jam = $r[pulangKoreksi_jam] == "t" ? "checked" : "";

	setValidation("is_null", "mulaiKoreksi_tanggal", "anda harus mengisi tanggal");
	echo getValidation();

	if (!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
	$sql_ = "select	id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='" . $r[idPegawai] . "'";
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
		<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<p class="btnSave">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
				<input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" />
			</p>
			<br clear="all" />
			<div id="general">
				<input type="hidden" id="inp[idPegawai]" name="inp[idPegawai]" value="<?= $r[idPegawai] ?>">
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createField("Nomor", "inp[nomorKoreksi]", $r[nomorKoreksi], "", "t", "", "", "", "t") ?></p>
							<p><?= $ui->createField("Nama", "inp[namaPegawai]", $r_[namaPegawai], "", "t", "", "", "", "t") ?></p>
							<p><?= $ui->createField("NPP", "inp[nikPegawai]", $r_[nikPegawai], "", "t", "", "", "", "t") ?></p>
						</td>
						<td width="50%">
							<p><?= $ui->createField("Tanggal", "inp[tanggalKoreksi]", getTanggal($r[tanggalKoreksi]), "", "", "", "onchange=\"getNomor('" . getPar($par, "mode") . "');\"", "", "", "t") ?></p>
							<p><?= $ui->createField("Jabatan", "inp[namaJabatan]", $r_[namaJabatan], "", "", "", "", "", "t") ?></p>
							<p><?= $ui->createField("Divisi", "inp[namaDivisi]", $r_[namaDivisi], "", "", "", "", "", "t") ?></p>
						</td>
					</tr>
				</table>
				<div class="widgetbox">
					<div class="title">
						<h3>DATA ABSENSI</h3>
					</div>
				</div>
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createField("Tanggal", "inp[mulaiKoreksi_tanggal]", getTanggal($mulaiKoreksi_tanggal), "t", "t", "", "onchange=\"getAbsen('" . getPar($par, "mode,idPegawai,tanggalAbsen") . "');\"", "", "", "t") ?></p>
							<p><?= $ui->createTimePicker("Jam Datang", "mulaiKoreksi", substr($mulaiKoreksi, 0, 5), "", "t") ?></p>
							<p><?= $ui->createTimePicker("Jam Pulang", "selesaiKoreksi", substr($selesaiKoreksi, 0, 5), "", "t") ?></p>
							<p><?= $ui->createTextArea("Keterangan", "inp[keteranganKoreksi]", $r[keteranganKoreksi], "", "t") ?></p>
						</td>
						<td width="50%">
							<p>
								<label class="l-input-small">Koreksi Jam</label>
								<div class="field">
									<input type="checkbox" id="inp[masukKoreksi_jam]" name="inp[masukKoreksi_jam]" value="t" onclick="cekMasuk();" <?= $masukKoreksi_jam ?> /> Datang&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="checkbox" id="inp[pulangKoreksi_jam]" name="inp[pulangKoreksi_jam]" value="t" onclick="cekPulang();" <?= $pulangKoreksi_jam ?> /> Pulang
								</div>
							</p>
							<p><?= $ui->createTimePicker("Perubahan Datang", "masukKoreksi", substr($r[masukKoreksi], 0, 5), "", "", "onchange=\"cekMasuk();\"") ?></p>
							<p><?= $ui->createTimePicker("Perubahan Pulang", "pulangKoreksi", substr($r[pulangKoreksi], 0, 5), "", "", "onchange=\"cekPulang();\"") ?></p>
							<p><?= $ui->createFile("Dokumen", "fileKoreksi", $r[fileKoreksi], "", "", "essKoreksi", $r[idKoreksi], "delFile") ?></p>
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

	if (empty($par[tahunKoreksi])) $par[tahunKoreksi] = date('Y');

	$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<?php require_once "tmpl/emp_header_basic.php"; ?>
		<form method="post" class="stdform">
			<div id="pos_l" style="float:left;">
				<p>
					<input placeholder="Search.." type="text" id="par[filter]" name="par[filter]" style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" />
					<?= comboYear("par[tahunKoreksi]", $par[tahunKoreksi]) ?>
					<input type="submit" value="GO" class="btn btn_search btn-small" /> </td>
				</p>
			</div>
			<div id="pos_r">
				<?php if (isset($menuAccess[$s]["add"])) ?><a href="?par[mode]=add<?= getPar($par, 'mode,idKoreksi') ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th rowspan="2" width="20">No.</th>
					<th rowspan="2" style="*">Nomor</th>
					<th rowspan="2" width="75">Tanggal</th>
					<th colspan="3" width="175">Koreksi</th>
					<th colspan="2" width="100">Approval</th>
					<th rowspan="2" width="200">Keterangan</th>
					<th rowspan="2" width="30">Bukti</th>
					<th rowspan="2" width="30">Detail</th>
					<th rowspan="2" width="30">Print</th>
					<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th rowspan="2" width="50">Kontrol</th>
				</tr>
				<tr>
					<th width="75">Tanggal</th>
					<th width="50">Datang</th>
					<th width="50">Pulang</th>
					<th width="50">Atasan</th>
					<th width="50">MANAGER</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "where year(t1.tanggalKoreksi)='$par[tahunKoreksi]'";
					if (!empty($cID)) $filter .= " and t1.idPegawai='" . $cID . "'";
					if (!empty($par[filter]))
						$filter .= " and (lower(t1.nomorKoreksi) like '%" . strtolower($par[filter]) . "%')";

					$sql = "select * from att_koreksi t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorKoreksi";
					$res = db($sql);
					while ($r = mysql_fetch_array($res)) {
						$no++;
						$persetujuanKoreksi = $r[persetujuanKoreksi] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
						$persetujuanKoreksi = $r[persetujuanKoreksi] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanKoreksi;
						$persetujuanKoreksi = $r[persetujuanKoreksi] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanKoreksi;

						$sdmKoreksi = $r[sdmKoreksi] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
						$sdmKoreksi = $r[sdmKoreksi] == "f" ? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKoreksi;
						$sdmKoreksi = $r[sdmKoreksi] == "r" ? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmKoreksi;
						$view = empty($r[fileKoreksi]) ? "" : "<a href=\"download.php?d=essKoreksi&f=$r[idKoreksi]\"><img src=\"" . getIcon($r[fileKoreksi]) . "\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";

						list($mulaiKoreksi) = explode(" ", $r[mulaiKoreksi]);

						if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
							$control = "<td align=\"center\">";
							if (in_array($r[persetujuanKoreksi], array("", "r")) || in_array($r[sdmKoreksi], array("", "r"))) {
								if (isset($menuAccess[$s]["edit"]) && $r[persetujuanKoreksi] != 't') $control .= "<a href=\"?par[mode]=edit&par[idKoreksi]=$r[idKoreksi]" . getPar($par, "mode,idKoreksi") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
							}
							if (in_array($r[persetujuanKoreksi], array("")) || in_array($r[sdmKoreksi], array(""))) {
								if (isset($menuAccess[$s]["delete"])) $control .= "<a href=\"?par[mode]=del&par[idKoreksi]=$r[idKoreksi]" . getPar($par, "mode,idKoreksi") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
							}
							$control .= "</td>";
						}
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[nomorKoreksi] ?></td>
						<td align="center"><?= getTanggal($r[tanggalKoreksi]) ?></td>
						<td align="center"><?= getTanggal($mulaiKoreksi) ?></td>
						<td align="center"><?= substr($r[masukKoreksi], 0, 5) ?></td>
						<td align="center"><?= substr($r[pulangKoreksi], 0, 5) ?></td>
						<td align="center"><?= $persetujuanKoreksi ?></td>
						<td align="center"><?= $sdmKoreksi ?></td>
						<td><?= $r[keteranganKoreksi] ?></td>
						<td align="center"><?= $view ?></td>
						<td align="center">
							<a href="?par[mode]=det&par[idKoreksi]=<?= $r[idKoreksi].getPar($par, 'mode,idKoreksi') ?>" title="Detail Data" class="detail"><span>Detail</span></a>
						</td>
						<td align="center">
							<a href="#" title="Detail Data" class="detail" onclick="openBox('ajax.php?par[mode]=print&par[id]=<?= $r[idKoreksi].getPar($par, 'mode') ?>',1200,600);"><span>Detail</span></a>
						</td>
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
	global $s, $par, $arrTitle, $ui;

	$sql = "select * from att_koreksi where idKoreksi='$par[idKoreksi]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	if (empty($r[nomorKoreksi])) $r[nomorKoreksi] = gNomor();
	if (empty($r[tanggalKoreksi])) $r[tanggalKoreksi] = date('Y-m-d');
	list($mulaiKoreksi_tanggal, $mulaiKoreksi) = explode(" ", $r[mulaiKoreksi]);
	list($selesaiKoreksi_tanggal, $selesaiKoreksi) = explode(" ", $r[selesaiKoreksi]);
	$arrKoreksi[] = $r[masukKoreksi_jam] == "t" ? "Datang" : "";
	$arrKoreksi[] = $r[pulangKoreksi_jam] == "t" ? "Pulang" : "";

	$sql_ = "select	id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='" . $r[idPegawai] . "'";
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
		<form id="form" name="form" method="post" class="stdform" enctype="multipart/form-data">
			<p class="btnSave">
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode,idPegawai") ?>';" />
			</p>
			<div id="general">
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createSpan("Nomor", $r[nomorKoreksi]) ?></p>
							<p><?= $ui->createSpan("NPP", $r_[nikPegawai]) ?></p>
							<p><?= $ui->createSpan("Nama", $r_[namaPegawai]) ?></p>
						</td>
						<td width="50%">
							<p><?= $ui->createSpan("Tanggal", getTanggal($r[tanggalKoreksi], "t")) ?></p>
							<p><?= $ui->createSpan("Jabatan", $r_[namaJabatan]) ?></p>
							<p><?= $ui->createSpan("Divisi", $r_[namaDivisi]) ?></p>
						</td>
					</tr>
				</table>
				<div class="widgetbox">
					<div class="title">
						<h3>DATA ABSENSI</h3>
					</div>
				</div>
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createSpan("Tanggal", getTanggal($mulaiKoreksi_tanggal, "t")) ?></p>
							<p><?= $ui->createSpan("Jam Datang", substr($mulaiKoreksi, 0, 5)) ?></p>
							<p><?= $ui->createSpan("Jam Pulang", substr($selesaiKoreksi, 0, 5)) ?></p>
							<p><?= $ui->createSpan("Keterangan", nl2br($r[keteranganKoreksi])) ?></p>
						</td>
						<td width="55%">
							<p><?= $ui->createSpan("Koreksi Jam", implode(", ", $arrKoreksi)) ?></p>
							<p><?= $ui->createSpan("Perubahan Datang", substr($r[masukKoreksi], 0, 5)) ?></p>
							<p><?= $ui->createSpan("Perubahan Pulang", substr($r[pulangKoreksi], 0, 5)) ?></p>
						</td>
					</tr>
				</table>

				<?php
					$persetujuanKoreksi = "Belum Diproses";
					$persetujuanKoreksi = $r[persetujuanKoreksi] == "t" ? "Disetujui" : $persetujuanKoreksi;
					$persetujuanKoreksi = $r[persetujuanKoreksi] == "f" ? "Ditolak" : $persetujuanKoreksi;
					$persetujuanKoreksi = $r[persetujuanKoreksi] == "r" ? "Diperbaiki" : $persetujuanKoreksi;
					?>
				<div class="widgetbox">
					<div class="title">
						<h3>APPROVAL ATASAN</h3>
					</div>
				</div>
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createSpan("Status", $persetujuanKoreksi) ?></p>
							<p><?= $ui->createSpan("Keterangan", nl2br($r[catatanKoreksi])) ?></p>
						</td>
						<td width="50%">&nbsp;</td>
					</tr>
				</table>

				<?php
					$sdmKoreksi = "Belum Diproses";
					$sdmKoreksi = $r[sdmKoreksi] == "t" ? "Disetujui" : $sdmKoreksi;
					$sdmKoreksi = $r[sdmKoreksi] == "f" ? "Ditolak" : $sdmKoreksi;
					$sdmKoreksi = $r[sdmKoreksi] == "r" ? "Diperbaiki" : $sdmKoreksi;
					?>
				<div class="widgetbox">
					<div class="title">
						<h3>APPROVAL MANAGER</h3>
					</div>
				</div>
				<table width="100%">
					<tr>
						<td width="50%">
							<p><?= $ui->createSpan("Status", $sdmKoreksi) ?></p>
							<p><?= $ui->createSpan("Keterangan", nl2br($r[noteKoreksi])) ?></p>
						</td>
						<td width="55%">&nbsp;</td>
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

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	$sql = "select t1.*, t2.reg_no, t2.name, t3.pos_name, t3.div_id from att_koreksi t1 join emp t2 on t1.idPegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') where idKoreksi = '$par[id]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$r[cekm] = empty($r[masukKoreksi_jam]) ? "" : "Datang";
	$r[cekf] = empty($r[pulangKoreksi_jam]) ? "" : "Pulang";

	list($r[tglKoreksi], $r[jamMasuk]) = explode(" ", $r[mulaiKoreksi]);
	list($r[tglKoreksiSelesai], $r[jamSelesai]) = explode(" ", $r[selesaiKoreksi]);

	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetLeftMargin(15);

	$pdf->SetFont('Arial', 'B', 13);
	$pdf->setFillColor(0, 0, 0);
	$pdf->SetTextColor(255, 255, 255);

	$pdf->Cell(180, 8, 'Rekap Presensi Harian - Koreksi Absen', 0, 0, 'C', '#000000');
	$pdf->SetTextColor(0, 0, 0);

	$pdf->setFillColor(230, 230, 230);
	$pdf->Ln(15);
	$pdf->SetFont('Arial', '', 10);

	$pdf->Cell(35, 6, 'NOMOR', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[nomorKoreksi], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'TANGGAL', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, getTanggal($r[tanggalKoreksi]), 0, 0);
	$pdf->SetFont('Arial', '', 10);

	$pdf->Ln(7);

	$pdf->Cell(35, 6, 'NPP', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, strtoupper($r[reg_no]), 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'JABATAN', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[pos_name], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Cell(35, 6, 'NAMA', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[name], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'DIVISI', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $arrMaster[$r[div_id]], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Ln();
	$pdf->Ln();

	$pdf->Cell(35, 6, 'TGL ABSENSI', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, getTanggal($r[tglKoreksi]), 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'KOREKSI JAM', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[cekm] . " " . $r[cekf], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Cell(35, 6, 'JAM DATANG', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[jamMasuk], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'KOREKSI DATANG', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[masukKoreksi], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Cell(35, 6, 'JAM PULANG', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[jamSelesai], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(60, 6, ' ', 0, 0, 'C');
	$pdf->Cell(35, 6, 'KOREKSI PULANG', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[pulangKoreksi], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Cell(35, 6, 'KETERANGAN', 0, 0, 'L', 'true');
	$pdf->Cell(3, 6, ' ', 0, 0, 'C');
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(5, 6, $r[keteranganKoreksi], 0, 0);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Ln(7);

	$pdf->Ln();
	$pdf->Ln();

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
		case "abs":
			$text = gAbsen();
			break;
		case "print":
			$text = pdf();
			break;
		case "peg":
			$text = pegawai();
			break;
		case "del":
			if (isset($menuAccess[$s]["delete"])) $text = hapus();
			else $text = lihat();
			break;
		case "det":
			$text = detail();
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
		default:
			$text = lihat();
			break;
	}
	return $text;
}
?>
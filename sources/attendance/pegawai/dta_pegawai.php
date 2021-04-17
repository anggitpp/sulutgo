<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function cek()
{
	global $inp, $par;
	if (getField("select idSetting from att_setting where kodeSetting='$inp[kodeSetting]' and idPegawai!='$par[idPegawai]'"))
		return "maaf, id absen \" $inp[kodeSetting] \" sudah ada";
}

function ubah()
{
	global $inp, $par, $cUser;

	repField();

	$idSetting = getField("select idSetting from att_setting order by idSetting desc limit 1") + 1;

	$sql = getField("select idSetting from att_setting where idPegawai='$par[idPegawai]'") ?
		"update att_setting set idMesin='$inp[idMesin]', idShift='$inp[idShift]', kodeSetting='$inp[kodeSetting]', nomorSetting='$inp[nomorSetting]', mulaiSetting='" . setTanggal($inp[mulaiSetting]) . "', selesaiSetting='" . setTanggal($inp[selesaiSetting]) . "', keteranganSetting='$inp[keteranganSetting]', statusSetting='$inp[statusSetting]', update_by='$cUser', update_date='" . date('Y-m-d H:i:s') . "' where idPegawai='$par[idPegawai]'"
		: "insert into att_setting (idSetting, idPegawai, idMesin, idShift, kodeSetting, nomorSetting, mulaiSetting, selesaiSetting, keteranganSetting, statusSetting, create_by, create_date) values ('$idSetting', '$par[idPegawai]', '$inp[idMesin]', '$inp[idShift]', '$inp[kodeSetting]', '$inp[nomorSetting]', '" . setTanggal($inp[mulaiSetting]) . "', '" . setTanggal($inp[selesaiSetting]) . "', '$inp[keteranganSetting]', '$inp[statusSetting]', '$cUser', '" . date('Y-m-d H:i:s') . "')";
	db($sql);
	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,idPegawai") . "';</script>";
}

function lihat()
{
	global $s, $par, $arrTitle, $menuAccess, $areaCheck;

	$arr_types = "SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'S04' AND `statusData` = 't' ORDER BY `urutanData`";

	$arrMaster = arrayQuery("SELECT kodeData id, namaData description FROM mst_data where kodeCategory IN ('X03', 'X04', 'X05', 'X06')");
	$queryLokasi = "SELECT kodeData id, namaData description FROM mst_data where kodeCategory = 'S06' and kodeData IN($areaCheck)";

	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form action="" method="post" id="form" class="stdform">
			<p class="btnSave">
				<?= comboData($arr_types, "kodeData", "namaData", "par[tipePegawai]", "-- Semua Status --", $par['tipePegawai'], "onchange=\"document.getElementById('form').submit();\"") ?>
				<?= comboData($queryLokasi, "id", "description", "par[idLokasi]", " -- LOKASI --", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"") ?>
			</p>
			<div id="pos_l" style="float:left;">
				<p>
					<input type="text" name="par[filterData]" value="<?= $par[filterData] ?>" placeholder="Search.." style="width:200px;" />
					<input type="submit" value="GO" class="btn btn_search btn-small" />
				</p>
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th width="*">Nama</th>
					<th width="100">NPP</th>
					<th width="150">Jabatan</th>
					<th width="150">Divisi</th>
					<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "WHERE t1.status = '535' and t2.location IN ($areaCheck)";
					if (!empty($par[tipePegawai]))
						$filter .= "AND t1.cat = '$par[tipePegawai]'";
					if (!empty($par[idLokasi]))
						$filter .= "AND t2.location = '$par[idLokasi]'";
					if (!empty($par[filterData]))
						$filter .= " AND (t1.name LIKE '%$par[filterData]%' or t1.reg_no LIKE '%$par[filterData]%'";
					$sql = "SELECT t1.id, t1.name, t1.reg_no, t2.pos_name, t2.div_id FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by t1.name";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_assoc($res)) {
						$no++;
						$control = "<a href=\"?par[mode]=det&par[idPegawai]=$r[id]" . getPar($par, "mode,idPegawai") . "\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";
						if (!empty($menuAccess[$s]["edit"]))
							$control .= "<a href=\"?par[mode]=edit&par[idPegawai]=$r[id]" . getPar($par, "mode,idPegawai") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[name] ?></td>
						<td align="center"><?= $r[reg_no] ?></td>
						<td><?= $r[pos_name] ?></td>
						<td><?= $arrMaster[$r[div_id]] ?></td>
						<td align="center"><?= $control ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?php
}

function form()
{
	global $s, $par, $arrTitle, $ui;

	$_SESSION["curr_emp_id"] = $par[idPegawai];

	$queryShift = "SELECT idShift id, namaShift description from dta_shift where statusShift='t' order by idShift";
	$queryMesin = "SELECT idMesin id, namaMesin description from dta_mesin where statusMesin='t' order by idMesin";

	setValidation("is_null", "inp[idShift]", "anda harus mengisi jadwal kerja");
	setValidation("is_null", "inp[idMesin]", "anda harus mengisi mesin absen");
	setValidation("is_null", "inp[kodeSetting]", "anda harus mengisi id absen");
	echo getValidation();
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<br clear="all" />
		<?php require_once "tmpl/emp_header_basic.php";

			$sql = "select * from att_setting where idPegawai='$par[idPegawai]'";
			$res = db($sql);
			$r = mysql_fetch_assoc($res);

			?>
		<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" enctype="multipart/form-data" onsubmit="return save('<?= getPar($par, 'mode') ?>')">
			<p class="btnSave">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, idPegawai") ?>'" />
			</p>

			<div class="widgetbox">
				<div class="title">
					<h3>SETTING</h3>
				</div>
			</div>
			<p><?= $ui->createComboData("Jadwal Kerja", $queryShift, "id", "description", "inp[idShift]", $r[idShift], "", "", "", "t") ?></p>
			<p><?= $ui->createComboData("Mesin Absen", $queryMesin, "id", "description", "inp[idMesin]", $r[idMesin], "", "", "", "t") ?></p>
			<p><?= $ui->createTextArea("Keterangan", "inp[keteranganSetting]", $r[keteranganSetting]) ?></p>
			<p><?= $ui->createField("ID Absen", "inp[kodeSetting]", $r[kodeSetting], "t", "", "style=\"width:250px;\"") ?></p>

			<br clear="all" />

			<div class="widgetbox">
				<div class="title">
					<h3>SETTING</h3>
				</div>
			</div>
			<p><?= $ui->createField("No. Kartu", "inp[nomorSetting]", $r[nomorSetting], "", "", "style=\"width:250px;\"") ?></p>
			<p><?= $ui->createField("Mulai Kartu", "inp[mulaiSetting]", getTanggal($r[mulaiSetting]), "", "", "", "", "", "", "t") ?></p>
			<p><?= $ui->createField("Selesai Kartu", "inp[selesaiSetting]", getTanggal($r[selesaiSetting]), "", "", "", "", "", "", "t") ?></p>
			<p><?= $ui->createRadio("Status Mesin", "inp[statusMesin]", array("t" => "Aktif", "f" => "Tidak Aktif"), $r[statusMesin]) ?></p>
	</div>
	</form>
<?php
}

function detail()
{
	global $s, $par, $arrTitle, $ui;

	$_SESSION["curr_emp_id"] = $par[idPegawai];

	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<br clear="all" />
		<?php require_once "tmpl/emp_header_basic.php";

			$sql = "select * from att_setting where idPegawai='$par[idPegawai]'";
			$res = db($sql);
			$r = mysql_fetch_assoc($res);

			$statusMesin =  $r[statusMesin] == "f" ? "Tidak Aktif" : "Aktif";

			?>
		<form id="form" name="form" method="post" class="stdform">
			<div class="widgetbox">
				<div class="title">
					<h3>SETTING</h3>
				</div>
			</div>
			<p><?= $ui->createSpan("Jadwal Kerja", getField("select namaShift from dta_shift where idShift='$r[idShift]'")) ?></p>
			<p><?= $ui->createSpan("Mesin Absen", getField("select namaMesin from dta_mesin where idMesin='$r[idMesin]'")) ?></p>
			<p><?= $ui->createSpan("Keterangan", nl2br($r[keteranganSetting])) ?></p>
			<div class="widgetbox">
				<div class="title">
					<h3>KARTU AKSES</h3>
				</div>
			</div>
			<p><?= $ui->createSpan("No. Kartu", $r[nomorSetting]) ?></p>
			<p><?= $ui->createSpan("Masa Berlaku", getTanggal($r[mulaiSetting], "t") . " <strong>s.d</strong> " . getTanggal($r[selesaiSetting], "t")) ?></p>
			<p><?= $ui->createSpan("Status", $statusMesin) ?></p>
		</form>
	</div>
<?php
}

function getContent($par)
{
	global $s, $_submit, $menuAccess;
	switch ($par[mode]) {
		case "cek":
			$text = cek();
			break;
		case "det":
			$text = detail();
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

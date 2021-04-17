<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function hapus()
{
	global $par;

	$sql = "delete from dta_shift where idShift='$par[idShift]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,idShift") . "';</script>";
}

function ubah()
{
	global $inp, $par, $cUser;

	repField();

	$inp[update_by] = $cUser;
	$inp[update_date] = date('Y-m-d H:i:s');

	$sql = "update dta_shift set idLokasi='$inp[idLokasi]', kodeShift='$inp[kodeShift]', namaShift='$inp[namaShift]', groupShift='$inp[groupShift]', mulaiShift='$inp[mulaiShift]', selesaiShift='$inp[selesaiShift]', mulaiShift_istirahat='$inp[mulaiShift_istirahat]', selesaiShift_istirahat='$inp[selesaiShift_istirahat]', lemburShift='" . setAngka($inp[lemburShift]) . "', hariShift='" . setAngka($inp[hariShift]) . "', keteranganShift='$inp[keteranganShift]', flagShift='$inp[flagShift]', nightShift='$inp[nightShift]', statusShift='$inp[statusShift]', update_by='$inp[update_by]', update_date='$inp[update_date]' where idShift='$par[idShift]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambah()
{
	global $inp, $cUser;

	repField();

	$idShift = getLastId("dta_shift", "idShift");

	$inp[create_by] = $cUser;
	$inp[create_date] = date('Y-m-d H:i:s');

	$sql = "insert into dta_shift (idShift, idLokasi, kodeShift, namaShift, groupShift, mulaiShift, selesaiShift, mulaiShift_istirahat, selesaiShift_istirahat, lemburShift, jumlahShift, hariShift, keteranganShift, flagShift, nightShift, statusShift, create_by, create_date) values ('$idShift', '$inp[idLokasi]', '$inp[kodeShift]', '$inp[namaShift]', '$inp[groupShift]', '$inp[mulaiShift]', '$inp[selesaiShift]', '$inp[mulaiShift_istirahat]', '$inp[selesaiShift_istirahat]', '" . setAngka($inp[lemburShift]) . "', '20', '" . setAngka($inp[hariShift]) . "', '$inp[keteranganShift]', '$inp[flagShift]', '$inp[nightShift]', '$inp[statusShift]', '$inp[create_by]', '$inp[create_date]')";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
	global $s, $par, $arrTitle, $arrParameter, $ui;

	$sql = "select * from dta_shift where idShift='$par[idShift]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$queryLokasi = "SELECT kodeData id, namaData description from mst_data where statusData='t' and kodeCategory='" . $arrParameter[7] . "' order by urutanData";

	setValidation("is_null", "inp[kodeShift]", "anda harus mengisi kode");
	setValidation("is_null", "mulaiShift", "anda harus mengisi mulai");
	setValidation("is_null", "selesaiShift", "anda harus mengisi selesai");
	setValidation("is_null", "mulaiShift_istirahat", "anda harus mengisi mulai istirahat");
	setValidation("is_null", "selesaiShift_istirahat", "anda harus mengisi selesai istirahat");
	setValidation("is_null", "inp[lemburShift]", "anda harus mengisi lembur otomatis");
	setValidation("is_null", "inp[keteranganShift]", "anda harus mengisi keterangan");
	echo getValidation();
	?>
	<div class="centercontent contentpopup">
		<div class="pageheader">
			<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
			<?= getBread(ucwords($par[mode] . " data")) ?>
		</div>
		<div class="contentwrapper">
			<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
				<p class="btnSave">
					<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
					<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();" />
				</p>
				<div id="general" class="subcontent">
					<p><?= $ui->createComboData("Lokasi Kerja", $queryLokasi, "id", "description", "inp[idLokasi]", $r[idLokasi], "", "", "t", "", "", "All") ?></p>
					<p><?= $ui->createField("Nama", "inp[namaShift]", $r[namaShift]) ?></p>
					<table width="100%">
						<tr>
							<td style="width:50%">
								<p><?= $ui->createField("Kode", "inp[kodeShift]", $r[kodeShift], "t", "t", "style=\"width:75px;\"") ?></p>
							</td>
							<td style="width:50%;">
								<p><?= $ui->createRadio("Hari Kerja", "inp[hariShift]", array("5" => "5 Hari", "6" => "6 Hari"), $r[hariShift]) ?></p>
							</td>
						</tr>
					</table>
					<table width="100%">
						<tr>
							<td style="width:50%">
								<div class="widgetbox">
									<div class="title">
										<h3>Jadwal Kerja</h3>
									</div>
								</div>
								<p><?= $ui->createTimePicker("Mulai", "mulaiShift", substr($r[mulaiShift], 0, 5), "t", "t") ?></p>
								<p><?= $ui->createTimePicker("Selesai", "selesaiShift", substr($r[selesaiShift], 0, 5), "t", "t") ?></p>
								<p><?= $ui->createField("Lembur Otomatis (Jam)", "inp[lemburShift]", $r[lemburShift], "t", "t", "style=\"width:75px\"") ?> </p>
								<p><?= $ui->createField("Keterangan", "inp[keteranganShift]", $r[keteranganShift], "t", "t") ?></p>
								<p><?= $ui->createRadio("Status", "inp[statusShift]", array("t" => "Aktif", "f" => "Tidak Aktif"), $r[statusShift], "t") ?></p>
							</td>
							<td style="width:50%;">
								<div class="widgetbox">
									<div class="title">
										<h3>Jadwal Istirahat</h3>
									</div>
								</div>
								<p><?= $ui->createTimePicker("Mulai", "mulaiShift_istirahat", substr($r[mulaiShift_istirahat], 0, 5), "t", "t") ?></p>
								<p><?= $ui->createTimePicker("Selesai", "selesaiShift_istirahat", substr($r[selesaiShift_istirahat], 0, 5), "t", "t") ?></p>
								<p><?= $ui->createField("Group", "inp[groupShift]", $r[groupShift], "", "t") ?></p>
								<p><?= $ui->createSingleCheckBox("Hari Jum'at ?", "inp[flagShift]", "1", $r[flagShift], "", "t") ?></p>
								<p><?= $ui->createSingleCheckBox("Night Shift ?", "inp[nightShift]", "1", $r[nightShift], "", "t") ?></p>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
	<style>
		.chosen-container {
			min-width: 260px;
		}
	</style>
<?php
}

function lihat()
{
	global $s, $par, $arrTitle, $menuAccess;
	?>

	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form action="" method="post" class="stdform">
			<div id="pos_r">
				<?php if (isset($menuAccess[$s]["add"])) ?><a href="#Add" class="btn btn1 btn_document" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode', 'idShift') ?>',875,550);"><span>Tambah Data</span></a>
			</div>
			<div id="pos_l">
				<input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
				<input type="submit" value="GO" class="btn btn_search btn-small" />
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th rowspan="2" width="20" style="vertical-align:middle;">No.</th>
					<th rowspan="2" width="150" style="vertical-align:middle;">Lokasi Kerja</th>
					<th rowspan="2" width="150" style="vertical-align:middle;">Nama</th>
					<th rowspan="2" width="100" style="vertical-align:middle;">Kode</th>
					<th colspan="2" width="200">Jam</th>
					<th rowspan="2" width="150" style="vertical-align:middle;">Keterangan</th>
					<th rowspan="2" width="50" style="vertical-align:middle;">Group</th>
					<th rowspan="2" width="50" style="vertical-align:middle;">Status</th>
					<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th style="vertical-align:middle;" rowspan="2" width="50">Kontrol</th>
				</tr>
				<tr>
					<th width="100">Masuk</th>
					<th width="100">Pulang</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (!empty($par[filterData]))
						$filter = "WHERE (t1.kodeShift LIKE '%$par[filterData]%' OR t1.namaShift LIKE '%$par[filterData]%')";
					$sql = "SELECT t1.idShift, t2.namaData, t1.kodeShift, t1.namaShift, t1.mulaiShift, t1.selesaiShift, t1.keteranganShift, t1.groupShift, t1.statusShift FROM dta_shift t1 left join mst_data t2 on (t1.idLokasi = t2.kodeData AND t2.kodeCategory = 'S06') $filter order by t1.idShift ";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_assoc($res)) {
						$no++;
						$r[namaData] = empty($r[namaData]) ? " ALL " : $r[namaData];
						$r[statusShift] = $r[statusShift] == "t" ? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";
						if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
							$control = "";
							if (!empty($menuAccess[$s]["edit"]))
								$control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[idShift]=$r[idShift]" . getPar($par, "mode,id") . "', 875,550)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
							if (!empty($menuAccess[$s]["delete"]))
								$control .= "<a href=\"?par[mode]=del&par[idShift]=$r[idShift]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						}
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[namaData] ?></td>
						<td><?= $r[namaShift] ?></td>
						<td align="center"><?= $r[kodeShift] ?></td>
						<td align="center"><?= substr($r[mulaiShift], 0, 5) ?></td>
						<td align="center"><?= substr($r[selesaiShift], 0, 5) ?></td>
						<td><?= $r[keteranganShift] ?></td>
						<td><?= $r[groupShift] ?></td>
						<td align="center"><?= $r[statusShift] ?></td>
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

function getContent($par)
{
	global $s, $_submit, $menuAccess;
	switch ($par[mode]) {
		case "lst":
			$text = lData();
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
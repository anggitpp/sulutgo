<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function cek()
{
	global $inp, $par;

	if (getField("select idCuti from dta_cuti where namaCuti='$inp[namaCuti]' and idCuti!='$par[idCuti]'"))

		return "maaf, tipe cuti \" $inp[namaCuti] \" sudah ada";
}

function hapus()
{
	global $par;

	$sql = "delete from dta_cuti where idCuti='$par[idCuti]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode,idCuti") . "';</script>";
}

function ubah()
{
	global $inp, $par, $cUser;

	repField();

	$inp[mulaiCuti] = setTanggal($inp[mulaiCuti]);
	$inp[selesaiCuti] = setTanggal($inp[selesaiCuti]);
	$inp[masaCuti] = setAngka($inp[masaCuti]);
	$inp[updateBy] = $cUser;
	$inp[updateTime] = date('Y-m-d H:i:s');

	$sql = "update dta_cuti set idLokasi = '$inp[idLokasi]', mulaiCuti = '$inp[mulaiCuti]', selesaiCuti = '$inp[selesaiCuti]', namaCuti = '$inp[namaCuti]', jatahCuti = '$inp[jatahCuti]', masaCuti = '$inp[masaCuti]', keteranganCuti = '$inp[keteranganCuti]', statusCuti = '$inp[statusCuti]', updateBy = '$inp[updateBy]', updateTime = '$inp[updateTime]' where idCuti='$par[idCuti]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambah()
{
	global $inp, $cUser;

	repField();

	$idCuti = getField("select idCuti from dta_cuti order by idCuti desc limit 1") + 1;
	$inp[mulaiCuti] = setTanggal($inp[mulaiCuti]);
	$inp[selesaiCuti] = setTanggal($inp[selesaiCuti]);
	$inp[masaCuti] = setAngka($inp[masaCuti]);
	$inp[createBy] = $cUser;
	$inp[createTime] = date('Y-m-d H:i:s');
	$sql = "insert into dta_cuti set idCuti = '$idCuti', idLokasi = '$inp[idLokasi]', mulaiCuti = '$inp[mulaiCuti]', selesaiCuti = '$inp[selesaiCuti]', namaCuti = '$inp[namaCuti]', jatahCuti = '$inp[jatahCuti]', masaCuti = '$inp[masaCuti]', keteranganCuti = '$inp[keteranganCuti]', statusCuti = '$inp[statusCuti]', createBy = '$inp[createBy]', createTime = '$inp[createTime]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
	global $s, $par, $arrTitle, $arrParameter, $ui;

	$sql = "select * from dta_cuti where idCuti='$par[idCuti]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$queryLokasi = "SELECT kodeData id, namaData description from mst_data where statusData='t' and kodeCategory='" . $arrParameter[7] . "' order by urutanData";

	setValidation("is_null", "inp[namaCuti]", "anda harus mengisi tipe cuti");
	setValidation("is_null", "inp[jatahCuti]", "anda harus mengisi jumlah");
	setValidation("is_null", "inp[mulaiCuti]", "anda harus mengisi masa berlaku");
	setValidation("is_null", "inp[selesaiCuti]", "anda harus mengisi masa berlaku");
	setValidation("is_null", "inp[masaCuti]", "anda harus mengisi masa kerja lebih dari");
	echo getValidation();
	?>
	<div id="prosesImg" align="center" style="z-index:9; background:#fff; position:absolute; width:100%; height:100%; opacity:0.5; display:none;">
		<img src="styles/images/loaders/loader6.gif" style="margin-top:250px;">
	</div>
	<div class="centercontent contentpopup">
		<div class="pageheader">
			<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
			<?= getBread(ucwords($par[mode] . " data")) ?>
		</div>
		<div class="contentwrapper">
			<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
				<p class="btnSave">
					<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" onclick="return save('<?= getPar($par, 'mode') ?>');" />
					<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();" />
				</p>
				<div id="general">
					<p><?= $ui->createField("Tipe Cuti", "inp[namaCuti]", $r[namaCuti], "t") ?></p>
					<p><?= $ui->createField("Jumlah", "inp[jatahCuti]", $r[jatahCuti], "t", "", "style=\"width:50px;\"", "", "", "", "", "Hari") ?></p>
					<p><?= $ui->createTextArea("Keterangan", "inp[keteranganCuti]", $r[keteranganCuti]) ?></p>
					<p><?= $ui->createField("Mulai Cuti", "inp[mulaiCuti]", getTanggal($r[mulaiCuti]), "t", "", "", "", "", "", "t") ?></p>
					<p><?= $ui->createField("Selesai Cuti", "inp[selesaiCuti]", getTanggal($r[selesaiCuti]), "t", "", "", "", "", "", "t") ?></p>
					<p><?= $ui->createField("Masa Kerja lebih dari", "inp[masaCuti]", getAngka($r[masaCuti]), "t", "", "style=\"width:50px;\"", "", "", "", "", "Bulan") ?></p>
					<p><?= $ui->createComboData("Berlaku untuk", $queryLokasi, "id", "description", "inp[idLokasi]", $r[idLokasi], "", "", "", "", "", "Seluruh Pegawai") ?></p>
					<p><?= $ui->createRadio("Status", "inp[statusCuti]", array("t" => "Aktif", "f" => "Tidak Aktif"), $r[statusCuti]) ?></p>
				</div>
			</form>
		</div>
	</div>
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
		<form method="post" class="stdform">
			<div id="pos_r">
				<?php if (isset($menuAccess[$s]["add"])) ?><a href="#Add" class="btn btn1 btn_document" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode,idCuti') ?>',875,525);"><span>Tambah Data</span></a>
			</div>
			<div id="pos_l">
				<p>
					<input type="text" id="par[filter]" name="par[filter]" placeholder="Search.." style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" /></td>
					<input type="submit" value="GO" class="btn btn_search btn-small" />
				</p>
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th width="*">Tipe Cuti</th>
					<th width="75">Jumlah</th>
					<th width="50">Masa Berlaku</th>
					<th width="150">Berlaku Untuk</th>
					<th width="50">Status</th>
					<?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "where idCuti > 0";
					if (!empty($par[filter]))
						$filter .= " and (lower(t1.namaCuti) like '%" . strtolower($par[filter]) . "%' or lower(t2.namaData) like '%" . strtolower($par[filter]) . "%')";
					$sql = "select *,year(mulaiCuti) as tahunCuti from dta_cuti t1 left join mst_data t2 on (t1.idLokasi=t2.kodeData) $filter order by idCuti";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_array($res)) {
						$no++;
						$r[namaData] = empty($r[namaData]) ? "Seluruh Pegawai" : $r[namaData];
						$statusCuti = $r[statusCuti] == "t" ? "<img src=\"styles/images/t.png\" title='Aktif'>" : "<img src=\"styles/images/f.png\" title='Tidak Aktif'>";
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[namaCuti] ?></td>
						<td align="right"><?= getAngka($r[jatahCuti]) ?> hari</td>
						<td align="center"><?= $r[tahunCuti] ?></td>
						<td><?= $r[namaData] ?></td>
						<td align="center"><?= $statusCuti ?></td>
						<?php
								if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
									$control = "<td align=\"center\">";
									if (isset($menuAccess[$s]["edit"])) $control .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idCuti]=$r[idCuti]" . getPar($par, "mode,idCuti") . "',875,525);\"><span>Edit</span></a>";
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
function getContent($par)
{
	global $s, $_submit, $menuAccess;

	switch ($par[mode]) {
		case "cek":
			$text = cek();
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
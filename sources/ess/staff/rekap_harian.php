<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function lihat()
{
	global $s, $par, $arrTitle, $cID, $menuAccess;

	if (empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
	if (empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
		<?= getBread(ucwords($par[mode] . " data")) ?>
	</div>
	<form id="form" action="" method="post" class="stdform">
		<div style="position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;">
			<input type="button" value="&lsaquo;" class="btn btn_search btn-small" style="margin-right:5px;" onclick="prevDate();" />
			<?= comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"") . " " . comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"") ?>
			<input type="button" value="&rsaquo;" class="btn btn_search btn-small" onclick="nextDate();" />
		</div>
	</form>
	<div class="contentwrapper">
		<br clear="all" />
		<form method="post" class="stdform">
			<div id="pos_l">
				<input placeholder="Search.." type="text" id="par[filter]" name="par[filter]" style="width:250px;" value="<?= $par[filter] ?>" class="mediuminput" />
				<input type="submit" value="GO" class="btn btn_search btn-small" />
			</div>
		</form>
		<br clear="all" />
		<div id="general">
			<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
				<thead>
					<tr>
						<th rowspan="2" width="20">No.</th>
						<th rowspan="2" width="*">Nama</th>
						<th rowspan="2" width="100">NIK</th>
						<th rowspan="2" width="120">Tanggal</th>
						<th colspan="2" width="80">Jadwal</th>
						<th colspan="2" width="80">Aktual</th>
						<th rowspan="2" width="40">Durasi</th>
						<th rowspan="2" width="150">Keterangan</th>
					</tr>
					<tr>
						<th width="40">Masuk</th>
						<th width="40">Pulang</th>
						<th width="40">Masuk</th>
						<th width="40">Pulang</th>
					</tr>
				</thead>
				<tbody>
					<?php
                        if(!isset($menuAccess[$s]["apprlv2"]))
                            $filter = " AND (leader_id ='$cID' or administration_id='$cID') ";
						if (!empty($par[filter]))
							$filter .= " AND lower(reg_no) LIKE '%".strtolower($par[filter])."%' or lower(name) LIKE '%".strtolower($par[filter])."%'";
						$arrNormal = getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='office hour'");
						$arrShift = arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
						$arrJadwal = arrayQuery("select idPegawai, tanggalJadwal, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal t1 join emp t2 on t1.idPegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') where (leader_id ='$cID' or administration_id='$cID') and year(tanggalJadwal)='" . $par[tahunAbsen] . "' and month(tanggalJadwal)='" . $par[bulanAbsen] . "' $filter");

						$sql = "select t1.*, t2.name, t2.reg_no from dta_absen t1 join emp t2 on t1.idPegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') where '" . $par[tahunAbsen] . "" . $par[bulanAbsen] . "' between concat(year(mulaiAbsen), LPAD(month(mulaiAbsen),2,'0')) and concat(year(selesaiAbsen), LPAD(month(selesaiAbsen),2,'0') $filter)";
						$res = db($sql);
						while ($r = mysql_fetch_assoc($res)) {
							list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ? explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal);
							list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
							list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

							if (isset($arrJadwal[$r[idPegawai]]["$r[tanggalAbsen]"]))
								list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$r[idPegawai]]["$r[tanggalAbsen]"]);

							$arr["$r[idPegawai]"]["$r[tanggalAbsen]"] = $r;
						}
						$no = 0;
						if (is_array($arr)) {
							ksort($arr);
							reset($arr);
							while (list($idPegawai, $tanggalAbsen) = each($arr)) {
								while (list($list, $r) = each($tanggalAbsen)) {
									$no++;
									if ($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
									if ($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
									?>
								<tr>
									<td><?= $no ?>.</td>
									<td><?= $r[name] ?></td>
									<td align="center"><?= $r[reg_no] ?></td>
									<td align="center"><a href="?par[mode]=det&par[idPegawai]=<?= $r[idPegawai] ?>&par[tanggalAbsen]=<?= getTanggal($r[tanggalAbsen]) ?>&par[keteranganAbsen]=<?= $r[keteranganAbsen] . getPar($par, "mode,tanggalAbsen,keteranganAbsen") ?>" title="Detail Data" class="detil"><?= getTanggal($r[tanggalAbsen], "t") ?></a></td>
									<td align="center"><?= substr($r[mulaiShift], 0, 5) ?></td>
									<td align="center"><?= substr($r[selesaiShift], 0, 5) ?></td>
									<td align="center"><?= substr($r[masukAbsen], 0, 5) ?></td>
									<td align="center"><?= substr($r[pulangAbsen], 0, 5) ?></td>
									<td align="center"><?= substr(str_replace("-", "", $r[durasiAbsen]), 0, 5) ?></td>
									<td><?= $r[keteranganAbsen] ?></td>
								</tr>
					<?php
								}
							}
						}
						?>
				</tbody>
			</table>
		</div>
	</div>
<?php
}

function detail()
{
	global $s, $par, $arrTitle, $ui;

	$_SESSION["curr_emp_id"] = $par[idPegawai];
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
		<?= getBread(ucwords($par[mode] . " data")) ?>
	</div>
	<?php require_once "tmpl/emp_header_basic.php";

		$sql = "select * from dta_absen where idPegawai='$par[idPegawai]' and '" . setTanggal($par[tanggalAbsen]) . "' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$dtaNormal = getField("select concat(namaShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
		$dtaShift = getField("select concat(t2.namaShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]'");
		$dtaJadwal = getField("select concat(t2.namaShift, ',\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]' and tanggalJadwal='" . setTanggal($par[tanggalAbsen]) . "'");

		list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t", $dtaShift);
		if (!empty($dtaJadwal)) list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);

		list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
		list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

		if ($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
		if ($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
		list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
		$nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn . " / " . $pulangAbsen_sn;
		?>
	<div class="contentwrapper">
		<form id="form" class="stdform">
			<div id="general">
				<div class="widgetbox">
					<div class="title" style="margin-top:30px; margin-bottom:0px;">
						<h3>DATA ABSENSI</h3>
					</div>
				</div>
				<p><?= $ui->createSpan("Tanggal", getTanggal(setTanggal($par[tanggalAbsen]), "t")) ?></p>
				<p><?= $ui->createSpan("Jadwal Kerja", $r[namaShift] . " " . substr($r[mulaiShift], 0, 5) . " - " . substr($r[selesaiShift], 0, 5)) ?></p>
				<?php
					if (empty($r[keteranganAbsen])) {
						?>
					<p><?= $ui->createSpan("Aktual", substr($r[masukAbsen], 0, 5) . " - " . substr($r[pulangAbsen], 0, 5)) ?></p>
					<p><?= $ui->createSpan("SN Mesin", $nomorAbsen) ?></p>
				<?php
					} else {
						?>
					<p><?= $ui->createSpan("Keterangan", $r[keteranganAbsen]) ?></p>
					<p><?= $ui->createSpan("Nomor", $r[nomorAbsen]) ?></p>
				<?php
					}
					?>
			</div>
			<p>
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode,tanggalAbsen,keteranganAbsen') ?>' ;" style="float:right;" />
			</p>
		</form>
	</div>
<?php
}

function getContent($par)
{
	global $db;
	switch ($par[mode]) {
		case "det":
			$text = detail();
			break;
		default:
			$text = lihat();
			break;
	}
	return $text;
}

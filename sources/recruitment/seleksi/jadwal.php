<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

function lihat()
{
	global $s, $par, $arrTitle;

	$par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

	$arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN('R09', 'R07')");

	?>

	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form action="" method="post" id="form" class="stdform">
			<p class="btnSave"><?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"") ?></p>
			<div id="pos_l" style="float:left;">
				<p>
					<input type="text" placeholder="Search.." name="par[filterDara]" value="" .$par[filterData]."" style="width:100px;" />
					<?= comboData("SELECT t1.id id, concat(t1.subject, ' - ', t2.subject) description from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi order by t1.subject", "id", "description", "par[idRencana]", "All", $par[idRencana], "onchange=\"document.getElementById('form').submit();\"", "310px;", "chosen-select") ?>
					<?= comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'R09'", "id", "description", "par[idPhase]", "All", $par[idPhase], "onchange=\"document.getElementById('form').submit();\"") ?>
					<input type="submit" value="GO" class="btn btn_search btn-small" />
				</p>
			</div>
			<div id="pos_r" style="float:right;">
				<a href="?par[mode]=xls<?= getPar($par, "mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th width="*">Nama</th>
					<th width="100">Tanggal</th>
					<th width="100">Posisi</th>
					<th width="100">Tahapan</th>
					<th width="100">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "where year(t1.sel_date) = '$par[tahunData]'";
					if (!empty($par[idRencana]))
						$filter .= " and t2.id_rencana = '$par[idRencana]'";
					if (!empty($par[idPhase]))
						$filter .= " and t1.phase_id = '$par[idPhase]'";
					$sql = "SELECT t1.sel_date, t1.phase_id, t1.sel_status, t2.name, t3.subject FROM rec_selection_appl t1 join rec_applicant t2 on t1.parent_id = t2.id join rec_job_posisi t3 on t2.id_posisi = t3.id_posisi $filter";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_assoc($res)) {
						$no++;
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[name] ?></td>
						<td align="center"><?= getTanggal($r[sel_date]) ?></td>
						<td><?= $r[subject] ?></td>
						<td><?= $arrMaster[$r[phase_id]] ?></td>
						<td><?= $arrMaster[$r[sel_status]] ?></td>
					</tr>
				<?php
					}
					?>
			</tbody>
		</table>
	</div>
	<?php
		if ($par[mode] == "xls") {
			xls();
			echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}
		?>
<?php
}

function xls()
{
    global $s, $arrTitle, $fExport, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN('R09', 'R07')");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no",  "nama", "tanggal", "posisi", "tahapan", "status");

    $filter = "where t1.sel_date = '" . setTanggal($par[tanggalData]) . "' and t2.id_rencana !=''";
	if (!empty($par[idRencana]))
		$filter .= " and t2.id_rencana = '$par[idRencana]'";
	if (!empty($par[idPhase]))
		$filter .= " and t1.phase_id = '$par[idPhase]'";
	$sql = "SELECT t1.sel_date, t1.phase_id, t1.sel_status, t2.name, t3.subject FROM rec_selection_appl t1 join rec_applicant t2 on t1.parent_id = t2.id join rec_job_posisi t3 on t2.id_posisi = t3.id_posisi $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            getTanggal($r[sel_date]) . "\t center",
            $r[subject] . "\t left",
            $arrMaster[$r[phase_id]] . "\t left",
            $arrMaster[$r[sel_status]] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}

function getContent($par)
{
	global $s;
	switch ($par[mode]) {
		default:
			$text = lihat();
			break;
	}
	return $text;
}
?>
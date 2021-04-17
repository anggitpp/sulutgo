<?php
global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;
$arrStatus = array("" => "", "f" => "Tidak Approve", "t" => "Approve");

$sql = "SELECT * FROM pen_penilaian WHERE idPenilaian='$par[idPenilaian]'";
// echo $sql;
$res=db($sql);
$r=mysql_fetch_array($res);

?>
<div class="contentpopup" style="margin-left: 10px">
	<div class="pageheader">
		<h1 class="pagetitle">Approval <?= $arrTitle[$s]." &raquo; ".getField("SELECT name FROM dta_pegawai WHERE id = '$par[idPegawai]'") ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread() ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">	
		<fieldset><legend>Approval</legend>
			<p>
				<label class="l-input-small">Nama</label>
				<span class="field">
					<?= $r[apprNama] ? $r[apprNama] : "-" ?>
				</span>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<span class="field">
					<?= $r[apprStatus] ? $arrStatus[$r[apprStatus]] : "-" ?>
				</span>
			</p>
			<p>
				<label class="l-input-small">Tanggal</label>
				<span class="field">
					<?= $r[apprTanggal] ? getTanggal($r[apprTanggal]) : "-" ?>
				</span>
			</p>
			<p>
				<label class="l-input-small">Catatan</label>
				<div class="field">
					<?= $r[apprKeterangan] ? nl2br($r[apprKeterangan]) : "-" ?>
				</div>
			</p>
			<p style="position:absolute;top:10px;right:20px;">
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
			</fieldset>
		</form>	
	</div>
</div>
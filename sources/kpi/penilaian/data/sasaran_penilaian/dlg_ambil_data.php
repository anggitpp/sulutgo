<?php
global $s, $par, $menuAccess, $arrTitle, $fFile;

if (isset($_POST["btnSimpan"])) {
	insertData();
	die();
}
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; Ambil Data</h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords("ambil data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
			<p>
				<label class="l-input-small" style="width: 150px">Dari Tipe</label>
				<div class="field">
					<?= comboData("SELECT idKode, namaKode FROM pen_setting_kode WHERE statusKode='t'", "idKode", "namaKode", "inp[idKode]", "", $r[idKode], "", "150px"); ?> &nbsp; <?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "inp[kodeTipe]", "", $r[kodeTipe], "", "150px"); ?>
				</div>
			</p>

			<p style="margin-top: 20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername, $fFile;			
	repField();

	$sql = "DELETE FROM pen_sasaran WHERE kodeTipe = '$par[kodeTipe]' AND idKode = '$par[idKode]'";
	db($sql);

	$sql = "SELECT * FROM pen_sasaran WHERE kodeTipe = '$inp[kodeTipe]' AND idKode = '$inp[idKode]'";
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		$r[idAspek] = getField("SELECT t2.idAspek FROM pen_setting_kode t1 JOIN pen_setting_aspek t2 ON t2.kodeAspek = t1.kodeAspek WHERE t1.idKode = '$par[idKode]' AND t2.namaAspek = '".getField("SELECT namaAspek FROM pen_setting_aspek WHERE idAspek = '$r[idAspek]'")."'");
		$nextId = getField("SELECT idSasaran FROM pen_sasaran ORDER BY idSasaran DESC LIMIT 1")+1;

		$sql = "INSERT INTO pen_sasaran (idSasaran, kodeTipe, idKode, idAspek, idPrespektif, namaSasaran, keteranganSasaran, kodeKategori, createBy, createDate) VALUES ('$nextId', '$par[kodeTipe]', '$par[idKode]', '$r[idAspek]', '$r[idPrespektif]', '$r[namaSasaran]', '$r[keteranganSasaran]', '$r[kodeKategori]', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);

		$sql = "DELETE FROM pen_sasaran_detail WHERE idSasaran = '$nextId'";
		db($sql);

		$sql_ = "SELECT * FROM pen_sasaran_detail WHERE idSasaran = '$r[idSasaran]'";
		$res_ = db($sql_);
		$cm = 0;
		while($r_ = mysql_fetch_array($res_)){
			$sql = "INSERT INTO pen_sasaran_detail (idSasaran, kodeDetail, bobotPilihan, namaPilihan, keteranganPilihan, createBy, createDate) VALUES ('$nextId', '".($cm+1)."', '$r_[bobotPilihan]', '$r_[namaPilihan]', '$r_[keteranganPilihan]', '$cUsername', '".date("Y-m-d H:i:s")."');";
			db($sql);

			$cm++;
		}
	}

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
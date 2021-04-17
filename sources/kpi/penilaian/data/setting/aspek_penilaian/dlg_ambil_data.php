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
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return cek();" enctype="multipart/form-data">
			
            <span style="margin-top: -90px; float: right; position: relative;">
    			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
    			<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
    		</span>
            <fieldset>
            <p>
				<label class="l-input-small" style="width: 150px">Kategori Lama</label>
				<div class="field">
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[kategoriLama]", "", $r[kategoriLama], "", "300px"); ?>
				</div>
			</p>

			<p>
				<label class="l-input-small" style="width: 150px">Kategori Baru</label>
				<div class="field">
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[kategoriBaru]", "", $r[kategoriBaru], "", "300px"); ?>
				</div>
			</p>
            </fieldset>
		</form>
	</div>
</div>
<script type="text/javascript">
	function cek(form) {
		var kategoriLama = document.getElementById('inp[kategoriLama]').value;
		var kategoriBaru = document.getElementById('inp[kategoriBaru]').value;

		if(kategoriLama == kategoriBaru){
			alert("Kategori Baru tidak boleh sama dengan Kategori Lama.");
			return false;
		}else{
			form.submit();
		}
	}
</script>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername, $fFile;			
	repField();

	$sql = "DELETE FROM pen_setting_aspek WHERE idPeriode = '$inp[kategoriBaru]'";
	db($sql);

	$sql = "SELECT * FROM pen_setting_aspek WHERE idPeriode = '$inp[kategoriLama]'";
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		$nextId = getField("SELECT idAspek FROM pen_setting_aspek ORDER BY idAspek DESC LIMIT 1")+1;

		$sql="INSERT INTO pen_setting_aspek (idAspek, idPeriode, namaAspek, penilaianAspek, keteranganAspek, aspekKode, urutanAspek, targetAspek, bobotAspek, statusAspek, createBy, createDate) VALUES ('$nextId', '$inp[kategoriBaru]', '$r[namaAspek]', '$r[penilaianAspek]', '$r[keteranganAspek]', '$r[aspekKode]', '$r[urutanAspek]', '$r[targetAspek]', '$r[bobotAspek]', '$r[statusAspek]', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);

		$sql = "DELETE FROM pen_setting_aspek_detail WHERE idAspek = '$nextId'";
		db($sql);

		$sql_ = "SELECT * FROM pen_setting_aspek_detail WHERE idAspek = '$r[idAspek]'";
		$res_ = db($sql_);
		$cm = 0;
		while($r_ = mysql_fetch_array($res_)){
		    $cm++;
			$sql = "INSERT INTO pen_setting_aspek_detail(idAspek, kodeDetail, kodeTipe, nilaiDetail, createBy, createDate) VALUES ('$nextId', '".$cm."', '$r_[kodeTipe]', '$r_[nilaiDetail]', '$cUsername', '".date("Y-m-d H:i:s")."')";
			db($sql);

			
		}
	}

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
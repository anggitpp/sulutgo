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
				<label class="l-input-small" style="width: 150px">Konversi Lama</label>
				<div class="field">
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[konversiLama]", "", $r[konversiLama], "", "300px"); ?>
					
				</div>
			</p>

			<p>
				<label class="l-input-small" style="width: 150px">Konversi Baru</label>
				<div class="field">					
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[konversiBaru]", "", $r[konversiBaru], "", "300px"); ?>
					
				</div>
			</p>
            </fieldset>
		</form>
	</div>
</div>
<script type="text/javascript">
	function cek(form) {
		var konversiLama = document.getElementById('inp[konversiLama]').value;
		var konversiBaru = document.getElementById('inp[konversiBaru]').value;

		if(konversiLama == konversiBaru){
			alert("Konversi Baru tidak boleh sama dengan Konversi Lama.");
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

	$sql = "DELETE FROM pen_setting_konversi WHERE idPeriode = '$inp[konversiBaru]'";
	db($sql);

	$sql = "SELECT * FROM pen_setting_konversi WHERE idPeriode = '$inp[konversiLama]'";
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		$nextId = getField("SELECT idKonversi FROM pen_setting_konversi ORDER BY idKonversi DESC LIMIT 1")+1;
		$fileSK = "";
		if(!empty($r[skKonversi])){
			$fileSK = "sk-" . $nextId . "." . getExtension($r[skKonversi]);
			copy($fFile . $r[skKonversi], $fFile . $fileSK);
		}

		$sql="INSERT INTO pen_setting_konversi (idKonversi, idPeriode, nilaiMin, nilaiMax, uraianKonversi, penjelasanKonversi, warnaKonversi, skKonversi, statusKonversi, createBy, createDate) VALUES ('$nextId', '$inp[konversiBaru]', '$r[nilaiMin]', '$r[nilaiMax]', '$r[uraianKonversi]', '$r[penjelasanKonversi]', '$r[warnaKonversi]', '$fileSK', '$r[statusKonversi]', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);
	}

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
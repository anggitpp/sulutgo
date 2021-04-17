<?php
global $s, $par, $menuAccess, $arrTitle, $fFile;


if (isset($_POST["btnSimpan"])) {
	switch ($par[mode]) {
		case "add":
		insertData();
		die();
		break;

		case "edit":
		updateData();
		die();
		break;
	}
}

$arrStatus = array("t" => "Aktif", "f" => "Tidak Aktif");
$sql  = "SELECT * FROM pen_setting_konversi WHERE idKonversi = '$par[idKonversi]'";
$res = db($sql);
$r = mysql_fetch_array($res);
$r[idPeriode] = empty($r[idPeriode]) ? $par[idPeriode] : $r[idPeriode];
$r[warnaKonversi] = empty($r[warnaKonversi]) ? "#fc9a9a" : $r[warnaKonversi];

?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords($par[mode]." data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<fieldset>
				<legend> KONVERSI </legend>
			
			<p>
				<label class="l-input-small">Periode</label>
				<div class="field">
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[idPeriode]", "", $r[idPeriode], "", "375px"); ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Nilai</label>
				<div class="field">
					<input type="text" id="inp[nilaiMin]" name="inp[nilaiMin]" value="<?= $r[nilaiMin] ?>" class="mediuminput" style="width: 70px; text-align: right;" placeholder="( min )" /> &nbsp; s/d &nbsp; <input type="text" id="inp[nilaiMax]" name="inp[nilaiMax]" value="<?= $r[nilaiMax] ?>" class="mediuminput" style="width: 70px; text-align: right;" placeholder="( max )"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kode</label>
				<div class="field">
					<input type="text" class="mediuminput" id="inp[uraianKonversi]" name="inp[uraianKonversi]" value="<?= $r[uraianKonversi] ?>">
				</div>
			</p>
			<p>
				<label class="l-input-small">Predikat Yudicium</label>
				<div class="field">
					<textarea class="mediuminput" id="inp[penjelasanKonversi]" name="inp[penjelasanKonversi]" style="height: 50px"><?= $r[penjelasanKonversi] ?></textarea>
				</div>
			</p>
			<table width="100%">
				<tr>
					<td style="width: 50%">
						<p>
							<label class="l-input-small2">Warna</label>
							<div class="field">						
								<input type="text" id="isiWarna" name="inp[warnaKonversi]" value="<?= $r[warnaKonversi] ?>" class="width100" />
								<span id="colorSelector" class="colorselector">
									<span></span>
								</span>
							</div>
						</p>

					</td>
					<td style="width: 50%">
						<p>
							<label class="l-input-small">Status</label>
							<div class="field fradio">
								<?php
								foreach($arrStatus as $key => $value){
									$checked = $r[statusKonversi] == $key ? "checked=\"checked\"" : "";
									?>
									<input type="radio" <?= $checked ?> name="inp[statusKonversi]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php 
								} 
								?>
							</div>
						</p>
					</td>
				</tr>
			</table>
			<p>
				<label class="l-input-small">File SK</label>
				<div class="field">
					<?php
					if(empty($r[skKonversi])){
						?>
						<input type="file" id="fileSK" name="fileSK" style="margin-top: 5px;"/>
						<?php 
					}else{
						?>
						<a href="download.php?d=pen_setting_konversi&amp;f=<?= $r[idKonversi] ?>">
							<img src="<?= getIcon($r[skKonversi]) ?>" title="Download" style="margin-top: 5px;">
						</a>&nbsp;
						<a href="?par[mode]=delFile<?= getPar($par, "mode"); ?>" onclick="return confirm('are you sure to delete this file?')" >Delete</a>
						<?php
					} 
					?>
				</div>
			</p>
			</fieldset>

			<p style="position: absolute;top:10px;right: 20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
				<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<script type="text/javascript">
	function warna(hex){
		jQuery('#colorSelector span').css('backgroundColor', hex);
	}

	jQuery(document).ready(function($) {
		warna('<?= $r[warnaKonversi] ?>');
	});
</script>
<?php
function uploadFile($idKonversi) {
	global $s, $inp, $par, $fFile;
	$fileUpload = $_FILES["fileSK"]["tmp_name"];
	$fileUpload_name = $_FILES["fileSK"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$fileSK = "sk-" . $idKonversi . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileSK);
	}

	if (empty($fileSK))
		$fileSK = getField("select skKonversi from pen_setting_konversi where idKonversi='$idKonversi'");
	return $fileSK;
}

function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idKonversi FROM pen_setting_konversi ORDER BY idKonversi DESC LIMIT 1")+1;
	$fileSK = uploadFile($nextId);
	$sql="INSERT INTO pen_setting_konversi (idKonversi, idPeriode, nilaiMin, nilaiMax, uraianKonversi, penjelasanKonversi, warnaKonversi, skKonversi, statusKonversi, createBy, createDate) VALUES ('$nextId', '$inp[idPeriode]', '$inp[nilaiMin]', '$inp[nilaiMax]', '$inp[uraianKonversi]', '$inp[penjelasanKonversi]', '$inp[warnaKonversi]', '$fileSK', '$inp[statusKonversi]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	
	db($sql);

	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[idPeriode]=$par[idPeriode]".getPar($par, "mode,idKonversi")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$fileSK = uploadFile($par[idKonversi]);
	$sql="UPDATE pen_setting_konversi SET idPeriode = '$inp[idPeriode]', nilaiMin = '$inp[nilaiMin]', nilaiMax = '$inp[nilaiMax]', uraianKonversi = '$inp[uraianKonversi]', penjelasanKonversi = '$inp[penjelasanKonversi]', warnaKonversi = '$inp[warnaKonversi]', skKonversi = '$fileSK', statusKonversi = '$inp[statusKonversi]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idKonversi = '$par[idKonversi]'";
	db($sql);

	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[idPeriode]=$par[idPeriode]".getPar($par, "mode,idKonversi")."';
	</script>";
}
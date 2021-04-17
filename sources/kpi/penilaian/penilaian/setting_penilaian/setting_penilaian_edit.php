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
$sql = "SELECT * FROM pen_setting_penilaian WHERE idSetting='$par[idSetting]'";
$res = db($sql);
$r = mysql_fetch_array($res);

setValidation("is_null","inp[namaSetting]","anda harus mengisi penilaian");
setValidation("is_null","inp[idKode]","anda harus mengisi kode penilaian");
echo getValidation();
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
				<legend> SETTING PENILAIAN </legend>
			
			<p>
				<label class="l-input-small">Penilaian</label>
				<div class="field">
					<input type="text" id="inp[namaSetting]" name="inp[namaSetting]" value="<?= $r[namaSetting] ?>" class="mediuminput"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kode Penilaian</label>
				<div class="field">
					<?= comboData("SELECT idKode, namaKode FROM pen_setting_kode WHERE statusKode = 't'", "idKode", "namaKode", "inp[idKode]", " ", $r[idKode], "", "225px"); ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Periode</label>
				<div class="field">
					<input type="text" id="inp[periodeMulai]" name="inp[periodeMulai]" value="<?= getTanggal($r[periodeMulai]); ?>" class="mediuminput hasDatePicker"/> &nbsp; s/d &nbsp; <input type="text" id="inp[periodeSelesai]" name="inp[periodeSelesai]" value="<?= getTanggal($r[periodeSelesai]); ?>" class="mediuminput hasDatePicker"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Pelaksanaan</label>
				<div class="field">
					<input type="text" id="inp[pelaksanaanMulai]" name="inp[pelaksanaanMulai]" value="<?= getTanggal($r[pelaksanaanMulai]); ?>" class="mediuminput hasDatePicker"/> &nbsp; s/d &nbsp; <input type="text" id="inp[pelaksanaanSelesai]" name="inp[pelaksanaanSelesai]" value="<?= getTanggal($r[pelaksanaanSelesai]); ?>" class="mediuminput hasDatePicker"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Keterangan</label>
				<div class="field">
					<textarea id="inp[keteranganSetting]" name="inp[keteranganSetting]" class="mediuminput" style="height: 70px"><?= $r[keteranganSetting] ?></textarea>
				</div>
			</p>
			<p>
				<label class="l-input-small">SK</label>
				<div class="field">
					<?php
					if(empty($r[skSetting])){
						?>
						<input type="file" id="fileSK" name="fileSK" style="margin-top: 5px;"/>
						<?php 
					}else{
						?>
						<a href="download.php?d=pen_setting_penilaian&amp;f=<?= $r[idSetting] ?>">
							<img src="<?= getIcon($r[skSetting]) ?>" title="Download" style="margin-top: 5px;">
						</a>&nbsp;
						<a href="?par[mode]=delFile<?= getPar($par, "mode"); ?>" onclick="return confirm('are you sure to delete this file?')" >Delete</a>
						<?php
					} 
					?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field fradio">
					<?php
					foreach($arrStatus as $key => $value){
						$checked = $r[statusSetting] == $key ? "checked=\"checked\"" : "";
						?>
						<input type="radio" <?= $checked ?> name="inp[statusSetting]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
					} 
					?>
				</div>
			</p>
			<p style="position:absolute;top:10px;right:20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
			</fieldset>
		</form>
	</div>
</div>
<?php
function uploadFile($idSetting) {
	global $s, $inp, $par, $fFile;
	$fileUpload = $_FILES["fileSK"]["tmp_name"];
	$fileUpload_name = $_FILES["fileSK"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$skSetting = "sk-" . $idSetting . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $skSetting);
	}

	if (empty($skSetting))
		$skSetting = getField("select skSetting from pen_setting_penilaian where idSetting='$idSetting'");
	return $skSetting;
}

function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idSetting FROM pen_setting_penilaian ORDER BY idSetting DESC LIMIT 1")+1;
	$skSetting = uploadFile($nextId);
	$sql="INSERT INTO pen_setting_penilaian (idSetting, idKode, namaSetting, periodeMulai, periodeSelesai, pelaksanaanMulai, pelaksanaanSelesai, keteranganSetting, skSetting, statusSetting, createBy, createDate) VALUES ('$nextId', '$inp[idKode]', '$inp[namaSetting]', '".setTanggal($inp[periodeMulai])."', '".setTanggal($inp[periodeSelesai])."', '".setTanggal($inp[pelaksanaanMulai])."', '".setTanggal($inp[pelaksanaanSelesai])."', '$inp[keteranganSetting]', '$skSetting', '$inp[statusSetting]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$skSetting = uploadFile($par[idSetting]);
	$sql="UPDATE pen_setting_penilaian SET idKode = '$inp[idKode]', namaSetting = '$inp[namaSetting]', periodeMulai = '".setTanggal($inp[periodeMulai])."', periodeSelesai = '".setTanggal($inp[periodeSelesai])."', pelaksanaanMulai = '".setTanggal($inp[pelaksanaanMulai])."', pelaksanaanSelesai = '".setTanggal($inp[pelaksanaanSelesai])."', keteranganSetting = '$inp[keteranganSetting]', skSetting = '$skSetting', statusSetting = '$inp[statusSetting]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idSetting = '$par[idSetting]'";
	db($sql);

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
/* End of file setting_penilaian_edit.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/setting_penilaian/setting_penilaian_edit.php */
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

$sql = "SELECT * FROM pen_dokumen WHERE kodeDokumen = '$par[kodeDokumen]'";
$res = db($sql);
$r = mysql_fetch_array($res);

setValidation("is_null","inp[judulDokumen]","anda harus mengisi judul");
setValidation("is_null","inp[kategoriDokumen]","anda harus mengisi kategori");
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
			<fieldset><legend>DOKUMEN</legend><p>
				<label class="l-input-small">Judul</label>
				<div class="field">
					<input type="text" id="inp[judulDokumen]" name="inp[judulDokumen]" value="<?= $r[judulDokumen] ?>" class="longinput"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Penerbit</label>
				<div class="field">
					<input type="text" id="inp[penerbitDokumen]" name="inp[penerbitDokumen]" value="<?= $r[penerbitDokumen] ?>" class="mediuminput" style="width: 275px;"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Tujuan</label>
				<div class="field">
					<input type="text" id="inp[tujuanDokumen]" name="inp[tujuanDokumen]" value="<?= $r[tujuanDokumen] ?>" class="mediuminput" style="width: 275px;"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field fradio">
					<?php
					foreach($arrStatus as $key => $value){
						$checked = $r[statusDokumen] == $key ? "checked=\"checked\"" : "";
						?>
						<input type="radio" <?= $checked ?> name="inp[statusDokumen]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
					} 
					?>
				</div>
			</p>
			</fieldset>

			<ul class="hornav" style="margin:10px 0px !important;">
				<li class="current"><a href="#perihal">Perihal</a></li>
				<li><a href="#detil">Detil</a></li>
				<li><a href="#catatan">Catatan</a></li>
			</ul>

			<div class="subcontent" id="perihal" style="border-radius:0; display: block;">
				<textarea id="inp[perihalDokumen]" name="inp[perihalDokumen]" class="mediuminput" style="width:98%; height: 70px"><?= $r[perihalDokumen] ?></textarea>
			</div>

			<div class="subcontent" id="detil" style="border-radius:0; display: none;">
				<textarea id="inp[detilDokumen]" name="inp[detilDokumen]" class="mediuminput" style="width:98%; height: 70px"><?= $r[detilDokumen] ?></textarea>
			</div>

			<div class="subcontent" id="catatan" style="border-radius:0; display: none;">
				<textarea id="inp[catatanDokumen]" name="inp[catatanDokumen]" class="mediuminput" style="width:98%; height: 70px"><?= $r[catatanDokumen] ?></textarea>
			</div>

			<p>
				<label class="l-input-small">File</label>
				<div class="field">
					<?php
					if(empty($r[fileDokumen])){
						?>
						<input type="file" id="fileDokumen" name="fileDokumen" style="margin-top: 5px;"/>
						<?php 
					}else{
						?>
						<a href="download.php?d=dokumen_penilaian&amp;f=<?= $r[kodeDokumen] ?>">
							<img src="<?= getIcon($r[fileDokumen]) ?>" title="Download" style="margin-top: 5px;">
						</a>&nbsp;
						<a href="?par[mode]=delFile<?= getPar($par, "mode"); ?>" onclick="return confirm('are you sure to delete this file?')" >Delete</a>
						<?php
					} 
					?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kategori</label>
				<div class="field">
					<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PN02' AND statusData='t'", "kodeData", "namaData", "inp[kategoriDokumen]", " ", $r[kategoriDokumen], "", "280px"); ?>
				</div>
			</p>

			<p style="position:absolute;top:10px;right:20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function uploadFile($kodeDokumen) {
	global $s, $inp, $par, $fFile;
	$fileUpload = $_FILES["fileDokumen"]["tmp_name"];
	$fileUpload_name = $_FILES["fileDokumen"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$fileDokumen = "doc-" . $kodeDokumen . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileDokumen);
	}

	if (empty($fileDokumen))
		$fileDokumen = getField("select fileDokumen from pen_dokumen where kodeDokumen='$kodeDokumen'");
	return $fileDokumen;
}

function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT kodeDokumen FROM pen_dokumen ORDER BY kodeDokumen DESC LIMIT 1")+1;
	$fileDokumen = uploadFile($nextId);
	$sql="INSERT INTO pen_dokumen (kodeDokumen, judulDokumen, penerbitDokumen, tujuanDokumen, fileDokumen, kategoriDokumen, perihalDokumen, detilDokumen, catatanDokumen, statusDokumen,  createBy, createDate) VALUES ('$nextId', '$inp[judulDokumen]', '$inp[penerbitDokumen]', '$inp[tujuanDokumen]', '$fileDokumen', '$inp[kategoriDokumen]', '$inp[perihalDokumen]', '$inp[detilDokumen]', '$inp[catatanDokumen]', '$inp[statusDokumen]',  '$cUsername', '".date("Y-m-d H:i:s")."');";
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

	$fileDokumen = uploadFile($par[kodeDokumen]);
	$sql="UPDATE pen_dokumen SET judulDokumen = '$inp[judulDokumen]', penerbitDokumen = '$inp[penerbitDokumen]', tujuanDokumen = '$inp[tujuanDokumen]', fileDokumen = '$fileDokumen', kategoriDokumen = '$inp[kategoriDokumen]', perihalDokumen = '$inp[perihalDokumen]', detilDokumen = '$inp[detilDokumen]', catatanDokumen = '$inp[catatanDokumen]', statusDokumen = '$inp[statusDokumen]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE kodeDokumen = '$par[kodeDokumen]'";
	db($sql);

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
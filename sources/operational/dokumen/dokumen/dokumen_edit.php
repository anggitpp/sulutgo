<?php

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

$res = db("SELECT * FROM pen_dokumen WHERE kodeDokumen = '$par[id]'");
$r = mysql_fetch_assoc($res);

$r['statusDokumen'] = $r['statusDokumen'] ?: "t";

setValidation("is_null","inp[judulDokumen]","anda harus mengisi judul");
setValidation("is_null","inp[kategoriDokumen]","anda harus mengisi kategori");
echo getValidation();

?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords($par['mode'] . " data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<fieldset>
			<legend>&emsp;DOKUMEN&emsp;</legend>
			<p>
				<label class="l-input-small">Judul</label>
				<div class="field">
					<input type="text" id="inp[judulDokumen]" name="inp[judulDokumen]" value="<?= $r[judulDokumen] ?>" class="longinput"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Sumber</label>
				<div class="field">
					<input type="text" id="inp[penerbitDokumen]" name="inp[penerbitDokumen]" value="<?= $r[penerbitDokumen] ?>" class="longinput"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Tujuan</label>
				<div class="field">
					<input type="text" id="inp[tujuanDokumen]" name="inp[tujuanDokumen]" value="<?= $r[tujuanDokumen] ?>" class="longinput"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field fradio">
					<?php foreach($arr_status as $key => $value) : $checked = $r['statusDokumen'] == $key ? "checked" : ""; ?>
						<input type="radio" <?= $checked ?> name="inp[statusDokumen]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endforeach; ?>
				</div>
			</p>
			</fieldset>

			<br>

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

			<br>

			<p>
				<label class="l-input-small">File</label>
				<div class="field">
					<?php
					if(empty($r[fileDokumen])) {
						?>
						<input type="file" id="fileDokumen" name="fileDokumen" style="margin-top: 5px;"/>
						<?php 
					} else {
						?>
						<a href="download.php?d=dokumen_penilaian&amp;f=<?= $r[kodeDokumen] ?>">
							<img src="<?= getIcon($r[fileDokumen]) ?>" title="Download" style="margin-top: 5px;">
						</a>&nbsp;
						<a href="?<?= getPar($par, "mode"); ?>&par[mode]=remove" onclick="return confirm('are you sure to delete this file?')">Delete</a>
						<?php
					} 
					?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kategori</label>
				<div class="field">
					<?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = '$arrParam[$s]' AND `statusData` = 't'", "kodeData", "namaData", "inp[kategoriDokumen]", "", $r[kategoriDokumen], "", "20rem", ""); ?>
				</div>
			</p>

			<?= $par['mode'] == 'edit' ? getHistory("pen_dokumen", "kodeDokumen", "$par[id]", "createBy", "createDate", "updateBy", "updateDate") : "" ?>

			<p style="position:absolute; top:10px; right:20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
			</p>
		</form>
	</div>
</div>

<?php
function insertData() {
	
	global $s, $par, $inp, $cUsername, $path_document;

	repField();

	$file = customeUpload($_FILES['fileDokumen'], md5(date('Y-m-d H:i:s')), $path_document);

	$sql = "INSERT INTO `pen_dokumen` SET 
	
	`menu_id` 			= '$s',
	`judulDokumen`		= '$inp[judulDokumen]',
	`penerbitDokumen`	= '$inp[penerbitDokumen]',
	`tujuanDokumen`		= '$inp[tujuanDokumen]',
	`fileDokumen`		= '$file',
	`kategoriDokumen`	= '$inp[kategoriDokumen]',
	`perihalDokumen`	= '$inp[perihalDokumen]',
	`detilDokumen`		= '$inp[detilDokumen]',
	`catatanDokumen`	= '$inp[catatanDokumen]',
	`statusDokumen`		= '$inp[statusDokumen]',
	`createBy`			= '$cUsername',
	`createDate`		= '" . date('Y-m-d H:i:s') . "'";

	db($sql);

	echo "<script>closeBox(); reloadPage();</script>";
}

function updateData() {
	
	global $s, $par, $inp, $cUsername, $path_document;
	
	repField();

	$file_last 	= getField("SELECT `fileDokumen` FROM `pen_dokumen` WHERE `kodeDokumen` = '$par[id]'");

	$file 		= customeUpload($_FILES['fileDokumen'], md5(date('Y-m-d H:i:s')), $path_document, $file_last);

	$sql = "UPDATE `pen_dokumen` SET 
	
	`judulDokumen`		= '$inp[judulDokumen]',
	`penerbitDokumen`	= '$inp[penerbitDokumen]',
	`tujuanDokumen`		= '$inp[tujuanDokumen]',
	`fileDokumen`		= '$file',
	`kategoriDokumen`	= '$inp[kategoriDokumen]',
	`perihalDokumen`	= '$inp[perihalDokumen]',
	`detilDokumen`		= '$inp[detilDokumen]',
	`catatanDokumen`	= '$inp[catatanDokumen]',
	`statusDokumen`		= '$inp[statusDokumen]',
	`updateBy` 			= '$cUsername', 
	`updateDate` 		= '" . date("Y-m-d H:i:s") . "' 
	
	WHERE 
	
	`kodeDokumen` = '$par[id]'";
	
	db($sql);

	echo "<script>closeBox(); reloadPage();</script>";
}

function customeUpload( $file, $file_rename = "", $directory, $last_file = null ) {

    if ( !empty($file['tmp_name']) ) {

      if ( !is_dir( $directory ) ) mkdir( $directory , 0755 , true );

      $file_temp = $file['tmp_name'];
      $file_name = $file['name'];

      $extension = explode(".", $file_name);
      $file_renamed = empty($file_rename) ? $file_name : $file_rename . "." . end($extension);

      $file_renamed = str_replace( "/", ".", $file_renamed );

      move_uploaded_file( $file_temp, $directory . "/" . $file_renamed );
  
      return $file_renamed;
    }

  return $last_file;
}
?>
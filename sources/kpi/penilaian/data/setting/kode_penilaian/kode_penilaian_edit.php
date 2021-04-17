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

$sql  = "SELECT * FROM pen_setting_kode WHERE idKode = '$par[idKode]'";
$res = db($sql);
$r = mysql_fetch_array($res);
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<div style="margin-top: 10px">
		<?php echo getBread(ucwords($par[mode]." data")) ?>
	</div>
	<span class="pagedesc">&nbsp;</span>
</div>
<div id="contentwrapper" class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
        <span style="margin-top: -75px; float: right; position: relative;">
			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
			<input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, "mode,idKode"); ?>';"/>
		</span>
        <fieldset>
        <p>
			<label class="l-input-small">Tipe Penilaian</label>
			<div class="field">
				<?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "inp[kodeTipe]", " ", $r[kodeTipe], "", "220px");?>
			</div>
		</p>
        <p>
			<label class="l-input-small">Sub</label>
			<div class="field">
				<input type="text" id="inp[subKode]" name="inp[subKode]" value="<?= $r[subKode] ?>" class="mediuminput" style="width: 375px;">
			</div>
		</p>
		<p>
                <label class="l-input-small">Kode Penilaian</label>
			<div class="field">
				<input type="text" id="inp[namaKode]" name="inp[namaKode]" value="<?= $r[namaKode] ?>" class="mediuminput" style="width: 375px;">
			</div>
		</p>
		<p>
			<label class="l-input-small">Keterangan</label>
			<div class="field">
				<textarea id="inp[keteranganKode]" name="inp[keteranganKode]" class="mediuminput" style="width: 375px; height: 50px"><?= $r[keteranganKode] ?></textarea>
			</div>
		</p>
		<table width="100%">
			<tr>
				<td style="width: 50%">
					<p>
						<label class="l-input-small2">Status</label>
						<div class="field fradio">
							<?php
							foreach($arrStatus as $key => $value){
								$checked = $r[statusKode] == $key ? "checked=\"checked\"" : "";
								?>
								<input type="radio" <?= $checked ?> name="inp[statusKode]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<?php 
							} 
							?>
						</div>
					</p>
				</td>
				<td style="width: 50%">
					<p>
						<label class="l-input-small">Kode</label>
						<div class="field">
							<input type="text" id="inp[kodeKode]" name="inp[kodeKode]" style="width: 100px" class="mediuminput" value="<?= $r[kodeKode] ?>">
						</div>
					</p>
				</td>
			</tr>
			<tr>
				<td style="width: 50%">
					<p>
						<label class="l-input-small2">Berlaku Sejak</label>
						<div class="field">
							<input type="text" id="inp[tanggalMulai]" name="inp[tanggalMulai]" style="width: 100px" class="mediuminput hasDatePicker" value="<?= getTanggal($r[tanggalMulai]) ?>">
						</div>
					</p>
				</td>
				<td style="width: 50%">
					<p>
						<label class="l-input-small">Berakhir</label>
						<div class="field">
							<input type="text" id="inp[tanggalSelesai]" name="inp[tanggalSelesai]" style="width: 100px" class="mediuminput hasDatePicker" value="<?= getTanggal($r[tanggalSelesai]) ?>">
						</div>
					</p>
				</td>
			</tr>
		</table>
		<p>
			<label class="l-input-small">File SK</label>
			<div class="field">
				<?php
				if(empty($r[skKode])){
					?>
					<input type="file" id="fileSK" name="fileSK" style="margin-top: 5px;"/>
					<?php 
				}else{
				    echo "<a href=\"#Preview\" title=\"Preview File\" onclick=\"openBox('view.php?&par[tipe]=file_pen_kode&par[idKode]=$r[idKode]',900,500);\"><img style=\" height:20px;\" src=\"".getIcon($r[skKode])."\"></a>";
					?>
					&nbsp;
					<a href="?par[mode]=delFile<?= getPar($par, "mode"); ?>" onclick="return confirm('are you sure to delete this file?')" >Delete</a>
					<?php
				} 
				?>
			</div>
		</p>
		<div style="display:none">
		<div class="widgetbox" style="margin-bottom: -10px">
			<div class="title"><h3>Acuan Penilaian</h3></div>
		</div>
		
		<p>
			<label class="l-input-small">Konversi Nilai</label>
			<div class="field">
				<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN03' AND statusData='t'", "kodeData", "namaData", "inp[kodeKonversi]", " ", $r[kodeKonversi], "", "275px"); ?>
			</div>
		</p>
		<p>
			<label class="l-input-small">Aspek Penilaian</label>
			<div class="field">
				<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN04' AND statusData='t'", "kodeData", "namaData", "inp[kodeAspek]", " ", $r[kodeAspek], "", "275px"); ?>
			</div>
		</p>
		<p>
			<label class="l-input-small">Prespektif</label>
			<div class="field">
				<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN05' AND statusData='t'", "kodeData", "namaData", "inp[kodePrespektif]", " ", $r[kodePrespektif], "", "275px"); ?>
			</div>
		</p>
		</div>
        </fieldset>
		
	</form>
</div>
<?php
function uploadFile($idKode) {
	global $s, $inp, $par, $fFile;
	$fileUpload = $_FILES["fileSK"]["tmp_name"];
	$fileUpload_name = $_FILES["fileSK"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFile);
		$fileSK = "sk-" . $idKode . "." . getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileSK);
	}

	if (empty($fileSK))
		$fileSK = getField("select skKode from pen_setting_kode where idKode='$idKode'");
	return $fileSK;
}

function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idKode FROM pen_setting_kode ORDER BY idKode DESC LIMIT 1")+1;
	$fileSK = uploadFile($nextId);
	$sql="INSERT INTO pen_setting_kode (idKode, namaKode, subKode, keteranganKode, kodeKode, kodeTipe, tanggalMulai, tanggalSelesai, skKode, statusKode, kodeKonversi, kodeAspek, kodePrespektif, createBy, createDate) VALUES ('$nextId', '$inp[namaKode]', '$inp[subKode]', '$inp[keteranganKode]', '$inp[kodeKode]', '$inp[kodeTipe]', '".setTanggal($inp[tanggalMulai])."', '".setTanggal($inp[tanggalSelesai])."', '$fileSK', '$inp[statusKode]', '$inp[kodeKonversi]', '$inp[kodeAspek]', '$inp[kodePrespektif]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	echo "
	<script>
		window.location='?par[mode]edit&par[idKode]=$nextId".getPar($par, "mode")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$fileSK = uploadFile($par[idKode]);
	$sql="UPDATE pen_setting_kode SET namaKode = '$inp[namaKode]', subKode = '$inp[subKode]', keteranganKode = '$inp[keteranganKode]', kodeKode = '$inp[kodeKode]', kodeTipe = '$inp[kodeTipe]', tanggalMulai = '".setTanggal($inp[tanggalMulai])."', tanggalSelesai = '".setTanggal($inp[tanggalSelesai])."', skKode = '$fileSK', statusKode = '$inp[statusKode]', kodeKonversi = '$inp[kodeKonversi]', kodeAspek = '$inp[kodeAspek]', kodePrespektif = '$inp[kodePrespektif]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idKode = '$par[idKode]'";
	db($sql);

	echo "
	<script>
		window.location='index.php?par[mode]=lihat".getPar($par, "mode, idKode")."';
	</script>";
}
?>
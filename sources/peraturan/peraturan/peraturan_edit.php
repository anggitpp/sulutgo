<?php
/**
* Global Definition
* $s = kodeMenu
* $par = Current Parameter (ex: ?c=7&p=27&m=287&s=287)
* $menuAccess = Array contains Menu Accesses (ex: $menuAccess[$s]['edit'])
* $arrTitle = Array contains Menu Titles
* getPar(currentPar, excludedPar) = Modify current parameter (ex: getPar($par, "mode") will remove 'par[mode]' from current parameter)
*/
global $s, $par, $menuAccess, $arrTitle;

// Required for submit button action
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

// Add new 'key' => 'val' if you want to put extra status
$arrStatus = array("t" => "Tampil", "f" => "Tidak Tampil");

$sql = "SELECT * FROM per_pasal_ayat WHERE idAyat = '$par[idAyat]'";
$res = db($sql);
$r = mysql_fetch_array($res);
?>
<div class="contentpopup">
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<div style="margin-top:10px;">
	<?= getBread(ucwords($par[mode]." data")) ?>	
	<span class="pagedesc">&nbsp;</span>	
	</div>						
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
		<p>
			<label class="l-input-small">Ayat</label>
			<div class="field">
				<input type="text" class="mediuminput" id="inp[namaAyat]" name="inp[namaAyat]" value="<?= $r[namaAyat] ?>">
			</div>
		</p>
		<p>
			<label class="l-input-small">Penjelasan</label>
			<div class="field">
				<textarea class="mediuminput" id="inp[keteranganAyat]" name="inp[keteranganAyat]" style="height: 50px"><?= $r[keteranganAyat] ?></textarea>
			</div>
		</p>
		<p>
			<label class="l-input-small">Urutan</label>
			<div class="field">
				<input type="text" class="mediuminput" style="width:100px; text-align:right;" onkeyup="cekAngka(this);" id="inp[urutanAyat]" name="inp[urutanAyat]" value="<?= $r[urutanAyat] ?>">
			</div>
		</p>
		<p>
			<label class="l-input-small">Status</label>
			<div class="field fradio">
				<?php
				foreach($arrStatus as $key => $value){
					$checked = $r[statusAyat] == $key ? "checked=\"checked\"" : "";
					?>
					<input type="radio" <?= $checked ?> name="inp[statusAyat]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php 
				} 
				?>
			</div>
		</p>
		<p style="position:absolute; top:10px; right:20px;">
			<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
			<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
		</p>
	</form>
</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;
	// Converts input with name inp[*] to php variables, access it with $inp[key]	
	repField();

	$nextId = getField("SELECT idAyat FROM per_pasal_ayat WHERE kodePasal='$par[kodePasal]' ORDER BY idAyat DESC LIMIT 1")+1;
	$sql="INSERT INTO per_pasal_ayat (idAyat, kodePasal, namaAyat, keteranganAyat, urutanAyat, statusAyat, createBy, createDate) VALUES ('$nextId', '$par[kodePasal]', '$inp[namaAyat]', '$inp[keteranganAyat]', '$inp[urutanAyat]', '$inp[statusAyat]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	// Redirect to default case
	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[mode]=det".getPar($par, "mode,idAyat")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;
	// Converts input with name inp[*] to php variables, access it with $inp[key]
	repField();

	$sql="UPDATE per_pasal_ayat SET namaAyat = '$inp[namaAyat]', keteranganAyat = '$inp[keteranganAyat]', urutanAyat = '$inp[urutanAyat]', statusAyat = '$inp[statusAyat]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idAyat = '$par[idAyat]' AND kodePasal = '$par[kodePasal]'";
	// echo $sql;
	db($sql);

	// Redirect to default case
echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[mode]=det".getPar($par, "mode,idAyat")."';
	</script>";
}
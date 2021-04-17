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
$arrStatus = array("t" => "Aktif", "f" => "Tidak Aktif");

$sql = "SELECT * FROM pen_contoh WHERE kodeContoh = '$par[kodeContoh]'";
$res = db($sql);
$r = mysql_fetch_array($res);
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread(ucwords($par[mode]." data")) ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
		<p>
			<label class="l-input-small">Nama Contoh</label>
			<div class="field">
				<input type="text" class="mediuminput" id="inp[namaContoh]" name="inp[namaContoh]" value="<?= $r[namaContoh] ?>">
			</div>
		</p>
		<p>
			<label class="l-input-small">Keterangan Contoh</label>
			<div class="field">
				<textarea class="mediuminput" id="inp[keteranganContoh]" name="inp[keteranganContoh]" style="height: 50px"><?= $r[keteranganContoh] ?></textarea>
			</div>
		</p>
		<p>
			<label class="l-input-small">Status</label>
			<div class="field fradio">
				<?php
				foreach($arrStatus as $key => $value){
					$checked = $r[statusContoh] == $key ? "checked=\"checked\"" : "";
					?>
					<input type="radio" <?= $checked ?> name="inp[statusContoh]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php 
				} 
				?>
			</div>
		</p>
		<p style="margin-top: 20px;">
			<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
			<input type="button" class="cancel radius2" value="Cancel" onclick="window.location='?<?= getPar($par, "mode,kodeContoh") ?>'"/>
		</p>
	</form>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;
	// Converts input with name inp[*] to php variables, access it with $inp[key]	
	repField();

	$nextId = getField("SELECT kodeContoh FROM pen_contoh ORDER BY kodeContoh DESC LIMIT 1")+1;
	$sql="INSERT INTO pen_contoh (kodeContoh, namaContoh, keteranganContoh, statusContoh, createBy, createDate) VALUES ('$nextId', '$inp[namaContoh]', '$inp[keteranganContoh]', '$inp[statusContoh]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	// Redirect to default case
	echo "
	<script>
		window.location='?".getPar($par, "mode,kodeContoh")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;
	// Converts input with name inp[*] to php variables, access it with $inp[key]
	repField();

	$sql="UPDATE pen_contoh SET namaContoh = '$inp[namaContoh]', keteranganContoh = '$inp[keteranganContoh]', statusContoh = '$inp[statusContoh]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE kodeContoh = '$par[kodeContoh]'";
	// echo $sql;
	db($sql);

	// Redirect to default case
	echo "
	<script>
		window.location='?".getPar($par, "mode,kodeContoh")."';
	</script>";
}
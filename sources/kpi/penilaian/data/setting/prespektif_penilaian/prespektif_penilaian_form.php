<?php
global $s, $par, $menuAccess, $arrTitle;

if (isset($_POST["btnSimpan"])) {
	switch ($par[mode]) {
		case "addIndikator":
		insertData();
		die();
		break;

		case "editIndikator":
		updateData();
		die();
		break;
	}
}

// $arrStatus = array("t" => "Aktif", "f" => "Tidak Aktif");

$sql  = "SELECT * FROM pen_setting_prespektif_indikator WHERE idPrespektif = '$par[idPrespektif]' and kodeIndikator='$par[kodeIndikator]'";
$false = $r[statusIndikator] == "f" ? "checked=\"checked\"" : "";
$true = empty($false) ? "checked=\"checked\"" : "";
$res = db($sql);
$r = mysql_fetch_array($res);
if(empty($r[urutanIndikator]))
$r[urutanIndikator] = empty($r[urutanIndikator]) ? getField("SELECT urutanIndikator FROM pen_setting_prespektif_indikator WHERE idPrespektif = '$par[idPrespektif]' and indukIndikator='$par[indukIndikator]' ORDER BY urutanIndikator DESC LIMIT 1")+1 : $r[urutanIndikator];
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
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">		
		<fieldset>
			<legend>Indikator</legend>
	
			<p>
				<label class="l-input-small">Uraian</label>
				<div class="field">
					<input type="text" class="mediuminput" id="inp[uraianIndikator]" name="inp[uraianIndikator]" value="<?= $r[uraianIndikator] ?>">
				</div>
			</p>
			<p>
				<label class="l-input-small">Urutan</label>
				<div class="field">						
					<input type="text" class="mediuminput" id="inp[urutanIndikator]" name="inp[urutanIndikator]" value="<?= $r[urutanIndikator] ?>" style="width: 50px; text-align: right;" onkeyup="cekAngka(this);">
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field fradio">
					
						<input type="radio" name="inp[statusIndikator]" id="inp[statusIndikator]" value="t" <?= $true ?> > Aktif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="inp[statusIndikator]" id="inp[statusIndikator]" value="f" <?= $false ?> > Tidak Aktif &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
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
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();
	$levelIndikator = getField("select levelIndikator from pen_setting_prespektif_indikator where idPrespektif='$par[idPrespektif]' and kodeIndikator='$par[indukIndikator]'") + 1;
	$nextId = getField("SELECT kodeIndikator FROM pen_setting_prespektif_indikator where idPrespektif='$par[idPrespektif]' ORDER BY kodeIndikator DESC LIMIT 1")+1;
	
	$sql="INSERT INTO pen_setting_prespektif_indikator (idPrespektif, kodeIndikator, indukIndikator, uraianIndikator, statusIndikator, levelIndikator, urutanIndikator, createBy, createDate) VALUES ('$par[idPrespektif]', '$nextId', '$par[indukIndikator]', '$inp[uraianIndikator]', '$inp[statusIndikator]', '$levelIndikator', '$inp[urutanIndikator]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox(); 
		parent.window.location='index.php?par[mode]=det&par[kodeIndikator]=$inp[kodeIndikator]".getPar($par, "mode,kodeIndikator")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$sql="UPDATE pen_setting_prespektif_indikator SET uraianIndikator='$inp[uraianIndikator]', statusIndikator='$inp[statusIndikator]', urutanIndikator='$inp[urutanIndikator]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idPrespektif = '$par[idPrespektif]' and kodeIndikator='$par[kodeIndikator]'";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[mode]=det&par[kodeIndikator]=$inp[kodeIndikator]".getPar($par, "mode,kodeIndikator")."';
	</script>";
}
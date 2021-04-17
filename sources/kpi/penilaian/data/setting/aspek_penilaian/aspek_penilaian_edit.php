<?php
global $s, $par, $menuAccess, $arrTitle;

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
$sql  = "SELECT * FROM pen_setting_aspek WHERE idAspek = '$par[idAspek]'";
$res = db($sql);
$r = mysql_fetch_array($res);
$r[idPeriode] = empty($r[idPeriode]) ? $par[idPeriode] : $r[idPeriode];?>
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
		<legend> ASPEK PENILAIAN </legend>
            <p>
				<label class="l-input-small">Periode</label>
				<div class="field">
				    <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "inp[idPeriode]", "", $r[idPeriode], "", "375px"); ?>
                </div>
			</p>
			<p>
				<label class="l-input-small">Aspek</label>
				<div class="field">
					<input type="text" class="mediuminput" id="inp[namaAspek]" name="inp[namaAspek]" value="<?= $r[namaAspek] ?>">
				</div>
			</p>
			<p>
				<label class="l-input-small">Penilaian</label>
				<div class="field">
					<input type="text" class="mediuminput" id="inp[penilaianAspek]" name="inp[penilaianAspek]" value="<?= $r[penilaianAspek] ?>">
				</div>
			</p>
			<p>
				<label class="l-input-small">Keterangan</label>
				<div class="field">
					<textarea class="mediuminput" id="inp[keteranganAspek]" name="inp[keteranganAspek]" style="height: 50px"><?= $r[keteranganAspek] ?></textarea>
				</div>
			</p>
			<table width="100%">
				<tr>
					<td style="width: 50%">
						<p>
							<label class="l-input-small2">Kode</label>
							<div class="field">						
								<input type="text" class="mediuminput" id="inp[aspekKode]" name="inp[aspekKode]" value="<?= $r[aspekKode] ?>">
							</div>
						</p>
					</td>
					<td style="width: 50%">
						<p>
							<label class="l-input-small">Urutan</label>
							<div class="field">						
								<input type="text" class="mediuminput" id="inp[urutanAspek]" name="inp[urutanAspek]" value="<?= $r[urutanAspek] ?>" style="width: 50px; text-align: right;" onkeyup="cekAngka(this);">
							</div>
						</p>
					</td>
				</tr>
				<tr>
					<td width="50%">
                        <p>
            				<label class="l-input-small2">Bobot</label>
            				<div class="field">
            					<input type="text" class="mediuminput" id="inp[bobotAspek]" name="inp[bobotAspek]" value="<?= $r[bobotAspek] ?>" style="width: 30px;"> %
            				</div>
            			</p>
					</td>
					<td width="50%">
						<p>
							<label class="l-input-small">Status</label>
							<div class="field fradio">
								<?php
								foreach($arrStatus as $key => $value){
									$checked = $r[statusAspek] == $key ? "checked=\"checked\"" : "";
									?>
									<input type="radio" <?= $checked ?> name="inp[statusAspek]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php 
								} 
								?>
							</div>
						</p>
					</td>
				</tr>
			</table>
			</fieldset>
			<p style="position:absolute;top:10px;right:20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
				<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idAspek FROM pen_setting_aspek ORDER BY idAspek DESC LIMIT 1")+1;
	$sql="INSERT INTO pen_setting_aspek (idAspek, idPeriode, namaAspek, penilaianAspek, keteranganAspek, aspekKode, urutanAspek, statusAspek, bobotAspek, createBy, createDate) VALUES ('$nextId', '$inp[idPeriode]', '$inp[namaAspek]', '$inp[penilaianAspek]', '$inp[keteranganAspek]', '$inp[aspekKode]', '$inp[urutanAspek]', '$inp[statusAspek]', '$inp[bobotAspek]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox(); 
		parent.window.location='index.php?par[idPeriode]=$par[idPeriode]".getPar($par, "mode,idAspek")."';
		</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$sql="UPDATE pen_setting_aspek SET idPeriode = '$inp[idPeriode]', namaAspek = '$inp[namaAspek]', penilaianAspek = '$inp[penilaianAspek]', keteranganAspek = '$inp[keteranganAspek]', aspekKode = '$inp[aspekKode]', urutanAspek = '$inp[urutanAspek]', statusAspek = '$inp[statusAspek]', bobotAspek = '$inp[bobotAspek]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idAspek = '$par[idAspek]'";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[idPeriode]=$par[idPeriode]".getPar($par, "mode,idAspek,idPeriode")."';
	</script>";
}
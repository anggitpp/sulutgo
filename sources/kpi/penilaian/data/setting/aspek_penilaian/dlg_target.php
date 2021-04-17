<?php
global $s, $par, $menuAccess, $arrTitle, $fFile;

if (isset($_POST["btnSimpan"])) {
	insertData();
	die();
}

$infoAspek = getField("SELECT CONCAT(namaAspek, '~',penilaianAspek) FROM pen_setting_aspek WHERE idAspek = '$par[idAspek]'");
list($namaAspek, $penilaianAspek) = explode("~", $infoAspek);
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; Target</h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords("ambil data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
    		<fieldset>
    		<legend> ASPEK PENILAIAN </legend>
			<p>
				<label class="l-input-small" style="width: 150px">Aspek</label>
				<span class="field">
					<?= $namaAspek ?> &nbsp;
				</span>
			</p>

			<p>
				<label class="l-input-small" style="width: 150px">Penilaian</label>
				<span class="field">
					<?= nl2br($penilaianAspek) ?> &nbsp;
				</span>
			</p>
			</fieldset>
			
			<div class="widgetbox" style="margin-bottom: -10px">
				<div class="title"><h3>SETTING NILAI</h3></div>
			</div>
			
			<?php
            $getKode = getField("select kodeAspek from pen_setting_aspek where idAspek='$par[idAspek]'");
            $kodeTIpe = getField("select kodeTipe from pen_setting_kode where idKode = $getKode");
            
			$sql = "SELECT t1.kodeTipe, t1.namaTipe, COALESCE(t2.nilaiDetail, 0) nilaiDetail FROM pen_tipe t1 LEFT JOIN pen_setting_aspek_detail t2 ON (t2.kodeTipe = t1.kodeTipe AND t2.idAspek = '$par[idAspek]') WHERE t1.statusTipe='t' and t1.kodeTipe='".$kodeTIpe."' ORDER BY t1.namaTipe";
			$res = db($sql);
			$no = 0;
			while($r = mysql_fetch_array($res)){
				?>
				<input type="hidden" id="dtlAspekIds_<?= $no ?>" name="dtlAspekIds[]" value="<?= $no ?>">
				<input type="hidden" id="dtlAspekKodeTipe_<?= $no ?>" name="dtlAspekKodeTipe[]" value="<?= $r[kodeTipe] ?>">
				<p>
					<label class="l-input-small" style="width: 150px"><?= $r[namaTipe] ?>  </label>
					<div class="field">
						<input type="text" class="mediuminput" id="dtlAspekNilaiDetail_<?= $no ?>" name="dtlAspekNilaiDetail[]" value="<?= $r[nilaiDetail] ?>" style="width: 70px; text-align: right;" onkeyup="cekAngka(this);">
					</div>
				</p>
				<?php
				$no++;
			}
			?>
			<p style="position:absolute;top:10px;right:20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
				<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername, $fFile;			
	repField();

	$sql = "DELETE FROM pen_setting_aspek_detail WHERE idAspek = '$par[idAspek]'";
	db($sql);

	$cm = 0;
	$dtlAspekIds = $_POST['dtlAspekIds'];
	foreach($dtlAspekIds as $dtlAspekId){
		$dtlAspekKodeTipe = $_POST['dtlAspekKodeTipe'][$cm];
		$dtlAspekNilaiDetail = $_POST['dtlAspekNilaiDetail'][$cm];
		
		$sql = "INSERT INTO pen_setting_aspek_detail(idAspek, kodeDetail, kodeTipe, nilaiDetail, createBy, createDate) VALUES ('$par[idAspek]', '".($cm+1)."', '$dtlAspekKodeTipe', '".setAngka($dtlAspekNilaiDetail)."', '$cUsername', '".date("Y-m-d H:i:s")."')";
		db($sql);

		$cm++;
	}

	$sql = "UPDATE pen_setting_aspek SET targetAspek=(SELECT AVG(nilaiDetail) FROM pen_setting_aspek_detail WHERE idAspek = '$par[idAspek]') WHERE idAspek = '$par[idAspek]'";
	db($sql);
	
	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[kodeAspek]=$par[kodeAspek]".getPar($par, "mode,idAspek,kodeAspek")."';
	</script>";
}
?>
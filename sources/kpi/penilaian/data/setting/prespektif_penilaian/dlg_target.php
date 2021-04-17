<?php
global $s, $par, $menuAccess, $arrTitle, $fFile;

if (isset($_POST["btnSimpan"])) {
	insertData();
	die();
}

$infoAspek = getField("SELECT CONCAT(namaPrespektif, '~',keteranganPrespektif) FROM pen_setting_prespektif WHERE idPrespektif = '$par[idPrespektif]'");
list($namaPrespektif, $keteranganPrespektif) = explode("~", $infoAspek);
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; Target Korporat</h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords("ambil data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
			<p>
				<label class="l-input-small" style="width: 150px">Prespektif</label>
				<span class="field">
					<?= $namaPrespektif ?> &nbsp;
				</span>
			</p>

			<p>
				<label class="l-input-small" style="width: 150px">Keterangan</label>
				<span class="field">
					<?= nl2br($keteranganPrespektif) ?> &nbsp;
				</span>
			</p>
			
			<div class="widgetbox" style="margin-bottom: -10px">
				<div class="title"><h3>SETTING PER TIPE PENILAIAN</h3></div>
			</div>
			
			<?php
			$sql = "SELECT t1.kodeTipe, t1.namaTipe, COALESCE(t2.nilaiDetail, 0) nilaiDetail FROM pen_tipe t1 LEFT JOIN pen_setting_prespektif_detail t2 ON (t2.kodeTipe = t1.kodeTipe AND t2.idPrespektif = '$par[idPrespektif]') WHERE t1.statusTipe='t' ORDER BY t1.namaTipe";
			$res = db($sql);
			$no = 0;
			while($r = mysql_fetch_array($res)){
				?>
				<input type="hidden" id="dtlPrespektifIds_<?= $no ?>" name="dtlPrespektifIds[]" value="<?= $no ?>">
				<input type="hidden" id="dtlPrespektifKodeTipe_<?= $no ?>" name="dtlPrespektifKodeTipe[]" value="<?= $r[kodeTipe] ?>">
				<p>
					<label class="l-input-small" style="width: 150px"><?= $r[namaTipe] ?></label>
					<div class="field">
						<input type="text" class="mediuminput" id="dtlPrespektifNilaiDetail_<?= $no ?>" name="dtlPrespektifNilaiDetail[]" value="<?= $r[nilaiDetail] ?>" style="width: 70px; text-align: right;" onkeyup="cekAngka(this);">
					</div>
				</p>
				<?php
				$no++;
			}
			?>
			<p style="margin-top: 20px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername, $fFile;			
	repField();

	$sql = "DELETE FROM pen_setting_prespektif_detail WHERE idPrespektif = '$par[idPrespektif]'";
	db($sql);

	$cm = 0;
	$dtlPrespektifIds = $_POST['dtlPrespektifIds'];
	foreach($dtlPrespektifIds as $dtlPrespektifId){
		$dtlPrespektifKodeTipe = $_POST['dtlPrespektifKodeTipe'][$cm];
		$dtlPrespektifNilaiDetail = $_POST['dtlPrespektifNilaiDetail'][$cm];
		
		$sql = "INSERT INTO pen_setting_prespektif_detail(idPrespektif, kodeDetail, kodeTipe, nilaiDetail, createBy, createDate) VALUES ('$par[idPrespektif]', '".($cm+1)."', '$dtlPrespektifKodeTipe', '".setAngka($dtlPrespektifNilaiDetail)."', '$cUsername', '".date("Y-m-d H:i:s")."')";
		db($sql);

		$cm++;
	}

	$sql = "UPDATE pen_setting_prespektif SET targetPrespektif = (SELECT AVG(nilaiDetail) FROM pen_setting_prespektif_detail WHERE idPrespektif = '$par[idPrespektif]') WHERE idPrespektif = '$par[idPrespektif]'";
	db($sql);
	
	echo "
	<script>
		closeBox();
		parent.window.location='index.php?par[kodePrespektif]=$par[kodePrespektif]".getPar($par, "mode,idPrespektif,kodePrespektif")."';
	</script>";
}
?>
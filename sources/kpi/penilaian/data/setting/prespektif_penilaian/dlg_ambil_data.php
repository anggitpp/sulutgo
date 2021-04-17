<?php
global $s, $par, $menuAccess, $arrTitle, $fFile;

if (isset($_POST["btnSimpan"])) {
	insertData();
	die();
}
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; Ambil Data</h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords("ambil data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return cek();" enctype="multipart/form-data">
			<span style="margin-top: -95px; float: right; position: relative;">
    			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
    			<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
    		</span>
            
            <fieldset>
            <p>
				<label class="l-input-small" style="width: 150px">Kategori Lama</label>
				<div class="field">	
					<?= comboData("SELECT idKode, subKode FROM  pen_setting_kode WHERE statusKode = 't'", "idKode", "subKode", "inp[idKodeLama]", "", $r[idPrespektifLama], "", "300px"); ?>
				</div>
			</p>

			<p>
				<label class="l-input-small" style="width: 150px">Kategori Baru</label>
				<div class="field">
					<?= comboData("SELECT idKode, subKode FROM  pen_setting_kode WHERE statusKode = 't'", "idKode", "subKode", "inp[idKodeBaru]", "", $r[idPrespektifBaru], "", "300px"); ?>
				</div>
			</p>
            </fieldset>
		</form>
	</div>
</div>
<script type="text/javascript">
	function cek(form) {
		var kategoriLama = document.getElementById('inp[idKodeLama]').value;
		var kategoriBaru = document.getElementById('inp[idKodeBaru]').value;

		if(kategoriLama == kategoriBaru){
			alert("Kategori Baru tidak boleh sama dengan Kategori Lama.");
			return false;
		}else{
			form.submit();
		}
	}
</script>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername, $fFile;			
	repField();

	$sql = "DELETE FROM pen_setting_prespektif WHERE idKode = '$inp[idKodeBaru]'";
	db($sql);

	$sql = "SELECT * FROM pen_setting_prespektif WHERE idKode = '$inp[idKodeLama]'";
	$res = db($sql);
    
    $getTipe = getField("select kodeTipe from pen_setting_kode where idKode = $inp[idKodeBaru]");
    
	while($r = mysql_fetch_array($res)){
		$nextId = getField("SELECT idPrespektif FROM pen_setting_prespektif ORDER BY idPrespektif DESC LIMIT 1")+1;
        

		$sql="INSERT INTO pen_setting_prespektif (idPrespektif, idTipe, idKode, idAspek, kodeNama, namaPrespektif, kpiPrespektif, keteranganPrespektif, targetPrespektif, status, urut, createBy, createDate) VALUES ('$nextId', '$getTipe', '$inp[idKodeBaru]', '$r[idAspek]', '$r[kodeNama]', '$r[namaPrespektif]', '$r[kpiPrespektif]', '$r[keteranganPrespektif]','$r[targetPrespektif]','$r[status]','$r[urut]', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);

		//$sql = "DELETE FROM pen_setting_prespektif_detail WHERE idPrespektif = '$nextId'";
//		db($sql);
//
//		$sql_ = "SELECT * FROM pen_setting_prespektif_detail WHERE idPrespektif = '$r[idPrespektif]'";
//		$res_ = db($sql_);
//		$cm = 0;
//		while($r_ = mysql_fetch_array($res_)){
//			$sql = "INSERT INTO pen_setting_prespektif_detail(idPrespektif, kodeDetail, kodeTipe, nilaiDetail, createBy, createDate) VALUES ('$nextId', '".($cm+1)."', '$r_[kodeTipe]', '$r_[nilaiDetail]', '$cUsername', '".date("Y-m-d H:i:s")."')";
//			db($sql);
//
//			$cm++;
//		}
	}

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
<?php
global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;

if (isset($_POST["btnSimpan"])) {
	insertData();
	die();
}

$arrStatus = array("" => "", "f" => "Tidak Approve", "t" => "Approve");

$sql = "SELECT * FROM pen_penilaian WHERE idPenilaian='$par[idPenilaian]'";
// echo $sql;
$res=db($sql);
$r=mysql_fetch_array($res);
if(empty($r[apprTanggal]) || $r[apprTanggal] == "0000-00-00") $r[apprTanggal] = date('Y-m-d');

$namaPegawai = empty($r[apprNama]) ? getField("SELECT namaUser FROM app_user WHERE username='$cUsername'") : $r[apprNama];

setValidation("is_null","inp[apprStatus]","anda harus mengisi status");
echo getValidation();
?>
<div class="contentpopup" style="margin-left: 10px">
	<div class="pageheader">
		<h1 class="pagetitle">Approval <?= $arrTitle[$s]." &raquo; ".getField("SELECT name FROM dta_pegawai WHERE id = '$par[idPegawai]'") ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread() ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">													
			<p>
				<label class="l-input-small">Nama</label>
				<div class="field">
					<input type="text" id="inp[namaPegawai]" name="inp[namaPegawai]" value="<?= $namaPegawai ?>" class="mediuminput" style="width:350px;"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Status</label>
				<div class="field">
					<?= comboKey('inp[apprStatus]', $arrStatus, $r[apprStatus]); ?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Tanggal</label>
				<div class="field">
					<input type="text" id="inp[apprTanggal]" name="inp[apprTanggal]" size="10" maxlength="10" value="<?= getTanggal($r[apprTanggal]) ?>" class="vsmallinput hasDatePicker"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Catatan</label>
				<div class="field">
					<textarea id="inp[apprKeterangan]" name="inp[apprKeterangan]" rows="3" cols="50" class="longinput" style="height:50px; width:350px;"><?= $r[apprKeterangan] ?></textarea>
				</div>
			</p>
			<p>
				<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>
			</p>
		</form>	
	</div>
</div>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$sql="update pen_penilaian set apprNama='$inp[namaPegawai]', apprStatus='".$inp[apprStatus]."', apprTanggal='".setTanggal($inp[apprTanggal])."', apprKeterangan='".$inp[apprKeterangan]."', updateBy='$cUsername', updateDate='".date('Y-m-d H:i:s')."' where idPenilaian = '$par[idPenilaian]'";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox(); reloadPage();
	</script>";
}
?>
<?php

global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername,$cNama;

$fFoto = "files/catatan/catatan/";



if (isset($_POST["btnSimpan"])) {

	switch ($par[mode]) {

		case "add":

		save('insert');

		die();

		break;

		case "edit":

		save('update');

		die();

		break;

	}

}



$sql = "SELECT * FROM catatan_sistem WHERE idCatatan = '$par[idCatatan]'";

$res = db($sql);

$r = mysql_fetch_array($res);



//$fil = empty($r[propinsiToko]) ? "" : "and kodeInduk = '$r[propinsiToko]'";



$y = $r[Testing] == '0' ? "checked" : "";

$n = $r[Testing] == '1' ? "checked" : "";

$default = empty($r[Testing]) ? "checked" : "";

$e = $r[Status] == '0' ? "checked" : "";

$f = $r[Status] == '1' ? "checked" : "";

$g = $r[Status] == '2' ? "checked" : "";

$default_status = empty($r[Status]) ? "checked" : "";

$cNama = empty($r[createdBy]) ? $cNama : getField("select namaUser from app_user where username = '$r[createdBy]'");

empty($r['tanggalMulai']) ? $r['tanggalMulai'] = date('d/m/Y') : $r['tanggalMulai'] = getTanggal($r[tanggalMulai]);

empty($r['tanggalSelesai']) ? $r['tanggalSelesai'] = date('d/m/Y') : $r['tanggalSelesai'] = getTanggal($r[tanggalSelesai]);

empty($r['tanggalAktual']) ? $r['tanggalAktual'] = date('d/m/Y') : $r['tanggalAktual'] = getTanggal($r[tanggalAktual]);

empty($r['tanggalTest']) ? $r['tanggalTest'] = date('d/m/Y') : $r['tanggalTest'] = getTanggal($r[tanggalTest]);

$inp[Tanggal] = $inp[Tanggal] == null ? date('d/m/Y') : getTanggal($inp[Tanggal]);

setValidation("is_null", "inp[Temuan]", "Anda belum mengisi Temuan");

setValidation("is_null", "inp[Penjelasan]", "Anda belum mengisi Penjelasan");

setValidation("is_null", "inp[tanggalMulai]", "Anda belum memilih Tanggal Mulai");

setValidation("is_null", "inp[tanggalSelesai]", "Anda belum memilih Tanggal Selesai");

setValidation("is_null", "inp[tanggalAktual]", "Anda belum memilih Tanggal Aktual");

setValidation("is_null", "inp[tanggalTest]", "Anda belum memilih Tanggal Test");

setValidation("is_null", "inp[Keterangan]", "Anda belum memilih Keterangan");

echo getValidation();



?>

<div class="pageheader">

	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>

	<?= getBread(ucwords($par[mode]." data")) ?>

	<span class="pagedesc">&nbsp;</span>

</div>



<div id="contentwrapper" class="contentwrapper">



	<form class="stdform" method="POST" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" id="form" enctype="multipart/form-data">



		<div style="position:absolute; top: 15px; right: 20px;">

			<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />

			<input type="button" class="cancel radius2" value="Batal" onclick="location.href='?<?= getPar($par,"mode,idCatatan") ?>';" />

		</div>



		<fieldset>

			<legend>CATATAN</legend>

			<p>

				<label class="l-input-small">Tanggal</label>
				<div class="field">
					<input type="text" id="inp[Tanggal]" name="inp[Tanggal]" class="smallinput hasDatePicker" value="<?= $inp[Tanggal] ?>" readonly/>
					<!-- <input type="text" id="inp[Tanggal_tampil]" name="inp[Tanggal_tampil]" class="smallinput" value="<?php	

					$arrHari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu");

					echo $arrHari[date('w')].", ".getTanggal(date('Y-m-d'),"t");			

					?>" readonly/> -->

				</div>
			</p>
			<p>

				
				<label class="l-input-small">Nama</label>

				<div class="field">

					<input type="text" id="inp[createdBy]" name="inp[createdBy]" class="vsmallinput" value="<?= $cNama;?>" readonly/>

				</div>


			</p>


			<p>

				<label class="l-input-small">Temuan</label>

				<div class="field">

					<input type="text" id="inp[Temuan]" name="inp[Temuan]" class="smallinput" value="<?= $r[Temuan] ?>" />

				</div>

			</p>



			<p>

				<label class="l-input-small">Penjelasan</label>

				<div class="field">

					<textarea class="smallinput" style="height: 50px" id="inp[Penjelasan]" name="inp[Penjelasan]"><?= $r[Penjelasan] ?></textarea>

				</div>

			</p>






			<p>

				<label class="l-input-small">File</label>

				<div class="field">



					<?php

					if(empty($r[File])){

						?>



						<input type="text" id="fileTemp" name="fileTemp" class="smallinput">

						<div class="fakeupload" style="padding-left: 40px;">

							<input type="file" id="file" name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;">

						</div>



						<?php }else{ ?>
								

						<a href="download.php?d=catatan&f=<?= $r[idCatatan] ?>"><img src=<?= getIcon($fFoto."/".$r[File]) ?> align="left" style="padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;"></a>			
						<!-- <img src="<?= $fFoto.$r[File] ?>" width="50px"> -->



						<a href="?par[mode]=delFoto<?= getPar($par,"mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>



						<?php } ?>



					</div>

				</p>







			</fieldset>

			<fieldset>

				<legend>PENYELESAIAN</legend>

				<p>

					<table style="width:100%">

						<tr>

							<td width="40%">

								<label class="l-input-small" style="width:50%">Tanggal Mulai</label>

								<div class="field">

									<input type="text" id="inp[tanggalMulai]" name="inp[tanggalMulai]" class="smallinput hasDatePicker" value="<?= $r[tanggalMulai] ?>"/>

								</div>

							</td>

							<td width="60%">

								<label>Target Selesai</label>

								<div class="field">

									<input type="text" id="inp[tanggalSelesai]" name="inp[tanggalSelesai]" class="smallinput hasDatePicker" value="<?= $r[tanggalSelesai] ?>"/>

								</div>

							</td>

						</tr>

					</table>

				</p>

				<p>

					<table style="width:100%">

						<tr>

							<td width="40%">

								<label class="l-input-small" style="width:50%">PIC</label>

								<div class="field">

									<input type="text" id="inp[PIC]" name="inp[PIC]" class="smallinput" value="<?= $r[PIC] ?>"/>

								</div>

							</td>

							<td width="60%">

								<label>Target Aktual</label>

								<div class="field">

									<input type="text" id="inp[tanggalAktual]" name="inp[tanggalAktual]" class="smallinput hasDatePicker" value="<?= $r[tanggalAktual] ?>"/>

								</div>

							</td>

						</tr>

					</table>

				</p>




				<p>

					<label class="l-input-small">Keterangan</label>

					<div class="field">

						<textarea class="smallinput" style="height: 50px" id="inp[Keterangan]" name="inp[Keterangan]"><?= $r[Keterangan] ?></textarea>

					</div>

				</p>

				<p>

					<table style="width:100%">

						<tr>

							<td width="40%">

								<label class="l-input-small" style="width:50%">Testing</label>

								<div class="field">

									<div class="sradio" style="padding-top:5px;padding-left:8px;">

										<input type="radio" name="inp[Testing]" value="0" <?= $y." ".$default ?>> <span style="padding-right:10px;">Belum</span>

										<input type="radio" name="inp[Testing]" value="1" <?= $n ?>> <span style="padding-right:10px;">Sudah</span>

									</div>

								</div>

							</td>

							<td width="60%">

								<label>Tanggal Test</label>

								<div class="field">

									<input type="text" id="inp[tanggalTest]" name="inp[tanggalTest]" class="smallinput hasDatePicker" value="<?= $r[tanggalTest] ?>"/>

								</div>

							</td>

						</tr>

					</table>

				</p>

				<p>

					<label class="l-input-small">Status</label>

					<div class="field">

						<div class="sradio" style="padding-top:5px;padding-left:8px;">

							<input type="radio" name="inp[Status]" value="0" <?= $e." ".$default_status ?>> <span style="padding-right:10px;">Belum</span>

							<input type="radio" name="inp[Status]" value="1" <?= $f ?>> <span style="padding-right:10px;">Selesai</span>
							<input type="radio" name="inp[Status]" value="2" <?= $g ?>> <span style="padding-right:10px;">Pending</span>

						</div>

					</div>

				</p>











			</fieldset>








		</form>



	</div>



	<?php



	function save($params = ""){

		global $s, $inp, $par, $cUsername, $fFoto;

		repField();



		// $first = array("-"," ",":");

		// $end = array("","","");	

		// $curdate = str_replace($first, $end, date('Y-m-d H:i'));



		$file = $_FILES["file"]["tmp_name"];

		$file_name = $_FILES["file"]["name"];

		if(($file!="") and ($file!="none")){						

			fileUpload($file,$file_name,$fFoto);			

			$file = "catatan_".uniqid()."_".$curdate.".".getExtension($file_name);

			fileRename($fFoto, $file_name, $file);			

		}



		if(empty($file)) $file = getField("select File from catatan_sistem where idCatatan='$par[idCatatan]'");



	//$id_lokasi = getField("SELECT id_lokasi FROM alamat_lokasi ORDER BY id_lokasi DESC LIMIT 1")+1;	



		if ($params == "insert") {

			$time=date("Y-m-d H:i:s");

			$idCatatan = getField("SELECT idCatatan from catatan_sistem order by idCatatan desc")+1;

			$sql = "INSERT INTO catatan_sistem(idCatatan,Tanggal,Temuan,Penjelasan,tanggalMulai,tanggalSelesai,tanggalAktual,PIC,Keterangan,Testing,tanggalTest,Status,createdBy,createdDate,File) VALUES ('$idCatatan','".setTanggal($inp[Tanggal])."','$inp[Temuan]','$inp[Penjelasan]','".setTanggal($inp[tanggalMulai])."','".setTanggal($inp[tanggalSelesai])."','".setTanggal($inp[tanggalAktual])."','$inp[PIC]','$inp[Keterangan]','$inp[Testing]','".setTanggal($inp[tanggalTest])."','$inp[Status]','$cUsername','$time','$file')";



		}else{

			$time=date("Y-m-d H:i:s");

			$sql = "UPDATE catatan_sistem SET

			Tanggal = '".setTanggal($inp[Tanggal])."',

			Temuan = '$inp[Temuan]',

			tanggalMulai = '".setTanggal($inp[tanggalMulai])."',

			tanggalSelesai = '".setTanggal($inp[tanggalSelesai])."',

			tanggalAktual = '".setTanggal($inp[tanggalMulai])."',

			PIC = '$inp[PIC]',

			Keterangan = '$inp[Keterangan]',

			Testing = '$inp[Testing]',

			tanggalTest = '".setTanggal($inp[tanggalTest])."',

			Status = '$inp[Status]',

			updateBy = '$inp[idPegawai]',

			updateDate = '$time',

			File = '$file'

			WHERE idCatatan = '$par[idCatatan]'

			";

		}

// echo $sql;
		// die();
		db($sql);


		echo "

		<script>

			alert('DATA BERHASIL DISIMPAN');

			window.location='index.php?" . getPar($par, "mode") . "';

		</script>

		";



	}



	?>
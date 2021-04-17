<?php
global $s, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername,$cNama,$cNickname;
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

$arrNama = arrayQuery("select username, nickName from app_user");
$sql = "SELECT * FROM catatan_sistem WHERE idCatatan = '$par[idCatatan]'";
// echo $sql;
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
empty($r[tanggalMulai]) ? $r[tanggalMulai] = date("d/m/Y") : $r[tanggalMulai] = getTanggal($r[tanggalMulai]);
empty($r['tanggalSelesai']) ? $r['tanggalSelesai'] = date('d/m/Y') : $r['tanggalSelesai'] = getTanggal($r[tanggalSelesai]);

//empty($r['tanggalAktual']) ? $r['tanggalAktual'] = date('d/m/Y') : $r['tanggalAktual'] = getTanggal($r[tanggalAktual]);
empty($r['tanggalTest']) ? $r['tanggalTest'] = date('d/m/Y') : $r['tanggalTest'] = getTanggal($r[tanggalTest]);
$inp[Tanggal] = $inp[Tanggal] == null ? date('d/m/Y') : getTanggal($inp[Tanggal]);
setValidation("is_null", "inp[Temuan]", "Anda belum mengisi Temuan");
setValidation("is_null", "inp[Penjelasan]", "Anda belum mengisi Penjelasan");
setValidation("is_null", "inp[tanggalMulai]", "Anda belum memilih Tanggal Mulai");
setValidation("is_null", "inp[tanggalSelesai]", "Anda belum memilih Tanggal Selesai");
// setValidation("is_null", "inp[tanggalAktual]", "Anda belum memilih Tanggal Aktual");

setValidation("is_null", "inp[tanggalTest]", "Anda belum memilih Tanggal Test");
// setValidation("is_null", "inp[Keterangan]", "Anda belum memilih Keterangan");
echo getValidation();

// echo $r[namaUser];
// echo $r[createdBy];
$r[createdBy] = empty($r[createdBy]) ? $cUsername : $r[createdBy];
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
		
		<ul class="hornav">
			<li class="current"><a href="#tabCatatan">CATATAN</a></li>
			<li><a href="#tabDokumen">DOKUMEN</a></li>
		</ul>
		<div id="tabCatatan" class="subcontent" style="display: block;">
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
					<input type="text" id="inp[createdBy]" name="inp[createdBy]" class="vsmallinput" value="<?= $r[createdBy];?>" readonly/>
				</div>
			</p>

			<p>
				<label class="l-input-small">Judul</label>
				<div class="field">
					<input type="text" id="inp[Temuan]" name="inp[Temuan]" class="smallinput" style="width:390px;" value="<?= $r[Temuan] ?>" />
				</div>
			</p>

			<p>
				<label class="l-input-small">Penjelasan</label>
				<div class="field">
					<textarea class="smallinput" style="height: 50px;width:390px;" id="inp[Penjelasan]" name="inp[Penjelasan]"><?= $r[Penjelasan] ?></textarea>
				</div>
			</p>

			<p>
				<label class="l-input-small">Kategori</label>
				<div class="field">
					<?//= comboData("SELECT namaData from mst_data where kodeCategory='ctelp' and statusData='t' order by urutanData","kodeData","namaData","inp[kategori]"," ",$r[kategori],"", "200px")?>
					<?= comboData("select * from mst_data where kodeCategory='KC' and statusData='t' order by urutanData","kodeData","namaData","inp[kategori_catatan]"," ",$r[kategori_catatan],"onchange=\"order('".getPar($par, "mode")."');\"", "200px","chosen-select")?>
				</div>
			</p>

			<p>
				<label class="l-input-small">File</label>
				<div class="field">
					<?php
					if(empty($r[File])){
						?>
						<input type="text" id="fileTemp" style="width: 300px;" name="fileTemp" class="smallinput">

						<div class="fakeupload" style="padding-right: 1px;" >
							<input type="file" id="file"  name="file" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;">
						</div>

						<?php }else{ ?>
						<img src="<?= getIcon($r[File]) ?>" width="25px" >
						<a href="?par[mode]=delFoto<?= getPar($par,"mode") ?>" onclick="return confirm('are you sure to delete image ?')" class="action delete"><span>Delete</span></a>
						<?php } ?>
					</div>
				</p>
		</div>

		<div id="tabDokumen" class="subcontent" style="display: none;">
			<div class="title" >
				<p style="float:right;">
				<?php
				if(empty($par[idCatatan])){
				?>
					<a onclick="alert('Silahkan klik tombol SIMPAN terlebih dahulu');" href="#" class="btn btn1 btn_document"><span>Tambah Data</span></a>
				<?php
				}else{
				?>
					<a onclick="openBox('popup.php?par[mode]=tambahFile<?= getPar($par,"mode")?>',725,300);" href="#" class="btn btn1 btn_document"><span>Tambah Data</span></a>
				<?php
				}
				?>
				</p>
			</div>
			<br clear="all"/>
			<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
				<thead>
					<tr>
						<th width="20">No.</th>
						<th width="*">Dokumen</th>
						<th width="50">View</th>
						<th width="50">DL</th>
						<th width="100">Upload</th>
						<th width="100">User</th>
						<th width="80">Size</th>
						<th width="50">Kontrol</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$sql2="select *, date(createDate) as tanggal from doc_file where idRencana = '$par[idCatatan]' ";
					$res2=db($sql2);
					while($r2=mysql_fetch_array($res2)){			
						$no++;
						?>
						<tr>
							<td><?= $no ?></td>
							<td><?= $r2[namaFile] ?></td>

							<td align="center"><a href="#" onclick="openBox('view.php?doc=fileDoc&par[idDoc]=<?= $r2[id] ?><?= getPar($par,"mode")?>',725,500);" class="detail"><span>Detail</span></a></td>
							<td align="center"><a href="download.php?d=fileDocRencana&f=<?= $r2[id] ?>"><img src="<?=getIcon($r2[file])?>" align="center" style="padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;"></a></td>
							<td align="center"><?= getTanggal($r2[tanggal]) ?></td>
							<td align="left"><?= $r2[createBy] ?></td>

							<td align="center"><?= getSizeFile($fRencana.$r2[file]) ?></td>
							<td align="center">
								<a onclick="openBox('popup.php?par[mode]=editDoc&par[idDoc]=<?= $r2[id] ?><?= getPar($par,"mode")?>',725,300);" href="#" title="Edit Data" class="edit"><span>Edit</span></a>
								<a href="?par[mode]=delDok&par[idDoc]=<?= $r2[id] ?><?= getPar($par,"mode,idDoc")?>" onclick="return confirm('are you sure to delete data ?');" title="Delete Data" class="delete"><span>Delete</span></a></td>
							</tr>		
							<?php  
						}
						?>
					</tbody>
				</table>
		</div>
		<br>
		<fieldset>
			<legend>PENYELESAIAN</legend>
			<p>
				<table style="width:100%">
					<tr>
						<td width="60%">
							<label class="l-input-small" style="width:33%">Tanggal Mulai</label>
							<div class="field">
								<input type="text" id="inp[tanggalMulai]" name="inp[tanggalMulai]" class="smallinput hasDatePicker" value="<?= $r[tanggalMulai] ?>"/>
							</div>
						</td>

						<td width="40%">
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
						<td width="60%">
							<label class="l-input-small" style="width:33%">PIC</label>
							<div class="field">
								<input type="text" id="inp[PIC]" name="inp[PIC]" class="smallinput" value="<?= $r[PIC] ?>"/>
							</div>
						</td>

						<td width="40%">
							<label>Target Aktual</label>
							<div class="field">
								<input type="text" id="inp[tanggalAktual]" name="inp[tanggalAktual]" class="smallinput hasDatePicker" value="<?= getTanggal($r[tanggalAktual]) ?>"/>
							</div>
						</td>
					</tr>
				</table>
			</p>
			<p>
				<label class="l-input-small">Keterangan</label>
				<div class="field">
					<textarea class="smallinput" style="height: 50px;width:390px;" id="inp[Keterangan]" name="inp[Keterangan]"><?= $r[Keterangan] ?></textarea>
				</div>
			</p>
			<p>
				<table style="width:100%">
					<tr>
						<td width="60%">
							<label class="l-input-small" style="width:33%">Testing</label>
							<div class="field">
								<div class="sradio" style="padding-top:5px;padding-left:8px;">
									<input type="radio" name="inp[Testing]" value="0" <?= $y." ".$default ?>> <span style="padding-right:10px;">Belum</span>
									<input type="radio" name="inp[Testing]" value="1" <?= $n ?>> <span style="padding-right:10px;">Sudah</span>
								</div>
							</div>
						</td>
						<td width="40%">
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

		<br>

		<?php if ($par[mode] != "add") { ?>
		<fieldset>

			<legend>HISTORY</legend>

			<table style="width: 100%">
				<tr>
					<td width="50%">
						<label class="l-input-small" style="width:50%">Input Date</label>
						<span class="field"><?= $r[createdDate] ?> &nbsp;</span>
					</td>
					<td width="50%">
						<label class="l-input-small">Input By</label>
						<span class="field"><?= $r[createdBy] ?> &nbsp;</span>		
					</td>
				</tr>
			</table>

			<table style="width: 100%">
				<tr>
					<td width="50%">
						<label class="l-input-small" style="width:50%">Update Date</label>
						<span class="field"><?= $r[updateDate] ?> &nbsp;</span>
					</td>
					<td width="50%">
						<label class="l-input-small">Update By</label>
						<span class="field"><?= $arrNama[$r[updateBy]] ?> &nbsp;</span>		
					</td>
				</tr>
			</table>

		</fieldset>
		<?php } ?>

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



		// if(empty($file)) $file = getField("select File from catatan_sistem where idCatatan='$par[idCatatan]'");



	//$id_lokasi = getField("SELECT id_lokasi FROM alamat_lokasi ORDER BY id_lokasi DESC LIMIT 1")+1;	



			if ($params == "insert") {

				$time=date("Y-m-d H:i:s");

				$idCatatan = getField("SELECT idCatatan from catatan_sistem order by idCatatan desc")+1;

				$nomor = "C"."$idCatatan";

				$sql = "INSERT INTO catatan_sistem(idCatatan,nomor,Tanggal,Temuan,Penjelasan,tanggalMulai,kategori_catatan,tanggalSelesai,tanggalAktual,PIC,Keterangan,Testing,tanggalTest,Status,createdBy,createdDate,File) VALUES ('$idCatatan','$nomor','".setTanggal($inp[Tanggal])."','$inp[Temuan]','$inp[Penjelasan]','".setTanggal($inp[tanggalMulai])."','$inp[kategori_catatan]','".setTanggal($inp[tanggalSelesai])."','".setTanggal($inp[tanggalAktual])."','$inp[PIC]','$inp[Keterangan]','$inp[Testing]','".setTanggal($inp[tanggalTest])."','$inp[Status]','$cUsername','$time','$file')";


			db($sql);


			echo "

			<script>

				alert('DATA BERHASIL DISIMPAN');

				window.location='index.php?par[idCatatan]=$idCatatan" . getPar($par) . "';

			</script>

			";



			}else{

				$time=date("Y-m-d H:i:s");

				$sql = "UPDATE catatan_sistem SET

				Tanggal = '".setTanggal($inp[Tanggal])."',

				Temuan = '$inp[Temuan]',
				Penjelasan = '$inp[Penjelasan]',


				tanggalMulai = '".setTanggal($inp[tanggalMulai])."',

				tanggalSelesai = '".setTanggal($inp[tanggalSelesai])."',

				tanggalAktual = '".setTanggal($inp[tanggalMulai])."',

				PIC = '$inp[PIC]',

				Keterangan = '$inp[kategori_catatan]',

				Testing = '$inp[Testing]',

				tanggalTest = '".setTanggal($inp[tanggalTest])."',

				Status = '$inp[Status]',

				updateBy = '$cUsername',

				updateDate = '$time',

				File = '$file'

				WHERE idCatatan = '$par[idCatatan]'

				";

			db($sql);


			echo "

			<script>

				alert('DATA BERHASIL DISIMPAN');

				window.location='index.php?par[idCatatan]=$par[idCatatan]" . getPar($par) . "';

			</script>

			";


			}
		 // echo $sql;
		 // die();



		


		}



		?>
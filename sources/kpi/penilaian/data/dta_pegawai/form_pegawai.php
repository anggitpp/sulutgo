<?php
global $s, $par, $menuAccess, $arrTitle, $arrParameter, $cUsername, $json, $migrasi;
$folder_upload = "images/foto/";
if (isset($_POST["btnSimpan"])) {
	simpanPegawai();
}

function simpanPegawai(){
	global $s,$inp,$par,$detail,$cUsername,$folder_upload;			
	repField();
	$nextId = getField("SELECT id FROM emp ORDER BY id DESC LIMIT 1")+1;
	$nextId2 = getField("SELECT id FROM pen_pegawai ORDER BY id DESC LIMIT 1")+1;
	if(empty($par[idPegawai])){	
		$file = uploadFiles("$nextId", "file", "$folder_upload", "foto".date("Y")."-");

		$sql="INSERT INTO emp (id, name, reg_no, cat, birth_date, join_date, gender, create_by, create_date, pic_filename, status) 
                       VALUES ('$nextId', '$inp[nama_pegawai]', '$inp[NPP]', '531', '".setTanggal($inp[tanggal_lahir])."', '".setTanggal($inp[join_date])."', '$inp[gender]', '$cUsername', '".date("Y-m-d H:i:s")."','$file','t');";
        
		db($sql);

		$sql2="INSERT INTO emp_phist (id, parent_id, dir_id, div_id, pos_name, rank, grade, skala, location, remark, leader_id, top_id, status, create_by, create_date) VALUES ('', '$nextId', '$inp[direktorat]', '$inp[divisi]', '$inp[jabatan]', '$inp[pangkat]', '$inp[grade]', '$inp[skala]', '$inp[lokasi_kerja]', '$inp[keterangan]', '$inp[dari_atasan]', '$inp[atasan_langsung]','1', '$cUsername', '".date("Y-m-d H:i:s")."');";
        
		db($sql2);

		$sql3="INSERT INTO pen_pegawai (id, idPegawai, createBy, createDate) VALUES ('$nextId2', '$nextId', '$cUsername', '".date("Y-m-d H:i:s")."');";

		db($sql3);
	}else{
		if(empty($r[pic_filename])){
			$file = uploadFiles("$nextId", "file", "$folder_upload", "foto".date("Y")."-");
			$sql="UPDATE emp SET name = '$inp[nama_pegawai]', reg_no = '$inp[NPP]', birth_date = '".setTanggal($inp[tanggal_lahir])."', join_date = '".setTanggal($inp[join_date])."', gender = '$inp[gender]', pic_filename = '$file' WHERE id = '$par[idPegawai]'";
		}else{
			$sql="UPDATE emp SET name = '$inp[nama_pegawai]', reg_no = '$inp[NPP]', birth_date = '".setTanggal($inp[tanggal_lahir])."', join_date = '".setTanggal($inp[join_date])."', gender = '$inp[gender]' WHERE id = '$par[idPegawai]'";
		}

		db($sql);
		
		$sql2="UPDATE emp_phist SET dir_id = '$inp[direktorat]', div_id = '$inp[divisi]', pos_name = '$inp[jabatan]', rank = '$inp[pangkat]', grade = '$inp[grade]', skala = '$inp[skala]', location = '$inp[lokasi_kerja]', remark = '$inp[keterangan]', leader_id = '$inp[dari_atasan]', top_id = '$inp[atasan_langsung]' where parent_id = '$par[idPegawai]'";

		db($sql2);
	}
	echo "
	<script>
		alert('Data pegawai berhasil disimpan'); closeBox(); reloadPage();
	</script>";

}

$default = "checked";

$query = db("select * from emp as a
join emp_phist as b on(a.id = b.parent_id) where a.id = '$par[idPegawai]'");
$r = mysql_fetch_array($query);

$queryDir = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='X03' order by kodeInduk, urutanData";
$queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' and t1.kodeInduk = '$r[dir_id]' order by t1.urutanData";
$queryRank = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S09' order by namaData";
$queryGrade = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S10' order by namaData";
$queryLocation = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S06' order by namaData";
$querySkala = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'SG' and statusData = 't' order by urutanData";

?>
<style type="text/css">
	#inp_direktorat__chosen{
		min-width: 220px;
	}

	#inp_divisi__chosen{
		min-width: 220px;
	}

	#inp_pangkat__chosen{
		min-width: 220px;
	}

	#inp_grade__chosen{
		min-width: 220px;
	}

	#inp_skala__chosen{
		min-width: 220px;
	}

	#inp_lokasi_kerja__chosen{
		min-width: 220px;
	}

	#inp_atasan_langsung__chosen{
		min-width: 220px;
	}

	#inp_atasan_dari__chosen{
		min-width: 220px;
	}
</style>
<div class="centercontent contentpopup">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>					
	</div>
	<div id="contentwrapper" class="contentwrapper">								
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<input type="hidden" id="chk" name="chk" value=""/>
			<div style="position:absolute; right:20px; top:14px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>

				<input type="button" class="cancel radius2" style="float:right" value="Batal" onclick="closeBox();">
			</div>
			<?php  
			if(!empty($r[pic_filename])){
				?>
				<fieldset>
					<p>
						<div align="center">
							<img src="<?= $folder_upload.$r[pic_filename] ?>" title="Download" style="margin-top: 5px; width:120px; height: 150px;">
							<br>
							<a href="?par[mode]=delete_file&par[idf]=<?= $par[idPegawai] ?><?=getPar($par,"mode,idPegawai")?>" onclick="return confirm('are you sure to delete this file?')">Delete</a>
						</div>
					</p>
				</fieldset>
				<br>
				<?php  
			}
			?>	
			<fieldset>
				<legend>PEGAWAI</legend>
				<p>
					<label class="l-input-small">Nama Pegawai</label>
					<div class="field">
						<input type="text" id="inp[nama_pegawai]" name="inp[nama_pegawai]" value="<?= $r[name] ?>" class="mediuminput" style="width: 275px;"/>
					</div>
				</p>
				<p>
					<label class="l-input-small">NPP</label>
					<div class="field">
						<input type="text" id="inp[NPP]" name="inp[NPP]" value="<?= $r[reg_no] ?>" class="mediuminput" style="width: 275px;"/>
					</div>
				</p>
				<p>
					<label class="l-input-small">Tanggal Lahir</label>
					<div class="field">
						<input type="text" id="inp[tanggal_lahir]" name="inp[tanggal_lahir]" value="<?= getTanggal($r[birth_date]) ?>" class="hasDatePicker" style="width: 275px;"/>
					</div>
				</p>
                <p>
					<label class="l-input-small">Tanggal Masuk</label>
					<div class="field">
						<input type="text" id="inp[join_date]" name="inp[join_date]" value="<?= getTanggal($r[join_date]) ?>" class="hasDatePicker" style="width: 275px;"/>
					</div>
				</p>
				<p>
					<label class="l-input-small">Gender</label>
					<div class="field">
						<div class="fradio">
							<input type="radio" id="inp[gender]" <?= $default ?> name="inp[gender]" value="M" style="width:300px;" <?= ($r[gender] == 'M' ? "checked" : '') ?>/> Pria

							<input type="radio" id="inp[gender]" name="inp[gender]" value="F" style="width:300px;" <?= ($r[gender] == 'F' ? "checked" : '') ?>/> Wanita
						</div>
					</div>
				</p>
				<?php
				if(empty($r[pic_filename])){
					?>
					<p>
						<label class="l-input-small">Foto</label>
						<div class="field">
							<input type="file" id="file" name="file" class="mediuminput" style="width: 300px; margin-top: 5px;">
						</div>
					</p>
					<?php  
				}
				?>
			</fieldset>
			<br>
			<fieldset>
				<legend>POSISI</legend>
				<p>
					<label class="l-input-small">Direktorat</label>
					<div class="field">
						<?= comboData($queryDir, "id", "description", "inp[direktorat]", "- Pilih Direktorat -", $r[dir_id], "onchange=\"getSub('direktorat', 'divisi', '" . getPar($par, "mode") . "')\"", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Divisi</label>
					<div class="field">
						<?= comboData($queryDiv, "id", "description", "inp[divisi]", "- Pilih Divisi -", $r[div_id], "", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Jabatan / Posisi</label>
					<div class="field">
						<input type="text" id="inp[jabatan]" name="inp[jabatan]" value="<?= $r[pos_name] ?>" class="mediuminput" style="width: 275px;"/>
					</div>
				</p>
				<p>
					<label class="l-input-small">Pangkat</label>
					<div class="field">
						<?= comboData($queryRank, "id", "description", "inp[pangkat]", "- Pilih Pangkat -", $r[rank], "onchange=\"getSub('pangkat', 'grade', '" . getPar($par, "mode") . "')\"", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Grade</label>
					<div class="field">
						<?= comboData($queryGrade, "id", "description", "inp[grade]", "- Pilih Grade -", $r[grade], "", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Skala</label>
					<div class="field">
						<?= comboData($querySkala, "id", "description", "inp[skala]", "- Pilih Skala -", $r[skala], "", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Lokasi Kerja</label>
					<div class="field">
						<?= comboData($queryLocation, "id", "description", "inp[lokasi_kerja]", "- Pilih Lokasi Kerja -", $r[location], "", "230px","chosen-select"); ?>
					</div>
				</p>
				<p>
					<label class="l-input-small">Keterangan</label>
					<div class="field">
						<textarea name="inp[keterangan]" style='width:300px;'/><?= $r[remark] ?></textarea>
					</div>
				</p>
			</fieldset>
			<br/>
			<br style="clear: both;">
			<div align="right">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/> 
				<input type="button" class="cancel radius2" style="float:right" value="Batal" onclick="closeBox();">
			</div>
		</form>
	</div>
</div>
<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/recruit/kebutuhan/";

function getPegawai()
{
	global $par;

	$sql = "SELECT t1.pos_name, t2.namaData as namaDivisi, t3.namaData as namaDepartemen from emp_phist t1 left join mst_data t2 on t1.div_id = t2.kodeData left join mst_data t3 on t1.dept_id = t3.kodeData where t1.parent_id = '$par[idPegawai]' and t1.status = '1'";
	$res = db($sql);
	$r = mysql_fetch_assoc($res);

	return implode("\t", $r);
}

function getPosisi()
{
	global $par;

	$sql = "SELECT * from rec_job_posisi where id_posisi = '$par[idPosisi]'";
	$res = db($sql);
	$r = mysql_fetch_assoc($res);

	echo json_encode($r);
}

function subData()
{
	global $par;

	$data = arrayQuery("SELECT concat(kodeData, '\t', namaData) from mst_data where statusData = 't' and kodeInduk = '$par[kodeInduk]' order by namaData");

	return implode("\n", $data);
}

function getNumber()
{
	global $inp;

	$prefix = "REC";
	list($hari, $bulan, $tahun) = explode("/", $inp[propose_date]);
	$tahun = empty($tahun) ? date('Y') : $tahun;
	$bulan = empty($bulan) ? date('m') : $bulan;
	$getLastNumber = getField("select substr(no, 1, 3) from rec_plan where year(propose_date) = '$tahun' order by substr(no, 1, 3) desc limit 1");

	return str_pad(($getLastNumber + 1), 3, "0", STR_PAD_LEFT) . "/" . $prefix . "/" . getRomawi($bulan) . "/" . substr($tahun, -2, 2);
}

function tambah()
{
	global $inp, $par, $cUser, $arrParam, $s;

	repField();

	$id = getLastId("rec_plan", "id");
	$inp[propose_date] = setTanggal($inp[propose_date]);
	$inp[need_date] = setTanggal($inp[need_date]);
	$inp[male] = empty($inp[male]) ? 0 : 1;
	$inp[female] = empty($inp[female]) ? 0 : 1;
	$inp[no] = getNumber();
	$inp[create_by] = $cUser;
	$inp[create_date] = date('Y-m-d H:i:s');
	$inp[cat] = $arrParam[$s];

	$sql = "insert into rec_plan set id='$id', cat = '$inp[cat]', no = '$inp[no]', emp_id = '$inp[emp_id]', subject = '$inp[subject]', propose_date = '$inp[propose_date]', need_date = '$inp[need_date]', loc_id = '$inp[loc_id]', id_posisi = '$inp[id_posisi]', pos_function = '$inp[pos_function]', person_needed = '$inp[person_needed]', edu_id = '$inp[edu_id]', edu_id2 = '$inp[edu_id2]', edu_fac_id = '$inp[edu_fac_id]', edu_fac_id2 = '$inp[edu_fac_id2]', edu_fac_id3 = '$inp[edu_fac_id3]', edu_dept_id = '$inp[edu_dept_id]', edu_dept_id2 = '$inp[edu_dept_id2]', edu_dept_id3 = '$inp[edu_dept_id3]', emp_sta = '$inp[emp_sta]', male = '$inp[male]', female = '$inp[female]', age_from = '$inp[age_from]', age_to = '$inp[age_to]', salary = '$inp[salary]', vendor = '$inp[vendor]', weight = '$inp[weight]', height = '$inp[height]', term = '$inp[term]', experience = '$inp[experience]', characters = '$inp[characters]', expertise = '$inp[expertise]', job_desk = '$inp[job_desk]', abilities = '$inp[abilities]', reason = '$inp[reason]', comliterates = '$inp[comliterates]', language = '$inp[language]', remark = '$inp[remark]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
	global $inp, $par, $cUser;

	repField();

	$inp[propose_date] = setTanggal($inp[propose_date]);
	$inp[need_date] = setTanggal($inp[need_date]);
	$inp[male] = empty($inp[male]) ? 0 : 1;
	$inp[female] = empty($inp[female]) ? 0 : 1;
	$inp[no] = getNumber();
	$inp[update_by] = $cUser;
	$inp[update_date] = date('Y-m-d H:i:s');

	$sql = "update rec_plan set no = '$inp[no]', emp_id = '$inp[emp_id]', subject = '$inp[subject]', propose_date = '$inp[propose_date]', need_date = '$inp[need_date]', loc_id = '$inp[loc_id]', id_posisi = '$inp[id_posisi]', pos_function = '$inp[pos_function]', person_needed = '$inp[person_needed]', edu_id = '$inp[edu_id]', edu_id2 = '$inp[edu_id2]', edu_fac_id = '$inp[edu_fac_id]', edu_fac_id2 = '$inp[edu_fac_id2]', edu_fac_id3 = '$inp[edu_fac_id3]', edu_dept_id = '$inp[edu_dept_id]', edu_dept_id2 = '$inp[edu_dept_id2]', edu_dept_id3 = '$inp[edu_dept_id3]', emp_sta = '$inp[emp_sta]', male = '$inp[male]', female = '$inp[female]', age_from = '$inp[age_from]', age_to = '$inp[age_to]', salary = '$inp[salary]', vendor = '$inp[vendor]', weight = '$inp[weight]', height = '$inp[height]', term = '$inp[term]',  experience = '$inp[experience]', characters = '$inp[characters]', expertise = '$inp[expertise]', job_desk = '$inp[job_desk]', abilities = '$inp[abilities]', reason = '$inp[reason]', comliterates = '$inp[comliterates]', language = '$inp[language]', remark = '$inp[remark]', status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function setApprove()
{
	global $inp, $par, $cUser;

	repField();

	$inp[approve_by] = $cUser;
	$inp[approve_date] = date('Y-m-d H:i:s');

	$sql = "update rec_plan set approve = '$inp[approve]', approve_remark = '$inp[approve_remark]', approve_by = '$inp[approve_by]', approve_date = '$inp[approve_date]' where id = '$par[id]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode, id") . "'</script>";
}

function lihat()
{
	global $s, $par, $arrTitle, $menuAccess, $arrParam;

	$par[tahunKebutuhan] = empty($par[tahunKebutuhan]) ? date('Y') : $par[tahunKebutuhan];

	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form action="" method="post" id="form" class="stdform">
			<div id="pos_l" style="float:left;">
				<p>
					<input type="text" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" style="width:250px;" class="mediuminput" />
					<input type="submit" value="GO" class="btn btn_search btn-small" />
				</p>
			</div>
			<div id="pos_r" style="float:right;">
				<?= comboData("SELECT distinct(year(propose_date)) as tahunData FROM rec_plan order by year(propose_date) asc", "tahunData", "tahunData", "par[tahunKebutuhan]", "", $par[tahunKebutuhan], "onchhange=\"document.getElementById('form').submit();\"", "80px", "chosen-select") ?>
				<a href="?par[mode]=add<?= getPar($par, "mode,kodeAktifitas") ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th width="100">Nomor</th>
					<th width="100">Tanggal</th>
					<th width="*">Posisi</th>
					<th width="50">Jumlah</th>
					<th width="50">Approval</th>
					<th width="50">Status</th>
					<?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "where year(propose_date) = '$par[tahunKebutuhan]' and cat = '" . $arrParam[$s] . "'";
					if (!empty($par[filterData]))
						$filter .= " and (nomor LIKE '%$par[filterData]%' or t2.subject LIKE '%$par[filterData]%') ";

					$sql = "SELECT t1.id, t1.no, t1.propose_date, t1.person_needed, t1.approve, t1.status, t2.subject from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi $filter order by no";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_array($res)) {
						$no++;

						if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
							$control = "";
							if (!empty($menuAccess[$s]["edit"]))
								$control .= "<a href=\"?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
							if (!empty($menuAccess[$s]["delete"]))
								$control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						}
						?>
					<tr>
						<td align="right"><?= $no ?>.</td>
						<td align="center"><?= $r[no] ?></td>
						<td align="center"><?= getTanggal($r[propose_date]) ?></td>
						<td><?= $r[subject] ?></td>
						<td align="right"><?= $r[person_needed] ?></td>
						<td align="center"><a href="?par[mode]=approve&par[id]=<?= $r[id] . getPar($par, "mode,id") ?>"><img src="styles/images/<?= $r[approve] == "t" ? "t" : "f" ?>.png" title='<?= $r[approve] == "t" ? "Disetujui" : "Ditolak" ?>'></a></td>
						<td align="center"><img src="styles/images/<?= $r[status] == "t" ? "t" : "f" ?>.png" title='<?= $r[status] == "t" ? "Disetujui" : "Ditolak" ?>'></td>
						<td align="center"><?= $control ?></td>
					</tr>
				<?php
					}
					?>
			</tbody>
		</table>
	</div>
<?php
}

function hapus()
{
	global $par;

	$sql = "delete from rec_plan where id='$par[id]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function form()
{
	global $s, $par, $arrTitle, $arrParameter, $ui;

	$sql = "SELECT * FROM rec_plan WHERE id='$par[id]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$r[no] = empty($r[no]) ? getNumber() : $r[no];

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('X04', 'X05', 'X06')");

	$stylePerguruan = $r[edu_id2] > 614 ? "style=\"display:block\"" : "style=\"display:none\"";

	setValidation("is_null", "inp[subject]", "you must fill Judul Permintaan");
	setValidation("is_null", "inp[propose_date]", "you must fill Tanggal Pengajuan");
	setValidation("is_null", "inp[need_date]", "you must fill Tanggal Kebutuhan");
	setValidation("is_null", "inp[emp_id]", "you must fill NPP - Nama");
	setValidation("is_null", "inp[edu_id]", "you must fill Pendidikan dari");
	setValidation("is_null", "inp[edu_id2]", "you must fill Pendidikan sampai");
	//setValidation("is_null", "inp[id_posisi]", "you must fill Job Posisi");
	//setValidation("is_null", "inp[emp_sta]", "you must fill Status Pegawai");
	echo getValidation();
	?>

	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread(ucwords($par['mode'] . " data")) ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?> " onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<p style="position:absolute;top:5px;right:5px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode"); ?>'" />
			</p>
			<div class="widgetbox">
				<div class="title">
					<h3>PEMOHON</h3>
				</div>
			</div>
			<table style="width: 100%">
				<tr>
					<td style="width: 50%">
						<p><?= $ui->createField("Nomor Registrasi", "inp[no]", $r[no], "t", "t", "", "", "", "t") ?></p>
						<p><?= $ui->createField("Judul Permintaan", "inp[subject]", $r[subject], "t", "t") ?></p>
						<p><?= $ui->createField("Tanggal Pengajuan", "inp[propose_date]", getTanggal($r[propose_date]), "t", "t", "", "", "", "", "t") ?></p>
						<p><?= $ui->createField("Tanggal Kebutuhan", "inp[need_date]", getTanggal($r[need_date]), "t", "t", "", "", "", "", "t") ?></p>
						<p><?= $ui->createComboData("Lokasi Penempatan", "select kodeData id, namaData description from mst_data where kodeCategory = 'S06' and statusData = 't' order by urutanData", "id", "description", "inp[loc_id]", $r[loc_id], "", "300px", "t", "", "t") ?></p>
					</td>
					<td style="width: 50%">
						<?php
							$sql_ = "select pos_name, div_id, dept_id from emp_phist where parent_id = '$r[emp_id]' AND status = '1'";
							$res_ = db($sql_);
							$r_ = mysql_fetch_array($res_);
							?>
						<p><?= $ui->createComboData("Nama", "select id, concat(reg_no, ' - ', name) description from emp where status = '535' order by name", "id", "description", "inp[emp_id]", $r[emp_id], "onchange=\"getPegawai(this.value, '" . getPar($par, "mode") . "');\"", "300px", "t", "t") ?></p>
						<p><?= $ui->createField("Jabatan", "jabatan", $r_[pos_name], "", "", "", "", "", "t") ?></p>
						<p><?= $ui->createField("Divisi", "divisi", $arrMaster[$r_[div_id]], "", "", "", "", "", "t") ?></p>
						<p><?= $ui->createField("Departemen", "departemen", $arrMaster[$r_[dept_id]], "", "", "", "", "", "t") ?></p>
						<p>
							<?= $ui->createComboData("Job Posisi", "select id_posisi id, subject description from rec_job_posisi order by id_posisi", "id", "description", "inp[id_posisi]", $r[id_posisi], "onchange=\"getPosisi(this.value, '" . getPar($par, "mode") . "')\"", "300px", "t", "t") ?>
						</p>
					</td>
				</tr>
			</table>
			<div class="widgetbox">
				<div class="title">
					<h3>PERSYARATAN</h3>
				</div>
			</div>
			<table style="width: 100%">
				<tr>
					<td style="width: 50%">
						<p>
							<label class="l-input-small2">Jenis Kelamin</label>
							<div class="field">
								<input type="checkbox" id="male" name="inp[male]" value="1" <?= ($r[male] == 1 ? " checked=checked" : "") ?>>&nbsp;&nbsp;Laki-Laki&nbsp;&nbsp;
								<input type="checkbox" id="female" name="inp[female]" value="1" <?= ($r[female] == 1 ? " checked=checked" : "") ?>>&nbsp;&nbsp;Perempuan
							</div>
						</p>
						<p>
							<?= $ui->createComboData("Min. Pendidikan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R11' and statusData = 't' order by urutanData", "id", "description", "inp[edu_id]", $r[edu_id], "", "300px", "t", "t", "t") ?>
						</p>
						<div id="divFakultas" <?= $stylePerguruan ?>>
							<p>
								<?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id]", $r[edu_fac_id], "onchange=\"getSub('edu_fac_id', 'edu_dept_id', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
							</p>
							<p>
								<?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id2]", $r[edu_fac_id2], "onchange=\"getSub('edu_fac_id2', 'edu_dept_id2', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
							</p>
							<p>
								<?= $ui->createComboData("Fakultas", "select kodeData id, namaData description from mst_data where kodeCategory = 'R12' order by namaData", "id", "description", "inp[edu_fac_id3]", $r[edu_fac_id3], "onchange=\"getSub('edu_fac_id3', 'edu_dept_id3', '" . getPar($par, "mode") . "');\"", "300px", "t", "", "t") ?>
							</p>
						</div>
						<p><?= $ui->createField("Jumlah", "inp[person_needed]", $r[person_needed], "", "t", "style='width:50px;'", "onkeyup=\"cekAngka(this);\"") ?></p>
						<p>
							<label class="l-input-small2">Usia</label>
							<div class="field">
								<input type="text" id="inp[age_from]" name="inp[age_from]" style="width:50px;" value="<?= $r[age_from] ?>" class="mediuminput" />
								&nbsp; s/d &nbsp;
								<input type="text" id="inp[age_to]" name="inp[age_to]" style="width:50px;" value="<?= $r[age_to] ?>" class="mediuminput" />
							</div>
						</p>
						<p><?= $ui->createField("Tinggi", "inp[height]", $r[height], "", "t", "style='width:50px;'", "onkeyup=\"cekAngka(this);\"") ?></p>
						<p><?= $ui->createField("Berat", "inp[weight]", $r[weight], "", "t", "style='width:50px;'", "onkeyup=\"cekAngka(this);\"") ?></p>
					</td>
					<td style="width: 50%">
						<p><?= $ui->createField("Job Function", "inp[pos_function]", $r[pos_function]) ?></p>
						<p>
							<?= $ui->createComboData("Status Pegawai", "select kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='" . $arrParameter[5] . "' and statusData = 't' order by urutanData", "id", "description", "inp[emp_sta]", $r[emp_sta], "", "300px", "t", "t") ?>
						</p>
						<div id="divJurusan" <?= $stylePerguruan ?>>
							<p>
								<?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id]", $r[edu_dept_id], "", "300px", "t") ?>
							</p>
							<p>
								<?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id2]", $r[edu_dept_id2], "", "300px", "t") ?>
							</p>
							<p>
								<?= $ui->createComboData("Jurusan", "select kodeData id, namaData description from mst_data where kodeCategory = 'R13' order by namaData", "id", "description", "inp[edu_dept_id3]", $r[edu_dept_id3], "", "300px", "t") ?>
							</p>
						</div>
						<p>
							<?= $ui->createComboData("Vendor", "select kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='" . $arrParameter[83] . "' and statusData = 't' order by urutanData", "id", "description", "inp[vendor]", $r[vendor], "", "300px", "t") ?>
						</p>
						<p>
							<?= $ui->createComboData("Gaji Minimal", "select kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='GM' and statusData = 't' order by urutanData", "id", "description", "inp[salary]", $r[salary], "", "300px", "t") ?>
						</p>
						<p>
							<?= $ui->createComboData("Jangka Waktu", "select kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='JWP' and statusData = 't' order by urutanData", "id", "description", "inp[term]", $r[term], "", "300px", "t") ?>
						</p>
						<p><?= $ui->createField("Pengalaman Kerja", "inp[experience]", $r[experience]) ?></p>
					</td>
				</tr>
			</table>

			<div class="widgetbox">
				<div class="title">
					<h3>TASK/RESPONSIBILITIES & JOB DESCRIPTION</h3>
				</div>
			</div>
			<div style="width:100%">
				<p id="jobdesc" style="float:left;"><?= getField("select jobdesc from rec_plan where id = '$r[pos_available]'") ?></p>
			</div>
			<div class="widgetbox">
				<div class="title">
					<h3>INFO TAMBAHAN</h3>
				</div>
			</div>
			<table style="width: 100%">
				<tr>
					<td style="width: 50%">
						<p><?= $ui->createTextArea("Karakter Pribadi", "inp[characters]", $r[characters], "style='width:300px'", "t") ?></p>
						<p><?= $ui->createTextArea("Uraian Tugas Utama", "inp[job_desk]", $r[job_desk], "style='width:300px'", "t") ?></p>
						<p><?= $ui->createTextArea("Keahlian Komputer", "inp[comliterates]", $r[comliterates], "style='width:300px'", "t") ?></p>
					</td>
					<td style="width: 50%">
						<p><?= $ui->createTextArea("Keahlian Khusus", "inp[expertise]", $r[expertise], "style='width:300px'") ?></p>
						<p><?= $ui->createTextArea("Kemampuan", "inp[abilities]", $r[abilities], "style='width:300px'") ?></p>
						<p><?= $ui->createTextArea("Kemampuan Bahasa", "inp[language]", $r[language], "style='width:300px'") ?></p>
					</td>
				</tr>
			</table>

			<div class="widgetbox">
				<div class="title" style="margin-bottom:0px;">
					<h3>CATATAN</h3>
				</div>
			</div>
			<p><?= $ui->createTextArea("Alasan Perekrutan", "inp[reason]", $r[reason]) ?></p>
			<p><?= $ui->createTextArea("Catatan", "inp[remark]", $r[remark]) ?></p>
			<p><?= $ui->createRadio("Status", "inp[status]", array(""=> "Proses", "o" => "Pending", "f" => "Ditolak", "t" => "Diterima"), $r[status]) ?></p>
			</ul>
			<br clear="all" />
			<?php
				if ($par[mode] == "approve") {
					?>
				<div class="widgetbox">
					<div class="title" style="margin-bottom:0px;">
						<h3>APPROVAL</h3>
					</div>
				</div>
				<p><?= $ui->createRadio("Status", "inp[approve]", array("t" => "Disetujui", "f" => "Ditolak"), $r[approve]) ?></p>
				<p><?= $ui->createTextArea("Keterangan", "inp[approve_remark]", $r[approve_remark]) ?></p>
			<?php
				}
				?>
		</form>
	</div>
<?php
}

function getContent($par)
{
	global $s, $_submit, $menuAccess;

	switch ($par[mode]) {
		case "lst":
			$text = lData();
			break;
		case "subData":
			$text = subData();
			break;
		case "detail_kebutuhan":
			$text = detail_kebutuhan();
			break;
		case "peg":
			$text = pegawai();
			break;
		case "addFile":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formFile() : tambahFile();
			else $text = lihat();
			break;
		case "addPegawai":
			if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPegawai() : tambahPegawai();
			else $text = lihat_kebutuhan();
			break;
		case "edit":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
			else $text = lihat();
			break;
		case "approve":
			if (isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form() : setApprove();
			else $text = lihat();
			break;
		case "delPeg":
			$text = hapusPegawai();
			break;
		case "del":
			$text = hapus();
			break;
		case "lihat_kebutuhan":
			$text =  lihat_kebutuhan();
			break;
		case "getPosisi":
			$text = getPosisi();
			break;
		case "add":
			if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
			else $text = lihat();
			break;
		case "updStatus":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formStatus() : updateStatus();
			else $text = lihat();
			break;
		case "addApproval":
			if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formApproval() : tambahApproval();
			else $text = lihat();
			break;
		case "editApproval":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formApproval() : ubahApproval();
			else $text = lihat();
			break;
		case 'jobposisi':
			$text = jobposisi();
			break;
		case 'getPegawai':
			$text = getPegawai();
			break;
		case "sub":
			$text = sub();
			break;
		default:
			$text = lihat();
			break;
	}

	return $text;
}
?>
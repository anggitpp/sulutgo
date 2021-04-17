<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function subData()
{
	global $par;

	$data = arrayQuery("SELECT concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");

	return implode("\n", $data);
}
function divisi()
{
	global $par;

	$data = arrayQuery("SELECT concat(t1.kodeData, '\t', t1.namaData) from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk	where t2.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");

	return implode("\n", $data);
}
function departemen()
{
	global $par;

	$data = arrayQuery("SELECT concat(t1.kodeData, '\t', t1.namaData) from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
	where t3.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");

	return implode("\n", $data);
}
function unit()
{
	global $par;

	$data = arrayQuery("SELECT concat(t1.kodeData, '\t', t1.namaData) from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");

	return implode("\n", $data);
}

function ubah()
{
	global $inp, $par, $cUser;

	$inp[start_date] = setTanggal($inp[start_date]);
	$inp[end_date] = setTanggal($inp[end_date]);
	$inp[sk_date] = setTanggal($inp[sk_date]);
	$inp[create_by] = $cUser;
	$inp[create_date] = date('Y-m-d H:i:s');
	$inp[update_by] = $cUser;
	$inp[update_date] = date('Y-m-d H:i:s');

	$sql = "SELECT * from rec_applicant where id = '$par[id]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	if (!empty($r[emp_id])) {
		//--------------------- UPDATE DATA PEGAWAI -----------------------//
		$sql = "update emp set name = '$r[name]', alias = '$r[alias]', birth_place = '$r[birth_place]', birth_date = '$r[birth_date]', ktp_no = '$r[ktp_no]', gender = '$r[gender]', ktp_address = '$r[ktp_address]', ktp_prov = '$r[ktp_prov]', ktp_city = '$r[ktp_city]', dom_address = '$r[dom_address]', dom_prov = '$r[dom_prov]', dom_city = '$r[dom_city]', cell_no = '$r[cell_no]', phone_no = '$r[phone_no]', religion = '$r[religion]', cat = '$inp[cat]', join_date = '$inp[start_date]', leave_date = '$inp[end_date]', uni_cloth = '$r[uni_cloth]', uni_pant = '$r[uni_pant]', uni_shoe = '$r[uni_shoe]', marital = '$r[marital]', email = '$r[email]', npwp_no = '$r[npwp_no]', npwp_date = '$r[npwp_date]', bpjs_no = '$r[bpjs_no]', bpjs_date = '$r[bpjs_date]', blood_type = '$r[blood_type]', blood_resus = '$r[blood_resus]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$r[emp_id]'";
		db($sql);

		//--------------------- UPDATE DATA POSISI -----------------------//
		$sql = "update emp_phist set pos_name = '$inp[pos_name]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', remark = '$inp[remark]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', group_id = '$inp[group_id]', proses_id = '$inp[proses_id]', company_id = '$inp[company_id]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$r[emp_id]' ";
		db($sql);

		//--------------------- UPDATE DATA PLACEMENT -----------------------//
		$sql = "update rec_applicant_placement set cat = '$inp[cat]', pos_name = '$inp[pos_name]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', remark = '$inp[remark]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', group_id = '$inp[group_id]', proses_id = '$inp[proses_id]', company_id = '$inp[company_id]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$par[id]' ";
		db($sql);
	} else {
		//--------------------- INSERT DATA PEGAWAI -----------------------//
		$id = getField("select id from emp order by id desc limit 1") + 1;
		$sql = "insert into emp set id = '$id', name = '$r[name]', alias = '$r[alias]', birth_place = '$r[birth_place]', birth_date = '$r[birth_date]', ktp_no = '$r[ktp_no]', gender = '$r[gender]', ktp_address = '$r[ktp_address]', ktp_prov = '$r[ktp_prov]', ktp_city = '$r[ktp_city]', dom_address = '$r[dom_address]', dom_prov = '$r[dom_prov]', dom_city = '$r[dom_city]', cell_no = '$r[cell_no]', phone_no = '$r[phone_no]', religion = '$r[religion]', cat = '$inp[cat]', status = '535', join_date = '$inp[start_date]', leave_date = '$inp[end_date]', uni_cloth = '$r[uni_cloth]', uni_pant = '$r[uni_pant]', uni_shoe = '$r[uni_shoe]', marital = '$r[marital]', email = '$r[email]', npwp_no = '$r[npwp_no]', npwp_date = '$r[npwp_date]', bpjs_no = '$r[bpjs_no]', bpjs_date = '$r[bpjs_date]', blood_type = '$r[blood_type]', blood_resus = '$r[blood_resus]', create_by = '$r[create_by]', create_date = '$r[create_date]'";
		db($sql);

		//--------------------- INSERT DATA POSISI -----------------------//
		$idPhist = getField("select id from emp_phist order by id desc limit 1") + 1;
		$sql = "insert into emp_phist set id = '$idPhist', parent_id = '$id', pos_name = '$inp[pos_name]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', status = '1', remark = '$inp[remark]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', group_id = '$inp[group_id]', proses_id = '$inp[proses_id]', kategori = '$inp[kategori]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
		db($sql);

		//--------------------- INSERT DATA PLACEMENT -----------------------//
		$sql = "update rec_applicant_placement set cat = '$inp[cat]', pos_name = '$inp[pos_name]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', remark = '$inp[remark]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', group_id = '$inp[group_id]', proses_id = '$inp[proses_id]', company_id = '$inp[company_id]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$par[id]' ";
		db($sql);

		$sql = "update rec_applicant set emp_id = '$id' where id = '$par[id]'";
		db($sql);
	}

	echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function form()
{
	global $s, $par, $arrTitle, $arrParameter, $ui;

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
	$arrPosisi = arrayQuery("select t1.id, t2.subject from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi");

	$sql_ = "select name, id_posisi, birth_date, birth_place, religion, id_pelamar from rec_applicant where id='$par[id]'";
	$res_ = db($sql_);
	$r_ = mysql_fetch_array($res_);

	$sql = "select * from rec_applicant_placement where parent_id = '$par[id]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	//QUERY COMBO DATA
	$queryRank = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S09' order by namaData";
	$queryCat = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S04' order by urutanData";
	$queryGrade = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S10' and kodeInduk = '$r[rank]' order by namaData";
	$queryLocation = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S06' order by namaData";
	$queryDir = "SELECT kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X03' order by kodeInduk, urutanData";
	$queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' and t1.kodeInduk = '$r[dir_id]' order by t1.urutanData";
	$queryDept = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1	JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk where t3.kodeCategory='X03' and t1.kodeInduk = '$r[div_id]' order by t1.urutanData";
	$queryUnit = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1	JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X03' and t1.kodeInduk = '$r[dept_id]' order by t1.urutanData";
	$queryProv = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S02' order by namaData";
	$queryCity = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[prov_id]' order by namaData";
	$queryEmp = "SELECT id, concat(reg_no , ' - ', name) description from emp WHERE status=535 order by name";
	$queryJenis = "SELECT idJenis id, namaJenis description from pay_jenis where statusJenis='t' order by namaJenis";
	$queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
	$queryShift = "SELECT idShift id, namaShift description from dta_shift where statusShift = 't' order by namaShift";
	$queryCompany = "SELECT kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='" . $arrParameter[47] . "' order by kodeInduk,urutanData";
	$queryKategori = "SELECT kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='KT' order by kodeInduk,urutanData";

	$r[pos_name] = empty($r[pos_name]) ? $arrPosisi[$r_[id_posisi]] : $r[pos_name];

	setValidation("is_null", "inp[pos_name]", "anda harus mengisi Posisi");
	setValidation("is_null", "inp[rank]", "anda harus mengisi Jabatan");
	setValidation("is_null", "inp[location]", "anda harus mengisi Lokasi Kerja");
	setValidation("is_null", "inp[start_date]", "anda harus mengisi Mulai");
	setValidation("is_null", "inp[dir_id]", "anda harus mengisi Top Management Level 1 ");
	setValidation("is_null", "inp[cat]", "anda harus mengisi Status Pegawai");
	echo getValidation();
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread(ucwords($par[mode] . " data")) ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<p style="position:absolute;top:5px;right:5px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
			</p>
			<fieldset>
				<table style="width:100%">
					<tr>
						<td style="width:50%">
							<p><?= $ui->createSpan("Nama Pegawai", $r_[name]) ?></p>
							<p><?= $ui->createSpan("Posisi", $arrPosisi[$r_[id_posisi]]) ?></p>
							<p><?= $ui->createSpan("Tanggal Lahir", getTanggal($r_[birth_date])) ?></p>
						</td>
						<td style="width:50%">
							<p><?= $ui->createSpan("ID Pelamar", $r_[id_pelamar]) ?></p>
							<p><?= $ui->createSpan("Agama", $arrMaster[$r_[religion]]) ?></p>
							<p><?= $ui->createSpan("Tempat Lahir", $arrMaster[$r_[birth_place]]) ?></p>
						</td>
					</tr>
				</table>
			</fieldset>
			<br clear="all" />

			<fieldset>
				<legend> DATA JABATAN </legend>
				<table style="width:100%">
					<tr>
						<td style="width:50%">
							<p><?= $ui->createField("Posisi", "inp[pos_name]", $r[pos_name], "t", "t") ?></p>
							<p><?= $ui->createComboData("Jabatan", $queryRank, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "t", "t") ?></p>
							<p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no], "", "t") ?></p>
							<p><?= $ui->createField("Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "t", "", "", "", "", "t") ?></p>
							<p><?= $ui->createField("Keterangan", "inp[remark]", $r[remark], "", "t") ?></p>
						</td>
						<td style="width:50%">
							<p><?= $ui->createComboData("Status Pegawai", $queryCat, "id", "description", "inp[cat]", $r[cat], "", "", "t", "t") ?></p>
							<p><?= $ui->createComboData("Grade", $queryGrade, "id", "description", "inp[grade]", $r[grade], "", "", "t") ?></p>
							<p><?= $ui->createField("Tanggal SK", "inp[sk_date]", getTanggal($r[sk_date]), "", "", "", "", "", "", "t") ?></p>
							<p><?= $ui->createField("Selesai", "inp[end_date]", getTanggal($r[end_date]), "", "", "", "", "", "", "t") ?></p>
							<p><?= $ui->createComboData("Lokasi Kerja", $queryLocation, "id", "description", "inp[location]", $r[location], "", "", "t", "t") ?></p>
						</td>
					</tr>
				</table>
			</fieldset>

			<ul class="editornav">
				<li class="current"><a href="#organisasi">ORGANISASI</a></li>
				<li><a href="#lokasi">LOKASI</a></li>
				<li><a href="#struktur">STRUKTUR</a></li>
				<li><a href="#setting">SETTING</a></li>
			</ul>

			<div id="organisasi" class="subcontent1">
				<br>
				<p><?= $ui->createComboData("Directorate", $queryDir, "id", "description", "inp[dir_id]", $r[dir_id], "onchange=\"getSub('dir_id', 'div_id', '" . getPar($par, "mode") . "', 'divisi')\"", "", "t", "t") ?></p>
				<p><?= $ui->createComboData("Divisi", $queryDiv, "id", "description", "inp[div_id]", $r[div_id], "onchange=\"getSub('div_id', 'dept_id', '" . getPar($par, "mode") . "', 'departemen')\"", "", "t", "t") ?></p>
				<p><?= $ui->createComboData("Departemen", $queryDept, "id", "description", "inp[dept_id]", $r[dept_id], "onchange=\"getSub('dept_id', 'unit_id', '" . getPar($par, "mode") . "')\", 'unit'", "", "t") ?></p>
				<p><?= $ui->createComboData("Unit", $queryUnit, "id", "description", "inp[unit_id]", $r[unit_id], "", "", "t") ?></p>
			</div>

			<div id="lokasi" class="subcontent1" style="display:none;">
				<br>
				<p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[prov_id]", $r[prov_id], "onchange=\"getSub('prov_id', 'city_id', '" . getPar($par, "mode") . "')\"", "", "t") ?></p>
				<p><?= $ui->createComboData("Kota", $queryCity, "id", "description", "inp[city_id]", $r[city_id], "", "", "t") ?></p>
			</div>

			<div id="struktur" class="subcontent1" style="display:none;">
				<br>
				<p><?= $ui->createComboData("Atasan", $queryEmp, "id", "description", "inp[leader_id]", $r[leader_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Tata Usaha", $queryEmp, "id", "description", "inp[administration_id]", $r[administration_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Pengganti 1", $queryEmp, "id", "description", "inp[replacement_id]", $r[replacement_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Pengganti 2", $queryEmp, "id", "description", "inp[replacement2_id]", $r[replacement2_id], "", "", "t") ?></p>
			</div>

			<div id="setting" class="subcontent1" style="display:none;">
				<br>
				<p><?= $ui->createRadio("Hak Lembur", "inp[lembur]", array("t" => "Ya", "f" => "Tidak"), $r[lembur]) ?></p>
				<p><?= $ui->createComboData("Jenis Payroll", $queryJenis, "id", "description", "inp[payroll_id]", $r[payroll_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Location Process", $queryLocation, "id", "description", "inp[group_id]", $r[group_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Group Process", $queryGroup, "id", "description", "inp[proses_id]", $r[proses_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Shift Kerja", $queryShift, "id", "description", "inp[shift_id]", $r[shift_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Perusahaan", $queryCompany, "id", "description", "inp[company_id]", $r[company_id], "", "", "t") ?></p>
				<p><?= $ui->createComboData("Kategori", $queryKategori, "id", "description", "inp[kategori]", $r[kategori], "", "", "t") ?></p>
			</div>
		</form>
	</div>
	</fieldset>
	</div>
	<style>
		.chosen-container {
			min-width: 250px;
		}

		#inp_dir_id__chosen {
			min-width: 350px;
		}

		#inp_div_id__chosen {
			min-width: 350px;
		}

		#inp_dept_id__chosen {
			min-width: 350px;
		}

		#inp_unit_id__chosen {
			min-width: 350px;
		}

		#inp_prov_id__chosen {
			min-width: 350px;
		}

		#inp_city_id__chosen {
			min-width: 350px;
		}

		#inp_manager_id__chosen {
			min-width: 350px;
		}

		#inp_leader_id__chosen {
			min-width: 350px;
		}

		#inp_administration_id__chosen {
			min-width: 350px;
		}

		#inp_replacement_id__chosen {
			min-width: 350px;
		}

		#inp_replacement2_id__chosen {
			min-width: 350px;
		}

		#inp_payroll_id__chosen {
			min-width: 350px;
		}

		#inp_kategori__chosen {
			min-width: 350px;
		}

		#inp_group_id__chosen {
			min-width: 350px;
		}

		#inp_proses_id__chosen {
			min-width: 350px;
		}

		#inp_shift_id__chosen {
			min-width: 350px;
		}

		#inp_company_id__chosen {
			min-width: 350px;
		}
	</style>
<?php
}

function lihat()
{
	global $s, $par, $arrTitle;

	$par[tahunData] = empty($par[tahunData]) ? date("Y") : $par[tahunData];

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN('R11', 'R13')");
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread() ?>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" action="" method="post" class="stdform">
			<p style="position:absolute;top:5px;right:15px"><?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"") ?></p>
			<div id="pos_l" style="float:left;">
				<p>
					<input type="text" name="par[filterData]" placeholder="Search.." value="<?= $par[filterData] ?>" style="width:200px;" />
					<?= comboData("SELECT t1.id id, concat(t1.subject, ' - ', t2.subject) description from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi order by t1.subject", "id", "description", "par[idRencana]", "All", $par[idRencana], "onchange=\"document.getElementById('form').submit();\"", "310px;", "chosen-select") ?>
					<input type="submit" value="GO" class="btn btn_search btn-small" />
				</p>
			</div>
			<div id="pos_r" style="float:right;">
				<a href="?par[mode]=xls<?= getPar($par, "mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
			</div>
		</form>
		<br clear="all" />
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th width="*">NAMA</th>
					<th width="80">ID Pelamar</th>
					<th width="80">Umur</th>
					<th width="200">Posisi</th>
					<th width="100">Pendidikan</th>
					<th width="200">Jurusan</th>
					<th width="100">Control</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$filter = "where year(t1.tgl_input) = '$par[tahunData]'";
					if (!empty($par[filterData]))
						$filter .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($par[filterData])) . "%'";
					if (!empty($par[idRencana]))
						$filter .= " and t1.id_rencana = '" . $par[idRencana] . "'";
					$sql = "SELECT t1.id, t1.name, t1.id_pelamar, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT CONCAT(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 where x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) dataPendidikan, t1.emp_id, t2.subject FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi join rec_applicant_placement t3 on (t1.id = t3.parent_id) $filter";
					$res = db($sql);
					$no = 0;
					while ($r = mysql_fetch_assoc($res)) {
						list($r[eduType], $r[eduDept]) = explode("\t", $r[dataPendidikan]);
						$no++;
						$tahapan = empty($r[emp_id]) ? "<img src=\"styles/images/f.png\">" : "<img src=\"styles/images/t.png\">";
						?>
					<tr>
						<td><?= $no ?>.</td>
						<td><?= $r[name] ?></td>
						<td align="center"><?= $r[id_pelamar] ?></td>
						<td align="right"><?= $r[umur] ?></td>
						<td><?= $r[emp_id] ?></td>
						<td><?= $arrMaster[$r[eduType]] ?></td>
						<td><?= $arrMaster[$r[eduDept]] ?></td>
						<td align="center"><a href="?par[mode]=edit&par[id]=<?= $r[id] . getPar($par, "mode, id") ?>"><?= $tahapan ?></a></td>
					</tr>
				<?php
					}
					?>
			</tbody>
		</table>
	</div>
	<script>
		function showFilter() {
			jQuery('#form_filter').show('slow');
			jQuery('#sFilter').hide();
			jQuery('#hFilter').show();
		}

		function hideFilter() {
			jQuery('#form_filter').hide('slow');
			jQuery('#sFilter').show();
			jQuery('#hFilter').hide();
		}
	</script>
	<?php
		if ($par[mode] == "xls") {
			xls();
			echo "<iframe src=\"download.php?d=exp&f=exp-PENETAPAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}
		?>
<?php
}

function xls()
{
	global $fExport, $par;

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");

	$direktori = $fExport;
	$namaFile = "exp-PENETAPAN.xls";
	$judul = "PENETAPAN";

	$field = array("no", "nama", "id pelamar", "umur", "posisi", "pendidikan", "jurusan", "tahapan");

	$filter = "where year(t1.tgl_input) = '$par[tahunData]'";
	if (!empty($par[filterData]))
		$filter .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($par[filterData])) . "%'";
	if (!empty($par[idRencana]))
		$filter .= " and t1.id_rencana = '" . $par[idRencana] . "'";

	$sql = "SELECT t1.id, t1.name, t1.id_pelamar, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, (SELECT CONCAT(x1.edu_type, '\t', x1.edu_dept) FROM rec_applicant_edu x1 where x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) dataPendidikan, t1.emp_id, t2.subject FROM rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi join rec_selection_appl t3 on (t1.id = t3.parent_id AND t3.phase_id = '607' AND t3.sel_status = '601') $filter";
	$res = db($sql);
	$no = 0;
	while ($r = mysql_fetch_assoc($res)) {
		list($r[eduType], $r[eduDept]) = explode("\t", $r[dataPendidikan]);
		$no++;
		$tahapan = empty($r[emp_id]) ? "Tidak Lulus" : "Lulus";
		$data[] = array(
			$no . "\t center",
			$r[name] . "\t left",
			$r[id_pelamar] . "\t center",
			$r[umur] . "\t left",
			$r[subject] . "\t left",
			$arrMaster[$r[eduType]] . "\t left",
			$arrMaster[$r[eduDept]] . "\t left",
			$tahapan . "\t left"
		);
	}
	exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par)
{
	global $s, $_submit, $menuAccess;
	switch ($par[mode]) {
		case "subData":
			$text = subData();
			break;
		case "divisi":
			$text = divisi();
			break;
		case "departemen":
			$text = departemen();
			break;
		case "unit":
			$text = unit();
			break;
		case "edit":
			if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
			else $text = lihat();
			break;
		default:
			$text = lihat();
			break;
	}
	return $text;
}
?>
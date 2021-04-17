<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fKTP = "files/emp/ktp/";
$fPIC = "files/emp/pic/";
$fExport = "files/export/";
$fFile = "files/export/";
$fLog = "files/logPegawai.log";

function setFile()
{
    global $par, $fFile, $fLog, $cID;
    if (!in_array(strtolower(substr($_FILES[fileData][name], -3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name], -4)), array("xlsx"))) {
        return "file harus dalam format .xls atau .xlsx";
    } else {
        $fileUpload = $_FILES[fileData][tmp_name];
        $fileUpload_name = $_FILES[fileData][name];
        echo $par[fileData];
        if (($fileUpload != "") and ($fileUpload != "none")) {
            fileUpload($fileUpload, $fileUpload_name, $fFile);
            $fileData = md5($cID . "-" . date("Y-m-d H:i:s")) . "." . getExtension($fileUpload_name);
            fileRename($fFile, $fileUpload_name, $fileData);

            if (file_exists($fLog)) unlink($fLog);
            $fileName = fopen($fLog, "a+");
            fwrite($fileName, "START : " . date("Y-m-d H:i:s") . "\r\n\r\n");
            fclose($fileName);

            return "fileData" . $fileData;
        }
    }
}

function setData()
{
    global $par, $fFile, $fLog;

    $inputFileName = $fFile . $par[fileData];
    require_once('plugins/PHPExcel/IOFactory.php');

    try {
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
        die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    }

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();

    $arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory NOT IN('S06', 'S15', 'CB', 'DV')");
    $arrDivisi = arrayQuery("SELECT lower(trim(t1.namaData)), t1.kodeData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' order by t1.urutanData");
    $arrDepartemen = arrayQuery("SELECT lower(trim(t1.namaData)), t1.kodeData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk where t3.kodeCategory='X03' order by t1.urutanData");
    $arrLokasi = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory = 'S06'");

    $result = $par[rowData] . ". ";
    $getExcel = $objPHPExcel->getActiveSheet()->getCell('B5')->getValue();
    if ($getExcel == "NAMA") {
        if ($par[rowData] <= $highestRow) {
            $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':AV' . $par[rowData], NULL, TRUE, TRUE);
            $dta = $rowData[0];
            $tRow = 6;

            if (!in_array(trim(strtolower($dta[1])), array("", "NAMA"))) {
                $name = $dta[1];
                $kode = $dta[2];
                $birth_date = implode('-', array_reverse(explode('/', $dta[3])));
                $gender = $dta[5] == "P" ? "F" : "M";
                $marital = substr($dta[6],0,1) == "K" ? 4474 : 4475;
                $ptkp = $arrMaster[trim(strtolower($dta[6]))];
                $id_pendidikan = $arrMaster[trim(strtolower($dta[7]))];
                $religion = $arrMaster[trim(strtolower($dta[8]))];
                $reg_no = $dta[9];
                $ktp_prov = $arrMaster[trim(strtolower($dta[10]))];
                $ktp_city = $arrMaster[trim(strtolower($dta[11]))];
                $bpjs_no_ks = $dta[12];
                $bpjs_no = $dta[13];
                $leader_id = $arrPegawai[trim(strtolower($dta[14]))];
                $administration_id = $arrPegawai[trim(strtolower($dta[15]))];
                $payroll_id = $arrGaji[trim(strtolower($dta[16]))];
                $group_id = $arrMaster[trim(strtolower($dta[17]))];
                $proses_id = $arrMaster[trim(strtolower($dta[18]))];
                $shift_id = $arrShift[trim(strtolower($dta[19]))];
                $grade = $arrMaster[trim(strtolower($dta[20]))];
                $job_group = $arrMaster[trim(strtolower($dta[21]))];
                $job_group_date = implode('-', array_reverse(explode('/', $dta[22])));
                $rank = $arrMaster[trim(strtolower($dta[23]))];
                $rank_date = implode('-', array_reverse(explode('/', $dta[24])));
                $skala = $arrMaster[trim(strtolower($dta[25]))];
                $pos_name = $dta[27];
                $pos_name_date = implode('-', array_reverse(explode('/', $dta[28])));
                $cat = $arrMaster[trim(strtolower($dta[31]))];
                $cat_date = implode('-', array_reverse(explode('/', $dta[32])));
                $join_date = implode('-', array_reverse(explode('/', $dta[33])));
                $tmt_masa_kerja = implode('-', array_reverse(explode('/', $dta[34])));
                $tmt_berhenti = implode('-', array_reverse(explode('/', $dta[35])));
                $dir_id = substr($dta[38], 0, 1) == "C" ? 3509 : 4091;
                $div_id = $dir_id == 3509 ? $arrDivisi[trim(strtolower($dta[38]))] : $arrDivisi[trim(strtolower($dta[40]))];
                $dept_id = $dir_id == 3509 ? $arrDepartemen[trim(strtolower($dta[40]))] : $arrDepartemen[trim(strtolower($dta[43]))];
                $unit_id =  getField("SELECT t1.kodeData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X03' AND t1.kodeInduk = '".$dept_id."' AND lower(trim(t1.namaData)) = '".strtolower(trim($dta[43]))."' order by t1.urutanData");
                $location = $arrLokasi[trim(strtolower($dta[47]))];

                $cekPegawai = getField("select id from emp where reg_no = '" . $dta[9] . "' AND name = '".$dta[1]."'");
                $fileName = fopen($fLog, "a+");

                if (!empty($cekPegawai)) {
                    $sql = "update emp set reg_no = '$reg_no', kode = '$kode', name = '$name', birth_date = '$birth_date', gender = '$gender', marital = '$marital', ptkp = '$ptkp', id_pendidikan = '$id_pendidikan', religion = '$religion', ktp_prov = '$ktp_prov', ktp_city = '$ktp_city', bpjs_no_ks = '$bpjs_no_ks', bpjs_no = '$bpjs_no', cat = '$cat', cat_date = '$cat_date', join_date = '$join_date', tmt_masa_kerja = '$tmt_masa_kerja', tmt_berhenti = '$tmt_berhenti', update_by = 'migrasi', update_date = '" . date('Y-m-d H:i:s') . "' where id = '$cekPegawai'";
                    db($sql);
//                    $result.=$sql;

                    $sql = "update emp_phist set leader_id = '$leader_id', administration_id = '$administration_id', payroll_id = '$payroll_id', proses_id = '$proses_id', group_id = '$group_id', shift_id = '$shift_id', grade = '$grade', job_group = '$job_group', job_group_date = '$job_group_date', rank = '$rank', rank_date = '$rank_date', skala = '$skala', pos_name = '$pos_name', pos_name_date = '$pos_name_date', dir_id = '$dir_id', div_id = '$div_id', dept_id = '$dept_id', unit_id = '$unit_id', location = '$location', status = '1', update_by = 'migrasi', update_date = '" . date('Y-m-d H:i:s') . "' where parent_id = '$cekPegawai' and status = '1'";
                    db($sql);
//                    $result.=$sql;
                    fwrite($fileName, "OK : " . $dta[3] . "\t" . $dta[1] . "\r\n");
                } else {
                    $id = getLastId("emp", "id");
                    $sql = "INSERT IGNORE into emp set id = '$id', reg_no = '$reg_no', kode = '$kode', name = '$name', birth_date = '$birth_date', gender = '$gender', marital = '$marital', ptkp = '$ptkp', id_pendidikan = '$id_pendidikan', religion = '$religion', ktp_prov = '$ktp_prov', ktp_city = '$ktp_city', bpjs_no_ks = '$bpjs_no_ks', bpjs_no = '$bpjs_no', cat = '$cat', cat_date = '$cat_date', join_date = '$join_date', tmt_masa_kerja = '$tmt_masa_kerja', tmt_berhenti = '$tmt_berhenti', status = '535', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";

                    db($sql);

                    $idPhist = getField("SELECT id from emp_phist order by id desc limit 1") + 1;
                    $sql = "INSERT IGNORE into emp_phist set id = '$idPhist', parent_id = '$id', leader_id = '$leader_id', administration_id = '$administration_id', payroll_id = '$payroll_id', proses_id = '$proses_id', group_id = '$group_id', shift_id = '$shift_id', grade = '$grade', job_group = '$job_group', job_group_date = '$job_group_date', rank = '$rank', rank_date = '$rank_date', skala = '$skala', pos_name = '$pos_name', pos_name_date = '$pos_name_date', dir_id = '$dir_id', div_id = '$div_id', dept_id = '$dept_id', unit_id = '$unit_id', location = '$location', status = '1', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";

                    db($sql);
                    fwrite($fileName, "OK : " . $dta[3] . "\t" . $dta[1] . "\r\n");
                }


                fclose($fileName);
                sleep(1);

                $tRow++;
            }

            $rowData = $par[rowData] - 5;
            $highestRow = $highestRow - 5;
            $progresData = getAngka($rowData / $highestRow * 100);

            return $progresData . "\t(" . $progresData . "%) " . getAngka($rowData) . " of " . getAngka($highestRow) . "\t" . $result;
        }
    }
}

function endProses()
{
    global $s, $inp, $par, $fLog, $cID;

    $fileName = fopen($fLog, "a+");
    fwrite($fileName, "\r\nEND : " . date("Y-m-d H:i:s"));
    fclose($fileName);
    sleep(1);

    return "import data selesai : " . getTanggal(date('Y-m-d'), "t") . ", " . date('H:i');
}

function formUpload()
{
    global $s, $par, $arrTitle;
    $text .= "
	<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
			" . getBread(ucwords("import data")) . "
			<span class=\"pagedesc\">&nbsp;</span>                 
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form class=\"stdform\" enctype=\"multipart/form-data\">   
				<div id=\"formInput\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">
							<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:290px;\" maxlength=\"100\" />
							<div class=\"fakeupload\">
								<input type=\"file\" id=\"fileData\" name=\"fileData\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>
						</div>
					</p>                 
					<p>
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=tmpPegawai\" class=\"detil\">* download template.xls</a></div>                
					</p>
				</div>
				<div id=\"prosesImg\" align=\"center\" style=\"display:none; position:absolute; left:50%; top:50%;\">                      
					<img src=\"styles/images/loaders/loader6.gif\">
				</div>
				<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">                     
					<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
					<div class=\"bar2\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%; height:20px;\"></div></div>
				</div>                 
				<span id=\"progresRes\"></span>
				<div id=\"progresEnd\" class=\"progress\" style=\"margin-top:30px; display:none;\">                
					<a href=\"download.php?d=logPegawai\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>               
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?" . getPar($par, "mode") . "';\"/>
				</div>
				<br clear=\"all\">
				<p style=\"position: absolute; right: 20px; top: 10px;\">
					<input type=\"button\" class=\"btnSubmit radius2\" name=\"btnSimpan\" value=\"Upload\" onclick=\"setProses('" . getPar($par, "mode") . "');\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"closeBox();\"/>
				</p>
			</form>
		</div>
	</div>";

    return $text;
}


function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]' order by namaData");

    return implode("\n", $data);
}

function uploadKTP($id)
{
    global $fKTP;

    $fileUpload = $_FILES["ktpFilename"]["tmp_name"];
    $fileUpload_name = $_FILES["ktpFilename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fKTP);
        $ktp_filename = "ktp-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fKTP, $fileUpload_name, $ktp_filename);
    }

    if (empty($ktp_filename)) $ktp_filename = getField("select ktp_filename from emp where id='$id'");

    return $ktp_filename;
}

function uploadPIC($id)
{
    global $fPIC;

    $fileUpload = $_FILES["picFilename"]["tmp_name"];
    $fileUpload_name = $_FILES["picFilename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fPIC);
        $pic_filename = "doc-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fPIC, $fileUpload_name, $pic_filename);
    }
    if (empty($pic_filename)) $pic_filename = getField("select pic_filename from emp where id='$id'");

    return $pic_filename;
}

function tambah()
{
    global $par, $cUser, $inp;

    $id = getLastId("emp", "id");
    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[join_date] = setTanggal($inp[join_date]);
    $inp[leave_date] = setTanggal($inp[leave_date]);
    $inp[bpjs_date] = setTanggal($inp[bpjs_date]);
    $inp[bpjs_date_ks] = setTanggal($inp[bpjs_date_ks]);
    $inp[npwp_date] = setTanggal($inp[npwp_date]);
    $inp[cat_date] = setTanggal($inp[cat_date]);
    $inp[tmt_masa_kerja] = setTanggal($inp[tmt_masa_kerja]);
    $inp[pic_filename] = uploadPIC($id);
    $inp[ktp_filename] = uploadKTP($id);
    $inp[create_date] = date('Y-m-d H:i:s');
    $inp[create_by] = $cUser;

    $sql = "INSERT INTO emp set id = '$id', cat_date = '$inp[cat_date]', tmt_masa_kerja = '$inp[tmt_masa_kerja]', cat = '$inp[cat]', name = '$inp[name]', alias = '$inp[alias]', reg_no = '$inp[reg_no]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', kk_no = '$inp[kk_no]', ktp_filename = '$inp[ktp_filename]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', phone_no = '$inp[phone_no]', cell_no = '$inp[cell_no]', email = '$inp[email]', marital = '$inp[marital]', ptkp = '$inp[ptkp]', religion = '$inp[religion]', pic_filename = '$inp[pic_filename]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', status = '$inp[status]', join_date = '$inp[join_date]', leave_date = '$inp[leave_date]', nation = '$inp[nation]', kode = '$inp[kode]', id_pendidikan = '$inp[id_pendidikan]', id_cabang = '$inp[id_cabang]', id_divisi = '$inp[id_divisi]', id_departemen = '$inp[id_departemen]', id_unit = '$inp[id_unit]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    $idPhist = getLastId("emp_phist", "id");
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[div_id_date] = setTanggal($inp[div_id_date]);
    $inp[pos_name_date] = setTanggal($inp[pos_name_date]);
    $inp[grade_date] = setTanggal($inp[grade_date]);
    $inp[rank_date] = setTanggal($inp[rank_date]);
    $inp[job_group_date] = setTanggal($inp[job_group_date]);


    $sql = "INSERT INTO emp_phist set id = '$idPhist', parent_id = '$id', rank_date = '$inp[rank_date]', job_group = '$inp[job_group]', job_group_date = '$inp[job_group_date]', pos_name = '$inp[pos_name]', pos_name_date = '$inp[pos_name_date]', grade_date = '$inp[grade_date]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id_date = '$inp[div_id_date]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', skala = '$inp[skala]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '1', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $par, $cUser, $inp;

    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[join_date] = setTanggal($inp[join_date]);
    $inp[leave_date] = setTanggal($inp[leave_date]);
    $inp[bpjs_date] = setTanggal($inp[bpjs_date]);
    $inp[bpjs_date_ks] = setTanggal($inp[bpjs_date_ks]);
    $inp[npwp_date] = setTanggal($inp[npwp_date]);
    $inp[cat_date] = setTanggal($inp[cat_date]);
    $inp[tmt_masa_kerja] = setTanggal($inp[tmt_masa_kerja]);
    $inp[pic_filename] = uploadPIC($par[id]);
    $inp[ktp_filename] = uploadKTP($par[id]);
    $inp[update_date] = date('Y-m-d H:i:s');
    $inp[update_by] = $cUser;

    $sql = "UPDATE emp set cat_date = '$inp[cat_date]', tmt_masa_kerja = '$inp[tmt_masa_kerja]', cat = '$inp[cat]', name = '$inp[name]', alias = '$inp[alias]', reg_no = '$inp[reg_no]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', kk_no = '$inp[kk_no]', ktp_filename = '$inp[ktp_filename]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', phone_no = '$inp[phone_no]', cell_no = '$inp[cell_no]', email = '$inp[email]', marital = '$inp[marital]', ptkp = '$inp[ptkp]', religion = '$inp[religion]', pic_filename = '$inp[pic_filename]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', status = '$inp[status]', join_date = '$inp[join_date]', leave_date = '$inp[leave_date]', nation = '$inp[nation]', kode = '$inp[kode]', id_pendidikan = '$inp[id_pendidikan]', id_cabang = '$inp[id_cabang]', id_divisi = '$inp[id_divisi]', id_departemen = '$inp[id_departemen]', id_unit = '$inp[id_unit]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[div_id_date] = setTanggal($inp[div_id_date]);
    $inp[pos_name_date] = setTanggal($inp[pos_name_date]);
    $inp[grade_date] = setTanggal($inp[grade_date]);
    $inp[rank_date] = setTanggal($inp[rank_date]);
    $inp[job_group_date] = setTanggal($inp[job_group_date]);

    $sql = "UPDATE emp_phist set rank_date = '$inp[rank_date]', job_group = '$inp[job_group]', job_group_date = '$inp[job_group_date]', pos_name_date = '$inp[pos_name_date]', grade_date = '$inp[grade_date]', pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id_date = '$inp[div_id_date]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', skala = '$inp[skala]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$par[id]' AND status = '1'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par;

    $sql = "DELETE FROM emp where id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_phist where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_asset where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_bank where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_char where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_contact where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_edu where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_family where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_file where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_health where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_punish where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_pwork where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_reward where parent_id = '$par[id]'";
    db($sql);
    $sql = "DELETE FROM emp_training where parent_id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode, id") . "'</script>";
}

function form()
{
    global $s, $arrTitle, $par, $ui, $arrParameter;

    $sql = "SELECT *, t1.status FROM emp t1 left join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') where t1.id = '$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    //QUERY COMBO DATA

    $queryBirthPlace = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData, t1.namaData";
    $queryProv = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S02' order by namaData";
    $queryCity = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S03' and kodeInduk = '$r[ktp_prov]' order by namaData";
    $queryCityDom = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S03' and kodeInduk = '$r[dom_prov]' order by namaData";
    $queryCityId = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S03' and kodeInduk = '$r[prov_id]' order by namaData";
    $queryNation = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S01' order by namaData";
    $queryReligion = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S07' order by namaData";
    $queryRank = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S09' order by namaData";
    $queryGrade = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S10' order by namaData";
    $queryLocation = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S06' order by namaData";
    $queryDir = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='X03' order by kodeInduk, urutanData";
    $queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' and t1.kodeInduk = '$r[dir_id]' order by t1.urutanData";
    $queryDept = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk where t3.kodeCategory='X03' and t1.kodeInduk = '$r[div_id]' order by t1.urutanData";
    $queryUnit = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X03' and t1.kodeInduk = '$r[dept_id]' order by t1.urutanData";
    $queryJenis = "SELECT idJenis id, namaJenis description from pay_jenis where statusJenis='t' order by namaJenis";
    $queryEmp = "SELECT id, name description from emp where status = '535'";
    $queryProcess = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[7] . "' order by urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryShift = "SELECT idShift id, namaShift description from dta_shift where statusShift = 't' order by namaShift";
    $queryKategori = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='KT' order by kodeInduk,urutanData";
    $queryCat = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S04' and statusData = 't' order by urutanData";
    $queryStatus = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S05' and statusData = 't' order by urutanData";
    $queryPTKP = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S08' order by namaData";
    $queryMarital = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S25' order by namaData";
    $queryCabang = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'CB' and statusData = 't' order by urutanData";
    $queryPendidikan = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R11' and statusData = 't' order by urutanData";
    $queryDivisi = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'DV' and statusData = 't' and kodeInduk = '$r[id_cabang]' order by urutanData";
    $querySkala = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'SG' and statusData = 't' order by urutanData";
    $queryJobGroup = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'JG' and statusData = 't' order by urutanData";

    setValidation("is_null", "inp[name]", "Nama Lengkap pada tab Data Pegawai tidak boleh kosong ..");
    setValidation("is_null", "inp[birth_date]", "Tanggal Lahir pada tab Data Pegawai tidak boleh kosong ..");
    setValidation("is_null", "inp[birth_place]", "Tempat Lahir pada tab Data Pegawai tidak boleh kosong ..");
    setValidation("is_null", "inp[reg_no]", "NPP pada tab Data Pegawai tidak boleh kosong ..");
    setValidation("is_null", "inp[ktp_no]", "No. KTP pada tab Data Pegawai tidak boleh kosong ..");
    setValidation("is_null", "inp[pos_name]", "Posisi pada tab Posisi tidak boleh kosong ..");
    setValidation("is_null", "inp[sk_date]", "Tanggal SK pada tab Posisi tidak boleh kosong ..");
    setValidation("is_null", "inp[start_date]", "Tanggal Mulai pada tab Posisi tidak boleh kosong ..");
    setValidation("is_null", "inp[location]", "Lokasi Kerja pada tab Posisi tidak boleh kosong ..");
    setValidation("is_null", "inp[dir_id]", "Cabang pada tab Posisi tidak boleh kosong ..");
    setValidation("is_null", "inp[cat]", "Status Pegawai pada tab Status tidak boleh kosong ..");
    setValidation("is_null", "inp[status]", "Status Aktif pada tab Status tidak boleh kosong ..");
    setValidation("is_null", "inp[join_date]", "Mulai Bekerja pada tab Status tidak boleh kosong ..");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return cekStatus(); " enctype="multipart/form-data">
            <p style="position:absolute;top:5px;right:5px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>" />
            </p>
            <br clear="all" />
            <ul class="hornav">
                <li class="current"><a href="#data">Data Pegawai</a></li>
                <li><a href="#posisi">Posisi</a></li>
                <li><a href="#status">Status</a></li>
                <li><a href="#photo">Foto</a></li>
            </ul>
            <!-- DATA PEGAWAI -->
            <div id="data" class="subcontent">
                <fieldset>
                    <legend> IDENTITAS </legend>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createField("Nama Lengkap", "inp[name]", $r[name], "t", "t", "style=\"text-transform: uppercase;\"") ?></p>
                                <p><?= $ui->createField("Panggilan", "inp[alias]", $r[alias], "", "t") ?></p>
                                <p><?= $ui->createComboData("Tempat Lahir", $queryBirthPlace, "id", "description", "inp[birth_place]", $r[birth_place], "", "", "t", "t", "t") ?></p>
                                <p><?= $ui->createDatePicker("Tanggal Lahir", "inp[birth_date]", $r[birth_date], "t", "t") ?></p>
                                <p><?= $ui->createTextArea("Alamat KTP", "inp[ktp_address]", $r[ktp_address], "", "t") ?></p>
                                <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[ktp_prov]", $r[ktp_prov], "onchange=\"getSub('ktp_prov', 'ktp_city', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData("Kota", $queryCity, "id", "description", "inp[ktp_city]", $r[ktp_city], "", "", "t", "", "t") ?></p>
                                <p><?= $ui->createField("Telp. Rumah", "inp[phone_no]", $r[phone_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                                <p><?= $ui->createComboData("Asal Negara", $queryNation, "id", "description", "inp[nation]", $r[nation], "", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData("Pend. Terakhir", $queryPendidikan, "id", "description", "inp[id_pendidikan]", $r[id_pendidikan], "", "", "t", "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                <p><?= $ui->createField("Kode", "inp[kode]", $r[kode], "", "t") ?></p>
                                <p><?= $ui->createField("NPP", "inp[reg_no]", $r[reg_no], "t", "t") ?></p>
                                <p><?= $ui->createField("No. KTP", "inp[ktp_no]", $r[ktp_no], "t", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                                <p><?= $ui->createRadio("Jenis Kelamin", "inp[gender]", array("M" => "Laki-Laki", "F" => "Perempuan"), $r[gender], "t") ?></p>
                                <p><?= $ui->createComboData("Agama", $queryReligion, "id", "description", "inp[religion]", $r[religion], "", "", "t", "", "t") ?></p>
                                <p><?= $ui->createTextArea("Alamat Domisili", "inp[dom_address]", $r[dom_address], "", "t") ?></p>
                                <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[dom_prov]", $r[dom_prov], "onchange=\"getSub('dom_prov', 'dom_city', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData("Kota", $queryCityDom, "id", "description", "inp[dom_city]", $r[dom_city], "", "", "t", "", "t") ?></p>
                                <p><?= $ui->createField("Nomor HP", "inp[cell_no]", $r[cell_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                                <p><?= $ui->createField("Nomor KK", "inp[kk_no]", $r[kk_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend> TUPOKSI </legend>
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createComboData("Unit Kerja", $queryCabang, "id", "description", "inp[id_cabang]", $r[id_cabang], "onchange=\"getSub('id_cabang', 'id_divisi', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData("Divisi", $queryDivisi, "id", "description", "inp[id_divisi]", $r[id_divisi], "", "", "t", "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                <p><?= $ui->createField("Departemen", "inp[id_departemen]", $r[id_departemen], "", "t") ?></p>
                                <p><?= $ui->createField("Unit", "inp[id_unit]", $r[id_unit], "", "t") ?></p>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <!-- DATA POSISI -->

            <div id="posisi" class="subcontent" style="display:none">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Jabatan", "inp[pos_name]", $r[pos_name], "t", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryRank, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Skala Gaji", $querySkala, "id", "description", "inp[skala]", $r[skala], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no], "", "t") ?></p>
                            <p><?= $ui->createDatePicker("Tanggal Mulai", "inp[start_date]", $r[start_date], "t", "t") ?></p>
                            <p><?= $ui->createField("Keterangan", "inp[remark]", $r[remark], "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p>&nbsp;</p>
                            <p><?= $ui->createComboData("Grade", $queryGrade, "id", "description", "inp[grade]", $r[grade], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Job Group", $queryJobGroup, "id", "description", "inp[job_group]", $r[job_group], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createDatePicker("Tanggal SK", "inp[sk_date]", $r[sk_date], "t", "t") ?></p>
                            <p><?= $ui->createDatePicker("Tanggal Selesai", "inp[end_date]", $r[end_date], "", "t") ?></p>
                            <p><?= $ui->createComboData("Lokasi Kerja", $queryLocation, "id", "description", "inp[location]", $r[location], "", "", "t", "t", "t") ?></p>
                        </td>
                    </tr>
                </table>

                <fieldset>
                    <legend>TMT</legend>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 50%">
                                <p><?= $ui->createDatePicker("Jabatan", "inp[pos_name_date]", $r[pos_name_date],"","t") ?></p>
                                <p><?= $ui->createDatePicker("Grade", "inp[grade_date]", $r[grade_date],"","t") ?></p>
                                <p><?= $ui->createDatePicker("Unit Kerja", "inp[div_id_date]", $r[div_id_date],"","t") ?></p>
                            </td>
                            <td style="width: 50%">
                                <p><?= $ui->createDatePicker("Pangkat", "inp[rank_date]", $r[rank_date],"","t") ?></p>
                                <p><?= $ui->createDatePicker("Job Group", "inp[job_group_date]", $r[job_group_date],"","t") ?></p>
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

                <!-- DATA ORGANISASI -->

                <div id="organisasi" class="subcontent1">
                    <table style="width:100%">
                        <tr>
                            <td style="width:50%">
                                <p><?= $ui->createComboData("Kantor", $queryDir, "id", "description", "inp[dir_id]", $r[dir_id], "onchange=\"getSub('dir_id', 'div_id', '" . getPar($par, "mode") . "')\"", "", "t", "t", "t") ?></p>
                                <p><?= $ui->createComboData($arrParameter[38], $queryDiv, "id", "description", "inp[div_id]", $r[div_id], "onchange=\"getSub('div_id', 'dept_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData($arrParameter[40], $queryDept, "id", "description", "inp[dept_id]", $r[dept_id], "onchange=\"getSub('dept_id', 'unit_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                                <p><?= $ui->createComboData($arrParameter[41], $queryUnit, "id", "description", "inp[unit_id]", $r[unit_id], "", "", "t", "", "t") ?></p>
                            </td>
                            <td style="width:50%">
                                &nbsp;
                            </td>
                        </tr>
                    </table>

                </div>

                <!-- DATA LOKASI -->

                <div id="lokasi" class="subcontent1" style="display:none">
                    <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[prov_id]", $r[prov_id], "onchange=\"getSub('prov_id', 'city_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData("Kota", $queryCityId, "id", "description", "inp[city_id]", $r[city_id], "", "", "t", "", "t") ?></p>
                </div>

                <!-- DATA STRUKTUR -->

                <div id="struktur" class="subcontent1" style="display:none">
                    <p><?= $ui->createComboData("Atasan", $queryEmp, "id", "description", "inp[leader_id]", $r[leader_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Tata Usaha", $queryEmp, "id", "description", "inp[administration_id]", $r[administration_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Pengganti 1", $queryEmp, "id", "description", "inp[replacement_id]", $r[replacement_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Pengganti 2", $queryEmp, "id", "description", "inp[replacement2_id]", $r[replacement2_id], "", "", "t") ?></p>
                </div>

                <!-- DATA SETTING -->

                <div id="setting" class="subcontent1" style="display:none">
                    <p><?= $ui->createRadio("Hak Lembur", "inp[lembur]", array("t" => "Ya", "f" => "Tidak"), $r[lembur]) ?></p>
                    <p><?= $ui->createComboData("Jenis Payroll", $queryJenis, "id", "description", "inp[payroll_id]", $r[payroll_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Location Process", $queryProcess, "id", "description", "inp[group_id]", $r[group_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Group Process", $queryGroup, "id", "description", "inp[proses_id]", $r[proses_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Shift Kerja", $queryShift, "id", "description", "inp[shift_id]", $r[shift_id], "", "", "t") ?></p>
                    <p><?= $ui->createComboData("Kategori", $queryKategori, "id", "description", "inp[kategori]", $r[kategori], "", "", "t") ?></p>
                </div>
            </div>

            <!-- DATA STATUS -->

            <div id="status" class="subcontent" style="display:none">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <div class="widgetbox">
                                <div class="title">
                                    <h3>STATUS PEGAWAI</h3>
                                </div>
                            </div>
                            <p><?= $ui->createComboData("Status Pegawai", $queryCat, "id", "description", "inp[cat]", $r[cat], "", "", "t", "t", "t") ?></p>
                            <p><?= $ui->createComboData("Status Aktif", $queryStatus, "id", "description", "inp[status]", $r[status], "", "", "t", "t", "t") ?></p>
                            <p><?= $ui->createDatePicker("Mulai Bekerja", "inp[join_date]", $r[join_date], "t", "t") ?></p>
                            <p><?= $ui->createDatePicker("Selesai Bekerja", "inp[leave_date]", $r[leave_date], "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <div class="widgetbox">
                                <div class="title">
                                    <h3>UKURAN SERAGAM</h3>
                                </div>
                            </div>
                            <p><?= $ui->createField("Baju", "inp[uni_cloth]", $r[uni_cloth], "", "t") ?></p>
                            <p><?= $ui->createField("Celana", "inp[uni_pant]", $r[uni_pant], "", "t") ?></p>
                            <p><?= $ui->createField("Sepatu", "inp[uni_shoe]", $r[uni_shoe], "", "t") ?></p>
                        </td>
                    </tr>
                </table>

                <fieldset>
                    <legend>TMT</legend>
                    <table style="width: 100%">
                        <tr>
                            <td style="width: 50%">
                                <p><?= $ui->createDatePicker("Status", "inp[cat_date]", $r[cat_date],"","t") ?></p>
                                <p><?= $ui->createDatePicker("Masa Kerja", "inp[tmt_masa_kerja]", $r[tmt_masa_kerja],"","t") ?></p>

                            </td>
                            <td style="width: 50%">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <div class="widgetbox">
                    <div class="title">
                        <h3>DETAIL INFO</h3>
                    </div>
                </div>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createComboData("Status PTKP", $queryPTKP, "id", "description", "inp[ptkp]", $r[ptkp], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Status Kawin", $queryMarital, "id", "description", "inp[marital]", $r[marital], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Email", "inp[email]", $r[email], "", "t") ?></p>
                            <p><?= $ui->createField("No. NPWP", "inp[npwp_no]", $r[npwp_no], "", "t") ?></p>
                            <p><?= $ui->createField("Tgl. NPWP", "inp[npwp_date]", getTanggal($r[npwp_date]), "", "t", "", "", "", "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createField("No. BPJS TK", "inp[bpjs_no]", $r[bpjs_no], "", "t") ?></p>
                            <p><?= $ui->createField("Tgl. BPJS TK", "inp[bpjs_date]", getTanggal($r[bpjs_date]), "", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("No. BPJS KS", "inp[bpjs_no_ks]", $r[bpjs_no_ks], "", "t") ?></p>
                            <p><?= $ui->createField("Tgl. BPJS KS", "inp[bpjs_date_ks]", getTanggal($r[bpjs_date_ks]), "", "t", "", "", "", "", "t") ?></p>
                            <p>
                                <label class="l-input-small2">Gol. Darah</label>
                            <div class="field">
                                <?= comboArray("inp[blood_type]", array("A", "B", "O", "AB"), $r[blood_type]) . " " . comboArray("inp[blood_resus]", array("+", "-"), $r[blood_resus]) ?>
                            </div>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- DATA FOTO -->

            <div id="photo" class="subcontent" style="display: none;margin-top: 0px;">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p>
                                <label class="l-input-small2">File KTP</label>
                            <div id="ktpPreview" <?php if ($par[mode] == "edit" && $r[ktp_filename] != "") { ?> style="background-image:  url(<?= APP_URL . "/files/emp/ktp/" . $r[ktp_filename] ?>)" <?php } ?>">
            </div>
            <br />
            <input id="ktpFilename" type="file" name="ktpFilename" class="img" style="padding-left: 240px;" />
            </p>
            </td>
            <td style="width:50%">
                <p>
                    <label class="l-input-small">Foto</label>
                <div id="fotoPreview" <?php if ($par[mode] == "edit" && $r[pic_filename] != "") { ?> style="background-image:  url(<?= APP_URL . "/files/emp/pic/" . $r[pic_filename] ?>)" <?php } ?>">
    </div>
    <br />
    <input id="picFilename" type="file" name="picFilename" class="img" style="padding-left: 240px;" />
    </p>
    </td>
    </tr>
    </table>
    </div>
    </form>
    </div>
    <style>
        #p0 {
            margin: 5px 0;
        }

        .chosen-container {
            min-width: 250px;
        }

        #ktpPreview {
            border: #069 solid 1px;
            padding-left: 160px;
            width: 180px;
            height: 180px;
            background-position: center center;
            background-size: cover;
            -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
            display: inline-block;
        }

        #fotoPreview {
            border: #069 solid 1px;
            width: 180px;
            height: 180px;
            background-position: center center;
            background-size: cover;
            -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
            display: inline-block;
        }

        fieldset {
            border: 2px solid #0A246A;
            border-radius: 8px;
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        legend {
            font-weight: bold;
            font-size: 1.2em;
            color: #0A246A;
            padding: 5px;
        }

        fieldset label {
            margin-left: 10px;
        }
    </style>
    <?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $arrParam;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S09', 'X03','X04', 'X05') and statusData = 't'");
    $par[filterUsia] = empty($par[filterUsia]) ? 57 : $par[filterUsia];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $rangeTahun = $par[tahunData] != date('Y') ? $par[tahunData] - date('Y') : 0;
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div style="position: absolute;top: 10px;right: 5px;">
                <?= comboKey("par[filterUsia]", array("57" => "MPP-57", "38" => "MPP-38"), $par[filterUsia], "onchange=\"document.getElementById('form').submit();\"") ?>
                <?= comboYear("par[tahunData]", $par[tahunData], "5", "onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="posr_r" style="float: right">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="*">NAMA</th>
                <th width="80">ID</th>
                <th width="150">Jabatan</th>
                <th width="150">Pangkat</th>
                <th width="150">Unit Kerja</th>
                <th width="80">Tgl. Lahir</th>
                <th width="100">Tgl. Keluar</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($par[filterUsia] == "57") {
                $filterPensiun = "AND $par[tahunData] - year(birth_date) = '57'";
            }else{
                $filterPensiun = "AND $par[tahunData] - year(birth_date) = '37'";
            }
            $filter = "WHERE t1.status ='535' $filterPensiun";
            if (!empty($par[filterData]))
                $filter .= " and (lower(name) LIKE '%$par[filterData]%' OR lower(reg_no) LIKE '%$par[filterData]%' OR lower(pos_name) LIKE '%$par[filterData]%')";
            $sql = "SELECT t1.id, t1.name, t1.reg_no, t1.birth_date, t1.join_date, t1.leave_date, t2.pos_name, t2.rank, t2.div_id, replace(
                        case when coalesce(leave_date,NULL) IS NULL THEN
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
                        when leave_date = '0000-00-00' THEN
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
                        ELSE
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
                        END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') as umurPegawai, DATE_ADD( birth_date, INTERVAL 58 YEAR ) as tahunPensiun from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $date1 = date('Y-m-d');
                $date2 = $r[tahunPensiun];
                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*58*60*24));
                $months = floor(($diff - $years * 365*58*60*24) / (30*58*60*24));
                $days = floor(($diff - $years * 365*58*60*24 - $months*30*58*60*24)/ (58*60*24));

                $r[sisaPensiun] = $years." tahun ". $months. " bulan ".$days." hari";
                $no++;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td align="center"><?= $r[reg_no] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[rank]] ?></td>
                    <td><?= $arrMaster[$r[div_id]] ?></td>
                    <td align="center"><?= getTanggal($r[birth_date]) ?></td>
                    <td align="center"><?= getTanggal($r[leave_date]) ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <style>
        .chosen-container {
            min-width: 200px;
        }
    </style>
    <?php
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    ?>
    <?php
}

function xls()
{

    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $arrParam, $fExport;

    $par[filterUsia] = empty($par[filterUsia]) ? 57 : $par[filterUsia];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $rangeTahun = $par[tahunData] != date('Y') ? $par[tahunData] - date('Y') : 0;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN('S09','X03', 'X04', 'X05') and statusData = 't'");
    $arrKode = arrayQuery("select kodeMaster, kodeData from mst_data where kodeCategory = 'S04' and statusData = 't'");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";


    $field = array("no", "nama", "ID", "jabatan", "pangkat","unit kerja", "tanggal lahir", "tanggal keluar");


    if($par[filterUsia] == "57") {
        $filterPensiun = "AND $par[tahunData] - year(birth_date) = '57'";
    }else{
        $filterPensiun = "AND $par[tahunData] - year(birth_date) = '37'";
    }
    $filter = "WHERE t1.status ='535' $filterPensiun";
    if (!empty($par[filterData]))
        $filter .= " and (lower(name) LIKE '%$par[filterData]%' OR lower(reg_no) LIKE '%$par[filterData]%' OR lower(pos_name) LIKE '%$par[filterData]%')";
    $sql = "SELECT t1.id, t1.name, t1.reg_no, t1.birth_date, t1.join_date, t1.leave_date, t2.pos_name, t2.rank, t2.div_id, replace(
            case when coalesce(leave_date,NULL) IS NULL THEN
            CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
            when leave_date = '0000-00-00' THEN
            CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
            ELSE
            CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
            END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') as umurPegawai, DATE_ADD( birth_date, INTERVAL 58 YEAR ) as tahunPensiun from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $date1 = date('Y-m-d');
        $date2 = $r[tahunPensiun];
        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365*58*60*24));
        $months = floor(($diff - $years * 365*58*60*24) / (30*58*60*24));
        $days = floor(($diff - $years * 365*58*60*24 - $months*30*58*60*24)/ (58*60*24));

        $r[sisaPensiun] = $years." tahun ". $months. " bulan ".$days." hari";
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[rank]] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            getTanggal($r[birth_date]) . "\t left",
            getTanggal($r[leave_date]) . "\t left"
        );

    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par)
{
    global $menuAccess, $s, $_submit, $m;
    switch ($par[mode]) {
        default:
            $text = lihat();
            break;
    }

    return $text;
}

?>
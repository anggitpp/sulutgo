<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fKTP = "files/recruit/ktp/";
$fPIC = "files/recruit/pic/";
$fEdu = "files/recruit/edu/";
$fWork = "files/recruit/pwork/";
$fTrain = "files/recruit/ptraining/";
$fFile = "files/recruit/file/";

function getNumber()
{
    $getlastNumber = getField("SELECT id_pelamar FROM rec_applicant where year(cre_date) = " . date('Y') . " order by id_pelamar DESC LIMIT 1");
    $str   = (empty($getlastNumber)) ? "0000" : substr($getlastNumber, 4, 8);

    $incNum = str_pad($str + 1, 4, "0", STR_PAD_LEFT);
    $year   = date("y");

    return "DP" . $year . $incNum;
}

function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]' order by namaData");

    return implode("\n", $data);
}

function setAdministrasi()
{
    global $par;

    $r[administrasi] = $par[administrasi] == "t" ? "f" : "t";
    $sql = "update rec_applicant set administrasi = '$r[administrasi]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode, id, administrasi") . "';</script>";
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
    if (empty($ktp_filename)) $ktp_filename = getField("select ktp_filename from rec_applicant where id='$id'");

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
    if (empty($pic_filename)) $pic_filename = getField("select pic_filename from rec_applicant where id='$id'");

    return $pic_filename;
}


function uploadPendidikan($id)
{
    global $fEdu;
    $fileUpload = $_FILES["edu_filename"]["tmp_name"];
    $fileUpload_name = $_FILES["edu_filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fEdu);
        $edu_filename = "recedu-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fEdu, $fileUpload_name, $edu_filename);
    }
    if (empty($edu_filename)) $edu_filename = getField("select edu_filename from rec_applicant_edu where id='$id'");

    return $edu_filename;
}


function uploadPengalaman($id)
{
    global $fWork;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fWork);
        $filename = "recwork-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fWork, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from rec_applicant_pwork where id='$id'");

    return $filename;
}

function uploadPelatihan($id)
{
    global $fTrain;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fTrain);
        $filename = "ptraining-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fTrain, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from rec_applicant_ptraining where id='$id'");

    return $filename;
}

function uploadFile($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $filename = "pfile-" . $id . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from rec_applicant_pfile where id='$id'");

    return $filename;
}

function tambah()
{
    global $inp, $par, $cUser;

    $id = getLastId("rec_applicant", "id");
    $inp[name] = strtoupper($inp[name]);
    $inp[alias] = strtoupper($inp[alias]);
    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[npwp_date] = setTanggal($inp[npwp_date]);
    $inp[bpjs_date] = setTanggal($inp[bpjs_date]);
    $inp[bpjs_date_ks] = setTanggal($inp[bpjs_date_ks]);
    $inp[tgl_input] = setTanggal($inp[tgl_input]);
    $inp[ktp_filename] = uploadKTP($id);
    $inp[pic_filename] = uploadPIC($id);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into rec_applicant set id = '$id', id_posisi = '$inp[id_posisi]', id_rencana = '$inp[id_rencana]', name = '$inp[name]', alias = '$inp[alias]', rekomendasi = '$inp[rekomendasi]', id_pelamar = '$inp[id_pelamar]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', cell_no = '$inp[cell_no]', phone_no = '$inp[phone_no]', religion = '$inp[religion]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', marital = '$inp[marital]', email = '$inp[email]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', height = '$inp[height]', weight = '$inp[weight]', ktp_filename = '$inp[ktp_filename]', pic_filename = '$inp[pic_filename]', tgl_input = '$inp[tgl_input]', characteristic = '$inp[characteristic]', abilities = '$inp[abilities]', hobby = '$inp[hobby]', organization = '$inp[organization]', mother_name = '$inp[mother_name]',  emergency_contact = '$inp[emergency_contact]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    $sql = "select kodeData from mst_data where kodeCategory = 'R09' and statusData = 't'";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $idSelection = getField("select id from rec_selection_appl order by id desc limit 1") + 1;
        $sql_ = "insert into rec_selection_appl set id = '$idSelection', parent_id = '$id', phase_id = '$r[kodeData]'";
        db($sql_);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function tambahPendidikan()
{
    global $inp, $par, $cUser;

    repField();

    $id = getLastId("rec_applicant_edu", "id");
    $inp[edu_filename] = uploadPendidikan($id);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into rec_applicant_edu set id = '$id', parent_id = '$par[id]', edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', edu_ipk = '$inp[edu_ipk]', edu_filename = '$inp[edu_filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambahPengalaman()
{
    global $inp, $par, $cUser;

    repField();

    $id = getLastId("rec_applicant_pwork", "id");
    $inp[filename] = uploadPengalaman($id);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into rec_applicant_pwork set id = '$id', parent_id = '$par[id]', company_name = '$inp[company_name]', position = '$inp[position]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', division = '$inp[division]', dept = '$inp[dept]', city = '$inp[city]', responsibility = '$inp[responsibility]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambahPelatihan()
{
    global $inp, $par, $cUser;

    repField();

    $id = getLastId("rec_applicant_ptraining", "id");
    $inp[filename] = uploadPelatihan($id);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into rec_applicant_ptraining set id = '$id', parent_id = '$par[id]', name = '$inp[name]', position = '$inp[position]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', training = '$inp[training]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function tambahFile()
{
    global $inp, $par, $cUser;

    repField();

    $id = getLastId("rec_applicant_pfile", "id");
    $inp[filename] = uploadFile($id);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into rec_applicant_pfile set id = '$id', parent_id = '$par[id]', name = '$inp[name]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]' ";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    $inp[name] = strtoupper($inp[name]);
    $inp[alias] = strtoupper($inp[alias]);
    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[npwp_date] = setTanggal($inp[npwp_date]);
    $inp[bpjs_date] = setTanggal($inp[bpjs_date]);
    $inp[bpjs_date_ks] = setTanggal($inp[bpjs_date_ks]);
    $inp[tgl_input] = setTanggal($inp[tgl_input]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');
    $inp[ktp_filename] = uploadKTP($par[id]);
    $inp[pic_filename] = uploadPIC($par[id]);

    $sql = "update rec_applicant set  name = '$inp[name]', id_posisi = '$inp[id_posisi]', id_rencana = '$inp[id_rencana]', alias = '$inp[alias]', rekomendasi = '$inp[rekomendasi]', id_pelamar = '$inp[id_pelamar]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', cell_no = '$inp[cell_no]', phone_no = '$inp[phone_no]', religion = '$inp[religion]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', marital = '$inp[marital]', email = '$inp[email]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', height = '$inp[height]', weight = '$inp[weight]', ktp_filename = '$inp[ktp_filename]', pic_filename = '$inp[pic_filename]', tgl_input = '$inp[tgl_input]',  characteristic = '$inp[characteristic]', abilities = '$inp[abilities]', hobby = '$inp[hobby]', organization = '$inp[organization]', mother_name = '$inp[mother_name]',  emergency_contact = '$inp[emergency_contact]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function ubahPendidikan()
{
    global $inp, $par, $cUser;

    repField();

    $eduFilename = uploadPendidikan($par[idPendidikan]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update rec_applicant_edu set edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', edu_ipk = '$inp[edu_ipk]', edu_filename = '$eduFilename', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[idPendidikan]' ";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubahPengalaman()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = uploadPengalaman($par[idPengalaman]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);

    $sql = "update rec_applicant_pwork set company_name = '$inp[company_name]', position = '$inp[position]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', division = '$inp[division]', dept = '$inp[dept]', city = '$inp[city]', responsibility = '$inp[responsibility]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[idPengalaman]' ";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubahPelatihan()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = uploadPelatihan($par[idPelatihan]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);

    $sql = "update rec_applicant_ptraining set name = '$inp[name]', position = '$inp[position]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', training = '$inp[training]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[idPelatihan]' ";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubahFile()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = uploadFile($par[idFile]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update rec_applicant_pfile set name = '$inp[name]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[idFile]' ";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function hapus()
{
    global $par, $fKTP, $fPIC;

    $ktpFilename = getField("select ktp_filename from rec_applicant where id='$par[id]'");
    if (file_exists($fKTP . $ktpFilename) and $ktpFilename != "") unlink($fKTP . $ktpFilename);

    $picFilename = getField("select pic_filename from rec_applicant where id='$par[id]'");
    if (file_exists($fPIC . $picFilename) and $picFilename != "") unlink($fPIC . $picFilename);

    $sql = "delete from rec_applicant where id='$par[id]'";
    db($sql);
    $sql = "delete from rec_selection_appl where parent_id='$par[id]'";
    db($sql);
    $sql = "delete from rec_applicant_edu where parent_id='$par[id]'";
    db($sql);
    $sql = "delete from rec_applicant_pwork where parent_id='$par[id]'";
    db($sql);
    $sql = "delete from rec_applicant_ptraining where parent_id='$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function hapusPendidikan()
{
    global $par, $fEdu;

    $eduFilename = getField("select edu_filename from rec_applicant_edu where id='$par[idPendidikan]'");
    if (file_exists($fEdu . $eduFilename) and $eduFilename != "") unlink($fEdu . $eduFilename);

    $sql = "delete from rec_applicant_edu where id='$par[idPendidikan]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?par[mode]=edit" . getPar($par, "mode,idPendidikan") . "';</script>";
}

function hapusPengalaman()
{
    global $par, $fWork;

    $eduFilename = getField("select edu_filename from rec_applicant_pwork where id='$par[idPengalaman]'");
    if (file_exists($fWork . $eduFilename) and $eduFilename != "") unlink($fWork . $eduFilename);

    $sql = "delete from rec_applicant_pwork where id='$par[idPengalaman]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?par[mode]=edit" . getPar($par, "mode,idPengalaman") . "';</script>";
}

function hapusPelatihan()
{
    global $par, $fTrain;

    $trainFilename = getField("select filename from rec_applicant_ptraining where id='$par[idPelatihan]'");
    if (file_exists($fTrain . $trainFilename) and $trainFilename != "") unlink($fTrain . $trainFilename);

    $sql = "delete from rec_applicant_ptraining where id='$par[idPelatihan]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?par[mode]=edit" . getPar($par, "mode,idPelatihan") . "';</script>";
}

function hapusFile()
{
    global $par, $fFile;

    $eduFilename = getField("select filename from rec_applicant_pfile where id='$par[idFile]'");
    if (file_exists($fFile . $eduFilename) and $eduFilename != "") unlink($fFile . $eduFilename);

    $sql = "delete from rec_applicant_pfile where id='$par[idFile]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?par[mode]=edit" . getPar($par, "mode,id") . "';</script>";
}

function hapusFilePendidikan()
{
    global $par, $fEdu;

    $eduFilename = getField("select edu_filename from rec_applicant_edu where id='$par[idPendidikan]'");
    if (file_exists($fEdu . $eduFilename) and $eduFilename != "") unlink($fEdu . $eduFilename);

    $sql = "update rec_applicant_edu set edu_filename = '' where id='$par[idPendidikan]'";
    db($sql);

    echo "<script>alert('DOKUMEN BERHASIL DIHAPUS');window.location='?par[mode]=editPendidikan" . getPar($par, "mode,id") . "';</script>";
}

function hapusFilePengalaman()
{
    global $par, $fWork;

    $workFilename = getField("select filename from rec_applicant_pwork where id='$par[idPengalaman]'");
    if (file_exists($fWork . $workFilename) and $workFilename != "") unlink($fWork . $workFilename);

    $sql = "update rec_applicant_pwork set filename = '' where id='$par[idPengalaman]'";
    db($sql);

    echo "<script>alert('DOKUMEN BERHASIL DIHAPUS');window.location='?par[mode]=editPengalaman" . getPar($par, "mode,id") . "';</script>";
}

function hapusFilePelatihan()
{
    global $par, $fTrain;

    $filename = getField("select filename from rec_applicant_ptraining where id='$par[idPelatihan]'");
    if (file_exists($fTrain . $filename) and $filename != "") unlink($fTrain . $filename);

    $sql = "update rec_applicant_ptraining set filename = '' where id='$par[idPelatihan]'";
    db($sql);

    echo "<script>alert('DOKUMEN BERHASIL DIHAPUS');window.location='?par[mode]=editPelatihan" . getPar($par, "mode,id") . "';</script>";
}

function hapusFileFile()
{
    global $par, $fFile;

    $filename = getField("select filename from rec_applicant_pfile where id='$par[idFile]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update rec_applicant_pfile set filename = '' where id='$par[idFile]'";
    db($sql);

    echo "<script>alert('DOKUMEN BERHASIL DIHAPUS');window.location='?par[mode]=editFile" . getPar($par, "mode,id") . "';</script>";
}

function form()
{
    global $s, $par, $arrTitle, $arrParameter, $menuAccess, $ui;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $sql = "SELECT * FROM rec_applicant WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $r[id_pelamar] = empty($r[id_pelamar]) ? getNumber("DP") : $r[id_pelamar];

    // QUERY COMBO DATA
    $queryPosisi = "SELECT id_posisi id, subject description from rec_job_posisi order by subject";
    $queryRencana = "SELECT id, subject description from rec_plan order by subject";
    $queryTempatLahir = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData, t1.namaData";
    $queryProvinsi = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S02' and statusData = 't' order by namaData";
    $queryKotaKTP = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[ktp_prov]' and statusData = 't' order by namaData";
    $queryKotaDom = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[dom_prov]' and statusData = 't' order by namaData";
    $queryAgama = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S07' and statusData = 't' order by namaData";
    $queryPerkawinan = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S08' and statusData = 't' order by namaData";

    $styleTab = $par[mode] == "add" ? "style=\"pointer-events: none;cursor: default;\"" : "";
    $styleText = $par[mode] == "add" ? "display:block" : "display:none";

    setValidation("is_null", "inp[name]", "anda harus mengisi Nama");
    setValidation("is_null", "inp[ktp_no]", "anda harus mengisi KTP");
    setValidation("is_null", "inp[birth_place]", "anda harus mengisi Tempat Lahir");
    setValidation("is_null", "inp[birth_date]", "anda harus mengisi Tanggal Lahir");
    setValidation("is_null", "inp[tgl_input]", "anda harus mengisi Tanggal Input");
    setValidation("is_null", "inp[id_posisi]", "anda harus mengisi Posisi");
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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>';" />
            </p>
            <input type="hidden" id="par[mode]" value=<?= $par[id] ?>>
            <fieldset>
                <legend>UMUM</legend>
                <table style="width: 100%">
                    <tr>
                        <td style="width: 50%">
                            <p><?= $ui->createField("Tanggal Input", "inp[tgl_input]", getTanggal($r[tgl_input]), "t", "t", "", "", "", "", "t") ?></p>

                        </td>
                        <td style="width: 50%">
                            <p><?= $ui->createComboData("Posisi", $queryPosisi, "id", "description", "inp[id_posisi]", $r[id_posisi], "", "", "t", "t", "t") ?></p>
                            <!-- <p><?= $ui->createField("Rekomendasi", "inp[rekomendasi]", $r[rekomendasi]) ?></p>
                            <p><?= $ui->createComboData("Rencana", $queryRencana, "id", "description", "inp[id_rencana]", $r[id_rencana], "", "", "t", "") ?></p> -->
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <span class="required">
                <h5 style="margin-left:40px;<?= $styleText ?>">** MOHON KLIK SIMPAN UNTUK PINDAH KE TAB LAIN</h5>
            </span>

            <br>
            <ul class="hornav">
                <li class="current"><a href="#identitas">Identitas</a></li>
                <li><a <?= $styleTab ?> href="#photo">Foto</a></li>
                <li><a <?= $styleTab ?> href="#pendidikan">Pendidikan</a></li>
                <li><a <?= $styleTab ?> href="#keahlian">Keahlian</a></li>
                <li><a <?= $styleTab ?> href="#pengalaman">Pengalaman</a></li>
                <li><a <?= $styleTab ?> href="#pelatihan">Pelatihan</a></li>
                <li><a <?= $styleTab ?> href="#file">File</a></li>
            </ul>
            <br>

            <!-- DATA IDENTITAS START -->
            <div id="identitas" class="subcontent" style="margin:0">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Nama Lengkap", "inp[name]", $r[name], "t", "t", "style=\"text-transform:uppercase;\"") ?></p>
                            <p><?= $ui->createField("Panggilan", "inp[alias]", $r[alias], "", "t", "style=\"text-transform:uppercase;\"") ?></p>
                            <p><?= $ui->createComboData("Tempat Lahir", $queryTempatLahir, "id", "description", "inp[birth_place]", $r[birth_place], "", "", "t", "t", "t") ?></p>
                            <p><?= $ui->createField("Tanggal Lahir", "inp[birth_date]", getTanggal($r[birth_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createTextArea("Alamat KTP", "inp[ktp_address]", $r[ktp_address], "", "t") ?></p>
                            <p><?= $ui->createComboData("Provinsi", $queryProvinsi, "id", "description", "inp[ktp_prov]", $r[ktp_prov], "onchange=\"getSub('ktp_prov', 'ktp_city', '" . getPar($par, "mode") . "');\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Kota", $queryKotaKTP, "id", "description", "inp[ktp_city]", $r[ktp_city], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Telp. Rumah", "inp[phone_no]", $r[phone_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            <p><?= $ui->createField("Email", "inp[email]", $r[email], "", "t") ?></p>
                            <p><?= $ui->createField("No. NPWP", "inp[npwp_no]", $r[npwp_no], "", "t") ?></p>
                            <p><?= $ui->createField("Tanggal NPWP", "inp[npwp_date]", getTanggal($r[npwp_date]), "", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Tinggi Badan", "inp[height]", $r[height], "", "t", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", 3) ?></p>
                            <p><?= $ui->createField("Berat Badan", "inp[weight]", $r[weight], "", "t", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", 3) ?></p>
                            <p><?= $ui->createField("Uk. Baju", "inp[uni_cloth]", $r[uni_cloth], "", "t", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", 2) ?></p>
                            <p><?= $ui->createField("Uk. Celana", "inp[uni_pant]", $r[uni_pant], "", "t", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", 2) ?></p>
                            <p><?= $ui->createField("Uk. Sepatu", "inp[uni_shoe]", $r[uni_shoe], "", "t", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", 2) ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createField("ID Pelamar", "inp[id_pelamar]", $r[id_pelamar], "", "t", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("No. KTP", "inp[ktp_no]", $r[ktp_no], "t", "t", "", "onkeyup=\"cekPhone(this);\"", "16") ?></p>
                            <p><?= $ui->createField("Nama Ibu Kandung", "inp[mother_name]", $r[mother_name], "", "t") ?></p>
                            <p><?= $ui->createRadio("Jenis Kelamin", "inp[gender]", array("M" => "Laki-Laki", "F" => "Perempuan"), $r[gender], "t") ?></p>
                            <p><?= $ui->createTextArea("Alamat Domisili", "inp[dom_address]", $r[dom_address], "", "t") ?></p>
                            <p><?= $ui->createComboData("Provinsi", $queryProvinsi, "id", "description", "inp[dom_prov]", $r[dom_prov], "onchange=\"getSub('dom_prov', 'dom_city', '" . getPar($par, "mode") . "');\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Kota", $queryKotaDom, "id", "description", "inp[dom_city]", $r[dom_city], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Agama", $queryAgama, "id", "description", "inp[religion]", $r[religion], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Nomor HP", "inp[cell_no]", $r[cell_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            <p><?= $ui->createField("No. BPJS KS", "inp[bpjs_no_ks]", $r[bpjs_no_ks], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            <p><?= $ui->createField("Tanggal BPJS KS", "inp[bpjs_date_ks]", getTanggal($r[bpjs_date_ks]), "", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("No. BPJS TK", "inp[bpjs_no]", $r[bpjs_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            <p><?= $ui->createField("Tanggal BPJS TK", "inp[bpjs_date]", getTanggal($r[bpjs_date]), "", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createComboData("Status Perkawinan", $queryPerkawinan, "id", "description", "inp[marital]", $r[marital], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Emergency Contact", "inp[emergency_contact]", $r[emergency_contact], "onkeyup=\"cekPhone(this);\"", "t") ?></p>
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
            <!-- DATA IDENTITAS END -->

            <!-- DATA FOTO START -->
            <div id="photo" class="subcontent" style="display: none;margin-top: 0px;">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p>
                                <label class="l-input-small2">File KTP</label>
                                <div id="ktpPreview" <?php if ($par[mode] == "edit" && $r[ktp_filename] != "") { ?> style="background-image:  url(<?= APP_URL . "/files/recruit/ktp/" . $r[ktp_filename] ?>)" <?php } ?>">
                                </div>
                                <br />
                                <input id="ktpFilename" type="file" name="ktpFilename" class="img" style="padding-left: 240px;" />
                            </p>
                        </td>
                        <td style="width:50%">
                            <p>
                                <label class="l-input-small">Foto</label>
                                <div id="fotoPreview" <?php if ($par[mode] == "edit" && $r[pic_filename] != "") { ?> style="background-image:  url(<?= APP_URL . "/files/recruit/pic/" . $r[pic_filename] ?>)" <?php } ?>">
                                </div>
                                <br />
                                <input id="picFilename" type="file" name="picFilename" class="img" style="padding-left: 240px;" />
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- DATA FOTO END -->

            <!-- DATA PENDIDIKAN START -->
            <div id="pendidikan" class="subcontent" style="display:none;">
                <div class="widgetbox">
                    <div class="title">
                        <h3>
                            <p style="float:left;margin-top:20px;">DATA PENDIDIKAN</p>
                        </h3>
                        <p style="float:right">
                            <a href="#" onclick="openBox('popup.php?par[mode]=addPendidikan<?= getPar($par, 'mode') ?>',900,500)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
                        </p>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
                    <thead>
                        <tr>
                            <th width="20">No.</th>
                            <th width="100">TINGKATAN</th>
                            <th width="*">NAMA LEMBAGA</th>
                            <th width="150">Jurusan</th>
                            <th width="150">Kota</th>
                            <th width="50">Tahun</th>
                            <th width="50">File</th>
                            <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql_ = "SELECT * from rec_applicant_edu where parent_id = '$par[id]' ";
                            $res_ = db($sql_);
                            $no = 0;
                            while ($r_ = mysql_fetch_assoc($res_)) {
                                $no++;
                                if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                                    $control = "";
                                    if (!empty($menuAccess[$s]["edit"]))
                                        $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editPendidikan&par[idPendidikan]=$r_[id]" . getPar($par, "mode,id") . "', 900,500)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                                    if (!empty($menuAccess[$s]["delete"]))
                                        $control .= "<a href=\"?par[mode]=delPendidikan&par[idPendidikan]=$r_[id]" . getPar($par, "mode,idPendidikan") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                }
                                ?>
                            <tr>
                                <td><?= $no ?>.</td>
                                <td><?= $arrMaster[$r_[edu_type]] ?></td>
                                <td><?= $r_[edu_name] ?></td>
                                <td><?= $arrMaster[$r_[edu_fac]] ?></td>
                                <td><?= $arrMaster[$r_[edu_city]] ?></td>
                                <td align="center"><?= $r_[edu_year] ?></td>
                                <td align="center"><a href="download.php?d=recPendidikan&f=<?= $r_[id] ?>"><img src=<?= getIcon($r_[edu_filename]) ?>></a></td>
                                <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><td align="center"><?= $control ?></td>
                            </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <!-- DATA PENDIDIKAN END  -->

            <!-- DATA KEAHLIAN START -->
            <div id="keahlian" class="subcontent" style="display:none;">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createTextArea("Karakter Pribadi", "inp[characteristic]", $r[characteristic], "") ?></p>
                            <p><?= $ui->createTextArea("Keahlian Khusus", "inp[abilities]", $r[abilities], "") ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createTextArea("Hobi", "inp[hobby]", $r[hobby], "") ?></p>
                            <p><?= $ui->createTextArea("Organisasi Sosial", "inp[organization]", $r[organization], "") ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- DATA KEAHLIAN END -->

            <!-- DATA PENGALAMAN START -->
            <div id="pengalaman" class="subcontent" style="margin:0;display:none;">
                <div class="widgetbox">
                    <div class="title">
                        <h3>
                            <p style="float:left;margin-top:20px;">DATA PENGALAMAN</p>
                        </h3>
                        <p style="float:right">
                            <a href="#" onclick="openBox('popup.php?par[mode]=addPengalaman<?= getPar($par, 'mode') ?>',900,500)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
                        </p>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
                    <thead>
                        <tr>
                            <th width="20">No.</th>
                            <th width="100">PERUSAHAAN</th>
                            <th width="*">JABATAN</th>
                            <th width="150">BAGIAN</th>
                            <th width="80">Tahun</th>
                            <th width="50">REFF</th>
                            <th width="50">Status</th>
                            <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql_ = "select *, year(start_date) as mulai from rec_applicant_pwork where parent_id = '$par[id]' ";
                            $res_ = db($sql_);
                            $no = 0;
                            while ($r_ = mysql_fetch_assoc($res_)) {
                                $no++;
                                $r_[status] = $r_[status] == "t" ? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";

                                if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                                    $control = "";
                                    if (!empty($menuAccess[$s]["edit"]))
                                        $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editPengalaman&par[idPengalaman]=$r_[id]" . getPar($par, "mode, idPengalaman") . "',900,500)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                                    if (!empty($menuAccess[$s]["delete"]))
                                        $control .= "<a href=\"?par[mode]=delPengalaman&par[idPengalaman]=$r_[id]" . getPar($par, "mode,idPengalaman") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                }
                                ?>
                            <tr>
                                <td><?= $no ?>.</td>
                                <td><?= $r_[company_name] ?></td>
                                <td><?= $r_[position] ?></td>
                                <td><?= $r_[dept] ?></td>
                                <td align="center"><?= $r_[mulai] ?></td>
                                <td align="center"><a href="download.php?d=recPengalaman&f=<?= $r_[id] ?>"><img src="<?= getIcon($r_[filename]) ?>"></a></td>
                                <td align="center"><?= $r_[status] ?></td>
                                <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><td><?= $control ?></td>
                            </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <!-- DATA PENGALAMAN END -->

            <!-- DATA PELATIHAN START -->
            <div id="pelatihan" class="subcontent" style="display:none;">
                <div class="widgetbox">
                    <div class="title">
                        <h3>
                            <p style="float:left;margin-top:20px;">DATA PELATIHAN</p>
                        </h3>
                        <p style="float:right">
                            <a href="#" onclick="openBox('popup.php?par[mode]=addPelatihan<?= getPar($par, 'mode') ?>',900,500)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
                        </p>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
                    <thead>
                        <tr>
                            <th width="20">No.</th>
                            <th width="100">Lembaga</th>
                            <th width="*">Nama Training</th>
                            <th width="150">BAGIAN</th>
                            <th width="80">Tahun</th>
                            <th width="50">Sertifikasi</th>
                            <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql_ = "select *, year(start_date) as mulai from rec_applicant_ptraining where parent_id = '$par[id]' ";
                            $res_ = db($sql_);
                            $no = 0;
                            while ($r_ = mysql_fetch_assoc($res_)) {
                                $no++;

                                if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                                    $control = "";
                                    if (!empty($menuAccess[$s]["edit"]))
                                        $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editPelatihan&par[idPelatihan]=$r_[id]" . getPar($par, 'mode') . "',900,500)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                                    if (!empty($menuAccess[$s]["delete"]))
                                        $control .= "<a href=\"?par[mode]=delPelatihan&par[idPelatihan]=$r_[id]" . getPar($par, "mode,idPelatihan") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                }
                                ?>
                            <tr>
                                <td><?= $no ?>.</td>
                                <td><?= $r_[name] ?></td>
                                <td><?= $r_[training] ?></td>
                                <td><?= $r_[position] ?></td>
                                <td align="center"><?= $r_[mulai] ?></td>
                                <td align="center"><a href="download.php?d=recPelatihan&f=<?= $r_[id] ?>"><img src="<?= getIcon($r_[filename]) ?>"></a></td>
                                <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><td><?= $control ?></td>
                            </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <!-- DATA PELATIHAN END -->

            <!-- DATA FILE START -->
            <div id="file" class="subcontent" style="margin:0;display:none;">
                <div class="widgetbox">
                    <div class="title">
                        <h3>
                            <p style="float:left;margin-top:20px;">DATA FILE</p>
                        </h3>
                        <p style="float:right">
                            <a href="#" onclick="openBox('popup.php?par[mode]=addFile<?= getPar($par, 'mode') ?>',600,300)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
                        </p>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntables">
                    <thead>
                        <tr>
                            <th width="20">No.</th>
                            <th width="*">NAMA FILE</th>
                            <th width="80">FILE</th>
                            <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th width="50">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql_ = "select * from rec_applicant_pfile where parent_id = '$par[id]' ";
                            $res_ = db($sql_);
                            $no = 0;
                            while ($r_ = mysql_fetch_assoc($res_)) {
                                $no++;
                                if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                                    $control = "";
                                    if (!empty($menuAccess[$s]["edit"]))
                                        $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editFile&par[idFile]=$r_[id]" . getPar($par, 'mode') . "',600,300)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                                    if (!empty($menuAccess[$s]["delete"]))
                                        $control .= "<a href=\"?par[mode]=delFile&par[idFile]=$r_[id]" . getPar($par, "mode,idFile") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                                }
                                ?>
                            <tr>
                                <td><?= $no ?>.</td>
                                <td><?= $r_[name] ?></td>
                                <td align="center"><a href="download.php?d=recFile&f=<?= $r_[id] ?>"><img src="<?= getIcon($r_[filename]) ?>"></a></td>
                                <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?> <td><?= $control ?></td>
                            </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <!-- DATA FILE END -->
            <style>
                #p0 {
                    margin: 5px 0;
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
                    /*border: 1px solid #03F;*/
                    padding: 5px;
                }

                fieldset label {
                    margin-left: 10px;
                }

                .chosen-container {
                    min-width: 250px;
                }
            </style>
        </form>
    </div>
<?php
}

function formPendidikan()
{
    global $s, $par, $arrTitle, $arrParameter, $ui;

    $sql = "select * from rec_applicant_edu where id='$par[idPendidikan]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $queryTipe = "SELECT kodeData id, namaData description from mst_data where statusData='t' and kodeCategory='" . $arrParameter[32] . "' order by urutanData";
    $queryKota = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData, t1.namaData";
    $queryFakultas = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='" . $arrParameter[33] . "' and statusData = 't' order by kodeInduk,urutanData";
    $queryJurusan = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='" . $arrParameter[34] . "' and kodeInduk = '$r[edu_fac]' and statusData = 't' order by kodeInduk,urutanData";

    $stylePerguruan = $r[edu_type] > 614 && !empty($r[edu_type]) ? "style=\"display:block\"" : "style=\"display:none\"";

    setValidation("is_null", "inp[edu_type]", "anda harus mengisi Tipe");
    setValidation("is_null", "inp[edu_name]", "anda harus mengisi Nama Lembaga");
    setValidation("is_null", "inp[edu_city]", "anda harus mengisi Kota");
    setValidation("is_null", "inp[edu_year]", "anda harus mengisi Tahun Lulus");
    echo getValidation();
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> - Data Pendidikan</h1>
    </div>
    <div class="contentwrapper contentpopup">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <style>
                #inp_edu_city__chosen {
                    min-width: 200px;
                }

                #inp_edu_type__chosen {
                    min-width: 400px;
                }

                #inp_edu_fac__chosen {
                    min-width: 200px;
                }

                #inp_edu_dept__chosen {
                    min-width: 200px;
                }
            </style>

            <div class="subcontent">
                <p><?= $ui->createComboData("Tipe", $queryTipe, "id", "description", "inp[edu_type]", $r[edu_type], "onchange=\"setPerguruan(this.value);\"", "400px", "t", "t") ?></p>
                <p><?= $ui->createField("Nama Lembaga", "inp[edu_name]", $r[edu_name], "t") ?></p>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createComboData("Kota", $queryKota, "id", "description", "inp[edu_city]", $r[edu_city], "", "150px", "t", "t", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createField("Tahun Lulus", "inp[edu_year]", $r[edu_year], "t", "t", "style=\"width:50px;\"", "onkeyup=\"cekPhone(this);\"", 4) ?></p>
                        </td>
                    </tr>
                </table>
                <div id="divPerguruan" <?= $stylePerguruan ?>>
                    <p><?= $ui->createTextArea("Judul Skripsi/Tesis", "inp[edu_essay]", $r[edu_essay]) ?></p>
                    <p><?= $ui->createComboData("Fakultas", $queryFakultas, "id", "description", "inp[edu_fac]", $r[edu_fac], "onchange=\"getSub('edu_fac', 'edu_dept', '" . getPar($par, "mode") . "')\"", "", "t") ?></p>
                    <p><?= $ui->createComboData("Jurusan", $queryJurusan, "id", "description", "inp[edu_dept]", $r[edu_dept], "", "", "t") ?></p>
                </div>
                <p><?= $ui->createField("Nilai Rata Rata/IPK", "inp[edu_ipk]", $r[edu_ipk]) ?></p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createFile("Dokumen", "edu_filename", $r[edu_filename], "", "", "recPendidikan", $r[id], "delFilePendidikan") ?></p>
            </div>
            <p style="position:absolute;top:-10px;right:10px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, "mode,idPegawai") ?>'" />
            </p>
        </form>
    </div>
<?php
}

function formPengalaman()
{
    global $s, $par, $arrTitle, $ui;

    $sql = "select * from rec_applicant_pwork where id='$par[idPengalaman]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    setValidation("is_null", "inp[company_name]", "anda harus mengisi Perusahaan");
    setValidation("is_null", "inp[position]", "anda harus mengisi Jabatan");
    setValidation("is_null", "inp[start_date]", "anda harus mengisi Mulai");
    setValidation("is_null", "inp[end_date]", "anda harus mengisi Selesai");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> - Data Pengalaman</h1>

    </div>
    <div class="contentwrapper contentpopup">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <div class="subcontent">
                <p><?= $ui->createField("Perusahaan", "inp[company_name]", $r[company_name], "t") ?></p>
                <p><?= $ui->createField("Jabatan", "inp[position]", $r[position], "t") ?></p>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Divisi", "inp[division]", $r[division], "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createField("Selesai", "inp[end_date]", getTanggal($r[end_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Bagian", "inp[dept]", $r[dept], "", "t") ?></p>
                        </td>
                </table>
                <p><?= $ui->createField("Lokasi/Kota", "inp[city]", $r[city]) ?></p>
                <p><?= $ui->createTextArea("Tugas/Tanggung Jawab", "inp[responsibility]", $r[responsibility]) ?></p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createFile("Dokumen", "filename", $r[filename], "", "", "recPengalaman", $r[id], "delFilePengalaman") ?></p>
                <p><?= $ui->createRadio("Status", "inp[status]", array("t" => "Aktif", "f" => "Tidak Aktif"), $r[status]) ?></p>
            </div>
            <p style="position:absolute;top:-10px;right:10px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="closeBox();" />
            </p>
        </form>
    </div>
<?php
}

function formPelatihan()
{
    global $s, $par, $arrTitle, $ui;

    $sql = "select * from rec_applicant_ptraining where id='$par[idPelatihan]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    setValidation("is_null", "inp[name]", "anda harus mengisi Lembaga");
    setValidation("is_null", "inp[training]", "anda harus mengisi Nama Training");
    setValidation("is_null", "inp[position]", "anda harus mengisi Bagian");
    setValidation("is_null", "inp[start_date]", "anda harus mengisi Mulai");
    setValidation("is_null", "inp[end_date]", "anda harus mengisi Selesai");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> - Data Pelatihan</h1>
    </div>
    <div class="contentwrapper contentpopup">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <div class="subcontent">
                <p><?= $ui->createField("Lembaga", "inp[name]", $r[name], "t") ?></p>
                <p><?= $ui->createField("Nama Training", "inp[training]", $r[training], "t") ?></p>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "t", "", "", "", "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p><?= $ui->createField("Selesai", "inp[end_date]", getTanggal($r[end_date]), "t", "t", "", "", "", "", "t") ?></p>
                        </td>
                </table>
                <p><?= $ui->createField("Bagian", "inp[position]", $r[position]) ?></p>
                <p><?= $ui->createField("Lokasi/Kota", "inp[location]", $r[location]) ?></p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createFile("Dokumen", "filename", $r[filename], "", "", "recPelatihan", $r[id], "delFilePelatihan") ?></p>
            </div>
            <p style="position:absolute;top:-10px;right:10px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="closeBox();" />
            </p>
        </form>
    </div>
<?php
}

function formFile()
{
    global $s, $par, $arrTitle, $ui;

    $sql = "select * from rec_applicant_pfile where id='$par[idFile]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    setValidation("is_null", "inp[name]", "anda harus mengisi Nama");
    echo getValidation();
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> - Data File</h1>
    </div>
    <div class="contentwrapper contentpopup">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <div class="subcontent">
                <p><?= $ui->createField("Nama", "inp[name]", $r[name], "t") ?></p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "recFile", $r[id], "delFileFile") ?></p>
            </div>
            <p style="position:absolute;top:-10px;right:10px;">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Batal" onclick="closeBox();" />
            </p>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    // $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    if (!empty($par[tahunData]))
        $filterData = "WHERE year(sel_date) = '$par[tahunData]'";

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");
    $arrSeleksi = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'R09' and statusData = 't'");
    $arrDataSeleksi = arrayQuery("SELECT parent_id, phase_id, sel_status FROM rec_selection_appl $filterData");
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="" method="post" id="form" class="stdform">
            <p style="position:absolute;top:5px;right:15px"><?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\"", "", " - ALL TAHUN -") ?></p>
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" name="par[filterData]" value="<?= $par[filterData] ?>" placeholder="Search.." style="width:200px;" />
                    <?= comboData("SELECT id_posisi id, subject description from rec_job_posisi order by subject", "id", "description", "par[idPosisi]", "All Posisi", $par[idPosisi], "", "200px", "chosen-select") ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
            <div id="pos_r" style="float:right">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
                <a href="?par[mode]=add<?= getPar($par, "mode,kodeAktifitas") ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th rowspan="2" width="20" style="vertical-align:middle">No.</th>
                    <th rowspan="2" width="*" style="vertical-align:middle">NAMA</th>
                    <th rowspan="2" width="60" style="vertical-align:middle">ID Pelamar</th>
                    <th rowspan="2" width="50" style="vertical-align:middle">JK</th>
                    <th rowspan="2" width="50" style="vertical-align:middle">Umur</th>
                    <th rowspan="2" width="150" style="vertical-align:middle">Posisi</th>
                    <th rowspan="2" width="150" style="vertical-align:middle">Pendidikan</th>
                    <th rowspan="2" width="150" style="vertical-align:middle">Jurusan</th>
                    <th colspan="6" width="50" style="vertical-align:middle">Tahapan</th>
                    <th rowspan="2" width="50" style="vertical-align:middle">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) ||  isset($menuAccess[$s]["delete"])) ?><th rowspan="2" width="50" style="vertical-align:middle">Kontrol</th>
                </tr>
                <tr>
                    <th width="7px"><img src="images/tt_administrasi.png"></th>
                    <th width="7px"><img src="images/tt_panggilan.png"></th>
                    <th width="7px"><img src="images/tt_psikotes.png"></th>
                    <th width="7px"><img src="images/tt_wawancarahr.png"></th>
                    <th width="7px"><img src="images/tt_wawancarauser.png"></th>
                    <th width="7px"><img src="images/tt_mcu.png"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "where t1.id is not null";
                    if (!empty($par[tahunData]))
                        $filter .= " and year(tgl_input) = '$par[tahunData]'";
                    if (!empty($par[filterData]))
                        $filter .= " and t1.name LIKE '%$par[filterData]%' ";
                    if (!empty($par[idPosisi]))
                        $filter .= " and t2.id_posisi = '$par[idPosisi]' ";
                    $sql = "SELECT t1.id, t1.id_pelamar, t1.name, t1.gender, t1.id_posisi, t1.administrasi, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, t2.subject, t3.edu_type, t3.edu_dept from rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi left join rec_applicant_edu t3 on t1.id = t3.parent_id $filter order by t1.name ";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";
                        $administrasi = $r[administrasi] == "t" ? "<img src=\"styles/images/t.png\" title=\"Lolos\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Lolos\">";
                        $statusAdministrasi = "<a href=\"?par[mode]=setAdministrasi&par[id]=$r[id]&par[administrasi]=$r[administrasi]" . getPar($par, "mode, id") . "\" onclick=\"return confirm('anda yakin akan mengubah data ini ?')\">" . $administrasi . "</a>";
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td><?= $r[id_pelamar] ?></td>
                        <td><?= $r[gender] ?></td>
                        <td align="right"><?= $r[umur] ?></td>
                        <td><?= $r[subject] ?></td>
                        <td><?= $arrMaster[$r[edu_type]] ?></td>
                        <td><?= $arrMaster[$r[edu_dept]] ?></td>
                        <td><?= $statusAdministrasi ?></td>
                        <?php foreach ($arrSeleksi as $key => $value) {
                                    $labelSaran = $key != 607 ? "Disarankan" : "Lulus";
                                    $hasilSeleksi = $arrDataSeleksi[$r[id]][$key] == "601" ? "<img src=\"styles/images/t.png\" title=\"" . $labelSaran . "\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
                                    $hasilSeleksi = $arrDataSeleksi[$r[id]][$key] == "2932" ? "<img src=\"styles/images/o.png\" title=\"Close\">" : $hasilSeleksi;
                                    $hasilSeleksi = $arrDataSeleksi[$r[id]][$key] == "600" ? "<img src=\"styles/images/f.png\" title=\"Tidak " . $labelSaran . "\">" : $hasilSeleksi;
                                    echo "<td align=\"center\">$hasilSeleksi</td>";
                                }
                                ?>
                        <td><?= $control ?></td>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
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
    global $s, $arrTitle, $fExport, $par;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R13')");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no",  "nama", "id pelamar", "jenis kelamin", "umur", "posisi", "pendidikan", "jurusan", "administrasi", "seleksi", "penempatan");

    $filter = "where year(tgl_input) = '$par[tahunData]'";
    if (!empty($par[filterData]))
        $filter .= " and t1.name LIKE '%$par[filterData]%' ";
    if (!empty($par[idPosisi]))
        $filter .= " and t2.id_posisi = '$par[idPosisi]' ";

    $sql = "SELECT t1.id, t1.id_pelamar, t1.name, t1.gender, t1.id_posisi, t1.administrasi, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, t2.subject, t3.edu_type, t3.edu_dept from rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi left join rec_applicant_edu t3 on t1.id = t3.parent_id $filter order by t1.name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";
        $statusAdministrasi = $r[administrasi] == "t" ? "Lolos" : "Tidak Lolos";
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[id_pelamar] . "\t center",
            $r[gender] . "\t left",
            $r[subject] . "\t left",
            $arrMaster[$r[edu_type]] . "\t left",
            $arrMaster[$r[edu_dept]] . "\t left",
            $statusAdministrasi . "\t left",
            $statusPenempatan . "\t left",
            $statusPenempatan . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 11, $field, $data);
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "subData":
            $text = subData();
            break;
        case "setAdministrasi":
            $text = setAdministrasi();
            break;
        case "lst":
            $text = lData();
            break;
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "addPendidikan":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPendidikan() : tambahPendidikan();
            else $text = lihat();
            break;
        case "editPendidikan":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPendidikan() : ubahPendidikan();
            else $text = lihat();
            break;
        case "delPendidikan":
            if (isset($menuAccess[$s]["delete"])) $text = hapusPendidikan();
            else $text = lihat();
            break;
        case "delFilePendidikan":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFilePendidikan();
            else $text = lihat();
            break;
        case "addPengalaman":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPengalaman() : tambahPengalaman();
            else $text = lihat();
            break;
        case "editPengalaman":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPengalaman() : ubahPengalaman();
            else $text = lihat();
            break;
        case "delPengalaman":
            if (isset($menuAccess[$s]["delete"])) $text = hapusPengalaman();
            else $text = lihat();
            break;
        case "delFilePengalaman":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFilePengalaman();
            else $text = lihat();
            break;
        case "addPelatihan":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPelatihan() : tambahPelatihan();
            else $text = lihat();
            break;
        case "editPelatihan":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPelatihan() : ubahPelatihan();
            else $text = lihat();
            break;
        case "delPelatihan":
            if (isset($menuAccess[$s]["delete"])) $text = hapusPelatihan();
            else $text = lihat();
            break;
        case "delFilePelatihan":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFilePelatihan();
            else $text = lihat();
            break;
        case "addFile":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formFile() : tambahFile();
            else $text = lihat();
            break;
        case "editFile":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formFile() : ubahFile();
            else $text = lihat();
            break;
        case "delFile":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFile();
            else $text = lihat();
            break;
        case "delFileFile":
            if (isset($menuAccess[$s]["delete"])) $text = hapusFileFile();
            else $text = lihat();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fKTP = "files/emp/ktp/";
$fPIC = "files/emp/pic/";
$fExport = "files/export/";

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
    $inp[pic_filename] = uploadPIC($id);
    $inp[ktp_filename] = uploadKTP($id);
    $inp[create_date] = date('Y-m-d H:i:s');
    $inp[create_by] = $cUser;

    $sql = "INSERT INTO emp set id = '$id', cat = '$inp[cat]', name = '$inp[name]', alias = '$inp[alias]', reg_no = '$inp[reg_no]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', kk_no = '$inp[kk_no]', ktp_filename = '$inp[ktp_filename]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', phone_no = '$inp[phone_no]', cell_no = '$inp[cell_no]', email = '$inp[email]', marital = '$inp[marital]', ptkp = '$inp[ptkp]', religion = '$inp[religion]', pic_filename = '$inp[pic_filename]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', status = '$inp[status]', join_date = '$inp[join_date]', leave_date = '$inp[leave_date]', nation = '$inp[nation]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    echo $sql . "<br>";
    db($sql);

    $idPhist = getLastId("emp_phist", "id");
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);

    $sql = "INSERT INTO emp_phist set id = '$idPhist', parent_id = '$id', pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '1', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    echo $sql;
    db($sql);

    die();

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
    $inp[pic_filename] = uploadPIC($par[id]);
    $inp[ktp_filename] = uploadKTP($par[id]);
    $inp[update_date] = date('Y-m-d H:i:s');
    $inp[update_by] = $cUser;

    $sql = "UPDATE emp set cat = '$inp[cat]', name = '$inp[name]', alias = '$inp[alias]', reg_no = '$inp[reg_no]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', kk_no = '$inp[kk_no]', ktp_filename = '$inp[ktp_filename]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', phone_no = '$inp[phone_no]', cell_no = '$inp[cell_no]', email = '$inp[email]', marital = '$inp[marital]', ptkp = '$inp[ptkp]', religion = '$inp[religion]', pic_filename = '$inp[pic_filename]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', status = '$inp[status]', join_date = '$inp[join_date]', leave_date = '$inp[leave_date]', nation = '$inp[nation]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);

    $sql = "UPDATE emp_phist set pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where parent_id = '$par[id]' AND status = '1'";
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
    $queryGrade = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S10' and kodeInduk = '$r[rank]' order by namaData";
    $queryLocation = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S06' order by namaData";
    $queryDir = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='X04' order by kodeInduk, urutanData";
    $queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X04' and t1.kodeInduk = '$r[dir_id]' order by t1.urutanData";
    $queryDept = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk where t3.kodeCategory='X04' and t1.kodeInduk = '$r[div_id]' order by t1.urutanData";
    $queryUnit = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X04' and t1.kodeInduk = '$r[dept_id]' order by t1.urutanData";
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
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
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
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Nama Lengkap", "inp[name]", $r[name], "t", "t", "style=\"text-transform: uppercase;\"") ?></p>
                            <p><?= $ui->createField("Panggilan", "inp[alias]", $r[alias], "", "t") ?></p>
                            <p><?= $ui->createComboData("Tempat Lahir", $queryBirthPlace, "id", "description", "inp[birth_place]", $r[birth_place], "", "", "t", "t", "t") ?></p>
                            <p><?= $ui->createField("Tanggal Lahir", "inp[birth_date]", getTanggal($r[birth_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createTextArea("Alamat KTP", "inp[ktp_address]", $r[ktp_address], "", "t") ?></p>
                            <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[ktp_prov]", $r[ktp_prov], "onchange=\"getSub('ktp_prov', 'ktp_city', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Kota", $queryCity, "id", "description", "inp[ktp_city]", $r[ktp_city], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Telp. Rumah", "inp[phone_no]", $r[phone_no], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>
                            <p><?= $ui->createComboData("Asal Negara", $queryNation, "id", "description", "inp[nation]", $r[nation], "", "", "t", "", "t") ?></p>

                        </td>
                        <td style="width:50%">
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
            </div>

            <!-- DATA POSISI -->
            <div id="posisi" class="subcontent" style="display:none">
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Posisi", "inp[pos_name]", $r[pos_name], "t", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryRank, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no], "", "t") ?></p>
                            <p><?= $ui->createField("Tanggal Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Keterangan", "inp[remark]", $r[remark], "", "t") ?></p>
                        </td>
                        <td style="width:50%">
                            <p>&nbsp;</p>
                            <p><?= $ui->createComboData("Grade", $queryGrade, "id", "description", "inp[grade]", $r[grade], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Tanggal SK", "inp[sk_date]", getTanggal($r[sk_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Tanggal Selesai", "inp[end_date]", getTanggal($r[end_date]), "", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createComboData("Lokasi Kerja", $queryLocation, "id", "description", "inp[location]", $r[location], "", "", "t", "t", "t") ?></p>
                        </td>
                    </tr>
                </table>
                <ul class="editornav">
                    <li class="current"><a href="#organisasi">ORGANISASI</a></li>
                    <li><a href="#lokasi">LOKASI</a></li>
                    <li><a href="#struktur">STRUKTUR</a></li>
                    <li><a href="#setting">SETTING</a></li>
                </ul>

                <!-- DATA ORGANISASI -->
                <div id="organisasi" class="subcontent1">
                    <p><?= $ui->createComboData($arrParameter[38], $queryDir, "id", "description", "inp[dir_id]", $r[dir_id], "onchange=\"getSub('dir_id', 'div_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData($arrParameter[39], $queryDiv, "id", "description", "inp[div_id]", $r[div_id], "onchange=\"getSub('div_id', 'dept_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData($arrParameter[40], $queryDept, "id", "description", "inp[dept_id]", $r[dept_id], "onchange=\"getSub('dept_id', 'unit_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData($arrParameter[41], $queryUnit, "id", "description", "inp[unit_id]", $r[unit_id], "", "", "t", "t", "t") ?></p>
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
                            <p><?= $ui->createComboData("Status Pegawai", $queryCat, "id", "description", "inp[cat]", $r[cat], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Status Aktif", $queryStatus, "id", "description", "inp[status]", $r[status], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Mulai Bekerja", "inp[join_date]", getTanggal($r[join_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Selesai Bekerja", "inp[leave_date]", getTanggal($r[leave_date]), "", "t", "", "", "", "", "t") ?></p>
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
            /*border: 1px solid #03F;*/
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
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParam, $arrParameter;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S09', 'X03','X04', 'X05') and statusData = 't'");
    $arrKode = arrayQuery("select kodeMaster, kodeData from mst_data where kodeCategory = 'S05' and statusData = 't'");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small" onclick="showFilter()" />
                    <input type="button" style="display:none" id="hFilter" value="-" class="btn btn_search btn-small" onclick="hideFilter()" />
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <br clear="all" />
            <fieldset id="form_filter" style="display:none">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createComboData("Lokasi", $queryLokasi, "id", "description", "par[idLokasi]", $par[idLokasi], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryPangkat, "id", "description", "par[idPangkat]", $par[idPangkat], "", "", "t", "", "t") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createComboData("Group Process", $queryGroup, "id", "description", "par[idGroup]", $par[idGroup], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Jabatan", $queryJabatan, "id", "description", "par[idJabatan]", $par[idJabatan], "", "", "t", "", "t") ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">NAMA</th>
                    <th width="80">NPP</th>
                    <th width="150">Jabatan</th>
                    <th width="150">Rank</th>
                    <th width="150">Unit Kerja</th>
                    <th width="80">Tgl. Lahir</th>
                    <th width="80">Tgl. Masuk</th>
                    <th width="80">Masa Kerja</th>
                    <th width="50">Detail</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $filter = "WHERE t1.status !='535' AND location IN($areaCheck)";
                if(!empty($arrParam[$s]))
                        $filter.=" and t1.status = '".$arrKode[$arrParam[$s]]."'";
                    if (!empty($par[idLokasi]))
                        $filter .= " and t2.location = '$par[idLokasi]'";
                    if (!empty($par[idGroup]))
                        $filter .= " and t2.proses_id = '$par[idGroup]'";
                    if (!empty($par[idPangkat]))
                        $filter .= " and t2.rank = '$par[idPangkat]'";
                    if (!empty($par[idJabatan]))
                        $filter .= " and t2.pos_name = '$par[idJabatan]'";
                    $sql = "SELECT t1.id, t1.name, t1.reg_no, t1.birth_date, t1.join_date, t2.pos_name, t2.rank, t2.div_id, replace(
                        case when coalesce(leave_date,NULL) IS NULL THEN
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
                        when leave_date = '0000-00-00' THEN
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
                        ELSE
                        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
                        END,' 0 bln','') masaKerja from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
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
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td align="center"><?= $r[reg_no] ?></td>
                        <td><?= $r[pos_name] ?></td>
                        <td><?= $arrMaster[$r[rank]] ?></td>
                        <td><?= $arrMaster[$r[div_id]] ?></td>
                        <td align="center"><?= getTanggal($r[birth_date]) ?></td>
                        <td align="center"><?= getTanggal($r[join_date]) ?></td>
                        <td align="right"><?= $r[masaKerja] ?></td>
                        <td align="center"><a href="?c=3&p=8&m=282&s=292&empid=<?= $r[id] ?>" title="Detail Data" class="detail"><span>Detail</span></a></td>
                        <td align="center"><?= $control ?></td>
                    </tr>
                <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <script>
        function showFilter() {
            jQuery('#form_filter').show('fast');
            jQuery('#sFilter').hide();
            jQuery('#hFilter').show();
        }

        function hideFilter() {
            jQuery('#form_filter').hide('fast');
            jQuery('#sFilter').show();
            jQuery('#hFilter').hide();
        }
    </script>
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
    global $s, $arrTitle, $fExport, $arrUrutan;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S09', 'X03','X04', 'X05') and statusData = 't'");
    $arrKode = arrayQuery("select urutanData, kodeData from mst_data where kodeCategory = 'S05' and statusData = 't'");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no",  "nama", "nik", "jabatan", "rank","unit kerja", "tanggal lahir", "tanggal masuk", "masa kerja");

    $filter = "WHERE t1.status = '" . $arrKode[$arrUrutan[$s] + 1] . "' AND location IN($areaCheck)";
    $sql = "SELECT t1.id, t1.name, t1.reg_no, t1.birth_date, t1.join_date, t2.pos_name, t2.rank, t2.div_id,replace(
        case when coalesce(leave_date,NULL) IS NULL THEN
        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
        when leave_date = '0000-00-00' THEN
        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
        ELSE
        CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
        END,' 0 bln','') masaKerja from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[rank]] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            getTanggal($r[birth_date]) . "\t left",
            getTanggal($r[join_date]) . "\t left",
            $r[masaKerja] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);
}

function getContent($par)
{
    global $menuAccess, $s, $_submit, $m;
    switch ($par[mode]) {
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
        case "subData":
            $text = subData();
            break;
        default:
            $text = lihat();
            break;
    }

    return $text;
}
?>
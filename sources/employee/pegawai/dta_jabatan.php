<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cUser : $_SESSION['curr_emp_id'];

function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]' order by namaData");

    return implode("\n", $data);
}

function tambah()
{
    global $par, $inp, $cUser;

    repField();

    $id = getLastId("emp_phist", "id");

    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO emp_phist set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '$inp[status]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    if ($inp[status] == 1) {
        $sql = "UPDATE emp_phist set status = '0' WHERE parent_id = '" . $_SESSION['curr_emp_id'] . "' AND id !='$id'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $par, $inp, $cUser;

    repField();

    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "UPDATE emp_phist set pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '$inp[status]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$par[id]'";
    db($sql);

    if ($inp[status] == 1) {
        $sql = "UPDATE emp_phist set status = '0' WHERE parent_id = '" . $_SESSION['curr_emp_id'] . "' AND id !='$par[id]'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'S13'");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php include './tmpl/emp_header_basic.php'; ?>
        <div id="pos_r">
            <?php if (isset($menuAccess[$s]["add"])) ?><a href="?par[mode]=add<?= getPar($par, "mode") ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
        </div>
        <br clear="all" />

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Posisi</th>
                    <th width="100">Pangkat</th>
                    <th width="100">Grade</th>
                    <th width="100">Periode</th>
                    <th width="100">Lokasi Kerja</th>
                    <th width="150">Departemen</th>
                    <th width="50">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT id, pos_name, rank, grade, year(start_date) as tahunMulai, year(end_date) as tahunSelesai, location, dept_id, status FROM emp_phist WHERE parent_id = '$_SESSION[curr_emp_id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[status] = $r[status] == 1 ? "<img src=\"styles/images/t.png\">" : "<img src=\"styles/images/f.png\">";
                        $r[tahunSelesai] = empty($r[tahunSelesai]) ? "current" : $r[tahunSelesai];
                        $r[periode] = $r[tahunMulai] . " - " . $r[tahunSelesai];
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "<td align=\"center\">";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                            $control .= "</td>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[pos_name] ?></td>
                        <td><?= $arrMaster[$r[rank]] ?></td>
                        <td><?= $arrMaster[$r[grade]] ?></td>
                        <td align="center"><?= $r[periode] ?></td>
                        <td><?= $arrMaster[$r[location]] ?></td>
                        <td><?= $arrMaster[$r[dept_id]] ?></td>
                        <td align="center"><?= $r[status] ?></td>
                        <?= $control ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}

function form()
{
    global $s, $par, $arrTitle, $arrParameter, $ui;

    $sql = "select * from emp_phist where id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $r[status] = empty($r[status]) && $r[status] != '0' ? 1 : $r[status];

    $queryProv = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S02' order by namaData";
    $queryCityId = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S03' and kodeInduk = '$r[prov_id]' order by namaData";
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

    setValidation("is_null", "inp[pos_name]", "anda harus mengisi Jabatan pada tab Posisi");
    setValidation("is_null", "inp[rank]", "anda harus mengisi Pangkat pada tab Posisi");
    setValidation("is_null", "inp[location]", "anda harus mengisi Lokasi Kerja pada tab Posisi");
    setValidation("is_null", "inp[start_date]", "anda harus mengisi Mulai pada tab Posisi");
    setValidation("is_null", "inp[dir_id]", "anda harus mengisi Perusahaan pada tab Posisi > Organisasi");
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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>" />
            </p>
            <fieldset>
                <legend>DATA POSISI</legend>
                <table style="width:100%">
                    <tr>
                        <td style="width:50%">
                            <p><?= $ui->createField("Posisi", "inp[pos_name]", $r[pos_name], "t", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryRank, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                            <p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no], "", "t") ?></p>
                            <p><?= $ui->createField("Tanggal Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "t", "", "", "", "", "t") ?></p>
                            <p><?= $ui->createField("Keterangan", "inp[remark]", $r[remark], "", "t") ?></p>
                            <p><?= $ui->createRadio("Status", "inp[status]", array("1" => "Aktif", "0" => "Tidak Aktif"), $r[status], "t") ?></p>
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
                    <p><?= $ui->createComboData($arrParameter[41], $queryUnit, "id", "description", "inp[unit_id]", $r[unit_id], "", "", "t", "", "t") ?></p>
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
            </fieldset>
            <style>
                .chosen-container {
                    min-width: 250px;
                }
            </style>
        </form>
    </div>
<?php
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;
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
<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/emp/";
$fExport = "files/export/";
$fLog = "files/log/logJabatan.log";

ini_set('precision', '15');

function setFile()
{
    global $fFile, $fLog, $cID;

    if (!in_array(strtolower(substr($_FILES[fileData][name], -3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name], -4)), array("xlsx"))) {
        return "file harus dalam format .xls atau .xlsx";
    } else {
        $fileUpload = $_FILES[fileData][tmp_name];
        $fileUpload_name = $_FILES[fileData][name];
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

    $arrLocation = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory = 'S06'");
    $arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory != 'S06'");
    $arrPegawai = arrayQuery("select lower(trim(name)), id from emp");
    $arrJenis = arrayQuery("select lower(trim(namaJenis)), idJenis from pay_jenis");
    $arrShift = arrayQuery("SELECT lower(trim(namaShift)), idShift FROM dta_shift");

    $result = $par[rowData] . ". ";
    if ($par[rowData] <= $highestRow) {
        $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':AE' . $par[rowData], NULL, TRUE, TRUE);
        $dta = $rowData[0];
        $tRow = 6;

        if (!in_array(trim(strtolower($dta[1])), array("", "ID"))) {
            $parentId = getField("select id from emp where reg_no = '" . $dta[1] . "'");

            $fileName = fopen($fLog, "a+");

            $pos_name = $dta[4];
            $rank = $arrMaster[trim(strtolower($dta[5]))];
            $grade = $arrMaster[trim(strtolower($dta[6]))];
            $sk_no = $dta[7];
            $sk_date = implode('-', array_reverse(explode('/', $dta[8])));
            $start_date = implode('-', array_reverse(explode('/', $dta[9])));
            $end_date = implode('-', array_reverse(explode('/', $dta[10])));
            $location = $arrLocation[trim(strtolower($dta[11]))];
            $pos_name_date = implode('-', array_reverse(explode('/', $dta[12])));
            $rank_date = implode('-', array_reverse(explode('/', $dta[13])));
            $grade_date = implode('-', array_reverse(explode('/', $dta[14])));
            $job_group_date = implode('-', array_reverse(explode('/', $dta[15])));
            $div_id_date = implode('-', array_reverse(explode('/', $dta[16])));

            $dir_id = getField("SELECT kodeData from mst_data where kodeCategory='X03' AND lower(trim(namaData)) = '" . trim(strtolower($dta[17])) . "'");
            $div_id = getField("SELECT t1.kodeData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' and t1.kodeInduk = '$dir_id' AND lower(trim(t1.namaData)) = '" . trim(strtolower($dta[18])) . "'");
            $dept_id = getField("SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk where t3.kodeCategory='X03' and t1.kodeInduk = '$div_id' AND lower(trim(t1.namaData)) = '" . trim(strtolower($dta[19])) . "' ");
            $unit_id = getField("SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X03' and t1.kodeInduk = '$dept_id' AND lower(trim(t1.namaData)) = '" . trim(strtolower($dta[20])) . "'");
            $prov_id = $arrMaster[trim(strtolower($dta[21]))];
            $city_id = $arrMaster[trim(strtolower($dta[22]))];
            $leader_id = $arrPegawai[trim(strtolower($dta[23]))];
            $administration_id = $arrPegawai[trim(strtolower($dta[24]))];
            $payroll_id = $arrJenis[trim(strtolower($dta[25]))];
            $group_id = $arrLocation[trim(strtolower($dta[26]))];
            $proses_id = $arrMaster[trim(strtolower($dta[27]))];
            $shift_id = $arrShift[trim(strtolower($dta[28]))];
            $status = trim(strtolower($dta[29]));
            $remark = $dta[30];
            $statusAktif = $status == "ya" ? 1 : 0;

            if (!empty($start_date)) {
                $exist = getField("SELECT id FROM emp_phist WHERE parent_id = '$parentId' AND start_date = '$start_date' AND grade = '$grade'");

                if ($status == "ya") {
                    db("UPDATE emp_phist set status = '0' WHERE parent_id = '$parentId'");
                }

                if (!$exist) {
                    $id = getLastId("emp_phist", "id");
                    $sql = "INSERT into emp_phist set id = '$id', parent_id = '$parentId', pos_name = '$pos_name', rank = '$rank', grade = '$grade', 
                    sk_no = '$sk_no', sk_date = '$sk_date', start_date = '$start_date', end_date = '$end_date', location = '$location', dir_id = '$dir_id', 
                    div_id = '$div_id', dept_id = '$dept_id', unit_id = '$unit_id', prov_id = '$prov_id', city_id = '$city_id', leader_id = '$leader_id', 
                    administration_id = '$administration_id', payroll_id = '$payroll_id', group_id = '$group_id', proses_id = '$proses_id', shift_id = '$shift_id', 
                    pos_name_date = '$pos_name_date', rank_date = '$rank_date', grade_date = '$grade_date', job_group_date = '$job_group_date', div_id_date = '$div_id_date',
                    remark = '$remark', status = '$statusAktif', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";
                    $result .= $sql;
                    db($sql);

                    fwrite($fileName, "OK : " . $dta[3] . "\t" . $dta[2] . "\t" . $dta[5] . "\r\n");
                } else {
                    $sql = "UPDATE emp_phist SET parent_id = '$parentId', pos_name = '$pos_name', rank = '$rank', grade = '$grade', sk_no = '$sk_no', 
                    sk_date = '$sk_date', start_date = '$start_date', end_date = '$end_date', location = '$location', dir_id = '$dir_id', div_id = '$div_id', 
                    dept_id = '$dept_id', unit_id = '$unit_id', prov_id = '$prov_id', city_id = '$city_id', leader_id = '$leader_id', administration_id = '$administration_id', 
                    payroll_id = '$payroll_id', group_id = '$group_id', proses_id = '$proses_id', shift_id = '$shift_id', 
                    pos_name_date = '$pos_name_date', rank_date = '$rank_date', grade_date = '$grade_date', job_group_date = '$job_group_date', 
                    div_id_date = '$div_id_date', status = '$statusAktif',  remark = '$remark', update_by = 'migrasi', update_date = '" . date('Y-m-d H:i:s') . "' where id = '$exist' ";
                    $result .= $sql;
                    db($sql);

                    fwrite($fileName, "OK : " . $dta[3] . "\t" . $dta[2] . "\t" . $dta[5] . "\r\n");
                }

                fclose($fileName);
                sleep(1);

                $tRow++;
            }
        }

        $rowData = $par[rowData] - 5;
        $highestRow = $highestRow - 5;
        $progresData = getAngka($rowData / $highestRow * 100);

        return $progresData . "\t(" . $progresData . "%) " . getAngka($rowData) . " of " . getAngka($highestRow) . "\t" . $result;
    }
}

function endProses()
{
    global $fLog;

    $fileName = fopen($fLog, "a+");
    fwrite($fileName, "\r\nEND : " . date("Y-m-d H:i:s"));
    fclose($fileName);
    sleep(1);

    return "import data selesai : " . getTanggal(date('Y-m-d'), "t") . ", " . date('H:i');
}

function formUpload()
{
    global $s, $par, $arrTitle;
    ?>
    <div class="centercontent contentpopup">
        <div class="pageheader">
            <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
            <?= getBread(ucwords("import data")) ?>
            <span class="pagedesc">&nbsp;</span>
        </div>
        <div id="contentwrapper" class="contentwrapper">
            <form class="stdform" enctype="multipart/form-data">
                <div id="formInput" class="subcontent">
                    <p>
                        <label class="l-input-small">File</label>
                        <div class="field">
                            <input type="text" id="fileTemp" name="fileTemp" class="input" style="width:290px;" />
                            <div class="fakeupload">
                                <input type="file" id="fileData" name="fileData" class="realupload" size="50" onchange="this.form.fileTemp.value = this.value;" />
                            </div>
                        </div>
                    </p>
                    <p>
                        <div style="float:right; margin-top:10px; margin-right:150px;"><a href="download.php?d=tmpJabatan" class="detil">* download template.xls</a></div>
                    </p>
                </div>
                <div id="prosesImg" align="center" style="display:none; position:absolute; left:50%; top:50%;">
                    <img src="styles/images/loaders/loader6.gif">
                </div>
                <div id="progresBar" class="progress" style="display:none;">
                    <strong>Progress</strong> <span id="progresCnt">(0%) </span>
                    <div class="bar2">
                        <div id="persenBar" class="value orangebar" style="width: 0%; height:20px;"></div>
                    </div>
                </div>
                <span id="progresRes"></span>
                <div id="progresEnd" class="progress" style="margin-top:30px; display:none;">
                    <a href="download.php?d=logJabatan" class="btn btn1 btn_inboxi"><span>Download Result</span></a>
                    <input type="button" class="cancel radius2" style="float:right" value="Close" onclick="window.parent.location='index.php?<?= getPar($par, 'mode') ?>';" />
                </div>
                <br clear="all">
                <p style="position: absolute; right: 20px; top: 10px;">
                    <input type="button" class="btnSubmit radius2" name="btnSimpan" value="Upload" onclick="setProses('<?= getPar($par, 'mode') ?>');" />
                    <input type="button" class="cancel radius2" value="Kembali" onclick="closeBox();" />
                </p>
            </form>
        </div>
    </div>
<?php
}

function getData()
{
    global $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN('S04', 'S09')");

    $sql = "SELECT t1.reg_no, t1.cat, t1.join_date, t2.rank, t2.pos_name FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.id = '$par[idPegawai]'";
    $res = db($sql);
    $r = mysql_fetch_row($res);
    $r[1] = $arrMaster[$r[1]];
    $r[3] = $arrMaster[$r[3]];
    $r[2] = getTanggal($r[2]);

    return json_encode($r);
}

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
    $inp[div_id_date] = setTanggal($inp[div_id_date]);
    $inp[pos_name_date] = setTanggal($inp[pos_name_date]);
    $inp[grade_date] = setTanggal($inp[grade_date]);
    $inp[rank_date] = setTanggal($inp[rank_date]);
    $inp[job_group_date] = setTanggal($inp[job_group_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT INTO emp_phist set id = '$id', parent_id = '$inp[parent_id]', rank_date = '$inp[rank_date]', job_group = '$inp[job_group]', job_group_date = '$inp[job_group_date]', pos_name_date = '$inp[pos_name_date]', grade_date = '$inp[grade_date]', div_id_date = '$inp[div_id_date]',  pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '$inp[status]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    if ($inp[status] == 1) {
        $sql = "UPDATE emp_phist set status = '0' WHERE parent_id = '$inp[parent_id]' AND id !='$id'";
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
    $inp[div_id_date] = setTanggal($inp[div_id_date]);
    $inp[pos_name_date] = setTanggal($inp[pos_name_date]);
    $inp[grade_date] = setTanggal($inp[grade_date]);
    $inp[rank_date] = setTanggal($inp[rank_date]);
    $inp[job_group_date] = setTanggal($inp[job_group_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "UPDATE emp_phist set rank_date = '$inp[rank_date]', job_group = '$inp[job_group]', job_group_date = '$inp[job_group_date]', pos_name_date = '$inp[pos_name_date]', grade_date = '$inp[grade_date]', div_id_date = '$inp[div_id_date]',  pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', group_id = '$inp[group_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', location = '$inp[location]', rank = '$inp[rank]', grade = '$inp[grade]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', remark = '$inp[remark]', status = '$inp[status]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', proses_id = '$inp[proses_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' WHERE id = '$par[id]'";
    db($sql);

    if ($inp[status] == 1) {
        $sql = "UPDATE emp_phist set status = '0' WHERE parent_id = '$inp[parent_id]' AND id !='$par[id]'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}


function hapus()
{
    global $par;

    $sql = "DELETE from emp_phist where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $areaCheck;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data");
    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small" onclick="showFilter()" />
                    <input type="button" style="display:none" id="hFilter" value="-" class="btn btn_search btn-small" onclick="hideFilter()" />
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="#Upload" class="btn btn1 btn_inboxo" onclick="openBox('popup.php?par[mode]=upl<?= getPar($par, "mode") ?>',725,250)"><span>Import Data</span></a>
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
                <?php if (isset($menuAccess[$s]["add"])) ?><a href="?par[mode]=add<?= getPar($par, "mode") ?>" class="btn btn1 btn_document"><span>Tambah Data</span></a>
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
                    <th width="100">ID</th>
                    <th width="*">PEGAWAI</th>
                    <th width="100">NPP</th>
                    <th width="150">Posisi</th>
                    <th width="100">Pangkat</th>
                    <th width="100">Grade</th>
                    <th width="100">Periode</th>
                    <th width="100">Lokasi Kerja</th>
                    <th width="150">Departemen</th>
                    <th width="100">TMT Unit</th>
                    <th width="50">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) echo "<th width=\"50\">Control</th>"; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "WHERE t2.location IN ($areaCheck)";
                    if (!empty($par[filterData]))
                        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t1.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";
                    if (!empty($par[idLokasi]))
                        $filter .= " and t2.location = '$par[idLokasi]'";
                    if (!empty($par[idGroup]))
                        $filter .= " and t2.proses_id = '$par[idGroup]'";
                    if (!empty($par[idPangkat]))
                        $filter .= " and t2.rank = '$par[idPangkat]'";
                    if (!empty($par[idJabatan]))
                        $filter .= " and t2.pos_name = '$par[idJabatan]'";
                    $sql = "SELECT t1.name, t1.reg_no,t1.kode,  t2.id, t2.pos_name, t2.rank, t2.grade, year(t2.start_date) as tahunMulai, year(t2.end_date) as tahunSelesai, t2.location, t2.dept_id, t2.status, t2.div_id_date FROM emp t1 join emp_phist t2 on t1.id = t2.parent_id $filter order by name";
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
                        <td><?= $r[reg_no] ?></td>
                        <td><?= $r[name] ?></td>
                        <td><?= $r[kode] ?></td>
                        <td><?= $r[pos_name] ?></td>
                        <td><?= $arrMaster[$r[rank]] ?></td>
                        <td><?= $arrMaster[$r[grade]] ?></td>
                        <td align="center"><?= $r[periode] ?></td>
                        <td><?= $arrMaster[$r[location]] ?></td>
                        <td><?= $arrMaster[$r[dept_id]] ?></td>
                        <td><?= getTanggal($r[div_id_date]) ?></td>
                        <td align="center"><?= $r[status] ?></td>
                        <?= $control ?>
                    </tr>
                <?php } ?>
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

function form()
{
    global $s, $par, $arrTitle, $arrParameter, $ui;

    $arrMaster = arrayQuery("SELECT kodeData id, namaData description FROM mst_data where kodeCategory IN ('S09', 'S05')");

    $sql = "SELECT t1.*, t2.reg_no, t2.status as statusPegawai, t2.join_date, t2.name as namaPegawai, t2.pic_filename from emp_phist t1 join emp t2 on t1.parent_id = t2.id where t1.id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryPegawai = "SELECT id, name description FROM emp WHERE status = '535'";

    $r[status] = empty($r[status]) && $r[status] != '0' ? 1 : $r[status];

    $queryProv = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S02' order by namaData";
    $queryCityId = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S03' and kodeInduk = '$r[prov_id]' order by namaData";
    $queryRank = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S09' order by namaData";
    $queryGrade = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory = 'S10' and kodeInduk = '$r[rank]' order by namaData";
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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
            </p>
            <fieldset>
                <legend> DATA PEGAWAI </legend>
                <table style="width:100%">
                    <tr>
                        <td rowspan="3" style="width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;">
                            <img alt="<?= $r["reg_no"] ?>" width="100%" height="100px" src="files/emp/pic/<?= ($r["pic_filename"] == "" ? "nophoto.jpg" : $r["pic_filename"]) ?>" class="pasphoto">
                        </td>
                        <td style="width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
                            <?php
                                if ($par[mode] == "add") {
                                    ?>
                                <p><?= $ui->createComboData("Nama Pegawai", $queryPegawai, "id", "description", "inp[parent_id]", $r[parent_id], "onchange=\"getData(this.value,'" . getPar($par, "mode") . "')\"", "t", "t", "t", "") ?></p>
                            <?php
                                } else {
                                    ?>
                                <p><?= $ui->createSpan("Nama Pegawai", $r[namaPegawai], "t") ?></p>
                            <?php
                                }
                                ?>
                            <p><?= $ui->createSpan("Pangkat", $arrMaster[$r[rank]], "pangkat", "t") ?></p>
                            <p><?= $ui->createSpan("Jabatan", $r[pos_name], "jabatan", "t") ?></p>
                        </td>
                        <td style="width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
                            <p><?= $ui->createSpan("NIK", $r[reg_no], "nik") ?></p>
                            <p><?= $ui->createSpan("Status", $arrMaster[$r[status]], "status") ?></p>
                            <p><?= $ui->createSpan("Mulai Kerja", getTanggal($r[join_date]), "tanggal") ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
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
                    <p><?= $ui->createComboData("KANTOR", $queryDir, "id", "description", "inp[dir_id]", $r[dir_id], "onchange=\"getSub('dir_id', 'div_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData("UNIT KERJA", $queryDiv, "id", "description", "inp[div_id]", $r[div_id], "onchange=\"getSub('div_id', 'dept_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData("DEPARTEMEN/CABANG", $queryDept, "id", "description", "inp[dept_id]", $r[dept_id], "onchange=\"getSub('dept_id', 'unit_id', '" . getPar($par, "mode") . "')\"", "", "t", "", "t") ?></p>
                    <p><?= $ui->createComboData("UNIT/KANTOR KAS", $queryUnit, "id", "description", "inp[unit_id]", $r[unit_id], "", "", "t", "", "t") ?></p>
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

function xls()
{
    global $s, $arrTitle, $fExport, $areaCheck, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "id", "pegawai", "npp", "posisi", "pangkat", "grade", "periode", "lokasi kerja", "departemen", "tmt unit", "status");

    $filter = "WHERE t2.location IN ($areaCheck)";
    if (!empty($par[filterData]))
        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t1.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";
    if (!empty($par[idLokasi]))
        $filter .= " and t2.location = '$par[idLokasi]'";
    if (!empty($par[idGroup]))
        $filter .= " and t2.proses_id = '$par[idGroup]'";
    if (!empty($par[idPangkat]))
        $filter .= " and t2.rank = '$par[idPangkat]'";
    if (!empty($par[idJabatan]))
        $filter .= " and t2.pos_name = '$par[idJabatan]'";
    $sql = "SELECT t1.name, t1.reg_no, t1.kode, t2.id, t2.pos_name, t2.rank, t2.grade, year(t2.start_date) as tahunMulai, year(t2.end_date) as tahunSelesai, t2.location, t2.dept_id, t2.status, t2.div_id_date FROM emp t1 join emp_phist t2 on t1.id = t2.parent_id $filter order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[status] = $r[status] == "t" ? "Aktif" : "Tidak Aktif";
        $data[] = array(
            $no . "\t center",
            $r[reg_no] . "\t center",
            $r[name] . "\t left",
            $r[kode] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[rank]] . "\t left",
            $arrMaster[$r[grade]] . "\t left",
            $r[periode] . "\t center",
            $arrMaster[$r[location]] . "\t left",
            $arrMaster[$r[dept_id]] . "\t left",
            getTanggal($r[div_id_date]) . "\t left",
            $r[status] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 12, $field, $data);
}

function getContent($par)
{
    global $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "end":
            if (isset($menuAccess[$s]["add"])) $text = endProses();
            break;
        case "dat":
            if (isset($menuAccess[$s]["add"])) $text = setData();
            break;
        case "tab":
            if (isset($menuAccess[$s]["add"])) $text = setFile();
            break;
        case "upl":
            $text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
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
        case "subData":
            $text = subData();
            break;
        case "getData":
            $text = getData();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
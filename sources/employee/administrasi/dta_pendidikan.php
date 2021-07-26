<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/emp/edu/";
$fExport = "files/export/";
$fLog = "files/logPendidikan.log";

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

    $arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory IN('R11', 'R12', 'R13', 'S03')");

    $result = $par[rowData] . ". ";
    if ($par[rowData] <= $highestRow) {
        $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':L' . $par[rowData], NULL, TRUE, TRUE);
        $dta = $rowData[0];
        $tRow = $par[rowData];

        if (!in_array(trim(strtolower($dta[0])), array("", "NO"))) {
            $reg_no = $objPHPExcel->getActiveSheet()->getCell('B'.$tRow)->getValue();
            $parentId = getField("select id from emp where reg_no = '$reg_no'");

            $fileName = fopen($fLog, "a+");

            $id = getField("SELECT id from emp_edu order by id desc limit 1") + 1;
            $edu_type = $arrMaster[strtolower(trim($dta[4]))];
            $edu_name = $dta[5];
            $edu_city = $arrMaster[strtolower(trim($dta[6]))];
            $edu_year = $dta[7];
            $edu_graduate = $dta[8];
            $edu_fac = $arrMaster[strtolower(trim($dta[9]))];
            $edu_dept = $arrMaster[strtolower(trim($dta[10]))];
            $edu_essay = $dta[10];

            $exist = getField("SELECT id FROM emp_edu WHERE parent_id = '$parentId' AND edu_type = '$edu_type'");
            if(!$exist) {
                $sql = "insert into emp_edu set id = '$id', parent_id = '$parentId', edu_type = '$edu_type', edu_name = '$edu_name', 
                edu_city = '$edu_city', edu_year = '$edu_year', edu_graduate = '$edu_graduate', edu_fac = '$edu_fac', edu_dept = '$edu_dept', 
                edu_essay = '$edu_essay', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";
                db($sql);
            }else {
                $sql = "UPDATE emp_edu set edu_type = '$edu_type', edu_name = '$edu_name', 
                edu_city = '$edu_city', edu_year = '$edu_year', edu_graduate = '$edu_graduate', edu_fac = '$edu_fac', edu_dept = '$edu_dept', 
                edu_essay = '$edu_essay', update_by = 'migrasi', update_date = '" . date('Y-m-d H:i:s') . "' WHERE id = '$exist'";
                db($sql);
            }

            fwrite($fileName, "OK : " . $dta[4] . "\t" . $dta[2] . "\r\n");

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

function endProses()
{
    global $fLog;

    $fileName = fopen($fLog, "a+");
    fwrite($fileName, "\r\nEND : " . date("Y-m-d H:i:s"));
    fclose($fileName);
    sleep(1);

    return "import data selesai : " . getTanggal(date('Y-m-d'), "t") . ", " . date('H:i');
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

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "edu-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_edu where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_edu where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_edu set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_edu where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_edu where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser, $par;

    repField();

    $id = getLastId("emp_edu", "id");
    $inp[filename] = upload($id);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_edu set id = '$id', parent_id = '$inp[parent_id]', edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_graduate = '$inp[edu_graduate]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[pnh_date_start] = setTanggal($inp[pnh_date_start]);
    $inp[pnh_date_end] = setTanggal($inp[pnh_date_end]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_edu set edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_graduate = '$inp[edu_graduate]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
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
                        <div style="float:right; margin-top:10px; margin-right:150px;"><a href="download.php?d=tmpPendidikan" class="detil">* download template.xls</a></div>
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
                    <a href="download.php?d=logPendidikan" class="btn btn1 btn_inboxi"><span>Download Result</span></a>
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

function form()
{
    global $par, $ui, $arrTitle, $arrParameter, $s;

    $arrMaster = arrayQuery("SELECT kodeData id, namaData description FROM mst_data where kodeCategory IN ('S09', 'S05')");

    $sql = "SELECT t1.*, t2.reg_no, t2.status, t2.join_date, t3.pos_name, t3.rank, t2.name as namaPegawai, t2.pic_filename FROM emp_edu t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') WHERE t1.id = '$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $queryPegawai = "SELECT id, name description FROM emp WHERE status = '535'";

    $queryEdu = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R11' and statusData = 't' order by urutanData";
    $queryFac = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R12' and statusData = 't' order by namaData";
    $queryDept = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R13' and statusData = 't' and kodeInduk = '$r[edu_fac]' order by namaData";
    $queryCity = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
    where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData";

    setValidation("is_null", "inp[pnh_no]", "anda harus mengisi Nomor Sertifikat");
    setValidation("is_null", "inp[pnh_subject]", "anda harus mengisi Perihal");
    setValidation("is_null", "inp[pnh_agency]", "anda harus mengisi Lembaga");
    setValidation("is_null", "inp[pnh_year]", "anda harus mengisi Tahun");
    setValidation("is_null", "inp[pnh_type]", "anda harus mengisi Tipe");
    setValidation("is_null", "inp[pnh_date_start]", "anda harus mengisi Mulai");
    setValidation("is_null", "inp[pnh_date_end]", "anda harus mengisi Selesai");
    echo getValidation();

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
            <style>
                .chosen-container {
                    min-width: 260px;
                }
            </style>
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode') ?>'" />
            </p>
            <br clear="all" />
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
            <br clear="all" />
            <fieldset>
                <legend> DATA PERINGATAN </legend>
                <p><?= $ui->createComboData("Tipe", $queryEdu, "id", "description", "inp[edu_type]", $r[edu_type], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Nama Lembaga", "inp[edu_name]", $r[edu_name], "t") ?></p>
                <p><?= $ui->createComboData("Kota", $queryCity, "id", "description", "inp[edu_city]", $r[edu_city], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Tahun Masuk", "inp[edu_year]", $r[edu_year], "", "", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", "4") ?></p>
                <p><?= $ui->createField("Tahun Lulus", "inp[edu_graduate]", $r[edu_graduate], "", "", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", "4") ?></p>
                <p><?= $ui->createField("Judul Skripsi/Tesis", "inp[edu_essay]", $r[edu_essay]) ?></p>
                <p><?= $ui->createComboData("Fakultas", $queryFac, "id", "description", "inp[edu_fac]", $r[edu_fac], "onchange=\"getSub('edu_fac', 'edu_dept', '" . getPar($par, "mode") . "')\"", "", "t") ?></p>
                <p><?= $ui->createComboData("Jurusan", $queryDept, "id", "description", "inp[edu_dept]", $r[edu_dept], "", "", "t") ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empEdu", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
            </fieldset>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $areaCheck, $arrParameter;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R12','R13','S06', 'S03')");
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
                    <th width="100">TINGKATAN</th>
                    <th width="150">NAMA LEMBAGA</th>
                    <th width="150">FAKULTAS</th>
                    <th width="150">JURUSAN</th>
                    <th width="100">KOTA</th>
                    <th width="50">TAHUN</th>
                    <th width="50">FILE</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $filter = "WHERE t3.location IN ($areaCheck)";
                    if (!empty($par[filterData]))
                        $filter .= " and (lower(t2.name) LIKE '%".mysql_escape_string(strtolower($par[filterData]))."%' OR lower(t2.reg_no) LIKE '%".mysql_escape_string(strtolower($par[filterData]))."%')";
                    if (!empty($par[idLokasi]))
                        $filter .= " and t3.location = '$par[idLokasi]'";
                    if (!empty($par[idGroup]))
                        $filter .= " and t3.proses_id = '$par[idGroup]'";
                    if (!empty($par[idPangkat]))
                        $filter .= " and t3.rank = '$par[idPangkat]'";
                    if (!empty($par[idJabatan]))
                        $filter .= " and t3.pos_name = '$par[idJabatan]'";
                    $sql = "SELECT t1.*, t2.name, t2.reg_no, t2.kode FROM emp_edu t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "<td align=\"center\">";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                            $control .= " </td>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td align="center"><?= $r[reg_no] ?></td>
                        <td><?= $r[name] ?></td>
                        <td align="center"><?= $r[kode] ?></td>
                        <td><?= $arrMaster[$r[edu_type]] ?></td>
                        <td><?= $r[edu_name] ?></td>
                        <td><?= $arrMaster[$r[edu_fac]] ?></td>
                        <td><?= $arrMaster[$r[edu_dept]] ?></td>
                        <td><?= $arrMaster[$r[edu_city]] ?></td>
                        <td align="center"><?= $r[edu_graduate] ?></td>
                        <td align="center"><a href="download.php?d=empEdu&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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

function xls()
{
    global $s, $arrTitle, $fExport, $areaCheck, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R12','R13','S06', 'S03')");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no","id", "pegawai", "npp", "tingkatan", "nama lembaga", "fakultas", "jurusan", "kota", "tahun");

    $filter = "WHERE t3.location IN ($areaCheck)";
    if (!empty($par[filterData]))
        $filter .= " and (lower(t2.name) LIKE '%".mysql_escape_string(strtolower($par[filterData]))."%' OR lower(t2.reg_no) LIKE '%".mysql_escape_string(strtolower($par[filterData]))."%')";
    if (!empty($par[idLokasi]))
        $filter .= " and t3.location = '$par[idLokasi]'";
    if (!empty($par[idGroup]))
        $filter .= " and t3.proses_id = '$par[idGroup]'";
    if (!empty($par[idPangkat]))
        $filter .= " and t3.rank = '$par[idPangkat]'";
    if (!empty($par[idJabatan]))
        $filter .= " and t3.pos_name = '$par[idJabatan]'";
    $sql = "SELECT t1.*, t2.name, t2.reg_no, t2.kode FROM emp_edu t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[reg_no] . "\t center",
            $r[name] . "\t left",
            $r[kode] . "\t center",
            $arrMaster[$r[edu_type]] . "\t left",
            $r[edu_name] . "\t left",
            $arrMaster[$r[edu_fac]] . "\t left",
            $arrMaster[$r[edu_dept]] . "\t left",
            $arrMaster[$r[edu_city]] . "\t left",
            $r[edu_year] . "\t right",
        );
    }
    exportXLS($direktori, $namaFile, $judul, 10, $field, $data);
}

function getContent($par)
{
    global $menuAccess, $s, $_submit;
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
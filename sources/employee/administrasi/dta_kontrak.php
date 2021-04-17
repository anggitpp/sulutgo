<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/emp/contract/";

$fExport = "files/export/";
$fLog = "files/log/logAset.log";

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

    $arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory IN ('S21', 'S22')");

    $result = $par[rowData] . ". ";
    if ($par[rowData] <= $highestRow) {
        $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':L' . $par[rowData], NULL, TRUE, TRUE);
        $dta = $rowData[0];
        $tRow = 6;

        if (!in_array(trim(strtolower($dta[1])), array("", "NPP PEGAWAI"))) {
            $parentId = getField("select id from emp where reg_no = '" . $dta[1] . "'");

            $fileName = fopen($fLog, "a+");

            $idAset = getField("SELECT id from emp_asset order by id desc limit 1") + 1;
            $sql = "insert into emp_asset set id = '$idAset', parent_id = '$parentId', ast_name = '" . $dta[3] . "', ast_no = '" . $dta[4] . "', ast_usage = '" . $dta[5] . "', ast_date = '" . implode('-', array_reverse(explode('/', $dta[3]))) . "', ast_cat = '" . $arrMaster[trim(strtolower($dta[7]))] . "', ast_type = '" . $arrMaster[trim(strtolower($dta[8]))] . "', ast_date_start = '" . implode('-', array_reverse(explode('/', $dta[9]))) . "', ast_date_end = '" . implode('-', array_reverse(explode('/', $dta[10]))) . "', ast_jenis = '" . $dta[11] . "', status = 't', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";
            db($sql);

            fwrite($fileName, "OK : " . $dta[3] . "\t" . $dta[2] . "\r\n");

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
        $filename = "contract-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_contract where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $rel_filename = getField("select rel_filename from emp_contract where id='$par[id]'");
    if (file_exists($fFile . $rel_filename) and $rel_filename != "") unlink($fFile . $rel_filename);

    $sql = "update emp_contract set rel_filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $rel_filename = getField("select rel_filename from emp_contract where id='$par[id]'");
    if (file_exists($fFile . $rel_filename) and $rel_filename != "") unlink($fFile . $rel_filename);

    $sql = "delete from emp_contract where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser;

    repField();

    $id = getLastId("emp_contract", "id");
    $inp[filename] = upload($id);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT into emp_contract set id = '$id', parent_id = '$inp[parent_id]', rank = '$inp[rank]', grade = '$inp[grade]', pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_contract set sk_no = '$inp[sk_no]', rank = '$inp[rank]', grade = '$inp[grade]', pos_name = '$inp[pos_name]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('UPDATE DATA BERHASIL');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $arrMaster = arrayQuery("SELECT kodeData id, namaData description FROM mst_data where kodeCategory IN ('S09', 'S05')");

    $sql = "SELECT * FROM emp_contract WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $sql = "SELECT t1.*, t2.reg_no, t2.status as statusPegawai, t2.join_date, t3.pos_name posPegawai, t3.rank rankPegawai, t2.name as namaPegawai, t2.pic_filename FROM emp_contract t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') WHERE t1.id = '$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    $queryPegawai = "SELECT id, name description FROM emp WHERE status = '535'";

    $queryRank = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S09' and statusData = 't' order by namaData";
    $queryGrade = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S10' and kodeInduk = '$r[rank]' and statusData = 't' order by namaData";
    $queryLocation = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S06' and statusData = 't' order by namaData";

    setValidation("is_null", "inp[name]", "anda harus mengisi Nama");
    setValidation("is_null", "inp[birth_place]", "anda harus mengisi Tempat Lahir");
    setValidation("is_null", "inp[rel]", "anda harus mengisi Hubungan");
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
                                <p><?= $ui->createComboData("Nama Pegawai", $queryPegawai, "id", "description", "inp[parent_id]", $r[parent_id], "onchange=\"getData(this.value,'" . getPar($par, "mode") . "')\"", "t", "t", "t", "t") ?></p>
                            <?php
                            } else {
                            ?>
                                <p><?= $ui->createSpan("Nama Pegawai", $r[namaPegawai], "t", "t") ?></p>
                            <?php
                            }
                            ?>
                            <p><?= $ui->createSpan("Pangkat", $arrMaster[$r[rankPegawai]], "pangkat", "t") ?></p>
                            <p><?= $ui->createSpan("Jabatan", $r[posPegawai], "jabatan", "t") ?></p>
                        </td>
                        <td style="width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
                            <p><?= $ui->createSpan("NPP", $r[reg_no], "nik") ?></p>
                            <p><?= $ui->createSpan("Status", $arrMaster[$r[status]], "status") ?></p>
                            <p><?= $ui->createSpan("Mulai Kerja", getTanggal($r[join_date]), "tanggal") ?></p>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br clear="all" />
            <fieldset>
                <legend> DATA KONTRAK </legend>
                <p><?= $ui->createField("Posisi", "inp[pos_name]", $r[pos_name], "t") ?></p>
                <p><?= $ui->createComboData("Pangkat", $queryRank, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "") ?></p>
                <p><?= $ui->createComboData("Grade", $queryGrade, "id", "description", "inp[grade]", $r[grade], "", "", "t") ?></p>
                <p><?= $ui->createField("Nomor SK", "inp[sk_no]", $r[sk_no], "t") ?></p>
                <p><?= $ui->createField("Tanggal SK", "inp[sk_date]", getTanggal($r[sk_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Tanggal Berlaku", "inp[start_date]", getTanggal($r[start_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Berakhir", "inp[end_date]", getTanggal($r[end_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empKontrak", $r[id], "delFile") ?> </p>
                <p><?= $ui->createComboData("Lokasi Kerja", $queryLocation, "id", "description", "inp[location]", $r[location], "", "", "t", "t") ?></p>
<!--                <p>--><?//= $ui->createRadio("Status", "inp[status]", array("t" => "Aktif", "f" => "Tidak Aktif")) ?><!--</p>-->
            </fieldset>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $areaCheck;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'S06'");
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
                    <th width="*">PEGAWAI</th>
                    <th width="100">NPP</th>
                    <th width="100">POSISI</th>
                    <th width="100">NOMOR</th>
                    <th width="100">LOKASI</th>
                    <th width="100">TGL MULAI</th>
                    <th width="100">TGL SELESAI</th>
                    <th width="50">BUKTI</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT t1.*, t2.name, t2.reg_no FROM emp_contract t1 join emp t2 on t1.parent_id = t2.id order by t1.id desc";
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
                        <td><?= $r[reg_no] ?></td>
                        <td><?= $r[pos_name] ?></td>
                        <td><?= $r[sk_no] ?></td>
                        <td><?= $arrMaster[$r[location]] ?></td>
                        <td align="center"><?= getTanggal($r[start_date]) ?></td>
                        <td align="center"><?= getTanggal($r[end_date]) ?></td>
                        <td align="center"><a href="download.php?d=empContract&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
                        <td align="center"><?= $control ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
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
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "delFile":
            if (isset($menuAccess[$s]["edit"])) $text = hapusFile();
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
<?php
ini_set('precision', '15');
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/emp/";
$fExport = "files/export/";
$fLog = "files/log/logJabatan.log";

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

    $arrData = arrayQuery("SELECT id_pegawai, CONCAT_WS(',', total_kgd, total_lumpsum) FROM emp_kgd WHERE tahun = '".($par[tahunData] - 1)."'");

    $result = $par[rowData] . ". ";
    if ($par[rowData] <= $highestRow) {
        $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':H' . $par[rowData], NULL, TRUE, TRUE);
        $dta = $rowData[0];
        $tRow = 7;

        if (!in_array(trim(strtolower($dta[1])), array("", "nama"))) {
            $parentId = getField("select id from emp where reg_no = '" . trim($dta[2]) . "'");

            $fileName = fopen($fLog, "a+");
            if(!empty($parentId)){
                $cekID = getField("SELECT id FROM emp_kgd WHERE id_pegawai = '$parentId'");
                db("DELETE FROM emp_kgd WHERE id = '$cekID' AND tahun = '$par[tahunData]'");

                list($kgd, $lumpsum) = explode(",", $arrData[$parentId]);

                $id = getLastId("emp_kgd","id");
                $totalKGD = $dta[3] + $kgd;
                $totalLumpsum = $dta[4] + $lumpsum;
                $sql = "INSERT INTO emp_kgd SET id = '$id', tahun ='$par[tahunData]', id_pegawai = '$parentId', kgd = '$dta[3]', lumpsum = '$dta[4]', total_kgd = '$totalKGD', total_lumpsum = '$totalLumpsum', keterangan = '$dta[5]', sanksi = '$dta[6]', usulan = '$dta[7]', create_by = 'import', create_date = 'now()'";
                db($sql);
            }else{
                fwrite($fileName, "Error ID tidak terdaftar " . $dta[1]."\r\n");
            }

            fclose($fileName);
            sleep(1);

            $tRow++;
        }

        $rowData = $par[rowData] - 6;
        $highestRow = $highestRow - 6;
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
                    <div style="float:right; margin-top:10px; margin-right:150px;"><a href="download.php?d=tmpKGD" class="detil">* download template.xls</a></div>
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

function tambah()
{
    global $inp, $cUser, $par;

    repField();

    $id = getLastId("emp_kgd", "id");
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_kgd set id = '$id', id_pegawai = '$par[id]', tahun = '$par[tahun]', kgd = '$inp[kgd]', lumpsum = '$inp[lumpsum]',total_kgd = '$inp[total_kgd]', total_lumpsum = '$inp[total_lumpsum]', total_akumulasi = '$inp[total_akumulasi]', keterangan = '$inp[keterangan]',sanksi = '$inp[sanksi]',usulan = '$inp[usulan]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function lihat(){
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" method="post" class="stdform">
            <div style="position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;">
                <?= comboYear("par[tahunData]", $par[tahunData], "5","onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <div id="pos_l" style="float: left">
                <?= comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","- Pilih Lokasi -",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px") ?>
            </div>
            <div id="pos_r" style="float: right">
                <a href="#Upload" class="btn btn1 btn_inboxo" onclick="openBox('popup.php?par[mode]=upl<?= getPar($par, "mode") ?>',725,250)"><span>Import Data</span></a>
                <a href="?par[mode]=xls<?= getPar($par,"mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
            </div>
            <br>
        </form>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dynscroll">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align:middle;min-width:20px">No.</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:150px">NAMA</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">ID</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">Job Group</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">TMT Job Group</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">Pangkat</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">TMT Pangkat</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">Jabatan</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">Status Pegawai</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">TMT Status Pegawai</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:150px">Kantor</th>
                    <th colspan="2" style="vertical-align:middle;min-width:100px">Pengumpulan KGD S/D 2019</th>
                    <th colspan="2" style="vertical-align:middle;min-width:100px">Penilaian KPI 2020</th>
                    <th colspan="3" style="vertical-align:middle;min-width:100px">Rekap KGD & Lumpsum</th>
                    <th colspan="2" style="vertical-align:middle;min-width:100px">Keterangan</th>
                    <th colspan="2" style="vertical-align:middle;min-width:100px">Pengumpulan KGD S/D 2020</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:100px">Usulan</th>
                    <th rowspan="2" style="vertical-align:middle;min-width:30px">Kontrol</th>
                </tr>
                <tr>
                    <th>KGD</th>
                    <th>Lumpsum</th>
                    <th>KGD</th>
                    <th>Lumpsum</th>
                    <th>KGD</th>
                    <th>Lumpsum</th>
                    <th>Akumulasi Lumpsum</th>
                    <th>Naik / Pengangkatan</th>
                    <th>Sanksi</th>
                    <th>KGD</th>
                    <th>Lumpsum</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory IN ('JG', 'S09', 'S04', 'X03')");

                if(!empty($par[idLokasi]))
                    $filter =" AND t2.location = '$par[idLokasi]'";
                $sql = "SELECT t1.id, t1.name, t1.reg_no, t2.job_group, t2.job_group_date, t2.rank, t2.rank_date, t2.pos_name, t1.cat, t1.cat_date, t2.dir_id, t3.kgd, t3.lumpsum, t3.total_kgd, t3.total_lumpsum, t3.total_akumulasi, t3.keterangan, t3.sanksi, t3.usulan, t4.kgd as kgdBefore, t4.lumpsum as lumpsumBefore FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_kgd t3 on (t1.id = t3.id_pegawai AND t3.tahun = '$par[tahunData]') left join emp_kgd t4 on (t1.id = t4.id_pegawai AND t4.tahun = '".($par[tahunData]-1)."') WHERE t1.status = '535' $filter";
                $res = db($sql);
                while ($r = mysql_fetch_assoc($res)){
                    $no++;
                    ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td align="center"><?= $r[reg_no] ?></td>
                        <td><?= $arrMaster[$r[job_group]] ?></td>
                        <td align="center"><?= getTanggal($r[job_group_date]) ?></td>
                        <td><?= $arrMaster[$r[rank]] ?></td>
                        <td align="center"><?= getTanggal($r[rank_date]) ?></td>
                        <td><?= $r[pos_name] ?></td>
                        <td><?= $arrMaster[$r[cat]] ?></td>
                        <td align="center"><?= getTanggal($r[cat_date]) ?></td>
                        <td><?= $arrMaster[$r[dir_id]] ?></td>
                        <td align="right"><?= $r[kgdBefore] ?></td>
                        <td align="right"><?= $r[lumpsumBefore] ?></td>
                        <td align="right"><?= $r[kgd] ?></td>
                        <td align="right"><?= $r[lumpsum] ?></td>
                        <td align="right"><?= $r[total_kgd] ?></td>
                        <td align="right"><?= $r[total_lumpsum] ?></td>
                        <td align="right"><?= $r[total_akumulasi] ?></td>
                        <td><?= $r[keterangan] ?></td>
                        <td><?= $r[sanksi] ?></td>
                        <td align="right"><?= $r[total_kgd] ?></td>
                        <td align="right"><?= $r[total_lumpsum] ?></td>
                        <td><?= $r[usulan] ?></td>
                        <td align="center"><a href="?par[mode]=edit&par[id]=<?= $r[id]. getPar($par, 'mode,id') ?>" title="Edit Data" class="edit" ><span>Edit</span></a></td>
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
        echo "<iframe src=\"download.php?d=exp&f=exp-PENILAIAN KGD LUMPSUM.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT t1.*, t4.kgd as kgdBefore, t4.lumpsum as lumpsumBefore, t2.reg_no, t2.status, t2.join_date, t3.pos_name, t3.rank, t2.name as namaPegawai, t2.pic_filename FROM emp_kgd t1 join emp t2 on t1.id_pegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') left join emp_kgd t4 on (t2.id = t4.id_pegawai AND t4.tahun = '".($par[tahunData]-1)."') WHERE t2.id = '$par[id]' AND t1.tahun = '$par[tahunData]'";
    $res = db($sql);
    $r = mysql_fetch_assoc($res);

    setValidation("is_null", "inp[company_name]", "anda harus mengisi Perusahaan");
    setValidation("is_null", "inp[position]", "anda harus mengisi Jabatan");
    setValidation("is_null", "inp[start_date]", "anda harus mengisi Mulai");
    setValidation("is_null", "inp[end_date]", "anda harus mengisi Selesai");
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
                        <td style="width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
                            <p><?= $ui->createSpan("Nama Pegawai", $r[namaPegawai], "","t") ?></p>
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
                <legend> DATA KGD & LUMPSUM </legend>
                <p><?= $ui->createSpan("KGD ".($par[tahunData]-1)." ", $r[kgdBefore], "kgd2") ?></p>
                <p><?= $ui->createSpan("LUMPSUM ".($par[tahunData]-1)." ", $r[lumpsumBefore], "lumpsum2") ?></p>
                <p><?= $ui->createField("KGD ".$par[tahunData]." ", "inp[kgd]", $r[kgd], "t", "","style=\"width:50px;\"","onkeyup=\"cekPhone(this);\"","2") ?></p>
                <p><?= $ui->createField("LUMPSUM ".$par[tahunData]." ", "inp[lumpsum]", $r[lumpsum], "t", "","style=\"width:50px;\"","onkeyup=\"cekPhone(this);\"","2") ?></p>
                <p><?= $ui->createField("REKAP KGD", "inp[total_kgd]", $r[total_kgd], "", "","style=\"width:50px;\"","","","t") ?></p>
                <p><?= $ui->createField("REKAP LUMPSUM", "inp[total_lumpsum]", $r[total_lumpsum], "", "","style=\"width:50px;\"","","","t") ?></p>
                <p><?= $ui->createField("AKUMULASI/LUMPSUM", "inp[total_akumulasi]", $r[total_akumulasi], "", "","style=\"width:50px;\"","","","t") ?></p>
                <p><?= $ui->createField("Naik/Pengangkatan", "inp[keterangan]", $r[keterangan]) ?></p>
                <p><?= $ui->createField("Sanksi", "inp[sanksi]", $r[sanksi]) ?></p>
                <p><?= $ui->createField("Usulan", "inp[usulan]", $r[usulan]) ?></p>
            </fieldset>
        </form>
    </div>
    <?php
}

function xls()
{

    global $par, $fExport;

    $direktori = $fExport;
    $namaFile = "exp-PENILAIAN KGD LUMPSUM.xls";
    $judul = "PENILAIAN KGD LUMPSUM";

    $field = array("no", "nama", "id", "job group", "tmt job group","pangkat", "tmt pangkat", "jabatan", "status pegawai", "tmt status pegawai", "kantor", "pengumpulan kgd s/d 2019" => array("KGD", "Lumpsum"), "penilaian kpi 2020" => array("KGD", "Lumpsum"), "rekap kgd & lumpsum" => array("KGD", "Lumpsum", "Akumulasi Lumpsum"), "keterangan" => array("naik/pengangkatan", "sanksi"), "pengumpulan kgd s/d 2020" => array("KGD", "Lumpsum"), "usulan");

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory IN ('JG', 'S09', 'S04', 'X03')");

    if(!empty($par[idLokasi]))
        $filter =" AND t2.location = '$par[idLokasi]'";
    $sql = "SELECT t1.id, t1.name, t1.reg_no, t2.job_group, t2.job_group_date, t2.rank, t2.rank_date, t2.pos_name, t1.cat, t1.cat_date, t2.dir_id, t3.kgd, t3.lumpsum, t3.total_kgd, t3.total_lumpsum, t3.total_akumulasi, t3.keterangan, t3.sanksi, t3.usulan, t4.kgd as kgdBefore, t4.lumpsum as lumpsumBefore FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_kgd t3 on (t1.id = t3.id_pegawai AND t3.tahun = '$par[tahunData]') left join emp_kgd t4 on (t1.id = t4.id_pegawai AND t4.tahun = '".($par[tahunData]-1)."') WHERE t1.status = '535' $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] ."\t",
            $r[reg_no] ."\t center",
            $arrMaster[$r[job_group]] ."\t",
            getTanggal($r[job_group_date]) ."\t center",
            $arrMaster[$r[rank]] ."\t",
            getTanggal($r[rank_date]) ."\t center",
            $r[pos_name] ."\t",
            $arrMaster[$r[cat]] ."\t",
            getTanggal($r[cat_date]) ."\t center",
            $arrMaster[$r[dir_id]] ."\t",
            $r[kgdBefore] ."\t right",
            $r[lumpsumBefore] ."\t right",
            $r[kgd] ."\t right",
            $r[lumpsum] ."\t right",
            $r[total_kgd] ."\t right",
            $r[total_lumpsum] ."\t right",
            $r[total_akumulasi]."\t right",
            $r[keterangan] ."\t right",
            $r[sanksi] ."\t right",
            $r[kgd] + $r[kgd_kpi] ."\t right",
            $r[lumpsum] + $r[lumpsum_kpi] ."\t right",
            $r[usulan] ."\t right",
        );
    }
    exportXLS($direktori, $namaFile, $judul, 23, $field, $data);
}

function getContent($par){
    global $db,$s,$_submit,$menuAccess;
    switch($par[mode]){
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
            $text = empty($_submit) ? form() : ubah();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
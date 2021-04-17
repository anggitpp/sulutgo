<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";



$fExport = "files/export/";

$fFile = "files/emp/kontak/";

$fLog = "files/log/logKontak.log";



function subData()

{

    global $par;



    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");



    return implode("\n", $data);

}



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



    $arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory IN('S02', 'S03')");



    $result = $par[rowData] . ". ";

    if ($par[rowData] <= $highestRow) {

        $rowData = $sheet->rangeToArray('A' . $par[rowData] . ':P' . $par[rowData], NULL, TRUE, TRUE);

        $dta = $rowData[0];

        $tRow = 6;



        if (!in_array(trim(strtolower($dta[1])), array("", "ID"))) {

            $parentId = getField("select id from emp where reg_no = '" . $dta[1] . "'");



            $fileName = fopen($fLog, "a+");

            db("delete from emp_contact where parent_id = '$parentId'");

            $idKontak = getLastId("emp_contact", "id");
            $sr_nama = $dta[4];
            $sr_hub = $dta[5];
            $sr_phone = $dta[6];
            $sr_address = $dta[7];
            $sr_prov = $arrMaster[trim(strtolower($dta[8]))];
            $sr_city = $arrMaster[trim(strtolower($dta[9]))];
            $br_nama = $dta[10];
            $br_hub = $dta[11];
            $br_phone = $dta[12];
            $br_address = $dta[13];
            $br_prov = $arrMaster[trim(strtolower($dta[14]))];
            $br_city = $arrMaster[trim(strtolower($dta[15]))];

            $sql = "insert into emp_contact set id = '$idKontak', parent_id = '$parentId', sr_nama = '$sr_nama', sr_hub = '$sr_hub', 
            sr_phone = '$sr_phone', sr_address = '$sr_address', sr_prov = '$sr_prov', sr_city = '$sr_city', br_nama = '$br_nama', br_hub = '$br_hub', 
            br_phone = '$br_phone', br_address = '$br_address', br_prov = '$br_prov', br_city = '$br_city', create_by = 'migrasi', create_date = '" . date('Y-m-d H:i:s') . "'";

            db($sql);



            fwrite($fileName, "SUKSES : NPP " . $dta[1] . "\r\n");



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



    $sql = "SELECT t1.reg_no, t1.cat, t1.join_date, t2.rank, t2.pos_name, t2.parent_id FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.id = '$par[idPegawai]'";

    $res = db($sql);

    $r = mysql_fetch_row($res);

    $r[1] = $arrMaster[$r[1]];

    $r[3] = $arrMaster[$r[3]];

    $r[2] = getTanggal($r[2]);



    return json_encode($r);

}



function ubah()

{

    global $inp, $cUsername, $par;



    db("DELETE FROM emp_contact where parent_id = '$inp[parent_id]'");

    $id = empty($par[id]) ? getLastId("emp_contact", "id") : $par[id];

    $sql = "insert into emp_contact set id = '$id', parent_id = '$inp[parent_id]', sr_nama = '$inp[sr_nama]', sr_hub = '$inp[sr_hub]', sr_phone = '$inp[sr_phone]', sr_address = '$inp[sr_address]', sr_prov = '$inp[sr_prov]', sr_city = '$inp[sr_city]', br_nama = '$inp[br_nama]', br_hub = '$inp[br_hub]', br_phone = '$inp[br_phone]', br_address = '$inp[br_address]', br_prov = '$inp[br_prov]', br_city = '$inp[br_city]', create_by = '$cUsername', create_date = '" . date('Y-m-d H:i:s') . "'";

    db($sql);



    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?" . getPar($par, "mode, id") . "';</script>";

}

// function ubah()
// {
//     global $inp, $cUsername;

//     $cekID = getField("select id from emp_contact where parent_id = '" . $_SESSION['curr_emp_id'] . "'");
//     if ($cekID) {
//         $sql = "update emp_contact set sr_nama = '$inp[sr_nama]', sr_hub = '$inp[sr_hub]', sr_phone = '$inp[sr_phone]', sr_address = '$inp[sr_address]', sr_prov = '$inp[sr_prov]', sr_city = '$inp[sr_city]', br_nama = '$inp[br_nama]', br_hub = '$inp[br_hub]', br_phone = '$inp[br_phone]', br_address = '$inp[br_address]', br_prov = '$inp[br_prov]', br_city = '$inp[br_city]', update_by = '$cUsername', update_date = '" . date('Y-m-d H:i:s') . "' where id = '$cekID' ";
//         db($sql);
//     } else {
//         $id = getLastId("emp_contact", "id");
//         $sql = "insert into emp_contact set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', sr_nama = '$inp[sr_nama]', sr_hub = '$inp[sr_hub]', sr_phone = '$inp[sr_phone]', sr_address = '$inp[sr_address]', sr_prov = '$inp[sr_prov]', sr_city = '$inp[sr_city]', br_nama = '$inp[br_nama]', br_hub = '$inp[br_hub]', br_phone = '$inp[br_phone]', br_address = '$inp[br_address]', br_prov = '$inp[br_prov]', br_city = '$inp[br_city]', create_by = '$cUsername', create_date = '" . date('Y-m-d H:i:s') . "'";
//         db($sql);
//     }

//     echo "<script>alert('DATA BERHASIL DISIMPAN');reloadPage();</script>";
// }

function hapus()

{

    global $par;



    $sql = "delete from emp_contact where id='$par[id]'";

    db($sql);



    echo "<script>window.location='?" . getPar($par, "mode,id") . "';</script>";

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

                        <div style="float:right; margin-top:10px; margin-right:150px;"><a href="download.php?d=tmpKontak" class="detil">* download template.xls</a></div>

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

                    <a href="download.php?d=logKontak" class="btn btn1 btn_inboxi"><span>Download Result</span></a>

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

    global $s, $arrTitle, $par, $ui;



    $arrMaster = arrayQuery("SELECT kodeData id, namaData description FROM mst_data where kodeCategory IN ('S09', 'S05')");



    $sql = "SELECT t1.*, t2.reg_no, t2.status, t2.join_date, t3.pos_name, t3.rank, t2.pic_filename, t2.name as namaPegawai FROM emp_contact t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') WHERE t1.id = '$par[id]'";

    $res = db($sql);

    $r = mysql_fetch_assoc($res);



    $queryPegawai = "SELECT id, name description FROM emp WHERE status = '535'";

    $queryProv = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S02' and statusData = 't' order by namaData";

    $queryKotaSr = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[sr_prov]' and statusData = 't' order by namaData";

    $queryKotaBr = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[br_prov]' and statusData = 't' order by namaData";

    setValidation("is_null", "inp[parent_id]", "Anda Harus Memilih Nama Pegawai");
    echo getValidation();
    ?>



    <div class="pageheader">

        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>

        <?= getBread(ucwords($par[mode] . " data")) ?>

    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">

            <p class="btnSave">

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

                                <p><?= $ui->createComboData("Nama", $queryPegawai, "id", "description", "inp[parent_id]", $r[parent_id], "onchange=\"getData(this.value,'" . getPar($par, "mode") . "')\"", "t", "t", "", "") ?></p>

                            <?php

                                } else {

                                    ?>
                                <input type="hidden" name= "inp[parent_id]" value="<?= $r[parent_id] ?>">
                                <p><?= $ui->createSpan("Nama", $r[namaPegawai], "t") ?></p>

                            <?php

                                }

                                ?>

                            <p><?= $ui->createSpan("Pangkat", $arrMaster[$r[rank]], "pangkat", "t") ?></p>

                            <p><?= $ui->createSpan("Jabatan", $r[pos_name], "jabatan", "t") ?></p>

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

                <legend> DATA KONTAK </legend>

                <table style="width:100%">

                    <tr>

                        <td style="width:50%">

                            <div class="widgetbox">

                                <div class="title">

                                    <h3>SERUMAH</h3>

                                </div>

                            </div>

                            <p><?= $ui->createField("Nama", "inp[sr_nama]", $r[sr_nama], "", "t") ?></p>

                            <p><?= $ui->createField("Hubungan", "inp[sr_hub]", $r[sr_hub], "", "t") ?></p>

                            <p><?= $ui->createField("No. Telp", "inp[sr_phone]", $r[sr_phone], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>

                            <p><?= $ui->createTextArea("Alamat", "inp[sr_address]", $r[sr_address], "", "t") ?></p>

                            <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[sr_prov]", $r[sr_prov], "onchange=\"getSub('sr_prov','sr_city','" . getPar($par, "mode") . "');\"", "", "t", "", "t") ?></p>

                            <p><?= $ui->createComboData("Kota", $queryKotaSr, "id", "description", "inp[sr_city]", $r[sr_city], "", "", "t", "", "t") ?></p>

                        </td>

                        <td style="width:50%">

                            <div class="widgetbox">

                                <div class="title">

                                    <h3>BEDA RUMAH</h3>

                                </div>

                            </div>

                            <p><?= $ui->createField("Nama", "inp[br_nama]", $r[br_nama], "", "t") ?></p>

                            <p><?= $ui->createField("Hubungan", "inp[br_hub]", $r[br_hub], "", "t") ?></p>

                            <p><?= $ui->createField("No. Telp", "inp[br_phone]", $r[br_phone], "", "t", "", "onkeyup=\"cekPhone(this);\"") ?></p>

                            <p><?= $ui->createTextArea("Alamat", "inp[br_address]", $r[br_address], "", "t") ?></p>

                            <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[br_prov]", $r[br_prov], "onchange=\"getSub('br_prov','br_city','" . getPar($par, "mode") . "');\"", "", "t", "", "t") ?></p>

                            <p><?= $ui->createComboData("Kota", $queryKotaBr, "id", "description", "inp[br_city]", $r[br_city], "", "", "t", "", "t") ?></p>

                        </td>

                    </tr>

                </table>

            </fieldset>

        </form>

    </div>

<?php

}



function lihat()

{

    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $areaCheck;



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

                    <th rowspan="2" width="20">No.</th>

                    <th rowspan="2" width="80">ID</th>

                    <th rowspan="2" width="*">PEGAWAI</th>

                    <th rowspan="2" width="80">NPP</th>

                    <th colspan="2" width="200">KONTAK</th>

                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th rowspan="2" width="50">Control</th>

                </tr>

                <tr>

                    <th width="100">SERUMAH</th>

                    <th width="100">BEDA RUMAH</th>

                </tr>

            </thead>

            <tbody>

                <?php

                    $filter = "WHERE t3.location IN ($areaCheck)";

                    if (!empty($par[filterData]))

                        $filter .= " and (lower(t2.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t2.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";

                    if (!empty($par[idLokasi]))

                        $filter .= " and t3.location = '$par[idLokasi]'";

                    if (!empty($par[idGroup]))

                        $filter .= " and t3.proses_id = '$par[idGroup]'";

                    if (!empty($par[idPangkat]))

                        $filter .= " and t3.rank = '$par[idPangkat]'";

                    if (!empty($par[idJabatan]))

                        $filter .= " and t3.pos_name = '$par[idJabatan]'";

                    $sql = "SELECT t1.id, t1.sr_phone, t1.br_phone, t2.name, t2.reg_no, t2.kode FROM emp_contact t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";

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

                        <td><?= $r[sr_phone] ?></td>

                        <td><?= $r[br_phone] ?></td>

                        <?= $control ?>

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

    global $s, $arrTitle, $fExport, $areaCheck, $par;



    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S12', 'S03') and statusData = 't'");



    $direktori = $fExport;

    $namaFile = "exp-" . $arrTitle[$s] . ".xls";

    $judul = "" . $arrTitle[$s] . "";



    $field = array("no", "pegawai", "nik", "nama", "hubungan", "gender", "tempat lahir", "tanggal lahir", "umur");



    $filter = "WHERE t3.location IN ($areaCheck)";

    if (!empty($par[filterData]))

        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t2.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t2.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";

    if (!empty($par[idLokasi]))

        $filter .= " and t3.location = '$par[idLokasi]'";

    if (!empty($par[idGroup]))

        $filter .= " and t3.proses_id = '$par[idGroup]'";

    if (!empty($par[idPangkat]))

        $filter .= " and t3.rank = '$par[idPangkat]'";

    if (!empty($par[idJabatan]))

        $filter .= " and t3.pos_name = '$par[idJabatan]'";

    $sql = "SELECT t1.id, t1.name as namaKeluarga, t1.rel, t1.rel_filename, t1.gender, t1.birth_place, t1.birth_date, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn') as umur, t2.name, t2.reg_no FROM emp_family t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";

    $res = db($sql);

    $no = 0;

    while ($r = mysql_fetch_assoc($res)) {

        $no++;

        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";

        $data[] = array(

            $no . "\t center",

            $r[name] . "\t left",

            $r[reg_no] . "\t center",

            $r[namaKeluarga] . "\t left",

            $arrMaster[$r[rel]] . "\t left",

            $r[gender] . "\t left",

            $arrMaster[$r[birth_date]] . "\t left",

            getTanggal($r[birth_date]) . "\t left",

            $r[umur] . "\t left"

        );

    }

    exportXLS($direktori, $namaFile, $judul, 9, $field, $data);

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

            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : ubah();

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
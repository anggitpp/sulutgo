<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cID : $_SESSION['curr_emp_id'];

$fFile = "files/emp/edu/";

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
    global $inp, $cID;

    repField();

    $id = getLastId("emp_edu", "id");
    $inp[edu_date] = setTanggal($inp[edu_date]);
    $inp[filename] = upload($id);
    $inp[create_by] = $cID;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_edu set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_graduate = '$inp[edu_graduate]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cID;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[edu_date] = setTanggal($inp[edu_date]);
    $inp[update_by] = $cID;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_edu set edu_type = '$inp[edu_type]', edu_name = '$inp[edu_name]', edu_city = '$inp[edu_city]', edu_year = '$inp[edu_year]', edu_graduate = '$inp[edu_graduate]', edu_fac = '$inp[edu_fac]', edu_dept = '$inp[edu_dept]', edu_essay = '$inp[edu_essay]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $arrParameter, $s;

    $sql = "SELECT * FROM emp_edu WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryEdu = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R11' and statusData = 't' order by urutanData";
    $queryFac = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R12' and statusData = 't' order by namaData";
    $queryDept = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'R13' and statusData = 't' and kodeInduk = '$r[edu_fac]' order by namaData";
    $queryCity = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
    where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData";

    setValidation("is_null", "inp[edu_type]", "anda harus mengisi Tipe Pendidikan");
    setValidation("is_null", "inp[edu_name]", "anda harus mengisi Nama Lembaga");
    setValidation("is_null", "inp[edu_city]", "anda harus mengisi Kota");
    setValidation("is_null", "inp[edu_year]", "anda harus mengisi Tahun Lulus");
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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode") ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA PENDIDIKAN </legend>
                <p><?= $ui->createComboData("Tipe", $queryEdu, "id", "description", "inp[edu_type]", $r[edu_type], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Nama Lembaga", "inp[edu_name]", $r[edu_name], "t") ?></p>
                <p><?= $ui->createComboData("Kota", $queryCity, "id", "description", "inp[edu_city]", $r[edu_city], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Tahun Masuk", "inp[edu_year]", $r[edu_year], "t", "", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", "4") ?></p>
                <p><?= $ui->createField("Tahun Lulus", "inp[edu_graduate]", $r[edu_graduate], "t", "", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", "4") ?></p>
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
    global $s, $par, $arrTitle, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('R11', 'R12','R13','S06')");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php include './tmpl/emp_header_basic.php'; ?>
        <div id="pos_r">
            <?php if (isset($menuAccess[$s]["add"])) ?><a href="#" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode') ?>',700,600)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
        </div>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="100">TINGKATAN</th>
                    <th width="*">NAMA LEMBAGA</th>
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
                    $sql = "SELECT * FROM emp_edu WHERE parent_id = '$_SESSION[curr_emp_id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "', 700, 600)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $arrMaster[$r[edu_type]] ?></td>
                        <td><?= $r[edu_name] ?></td>
                        <td><?= $arrMaster[$r[edu_fac]] ?></td>
                        <td><?= $arrMaster[$r[edu_dept]] ?></td>
                        <td><?= $arrMaster[$r[edu_city]] ?></td>
                        <td align="center"><?= $r[edu_year] ?></td>
                        <td align="center"><a href="download.php?d=empEdu&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
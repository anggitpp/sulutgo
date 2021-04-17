<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cID : $_SESSION['curr_emp_id'];

$fFile = "files/emp/work/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "pwork-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_pwork where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_pwork where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_pwork set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_pwork where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_pwork where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cID;

    repField();

    $id = getLastId("emp_pwork", "id");
    $inp[filename] = upload($id);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[create_by] = $cID;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_pwork set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', company_name = '$inp[company_name]', position = '$inp[position]', division = '$inp[division]', dept = '$inp[dept]', city = '$inp[city]', start_date = '$inp[start_date]', job_desc = '$inp[job_desc]', end_date = '$inp[end_date]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cID;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[update_by] = $cID;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_pwork set company_name = '$inp[company_name]', position = '$inp[position]', division = '$inp[division]', dept = '$inp[dept]', city = '$inp[city]', start_date = '$inp[start_date]', job_desc = '$inp[job_desc]', end_date = '$inp[end_date]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_pwork WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode") ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA PEKERJAAN </legend>
                <p><?= $ui->createField("Perusahaan", "inp[company_name]", $r[company_name], "t") ?></p>
                <p><?= $ui->createField("Jabatan", "inp[position]", $r[position], "t") ?></p>
                <p><?= $ui->createField("Mulai", "inp[start_date]", getTanggal($r[start_date]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Selesai", "inp[end_date]", getTanggal($r[end_date]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Divisi", "inp[division]", $r[division]) ?></p>
                <p><?= $ui->createField("Bagian", "inp[dept]", $r[dept]) ?></p>
                <p><?= $ui->createField("Lokasi/Kota", "inp[city]", $r[city]) ?></p>
                <p><?= $ui->createTextArea("Tugas", "inp[job_desc]", $r[job_desc]) ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empWork", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
            </fieldset>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

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
                    <th width="*">PERUSAHAAN</th>
                    <th width="150">JABATAN</th>
                    <th width="150">Bagian</th>
                    <th width="100">MULAI</th>
                    <th width="100">selesai</th>
                    <th width="50">FILE</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM emp_pwork WHERE parent_id = '$_SESSION[curr_emp_id]'";
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
                        <td><?= $r[company_name] ?></td>
                        <td><?= $r[position] ?></td>
                        <td><?= $r[dept] ?></td>
                        <td align="center"><?= getTanggal($r[start_date]) ?></td>
                        <td align="center"><?= getTanggal($r[end_date]) ?></td>
                        <td align="center"><a href="download.php?d=empWork&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
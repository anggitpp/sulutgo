<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cUser : $_SESSION['curr_emp_id'];

$fFile = "files/emp/file/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "file-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_file where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_file where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_file set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_file where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_file where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser;

    repField();

    $id = getLastId("emp_file", "id");
    $inp[filename] = upload($id);
    $inp[file_start_date] = setTanggal($inp[file_start_date]);
    $inp[file_end_date] = setTanggal($inp[file_end_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_file set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', file_no = '$inp[file_no]', file_name = '$inp[file_name]', file_type = '$inp[file_type]', file_start_date = '$inp[file_start_date]', file_end_date = '$inp[file_end_date]', filename = '$inp[filename]', remark = '$inp[remark]',  status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[file_start_date] = setTanggal($inp[file_start_date]);
    $inp[file_end_date] = setTanggal($inp[file_end_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_file set file_no = '$inp[file_no]', file_name = '$inp[file_name]', file_type = '$inp[file_type]', file_start_date = '$inp[file_start_date]', file_end_date = '$inp[file_end_date]', filename = '$inp[filename]', remark = '$inp[remark]',  status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_file WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryJenis = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'JF' and statusData = 't' order by namaData";

    setValidation("is_null", "inp[file_name]", "anda harus mengisi Nama");
    setValidation("is_null", "inp[file_type]", "anda harus mengisi Jenis");
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
                <legend> DATA FILE </legend>
                <p><?= $ui->createField("Nomor", "inp[file_no]", $r[file_no]) ?></p>
                <p><?= $ui->createField("Nama", "inp[file_name]", $r[file_name], "t") ?></p>
                <p><?= $ui->createComboData("Jenis", $queryJenis, "id", "description", "inp[file_type]", $r[file_type], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Tanggal Berlaku", "inp[file_start_date]", getTanggal($r[file_start_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Tanggal Berakhir", "inp[file_end_date]", getTanggal($r[file_end_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empfile", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createRadio("Status", "inp[status]", array("t" => "Aktif", "f" => "Tidak Aktif"), $r[status]) ?></p>
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
            <?php if (isset($menuAccess[$s]["add"])) ?><a href="#" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode') ?>',700,500)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
        </div>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">NOMOR</th>
                    <th width="100">NAMA</th>
                    <th width="150">JENIS</th>
                    <th width="100">MULAI</th>
                    <th width="100">SELESAI</th>
                    <th width="50">FILE</th>
                    <th width="50">STATUS</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM emp_file WHERE parent_id = '$_SESSION[curr_emp_id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[status] = $r[status] == "t" ? "<img src=\"styles/images/t.png\">" : "<img src=\"styles/images/f.png\">";
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "', 700, 500)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[file_no] ?></td>
                        <td><?= $r[file_name] ?></td>
                        <td><?= $arrMaster[$r[file_type]] ?></td>
                        <td align="center"><?= getTanggal($r[file_start_date]) ?></td>
                        <td align="center"><?= getTanggal($r[file_end_date]) ?></td>
                        <td align="center"><a href="download.php?d=empFile&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
                        <td align="center"><?= $r[status] ?></td>
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
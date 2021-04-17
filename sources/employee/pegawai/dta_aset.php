<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cUser : $_SESSION['curr_emp_id'];

$fFile = "files/emp/asset/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "asset-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_asset where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_asset where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_asset set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_asset where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_asset where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser;

    repField();

    $id = getLastId("emp_asset", "id");
    $inp[filename] = upload($id);
    $inp[ast_date] = setTanggal($inp[ast_date]);
    $inp[ast_date_start] = setTanggal($inp[ast_date_start]);
    $inp[ast_date_end] = setTanggal($inp[ast_date_end]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_asset set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', ast_name = '$inp[ast_name]', ast_no = '$inp[ast_no]', ast_usage = '$inp[ast_usage]', ast_date = '$inp[ast_date]', ast_cat = '$inp[ast_cat]', ast_type = '$inp[ast_type]', ast_date_start = '$inp[ast_date_start]', ast_date_end = '$inp[ast_date_end]', ast_jenis = '$inp[ast_jenis]', filename = '$inp[filename]', remark = '$inp[remark]',  status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[ast_date] = setTanggal($inp[ast_date]);
    $inp[ast_date_start] = setTanggal($inp[ast_date_start]);
    $inp[ast_date_end] = setTanggal($inp[ast_date_end]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_asset set ast_name = '$inp[ast_name]', ast_no = '$inp[ast_no]', ast_usage = '$inp[ast_usage]', ast_date = '$inp[ast_date]', ast_cat = '$inp[ast_cat]', ast_type = '$inp[ast_type]', ast_date_start = '$inp[ast_date_start]', ast_date_end = '$inp[ast_date_end]', ast_jenis = '$inp[ast_jenis]', filename = '$inp[filename]', remark = '$inp[remark]',  status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_asset WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryCat = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S21' and statusData = 't' order by namaData";
    $queryType = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S22' and statusData = 't' order by namaData";

    setValidation("is_null", "inp[ast_name]", "anda harus mengisi Nama Aset");
    setValidation("is_null", "inp[ast_no]", "anda harus mengisi Nomor Seri");
    setValidation("is_null", "inp[ast_usage]", "anda harus mengisi Peruntukan");
    setValidation("is_null", "inp[ast_date]", "anda harus mengisi Diberikan Sejak");
    setValidation("is_null", "inp[ast_cat]", "anda harus mengisi Kategori");
    setValidation("is_null", "inp[ast_type]", "anda harus mengisi Tipe");
    setValidation("is_null", "inp[ast_date_start]", "anda harus mengisi Tanggal Berlaku");
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
                <legend> DATA ASET </legend>
                <p><?= $ui->createField("Nama Aset", "inp[ast_name]", $r[ast_name], "t") ?></p>
                <p><?= $ui->createField("Nomor Seri", "inp[ast_no]", $r[ast_no], "t") ?></p>
                <p><?= $ui->createField("Peruntukan", "inp[ast_usage]", $r[ast_usage], "t") ?></p>
                <p><?= $ui->createField("Diberikan Sejak", "inp[ast_date]", getTanggal($r[ast_date]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createComboData("Kategori", $queryCat, "id", "description", "inp[ast_cat]", $r[ast_cat], "", "", "t", "t") ?></p>
                <p><?= $ui->createComboData("Tipe", $queryType, "id", "description", "inp[ast_type]", $r[ast_type], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Tanggal Berlaku", "inp[ast_date_start]", getTanggal($r[ast_date_start]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Tanggal Berakhir", "inp[ast_date_end]", getTanggal($r[ast_date_end]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Jenis", "inp[ast_jenis]", $r[ast_jenis], "t") ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empAsset", $r[id], "delFile") ?> </p>
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

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory IN ('S21', 'S22')");

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
                    <th width="*">ASET</th>
                    <th width="100">NO SERI</th>
                    <th width="150">KATEGORI</th>
                    <th width="150">TIPE</th>
                    <th width="100">TANGGAL</th>
                    <th width="50">FILE</th>
                    <th width="50">STATUS</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM emp_asset WHERE parent_id = '$_SESSION[curr_emp_id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[status] = $r[status] == "t" ? "<img src=\"styles/images/t.png\">" : "<img src=\"styles/images/f.png\">";
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
                        <td><?= $r[ast_name] ?></td>
                        <td><?= $r[ast_no] ?></td>
                        <td><?= $arrMaster[$r[ast_cat]] ?></td>
                        <td><?= $arrMaster[$r[ast_type]] ?></td>
                        <td align="center"><?= getTanggal($r[ast_date]) ?></td>
                        <td align="center"><a href="download.php?d=empAsset&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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
<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/emp/pengumuman/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "pengumuman-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_pengumuman where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_pengumuman where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_pengumuman set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_pengumuman where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_pengumuman where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser;

    repField();

    $id = getLastId("emp_pengumuman", "id");
    $inp[filename] = upload($id);
    $inp[tanggal] = setTanggal($inp[tanggal]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_pengumuman set id = '$id', judul = '$inp[judul]', tanggal = '$inp[tanggal]', sumber = '$inp[sumber]', resume = '$inp[resume]', detail = '$inp[detail]', filename = '$inp[filename]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[tanggal] = setTanggal($inp[tanggal]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_pengumuman set judul = '$inp[judul]', tanggal = '$inp[tanggal]', sumber = '$inp[sumber]', resume = '$inp[resume]', detail = '$inp[detail]', filename = '$inp[filename]', status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_pengumuman WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[judul]", "anda harus mengisi Judul");
    setValidation("is_null", "inp[tanggal]", "anda harus mengisi Tanggal");
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
                <legend> DATA PENGUMUMAN </legend>
                <p><?= $ui->createField("Judul", "inp[judul]", $r[judul], "t") ?></p>
                <p><?= $ui->createField("Tanggal", "inp[tanggal]", getTanggal($r[tanggal]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Sumber", "inp[sumber]", $r[sumber]) ?></p>
                <p><?= $ui->createTextArea("Ringkasan", "inp[resume]", $r[resume]) ?></p>
                <p><?= $ui->createTextArea("Detail", "inp[detail]", $r[detail]) ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empPengumuman", $r[id], "delFile") ?> </p>
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
        <form action="" method="post" id="form" class="stdform">
            <div id="pos_r">
                <?php if (isset($menuAccess[$s]["add"])) ?><a href="#" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode') ?>',600,400)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
            </div>
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" name="par[filterData]" value="<?= $par[filterData] ?>" placeholder="Search.." style="width:200px;" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                </p>
            </div>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Judul</th>
                    <th width="100">Tanggal</th>
                    <th width="50">File</th>
                    <th width="50">Status</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($par[filterData]))
                        $filter = "WHERE judul LIKE '%$par[filterData]%'";
                    $sql = "SELECT * FROM emp_pengumuman $filter order by id";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        $r[status] = $r[status] == "t" ? "<img src=\"styles/images/t.png\">" : "<img src=\"styles/images/f.png\">";
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "', 600, 400)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[judul] ?></td>
                        <td align="center"><?= getTanggal($r[tanggal]) ?></td>
                        <td align="center"><a href="download.php?d=empPengumuman&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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
<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cUser : $_SESSION['curr_emp_id'];

$fFile = "files/emp/family/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["rel_filename"]["tmp_name"];
    $fileUpload_name = $_FILES["rel_filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $rel_filename = "family-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $rel_filename);
    }
    if (empty($rel_filename)) $rel_filename = getField("select rel_filename from emp_family where id='$id'");

    return $rel_filename;
}

function hapusFile()
{
    global $par, $fFile;

    $rel_filename = getField("select rel_filename from emp_family where id='$par[id]'");
    if (file_exists($fFile . $rel_filename) and $rel_filename != "") unlink($fFile . $rel_filename);

    $sql = "update emp_family set rel_filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $rel_filename = getField("select rel_filename from emp_family where id='$par[id]'");
    if (file_exists($fFile . $rel_filename) and $rel_filename != "") unlink($fFile . $rel_filename);

    $sql = "delete from emp_family where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cUser;

    repField();

    $id = getLastId("emp_family", "id");
    $inp[rel_filename] = upload($id);
    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[create_by] = $cUser;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_family set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', name = '$inp[name]', rel = '$inp[rel]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', gender = '$inp[gender]', rel_filename = '$inp[rel_filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cUser;

    repField();

    $inp[rel_filename] = upload($par[id]);
    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[update_by] = $cUser;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_family set name = '$inp[name]', rel = '$inp[rel]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', gender = '$inp[gender]', rel_filename = '$inp[rel_filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $arrParameter, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_family WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryRel = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S12' and statusData = 't' order by namaData";
    $queryBirthPlace = "SELECT t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
    where t2.kodeCategory='" . $arrParameter[3] . "' AND t2.kodeInduk='1' AND t1.kodeCategory='" . $arrParameter[4] . "' order by t2.namaData";

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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode") ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA KELUARGA </legend>
                <p><?= $ui->createField("Nama Lengkap", "inp[name]", $r[name], "t") ?></p>
                <p><?= $ui->createComboData("Hubungan", $queryRel, "id", "description", "inp[rel]", $r[rel], "", "", "t", "t") ?></p>
                <p><?= $ui->createRadio("Jenis Kelamin", "inp[gender]", array("M" => "Laki-Laki", "F" => "Perempuan"), $r[gender]) ?></p>
                <p><?= $ui->createComboData("Tempat Lahir", $queryBirthPlace, "id", "description", "inp[birth_place]", $r[birth_place], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Tanggal Lahir", "inp[birth_date]", getTanggal($r[birth_date]), "", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createFile("Bukti", "rel_filename", $r[rel_filename], "", "", "empFamily", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
            </fieldset>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'S12'");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php include './tmpl/emp_header_basic.php'; ?>
        <div id="pos_r">
            <?php if (isset($menuAccess[$s]["add"])) ?><a href="#" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode') ?>',600,450)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
        </div>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">NAMA</th>
                    <th width="100">HUBUNGAN</th>
                    <th width="100">TGL. LAHIR</th>
                    <th width="50">BUKTI</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM emp_family WHERE parent_id = '$_SESSION[curr_emp_id]'";
                    $res = db($sql);
                    $no = 0;
                    while ($r = mysql_fetch_assoc($res)) {
                        $no++;
                        if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                            $control = "";
                            if (!empty($menuAccess[$s]["edit"]))
                                $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "', 600, 450)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                            if (!empty($menuAccess[$s]["delete"]))
                                $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                        }
                        ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td><?= $arrMaster[$r[rel]] ?></td>
                        <td align="center"><?= getTanggal($r[birth_date]) ?></td>
                        <td align="center"><a href="download.php?d=empFamily&f=<?= $r[id] ?>"><img src=<?= getIcon($r[rel_filename]) ?>></a></td>
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
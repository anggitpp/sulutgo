<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cID : $_SESSION['curr_emp_id'];

$fFile = "files/emp/punish/";

function upload($id)
{
    global $fFile;
    $fileUpload = $_FILES["filename"]["tmp_name"];
    $fileUpload_name = $_FILES["filename"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        $filename = "punish-" . $id . "." . getExtension($fileUpload_name);
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        fileRename($fFile, $fileUpload_name, $filename);
    }
    if (empty($filename)) $filename = getField("select filename from emp_punish where id='$id'");

    return $filename;
}

function hapusFile()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_punish where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "update emp_punish set filename='' where id='$par[id]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $par, $fFile;

    $filename = getField("select filename from emp_punish where id='$par[id]'");
    if (file_exists($fFile . $filename) and $filename != "") unlink($fFile . $filename);

    $sql = "delete from emp_punish where id='$par[id]'";
    db($sql);


    echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}

function tambah()
{
    global $inp, $cID;

    repField();

    $id = getLastId("emp_punish", "id");
    $inp[filename] = upload($id);
    $inp[pnh_date_start] = setTanggal($inp[pnh_date_start]);
    $inp[pnh_date_end] = setTanggal($inp[pnh_date_end]);
    $inp[create_by] = $cID;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "insert into emp_punish set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', pnh_no = '$inp[pnh_no]', pnh_subject = '$inp[pnh_subject]', pnh_agency = '$inp[pnh_agency]', pnh_year = '$inp[pnh_year]', pnh_type = '$inp[pnh_type]', pnh_date_start = '$inp[pnh_date_start]', pnh_date_end = '$inp[pnh_date_end]', filename = '$inp[filename]', remark = '$inp[remark]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cID;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[pnh_date_start] = setTanggal($inp[pnh_date_start]);
    $inp[pnh_date_end] = setTanggal($inp[pnh_date_end]);
    $inp[update_by] = $cID;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_punish set pnh_no = '$inp[pnh_no]', pnh_subject = '$inp[pnh_subject]', pnh_agency = '$inp[pnh_agency]', pnh_year = '$inp[pnh_year]', pnh_type = '$inp[pnh_type]', pnh_date_start = '$inp[pnh_date_start]', pnh_date_end = '$inp[pnh_date_end]', filename = '$inp[filename]', remark = '$inp[remark]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_punish WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $queryType = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S20' and statusData = 't' order by `urutanData`";

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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode") ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA PERINGATAN </legend>
                <p><?= $ui->createField("Nomor Peringatan", "inp[pnh_no]", $r[pnh_no], "t") ?></p>
                <p><?= $ui->createField("Perihal", "inp[pnh_subject]", $r[pnh_subject], "t") ?></p>
                <p><?= $ui->createField("Penerbit", "inp[pnh_agency]", $r[pnh_agency], "t") ?></p>
                <p><?= $ui->createField("Tahun", "inp[pnh_year]", $r[pnh_year], "t", "", "style=\"width:50px\"", "onkeyup=\"cekPhone(this);\"", "4") ?></p>
                <p><?= $ui->createComboData("Tipe", $queryType, "id", "description", "inp[pnh_type]", $r[pnh_type], "", "", "t", "t") ?></p>
                <p><?= $ui->createField("Mulai", "inp[pnh_date_start]", getTanggal($r[pnh_date_start]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createField("Selesai", "inp[pnh_date_end]", getTanggal($r[pnh_date_end]), "t", "", "", "", "", "", "t") ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empPunish", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
            </fieldset>
        </form>
    </div>
<?php
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    $arrMaster = arrayQuery("SELECT kodeData, namaData from mst_data where kodeCategory = 'S13'");
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
                    <th width="150">NOMOR PERINGATAN</th>
                    <th width="*">PERIHAL</th>
                    <th width="100">PENERBIT</th>
                    <th width="100">TIPE</th>
                    <th width="100">TAHUN</th>
                    <th width="50">FILE</th>
                    <?php if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ?><th width="50">Control</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT * FROM emp_punish WHERE parent_id = '$_SESSION[curr_emp_id]'";
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
                        <td><?= $r[pnh_no] ?></td>
                        <td><?= $r[pnh_subject] ?></td>
                        <td><?= $r[pnh_agency] ?></td>
                        <td><?= $arrMaster[$r[pnh_type]] ?></td>
                        <td align="center"><?= $r[pnh_year] ?></td>
                        <td align="center"><a href="download.php?d=empPunish&f=<?= $r[id] ?>"><img src=<?= getIcon($r[filename]) ?>></a></td>
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
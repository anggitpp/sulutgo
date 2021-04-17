<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION['curr_emp_id'] = empty($_SESSION['curr_emp_id']) ? $cID : $_SESSION['curr_emp_id'];

$fFile = "files/emp/contract/";

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
    global $inp, $cID;

    repField();

    $id = getLastId("emp_contract", "id");
    $inp[filename] = upload($id);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[create_by] = $cID;
    $inp[create_date] = date('Y-m-d H:i:s');

    $sql = "INSERT into emp_contract set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', rank = '$inp[rank]', grade = '$inp[grade]', pos_name = '$inp[pos_name]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', create_by = '$inp[create_by]', create_date = '$inp[create_date]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah()
{
    global $inp, $par, $cID;

    repField();

    $inp[filename] = upload($par[id]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[update_by] = $cID;
    $inp[update_date] = date('Y-m-d H:i:s');

    $sql = "update emp_contract set sk_no = '$inp[sk_no]', rank = '$inp[rank]', grade = '$inp[grade]', pos_name = '$inp[pos_name]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', filename = '$inp[filename]', remark = '$inp[remark]', status = '$inp[status]', update_by = '$inp[update_by]', update_date = '$inp[update_date]' where id = '$par[id]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function form()
{
    global $par, $ui, $arrTitle, $s;

    $sql = "SELECT * FROM emp_contract WHERE id='$par[id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

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
                <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode") ?>'" />
            </p>
            <br clear="all" />
            <fieldset>
                <legend> DATA KONTRAK </legend>
                <p><?= $ui->createField("Nomor", "inp[nomor]", $r[nomor], "t") ?></p>
                <p><?= $ui->createField("Perihal", "inp[perihal]", $r[perihal], "t") ?></p>
                <p><?= $ui->createField("Tanggal", "inp[tanggal]", getTanggal($r[tanggal]), "", "", "", "", "", "", "t") ?></p>

                <p><?= $ui->createComboData("Kategori", $queryKategori, "id", "description", "inp[rank]", $r[rank], "onchange=\"getSub('rank', 'grade', '" . getPar($par, "mode") . "')\"", "", "t", "") ?></p>
                <p><?= $ui->createComboData("Tipe", $queryTipe, "id", "description", "inp[grade]", $r[grade], "", "", "t") ?></p>
                <p><?= $ui->createFile("File", "filename", $r[filename], "", "", "empKontrak", $r[id], "delFile") ?> </p>
                <p><?= $ui->createTextArea("Keterangan", "inp[remark]", $r[remark]) ?></p>
                <p><?= $ui->createRadio("Status", "inp[status]", array("t" => "Aktif", "f" => "Tidak Aktif")) ?></p>
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
            <?php if (isset($menuAccess[$s]["add"])) ?><a href="#" onclick="openBox('popup.php?par[mode]=add<?= getPar($par, 'mode') ?>',900,550)" class="btn btn1 btn_document"><span>Tambah Data</span></a>
        </div>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">PERIHAL</th>
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
                $sql = "SELECT * FROM emp_contract WHERE parent_id = '$_SESSION[curr_emp_id]'";
                $res = db($sql);
                $no = 0;
                while ($r = mysql_fetch_assoc($res)) {
                    $no++;
                    if (!empty($menuAccess[$s]["edit"]) || !empty($menuAccess[$s]["delete"])) {
                        $control = "";
                        if (!empty($menuAccess[$s]["edit"]))
                            $control .= "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]" . getPar($par, "mode,id") . "', 900, 550)\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                        if (!empty($menuAccess[$s]["delete"]))
                            $control .= "<a href=\"?par[mode]=del&par[id]=$r[id]" . getPar($par, "mode,id") . "\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                    }
                ?>
                    <tr>
                        <td><?= $no ?>.</td>
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
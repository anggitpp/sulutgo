<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION["curr_emp_id"] = empty($_SESSION["curr_emp_id"]) ? $cID : $_SESSION["curr_emp_id"];

function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");

    return implode("\n", $data);
}

function ubah()
{
    global $inp, $cUsername;

    $cekID = getField("select id from emp_contact where parent_id = '" . $_SESSION['curr_emp_id'] . "'");
    if ($cekID) {
        $sql = "update emp_contact set sr_nama = '$inp[sr_nama]', sr_hub = '$inp[sr_hub]', sr_phone = '$inp[sr_phone]', sr_address = '$inp[sr_address]', sr_prov = '$inp[sr_prov]', sr_city = '$inp[sr_city]', br_nama = '$inp[br_nama]', br_hub = '$inp[br_hub]', br_phone = '$inp[br_phone]', br_address = '$inp[br_address]', br_prov = '$inp[br_prov]', br_city = '$inp[br_city]', update_by = '$cUsername', update_date = '" . date('Y-m-d H:i:s') . "' where id = '$cekID' ";
        db($sql);
    } else {
        $id = getLastId("emp_contact", "id");
        $sql = "insert into emp_contact set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', sr_nama = '$inp[sr_nama]', sr_hub = '$inp[sr_hub]', sr_phone = '$inp[sr_phone]', sr_address = '$inp[sr_address]', sr_prov = '$inp[sr_prov]', sr_city = '$inp[sr_city]', br_nama = '$inp[br_nama]', br_hub = '$inp[br_hub]', br_phone = '$inp[br_phone]', br_address = '$inp[br_address]', br_prov = '$inp[br_prov]', br_city = '$inp[br_city]', create_by = '$cUsername', create_date = '" . date('Y-m-d H:i:s') . "'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');reloadPage();</script>";
}

function form()
{
    global $s, $par, $arrTitle, $ui;
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php
            include './tmpl/emp_header_basic.php';
            $sql = "SELECT * from emp_contact where parent_id ='$_SESSION[curr_emp_id]'";
            $res = db($sql);
            $r = mysql_fetch_assoc($res);

            $queryProv = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S02' and statusData = 't' order by namaData";
            $queryKotaSr = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[sr_prov]' and statusData = 't' order by namaData";
            $queryKotaBr = "SELECT kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[br_prov]' and statusData = 't' order by namaData";
            ?>
        <form method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>">
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
            </p>
            <br clear="all" />
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
                        <p><?= $ui->createField("Alamat", "inp[sr_address]", $r[sr_address], "", "t") ?></p>
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
                        <p><?= $ui->createField("Alamat", "inp[br_address]", $r[br_address], "", "t") ?></p>
                        <p><?= $ui->createComboData("Provinsi", $queryProv, "id", "description", "inp[br_prov]", $r[br_prov], "onchange=\"getSub('br_prov','br_city','" . getPar($par, "mode") . "');\"", "", "t", "", "t") ?></p>
                        <p><?= $ui->createComboData("Kota", $queryKotaBr, "id", "description", "inp[br_city]", $r[br_city], "", "", "t", "", "t") ?></p>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}

function getContent($par)
{
    global $_submit;
    switch ($par[mode]) {
        case "subData":
            $text = subData();
            break;
        default:
            $text = empty($_submit) ? form() : ubah();
            break;
    }
    return $text;
}
?>
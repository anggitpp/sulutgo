<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION["curr_emp_id"] = (isset($_GET["empid"]) ? $_GET["empid"] : $_SESSION["curr_emp_id"]);
$_SESSION["curr_emp_id"] = empty($_SESSION["curr_emp_id"]) ? $cID : $_SESSION["curr_emp_id"];
if (!empty($par[idPegawai])) $_SESSION["curr_emp_id"] = $par[idPegawai];

function lihat()
{
    global $s, $par, $arrTitle, $ui;

    $sql = "SELECT *,t1.id as id FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.id='$_SESSION[curr_emp_id]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $arrPegawai = arrayQuery("select id, name from emp");

    $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-Laki";

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form method="post" class="stdform">
            <br clear="all" />
            <table style="width:100%">
                <tr>
                    <td style="width: 15%; padding-right: 10px; padding-top: 5px;">
                        <img alt="<?= $r[reg_no] ?>" width="100%" height="200px" src="<?= APP_URL . "/files/emp/pic/" . (empty($r[pic_filename]) ? "nophoto.jpg" : $r[pic_filename]) ?>">
                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <p><?= $ui->createSpan("Nama Lengkap", $r[name]) ?></p>
                        <p><?= $ui->createSpan("Nama Panggilan", $r[alias]) ?></p>
                        <p><?= $ui->createSpan("Tempat Lahir", $arrMaster[$r[birth_place]]) ?></p>
                        <p><?= $ui->createSpan("No. KTP", $r[ktp_no]) ?></p>
                        <p><?= $ui->createSpan("Jenis Kelamin", $r[gender]) ?></p>
                        <p><?= $ui->createSpan("Alamat KTP", $r[ktp_address]) ?></p>
                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <p><?= $ui->createSpan("ID", $r[reg_no]) ?></p>
                        <p><?= $ui->createSpan("NPP", $r[kode]) ?></p>
                        <p><?= $ui->createSpan("Tgl. Lahir", getTanggal($r[birth_date], "t")) ?></p>
                        <p><?= $ui->createSpan("File KTP", "<a href=\"download.php?d=empktp&f=$r[id]" . getPar($par, "mode") . "\"><img src=\"" . getIcon($r[ktp_filename]) . "\" style=\"height:20px;\"></a>") ?></p>
                    </td>
                </tr>
            </table>
            <table style="width:100%">
                <tr>
                    <td style="width:50%">
                        <p><?= $ui->createSpan("Propinsi KTP", $arrMaster[$r[ktp_prov]]) ?></p>
                        <p><?= $ui->createSpan("Alamat Domisili", $r[dom_address]) ?></p>
                        <p><?= $ui->createSpan("Propinsi Domisili", $arrMaster[$r[dom_prov]]) ?></p>
                        <p><?= $ui->createSpan("Telp. Rumah", $r[phone_no]) ?></p>
                        <p><?= $ui->createSpan("Email", $r[email]) ?></p>
                        <p><?= $ui->createSpan("Status Pegawai", $arrMaster[$r[cat]]) ?></p>
                        <p><?= $ui->createSpan("No. NPWP", $r[npwp_no]) ?></p>
                        <p><?= $ui->createSpan("Masuk Kerja", getTanggal($r[join_date])) ?></p>
                    </td>
                    <td style="width:50">
                        <p><?= $ui->createSpan("Kab/Kota KTP", $arrMaster[$r[ktp_city]]) ?></p>
                        <p>&nbsp;</p>
                        <p><?= $ui->createSpan("Kab/Kota Domisili", $arrMaster[$r[dom_city]]) ?></p>
                        <p><?= $ui->createSpan("No. HP", $r[cell_no]) ?></p>
                    </td>
                </tr>
            </table>

            <div class="widgetbox">
                <div class="title">
                    <h3>POSISI SAAT INI</h3>
                </div>
            </div>
            <table style="width:100%">
                <tr>
                    <td style="width:50%">
                        <p><?= $ui->createSpan("Jabatan", $r[pos_name]) ?></p>
                        <p><?= $ui->createSpan("Pangkat", $arrMaster[$r[rank]]) ?></p>
                        <p><?= $ui->createSpan("Direktorat", $arrMaster[$r[dir_id]]) ?></p>
                        <p><?= $ui->createSpan("Divisi", $arrMaster[$r[div_id]]) ?></p>
                        <p><?= $ui->createSpan("Departemen", $arrMaster[$r[dept_id]]) ?></p>
                        <p><?= $ui->createSpan("Unit", $arrMaster[$r[unit_id]]) ?></p>
                        <p><?= $ui->createSpan("Lokasi Kerja", $arrMaster[$r[location]]) ?></p>
                    </td>
                    <td style="width:50%">
                        <p>&nbsp;</p>
                        <p><?= $ui->createSpan("Grade", $arrMaster[$r[grade]]) ?></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%">
                        <p><?= $ui->createSpan("Mulai Kontrak", getTanggal($r[start_date])) ?></p>
                        <p><?= $ui->createSpan("Atasan", $arrPegawai[$r[leader_id]]) ?></p>
                        <p><?= $ui->createSpan("Tata Usaha", $arrPegawai[$r[administration_id]]) ?></p>
                        <p><?= $ui->createSpan("Pengganti", $arrPegawai[$r[replacement_id]]) ?></p>
                    </td>
                    <td style="width:50%">
                        <p><?= $ui->createSpan("Selesai Kontrak", getTanggal($r[end_date])) ?></p>
                    </td>
                </tr>
            </table>

        </form>
    </div>
<?php
}

function getContent($par)
{
    switch ($par[mode]) {
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/upload/";

function lihat()
{
    global $s, $par, $arrTitle, $areaCheck;

    if (empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
    if (empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
    if (empty($par[idPegawai])) $par[idPegawai] = getField("select id from emp where status='535' order by name limit 1");

    $queryEmp = "SELECT t1.id, name description from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') where t1.status='535' AND t2.location IN ( $areaCheck ) order by name";

    $day = date('t', mktime(0, 0, 0, $par[bulanJadwal], 1, $par[tahunJadwal])); // Get the amount of days

    $_SESSION["curr_emp_id"] = $par[idPegawai];
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <form id="form" action="" method="post" class="stdform">
        <p class="btnSave">
            <?= comboData($queryEmp, "id", "description", "par[idPegawai]", "", $par[idPegawai], "onchange=\"document.getElementById('form').submit();\"", "300px;", "chosen-select") ?>
            <input type="button" value="&lsaquo;" class="btn btn_search btn-small" onclick="prevDate();" />
            <?= comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"") . " " . comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"") ?>
            <input type="button" value="&rsaquo;" class="btn btn_search btn-small" onclick="nextDate();" />
            <input type="hidden" id="par[mode]" name="par[mode]" value="<?= $par[mode] ?>">
        </p>
    </form>
    <div style="padding:10px;">
        <?php require_once "tmpl/emp_header_basic.php";
            $sql = "select t1.tanggalJadwal, t2.namaShift, t1.mulaiJadwal, t1.selesaiJadwal, t1.keteranganJadwal from dta_jadwal t1 join dta_shift t2 on t1.idShift = t2.idShift where t1.idPegawai='" . $par[idPegawai] . "' and month(t1.tanggalJadwal)='" . $par[bulanJadwal] . "' and year(t1.tanggalJadwal)='" . $par[tahunJadwal] . "'";
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                list($tahun, $bulan, $tanggal) = explode("-", $r[tanggalJadwal]);
                $arrData[$tanggal] = implode("\t", array($r[namaShift], $r[mulaiJadwal], $r[selesaiJadwal], $r[keteranganJadwal]));
            }

            $shiftDefault = getField("SELECT idShift from dta_shift order by idShift asc limit 1");
            $arrShift = arrayQuery("SELECT idShift, concat(namaShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift");
            ?>
    </div>
    <br clear="all" />
    <div class="contentwrapper">
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>" enctype="multipart/form-data">
            <div class="widgetbox">
                <div class="title">
                    <h3>JADWAL KERJA</h3>
                </div>
            </div>
            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                <thead>
                    <tr>
                        <th style="vertical-align:middle" rowspan="2" width="20">No.</th>
                        <th style="vertical-align:middle" rowspan="2" width="*">Tanggal</th>
                        <th style="vertical-align:middle" rowspan="2" width="150">Shift</th>
                        <th colspan="2" width="150">Jadwal</th>
                        <th style="vertical-align:middle" rowspan="2" width="200">Keterangan</th>
                    </tr>
                    <tr>
                        <th width="75">Masuk</th>
                        <th width="75">Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for ($i = 1; $i <= $day; $i++) {
                            $i = $i < 10 ? "0" . $i : $i;
                            $no++;
                            $tanggalJadwal = implode("-", array($par[tahunJadwal], $par[bulanJadwal], $i));
                            list($namaShift, $masukShift, $keluarShift, $keteranganShift) = explode("\t", (!empty($arrData[$i]) ? $arrData[$i] : $arrShift[$shiftDefault]));
                            $week = date("w", strtotime($tanggalJadwal));
                            $color = in_array($week, array(0, 6)) ? "style=\"background:#f2dbdb;\"" : "";

                            ?>
                        <tr <?= $color ?>>
                            <td><?= $no ?>.</td>
                            <td align="left"><?= getHari($tanggalJadwal) . ", " . getTanggal($tanggalJadwal, "t") ?></td>
                            <td><?= $namaShift ?></td>
                            <td align="center"><?= substr($masukShift, 0, 5) ?></td>
                            <td align="center"><?= substr($keluarShift, 0, 5) ?></td>
                            <td><?= $keteranganShift ?></td>
                        </tr>
                    <?php
                        }
                        ?>
                </tbody>
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
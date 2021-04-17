<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/upload/";

function lihat()
{
    global $s, $par, $arrTitle, $cID;

    if (empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
    if (empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');

    $day = date("t", strtotime($par[tahunJadwal] . "-" . $par[bulanJadwal] . "-01"));
    $_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread(ucwords($par[mode] . " data")) ?>
    </div>
    <form id="form" method="post" class="stdform">
        <p class="btnSave">
            <input type="button" value="&lsaquo;" class="btn btn_search btn-small" style="margin-right:5px;" onclick="prevDate();" />
            <?= comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"") . " " . comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"") ?>
            <input type="button" value="&rsaquo;" class="btn btn_search btn-small" style="margin-right:5px;" onclick="nextDate();" />
        </p>
    </form>
    <div class="contentwrapper">
        <br clear="all" />
        <?php
            require_once "tmpl/emp_header_basic.php";

            $sql = "select * from dta_jadwal where idPegawai='" . $par[idPegawai] . "' and month(tanggalJadwal)='" . $par[bulanJadwal] . "' and year(tanggalJadwal)='" . $par[tahunJadwal] . "'";
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                list($tahun, $bulan, $tanggal) = explode("-", $r[tanggalJadwal]);
                $arr["idShift"][intval($tanggal)] = $r[idShift];
                $arr["mulaiJadwal"][intval($tanggal)] = $r[mulaiJadwal];
                $arr["selesaiJadwal"][intval($tanggal)] = $r[selesaiJadwal];
                $arr["keteranganJadwal"][intval($tanggal)] = $r[keteranganJadwal];
            }
            ?>
        <br clear="all" />
        <form id="form" name="form" method="post" class="stdform" action="?_submit=1" .getPar($par)."" enctype="multipart/form-data">
            <div id="general">
                <div class="widgetbox">
                    <div class="title">
                        <h3>JADWAL KERJA</h3>
                    </div>
                </div>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                        <tr>
                            <th rowspan="2" width="20">No.</th>
                            <th rowspan="2" width="150">Tanggal</th>
                            <th rowspan="2" width="150">Shift</th>
                            <th colspan="2" width="150">Jadwal</th>
                            <th rowspan="2" width="150">Keterangan</th>
                        </tr>
                        <tr>
                            <th width="75">Masuk</th>
                            <th width="75">Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $arrShift = arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");
                            ksort($arrShift);
                            reset($arrShift);
                            list($normalShift, $mulaiNormal, $selesaiNormal) = explode("\t", getField("select concat(namaShift,'\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift limit 1"));

                            for ($i = 1; $i <= $day; $i++) {
                                $week = date("w", strtotime($par[tahunJadwal] . "-" . $par[bulanJadwal] . "-" . str_pad($i, 2, "0", STR_PAD_LEFT)));
                                $color = in_array($week, array(0, 6)) ? "#f2dbdb" : "#ffffff";

                                $arrShift["" . $arr[idShift][$i] . ""] = empty($arr[idShift][$i]) ? $normalShift : $arrShift["" . $arr[idShift][$i] . ""];
                                $arr[mulaiJadwal][$i] = empty($arr[idShift][$i])  ? $mulaiNormal : $arr[mulaiJadwal][$i];
                                $arr[selesaiJadwal][$i] = empty($arr[idShift][$i])  ? $selesaiNormal : $arr[selesaiJadwal][$i];
                                ?>
                            <tr style="background:" .$color.";">
                                <td><?= $i ?>.</td>
                                <td><?= str_pad($i, 2, "0", STR_PAD_LEFT) . " " . getBulan($par[bulanJadwal]) . " " . $par[tahunJadwal] ?></td>
                                <td><?= $arrShift["" . $arr[idShift][$i] . ""] ?></td>
                                <td align="center"><?= substr($arr[mulaiJadwal][$i], 0, 5) ?></td>
                                <td align="center"><?= substr($arr[selesaiJadwal][$i], 0, 5) ?></td>
                                <td><?= $arr[keteranganJadwal][$i] ?></td>
                            </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
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
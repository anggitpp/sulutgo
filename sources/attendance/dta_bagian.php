<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function lihat()
{
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    if (empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
    if (empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');

    $arrStatus = array("", "I", "IK", "C", "S1", "S2", "D", "T", "M");
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE statusData = 't'");
    $queryUnitKerja = "SELECT t1.kodeData id, t1.namaData description from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' order by t1.urutanData";
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" action="" method="post" class="stdform">
            <div id="pos_l">
                <?= comboData($queryUnitKerja, "id", "description", "par[idUnitKerja]", "- Unit Kerja -", $par[idUnitKerja], "onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <div style="position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;">
                <input type="button" value="&lsaquo;" onclick="prevDate();" class="btn btn_search btn-small"/>
                <?= comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"") ?>
                <?= comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"") ?>
                <input type="button" value="&rsaquo;" class="btn btn_search btn-small" onclick="nextDate();"/>
            </div>
        </form>
        <br clear="all"/>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th style="min-width:350px;">Unit Kerja</th>
                <?php
                if (is_array($arrStatus)) {
                    reset($arrStatus);
                    while (list($i, $statusAbsen) = each($arrStatus)) {
                        $statusAbsen = empty($statusAbsen) ? "JML" : $statusAbsen;
                        echo "<th style=\"width:40px;\">" . $statusAbsen . "</th>";
                    }
                }
                ?>
                <th style="width:100px;">Prosentase</th>
                <th style="width:50px;">Detail</th>
            </tr>
            </thead>
            <tbody>
            <?php

            $filter = !empty($par[idUnitKerja]) ? " AND t1.kodeInduk = '$par[idUnitKerja]'" : "";
            $arrDepartemen = arrayQuery("SELECT t1.kodeData id, concat(t2.namaData, ' - ',t1.namaData) description from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData = t2.kodeInduk where t3.kodeCategory='X03' $filter order by t1.urutanData");

            $sql = "select t1.div_id, jumlahShift from dta_pegawai t1 join att_setting t2 join dta_shift t3 on (t1.id=t2.idPegawai and t2.idShift=t3.idShift)";
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                $arrShift["$r[div_id]"] += $r[jumlahShift];
            }

            $sql = "select t2.div_id, t1.statusAbsen from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where '" . $par[tahunAbsen] . $par[bulanAbsen] . "' between concat(year(t1.mulaiAbsen), LPAD(month(t1.mulaiAbsen),2,'0')) and concat(year(t1.selesaiAbsen), LPAD(month(t1.selesaiAbsen),2,'0')) order by t1.mulaiAbsen";
            //            echo $sql;
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {
                $arrAbsen[$arrMaster["$r[div_id]"]]["$r[statusAbsen]"]++;
            }

            //            debugVar($arrDepartemen);

            if (is_array($arrDepartemen)) {
                reset($arrDepartemen);
                while (list($idDepartemen, $namaDepartemen) = each($arrDepartemen)) {
                    $no++;
                    ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $namaDepartemen ?></td>
                        <?php
                        if (is_array($arrStatus)) {
                            reset($arrStatus);
                            while (list($i, $statusAbsen) = each($arrStatus)) {
                                echo "<td align=\"center\">" . getAngka($arrAbsen[$arrMaster[$idDepartemen]][$statusAbsen], 0, 5) . "</td>";
                            }
                        }
                        $persenAbsen = $arrShift[$idDepartemen] > 0 ? $arrAbsen[$idDepartemen][""] / $arrShift[$idDepartemen] * 100 : 0;
                        ?>
                        <td align="center"><?= getAngka($persenAbsen) ?>%</td>
                        <td align="center"><a title="Detail Data" class="detail"
                                              href="?par[mode]=det&par[idDivisi]=<?= $idDepartemen . getPar($par, "mode,idDivisi") ?>"><span>Detail</span></a>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}

function detail()
{
    global $s, $par, $arrTitle, $arrParameter;

    if (empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
    if (empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
    if (!empty($par[idDivisi])) $filter = " and kodeInduk='" . $par[idDivisi] . "'";

    $day = date("t", strtotime($par[tahunAbsen] . "-" . $par[bulanAbsen] . "-01"));
    $arrStatus = array("", "I", "C", "S", "D", "T", "M");

    $text .= "
<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread() . "

</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
        <div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\" >
        <input type = \"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\"
        onclick=\"prevDate();\"/>
        " . comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"") .
        " " . comboYear("par[tahunAbsen]", $par[tahunAbsen], "",
            "onchange=\"document.getElementById('form').submit();\"") . "
        <input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
</div>
<div id=\"pos_l\" style=\"float:left;\">
    <table>
        <tr>
            <td>Divisi :</td>
            <td>" . comboData("select * from mst_data where kodeCategory='" . $arrParameter[7] . "' and statusData='t'
                order by urutanData", "kodeData", "namaData", "par[idDivisi]", "All", $par[idDivisi],
            "onchange=\"document.getElementById('form').submit();\"", "250px") . "
            </td>
            <td>" . comboData("select * from mst_data where kodeCategory='" . $arrParameter[8] . "' " . $filter . " and
                statusData='t' order by urutanData", "kodeData", "namaData", "par[idDepartemen]", "All",
            $par[idDepartemen], "onchange=\"document.getElementById('form').submit();\"", "250px") . "
            </td>
        </tr>
    </table>
</div>
<div id=\"pos_r\" style=\"float:right;\">
    <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par,
            "mode,idDivisi") . "';\" style=\"float:right;\" />
</div>
<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"" . $par[mode] . "\" >
<br clear=\"all\"/>
</form>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
<thead>
<tr>
    <th width=\"20\">No.</th>
    <th style=\"min-width:350px;\">Nama</th>
    <th style=\"width:100px;\">NIK</th>
    ";
    if (is_array($arrStatus)) {
        reset($arrStatus);
        while (list($i, $statusAbsen) = each($arrStatus)) {
            $statusAbsen = empty($statusAbsen) ? "JML" : $statusAbsen;
            $text .= "
    <th style=\"width:40px;\">" . $statusAbsen . "</th>
    ";
        }
    }
    $text .= "
    <th style=\"width:100px;\">Prosentase</th>
    <th style=\"width:50px;\">Detail</th>
</tr>
</thead>
<tbody>";

    $arrShift = arrayQuery("select t1.idPegawai, jumlahShift from att_setting t1 join dta_shift t2 on
(t1.idShift=t2.idShift)");

    $sql = "select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where '" . $par[tahunAbsen] . "" .
        $par[bulanAbsen] . "' between concat(year(t1.mulaiAbsen), LPAD(month(t1.mulaiAbsen),2,'0')) and
concat(year(t1.selesaiAbsen), LPAD(month(t1.selesaiAbsen),2,'0')) order by t1.mulaiAbsen";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $arrAbsen["$r[idPegawai]"]["$r[statusAbsen]"]++;
    }

    $filter = "where id is not null";
    if (!empty($par[idDivisi])) $filter .= " and div_id='" . $par[idDivisi] . "'";
    if (!empty($par[idDepartemen])) $filter .= " and dept_id='" . $par[idDepartemen] . "'";

    $sql = "select * from dta_pegawai $filter order by name";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        $text .= "
<tr>
    <td>$no.</td>
    <td>" . strtoupper($r[name]) . "</td>
    <td>$r[reg_no]</td>
    ";
        if (is_array($arrStatus)) {
            reset($arrStatus);
            while (list($i, $statusAbsen) = each($arrStatus)) {
                $text .= "
    <td align=\"center\">" . getAngka($arrAbsen["$r[id]"][$statusAbsen], 0, 5) . "</td>
    ";
            }
        }

        $persenAbsen = $arrShift["$r[id]"] > 0 ? $arrAbsen["$r[id]"][""] / $arrShift["$r[id]"] * 100 : 0;
        $text .= "
    <td align=\"center\">" . getAngka($persenAbsen) . "%</td>
    <td align=\"center\"><a href=\"?par[mode]=dta&par[idPegawai]=$r[id]" . getPar($par, "mode,idPegawai") . "\"
        title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>
</tr>
";
    }

    $text .= "
</tbody>
</table>
</div>";
    return $text;
}

function data()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    if (empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
    if (empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');

    $_SESSION["curr_emp_id"] = $par[idPegawai];
    echo "
<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " .
        $arrTitle[$s] . "</h1>
    " . getBread(ucwords($par[mode] . " data")) . "
</div>
<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    <div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\" >
    <input type = \"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\"
    onclick=\"prevDate();\"/>
    " . comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"") . " "
        . comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"") . "
    <input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
    <input type=\"hidden\" id=\"par[idPegawai]\" name=\"par[idPegawai]\" value=\"" . $par[idPegawai] . "\" >
    <input type=\"hidden\" id=\"par[idDivisi]\" name=\"par[idDivisi]\" value=\"" . $par[idDivisi] . "\" >
    <input type=\"hidden\" id=\"par[idDepartemen]\" name=\"par[idDepartemen]\" value=\"" . $par[idDepartemen] . "\" >
    <input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"" . $par[mode] . "\" >
    </div>
</form>
<div style=\"padding:10px;\">";

    require_once "tmpl/__emp_header__.php";

    $text .= "
</div>
<div class=\"contentwrapper\">
    <div id=\"general\">
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id = \"dyntable\" style=\"margin-top:30px;\">
        <thead>
        <tr>
            <th rowspan=\"2\" width=\"20\">No.</th>
            <th rowspan=\"2\" style=\"min-width:150px;\">Tanggal</th>
            <th colspan=\"2\" style=\"width:80px;\">Jadwal</th>
            <th colspan=\"2\" style=\"width:80px;\">Aktual</th>
            <th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
            <th rowspan=\"2\" style=\"width:80px;\">Keterangan</th>
        </tr>
        <tr>
            <th style=\"width:40px;\">Masuk</th>
            <th style=\"width:40px;\">Pulang</th>
            <th style=\"width:40px;\">Masuk</th>
            <th style=\"width:40px;\">Pulang</th>
        </tr>
        </thead>
        <tbody>";

    $arrNormal = getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where
        trim(lower(namaShift))='normal'");
    $arrShift = arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1
        join dta_shift t2 on (t1.idShift=t2.idShift)");
    $arrJadwal = arrayQuery("select tanggalJadwal, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where
        idPegawai='$par[idPegawai]' and year(tanggalJadwal)='" . $par[tahunAbsen] . "' and month(tanggalJadwal)='" .
        $par[bulanAbsen] . "'");

    $sql = "select * from dta_absen where idPegawai='$par[idPegawai]' and '" . $par[tahunAbsen] . "" .
        $par[bulanAbsen] . "' between concat(year(mulaiAbsen), LPAD(month(mulaiAbsen),2,'0')) and
        concat(year(selesaiAbsen), LPAD(month(selesaiAbsen),2,'0'))";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
            explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal);

        list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
        list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

        if (isset($arrJadwal["$r[tanggalAbsen]"]))
            list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[tanggalAbsen]"]);

        $arr["$r[tanggalAbsen]"] = $r;
    }

    if (is_array($arr)) {
        ksort($arr);
        reset($arr);
        while (list($tanggalAbsen, $r) = each($arr)) {
            $no++;

            if ($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
            if ($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

            $text .= "
        <tr>
            <td>$no.</td>
            <td><a href=\"?par[mode]=dat&par[tanggalAbsen]=" . getTanggal($r[tanggalAbsen]) .
                "&par[keteranganAbsen]=$r[keteranganAbsen]" . getPar($par, "mode,tanggalAbsen,keteranganAbsen") . "\"
                title=\"Detail Data\" class=\"detil\">" . getTanggal($r[tanggalAbsen], "t") . "</a></td>
            <td align=\"center\">" . substr($r[mulaiShift], 0, 5) . "</td>
            <td align=\"center\">" . substr($r[selesaiShift], 0, 5) . "</td>
            <td align=\"center\">" . substr($r[masukAbsen], 0, 5) . "</td>
            <td align=\"center\">" . substr($r[pulangAbsen], 0, 5) . "</td>
            <td align=\"center\">" . substr(str_replace("-", "", $r[durasiAbsen]), 0, 5) . "</td>
            <td>$r[keteranganAbsen]</td>
        </tr>
        ";
        }
    }

    $text .= "
        </tbody>
        </table>
        <p>
            <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?par[mode]=det"
        . getPar($par, "mode,idPegawai") . "';\" style=\"float:right;\" />
        </p>
    </div>
</div>";
    return $text;
}

function view()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    $_SESSION["curr_emp_id"] = $par[idPegawai];
    echo "
<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " .
        $arrTitle[$s] . "</h1>
    " . getBread(ucwords($par[mode] . " data")) . "
</div>
<div style=\"padding:10px;\">";

    require_once "tmpl/__emp_header__.php";

    $sql = "select * from dta_absen where idPegawai='$par[idPegawai]' and '" . setTanggal($par[tanggalAbsen]) . "'
    between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $dtaNormal = getField("select concat(namaShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where
    trim(lower(namaShift))='normal'");
    $dtaShift = getField("select concat(t2.namaShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1
    join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]'");
    $dtaJadwal = getField("select concat(t2.namaShift, ',\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1
    join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]' and tanggalJadwal='" .
        setTanggal($par[tanggalAbsen]) . "'");

    list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t",
        $dtaShift);
    if (!empty($dtaJadwal)) list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);

    list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
    list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

    if ($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
    if ($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

    $text .= "
</div>
<div class=\"contentwrapper\">
    <form id=\"form\" class=\"stdform\">
        <div id=\"general\">
            <div class=\"widgetbox\">
                <div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\" ><h3 > DATA ABSENSI </h3 ></div >
        </div >
        <p >
            <label class=\"l-input-small\">Tanggal</label>
            <span class=\"field\">" . getTanggal(setTanggal($par[tanggalAbsen]), "t") . "&nbsp;</span>
        </p>
        <p>
            <label class=\"l-input-small\">Jadwal Kerja</label>
            <span class=\"field\">$r[namaShift] " . substr($r[mulaiShift], 0, 5) . " - " . substr($r[selesaiShift], 0, 5) . "&nbsp;</span>
        </p>";

    list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
    $nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn .
        " / " . $pulangAbsen_sn;

    if (empty($r[keteranganAbsen]))
        $text .= "<p>
            <label class=\"l-input-small\">Aktual</label>
            <span class=\"field\">" . substr($r[masukAbsen], 0, 5) . " - " . substr($r[pulangAbsen], 0, 5) . "&nbsp;</span>
        </p>
        <p>
            <label class=\"l-input-small\">SN Mesin</label>
            <span class=\"field\">" . $nomorAbsen . "&nbsp;</span>
        </p>";
    else
        $text .= "<p>
            <label class=\"l-input-small\">Keterangan</label>
            <span class=\"field\">$r[keteranganAbsen]&nbsp;</span>
        </p>
        <p>
            <label class=\"l-input-small\">Nomor</label>
            <span class=\"field\">$r[nomorAbsen]&nbsp;</span>
        </p>";
    $text .= "
</div>
<p>
    <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?par[mode]=dta" .
        getPar($par, "mode,tanggalAbsen,keteranganAbsen") . "';\" style=\"float:right;\" />
</p>
</form>";
    return $text;
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "dat":
            $text = view();
            break;
        case "dta":
            $text = data();
            break;
        case "det":
            $text = detail();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}

?>
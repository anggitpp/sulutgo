<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";

function lihat()
{
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    $queryKantor = "SELECT kodeData id, namaData description, kodeInduk from mst_data where kodeCategory='X03' order by kodeInduk, urutanData";
    $queryUnitKerja = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' and t1.kodeInduk = '$par[idKantor]' order by t1.urutanData";
    $queryLokasiKerja = "SELECT kodeData, namaData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[7] . "' and kodeData in ($areaCheck) order by urutanData";

    $countColumnJabatan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S09' and statusData = 't'");
    $countColumnStatus = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S04' and statusData = 't'");
    $countColumnAgama = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S07' and statusData = 't'");
    $countColumnPendidikan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'R11' and statusData = 't'");
    $countColumnPerkawinan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S08' and statusData = 't'");
    $countColumnNation = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S01' and statusData = 't'");
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE statusData = 't'");

    $filter = "WHERE t1.status = '535' AND t2.div_id !='0'";
    if(!empty($par[idKantor]))
        $filter.=" AND t2.dir_id = '$par[idKantor]'";
    if(!empty($par[idDivisi]))
        $filter.=" AND t2.div_id = '$par[idUnit]'";
    if(!empty($par[idLokasi]))
        $filter.=" AND t2.location = '$par[idLokasi]'";
    $sql = "SELECT t1.id, t1.cat, t1.religion, t1.nation, t1.gender, t1.ptkp, t2.div_id, t2.rank FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $arrJumlah["divisi"][$arrMaster[$r[div_id]]]++;
        $arrJumlah["rank"][$arrMaster[$r[div_id]]][$r[rank]]++;
        $arrJumlah["status"][$arrMaster[$r[div_id]]][$r[cat]]++;
        $arrJumlah["agama"][$arrMaster[$r[div_id]]][$r[religion]]++;
        $arrJumlah["nation"][$arrMaster[$r[div_id]]][$r[nation]]++;
        $arrJumlah["gender"][$arrMaster[$r[div_id]]][$r[gender]]++;
        $arrJumlah["ptkp"][$arrMaster[$r[div_id]]][$r[ptkp]]++;
    }
     ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all"/>
        <form id="form" action="" method="post" class="stdform">
            <div style="position: absolute;right: 5px;top: 10px;">
                Lokasi Kerja
                : <?= comboData($queryLokasiKerja, "kodeData", "namaData", "par[idLokasi]", "All", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"", "310px") ?>
            </div>

            <div id="pos_r">
                <a href="?par[mode]=xls<?= getPar($par, "mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
            </div>
            <div id="pos_l">
                <?= comboData($queryKantor, "id", "description", "par[idKantor]", "- KANTOR -", $par[idKantor], "onchange=\"document.getElementById('form').submit();\"", "310px") ?>
                <?= comboData($queryUnitKerja, "id", "description", "par[idUnit]", "- UNIT KERJA -", $par[idUnit], "onchange=\"document.getElementById('form').submit();\"", "310px") ?>
            </div>
        </form>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dynscroll">
            <thead>
            <tr>
                <th rowspan="2" width="10" style="min-width:10px;vertical-align:middle;">No.</th>
                <th rowspan="2" width="200" style="min-width:200px;vertical-align:middle;">UNIT KERJA/CABANG</th>
                <th rowspan="2" width="20" style="min-width:50px;vertical-align:middle;">TOTAL JUMLAH KARYAWAN</th>
                <th colspan="<?= $countColumnJabatan ?>" style="min-width:80px;vertical-align:middle;">JABATAN
                    KARYAWAN
                </th>
                <th colspan="<?= $countColumnStatus ?>" style="min-width:40px;vertical-align:middle;">STATUS KARYAWAN
                </th>
                <th colspan="2" style="min-width:40px;vertical-align:middle;">JENIS KELAMIN</th>
                <th colspan="<?= $countColumnAgama ?>" style="min-width:40px;vertical-align:middle;">AGAMA</th>
                <th colspan="<?= $countColumnPendidikan ?>" style="min-width:85px;vertical-align:middle;">PENDIDIKAN
                    TERAKHIR
                </th>
                <th colspan="<?= $countColumnPerkawinan ?>" style="min-width:85px;vertical-align:middle;">STATUS
                    PERKAWINAN
                </th>
                <th colspan="<?= $countColumnNation ?>" style="min-width:85px;vertical-align:middle;">KEWARGANEGARAAN
                </th>
            </tr>
            <tr>
                <?php
                $sql = "SELECT kodeData,namaData from mst_data where kodeCategory='S09' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($r = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\">$r[namaData]</th>";
                }

                $sql = "select kodeData,namaData from mst_data where kodeCategory='S04' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($r = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\">$r[namaData]</th>";
                }
                ?>
                <th style="width:40px;">MALE</th>
                <th style="width:40px;">FEMALE</th>
                <?php
                $sql = "select kodeData,namaData from mst_data where kodeCategory='S07' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($r = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\" >" . substr(str_replace("-", "", $r[namaData]), 0, 1) . "</th>";
                }
                $sql = "select kodeData,namaData from mst_data where kodeCategory='R11' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($rl = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\" >$rl[namaData]</th>";
                }
                $sql = "select kodeData,namaData from mst_data where kodeCategory='S08' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($rg = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\" >$rg[namaData]</th>";
                }
                $sql = "select kodeData,namaData from mst_data where kodeCategory='S01' AND statusData='t' order by urutanData";
                $res = db($sql);
                while ($r = mysql_fetch_array($res)) {
                    echo "<th style=\"width:40px;\">$r[namaData]</th>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "where t2.kodeCategory='X03' AND t2.statusData = 't' AND t1.statusData = 't'";
            if(!empty($par[idKantor]))
                $filter.=" AND t1.kodeInduk = '$par[idKantor]'";
            if(!empty($par[idKantor]) && !empty($par[idUnit]))
                $filter.=" AND t1.kodeData = '$par[idUnit]'";
            $sql = "SELECT t1.kodeData, t1.namaData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk $filter group by t1.namaData order by t1.namaData";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $no++;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[namaData] ?></td>
                    <td><?= $arrJumlah["divisi"][$r[namaData]] ?></td>
                    <?php
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S09' and statusData = 't' order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["rank"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S04' and statusData = 't'  order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["status"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    ?>
                    <td><?= $arrJumlah["gender"][$r[namaData]]["M"] ?></td>
                    <td><?= $arrJumlah["gender"][$r[namaData]]["F"] ?></td>
                    <?php
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S07' and statusData = 't'  order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["agama"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'R11' and statusData = 't'  order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["pendidikan"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S08' and statusData = 't'  order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["ptkp"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S01' and statusData = 't'  order by urutanData";
                    $res_ = db($sql_);
                    while ($r_ = mysql_fetch_assoc($res_)) {
                        ?>
                        <td><?= $arrJumlah["nation"][$r[namaData]][$r_[kodeData]] != "" ?: 0 ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
            </tfoot>
        </table>
    </div>
    <?php

    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=" . ucwords(strtolower($arrTitle[$s])) . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function xls()
{
    global $s, $par, $arrTitle, $cNama, $fFile;
    require_once 'plugins/PHPExcel.php';

    $countColumnJabatan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S09' and statusData = 't'");
    $countColumnStatus = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S04' and statusData = 't'");
    $countColumnAgama = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S07' and statusData = 't'");
    $countColumnPendidikan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'R11' and statusData = 't'");
    $countColumnPerkawinan = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S08' and statusData = 't'");
    $countColumnNation = getField("SELECT COUNT(kodeData) FROM mst_data WHERE kodeCategory = 'S01' and statusData = 't'");
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE statusData = 't'");

    $filter = "WHERE t1.status = '535' AND t2.div_id !='0'";
    if(!empty($par[idKantor]))
        $filter.=" AND t2.dir_id = '$par[idKantor]'";
    if(!empty($par[idDivisi]))
        $filter.=" AND t2.div_id = '$par[idUnit]'";
    if(!empty($par[idLokasi]))
        $filter.=" AND t2.location = '$par[idLokasi]'";
    $sql = "SELECT t1.id, t1.cat, t1.religion, t1.nation, t1.gender, t1.ptkp, t2.div_id, t2.rank FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $arrJumlah["divisi"][$arrMaster[$r[div_id]]]++;
        $arrJumlah["rank"][$arrMaster[$r[div_id]]][$r[rank]]++;
        $arrJumlah["status"][$arrMaster[$r[div_id]]][$r[cat]]++;
        $arrJumlah["agama"][$arrMaster[$r[div_id]]][$r[religion]]++;
        $arrJumlah["nation"][$arrMaster[$r[div_id]]][$r[nation]]++;
        $arrJumlah["gender"][$arrMaster[$r[div_id]]][$r[gender]]++;
        $arrJumlah["ptkp"][$arrMaster[$r[div_id]]][$r[ptkp]]++;
    }

    $cols = 4;
    $colsMax = $cols + $countColumnJabatan + $countColumnStatus + 2 + $countColumnAgama + $countColumnPendidikan + $countColumnPerkawinan + $countColumnNation - 1;

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    for ($i = 4; $i <= $colsMax; $i++) {
        $objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(15);
        $cols++;
    }
    $cols = 4;

    $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
    $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:' . numToAlpha($colsMax) . '1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:' . numToAlpha($colsMax) . '2');
    $objPHPExcel->getActiveSheet()->mergeCells('A3:' . numToAlpha($colsMax) . '3');

    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAP JABATAN');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Tanggal : ' . $par[tanggalAbsen]);

    $objPHPExcel->getActiveSheet()->getStyle('A4:' . numToAlpha($colsMax) . '5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A4:' . numToAlpha($colsMax) . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:' . numToAlpha($colsMax) . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:' . numToAlpha($colsMax) . '5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:' . numToAlpha($colsMax) . '4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A5:' . numToAlpha($colsMax) . '5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $colsJabatan = $cols + $countColumnJabatan - 1;
    $colsStatus = $colsJabatan + $countColumnStatus;
    $colsGender = $colsStatus + 2;
    $colsAgama = $colsGender + $countColumnAgama;
    $colsPendidikan = $colsAgama + $countColumnPendidikan;
    $colsPerkawinan = $colsPendidikan + $countColumnPerkawinan;
    $colsNation = $colsPerkawinan + $countColumnNation;

    $objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
    $objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
    $objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols) . '4:' . numToAlpha($colsJabatan) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsJabatan + 1) . '4:' . numToAlpha($colsStatus) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsStatus + 1) . '4:' . numToAlpha($colsGender) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsGender + 1) . '4:' . numToAlpha($colsAgama) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsAgama + 1) . '4:' . numToAlpha($colsPendidikan) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsPendidikan + 1) . '4:' . numToAlpha($colsPerkawinan) . '4');
    $objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($colsPerkawinan + 1) . '4:' . numToAlpha($colsNation) . '4');

    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
    $objPHPExcel->getActiveSheet()->setCellValue('B4', 'UNIT KERJA/CABANG');
    $objPHPExcel->getActiveSheet()->setCellValue('C4', 'TOTAL JUMLAH KARYAWAN');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', 'JABATAN KARYAWAN');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsJabatan + 1) . '4', 'STATUS KARYAWAN');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsStatus + 1) . '4', 'GENDER');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsGender + 1) . '4', 'AGAMA');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsAgama + 1) . '4', 'PENDIDIKAN TERAKHIR');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsPendidikan + 1) . '4', 'STATUS PERKAWINAN');
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($colsPerkawinan + 1) . '4', 'KEWARGANEGARAAN');

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S09' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', $r_[namaData]);
        $cols++;
    }

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S04' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', $r_[namaData]);
        $cols++;
    }

    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', "MALE");
    $cols++;
    $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', "FEMALE");
    $cols++;

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S07' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', substr(str_replace("-", "", $r_[namaData]), 0, 1));
        $cols++;
    }

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'R11' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', $r_[namaData]);
        $cols++;
    }

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S08' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', $r_[namaData]);
        $cols++;
    }

    $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S01' and statusData = 't' order by urutanData";
    $res_ = db($sql_);
    while ($r_ = mysql_fetch_assoc($res_)) {
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . '5', $r_[namaData]);
        $cols++;
    }

    $rows = 6;
    $filter = "where t2.kodeCategory='X03' AND t2.statusData = 't' AND t1.statusData = 't'";
    if(!empty($par[idKantor]))
        $filter.=" AND t1.kodeInduk = '$par[idKantor]'";
    if(!empty($par[idKantor]) && !empty($par[idUnit]))
        $filter.=" AND t1.kodeData = '$par[idUnit]'";
    $sql = "SELECT t1.kodeData, t1.namaData from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk $filter group by t1.namaData order by t1.namaData";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, $no);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rows, strtoupper($r[namaData]));
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rows, $arrJumlah["divisi"][$r[namaData]]);

        $cols = 4;
        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S09' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["rank"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S04' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["status"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["gender"][$r[namaData]]["M"]);
        $cols++;
        $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["gender"][$r[namaData]]["F"]);
        $cols++;

        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S07' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["agama"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'R11' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["pendidikan"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S08' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["ptkp"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $sql_ = "SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S01' and statusData = 't' order by urutanData";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_assoc($res_)) {
            $objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols) . $rows, $arrJumlah["nation"][$r[namaData]][$r_[kodeData]]);
            $cols++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols - 1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $rows++;
    }

    $rows--;

    $cols = 4;
    $colsMax = $cols + $countColumnJabatan + $countColumnStatus + 2 + $countColumnAgama + $countColumnPendidikan + $countColumnPerkawinan + $countColumnNation - 1;

    $objPHPExcel->getActiveSheet()->getStyle('A4:A' . $rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:A' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B4:B' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('C4:C' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    for ($i = 4; $i <= $colsMax; $i++) {
        $objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols) . '4:' . numToAlpha($cols) . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $cols++;
    }

    $objPHPExcel->getActiveSheet()->getStyle('A1:' . numToAlpha($colsMax) . $rows)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:' . numToAlpha($colsMax) . $rows)->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle('A6:' . numToAlpha($colsMax) . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

    $objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
    $objPHPExcel->setActiveSheetIndex(0);

// Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fFile . ucwords(strtolower($arrTitle[$s])) . ".xls");
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        default:
            $text = lihat();
            break;
    }
    return $text;
}

?>
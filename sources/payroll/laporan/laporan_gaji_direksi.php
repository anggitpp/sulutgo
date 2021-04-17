<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

function lihat()
{
    global $s, $par, $arrTitle, $ui;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $par[bulanData] = empty($par[bulanData]) ? date('m') : $par[bulanData];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $sql = "SELECT t1.idPegawai, t1.nilaiProses, t2.kodeKomponen FROM pay_proses_".$par[tahunData].$par[bulanData]." t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t2.kodeKomponen IN ('GPK', 'TJB')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrNilai[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiProses];
    }
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small" onclick="showFilter()" />
                    <input type="button" style="display:none" id="hFilter" value="-" class="btn btn_search btn-small" onclick="hideFilter()" />
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <div style="position: absolute; top: 5px;right: 10px;">
                <?= comboMonth("par[bulanData]", $par[bulanData] ,"onchange=\"document.getElementById('form').submit();\"") ?>
                <?= comboYear("par[tahunData]", $par[tahunData], "5", "onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <fieldset id="form_filter" style="display:none">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createComboData("Lokasi", $queryLokasi, "id", "description", "par[idLokasi]", $par[idLokasi], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryPangkat, "id", "description", "par[idPangkat]", $par[idPangkat], "", "", "t", "", "t") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createComboData("Group Process", $queryGroup, "id", "description", "par[idGroup]", $par[idGroup], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Jabatan", $queryJabatan, "id", "description", "par[idJabatan]", $par[idJabatan], "", "", "t", "", "t") ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <br clear="all" />
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="*">NAMA</th>
                <th width="200">CABANG/UNIT KERJA</th>
                <th width="100">Status Pegawai</th>
                <th width="100">K/TK</th>
                <th width="100">Skala</th>
                <th width="100">Pangkat</th>
                <th width="100">Job Group</th>
                <th width="100">Gaji Dasar</th>
                <th width="100">Pembulatan</th>
                <th width="100">Gaji Bersih</th>
                <th width="100">Tj. Jabatan</th>
                <th width="100">Gaji Bruto</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t1.status = '535' AND t1.cat = '4733' AND t2.rank IN ('3141', '4073')";
            if (!empty($par[filterData]))
                $filter .= " and (lower(name) LIKE '%$par[filterData]%' OR lower(reg_no) LIKE '%$par[filterData]%' OR lower(pos_name) LIKE '%$par[filterData]%')";
            if (!empty($par[idLokasi]))
                $filter .= " and t2.location = '$par[idLokasi]'";
            if (!empty($par[idGroup]))
                $filter .= " and t2.proses_id = '$par[idGroup]'";
            if (!empty($par[idPangkat]))
                $filter .= " and t2.rank = '$par[idPangkat]'";
            if (!empty($par[idJabatan]))
                $filter .= " and t2.pos_name = '$par[idJabatan]'";
            $sql = "SELECT t1.id, t1.join_date, t1.name, t1.cat, t1.marital, t2.div_id, t2.skala, t2.rank, t2.job_group from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter group by t1.id order by name";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $no++;
                $r[marital] = $arrMaster[$r[marital]] == "Kawin" ? "K" : "TK";
                $gajiPokok = $arrNilai[$r[id]]["GPK"];
                $pembulatan = $gajiPokok != 0 &&  substr($gajiPokok, -2, 2) != '00' && substr($gajiPokok, -2, 2) != '0'  ? 100 - substr($gajiPokok, -2, 2) : 0;
                $gajiBersih = $gajiPokok + $pembulatan;
                $tunjanganJabatan = $arrNilai[$r[id]]["TJB"];
                $gajiBruto = $gajiBersih + $tunjanganJabatan;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td><?= $arrMaster[$r[div_id]] ?></td>
                    <td><?= $arrMaster[$r[cat]] ?></td>
                    <td><?= $r[marital] ?></td>
                    <td><?= $arrMaster[$r[skala]] ?></td>
                    <td><?= $arrMaster[$r[rank]] ?></td>
                    <td><?= $arrMaster[$r[job_group]] ?></td>
                    <td align="right"><?= getAngka($gajiPokok) ?></td>
                    <td align="right"><?= getAngka($pembulatan) ?></td>
                    <td align="right"><?= getAngka($gajiBersih) ?></td>
                    <td align="right"><?= getAngka($tunjanganJabatan) ?></td>
                    <td align="right"><?= getAngka($gajiBruto) ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <script>
        function showFilter() {
            jQuery('#form_filter').show('fast');
            jQuery('#sFilter').hide();
            jQuery('#hFilter').show();
        }

        function hideFilter() {
            jQuery('#form_filter').hide('fast');
            jQuery('#sFilter').show();
            jQuery('#hFilter').hide();
        }
    </script>
    <style>
        .chosen-container {
            min-width: 200px;
        }
    </style>
    <?php
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    ?>
    <?php
}

function xls()
{

    global $s, $arrTitle, $fExport, $par;

    $par[bulanData] = empty($par[bulanData]) ? date('m') : $par[bulanData];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . " - ".getBulan($par[bulanData])." ".$par[tahunData];

    $sql = "SELECT t1.idPegawai, t1.nilaiProses, t2.kodeKomponen FROM pay_proses_".$par[tahunData].$par[bulanData]." t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t2.kodeKomponen IN ('GPK', 'TJB')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrNilai[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiProses];
    }

    $field = array("no",  "nama", "cabang/unit kerja", "status pegawai", "k/tk", "skala", "pangkat", "job group", "gaji dasar", "pembulatan", "gaji bersih", "tj.jabatan", "gaji bruto");

    $filter = "WHERE t1.status = '535' AND t1.cat = '4733' AND t2.rank IN ('3141', '4073')";
    if (!empty($par[filterData]))
        $filter .= " and (lower(name) LIKE '%$par[filterData]%' OR lower(reg_no) LIKE '%$par[filterData]%' OR lower(pos_name) LIKE '%$par[filterData]%')";
    if (!empty($par[idLokasi]))
        $filter .= " and t2.location = '$par[idLokasi]'";
    if (!empty($par[idGroup]))
        $filter .= " and t2.proses_id = '$par[idGroup]'";
    if (!empty($par[idPangkat]))
        $filter .= " and t2.rank = '$par[idPangkat]'";
    if (!empty($par[idJabatan]))
        $filter .= " and t2.pos_name = '$par[idJabatan]'";
    $sql = "SELECT t1.id, t1.join_date, t1.name, t1.cat, t1.marital, t2.div_id, t2.skala, t2.rank, t2.job_group from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter group by t1.id order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[marital] = $arrMaster[$r[marital]] == "Kawin" ? "K" : "TK";
        $gajiPokok = $arrNilai[$r[id]]["GPK"];
        $pembulatan = $gajiPokok != 0 &&  substr($gajiPokok, -2, 2) != '00' && substr($gajiPokok, -2, 2) != '0'  ? 100 - substr($gajiPokok, -2, 2) : 0;
        $gajiBersih = $gajiPokok + $pembulatan;
        $tunjanganJabatan = $arrNilai[$r[id]]["TJB"];
        $gajiBruto = $gajiBersih + $tunjanganJabatan;

        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            $arrMaster[$r[cat]] . "\t left",
            $r[marital] . "\t left",
            $arrMaster[$r[skala]] . "\t left",
            $arrMaster[$r[pangkat]] . "\t left",
            $arrMaster[$r[job_group]] . "\t left",
            getAngka($gajiPokok) . "\t right",
            getAngka($pembulatan) . "\t right",
            getAngka($gajiBersih) . "\t right",
            getAngka($tunjanganJabatan) . "\t right",
            getAngka($gajiBruto) . "\t right",
        );
        $totalPokok += $gajiPokok;
        $totalPembulatan += $pembulatan;
        $totalBersih += $gajiBersih;
        $totalJabatan += $tunjanganJabatan;
        $totalBruto += $gajiBruto;
    }
    exportXLS($direktori, $namaFile, $judul, 13, $field, $data, true, 8, array("TOTAL\t right", getAngka($totalPokok)."\t right", getAngka($totalPembulatan)."\t right", getAngka($totalBersih)."\t right", getAngka($totalJabatan)."\t right", getAngka($totalBruto)."\t right"));
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

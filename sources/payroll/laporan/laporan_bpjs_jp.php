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

    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];
    $par[bulanData] = empty($par[bulanData]) ? date('m') : $par[bulanData];

    $sql = "SELECT t1.idPegawai, t1.nilaiUpload, t2.kodeKomponen FROM pay_upload t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t1.bulanUpload = '$par[bulanData]' AND t1.tahunUpload = '$par[tahunData]' AND t2.kodeKomponen IN ('P45', 'P46')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrNilai[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiUpload];
    }

    $sql = "SELECT t1.idPegawai, t1.nilaiProses, t2.kodeKomponen FROM pay_proses_".$par[tahunData].$par[bulanData]." t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t2.kodeKomponen IN ('GPK', 'TJB')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrGaji[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiProses];
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
                <th width="100">Gaji Bruto</th>
                <th width="100">Iuran Perusahaan</th>
                <th width="100">Iuran Pegawai</th>
                <th width="100">Total Iuran</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t1.status = '535'";
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
            $sql = "SELECT t1.id, t1.join_date, t1.name, t2.div_id from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter group by t1.id order by name";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                if(!empty(isset($arrNilai[$r[id]]))){
                    $no++;
                    $gajiPokok = $arrGaji[$r[id]]["GPK"];
                    $pembulatan = 0;
                    $gajiBersih = $gajiPokok + $pembulatan;
                    $tunjanganJabatan = $arrGaji[$r[id]]["TJB"];
                    $gajiBruto = $gajiBersih + $tunjanganJabatan;
                    $iuranPerusahaan = $arrNilai[$r[id]]['P45'];
                    $iuranPegawai = $arrNilai[$r[id]]['P46'];
                    $iuranTotal = $iuranPegawai + $iuranPerusahaan;
                    ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td><?= $arrMaster[$r[div_id]] ?></td>
                        <td align="right"><?= getAngka($gajiBruto) ?></td>
                        <td align="right"><?= getAngka($iuranPerusahaan) ?></td>
                        <td align="right"><?= getAngka($iuranPegawai) ?></td>
                        <td align="right"><?= getAngka($iuranTotal) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <script>
        function showFilter() {
            jQuery('#form_filter').show('fast');
            jQuery('#sFilter').hide();
            jQuery('#hFilter').show();x
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

    $sql = "SELECT t1.idPegawai, t1.nilaiUpload, t2.kodeKomponen FROM pay_upload t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t1.bulanUpload = '$par[bulanData]' AND t1.tahunUpload = '$par[tahunData]' AND t2.kodeKomponen IN ('P45', 'P46')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrNilai[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiUpload];
    }

    $sql = "SELECT t1.idPegawai, t1.nilaiProses, t2.kodeKomponen FROM pay_proses_".$par[tahunData].$par[bulanData]." t1 join dta_komponen t2 on t1.idKomponen = t2.idKomponen WHERE t2.kodeKomponen IN ('GPK', 'TJB')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrGaji[$r[idPegawai]][$r[kodeKomponen]] = $r[nilaiProses];
    }

    $field = array("no",  "nama", "cabang/unit kerja", "gaji bruto", "iuran perusahaan", "iuran pegawai", "total iuran");

    $filter = "WHERE t1.status = '535'";
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
    $sql = "SELECT t1.id, t1.name, t2.div_id from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        if (!empty(isset($arrNilai[$r[id]]))) {
            $no++;
            $gajiPokok = $arrGaji[$r[id]]["GPK"];
            $pembulatan = 0;
            $gajiBersih = $gajiPokok + $pembulatan;
            $tunjanganJabatan = $arrGaji[$r[id]]["TJB"];
            $gajiBruto = $gajiBersih + $tunjanganJabatan;
            $iuranPerusahaan = $arrNilai[$r[id]]['P45'];
            $iuranPegawai = $arrNilai[$r[id]]['P46'];
            $iuranTotal = $iuranPegawai + $iuranPerusahaan;
            $data[] = array(
                $no . "\t center",
                $r[name] . "\t left",
                $arrMaster[$r[div_id]] . "\t left",
                getAngka($gajiBruto) . "\t right",
                getAngka($iuranPerusahaan) . "\t right",
                getAngka($iuranPegawai) . "\t right",
                getAngka($iuranTotal) . "\t right",
            );
            $totalGaji += $gajiBruto;
            $totalPerusahaan += $iuranPerusahaan;
            $totalPegawai += $iuranPegawai;
            $totalIuran += $iuranTotal;
        }
    }
    exportXLS($direktori, $namaFile, $judul, 7, $field, $data, true, 4, array(getAngka($totalGaji)."\t right", getAngka($totalPerusahaan)."\t right", getAngka($totalPegawai)."\t right", getAngka($totalIuran)."\t right"));
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

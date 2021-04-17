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

    $sql="select namaKomponen, tipeKomponen, nilaiProses, idPegawai from pay_proses_".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
        $arrNilai[$r[idPegawai]]["$r[tipeKomponen]"] += $r[nilaiProses];
        $tipeKomponen = $r[tipeKomponen];
        $urutanKomponen++;
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
                <th width="100">No. Rekening</th>
                <th width="100">Gaji Bruto</th>
                <th width="100">Total Potongan</th>
                <th width="100">THP</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t1.status = '535' ";
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
            $sql = "SELECT x1.nilaiProses, t1.id, t1.join_date, t1.name, t3.account_no from pay_proses_".$par[tahunData].$par[bulanData]." x1 join emp t1 on x1.idPegawai = t1.id join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_bank t3 on t1.id = t3.parent_id $filter group by t1.id order by name";
//            e
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $no++;
                $nilaiBruto = $arrNilai[$r[id]]["t"];
                $nilaiPotongan = $arrNilai[$r[id]]["p"];
                $nilaiTHP = $nilaiBruto - $nilaiPotongan;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td><?= $r[account_no] ?></td>
                    <td align="right"><?= getAngka($nilaiBruto) ?></td>
                    <td align="right"><?= getAngka($nilaiPotongan) ?></td>
                    <td align="right"><?= getAngka($nilaiTHP) ?></td>
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

    $sql="select namaKomponen, tipeKomponen, nilaiProses, idPegawai from pay_proses_".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
        $arrNilai[$r[idPegawai]]["$r[tipeKomponen]"] += $r[nilaiProses];
        $tipeKomponen = $r[tipeKomponen];
        $urutanKomponen++;
    }

    $field = array("no",  "nama", "no rekening", "gaji bruto", "total potongan", "take home pay");

    $filter = "WHERE t1.status = '535' ";
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
    $sql = "SELECT x1.nilaiProses, t1.id, t1.join_date, t1.name, t3.account_no from pay_proses_".$par[tahunData].$par[bulanData]." x1 join emp t1 on x1.idPegawai = t1.id join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_bank t3 on t1.id = t3.parent_id $filter group by t1.id order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $nilaiBruto = $arrNilai[$r[id]]["t"];
        $nilaiPotongan = $arrNilai[$r[id]]["p"];
        $nilaiTHP = $nilaiBruto - $nilaiPotongan;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[account_no] . "\t left",
            getAngka($nilaiBruto) . "\t right",
            getAngka($nilaiPotongan) . "\t right",
            getAngka($nilaiTHP) . "\t right",
        );
        $totalBruto += $nilaiBruto;
        $totalPotongan += $nilaiPotongan;
        $totalTHP += $nilaiTHP;
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data, true, 3, array("TOTAL\tright",getAngka($totalBruto)."\t right",getAngka($totalPotongan)."\t right", getAngka($totalTHP)."\t right"));
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

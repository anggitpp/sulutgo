<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $areaCheck;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' order by t1.urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";

    $arrMaster =arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $par[bulanData] = empty($par[bulanData]) ? date('m') : $par[bulanData];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div style="position: absolute; top: 5px;right: 10px;">
                <?= comboMonth("par[bulanData]", $par[bulanData], "onchange=\"document.getElementById('form').submit();\"") ?>
                <?= comboYear("par[tahunData]", $par[tahunData], "5","onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <div id="pos_l" style="float:left;">
                <p>
                    <?= comboData($queryLokasi, "id", "description", "par[idLokasi]", "- Pilih Lokasi -", $par[idLokasi],"", "","chosen-select") ?>
                    <input type="submit" value="GO" class="btn btn_search btn-small" />
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small" onclick="showFilter()" />
                    <input type="button" style="display:none" id="hFilter" value="-" class="btn btn_search btn-small" onclick="hideFilter()" />
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <br clear="all" />
            <fieldset id="form_filter" style="display:none">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createComboData("Pangkat", $queryPangkat, "id", "description", "par[idPangkat]", $par[idPangkat], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Unit Kerja", $queryDiv, "id", "description", "par[idUnit]", $par[idUnit], "", "", "t", "", "t") ?></p>
                        </td>
                        <td width="50%">
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
                <th rowspan="2" width="20">No.</th>
                <th rowspan="2" width="*">Nama</th>
                <th rowspan="2" width="100">Bagian</th>
                <th rowspan="2" width="100">Jabatan</th>
                <th colspan="2" width="100">Tanggal</th>
            </tr>
            <tr>
                <th>Mulai</th>
                <th>Selesai</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t1.status = '535' AND t2.location IN ($areaCheck) AND ".$par[tahunData].$par[bulanData]." BETWEEN concat(year(x1.start_date), LPAD(MONTH(x1.start_date), 2, '0')) AND concat(year(x1.end_date), LPAD(MONTH(x1.end_date), 2, '0')) ";
            if(!empty($par[idLokasi]))
                $filter.=" AND t2.location = '$par[idLokasi]'";
            if(!empty($par[idPangkat]))
                $filter.=" AND t2.rank = '$par[idPangkat]'";
            if(!empty($par[idUnit]))
                $filter.= " AND t2.div_id = '$par[idUnit]'";
            $sql = "SELECT t1.name, x1.pos_name, x1.rank, x1.start_date, x1.end_date FROM emp_contract x1 join emp t1 on x1.parent_id = t1.id join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
            $res = db($sql);
            while ($r = mysql_fetch_assoc($res)){
                $no++;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[rank]] ?></td>
                    <td align="center"><?= getTanggal($r[start_date]) ?></td>
                    <td align="center"><?= getTanggal($r[end_date]) ?></td>
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
    global $s, $arrTitle, $fExport, $areaCheck, $par;

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $arrMaster =arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $field = array("no", "nama", "bagian", "jabatan", "tanggal" => array("mulai", "selesai"));

    $filter = "WHERE t1.status = '535' AND t2.location IN ($areaCheck) AND ".$par[tahunData].$par[bulanData]." BETWEEN concat(year(x1.start_date), LPAD(MONTH(x1.start_date), 2, '0')) AND concat(year(x1.end_date), LPAD(MONTH(x1.end_date), 2, '0')) ";
    if(!empty($par[idLokasi]))
        $filter.=" AND t2.location = '$par[idLokasi]'";
    if(!empty($par[idPangkat]))
        $filter.=" AND t2.rank = '$par[idPangkat]'";
    if(!empty($par[idUnit]))
        $filter.= " AND t2.div_id = '$par[idUnit]'";
    $sql = "SELECT t1.name, x1.pos_name, x1.rank, x1.start_date, x1.end_date FROM emp_contract x1 join emp t1 on x1.parent_id = t1.id join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[pos_name] . "\t left",
            $arrMaster[$r[rank]] . "\t left",
            getTanggal($r[start_date]) . "\t center",
            getTanggal($r[end_date]) . "\t center",
        );
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
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
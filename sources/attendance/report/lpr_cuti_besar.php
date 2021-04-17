<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $arrParam;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryGroup = "SELECT kodeData id, namaData description, kodeInduk from mst_data where statusData = 't' and kodeCategory='" . $arrParameter[49] . "' order by kodeInduk,urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData = 't'");
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form action="?_submit=1<?= getPar($par) ?>" method="post" id="form" class="stdform">
            <div id="pos_l" style="float:left;">
                <p>
                    <input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>"
                           style="width:200px;"/> <input type="submit" value="GO" class="btn btn_search btn-small"/>
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small"
                           onclick="showFilter()"/> <input type="button" style="display:none" id="hFilter" value="-"
                                                           class="btn btn_search btn-small" onclick="hideFilter()"/>
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
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
        <br clear="all"/>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th rowspan="2" width="20">No.</th>
                <th rowspan="2" width="*">NAMA</th>
                <th rowspan="2" width="80">ID</th>
                <th rowspan="2" width="150">Jabatan</th>
                <th rowspan="2" width="150">Unit Kerja</th>
                <th colspan="2" width="160">Tanggal</th>
                <th colspan="3" width="150">Detail Cuti (Hari)</th>
                <th rowspan="2" width="150">Keterangan</th>
            </tr>
            <tr>
                <th width="80">Mulai</th>
                <th width="80">Selesai</th>
                <th width="50">Jatah</th>
                <th width="50">Jumlah</th>
                <th width="50">Sisa</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t4.namaCuti LIKE 'Cuti Besar%'";
            if (!empty($par[filterKategori]))
                $filter .= " and lower(pos_name) LIKE '" . strtolower($par[filterKategori]) . "%'";
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
            $sql = "SELECT t1.name, t1. reg_no, t2.pos_name, t2.div_id, t3.mulaiCuti, t3.selesaiCuti, t3.jatahCuti, t3.jumlahCuti, t3.sisaCuti, t3.keteranganCuti FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') join att_cuti t3 join dta_cuti t4 on t3.idTipe = t4.idCuti on t1.id = t3.idPegawai $filter";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $no++;
                ?>
                <tr>
                    <td><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td align="center"><?= $r[reg_no] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[div_id]] ?></td>
                    <td align="center"><?= getTanggal($r[mulaiCuti]) ?></td>
                    <td align="center"><?= getTanggal($r[selesaiCuti]) ?></td>
                    <td align="center"><?= $r[jatahCuti] ?></td>
                    <td align="center"><?= $r[jumlahCuti] ?></td>
                    <td align="center"><?= $r[sisaCuti] ?></td>
                    <td><?= $r[keteranganCuti] ?></td>
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

    global $s, $par, $arrTitle, $menuAccess, $ui, $arrParameter, $arrParam, $fExport;

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData = 't'");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "nama", "id", "jabatan", "unit kerja", "tanggal" => array("mulai", "selesai"), "detail cuti (hari)" => array("jatah", "jumlah", "sisa"), "keterangan");

    $filter = "WHERE t4.namaCuti LIKE 'Cuti Besar%'";
    if (!empty($par[filterKategori]))
        $filter .= " and lower(pos_name) LIKE '" . strtolower($par[filterKategori]) . "%'";
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
    $sql = "SELECT t1.name, t1. reg_no, t2.pos_name, t2.div_id, t3.mulaiCuti, t3.selesaiCuti, t3.jatahCuti, t3.jumlahCuti, t3.sisaCuti, t3.keteranganCuti FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') join att_cuti t3 join dta_cuti t4 on t3.idTipe = t4.idCuti on t1.id = t3.idPegawai $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;

        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            getTanggal($r[mulaiCuti]) . "\t left",
            getTanggal($r[selesaiCuti]) . "\t left",
            $r[jatahCuti] . "\t left",
            $r[jumlahCuti] . "\t left",
            $r[sisaCuti] . "\t left",
            $r[keteranganCuti] . "\t left",
        );
    }
    exportXLS($direktori, $namaFile, $judul, 11, $field, $data);
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
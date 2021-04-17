<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

function lihat()
{
    global $s, $par, $arrTitle, $ui, $areaCheck, $arrParam;

    $queryLokasi = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData";
    $queryPangkat = "SELECT kodeData id, namaData description FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData";
    $queryStatus = "SELECT kodeData id, namaData description from mst_data where statusData = 't' and kodeCategory='S04' order by urutanData";
    $queryJabatan = "SELECT DISTINCT(pos_name) description, pos_name id FROM emp_phist ORDER BY pos_name ASC";

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $kontrakBulan = date('m') > 9 ? date('m') + 3 - 12 : date('m') + 3;
    $kontrakTahun = date('m') > 9 ? date('Y') + 1 : date('Y');
    $kontrakMax = $kontrakTahun . str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);
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
                           style="width:200px;"/>
                    <input type="submit" value="GO" class="btn btn_search btn-small"/>
                    <input type="button" id="sFilter" value="+" class="btn btn_search btn-small"
                           onclick="showFilter()"/>
                    <input type="button" style="display:none" id="hFilter" value="-" class="btn btn_search btn-small"
                           onclick="hideFilter()"/>
                </p>
            </div>
            <div id="pos_r" style="float:right; margin-top:5px;">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <br clear="all"/>
            <fieldset id="form_filter" style="display:none">
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <p><?= $ui->createComboData("Lokasi", $queryLokasi, "id", "description", "par[idLokasi]", $par[idLokasi], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Pangkat", $queryPangkat, "id", "description", "par[idPangkat]", $par[idPangkat], "", "", "t", "", "t") ?></p>
                        </td>
                        <td width="50%">
                            <p><?= $ui->createComboData("Status", $queryStatus, "id", "description", "par[idStatus]", $par[idStatus], "", "", "t", "", "t") ?></p>
                            <p><?= $ui->createComboData("Jabatan", $queryJabatan, "id", "description", "par[idJabatan]", $par[idJabatan], "", "", "t", "", "t") ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="100">NPP</th>
                <th width="*">Nama</th>
                <th width="150">Jabatan</th>
                <th width="150">Unit Kerja</th>
                <th width="100">Sisa Hari</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $filterStatus = $arrParam[$s] == 1 ? " and t1.cat = '4345'" : "and t1.cat != '4345'";
            $filter = " WHERE t1.status = '535' $filterStatus and concat(year(t1.leave_date), LPAD(month(t1.leave_date),2,'0')) between '" . date('Y') . str_pad(date('m'), 2, "0", STR_PAD_LEFT) . "' and  '" . $kontrakMax . "'";
            if (!empty($par[filterData]))
                $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t1.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";
            if (!empty($par[idLokasi]))
                $filter .= " and t2.location = '$par[idLokasi]'";
            if (!empty($par[idStatus]))
                $filter .= " and t1.cat = '$par[idStatus]'";
            if (!empty($par[idPangkat]))
                $filter .= " and t2.rank = '$par[idPangkat]'";
            if (!empty($par[idJabatan]))
                $filter .= " and t2.pos_name = '$par[idJabatan]'";
            $sql = "SELECT t1.id as idPegawai, t1.name, t1.reg_no, t2.pos_name, t2.dir_id, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t1.leave_date),' Hari') sisaHari FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by t1.name";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_assoc($res)) {
                $no++;
                ?>
                <tr>
                    <td align="center"><?= $no ?></td>
                    <td align="center"><?= $r[reg_no] ?></td>
                    <td><?= $r[name] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[dir_id]] ?></td>
                    <td align="right"><?= $r[sisaHari] ?></td>
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
        #par_idSK__chosen {
            min-width: 80px;
        }

        .chosen-container {
            min-width: 200px;
        }
    </style>
    <?php
    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    ?><?php
}

function xls()
{
    global $s, $arrTitle, $fExport, $par, $arrParam;

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "Laporan Akan Habis Kontrak";

    $field = array("no", "nik", "nama", "jabatan", "unit kerja", "sisa hari");

    $kontrakBulan = date('m') > 9 ? date('m') + 3 - 12 : date('m') + 3;
    $kontrakTahun = date('m') > 9 ? date('Y') + 1 : date('Y');
    $kontrakMax = $kontrakTahun . str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);

    $filterStatus = $arrParam[$s] == 1 ? " and t1.cat = '4345'" : "and t1.cat != '4345'";
    $filter = " WHERE t1.status = '535' $filterStatus and concat(year(t1.leave_date), LPAD(month(t1.leave_date),2,'0')) between '" . date('Y') . str_pad(date('m'), 2, "0", STR_PAD_LEFT) . "' and  '" . $kontrakMax . "'";
    if (!empty($par[filterData]))
        $filter .= " and (lower(t1.name) LIKE '%" . strtolower($par[filterData]) . "%' OR lower(t1.reg_no) LIKE '%" . strtolower($par[filterData]) . "%')";
    if (!empty($par[idLokasi]))
        $filter .= " and t2.location = '$par[idLokasi]'";
    if (!empty($par[idStatus]))
        $filter .= " and t1.cat = '$par[idStatus]'";
    if (!empty($par[idPangkat]))
        $filter .= " and t2.rank = '$par[idPangkat]'";
    if (!empty($par[idJabatan]))
        $filter .= " and t2.pos_name = '$par[idJabatan]'";
    $sql = "SELECT t1.id as idPegawai, t1.name, t1.reg_no, t2.pos_name, t2.dir_id, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t1.leave_date),' Hari') sisaHari FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by t1.name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[dir_id]]."\t left",
            $r[sisaHari]."\t right"
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
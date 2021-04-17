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
                    <th width="150">CABANG</th>
                    <th width="80">GAJI DASAR</th>
                    <th width="150">PhDP</th>
                    <th width="150">DONASI PENSIUN 19.8%</th>
                    <th width="80">IURAN PENSIUN 5%</th>
                    <th width="80">JUMLAH IURAN</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $filter = "WHERE t1.status = '535' AND year(cat_date) <= 2008 AND t1.cat = '531'";
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
                $sql = "SELECT t1.id, t1.join_date, t1.name, t2.div_id, t3.nilaiProses from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') join pay_proses_".$par[tahunData].$par[bulanData]." t3 on t1.id = t3.idPegawai join dta_komponen t4 on (t3.idKomponen = t4.idKomponen AND t4.kodeKomponen = 'GPK') $filter group by t1.id order by name";
                $res = db($sql);
                $no = 0;
                while ($r = mysql_fetch_assoc($res)) {
                    $no++;
                    $r[phdp] = $r[nilaiProses] * 87 / 100;
                    $r[donasiPensiun] = $r[phdp] * 19.8 / 100;
                    $r[iuranPensiun] = $r[phdp] * 5 / 100;
                    $r[totalIuran] = $r[donasiPensiun] + $r[iuranPensiun];
                ?>
                    <tr>
                        <td><?= $no ?>.</td>
                        <td><?= $r[name] ?></td>
                        <td><?= $arrMaster[$r[div_id]] ?></td>
                        <td align="right"><?= getAngka($r[nilaiProses]) ?></td>
                        <td align="right"><?= getAngka($r[phdp]) ?></td>
                        <td align="right"><?= getAngka($r[donasiPensiun]) ?></td>
                        <td align="right"><?= getAngka($r[iuranPensiun]) ?></td>
                        <td align="right"><?= getAngka($r[totalIuran]) ?></td>
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

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $field = array("no",  "nama", "cabang", "gaji dasar", "phdp", "donasi pensiun 19.8%", "iuran pensiun 5%", "jumlah iuran");

    $filter = "WHERE t1.status = '535' AND year(cat_date) <= 2008 AND t1.cat = '531'";
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
    $sql = "SELECT t1.id, t1.join_date, t1.name, t2.div_id, t3.nilaiProses from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') join pay_proses_".$par[tahunData].$par[bulanData]." t3 on t1.id = t3.idPegawai join dta_komponen t4 on (t3.idKomponen = t4.idKomponen AND t4.kodeKomponen = 'GPK') $filter group by t1.id order by name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[phdp] = $r[nilaiProses] * 87 / 100;
        $r[donasiPensiun] = $r[phdp] * 19.8 / 100;
        $r[iuranPensiun] = $r[phdp] * 5 / 100;
        $r[totalIuran] = $r[donasiPensiun] + $r[iuranPensiun];
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            getAngka($r[nilaiProses]) . "\t right",
            getAngka($r[phdp]) . "\t right",
            getAngka($r[donasiPensiun]) . "\t right",
            getAngka($r[iuranPensiun]) . "\t right",
            getAngka($r[totalIuran]) . "\t right"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}


function getContent($par)
{
    global $menuAccess, $s, $_submit, $m;
    switch ($par[mode]) {
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah();
            else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah();
            else $text = lihat();
            break;
        case "end":
            if (isset($menuAccess[$s]["add"])) $text = endProses();
            break;
        case "dat":
            if (isset($menuAccess[$s]["add"])) $text = setData();
            break;
        case "tab":
            if (isset($menuAccess[$s]["add"])) $text = setFile();
            break;
        case "upl":
            $text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus();
            else $text = lihat();
            break;
        case "subData":
            $text = subData();
            break;
        default:
            $text = lihat();
            break;
    }

    return $text;
}

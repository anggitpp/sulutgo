<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function lihat(){
    global $s, $par, $arrTitle;

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    if(empty($par[tahunCuti])) $par[tahunCuti] = date('Y');

    $queryLocation = "SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory = 'S06' AND statusData = 't' order by namaData";

    $sql = "SELECT idPegawai, jumlahCuti FROM att_cuti WHERE year(mulaiCuti) = '$par[tahunCuti]'";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrJumlahCuti[$r[idPegawai]] += $r[jumlahCuti];
    }
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <br clear="all"/>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" class="stdform" method="post">
            <div style="position: absolute; top: 5px;right: 10px;">
                <?= comboYear("par[tahunCuti]", $par[tahunCuti], "5", "onchange=\"document.getElementById('form').submit();\"") ?>
            </div>
            <div id="pos_r">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <div id="pos_l">
                <input type="text" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" class="smallinput" style="width: 200px" />
                <?= comboData($queryLocation, "id", "description", "par[idLokasi]", "- SEMUA LOKASI -", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"", "","chosen-select") ?>
                <input type="submit" class="btnSubmit" value="GO">
            </div>
        </form>
        <br clear="all"/>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dynscroll">
            <thead>
                <tr>
                    <th width="20">No.</th>
                    <th width="*">Nama</th>
                    <th width="100">NIK</th>
                    <th width="150">Jabatan</th>
                    <th width="150">Unit Kerja</th>
                    <th width="100">Jumlah Cuti</th>
                    <th width="100">Sisa Cuti</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $filter = "WHERE t1.id is not null";
            if(!empty($par[filterData]))
                $filter.=" AND lower(t1.name) LIKE '%".strtolower($par[filterData])."%' OR lower(t1.reg_no) LIKE '%".strtolower($par[filterData])."%' ";
            if(!empty($par[idLokasi]))
                $filter.=" AND t2.location = '$par[idLokasi]'";
            $sql="select t1.id, t1.name, t1.reg_no, t2.pos_name, t2.div_id FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by t1.name";
            $res=db($sql);
            $getTotalCuti = getField("SELECT sum(jatahCuti) FROM dta_cuti WHERE year(mulaiCuti) = '$par[tahunCuti]'");
            while($r=mysql_fetch_array($res)){
                $no++;
                ?>
                <tr>
                    <td align="center"><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td><?= $r[reg_no] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[div_id]] ?></td>
                    <td align="center"><?= empty($arrJumlahCuti[$r[id]]) ? 0 : $arrJumlahCuti[$r[id]]  ?></td>
                    <td align="center"><?= $getTotalCuti - $arrJumlahCuti[$r[id]] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
    if($par[mode] == "xls"){
        xls();
        echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function xls()
{
    global $s, $arrTitle, $fExport, $par;

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $sql = "SELECT idPegawai, jumlahCuti FROM att_cuti WHERE year(mulaiCuti) = '$par[tahunCuti]'";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrJumlahCuti[$r[idPegawai]] += $r[jumlahCuti];
    }

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "nama", "nik", "jabatan", "unit kerja", "jumlah cuti", "sisa cuti");

    $filter = "WHERE t1.id is not null";
    if(!empty($par[filterData]))
        $filter.=" AND lower(t1.name) LIKE '%".strtolower($par[filterData])."%' OR lower(t1.reg_no) LIKE '%".strtolower($par[filterData])."%' ";
    if(!empty($par[idLokasi]))
        $filter.=" AND t2.location = '$par[idLokasi]'";
    $sql="select t1.id, t1.name, t1.reg_no, t2.pos_name, t2.div_id FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter order by t1.name";
    $res=db($sql);
    $getTotalCuti = getField("SELECT sum(jatahCuti) FROM dta_cuti WHERE year(mulaiCuti) = '$par[tahunCuti]'");
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t left",
            $r[pos_name] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            empty($arrJumlahCuti[$r[id]]) ? 0 : $arrJumlahCuti[$r[id]] . "\t center",
            $getTotalCuti - $arrJumlahCuti[$r[id]] . "\t center"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 7, $field, $data);
}

function getContent($par){
    global $db,$s,$_submit,$menuAccess;
    switch($par[mode]){
        default:
            $text = lihat();
            break;
    }
    return $text;
}
?>
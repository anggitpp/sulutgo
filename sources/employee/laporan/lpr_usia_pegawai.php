<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function lihat(){
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
    <form id="form" action="" method="post" class="stdform">
        <div style="position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;">
            Lokasi Kerja : <?= comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px") ?>
        </div>
        <div id="pos_r">
            <a href="?par[mode]=xls<?= getPar($par,"mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
        </div>
    </form>
    <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
    <thead>
    <tr>
        <th width="20">No.</th>
        <th width="*">Umur</th>
        <th width="100">L</th>
        <th width="100">P</th>
        <th width="75">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $filter = !empty($par[idLokasi]) ? " AND t2.location = '$par[idLokasi]'" : "";
    $sql = "SELECT t1.id, gender, cat, TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ) as umur FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        if($r[umur] <= 20) {
            $arrUmur["17 - 20 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 25) {
            $arrUmur["21 - 25 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 30) {
            $arrUmur["26 - 30 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 35) {
            $arrUmur["31 - 35 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 40) {
            $arrUmur["36 - 40 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 45) {
            $arrUmur["41 - 45 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 50) {
            $arrUmur["46 - 50 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 55) {
            $arrUmur["51 - 55 Tahun"][$r[gender]]++;
        }else if($r[umur] > 55) {
            $arrUmur["&gt;56 Tahun"][$r[gender]]++;
        }
    }

    $no=0;
    foreach ($arrUmur as $key => $value){
        $no++;
        ?>
        <tr>
            <td><?= $no ?>.</td>
            <td><?= $key ?></td>
            <td align="right"><?= $arrUmur[$key]["M"] ?></td>
            <td align="right"><?= $arrUmur[$key]["F"] ?></td>
            <td align="right"><?= $arrUmur[$key]["M"] + $arrUmur[$key]["F"] ?></td>
        </tr>
        <?php
    }

    if ($par[mode] == "xls") {
        xls();
        echo "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
}

function xls()
{

    global $s, $par, $arrTitle, $fExport;

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "Umur", "L", "P","JUMLAH");

    $filter = !empty($par[idLokasi]) ? " AND t2.location = '$par[idLokasi]'" : "";
    $sql = "SELECT t1.id, gender, cat, TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ) as umur FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        if($r[umur] <= 20) {
            $arrUmur["17 - 20 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 25) {
            $arrUmur["21 - 25 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 30) {
            $arrUmur["26 - 30 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 35) {
            $arrUmur["31 - 35 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 40) {
            $arrUmur["36 - 40 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 45) {
            $arrUmur["41 - 45 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 50) {
            $arrUmur["46 - 50 Tahun"][$r[gender]]++;
        }else if($r[umur] <= 55) {
            $arrUmur["51 - 55 Tahun"][$r[gender]]++;
        }else if($r[umur] > 55) {
            $arrUmur["&gt;56 Tahun"][$r[gender]]++;
        }
    }

    $no=0;
    foreach ($arrUmur as $key => $value){
        $no++;
        $data[] = array(
            $no . "\t center",
            $key . "\t left",
            $arrUmur[$key]["M"] . "\t right",
            $arrUmur[$key]["F"] . "\t right",
            $arrUmur[$key]["M"] + $arrUmur[$key]["F"] . "\t right"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 5, $field, $data);
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
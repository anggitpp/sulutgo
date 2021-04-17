<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function lihat(){
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    $arrGrade = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory ='S10' AND statusData = 't' order by urutanData");

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
        <th width="*">Job Grade</th>
        <th width="100">L</th>
        <th width="100">P</th>
        <th width="75">Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $filter = !empty($par[idLokasi]) ? " AND t2.location = '$par[idLokasi]'" : "";
    $sql = "SELECT t1.id, gender, t2.grade FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrData[$r[gender]][$r[grade]]++;
    }
    $no=0;
    foreach ($arrGrade as $key => $value){
        $no++;
        ?>
        <tr>
            <td><?= $no ?>.</td>
            <td><?= $value ?></td>
            <td align="right"><?= $arrData["M"][$key] ?></td>
            <td align="right"><?= $arrData["F"][$key] ?></td>
            <td align="right"><?= $arrData["M"][$key] + $arrData["F"][$key] ?></td>
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

    $arrGrade = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory ='R11' AND statusData = 't' order by urutanData");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "Job Grade", "L", "P","JUMLAH");

    $filter = !empty($par[idLokasi]) ? " AND t2.location = '$par[idLokasi]'" : "";
    $sql = "SELECT t1.id, gender, grade FROM emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') WHERE t1.status = '535' $filter";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)){
        $arrData[$r[gender]][$r[grade]]++;
    }
    $no=0;
    foreach ($arrGrade as $key => $value){
        $no++;
        $data[] = array(
            $no . "\t center",
            $value . "\t left",
            $arrData["M"][$key] . "\t right",
            $arrData["F"][$key] . "\t right",
            $arrData["M"][$key] + $arrData["F"][$key] . "\t right"
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
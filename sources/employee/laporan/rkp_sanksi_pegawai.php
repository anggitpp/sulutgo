<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function lihat(){
    global $s, $par, $arrTitle, $arrParameter, $areaCheck;

    $tglMulai = implode("/", array('01','01', date('Y')));
    $tglSelesai = implode("/", array('31','12', date('Y')));
    $par[tanggalMulai] = empty($par[tanggalMulai]) ? $tglMulai : $par[tanggalMulai];
    $par[tanggalSelesai] = empty($par[tanggalSelesai]) ? $tglSelesai : $par[tanggalSelesai];
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
        <div id="pos_r" style="float: right">
            <a href="?par[mode]=xls<?= getPar($par,"mode") ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
        </div>
        <div id="pos_l" style="float: left">
            <input type="text" id="par[filterData]" name="par[filterData]" value="<?= $par[filterData] ?>" style="width:200px;" />
            <input type="text" class="hasDatePicker" name="par[tanggalMulai]" value="<?= $par[tanggalMulai] ?>">
            <input type="text" class="hasDatePicker" name="par[tanggalSelesai]" value="<?= $par[tanggalSelesai] ?>">
            <input type="submit" value="GO" class="btn btn_search btn-small" />
        </div>
    </form>
    <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
    <thead>
    <tr>
        <th width="20">No.</th>
        <th width="150">Nama</th>
        <th width="100">Nomor</th>
        <th width="100">Tanggal</th>
        <th width="100">Sanksi</th>
        <th width="*">Keterangan</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");
    $filter = "WHERE t1.pnh_date_start BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."'";
    if(!empty($par[idLokasi]))
        $filter.=" AND t3.location = '$par[idLokasi]'";
    if(!empty($par[filterData]))
        $filter.=" AND (lower(t2.name) LIKE '%$par[filterData]%' OR lower(t2.reg_no) LIKE '%$par[filterData]%' OR lower(t1.remark) LIKE '%$par[filterData]%'
        OR lower(t1.pnh_no) LIKE '%$par[filterData]%') OR lower(t1.pnh_no)  LIKE '%$par[filterData]%' OR lower(t4.namaData) LIKE '%$par[filterData]%'";
    $sql = "SELECT t2.name, t1.pnh_date_start, t1.pnh_type, t1.pnh_no, t1.remark FROM emp_punish t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') join mst_data t4 on t1.pnh_type = t4.kodeData $filter order by t1.id";
    $res = db($sql);
    $no=0;
    while ($r = mysql_fetch_assoc($res)){
        $no++;
        ?>
        <tr>
            <td><?= $no ?>.</td>
            <td><?= $r[name] ?></td>
            <td align="center"><?= $r[pnh_no] ?></td>
            <td align="center"><?= getTanggal($r[pnh_date_start]) ?></td>
            <td><?= $arrMaster[$r[pnh_type]] ?></td>
            <td><?= $r[remark] ?></td>
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

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "DAFTAR SANKSI PEGAWAI";

    $field = array("no", "nama", "nomor", "tanggal", "sanksi", "keterangan");

    $filter = "WHERE t1.pnh_date_start BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."'";
    if(!empty($par[idLokasi]))
        $filter.=" AND t3.location = '$par[idLokasi]'";
    if(!empty($par[filterData]))
        $filter.=" AND (lower(t2.name) LIKE '%$par[filterData]%' OR lower(t2.reg_no) LIKE '%$par[filterData]%' OR lower(t1.remark) LIKE '%$par[filterData]%'
        OR lower(t1.pnh_no) LIKE '%$par[filterData]%') OR lower(t1.pnh_no)  LIKE '%$par[filterData]%' OR lower(t4.namaData) LIKE '%$par[filterData]%'";
    $sql = "SELECT t2.name, t1.pnh_date_start, t1.pnh_type, t1.pnh_no, t1.remark FROM emp_punish t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') join mst_data t4 on t1.pnh_type = t4.kodeData $filter order by t1.id";
    $res = db($sql);
    $no=0;
    while ($r = mysql_fetch_assoc($res)){
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[pnh_no] . "\t center",
            getTanggal($r[pnh_date_start]) . "\t center",
            $arrMaster[$r[pnh_type]] . "\t left",
            $r[remark] . "\t left"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
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
<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";

function lihat(){
    global $s, $par, $arrTitle;

    $arrMaster = arrayQuery("SELECT kodeData, namaData FROM mst_data");

    if(empty($par[tahunLembur])) $par[tahunLembur] = date('Y');

    $par[tanggalMulai] = empty($par[tanggalMulai]) ? "01/01/".$par[tahunLembur] : $par[tanggalMulai];
    $par[tanggalSelesai] = empty($par[tanggalSelesai]) ? "31/12/".$par[tahunLembur] : $par[tanggalSelesai];

    $queryLocation = "SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory = 'S06' AND statusData = 't' order by namaData";
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <br clear="all"/>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" class="stdform" method="post">
            <div style="position: absolute; top: 5px;right: 10px;">
                <?= comboData($queryLocation, "id", "description", "par[idLokasi]", "- SEMUA LOKASI -", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"", "100%","chosen-select") ?>
            </div>
            <div id="pos_r">
                <a href="?par[mode]=xls<?= getPar($par, 'mode') ?>" class="btn btn1 btn_inboxi"><span>Export Data</span></a>&nbsp;
            </div>
            <div id="pos_l">
                <input type="text" placeholder="Search.." name="par[filterData]" value="<?= $par[filterData] ?>" class="smallinput" style="width: 200px" />
                <input type="text" name="par[tanggalMulai]" value="<?= $par[tanggalMulai] ?>" class="smallinput hasDatePicker">
                &nbsp;s/d&nbsp;
                <input type="text" name="par[tanggalSelesai]" value="<?= $par[tanggalSelesai] ?>" class="smallinput hasDatePicker">
                <input type="submit" class="btnSubmit" value="GO">
            </div>
        </form>
        <br clear="all"/>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dynscroll">
			<thead>
				<tr>
					<th style="vertical-align: middle" rowspan="2" width="20">No.</th>
                    <th style="vertical-align: middle" rowspan="2" width="*">Nama</th>
                    <th style="vertical-align: middle" rowspan="2" width="150">Jabatan</th>
                    <th style="vertical-align: middle" rowspan="2" width="150">Unit Kerja</th>
                    <th style="vertical-align: middle" rowspan="2" width="200">Keterangan</th>
                    <th style="vertical-align: middle" rowspan="2" width="100">Tanggal</th>
                    <th colspan="2" width="100">Jam</th>
                    <th style="vertical-align: middle" rowspan="2" width="100">Durasi</th>
				</tr>
                <tr>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
			</thead>
			<tbody>
            <?php
            $filter = "WHERE t1.tanggalLembur BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."'";
            if(!empty($par[filterData]))
                $filter.=" AND lower(t2.name) LIKE '%".strtolower($par[filterData])."%' OR lower(t2.reg_no) LIKE '%".strtolower($par[filterData])."%' ";
            if(!empty($par[idLokasi]))
                $filter.=" AND t3.location = '$par[idLokasi]'";
            $sql="select t1.tanggalLembur, time(t1.mulaiLembur) as mulaiLembur, time(t1.selesaiLembur) as selesaiLembur, t1.keteranganLembur, t2.name, t3.pos_name, t3.div_id 
            from att_lembur t1 join emp t2 on t1.idPegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";
            $res=db($sql);
            while($r=mysql_fetch_array($res)){
                $no++;
                ?>
                <tr>
                    <td align="center"><?= $no ?>.</td>
                    <td><?= $r[name] ?></td>
                    <td><?= $r[pos_name] ?></td>
                    <td><?= $arrMaster[$r[div_id]] ?></td>
                    <td><?= $r[keteranganLembur] ?></td>
                    <td align="center"><?= $r[tanggalLembur] ?></td>
                    <td align="center"><?= $r[mulaiLembur] ?></td>
                    <td align="center"><?= $r[selesaiLembur] ?></td>
                    <td align="center"><?= convertMinsToHours(selisihMenit2($r[selesaiLembur], $r[mulaiLembur])) ?></td>
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

    $direktori = $fExport;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "nama", "jabatan", "unit kerja", "keterangan", "tanggal", "jam" => array("mulai", "selesai"), "durasi");

    $filter = "WHERE t1.tanggalLembur BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."'";
    if(!empty($par[filterData]))
        $filter.=" AND lower(t2.name) LIKE '%".strtolower($par[filterData])."%' OR lower(t2.reg_no) LIKE '%".strtolower($par[filterData])."%' ";
    if(!empty($par[idLokasi]))
        $filter.=" AND t3.location = '$par[idLokasi]'";
    $sql="select t1.tanggalLembur, time(t1.mulaiLembur) as mulaiLembur, time(t1.selesaiLembur) as selesaiLembur, t1.keteranganLembur, t2.name, t3.pos_name, t3.div_id 
            from att_lembur t1 join emp t2 on t1.idPegawai = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $filter order by t2.name";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[pos_name] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            $r[keteranganLembur] . "\t left",
            getTanggal($r[tanggalLembur]) . "\t center",
            $r[mulaiLembur] . "\t left",
            $r[selesaiLembur] . "\t left",
            convertMinsToHours(selisihMenit2($r[selesaiLembur], $r[mulaiLembur])) . "\t left"
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
<?php
$par[filterTahun] = isset($par[filterTahun]) ? $par[filterTahun] : date('Y');
$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
$arrCuti = arrayQuery("SELECT idPegawai, MONTH(mulaiCuti), SUM(jumlahCuti) FROM att_cuti WHERE persetujuanCuti = 't' AND sdmCuti = 't' AND YEAR(mulaiCuti) = '$par[filterTahun]' group by 1,2");
$par[filterTahunSelesai] = $par[filterTahun]+1;
$arrMasterCuti = arrayQuery("SELECT (CASE WHEN MONTH(mulaiCuti) >= '01' AND MONTH(selesaiCuti) <= '06' THEN 'smt1' ELSE 'smt2' END), sum(jatahCuti) FROM dta_cuti WHERE (YEAR(mulaiCuti) = '$par[filterTahun]' AND YEAR(selesaiCuti) = '$par[filterTahunSelesai]') AND statusCuti = 't'");
$fExport = 'files/export/';

switch ($par[mode]) {
  case 'export':
  xls();
  break;
}

if ($_GET['json'] == 1) {
  header('Content-type: application/json');

  $filter = "where t1.status='".$status."' AND t2.location IN ( $areaCheck )";
  if(!empty($par[idLokasi]))
    $filter.= " and t2.location='".$par[idLokasi]."'";
  if(!empty($par[divId]))
    $filter.= " and t2.div_id='".$par[divId]."'";
  if(!empty($par[deptId]))
    $filter.= " and t2.dept_id='".$par[deptId]."'";
  if(!empty($par[unitId]))
    $filter.= " and t2.unit_id='".$par[unitId]."'";
  $sql = "
  SELECT
  t1.id, t1.reg_no, UPPER(t1.name) name, t2.pos_name
  FROM emp t1
  LEFT JOIN emp_phist t2
  ON (t1.id = t2.parent_id AND t2.status=1)
  $filter
  ORDER BY name";
  $ret = array();
  $res = db($sql);
  while ($r = mysql_fetch_assoc($res)) {
    $r['jmlCutiSmt1'] = 0;
    $r['jmlCutiSmt2'] = 0;
    for ($i = 1; $i <= 12; ++$i) {
      if ($i <= 6) {
        $r['jmlCutiSmt1'] += isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
      } else {
        $r['jmlCutiSmt2'] += isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
      }
      $r["jmlCuti_$i"] = isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
    }
    $r['sisaCutiSmt1'] = $arrMasterCuti['smt1'] - $r['jmlCutiSmt1'];
    $r['sisaCutiSmt2'] = $arrMasterCuti['smt2'] - $r['jmlCutiSmt2'];
    $ret[] = $r;
  }
  echo json_encode(array('sEcho' => 1, 'aaData' => $ret));
  exit();
}

?>

<script src="sources/js/default.js"></script>
<div class="pageheader">
  <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
  <div style="margin-top: 10px;">
    <?= getBread() ?>
  </div>
  <span class="pagedesc">&nbsp;</span>
</div>

<div id="contentwrapper" class="contentwrapper">
      <form id="form" action="" method="post" class="stdform">
        <div style="position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;">
            <div style="position:absolute; right: 0px;">
              <table>
                <tr>
                  <td>
                    Lokasi Kerja : <?php echo comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px") ?>
                  </td>
                  <td style="vertical-align:top;" id="bView">
                    <input type="button" value="+" style="font-size:26px; padding:0 6px;" class="btn btn_search btn-small" onclick="
                    document.getElementById('bView').style.display = 'none';
                    document.getElementById('bHide').style.display = 'table-cell';
                    document.getElementById('dFilter').style.visibility = 'visible';              
                    document.getElementById('fSet').style.height = 'auto';
                    document.getElementById('fSet').style.padding = '10px';
                    ">
                  </td>
                  <td style="vertical-align:top; display:none;" id="bHide">
                    <input type="button" value="-" style="font-size:26px; padding:0 9px;" class="btn btn_search btn-small" onclick="
                    document.getElementById('bView').style.display = 'table-cell';
                    document.getElementById('bHide').style.display = 'none';
                    document.getElementById('dFilter').style.visibility = 'collapse';             
                    document.getElementById('fSet').style.height = '0px';
                    document.getElementById('fSet').style.padding = '0px';
                    ">          
                  </td>
                  <td>
                    <b>Tahun :</b>&nbsp;&nbsp;<?php echo comboYear('par[filterTahun]', $par[filterTahun], '3', '', '110px'); ?>
                    &nbsp;
                    <input type="button" class="cancel radius2" value="Back" onclick="window.location = '?<?php echo preg_replace("/(&[ms]=\w+)/", "", getPar()); ?>';"/>
                  </td>
                </tr>
              </table>
            </div>
            <fieldset id="fSet" style="padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;">
              <div id="dFilter" style="visibility:collapse;">
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       where t3.kodeCategory='X03' order by t1.urutanData", "id", "description", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       where t4.kodeCategory='X03' AND t1.kodeInduk = '$par[divId]' order by t1.urutanData", "id", "description", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk

                       where t5.kodeCategory='X03' AND t1.kodeInduk = '$par[deptId]' order by t1.urutanData", "id", "description", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
              </div>
            </fieldset>
        </div>
      </form>
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
    <thead>
      <tr>
        <th rowspan="2" width="20px" style="vertical-align:middle;">No</th>
        <th rowspan="2" width="20px" style="vertical-align:middle;">Nik</th>
        <th rowspan="2" width="200px" style="vertical-align:middle;">Nama Karyawan</th>
        <th rowspan="2" width="100px" style="vertical-align:middle;">Jabatan</th>
<!--        <th rowspan="2" width="50px" style="vertical-align:middle;">Jml. Cuti Smtr I</th>-->
        <th colspan="12" width="20px" style="vertical-align:middle;">Bulan</th>
<!--        <th rowspan="2" width="30px" style="vertical-align:middle;">Sisa Cuti I</th>-->
<!--        <th rowspan="2" width="50px" style="vertical-align:middle;">Jml. Cuti Smtr II</th>-->
<!--        <th colspan="6" width="20px" style="vertical-align:middle;">Cuti Semester II</th>-->
<!--        <th rowspan="2" width="30px" style="vertical-align:middle;">Sisa Cuti II</th>-->
        <th rowspan="2" width="50px" style="vertical-align:middle;">Sisa Cuti</th>
      </tr>
      <tr>
        <th style="width:20px; vertical-align: middle;">Jan</th>
        <th style="width:20px; vertical-align: middle;">Feb</th>
        <th style="width:20px; vertical-align: middle;">Mar</th>
        <th style="width:20px; vertical-align: middle;">Apr</th>
        <th style="width:20px; vertical-align: middle;">Mei</th>
        <th style="width:20px; vertical-align: middle;">Juni</th>

        <th style="width:20px; vertical-align: middle;">Jul</th>
        <th style="width:20px; vertical-align: middle;">Ags</th>
        <th style="width:20px; vertical-align: middle;">Sep</th>
        <th style="width:20px; vertical-align: middle;">Okt</th>
        <th style="width:20px; vertical-align: middle;">Nov</th>
        <th style="width:20px; vertical-align: middle;">Des</th>
      </tr>
    </thead>
    <tbody>

    <!--      <td>No.</td>-->
    <!--      <td>Nik</td>-->
    <!--      <td>Nama Karyawan</td>-->
    <!--      <td>Jabatan</td>-->
    <!--      <td>Jml Cuti Smtr I</td>-->
    <!--      <td>Jan</td>-->
    <!--      <td>Feb</td>-->
    <!--      <td>Mar</td>-->
    <!--      <td>Apr</td>-->
    <!--      <td>Mei</td>-->
    <!--      <td>Jun</td>-->
    <!--      <td>Sisa Cuti I</td>-->
    <!--      <td>Jml Cuti Smtr II</td>-->
    <!--      <td>Jul</td>-->
    <!--      <td>Ags</td>-->
    <!--      <td>Sep</td>-->
    <!--      <td>Okt</td>-->
    <!--      <td>Nov</td>-->
    <!--      <td>Des</td>-->
    <!--      <td>Sisa Cuti II</td>-->
    <!--      <td>Sisa Cuti</td>-->

    </tbody>
  </table>
</div>

<style type="text/css">

  .alignRight{
    text-align: right;
  }

  .alignCenter{
    text-align: center;
  }

</style>

<?php
if ($par[mode] == 'export') {
  echo "<iframe src=\"download.php?d=exp&f=$arrTitle[$s]_".date('dmY').'.xls" frameborder="0" width="0" height="0"></iframe>';
}
?>



<script type="text/javascript">
  jQuery(document).ready(function () {
    ot = jQuery('#datatable').dataTable({
      "sScrollY": "100%",
      "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      "bSort": false,
      "bFilter": true,
      "iDisplayStart": 0,
      "sPaginationType": "full_numbers",
      "sAjaxSource": "ajax.php?json=1<?= getPar($par, 'mode,periodeAwal,periodeAkhir'); ?>",
      "aoColumns": [
      {"mData": null, "bSortable": true, "sClass": "alignRight"},
      {"mData": "reg_no", "bSortable": true},
      {"mData": "name", "bSortable": true},
      {"mData": "pos_name", "bSortable": true},
      {"mData": "jmlCuti_1", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_2", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_3", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_4", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_5", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_6", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_7", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_8", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_9", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_10", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_11", "bSortable": true, "sClass": "alignCenter"},
      {"mData": "jmlCuti_12", "bSortable": true, "sClass": "alignCenter"},
      {"mData": null, "bSortable": true, "sClass": "alignCenter", "fnRender": function(o){
        return parseFloat(o.aData['sisaCutiSmt1']) + parseFloat(o.aData['sisaCutiSmt2']);
      }},
      ],
      "aaSorting": [[1, "asc"]],
      "fnInitComplete": function (oSettings) {
        oSettings.oLanguage.sZeroRecords = "No data available";
      }, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
      "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
        return nRow;
      },
      "bProcessing": true,
      "oLanguage": {
        "sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
      }
    });

    jQuery("#par\\[filterTahun\\]").live("change", function (e) {
      e.preventDefault();
      ot.fnReloadAjax("ajax.php?json=1<?= getPar(); ?>" + "&par[filterTahun]=" + jQuery("#par\\[filterTahun\\]").val());
    });

    jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "9px").css("margin-top", "0px").css("font-size", "14px");

    jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
    jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

    jQuery("#datatable_wrapper #right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");


    jQuery("#btnExport").live("click", function(e){
      e.preventDefault();
      window.location = "?par[mode]=export<?= getPar(); ?>&par[filterTahun]=" + jQuery("#par\\[filterTahun\\]").val();
    });

    jQuery(window).bind('resize', function () {
      ot.fnAdjustColumnSizing();
    });

    jQuery(".togglemenu").click();

  });

</script>

<?php

function xls()
{
  global $s,$par,$fExport, $cNama, $arrTitle, $status, $arrCuti, $arrMasterCuti, $areaCheck;

  require_once 'plugins/PHPExcel.php';

  $objPHPExcel = new PHPExcel();
  $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);

  $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
  $objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);

  $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
  $objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');
  $objPHPExcel->getActiveSheet()->mergeCells('A3:Q3');

  $objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
  $objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
  $objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
  $objPHPExcel->getActiveSheet()->mergeCells('D4:D5');

  $objPHPExcel->getActiveSheet()->mergeCells('E4:P4');

  $objPHPExcel->getActiveSheet()->mergeCells('Q4:Q5');

  $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
  // $objPHPExcel->getActiveSheet()->setCellValue('A2', " Periode : " . $par[periodeAwal]. " s/d " . $par[periodeAkhir]);

  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:Q5')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

//  $objPHPExcel->getActiveSheet()->getStyle('E4:E5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//  $objPHPExcel->getActiveSheet()->getStyle('K4:K5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//
//  $objPHPExcel->getActiveSheet()->getStyle('M4:M5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//  $objPHPExcel->getActiveSheet()->getStyle('S4:S5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO');
  $objPHPExcel->getActiveSheet()->setCellValue('B4', 'NIK');
  $objPHPExcel->getActiveSheet()->setCellValue('C4', 'MAMA KARYAWAN');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', 'JABATAN');
    $objPHPExcel->getActiveSheet()->setCellValue('E4', 'BULAN');

  $objPHPExcel->getActiveSheet()->setCellValue('E5', 'JAN');
  $objPHPExcel->getActiveSheet()->setCellValue('F5', 'FEB');
  $objPHPExcel->getActiveSheet()->setCellValue('G5', 'MAR');
  $objPHPExcel->getActiveSheet()->setCellValue('H5', 'APR');
  $objPHPExcel->getActiveSheet()->setCellValue('I5', 'MEI');
  $objPHPExcel->getActiveSheet()->setCellValue('J5', 'JUN');
  $objPHPExcel->getActiveSheet()->setCellValue('K5', 'JUL');
  $objPHPExcel->getActiveSheet()->setCellValue('L5', 'AGS');
  $objPHPExcel->getActiveSheet()->setCellValue('M5', 'SEP');
  $objPHPExcel->getActiveSheet()->setCellValue('N5', 'OKT');
  $objPHPExcel->getActiveSheet()->setCellValue('O5', 'NOV');
  $objPHPExcel->getActiveSheet()->setCellValue('P5', 'DES');
  $objPHPExcel->getActiveSheet()->setCellValue('Q4', 'SISA CUTI');

  $filter = "where t1.status='".$status."' AND t2.location IN ( $areaCheck )";
  if(!empty($par[idLokasi]))
    $filter.= " and t2.location='".$par[idLokasi]."'";
  if(!empty($par[divId]))
    $filter.= " and t2.div_id='".$par[divId]."'";
  if(!empty($par[deptId]))
    $filter.= " and t2.dept_id='".$par[deptId]."'";
  if(!empty($par[unitId]))
    $filter.= " and t2.unit_id='".$par[unitId]."'";
  $sql = "
  SELECT
  t1.id, t1.reg_no, UPPER(t1.name) name, t2.pos_name
  FROM emp t1
  LEFT JOIN emp_phist t2
  ON (t1.id = t2.parent_id AND t2.status=1)
  $filter
  ORDER BY name";
  $res = db($sql);
  $currentRow = 6;
  $no = 0;
  while ($r = mysql_fetch_assoc($res)) {
    ++$no;
    $r['jmlCutiSmt1'] = 0;
    $r['jmlCutiSmt2'] = 0;
    for ($i = 1; $i <= 12; ++$i) {
      if ($i <= 6) {
        $r['jmlCutiSmt1'] += isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
      } else {
        $r['jmlCutiSmt2'] += isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
      }
      $r["jmlCuti_$i"] = isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
    }
    $r['sisaCutiSmt1'] = $arrMasterCuti['smt1'] - $r['jmlCutiSmt1'];
    $r['sisaCutiSmt2'] = $arrMasterCuti['smt2'] - $r['jmlCutiSmt2'];

    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow.":Q".$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$currentRow, $no.".",PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$currentRow, $r[reg_no],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$currentRow, $r[name],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$currentRow, $r[pos_name],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$currentRow, $r[jmlCuti_1],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$currentRow, $r[jmlCuti_2],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$currentRow, $r[jmlCuti_3],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$currentRow, $r[jmlCuti_4],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$currentRow, $r[jmlCuti_5],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$currentRow, $r[jmlCuti_6],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.$currentRow, $r[jmlCuti_7],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('L'.$currentRow, $r[jmlCuti_8],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$currentRow, $r[jmlCuti_9],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$currentRow, $r[jmlCuti_10],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('O'.$currentRow, $r[jmlCuti_11],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('P'.$currentRow, $r[jmlCuti_12],PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('Q'.$currentRow, (intval($r[sisaCutiSmt1]) + intval($r[sisaCutiSmt2])),PHPExcel_Cell_DataType::TYPE_STRING);

    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':Q'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    ++$currentRow;
  }

  $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

  $objPHPExcel->setActiveSheetIndex(0);

  // Save Excel file
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save($fExport.$arrTitle[$s].'_'.date('dmY').'.xls');
}

?>

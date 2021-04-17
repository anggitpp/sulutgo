<?php
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$fFile = "files/export/";


$emp = new Emp();
$cutil = new Common();
$ui = new UIHelper();

if ($_GET["json"] == 'excel') {
  list($cLocId, $cDivId) = explode("~", $_GET["params"]);
  xls($cLocId, $cDivId);
}

$queryDiv = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' order by t1.namaData";

$cLocId = isset($_POST["cLocId"]) ? $_POST["cLocId"] : "";
$cDivId = isset($_POST["cDivId"]) ? $_POST["cDivId"] : "";
if ($_GET["json"] == 1) {
  $locId = isset($_GET["clocid"]) ? $_GET["clocid"] : "";
  $divId = isset($_GET["cdivid"]) ? $_GET["cdivid"] : "";
  header("Content-type: application/json");
  
  $ret = getData($locId, $divId);
  echo json_encode(array("sEcho" => 1, "aaData" => $ret));
  exit();
}
?>
<div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
  <?= getBread() ?>
  <span class="pagedesc">&nbsp;</span>
</div>
<?= empLocHeader(); ?>
<div id="contentwrapper" class="contentwrapper">
  <form action="?<?php echo getPar($par, "mode") ?>" id="form" method="post" class="stdform">
    <table style="width:100%">
      <tr>
        <td style="width:50%; text-align:left; vertical-align:top;">
          <table>
            <tr>
              <td style="vertical-align:top;">
                <p>
                  <label class="l-input-small" style="width:100px; text-align:left; padding-left:10px;">LOKASI</label>
                  <div class="field" style="margin-left:150px;">
                    <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "cLocId", "--LOKASI--", $cLocId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                  </div>
                </p>
              </td>
              <td style="vertical-align:top; padding-top: 4px;" id="bView">
                <input type="button" value="+" style="font-size:26px; padding:0 6px;" class="btn btn_search btn-small" onclick="
                document.getElementById('bView').style.display = 'none';
                document.getElementById('bHide').style.display = 'table-cell';
                document.getElementById('dFilter').style.visibility = 'visible';              
                document.getElementById('fSet').style.height = 'auto';
                " />
              </td>
              <td style="vertical-align:top; padding-top: 4px; display:none;" id="bHide">
                <input type="button" value="-" style="font-size:26px; padding:0 8px;" class="btn btn_search btn-small" style="display:none" onclick="
                document.getElementById('bView').style.display = 'table-cell';
                document.getElementById('bHide').style.display = 'none';
                document.getElementById('dFilter').style.visibility = 'collapse';             
                document.getElementById('fSet').style.height = '0px';
                " />          
              </td>   
            </tr>
          </table>          
          <fieldset id="fSet" style="padding:0px; border: 0px; height:0px;">
            <div id="dFilter" style="visibility:collapse;">
              <p>
                <label class="l-input-small" style="width:100px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData($queryDiv, "id", "description", "cDivId", "--".strtoupper($arrParameter[39])."--", $cDivId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
            </div>
          </fieldset>
        </div>        
      </td>
      <td style="width:50%; text-align:right; vertical-align:top;">
        <a href="#" id="btnExpExcel" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
      </td>

      <?php
      if($_GET['json'] == 'excel'){      
        echo "<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]))." ".date('Y-m-d H:i').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
      }
      ?>

    </tr>
  </table>
</form>
<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
  <thead>
    <tr>
      <th rowspan="2" style="width:20; vertical-align: middle;">No.</th>
      <th rowspan="2" style="vertical-align: middle;">Jabatan</th>
      <th colspan="2" style="vertical-align: middle;">Jumlah</th>
      <th rowspan="2" style="width: 100px; vertical-align: middle;">Total</th>
    </tr>
    <tr>
      <th style="text-align: center;width: 70px; vertical-align: middle;">Laki-Laki</th>
      <th style="text-align: center;width: 70px; vertical-align: middle;">Perempuan</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
</div>
<script>
  jQuery(document).ready(function () {
    ot = jQuery('#datatable').dataTable({
      "bSort": false,
      "bFilter": false,
      "bPaginate": false,
      "iDisplayStart": 0,
      "sAjaxSource": sajax + "&json=1&clocid=<?= $cLocId ?>&cdivid=<?= $cDivId ?>",
      "aoColumns": [
      {"mData": null, "sWidth": "20px", "bSortable": false, "sClass": "alignRight", "fnRender": function(o){
        return o.aData['viewType'] == 'row' ? o.aData['jabId'] : "&nbsp;";
      }},
      {"mData": null, "bSortable": false, "fnRender": function(o){
        return o.aData['viewType'] == 'row' ? "<a href=\"#\" id=\"jab_" + o.aData['noRow'] + "\" class=\"link-jab\">" + o.aData['posName'] + "</a>" : "&nbsp;";
      }},
      {"mData": null, "sWidth": "120px", "bSortable": false, "sClass": "alignRight", "fnRender": function(o){
        return o.aData['viewType'] == 'row' ? o.aData['cmale'] : "&nbsp;";
      }},
      {"mData": null, "sWidth": "120px", "bSortable": false, "sClass": "alignRight", "fnRender": function(o){
        return o.aData['viewType'] == 'row' ? o.aData['cfemale'] : "&nbsp;";
      }},
      {"mData": null, "sWidth": "120px", "bSortable": false, "sClass": "alignRight", "fnRender": function(o){
        return o.aData['viewType'] == 'row' ? o.aData['ctotal'] : "&nbsp;";
      }},
      ],
      "aaSorting": [[1, "asc"]],
      "sPaginationType": "full_numbers",
      "fnInitComplete": function (oSettings) {
        oSettings.oLanguage.sZeroRecords = "No data available";
      },
      "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        if(aData['viewType'] == "row")
          jQuery("td:first", nRow).html(aData['noRow'] + ".");
        else{
          jQuery("td", nRow).remove();
          jQuery(nRow).addClass("lst_" + aData['jabId']).css("display", "none");
          jQuery(nRow).append("<td>&nbsp;</td>");
          jQuery(nRow).append("<td colspan=\"4\">" + aData['noFooter'] + ".&nbsp;" + aData['name'] + " ( " + aData['reg_no'] + " )</td>");
        }
        return nRow;
      },
      "bProcessing": true,
      "sDom": "<'top'f>rt<'bottom'lp><'clear'>",
    });

    jQuery("#btnExpExcel").click(function (e) {
      e.preventDefault();
      var sMonth = jQuery("#cLocId").val() + "~" + jQuery("#cDivId").val();
      window.location = "?json=excel&params=" + sMonth + "<?php echo getPar() ?>";
      // alert("?json=excel&params=" + sMonth + "<?php echo getPar() ?>");
    });

    <?php
    if(!empty($cDivId)){
      echo "jQuery('#bView > input').click();";
    }
    ?>

    jQuery("a.link-jab").live("click", function(e){
      e.preventDefault();
      var rowNum = jQuery(this).attr("id").split("_")[1];

      if(jQuery("tr.lst_" + rowNum).css("display") == "none")
        jQuery("tr.lst_" + rowNum).css("display", "table-row");
      else
        jQuery("tr.lst_" + rowNum).css("display", "none");
    });
  });
</script>
<?php
function getData($locId, $divId){
  global $areaCheck;
  $filter = "WHERE t1.`status`=535 AND t2.location IN ($areaCheck)";
  if (!empty($locId))
    $filter.=" AND t2.location=$locId ";
  if (!empty($divId))
    $filter.=" AND t2.div_id=$divId ";
  $sql = "
  SELECT 
  t2.id jabId, IFNULL(t2.pos_name,'') posName,
  SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
  SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
  count(*) ctotal
  FROM emp t1
  LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
  $filter 
  GROUP BY t2.pos_name
  ORDER BY t2.pos_name
  ";
  $lastPosName = "";
  $ctd = 0;
  $res = db($sql);
  $ret = array();
  while($r = mysql_fetch_assoc($res)){
    if($lastPosName != $r[posName]){
      if($ctd != 0 && $lastPosName != ''){
        $tNo = 0;
        $tFilter = "WHERE LOWER(pos_name) = '".strtolower($lastPosName)."' AND status = 535 AND location IN ($areaCheck)";
        if (!empty($locId))
          $tFilter.=" AND location=$locId ";
        if (!empty($divId))
          $tFilter.=" AND div_id=$divId ";
        $arrEmp = arrayQuery("SELECT name, reg_no FROM dta_pegawai $tFilter ORDER BY name");
        foreach($arrEmp as $name => $reg_no){
          $tNo++;
          $ret[] = array("viewType" => "footer", "jabId" => ($ctd), "noFooter" => $tNo, "name" => $name, "reg_no" => $reg_no);
        }
      }
      $lastPosName = $r[posName];
    }

    $r['viewType'] = "row";
    $r['noRow'] = $ctd+1;
    $ret[] = $r;

    $ctd++;
  }

  if($ctd != 0 && $lastPosName != ''){
    $tNo = 0;
    $tFilter = "WHERE LOWER(pos_name) = '".strtolower($lastPosName)."' AND status = 535 AND location IN ($areaCheck)";
    if (!empty($locId))
      $tFilter.=" AND location=$locId ";
    if (!empty($divId))
      $tFilter.=" AND div_id=$divId ";
    $arrEmp = arrayQuery("SELECT name, reg_no FROM dta_pegawai $tFilter ORDER BY name");
    foreach($arrEmp as $name => $reg_no){
      $tNo++;
      $ret[] = array("viewType" => "footer", "jabId" => ($ctd+1), "noFooter" => $tNo, "name" => $name, "reg_no" => $reg_no);
    }
  }

  return $ret;
}

function xls($locId, $divId){   
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $cutil;
  require_once 'plugins/PHPExcel.php';

  $objPHPExcel = new PHPExcel();        
  $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);  
    // $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);    

  $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
  $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
  $objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
  $objPHPExcel->getActiveSheet()->mergeCells('A5:A6');
  $objPHPExcel->getActiveSheet()->mergeCells('B5:B6');
  $objPHPExcel->getActiveSheet()->mergeCells('E5:E6');

  $objPHPExcel->getActiveSheet()->mergeCells('C5:D5');

  $objPHPExcel->getActiveSheet()->getStyle('A1:A3')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($arrTitle[$s]));
  $objPHPExcel->getActiveSheet()->setCellValue('A2', strtoupper("lokasi: " . (empty($locId) ? "ALL" : $cutil->getMstDataDesc($locId))));
  $objPHPExcel->getActiveSheet()->setCellValue('A3', strtoupper("departemen: " . (empty($divId) ? "ALL" : $cutil->getMstDataDesc($divId))));


  $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getFont()->setBold(true);  
  $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:E5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);   

  $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);   

  $objPHPExcel->getActiveSheet()->setCellValue('A5', 'NO.');
  $objPHPExcel->getActiveSheet()->setCellValue('B5', 'JABATAN');
  $objPHPExcel->getActiveSheet()->setCellValue('C5', 'JUMLAH');
  $objPHPExcel->getActiveSheet()->setCellValue('C6', 'LAKI LAKI');
  $objPHPExcel->getActiveSheet()->setCellValue('D6', 'PEREMPUAN');
  $objPHPExcel->getActiveSheet()->setCellValue('E5', 'TOTAL');


  $rows = 7;
  $ctd = 0;
  foreach(getData($locId, $divId) as $r){
    if($r['viewType'] == "row"){
      $ctd = 0;
      $no++;
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);       
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[posName]);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[cmale]);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[cfemale]);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[ctotal]);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

      $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
      $objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    }else{
      $ctd++;
      $objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':E'.$rows);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "");
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $ctd . ". " . $r[name] . " ( " . $r[reg_no] . " ) ");   
      $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }
    $rows++;

  }



  $rows--;
  $objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B5:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('C5:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('D5:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('E5:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setWrapText(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getFont()->setName('Arial');
  $objPHPExcel->getActiveSheet()->getStyle('A6:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

  $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325); 

  $objPHPExcel->getActiveSheet()->setTitle("Laporan");
  $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save($fFile.ucwords(strtolower($arrTitle[$s]))." ".date('Y-m-d H:i').".xls");
}

?>


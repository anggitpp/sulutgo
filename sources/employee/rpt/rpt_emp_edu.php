<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$emp = new Emp();
$cutil = new Common();
$ui = new UIHelper();

if ($_GET["json"] == 'excel') {
  list($cEduId, $cLocId, $cDivId, $cDeptId, $cUnitId) = explode("~", $_GET["params"]);
  $res = $emp->exportToExcelReportEdu($cEduId, $cLocId, $cDivId, $cDeptId, $cUnitId);
  $uie = new UiHelperExtra();
  $uie->exportXLS($res, array("Laporan Jumlah Pegawai per Pendidikan",
   "Lokasi : " . (empty($cLocId) ? "ALL" : $cutil->getMstDataDesc($cLocId)),
   $arrParameter[39]." : " . (empty($cDivId) ? "ALL" : $cutil->getMstDataDesc($cDivId)),
   $arrParameter[40]." : " . (empty($cDeptId) ? "ALL" : $cutil->getMstDataDesc($cDeptId)),
   $arrParameter[41]." : " . (empty($cUnitId) ? "ALL" : $cutil->getMstDataDesc($cUnitId)),
   "Pendidikan : " . (empty($cEduId) ? "ALL" : $cutil->getMstDataDesc($cEduId)),
   ), "laporan_jumlah_pegawai_per_pendidikan_" . date('Y-m-d H:i') . ".xlsx");
}
$cEduId = isset($_POST["ceduid"]) ? $_POST["ceduid"] : "";
$cLocId = isset($_POST["cLocId"]) ? $_POST["cLocId"] : $cLocId;
$cDivId = isset($_POST["cDivId"]) ? $_POST["cDivId"] : $cDivId;
$cDeptId = isset($_POST["cDeptId"]) ? $_POST["cDeptId"] : $cDeptId;
$cUnitId = isset($_POST["cUnitId"]) ? $_POST["cUnitId"] : $cUnitId;
if ($_GET["json"] == 1) {
  $eduId = isset($_GET["ceduid"]) ? $_GET["ceduid"] : "";
  $locId = isset($cLocId) ? $cLocId : $_GET["clocid"];
  $divId = isset($cDivId) ? $cDivId : $_GET["cdivid"];
  $deptId = isset($cDeptId) ? $cDeptId : $_GET["cdeptid"];
  $unitId = isset($cUnitId) ? $cUnitId : $_GET["cunitid"];
  header("Content-type: application/json");
  echo $emp->loadTableReportEdu($eduId, $locId, $divId, $deptId, $unitId);
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
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">LOKASI</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "cLocId", "--LOKASI--", $cLocId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">Pendidikan</label>
                  <div class="field" style="margin-left:240px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='R11' ORDER BY urutanData", "kodeData", "namaData", "ceduid", "--PENDIDIKAN--", $cEduId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
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
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "cDivId", "--".strtoupper($arrParameter[39])."--", $cDivId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$cDivId' ORDER BY urutanData", "kodeData", "namaData", "cDeptId", "--".strtoupper($arrParameter[40])."--", $cDeptId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$cDeptId' ORDER BY urutanData", "kodeData", "namaData", "cUnitId", "--".strtoupper($arrParameter[41])."--", $cUnitId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
            </div>
          </fieldset>
        </div>        
      </td>
      <td style="width:50%; text-align:right; vertical-align:top;">
        <a href="#" id="btnExpExcel" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
      </td>

    </tr>
  </table>
</form>
<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
  <thead>
    <tr>
      <th rowspan="2" style="width:40px;">No.</th>
      <th rowspan="2" >Pendidikan</th>
      <th colspan="2" >Jumlah</th>
      <th rowspan="2" style="width: 100px;">Total</th>
    </tr>
    <tr>
      <th style="text-align: center;width: 70px;">Laki-Laki</th>
      <th style="text-align: center;width: 70px;">Perempuan</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
</div>
<script>
  jQuery(document).ready(function () {
    ot = jQuery('#datatable').dataTable({
      "bSort": true,
      "bFilter": true,
      "iDisplayStart": 0,
      "sAjaxSource": sajax + "&json=1&ceduid=<?= $cEduId ?>&clocid=<?= $cLocId ?>&cdivid=<?= $cDivId ?>&cdeptid=<?= $cDeptId ?>&cunitid=<?= $cUnitId ?>",
      "aoColumns": [
      {"mData": "eduId", "bSortable": false, "sClass": "alignRight"},
      {"mData": "eduName"},
      {"mData": "cmale", "sClass": "alignRight"},
      {"mData": "cfemale", "sClass": "alignRight"},
      {"mData": "ctotal", "sClass": "alignRight"}
      ],
      "aaSorting": [[1, "desc"]],
      "sPaginationType": "full_numbers",
      "fnInitComplete": function (oSettings) {
        oSettings.oLanguage.sZeroRecords = "No data available";
      },
      "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
        return nRow;
      },
      "bProcessing": true,
      "oLanguage": {
        "sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
      },
      "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
    });

    jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "8px").css("font-size", "14px");
    jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");

    jQuery("#btnExpExcel").click(function (e) {
      e.preventDefault();
      var sMonth = jQuery("#ceduid").val() + "~" + jQuery("#cLocId").val() + "~" + jQuery("#cDivId").val() + "~" +jQuery("#cDeptId").val() + "~" +jQuery("#cUnitId").val();
      window.open(sajax + "&json=excel&params=" + sMonth, '_blank');
    });
    <?php
    if(!empty($cDivId) || !empty($cDeptId) || !empty($cUnitId)){
      echo "jQuery('#bView > input').click();";
    }
    ?>
  });
</script>
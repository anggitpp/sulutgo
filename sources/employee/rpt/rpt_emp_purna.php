<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$emp = new Emp();
$cutil = new Common();
$ui = new UIHelper();

if ($_GET["json"] == 'excel') {
  list($cLocId, $cStsId, $cMonth, $cYear) = explode("~", $_GET["params"]);
  $res = $emp->exportToExcelReportPurna($cLocId, $cStsId, $cMonth, $cYear);
  $uie = new UiHelperExtra();
  $uie->exportXLS($res, array("Laporan Pegawai Purna Bakti",
      "Lokasi : " . (empty($cLocId) ? "ALL" : $cutil->getMstDataDesc($cLocId)),
	  "Status : " . (empty($cStsId) ? "ALL" : $cutil->getMstDataDesc($cStsId)),
	  "Periode : " . (empty($cMonth) ? "ALL" : getBulan($cMonth))." ".(empty($cYear) ? "ALL" : $cYear),
          ), "laporan_pegawai_purna_bakti_" . uniqid() . ".xlsx");
}
$cLocId = isset($_POST["clocid"]) ? $_POST["clocid"] : "";
$cStsId = isset($_POST["cstsid"]) ? $_POST["cstsid"] : "";
$cMonth = isset($_POST["cmonth"]) ? $_POST["cmonth"] : "";
$cYear = isset($_POST["cyear"]) ? $_POST["cyear"] : "";
if ($_GET["json"] == 1) {
  $locId = isset($_GET["clocid"]) ? $_GET["clocid"] : "";
  $stsId = isset($_GET["cstsid"]) ? $_GET["cstsid"] : "";
  $month = isset($_GET["cmonth"]) ? $_GET["cmonth"] : "";
  $year = isset($_GET["cyear"]) ? $_GET["cyear"] : "";
  header("Content-type: application/json");
  echo $emp->loadTableReportPurna($locId, $stsId, $month, $year);
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
  <form class="stdform" action="<?= $loc ?>" method="post" style="width: 50%">
    <div style="right:20px; position:absolute;z-index: 9999;">
      <p class="stdformbutton">      
		<a href="#" id="btnExpExcel" class="btn btn1 btn_inboxi"><span>Export Data</span></a>
      </p>
    </div>
	<p><label class="l-input-small">Lokasi Kerja</label>
      <span class="fieldB">
        <?php echo $cutil->generateSelectWithEmptyOption("SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory='S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "id", "description", "clocid", $cLocId, "<option value = \"\">All</option>", "onchange=\"this.form.submit();\"") ?>
      </span>
    </p>
	<p><label class="l-input-small">Status</label>
      <span class="fieldB">
        <?php echo $cutil->generateSelectWithEmptyOption("SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory='".$arrParameter[5]."' ORDER BY urutanData", "id", "description", "cstsid", $cStsId, "<option value = \"\">All</option>", "onchange=\"this.form.submit();\"") ?>
      </span>
    </p>
	<p><label class="l-input-small">Periode</label>
      <span class="fieldB">
        <?php echo comboMonth("cmonth", $cMonth, "onchange=\"this.form.submit();\"", "", "t")." ".comboYear("cyear", $cYear, "", "onchange=\"this.form.submit();\"", "", "t") ?>
      </span>
    </p>
  </form>
  <br class="clear"/>
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable" >
    <thead>
      <tr>
        <th style="width:40px;">No.</th>
        <th>Nama</th>
        <th style="width:100px;">NPP</th>
		<th>Status</th>
        <th>Divisi</th>
        <th>Jabatan</th>
        <th style="width:75px;">Tgl. Masuk</th>
		<th style="width:75px;">Tgl. Keluar</th>
		<th style="width:75px;">Masa Kerja</th>
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
      "sAjaxSource": sajax + "&json=1&clocid=<?= $cLocId ?>&cstsid=<?= $cStsId ?>&cmonth=<?= $cMonth ?>&cyear=<?= $cYear ?>",
      "aoColumns": [
        {"mData": "jabId", "bSortable": false, "sClass": "alignRight"},
        {"mData": "name"},
		{"mData": "reg_no"},
		{"mData": "statusPeg"},
        {"mData": "deptName"},
        {"mData": "posName"},
        {"mData": "joinDate", "sClass": "alignCenter"},
		{"mData": "leaveDate", "sClass": "alignCenter"},
        {"mData": "masaKerja", "sClass": "alignCenter"},       
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
      var sMonth = jQuery("#clocid").val() + "~" + jQuery("#cstsid").val() + "~" + jQuery("#cmonth").val() + "~" + jQuery("#cyear").val();
      window.open(sajax + "&json=excel&params=" + sMonth, '_blank');
    });
  });
</script>
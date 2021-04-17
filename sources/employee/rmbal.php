<?php
session_start();
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$_SESSION["entity_id"] = "";
$_SESSION["curr_emp_id"] = (isset($_GET["empid"]) ? $_GET["empid"] : $_SESSION["curr_emp_id"] );
if (empty($_SESSION["curr_emp_id"])) {
    echo 
  "<script>
    alert(\"Silakan memilih Pegawai terlebih dahulu...\");
    window.location.href=\"".APP_URL . "/?c=3&p=8&m=79&s=82\";
  </script>";
//  header("Location: " . APP_URL . "/index.php?c=3&p=8&m=79&s=82");
}
$cyear = isset($_GET["cyear"]) ? $_GET["cyear"] : date("Y");
if ($_GET[json] == 1) {
  header("Content-type: application/json");
  $emp = new EmpRmb();
  $emp->parentId = $_SESSION["curr_emp_id"];
  echo $emp->loadTableBalance($cyear);
  exit();
}
$ui = new UIHelper();
?>

<div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
  <?= getBread() ?>
  <span class="pagedesc">&nbsp;</span>
</div>

<script language="javascript">
  //var suri = '<?= $_SERVER["REQUEST_URI"] ?>';
  //var sop = suri.split("index.php").join("popup.php");
  //var sajax = suri.split("index.php").join("ajax.php");
  jQuery(document).ready(function () {
    var ot = jQuery('#datatable').dataTable({
      "bSort": true,
      "bFilter": true,
      "iDisplayStart": 0,
      "sAjaxSource": sajax + "&json=1",
      "aoColumns": [
        {"mData": "id", "bSortable": false, "sClass": "alignRight"},
        {"mData": "rmbDate"},
        {"mData": "rmbNo"},
        {"mData": "catName"},
        {"mData": "typeName"},
        {"mData": "rmbVal", "sClass": "alignRight"}
      ],
      "aaSorting": [[0, "desc"]],
      "sPaginationType": "full_numbers",
      "fnInitComplete": function (oSettings) {
        oSettings.oLanguage.sZeroRecords = "No data available";
      },
      "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        jQuery("td:first", nRow).html(iDisplayIndexFull + 1);
        return nRow;
      },
      "bProcessing": true,
      "oLanguage": {
        "sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
      },
      "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
        var iTotal = 0;
        for (var i = 0; i < aaData.length; i++)
        {
          iTotal += parseFloat(aaData[i]["rmbValue"]) * 1;
        }
        var iPageTotal = 0;
        for (var i = iStart; i < iEnd; i++)
        {
          iPageTotal += parseFloat(aaData[aiDisplay[i]]["rmbValue"]) * 1;
        }
        var nCells = nRow.getElementsByTagName('th');
        nCells[1].innerHTML = "<span class=\"mnum\" id=\"spPageTotal\">" + iPageTotal + "</span> / <span  class=\"mnum\"  id=\"spTotal\">" + iTotal + "</span>";
        formatNumber();
      }
    });
    jQuery("thead input").keyup(function () {
      var td = jQuery(this).parent();
      var idx = td.parent().children().index(td);
      ot.fnFilter(this.value, idx);
    });

    jQuery("thead select").change(function () {
      var td = jQuery(this).parent();
      var idx = td.parent().children().index(td);
      ot.fnFilter(this.value, idx);
    });
    jQuery("#datatable_wrapper #datatable_filter label").html("").append("<b>Tahun</b>&nbsp;&nbsp;<?php echo $ui->createComboYear("cyear", $cyear) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
    jQuery("#cyear").live("change", function (e) {
      e.preventDefault();
      ot.fnReloadAjax(sajax + "&json=1&cyear=" + jQuery(this).val());
    });
    function formatNumber() {
      jQuery(".mnum").each(function () {
        jQuery(this).css("text-align", "right");
        jQuery(this).autoNumeric("init", {wEmpty: 'zero', mDec: 0, vMin: 0, vMax: 9999999999999999999999999999});
      });
    }
  });
</script>


<div id="contentwrapper" class="contentwrapper">
  <?php include './tmpl/__emp_header__.php'; ?>
  <br class="clear" >
  <!-- table list data -->
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
    <thead>
      <tr>
        <th width="40px">No.</th>
        <th width="80px">Tanggal</th>
        <th>Nomor</th>
        <th>Kategori</th>
        <th>Tipe</th>
        <th width="150px">Nilai</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="5" style="text-align: right;font-weight: bold">TOTAL:</th>
        <th  style="text-align: right;font-weight: bold"></th>
      </tr>
    </tfoot>
  </table>
</div>
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
$cmonth = isset($_GET["cmonth"]) ? $_GET["cmonth"] : date("m");
if ($_GET[json] == 1) {
	
  header("Content-type: application/json");
  $emp = new EmpRmb();
  $emp->parentId = $_SESSION["curr_emp_id"];
  echo $emp->loadTablePeriod($cmonth, $cyear, $arrParam[$s]);
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

  jQuery(document).ready(function () {
    ot = jQuery('#datatable').dataTable({
      "bSort": true,
      "bFilter": true,
      "iDisplayStart": 0,
      "sAjaxSource": sajax + "&json=1",
      "aoColumns": [
        {"mData": "id", "bSortable": false, "sClass": "alignRight"},
        {"mData": "rmbDate"},
        {"mData": "rmbNo"},
        {"mData": "typeName"},
       <?php if($arrParam[$s] != "m"){ ?> {"mData": "catName"}, <?php } ?>
        {"mData": "rmbVal", "sClass": "alignRight"},
        {"mData": "status", "sClass": "alignCenter",
          "fnRender": function (o) {
            if (o.aData["status"] === "1") {
              return '<img src=\"styles/images/t.png\">';
            } else if (o.aData["status"] === "2") {
              return '<img src=\"styles/images/f.png\">';
			}else if (o.aData["status"] === "3") {
              return '<img src=\"styles/images/o.png\">';            
            } else {
              return '<img src=\"styles/images/p.png\">';
            }
          }
        },
        {"mData": "stPayment", "sClass": "alignCenter",
          "fnRender": function (o) {
            if (o.aData["stPayment"] === "1") {
              return '<img src=\"styles/images/t.png\">';
            } else {
              return '<img src=\"styles/images/f.png\">';
            }
          }
        },
        {"mData": null, "sClass": "alignCenter", "bSortable": false,
          "fnRender": function (o) {
            var ret = "";
            ret = "<a href=\"#edit\" onclick=\"openBox('" + sop.split("&mode=edit").join("") + "&mode=edit&id=" + o.aData["id"] + "',1000,525)\" title=\"Edit\" class=\"edit\"><span> Edit </span></a> ";
            ret += "<a id='rmRow_" + o.aData["id"] + "' href='javascript:void(0);' title='Hapus' class='delete'><span>Hapus</span></a>";
            return ret;
          }
        }
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
    jQuery('#datatable_wrapper #datatable_filter label').html("");
    jQuery('#datatable_wrapper #datatable_filter').append("<b>Periode</b>:&nbsp;<?php echo $ui->createComboMonth("cmonth", $cmonth); ?>");
    jQuery('#datatable_wrapper #datatable_filter').append("<span>&nbsp;<?php echo $ui->createComboYear("cyear", $cyear); ?></span>");
    jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn1 btn_document" href="#add" onclick=\"openBox(\'' + sop.split("&mode=add").join("") + '&mode=add\',1000,525)\""><span>Tambah Data</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
    jQuery("#cyear,#cmonth").live("change", function (e) {
      e.preventDefault();
      ot.fnReloadAjax(sajax + "&json=1&cyear=" + jQuery("#cyear").val() + "&cmonth=" + jQuery("#cmonth").val());
    });
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
        <th>Tipe</th>
        <th style="display:<?php echo $arrParam[$s] == "m" ? "none" : "block"; ?>">Kategori</th>
        <th width="100px">Nilai</th>
        <th width="80px">Approval</th>
        <th width="80px">Bayar</th>
        <th width="80px">Control</th>
      </tr>
    </thead>
    <thead>
      <tr id="sfilter">
        <th width="40px">&nbsp;</th>
        <th><input id="sfilter" type="text" style="width: 95%"/></th>
        <th><input id="sfilter" type="text" style="width: 95%"/></th>
        <th><input id="sfilter"  type="text" style="width: 95%"/></th>
        <th><input id="sfilter"  type="text" style="width: 95%"/></th>
        <th><input id="sfilter"  type="text" style="width: 95%"/></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </thead>  
    <tbody>
      <tr>
        <td colspan="10" class="dataTables_empty">Loading data from server</td>
      </tr>
    </tbody>
  </table>
</div>
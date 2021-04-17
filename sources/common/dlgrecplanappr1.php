<?php
session_start();

$cutil = new Common();
$ui = new UIHelper();
if (isset($_POST["btnSimpan"])) {
  $apprSta = $_POST["apprDivSta"];
  $apprDate = $_POST["apprDivDate"];
  $apprRem = $_POST["apprDivRemark"];
  $eid = $_SESSION["entity_id"];
  $sql = " UPDATE rec_plan SET appr_div_sta='$apprSta', appr_div_by='$cUsername', appr_div_date='$apprDate', appr_div_remark='$apprRem' WHERE id='$eid'";
//  echo $sql;
  $cutil->execute($sql);
  echo "<script>parent.approveClose();closeBox();</script>";
  exit();
}

$e = new RecPlan();
$e->id = $_GET["id"];
$_SESSION["entity_id"] = $e->id;
$e = $e->getById();


$__validate["formid"] = "apprForm";
$__validate["items"] = array(
    "apprDivSta" => array("rule" => "required", "msg" => "Field Status harus diisi.."),
    "apprDivDate" => array("rule" => "required", "msg" => "Field Tanggal harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<style>
  #p0 {
    margin: 5px 0;
  }
</style>
<div class="centercontent contentpopup">
  <div class="pageheader">
   
    <span class="pagedesc">&nbsp;</span>
  </div>
  <?php if (isset($menuAccess[$s]["apprlv1"])) { ?>
    <div id="contentwrapper" class="contentwrapper" style="margin-top: -60px;">
      <form id="apprForm"  action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" class="stdform">
        <div class="widgetbox">
          <div class="contenttitle2"><h3>Approval GENERAL MANAGER</h3></div>
          <p id="p0">
            <label class="l-input-small">Approve By&nbsp;&nbsp;</label>
            <span class="field"><?= $cUsername ?>&nbsp;</span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Tanggal", $e->apprDivDate, "apprDivDate", "apprDivDate", "hasDatePicker2", " style='width:20%;' ") ?></p>
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="fieldB"><?= $cutil->generateSelectArray2(array(1 => "Ditolak", 2 => "Pending", 3 => "Disetujui"), "apprDivSta", $e->apprDivSta) ?></span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $e->apprDivRemark, "apprDivRemark", "apprDivRemark", "", " style='width:60%;' ") ?></p>
          <center>
              <div style="top:10px; right:35px; position:absolute">

            <p class="stdformbutton">
              <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
              <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
            </p>
            </div>
          </center>
        </div>
      </form>
    </div>
    <?php
  } else {
    switch ($e->apprDivSta) {
      case 1:
        $status = "Ditolak";
        break;
      case 2:
        $status = "Pending";
        break;
      case 3:
        $status = "Disetujui";
        break;
      default:
        $status = "Belum ada approval.";
        break;
    }
    ?>
    <div id="contentwrapper" class="contentwrapper" style="margin-top: -60px;">
      <form id="apprForm"  action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" class="stdform">
        <div class="widgetbox">
          <div class="contenttitle2"><h3>Approval GENERAL MANAGER</h3></div>
          <p id="p0">
            <label class="l-input-small">Approve By&nbsp;&nbsp;</label>
            <span class="field"><?= $cUsername ?>&nbsp;</span>
          </p>
          <p id="p0">
            <label class="l-input-small">Tanggal&nbsp;&nbsp;</label>
            <span class="field"><?= $e->apprDivDate ?>&nbsp;</span>
          </p>
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="field"><?= $status ?>&nbsp;</span>
          </p>
          <p id="p0">
            <label class="l-input-small">Keterangan&nbsp;&nbsp;</label>
            <span class="field"><?= $e->apprDivRemark ?>&nbsp;</span>
          </p>
          <center>
            <p class="stdformbutton">
              <input type="button" class="cancel radius2 btn_back" value="Tutup" onclick="closeBox();"/>
            </p>
          </center>
        </div>
      </form>
    </div>
  <?php } ?>
</div>



<script language="javascript">
  jQuery(document).ready(function () {
    jQuery("#apprForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });

    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-30').chosen({allow_single_deselect: true, width: '30%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

  });
</script>
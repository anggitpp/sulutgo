<?php
session_start();

$cutil = new Common();
$ui = new UIHelper();
if (isset($_POST["btnSimpan"])) {
  $status = $_POST["status"];
  // $apprDate = $_POST["apprSdmDate"];
  // $apprRem = $_POST["apprSdmRemark"];
  $eid = $_SESSION["entity_id"];
  $sql = " UPDATE rec_plan SET status='$status' WHERE id='$eid'";
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
    "status" => array("rule" => "required", "msg" => "Field Status harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<style>
  #p0 {
    margin: 5px 0;
  }
</style>
<div class="centercontent contentpopup" style="margin-right: 50px;">
  <div class="pageheader">

    <span class="pagedesc">&nbsp;</span>
  </div>
  <?php if (isset($menuAccess[$s]["apprlv2"])) { ?>
    <div id="contentwrapper" class="contentwrapper" style="margin-top: -60px;">
      <form id="apprForm"  action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" class="stdform">
        <div class="widgetbox">
          <div class="contenttitle2"><h3>Status</h3></div>
         
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="fieldB"><?= $cutil->generateSelectArray2(array(1 => "Ditolak", 2 => "Disetujui"), "status", $e->status) ?></span>
          </p>
         
          <center>
           <div style="top:5px; right:35px; position:absolute">
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
    switch ($e->status) {
      case 1:
        $status = "Ditolak";
        break;
      case 2:
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
          <div class="contenttitle2"><h3>Status</h3></div>
       
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="field"><?= $status ?>&nbsp;</span>
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
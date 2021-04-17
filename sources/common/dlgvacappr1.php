<?php
session_start();


$cutil = new Common();
$ui = new UIHelper();
if (isset($_POST["btnSimpan"])) {
  $eid = $_SESSION["entity_id"];
  $e = new RecVacancy();
  $e->id = $eid;
  $e = $e->getById();
//  var_dump($e);
  $apprSta = $_POST["appr1Sta"];
  $apprDate = $_POST["appr1Date"];
  $apprRem = $_POST["appr1Remark"];
  $sql = " UPDATE rec_vacancy SET appr1_sta='$apprSta', appr1_by='$cUsername', appr1_date='$apprDate', appr1_remark='$apprRem' WHERE id='$eid'";
//  echo $sql;
  $cutil->execute($sql);
  if ($apprSta == 3) {
    $rpo = new RecPost();
    $rpo->vacId = $e->id;
    $rpo = $rpo->getByIdVacId();
    if ($rpo->id == null) {
      $rpo->planId = $e->planId;
      $rpo->vacId = $e->id;
      $rpo = $rpo->persist();
    }
    $sql1 = "INSERT into rec_post_detail (parent_id, post_type_id, post_sort, post_value, cre_by, cre_date)
           SELECT '$rpo->id', t1.kodeData, t1.urutanData, '', '$cUsername', CURRENT_TIMESTAMP()
            FROM mst_data t1 WHERE t1.kodeCategory='R10'
            ORDER BY t1.urutanData
            ";
    $cutil->execute($sql1);
  }
//  else {
//    $sql0 = "DELETE FROM rec_post WHERE vac_id=$eid";
//    $cutil->execute($sql0);
//  }
  echo "<script>parent.approveClose();closeBox();</script>";
  exit();
}

$e = new RecVacancy();
$e->id = $_GET["id"];
$_SESSION["entity_id"] = $e->id;
$e = $e->getById();
//var_dump($e);
$__validate["formid"] = "apprForm";
$__validate["items"] = array(
    "appr1Sta" => array("rule" => "required", "msg" => "Field Status harus diisi.."),
    "appr1Date" => array("rule" => "required", "msg" => "Field Tanggal harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<style>
  #p0 {
    margin: 5px 0;
  }
</style>
<div class="centercontent contentpopup" style="margin-right: 30px;">
  <div class="pageheader">
    
    <span class="pagedesc">&nbsp;</span>
  </div>
  <?php if (isset($menuAccess[$s]["apprlv1"])) { ?>
    <div id="contentwrapper" class="contentwrapper" style="margin-top: -60px;">
      <form id="apprForm"  action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" class="stdform">
        <div style="top:30px; right:35px; position:absolute">
      <p class="stdformbutton">
        <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
              <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
      </p>
    </div>     <br class="clear" />
        <div class="widgetbox">
          <div class="contenttitle2"><h3>Approval Lowongan</h3></div>
          <p id="p0">
            <label class="l-input-small">Approve By&nbsp;&nbsp;</label>
            <span class="field"><?= $cUsername ?>&nbsp;</span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Tanggal", $e->appr1Date, "appr1Date", "appr1Date", "hasDatePicker2", " style='width:20%;' ") ?></p>
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="fieldB"><?= $cutil->generateSelectArray2(array(1 => "Ditolak", 2 => "Pending", 3 => "Disetujui"), "appr1Sta", $e->appr1Sta) ?></span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $e->appr1Remark, "appr1Remark", "appr1Remark", "", "style='width:60%;' ") ?></p>
         
        </div>
      </form>
    </div>
    <?php
  } else {
    switch ($e->appr1Sta) {
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
            <span class="field"><?= $e->appr1Date ?>&nbsp;</span>
          </p>
          <p id="p0">
            <label class="l-input-small">Status&nbsp;&nbsp;</label>
            <span class="field"><?= $status ?>&nbsp;</span>
          </p>
          <p id="p0">
            <label class="l-input-small">Keterangan&nbsp;&nbsp;</label>
            <span class="field"><?= $e->appr1Remark ?>&nbsp;</span>
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
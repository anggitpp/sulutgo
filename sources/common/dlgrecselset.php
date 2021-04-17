<?php
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
  header("Location: " . str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
}

$cutil = new Common();
$ui = new UIHelper();
if (isset($_POST["btnSimpan"])) {
  $rs = new RecSelection();
  $rs->processForm();
  echo "<script>parent.window.location.reload();closeBox();</script>";
  die();
}
$cVac = $_GET["cvac"];
$rs = new RecSelection();
$rs->vacId = $cVac;
$rs = $rs->getByVacId();
$_SESSION["entity_id"] = $rs->id;
$rv = new RecVacancy();
$rv->id = $cVac;
$rv = $rv->getById();
//var_dump($rv);
$rp = new RecPlan();
$rp->id = $rv->planId;
$rp = $rp->getById();
//var_dump($rp);



$__validate["formid"] = "myForm";
$__validate["items"] = array(
  "eduType" => array("rule" => "required", "msg" => "Field Tingkatan harus diisi.."),
  "eduName" => array("rule" => "required", "msg" => "Field Nama Lembaga harus diisi.."),
  "eduCity" => array("rule" => "required", "msg" => "Field Kota harus diisi.."),
  "eduYear" => array("rule" => "required", "msg" => "Field Tahun Lulus harus diisi.."),
  );
#SMA = 554
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<style>
  #p0 {
    margin: 5px 0;
  }
  fieldset {
    margin-left: 10px;
    margin-right: 10px;
    margin-bottom: 10px;
  }
  legend {
    font-weight: bold;
    font-size: 1.2em;
    color: #0A246A;
    /*border: 1px solid #03F;*/
    padding: 5px;
  }
  fieldset label{
    margin-left: 10px;
  }
</style>


<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s] . " - " . " Setting Tahapan" ?></h1>
    <span class="pagedesc">&nbsp;</span>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
      <div style="top:10px; right:35px; position:absolute">
        <p class="stdformbutton">
          <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
          <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
        </p>
      </div>     <br class="clear" />
      <fieldset>
        <legend>LOWONGAN</legend>
        <p>
          <label class="l-input-small" style="width:15%">Judul</label>
          <span class="field" style="margin-left: 20%"><?= $rp->subject ?>&nbsp;</span>
        </p>        <table style="width: 100%">
        <tr>
          <td style="width: 50%">
            <p><label class="l-input-small">Tgl. Pengajuan</label><span class="field" style="margin-left: 40%"><?= getTgl($rp->proposeDate) ?>&nbsp;</span></p>
            <p><label class="l-input-small">Tgl. Kebutuhan</label><span class="field" style="margin-left: 40%"><?= getTgl($rp->needDate) ?>&nbsp;</span></p>
          </td>
          <td style="width: 50%">
            <p><label class="l-input-small">Posisi</label><span class="field" style="margin-left: 40%"><?= $rp->posAvailable ?>&nbsp;</span></p>
            <p><label class="l-input-small">Divisi-Bagian</label><span class="field" style="margin-left: 40%"><?= $cutil->getMstDataDesc($rp->divId) ?>&nbsp;</span></p>
          </td>
        </tr>
      </table>
    </fieldset>

    

    <div class="contenttitle2"><h3>Tahapan Seleksi</h3></div>
    <?php
    $rsph = new RecSelectionPhase();
    if (empty($rs->id)) {
        //NEW SET
      $rdata = $rsph->loadTableNew();
    } else {
        //EDIT SET
      $rdata = $rsph->loadTableByParentId($rs->id);
      $len = count($rdata);
      if ($len == 0) {
        $rdata = $rsph->loadTableNew();
      }
    }
    ?>
    <table style="width: 100%" class="stdtable stdtablequick">
      <thead>
        <tr>
          <th style="width: 30px;">No.</th>
          <th>Tahapan</th>
          <th style="width: 30px; text-align: center;">Check</th>
          <th style="width: 100px;">Tanggal</th>
          <th style="width: 100px;">Waktu</th>
          <th>Lokasi</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $len = count($rdata);
        foreach ($rdata as $r) {
          echo "
          <tr>
            <input type=\"hidden\" id=\"rsId_$no\" name=\"rsId[]\" value=\"" . $r["id"] . "\"  />
            <input type=\"hidden\" id=\"rsPhaseId_$no\" name=\"rsPhaseId[]\" value=\"" . $r["phaseId"] . "\"  />
            <input type=\"hidden\" id=\"rsPhaseSort_$no\" name=\"rsPhaseSort[]\" value=\"" . $r["phaseSort"] . "\"  />
            <td style=\"text-align: center;\">$no.</td>
            <td>" . $r["phaseName"] . "</td>
            <td style=\"text-align: center;\">";
              if ($no == $len) {
                echo "<input type=\"hidden\" id=\"rsPhaseStatus_$no\" name=\"rsPhaseStatus[]\" value=\"1\"  />";
                echo "<input type=\"checkbox\" id=\"rsPhaseFinal\" name=\"rsPhaseFinal\" value=\"1\" checked=checked disabled=disabled  />" . "</td>";
              } else {
                echo "<input type=\"checkbox\" id=\"rsPhaseStatus_$no\" name=\"rsPhaseStatus[]\" value=\"1\" " . ($r["phaseStatus"] == "1" || $no == $len ? " checked=checked " : "") . ($no == $len ? "disabled=disabled" : "") . " />" . "</td>";
              }
              echo "<td> <input type=\"text\" id=\"rsPhaseDate_$no\" name=\"rsPhaseDate[]\" value=\"" . $r["phaseDate"] . "\" class=\"hasDatePicker2\" /> </td>
              <td> <input type=\"text\" id=\"rsPhaseTime_$no\" name=\"rsPhaseTime[]\" value=\"" . $r["phaseTime"] . "\" /> </td>
              <td> <input type=\"text\" id=\"rsPhaseLoc_$no\" name=\"rsPhaseLoc[]\" value=\"" . $r["phaseLoc"] . "\"  /> </td>
              <td> <input type=\"text\" id=\"rsPhaseRemark_$no\" name=\"rsPhaseRemark[]\" value=\"" . $r["phaseRemark"] . "\"  /> </td>
            </tr>
            ";
            $no++;
          }
          ?>
        </tbody>
      </table>
    </br>
    <p>
      <?php echo $ui->createLabelSpanTextAreaB("Catatan", $rs->remark, "remark", "remark", "rows=5 style='margin-top:40px;margin-left:-433px;width:44%'") ?>
      <input type="hidden" value="<?= $rp->id ?>" name="planId" />
      <input type="hidden" value="<?= $rv->id ?>" name="vacId" />
    </p>
    <p>
      <?php if (!empty($rs->id)) { ?><span>Created: <?= $rs->creDate . " by " . $rs->creBy ?></span> <?php } ?>
      </p>
      
    </form>
  </div>
</div>
<script>
  jQuery(document).ready(function () {
    jQuery("#myForm").validate().settings.ignore = [];
    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
//    jQuery(".hasTimePicker").timepicker();
});
</script>
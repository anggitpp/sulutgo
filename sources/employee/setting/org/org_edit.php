<?php
session_start();
if (!isset($menuAccess[$s]["add"]) || !isset($menuAccess[$s]["edit"])) {
  $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
}

$empc = new MstData();
if (isset($_POST["btnSimpan"])) {
  $empc = $empc->processForm();
  $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
  $_SESSION["entity_id"] = "";
  $_SESSION["entity_cat"] = "";

//  echo $_SESSION["pg_org"] . "<br>";
//  header("Location: " . $loc);
  echo "<script>window.parent.location='$loc';</script>";
  die();
}

$level = $_GET["lv"];
$empc->kodeData = $_GET["id"];
$empc = $empc->getById();
$_SESSION["entity_id"] = $empc->kodeData;

$cutil = new Common();
$ui = new UIHelper();
$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
$oType = "Direktorat";
$eType = "Direktorat";
$kodeInduk0;
$kodeInduk1;
$kodeInduk2;
switch ($level) {
  case 2:
    $oType = "Divisi";
    $eType = "Direktorat";
    $kodeInduk = $kodeInduk0 = (isset($_GET["pid"]) ? $_GET["pid"] : $empc->kodeInduk);
    $cat = "X05";
    break;
  case 3:
    $oType = "Bagian";
    $eType = "Divisi";
    $kodeInduk = $kodeInduk1 = (isset($_GET["pid"]) ? $_GET["pid"] : $empc->kodeInduk);
    $kodeInduk0 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk1'", "id");
    $cat = "X06";
    break;
  case 4:
    $oType = "Unit";
    $eType = "Bagian";
    $kodeInduk = $kodeInduk2 = (isset($_GET["pid"]) ? $_GET["pid"] : $empc->kodeInduk);
    $kodeInduk1 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk2'", "id");
    $kodeInduk0 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk1'", "id");
    $cat = "X07";
    break;
}
$_SESSION["entity_cat"] = $cat;

$_SESSION["pg_org"] = $kodeInduk0;

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "name" => array("rule" => "required", "msg" => "Field Name harus diisi.."),
    "kodeInduk" => array("rule" => "required", "msg" => "Field $eType harus diisi.."),
    "kodeInduk0" => array("rule" => "required", "msg" => "Field Direktorat harus diisi.."),
    "kodeInduk1" => array("rule" => "required", "msg" => "Field Divisi harus diisi.."),
    "kodeInduk2" => array("rule" => "required", "msg" => "Field Bagian harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";
?>

<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
    <?= getBread(ucwords($mode . " data " . $oType)) ?>
    <span class="pagedesc">&nbsp;</span>

    <?php // include './tmpl/__emp_header__.php';    ?>
    <div id="contentwrapper" class="contentwrapper">
      <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
        <div class="widgetbox">
          <div class="contenttitle2"><h3>DATA <?= $oType ?></h3></div>
          <?php
          if ($level > 1) {
            //CB DIRECTORAT
            ?>
            <p id="p0">
              <label class="l-input-small">Direktorat&nbsp;&nbsp;<span class="required">*)</span></label>
              <span class="fieldB">
                <?php
                $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X04' order by urutanData";
                echo $cutil->generateSelectWithEmptyOptionId($sql, "id", "description", "kodeInduk0", ($level == 2 ? "kodeInduk" : "kodeInduk0"), $kodeInduk0, "", " $disabled class='single-deselect-td'");
                ?>
              </span>
            </p>
            <?php
          }
          if ($level > 2) {
            //CB DIVISI 
            ?>
            <p id="p0">
              <label class="l-input-small">Divisi&nbsp;&nbsp;<span class="required">*)</span></label>
              <span class="fieldB">
                <?php
                $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       where t2.kodeCategory='X04' order by t1.urutanData";
                echo $cutil->generateSelectChainedWithOptionId($sql, "id", "description", "kodeInduk", "kodeInduk1", ($level == 3 ? "kodeInduk" : "kodeInduk1"), $kodeInduk1, "", "class='single-deselect-td'");
                ?>
              </span>
            </p>
            <?php
          }
          if ($level > 3) {
            //CB BAGIAN
            ?>
            <p id="p0">
              <label class="l-input-small">Bagian&nbsp;&nbsp;<span class="required">*)</span></label>
              <span class="fieldB">
                <?php
                $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       where t3.kodeCategory='X04' order by t1.urutanData";
                echo $cutil->generateSelectChainedWithOptionId($sql, "id", "description", "kodeInduk", "kodeInduk2", ($level == 4 ? "kodeInduk" : "kodeInduk2"), $kodeInduk2, "", "class='single-deselect-td'");
                ?>
              </span>
            </p>
            <?php
          }
          ?>

          <p id="p0"><?= $ui->createLabelSpanInputAttr($oType, $empc->namaData, "namaData", "namaData", "mediuminput", "") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $empc->keteranganData, "keteranganData", "keteranganData", "mediuminput", "") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Order", (!empty($empc->urutanData) ? $empc->urutanData : $cutil->getDescription("SELECT coalesce(max(urutanData),0)+1 orderNo FROM mst_data where kodeInduk='$kodeInduk'", "orderNo")), "urutanData", "urutanData", "v20", " style='text-align:right;'") ?></p>

          <?php
          if ($empc->statusData == "t") {
            $ac = "checked='checked'";
          } else {
            $na = "checked='checked'";
          }
          ?>
          <p id="p0">
            <label class="l-input-small">Status</label>
            <span class="fieldB">
              <input type="radio" id="sta_0" name="statusData" value="f" <?= $na ?>/> <span class="sradio">Tidak Aktif</span>
              <input type="radio" id="sta_1" name="statusData" value="t" <?= $ac ?>/> <span class="sradio">Aktif</span>
            </span>
          </p>
          <br class="clearall"/>
          <center>
            <p class="stdformbutton">
              <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
              <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox()"/>
            </p>
          </center>
        </div>

      </form>
    </div>
  </div>

  <script type="text/javascript">
    jQuery(document).ready(function () {
      jQuery("#myForm").validate().settings.ignore = [];

      jQuery("#kodeInduk2").chained("#kodeInduk1");
      jQuery("#kodeInduk2").trigger("chosen:updated");

      jQuery("#kodeInduk1").bind("change", function () {
        jQuery("#kodeInduk2").trigger("chosen:updated");
      });
      jQuery("#kodeInduk1").chained("#kodeInduk0");
      jQuery("#kodeInduk1").trigger("chosen:updated");

      jQuery("#kodeInduk0").bind("change", function () {
        jQuery("#kodeInduk1").trigger("chosen:updated");
      });
      jQuery(".hasDatePicker2").datepicker({
        dateFormat: "yy-mm-dd"
      });
      jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
      jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
      jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});
      jQuery("#urutanData").autoNumeric("init", {mDec: 0, aSep: 0, vMin: 1, vMax: 9999});
    });
  </script>


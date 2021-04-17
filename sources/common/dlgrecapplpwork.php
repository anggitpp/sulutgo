<?php
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
  echo "<script>closeBox();</script>";
  header("Location: " . str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
}

$cutil = new Common();
$ui = new UIHelper();
/*
 * 
  $dataTmpl = "&r1=" . urlencode($r["companyName"]);
  $dataTmpl.= "&r2=" . urlencode($r["position"]);
  $dataTmpl.= "&r3=" . urlencode($r["startDate"]);
  $dataTmpl.= "&r4=" . urlencode($r["endDate"]);
  $dataTmpl.= "&r5=" . urlencode($r["divison"]);
  $dataTmpl.= "&r6=" . urlencode($r["dept"]);
  $dataTmpl.= "&r7=" . urlencode($r["city"]);
  $dataTmpl.= "&r8=" . urlencode($r["jobDesc"]);
  $dataTmpl.= "&r9=" . urlencode($r["responsibility"]);
  $dataTmpl.= "&r10=" . urlencode($r["filename"]);
  $dataTmpl.= "&r11=" . urlencode($r["remark"]);
  $dataTmpl.= "&r12=" . urlencode($r["status"]);
 */

$applPwork = new RecApplicantPwork("C");

if (isset($_POST["btnSimpan"])) {
  $rowNum = $_GET["rown"];
  ?>
  <script type="text/javascript">
    var rn = '<?= $rowNum ?>';
    var pwId = '<?= $_POST["id"] ?>';
    var pwCompName = '<?= strtoupper($_POST["companyName"]) ?>';
    var pwPosition = '<?= $_POST["position"] ?>';
    var pwStartDate = '<?= $_POST["startDate"] ?>';
    var pwEndDate = '<?= $_POST["endDate"] ?>';
    var pwDivision = '<?= $_POST["division"] ?>';
    var pwDept = '<?= $_POST["dept"] ?>';
    var pwCity = '<?= $_POST["city"] ?>';
    var pwJobDesc = '<?= $_POST["jobDesc"] ?>';
    var pwResponsibility = '<?= urlencode($_POST["responsibility"]) ?>';
    var pwRemark = '<?= $_POST["remark"] ?>';
    var pwStatus = '<?= $_POST["status"] ?>';
    var pwFilename = '<?= $_POST["tmpFilename"] ?>';
  </script>
  <?php
  if (!empty($_FILES["filename"]["name"])) {
    $fname = $applPwork->processForm();
//    echo "FNAME: $fname";
    echo "
    <script>  
      pwFilename='$fname'; 
    </script>";
  }
  ?>
  <script type="text/javascript">
    parent.commitPwork(rn, pwId, pwCompName, pwPosition, pwStartDate, pwEndDate, pwDivision, pwDept, pwCity, pwJobDesc, pwResponsibility, pwRemark, pwStatus, pwFilename);
    closeBox();
  </script>
  <?php
  die();
}

$applPwork->id = $_GET["id"];
$applPwork->companyName = $_GET["r1"];
$applPwork->position = $_GET["r2"];
$applPwork->startDate = $_GET["r3"];
$applPwork->endDate = $_GET["r4"];
$applPwork->division = $_GET["r5"];
$applPwork->dept = $_GET["r6"];
$applPwork->city = $_GET["r7"];
$applPwork->jobDesc = $_GET["r8"];
$applPwork->responsibility = urldecode($_GET["r9"]);
$applPwork->filename = $_GET["r10"];
$applPwork->remark = $_GET["r11"];
$applPwork->status = $_GET["r12"];

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "companyName" => array("rule" => "required", "msg" => "Field Perusahaan harus diisi.."),
    "position" => array("rule" => "required", "msg" => "Field Jabatan harus diisi.."),
    "startDate" => array("rule" => "required", "msg" => "Field Mulai harus diisi.."),
//    "endDate" => array("rule" => "required", "msg" => "Field Berhenti harus diisi.."),
);

require_once HOME_DIR . "/tmpl/__header__.php";
?>


<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
    <span class="pagedesc">&nbsp;</span>

    <div class="contentwrapper">
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">

    <div style="top:10px; right:35px; position:absolute">
      <p class="stdformbutton">
        <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
        <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
      </p>
    </div>     <br class="clear" />
        <div class="widgetbox">
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Perusahaan", $applPwork->companyName, "companyName", "companyName", "mediuminput", " style='text-transform:uppercase;' ") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Jabatan", $applPwork->position, "position", "position", "mediuminput", "") ?></p>
          <table style="width: 100%">
            <tr>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttrAPP("Mulai", $applPwork->startDate, "startDate", "startDate", "hasDatePicker2", "maxlength=10") ?></p>
              </td>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttrM("Berhenti", $applPwork->endDate, "endDate", "endDate", "hasDatePicker2", "maxlength=10") ?></p>
              </td>
            </tr>
          </table>
          <table style="width: 100%">
            <tr>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttrAPP("Divisi", $applPwork->division, "division", "division", "l-input-small", "") ?></p>
              </td>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttrAPP2("Bagian", $applPwork->dept, "dept", "dept", "mediuminput", "") ?></p>
              </td>
            </tr>
          </table>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Lokasi/Kota", $applPwork->city, "city", "city", "mediuminput", "") ?></p>
          <p>
            <label class="l-input-small">Tugas/ Tanggungjawab</label>
            <textarea id="responsibility" name="responsibility" rows="3" style="width:50%"><?= $applPwork->responsibility ?></textarea>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $applPwork->remark, "remark", "remark", "mediuminput", "") ?></p>
          <p>
            <label class="l-input-small">File Referensi</label>
            <span class="fieldB">
              <?php
              if (!empty($applPwork->filename)) {
                echo "<input type='hidden' name='tmpFilename' value='$applPwork->filename' />";
                echo "<a href=\"download.php?d=applpw&f=$applPwork->filename\"><img src=\"" . getIcon($applPwork->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
              }
              ?>
              <input id="relFilename" type="file" name="filename" style="" />
            </span>
          </p><br>
          <?php
          if ($applPwork->status == "" || $applPwork->status == "0") {
            $na = "checked='checked'";
          } else if ($applPwork->status == "2") {
            $pe = "checked='checked'";
          } else {
            $ac = "checked='checked'";
          }
          ?>
          <p id="p0">
            <label class="l-input-small">Status</label>
            <span class="fieldB">
              <input type="radio" id="sta_0" name="status" value="0" <?= $na ?>/> <span class="sradio">Tidak Aktif</span>
              <input type="radio" id="sta_2" name="status" value="2" <?= $pe ?>/> <span class="sradio">Pending</span>
              <input type="radio" id="sta_1" name="status" value="1" <?= $ac ?>/> <span class="sradio">Aktif</span>
            </span>
          </p>
        </div>
        
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery("#myForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});
    jQuery("#companyName").focus(60);

  });
</script>

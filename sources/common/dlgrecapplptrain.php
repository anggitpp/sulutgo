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
  $dataTmpl = "&r1=" . urlencode($r["instituteName"]);
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

$applPtrain = new RecApplicantPtrain("C");

if (isset($_POST["btnSimpan"])) {
  $rowNum = $_GET["rown"];
  ?>
  <script type="text/javascript">
    var rn = '<?= $rowNum ?>';
    var ptId = '<?= $_POST["id"] ?>';
    var ptName = '<?= strtoupper($_POST["name"]) ?>';
    var ptTraining = '<?= strtoupper($_POST["training"]) ?>';
    var ptPosition = '<?= $_POST["position"] ?>';
    var ptStart = '<?= $_POST["start"] ?>';
    var ptEnds = '<?= $_POST["ends"] ?>';
    var ptLocation = '<?= $_POST["location"] ?>';
    var ptRemark = '<?= $_POST["remark"] ?>';
    var ptFilename = '<?= $_POST["tmpFilename"] ?>';
  </script>
  <?php
  if (!empty($_FILES["filename"]["name"])) {
    $fname = $applPtrain->processForm();
//    echo "FNAME: $fname";
    echo "
    <script>  
      ptFilename='$fname'; 
    </script>";
  }
  ?>
  <script type="text/javascript">
    parent.commitPtrain(rn, ptId, ptName, ptTraining, ptPosition, ptStart, ptEnds, ptLocation, ptRemark, ptFilename);
    closeBox();
  </script>
  <?php
  die();
}

$applPtrain->id = $_GET["id"];
$applPtrain->name = $_GET["r1"];
$applPtrain->training = $_GET["r2"];
$applPtrain->position = $_GET["r3"];
$applPtrain->start = $_GET["r4"];
$applPtrain->ends = $_GET["r5"];
$applPtrain->location = $_GET["r6"];
$applPtrain->filename = $_GET["r7"];
$applPtrain->remark = $_GET["r8"];

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "name" => array("rule" => "required", "msg" => "Field Perusahaan harus diisi.."),
    "training" => array("rule" => "required", "msg" => "Field Training harus diisi.."),
    "position" => array("rule" => "required", "msg" => "Field Bagian harus diisi.."),
    "start" => array("rule" => "required", "msg" => "Field Mulai harus diisi.."),
   "ends" => array("rule" => "required", "msg" => "Field Berhenti harus diisi.."),
);

require_once HOME_DIR . "/tmpl/__header__.php";
?>


<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s]." - Data Training" ?></h1>
    <span class="pagedesc">&nbsp;</span>

    <div class="contentwrapper">
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">

    <div style="top:10px; right:35px; position:absolute">
      <p class="stdformbutton">
        <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
        <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
      </p>
    </div>     
        <div class="widgetbox">
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Lembaga", $applPtrain->name, "name", "name", "mediuminput", " style='text-transform:uppercase;' ") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama Training", $applPtrain->training, "training", "training", "mediuminput", "") ?></p>
          <table style="width: 100%">
            <tr>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttrAPP("Mulai", $applPtrain->start, "start", "start", "hasDatePicker2", "maxlength=10") ?></p>
              </td>
              <td style="width: 50%">
                <p id="p0"><?= $ui->createLabelSpanInputAttr("Berhenti", $applPtrain->ends, "ends", "ends", "hasDatePicker2", "maxlength=10") ?></p>
              </td>
            </tr>
          </table>
         
            <p id="p0"><?= $ui->createLabelSpanInputAttrZ("Bagian", $applPtrain->position, "position", "position", "mediuminput", "") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Lokasi/Kota", $applPtrain->location, "location", "location", "mediuminput", "") ?></p>
          
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $applPtrain->remark, "remark", "remark", "mediuminput", "") ?></p>
          <p>
            <label class="l-input-small">File Referensi</label>
            <span class="fieldB">
              <?php
              if (!empty($applPtrain->filename)) {
                echo "<input type='hidden' name='tmpFilename' value='$applPtrain->filename' />";
                echo "<a href=\"download.php?d=applpt&f=$applPtrain->filename\"><img src=\"" . getIcon($applPtrain->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
              }
              ?>
              <input id="relFilename" type="file" name="filename" style="" />
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
    jQuery("#name").focus(60);

  });
</script>

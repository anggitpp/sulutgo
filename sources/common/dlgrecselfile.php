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

$selFile = new RecSelectionFile("C");

if (isset($_POST["btnSimpan"])) {
  $rowNum = $_GET["rown"];
  ?>
  <script type="text/javascript">
    var rn = '<?= $rowNum ?>';
    var sfId = '<?= $_POST["id"] ?>';
    var sfName = '<?= strtoupper($_POST["name"]) ?>';
    var sfRemark = '<?= $_POST["remark"] ?>';
    var sfFilename = '<?= $_POST["tmpFilename"] ?>';
  </script>
  <?php
  if (!empty($_FILES["filename"]["name"])) {
    $fname = $selFile->processForm();
//    echo "FNAME: $fname";
    echo "
    <script>  
      sfFilename='$fname'; 
    </script>";
  }
  ?>
  <script type="text/javascript">
    parent.commitSelFile(rn, sfId, sfName, sfRemark, sfFilename);
    closeBox();
  </script>
  <?php
  die();
}

$selFile->id = $_GET["id"];
$selFile->name = $_GET["r1"];
$selFile->filename = $_GET["r2"];
$selFile->remark = $_GET["r3"];

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "name" => array("rule" => "required", "msg" => "Field Nama File harus diisi.."),
);

require_once HOME_DIR . "/tmpl/__header__.php";
?>


<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s]." - File" ?></h1>
    <span class="pagedesc">&nbsp;</span>

    <div class="contentwrapper">
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">

    <div style="top:10px; right:35px; position:absolute">
      <p class="stdformbutton">
        <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
        <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="location.href = '<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])) ?>';"/>
      </p>
    </div>     
        <div class="widgetbox">
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama File", $selFile->name, "name", "name", "mediuminput", " style='text-transform:uppercase;' ") ?></p>         
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $selFile->remark, "remark", "remark", "mediuminput", "") ?></p>
          <p>
            <label class="l-input-small">File Referensi</label>
            <span class="fieldB">
              <?php
              if (!empty($selFile->filename)) {
                echo "<input type='hidden' name='tmpFilename' value='$selFile->filename' />";
                echo "<a href=\"download.php?d=applpt&f=$selFile->filename\"><img src=\"" . getIcon($selFile->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
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

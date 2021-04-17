<?php
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
  echo "<script>closeBox();</script>";
  header("Location: " . str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
}

$cutil = new Common();
$ui = new UIHelper();

$applPfile = new RecApplicantPfile("C");

if (isset($_POST["btnSimpan"])) {
  $rowNum = $_GET["rown"];
  ?>
  <script type="text/javascript">
    var rn = '<?= $rowNum ?>';
    var ptId = '<?= $_POST["id"] ?>';
    var pfName = '<?= strtoupper($_POST["name"]) ?>';
    var ptRemark = '<?= $_POST["remark"] ?>';
    var ptFilename = '<?= $_POST["tmpFilename"] ?>';
  </script>
  <?php
  if (!empty($_FILES["filename"]["name"])) {
    $fname = $applPfile->processForm();
//    echo "FNAME: $fname";
    echo "
    <script>  
      ptFilename='$fname'; 
    </script>";
  }
  ?>
  <script type="text/javascript">
    parent.commitPfile(rn, ptId, pfName, ptRemark, ptFilename);
    closeBox();
  </script>
  <?php
  die();
}

$applPfile->id = $_GET["id"];
$applPfile->name = $_GET["r1"];
$applPfile->filename = $_GET["r2"];
$applPfile->remark = $_GET["r3"];

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "name" => array("rule" => "required", "msg" => "Field Perusahaan harus diisi.."),
);

require_once HOME_DIR . "/tmpl/__header__.php";
?>


<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s]." - Data File" ?></h1>
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
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Name", $applPfile->name, "name", "name", "mediuminput", " style='text-transform:uppercase;' ") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $applPfile->remark, "remark", "remark", "mediuminput", "") ?></p>
          <p>
            <label class="l-input-small">File</label>
            <span class="fieldB">
              <?php
              if (!empty($applPfile->filename)) {
                echo "<input type='hidden' name='tmpFilename' value='$applPfile->filename' />";
                echo "<a href=\"download.php?d=applpt&f=$applPfile->filename\"><img src=\"" . getIcon($applPfile->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
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
    jQuery("#name").focus(60);

  });
</script>

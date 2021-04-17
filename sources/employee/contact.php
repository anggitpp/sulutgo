<?php
session_start();
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
if (empty($_SESSION["curr_emp_id"])) {
    echo 
  "<script>
    alert(\"Silakan memilih Pegawai terlebih dahulu...\");
    window.location.href=\"".APP_URL . "/?c=3&p=8&m=79&s=82\";
  </script>";
//  header("Location: " . APP_URL . "/index.php?c=3&p=8&m=79&s=82");
}
$empc = new EmpContact();
if (isset($_POST["btnSimpan"])) {
  $empc = $empc->processForm();
  $_SESSION["entity_id"] = "";
//  header("Location: " . $loc);
//  die();
}
$empc->parentId = $_SESSION["curr_emp_id"];
$empc = $empc->getByParentId();
$_SESSION["entity_id"] = $empc->id;

$cutil = new Common();
$ui = new UIHelper();
$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
$__validate["formid"] = "myForm";
$__validate["items"] = array(
//    "name" => array("rule" => "required", "msg" => "Field Name harus diisi.."),
);

require_once HOME_DIR . "/tmpl/__header__.php";
?>


</style>  <div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
  <?= getBread() ?>
  <span class="pagedesc">&nbsp;</span>
</div>

<div id="contentwrapper" class="contentwrapper">
  <?php include './tmpl/__emp_header__.php'; ?>
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform">
    <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
      <tr>
        <td style="width: 50%;padding: 0px 10px 0px 10px;">
          <div class="widgetbox">
            <div class="contenttitle2"><h3>Serumah</h3></div>
          </div>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama", $empc->srNama, "srNama", "srNama", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Hubungan", $empc->srHub, "srHub", "srHub", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("No. Telp", $empc->srPhone, "srPhone", "srPhone", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Alamat", $empc->srAddress, "srAddress", "srAddress", "mediuminput", " $disabled") ?></p>
          <p id="p0">
            <label class="l-input-small">Propinsi</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "srProv", $empc->srProv, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p id="p0">
            <label class="l-input-small">Kab/Kota</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' order by kodeInduk,urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "srCity", $empc->srCity, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
        </td>
        <td style="width: 50%;padding: 0px 10px 0px 10px;">
          <div class="widgetbox">
            <div class="contenttitle2"><h3>Beda Rumah</h3></div>
          </div>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama", $empc->brNama, "brNama", "brNama", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Hubungan", $empc->brHub, "brHub", "brHub", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("No. Telp", $empc->brPhone, "brPhone", "brPhone", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Alamat", $empc->brAddress, "brAddress", "brAddress", "mediuminput", "maxlength=50 $disabled") ?></p>
          <p id="p0">
            <label class="l-input-small">Propinsi</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "brProv", $empc->brProv, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p id="p0">
            <label class="l-input-small">Kab/Kota</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' order by kodeInduk,urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "brCity", $empc->brCity, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center; padding-right: 30%;">
          <p class="stdformbutton">
            <br>
            <br>
            <?php
            if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
              echo '<input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;';
            }
            ?>
          </p>
        </td>
      </tr>
    </table>
  </form>
</div>
<script language="javascript">
  var suri = '<?= $_SERVER["REQUEST_URI"] ?>';
  var sajax = suri.split("index.php").join("ajax.php");
  jQuery(document).ready(function () {

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

    jQuery("#srCity").chained("#srProv");
    jQuery("#srCity").trigger("chosen:updated");

    jQuery("#srProv").bind("change", function () {
      jQuery("#srCity").trigger("chosen:updated");
    });


    jQuery("#brCity").chained("#brProv");
    jQuery("#brCity").trigger("chosen:updated");

    jQuery("#brProv").bind("change", function () {
      jQuery("#brCity").trigger("chosen:updated");
    });
    jQuery("#srNama").focus(20);
  });
</script>
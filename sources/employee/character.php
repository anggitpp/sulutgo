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
$empc = new EmpChar();
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
    <div style="top:200px; right:35px; position:absolute">
    </div>
    <br class="clear" />
    <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
      <tr>
        <td width="50%">
          <p>
            <?= $ui->createLabelClassSpanTextArea("Karakter Pribadi", $empc->characteristic, "characteristic", "characteristic", "style='width:80%' $disabled rows=3") ?>
          </p>
          <p>
            <?= $ui->createLabelClassSpanTextArea("Keahlian Khusus", $empc->abilities, "abilities", "abilities", "style='width:80%' $disabled rows=3") ?>
          </p>
        </td>
        <td width="50%">
          <p>
            <?= $ui->createLabelClassSpanTextArea("Hobi", $empc->hobby, "hobby", "hobby", "style='width:80%' $disabled rows=3") ?>
          </p>
          <p>
            <?= $ui->createLabelClassSpanTextArea("Organisasi Sosial", $empc->organization, "organization", "organization", "style='width:80%' $disabled rows=3") ?>
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

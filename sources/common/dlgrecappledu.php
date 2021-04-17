<?php
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
  header("Location: " . str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
}

$cutil = new Common();
$ui = new UIHelper();
/**
 * 
  $dataTmpl = "&r1=" . urlencode($r["eduType"]);
  $dataTmpl.= "&r2=" . urlencode($r["eduName"]);
  $dataTmpl.= "&r3=" . urlencode($r["eduCity"]);
  $dataTmpl.= "&r4=" . urlencode($r["eduYear"]);
  $dataTmpl.= "&r5=" . urlencode($r["eduFac"]);
  $dataTmpl.= "&r6=" . urlencode($r["eduDept"]);
  $dataTmpl.= "&r7=" . urlencode($r["eduEssay"]);
  $dataTmpl.= "&r8=" . urlencode($r["eduFilename"]);
  $dataTmpl.= "&r9=" . urlencode($r["remark"]);
 */
$applEdu = new RecApplicantEdu("C");
if (isset($_POST["btnSimpan"])) {
  $rowNum = $_GET["rown"];
  ?>
  <script type="text/javascript">
    var rn = '<?= $rowNum ?>';
    var eduId = '<?= $_POST["id"] ?>';
    var eduType = '<?= $_POST["eduType"] ?>';
    var eduName = '<?= $_POST["eduName"] ?>';
    var eduCity = '<?= $_POST["eduCity"] ?>';
    var eduYear = '<?= $_POST["eduYear"] ?>';
    var eduFac = '<?= $_POST["eduFac"] ?>';
    var eduDept = '<?= $_POST["eduDept"] ?>';
    var eduEssay = '<?= $_POST["eduEssay"] ?>';
    var remark = '<?= $_POST["remark"] ?>';
    var eduIpk = '<?= $_POST["eduIpk"] ?>';
    var cityName = '<?= $cutil->getMstDataDesc($_POST["eduCity"]) ?>';
    var facName = '<?= $cutil->getMstDataDesc($_POST["eduFac"]) ?>';
    var deptName = '<?= $cutil->getMstDataDesc($_POST["eduDept"]) ?>';
    var levelName = '<?= $cutil->getMstDataDesc($_POST["eduType"]) ?>';
    var eduFilename = '<?= $_POST["tmpEduFilename"] ?>';
  </script>
  <?php
  if (!empty($_FILES["eduFilename"]["name"])) {
    $fname = $applEdu->processForm();
//    echo "FNAME: $fname";
    echo "
    <script>  
      eduFilename='$fname'; 
    </script>";
  }
  ?>
  <script type="text/javascript">
    parent.commitEdu(rn, eduId, eduType, eduName, eduCity, eduYear, eduFac, eduDept, eduEssay, eduFilename, remark, eduIpk, cityName, levelName, facName, deptName);
    closeBox();
  </script>
  <?php
  die();
}
$applEdu->id = $_GET["id"];
$applEdu->eduType = $_GET["r1"];
$applEdu->eduName = $_GET["r2"];
$applEdu->eduCity = $_GET["r3"];
$applEdu->eduYear = $_GET["r4"];
$applEdu->eduFac = $_GET["r5"];
$applEdu->eduDept = $_GET["r6"];
$applEdu->eduEssay = $_GET["r7"];
$applEdu->eduFilename = $_GET["r8"];
$applEdu->remark = $_GET["r9"];
$applEdu->eduIpk = $_GET["r10"];

$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
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

<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
    <?= getBread(ucwords(str_replace("edu", "", $mode) . " data Pendidikan")) ?>
    <span class="pagedesc">&nbsp;</span>

    <div id="contentwrapper" class="contentwrapper">
      <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
        
        <div class="widgetbox">
          <!--<div class="contenttitle2"><h3>Data Pendidikan</h3></div>-->
          <p id="p0">
            <label class="l-input-small">Tipe</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[32]."' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "eduType", $applEdu->eduType, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama Lembaga", $applEdu->eduName, "eduName", "eduName", "mediuminput", " style='text-transform:uppercase;'") ?></p>
          <table style="width: 100%">
            <tr>
              <td style="width: 50%">
                <p id="p0">
                  <label class="l-input-medium" style="width: 160px;">Kota</label>
                  <span class="fieldC">
                    <?php
                    $sql = "select t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 
                      JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
                      where 
                      t2.kodeCategory='".$arrParameter[3]."' 
                      AND t2.kodeInduk='1'
                      AND t1.kodeCategory='".$arrParameter[4]."'
                      order by t2.namaData, t1.namaData";
                    echo $cutil->generateSelectWithEmptyOptionP($sql, "id", "description", "eduCity", $applEdu->eduCity, "", "class='single-deselect-td'");
                    ?>
                  </span>
                </p>
              </td>
              <td style="width: 50%">
                <p><?= $ui->createLabelSpanInputAttr("Tahun Lulus", $applEdu->eduYear, "eduYear", "eduYear", "v30", "", "l-input-medium") ?></p>
              </td>
            </tr>
          </table>
          <table style="width: 100%; display: none" id="s1Only">
            <tr>  
              <td style="width: 50%">
                <p id="p0">
                  <label class="l-input-medium" style="width: 160px;">Fakultas&nbsp;&nbsp;<span class="required">*)</span></label>
                  <span class="fieldC">
                    <?php
                    $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[33]."' order by kodeInduk,urutanData";
                    echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "eduFac", $applEdu->eduFac, "", " $disabled class='single-deselect-td'");
                    ?>
                  </span>
                </p>
              </td>
              <td style="width: 50%">
                <p id="p0">
                  <label class="l-input-small">Jurusan&nbsp;&nbsp;<span class="required">*)</span></label>
                  <span class="fieldB">
                    <?php
                    $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[34]."' order by kodeInduk,urutanData";
                    echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "eduDept", $applEdu->eduDept, "", "class='single-deselect-td'");
                    ?>
                  </span>
                </p>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <p id="p0"><?= $ui->createLabelClassSpanTextArea("Judul Skripsi/Tesis", $applEdu->eduEssay, "eduEssay", "eduEssay", "style='width:60%'") ?></p>
              </td>
            </tr>
          </table>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Nilai Rata Rata/IPK", $applEdu->eduIpk, "eduIpk", "eduIpk", "mediuminput", "style='width:6%'") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $applEdu->remark, "remark", "remark", "mediuminput", "") ?></p>

          <p>
            <label class="l-input-small">File</label>
            <span class="fieldB">
              <?php
              if ($mode == "eduedit" && $applEdu->eduFilename != "") {
                echo "<input type='hidden' name='tmpEduFilename' value='$applEdu->eduFilename' />";
                echo "<a href=\"download.php?d=appledu&f=$applEdu->eduFilename\"><img src=\"" . getIcon($applEdu->eduFilename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
              }
              ?>
              <input id="eduFilename" type="file" name="eduFilename" style="" />
            </span>
          </p><br>
          <!--          <?php
          if ($applEdu->status == "" || $applEdu->status == "0") {
            $na = "checked='checked'";
          } else if ($applEdu->status == "1") {
            $ac = "checked='checked'";
          } else {
            $pe = "checked='checked'";
          }
          ?>
                    <p id="p0">
                      <label class="l-input-small">Status</label>
                      <span class="fieldB">
                        <input type="radio" id="sta_0" name="status" value="0" <?= $na ?>/> <span class="sradio">Tidak Aktif</span>
                        <input type="radio" id="sta_2" name="status" value="2" <?= $pe ?>/> <span class="sradio">Pending</span>
                        <input type="radio" id="sta_1" name="status" value="1" <?= $ac ?>/> <span class="sradio">Aktif</span>
                      </span>
                    </p>-->
        </div>
        <center>
          <p class="stdformbutton">
            <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
            <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
          </p>
        </center>
      </form>
    </div>
  </div>

  <script type="text/javascript">
    jQuery(document).ready(function () {
      jQuery("#myForm").validate().settings.ignore = [];

      jQuery(".hasDatePicker2").datepicker({
        dateFormat: "yy-mm-dd"
      });
      jQuery("#eduYear").mask("9999");
      jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
      jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
      jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});


      jQuery("#eduDept").chained("#eduFac");
      jQuery("#eduDept").trigger("chosen:updated");

      jQuery("#eduFac").bind("change", function () {
        jQuery("#eduDept").trigger("chosen:updated");
      });
      jQuery("#eduType").on("change", function () {
        var edVal = jQuery(this).val();
        if (edVal > 614) {
          jQuery("#s1Only").show();
          jQuery("#eduFac").rules("add", {
            required: true,
            messages: {
              required: "Field Fakultas harus diisi.."
            }
          });
          jQuery("#eduDept").rules("add", {
            required: true,
            messages: {
              required: "Field Jurusan harus diisi.."
            }
          });
        } else {
          jQuery("#eduFac").rules("remove");
          jQuery("#eduDept").rules("remove");
          jQuery("#s1Only").hide();
        }
      });
      jQuery("#eduType").trigger("change");
    });
  </script>


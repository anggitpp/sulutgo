<?php
session_start();
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
//var_dump($menuAccess[$s]);
if (!isset($menuAccess[$s]["edit"])) {
//  echo'A';
  //header("Location: $loc");
  echo "<script>window.parent.location='$loc';</script>";
}

$empc = new EmpTup();
$mData = new MstData();
if (isset($_POST["btnSimpan"])) {
  $empc = $empc->processForm();
  $_SESSION["entity_id"] = "";
  $_SESSION["parent_id"] = "";

//  echo $_SESSION["pg_org"] . "<br>";
  echo "<script>window.parent.location='$loc';</script>";
  die();
}

$level = $_GET["lv"];
$empc->parentId = $_GET["id"];
$empc = $empc->getSingleRowByParentId();
$_SESSION["entity_id"] = $empc->id;
$_SESSION["parent_id"] = $empc->parentId;

$cutil = new Common();
$ui = new UIHelper();
$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
$oType = $arrParameter[38];
$eType = $arrParameter[38];
$kodeInduk0;
$kodeInduk1;
$kodeInduk2;
$mData->kodeData = $empc->parentId;
$mData->getById();
switch ($level) {
  case 2:
    $oType = $arrParameter[39];
    $eType = $arrParameter[38];
    $kodeInduk = $kodeInduk0 = (isset($_GET["pid"]) ? $_GET["pid"] : $mData->kodeInduk);
    $cat = "X05";
    break;
  case 3:
    $oType = $arrParameter[39];
    $eType = $arrParameter[38];
    $kodeInduk = $kodeInduk1 = (isset($_GET["pid"]) ? $_GET["pid"] : $mData->kodeInduk);
    $kodeInduk0 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk1'", "id");
    $cat = "X06";
    break;
  case 4:
    $oType = $arrParameter[41];
    $eType = $arrParameter[40];
    $kodeInduk = $kodeInduk2 = (isset($_GET["pid"]) ? $_GET["pid"] : $mData->kodeInduk);
    $kodeInduk1 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk2'", "id");
    $kodeInduk0 = $cutil->getDescription("SELECT t1.kodeInduk id FROM mst_data t1 WHERE t1.kodeData='$kodeInduk1'", "id");
    $cat = "X07";
    break;
}
$divisionName = $cutil->getMstDataDesc($kodeInduk0);
$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "code" => array("rule" => "required", "msg" => "Field Kode harus diisi.."),
    "positionName" => array("rule" => "required", "msg" => "Field Jabatan harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<style>
  .tinymce {
    height: 300px;
  }
</style>
<script type="text/javascript" src="<?= APP_URL ?>/scripts/tinymce/jquery.tinymce.js"></script>
<div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
  <?= getBread(ucwords($mode . " tupoksi " . $oType)) ?>
  <span class="pagedesc">&nbsp;</span>
</div>
<?php // include './tmpl/__emp_header__.php';    ?>
<div id="contentwrapper" class="contentwrapper">
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
    <div class="widgetbox">
      <div class="contenttitle2"><h3>JOBDESK JABATAN - PADA <?php echo $oType . " " . $mData->namaData ?> </h3></div>

      <p id="p0"><?= $ui->createLabelSpanInputAttr("Kode", $empc->code, "code", "code", "v20", "") ?></p>
      <?php if (!empty($empc->positionName)) { ?>
        <p id="p0"><?= $ui->createPLabelSpanDisplay("Jabatan", $empc->positionName); ?></p>
      <?php } else { ?>
        <p id="p0"><?= $ui->createLabelSpanInputAttr("Jabatan", $empc->positionName, "positionName", "positionName", "mediuminput", "") ?></p>
      <?php } ?>
      <p id="p0">
        <label class="l-input-small">Atasan Langsung</label>
        <span class="fieldB">
          <?php
          $sql = "select id, name description from emp  WHERE status=535  order by name";
          echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "leaderId", $empc->leaderId, "", " $disabled class='single-deselect-td'");
          ?>
        </span>
      </p>
      <p id="p0"><?= $ui->createPLabelSpanDisplay("Departemen", $divisionName); ?></p>
      <p>
        <label class="l-input-small">Dokumen</label>
        <span class="fieldB">
          <?php
          if ($mode == "edit" && !empty($empc->filename))
            echo "<a href=\"download.php?d=emptup&f=$empc->id\"><img src=\"" . getIcon($empc->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
          ?>
          <input id="relFilename" type="file" name="filename" style="" />
        </span>
      </p><br>

      <ul class="hornav">
        <li class="current"><a href="#tab_desc">Tugas</a></li>
        <li><a href="#tab_target">Kerja</a></li>
        <li><a href="#tab_result">Hasil</a></li>
        <li><a href="#tab_resource">Bahan</a></li>
      </ul>
      <div id="tab_desc" class="subcontent" style="margin-top: 0">
        <p>
          <textarea name="description" id="description" rows="7" cols="50" class="mediuminput tinymce"><?php echo $empc->description; ?></textarea>
        </p>
        <div>
          <div class="contenttitle2"><h4>TANGGUNG JAWAB</h4>
          </div>
          <h5>Supervisor</h5>
          <textarea name="spvResp" id="spvResp" rows="5" class="mediuminput tinymce"><?php echo $empc->spvResp; ?></textarea>
          <br/>
          <h5>Wewenang</h5>
          <textarea name="spvRole" id="spvRole" rows="5" class="mediuminput tinymce"><?php echo $empc->spvRole; ?></textarea>
          <p>
          </p>
        </div>
        <div>
          <div class="contenttitle2"><h4>Syarat</h4>
          </div>
          <p id="p0">
            <label class="l-input-small">Pendidikan</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description from mst_data where kodeCategory='E01'  order by urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "eduId", $empc->eduId, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>      
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Pengalaman", $empc->experience, "experience", "experience", "mediuminput", "") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Jabatan", $empc->skill, "skill", "skill", "mediuminput", "") ?></p>
        </div>
      </div>
      <div id="tab_target" class="subcontent" style="margin-top: 0;display: none">
        <p>
          <textarea name="target" id="target" rows="7" cols="50" class="mediuminput tinymce"><?php echo $empc->target; ?></textarea>
        </p>
      </div>
      <div id="tab_result" class="subcontent" style="margin-top: 0;display: none">
        <p>
          <textarea name="result" id="result" rows="7" cols="50" class="mediuminput tinymce"><?php echo $empc->result; ?></textarea>
        </p>
      </div>
      <div id="tab_resource" class="subcontent" style="margin-top: 0;display: none">
        <p>
          <textarea name="resource" id="resource" rows="7" cols="50" class="mediuminput tinymce"><?php echo $empc->resource; ?></textarea>
        </p>
      </div>
      <br class="clearall"/>
      <center>
        <p class="stdformbutton">
          <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
          <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="location.href = '<?php echo preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))) ?>';"/>
        </p>
      </center>
    </div>

  </form>
</div>

<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery('textarea.tinymce').tinymce({
      script_url: baseUrl + '/scripts/tinymce/tiny_mce.js',
      theme: "advanced",
      skin: "themepixels",
      width: "100%",
      plugins: "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
      inlinepopups_skin: "themepixels",
      theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,outdent,indent,blockquote,formatselect,fontselect,fontsizeselect",
      theme_advanced_buttons2: "pastetext,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,image,help,code,|,preview,|,forecolor,backcolor,removeformat,|,charmap,media,|,fullscreen",
      theme_advanced_buttons3: "table,tablecontrols",
      theme_advanced_toolbar_location: "top",
      theme_advanced_toolbar_align: "left",
      theme_advanced_statusbar_location: "bottom",
      theme_advanced_resizing: true,
      content_css: baseUrl + "/scripts/tinymce/css/tinymce.css",
      template_external_list_url: "lists/template_list.js",
      external_link_list_url: "lists/link_list.js",
      external_image_list_url: "lists/image_list.js",
      media_external_list_url: "lists/media_list.js",
      table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
      table_cell_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
      table_row_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
      table_cell_limit: 100,
      table_row_limit: 5,
      table_col_limit: 5,
      setup: function (ed) {
        ed.onKeyDown.add(function (ed, evt) {
          if (evt.keyCode === 9) {
            ed.execCommand('mceInsertRawHTML', false, '\x09');
            evt.preventDefault();
            evt.stopPropagation();
            return false;
          }
        });
      }
    });
    jQuery("#myForm").validate().settings.ignore = [];
    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

  });
</script>


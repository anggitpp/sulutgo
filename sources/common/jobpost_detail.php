<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
  header("Location: " . preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
}

$rv = new RecVacancy();
$rp = new RecPlan();
$rpo = new RecPost();
if (isset($_POST["btnSimpan"])) {
  $rpo = $rpo->processForm();
//  $loc.="&id=$rv->planId&mode=edit";
  header("Location: " . $loc);
  die();
}
$rpo->id = $_GET["id"];
$rpo = $rpo->getById();
$rv->id = $rpo->vacId;
$rv = $rv->getById();
$rp->id = $rpo->planId;
$rp = $rp->getById();
$_SESSION["entity_id"] = $rpo->id;
//echo "<br>PLAN: ";
//var_dump($rp);
//echo "<br>VAC: ";
//var_dump($rv);
//echo "<br>POST: ";
//var_dump($rpo);

$__validate["formid"] = "myForm";
$__validate["items"] = array(
    "selStartDate" => array("rule" => "required", "msg" => "Field Tanggal Mulai harus diisi.."),
    "selEndDate" => array("rule" => "required", "msg" => "Field Tanggal Selesai harus diisi.."),
    "annStartDate" => array("rule" => "required", "msg" => "Field Tanggal Mulai harus diisi.."),
    "annEndDate" => array("rule" => "required", "msg" => "Field Tanggal Selesai harus diisi.."),
);
require_once HOME_DIR . "/tmpl/__header__.php";

$cutil = new Common();
$ui = new UIHelper();
?>

<script type="text/javascript" src="<?= APP_URL ?>/scripts/tinymce/jquery.tinymce.js"></script>
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
    padding: 5px;
  }
  fieldset label{
    margin-left: 10px;
  }
</style>

<div class="centercontent contentpopup">

  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
    <span class="pagedesc">&nbsp;</span>
  </div>
  <div class="contentwrapper">
    <form class="stdform">
     
      <fieldset>
        <legend>LOWONGAN</legend>
        <table style="width: 100%">
          <tr>
            <td colspan="2">
              <p>
                <label class="l-input-small" style="width:20%">Judul</label>
                <span class="field" style="margin-left: 25%;"><?= $rp->subject ?>&nbsp;</span>
              </p>
            </td>
          </tr>
          <tr>
            <td style="width: 50%">
              <p>
                <label class="l-input-small2">Tgl. Pengajuan</label>
                <span class="field" style="margin-left: 50%;"><?= getTgl($rp->proposeDate) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Tgl. Kebutuhan</label>
                <span class="field" style="margin-left: 50%;"><?= getTgl($rp->needDate) ?>&nbsp;</span>
              </p>
            </td>
            <td style="width: 50%">

              <p>
                <label class="l-input-small2">Utk. Jabatan</label>
                <span class="field" style="margin-left: 50%;"><?= $rp->posAvailable ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Divisi</label>
                <span class="field" style="margin-left: 50%;"><?= $cutil->getDescription("SELECT namaData description FROM mst_data WHERE kodeData='$rp->divId'", "description") ?>&nbsp;</span>
              </p>

            </td>
          </tr>
        </table>
      </fieldset>

      <ul class="hornav">
        <li class="current"><a href="#tab_post">Posting</a></li>
        <li><a href="#tab_rem">Catatan</a></li>
      </ul>
      <div id="tab_post" class="subcontent">
        <table style="width: 100%" class="stdtable stdtablequick">
          <thead>
            <tr>
              <th style="width: 80px;">Tipe</th>
              <th style="width: 20px;">As Icon</th>
              <th>Posting</th>
              <th style="width: 100px;">File</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <?php
          $sql = "
        SELECT
        t1.id id,
        t1.parent_id parentId,
        t1.post_type_id postTypeId,
        t1.post_sort postSort,
        t1.post_filename postFilename,
        t1.post_type_field postTypeField,
        DATE_FORMAT(t1.post_value,'%d-%m-%Y') postValue,
        t1.remark remark,
        t2.namaData,
        t1.cre_by creBy,
        t1.cre_date creDate,
        t1.upd_by updBy,
        t1.upd_date updDate
        FROM	rec_post_detail t1
        JOIN mst_data t2 ON t1.post_type_id=t2.kodeData
        WHERE	t1.parent_id=$rpo->id
        ORDER BY t1.post_sort ASC
        ";
          $rpod = $cutil->executeSQL($sql);
          $arrDataType = array(1 => "Tanggal", 2 => "Text");
          $c = 0;
          foreach ($rpod as $r) {
            echo "<tr>";
            echo "<td>" . $r[namaData] . "</td>";
            echo "<td style='text-align:center;'>" . "<input type=\"checkbox\" disabled=\"disabled\" name=\"postTypeField[]\" " . ($r["postTypeField"] == 0 ? "checked=checked" : "") . "  >" . "</td>";
//          echo "<td>" . $cutil->generateSelectArrayNotNull2($arrDataType, "postTypeField[]", $r["postTypeField"]) . "</td>";
            echo "<td>" . $r["postValue"] . "</td>";
            echo "<td>" . ($r["postFilename"] != "" ? "<a href=\"download.php?d=posting&f=" . $r["id"] . "\"><img src=\"" . getIcon($r["postFilename"]) . "\" align=\"center\" style=\"vertical-align:middle\" >" . $r["postFilename"] . "</a>" : "") . "</td>";
            echo "<td>" . $r["remark"] . "</td>";
            echo "</tr>";
            $c++;
          }
          ?>
        </table>

      </div>
      <div id="tab_rem" class="subcontent" style="display: none;margin-top: 0px;">
        <textarea name="remark" id="remark" rows="10" cols="50" class="mediuminput tinymce" style="height: 400px;width:905px"><?= $rpo->remark ?></textarea>
      </div>
       <?php
if(empty($rpo->updBy)){
  $updBy = $rpo->creBy;
  $r[input_date] = $rpo->creDate;
}else{
  $updBy = $rpo->updBy;
  $r[input_date] = $rpo->updDate;
}
$nama = getField("select namaUser from app_user where username = '$updBy'");
if(!empty($rpo->creBy)){
        list($tanggalCreate, $waktuCreate) = explode(" ", $r[input_date]); 
        $waktu = getTanggal($tanggalCreate, "t")." @ ".substr($waktuCreate,0,5);
?>
   

            <fieldset>
              <legend>Last Update</legend>
             <p id="p0"><?= $ui->createLabelSpanInputAttr("Oleh", $nama, "lastupdate", "lastupdate", "mediuminput", "maxlength=50","readonly") ?></p>
              <p id="p0"><?= $ui->createLabelSpanInputAttr("Waktu", $waktu, "waktuupdate", "waktuupdate", "mediuminput", "maxlength=50","readonly") ?></p>
             
            </fieldset> 
            <?php     } ?>

      
    </form>
  </div>
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
  });
</script>
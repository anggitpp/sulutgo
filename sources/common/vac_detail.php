<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
session_start();
if (!isset($menuAccess[$s]["view"])) {
  header("Location: " . preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
}
$rv = new RecVacancy();
$rp = new RecPlan();
$emp = new Emp();
$planId = $_GET["id"];
$rv->planId = $planId;
$rv = $rv->getByParentId();
$_SESSION["entity_id"] = $rv->id;
$_SESSION["plan_id"] = $planId;
$rp->id = $rv->planId;
$rp = $rp->getById();
$emp->id = $rp->empId;
$remp = $emp->getByIdHeader();
foreach ($remp as $r) {
  $remp = $r;
}

$cutil = new Common();
$ui = new UIHelper();
?>

<script type="text/javascript" src="plugins/tinymce/jquery.tinymce.js"></script>
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
    <span class="pagedesc">&nbsp;</span>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
      <div class="widgetbox">
      <div class="title" style="margin-bottom:0px;"><h3><?= $rp->cat === 0 ? "RENCANA" : "KEBUTUHAN" ?></h3></div>
    </div>
		
        <table style="width: 100%">
          <tr>
            <td  style="width: 50%; vertical-align:top;">
              <p id="p0">
                <label class="l-input-small">Nomor </label>
                <span class="field" style="margin-left:36%"><?= $rp->no ?>&nbsp;</span>
              </p>
			  <p id="p0">
                <label class="l-input-small">Divisi</label>
                <span class="field" style="margin-left:36%"><?= $remp["divisi"] ?>&nbsp;</span>
              </p>
            </td>
            <td  style="width: 50%; vertical-align:top;">
              <p id="p0">
                <label class="l-input-small">NPP - Nama </label>
                <span class="field" style="margin-left:37%"><?= $remp["regNo"] . " " . $remp["name"] ?>&nbsp;</span>
              </p>
			  <p id="p0">
                <label class="l-input-small">Jabatan</label>
                <span id="reqPos" class="field" style="margin-left:37%"><?= $remp["jabatan"] ?>&nbsp;</span>
              </p>
            </td>
          </tr>         
        </table>  		
        <table style="width: 100%">
          <tr>
            <td style="width: 50%; vertical-align:top;">
			  <p>
				  <label class="l-input-small">Judul</label>
          <span class="field" style="margin-left: 37%"><?= $rp->subject ?>&nbsp;</span>
			  </p>
			  <p>
              <label class="l-input-small">Tgl. Pengajuan</label><span class="field" style="margin-left:37%"><?= getTgl($rp->proposeDate) ?>&nbsp;</span>
			  </p>
              <p>
			  <label class="l-input-small">Tgl. Kebutuhan</label><span class="field" style="margin-left:37%"><?= getTgl($rp->needDate) ?>&nbsp;</span>
			  </p>
            </td>
            <td  style="width: 50%">
			  <p id="p0"><span class="field" style="margin-left:0px;">&nbsp;<span></p>
              <p id="p0">
				<label class="l-input-small">Utk. Jabatan</label><span class="field" style="margin-left:37%"><?= getField("select pos_available from rec_plan where id ='".$rp->posAvailable."'") ?>&nbsp;</span>
			  </p>
              <p id="p0">
                <label class="l-input-small">Divisi&nbsp;&nbsp;</label>
                <span class="field" style="margin-left:37%">
                  <?php
                  $sql = " SELECT namaData description FROM mst_data WHERE kodeData='$rp->divId'";
                  echo $cutil->getDescription($sql, "description");
                  ?>
                </span>
              </p>
            </td>
          </tr>
        </table>
      <ul class="hornav">
        <li class="current"><a href="#tab_plan">Rencana</a></li>
        <li><a href="#tab_dtl">Detail</a></li>
      </ul>
      <div id="tab_plan" class="subcontent" style="margin-top: 0px;">
        <table style="width: 100%">
          <tr>
            <td  style="width: 50%">
              <p id="p0">
                <label class="l-input-small">Nomor </label>
                <span class="field" style="margin-left:37%"><?= $rv->no ?></span>
              </p>
			  <p>
                <label class="l-input-small">File Lowongan</label>
                <span class="field" style="margin-left:37%">
                  <?php
                  if ($rv->fileLowongan != "")
                    echo "<a href=\"download.php?d=vacancy&f=$rv->id\"  target=\"_blank\"><img src=\"" . getIcon($rv->fileLowongan) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
                  echo $rv->fileLowongan;
                  ?>
                &nbsp;</span>
              </p>
			  <p>
                <label class="l-input-small">Posting</label>
                <span class="field" style="margin-left:37%"><?= getTgl($rv->postStartDate) ?>&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;<?= getTgl($rv->postEndDate) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small">Seleksi</label>
                <span class="field" style="margin-left:37%"><?= getTgl($rv->selStartDate) ?>&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;<?= getTgl($rv->selEndDate) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small">Pengumuman</label>
                <span class="field" style="margin-left:37%"><?= getTgl($rv->annStartDate) ?>&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;<?= getTgl($rv->annEndDate) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small">Verifikasi Dok.</label>
                <span class="field" style="margin-left:37%"><?= getTgl($rv->verStartDate) ?>&nbsp;&nbsp;<b>s.d</b>&nbsp;&nbsp;<?= getTgl($rv->verEndDate) ?>&nbsp;</span>
              </p>
            </td>
            <td  style="width: 50%">
              <p id="p0">
                <label class="l-input-small">Kandidat</label>
                <span class="field" style="margin-left:37%"><?= ($rv->candidate == 1 ? "Eksternal" : "Internal") ?>&nbsp;</span>
              </p>
			  <p id="p0">
                <label class="l-input-small">Status</label>
                <?php
                switch ($rv->status) {
                  case 1:
                    $status = "Seleksi";
                    break;
                  case 2:
                    $status = "Batal";
                    break;
                  case 3:
                    $status = "Selesai";
                    break;
                  default:
                    $status = "Rencana";
                    break;
                }
                ?>
                <span class="field" style="margin-left:37%"><?= $status ?>&nbsp;</span>
              </p>   
			  <p id="p0">
                <label class="l-input-small">Approval</label>
                <span class="field" style="margin-left:37%">
                  <?php
                  switch ($rv->appr1Sta) {
                    case 1:
                      echo "<img src='styles/images/f.png' title='Ditolak'>&nbsp;&nbsp;&nbsp;&nbsp;<b>Ditolak</b>";
                      break;
                    case 2:
                      echo "<img src='styles/images/p.png' title='Pending'>&nbsp;&nbsp;&nbsp;&nbsp;<b>Pending</b>";
                      break;
                    case 3:
                      echo "<img src='styles/images/t.png' title='Disetujui'>&nbsp;&nbsp;&nbsp;&nbsp;<b>Disetujui</b>";
                      break;
                    default:
                      echo "Belum ada approval";
                      break;
                  }
                  ?>
                </span>
              </p>   
			  <p>
			  <label class="l-input-small">Catatan</label>
			  <span class="field" style="margin-left:37%"><?= nl2br($rv->remark); ?>&nbsp;</span>
			</p>
            </td>
          </tr>
        </table>
        <?php
if(empty($rv->updBy)){
  $updBy = $rv->creBy;
  $r[input_date] = $rv->creDate;
}else{
  $updBy = $rv->updBy;
  $r[input_date] = $rv->updDate;
}
$nama = getField("select namaUser from app_user where username = '$updBy'");
if(!empty($rv->creBy)){
        list($tanggalCreate, $waktuCreate) = explode(" ", $r[input_date]); 
        $waktu = getTanggal($tanggalCreate, "t")." @ ".substr($waktuCreate,0,5);
?>
   

            <fieldset>
              <legend>Last Update</legend>
             <p id="p0"><?= $ui->createLabelSpanInputAttr("Oleh", $nama, "lastupdate", "lastupdate", "mediuminput", "maxlength=50","readonly") ?></p>
              <p id="p0"><?= $ui->createLabelSpanInputAttr("Waktu", $waktu, "waktuupdate", "waktuupdate", "mediuminput", "maxlength=50","readonly") ?></p>
             
            </fieldset> 
            <?php     } ?>
      </div>
      <div id="tab_dtl" class="subcontent" style="display: none;margin-top: 0px;">
        <?= $rv->detailInfo ?>
      </div>


      
    </form>
  </div>
</div>
<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery("#myForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });

    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-30').chosen({allow_single_deselect: true, width: '30%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});


    jQuery('textarea.tinymce').tinymce({
      script_url: '<?= APP_URL ?>' + '/plugins/tinymce/tiny_mce.js',
      theme: "advanced",
      skin: "themepixels",
      readonly: 1,
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
      content_css: '<?= APP_URL ?>' + "/plugins/tinymce/tinymce.css",
      template_external_list_url: "lists/template_list.js",
      external_link_list_url: "lists/link_list.js",
      external_image_list_url: "lists/image_list.js",
      media_external_list_url: "lists/media_list.js",
      table_styles: "Header 1=header1;Header 2=header2;Header 3=header3",
      table_cell_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
      table_row_styles: "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
      table_cell_limit: 100,
      table_row_limit: 10,
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
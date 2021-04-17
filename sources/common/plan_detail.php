<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
session_start();
if (!isset($menuAccess[$s]["view"])) {
//  header("Location: " . preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
  die();
}
$e = new RecPlan();
global $db,$planType;

$e->id = $_GET["id"];
$e = $e->getById();
$_SESSION["entity_id"] = $e->id;
$emp = new Emp();
$emp->id = $e->empId;
$re = $emp->getByIdHeader();
foreach ($re as $rr)
  $re = $rr;
//var_dump($re);
$__validate["formid"] = "myForm";
$__validate["items"] = array(
  "empId" => array("rule" => "required", "msg" => "Field NPP Pemohon harus diisi.."),
  "subject" => array("rule" => "required", "msg" => "Field Judul harus diisi.."),
  "proposeDate" => array("rule" => "required", "msg" => "Field Tanggal Pengajuan harus diisi.."),
  "needDate" => array("rule" => "required", "msg" => "Field Tanggal Kebutuhan harus diisi.."),
  "eduId" => array("rule" => "required", "msg" => "Field Pendidikan harus diisi.."),
  "empSta" => array("rule" => "required", "msg" => "Field Status Pegawai harus diisi.."),
  );
require_once HOME_DIR . "/tmpl/__header__.php";

$cutil = new Common();
$ui = new UIHelper();
?>
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
    /*border: 1px solid #03F;*/
    padding: 5px;
  }
  fieldset label{
    margin-left: 10px;
  }
</style>
<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">Detail Rencana</h1>
    <span class="pagedesc">&nbsp;</span>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
      <div style="top:50px; right:35px; position:absolute">
        <p class="stdformbutton">
        </p>
      </div>
      <?php
      if($planType!=3){
        ?>
        <fieldset>
          <legend>PEMOHON</legend>
          <table style="width: 100%">
            <tr>
              <td  style="width: 50%">
                <p id="p0">
                  <label class="l-input-small">Nomor </label>
                  <span class="field" style="margin-left:39%"><?= $e->no ?>&nbsp;</span>
                </p>
              </td>
              <td  style="width: 50%">
                <p id="p0">
                  <label class="l-input-small">NPP - Nama </label>
                  <span class="field" style="margin-left:39%"><?= $re["regNo"] . " - " . $re["name"] ?>&nbsp;</span></p>
                </td>
              </tr>
              <tr>  
                <td  style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Divisi</label>
                    <span id="reqDiv" class="field" style="margin-left:39%"><?= $re["divisi"] ?>&nbsp;</span>
                  </p>
                </td>

                <td  style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Jabatan</label>
                    <span id="reqPos" class="field" style="margin-left:39%"><?= $re["jabatan"] ?>&nbsp;</span>
                  </p>
                </td>
              </tr>
            </table>
          </fieldset>
          <fieldset>
            <p>
              <label style="width: 15%;text-align: right;">Judul</label>
              <span class="field" style="margin-left:20%"><?= $e->subject ?>&nbsp;</span>
            </p>
            <table style="width: 100%">
              <tr>
                <td  style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Tgl. Pengajuan</label>
                    <span id="reqPos" class="field" style="margin-left:39%"><?= $e->proposeDate ?>&nbsp;</span>
                  </p>
                  <p id="p0">
                    <label class="l-input-small">Tgl. Kebutuhan</label>
                    <span id="reqPos" class="field" style="margin-left:39%"><?= $e->needDate ?>&nbsp;</span>
                  </p>
                </td>
                <td  style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Utk. Jabatan</label>
                    <span id="reqPos" class="field" style="margin-left:39%"><?= getField("select pos_available from rec_plan where id ='".$e->posAvailable."'") ?>&nbsp;</span>
                  </p>
                  <p id="p0">
                    <label class="l-input-small">Divisi&nbsp;&nbsp;</label>
                    <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->divId) ?></span>
                  </p>
                </td>
              </tr>
            </table>
          </fieldset>

          <fieldset>
            <legend>PERSYARATAN</legend>
            <table style="width: 100%">
              <tr>
                <td  style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Pendidikan</label>
                    <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduId) ?></span>
                  </p>
                </td>
                <td  style="width: 50%">

                  <p id="p0">
                    <label class="l-input-small">Status Pegawai</label>
                    <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->empSta) ?></span>
                  </p>
                </td>
              </tr>
            </table>
            <table style="width: 100%; display: none;margin-left: 10px" id="s1Only">
              <tr>  
                <td style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Fakultas&nbsp;&nbsp;<span class="required">*)</span></label>
                    <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduFacId) ?></span>
                  </p>
                </td>
                <td style="width: 50%">
                  <p id="p0">
                    <label class="l-input-small">Jurusan&nbsp;&nbsp;<span class="required">*)</span></label>
                    <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduDeptId) ?></span>
                  </p>
                </td>
              </tr>
            </table>
            <table style="width: 100%">
              <tr>
                <td  style="width: 50%">
                  <p id="p0">
                    <label style="width: 30%;text-align: right;">Jenis Kelamin</label>
                    <span class="field" style="margin-left:39%">
                      <input type="checkbox" disabled="disabled" id="male" name="male" value="<?= $e->male ?>" <?= $e->male != "" ? "checked=checked" : "" ?> ><span for="male">&nbsp;&nbsp;&nbsp;Laki-Laki</span>&nbsp;&nbsp;&nbsp;
                      <input type="checkbox" disabled="disabled" id="female" name="female" value="<?= $e->female ?>" <?= $e->female != "" ? "checked=checked" : "" ?> ><span for="male">&nbsp;&nbsp;&nbsp;Perempuan</span>
                    </span>
                  </p>
                </td>
                <td style="width: 50%;">
                  <label style="width: 83%;text-align: right;">Yang bersangkutan bertanggung jawab kpd</label>
                </td>
              </tr>
              <tr>
                <td  style="width: 50%;vertical-align: top;">
                  <p id="p0"><?= $ui->createLabelSpanInputAttr("Jumlah", $e->personNeeded, "personNeeded", "personNeeded", "v30", "", "l-input-small") ?></p>
                  <p id="p0">
                    <label class="l-input-small">Usia</label>
                    <span class="field" style="margin-left:39%"><?= $e->ageFrom . " s/d " . $e->ageTo ?> &nbsp;</span>
                  </p>
                </td>
                <td style="width: 50%;">
                  &nbsp;&nbsp;&nbsp;<textarea style="width:85%; margin-top: -10px;" id="reportTo" name="reportTo" rows="3" readonly><?= $e->reportTo ?></textarea>
                </td>         
              </tr>
            </table>
          </fieldset>
          <?php }else{
            ?>





            <fieldset>
              <legend>PERSYARATAN</legend>
              <table style="width: 100%">
                <tr>
                  <td  style="width: 50%">
                    <p>
                       <label class="l-input-small">Judul</label>
                      <span class="field" style="margin-left:20%"><?= $e->subject ?>&nbsp;</span>
                    </p>
                  </td>
                  <td style="width: 50%">
                    <p id="p0">
                      <label class="l-input-small">Utk. Jabatan</label>
                      <span id="reqPos" class="field" style="margin-left:39%"><?= $e->posAvailable ?>&nbsp;</span>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="width: 100%">
                <tr>
                  <td  style="width: 50%">
                    <p id="p0">
                      <label class="l-input-small">Pendidikan</label>
                      <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduId) ?> s/d <?= $cutil->getMstDataDesc($e->eduId2) ?></span>
                    </p>
                  </td>
                  <td  style="width: 50%">

                    <p id="p0">
                      <label class="l-input-small">Status Pegawai</label>
                      <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->empSta) ?></span>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="width: 100%; display: none;margin-left: 10px" id="s1Only">
                <tr>  
                  <td style="width: 50%">
                    <p id="p0">
                      <label class="l-input-small">Fakultas&nbsp;&nbsp;<span class="required">*)</span></label>
                      <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduFacId) ?></span>
                    </p>
                  </td>
                  <td style="width: 50%">
                    <p id="p0">
                      <label class="l-input-small">Jurusan&nbsp;&nbsp;<span class="required">*)</span></label>
                      <span class="field" style="margin-left:39%"><?= $cutil->getMstDataDesc($e->eduDeptId) ?></span>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="width: 100%">
                <tr>
                  <td  style="width: 50%">
                    <p id="p0">
                      <label style="width: 30%;text-align: right;">Jenis Kelamin</label>
                      <span class="field" style="margin-left:39%">
                        <input type="checkbox" disabled="disabled" id="male" name="male" value="<?= $e->male ?>" <?= $e->male != "" ? "checked=checked" : "" ?> ><span for="male">&nbsp;&nbsp;&nbsp;Laki-Laki</span>&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" disabled="disabled" id="female" name="female" value="<?= $e->female ?>" <?= $e->female != "" ? "checked=checked" : "" ?> ><span for="male">&nbsp;&nbsp;&nbsp;Perempuan</span>
                      </span>
                    </p>
                  </td>
                  <td style="width: 50%;">
                    <label style="width: 83%;text-align: right;">Yang bersangkutan bertanggung jawab kpd</label>
                  </td>
                </tr>
                <tr>
                  <td  style="width: 50%;vertical-align: top;">
                    <p id="p0"><?= $ui->createLabelSpanInputAttr("Jumlah", $e->personNeeded, "personNeeded", "personNeeded", "v30", "", "l-input-small") ?></p>
                    <p id="p0">
                      <label class="l-input-small">Usia</label>
                      <span class="field" style="margin-left:39%"><?= $e->ageFrom . " s/d " . $e->ageTo ?> &nbsp;</span>
                    </p>
                  </td>
                  <td style="width: 50%;">
                    &nbsp;&nbsp;&nbsp;<textarea style="width:85%; margin-top: -10px;" id="reportTo" name="reportTo" rows="3" readonly><?= $e->reportTo ?></textarea>
                  </td>         
                </tr>
              </table>
            </fieldset>
            <?php }
            ?>
            <fieldset>
              <legend>INFO TAMBAHAN</legend>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <label style="width: 80%;text-align: right;">Karakteristik</label>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px" id="characters" name="characters" rows="3" readonly><?= $e->characters ?></textarea>
                    </p>
                  </td>
                  <td width="50%">

                    <p>
                      <label style="width: 80%;text-align: right;">Uraian Tugas Utama</label>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px" id="jobDesk" name="jobDesk" rows="3" readonly><?= $e->jobDesk ?></textarea>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <label style="width: 80%;text-align: right;">Keahlian Khusus</label>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px" id="expertise" name="expertise" rows="3" readonly><?= $e->expertise ?></textarea>
                    </p>
                  </td>
                  <td width="50%">

                    <p>
                      <label style="width: 80%;text-align: right;">Kemampuan Tambahan</label>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px" id="Kemampuan" name="Kemampuan" rows="3" readonly><?= $e->abilites ?></textarea>
                    </p>
                  </td>
                </tr>
              </table>
            </fieldset>
            <fieldset>
              <legend>PENJELASAN</legend>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <label style="width: 80%;text-align: right;">Alasan Permintaan Penambahan Pegawai</label>
                    </p>
                  </td>
                  <td width="50%">
                    <p>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px;margin-top: -3px" id="reason" name="reason" rows="3" readonly><?= $e->reason ?></textarea>
                    </p>
                  </td>
                  <td width="50%">
                    <p>
                    </p>

                  </td>
                </tr>
              </table>
            </fieldset>
            <fieldset>
              <legend>DISPOSISI</legend>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <label style="width: 80%;text-align: right;">Hal-hal yang diangap penting untuk segera dilakukan</label>
                    </p>
                  </td>
                  <td width="50%">
                    <p>
                    </p>
                  </td>
                </tr>
              </table>
              <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
                <tr>
                  <td width="50%" style="vertical-align: top;">
                    <p>
                      <textarea style="width:82%; margin-top: 7px;margin-left: 10px;margin-top: -3px" id="disposition" name="disposition" rows="3" readonly><?= $e->disposition ?></textarea>
                    </p>
                  </td>
                  <td width="50%">
                    <p>
                    </p>

                  </td>
                </tr>
              </table>
            </fieldset>
            <?php
if(empty($e->updBy)){
  $updBy = $e->creBy;
  $r[input_date] = $e->creDate;
}else{
  $updBy = $e->updBy;
  $r[input_date] = $e->updDate;
}
$nama = getField("select namaUser from app_user where username = '$updBy'");
if(!empty($e->creBy)){
        list($tanggalCreate, $waktuCreate) = explode(" ", $r[input_date]); 
        $waktu = getTanggal($tanggalCreate, "t")." @ ".substr($waktuCreate,0,5);
?>
   

            <fieldset>
              <legend>Last Update</legend>
             <p id="p0"><?= $ui->createLabelSpanInputAttr("Oleh", $nama, "lastupdate", "lastupdate", "mediuminput", "maxlength=50","readonly") ?></p>
              <p id="p0"><?= $ui->createLabelSpanInputAttr("Waktu", $waktu, "waktuupdate", "waktuupdate", "mediuminput", "maxlength=50","readonly") ?></p>
             
            </fieldset> 
            <?php     } ?>
            <div style="margin-right: 50%">
              <p class="stdformbutton">
              </p>
            </div>
          </form>
        </div>

      </div>

      <script language="javascript">
        jQuery(document).ready(function () {
          jQuery("#myForm").validate().settings.ignore = [];

          jQuery(".hasDatePicker2").datepicker({
            dateFormat: "yy-mm-dd"
          });

          jQuery("#personNeeded").mask("9?999");
          jQuery("#ageFrom").mask("99");
          jQuery("#ageTo").mask("99");
          jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
          jQuery('.single-deselect-30').chosen({allow_single_deselect: true, width: '30%', search_contains: true});
          jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

        });
      </script>
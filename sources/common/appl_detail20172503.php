<?php
session_start();
global $db,$applCat;

$cyear = $_SESSION["appl_year"];
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
$appl = new RecApplicant();
$applVer = new RecApplicantVer();

$applVer->id = $_GET["id"];
$applVer = $applVer->getById();
$_SESSION["entity2_id"] = $applVer->id;
$appl->id = $applVer->applicantId;
$appl = $appl->getById();
$_SESSION["entity_id"] = $appl->id;
$_SESSION["applicant_cat"] = $applCat;
$applChar = new RecApplicantChar();
$applChar->parentId = $appl->id;
$applChar = $applChar->getByParentId();
$_SESSION["entity3_id"] = $applChar->id;

$__validate["formid"] = "myForm";
$__validate["items"] = array(
  "name" => array("rule" => "required", "msg" => "Field Name harus diisi.."),
  "ktpNo" => array("rule" => "required", "msg" => "Field Nomor KTP harus diisi.."),
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

  #ktpPreview {
    border: #069 solid 1px;
    width: 180px;
    height: 180px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }
  #fotoPreview {
    border: #069 solid 1px;
    width: 180px;
    height: 180px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }  
</style>
<div class="centercontent contentpopup" >
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
    <span class="pagedesc">&nbsp;</span>
  </div>

  <div id="contentwrapper" class="contentwrapper">
    <form id="myForm" class="stdform" enctype="multipart/form-data">
      

      <fieldset>
        <legend>UMUM</legend>
        <table width="100%">
          <tr>
            <td  style="width: 50%">
              <p>
                <label class="l-input-small2">Tgl.Input </label>
                <span class="field" style="margin-left: 50%">&nbsp;&nbsp;<?= $appl->creDate == "" ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : $appl->creDate ?></span>
              </p>
            </td>
            <td  style="width: 50%">
              <p>
                <label class="l-input-small">File Lamaran</label>
                <span class="field" style="margin-left: 40%">
                  <?php
                  echo ($applVer->fileApplicant != "" ? "<a href=\"download.php?d=fileappl&f=" . $applVer->id . "\"><img src=\"" . getIcon($applVer->fileApplicant) . "\" align=\"center\" style=\"vertical-align:middle\" >" . $applVer->fileApplicant . "</a>" : "&nbsp;");
                  ?>
                </span>
              </p>
            </td>
          </tr>
          <tr>
            <td  style="width: 50%">
              <p>
                <label class="l-input-small2">Posisi yang dilamar </label>
                <span class="field" style="margin-left: 50%"><?= $cutil->getDescription("
                  SELECT t1.id id, CONCAT(t3.namaData,' - ',t2.pos_available) description
                  FROM rec_vacancy t1
                  JOIN rec_plan t2 ON t1.plan_id=t2.id
                  JOIN mst_data t3 ON t3.kodeData=t2.div_id 
                  WHERE t1.id=$applVer->vacId
                  ", "description") ?>&nbsp;
                </span>
              </p>
            </td>
            <td  style="width: 50%">
              <p>
                <label class="l-input-small">Rekomendasi </label>
                <span class="field" style="margin-left: 40%"><?= $applVer->recommendation ?>&nbsp;</span>
              </p>
            </td>
          </tr>
        </table>
      </fieldset>
      <fieldset>
        <legend>VERIFIKASI</legend>
        <table width="100%">
          <tr>
            <td  style="width: 80%">
              <p>
                <input type="checkbox" id="vadmStatus" name="vadmStatus" <?= ($applVer->admStatus == "1" ? "checked=checked" : "") ?> value="1" disabled="disabled"/><span>&nbsp;&nbsp;&nbsp;&nbsp;Administrasi&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="checkbox" id="vselStatus" name="vselStatus" <?= ($applVer->selStatus == "1" ? "checked=checked" : "") ?> value="1" disabled="disabled" /><span>&nbsp;&nbsp;&nbsp;&nbsp;Seleksi&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="checkbox" id="vresultStatus" name="vresultStatus" <?= ($applVer->resultStatus == "1" ? "checked=checked" : "") ?> value="1" disabled="disabled" /><span>&nbsp;&nbsp;&nbsp;&nbsp;Penetapan&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="checkbox" id="vplacementStatus" name="vplacementStatus" <?= ($applVer->placementStatus == "1" ? "checked=checked" : "") ?> value="1" disabled="disabled" /><span>&nbsp;&nbsp;&nbsp;&nbsp;Penempatan&nbsp;&nbsp;&nbsp;&nbsp;</span>
              </p><br>
            </td>
          </tr>
          <tr><td><span>* Jika Anda mencentang  Administrasi maka lolos ke tahapan seleksi</span><br></td></tr>
          <tr>
            <td  style="width: 100%">
              <p><br>
                <label class="l-input-small">Status Terakhir</label>
                <span class="fieldB">
                  <?php echo $cutil->generateRadioArrayFlex(array(0 => "Tidak Disarankan", 1 => "Disarankan",), "vlastStatus", $applVer->lastStatus, " disabled='disabled'") ?>
                </span>
              </p>
            </td>
          </tr>
        </table>
      </fieldset>

      <ul class="hornav">
        <li class="current"><a href="#tab_1">Identitas</a></li>
        <li><a href="#tab_pic">Foto</a></li>
        <li><a href="#tab_2">Pendidikan</a></li>
        <li><a href="#tab_3">Keahlian</a></li>
        <li><a href="#tab_4">Pengalaman</a></li>
      </ul>

      <div id="tab_1" class="subcontent" style="margin:0">
        <table width="100%">
          <tr>
            <td width="50%">
              <p>
                <label class="l-input-small2">Nama Lengkap</label>
                <span class="field" style="margin-left:48%">
                  <?= $appl->name ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Panggilan</label>
                <span class="field" style="margin-left:48%">
                  <?= $appl->alias ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Tempat Lahir</label>
                <span class="field" style="margin-left:48%">
                  <?php
                  $sql = "select t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 
                  JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
                  WHERE 
                  t2.kodeCategory='X02' 
                  AND t2.kodeInduk='1'
                  AND t1.kodeCategory='X03'
                  AND t1.kodeData='$appl->birthPlace'
                  order by t2.namaData, t1.namaData";
                  echo $cutil->getDescription($sql, "description");
                  ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">No. KTP</label>
                <span class="field" style="margin-left:48%">
                  <?= $appl->ktpNo ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Berlaku sampai dengan</label>
                <span class="field" style="margin-left:48%">
                  <?= $appl->ktpValid ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Jenis Kelamin</label>
                <span class="field" style="margin-left:48%"><?= ($appl->gender == "F" ? "Perempuan" : "Laki-Laki") ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Alamat KTP</label>
                <span class="fieldB" ><?= $appl->ktpAddress ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Alamat Domisili</label>
                <span class="field" style="margin-left:200px;"><?= $appl->domAddress ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Propinsi</label>
                <span class="field" style="margin-left:48%"><?= $cutil->getMstDataDesc($appl->ktpProv) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Telp. Rumah</label>
                <span class="field" style="margin-left:48%"><?= $appl->phoneNo ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Email</label>
                <span class="field" style="margin-left:48%"><?= $appl->email ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">No. NPWP</label>
                <span class="field" style="margin-left:48%"><?= $appl->npwpNo ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">No. BPJS</label>
                <span class="field" style="margin-left:48%"><?= $appl->bpjsNo ?>&nbsp;</span>
              </p>
               <p>
                <label class="l-input-small2">Tinggi Badan</label>
                <span class="field" style="margin-left:48%"><?= $appl->tinggiBadan ?> CM</span>
              </p>
              <p>
                <label class="l-input-small2">Berat Badan</label>
                <span class="field" style="margin-left:48%"><?= $appl->beratBadan ?> KG</span>
              </p>
            
            </td>
            <td width="50%">
              <p>&nbsp;</p>
              <p>
                <label class="l-input-small2">Tgl. Lahir</label>
                <span class="field" style="margin-left:48%">
                  <?= $appl->birthDate ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Agama</label>
                <span class="field" style="margin-left:48%"><?= $cutil->getMstDataDesc($appl->religion) ?>&nbsp;</span>
              </p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>&nbsp;</p>
              <p>
                <label class="l-input-small2">Kab/Kota</label>
                <span class="field" style="margin-left:48%"><?= $cutil->getMstDataDesc($appl->ktpCity) ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Nomor HP</label>
                <span class="field" style="margin-left:48%"><?= $appl->cellNo ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Marital</label>
                <span class="field" style="margin-left:48%">
                  <?php echo $cutil->getDescription("select kodeData id, concat(namaData,' - ',keteranganData) description from mst_data where kodeData='$appl->marital'", "description") ?>&nbsp;
                </span>
              </p>
              <p>
                <label class="l-input-small2">Tgl.NPWP</label>
                <span class="field" style="margin-left:48%"><?= $appl->npwpDate ?>&nbsp;</span>
              </p>
              <p>
                <label class="l-input-small2">Tgl.BPJS</label>
                <span class="field" style="margin-left:48%"><?= $appl->bpjsDate ?>&nbsp;</span>
              </p>
                <p>
                <label class="l-input-small2">Golongan Darah</label>
                <span class="field" style="margin-left:48%"><?= $appl->bloodType ?>&nbsp;&nbsp;<b>Rhesus</b>&nbsp;&nbsp;<?= $appl->bloodResus ?></span>
              </p>
            </td>
          </tr>
        </table>
         <?php
if(empty($appl->updBy)){
  $updBy = $appl->creBy;
  $r[input_date] = $appl->creDate;
}else{
  $updBy = $appl->updBy;
  $r[input_date] = $appl->updDate;
}
$nama = getField("select namaUser from app_user where username = '$updBy'");
if(!empty($appl->creBy)){
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

      <div id="tab_pic" class="subcontent" style="display: none;margin-top: 0px;">
        <table style="width: 100%">
          <tr>
            <td width="40%">
              <label class="l-input-small5">File KTP</label>
              <div id="ktpPreview"
              <?php
              if ($mode == "edit" && $appl->ktpFilename != "") {
                echo "style=\"background-image:  url('" . APP_URL . "/files/recruit/ktp/" . $appl->ktpFilename . "')\" ";
              }
              ?>></div>
              <br/>
              <!--<input id="ktpFilename" type="file" name="ktpFilename" class="img" style="padding-left: 190px;" />-->   
            </td>
            <td width="60%">
              <label class="l-input-small5">Foto</label>
              <div id="fotoPreview" 
              <?php
              if ($mode == "edit" && $appl->picFilename != "") {
                echo "style=\"background-image:  url('" . APP_URL . "/files/recruit/pic/" . $appl->picFilename . "')\" ";
              }
              ?>
              ></div>
              <br/>
              <!--<input id="picFilename" type="file" name="picFilename" class="img" style="padding-left: 170px;" />-->   
            </td>
          </tr>
        </table>
      </div>
      <div id="tab_2" class="subcontent" style="margin:0;display: none">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="edutable">
          <thead>
            <tr>
              <th>Tingkatan</th>
              <th>Nama Lembaga</th>
              <th>Jurusan</th>
              <th>Kota</th>
              <th>Tahun</th>
              <th>File</th>
              <!--<th width="80px">Control</th>-->
            </tr>
          </thead> 
          <tbody>
            <?php
            $applEdu = new RecApplicantEdu();
//          echo "APPL_ID; $appl->id <br>";
            $redu = $applEdu->getEduByParentId($appl->id);
//          var_dump($redu);
            $rno = 0;
            foreach ($redu as $r) {
              $rowTmpl.="<tr>";
              $rowTmpl.= "<td>" . $r["levelName"] . "</td>";
              $rowTmpl.= "<td>" . $r["eduName"] . "</td>";
              $rowTmpl.= "<td>" . $r["deptName"] . "</td>";
              $rowTmpl.= "<td>" . $r["cityName"] . "</td>";
              $rowTmpl.= "<td>" . $r["eduYear"] . "</td>";
              $rowTmpl.= "<td style=\"text-align: center;\">" . ($r["eduFilename"] != "" ? "<a href=\"?download.php&d=appledu&f=" . $r["eduFilename"] . "\">" . "<img src=\"" . getIcon($r["eduFilename"]) . "\" align=\"center\" style=\"vertical-align:middle\" >" . "</a>" : "") . "</td>";
//              $rowTmpl.= "<td style=\"text-align: center;\">";
//              $rowTmpl.= "<a class='detail' href=\"javascript:void(0);\" onclick=\"openBox('" . str_replace("index", "popup", $loc) . "&mode=eduview&&id" . $r["id"] . "',850,470)\" ><span>Detail</span></a>";
//              $rowTmpl.= "</td>";
              $rowTmpl.= "</tr>";
              echo $rowTmpl;
              $rno++;
            }
            ?>
          </tbody>
        </table>
      </div>
      <div id="tab_3" class="subcontent" style="margin:0;display: none">
        <table style="margin-left: 1%;margin-right: 10%;width: 100%;"> 
          <tr>
            <td width="50%">
              <p>
                <?= $ui->createLabelClassSpanTextArea("Karakter Pribadi", $applChar->characteristic, "characteristic", "characteristic",  "style='width:85%;margin-top: 5px;' rows=3", "", "l-input-textarea", "", "width: 83%; text-align: left;") ?>
              </p>
              <p>
                <?= $ui->createLabelClassSpanTextArea("Keahlian Khusus", $applChar->abilities, "abilities", "abilities", "style='width:85%;margin-top: 5px;' rows=3", "", "l-input-textarea", "", "width: 83%; text-align: left;") ?>
              </p>
            </td>
            <td width="50%">
              <p>
                <?= $ui->createLabelClassSpanTextArea("Hobi", $applChar->hobby, "hobby", "hobby", "style='width:85%;margin-top: 5px;' rows=3", "", "l-input-textarea", "", "width: 83%; text-align: left;") ?>
              </p>
              <p>
                <?= $ui->createLabelClassSpanTextArea("Organisasi Sosial", $applChar->organization, "organization", "organization", "style='width:85%;margin-top: 5px;' rows=3", "", "l-input-textarea", "", "width: 83%; text-align: left;") ?>
              </p>
            </td>
          </tr>
        </table>
      </div>

      <div id="tab_4" class="subcontent" style="margin:0;display: none">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="pworktable">
          <thead>
            <tr>
              <th>Perusahaan</th>
              <th>Jabatan</th>
              <th>Bagian</th>
              <th>Tahun</th>
              <th>Reff</th>
              <th>Status</th>
              <!--<th width="80px">Control</th>-->
            </tr>
          </thead> 
          <tbody>
            <?php
            $applPwork = new RecApplicantPwork();
            $rpwork = $applPwork->getPworkByParentId($appl->id);
            $pwno = 0;
            foreach ($rpwork as $r) {
              $pwStatus = "";
              switch ($r["status"]) {
                case 1:
                $pwStatus = "<img src=\"styles/images/t.png\" alt=\"Aktif\" style=\"vertical-align: middle\" />";
                break;
                case 2:
                $pwStatus = "<img src=\"styles/images/p.png\" alt=\"Pending\" style=\"vertical-align: middle\" />";
                break;
                default:
                $pwStatus = "<img src=\"styles/images/f.png\" alt=\"Tidak Aktif\" style=\"vertical-align: middle\" />";
                break;
              }
              $rowTmpl = "<tr>";
              $rowTmpl.= "<td>" . $r["companyName"] . "</td>";
              $rowTmpl.= "<td>" . $r["position"] . "</td>";
              $rowTmpl.= "<td>" . $r["dept"] . "</td>";
              $rowTmpl.= "<td style=\"text-align: center;\">" . $r["dtRange"] . "</td>";
              $rowTmpl.= "<td style=\"text-align: center;\">" . ($r["filename"] != "" ? "<a href=\"?download.php&d=applpw&f=" . $r["filename"] . "\">" . "<img src=\"" . getIcon($r["filename"]) . "\" align=\"center\" style=\"vertical-align:middle\" >" . "</a>" : "") . "</td>";
              $rowTmpl.= "<td style=\"text-align: center;\">" . $pwStatus . "</td>";
//              $rowTmpl.= "<td  style=\"text-align: center;\">";
//              $rowTmpl.= "<a class='detail' href=\"javascript:void(0);\" onclick=\"openBox('" . str_replace("index", "popup", $loc) . "&mode=pworkview&id=" . $r["id"] . "',850,535)\" ><span>Detail</span></a>";
//              $rowTmpl.= "</td>";
              $rowTmpl.= "</tr>";
              echo $rowTmpl;
              $pwno++;
            }
            ?>
          </tbody>
        </table>
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

    jQuery("#npwpNo").mask("99.999.999.9-999.999");
    jQuery("#name").focus(20);
    jQuery("#ktpFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#ktpPreview").css("background-image", "url(" + this.result + ")");
        };
      }
    });
    jQuery("#picFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#fotoPreview").css("background-image", "url(" + this.result + ")");
        };
      }
    });
  });
</script>

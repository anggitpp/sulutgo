<?php
$loc = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
session_start();
// if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])){
  //header("Location: " . preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
  // echo "<script>window.location='".preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))."';</script>";
// }
//  echo "<script>logout();</script>";
if(!empty($_GET['empid'])){
  $cID = $_GET['empid'];
}else{
  $cID = $_SESSION["curr_emp_id"];
}

// echo $_SESSION["empid"];

$_GET['id'] = $cID;
$_SESSION["curr_emp_id"] = $cID;
$emp = new Emp();
if (isset($_POST["btnSimpan"])) {
  $emp = $emp->processForm();
  $_SESSION["entity_id"] = $emp->id;
  $_SESSION["curr_emp_id"] = $emp->id;
  $cat = $emp->cat;
  if ($emp->status != 535) {
//  $loc.="&id=$emp->id&mode=edit";    
  } else {
    $loc.="&id=$emp->id&mode=edit";
  }
//  echo 'loc: ' . $loc;
//  header("Location: " . $loc);  
  echo "<script>
  alert('UPDATE DATA BERHASIL');
  window.location='".$loc."';
</script>";
die();
}

global $db,$empType;
$emp->id = $_GET["id"];
$emp = $emp->getById();
$_SESSION["entity_id"] = $emp->id;
$_SESSION["emp_cat"] = $empType;


$__validate["formid"] = "myForm";
$__validate["items"] = array(
  "name" => array("rule" => "required", "msg" => "Field Name harus diisi.."),
  "status" => array("rule" => "required", "msg" => "Field Status pegawai harus diisi.."),
  "regNo" => array("rule" => "required", "msg" => "Field NPP harus diisi.."),
//    "birthPlace" => array("rule" => "required", "msg" => "Field Tempat Lahir harus diisi.."),
//    "birthDate" => array("rule" => "required", "msg" => "Field Tanggal Lahir harus diisi.."),
  "joinDate" => array("rule" => "required", "msg" => "Field Tanggal Masuk harus diisi.."),
  "ktpNo" => array("rule" => "required", "msg" => "Field Nomor KTP harus diisi.."),
//    "ktpValid" => array("rule" => "required", "msg" => "Field Tanggal berlaku KTP harus diisi.."),
//    "ktpAddress" => array("rule" => "required", "msg" => "Field Alamat KTP harus diisi.."),
//    "ktpProv" => array("rule" => "required", "msg" => "Field Propinsi harus diisi.."),
//    "ktpCity" => array("rule" => "required", "msg" => "Field Kab/Kota harus diisi.."),
//    "domAddress" => array("rule" => "required", "msg" => "Field Alamat Domisili harus diisi.."),
//    "domProv" => array("rule" => "required", "msg" => "Field Propinsi harus diisi.."),
//    "domCity" => array("rule" => "required", "msg" => "Field Kab/Kota harus diisi.."),
//    "email" => array("rule" => "required", "msg" => "Field Email harus diisi.."),
//    "email" => array("rule" => "email", "msg" => "Silakan masukkan email yang valid.."),
  );
require_once HOME_DIR . "/tmpl/__header__.php";

$cutil = new Common();
$ui = new UIHelper();
?>

<style>
  #p0 {
    margin: 5px 0;
  }

  #ktpPreview {
    border: #069 solid 1px;
    padding-left: 160px;
    width: 180px;
    height: 180px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }

  #kkPreview {
    border: #069 solid 1px;
    padding-left: 160px;
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
  fieldset {
    border: 2px solid #0A246A;
    border-radius: 8px;
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


<div class="pageheader">
  <h1 class="pagetitle"><?php if($c!=27){
    echo $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " ;
    }
    echo $arrTitle[$s] ?></h1>
  <?= getBread(ucwords($mode . " data")) ?>
  <span class="pagedesc">&nbsp;</span>
</div>

<div id="contentwrapper" class="contentwrapper">
  <form action="<?php echo preg_replace("/&id=\d+/", "", preg_replace("/&modedf=\w+/", "", $_SERVER["REQUEST_URI"])) ?>" method="post" id="myForm" class="stdform" enctype="multipart/form-data">
    <div style="top:10px; right:35px; position:absolute">
      <p class="stdformbutton">
      <a href="#" onclick="openBox('ajax.php?mode=printCV&par[id]=<?= $cID.getPar($par,'mode')?>',1200,600);" id="btnExpExcel" class="btn btn1 btn_inboxi"><span>Print CV</span></a>
      <?php if (isset($menuAccess[$s]["add"]) && isset($menuAccess[$s]["edit"])){
        ?>
        <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
        <?php 
        }?>
        
      </p>
    </div>
    <ul class="hornav">
      <li class="current"><a href="#tab_data">Data Pegawai</a></li>
      <li><a href="#tab_pos">Posisi</a></li>
      <li><a href="#tab_sta">Status</a></li>
      <li><a href="#tab_pic">Foto</a></li>
      <!--<li><a href="#tab_pla">Plafon</a></li>-->
    </ul>
    <div id="tab_data" class="subcontent" style="margin-top: 0px;">    
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="55%">		
           <p id="p0"><?= $ui->createLabelSpanInputAttr("Nama Lengkap", strtoupper($emp->name), "name", "name", "mediuminput", "maxlength=150") ?></p>
           <p id="p0"><?= $ui->createLabelSpanInputAttr("Gelar Depan", $emp->depan, "depan", "depan", "smallinput", "maxlength=50") ?></p>
           <p id="p0"><?= $ui->createLabelSpanInputAttr("Gelar Belakang", $emp->belakang, "belakang", "belakang", "smallinput", "maxlength=50") ?></p>
           <p id="p0"><?= $ui->createLabelSpanInputAttr("Panggilan", $emp->alias, "alias", "alias", "mediuminput", "maxlength=50") ?></p>
           <!--<p id="p0"><?= $ui->createLabelSpanInputAttr("Tempat Lahir", $emp->birthPlace, "birthPlace", "birthPlace", "mediuminput", "maxlength=50") ?></p>-->
           <p id="p0">
            <label class="l-input-small">Tempat Lahir</label>
            <span class="fieldB">
              <?php
              $sql = "select t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 
              JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
              where 
              t2.kodeCategory='".$arrParameter[3]."' 
              AND t2.kodeInduk='1'
              AND t1.kodeCategory='".$arrParameter[4]."'
              order by t2.namaData, t1.namaData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "birthPlace", $emp->birthPlace, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("Tanggal Lahir", $emp->birthDate, "birthDate", "birthDate", "hasDatePicker2", "maxlength=10") ?></p>
          <?php
          if ($emp->gender == "" || $emp->gender == "M") {
            $male = "checked='checked'";
          } else {
            $female = "checked='checked'";
          }
          ?>
          <p id="p0">
            <label class="l-input-small">Jenis Kelamin</label>
            <span class="fieldB">
              <input type="radio" id="gender_m" name="gender" value="M" <?= $male ?>/> <span class="sradio">Laki-Laki</span>
              <input type="radio" id="gender_f" name="gender" value="F" <?= $female ?>/> <span class="sradio">Perempuan</span>
            </span>
          </p>
        </td>
        <td width="45%">
          <p id="p0"><?= $ui->createLabelSpanInputAttr("NPP", $emp->regNo, "regNo", "regNo", "mediuminput", "maxlength=20") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("No. KTP", $emp->ktpNo, "ktpNo", "ktpNo", "mediuminput", "maxlength=50") ?></p>
          <p id="p0"><?= $ui->createLabelSpanInputAttr("No. KK", $emp->kkNo, "kkNo", "kkNo", "mediuminput", "maxlength=50") ?></p>
          <p id="p0">
            <label class="l-input-small">Tipe</label>
            <span class="fieldB">
             <?php
             $sql = "select kodeData id, namaData description from mst_data where kodeCategory='JP' order by urutanData";
             echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "tipe", $emp->tipe, "", "class='single-deselect-td'");
             ?>
           </span>
         </p>
         <div style="display:none">
          <p id="p0">
            <label class="l-input-small">Berlaku s.d </label>
            <span class="fieldB">
              <input type="text" id="ktpValid" name="ktpValid" class="mediuminput hasDatePicker2" value="<?= $emp->ktpValid ?>" maxlength=10 /> &nbsp; <input type="checkbox" id="ktpLifetime" name="ktpLifetime" value="t" <?= $emp->ktpLifetime == "t" ? "checked='checked'" : "" ?>> Seumur Hidup
            </span>
          </p>
        </div>
        <p id="p0">
          <label class="l-input-small">Agama</label>
          <span class="fieldB">
           <?php
           $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[8]."' order by urutanData";
           echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "religion", $emp->religion, "", "class='single-deselect-td'");
           ?>
         </span>
       </p>
     </td>
   </tr>                
   <tr>
    <td>
     <p id="p0"><?= $ui->createLabelClassSpanTextArea("Alamat KTP", $emp->ktpAddress, "ktpAddress", "ktpAddress", "style='width:60%' rows=3", "", "l-input-small") ?></p>
     <p id="p0">
      <label class="l-input-small">Propinsi</label>
      <span class="fieldB">
        <?php
        $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData";
        echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "ktpProv", $emp->ktpProv, "", "class='single-deselect-td'");
        ?>
      </span>
    </p>
    <p id="p0">
      <label class="l-input-small">Kab/Kota</label>
      <span class="fieldB">
        <?php
        $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' order by kodeInduk,urutanData";
        echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "ktpCity", $emp->ktpCity, "", "class='single-deselect-td'");
        ?>
      </span>
    </p>
    <p id="p0"><?= $ui->createLabelSpanInputAttr("Telp. Rumah", $emp->phoneNo, "phoneNo", "phoneNo", "mediuminput", "maxlength=50") ?></p>
  </td>
  <td>
   <p id="p0"><?= $ui->createLabelClassSpanTextArea("Alamat Domisili", $emp->domAddress, "domAddress", "domAddress", "style='width:60%' rows=3", "", "l-input-small") ?></p>
   <p id="p0">
    <label class="l-input-small">Propinsi</label>
    <span class="fieldB">
      <?php
      $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData";
      echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "domProv", $emp->domProv, "", "class='single-deselect-td'");
      ?>
    </span>
  </p>
  <p id="p0">
    <label class="l-input-small">Kab/Kota</label>
    <span class="fieldB">
      <?php
      $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' order by kodeInduk,urutanData";
      echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "domCity", $emp->domCity, "", "class='single-deselect-td'");
      ?>
    </span>
  </p>
  <p id="p0"><?= $ui->createLabelSpanInputAttr("Nomor HP", $emp->cellNo, "cellNo", "cellNo", "mediuminput", "maxlength=20") ?></p>

</td>
</tr>       
</table>  
</div>

<div id="tab_pos" class="subcontent" style="display: none;margin-top: 0px;">
  <div id="pos_r"><a onclick="openBox('<?php echo str_replace("index", "popup", $loc) . "&mode=jabadd&tipe=" ?>' + document.getElementById('tipe').value, 950, 575)" class="btn btn1 btn_document" href="#Add"><span>Tambah Data</span></a></div>
  <br class="clear"/>
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="jobtable">
    <thead>
      <tr>
        <th width="30px">No.</th>
        <th>Jabatan</th>
        <th>Pangkat</th>
        <th width="80px">Grade</th>
        <th>Lokasi</th>
        <th width="80px">Tahun</th>
        <th width="100px">Nomor SK</th>
        <th width="50px">Status</th>
        <th width="80px">Control</th>
      </tr>
    </thead> 
    <tbody>
      <?php
      $empc = new EmpPhist();
      $empc->parentId = $emp->id;
      $drs = $empc->getByParentId();
      $rno = 0;
      foreach ($drs as $r) {
       $no++;
       $dataTmpl = "&r1=" . urlencode($r->posName);
       $dataTmpl .= "&r2=" . urlencode($r->skNo);
       $dataTmpl .= "&r3=" . urlencode($r->skDate);
       $dataTmpl .= "&r4=" . urlencode($r->dirId);
       $dataTmpl .= "&r5=" . urlencode($r->divId);
       $dataTmpl .= "&r6=" . urlencode($r->deptId);
       $dataTmpl .= "&r7=" . urlencode($r->unitId);
       $dataTmpl .= "&r8=" . urlencode($r->lineId);
       $dataTmpl .= "&r9=" . urlencode($r->startDate);
       $dataTmpl .= "&r10=" . urlencode($r->endDate);
       $dataTmpl .= "&r11=" . urlencode($r->status);
       $dataTmpl .= "&r12=" . urlencode($r->remark);
       $dataTmpl .= "&r13=" . urlencode($r->location);
       $dataTmpl .= "&r14=" . urlencode($r->rank);
       $dataTmpl .= "&r15=" . urlencode($r->grade);
       $dataTmpl .= "&r16=" . urlencode($r->leaderId);
       $dataTmpl .= "&r17=" . urlencode($r->administrationId);
       $dataTmpl .= "&r18=" . urlencode($r->replacementId);
       $dataTmpl .= "&r19=" . urlencode($r->replacement2Id);
       $dataTmpl .= "&r20=" . urlencode($r->provId);
       $dataTmpl .= "&r21=" . urlencode($r->cityId);
       $dataTmpl .= "&r22=" . urlencode($r->lembur);
       $dataTmpl .= "&r23=" . urlencode($r->payrollId);
       $dataTmpl .= "&r24=" . urlencode($r->prosesId);
       $dataTmpl .= "&r25=" . urlencode($r->groupId);
       $dataTmpl .= "&r26=" . urlencode($r->penilaianId);
       $dataTmpl .= "&r27=" . urlencode($r->shiftId);
       $dataTmpl .= "&r28=" . urlencode($r->companyId);
       $dataTmpl .= "&r29=" . urlencode($r->region);
       $dataTmpl .= "&r30=" . urlencode($r->laporanId);
       $dataTmpl .= "&r31=" . urlencode($r->kategoriId);
       $dataTmpl .= "&r32=" . urlencode($r->skala);
       $dataTmpl .= "&r33=" . urlencode($r->perdin);

       $rowTmpl = "<tr id='pJabRow_" . $rno . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabId_" . $rno . "' name='pJabId[]' value='" . $r->id . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabPosName_" . $rno . "' name='pJabPosName[]' value='" . $r->posName . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabSkNo_" . $rno . "' name='pJabSkNo[]' value='" . $r->skNo . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabSkDate_" . $rno . "' name='pJabSkDate[]' value='" . $r->skDate . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabDirId_" . $rno . "' name='pJabDirId[]' value='" . $r->dirId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabDivId_" . $rno . "' name='pJabDivId[]' value='" . $r->divId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabDeptId_" . $rno . "' name='pJabDeptId[]' value='" . $r->deptId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabUnitId_" . $rno . "' name='pJabUnitId[]' value='" . $r->unitId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabLineId_" . $rno . "' name='pJabLineId[]' value='" . $r->lineId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabProvId_" . $rno . "' name='pJabProvId[]' value='" . $r->provId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabCityId_" . $rno . "' name='pJabCityId[]' value='" . $r->cityId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabStartDate_" . $rno . "' name='pJabStartDate[]' value='" . $r->startDate . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabEndDate_" . $rno . "' name='pJabEndDate[]' value='" . $r->endDate . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabStatus_" . $rno . "' name='pJabStatus[]' value='" . $r->status . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabRemark_" . $rno . "' name='pJabRemark[]' value='" . $r->remark . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabLocation_" . $rno . "' name='pJabLocation[]' value='" . $r->location . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabRegion_" . $rno . "' name='pJabRegion[]' value='" . $r->region . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabRank_" . $rno . "' name='pJabRank[]' value='" . $r->rank . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabGrade_" . $rno . "' name='pJabGrade[]' value='" . $r->grade . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabLeaderId_" . $rno . "' name='pJabLeaderId[]' value='" . $r->leaderId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabAdministrationId_" . $rno . "' name='pJabAdministrationId[]' value='" . $r->administrationId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabReplacementId_" . $rno . "' name='pJabReplacementId[]' value='" . $r->replacementId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabReplacement2Id_" . $rno . "' name='pJabReplacement2Id[]' value='" . $r->replacement2Id . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabLembur_" . $rno . "' name='pJabLembur[]' value='" . $r->lembur . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabPayrollId_" . $rno . "' name='pJabPayrollId[]' value='" . $r->payrollId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabProsesId_" . $rno . "' name='pJabProsesId[]' value='" . $r->prosesId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabGroupId_" . $rno . "' name='pJabGroupId[]' value='" . $r->groupId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabPenilaianId_" . $rno . "' name='pJabPenilaianId[]' value='" . $r->penilaianId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabShiftId_" . $rno . "' name='pJabShiftId[]' value='" . $r->shiftId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabCompanyId_" . $rno . "' name='pJabCompanyId[]' value='" . $r->companyId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabLaporanId_" . $rno . "' name='pJabLaporanId[]' value='" . $r->laporanId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabKategoriId_" . $rno . "' name='pJabKategoriId[]' value='" . $r->kategoriId . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabSkala_" . $rno . "' name='pJabSkala[]' value='" . $r->skala . "'>";
       $rowTmpl.= "<input type='hidden' id='pJabPerdin_" . $rno . "' name='pJabPerdin[]' value='" . $r->perdin . "'>";

       $rowTmpl.= "<td align=\"center\">" . $no . ".</td>";
       $rowTmpl.= "<td>" . $r->posName . "</td>";
       $rowTmpl.= "<td>" . $cutil->getMstDataDesc($r->rank) . "</td>";
       $rowTmpl.= "<td>" . $cutil->getMstDataDesc($r->grade) . "</td>";
       $rowTmpl.= "<td>" . $cutil->getMstDataDesc($r->location) . "</td>";
       $rowTmpl.= "<td style='text-align:center'>" . substr($r->startDate, 0, 4) . ($r->endDate == null || $r->endDate == "" ? " - current " : " - " . substr($r->endDate, 0, 4)) . "</td>";
       $rowTmpl.= "<td>" . $r->skNo . "</td>";            
       $rowTmpl.= "<td style='text-align:center'>" . ($r->status == "1" ? "<img src=\"styles/images/t.png\" title='Active'>" : "<img src=\"styles/images/f.png\" title='Not Active'>") . "</td>";
       $rowTmpl.= "<td style='text-align:center'>";
       if(isset($menuAccess[$s]["edit"])){
       $rowTmpl.= "<a class='edit editRow' href=\"javascript:void(0);\" onclick=\"openBox('popup.php?" . getPar() . "&mode=jabedit&tipe=' + document.getElementById('tipe').value + '&rown=" . $rno . "&id=" . $r->id . $dataTmpl . "',950,575)\" ><span>Remove</span></a>";
       }
       if(isset($menuAccess[$s]["delete"])){
       $rowTmpl.= "<a class='delete delRow' href=\"#delete\" onclick=\"if(confirm('Are you sure to delete data from list?')) { jQuery(this).parent().parent().remove(); }\"><span>Remove</span></a>";
       }
       $rowTmpl.= "</td>";
       $rowTmpl.= "</tr>";
       $rno++;
       echo $rowTmpl;
     }
     ?>
   </tbody>
 </table>
</div>
<div id="tab_sta" class="subcontent" style="display: none;margin-top: 0px;">
  <table style="width: 100%;">
    <tr>
      <td  style="width: 55%; padding-right:20px;">
       <div class="widgetbox">
        <div class="title" style="margin-bottom:0px;"><h3>Status Pegawai</h3></div>
      </div>
      <p id="p0">
       <label class="l-input-small">Status Pegawai</label>
       <span class="fieldB">
        <?php
        $emp->cat = empty($emp->cat) ? $par[empType] : $emp->cat;
        echo $cutil->generateSelect("SELECT kodeData id, namaData description FROM mst_data WHERE kodeCategory='".$arrParameter[5]."' ORDER BY urutanData asc", "id", "description", "cat", $emp->cat,"","disabled");
        ?>
      </span>
    </p>
    <?php 
    if($_GET[mode]!="edit"){
      ?>
      <p id="p0">
       <label class="l-input-small">Status Aktif</label>
       <span class="fieldB">
        <?php
        echo $cutil->generateSelectWithEmptyOption("select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[6]."' order by urutanData", "id", "description", "status", $emp->status,"","disabled")
        ?>
      </span>
    </p>
    <?php 
  }
  ?>
  <p id="p0"><?= $ui->createLabelSpanInputAttr("Mulai Bekerja", $emp->joinDate, "joinDate", "joinDate", "hasDatePicker2", "disabled") ?></p>        
  <div style="display:none" id="p0"><?= $ui->createLabelSpanInputAttr("Tanggal Keluar", $emp->leaveDate, "leaveDate", "leaveDate", "hasDatePicker2", "maxlength=10") ?></div>
</td>
<td  style="width: 45%">
 <div class="widgetbox">
  <div class="title" style="margin-bottom:0px;"><h3>Ukuran Seragam</h3></div>
</div>
<p id="p0"><?= $ui->createLabelSpanInputAttr("Baju", $emp->uniCloth, "uniCloth", "uniCloth", "smallinput", "maxlength=40") ?></p>
<p id="p0"><?= $ui->createLabelSpanInputAttr("Celana", $emp->uniPant, "uniPant", "uniPant", "smallinput", "maxlength=40") ?></p>
<p id="p0"><?= $ui->createLabelSpanInputAttr("Sepatu", $emp->uniShoe, "uniShoe", "uniShoe", "smallinput", "maxlength=40") ?></p>
</td>
</tr>
</table>
<div class="widgetbox">
 <div class="title" style="margin-bottom:0px;"><h3>Detail Info</h3></div>
</div>
<table style="width: 100%; margin-top:-10px;">
  <tr>
    <td  style="width: 55%">
     <p id="p0">
       <label class="l-input-small">Status Perkawinan</label>
       <span class="fieldB">
        <?php echo $cutil->generateSelectWithEmptyOption("select kodeData id, concat(namaData,' - ',keteranganData) description from mst_data where kodeCategory='".$arrParameter[9]."' order by urutanData", "id", "description", "marital", $emp->marital, "", "class='single-deselect-td'") ?>
      </span>
    </p>
    <p id="p0"><?= $ui->createLabelSpanInputAttr("Email", $emp->email, "email", "email", "mediuminput", "maxlength=50") ?></p>
    <p id="p0"><?= $ui->createLabelSpanInputAttr("No. NPWP", $emp->npwpNo, "npwpNo", "npwpNo", "mediuminput", "maxlength=40") ?></p>
    <p id="p0"><?= $ui->createLabelSpanInputAttr("Tgl. NPWP", $emp->npwpDate, "npwpDate", "npwpDate", "mediuminput hasDatePicker2", "maxlength=10") ?></p>
    <p id="p0">
      <?php
      if ($emp->lembur == "" || $emp->lembur == "f") {
        $lembur_t = "checked='checked'";
      } else {
        $lembur_f = "checked='checked'";
      }
      ?>
      <label class="l-input-small">Hak Lembur</label>
      <span class="fieldB">
        <input type="radio" id="lembur_t" name="lembur" value="t" <?= $lembur_t ?>/> <span class="sradio">Ya</span>
        <input type="radio" id="lembur_f" name="lembur" value="f" <?= $lembur_f ?>/> <span class="sradio">Tidak</span>
      </span>
    </p>
  </td>
  <td  style="width: 45%">
   <p id="p0"><?= $ui->createLabelSpanInputAttr("No. BPJS TK", $emp->bpjsNo, "bpjsNo", "bpjsNo", "mediuminput", "maxlength=40") ?></p>
   <p id="p0"><?= $ui->createLabelSpanInputAttr("Tgl. BPJS TK", $emp->bpjsDate, "bpjsDate", "bpjsDate", "mediuminput hasDatePicker2", "maxlength=10") ?></p>
   <p id="p0"><?= $ui->createLabelSpanInputAttr("No. BPJS KS", $emp->bpjsNoKS, "bpjsNoKS", "bpjsNoKS", "mediuminput", "maxlength=40") ?></p>
   <p id="p0"><?= $ui->createLabelSpanInputAttr("Tgl. BPJS KS", $emp->bpjsDateKS, "bpjsDateKS", "bpjsDateKS", "mediuminput hasDatePicker2", "maxlength=10") ?></p>
   <p id="p0"><label class="l-input-small">Gol. Darah</label>
    <span class="fieldB">
      <?= $cutil->generateSelectArray(array("A", "B", "O", "AB"), "bloodType", $emp->bloodType) ?>&nbsp;&nbsp;&nbsp;<strong>Rhesus</strong>&nbsp;&nbsp;&nbsp;
      <?= $cutil->generateSelectArray(array("+", "-"), "bloodResus", $emp->bloodResus) ?>
    </span>
  </p>
</td>
</tr>
</table> 
<div class="widgetbox">
 <div class="title" style="margin-bottom:0px;"><h3>Sosial Media</h3></div>
</div>
<table style="width: 100%; margin-top:-10px;">
  <tr>
    <td  style="width: 55%">
      <p id="p0"><?= $ui->createLabelSpanInputAttr("Facebook", $emp->facebook, "facebook", "facebook", "mediuminput", "maxlength=50") ?></p>
      <p id="p0"><?= $ui->createLabelSpanInputAttr("Twitter", $emp->twitter, "twitter", "twitter", "mediuminput", "maxlength=50") ?></p>
    </td>
    <td  style="width: 45%">
     <p id="p0"><?= $ui->createLabelSpanInputAttr("Instagram", $emp->instagram, "instagram", "instagram", "mediuminput", "maxlength=50") ?></p>
   </td>
 </tr>
</table>


</div>
<div id="tab_pic" class="subcontent" style="display: none;margin-top: 0px;">
  <table style="width: 100%">
    <tr>
      <td width="40%" style="padding-bottom:20px;">
        <label class="l-input-small">Foto</label>
        <div id="fotoPreview" onclick="openBox('index.php?par[mode]=edit&viewGambar&url=<?= APP_URL . "/files/emp/pic/" . $emp->picFilename ?><?= getPar($par); ?>',500,300);"
          <?php
          if ($mode == "edit" && $emp->picFilename != "") {
            echo "style=\"background-image:  url('" . APP_URL . "/files/emp/pic/" . $emp->picFilename . "')\" ";
          }
          ?>
          ></div>
          <br/>
          <input id="picFilename" type="file" name="picFilename" class="img" style="padding-left: 170px;" />   

        </td>
      </tr>
      <tr>
        <td width="60%" style="padding-bottom:20px;">
         <label class="l-input-small">File KTP</label>
         <div id="ktpPreview" onclick="openBox('index.php?par[mode]=viewGambar&par[url]=<?= APP_URL . "/files/emp/ktp/" . $emp->ktpFilename ?><?= getPar($par,"mode"); ?>',500,300);"
          <?php
          if ($mode == "edit" && $emp->ktpFilename != "") {
            echo "style=\"background-image:  url('" . APP_URL . "/files/emp/ktp/" . $emp->ktpFilename . "')\" ";
          }
          ?>></div>
          <br/>
          <input id="ktpFilename" type="file" name="ktpFilename" class="img" style="padding-left: 190px;" />   
        </td>
      </tr>
      <tr>
       <td width="60%">


         <label class="l-input-small">File KK</label>
         <div id="kkPreview" onclick="openBox('index.php?par[mode]=viewGambar&par[url]=<?= APP_URL . "/files/emp/ktp/" . $emp->kkFilename ?><?= getPar($par,"mode"); ?>',500,300);"
          <?php
          if ($mode == "edit" && $emp->kkFilename != "") {
            echo "style=\"background-image:  url('" . APP_URL . "/files/emp/ktp/" . $emp->kkFilename . "')\" ";
          }
          ?>></div>
          <br/>
          <input id="kkFilename" type="file" name="kkFilename" class="img" style="padding-left: 190px;" />   
        </td>
        <td width="40%">
          &nbsp;
        </td>
      </tr>
    </table>
  </div>
  <div id="tab_pla" class="subcontent" style="display: none;margin-top: 0px;">
    <div id="pos_r"><a onclick="openBox('<?php echo str_replace("index", "popup", $loc) . "&mode=fasadd" ?>', 750, 450)" class="btn btn1 btn_document" href="#Add"><span>Tambah Data</span></a></div>
    <br class="clear"/>
    <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="fastable">
      <thead>
        <tr>
          <th>Fasilitas/Plafon</th>
          <th width="150px">Masa Berlaku</th>
          <th width="125px">Nilai</th>
          <th width="80px">Control</th>
        </tr>
      </thead> 
      <tbody>
        <?php
        $empfas = new EmpPlaf();
        $empfas->parentId = $emp->id;
        $drs = $empfas->getByParentId();
        $rno = 0;
        foreach ($drs as $r) {
          $dataTmpl = "&r1=" . urlencode($r->plafonId);
          $dataTmpl .= "&r2=" . urlencode($r->satuanId);
          $dataTmpl .= "&r3=" . urlencode($r->plafonValue);
          $dataTmpl .= "&r4=" . urlencode($r->satuanPos);
          $dataTmpl .= "&r5=" . urlencode($r->remark);
          $dataTmpl .= "&r6=" . urlencode($r->mulai);
          $dataTmpl .= "&r7=" . urlencode($r->selesai);
          $dataTmpl .= "&r8=" . urlencode($r->toleransi);
          $rowTmpl = "<tr id='pFasRow_" . $rno . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasId_" . $rno . "' name='pFasId[]' value='" . $r->id . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasPlafonId_" . $rno . "' name='pFasPlafonId[]' value='" . $r->plafonId . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasSatuanId_" . $rno . "' name='pFasSatuanId[]' value='" . $r->satuanId . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasPlafonValue_" . $rno . "' name='pFasPlafonValue[]' value='" . $r->plafonValue . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasSatuanPos_" . $rno . "' name='pFasSatuanPos[]' value='" . $r->satuanPos . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasRemark_" . $rno . "' name='pFasRemark[]' value='" . $r->remark . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasMulai_" . $rno . "' name='pFasMulai[]' value='" . $r->mulai . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasSelesai_" . $rno . "' name='pFasSelesai[]' value='" . $r->selesai . "'>";
          $rowTmpl.= "<input type='hidden' id='pFasToleransi_" . $rno . "' name='pFasToleransi[]' value='" . $r->toleransi . "'>";
//          $rowTmpl.= "<td>" . $rno . "</td>";
          $rowTmpl.= "<td>" . $cutil->getMstDataDesc($r->plafonId) . "</td>";
          $rowTmpl.= "<td>" . $r->mulai . " s.d " . $r->selesai . "</td>";
          $rowTmpl.= "<td style='text-align:right'>" . ($r->satuanPos == "d" ? $cutil->getMstDataDesc($r->satuanId) . " " . getAngka($r->plafonValue) : getAngka($r->plafonValue) . " " . $cutil->getMstDataDesc($r->satuanId) ) . "</td>";
          $rowTmpl.= "<td style='text-align:center'>";
          $rowTmpl.= "<a class='edit editRow' href=\"javascript:void(0);\" onclick=\"openBox('popup.php?" . getPar() . "&mode=fasedit&rown=" . $rno . "&id=" . $r->id . $dataTmpl . "',750,450)\" ><span>Remove</span></a>";
          $rowTmpl.= "<a class='delete delRow' href=\"#delete\" onclick=\"if(confirm('Are you sure to delete data from list?')) { jQuery(this).parent().parent().remove(); }\"><span>Remove</span></a>";
          $rowTmpl.= "</td>";
          $rowTmpl.= "</tr>";
          $rno++;
          echo $rowTmpl;
        }
        ?>
      </tbody>
    </table>
  </div>
</form>
</div>
<script language="javascript">
  var suri = '<?= $_SERVER["REQUEST_URI"] ?>';
  var sajax = suri.split("index.php").join("ajax.php");
  var isEdited = false;
  jQuery(document).ready(function () {
    jQuery("#myForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-30').chosen({allow_single_deselect: true, width: '30%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

    jQuery("#ktpCity").chained("#ktpProv");
    jQuery("#ktpCity").trigger("chosen:updated");

    jQuery("#ktpProv").bind("change", function () {
      jQuery("#ktpCity").trigger("chosen:updated");
    });

    jQuery("#domCity").chained("#domProv");
    jQuery("#domCity").trigger("chosen:updated");

    jQuery("#domProv").bind("change", function () {
      jQuery("#domCity").trigger("chosen:updated");
    });
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

    jQuery("#kkFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#kkPreview").css("background-image", "url(" + this.result + ")");
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

    // jQuery("#name").rules("add", {remote: {url: 'ajax.php', type: 'get', async: false, data: {'t': 'common', 'f': 'emp_check', 'chkmode': 'nama', 'id': '<?= $emp->id ?>', 'nik': jQuery("#regNo").val()}}, messages: {remote: "Nama Lengkap sudah pernah diinput."}});
    jQuery("#cellNo").rules("add", {remote: {url: 'ajax.php', type: 'get', async: false, data: {'t': 'common', 'f': 'emp_check', 'chkmode': 'hp', 'id': '<?= $emp->id ?>'}}, messages: {remote: "Nomor HP sudah pernah diinput."}});
    jQuery("#email").rules("add", {remote: {url: 'ajax.php', type: 'get', async: false, data: {'t': 'common', 'f': 'emp_check', 'chkmode': 'email', 'id': '<?= $emp->id ?>'}}, messages: {remote: "Email sudah pernah diinput."}});
    jQuery("#regNo").rules("add", {remote: {url: 'ajax.php', type: 'get', async: false, data: {'t': 'common', 'f': 'emp_check', 'chkmode': 'nik', 'id': '<?= $emp->id ?>'}}, messages: {remote: "NPP sudah pernah diinput."}});
    jQuery("#ktpNo").rules("add", {remote: {url: 'ajax.php', type: 'get', async: false, data: {'t': 'common', 'f': 'emp_check', 'chkmode': 'ktp', 'id': '<?= $emp->id ?>'}}, messages: {remote: "Nomor KTP sudah pernah diinput."}});
    jQuery("#status").bind("change", function () {
      if (jQuery(this).val() !== "535") {
        addAttributeRequired("leaveDate");
        jQuery("#leaveDate").rules("add", {required: true, messages: {required: "Field Tanggal Keluar harus diisi.."}});
      } else {
        jQuery("#leaveDate").rules("remove");
        var stext = jQuery("#leaveDate").parent().parent().children("label").html().replace('&nbsp;&nbsp;<span class="required">*)</span>', '');
        jQuery("#leaveDate").parent().parent().children("label").html(stext);
      }

    });
  });

function showTab(tabname) {
  jQuery("div [class='subcontent'][id='" + tabname + "']").css('display', 'block');
  jQuery("div [class='subcontent'][id!='" + tabname + "']").css('display', 'none');
  jQuery(".hornav li a[href!='#" + tabname + "']").parent().removeClass();
  jQuery(".hornav li a[href='#" + tabname + "']").parent().removeClass().addClass("current");
}

function commitFasRow(rownum, rowId, plafonId, plafName, satuanId, satName, plafonValue, satPos, mulai, selesai, toleransi, remark) {
  var _rnum = rownum;
  var state_ = "nr";
  if (_rnum === "") {
    if (jQuery("#fastable tbody tr:last").length > 0) {
      var maxNum = 0;
      jQuery("#fastable tbody input[id^='pFasId_']").each(function () {
        var num = parseInt(jQuery(this).attr("id").split("_")[1]);
        num++;
        if (num > maxNum) {
          maxNum = num;
        }
      });
      _rnum = maxNum;
    } else {
      _rnum = 0;
    }
  } else {
    state_ = "er";
  }
  var dataTmpl = "&r1=" + encodeURI(plafonId);
  dataTmpl += "&r2=" + encodeURI(satuanId);
  dataTmpl += "&r3=" + encodeURI(plafonValue);
  dataTmpl += "&r4=" + encodeURI(satPos);
  dataTmpl += "&r5=" + encodeURI(remark);
  dataTmpl += "&r6=" + encodeURI(mulai);
  dataTmpl += "&r7=" + encodeURI(selesai);
  dataTmpl += "&r8=" + encodeURI(toleransi);
  var trTmpl = "<tr id='pFasRow_" + _rnum + "'>";
  var rowTmpl = "<input type='hidden' id='pFasId_" + _rnum + "' name='pFasId[]' value='" + rowId + "'>";
  rowTmpl += "<input type='hidden' id='pFasPlafonId_" + _rnum + "' name='pFasPlafonId[]' value='" + plafonId + "'>";
  rowTmpl += "<input type='hidden' id='pFasSatuanId_" + _rnum + "' name='pFasSatuanId[]' value='" + satuanId + "'>";
  rowTmpl += "<input type='hidden' id='pFasPlafonValue_" + _rnum + "' name='pFasPlafonValue[]' value='" + plafonValue + "'>";
  rowTmpl += "<input type='hidden' id='pFasSatuanPos_" + _rnum + "' name='pFasSatuanPos[]' value='" + satPos + "'>";
  rowTmpl += "<input type='hidden' id='pFasRemark_" + _rnum + "' name='pFasRemark[]' value='" + remark + "'>";
  rowTmpl += "<input type='hidden' id='pFasMulai_" + _rnum + "' name='pFasMulai[]' value='" + mulai + "'>";
  rowTmpl += "<input type='hidden' id='pFasSelesai_" + _rnum + "' name='pFasSelesai[]' value='" + selesai + "'>";
  rowTmpl += "<input type='hidden' id='pFasToleransi_" + _rnum + "' name='pFasToleransi[]' value='" + toleransi + "'>";
//    rowTmpl += "<td>" + _rnum + "</td>";
rowTmpl += "<td>" + plafName + "</td>";
rowTmpl += "<td>" + mulai + ' s.d ' + selesai + "</td>";
rowTmpl += "<td style='text-align:right'>" + (satPos === "d" ? satName + " " + formatAngka(plafonValue) : formatAngka(plafonValue) + " " + satName) + "</td>";
rowTmpl += "<td  style='text-align:center'>";
rowTmpl += "<a class='edit editRow' href='javascript:void(0)' onclick=\"openBox('" + sop + "&mode=fasedit&rown=" + _rnum + "&id=" + rowId + dataTmpl + "',750,450)\" ><span>Remove</span></a>";
rowTmpl += "<a class='delete delRow' href=\"javascript:void(0)\" onclick=\"if(confirm('Are you sure to delete data from list?')) { jQuery(this).parent().parent().remove(); }\"><span>Remove</span></a>";
rowTmpl += "</td>";
if (state_ === "nr") {
  jQuery("#fastable  tbody").append(trTmpl + rowTmpl + "</tr>");
} else {
  jQuery("#fastable  tbody #pFasRow_" + _rnum).html("").html(rowTmpl);
}
}

function commitRow(rownum, rowId, posName, skNo, skDate, dirId, divId, deptId, unitId, lineId, startDate, endDate, remark, status, dirName, divName, deptName, unitName, lineName, location, rank, grade, leaderId, administrationId, replacementId, replacement2Id, provId, cityId, gradeName, rankName, locationName, lembur, payrollId, prosesId, groupId, penilaianId, shiftId, companyId, region, laporanId, kategoriId, skala, perdin) {
	
  var _rnum = rownum;
  var state_ = "nr";
  if (_rnum === "") {
    if (jQuery("#jobtable tbody tr:last").length > 0) {
      var maxNum = 0;
      jQuery("#jobtable tbody input[id^='pJabId_']").each(function () {
        var num = parseInt(jQuery(this).attr("id").split("_")[1]);
        num++;
        if (num > maxNum) {
          maxNum = num;
        }
      });
      _rnum = maxNum;
    } else {
      _rnum = 0;
    }
  } else {
    state_ = "er";
  }
  var dataTmpl = "&r1=" + encodeURI(posName);
  dataTmpl += "&r2=" + encodeURI(skNo);
  dataTmpl += "&r3=" + encodeURI(skDate);
  dataTmpl += "&r4=" + encodeURI(dirId);
  dataTmpl += "&r5=" + encodeURI(divId);
  dataTmpl += "&r6=" + encodeURI(deptId);
  dataTmpl += "&r7=" + encodeURI(unitId);
  dataTmpl += "&r8=" + encodeURI(lineId);
  dataTmpl += "&r9=" + encodeURI(startDate);
  dataTmpl += "&r10=" + encodeURI(endDate);
  dataTmpl += "&r11=" + encodeURI(status);
  dataTmpl += "&r12=" + encodeURI(remark);
  dataTmpl += "&r13=" + encodeURI(location);
  dataTmpl += "&r14=" + encodeURI(rank);
  dataTmpl += "&r15=" + encodeURI(grade);
  dataTmpl += "&r16=" + encodeURI(leaderId);
  dataTmpl += "&r17=" + encodeURI(administrationId);
  dataTmpl += "&r18=" + encodeURI(replacementId);
  dataTmpl += "&r19=" + encodeURI(replacement2Id);
  dataTmpl += "&r20=" + encodeURI(provId);
  dataTmpl += "&r21=" + encodeURI(cityId);
  dataTmpl += "&r22=" + encodeURI(lembur);
  dataTmpl += "&r23=" + encodeURI(payrollId);
  dataTmpl += "&r24=" + encodeURI(prosesId);
  dataTmpl += "&r25=" + encodeURI(groupId);
  dataTmpl += "&r26=" + encodeURI(penilaianId);
  dataTmpl += "&r27=" + encodeURI(shiftId);
  dataTmpl += "&r28=" + encodeURI(companyId);
  dataTmpl += "&r29=" + encodeURI(region);
  dataTmpl += "&r30=" + encodeURI(laporanId);
  dataTmpl += "&r31=" + encodeURI(kategoriId);
  dataTmpl += "&r32=" + encodeURI(skala);
  dataTmpl += "&r33=" + encodeURI(perdin);
  var trTmpl = "<tr id='pJabRow_" + _rnum + "'>";
  var rowTmpl = "<input type='hidden' id='pJabId_" + _rnum + "' name='pJabId[]' value='" + rowId + "'>";
  rowTmpl += "<input type='hidden' id='1" + _rnum + "' name='pJabPosName[]' value='" + posName + "'>";
  rowTmpl += "<input type='hidden' id='pJabSkNo_" + _rnum + "' name='pJabSkNo[]' value='" + skNo + "'>";
  rowTmpl += "<input type='hidden' id='pJabSkDate_" + _rnum + "' name='pJabSkDate[]' value='" + skDate + "'>";
  rowTmpl += "<input type='hidden' id='pJabDirId_" + _rnum + "' name='pJabDirId[]' value='" + dirId + "'>";
  rowTmpl += "<input type='hidden' id='pJabDivId_" + _rnum + "' name='pJabDivId[]' value='" + divId + "'>";
  rowTmpl += "<input type='hidden' id='pJabDeptId_" + _rnum + "' name='pJabDeptId[]' value='" + deptId + "'>";
  rowTmpl += "<input type='hidden' id='pJabUnitId_" + _rnum + "' name='pJabUnitId[]' value='" + unitId + "'>";
  rowTmpl += "<input type='hidden' id='pJabLineId_" + _rnum + "' name='pJabLineId[]' value='" + lineId + "'>";
  rowTmpl += "<input type='hidden' id='pJabStartDate_" + _rnum + "' name='pJabStartDate[]' value='" + startDate + "'>";
  rowTmpl += "<input type='hidden' id='pJabEndDate_" + _rnum + "' name='pJabEndDate[]' value='" + endDate + "'>";
  rowTmpl += "<input type='hidden' id='pJabStatus_" + _rnum + "' name='pJabStatus[]' value='" + status + "'>";
  rowTmpl += "<input type='hidden' id='pJabRemark_" + _rnum + "' name='pJabRemark[]' value='" + remark + "'>";
  rowTmpl += "<input type='hidden' id='pJabLocation_" + _rnum + "' name='pJabLocation[]' value='" + location + "'>";
  rowTmpl += "<input type='hidden' id='pJabRegion_" + _rnum + "' name='pJabRegion[]' value='" + region + "'>";
  rowTmpl += "<input type='hidden' id='pJabRank_" + _rnum + "' name='pJabRank[]' value='" + rank + "'>";
  rowTmpl += "<input type='hidden' id='pJabGrade_" + _rnum + "' name='pJabGrade[]' value='" + grade + "'>";
  rowTmpl += "<input type='hidden' id='pJabLeaderId_" + _rnum + "' name='pJabLeaderId[]' value='" + leaderId + "'>";
  rowTmpl += "<input type='hidden' id='pJabAdministrationId_" + _rnum + "' name='pJabAdministrationId[]' value='" + administrationId + "'>";
  rowTmpl += "<input type='hidden' id='pJabReplacementId_" + _rnum + "' name='pJabReplacementId[]' value='" + replacementId + "'>";
  rowTmpl += "<input type='hidden' id='pJabReplacement2Id_" + _rnum + "' name='pJabReplacement2Id[]' value='" + replacement2Id + "'>";
  rowTmpl += "<input type='hidden' id='pJabProvId_" + _rnum + "' name='pJabProvId[]' value='" + provId + "'>";
  rowTmpl += "<input type='hidden' id='pJabCityId_" + _rnum + "' name='pJabCityId[]' value='" + cityId + "'>";
  rowTmpl += "<input type='hidden' id='pJabLembur_" + _rnum + "' name='pJabLembur[]' value='" + lembur + "'>";
  rowTmpl += "<input type='hidden' id='pJabPayrollId_" + _rnum + "' name='pJabPayrollId[]' value='" + payrollId + "'>";
  rowTmpl += "<input type='hidden' id='pJabProsesId_" + _rnum + "' name='pJabProsesId[]' value='" + prosesId + "'>";
  rowTmpl += "<input type='hidden' id='pJabGroupId_" + _rnum + "' name='pJabGroupId[]' value='" + groupId + "'>";
  rowTmpl += "<input type='hidden' id='pJabPenilaianId_" + _rnum + "' name='pJabPenilaianId[]' value='" + penilaianId + "'>";
  rowTmpl += "<input type='hidden' id='pJabShiftId_" + _rnum + "' name='pJabShiftId[]' value='" + shiftId + "'>";
  rowTmpl += "<input type='hidden' id='pJabCompanyId_" + _rnum + "' name='pJabCompanyId[]' value='" + companyId + "'>";
  rowTmpl += "<input type='hidden' id='pJabLaporanId_" + _rnum + "' name='pJabLaporanId[]' value='" + laporanId + "'>";
  rowTmpl += "<input type='hidden' id='pJabKategoriId_" + _rnum + "' name='pJabKategoriId[]' value='" + kategoriId + "'>";
  rowTmpl += "<input type='hidden' id='pJabSkala_" + _rnum + "' name='pJabSkala[]' value='" + skala + "'>";
  rowTmpl += "<input type='hidden' id='pJabPerdin_" + _rnum + "' name='pJabPerdin[]' value='" + perdin + "'>";
  
  rowTmpl += "<td style='text-align:center'>" + (_rnum * 1 + 1) + ".</td>";
  rowTmpl += "<td>" + posName + "</td>";	
  rowTmpl += "<td>" + rankName + "</td>";	
  rowTmpl += "<td>" + gradeName + "</td>";	
  rowTmpl += "<td>" + locationName + "</td>";	
  rowTmpl += "<td style='text-align:center'>" + startDate.substring(0, 4) + (endDate === "" ? " - current" : " - " + endDate.substring(0, 4)) + "</td>";
  rowTmpl += "<td>" + skNo + "</td>";        
  rowTmpl += "<td style='text-align:center'>" + (status === "1" ? "<img src=\"styles/images/t.png\" title='Active'>" : "<img src=\"styles/images/f.png\" title='Not Active'>") + "</td>";
  rowTmpl += "<td  style='text-align:center'>";
  rowTmpl += "<a class='edit editRow' href='javascript:void(0)' onclick=\"openBox('" + sop + "&mode=jabedit&rown=" + _rnum + "&id=" + rowId + dataTmpl + "',950,575)\" ><span>Remove</span></a>";
  rowTmpl += "<a class='delete delRow' href=\"javascript:void(0)\" onclick=\"if(confirm('Are you sure to delete data from list?')) { jQuery(this).parent().parent().remove(); }\"><span>Remove</span></a>";
  rowTmpl += "</td>";
  if (state_ === "nr") {
    jQuery("#jobtable  tbody").append(trTmpl + rowTmpl + "</tr>");
  } else {
    jQuery("#jobtable  tbody #pJabRow_" + _rnum).html("").html(rowTmpl);
  }
  if (status === "1") {
    jQuery("#jobtable tbody input[id^='pJabStatus']:not([id$='_" + _rnum + "'])").each(function () {
      jQuery(this).val(0);
      jQuery(this).parent().find("td").eq(7).html("").html("<img src=\"styles/images/f.png\" title=\"Not Active\">");
    });
  }
  isEdited = true;
  alert('Data telah disimpan. Mohon klik tombol \'SIMPAN\' pada form utama.');
}

</script>

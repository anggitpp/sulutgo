<?php
session_start();
if (!isset($menuAccess[$s]["add"]) || !isset($menuAccess[$s]["edit"])) {
  header("Location: " . str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
}
//
/*
  var dataTmpl = "&r1=" + encodeURI(posName);
  dataTmpl += "&r2=" + encodeURI(skNo);
  dataTmpl += "&r3=" + encodeURI(skDate);
  dataTmpl += "&r4=" + encodeURI(dirId);
  dataTmpl += "&r5=" + encodeURI(divId);
  dataTmpl += "&r6=" + encodeURI(deptId);
  dataTmpl += "&r7=" + encodeURI(unitId);
  dataTmpl += "&r8=" + encodeURI(startDate);
  dataTmpl += "&r9=" + encodeURI(endDate);
  dataTmpl += "&r10=" + encodeURI(status);
  dataTmpl += "&r11=" + encodeURI(remark);
 */
$rowNum = $_GET["rown"];
$empc = new EmpPhist("C");
$empc->id = $_GET["id"];
$empc->posName = $_GET["r1"];
$empc->skNo = $_GET["r2"];
$empc->skDate = $_GET["r3"];
$empc->dirId = $_GET["r4"];
$empc->divId = $_GET["r5"];
$empc->deptId = $_GET["r6"];
$empc->unitId = $_GET["r7"];
$empc->startDate = $_GET["r8"];
$empc->endDate = $_GET["r9"];
$empc->status = $_GET["r10"];
$empc->remark = $_GET["r11"];
$empc->location = $_GET["r12"];
$empc->rank = $_GET["r13"];
$empc->grade = $_GET["r14"];
$empc->leaderId = $_GET["r15"];
$empc->administrationId = $_GET["r16"];
$empc->replacementId = $_GET["r17"];
$empc->replacement2Id = $_GET["r18"];
$empc->provId = $_GET["r19"];
$empc->cityId = $_GET["r20"];
$empc->lembur = $_GET["r21"];
$empc->payrollId = $_GET["r22"];
$empc->prosesId = $_GET["r23"];
$empc->groupId = $_GET["r24"];
$empc->penilaianId = $_GET["r25"];
$empc->shiftId = $_GET["r26"];
$empc->companyId = $_GET["r27"];
$empc->kategori = $_GET["r28"];
$empc->perdin = $_GET["r29"];
$empc->obat = $_GET["r30"];
$empc->topId = $_GET["r31"];
$empc->filename = $_GET["r32"];
$empc->managerId = $_GET["r33"];




$cutil = new Common();
$ui = new UIHelper();
$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
$__validate["formid"] = "jabForm";
$__validate["items"] = array(
//    "skNo" => array("rule" => "required", "msg" => "Field Nomor SK harus diisi.."),
    "posName" => array("rule" => "required", "msg" => "Field Jabatan harus diisi.."),
    "topId" => array("rule" => "required", "msg" => "Field ".$arrParameter[85]." harus diisi.."),
    "dirId" => array("rule" => "required", "msg" => "Field ".$arrParameter[38]." harus diisi.."),
//    "divId" => array("rule" => "required", "msg" => "Field ".$arrParameter[39]." harus diisi.."),
//    "endDate" => array("rule" => "required", "msg" => "Field Tanggal Akhir harus diisi.."),
    "startDate" => array("rule" => "required", "msg" => "Field Tanggal Mulai harus diisi.."),
//    "skDate" => array("rule" => "required", "msg" => "Field Tanggal SK harus diisi.."),
);
#KONTRAK = 584
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<div class="centercontent contentpopup">
  <div class="pageheader">           
  <h1 class="pagetitle">Data Jabatan</h1>
    <div id="contentwrapper" class="contentwrapper">
  <br clear="all">
      <form id="jabForm" class="stdform">
    <div style="position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;">
            <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
            <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
        </div>
        <div class="widgetbox">          
      <table cellpadding="0" cellspacing="4" style="width: 100%">
      <tr>  
      <td colspan="2">
        <label class="l-input-small" style="width:125px;">Posisi</label>
        <span class="fieldB">
        <input type="text" id="posName" name="posName"  value="<?php echo $empc->posName?>" class="mediuminput" maxlength="150"/>
        </span>
      </td>
      </tr>
      <tr>      
      <td style="width: 50%">
        <label class="l-input-small" style="width:125px;">Jabatan</label>
        <span class="fieldB">
          <?php
          $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[11]."' and statusData = 't' order by kodeInduk,urutanData";
          echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "rank", $empc->rank, "", " $disabled class='single-deselect-td'");
          ?>
        </span>
      </td>
      <td style="width: 50%">
        <label class="l-input-small" style="width:125px;">Golongan</label>
        <span class="fieldB">
          <?php
             $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
             JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
             where t2.kodeCategory='".$arrParameter[11]."' order by t1.urutanData";
             echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "grade", $empc->grade, "", "class='single-deselect-td'");
          ?>
        </span>
      </td>
      </tr>
      <tr>      
      <td style="width: 50%">
        <label class="l-input-small" style="width:125px;">Nomor SK</label>
        <span class="fieldB">
        <input type="text" id="skNo" name="skNo"  value="<?php echo $empc->skNo?>" class="mediuminput" maxlength="150" style="width:250px"/>
        </span>
      </td>
      <td style="width: 50%">
        <label class="l-input-small" style="width:125px;">Tanggal SK</label>
        <div class="fieldB">
          <input type="text" id="skDate" name="skDate" size="10" maxlength="10" value="<?php echo getTanggal($empc->skDate)?>" class="vsmallinput hasDatePicker2" />
        </div>  
      </td>
      </tr>
      <tr>      
      <td style="width: 50%">       
          <label class="l-input-small" style="width:125px;">File</label>
            <span class="fieldB">
              <?php
              if (!empty($empc->filename)) {
                echo "<input type='hidden' name='tmpFilename' value='$empc->filename' />";
                echo "<a href=\"download.php?d=applpt&f=$empc->filename\"><img src=\"" . getIcon($empc->filename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>&nbsp;&nbsp;&nbsp;";
              }
              ?> 
              <input id="filename" type="file" name="filename" style="" /></span> 
      </td>
      <td style="width: 50%">       
       <label class="l-input-small" style="width:125px;">Lokasi Kerja</label>
        <span class="fieldB"> 
          <?php 
            $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[7]."' order by urutanData";
            echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "location", $empc->location, "", " $disabled class='single-deselect-td'");
          ?>
        </span>           
      </td>
      </tr>
      <tr>      
      <td style="width: 50%">       
        <label class="l-input-small" style="width:125px;">Mulai</label>
        <div class="fieldB">
          <input type="text" id="startDate" name="startDate" size="10" maxlength="10" value="<?php echo getTanggal($empc->startDate)?>" class="vsmallinput hasDatePicker2" />
        </div>        
      </td>
      <td style="width: 50%">       
        <label class="l-input-small" style="width:125px;">Selesai</label>
        <div class="fieldB">
          <input type="text" id="endDate" name="endDate" size="10" maxlength="10" value="<?php echo getTanggal($empc->endDate)?>" class="vsmallinput hasDatePicker2" />
        </div>        
      </td>
      </tr>
      <tr>      
      <td style="width: 50%">       
          <?php 
          if ($empc->status == "" || $empc->status == "0") {
          $na = "checked='checked'";
          } else if ($empc->status == "1") {
          $ac = "checked='checked'";
          } else {
          $pe = "checked='checked'";
          }
          
          if ($empc->lembur == "" || $empc->lembur == "0") {
          $false = "checked='checked'";
          } else if ($empc->lembur == "1") {
          $true = "checked='checked'";
          }
          
        ?>
        <label class="l-input-small" style="width:125px;">Status</label>
        <span class="fieldB">
          <input type="radio" id="sta_0" name="status" value="0" <?= $na ?>/> <span class="sradio">Tidak Aktif</span>
          <input type="radio" id="sta_1" name="status" value="1" <?= $ac ?>/> <span class="sradio">Aktif</span>
        </span>       
      </td>
      <td style="width: 50%"> 
          
      </td>
      </tr>
      <tr>      
      <td colspan="2">
        <label class="l-input-small" style="width:125px;">Keterangan</label>                    
        <span class="fieldB">
        <input type="text" id="remark" name="remark"  value="<?php echo $empc->remark?>" class="mediuminput" maxlength="150"/>
        </span>
      </td>
      </tr>
      </table>
    <br clear="all">

    <ul class="hornav">
      <li class="current"><a href="#tab_organisasi">Organisasi</a></li>
      <li><a href="#tab_lokasi">Lokasi</a></li>
      <li><a href="#tab_struktur">Struktur</a></li>
      <li><a href="#tab_setting">Setting</a></li>
    </ul>
    <div id="tab_organisasi" class="subcontent" style="margin-top: 0px;">   
      <p>
            <label class="l-input-small" style="width:170px;"><?php echo $arrParameter[85]?></label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X03' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "topId", $empc->topId, "", " $disabled class='single-deselect-td'");
              ?>
            </span>
          </p>
      <p>
            <label class="l-input-small" style="width:170px;"><?php echo $arrParameter[38]?></label>
            <span class="fieldB">
              <?php
              $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       where t2.kodeCategory='X03' order by t1.urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "dirId", $empc->dirId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:170px;"><?php echo $arrParameter[39]?></label>
            <span class="fieldB">
              <?php
              $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       where t3.kodeCategory='X03' order by t1.urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "divId", $empc->divId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:170px;"><?php echo $arrParameter[40]?></label>
            <span class="fieldB">
              <?php
              $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
                       where t4.kodeCategory='X03' order by t1.urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "deptId", $empc->deptId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:170px;"><?php echo $arrParameter[41]?></label>
            <span class="fieldB">
              <?php
              $sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
                       JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk
                       where t5.kodeCategory='X03' order by t1.urutanData";
              echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "unitId", $empc->unitId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
    </div>
    <div id="tab_lokasi" class="subcontent" style="margin-top: 0px; display:none;">   
      <p>
      <label class="l-input-small" style="width:125px;">Propinsi</label>
      <span class="fieldB">
        <?php
        $sql = "select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData";
        echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "provId", $empc->provId, "", "class='single-deselect-td'");
        ?>
      </span>
      </p>
      <p>
              <label class="l-input-small" style="width:125px;">Kab/Kota</label>
              <span class="fieldB">
                <?php
                $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' order by kodeInduk,urutanData";
                echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "cityId", $empc->cityId, "", "class='single-deselect-td'");
                ?>
              </span>
            </p>
    </div>
    <div id="tab_struktur" class="subcontent" style="margin-top: 0px; display:none;">   
    <p>
            <label class="l-input-small" style="width:125px;">Manajer</label>
            <span class="fieldB">
              <?php
              $sql = "select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "managerId", $empc->managerId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>  
    <p>
            <label class="l-input-small" style="width:125px;">Atasan</label>
            <span class="fieldB">
              <?php
              $sql = "select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "leaderId", $empc->leaderId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:125px;">Tata Usaha</label>
            <span class="fieldB">
              <?php
              $sql = "select id id, concat(reg_no,' - ',name) description from emp WHERE status=535  order by name";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "administrationId", $empc->administrationId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:125px;">Pengganti 1</label>
            <span class="fieldB">
              <?php
              $sql = "select id id, concat(reg_no,' - ',name) description from emp WHERE status=535  order by name";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "replacementId", $empc->replacementId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
      <p>
            <label class="l-input-small" style="width:125px;">Pengganti 2</label>
            <span class="fieldB">
              <?php
              $sql = "select id id, concat(reg_no,' - ',name) description from emp WHERE status=535  order by name";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "replacement2Id", $empc->replacement2Id, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
    </div>  
    <div id="tab_setting" class="subcontent" style="margin-top: 0px; display:none;">   
      <p>
      <label class="l-input-small" style="width:125px;">Hak Lembur</label>
      <span class="fieldB">
        <input type="radio" id="lmb_0" name="lembur" value="0" <?= $false ?>/> <span class="sradio">Tidak</span>
        <input type="radio" id="lmb_" name="lembur" value="1" <?= $true ?>/> <span class="sradio">Ya</span>
      </span><br clear="all">
      </p>
      <p>
            <label class="l-input-small" style="width:125px;">Jenis Payroll</label>
            <span class="fieldB">
              <?php
              $sql = "select idJenis id, namaJenis description from pay_jenis where statusJenis='t' order by namaJenis";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "payrollId", $empc->payrollId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
      <p>
            <label class="l-input-small" style="width:125px;">Location Process</label>
            <span class="fieldB">
              <?php
          $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[7]."' order by urutanData";
          echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "groupId", $empc->groupId, "", "class='single-deselect-td'");
        ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:125px;">Group Process</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[49]."' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "prosesId", $empc->prosesId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
  
      <p>
            <label class="l-input-small" style="width:125px;">Shift Kerja</label>
            <span class="fieldB">
              <?php
              $sql = "select idShift id, namaShift description from dta_shift where statusShift='t' order by namaShift";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "shiftId", $empc->shiftId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
      <p>
            <label class="l-input-small" style="width:125px;">Perusahaan</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[47]."' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "companyId", $empc->companyId, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
            <p>
            <label class="l-input-small" style="width:125px;">Kategori</label>
            <span class="fieldB">
              <?php
              $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='KT' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "kategori", $empc->kategori, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
          <p>
            <label class="l-input-small" style="width:125px;">Perjalanan Dinas</label>
            <span class="fieldB">
              <?php
              $empc->perdin = empty($empc->perdin) ? getField("select kodeData from mst_data where kodeCategory = 'PJG' order by urutanData DESC LIMIT 1") : $empc->perdin;
              $sql = "select kodeData id, concat(namaData, ' ', '(', keteranganData, ')') description, kodeInduk from mst_data  where kodeCategory='PJG' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "perdin", $empc->perdin, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
           <p>
            <label class="l-input-small" style="width:125px;">Gol. Pengobatan</label>
            <span class="fieldB">
              <?php
              $empc->obat = empty($empc->obat) ? getField("select kodeData from mst_data where kodeCategory = 'GRI' order by urutanData DESC LIMIT 1") : $empc->obat;
              $sql = "select kodeData id, concat(namaData, ' ', '(', keteranganData, ')') description, kodeInduk from mst_data  where kodeCategory='GRI' order by kodeInduk,urutanData";
              echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "obat", $empc->obat, "", "class='single-deselect-td'");
              ?>
            </span>
          </p>
    </div>  

        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery("#jabForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "dd/mm/yy"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});


    jQuery("#dirId").chained("#topId");
    jQuery("#dirId").trigger("chosen:updated");

    jQuery("#topId").bind("change", function () {
      jQuery("#dirId").trigger("chosen:updated");
    });
    
    jQuery("#unitId").chained("#deptId");
    jQuery("#unitId").trigger("chosen:updated");

    jQuery("#deptId").bind("change", function () {
      jQuery("#unitId").trigger("chosen:updated");
    });

  jQuery("#cityId").chained("#provId");
    jQuery("#cityId").trigger("chosen:updated");

    jQuery("#provId").bind("change", function () {
      jQuery("#cityId").trigger("chosen:updated");
    });
  
    jQuery("#deptId").chained("#divId");
    jQuery("#deptId").trigger("chosen:updated");

    jQuery("#divId").bind("change", function () {
      jQuery("#deptId").trigger("chosen:updated");
    });
    jQuery("#divId").chained("#dirId");
    jQuery("#divId").trigger("chosen:updated");

    jQuery("#dirId").bind("change", function () {
      jQuery("#divId").trigger("chosen:updated");
    });
    jQuery("#grade").chained("#rank");
    jQuery("#grade").trigger("chosen:updated");

    jQuery("#rank").bind("change", function () {
      jQuery("#grade").trigger("chosen:updated");
    });


    jQuery("#jabForm").submit(function (event) {
      if (jQuery("#jabForm").valid()) {
        jQuery("#btnSimpan").attr("disabled", "disabled");
        event.preventDefault();
        var rownum = '<?php echo $rowNum ?>';
        var rowId = '<?php echo $empc->id ?>';
        var posName = jQuery("#posName").val().toUpperCase();
        var skNo = jQuery("#skNo").val().toUpperCase();
        var skDate = setTanggal(jQuery("#skDate").val());
        var topId = jQuery("#topId").val();
        var topName = jQuery("#topId option:selected").text().split("---").join("");
        
        var dirId = jQuery("#dirId").val();
        var dirName = jQuery("#dirId option:selected").text().split("---").join("");
        var divId = jQuery("#divId").val();
        var divName = jQuery("#divId option:selected").text().split("---").join("");
        var deptId = jQuery("#deptId").val();
        var deptName = jQuery("#deptId option:selected").text().split("---").join("");
        var unitId = jQuery("#unitId").val();
        var location = jQuery("#location").val();
    var locationName = jQuery("#location option:selected").text().split("---").join("");
        var rank = jQuery("#rank").val();
    var rankName = jQuery("#rank option:selected").text().split("---").join("");
        var grade = jQuery("#grade").val();
        var unitName = jQuery("#unitId option:selected").text().split("---").join("");
        var startDate = setTanggal(jQuery("#startDate").val());
        var endDate = setTanggal(jQuery("#endDate").val());
        var remark = jQuery("#remark").val();
        var status = jQuery("input[name='status']:checked").val();
        var leaderId = jQuery("#leaderId").val();
        var admId = jQuery("#administrationId").val();
        var replId = jQuery("#replacementId").val();
    var repl2Id = jQuery("#replacement2Id").val();
    var provId = jQuery("#provId").val();
    var cityId = jQuery("#cityId").val();
    var lembur = jQuery("input[name='lembur']:checked").val();
    var payrollId = jQuery("#payrollId").val();
    var prosesId = jQuery("#prosesId").val();
    var groupId = jQuery("#groupId").val();
    var penilaianId = jQuery("#penilaianId").val();
    var shiftId = jQuery("#shiftId").val();
    var companyId = jQuery("#companyId").val();
     var kategori = jQuery("#kategori").val();
    var perdin = jQuery("#perdin").val();
    var obat = jQuery("#obat").val();
    var filename = jQuery("#filename").val();
    var managerId = jQuery("#managerId").val();

   
        parent.commitRow(rownum, rowId, posName, skNo, skDate, dirId, divId, deptId, unitId, startDate, endDate, remark, status, dirName, divName, deptName, unitName, location, rank, grade, leaderId, admId, replId, repl2Id, provId, cityId, rankName, locationName, lembur, payrollId, prosesId, groupId, penilaianId, shiftId, companyId, kategori, perdin, obat, topId, topName, filename, managerId);
        closeBox();
        return false;
      } else {
        event.preventDefault();
        return false;
      }

      event.preventDefault();
      return false;
    });


  });

  function saveRow() {
  }
</script>


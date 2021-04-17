<?php
session_start();
if (!isset($menuAccess[$s]["add"]) && !isset($menuAccess[$s]["edit"])) {
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
$empc = new EmpPlaf("C");
$empc->id = $_GET["id"];
$empc->plafonId = $_GET["r1"];
$empc->satuanId = $_GET["r2"];
$empc->plafonValue = $_GET["r3"];
$empc->satuanPos = $_GET["r4"];
$empc->remark = $_GET["r5"];
$empc->mulai = $_GET["r6"];
$empc->selesai = $_GET["r7"];
$empc->toleransi = $_GET["r8"];

$cutil = new Common();
$ui = new UIHelper();
$disabled = "disabled=disabled";
if (isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"])) {
  $disabled = "";
}
$__validate["formid"] = "jabForm";
$__validate["items"] = array(
//    "skNo" => array("rule" => "required", "msg" => "Field Nomor SK harus diisi.."),
    "plafonId" => array("rule" => "required", "msg" => "Field Fasilitas harus diisi.."),
    "satuanId" => array("rule" => "required", "msg" => "Field Satuan harus diisi.."),
//    "divId" => array("rule" => "required", "msg" => "Field Divisi harus diisi.."),
//    "endDate" => array("rule" => "required", "msg" => "Field Tanggal Akhir harus diisi.."),
//    "startDate" => array("rule" => "required", "msg" => "Field Tanggal Mulai harus diisi.."),
//    "skDate" => array("rule" => "required", "msg" => "Field Tanggal SK harus diisi.."),
);
#KONTRAK = 584
require_once HOME_DIR . "/tmpl/__header__.php";
?>
<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">Data Fasilitas</h1>
    <?= getBread(ucwords(str_replace("fas", "", $mode) . " data Fasilitas")) ?>
    <span class="pagedesc">&nbsp;</span>
  </div>
  <div id="contentwrapper" class="contentwrapper" style="margin-top: -30px;">
    <form id="jabForm" class="stdform">
      <div class="widgetbox">
       
		<p id="p0">
			<label class="l-input-small">Fasilitas</label>
			<span class="fieldB">
			  <?php
			  $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='MD26' order by kodeInduk,urutanData";
			  echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "plafonId", $empc->plafonId, "", " $disabled class='single-deselect-td'");
			  ?>
			</span>
		</p>
						
		<p id="p0">			
			<label class="l-input-small">Jumlah</label>
            <span class="fieldB">
            <input type="text" id="plafonValue" name="plafonValue" class="smallinput mnum0" style="width:100px;" value="<?php echo $empc->plafonValue?>" />
            </span>			
		</p>
		
		<p>
			<label class="l-input-small">Satuan&nbsp;&nbsp;</label>
			<span class="fieldB">
			  <?php
			  $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='MD27' order by kodeInduk,urutanData";
			  echo $cutil->generateSelectWithEmptyOption($sql, "id", "description", "satuanId", $empc->satuanId, "", " $disabled class='single-deselect-td'");
			  ?>
			</span>
		</p>
		
		<?php
		  if ($empc->satuanPos == "" || $empc->satuanPos == "d") {
			$na = "checked='checked'";
		  } else {
			$pe = "checked='checked'";
		  }
		  ?>
		<p id="p0">
			<label class="l-input-small">Posisi Sat.</label>
			<span class="fieldB">
			  <input type="radio" id="sta_0" name="satuanPos" value="d" <?= $na ?>/> <span class="sradio">Depan</span>
			  <input type="radio" id="sta_1" name="satuanPos" value="b" <?= $pe ?>/> <span class="sradio">Belakang</span>
			  </span>
		</p>
       	&nbsp;
		
		<p>
			<label class="l-input-small">Masa Berlaku</label>
			<div class="field">
				<input type="text" id="mulai" name="mulai" size="10" maxlength="10" value="<?php echo $empc->mulai?>" class="vsmallinput hasDatePicker2" /> s.d <input type="text" id="selesai" name="selesai" size="10" maxlength="10" value="<?php echo $empc->selesai?>" class="vsmallinput hasDatePicker2" /> 
			</div>
		</p>
		<p id="p0">			
			<label class="l-input-small">Tenggat Waktu</label>
            <span class="fieldB">
            <input type="text" id="toleransi" name="toleransi" class="smallinput mnum0" style="width:100px;" value="<?php echo $empc->toleransi?>" />
            </span>			
		</p>
        <p id="p0"><?= $ui->createLabelSpanInputAttr("Keterangan", $empc->remark, "remark", "remark", "mediuminput", "") ?></p>
		<p>
          <input type="submit" class="submit radius2 btn_save" id="btnSimpan" name="btnSimpan" value="Simpan"/>&nbsp;
          <input type="button" class="cancel radius2 btn_back" value="Batal" onclick="closeBox();"/>
        </p>
      </div>
     
       
      
    </form>
  </div>

</div>
<script type="text/javascript">
  jQuery(document).ready(function () {
    jQuery("#jabForm").validate().settings.ignore = [];

    jQuery(".hasDatePicker2").datepicker({
      dateFormat: "yy-mm-dd"
    });
    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '60%', search_contains: true});
    jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});

    jQuery(".mnum0").each(function () {
      jQuery(this).css("text-align", "right");
      jQuery(this).autoNumeric("init", {wEmpty: 'zero', aDec: '.', aSep: ',', mDec: 0, vMin: 0, vMax: 9999999999999999999999999999});
    });

    jQuery("#jabForm").submit(function (event) {
      if (jQuery("#jabForm").valid()) {
        jQuery(".mnum0").each(function (index) {
          var id = jQuery(this).attr('id');
          var c = jQuery("#" + id).autoNumeric('get');
          jQuery("#" + id).autoNumeric('destroy');
          jQuery("#" + id).val(c);
        });
        jQuery("#btnSimpan").attr("disabled", "disabled");
        event.preventDefault();
        var rownum = '<?php echo $rowNum ?>';
        var rowId = '<?php echo $empc->id ?>';
        var plafonId = jQuery("#plafonId").val();
        var satuanId = jQuery("#satuanId").val();
        var plafonValue = jQuery("#plafonValue").val();
        var plafName = jQuery("#plafonId option:selected").text().split("---").join("");
        var satName = jQuery("#satuanId option:selected").text().split("---").join("");
        var remark = jQuery("#remark").val();
		var mulai = jQuery("#mulai").val();
		var selesai = jQuery("#selesai").val();
		var toleransi = jQuery("#toleransi").val();
        var satPos = jQuery("input[name='satuanPos']:checked").val();
        parent.commitFasRow(rownum, rowId, plafonId, plafName, satuanId, satName, plafonValue, satPos, mulai, selesai, toleransi, remark);
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


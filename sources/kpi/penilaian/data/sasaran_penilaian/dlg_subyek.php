<?php
global $s, $par, $menuAccess, $arrTitle;

if (isset($_POST["btnSimpan"])) {
	switch ($par[mode]) {
		case "addSubyek":
		insertData();
		die();
		break;

		case "editSubyek":
		updateData();
		die();
		break;
	}
}

$sql = "SELECT * FROM pen_sasaran WHERE idSasaran = '$par[idSasaran]'";
$res = db($sql);
$r = mysql_fetch_array($res);

$kodePrespektif = getField("SELECT kodePrespektif FROM pen_setting_kode WHERE idKode = '$par[idKode]'");
$namaAspek = getField("SELECT namaAspek FROM pen_setting_aspek WHERE idAspek = '$par[idAspek]'");
$arrPrespektif = arrayQuery("SELECT idPrespektif, namaPrespektif FROM pen_setting_prespektif WHERE kodePrespektif = '$kodePrespektif'");
$arrKategori = arrayQuery("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PN06' AND statusData = 't' ORDER BY urutanData ASC");
setValidation("is_null", "inp[namaSasaran]", "anda harus mengisi pertanyaan");
echo getValidation();
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; <?= $namaAspek ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread("Add Pertanyaan") ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<p style="position: absolute; right: 10px; top: 10px">
				<input type="submit" class="submit radius2" id="btnSimpan_1" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="reCloseBox('1');"/>
			</p>
			<p>
				<label class="l-input-small">Pertanyaan</label>
				<div class="field">
					<input type="text" class="mediuminput" id="inp[namaSasaran]" name="inp[namaSasaran]" value="<?= $r[namaSasaran] ?>">
				</div>
			</p>
			<p>
				<label for="" class="l-input-small">Keterangan</label>
				<div class="field">
					<textarea class="mediuminput" id="inp[keteranganSasaran]" name="inp[keteranganSasaran]" style="height: 50px"><?= $r[keteranganSasaran] ?></textarea>
				</div>
			</p>
			<p>
				<label class="l-input-small">Prespektif</label>
				<div class="field fradio">
					<?php
					foreach($arrPrespektif as $key => $value){
						$checked = $r[idPrespektif] == $key ? "checked=\"checked\"" : "";
						?>
						<input type="radio" <?= $checked ?> name="inp[idPrespektif]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
					} 
					?>
				</div>
			</p>
			<p>
				<label class="l-input-small">Kategori</label>
				<div class="field fradio">
					<?php
					foreach($arrKategori as $key => $value){
						$checked = $r[kodeKategori] == $key ? "checked=\"checked\"" : "";
						?>
						<input type="radio" <?= $checked ?> name="inp[kodeKategori]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php 
					} 
					?>
				</div>
			</p>

			<div class="widgetbox" style="margin-bottom: -10px">
				<div class="title"><h3>Obyektif Pilihan</h3></div>
			</div>
			
			<div id="pos_r">
				<a href="#add" id="btnAdd" class="btn btn1 btn_document"><span>Tambah Pilihan</span></a>
			</div>

			<table id="dtPilihan" class="stdtable" >
				<thead>
					<tr>
						<th width="20">NO</th>
						<th width="20">BOBOT</th>
						<th width="200">PILIHAN</th>
						<th>KETERANGAN</th>
						<th width="80">KONTROL</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "SELECT * FROM pen_sasaran_detail WHERE idSasaran = '$par[idSasaran]'";
					$res = db($sql);
					$no = 0;
					while($r = mysql_fetch_array($res)){
						$no++;
						?>
						<tr id='trPilihan_<?= $no ?>'>
							<td width='20' align='right' style='vertical-align: middle;'><?= $no ?>.</td>
							<td width='20' align='right' style='vertical-align: middle;'><?= $r[bobotPilihan] ?></td>
							<td width='200' style='vertical-align: middle;'><?= $r[namaPilihan] ?></td>
							<td><?= $r[keteranganPilihan] ?></td>
							<td width='80' align='center' style='vertical-align: middle;'>
								<a href="#edit" onclick="removeRowFromButton(this); addNewRow('<?= $no ?>', '<?= $r[bobotPilihan] ?>', '<?= $r[namaPilihan] ?>', '<?= $r[keteranganPilihan] ?>')" class="edit" title="Edit Data"><span>Edit Data</span></a>
								<a href="#del" onclick="removeRowWithHeader('<?= $no ?>');" class="delete" title="Delete Data"><span>Delete Data</span></a>
							</td>
							<input type='hidden' id='dtlPilihanBobot_<?= $no ?>' name='dtlPilihanBobot[]' value='<?= $r[bobotPilihan] ?>'/>
							<input type='hidden' id='dtlPilihanPertanyaan_<?= $no ?>' name='dtlPilihanPertanyaan[]' value='<?= $r[namaPilihan] ?>'/>
							<input type='hidden' id='dtlPilihanKeterangan_<?= $no ?>' name='dtlPilihanKeterangan[]' value='<?= $r[keteranganPilihan] ?>'/>
						</tr>
						<?php 
					}
					?>
				</tbody>
			</table>

			<p style="margin-top: 20px;">
				<input type="submit" class="submit radius2" id="btnSimpan_2" name="btnSimpan" value="Save"/>
				<input type="button" class="cancel radius2" value="Cancel" onclick="reCloseBox('2');"/>
			</p>
		</form>
	</div>
</div>
<script type="text/javascript">
	var edited = false;
	jQuery(document).ready(function() {
		jQuery("#btnAdd").live('click', function(o){
			if(jQuery("#btnSimpanBaru").length == 0){
				addNewRow();
			}else{
				alert("Simpan data terlehib dahulu");
				jQuery("#btnSimpanBaru").focus(100);
			}
		});

		jQuery("input[id*=btnSimpan_]").live('click', function(o){
			if(jQuery("#btnSimpanBaru").length > 0){
				o.preventDefault();
				alert("Simpan data terlebih dahulu");
				jQuery("#btnSimpanBaru").focus(100);
			}
		});
	});
	function reCloseBox(simpanId){
		if(edited){
			if(confirm("Pilihan telah di ubah.\nPilih 'OK' untuk save data.")){
				jQuery("#btnSimpan_" + simpanId).click();
			}else{
				closeBox();
			}
		}else{
			closeBox();
		}
	}

	function addNewRow(count, bobot, pilihan, keterangan){
		edited = true;
		count = typeof count !== 'undefined' ? count : 0;
		bobot = typeof bobot !== 'undefined' ? bobot : "0";
		pilihan = typeof pilihan !== 'undefined' ? pilihan : "";
		keterangan = typeof keterangan !== 'undefined' ? keterangan : "";

		if(count == 0)
			var newCount = jQuery("#dtPilihan tbody tr[id*='trPilihan_']").length+1;
		else
			var newCount = count;

		var tmplHeader = "";
		if(jQuery("tr[id*='trHeader_']").length == 0){
			tmplHeader += "<tr id='trHeader_" + newCount + "'>";
			tmplHeader += "<td colspan='5'>:: DATA BARU ::</td>";
			tmplHeader += "</tr>";
		}

		var tmplRow = "";
		if(count == 0)
			tmplRow += "<tr id='trPilihan_" + newCount + "'>";
		tmplRow += "	<td width='20' align='right' style='vertical-align: middle;'>" + newCount + ".</td>";
		tmplRow += "	<td width='20' align='center' style='vertical-align: middle;'><input type='text' id='dtlPilihanBobot_" + newCount + "' name='dtlPilihanBobot[]' value='" + bobot + "' onkeyup='cekAngka(this);' style='text-align: right'/></td>";
		tmplRow += "	<td width='200' align='center' style='vertical-align: middle;'><input type='text' id='dtlPilihanPertanyaan_" + newCount + "' name='dtlPilihanPertanyaan[]' value='" + pilihan + "'/></td>";
		tmplRow += "	<td><textarea id='dtlPilihanKeterangan_" + newCount + "' name='dtlPilihanKeterangan[]' style='width: 96%'>" + keterangan + "</textarea></td>";
		tmplRow += "	<td width='80' align='center' style='vertical-align: middle;'><a href=\"#del\" onclick=\"removeRowWithHeader('" + newCount + "');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a></td>";
		if(count == 0)
			tmplRow += "</tr>";
		if(count == 0){
			jQuery("#dtPilihan tbody").append(tmplHeader);
			jQuery("#dtPilihan tbody").append(tmplRow);
		}else{
			jQuery("#dtPilihan tbody tr#trPilihan_" + newCount).append(tmplRow);
		}

		jQuery("#dtlPilihanBobot_" + newCount).focus(100);
		formatTableWithHeader();
	}

	function removeRowWithHeader(no){
		jQuery("#trHeader_" + no).remove();
		jQuery("#trPilihan_" + no).remove();
		jQuery("#trFooter").remove();

		formatTableWithHeader();
	}

	function formatTableWithHeader(){
		var tmplFooter = "";
		tmplFooter += "<tr id='trFooter'>";
		tmplFooter += "<td colspan='5' align='right'>";
		tmplFooter += "<a href='#' id='btnSimpanBaru' onclick='formatTable();' class='btn btn_orange btn_archive' style='text-decoration: none;'><span>Simpan data baru</span></a>";
		tmplFooter += "</td>";
		tmplFooter += "</tr>";

		if(jQuery("#trFooter").length > 0){
			jQuery("#trFooter").remove();
		}

		var count = 0;
		jQuery("#dtPilihan tbody tr[id*='trPilihan_']").each(function() {
			count++;
			jQuery(this).find("td:first").html(count + ".");
		});

		if(count > 0)
			jQuery("#dtPilihan tbody").append(tmplFooter);
	}

	function formatTable(){
		if(jQuery("tr[id*='trHeader_']").length > 0 || jQuery("#trFooter").length > 0){
			jQuery("tr[id*='trHeader_']").remove();
			jQuery("#trFooter").remove();
		}

		var count = 1;
		jQuery("#dtPilihan tbody tr[id*='trPilihan_']").each(function() {
			jQuery(this).find("td:first").html(count + ".");
			if(jQuery(this).find("td:nth-child(2) input[type='text']").length > 0 && jQuery(this).find("td:nth-child(3) input[type='text']").length > 0 && jQuery(this).find("td:nth-child(4) textarea").length > 0){
				var varBobot = jQuery(this).find("td:nth-child(2) input").val();
				var varPilihan = jQuery(this).find("td:nth-child(3) input").val();
				var varKeterangan = jQuery(this).find("td:nth-child(4) textarea").val();
				var varKontrol = jQuery(this).find("td:last a");
				varKontrol.attr('onclick', 'removeRow(this); formatTable();');

				var varEdit = '<a href="#edit" onclick="removeRowFromButton(this); addNewRow(\'' + count + '\', \'' + varBobot + '\', \'' + varPilihan + '\', \'' + varKeterangan + '\')" class="edit" title="Edit Data"><span>Edit Data</span></a>';

				jQuery(this).find("td:nth-child(2)").css('text-align', 'right').html(varBobot);
				jQuery(this).find("td:nth-child(3)").css('text-align', 'left').html(varPilihan);
				jQuery(this).find("td:nth-child(4)").html(varKeterangan);
				jQuery(this).find("td:last").html(varEdit);
				jQuery(this).find("td:last").append(varKontrol);

				var tmplRowX = "";
				tmplRowX += "<input type='hidden' id='dtlPilihanBobot_" + count + "' name='dtlPilihanBobot[]' value='" + varBobot + "'/>";
				tmplRowX += "<input type='hidden' id='dtlPilihanPertanyaan_" + count + "' name='dtlPilihanPertanyaan[]' value='" + varPilihan + "'/>";
				tmplRowX += "<input type='hidden' id='dtlPilihanKeterangan_" + count + "' name='dtlPilihanKeterangan[]' value='" + varKeterangan + "'/>";
				jQuery(this).append(tmplRowX);
			}

			count++;
		});
	}

	function removeRow(elm){
		jQuery(elm).parent().parent().remove(); 
	}

	function removeRowFromButton(elm){
		jQuery(elm).parent().parent().empty(); 
	}
</script>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idSasaran FROM pen_sasaran ORDER BY idSasaran DESC LIMIT 1")+1;
	$sql = "INSERT INTO pen_sasaran (idSasaran, kodeTipe, idKode, idAspek, idPrespektif, namaSasaran, keteranganSasaran, kodeKategori, createBy, createDate) VALUES ('$nextId', '$par[kodeTipe]', '$par[idKode]', '$par[idAspek]', '$inp[idPrespektif]', '$inp[namaSasaran]', '$inp[keteranganSasaran]', '$inp[kodeKategori]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	db($sql);

	$sql = "DELETE FROM pen_sasaran_detail WHERE idSasaran = '$nextId'";
	db($sql);

	$cm = 0;
	$dtlPilihan = $_POST['dtlPilihanPertanyaan'];
	foreach($dtlPilihan as $row){
		$dtlPilihanPertanyaan = $row;
		$dtlPilihanBobot = $_POST['dtlPilihanBobot'][$cm];
		$dtlPilihanKeterangan = $_POST['dtlPilihanKeterangan'][$cm];

		$sql = "INSERT INTO pen_sasaran_detail (idSasaran, kodeDetail, bobotPilihan, namaPilihan, keteranganPilihan, createBy, createDate) VALUES ('$nextId', '".($cm+1)."', '$dtlPilihanBobot', '$dtlPilihanPertanyaan', '$dtlPilihanKeterangan', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);

		$cm++;
	}

	echo "
	<script type='text/javascript'>
		closeBox();
		parent.window.location = 'index.php?par[mode]=edit".getPar($par, "mode")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$sql = "UPDATE pen_sasaran SET idPrespektif = '$inp[idPrespektif]', namaSasaran = '$inp[namaSasaran]', keteranganSasaran = '$inp[keteranganSasaran]', kodeKategori = '$inp[kodeKategori]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idSasaran = '$par[idSasaran]'";
	db($sql);

	$sql = "DELETE FROM pen_sasaran_detail WHERE idSasaran = '$par[idSasaran]'";
	db($sql);

	$cm = 0;
	$dtlPilihan = $_POST['dtlPilihanPertanyaan'];
	foreach($dtlPilihan as $row){
		$dtlPilihanPertanyaan = $row;
		$dtlPilihanBobot = $_POST['dtlPilihanBobot'][$cm];
		$dtlPilihanKeterangan = $_POST['dtlPilihanKeterangan'][$cm];

		$sql = "INSERT INTO pen_sasaran_detail (idSasaran, kodeDetail, bobotPilihan, namaPilihan, keteranganPilihan, createBy, createDate) VALUES ('$par[idSasaran]', '".($cm+1)."', '$dtlPilihanBobot', '$dtlPilihanPertanyaan', '$dtlPilihanKeterangan', '$cUsername', '".date("Y-m-d H:i:s")."');";
		db($sql);

		$cm++;
	}

	echo "
	<script type='text/javascript'>
		closeBox(); 
		parent.window.location = 'index.php?par[mode]=edit".getPar($par, "mode,idSasaran")."';
	</script>";
}
?>
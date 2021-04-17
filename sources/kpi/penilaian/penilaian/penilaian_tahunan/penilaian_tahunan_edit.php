<?php
global $s, $par, $menuAccess, $arrTitle, $cID;
if (isset($_POST["saveContent"])) {
	updateData($_POST['referer']);
	die();
}

$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi, '~', t2.kodeAspek) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi, $kodeAspek) = explode("~", $infoSetting);

$infoEmp = getField("SELECT CONCAT(t1.reg_no, '~', IFNULL(t1.pic_filename, ''), '~', t1.name, '~', t1.pos_name, '~', t2.namaData, '~', t3.namaData) FROM dta_pegawai t1 JOIN mst_data t2 ON t2.kodeData = t1.cat LEFT JOIN mst_data t3 ON t3.kodeData = t1.rank WHERE t1.id = '$par[idPegawai]'");
list($reg_no, $pic_filename, $name, $pos_name, $status, $posisi) = explode("~", $infoEmp);

$arrAspek = arrayQuery("SELECT idAspek, CONCAT(namaAspek, 'tabsplit\t', penilaianAspek) FROM pen_setting_aspek WHERE kodeAspek = '$kodeAspek' ORDER BY urutanAspek");

$sqlPenilaian = "SELECT * FROM pen_penilaian WHERE idPenilaian = '$par[idPenilaian]'";
$resPenilaian = db($sqlPenilaian);
$rPenilaian = mysql_fetch_array($resPenilaian);

$keys = array_keys($arrAspek);
if(!isset($par[tab]))
	$par[tab] = $keys[0];

$lock = "";
if(isset($par[dlg]))
	$lock = "disabled=\"disabled\"";
?>
<?php
if(!empty($par[dlg]))
	echo '<div class="contentpopup" style="margin-left: 10px">';
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s]." &raquo; ".$namaSetting ?></h1>
	<?php
	if(!empty($par[dlg]))
		echo '<div style="margin-top: 10px">';
	?>
	<?= getBread(ucwords($par[mode]." data")) ?>	
	<?php
	if(!empty($par[dlg]))
		echo '</div>';
	?>
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form class="stdform" >
		<p style="position: absolute; right: 20px; top: 10px;">
			<input type="button" class="cancel radius2" value="Cancel" onclick="<?= empty($par[dlg]) ? "window.location='?par[mode]=view".getPar($par, "mode,idPenilaian,tab")."';" : "closeBox();" ?>"/>
		</p>
		<fieldset>
			<?php 
            	$_SESSION["curr_emp_id"] = $par[idPegawai];	
            	require_once "tmpl/__emp_header__penilaian.php";
            ?>
		</fieldset>
	</form>

	<ul class="hornav" style="margin:10px 0px !important;">
		<?php
		foreach($arrAspek as $key => $val){
			list($namaAspek, $penilaianAspek) = explode("tabsplit\t", $val);
			if($key == $par[tab])
				echo "<li class=\"current\"><a href=\"#tab_$key\">$namaAspek</a></li>";
			else
				echo "<li><a href=\"#tab_$key\">$namaAspek</a></li>";
		}
		?>
	</ul>

	<?php
	foreach($arrAspek as $key => $val){
		list($namaAspek, $penilaianAspek) = explode("tabsplit\t", $val);
		?>
		<div class="subcontent" id="tab_<?= $key ?>" style="border-radius:0; display: <?= $key != $par[tab] ? "none" : "block" ?>;">
			<form id="form_<?= $key ?>" action="index.php?par[tab]=<?= $key.getPar($par, "tab") ?>" class="stdform" method="POST">
				<input type="hidden" name="inp[idAspek]" value="<?= $key ?>">
				<table width="100%">
					<tr>
						<td>
							<p>
								<label class="l-input-small">Penilaian Aspek</label>
								<span class="field" id="displayTotalSubyek_<?= $key ?>">
									<?= $penilaianAspek ? $penilaianAspek : "-" ?>&nbsp;
								</span>
							</p>
						</td>
						<?php
						if(empty($par[dlg])){
							?>
							<td align="right" width="25%">
								<input type="submit" class="submit radius2" name="saveContent" value="Save"/>
							</td>
							<?php
						}
						?>
					</tr>
				</table>
				<table id="dtAspek_<?= $key ?>" class="stdtable" style="margin-top: 10px">
					<thead>
						<tr>
							<th width="20" style="vertical-align: middle;">NO</th>
							<th style="vertical-align: middle;">ASPEK PENILAIAN</th>
							<th width="120" colspan="2">20</th>
							<th width="120" colspan="2">40</th>
							<th width="120" colspan="2">60</th>
							<th width="120" colspan="2">80</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = "SELECT * FROM pen_sasaran WHERE kodeTipe = '$par[kodeTipe]' AND idAspek = '$key'";
						$res = db($sql);
						$no = 0;
						while($r = mysql_fetch_array($res)){
							$no++;
							$arrPilihan = arrayQuery("SELECT kodeDetail, namaPilihan FROM pen_sasaran_detail WHERE idSasaran = '$r[idSasaran]' ORDER BY bobotPilihan");
							// echo $r[idSasaran];
							$checkedValue = getField("SELECT idPilihan FROM pen_penilaian_detail WHERE idPenilaian = '$par[idPenilaian]' AND idAspek = '$key' AND idSasaran = '$r[idSasaran]'");
							?>
							<tr>
								<input type="hidden" id="dtlSasaranId_<?= $no-1 ?>" name="dtlSasaranIds[]" value="<?= $r[idSasaran] ?>">
								<td width="20" align="right"><?= $no ?>.</td>
								<td><?= $r[namaSasaran] ?></td>
								<?php
								foreach($arrPilihan as $kodeDetail => $namaPilihan){
									?>
									<td width="120"><?= $namaPilihan ?></td>
									<td width="10" align="center"><input type="radio" id="dtlSasaran_<?= $r[idSasaran] ?>_<?= $kodeDetail ?>" name="dtlSasaran_<?= $r[idSasaran] ?>" value="<?= $kodeDetail ?>" <?= $checkedValue == $kodeDetail ? "checked=\"checked\"" : "" ?> <?= $lock ?>></td>
									<?php
								}
								?>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</form>
		</div>
		<?php
	}
	?>
	<br clear="all">
	<a href="#prev" id="btnPrev" class="btn btn1" style="background-image: none;"><span style="margin-left: 0px;">&laquo; Previous</span></a>
	<a href="#next" id="btnNext" class="btn btn1" style="float: right; background-image: none;"><span style="margin-left: 0px;">Next &raquo;</span></a>
	<br clear="all">
	<br clear="all">

	<form class="stdform">
		<div class="widgetbox" style="margin-bottom: -10px">
			<div class="title"><h3>SARAN &amp; Kesimpulan</h3></div>
		</div>
		<textarea class="longinput" name="inp[saranPenilaian]" id="inp[saranPenilaian]" style="width: 98.7%" rows="5" <?= $lock ?>><?= $rPenilaian[saranPenilaian] ?></textarea>
	</form>
</div>
<?php
if(!empty($par[dlg]))
	echo '</div>';
?>
<script type="text/javascript">
	var oldTabKey = "#tab_<?= $par[tab] ?>";
	var currentTabKey = "#tab_<?= $par[tab] ?>";
	var nextTabKey = "#tab_<?= $keys[1] ? $keys[1] : $keys[0] ?>";

	jQuery(document).ready(function() {
		jQuery("form[id*=form_]").on('submit', function(e){
			e.preventDefault();
			save(jQuery(this).attr("id"));
		});

		jQuery(".hornav li a").click(function(){
			var tempOldTabKey = jQuery(this).parent().prev().find("a").attr("href");
			tempOldTabKey = typeof tempOldTabKey !== 'undefined' ? tempOldTabKey : "#tab_<?= end($keys) ?>";
			oldTabKey = tempOldTabKey;

			currentTabKey = jQuery(this).attr("href");

			var tempNextTabKey = jQuery(this).parent().next().find("a").attr("href");
			tempNextTabKey = typeof tempNextTabKey !== 'undefined' ? tempNextTabKey : "#tab_<?= $keys[0] ?>";
			nextTabKey = tempNextTabKey;
		});

		jQuery("#btnPrev").live("click", function(e){
			<?php
			if(empty($par[dlg])){
				?>
				save("form_" + currentTabKey.split("_")[1]);
				<?php
			}
			?>
			if(oldTabKey != currentTabKey){
				jQuery(".hornav li a[href='" + oldTabKey + "']").click();
			}
		});

		jQuery("#btnNext").live("click", function(e){
			<?php
			if(empty($par[dlg])){
				?>
				save("form_" + currentTabKey.split("_")[1]);
				<?php
			}
			?>
			if(currentTabKey != "#tab_<?= end($keys) ?>"){
				jQuery(".hornav li a[href='" + nextTabKey + "']").click();
			}
		});
	});

	function save(formId){
		// console.log(jQuery("#" + formId).attr("action"));
		// return;
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.onreadystatechange = function () {
			if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				if (xmlHttp.responseText)
					alert(xmlHttp.responseText);
			}
		}
		xmlHttp.open("POST", jQuery("#" + formId).attr("action").replace("index.php", "ajax.php"), true);
		xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
		var formData = new FormData();
		formData.append("saveContent", "saveContent");
		formData.append("referer", "popup");
		formData.append("inp[idAspek]", formId.split("_")[1]);
		formData.append("inp[saranPenilaian]", jQuery("#inp\\[saranPenilaian\\]").val());

		jQuery("input[id*='dtlSasaranId_']", "#" + formId).each(function(index, el) {
			formData.append("dtlSasaranIds[]", jQuery(this).val());
			var checked = jQuery('[name="dtlSasaran_' + jQuery(this).val() + '"]:checked');
			if (checked.length > 0){
				formData.append("dtlSasaran_" + jQuery(this).val(), checked.val());

			}
		});
		xmlHttp.send(formData);
	}
</script>
<?php 
function updateData($referer = ""){
	global $s,$inp,$par,$cUsername,$cID;			
	repField();

	$isExist = getField("SELECT idPenilaian FROM pen_penilaian WHERE idSetting = '$par[idSetting]' AND idPegawai = '$par[idPegawai]'");

	$nextId = "1";
	if(!$isExist){
		$nextId = getField("SELECT idPenilaian FROM pen_penilaian ORDER BY idPenilaian DESC LIMIT 1")+1;
		$sql = "INSERT INTO pen_penilaian(idPenilaian, idSetting, idPenilai, idPegawai, kodeTipe, saranPenilaian, tglPenilaian, createBy, createDate) VALUES ('$nextId', '$par[idSetting]', '$cID', '$par[idPegawai]', '$par[kodeTipe]', '$inp[saranPenilaian]', '".date("Y-m-d")."', '$cUsername', '".date("Y-m-d H:i:s")."');";
	}else{
		$nextId = $isExist;
		$sql = "UPDATE pen_penilaian SET kodeTipe = '$par[kodeTipe]', saranPenilaian = '$inp[saranPenilaian]', tglPenilaian = '".date("Y-m-d")."', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idPenilaian = '$nextId'";
	}
	// echo $sql."\n";
	db($sql);

	$sql = "DELETE FROM pen_penilaian_detail WHERE idPenilaian = '$nextId' AND idAspek = '$inp[idAspek]'";
	// echo $sql."\n";
	db($sql);

	$cm = 1;
	$dtlSasaranIds = $_POST['dtlSasaranIds'];
	foreach($dtlSasaranIds as $dtlSasaranId){
		$dtlSasaranChecked = $_POST['dtlSasaran_'.$dtlSasaranId];
		$subNilai = getField("SELECT bobotPilihan FROM pen_sasaran_detail WHERE idSasaran = '$dtlSasaranId' AND kodeDetail = '$dtlSasaranChecked'");

		$sql = "INSERT INTO pen_penilaian_detail (idPenilaian, kodeDetail, idAspek, idSasaran, idPilihan, bobotPilihan, createBy, createDate) VALUES ('$nextId', '$cm', '$inp[idAspek]', '$dtlSasaranId', '$dtlSasaranChecked', '$subNilai', '$cUsername', '".date("Y-m-d H:i:s")."');";
		// echo $sql."\n";
		db($sql);

		$cm++;
	}

	$sql = "UPDATE pen_penilaian SET nilaiPenilaian = (SELECT AVG(bobotPilihan) FROM pen_penilaian_detail WHERE idPenilaian = '$nextId') WHERE idPenilaian = '$nextId'";
	// echo $sql."\n";
	db($sql);

	echo "DATA TELAH DISIMPAN";

	if(empty($referer)){
		echo "
		<script type=\"text/javascript\">
			reloadPage();
		</script>
		";
	}
}

/* End of file penilaian_tahunan_edit.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_edit.php */
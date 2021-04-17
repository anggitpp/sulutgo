<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json, $cNama;

if(!isset($par[idSetting]))
	$par[idSetting] = getField("SELECT idSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai LIMIT 1");

$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeAspek) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeAspek) = explode("~", $infoSetting);

$arrTipe = arrayQuery("SELECT CONCAT(kodeTipe, 'tabsplit\t', namaTipe) FROM pen_tipe WHERE statusTipe = 't' ORDER BY urutanTipe");
$arrAspek = arrayQuery("SELECT CONCAT(idAspek, 'tabsplit\t', namaAspek, 'tabsplit\t', targetAspek) FROM pen_setting_aspek WHERE kodeAspek = '$kodeAspek' AND statusAspek = 't' ORDER BY urutanAspek");
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par, "mode,dlg") ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
		<p style="position: absolute; right: 20px; top: 127px;">
			<!-- <a href="#export" id="btnExport" class="btn btn1 btn_inboxo"><span>PDF</span></a> &nbsp;  --><?= comboData("SELECT idSetting, namaSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai", "idSetting", "namaSetting", "par[idSetting]", "", $par[idSetting], "onchange=\"document.form.submit();\"", "225px"); ?>
		</p>
		<p>
			<label class="l-input-small">Penilaian</label>
			<span class="field">
				<span id="displayNamaSetting"><?= $namaSetting ?></span>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">Pelaksanaan</label>
			<span class="field">
				<span id="displayTanggalPelaksanaan"><?= getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai) ?></span>&nbsp;
			</span>
		</p>

		<br clear="all">
		
		<div class="subcontent" style="border-radius:0; display: block;">
			<div id="divChartFirst" align="center"></div>
		</div>

		<?php
		foreach($arrAspek as $objAspek){
			list($idAspek, $namaAspek, $targetAspek) = explode("tabsplit\t", $objAspek);
			$tempNilai = getField("SELECT IFNULL(AVG(t1.bobotPilihan), 0.00) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian WHERE t2.idSetting = '$par[idSetting]' AND t1.idAspek = '$idAspek' AND t2.idPenilai = '$cID'");
			?>
			<div class="widgetbox" style="margin-bottom: -10px">
				<div class="title"><h3><?= $namaAspek ?></h3></div>
			</div>
			<p>
				<label class="l-input-small">Target Korporat</label>
				<span class="field">
					<?= getAngka($targetAspek, 2) ?>&nbsp;
				</span>
			</p>
			<p>
				<label class="l-input-small">Hasil</label>
				<span class="field">
					<?= getAngka($tempNilai, 2) ?>&nbsp;
				</span>
			</p>
			<div class="subcontent" style="border-radius:0; display: block;">
				<div id="divChartSecond_<?= $idAspek ?>" align="center"></div>
			</div>
			<?php 
		}
		?>
	</form>
</div>
<script type="text/javascript">
	function generateFirstChart(){
		var tmplChart = '';

		tmplChart += '<chart plotGradientColor=" " caption="Rekap Berdasarkan Aspek Penilaian" yaxisname="Nilai" bgcolor="FFFFFF" numberprefix="" yaxismaxvalue="100" showborder="0" theme="fint" showplotborder="0" showcanvasborder="0" >';
		<?php
		foreach($arrAspek as $objAspek){
			list($idAspek, $namaAspek, $targetAspek) = explode("tabsplit\t", $objAspek);
			$tempNilai = getField("SELECT AVG(t1.bobotPilihan) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian WHERE t2.idSetting = '$par[idSetting]' AND t1.idAspek = $idAspek AND t2.idPenilai = '$cID'");
			?>
			tmplChart +='		<set label="<?= $namaAspek ?>" value="<?= $tempNilai ?>" tooltext="<?= $namaAspek ?>{br}Nilai rata-rata: <?= getAngka($tempNilai, 2) ?>" />';
			<?php
		}
		?>
		tmplChart += '</chart>';
		return tmplChart;
	}
	<?php
	function generateSecondChart($namaAspek, $idAspek){
		global $par, $arrTipe, $cID;
		$tmplChart = '';
		$tmplChart .= '<chart plotGradientColor=" " caption="Rekap Aspek ' . $namaAspek . '" yaxisname="Nilai" bgcolor="FFFFFF" numberprefix="" yaxismaxvalue="100" showborder="0" theme="fint" showplotborder="0" showcanvasborder="0" >';

		foreach($arrTipe as $objTipe){
			list($kodeTipe, $namaTipe) = explode("tabsplit\t", $objTipe);
			$tempNilai = getField("SELECT IFNULL(AVG(t1.bobotPilihan), 0.00) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian JOIN pen_pegawai t3 ON t3.idPegawai = t2.idPegawai WHERE t2.idSetting = '$par[idSetting]' AND t2.idPenilai = '$cID' AND t1.idAspek = '$idAspek' AND t3.tipePenilaian = '$kodeTipe'");
			$tmplChart .='		<set label="'.$namaTipe.'" value="'.$tempNilai.'" tooltext="'.$namaTipe.'{br}Nilai rata-rata: '.getAngka($tempNilai, 2).'" />';

		}

		$tmplChart .= '</chart>';
		return $tmplChart;
	}
	?>

	var chart = new FusionCharts("Column2D", "chartFirst", "100%", "300");
	chart.setXMLData( generateFirstChart() );
	chart.render("divChartFirst");

	<?php
	foreach($arrAspek as $objAspek){
		list($idAspek, $namaAspek, $targetAspek) = explode("tabsplit\t", $objAspek);
		?>
		var chart = new FusionCharts("Column2D", "chartSecond_<?= $idAspek ?>", "100%", "300");
		chart.setXMLData( '<?= generateSecondChart($namaAspek, $idAspek) ?>');
		chart.render("divChartSecond_<?= $idAspek ?>");
		<?php
	}
	?>
</script>
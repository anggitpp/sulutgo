<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json, $cNama;

if(!isset($par[idSetting]))
	$par[idSetting] = getField("SELECT idSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai LIMIT 1");

$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeAspek) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeAspek) = explode("~", $infoSetting);

$arrTipe = arrayQuery("SELECT CONCAT(kodeTipe, 'tabsplit\t', namaTipe) FROM pen_tipe WHERE statusTipe = 't' ORDER BY urutanTipe");
$arrKategoriSasaran = arrayQuery("SELECT CONCAT(kodeData, 'tabsplit\t', namaData) FROM mst_data WHERE kodeCategory = 'PN06' AND statusData = 't' ORDER BY urutanData"); 
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
		
	</form>
</div>
<script type="text/javascript">
	var chart = new FusionCharts("MSColumn2D", "chartFirst", "100%", "300");
	chart.setXMLData( '<?= generateFirstChart() ?>');
	chart.render("divChartFirst");
</script>
<?php
function generateFirstChart(){
	global $par, $s, $arrTitle, $arrTipe, $arrKategoriSasaran;

	$tmplChart = "";
	$tmplChart  .= "<chart yaxisname=\"Nilai\" caption=\"".$arrTitle[$s]."\" numberprefix=\"\" plotgradientcolor=\"\" bgcolor=\"FFFFFF\" showalternatehgridcolor=\"0\" divlinecolor=\"CCCCCC\" showvalues=\"0\" showcanvasborder=\"0\" canvasborderalpha=\"0\" canvasbordercolor=\"CCCCCC\" canvasborderthickness=\"1\" yaxismaxvalue=\"100\" captionpadding=\"30\" yaxisvaluespadding=\"15\" legendshadow=\"0\" legendborderalpha=\"0\" palettecolors=\"#f8bd19,#008ee4,#33bdda,#e44a00,#6baa01,#583e78\" showplotborder=\"0\" showborder=\"0\">";

	$tmplChart .= "		<categories>";
	foreach($arrTipe as $objTipe){
		list($kodeTipe, $namaTipe) = explode("tabsplit\t", $objTipe);
		$tmplChart .= "		<category label=\"$namaTipe\" />";
	}
	$tmplChart .= "		</categories>";

	foreach($arrKategoriSasaran as $objKategoriSasaran){
		list($kodeData, $namaData) = explode("tabsplit\t", $objKategoriSasaran);
		$tmplChart .= "<dataset seriesname=\"$namaData\">";
		foreach($arrTipe as $objTipe){
			list($kodeTipe, $namaTipe) = explode("tabsplit\t", $objTipe);
			$tempNilai = getField("SELECT IFNULL(AVG(t1.bobotPilihan), 0.00) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian JOIN pen_pegawai t3 ON t3.idPegawai = t2.idPegawai JOIN pen_sasaran t4 ON t4.idSasaran = t1.idSasaran WHERE t2.idSetting = '$par[idSetting]'AND t3.tipePenilaian = '$kodeTipe' AND t4.kodeKategori = '$kodeData'");

			$tmplChart .= "		<set value=\"".getAngka($tempNilai, 2)."\" />";
		}
		$tmplChart .= "</dataset>";
	}

	$tmplChart .= "</chart>";
	return $tmplChart;
}

/* End of file komposisi_view.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/laporan/komposisi/komposisi_view.php */
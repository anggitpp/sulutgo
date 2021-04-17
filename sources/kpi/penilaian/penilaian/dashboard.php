<?php
global $s, $par, $menuAccess, $cID;

if(!isset($par[idSetting]))
	$par[idSetting] = getField("SELECT idSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai LIMIT 1");

$infoEmp = getField("SELECT CONCAT(t1.reg_no, '~', IFNULL(t1.pic_filename, ''), '~', t1.name, '~', IFNULL(t1.pos_name, '-'), '~', IFNULL(t2.namaData, '-'), '~', IFNULL(t3.namaData, '-'), '~', IFNULL(t5.kodeTipe, '')) FROM dta_pegawai t1 JOIN mst_data t2 ON t2.kodeData = t1.cat LEFT JOIN mst_data t3 ON t3.kodeData = t1.rank LEFT JOIN pen_pegawai t4 ON t4.idPegawai = t1.id LEFT JOIN pen_tipe t5 ON t5.kodeTipe = t4.tipePenilaian WHERE t1.id = '$cID'");
list($reg_no, $pic_filename, $name, $pos_name, $status, $posisi, $kodeTipe) = explode("~", $infoEmp);

$finalNilai = 0;
$subNilai = 0;
$subNo = 0;

$sql = "SELECT t4.name namaAtasan, t1.saranPenilaian, t1.tglPenilaian, t1.idPenilaian, t1.idSetting, t2.namaSetting, t3.kodeAspek, t3.kodePrespektif, t3.kodeKonversi FROM pen_penilaian t1 JOIN pen_setting_penilaian t2 ON t2.idSetting = t1.idSetting JOIN pen_setting_kode t3 ON t3.idKode = t2.idKode LEFT JOIN dta_pegawai t4 ON t4.id = t1.idPenilai WHERE t1.idPegawai = '$cID' AND t1.idSetting = '$par[idSetting]'";
$res = db($sql);
$r = mysql_fetch_array($res);

$arrAspek = arrayQuery("SELECT CONCAT(t3.idAspek, '~', t3.namaAspek) FROM pen_penilaian t1 LEFT JOIN pen_penilaian_detail t2 ON t2.idPenilaian = t1.idPenilaian LEFT JOIN pen_setting_aspek t3 ON t3.idAspek = t2.idAspek WHERE t1.idPegawai = '$cID' GROUP BY t3.namaAspek");

foreach($arrAspek as $aspek){
	if($aspek != NULL){
		list($idAspek, $namaAspek) = explode("~", $aspek);
		$tempIdAspek = getField("SELECT t2.idAspek FROM pen_penilaian_detail t1 JOIN pen_setting_aspek t2 ON t2.idAspek = t1.idAspek WHERE t2.namaAspek = '$namaAspek' AND t1.idPenilaian = '$r[idPenilaian]'");
		$tempNilai = getField("SELECT AVG(bobotPilihan) FROM pen_penilaian_detail WHERE idPenilaian = '$r[idPenilaian]' AND idAspek = '$tempIdAspek'");
		if(!empty($tempNilai)){
			$subNo++;
			$subNilai += $tempNilai;
			$tempNilai = getAngka($tempNilai, 2);
		}
	}
}

if($subNo > 0){
	$finalNilai = $subNilai / $subNo;
	$finalNilai = getAngka($finalNilai, 2);
}else{
	$finalNilai = "0.00";
}

$arrKonversi = arrayQuery("SELECT CONCAT(nilaiMin, '~', nilaiMax, '~', warnaKonversi) FROM pen_setting_konversi WHERE kodeKonversi = '$r[kodeKonversi]' ORDER BY nilaiMin ASC");
$targetKorporat = getField("SELECT ((SELECT AVG(targetAspek) FROM pen_setting_aspek WHERE kodeAspek = '$r[kodeAspek]')+(SELECT AVG(targetPrespektif) FROM pen_setting_prespektif WHERE kodePrespektif = '$r[kodePrespektif]')) / 2");

$years = range(date("Y", strtotime("-5 YEARS")), date("Y"));
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<?php
	if(!empty($infoEmp)){
		?>
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
			<p style="position: absolute; right: 20px; top: 10px;">
				<?= comboData("SELECT idSetting, namaSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai", "idSetting", "namaSetting", "par[idSetting]", "", $par[idSetting], "onchange='document.form.submit();'", "225px"); ?>
			</p>
			<?php
			if($cID != "0"){
				?>
				<fieldset>
					<table style="width: 100%">
						<tr>
							<td style="width: 10%; padding-right: 10px; padding-top: 5px;">
								<img alt="<?= $reg_no ?>" width="100%" height="105px" src="<?= APP_URL . "/files/emp/pic/" . (empty($pic_filename) ? "nophoto.jpg" : $pic_filename) ?>" >
							</td>
							<td style="width: 80%;vertical-align: top;">
								<div class="widgetbox" style="margin-bottom: 0px;">
									<table style="width: 100%;padding-top: -5px;" >
										<tr>
											<td style="padding-right: 5px;">
												<p>
													<label class="l-input-small">Nama</label>
													<span class="field">
														<?= $name ?>&nbsp;
													</span>
												</p>
												<p>
													<label class="l-input-small">NPP</label>
													<span class="field">
														<?= $reg_no ?>&nbsp;
													</span>
												</p>
												<p>
													<label class="l-input-small">Jabatan</label>
													<span class="field">
														<?= $pos_name ?>&nbsp;
													</span>
												</p>
											</td>
											<td style="width: 350px;">
												<p>
													<label class="l-input-small">Posisi</label>
													<span class="field">
														<?= $posisi ?>&nbsp;
													</span>
												</p>
												<p>
													<label class="l-input-small">Status</label>
													<span class="field">
														<?= $status ?>&nbsp;
													</span>
												</p>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</fieldset>
				<?php
			}
			?>
		</form>
		<?php
		if(count($arrAspek) >= 1){
			?>
			<br clear="all">
			<div class="one_fourth">
				<div class="ucup-box2 allports" style="width: 100%; height: 175px;">
					<div class="ucup-box2-content" style="padding-top: 30px;">
						<p class="ucup-box2-number" style="font-size: 45pt; margin-bottom: 35px;"><?= $finalNilai ?></p>
						<p class="ucup-box2-description" style="font-size: 10pt;">TARGET KORPORAT</p>
						<p class="ucup-box2-number" style="font-size: 25pt; margin-top: -5px;"><?= getAngka($targetKorporat, 2) ?></p>
					</div>
				</div>
			</div>
			<div class="three_fourth" style="margin-right: 0; float: right !important">
				<?php
				$no = 0;
				foreach($arrAspek as $aspek){
					if($aspek != NULL){
						if($no == 3)
							echo "<br clear=\"all\">";
						list($idAspek, $namaAspek) = explode("~", $aspek);
						echo "<div id=\"divGaugeAspek_$idAspek\" style=\"width: 30.333333333%; float: left; margin-left: 11.25px; margin-right: 11.25px;\"></div>";	
						$no++;
					}
				}
				?>
			</div>
			<br clear="all">
			<br clear="all">
			<form class="stdform">
				<div class="widgetbox" style="margin-bottom: -10px">
					<div class="title"><h3>SARAN &amp; Kesimpulan</h3></div>
				</div>
				<textarea class="longinput" name="inp[saranPenilaian]" id="inp[saranPenilaian]" style="width: 98.7%" rows="5" disabled="disabled"><?= $r[saranPenilaian] ?></textarea>
				<p>Nama Atasan: <?= $r[namaAtasan] ?>, Penilaian Tanggal: <?= getTanggal($r[tglPenilaian]) ?></p>
			</form>
			<div class="widgetbox" style="margin-bottom: -10px">
				<div class="title"><h3>Penilaian Tahunan</h3></div>
			</div>
			<div class="subcontent" style="border-radius:0; display: block;">
				<div id="divLineTahunan" align="center"></div>
			</div>
			<br clear="all">
			<ul class="hornav" style="margin:10px 0px !important;">
				<?php
				$no = 0;
				foreach($arrAspek as $aspek){
					if($aspek != NULL){
						list($idAspek, $namaAspek) = explode("~", $aspek);
						echo "<li ".($no == 0 ? "class='current'" : "")."><a href=\"#tab_$idAspek\">$namaAspek</a></li>";

						$no++;
					}
				}
				?>
			</ul>

			<?php
			$no = 0;
			foreach($arrAspek as $aspek){
				if($aspek != NULL){
					list($idAspek, $namaAspek) = explode("~", $aspek);
					?>
					<div class="subcontent" id="tab_<?= $idAspek ?>" style="border-radius:0; display: <?= $no > 0 ? "none" : "block" ?>;">
						<div id="divColumn2DAspek_<?= $idAspek ?>" align="center"></div>
					</div>
					<?php
					$no++;
				}
			}
			?>
		</div>
		<script type="text/javascript">

			function generateGaugeAspekChart(lowerLimit, upperLimit, currentValue, labelValue){
				var tmplChart = '';
				tmplChart += '<chart manageResize="1" origW="300" origH="175" palette="2" bgColor="999999,EEEEEE" lowerLimit="' + lowerLimit + '" upperLimit="' + upperLimit + '" showBorder="1" borderColor="888888" basefontColor="000000" chartBottomMargin="40" chartRightMargin="10" pivotFillMix="CCCCCC,000000" showPivotBorder="1" pivotBorderColor="000000">';
				tmplChart += '	<colorRange>';
				<?php
				foreach($arrKonversi as $konversi){
					list($nilaiMin, $nilaiMax, $warnaKonversi) = explode("~", $konversi);
					?>
					tmplChart += '			<color minValue="<?= $nilaiMin ?>" maxValue="<?= $nilaiMax ?>" code="<?= $warnaKonversi ?>"/>';
					<?php
				}
				?>
				tmplChart += '	</colorRange>';

				tmplChart += '<dials>';
				tmplChart += '		<dial value="' + currentValue + '" bgColor="000000" rearExtension="15" baseWidth="10"/>';
				tmplChart += '</dials>';

				tmplChart += '<annotations>';
				tmplChart += '		<annotationGroup x="143" y="50" showBelow="0" scaleText="1">';
				tmplChart += '			<annotation type="text" y="155" label="' + labelValue + '" fontColor="000000" fontSize="15" bold="1"/>';
				tmplChart += '		</annotationGroup>';
				tmplChart += '</annotations>';

				tmplChart += '</chart>';

				return tmplChart;
			}

			function generate2DColumnAspek(captionValue, yMaxValue, dataCollections) {
				var tmplChart = '';

				tmplChart += '<chart plotGradientColor=" " caption="' + captionValue + '" yaxisname="Nilai" numberprefix="" yaxismaxvalue="' + yMaxValue + '" showborder="0" theme="fint">';
				tmplChart += dataCollections;
				tmplChart += '</chart>';

				return tmplChart;
			}

			function generateLineTahunan(dataCollections, captionValue){
				var tmplChart = '';

				tmplChart += '<chart caption="' + captionValue + '" numberprefix="" bgcolor="FFFFFF" showalternatehgridcolor="0" plotbordercolor="008ee4" plotborderthickness="3" showvalues="0" divlinecolor="CCCCCC" showcanvasborder="0" tooltipbgcolor="00396d" tooltipcolor="FFFFFF" tooltipbordercolor="00396d" numdivlines="2" yaxisvaluespadding="20" anchorbgcolor="008ee4" anchorborderthickness="0" showshadow="0" anchorradius="4" chartrightmargin="25" canvasborderalpha="0" showborder="0">';
				tmplChart += dataCollections;
				tmplChart += '</chart>';
				
				return tmplChart;
			}
			<?php
			foreach($arrAspek as $aspek){
				if($aspek != NULL){
					list($idAspek, $namaAspek) = explode("~", $aspek);
					$tempIdAspek = getField("SELECT t2.idAspek FROM pen_penilaian_detail t1 JOIN pen_setting_aspek t2 ON t2.idAspek = t1.idAspek WHERE t2.namaAspek = '$namaAspek' AND t1.idPenilaian = '$r[idPenilaian]'");
					$tempNilai = getField("SELECT AVG(bobotPilihan) FROM pen_penilaian_detail WHERE idPenilaian = '$r[idPenilaian]' AND idAspek = '$tempIdAspek'");
					echo "
					var chart = new FusionCharts(\"AngularGauge\", \"chartGaugeAspek".$idAspek."\", \"100%\", \"175\");
					chart.setXMLData( generateGaugeAspekChart(0, 100, $tempNilai, '$namaAspek') );
					chart.render(\"divGaugeAspek_".$idAspek."\");
					";
					echo "
					var dataCollections = '';
					";
					foreach($years as $year){
						$tempNilai2 = getField("SELECT AVG(t1.bobotPilihan) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian WHERE t1.idAspek = '$tempIdAspek' AND YEAR(t2.tglPenilaian) = '$year' AND t2.idPegawai = '$cID'");
						echo "dataCollections += '<set label=\"$year\" value=\"".$tempNilai2."\" tooltext=\"Rata-rata nilai: $tempNilai2\"/>';";
					}
					echo "
					var chart = new FusionCharts(\"Column2D\", \"chartColumn2DAspek".$idAspek."\", \"100%\", \"300\");
					chart.setXMLData( generate2DColumnAspek(\"PENILAIAN ASPEK ".strtoupper($namaAspek)."\", 100, dataCollections) );
					chart.render(\"divColumn2DAspek_".$idAspek."\");
					";	
				}
			}
			?>
			var dataCollections = '';
			<?php
			foreach($years as $year){

				$tempNilai2 = getField("SELECT AVG(t1.bobotPilihan) FROM pen_penilaian_detail t1 JOIN pen_penilaian t2 ON t2.idPenilaian = t1.idPenilaian WHERE t2.idPenilaian = '$r[idPenilaian]' AND YEAR(t2.tglPenilaian) = '$year' AND t2.idPegawai = '$cID'");
				echo "dataCollections += '<set label=\"$year\" value=\"".($tempNilai2 ? $tempNilai2 : "0")."\" anchorradius=\"7\" tooltext=\"".($tempNilai2 ? "Rata-rata nilai: ".getAngka($tempNilai2, 2) : "Belum ada penilaian")."\"/>';\n";
			}
			?>
			var chart = new FusionCharts("Line", "chartLineTahunan", "100%", "300");
			chart.setXMLData( generateLineTahunan(dataCollections, "Penilaian Tahunan dari <?= reset($years)."-".end($years) ?>"));
			chart.render("divLineTahunan");
		</script>
		<?php
	}else{
		?>
		<br clear="all">
		<div class="notibar announcement" style="background-color: #F44336; border: none;">
			<a class="close"></a>
			<p style="color: #FFF"><b>Belum ada penilaian</b></p>
		</div>
		<?php
	}
}else{
	?>
	<div class="notibar announcement" style="background-color: #F44336; border: none;">
		<a class="close"></a>
		<p style="color: #FFF"><b>Anda bukan karyawan</b></p>
	</div>
	<?php
}
?>
<?php
/* End of file dashboard.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/dashboard.php */
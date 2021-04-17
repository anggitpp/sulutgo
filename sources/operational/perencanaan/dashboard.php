<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;							
	if(empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');

	$areaCheck2 = $areaCheck;
	if(!empty($par[lokasi]))
		$areaCheck = $par[lokasi];

	$arrRencana=arrayQuery("select month(mulaiPelatihan), count(idPelatihan) from plt_pelatihan where year(mulaiPelatihan)='".$par[tahunPelatihan]."' ".(isset($par[lokasi]) ? "AND idLokasi IN ($areaCheck)" : "") . " group by 1");
	$arrKategori=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[45]."' order by urutanData");

	$cntPelaksanaan	= arrayQuery("select pelaksanaanPelatihan, count(idPelatihan) from plt_pelatihan where ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan) ".(isset($par[lokasi]) ? "AND idLokasi IN ($areaCheck)" : "") . " group by 1");
	$cntMateri = arrayQuery("select idKategori, count(idMateri) from plt_materi where statusMateri='t' group by 1");
	$cntVendor = getField("select count(kodeVendor) from dta_vendor where statusVendor='t'");
	$cntTrainer = getField("select count(idTrainer) from dta_trainer where statusTrainer='t'");
	$cntPelatihan = getField("select count(idPelatihan) from plt_pelatihan where ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan) ".(isset($par[lokasi]) ? "AND idLokasi IN ($areaCheck)" : "") . "");
	$cntBiaya = arrayQuery("select month(t1.mulaiPelatihan), sum(t2.nilaiRab) from plt_pelatihan t1 join plt_pelatihan_rab t2 on (t1.idPelatihan=t2.idPelatihan) where ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan) ".(isset($par[lokasi]) ? "AND t1.idLokasi IN ($areaCheck)" : "") . " group by 1");

	$maxVendor = $cntVendor > 0 ? ceilAngka($cntVendor, pow(10,strlen($cntVendor))) : 10;
	$downVendor = 0.5 * $maxVendor;
	$upVendor = 0.8 * $maxVendor;

	$maxTrainer = $cntTrainer > 0 ? ceilAngka($cntTrainer, pow(10,strlen($cntTrainer))) : 10;
	$downTrainer = 0.5 * $maxTrainer;
	$upTrainer = 0.8 * $maxTrainer;

	$maxPelatihan = $cntPelatihan > 0 ? ceilAngka($cntPelatihan, pow(10,strlen($cntPelatihan))) : 10;
	$downPelatihan = 0.5 * $maxPelatihan;
	$upPelatihan = 0.8 * $maxPelatihan;

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."				
</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
			<!--Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ($areaCheck2) order by urutanData","kodeData","namaData","par[lokasi]","All",$par[lokasi],"onchange=\"document.getElementById('form').submit();\"")."-->
			&nbsp;Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"")."
		</div>				
	</form>	

	<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Rencana Pelatihan</h3></div>
	</div>			
	<div id=\"divRencana\" align=\"center\"></div>
	<script type=\"text/javascript\">
		var rencanaChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";

		for($i=1; $i<=12; $i++){
			$text.="<set label=\"".getBulan($i)."\" value=\"".$arrRencana[$i]."\"/> ";									
		}

		$text.="</chart>';
		var chart = new FusionCharts(\"Column3D\", \"chartRencana\", \"100%\", 250);
		chart.setXMLData( rencanaChart );
		chart.render(\"divRencana\");
	</script>

	<table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
		<tr>
			<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Vendor</h3></div>
				</div>
				<div id=\"divVendor\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var vendorChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxVendor."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downVendor."\"/>";
					$text.="<color minValue=\"".$downVendor."\" maxValue=\"".$upVendor."\"/>";
					$text.="<color minValue=\"".$upVendor."\" maxValue=\"".$maxVendor."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntVendor."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"143\" y=\"40\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntVendor)." Vendor\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";					
					$text.="</annotationGroup>";
					$text.="</annotations>";

					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartVendor\", \"275\", \"175\");
					chart.setXMLData( vendorChart );
					chart.render(\"divVendor\");
				</script>
			</td>
			<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Trainer</h3></div>
				</div>
				<div id=\"divTrainer\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var trainerChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTrainer."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downTrainer."\"/>";
					$text.="<color minValue=\"".$downTrainer."\" maxValue=\"".$upTrainer."\"/>";
					$text.="<color minValue=\"".$upTrainer."\" maxValue=\"".$maxTrainer."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntTrainer."\" bgColor=\"000000\" baseWidth=\"15\" radius=\"120\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"147\" y=\"40\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntTrainer)." Trainer\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";					
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartTrainer\", \"275\", \"175\");
					chart.setXMLData( trainerChart );
					chart.render(\"divTrainer\");
				</script>
			</td>				
			<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Pelatihan</h3></div>
				</div>
				<div id=\"divPelatihan\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var pelatihanChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxPelatihan."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"5\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\"  gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downPelatihan."\"/>";
					$text.="<color minValue=\"".$downPelatihan."\" maxValue=\"".$upPelatihan."\"/>";
					$text.="<color minValue=\"".$upPelatihan."\" maxValue=\"".$maxPelatihan."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntPelatihan."\" bgColor=\"000000\" baseWidth=\"10\" radius=\"70\" rearExtension=\"15\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"40\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntPelatihan)." Pelatihan\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";					
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"charteringatan\", \"275\", \"175\");
					chart.setXMLData( pelatihanChart );
					chart.render(\"divPelatihan\");
				</script>
			</td>
		</tr>
		<tr>
			<td style=\"vertical-align:top; padding-left:15px; padding-right:15px; padding-top:30px;\"  colspan=\"2\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Materi Pelatihan</h3></div>
				</div>
				<div id=\"divMateri\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var materiChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";
					
					if(is_array($arrKategori)){
						reset($arrKategori);
						while(list($idKategori, $namaKategori) = each($arrKategori)){				
							$text.="<set label=\"".$namaKategori."\" value=\"".$cntMateri[$idKategori]."\"/> ";					
						}
					}
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Bar2D\", \"chartMateri\", \"100%\", 300);
					chart.setXMLData( materiChart );
					chart.render(\"divMateri\");
				</script>
			</td>
			<td style=\"vertical-align:top; padding-left:15px; padding-right:15px; padding-top:30px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pelaksanaan</h3></div>
				</div>					
				<div id=\"divPelaksanaan\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var pelaksanaanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";

					$text.="<set value=\"".setAngka($cntPelaksanaan["i"])."\" label=\"Internal\" />";
					$text.="<set value=\"".setAngka($cntPelaksanaan["e"])."\" label=\"Eksternal\" />";						
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPelaksanaan\", \"100%\", 300);
					chart.setXMLData( pelaksanaanChart );
					chart.render(\"divPelaksanaan\");
				</script>
			</td>
		</tr>
	</table>

	<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Biaya Pelatihan</h3></div>
	</div>
	<div id=\"divBiaya\" align=\"center\"></div>
	<script type=\"text/javascript\">
		var biayaChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";

		for($i=1; $i<=12; $i++){
			$valBiaya = $cntBiaya[$i] > 0 ? $cntBiaya[$i] : 0;
			$text.="<set label=\"".getBulan($i)."\" value=\"".$valBiaya."\"/> ";									
		}

		$text.="</chart>';
		var chart = new FusionCharts(\"Line\", \"chartBiaya\", \"100%\", 250);
		chart.setXMLData( biayaChart );
		chart.render(\"divBiaya\");
	</script>";

	return $text;
}	

function getContent($par){
	global $db,$s,$_submit,$menuAccess;		
	switch($par[mode]){				
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
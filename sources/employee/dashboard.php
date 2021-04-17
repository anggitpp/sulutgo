<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fPokok = "files/pokok/";
	$fExport = "files/export/";
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;					
		if(empty($par[bulanData])) $par[bulanData] = date('m');
		if(empty($par[tahunData])) $par[tahunData] = date('Y');
		// $idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		// if(empty($par[idStatus])) $par[idStatus] = $idStatus;

		$cat1 = !empty($par[idStatus]) ? " and t1.cat = '$par[idStatus]'" : "";
		$cat = !empty($par[idStatus]) ? " and cat = '$par[idStatus]'" : "";
		
		
		$jmlUmur["&gt;50"]=0;		
		$jmlUmur["41-50"]=0;
		$jmlUmur["31-40"]=0;
		$jmlUmur["&lt;31"]=0;

		$jmlMasa["&gt;31"]=0;		
		$jmlMasa["26-30"]=0;
		$jmlMasa["21-25"]=0;
		$jmlMasa["16-20"]=0;
		$jmlMasa["11-15"]=0;
		$jmlMasa["5-10"]=0;
		$jmlMasa["4-5"]=0;
		$jmlMasa["3-4"]=0;
		$jmlMasa["2-3"]=0;
		$jmlMasa["1-2"]=0;
		$jmlMasa["&lt;1"]=0;
		
		$arrGender["M"]=0;		
		$arrGender["F"]=0;
		
		$usiaPensiun = $arrParameter[37];

		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' order by urutanData");
		$arrPendidikan = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[32]."' order by urutanData");
		$arrPelatihan = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[28]."' order by urutanData");
		$arrPerkawinan = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[9]."' order by urutanData");
		$arrStatus = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData");
		
		$sql="select *,CASE WHEN COALESCE(leave_date,NULL) IS NULL THEN TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN leave_date = '0000-00-00' THEN TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE TIMESTAMPDIFF(YEAR,  join_date, leave_date) END masaKerja from dta_pegawai WHERE location IN ($areaCheck) $cat AND (join_date <= '".$par[tahunData]."-".$par[bulanData]."-01' AND (leave_date > '".$par[tahunData]."-".$par[bulanData]."-31' OR leave_date = '0000-00-00' OR leave_date = '' OR leave_date IS NULL)) order by id";
			// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if($r[status] == $status){
				list($tahunLahir, $bulanLahir) = explode("-", $r[birth_date]);
				$usiaPegawai = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", date('Y')."-".date('m')."-01 00:00:00");
				list($tahunJoin, $bulanJoin) = explode("-", $r[join_date]);
				if($r[rank]!=3490){
				if($tahunJoin >= 1980 AND $tahunJoin <= 1990){
					$usiaPensiun = 60;
					if($usiaPensiun - $usiaPegawai < 1){
						$cntPensiun++;
					}
				}
				if($tahunJoin >= 1991 AND $tahunJoin <= 2012){
					if($r[gender] == "M"){
						if($r[rank] == "3148" || $r[rank] == "3150" || $r[rank] == "3365"){
							$usiaPensiun = 55;
						}else{
							$usiaPensiun = 60;
						}
					}
					else{
						$usiaPensiun = 55;
					}
					if($usiaPensiun - $usiaPegawai < 1){
						$cntPensiun++;
					}
				}
				if($tahunJoin > 2012){
					$usiaPensiun = 55;
					if($usiaPensiun - $usiaPegawai < 1){
						$cntPensiun++;
					}
				}
			}

				$cntPegawai++;

				if($r[masaKerja] < 1)
					$jmlMasa["&lt;1"]++;
				else if($r[masaKerja] <= 2)
					$jmlMasa["1-2"]++;
				else if($r[masaKerja] <= 3)
					$jmlMasa["2-3"]++;
				else if($r[masaKerja] <= 4)
					$jmlMasa["3-4"]++;
				else if($r[masaKerja] <= 5)
					$jmlMasa["4-5"]++;
				else if($r[masaKerja] <= 10)
					$jmlMasa["5-10"]++;
				else if($r[masaKerja] <= 15)
					$jmlMasa["11-15"]++;
				else if($r[masaKerja] <= 20)
					$jmlMasa["16-20"]++;
				else if($r[masaKerja] <= 25)
					$jmlMasa["21-25"]++;
				else if($r[masaKerja] <= 30)
					$jmlMasa["26-30"]++;
				else
					$jmlMasa["&gt;31"]++;


				
				if($usiaPegawai < 31)
					$jmlUmur["&lt;31"]++;
				else if($usiaPegawai <= 41)
					$jmlUmur["31-40"]++;
				else if($usiaPegawai <= 51)
					$jmlUmur["41-50"]++;
				else
					$jmlUmur["&gt;50"]++;
				
				$jmlPerkawinan["$r[marital]"]++;
				$jmlStatus["$r[cat]"]++;
				$jmlPangkat["$r[rank]"]++;
				$arrGender["$r[gender]"]++;				
			}
		}
		
		
		
		$kontrakBulan = $par[bulanData] > 10 ? $par[bulanData] + 2 - 12 : $par[bulanData] + 2;
		$kontrakTahun = $par[bulanData] > 10 ? $par[tahunData] + 1 : $par[tahunData];		
		$kontrakMax = $kontrakTahun."-".str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT)."-".date('d');
		$sql="select * from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) between '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."' and  '".$kontrakMax."' and t2.status='1' and t1.status='".$status."' AND t1.location IN ($areaCheck) $cat1 group by t1.id";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
			$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date];			
		}
		
		$sql="select * from dta_pegawai where end_date between '".date('Y-m-d')."' and  '".$kontrakMax."' and status='".$status."' AND location IN ($areaCheck) $cat";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
			$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date];			
		}
		
		$cntKontrak=count($arrKontrak);
		
		$sql="select * from dta_pegawai t1 join emp_edu t2 on (t1.id=t2.parent_id) where t1.status='".$status."' AND t1.location IN ($areaCheck) $cat1 order by t2.edu_year, t2.id";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrEducation["$r[parent_id]"]=$r[edu_type];
		}
						
		if(is_array($arrEducation)){
			reset($arrEducation);
			while(list($idPegawai, $idPendidikan) = each($arrEducation)){
				$jmlPendidikan[$idPendidikan]++;
			}
		}
				
		
		$jmlPelatihan=arrayQuery("select trn_type, count(*) from dta_pegawai t1 join emp_training t2 on (t1.id=t2.parent_id) where t1.status='".$status."' $cat1 AND t1.location IN ($areaCheck) group by 1");
		
		
		$cntPeringatan=getField("select count(id) from emp_punish where '".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)."' between concat(year(pnh_date_start), LPAD(month(pnh_date_start),2,'0')) and concat(year(pnh_date_end), LPAD(month(pnh_date_end),2,'0'))");
		
		$maxPegawai = $cntPegawai > 0 ? ceilAngka($cntPegawai, pow(10,strlen($cntPegawai))) : 10;
		$downPegawai = 0.5 * $maxPegawai;
		$upPegawai = 0.8 * $maxPegawai;
		
		$maxKontrak = $cntKontrak > 0 ? ceilAngka($cntKontrak, pow(10,strlen($cntKontrak))) : 10;
		$downKontrak = 0.5 * $maxKontrak;
		$upKontrak = 0.8 * $maxKontrak;
		
		$maxPensiun = $cntPensiun > 0 ? ceilAngka($cntPensiun, pow(10,strlen($cntPensiun))) : 10;
		$downPensiun = 0.5 * $maxPensiun;
		$upPensiun = 0.8 * $maxPensiun;
				
		$maxPeringatan = $cntPeringatan > 0 ? ceilAngka($cntPeringatan, pow(10,strlen($cntPeringatan))) : 10;
		$downPeringatan = 0.5 * $maxPeringatan;
		$upPeringatan = 0.8 * $maxPeringatan;
		$tahunbulan = $par[tahunData].$par[bulanData]-1;
		$jumlahPegawai = getField("select count(DISTINCT(idPegawai)) from pay_proses_".$tahunbulan);
			
		
		// echo $tahunbulan;
		// echo "select count(DISTINCT(idPegawai) from pay_proses_".$tahunbulan;

		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">	
			Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","-- ALL --",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."		
			Periode : ".comboMonth("par[bulanData]", $par[bulanData], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunData]", $par[tahunData], "", "onchange=\"document.getElementById('form').submit();\"")."

			</div>				
			</form>			
			<table style=\"width:100%; margin-bottom:10px; margin-left:-15px;\">
			<tr>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Total Pegawai</h3></div>
					</div>
					<div id=\"divPegawai\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPegawai".getPar($par,"mode")."',1075,550);\"></div>
					<script type=\"text/javascript\">
					var pegawaiChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxPegawai."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downPegawai."\"/>";
					$text.="<color minValue=\"".$downPegawai."\" maxValue=\"".$upPegawai."\"/>";
					$text.="<color minValue=\"".$upPegawai."\" maxValue=\"".$maxPegawai."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntPegawai."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"143\" y=\"60\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntPegawai)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"140\" label=\"Terima Gaji\" fontColor=\"000000\" fontSize=\"15\" />";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartPegawai\", \"225\", \"175\");
						chart.setXMLData( pegawaiChart );
						chart.render(\"divPegawai\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Habis Kontrak</h3></div>
					</div>
					<div id=\"divKontrak\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKontrak".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var kontrakChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxKontrak."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downKontrak."\"/>";
					$text.="<color minValue=\"".$downKontrak."\" maxValue=\"".$upKontrak."\"/>";
					$text.="<color minValue=\"".$upKontrak."\" maxValue=\"".$maxKontrak."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntKontrak."\" bgColor=\"000000\" baseWidth=\"15\" radius=\"120\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"147\" y=\"60\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntKontrak)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"140\" label=\"Kurang dari 2 Bulan\" fontColor=\"000000\" fontSize=\"15\" />";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartKontrak\", \"225\", \"175\");
						chart.setXMLData( kontrakChart );
						chart.render(\"divKontrak\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Usia Pensiun</h3></div>
					</div>
					<div id=\"divPensiun\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPensiun".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var pensiunChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxPensiun."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\"  pivotRadius=\"8\" pivotFillMix=\"{CCCCCC},{000000}\" pivotBorderColor=\"000000\" >";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downPensiun."\" />";
					$text.="<color minValue=\"".$downPensiun."\" maxValue=\"".$upPensiun."\" />";
					$text.="<color minValue=\"".$upPensiun."\" maxValue=\"".$maxPensiun."\" />";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntPensiun."\" bgColor=\"000000\" borderAlpha=\"0\" baseWidth=\"4\" topWidth=\"4\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"150\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntPensiun)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"140\" label=\"Kurang dari 1 Tahun\" fontColor=\"000000\" fontSize=\"15\" />";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartPensiun\", \"225\", \"175\");
						chart.setXMLData( pensiunChart );
						chart.render(\"divPensiun\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Surat Peringatan</h3></div>
					</div>
					<div id=\"divPeringatan\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPeringatan".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var peringatanChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxPeringatan."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"5\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\"  gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downPeringatan."\"/>";
					$text.="<color minValue=\"".$downPeringatan."\" maxValue=\"".$upPeringatan."\"/>";
					$text.="<color minValue=\"".$upPeringatan."\" maxValue=\"".$maxPeringatan."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntPeringatan."\" bgColor=\"000000\" baseWidth=\"10\" radius=\"70\" rearExtension=\"15\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntPeringatan)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"140\" label=\"Pelanggar Aturan\" fontColor=\"000000\" fontSize=\"15\" />";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"charteringatan\", \"225\", \"175\");
						chart.setXMLData( peringatanChart );
						chart.render(\"divPeringatan\");
					</script>
				</td>
			</tr>
			</table>
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Habis Kontrak (&plusmn; 2 Bulan)</h3></div>
			</div>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:100px;\">Nama</th>
					<th style=\"min-width:100px;\">Pangkat</th>
					<th style=\"min-width:100px;\">Jabatan</th>
					<th style=\"min-width:100px;\">Lokasi</th>
					<th style=\"width:85px;\">Start Date</th>
					<th style=\"width:85px;\">End Date</th>
				</tr>
			</thead>
			<tbody>";
			$no=1;
			if(is_array($arrKontrak)){
				reset($arrKontrak);
				while(list($id, $valKontrak) = each($arrKontrak)){
					list($namaPegawai, $idPangkat, $namaJabatan, $idLokasi, $mulaiKontrak, $selesaiKontrak) = explode("\t", $valKontrak);
					$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($namaPegawai)."</td>
							<td>".strtoupper($arrPangkat[$idPangkat])."</td>
							<td>".strtoupper($namaJabatan)."</td>
							<td>".strtoupper($arrLokasi[$idLokasi])."</td>
							<td align=\"center\">".getTanggal($mulaiKontrak)."</td>
							<td align=\"center\">".getTanggal($selesaiKontrak)."</td>
						</tr>";	
						$no++;
				}
			}
			$text.="</tbody>
			</table>
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pegawai Berdasarkan Umur</h3></div>
			</div>
			<div id=\"divUmur\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detUmur".getPar($par,"mode")."',1075,450);\"></div>
			<script type=\"text/javascript\">
			var umurChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" basefontColor=\"000000\" stack100Percent=\"1\" showPercentValues=\"0\" numberSuffix=\" Orang\">";
			$text.="<categories>";
			$text.="<category label=\"\" />";
			$text.="</categories>";
			
			if(is_array($jmlUmur)){
				reset($jmlUmur);
				while(list($lblUmur, $valUmur) = each($jmlUmur)){					
					$text.="<dataset seriesName=\"Umur ".$lblUmur."\" showValues=\"1\">";
					$text.="<set value=\"".$valUmur."\" />";
					$text.="</dataset>";
				}
			}
			$text.="</chart>';
			
			var chart = new FusionCharts(\"StackedBar3D\", \"chartUmur\", \"100%\", \"150\");
				chart.setXMLData( umurChart );
				chart.render(\"divUmur\");
			</script>

			

			<table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
			<tr>
				<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pendidikan</h3></div>
					</div>
					<div id=\"divPendidikan\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPendidikan".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var pendidikanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";
					
					if(is_array($arrPendidikan)){
						reset($arrPendidikan);
						while(list($idPendidikan, $namaPendidikan) = each($arrPendidikan)){
							$showValue = setAngka($jmlPendidikan[$idPendidikan]) > 0 ? 1 : 0;
							$text.="<set value=\"".setAngka($jmlPendidikan[$idPendidikan])."\" label=\"".$namaPendidikan."\" showValue=\"".$showValue."\" />";
						}	
					}			
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPendidikan\", \"100%\", 350);
						chart.setXMLData( pendidikanChart );
						chart.render(\"divPendidikan\");
					</script>
				</td>
				<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pelatihan</h3></div>
					</div>
					<div id=\"divPelatihan\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPelatihan".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var pelatihanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";
					
					if(is_array($arrPelatihan)){
						reset($arrPelatihan);
						while(list($idPelatihan, $namaPelatihan) = each($arrPelatihan)){
							$showValue = setAngka($jmlPelatihan[$idPelatihan]) > 0 ? 1 : 0;
							$text.="<set value=\"".setAngka($jmlPelatihan[$idPelatihan])."\" label=\"".$namaPelatihan."\" showValue=\"".$showValue."\" />";
						}	
					}			
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPelatihan\", \"100%\", 350);
						chart.setXMLData( pelatihanChart );
						chart.render(\"divPelatihan\");
					</script>
				</td>
				<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Perkawinan</h3></div>
					</div>
					<div id=\"divPerkawinan\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPerkawinan".getPar($par,"mode")."',1075,450);\" ></div>
					<script type=\"text/javascript\">
					var maritalChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";
					
					if(is_array($arrPerkawinan)){
						reset($arrPerkawinan);
						while(list($idPerkawinan, $namaPerkawinan) = each($arrPerkawinan)){							
							$showValue = setAngka($jmlPerkawinan[$idPerkawinan]) > 0 ? 1 : 0;
							$text.="<set value=\"".setAngka($jmlPerkawinan[$idPerkawinan])."\"  label=\"".$namaPerkawinan."\" showValue=\"".$showValue."\"/>";
						}	
					}			
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPerkawinan\", \"100%\", 350);
						chart.setXMLData( maritalChart );
						chart.render(\"divPerkawinan\");
					</script>
				</td>
			</tr>
			</table>
			
			<table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
			<tr>
				<td style=\"width:67%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Status Pegawai</h3></div>
					</div>
					<div id=\"divStatus\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPegawai".getPar($par,"mode")."',1075,450);\"></div>
					<script type=\"text/javascript\">
					var statusChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";
					
					if(is_array($arrStatus)){
						reset($arrStatus);
						while(list($idStatus, $namaStatus) = each($arrStatus)){				
							$text.="<set label=\"".$namaStatus."\" value=\"".$jmlStatus[$idStatus]."\"/> ";					
						}
					}
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Bar2D\", \"chartStatus\", \"100%\", 200);
						chart.setXMLData( statusChart );
						chart.render(\"divStatus\");
					</script>
				</td>
				<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Gender</h3></div>
					</div>
					<ul class=\"toplist\">";
				
				$totGender=array_sum($arrGender);
				if(is_array($arrGender)){
						reset($arrGender);
						while(list($idGender, $jmlGender) = each($arrGender)){							
						$persenGender = $jmlGender / $totGender * 100;
						
						$text.="<li>
									<div onclick=\"openBox('popup.php?par[mode]=detKelamin&par[idGender]=$idGender".getPar($par,"mode")."',1075,450);\">
										<span class=\"one_fourth\">
											<span class=\"left\">
												<img src=\"styles/images/".strtolower($idGender)."gender.jpg\">
											</span>
										</span>
										<span class=\"three_fourth last\">
											<span class=\"right\">
												<h1>".getAngka($persenGender)." %</h1>
												<span class=\"title\" style=\"margin-top:10px;\">".getAngka($jmlGender)." Orang</span>										
											</span>
										</span>
										<br clear=\"all\">
									</div>
								</li>";
						}
				}
				$text.="</ul>					
				</td>
			</tr>
			</table>
			
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pangkat</h3></div>
			</div>
			
			<div id=\"divPangkat\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPangkat".getPar($par,"mode")."',1075,450);\"></div>
			<script type=\"text/javascript\">
			var pangkatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";
			
			if(is_array($jmlPangkat)){
				reset($jmlPangkat);
				while(list($idPangkat, $valPangkat) = each($jmlPangkat)){									
					if(isset($arrPangkat[$idPangkat])) $text.="<set label=\"".$arrPangkat[$idPangkat]."\" value=\"".$valPangkat."\"/> ";					
				}
			}
			
			$text.="</chart>';
			var chart = new FusionCharts(\"Column3D\", \"chartPangkat\", \"100%\", 250);
				chart.setXMLData( pangkatChart );
				chart.render(\"divPangkat\");
			</script>

			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Masa Kerja</h3></div>
			</div>
			
			<div id=\"divMasa\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detMasa".getPar($par,"mode")."',1075,450);\"></div>
			<script type=\"text/javascript\">
			var masaChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";
			
			if(is_array($jmlMasa)){
				reset($jmlMasa);
				while(list($labelMasa, $valMasa) = each($jmlMasa)){									
					$text.="<set label=\"".$labelMasa."\" value=\"".$valMasa."\"/> ";					
				}
			}
			
			$text.="</chart>';
			var chart = new FusionCharts(\"Column3D\", \"chartMasa\", \"100%\", 250);
				chart.setXMLData( masaChart );
				chart.render(\"divMasa\");
			</script>
			
			";
					
		return $text;
	}	
	
	function detPegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;		
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Data Pegawai</h1>
				<span>&nbsp;</span>
			</div>    
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."
			<div style=\"position:absolute;right:15px;top:10px;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>	
			<div id =\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>	
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>					
					<th width=\"100\">Lokasi Kerja</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tanggal Lahir</th>
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tanggal Masuk</th>
					<th width=\"100\">Masa Kerja</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$filter = "where t1.status='".$status."'";	

		if(!empty($par[idStatus])){
			$filter.= " and t1.cat = '$par[idStatus]'";
		}	
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		$filter .= " AND t2.location IN ($areaCheck)";
		
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data ");
		$sql="select t1.*,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja,CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') umur, t2.pos_name, t2.div_id,t2.location from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>".getTanggal($r[birth_date])."</td>
					<td>$r[umur]</td>
					<td>".getTanggal($r[join_date])."</td>
					<td>$r[masaKerja]</td>

				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detKontrak(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;		
		if(empty($par[bulanData])) $par[bulanData] = date('m');
		if(empty($par[tahunData])) $par[tahunData] = date('Y');
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$kontrakBulan = $par[bulanData] > 9 ? $par[bulanData] + 2 - 12 : $par[bulanData] + 2;
		$kontrakTahun = $par[bulanData] > 9 ? $par[tahunData] + 1 : $par[tahunData];		
		$kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);
		$sql="select *,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja,CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') umur from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) between '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."' and  '".$kontrakMax."' and t2.status='1' and t1.status='".$status."' AND t1.location IN ($areaCheck) $cat1 group by t1.id";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
			$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date]."\t".$r[reg_no]."\t".$r[cat]."\t".$r[birth_date]."\t".$r[umur]."\t".$r[masaKerja];			
		}
		
		$sql="select *,replace(
			case when coalesce(leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN `leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja,CONCAT(TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, birth_date,  CURRENT_DATE ) % 12, ' bln') umur from dta_pegawai where concat(year(end_date), LPAD(month(end_date),2,'0')) between '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."' and  '".$kontrakMax."' and status='".$status."' AND location IN ($areaCheck) $cat";
		$res=db($sql);
		// echo $sql;
		while($r=mysql_fetch_array($res)){
			$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
			$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date]."\t".$r[reg_no]."\t".$r[cat]."\t".$r[birth_date]."\t".$r[umur]."\t".$r[masaKerja];			
		}
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Habis Kontrak (± 2 Bulan)</h1>
				<span>&nbsp;</span>
			</div>   
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tanggal Lahir</th>
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tanggal Masuk</th>
					<th width=\"100\">Tanggal Keluar</th>
					<th width=\"100\">Masa Kerja</th>
				</tr>
			</thead>
			<tbody>";
			$no=1;
			$arrMaster = arrayQuery("select kodeData, namaData from mst_data ");
			if(is_array($arrKontrak)){
				reset($arrKontrak);
				while(list($id, $valKontrak) = each($arrKontrak)){
					list($namaPegawai, $idPangkat, $namaJabatan, $idLokasi, $mulaiKontrak, $selesaiKontrak,$nik,$status,$birth_date,$umur,$masaKerja) = explode("\t", $valKontrak);
					$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($namaPegawai)."</td>
							<td>".$nik."</td>
							<td>".strtoupper($namaJabatan)."</td>
							<td>".strtoupper($arrMaster[$idLokasi])."</td>
							<td>".strtoupper($arrMaster[$status])."</td>
							<td align=\"center\">".getTanggal($birth_date)."</td>
							<td>".$umur."</td>

							<td align=\"center\">".getTanggal($mulaiKontrak)."</td>
							<td align=\"center\">".getTanggal($selesaiKontrak)."</td>
							<td>".$masaKerja."</td>

						</tr>";	
						$no++;
				}
			}
			$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPensiun(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;		
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
		// $usiaPensiun = $arrParameter[37];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Usia Pensiun</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."		
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>	
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi Kerja</th>
					<th width=\"100\">Status</th>
					
					<th width=\"100\">Tanggal Lahir</th>					
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tanggal Masuk</th>	
					<th width=\"100\">Masa Kerja</th>	
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

		$filter = "where t1.status='".$status."' and t1.birth_date is not null AND t2.rank !='3490'";	

		if(!empty($par[idStatus])){
			$filter.= " and t1.cat = '$par[idStatus]'";
		}	

		// $filter = "where t1.cat=".$par[idStatus]." and t1.status='".$status."' ";	
				
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		

		$filter .= " AND t2.location IN ($areaCheck)";
		
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select t1.*,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja,CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') umur, t2.pos_name, t2.div_id,t2.location from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			list($tahunLahir, $bulanLahir) = explode("-", $r[birth_date]);
	$usiaPegawai = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", date('Y')."-".date('m')."-01 00:00:00");
	list($tahunJoin, $bulanJoin) = explode("-", $r[join_date]);
	if($tahunJoin >= 1980 AND $tahunJoin <= 1990){
		$usiaPensiun = 60;
		if($usiaPensiun - $usiaPegawai < 1){
		$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster[$r[location]]."</td>
					<td>".$arrMaster[$r[cat]]."</td>
					<td align=\"center\">".getTanggal($r[birth_date])."</td>
					<td align=\"right\">".$r[umur]." </td>
					<td align=\"center\">".getTanggal($r[join_date])."</td>
					<td align=\"right\">".$r[masaKerja]." </td>


				</tr>";
		}
	}
	if($tahunJoin >= 1991 AND $tahunJoin <= 2012){
		if($r[gender] == "M"){
			if($r[rank] == "3148" || $r[rank] == "3150" || $r[rank] == "3365"){
				$usiaPensiun = 55;
			}else{
				$usiaPensiun = 60;
			}
		}
		else{
		$usiaPensiun = 55;
		}
		if($usiaPensiun - $usiaPegawai < 1){
		$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster[$r[location]]."</td>
					<td>".$arrMaster[$r[cat]]."</td>
					<td align=\"center\">".getTanggal($r[birth_date])."</td>
					<td align=\"right\">".$r[umur]." </td>
					<td align=\"center\">".getTanggal($r[join_date])."</td>
					<td align=\"right\">".$r[masaKerja]." </td>
				</tr>";
		}
	}
	if($tahunJoin > 2012){
		$usiaPensiun = 55;
		if($usiaPensiun - $usiaPegawai < 1){
		$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster[$r[location]]."</td>
					<td>".$arrMaster[$r[cat]]."</td>
					<td align=\"center\">".getTanggal($r[birth_date])."</td>
					<td align=\"right\">".$r[umur]." </td>
					<td align=\"center\">".getTanggal($r[join_date])."</td>
					<td align=\"right\">".$r[masaKerja]." </td>
				</tr>";
		}
	}
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPeringatan(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");		
		if(empty($par[bulanData])) $par[bulanData] = date('m');
		if(empty($par[tahunData])) $par[tahunData] = date('Y');
			
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Surat Peringatan</h1>
				<span>&nbsp;</span>
			</div>    
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">	
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."		
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>	
			
			</form>
			<br clear=\"all\" />
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tanggal Masuk</th>
					<th width=\"100\">Masa Kerja</th>
					<th width=\"100\">Nomor</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"100\">Perihal</th>					
				</tr>
			</thead>
			<tbody>";
			$filter = "where t1.cat=".$par[idStatus]."";	
			$no=1;
			$sql="select *,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja from dta_pegawai t1 join emp_punish t2 on (t1.id=t2.parent_id) $filter and '".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)."' between concat(year(t2.pnh_date_start), LPAD(month(t2.pnh_date_start),2,'0')) and concat(year(t2.pnh_date_end), LPAD(month(t2.pnh_date_end),2,'0')) AND t1.location IN ($areaCheck)";
			$res=db($sql);
			$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
			while($r=mysql_fetch_array($res)){
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>".$r[reg_no]."</td>
						<td>".$r[pos_name]."</td>
						<td>".$arrMaster[$r[location]]."</td>
						<td>".$arrMaster[$r[cat]]."</td>
						<td align=\"center\">".getTanggal($r[join_date])."</td>
						<td>".$r[masaKerja]."</td>
						<td>".$r[pnh_no]."</td>
						<td align=\"center\">".getTanggal($r[pnh_date_start])."</td>
						<td>".$r[pnh_subject]."</td>						
					</tr>";	
					$no++;				
			}
			$text.="</tbody>
			</table>
			</form>
			</div>";
		
		return $text;
	}
	
	function detUmur(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pegawai Berdasarkan Umur</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."		
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>	

			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboArray("par[umur]",array("Umur",">50","41-50","31-40","<31"),$par[umur])."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>

					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tanggal Lahir</th>					
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tanggal Masuk</th>					
					<th width=\"100\">Masa Kerja</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$filter = "where t1.status='".$status."' ";	

		if(!empty($par[idStatus])){
			$filter.= " and t1.cat = '$par[idStatus]'";
		}	
		


		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";

		$filter .= " AND t2.location IN ($areaCheck)";

		if($par[umur]==">50"){
			$filter.=" and TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ) >= '50'";
		}

		if($par[umur]=="41-50"){
			$filter.=" and TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ) BETWEEN '41' AND '49'";
		}

		if($par[umur]=="31-40"){
			$filter.=" and TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ) BETWEEN '31' AND '39'";
		}

		if($par[umur]=="<31"){
			$filter.=" and TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ) <= '30'";
		}
		
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select t1.*,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja, t2.pos_name, t2.div_id,t2.location from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			
			list($tahunLahir, $bulanLahir) = explode("-", $r[birth_date]);
			$usiaPegawai = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", $par[tahunData]."-".$par[bulanData]."-01 00:00:00");			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster[$r[location]]."</td>
					<td>".$arrMaster[$r[cat]]."</td>
					<td align=\"center\">".getTanggal($r[birth_date])."</td>
					<td align=\"right\">".$usiaPegawai." Tahun</td>
					<td align=\"center\">".getTanggal($r[join_date])."</td>
					<td>$r[masaKerja]</td>
				</tr>";
			
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPendidikan(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pendidikan Pegawai</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."		
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>	
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboData("select * from mst_data where kodeCategory = 'R11' order by kodeData","kodeData","namaData","par[pendidikan]","-- PENDIDIKAN --",$par[pendidikan],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Pendidikan</th>
					<th width=\"100\">Instansi</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$filter = "where t1.status='".$status."' ";	

		if(!empty($par[idStatus])){
			$filter.= " and t1.cat = '$par[idStatus]'";
		}	

		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";

		$filter .= " AND t1.location IN ($areaCheck)";

		if(!empty($par[pendidikan])){
			$filter.= " AND t2.edu_type = '$par[pendidikan]'";
		}
		
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData='t' order by urutanData");
		$sql="select t1.*, t2.edu_type, t2.edu_name, t1.location from dta_pegawai t1 join emp_edu t2 on (t1.id=t2.parent_id) $filter order by t2.edu_year, t2.id";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$arrEducation["$r[id]"] = $r;
		}
		
		if(is_array($arrEducation)){
			reset($arrEducation);
			while(list($idPegawai, $r) = each($arrEducation)){			
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>".$arrMaster["$r[edu_type]"]."</td>
					<td>$r[edu_name]</td>
				</tr>";
			}	
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPelatihan(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pelatihan Pegawai</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."	
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>		
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboData("select * from mst_data where kodeCategory = 'S17' order by kodeData","kodeData","namaData","par[pelatihan]","-- PELATIHAN --",$par[pelatihan],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Kategori</th>
					<th width=\"100\">Perihal</th>
					<th width=\"100\">Tahun</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$filter = "where t1.status='".$status."' ";	

		if(!empty($par[idStatus])){
			$filter.= " and t1.cat = '$par[idStatus]'";
		}	

		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		

		$filter .= " AND t1.location IN ($areaCheck)";

		if(!empty($par[pelatihan])){
			$filter.= " AND t2.trn_type = '$par[pelatihan]'";
		}

		
				
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData='t' order by urutanData");
		$sql="select t1.*, t2.trn_type, t2.trn_subject, t2.trn_year from dta_pegawai t1 join emp_training t2 on (t1.id=t2.parent_id) $filter order by t2.trn_year, t2.id";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>".$arrMaster["$r[trn_type]"]."</td>
					<td>$r[trn_subject]</td>
					<td>$r[trn_year]</td>
				</tr>";
		}
		
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPerkawinan(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Status Perkawinan</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."		
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>	
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboData("select * from mst_data where kodeCategory = 'S08' order by kodeData","kodeData","namaData","par[perkawinan]","-- PERKAWINAN --",$par[perkawinan],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Usia</th>
					<th width=\"100\">Masa Kerja</th>
					<th width=\"125\">Status Perkawinan</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$filter = "where status='".$status."' ";	

		if(!empty($par[idStatus])){
			$filter.= " and cat = '$par[idStatus]'";
		}	
		
		if($par[search] == "Nama")
			$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
			)";

		if(!empty($par[perkawinan])){
			$filter.= " AND marital = '$par[perkawinan]'";
		}


				
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData='t' order by urutanData");
		$sql="select *,replace(
			case when coalesce(leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN `leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, birth_date,  CURRENT_DATE ) % 12, ' bln') umur from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>$r[umur]</td>
					<td>$r[masaKerja]</td>
					<td>".$arrMaster["$r[marital]"]."</td>
				</tr>";
		}
		
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detKelamin(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;		
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pegawai ";
		$text.=$par[idGender] == "M" ? "Laki-laki" : "Perempuan";
		$text.="</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>			
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>					
					<th width=\"100\">Lokasi</th>					
					<th width=\"100\">Status</th>					
					<th width=\"100\">Gender</th>					
					<th width=\"100\">Tgl Lahir</th>					
					<th width=\"100\">Usia</th>					
					<th width=\"100\">Tgl Masuk</th>					
					<th width=\"100\">Masa Kerja</th>	
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$filter = "where t1.gender='".$par[idGender]."' and t1.status='".$status."'";	

		if(!empty($par[idStatus])){
			$filter.=" and t1.cat = '$par[idStatus]'";
		}
				
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		$filter .= " AND t2.location IN ($areaCheck)";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select t1.*,replace(
			case when coalesce(t1.leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN t1.`leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') umur,t2.pos_name, t2.location from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>$par[idGender]</td>
					<td>".getTanggal($r[birth_date])."</td>
					<td>$r[umur]</td>
					<td>".getTanggal($r[join_date])."</td>
					<td>$r[masaKerja]</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detPangkat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pangkat Pegawai</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."			
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboData("select * from mst_data where kodeCategory = 'S09' order by kodeData","kodeData","namaData","par[pangkat]","-- PANGKAT --",$par[pangkat],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Pangkat</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tgl Lahir</th>
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tgl Masuk</th>
					<th width=\"100\">Masa Kerja</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$filter = "where status='".$status."' and rank !=''";	
		
		if($par[search] == "Nama")
			$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
			)";

			if(!empty($par[idStatus])){
				$filter.=" and cat = '$par[idStatus]'";
			}

			if(!empty($par[pangkat])){
				$filter.=" and rank = '$par[pangkat]'";
			}

		$filter .= " AND location IN ($areaCheck)";
				
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData='t' order by urutanData");
		$sql="select *,replace(
			case when coalesce(leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN `leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, birth_date,  CURRENT_DATE ) % 12, ' bln') umur from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>".$arrMaster["$r[rank]"]."</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>".getTanggal($r[birth_date])."</td>
					<td>$r[umur]</td>
					<td>".getTanggal($r[join_date])."</td>
					<td>$r[masaKerja]</td>
				</tr>";
		}
		
		
		$text.="</tbody>
			</table>
			</div>";
		if($par[mode] == "xls"){
				xls();			
				$text.="<iframe src=\"download.php?d=exp&f=Data Pegawai ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			}
		
		return $text;
	}

	function detMasa(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;				
		// if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pangkat Pegawai</h1>
				<span>&nbsp;</span>
			</div>
			" . empLocHeader() . "
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "idStatus, filter, search")."			
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>
			<div id=\"pos_l\" style=\"float:left; margin-bottom:10px;\">
				<table>
					<tr>
					<td>Search : </td>
					<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboKey("par[masaKerja]",array(""=>"All","1"=>"<1 Tahun", "2" => "1-2 Tahun", "3" => "2 - 3 Tahun", "4" => "3 - 4 Tahun", "5" => "4 - 5 Tahun","10" => "5 - 10 Tahun", "15" => "11 - 15 Tahun", "20" => "16 - 20 Tahun", "25" => "21 - 25 Tahun", "30" => "26 - 30 Tahun", "31" => "> 31 Tahun"),$par[masaKerja])."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Pangkat</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Lokasi</th>
					<th width=\"100\">Status</th>
					<th width=\"100\">Tgl Lahir</th>
					<th width=\"100\">Usia</th>
					<th width=\"100\">Tgl Masuk</th>
					<th width=\"100\">Masa Kerja</th>
				</tr>
			</thead>
			<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$filter = "where status='".$status."'";

		switch ($par[masaKerja]) {
			case 1:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END < '1'";
			break;
			case 2:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END = '1' ";
			break;
			case 3:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END = '2'";
			break;
			case 4:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END = '3'";
			break;
			case 5:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END = '4'";
			break;

			case 10:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END BETWEEN '5' AND '10'";
			break;

			case 15:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END BETWEEN '11' AND '15'";
			break;

			case 20:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END BETWEEN '16' AND '20'";
			break;

			case 25:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END BETWEEN '21' AND '25'";
			break;

			case 30:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END BETWEEN '26' AND '30'";
			break;

			case 31:
				$filter.=" and case when coalesce(leave_date,NULL) IS NULL THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			WHEN `leave_date` = '0000-00-00' THEN
			TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE )
			ELSE
			TIMESTAMPDIFF(YEAR,  join_date, leave_date)	END > 30";
			break;
			
				default:
				$filter.= " ";
				break;
		}

		
		if($par[search] == "Nama")
			$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
			)";

			if(!empty($par[idStatus])){
				$filter.=" and cat = '$par[idStatus]'";
			}

			if(!empty($par[pangkat])){
				$filter.=" and rank = '$par[pangkat]'";
			}

		$filter .= " AND location IN ($areaCheck)";
				
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data where statusData='t' order by urutanData");
		$sql="select *,replace(
			case when coalesce(leave_date,NULL) IS NULL THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			WHEN `leave_date` = '0000-00-00' THEN
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			ELSE
			CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
			END,' 0 bln','') masaKerja, CONCAT(TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, birth_date,  CURRENT_DATE ) % 12, ' bln') umur from dta_pegawai $filter order by name";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>".$arrMaster["$r[rank]"]."</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[location]"]."</td>
					<td>".$arrMaster["$r[cat]"]."</td>
					<td>".getTanggal($r[birth_date])."</td>
					<td>$r[umur]</td>
					<td>".getTanggal($r[join_date])."</td>
					<td>$r[masaKerja]</td>
				</tr>";
		}
		
		
		$text.="</tbody>
			</table>
			</div>";
		if($par[mode] == "xls"){
				xls();			
				$text.="<iframe src=\"download.php?d=exp&f=Data Pegawai ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			}
		
		return $text;
	}

	function xls(){		
	global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID,$areaCheck;
	require_once 'plugins/PHPExcel.php';
	$par[idPegawai] = $cID;
	$sekarang = date('Y-m-d');
	if(empty($par[tahunHadir])) $par[tahunHadir]=date('Y');
	if(empty($par[bulanHadir])) $par[bulanHadir]=date('m');
	
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cName)
	->setLastModifiedBy($cName)
	->setTitle($arrTitle["".$_GET[p].""]);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "DATA IZIN KETIDAKHADIRAN");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "Periode : ".getBulan($par[bulanHadir])." ".$par[tahunHadir]);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');	
	$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
	$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
	$objPHPExcel->getActiveSheet()->mergeCells('F4:H4');
	$objPHPExcel->getActiveSheet()->mergeCells('I4:J4');	
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "NAMA");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "NPP");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "NOMOR");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "KETERANGAN");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "TANGGAL");
	$objPHPExcel->getActiveSheet()->setCellValue('I4', "APROVAL");
	$objPHPExcel->getActiveSheet()->setCellValue('F5', "DIBUAT");
	$objPHPExcel->getActiveSheet()->setCellValue('G5', "MULAI");
	$objPHPExcel->getActiveSheet()->setCellValue('H5', "SELESAI");
	$objPHPExcel->getActiveSheet()->setCellValue('I5', "ATASAN");
	$objPHPExcel->getActiveSheet()->setCellValue('J5', "SDM");
	
	$rows=6;		
				$filter = "where tipeIzin is null and year(t1.mulaiHadir)='$par[tahunHadir]' and month(t1.mulaiHadir)='$par[bulanHadir]' and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
						if(!empty($par[filter]))		
							$filter.= " and (
						lower(t1.nomorHadir) like '%".strtolower($par[filter])."%'
						or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
						or lower(t2.name) like '%".strtolower($par[filter])."%'	
						)";
						if(!empty($par[dept]))
						$filter.= " and t2.dept_id = '$par[dept]'";

						$filter .= " AND t2.location IN ($areaCheck)";
						$arrKat = arrayQuery("select kodeData, namaData from mst_data");
						$sql="select * from att_hadir t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorHadir";
						$res=db($sql);
						while($r=mysql_fetch_array($res)){			
							$no++;
							$persetujuanHadir = $r[persetujuanHadir] == "t"? "Disetujui" : "Belum Diproses";
							$persetujuanHadir = $r[persetujuanHadir] == "f"? "Ditolak" : $persetujuanHadir;
							$persetujuanHadir = $r[persetujuanHadir] == "r"? "Diperbaiki" : $persetujuanHadir;

							$sdmHadir = $r[sdmHadir] == "t"? "Disetujui" : "Belum Diproses";
							$sdmHadir = $r[sdmHadir] == "f"? "Ditolak" : $sdmHadir;
							$sdmHadir = $r[sdmHadir] == "r"? "Diperbaiki" : $sdmHadir;

							list($mulaiHadir) = explode(" ",$r[mulaiHadir]);
							list($selesaiHadir) = explode(" ",$r[selesaiHadir]);

			
							
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[nomorHadir]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrKat[$r[idKategori]]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getTanggal($r[tanggalHadir]));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($mulaiHadir));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getTanggal($selesaiHadir));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $persetujuanHadir);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $sdmHadir);
		
		
		$rows++;
	}
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:J'.$rows)->getAlignment()->setWrapText(true);						
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA IZIN KETIDAKHADIRAN");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."Data Pegawai ".$sekarang.".xls");
	}		
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){	
			case "detPegawai":
				$text = detPegawai();
			break;
			case "detKontrak":
				$text = detKontrak();
			break;
			case "detPensiun":
				$text = detPensiun();
			break;
			case "detPeringatan":
				$text = detPeringatan();
			break;
			case "detUmur":
				$text = detUmur();
			break;
			case "detPendidikan":
				$text = detPendidikan();
			break;
			case "detPelatihan":
				$text = detPelatihan();
			break;
			case "detPerkawinan":
				$text = detPerkawinan();
			break;
			case "detKelamin":
				$text = detKelamin();
			break;
			case "detPangkat":
				$text = detPangkat();
			break;
			case "detMasa":
				$text = detMasa();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
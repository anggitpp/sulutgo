<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fPokok = "files/pokok/";	
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;	
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData LIMIT 1");
		if(empty($par[idLokasi])) $par[idLokasi] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');		
		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
			$sekarang = date('Y-m-d');
			$sql = "select t1.*,t2.mulaiAbsen,day(t1.tanggalJadwal) as hari from dta_jadwal t1 join dta_absen t2 on (t1.tanggalJadwal = date(t2.mulaiAbsen) AND t1.idPegawai = t2.idPegawai) WHERE year(t1.tanggalJadwal) = '$par[tahunAbsen]' AND month(t1.tanggalJadwal) = '$par[bulanAbsen]'";
			// echo $sql;
			$res = db($sql);
			while ($r = mysql_fetch_assoc($res)) {
				// $tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$r['hari'];
				// if($tanggal <= $sekarang){
				$arrData['hadir'][$r['hari']]++;
				list($tanggal, $jam) = explode(" ", $r[mulaiAbsen]);
				
				if($r[mulaiJadwal] < $jam){
					$arrData['telat'][$r['hari']]++;
				}else{
					$result = "Good";
				}
				// }
				// echo $jam." = Masuk Absen - ".$r[mulaiJadwal]." = Masuk Jadwal ".$result." <br>";
			}

			$sql = "select t1.*,t2.mulaiAbsen,day(t1.tanggalJadwal) as hari from dta_jadwal t1 left join dta_absen t2 on (t1.tanggalJadwal = date(t2.mulaiAbsen) AND t1.idPegawai = t2.idPegawai) WHERE year(t1.tanggalJadwal) = '$par[tahunAbsen]' AND month(t1.tanggalJadwal) = '$par[bulanAbsen]' AND mulaiJadwal !='00:00:00' AND t2.idAbsen IS NULL";
			$res = db($sql);
			while ($r = mysql_fetch_assoc($res)) {
				// $arrData['alpha'][$i]
				// if(empty($r[idAbsen])){
					$arrData['alpha'][$r['hari']]++;
				// }
				// echo $r[idJadwal]."<br>";
			}
			// echo "Data Tanggal 1 : <br>";
			// echo "Telat = ".$arrData['telat'][1]."<br>";
			// echo "Hadir = ".$arrData['hadir'][1]."<br>";
			// echo "Alpha = ".$arrData['alpha'][1]."<br>";
			// echo $arrData['alpha'][1];
			// print_r($arrData['telat']);

			// echo "<pre>";
			// print_r($arrData['telat'][1]);
			// echo "</pre>";
			// die();				
		
		// $idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		// if(empty($par[idStatus])) $par[idStatus] = $idStatus;

		$cat1 = !empty($par[idStatus]) ? " and t1.cat = '$par[idStatus]'" : "";
		$cat2 = !empty($par[idStatus]) ? " and t2.cat = '$par[idStatus]'" : "";
		$cat = !empty($par[idStatus]) ? " and cat = '$par[idStatus]'" : "";
		
		$jmlTerlambat["&gt;60"]=0;		
		$jmlTerlambat["40-60"]=0;
		$jmlTerlambat["26-40"]=0;
		$jmlTerlambat["16-25"]=0;
		$jmlTerlambat["&lt;15"]=0;		
		
		$filterEmp = " AND location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filterEmp.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filterEmp.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filterEmp.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filterEmp.= " and unit_id='".$par[unitId]."'";

		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='pagi'");
		$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) JOIN dta_pegawai t3 ON t3.id = t1.idPegawai WHERE t3.id IS NOT NULL $filterEmp");
				
		$arrJadwal=arrayQuery("select month(t1.tanggalJadwal), t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) JOIN dta_pegawai t3 ON t3.id = t1.idPegawai where year(t1.tanggalJadwal)='".$par[tahunAbsen]."' and lower(trim(t2.namaShift)) not in ('off', 'cuti') $filterEmp");
		$arrPegawai=arrayQuery("select id, concat(reg_no,'\t',name) from dta_pegawai WHERE id IS NOT NULL $filterEmp order by id ");
		
		$sql="select * from dta_absen t1 join dta_pegawai t2 on t1.idPegawai = t2.id where year(t1.mulaiAbsen)='".$par[tahunAbsen]."' $cat2 order by t1.mulaiAbsen";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			if(isset($arrPegawai["$r[idPegawai]"])){
				list($tanggalAbsen, $mulaiAbsen)=explode(" ",$r[mulaiAbsen]);
				list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);

						
				if(intval($par[bulanAbsen]) == intval($bulanAbsen)){
										
					$mulaiJadwal = isset($arrJadwal[intval($bulanAbsen)][$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[intval($bulanAbsen)][$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];					
					if(in_array($mulaiJadwal, array("", "00:00:00"))) $mulaiJadwal = $arrNormal;
														
					if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
					list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
					list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);	

					if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){	
						$cntTerlambat++;
						$detTerlambat["$r[idPegawai]"]++;
						
						$arrTerlambat[intval($hariAbsen)]++;
						
						$d1 = $tanggalAbsen." ".$mulaiJadwal;
						$d2 = $r[mulaiAbsen];
						$menitTerlambat = selisihMenit($d1, $d2);										
						$totalTerlambat+=$menitTerlambat;
						
						if($menitTerlambat <= 15)
							$setTerlambat["&lt;15"]["$r[idPegawai]"]++;
						else if($menitTerlambat <= 25)
							$setTerlambat["16-25"]["$r[idPegawai]"]++;
						else if($menitTerlambat <= 40)
							$setTerlambat["26-40"]["$r[idPegawai]"]++;
						else if($menitTerlambat <= 60)
							$setTerlambat["40-60"]["$r[idPegawai]"]++;
						else
							$setTerlambat["&gt;60"]["$r[idPegawai]"]++;
														
						$topTerlambat["$r[idPegawai]"]+=$menitTerlambat;
						$dtaTerlambat["$r[idPegawai]"]++;
					}
					
					if(in_array($mulaiJadwal, array("", "00:00:00")) || $mulaiAbsen <= $mulaiJadwal) 
						$arrHadir[intval($hariAbsen)]++;				
					}											
				
				$arrAbsen[$tanggalAbsen]["$r[idPegawai]"]=$mulaiAbsen;
				if(!in_array($mulaiJadwal, array("", "00:00:00")) && $mulaiAbsen > $mulaiJadwal) $rkpTerlambat[intval($bulanAbsen)]["$r[idPegawai]"]++;
				
				if(in_array($mulaiJadwal, array("", "00:00:00")) || $mulaiAbsen <= $mulaiJadwal)  $rkpHadir[intval($bulanAbsen)]["$r[idPegawai]"]++;
			}
		}							
		
		$jumlahPegawai=getField("select count(*) from dta_pegawai where status='".$status."' $cat $filterEmp");			
		$jumlahTerlambat = $totalTerlambat > 60 ? floor($totalTerlambat / 60)." Jam ".($totalTerlambat%60)." Menit" : $totalTerlambat." Menit";
		$cntTerlambat = count($detTerlambat);
		$maxTerlambat = $cntTerlambat > 0 ? ceilAngka($cntTerlambat, pow(10,strlen($cntTerlambat))) : 10;
		$downTerlambat = 0.5 * $maxTerlambat;
		$upTerlambat = 0.8 * $maxTerlambat;
		
		$detKoreksi = arrayQuery("select idPegawai from att_koreksi JOIN dta_pegawai ON dta_pegawai.id = att_koreksi.idPegawai where year(mulaiKoreksi)='".$par[tahunAbsen]."' and month(mulaiKoreksi)='".$par[bulanAbsen]."' $cat $filterEmp group by 1");	
		$cntKoreksi = count($detKoreksi);
		$maxKoreksi = $cntKoreksi > 0 ? ceilAngka($cntKoreksi, pow(10,strlen($cntKoreksi))) : 10;
		$downKoreksi = 0.5 * $maxKoreksi;
		$upKoreksi = 0.8 * $maxKoreksi;
		
		$detLembur = arrayQuery("select idPegawai from att_lembur JOIN dta_pegawai ON dta_pegawai.id = att_lembur.idPegawai where year(mulaiLembur)='".$par[tahunAbsen]."' and month(mulaiLembur)='".$par[bulanAbsen]."' $cat $filterEmp group by 1");		
		$cntLembur = count($detLembur);
		$maxLembur = $cntLembur > 0 ? ceilAngka($cntLembur, pow(10,strlen($cntLembur))) : 10;
		$downLembur = 0.5 * $maxLembur;
		$upLembur = 0.8 * $maxLembur;
		
		$detCuti = arrayQuery("select idPegawai from att_cuti JOIN dta_pegawai ON dta_pegawai.id = att_cuti.idPegawai where year(mulaiCuti)='".$par[tahunAbsen]."' and month(mulaiCuti)='".$par[bulanAbsen]."' $cat $filterEmp group by 1");		
		
		$cntCuti = count($detCuti);		
		$maxCuti = $cntCuti > 0 ? ceilAngka($cntCuti, pow(10,strlen($cntCuti))) : 10;
		$downCuti = 0.5 * $maxCuti;
		$upCuti = 0.8 * $maxCuti;
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[14]."' order by urutanData");
		$arrHadir = arrayQuery("select idKategori, idPegawai, idPegawai from att_hadir JOIN dta_pegawai ON dta_pegawai.id = att_hadir.idPegawai where year(mulaiHadir)='".$par[tahunAbsen]."' and month(mulaiHadir)='".$par[bulanAbsen]."' $cat $filterEmp group by 1, 2");
		// echo "select idKategori, idPegawai, idPegawai from att_hadir JOIN dta_pegawai ON dta_pegawai.id = att_hadir.idPegawai where year(mulaiHadir)='".$par[tahunAbsen]."' and month(mulaiHadir)='".$par[bulanAbsen]."' $cat $filterEmp group by 1, 2";
		
		
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<fieldset>
						<legend>FILTER</legend>
				<div style=\"position: relative; left: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 800px;\">
						<div style=\"position:relative; left: 0px;\">

							<table>
								<tr>
									<td>
									Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
										Periode : ".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."&nbsp;&nbsp;Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "150px")."
									</td>
									<td style=\"vertical-align:top;\" id=\"bView\">
										<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'none';
										document.getElementById('bHide').style.display = 'table-cell';
										document.getElementById('dFilter').style.visibility = 'visible';							
										document.getElementById('fSet').style.height = 'auto';
										document.getElementById('fSet').style.padding = '10px';
										\">
									</td>
									<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
										<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 9px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'table-cell';
										document.getElementById('bHide').style.display = 'none';
										document.getElementById('dFilter').style.visibility = 'collapse';							
										document.getElementById('fSet').style.height = '0px';
										document.getElementById('fSet').style.padding = '0px';
										\">					
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
						
				</div>
				<br clear=\"all\"/>
				</fieldset>
			</form>	
			<br clear=\"all\"/>	
			<table style=\"width:100%; margin-bottom:10px; margin-left:-15px;\">
			<tr>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Datang Terlambat</h3></div>
					</div>
					<div id=\"divTerlambat\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var terlambatChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"25\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downTerlambat."\"/>";
					$text.="<color minValue=\"".$downTerlambat."\" maxValue=\"".$upTerlambat."\"/>";
					$text.="<color minValue=\"".$upTerlambat."\" maxValue=\"".$maxTerlambat."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntTerlambat."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"143\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntTerlambat)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartTerlambat\", \"225\", \"150\");
						chart.setXMLData( terlambatChart );
						chart.render(\"divTerlambat\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Koreksi Absen</h3></div>
					</div>
					<div id=\"divKoreksi\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKoreksi".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var koreksiChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxKoreksi."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"25\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downKoreksi."\"/>";
					$text.="<color minValue=\"".$downKoreksi."\" maxValue=\"".$upKoreksi."\"/>";
					$text.="<color minValue=\"".$upKoreksi."\" maxValue=\"".$maxKoreksi."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntKoreksi."\" bgColor=\"000000\" baseWidth=\"15\" radius=\"120\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntKoreksi)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartKoreksi\", \"225\", \"150\");
						chart.setXMLData( koreksiChart );
						chart.render(\"divKoreksi\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pengajuan Lembur</h3></div>
					</div>
					<div id=\"divLembur\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detLembur".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var lemburChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxLembur."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"25\" chartRightMargin=\"10\" gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\"  pivotRadius=\"8\" pivotFillMix=\"{CCCCCC},{000000}\" pivotBorderColor=\"000000\" >";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downLembur."\" />";
					$text.="<color minValue=\"".$downLembur."\" maxValue=\"".$upLembur."\" />";
					$text.="<color minValue=\"".$upLembur."\" maxValue=\"".$maxLembur."\" />";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntLembur."\" bgColor=\"000000\" borderAlpha=\"0\" baseWidth=\"4\" topWidth=\"4\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"150\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntLembur)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartLembur\", \"225\", \"150\");
						chart.setXMLData( lemburChart );
						chart.render(\"divLembur\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pengajuan Cuti</h3></div>
					</div>
					<div id=\"divCuti\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detCuti".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var cutiChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxCuti."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"25\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"5\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\"  gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downCuti."\"/>";
					$text.="<color minValue=\"".$downCuti."\" maxValue=\"".$upCuti."\"/>";
					$text.="<color minValue=\"".$upCuti."\" maxValue=\"".$maxCuti."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntCuti."\" bgColor=\"000000\" baseWidth=\"10\" radius=\"70\" rearExtension=\"15\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntCuti)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartCuti\", \"225\", \"150\");
						chart.setXMLData( cutiChart );
						chart.render(\"divCuti\");
					</script>
				</td>
			</tr>
			</table>
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Absensi Harian<span style=\"float:right\">".getBulan($par[bulanAbsen])." ".$par[tahunAbsen]."</span></h3></div>
			</div>
			<div id=\"divAbsen\" align=\"center\" onclick=\"window.location='?c=4&p=16&m=159&s=159&par[tanggalAbsen]=01/".$par[bulanAbsen]."/".$par[tahunAbsen]."';\"></div>
			<script type=\"text/javascript\">
			var absenChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" yAxisName=\"Orang\" palette=\"2\" canvasPadding=\"10\" bgColor=\"F7F7F7, E9E9E9\" numVDivLines=\"".$day."\" divLineAlpha=\"30\" labelPadding =\"10\" yAxisValuesPadding =\"10\" anchorRadius=\"1\" labelDisplay=\"WRAP\" showValues=\"0\" valuePosition=\"auto\" exportEnabled=\"0\" >";
			
			$text.="<categories>";
			for($i=1; $i<=$day; $i++) $text.="<category label=\"".$i."\" />";
			$text.="</categories>";

			

			$text.="<dataset seriesName=\"Alpha\" color=\"DA3608\">";
			for($i=1; $i<=$day; $i++){
				$tgl = $i;
				if($tgl <10){
					$tgl = "0".$tgl;
				}
				$tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$tgl;
				if($tanggal <= $sekarang){
				$valAlpha = isset($arrData['alpha'][$i]) ? $arrData['alpha'][$i] : 0;
				}else{
				$valAlpha = 0;
				}
					
				$text.="<set value=\"".$valAlpha."\" toolText=\"Alpha, ".$i." ".getBulan($par[bulanAbsen]).", ".getAngka($valAlpha)." Orang".$tanggal.$sekarang."\"/>";
				
			}
			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Telat\" color=\"015887\">";
			for($i=1; $i<=$day; $i++){
				$tgl = $i;
				if($tgl <10){
					$tgl = "0".$tgl;
				}
				$tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$tgl;
				if($tanggal <= $sekarang){
				$valTerlambat = isset($arrData['telat'][$i]) ? $arrData['telat'][$i] : 0;
				}else{
				$valTerlambat = 0;
				}
				// $valTerlambat = isset($arrTerlambat[$i]) ? $arrTerlambat[$i] : 0;
				$text.="<set value=\"".$valTerlambat."\" toolText=\"Telat, ".$i." ".getBulan($par[bulanAbsen]).", ".getAngka($valTerlambat)." Orang\"/>";
			}
			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Hadir\" color=\"78AE1C\">";
			for($i=1; $i<=$day; $i++){
				$tgl = $i;
				if($tgl <10){
					$tgl = "0".$tgl;
				}
				$tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$tgl;
				if($tanggal <= $sekarang){
				$valHadir = isset($arrData['hadir'][$i]) ? $arrData['hadir'][$i] : 0;
				}else{
				$valHadir = 0;
				}
				$text.="<set value=\"".$valHadir."\" toolText=\"Hadir, ".$i." ".getBulan($par[bulanAbsen]).", ".getAngka($valHadir)." Orang\"/>";
			}
			$text.="</dataset>";
			
			
			$text.="</chart>';
			var chart = new FusionCharts(\"MSLine\", \"chartAbsen\", \"100%\", 250);
				chart.setXMLData( absenChart );
				chart.render(\"divAbsen\");
			</script>
			
			<table style=\"width:100%; margin-top:30px; margin-bottom:10px;\">
			<tr>
				<td style=\"width:33%; vertical-align:top;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>TERLAMBAT (TOP 10 by Frekuensi)</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th style=\"min-width:100px;\">Nama</th>												
							<th style=\"width:50px;\">Frekuensi</th>
						</tr>
					</thead>
					<tbody>";
					$no=1;
					if(is_array($dtaTerlambat)){
						arsort($dtaTerlambat);
						reset($dtaTerlambat);
						while(list($idPegawai, $valTerlambat) = each($dtaTerlambat)){
							if($no<=10){								
								list($nikPegawai, $namaPegawai) = explode("\t", $arrPegawai[$idPegawai]);
								$text.="<tr>
										<td>$no.</td>										
										<td>".strtoupper($nikPegawai)."</td>										
										<td align=\"right\">".getAngka($valTerlambat)." Kali</td>
									</tr>";	
									$no++;
							}
						}
					}
					$text.="</tbody>
					</table>
					<a href=\"?c=4&p=19&m=176&s=176&par[tanggalAwal]=01/".$par[bulanAbsen]."/".$par[tahunAbsen]."&par[tanggalAkhir]=".date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"))."/".$par[bulanAbsen]."/".$par[tahunAbsen]."\" class=\"detil\" style=\"float:right;\">selengkapnya...</a>
				</td>
				<td style=\"width:33%; vertical-align:top; padding-left:30px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>TERLAMBAT (TOP 10 by Durasi)</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th style=\"min-width:100px;\">Nama</th>
							<th style=\"width:50px;\">Durasi</th>
						</tr>
					</thead>
					<tbody>";
					$no=1;
					if(is_array($topTerlambat)){
						arsort($topTerlambat);
						reset($topTerlambat);
						while(list($idPegawai, $valTerlambat) = each($topTerlambat)){
							if($no<=10){								
								list($nikPegawai, $namaPegawai) = explode("\t", $arrPegawai[$idPegawai]);
								$text.="<tr>
										<td>$no.</td>																				
										<td>".strtoupper($namaPegawai)."</td>
										<td align=\"right\">".getAngka($valTerlambat)." Menit</td>
									</tr>";	
									$no++;
							}
						}
					}
					$text.="</tbody>
					</table>
					<a href=\"?c=4&p=19&m=176&s=176&par[tanggalAwal]=01/".$par[bulanAbsen]."/".$par[tahunAbsen]."&par[tanggalAkhir]=".date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"))."/".$par[bulanAbsen]."/".$par[tahunAbsen]."\" class=\"detil\" style=\"float:right;\">selengkapnya...</a>
				</td>";
				
				$arrNormal=getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='pagi'");
				$arrShift=arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
				$arrJadwal=arrayQuery("select tanggalJadwal, idPegawai, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanAbsen]."' and year(t1.tanggalJadwal)='".$par[tahunAbsen]."'");		
				$arrAbsen=arrayQuery("select date(mulaiAbsen), idPegawai, mulaiAbsen from dta_absen where month(mulaiAbsen)='".$par[bulanAbsen]."' and year(mulaiAbsen)='".$par[tahunAbsen]."'");		
						
				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				if(!empty($par[idLokasi])) $filter=" and location='".$par[idLokasi]."'";
				$arrPegawai = arrayQuery("select id, concat(reg_no, '\t', name) from dta_pegawai where status='".$status."' ".$filter." order by name");
				
				$tanggalAbsen = $par[tahunAbsen]."-".$par[bulanAbsen]."-01";
				while($tanggalAbsen<=$par[tahunAbsen]."-".$par[bulanAbsen]."-".$day){											
					if(is_array($arrPegawai)){				
						reset($arrPegawai);		
						while(list($idPegawai, $valPegawai)=each($arrPegawai)){
							list($nikPegawai, $namaPegawai) = explode("\t", $valPegawai);
							
							list($kodeShift, $mulaiShift, $selesaiShift) = isset($arrShift[$idPegawai]) ?
							explode("\t", $arrShift[$idPegawai]) : explode("\t", $arrNormal) ;
											
							if(isset($arrJadwal[$tanggalAbsen][$idPegawai]))
							list($kodeShift, $mulaiShift, $selesaiShift) = explode("\t", $arrJadwal[$tanggalAbsen][$idPegawai]);
						
							if($mulaiShift == "00:00:00" && $selesaiShift == "00:00:00"  && !in_array(trim(strtolower($kodeShift)), array("c","off"))) list($kodeShift, $mulaiShift, $selesaiShift) = explode("\t", $arrNormal);
							
							if(							
								!in_array(trim(strtolower($kodeShift)), array("c","off")) &&
								!isset($arrAbsen[$tanggalAbsen][$idPegawai]) &&
								in_array(trim(strtolower($kodeShift)), array("n")) &&
								date('w', strtotime($tanggalAbsen)) > 0
							){
								$dtaAlpha[$nikPegawai]=$namaPegawai;
							}
						}
					}
					
					list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);
					$tanggalAbsen = date("Y-m-d", dateAdd("d", 1, mktime(0, 0, 0, $bulanAbsen, $hariAbsen, $tahunAbsen)));
				}				
				
		$text.="<td style=\"width:33%; vertical-align:top; padding-left:30px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>TIDAK HADIR</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:100px;\">Nama</th>
					
					<th width=\"75\">Tanggal</th>
					
				</tr>
			</thead>
			<tbody>
					";
		if(!empty($par[idLokasi])) $filter=" and t2.location='".$par[idLokasi]."'";
		if(!empty($par[idStatus])) $filter=" and t1.cat='".$par[idStatus]."'";
		if(!empty($par[divId])) $filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $filter.= " and t2.unit_id='".$par[unitId]."'";
				
		if($par[search] == "Nama")
			$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";
		
		$sql="select * from dta_absen where mulaiAbsen = '".date('Y-m-d')."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tanggalAbsen) = explode(" ", $r[mulaiAbsen]);
			list($selesaiAbsen) = explode(" ", $r[selesaiAbsen]);			
			while($tanggalAbsen <= $selesaiAbsen){				
				list($Y,$m,$d) = explode("-", $tanggalAbsen);
				$arrMasuk["".$r[idPegawai].""][$tanggalAbsen] = $r[statusAbsen];
				$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}
		
		
		// $tanggalJadwal=setTanggal($par[tanggalAkhir]) < date("Y-m-d") ? setTanggal($par[tanggalAkhir]) : date("Y-m-d");
		$no = 0;

		if(!empty($par[idLokasi])) $filter=" and t2.location='".$par[idLokasi]."'";
		if(!empty($par[idStatus])) $filter=" and t2.cat='".$par[idStatus]."'";
		if(!empty($par[divId])) $filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $filter.= " and t2.unit_id='".$par[unitId]."'";

		$sql="select * from dta_jadwal t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.tanggalJadwal = '".date('Y-m-d')."' and t1.idShift not in (select idShift from dta_shift where mulaiShift='00:00:00' and selesaiShift='00:00:00') and t1.idShift!='0' $filter order by t2.name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){		
			$cek = getField("select idAbsen from dta_absen where date(mulaiAbsen) = '$r[tanggalJadwal]' AND idPegawai = '$r[idPegawai]'");
			if(!$cek){
				if($no<=10){	
					$no++;
					$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td align=\"center\">".getTanggal($r[tanggalJadwal])."</td>
					</tr>";
				}
			}
		}
		$text.="
		</tbody>
		</table>
		<a href=\"?c=4&p=19&m=177&s=177&par[tanggalAwal]=01/".$par[bulanAbsen]."/".$par[tahunAbsen]."&par[tanggalAkhir]=".date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"))."/".$par[bulanAbsen]."/".$par[tahunAbsen]."\" class=\"detil\" style=\"float:right;\">selengkapnya...</a>
			</td>
			</tr>
			</table>
			<table>
			<tr>
			<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>TIDAK HADIR (TOP 10 by Frekuensi)</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th style=\"min-width:100px;\">Nama</th>												
							<th style=\"width:50px;\">Frekuensi</th>
						</tr>
					</thead>
					<tbody>";
					
					$month = date('m');
					if ($month < 10) {
						$month = "0".$month;
					}

					if($month == $par[bulanAbsen] AND date('Y')==$par[tahunAbsen]){
						$day = date('d') - 1;
					}else{
						$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));
					}

					$sql = "select *, day(tanggalJadwal) as hariAbsen from dta_jadwal where month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal) = '$par[tahunAbsen]'";
					$res = db($sql);
					while ($r = mysql_fetch_array($res)) {
						if ($r[hariAbsen] <= $day) {
							$arrJadwal[$r['tanggalJadwal']][$r['idPegawai']] = $r['idPegawai'];
						}
					}

					$sql = "select *,date(mulaiAbsen) as tanggalAbsen, day(mulaiAbsen) as hariAbsen from dta_absen where month(mulaiAbsen) = '$par[bulanAbsen]' AND year(mulaiAbsen) = '$par[tahunAbsen]'";
					$res = db($sql);
					while($r = mysql_fetch_array($res)){
						if ($r[hariAbsen] <= $day) {
						$arrAbseseni[$r['tanggalAbsen']][$r['idPegawai']] = $r['idPegawai'];
						}
					}

					foreach ($arrJadwal as $tanggalJadwal => $value) {
						foreach ($value as $idPegawai) {
							if (empty($arrAbseseni[$tanggalJadwal][$idPegawai])) {
								// if()
								$arrAlpha[$idPegawai]++;
							}				
						}
					}



					$no=1;
					if(is_array($arrAlpha)){
						arsort($arrAlpha);
						reset($arrAlpha);
						while(list($idPegawai, $valAlpha) = each($arrAlpha)){
							if($no<=10){								
								list($nikPegawai, $namaPegawai) = explode("\t", $arrPegawai[$idPegawai]);
								if (!empty($namaPegawai)) {
									$text.="<tr>
											<td>$no.</td>										
											<td>".strtoupper($namaPegawai)."</td>										
											<td align=\"right\">".getAngka($valAlpha)." Kali</td>
										</tr>";	
										$no++;
								}
							}
						}
					}

					$text.="</tbody>
					</table>
					</tr>
					</table>
			<table style=\"width:100%; margin-top:30px; margin-bottom:10px;\">
			<tr>
				<td style=\"width:50%; vertical-align:top;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pengajuan Izin</h3></div>
					</div>
					<div id=\"divIzin\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detIzin".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var izinChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" xAxisName=\"Pengajuan Izin\" yAxisName=\"Orang\">";
					
					if(is_array($arrKategori)){						
						reset($arrKategori);
						while(list($idKategori, $namaKategori) = each($arrKategori)){							
							$text.="<set label=\"".$namaKategori."\" value=\"".count($arrHadir[$idKategori])."\"/> ";
						}
					}
					
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Bar2D\", \"chartIzin\", \"100%\", 250);
						chart.setXMLData( izinChart );
						chart.render(\"divIzin\");
					</script>					
				</td>
				<td style=\"width:50%; vertical-align:top; padding-left:30px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Terlambat : ".$jumlahTerlambat."</h3></div>
					</div>
					<div id=\"divTelat\" align=\"center\"  onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var telatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" xAxisName=\"Waktu - Menit\" yAxisName=\"Orang\">";
					
					if(is_array($jmlTerlambat)){
						reset($jmlTerlambat);
						while(list($idTerlambat) = each($jmlTerlambat)){
							$text.="<set label=\"".$idTerlambat."\" value=\"".count($setTerlambat[$idTerlambat])."\"/> ";
						}
					}
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Bar3D\", \"telatRekap\", \"100%\", 250);
						chart.setXMLData( telatChart );
						chart.render(\"divTelat\");
					</script>
				</td>
			</tr>
			</table>
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Rekap Absen $par[tahunAbsen]</h3></div>
			</div>
			<div id=\"divRekap\" align=\"center\" onclick=\"window.location='?c=4&p=16&m=160&s=160&par[bulanAbsen]=".$par[bulanAbsen]."&par[tahunAbsen]=".$par[tahunAbsen]."';\"></div>
			<script type=\"text/javascript\">
			var rekapChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" yAxisName=\"Orang\" palette=\"2\" canvasPadding=\"10\" bgColor=\"F7F7F7, E9E9E9\" numVDivLines=\"".$day."\" divLineAlpha=\"30\" labelPadding =\"10\" yAxisValuesPadding =\"10\" anchorRadius=\"1\" labelDisplay=\"WRAP\" showValues=\"0\" valuePosition=\"auto\" exportEnabled=\"0\" >";

			$bulan = date('m');
			
			$text.="<categories>";
			for($b=1; $b<=12; $b++) $text.="<category label=\"".getBulan($b)."\" />";
			$text.="</categories>";

			
														
			$text.="<dataset seriesName=\"Alpha\" color=\"DA3608\">";
			
			for($i=1; $i<=$day; $i++){
				$valTerlambat = isset($rkpTerlambat[$i]) ? count($rkpTerlambat[$i]) : 0;
				$valHadir = isset($rkpHadir[$i]) ? count($rkpHadir[$i]) : 0;
				$valAlpha = $jumlahPegawai - $valTerlambat - $valHadir;
				$valAlpha = $i <= $bulan ? $valAlpha : 0;
				$text.="<set value=\"".$valAlpha."\" toolText=\"Alpha, ".getBulan($i).", ".getAngka($valAlpha)." Orang\"/>";
			}

			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Telat\" color=\"015887\">";
			for($i=1; $i<=12; $i++){
				$valTerlambat = isset($rkpTerlambat[$i]) ? count($rkpTerlambat[$i]) : 0;
				$valTerlambat = $i <= $bulan ? $valTerlambat : 0;
				$text.="<set value=\"".$valTerlambat."\" toolText=\"Telat, ".getBulan($i).", ".getAngka($valTerlambat)." Orang\"/>";
			}
			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Hadir\" color=\"78AE1C\">";
			for($i=1; $i<=12; $i++){
				$valHadir = isset($rkpHadir[$i]) ? count($rkpHadir[$i]) : 0;
				$valHadir = $i <= $bulan ? $valHadir : 0;
				$text.="<set value=\"".$valHadir."\" toolText=\"Hadir, ".getBulan($i).", ".getAngka($valHadir)." Orang\"/>";
			}
			$text.="</dataset>";
				
			$text.="</chart>';
			var chart = new FusionCharts(\"StackedColumn3D\", \"chartRekap\", \"100%\", 250);
				chart.setXMLData( rekapChart );
				chart.render(\"divRekap\");
			</script>";
					
		return $text;
	}	
	
	function detTerlambat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Datang Terlambat</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:100px;\">Nama</th>
					<th width=\"100\">NPP</th>															
					<th width=\"100\">Jumlah Hari</th>					
					<th width=\"100\">Total Durasi</th>
				</tr>
			</thead>
			<tbody>";
		
		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='pagi'");
		$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		
		$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where year(t1.tanggalJadwal)='".$par[tahunAbsen]."' and month(t1.tanggalJadwal)='".$par[bulanAbsen]."' not in ('off', 'cuti')");
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";
		
		$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where year(t2.mulaiAbsen)='".$par[tahunAbsen]."' and month(t2.mulaiAbsen)='".$par[bulanAbsen]."' ".$filter." order by t1.name, t2.mulaiAbsen ";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			list($tanggalAbsen, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);
			$mulaiJadwal = isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];
			
			if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
			list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
			list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);
			
			if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){		
								
				$d1 = $tanggalAbsen." ".$mulaiJadwal;
				$d2 = $r[mulaiAbsen];
				$durasiMenit = selisihMenit($d1, $d2);
				$detTerlambat["$r[idPegawai]"]++;
				$totTerlambat["$r[idPegawai]"]+=$durasiMenit;
				$arrPegawai["$r[idPegawai]"]=$r;
			}	
		}
		
		if(is_array($detTerlambat)){			
			reset($detTerlambat);
			while(list($idPegawai, $valTerlambat) = each($detTerlambat)){
				$r=$arrPegawai[$idPegawai];
				$no++;
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>						
						<td align=\"right\">".getAngka($valTerlambat)." Hari</td>					
						<td align=\"right\">".getAngka($totTerlambat[$idPegawai])." Menit</td>
						</tr>";	
			}
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detKoreksi(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Koreksi Absen</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" width=\"100\">Nomor</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th colspan=\"3\" width=\"175\">Koreksi</th>
				</tr>
				<tr>
					<th width=\"75\">Tanggal</th>
					<th width=\"50\">Datang</th>
					<th width=\"50\">Pulang</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorKoreksi is not null";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.mulaiKoreksi)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.mulaiKoreksi)='$par[tahunAbsen]'";		
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorKoreksi) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_koreksi t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorKoreksi";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanKoreksi;
			
			$sdmKoreksi = $r[sdmKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmKoreksi = $r[sdmKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKoreksi;			
			
			list($mulaiKoreksi) = explode(" ",$r[mulaiKoreksi]);
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorKoreksi]</td>
					<td align=\"center\">".getTanggal($r[tanggalKoreksi])."</td>
					<td align=\"center\">".getTanggal($mulaiKoreksi)."</td>
					<td align=\"center\">".substr($r[masukKoreksi],0,5)."</td>
					<td align=\"center\">".substr($r[pulangKoreksi],0,5)."</td>
					</tr>";				
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detLembur(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pengajuan Lembur</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:100px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" style=\"min-width:100px;\">No. SPL</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>					
					<th colspan=\"3\" width=\"150\">Izin Lembur</th>							
				</tr>
				<tr>					
					<th width=\"50\">In</th>
					<th style=\"50\">Out</th>
					<th style=\"50\">Durasi</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where t1.idPegawai is not null";
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalJadwal)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalJadwal)='$par[tahunAbsen]'";		
		
		$arrShift=arrayQuery("select t1.idPegawai, t2.kodeShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select t1.idPegawai, t1.tanggalJadwal, t2.kodeShift from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) $filter order by t1.idJadwal");
		
		$filter = "where idPegawai is not null";
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(tanggalAbsen)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(tanggalAbsen)='$par[tahunAbsen]'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, concat(masukAbsen, '\t', pulangAbsen, '\t', durasiAbsen) from att_absen $filter order by idAbsen");
				
		$filter = "where nomorLembur is not null";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.mulaiLembur)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.mulaiLembur)='$par[tahunAbsen]'";						
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";		
				
		$sql="select * from att_lembur t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;
			
			list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);					
			list($mulaiLembur_jam, $mulaiLembur_menit, $mulaiLembur_detik) = explode(":", $mulaiLembur);
			
			list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
			list($selesaiLembur_tahun, $selesaiLembur_bulan, $selesaiLembur_hari) = explode("-", $selesaiLembur_tanggal);
			list($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik) = explode(":", $selesaiLembur);
			
			$tanggalLembur = $mulaiLembur_tanggal;
			$jadwalShift = empty($arrJadwal["$r[id]"][$tanggalLembur]) ? $arrShift["$r[id]"] : $arrJadwal["$r[id]"][$tanggalLembur];
			$durasiLembur = $mulaiLembur_jam > $selesaiLembur_jam ?
			selisihJam($r[mulaiLembur], date('Y-m-d H:i:s', dateAdd("d", 1, mktime($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik, $selesaiLembur_bulan, $selesaiLembur_hari, $selesaiLembur_tahun)))):
			selisihJam($r[mulaiLembur], $r[selesaiLembur]);
			
			list($mulaiAbsen, $selesaiAbsen, $durasiAbsen) = explode("\t", $arrAbsen["$r[id]"][$tanggalLembur]);
			$week = date("w", strtotime($tanggalLembur));
			$hariLembur = (getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($jadwalShift, array("OFF","C")) || (in_array($week, array(0,6)) && in_array($jadwalShift, array("N")))) ? "LIBUR" : "KERJA";
			$overtimeLembur = empty($r[overtimeLembur]) ? $durasiAbsen : $r[overtimeLembur];
			
			$text.="<tr>
					<td>$no.</td>		
					<td>$r[name]</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorLembur]</td>
					<td align=\"center\">".getTanggal($tanggalLembur)."</td>					
					<td align=\"center\">".substr($mulaiLembur,0,5)."</td>
					<td align=\"center\">".substr($selesaiLembur,0,5)."</td>
					<td align=\"center\">".getAngka($durasiLembur)."</td>				
				</tr>";
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detCuti(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pengajuan Cuti</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" width=\"100\">Nomor</th>					
					<th colspan=\"3\" width=\"175\">Tanggal</th>
				</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"50\">Mulai</th>
					<th width=\"50\">Selesai</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorCuti is not null";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalCuti)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalCuti)='$par[tahunAbsen]'";				
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorCuti) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_cuti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorCuti";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanCuti = $r[persetujuanCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanCuti = $r[persetujuanCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanCuti;
			
			$sdmCuti = $r[sdmCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmCuti = $r[sdmCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmCuti;			
			
			list($mulaiCuti) = explode(" ",$r[mulaiCuti]);
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorCuti]</td>
					<td align=\"center\">".getTanggal($r[tanggalCuti])."</td>					
					<td align=\"center\">".getTanggal($r[mulaiCuti])."</td>
					<td align=\"center\">".getTanggal($r[selesaiCuti])."</td>
					</tr>";				
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detIzin(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Pengajuan Izin</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Nomor</th>					
					<th width=\"75\">Tanggal</th>
					<th width=\"75\">Mulai</th>
					<th width=\"75\">Selesai</th>										
				</tr>
			</thead>
			<tbody>";
		
				
		$filter = "where nomorIzin is not null";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalIzin)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalIzin)='$par[tahunAbsen]'";				
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorIzin) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from dta_izin t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.tanggalIzin, t1.nomorIzin";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanIzin = $r[persetujuanIzin] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanIzin = $r[persetujuanIzin] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanIzin;
			
			$sdmIzin = $r[sdmIzin] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmIzin = $r[sdmIzin] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmIzin;						
			
			list($mulaiIzin) = explode(" ",$r[mulaiIzin]);
			list($selesaiIzin) = explode(" ",$r[selesaiIzin]);
			$mulaiIzin = getTanggal($mulaiIzin);
			$selesaiIzin = getTanggal($selesaiIzin);
			
			if($r[keteranganIzin] == "Izin Sementara"){
				list($mulaiTanggal, $mulaiIzin) = explode(" ",$r[mulaiIzin]);
				list($mulaiTanggal, $selesaiIzin) = explode(" ",$r[selesaiIzin]);
				$mulaiIzin = substr($mulaiIzin,0,5);
				$selesaiIzin = substr($selesaiIzin,0,5);
				$sdmIzin = "";
			}
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorIzin]</td>
					<td align=\"center\">".getTanggal($r[tanggalIzin])."</td>					
					<td align=\"center\">".$mulaiIzin."</td>
					<td align=\"center\">".$selesaiIzin."</td>										
					</tr>";				
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){	
			case "detTerlambat":
				$text = detTerlambat();
			break;
			case "detKoreksi":
				$text = detKoreksi();
			break;
			case "detLembur":
				$text = detLembur();
			break;
			case "detCuti":
				$text = detCuti();
			break;
			case "detIzin":
				$text = detIzin();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
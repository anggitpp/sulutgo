<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername,$cID, $areaCheck;						
	$par[idPegawai] = $cID;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."				
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
				Periode : ".comboMonth("par[bulanData]", $par[bulanData], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunData]", $par[tahunData], "", "onchange=\"document.getElementById('form').submit();\"")."
			</div>				
		</form>	";				
		
		$sql_="select * from dta_pegawai where (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."') AND location IN ($areaCheck)";
		$res_=db($sql_);
		if(mysql_num_rows($res_)){
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$jmlTerlambat["&gt;60"]=0;		
			$jmlTerlambat["40-60"]=0;
			$jmlTerlambat["26-40"]=0;
			$jmlTerlambat["16-25"]=0;
			$jmlTerlambat["&lt;15"]=0;
			
			$kontrakBulan = $par[bulanData] > 9 ? $par[bulanData] + 3 - 12 : $par[bulanData] + 3;
			$kontrakTahun = $par[bulanData] > 9 ? $par[tahunData] + 1 : $par[tahunData];		
			$kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);
			$sql="select * from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= ".$kontrakMax." and t2.status='1' and t1.status='".$status."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck)";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
				$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date];
				$cntKontrak++;
			}
			
			$cntPeringatan=getField("select count(t2.id) from dta_pegawai t1 join emp_punish t2 on (t1.id=t2.parent_id) where '".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)."' between concat(year(t2.pnh_date_start), LPAD(month(t2.pnh_date_start),2,'0')) and concat(year(t2.pnh_date_end), LPAD(month(t2.pnh_date_end),2,'0')) and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck)");
			
			$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='normal'");
			$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");

			$arrJadwal=arrayQuery("select month(t1.tanggalJadwal), t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_pegawai t0 join dta_jadwal t1 join dta_shift t2 on (t0.id=t1.idPegawai and t1.idShift=t2.idShift) where year(t1.tanggalJadwal)='".$par[tahunData]."' and month(t1.tanggalJadwal)='".$par[bulanData]."' and lower(trim(t2.namaShift)) not in ('off', 'cuti') and (t0.leader_id='".$par[idPegawai]."' or t0.administration_id='".$par[idPegawai]."') AND t0.location IN ($areaCheck)");
			
			$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where year(t2.mulaiAbsen)='".$par[tahunData]."' and month(t2.mulaiAbsen)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) order by t2.mulaiAbsen";
			$res=db($sql);		
			while($r=mysql_fetch_array($res)){			
				list($tanggalAbsen, $mulaiAbsen)=explode(" ",$r[mulaiAbsen]);
				list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);
				
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
					
					if($menitTerlambat > 25){							
						$dtaTerlambat["$r[idPegawai]"] = $tanggalAbsen."\t".$menitTerlambat."\t".$r[name]."\t".$mulaiAbsen;
					}
				}								
			}
			
			$jumlahTerlambat = $totalTerlambat > 60 ? floor($totalTerlambat / 60)." Jam ".($totalTerlambat%60)." Menit" : $totalTerlambat." Menit";
			$cntTerlambat = count($detTerlambat);
			$maxTerlambat = $cntTerlambat > 0 ? ceilAngka($cntTerlambat, pow(10,strlen($cntTerlambat))) : 10;
			$downTerlambat = 0.5 * $maxTerlambat;
			$upTerlambat = 0.8 * $maxTerlambat;
			
			$cntKlaim = getField("select count(*) from dta_pegawai t1 join emp_rmb t2 on (t1.id=t2.parent_id) where year(t2.rmb_date)='".$par[tahunData]."' and month(t2.rmb_date)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck)");
			$payKlaim = getField("select count(*) from dta_pegawai t1 join emp_rmb t2 on (t1.id=t2.parent_id) where year(t2.rmb_date)='".$par[tahunData]."' and month(t2.rmb_date)='".$par[bulanData]."' and t2.pay_status=1 and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck)");
			$maxKlaim = $cntKlaim > 0 ? ceilAngka($cntKlaim, pow(10,strlen($cntKlaim))) : 10;
			$downKlaim = 0.5 * $maxKlaim;
			$upKlaim = 0.8 * $maxKlaim;		
			
			$detLembur = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_lembur t2 on (t1.id=t2.idPegawai) where year(t2.mulaiLembur)='".$par[tahunData]."' and month(t2.mulaiLembur)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");		
			$cntLembur = count($detLembur);			
			
			$detCuti = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_cuti t2 on (t1.id=t2.idPegawai) where year(t2.mulaiCuti)='".$par[tahunData]."' and month(t2.mulaiCuti)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");
			$cntCuti = count($detCuti);					
			
			$detSementara = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_izin t2 on (t1.id=t2.idPegawai) where year(t2.mulaiIzin)='".$par[tahunData]."' and month(t2.mulaiIzin)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");
			$cntSementara = count($detSementara);
			
			$detSakit = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_sakit t2 on (t1.id=t2.idPegawai) where year(t2.mulaiSakit)='".$par[tahunData]."' and month(t2.mulaiSakit)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");
			$cntSakit = count($detSakit);
			
			$detPelatihan = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_pelatihan t2 on (t1.id=t2.idPegawai) where year(t2.mulaiPelatihan)='".$par[tahunData]."' and month(t2.mulaiPelatihan)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");				
			$cntPelatihan = count($detPelatihan);
			
			$detDinas = arrayQuery("select t2.idPegawai from dta_pegawai t1 join att_dinas t2 on (t1.id=t2.idPegawai) where year(t2.mulaiDinas)='".$par[tahunData]."' and month(t2.mulaiDinas)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1");
			$cntDinas = count($detDinas);
			
			$maxKontrak = $cntKontrak > 0 ? ceilAngka($cntKontrak, pow(10,strlen($cntKontrak))) : 10;
			$downKontrak = 0.5 * $maxKontrak;
			$upKontrak = 0.8 * $maxKontrak;
			
			$maxPeringatan = $cntPeringatan > 0 ? ceilAngka($cntPeringatan, pow(10,strlen($cntPeringatan))) : 10;
			$downPeringatan = 0.5 * $maxPeringatan;
			$upPeringatan = 0.8 * $maxPeringatan;
			
			$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData");
			$arrHadir = arrayQuery("select t2.idKategori, t2.idPegawai, t1.id from dta_pegawai t1 join att_hadir t2  on (t1.id=t2.idPegawai) where year(t2.mulaiHadir)='".$par[tahunData]."' and month(t2.mulaiHadir)='".$par[bulanData]."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck) group by 1, 2");
			
			$text.="<table style=\"width:100%; margin-bottom:10px; margin-left:-15px;\">
			<tr>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Habis Kontrak</h3></div>
					</div>
					<div id=\"divKontrak\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKontrak".getPar($par,"mode")."',875,450);\"></div>
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
						$text.="<annotation type=\"text\" y=\"140\" label=\"Kurang dari 3 Bulan\" fontColor=\"000000\" fontSize=\"15\" />";
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
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Surat Peringatan</h3></div>
					</div>
					<div id=\"divPeringatan\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPeringatan".getPar($par,"mode")."',875,450);\"></div>
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
						var chart = new FusionCharts(\"AngularGauge\", \"chartPeringatan\", \"225\", \"175\");
						chart.setXMLData( peringatanChart );
						chart.render(\"divPeringatan\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Datang Terlambat</h3></div>
					</div>
					<div id=\"divTerlambat\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
						var terlambatChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";

						$text.="<colorRange>";
						$text.="<color minValue=\"0\" maxValue=\"".$downTerlambat."\"/>";
						$text.="<color minValue=\"".$downTerlambat."\" maxValue=\"".$upTerlambat."\"/>";
						$text.="<color minValue=\"".$upTerlambat."\" maxValue=\"".$maxTerlambat."\"/>";
						$text.="</colorRange>";

						$text.="<dials>";
						$text.="<dial value=\"".$cntTerlambat."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
						$text.="</dials>";

						$text.="<annotations>";
						$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
						$text.="<annotation type=\"text\" y=\"120\" label=\"".getAngka($cntTerlambat)." Orang\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
						$text.="<annotation type=\"text\" y=\"140\" label=\"Datang Terlambat\" fontColor=\"000000\" fontSize=\"15\" />";
						$text.="</annotationGroup>";
						$text.="</annotations>";

						$text.="</chart>';
						var chart = new FusionCharts(\"AngularGauge\", \"chartTerlambat\", \"225\", \"175\");
						chart.setXMLData( terlambatChart );
						chart.render(\"divTerlambat\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Klaim Total</h3></div>
					</div>
					<div id=\"divKlaim\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKlaim".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
						var klaimChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxKlaim."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\"  pivotRadius=\"8\" pivotFillMix=\"{CCCCCC},{000000}\" pivotBorderColor=\"000000\" >";

						$text.="<colorRange>";
						$text.="<color minValue=\"0\" maxValue=\"".$downKlaim."\" />";
						$text.="<color minValue=\"".$downKlaim."\" maxValue=\"".$upKlaim."\" />";
						$text.="<color minValue=\"".$upKlaim."\" maxValue=\"".$maxKlaim."\" />";
						$text.="</colorRange>";

						$text.="<dials>";
						$text.="<dial value=\"".$cntKlaim."\" bgColor=\"000000\" borderAlpha=\"0\" baseWidth=\"4\" topWidth=\"4\"/>";
						$text.="</dials>";

						$text.="<annotations>";
						$text.="<annotationGroup x=\"150\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
						$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntKlaim)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";					
						$text.="<annotation type=\"text\" y=\"150\" label=\"".getAngka($payKlaim)." Dibayar\" fontColor=\"000000\" fontSize=\"15\"/>";
						$text.="</annotationGroup>";
						$text.="</annotations>";

						$text.="</chart>';
						var chart = new FusionCharts(\"AngularGauge\", \"chartKlaim\", \"225\", \"175\");
						chart.setXMLData( klaimChart );
						chart.render(\"divKlaim\");
					</script>
				</td>
			</tr>
		</table>";

		$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[14]."' order by urutanData");
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pegawai</h3></div>
	</div>
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>					
				<th style=\"min-width:150px;\">Nama</th>
				<th style=\"width:100px;\">Nik</th>
				<th style=\"min-width:150px;\">Pangkat</th>
				<th style=\"min-width:150px;\">Jabatan</th>
				<th style=\"min-width:150px;\">Lokasi</th>
				<th style=\"min-width:50px;\">Kontrol</th>
			</tr>
		</thead>
		<tbody>";
			$no=1;
			while($r_=mysql_fetch_array($res_)){
				$text.="<tr>
				<td>$no.</td>
				<td>".strtoupper($r_[name])."</td>
				<td>$r_[reg_no]</td>
				<td>".strtoupper($arrPangkat["$r_[rank]"])."</td>
				<td>".strtoupper($r_[pos_name])."</td>
				<td>".strtoupper($arrLokasi["$r_[location]"])."</td>
				<td align=\"center\"><a href=\"?par[mode]=det&par[idPegawai]=$r_[id]&empid=$r_[id]".getPar($par,"mode,idPegawai")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>
			</tr>";	
			$no++;
		}
		$text.="</tbody>
	</table>";

	$text.="<table style=\"width:100%; margin-top:30px; margin-bottom:10px;\">
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
			<div id=\"divTelat\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
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
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Habis Kontrak (± 3 Bulan)</h3></div>
</div>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
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
";
}	
return $text;
}	

function detail(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
	$_SESSION["curr_emp_id"] = $par[idPegawai];		
	require_once "sources/employee/identity.php";
}

function detKontrak(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
	$kontrakBulan = $par[bulanData] > 9 ? $par[bulanData] + 3 - 12 : $par[bulanData] + 3;
	$kontrakTahun = $par[bulanData] > 9 ? $par[tahunData] + 1 : $par[tahunData];		
	$kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);
	$sql="select * from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= ".$kontrakMax." and t2.status='1' and t1.status='".$status."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') AND t1.location IN ($areaCheck)";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$r[location] = empty($r[loc1]) ? $r[location] : $r[loc1];
		$arrKontrak["$r[id]"]=$r[name]."\t".$r[rank]."\t".$r[pos_name]."\t".$r[location]."\t".$r[start_date]."\t".$r[end_date];
	}

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Habis Kontrak (± 3 Bulan)</h1>
		<div style=\"margin-top:10px;\">
			".getBread("Habis Kontrak (± 3 Bulan)")."
		</div>
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "   
	<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<br clear=\"all\" />
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
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
	</div>";

	return $text;
}

function detPeringatan(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
	$par[idPegawai] = $cID;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Surat Peringatan</h1>
		<div style=\"margin-top:10px;\">
			".getBread("Surat Peringatan")."
		</div>
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<br clear=\"all\" />
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:100px;\">Nama</th>
					<th style=\"width:100px;\">NPP</th>
					<th style=\"width:100px;\">Nomor</th>
					<th style=\"width:75px;\">Tanggal</th>
					<th style=\"min-width:100px;\">Perihal</th>					
				</tr>
			</thead>
			<tbody>";
				$no=1;
				$sql="select * from dta_pegawai t1 join emp_punish t2 on (t1.id=t2.parent_id) where '".$par[tahunData].str_pad($par[bulanData], 2, "0", STR_PAD_LEFT)."' between concat(year(t2.pnh_date_start), LPAD(month(t2.pnh_date_start),2,'0')) and concat(year(t2.pnh_date_end), LPAD(month(t2.pnh_date_end),2,'0')) and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."')";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>".$r[reg_no]."</td>
					<td>".$r[pnh_no]."</td>
					<td align=\"center\">".getTanggal($r[pnh_date_start])."</td>
					<td>".$r[pnh_subject]."</td>						
				</tr>";	
				$no++;				
			}
			$text.="</tbody>
		</table>
	</div>";

	return $text;
}

function detTerlambat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
	$par[idPegawai] = $cID;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Datang Terlambat</h1>
		<div style=\"margin-top:10px;\">
			".getBread("Datang Terlambat")."
		</div>
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
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

				$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='normal'");
				$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");


				$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_pegawai t0 join dta_jadwal t1 join dta_shift t2 on (t0.id=t1.idPegawai and t1.idShift=t2.idShift) where year(t1.tanggalJadwal)='".$par[tahunData]."' and month(t1.tanggalJadwal)='".$par[bulanData]."' and lower(trim(t2.namaShift)) not in ('off', 'cuti') and (t0.leader_id='".$par[idPegawai]."' or t0.administration_id='".$par[idPegawai]."')");

				if($par[search] == "Nama")
					$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where year(t2.mulaiAbsen)='".$par[tahunData]."' and month(t2.mulaiAbsen)='".$par[bulanData]."'  and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') ".$filter." order by t1.name, t2.mulaiAbsen ";
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

function detKlaim(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
	$par[idPegawai] = $cID;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Klaim Total</h1>
		<div style=\"margin-top:10px;\">
			".getBread("Klaim Total")."
		</div>
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
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
				<th style=\"min-width:150px;\">Kategori/Tipe</th>
				<th width=\"100\">Nilai</th>
			</tr>
		</thead>
		<tbody>";

			$filter = "where rmb_jenis!='' and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";		
			if(!empty($par[bulan]))
				$filter.= " and month(t1.rmb_date)='$par[bulan]'";
			if(!empty($par[tahun]))
				$filter.= " and year(t1.rmb_date)='$par[tahun]'";				
			if(!empty($par[lokasi]))
				$filter.= " and t2.location='".$par[lokasi]."'";
			if(!empty($par[filter]))		
				$filter.= " and (
			lower(t1.rmb_no) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			or lower(t2.name) like '%".strtolower($par[filter])."%'
			)";

			$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[22]."'");
			$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[23]."'");

			$arrKategori_ = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[17]."'");
			$arrTipe_ = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[18]."'");		

			$sql="select t1.*, t2.name, t2.reg_no from emp_rmb t1 left join dta_pegawai t2 on (t1.parent_id=t2.id) $filter order by t1.rmb_no";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){			
				$no++;

				if(empty($r[status])) $r[status] = 0;
				$status = $r[status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
				$status = $r[status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $status;
				$status = $r[status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $status;						

				$bayar = empty($r[pay_date]) ? "<img src=\"styles/images/f.png\" title=\"Belum Dibayar\">" : "<img src=\"styles/images/t.png\" title=\"Sudah Dibayar\">";


				$text.="<tr>
				<td>$no.</td>
				<td>$r[name]</td>					
				<td>$r[reg_no]</td>
				<td>$r[rmb_no]</td>					
				<td align=\"center\">".getTanggal($r[rmb_date])."</td>
				<td>";
					$text.=$r[rmb_jenis] == "k" ? "".$arrKategori["$r[rmb_cat]"]."" : "".$arrKategori_["$r[rmb_cat]"]." - ".$arrTipe_["$r[rmb_type]"]."";
					$text.="</td>
					<td align=\"right\">".getAngka($r[rmb_val])."</td>					
				</tr>";				
			}	

			$text.="</tbody>
		</table>
	</div>";

	return $text;
}

function detIzin(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
	if(empty($par[bulanData])) $par[bulanData] = date('m');
	if(empty($par[tahunData])) $par[tahunData] = date('Y');

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Pengajuan Izin</h1>
		<div style=\"margin-top:10px;\">
			".getBread("Pengajuan Izin")."
		</div>
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
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


			$filter = "where nomorIzin is not null and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";		
			if(!empty($par[bulanData]))
				$filter.= " and month(t1.tanggalIzin)='$par[bulanData]'";
			if(!empty($par[tahunData]))
				$filter.= " and year(t1.tanggalIzin)='$par[tahunData]'";				

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
		case "det":
		$text = detail();
		break;
		case "detPeringatan":
		$text = detPeringatan();
		break;
		case "detKontrak":
		$text = detKontrak();
		break;
		case "detTerlambat":
		$text = detTerlambat();
		break;
		case "detKlaim":
		$text = detKlaim();
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
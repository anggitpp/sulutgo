<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername,$cID;						
		$par[idPegawai] = $cID;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');	


				
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData");
		$arrHadir = arrayQuery("select idKategori, idPegawai, idPegawai from att_hadir where year(mulaiHadir)='".$par[tahunAbsen]."' and month(mulaiHadir)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."' group by 1, 2");
		
		$jmlTerlambat["&gt;60"]=0;		
		$jmlTerlambat["40-60"]=0;
		$jmlTerlambat["26-40"]=0;
		$jmlTerlambat["16-25"]=0;
		$jmlTerlambat["&lt;15"]=0;

		$arrAbsen = arrayQuery("select date(mulaiAbsen), idAbsen from dta_absen where idPegawai = '$par[idPegawai]' AND month(mulaiAbsen) = '$par[bulanAbsen]' AND year(mulaiAbsen) = '$par[tahunAbsen]'");
		$d=cal_days_in_month(CAL_GREGORIAN,7,2018);
		// echo "<pre>";
		// print_r($arrAbsen);
		// echo "</pre>";

		if($par[bulanAbsen] != date('m')){
			for ($i=1; $i <= $d; $i++) { 
				if($i<10){
				$i = "0".$i;
				}


				$tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$i;			
				$weekDay = date('w', strtotime($tanggal));
				if($weekDay < 6 && $weekDay !=0){
					if($par[tahunAbsen]<=date('Y')){
						if(empty($arrAbsen[$tanggal])){
							$cntAlpha++;
							// echo $tanggal;
						}
					}
				}
   			
			}
		}else{
			for ($i=1; $i <= date('d'); $i++) { 
				if($i<10){
				$i = "0".$i;
				}


				$tanggal = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$i;			
				$weekDay = date('w', strtotime($tanggal));
				if($weekDay < 6 && $weekDay !=0){
					if($par[tahunAbsen]<=date('Y')){
						if(empty($arrAbsen[$tanggal])){
							$cntAlpha++;
							// echo $tanggal;
						}
					}
				}
   			
			}
		}
		// echo $cntAlpha;



		// $arrJadwal = arrayQuery("select tanggalJadwal, idJadwal from dta_absen where idPegawai = '$par[idPegawai]' AND month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal) = '$par[tahunAbsen]'");
		// echo "select date(mulaiAbsen), idAbsen where idPegawai = '$par[idPegawai]' AND month(mulaiAbsen) = '$par[bulanAbsen]' AND year(mulaiAbsen) = '$par[tahunAbsen]'";
		// echo "select tanggalJadwal, idJadwal from dta_jadwal where idPegawai = '$par[idPegawai]' AND month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal) = '$par[tahunAbsen]'";
		// echo "<pre>";
		// print_r($arrAbsen);
		// echo "</pre>";

		// if(is_array($arrAbsen)){
		// 	reset($arrAbsen);
		// 	while(list($tanggalAbsen) = each($arrAbsen)){	
		// 		// echo $tanggalAbsen."<br>";
		// 		 $dt1 = strtotime($tanggalAbsen);
  //      			 $dt2 = date("l", $dt1);
  //      			 $dt3 = strtolower($dt2);
		// 		if(($dt3 == "saturday" )|| ($dt3 == "sunday")){
		// 			echo $tanggalAbsen." ini weekend <br>";
					
		// 		}else{
		// 			echo $tanggalAbsen." ini weekday <br>";
		// 		}

		// 	}
		// }

		// $weekDay = date('w', strtotime($date));

				
		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='normal'");
		$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
				
		$arrJadwal=arrayQuery("select month(t1.tanggalJadwal), t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where year(t1.tanggalJadwal)='".$par[tahunAbsen]."' and t1.idPegawai='".$par[idPegawai]."' and lower(trim(t2.namaShift)) not in ('off', 'cuti')");
		
		$sql="select * from dta_absen where year(mulaiAbsen)='".$par[tahunAbsen]."' and idPegawai='".$par[idPegawai]."' order by mulaiAbsen";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){			
				list($tanggalAbsen, $mulaiAbsen)=explode(" ",$r[mulaiAbsen]);
				list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);
						
				if(intval($par[bulanAbsen]) == intval($bulanAbsen)){
					$mulaiJadwal = isset($arrJadwal[intval($bulanAbsen)][$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[intval($bulanAbsen)][$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];					
					if(in_array($mulaiJadwal, array("", "00:00:00"))) $mulaiJadwal = $arrNormal;

					$dt1 = strtotime($tanggalAbsen);
       		$dt2 = date("N", $dt1);
  	  		if($dt2 < 6){
														
					if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
					list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
					list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);			
					if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){
						$cntTerlambat++;
					
						$d1 = $tanggalAbsen." ".$mulaiJadwal;
						$d2 = $r[mulaiAbsen];
						$menitTerlambat = selisihMenit($d1, $d2);										
						$totalTerlambat+=$menitTerlambat;						
						
						
						if($menitTerlambat <= 15)
							$jmlTerlambat["&lt;15"]++;
						else if($menitTerlambat <= 25)
							$jmlTerlambat["16-25"]++;
						else if($menitTerlambat <= 40)
							$jmlTerlambat["26-40"]++;
						else if($menitTerlambat <= 60)
							$jmlTerlambat["40-60"]++;
						else
							$jmlTerlambat["&gt;60"]++;						
					}												
				}
				}			
				
				$arrAbsen[$tanggalAbsen]=$mulaiAbsen;
				if(isset($arrJadwal[intval($bulanAbsen)][$tanggalAbsen]) && $mulaiAbsen > $arrJadwal[intval($bulanAbsen)][$tanggalAbsen]) $arrTerlambat[intval($bulanAbsen)]++;
				
				if(!isset($arrJadwal[$tanggalAbsen]) || $mulaiAbsen <= $arrJadwal[$tanggalAbsen])  $arrHadir[intval($bulanAbsen)]++;			
		}					
		
		if(is_array($arrJadwal)){
			reset($arrJadwal);
			while(list($bulanJadwal) = each($arrJadwal)){				
				if(is_array($arrJadwal[$bulanJadwal])){
					reset($arrJadwal[$bulanJadwal]);
					while(list($tanggalJadwal) = each($arrJadwal[$bulanJadwal])){
						list($_tahunJadwal, $_bulanJadwal, $_hariJadwal) = explode("-", $tanggalJadwal);
						if(!isset($arrAbsen[$tanggalJadwal])) $arrAlpha[intval($bulanJadwal)]++;
					}
				}
			}
		}
		
		$jumlahTerlambat = $totalTerlambat > 60 ? getAngka(floor($totalTerlambat / 60))." Jam ".getAngka(($totalTerlambat%60))." Menit" : getAngka($totalTerlambat)." Menit";			
						
		$maxTerlambat = $cntTerlambat > 0 ? ceilAngka($cntTerlambat, pow(10,strlen($cntTerlambat))) : 10;
		$downTerlambat = 0.5 * $maxTerlambat;
		$upTerlambat = 0.8 * $maxTerlambat;
		
		$rmb_limit = 0;
		
		$sql_="select
			id as parent_id,
			reg_no as nikPegawai,
			name as namaPegawai,
			marital
		from emp where id='".$cID."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);

		$getPokok = getField("select nilaiPokok from pay_pokok where idPegawai = '$r_[parent_id]'");

		// echo "select nilaiPokok from pay_pokok where idPegawai = '$r_[parent_id]'";

		$rawatJalan = getField("SELECT SUM(pengambilan) FROM rawatjalan_klaim WHERE idPegawai = '$r_[parent_id]' AND YEAR(tanggalKlaim) = '$par[tahunAbsen]' ");

		// echo "SELECT SUM(pengambilan) FROM rawatjalan_klaim WHERE idPegawai = '$r_[parent_id]' AND YEAR(tanggalKlaim) = '$par[tahunAbsen]'";



		$getNilai = getField("select nilai from rawatjalan_plafon where idJenis = '$r_[marital]' AND tahun = '".date('Y')."'");

		$cntRawatJalan = $getPokok * $getNilai - $rawatJalan;
		
		$sql__="select * from emp_phist where parent_id='".$cID."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);

		$obat = $r__[obat];
		
		$sql__="select * from pay_pengobatan where idPangkat='".$r__[rank]."' and idGrade='".$r__[grade]."'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		
		$rmb_limit = getField("select kodeData from mst_data where kodeData='".$r_[marital]."' and lower(namaData) like '%tk%'") ? $r__[lajangPengobatan] : $r__[keluargaPengobatan];		
		
		$rmb_balance = $rmb_limit - getField("select sum(rmb_val) from emp_rmb where parent_id='".$cID."' and year(rmb_date)='".$par[tahunAbsen]."' and concat(year(rmb_date), LPAD(month(rmb_date),2,'0'))<='".$par[tahunAbsen].$par[bulanAbsen]."' and status='1' and rmb_jenis='k'");
		
		$cntKlaim = $rmb_balance;		
		$maxKlaim = $rmb_limit;
		$downKlaim = 0.5 * $maxKlaim;
		$upKlaim = 0.8 * $maxKlaim;		
				
		$cntCuti = getField("select count(*) from att_cuti where year(mulaiCuti)='".$par[tahunAbsen]."' and month(mulaiCuti)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."'");
		$appCuti = getField("select count(*) from att_cuti where year(mulaiCuti)='".$par[tahunAbsen]."' and month(mulaiCuti)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."' and persetujuanCuti='t' and sdmCuti='t'");
		$sisaCuti = 12 - getField("select sum(jumlahCuti) from att_cuti where year(mulaiCuti)='".$par[tahunAbsen]."' and idPegawai='".$par[idPegawai]."' and persetujuanCuti='t' and sdmCuti='t'");
		$maxCuti = 12;
		$downCuti = 0.5 * $maxCuti;
		$upCuti = 0.8 * $maxCuti;

		$maxCutiBesar = 22;
		$downCutiBesar = 0.5 * $maxCutiBesar;
		$upCutiBesar = 0.8 * $maxCutiBesar;	

		$cntCutiBesar = getField("select jatahCuti from dta_cuti where idCuti = '7'"); #cutiBesar1

		$sisaCutiBesar = $cntCutiBesar - getField("select sum(jumlahCuti) from att_cuti where idTipe = '7' AND idPegawai = '$par[idPegawai]' AND persetujuanCuti = 't' AND sdmCuti = 't'");
				
		$cntLembur = getField("select count(*) from att_lembur where year(mulaiLembur)='".$par[tahunAbsen]."' and month(mulaiLembur)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."'");
		$appLembur = getField("select count(*) from att_lembur where year(mulaiLembur)='".$par[tahunAbsen]."' and month(mulaiLembur)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."' and persetujuanLembur='t'");		
		
		$maxLembur = $cntLembur > 0 ? ceilAngka($cntLembur, pow(10,strlen($cntLembur))) : 10;
		$downLembur = 0.5 * $maxLembur;
		$upLembur = 0.8 * $maxLembur;

		$cntKetidakhadiran = getField("select count(*) from att_hadir where year(tanggalHadir)='".$par[tahunAbsen]."' and month(tanggalHadir)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."'");
		$appKetidakhadiran = getField("select count(*) from att_hadir where year(tanggalHadir)='".$par[tahunAbsen]."' and month(tanggalHadir)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."' and persetujuanHadir='t'");

		$cntDinas = getField("select count(*) from ess_dinas where year(tanggalDinas)='".$par[tahunAbsen]."' and month(tanggalDinas)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."'");
		$appDinas = getField("select count(*) from ess_dinas where year(tanggalDinas)='".$par[tahunAbsen]."' and month(tanggalDinas)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."' and persetujuanDinas='t'");		
		
			
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
			Periode : ".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."
			</div>				
			</form>
			<table style=\"width:100%; margin-top:30px; margin-bottom:10px;\">
			<tr>
				<td style=\"width:50%; vertical-align:top;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pengajuan Izin</h3></div>
					</div>
					<div id=\"divIzin\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detIzin".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var izinChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" xAxisName=\"Pengajuan Izin\" yAxisName=\"Kali\">";


					
					$arrIzin = array("hadir"=>"Izin Ketidakhadiran", "cuti"=>"Izin Cuti","lembur"=>"Izin Lembur");

					if(is_array($arrIzin)){						
						reset($arrIzin);
						while(list($idIzin ,$namaIzin) = each($arrIzin)){							
							$text.="<set label=\"".$namaIzin." \" value=\"".getField("select count(id".ucwords($idIzin).") from att_$idIzin where idPegawai = '$par[idPegawai]' AND month(tanggal".ucwords($idIzin).") = '$par[bulanAbsen]' AND year(tanggal".ucwords($idIzin).") = '$par[tahunAbsen]'")."\"/> ";
							
							
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
					var telatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" xAxisName=\"Waktu - Menit\" yAxisName=\"Kali\">";
					
					if(is_array($jmlTerlambat)){
						reset($jmlTerlambat);
						while(list($idTerlambat, $valTerlambat) = each($jmlTerlambat)){
							$text.="<set label=\"".$idTerlambat."\" value=\"".$valTerlambat."\"/> ";
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
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Absensi ".$par[tahunAbsen]."</h3></div>
			</div>
			<div id=\"divAbsen\" align=\"center\" ></div>
			<script type=\"text/javascript\">
			var absenChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" yAxisName=\"Kali\" palette=\"2\" canvasPadding=\"10\" bgColor=\"F7F7F7, E9E9E9\" numVDivLines=\"12\" divLineAlpha=\"30\" labelPadding =\"10\" yAxisValuesPadding =\"10\" anchorRadius=\"1\" labelDisplay=\"WRAP\" showValues=\"0\" valuePosition=\"auto\" exportEnabled=\"0\" >";
			// onclick=\"window.location='?c=3&p=11&m=104&s=113&par[tahunAbsen]=".$par[tahunAbsen]."&par[bulanAbsen]=".$par[bulanAbsen]."';\"
			
			$text.="<categories>";
			for($i=1; $i<=12; $i++) $text.="<category label=\"".getBulan($i)."\" />";
			$text.="</categories>";
			
			$text.="<dataset seriesName=\"Alpha\" color=\"DA3608\">";
			for($i=1; $i<=12; $i++){
				$valAlpha = isset($arrAlpha[$i]) ? $arrAlpha[$i] : 0;
				$text.="<set value=\"".$valAlpha."\" toolText=\"Alpha, ".getBulan($i)." ".getBulan($par[bulanAbsen]).", ".getAngka($valHadir)." Kali\"/>";
			}
			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Telat\" color=\"015887\">";
			for($i=1; $i<=12; $i++){
				$valTerlambat = isset($arrTerlambat[$i]) ? $arrTerlambat[$i] : 0;
				$text.="<set value=\"".$valTerlambat."\" toolText=\"Telat, ".getBulan($i)." ".getBulan($par[bulanAbsen]).", ".getAngka($valHadir)." Kali\"/>";
			}
			$text.="</dataset>";
			
			$text.="<dataset seriesName=\"Hadir\" color=\"78AE1C\">";
			for($i=1; $i<=12; $i++){
				$valHadir = isset($arrHadir[$i]) ? $arrHadir[$i] : 0;
				$text.="<set value=\"".$valHadir."\" toolText=\"Hadir, ".getBulan($i)." ".getBulan($par[bulanAbsen]).", ".getAngka($valHadir)." Kali\"/>";
			}
			$text.="</dataset>";
			
			
			$text.="</chart>';
			var chart = new FusionCharts(\"MSLine\", \"chartAbsen\", \"100%\", 250);
				chart.setXMLData( absenChart );
				chart.render(\"divAbsen\");
			</script>";
			
		$sql="select * from dta_absen where year(mulaiAbsen)='".$par[tahunAbsen]."' and month(mulaiAbsen)='".$par[bulanAbsen]."'  and selesaiAbsen='0000-00-00 00:00:00' and idPegawai='".$par[idPegawai]."'";
		$res=db($sql);
		if(mysql_num_rows($res))
		{		
	
			$dtaNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");			
			$dtaShift=getField("select concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
			$dtaJadwal=arrayQuery("select tanggalJadwal, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where year(tanggalJadwal)='".$par[tahunAbsen]."' and month(tanggalJadwal)='".$par[bulanAbsen]."' and idPegawai='".$par[idPegawai]."'");
						
			$text.="<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Absen Kosong</h3></div>
			</div>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:75px;\">Tanggal</th>
					<th colspan=\"2\" style=\"min-width:150px;\">Jadwal</th>
					<th colspan=\"2\" style=\"min-width:150px;\">Aktual</th>
				</tr>
				<tr>
					<th style=\"min-width:75px;\">Masuk</th>
					<th style=\"min-width:75px;\">Pulang</th>
					<th style=\"min-width:75px;\">Masuk</th>
					<th style=\"min-width:75px;\">Pulang</th>
				</tr>
			</thead>
			<tbody>";
			$no=1;
			while($r=mysql_fetch_array($res)){
				list($mulaiTanggal, $mulaiAbsen) = explode(" ",$r[mulaiAbsen]);
				list($selesaiTanggal, $selesaiAbsen) = explode(" ",$r[selesaiAbsen]);
								
				list($r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) :
				explode("\t", $dtaShift) ;
				
				if(isset($dtaJadwal[$mulaiTanggal]))
					list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal[$mulaiTanggal]);
				
				$text.="<tr>
						<td>$no.</td>
						<td>".getTanggal($mulaiTanggal,"t")."</td>
						<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
						<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
						<td align=\"center\">".substr($mulaiAbsen,0,5)."</td>
						<td align=\"center\">".substr($selesaiAbsen,0,5)."</td>
					</tr>";	
					$no++;
			}
			$text.="</tbody>
			</table>";
		}	
		$text.="<table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
			<tr>				
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Keterlambatan</h3></div>
					</div>
					<div id=\"divTerlambat\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var terlambatChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
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
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntTerlambat)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"Datang Terlambat\" fontColor=\"000000\" fontSize=\"15\" />";
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
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pengajuan Lembur</h3></div>
					</div>
					<div id=\"divData\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detLembur".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var dataChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxLembur."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downLembur."\"/>";
					$text.="<color minValue=\"".$downLembur."\" maxValue=\"".$upLembur."\"/>";
					$text.="<color minValue=\"".$upLembur."\" maxValue=\"".$maxLembur."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntLembur."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"143\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntLembur)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"".getAngka($appLembur)." Disetujui\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartData\", \"225\", \"175\");
						chart.setXMLData( dataChart );
						chart.render(\"divData\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Sisa Rawat Jalan</h3></div>
					</div>
					<div id=\"divKlaim\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKlaim".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var klaimChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\"  pivotRadius=\"8\" pivotFillMix=\"{CCCCCC},{000000}\" pivotBorderColor=\"000000\" >";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downTerlambat."\"/>";
					$text.="<color minValue=\"".$downTerlambat."\" maxValue=\"".$upTerlambat."\"/>";
					$text.="<color minValue=\"".$upTerlambat."\" maxValue=\"".$maxTerlambat."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntRawatJalan."\" bgColor=\"000000\" borderAlpha=\"0\" baseWidth=\"4\" topWidth=\"4\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"150\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntRawatJalan)."\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"Balance\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartKlaim\", \"225\", \"175\");
						chart.setXMLData( klaimChart );
						chart.render(\"divKlaim\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Sisa Cuti Tahunan</h3></div>
					</div>
					<div id=\"divCuti\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detCuti".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var cutiChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxCuti."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"5\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\"  gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downCuti."\"/>";
					$text.="<color minValue=\"".$downCuti."\" maxValue=\"".$upCuti."\"/>";
					$text.="<color minValue=\"".$upCuti."\" maxValue=\"".$maxCuti."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$sisaCuti."\" bgColor=\"000000\" baseWidth=\"10\" radius=\"70\" rearExtension=\"15\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($sisaCuti)." Hari\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"".getAngka($maxCuti-$sisaCuti)." Diambil\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartCuti\", \"225\", \"175\");
						chart.setXMLData( cutiChart );
						chart.render(\"divCuti\");
					</script>
				</td>
			</tr>
			</table>
			<table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
			<tr>				
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Alpha</h3></div>
					</div>
					<div id=\"divAlpha\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detTerlambat".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var alphaChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downTerlambat."\"/>";
					$text.="<color minValue=\"".$downTerlambat."\" maxValue=\"".$upTerlambat."\"/>";
					$text.="<color minValue=\"".$upTerlambat."\" maxValue=\"".$maxTerlambat."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntAlpha."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntAlpha)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"Tidak Hadir\" fontColor=\"000000\" fontSize=\"15\" />";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartTerlambat\", \"225\", \"175\");
						chart.setXMLData( alphaChart );
						chart.render(\"divAlpha\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Izin Ketidakhadiran</h3></div>
					</div>
					<div id=\"divKetidakhadiran\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detKetidakhadiran".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var dataKetidakhadiran ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxLembur."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downLembur."\"/>";
					$text.="<color minValue=\"".$downLembur."\" maxValue=\"".$upLembur."\"/>";
					$text.="<color minValue=\"".$upLembur."\" maxValue=\"".$maxLembur."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntKetidakhadiran."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"143\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntKetidakhadiran)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"".getAngka($appKetidakhadiran)." Disetujui\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartData\", \"225\", \"175\");
						chart.setXMLData( dataKetidakhadiran );
						chart.render(\"divKetidakhadiran\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>SPPD</h3></div>
					</div>
					<div id=\"divSPPD\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detSPPD".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var sppdChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxTerlambat."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"12\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downTerlambat."\"/>";
					$text.="<color minValue=\"".$downTerlambat."\" maxValue=\"".$upTerlambat."\"/>";
					$text.="<color minValue=\"".$upTerlambat."\" maxValue=\"".$maxTerlambat."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$cntDinas."\" bgColor=\"000000\" rearExtension=\"15\" baseWidth=\"10\"/>";
					$text.="</dials>";
					
					$text.="<annotations>";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($cntDinas)." Kali\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"".getAngka($appDinas)." Disetujui\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
										
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartTerlambat\", \"225\", \"175\");
						chart.setXMLData( sppdChart );
						chart.render(\"divSPPD\");
					</script>
				</td>
				<td style=\"width:25%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Cuti Besar</h3></div>
					</div>
					<div id=\"divBesar\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detCuti".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var besarChart ='<chart manageResize=\"1\" origW=\"300\" origH=\"150\" palette=\"2\" bgColor=\"999999,EEEEEE\" lowerLimit=\"0\" upperLimit=\"".$maxCutiBesar."\" showBorder=\"1\" borderColor=\"888888\" basefontColor=\"000000\" chartBottomMargin=\"50\" chartRightMargin=\"10\" majorTMHeight=\"10\" gaugeInnerRadius=\"1\" pivotRadius=\"5\" pivotFillMix=\"CCCCCC,000000\" showPivotBorder=\"1\" pivotBorderColor=\"000000\"  gaugeStartAngle=\"220\" gaugeEndAngle=\"-40\">";
					
					$text.="<colorRange>";
					$text.="<color minValue=\"0\" maxValue=\"".$downCutiBesar."\"/>";
					$text.="<color minValue=\"".$downCutiBesar."\" maxValue=\"".$upCutiBesar."\"/>";
					$text.="<color minValue=\"".$upCutiBesar."\" maxValue=\"".$maxCutiBesar."\"/>";
					$text.="</colorRange>";
					
					$text.="<dials>";
					$text.="<dial value=\"".$sisaCutiBesar."\" bgColor=\"000000\" baseWidth=\"10\" radius=\"70\" rearExtension=\"15\"/>";
					$text.="</dials>";
					
					$text.="<annotations> ";
					$text.="<annotationGroup x=\"147\" y=\"50\" showBelow=\"0\" scaleText=\"1\">";
					$text.="<annotation type=\"text\" y=\"130\" label=\"".getAngka($sisaCutiBesar)." Hari\" fontColor=\"000000\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"135\" label=\"__________________________\" fontColor=\"666666\" fontSize=\"15\" bold=\"1\"/>";
					$text.="<annotation type=\"text\" y=\"155\" label=\"".getAngka($cntCutiBesar-$sisaCutiBesar)." Diambil\" fontColor=\"000000\" fontSize=\"15\"/>";
					$text.="</annotationGroup>";
					$text.="</annotations>";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"AngularGauge\", \"chartCuti\", \"225\", \"175\");
						chart.setXMLData( besarChart );
						chart.render(\"divBesar\");
					</script>
				</td>
			</tr>
			</table>";
		
		$sql="select * from dta_pegawai where id='".$par[idPegawai]."'";
		$res=db($sql);
		if(mysql_num_rows($res)){
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
				</tr>
			</thead>
			<tbody>";
			$no=1;
			while($r=mysql_fetch_array($res)){
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>
						<td>".strtoupper($arrPangkat["$r[rank]"])."</td>
						<td>".strtoupper($r[pos_name])."</td>
						<td>".strtoupper($arrLokasi["$r[location]"])."</td>
					</tr>";	
					$no++;
			}
			$text.="</tbody>
			</table>";
		}	

		$arrInap = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='RJI' order by urutanData");
		$arrSubInap = arrayQuery("select kodeInduk, GROUP_CONCAT(kodeData) from mst_data where statusData = 't' and kodeCategory = 'RJJ'");

		// echo "<pre>";
		// print_r($arrSubInap);
		// echo "</pre>";
		// echo "ini obat".$obat;


		$text.="<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>RAWAT INAP</h3></div>
			</div>
			
			<div id=\"divPangkat\" align=\"center\" ></div>
			<script type=\"text/javascript\">
			var pangkatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";
			
			if(is_array($arrInap)){
				reset($arrInap);
				while(list($idInap, $valInap) = each($arrInap)){
					$subInap = getField("select GROUP_CONCAT(kodeData) from mst_data where statusData = 't' and kodeCategory = 'RJJ' AND kodeInduk = '$idInap'");
					$jumlahInap = getField("SELECT SUM(nilai) FROM rawatinap_plafon WHERE idGolongan = '$obat' AND idJenis IN($subInap)");
					$jumlahPakai = getField("SELECT SUM(nilai) FROM rawatinap_klaim t1 join rawatinap_klaim_detail t2 on t1.id = t2.idKlaim WHERE t2.idKategori = '$idInap' AND t1.idPegawai = '$par[idPegawai]'");
					$sisaInap = $jumlahInap - $jumlahPakai;

					// $totalValue = getField("select ")
					 $text.="<set label=\"".$arrInap[$idInap]."\" value=\"".$sisaInap."\"/> ";					
				}
			}
			
			$text.="</chart>';
			var chart = new FusionCharts(\"Column3D\", \"chartPangkat\", \"100%\", 250);
				chart.setXMLData( pangkatChart );
				chart.render(\"divPangkat\");
			</script>";
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
						
		$filter = "where nomorIzin is not null and t2.id='".$par[idPegawai]."' ";		
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

		$filter = "where nomorIzin is not null and t2.id='".$par[idPegawai]."' ";		
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
		
		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift))='normal'");
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
		
		$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where year(t2.mulaiAbsen)='".$par[tahunAbsen]."' and month(t2.mulaiAbsen)='".$par[bulanAbsen]."' and t1.id='".$par[idPegawai]."' ".$filter." order by t1.name, t2.mulaiAbsen ";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			list($tanggalAbsen, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);
			$dt1 = strtotime($tanggalAbsen);
       		$dt2 = date("N", $dt1);
  	  		if($dt2 < 6){
			$mulaiJadwal = isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];
			
			if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
			list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
			list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);
			
			if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){		
								
				$d1 = $tanggalAbsen." ".$mulaiJadwal;
				$d2 = $r[mulaiAbsen];
				$durasiMenit = selisihMenit($d1, $d2);
				
				$no++;
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>						
						<td align=\"center\">".getTanggal($tanggalAbsen)."</td>					
						<td align=\"right\">".getAngka($durasiMenit)." Menit</td>
						</tr>";	
			}	
		}
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
				
		$filter = "where t1.idPegawai='".$par[idPegawai]."'";
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalJadwal)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalJadwal)='$par[tahunAbsen]'";		
		
		$arrShift=arrayQuery("select t1.idPegawai, t2.kodeShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select t1.idPegawai, t1.tanggalJadwal, t2.kodeShift from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) $filter order by t1.idJadwal");
		
		$filter = "where idPegawai='".$par[idPegawai]."'";
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(tanggalAbsen)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(tanggalAbsen)='$par[tahunAbsen]'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, concat(masukAbsen, '\t', pulangAbsen, '\t', durasiAbsen) from att_absen $filter order by idAbsen");
				
		$filter = "where nomorLembur is not null and idPegawai='".$par[idPegawai]."'";		
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
	
	function detKlaim(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Klaim Total</h1>
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
					<th style=\"min-width:150px;\">Kategori/Tipe</th>
					<th width=\"100\">Nilai</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where rmb_jenis!='' and t2.id='".$par[idPegawai]."'";		
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
					<th rowspan=\"2\" width=\"100\">Keterangan</th>					

				</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"50\">Mulai</th>
					<th width=\"50\">Selesai</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorCuti is not null and idPegawai='".$par[idPegawai]."'";		
		// if(!empty($par[bulanAbsen]))
		// 	$filter.= " and month(t1.tanggalCuti)='$par[bulanAbsen]'";
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
					<td>$r[keteranganCuti]</td>
					</tr>";				
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}

	function detKetidakhadiran(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Izin Ketidakhadiran</h1>
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
					<th rowspan=\"2\" width=\"100\">Keterangan</th>					

				</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"50\">Mulai</th>
					<th width=\"50\">Selesai</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorHadir is not null and idPegawai='".$par[idPegawai]."'";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalHadir)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalHadir)='$par[tahunAbsen]'";				
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorHadir) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_hadir t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorHadir";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanCuti = $r[persetujuanCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanCuti = $r[persetujuanCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanCuti;
			
			$sdmCuti = $r[sdmCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmCuti = $r[sdmCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmCuti;			
			
			list($mulaiHadir) = explode(" ",$r[mulaiHadir]);
			list($selesaiHadir) = explode(" ",$r[selesaiHadir]);
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorHadir]</td>
					<td align=\"center\">".getTanggal($r[tanggalHadir])."</td>					
					<td align=\"center\">".getTanggal($mulaiHadir)."</td>
					<td align=\"center\">".getTanggal($selesaiHadir)."</td>
					<td>$r[keteranganHadir]</td>
					</tr>";				
		}
		
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}

	function detSPPD(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">SPPD</h1>
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
					<th  width=\"175\">Tanggal</th>
					<th  width=\"175\">Kategori</th>
					<th  width=\"175\">Nilai</th>
				</tr>
				
			</thead>
			<tbody>";
		
		$filter = "where nomorDinas is not null and idPegawai='".$par[idPegawai]."'";		
		if(!empty($par[bulanAbsen]))
			$filter.= " and month(t1.tanggalCuti)='$par[bulanAbsen]'";
		if(!empty($par[tahunAbsen]))
			$filter.= " and year(t1.tanggalDinas)='$par[tahunAbsen]'";				
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from ess_dinas t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorDinas";
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
					<td>$r[nomorDinas]</td>
					<td align=\"center\">".getTanggal($r[tanggalDinas])."</td>					
					<td align=\"left\">".getField("select namaData from mst_data where kodeData = '$r[idKategori]'")."</td>
					<td align=\"left\">".getAngka($r[nilaiDinas])."</td>
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
			case "detIzin":
				$text = detIzin();
			break;
			case "detTerlambat":
				$text = detTerlambat();
			break;
			case "detLembur":
				$text = detLembur();
			break;
			case "detKlaim":
				$text = detKlaim();
			break;
			case "detCuti":
				$text = detCuti();
			break;
			case "detKetidakhadiran":
				$text = detKetidakhadiran();
			break;
			case "detSPPD":
				$text = detSPPD();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
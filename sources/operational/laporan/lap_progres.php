<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	$arrDepartemen = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X06' and kodeInduk in (895,896,923) order by urutanData");

	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$arrDepartemen;		
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t2.tanggalJadwal between '".setTanggal("".$_GET['mSearch']."")."' and '".setTanggal("".$_GET['tSearch']."")."'";						
		$sql="select * from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal) $sWhere";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunJadwal, $bulanJadwal) = explode("-", $r[tanggalJadwal]);												
			$arrPelatihan[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]["".$r[idPelatihan].""] = $r[judulPelatihan];
			$jmlPeserta[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			if($r[statusAbsensi] == "t")
				$jmlHadir[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			else
				$jmlTidak[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;			
		}
		
		$arrJadwal = arrayQuery("select concat(year(t2.tanggalJadwal), LPAD(month(t2.tanggalJadwal),2,0)), t1.idDepartemen, concat(sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");
		
		list($tanggalMulai, $bulanMulai, $tahunMulai) = explode("/",$_GET['mSearch']);
		list($tanggalSelesai, $bulanSelesai, $tahunSelesai) = explode("/",$_GET['tSearch']);		
		$tMulai = mktime(0,0,0,$bulanMulai,$tanggalMulai,$tahunMulai);
		$tSelesai = mktime(0,0,0,$bulanSelesai,$tanggalSelesai,$tahunSelesai);
		
		$tPelatihan = $tPeserta = $tHadir = $tTidak = $tWaktu = $aPelatihan = $aPresent = 0;
		while($tMulai <= $tSelesai){						
			list($tahun, $bulan) = explode("-",date("Y-m", $tMulai));
									
			if(is_array($arrDepartemen)){					
				reset($arrDepartemen);
				while(list($idDepartemen, $valDepartemen) = each($arrDepartemen)){
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
					$menitPelatihan = $menitPelatihan%60;			
					$waktuPelatihan = $jamPelatihan.".".round($menitPelatihan/60);
				
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jmlAverage = $jmlPeserta[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlPeserta[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPelatihan = $jamAverage.".".round($menitAverage/60);
									
					$jmlAverage = $jmlHadir[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlHadir[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPresent = $jamAverage.".".round($menitAverage/60);
				
					$tPelatihan+=count($arrPelatihan[$tahun.$bulan][$idDepartemen]);
					$tPeserta+=$jmlPeserta[$tahun.$bulan][$idDepartemen];
					$tHadir+=$jmlHadir[$tahun.$bulan][$idDepartemen];
					$tTidak+=$jmlTidak[$tahun.$bulan][$idDepartemen];
					$tWaktu+=$waktuPelatihan;
					$aPelatihan+=$avgPelatihan;
					$aPresent+=$avgPresent;					
				}
			}
			$tMulai = dateAdd("m", 1, $tMulai);			
		}		
		return getAngka($tPelatihan)."\t".getAngka($tPeserta)."\t".getAngka($tHadir)."\t".getAngka($tTidak)."\t".getAngka($tWaktu,1)."\t".getAngka($aPelatihan,1)."\t".getAngka($aPresent,1);
	}

	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$arrDepartemen;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t2.tanggalJadwal between '".setTanggal("".$_GET['mSearch']."")."' and '".setTanggal("".$_GET['tSearch']."")."'";						
		$sql="select * from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal) $sWhere";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunJadwal, $bulanJadwal) = explode("-", $r[tanggalJadwal]);												
			$arrPelatihan[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]["".$r[idPelatihan].""] = $r[judulPelatihan];
			$jmlPeserta[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			if($r[statusAbsensi] == "t")
				$jmlHadir[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			else
				$jmlTidak[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;			
		}
		
		$arrJadwal = arrayQuery("select concat(year(t2.tanggalJadwal), LPAD(month(t2.tanggalJadwal),2,0)), t1.idDepartemen, concat(sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");
		
		list($tanggalMulai, $bulanMulai, $tahunMulai) = explode("/",$_GET['mSearch']);
		list($tanggalSelesai, $bulanSelesai, $tahunSelesai) = explode("/",$_GET['tSearch']);		
		$tMulai = mktime(0,0,0,$bulanMulai,$tanggalMulai,$tahunMulai);
		$tSelesai = mktime(0,0,0,$bulanSelesai,$tanggalSelesai,$tahunSelesai);
		
		while($tMulai <= $tSelesai){						
			list($tahun, $bulan) = explode("-",date("Y-m", $tMulai));
						
			$tPelatihan = $tPeserta = $tHadir = $tTidak = $tWaktu = $aPelatihan = $aPresent = 0;
			if(is_array($arrDepartemen)){					
				reset($arrDepartemen);
				while(list($idDepartemen, $valDepartemen) = each($arrDepartemen)){
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
					$menitPelatihan = $menitPelatihan%60;			
					$waktuPelatihan = $jamPelatihan.".".round($menitPelatihan/60);
				
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jmlAverage = $jmlPeserta[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlPeserta[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPelatihan = $jamAverage.".".round($menitAverage/60);
									
					$jmlAverage = $jmlHadir[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlHadir[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPresent = $jamAverage.".".round($menitAverage/60);
				
					$data=array();
					$data[]=getBulan($bulan)." ".$tahun == $tmpPeriode ?
					"<div align=\"center\">&nbsp;</div>":
					"<div align=\"center\">".getBulan($bulan)." ".$tahun."</div>";
					$data[]="<div align=\"left\">".strtoupper($valDepartemen)."</div>";
					$data[]="<div align=\"center\">".getAngka(count($arrPelatihan[$tahun.$bulan][$idDepartemen]))."</div>";
					$data[]="<div align=\"center\">".getAngka($jmlPeserta[$tahun.$bulan][$idDepartemen])."</div>";
					$data[]="<div align=\"center\">".getAngka($jmlHadir[$tahun.$bulan][$idDepartemen])."</div>";
					$data[]="<div align=\"center\">".getAngka($jmlTidak[$tahun.$bulan][$idDepartemen])."</div>";
					$data[]="<div align=\"center\">".$waktuPelatihan."</div>";
					$data[]="<div align=\"center\">".$avgPelatihan."</div>";
					$data[]="<div align=\"center\">".$avgPresent."</div>";
					$data[]="<div align=\"center\">&nbsp;</div>";
					$json['aaData'][]=$data;
					
					$tPelatihan+=count($arrPelatihan[$tahun.$bulan][$idDepartemen]);
					$tPeserta+=$jmlPeserta[$tahun.$bulan][$idDepartemen];
					$tHadir+=$jmlHadir[$tahun.$bulan][$idDepartemen];
					$tTidak+=$jmlTidak[$tahun.$bulan][$idDepartemen];
					$tWaktu+=$waktuPelatihan;
					$aPelatihan+=$avgPelatihan;
					$aPresent+=$avgPresent;
					
					$tmpPeriode = getBulan($bulan)." ".$tahun;
				}
			}
																	
			$data=array();
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"left\"><strong>TOTAL</strong></div></div>";
			$data[]="<div align=\"center\">".getAngka($tPelatihan)."</div>";
			$data[]="<div align=\"center\">".getAngka($tPeserta)."</div>";
			$data[]="<div align=\"center\">".getAngka($tHadir)."</div>";
			$data[]="<div align=\"center\">".getAngka($tTidak)."</div>";
			$data[]="<div align=\"center\">".getAngka($tWaktu,1)."</div>";
			$data[]="<div align=\"center\">".getAngka($aPelatihan,1)."</div>";
			$data[]="<div align=\"center\">".getAngka($aPresent,1)."</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$json['aaData'][]=$data;
			
			$data=array();
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$data[]="<div align=\"center\">&nbsp;</div>";
			$json['aaData'][]=$data;
			
			$tMulai = dateAdd("m", 1, $tMulai);
			$cnt++;
		}
		
		$json["iTotalDisplayRecords"] = $cnt;			
		
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$arrDepartemen;		
		
		$mSearch = empty($_GET[mSearch]) ? date('01/m/Y') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];		
		$text = table(10, array(1,2,3,4,5,6,7,8,9,10), "lst", "false", "v");
		
		$rotation = "style=\"-webkit-transform: rotate(-90deg);
					-moz-transform: rotate(-90deg);
					-ms-transform: rotate(-90deg);
					-o-transform: rotate(-90deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
					margin-bottom:30px;
					\"";
		
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">			
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td><input type=\"text\" id=\"mSearch\" name=\"mSearch\" size=\"10\" maxlength=\"10\" value=\"".$mSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par,"mode")."');\" /></td>
				<td>s.d</td>
				<td><input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par,"mode")."');\" /></td>								
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>				
					<th style=\"min-width:100px; vertical-align:middle;\">Month</th>
					<th style=\"min-width:200px; vertical-align:middle;\">Departemen</th>
					<th style=\"max-width:100px; vertical-align:middle;\">Total Training</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Total Participant</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Total Present</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Total Absen</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Total Training Duration</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Average Duration</div></th>
					<th style=\"max-width:100px; vertical-align:middle;\">Present Of Participant</div></th>
					<th style=\"min-width:200px; vertical-align:middle;\">Remarks</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:left\"><strong>GRAND TOTAL</strong></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"tPelatihan\"></span></td>	
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"tPeserta\"></span></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"tHadir\"></span></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"tTidak\"></span></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"tWaktu\"></span></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"aPelatihan\"></span></td>
					<td style=\"vertical-align:middle; text-align:center;\"><span id=\"aPresent\"></span></td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
				</tr>
			</tfoot>
			</table>
			</div>
			<script>
				gData('".getPar($par, "mode")."');
			</script>";
			
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
			
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrDepartemen;
		require_once 'plugins/PHPExcel.php';
			
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(30);	
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Training Progress Report All Department"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'MONTH');		
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DEPARTEMEN');		
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'TOTAL TRAINING');		
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TOTAL PARTICIPANT');		
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TOTAL PRESENT');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TOTAL ABSEN');		
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TOTAL TRAINING DURATION');		
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'AVERAGE DURATION');		
		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'PRESENT OF PARTICIPANT');		
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'REMARKS');						
								
		$rows = 5;
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t2.tanggalJadwal between '".setTanggal("".$_GET['mSearch']."")."' and '".setTanggal("".$_GET['tSearch']."")."'";						
		$sql="select * from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal) $sWhere";
		$res=db($sql);
		
		while($r=mysql_fetch_array($res)){
			list($tahunJadwal, $bulanJadwal) = explode("-", $r[tanggalJadwal]);												
			$arrPelatihan[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]["".$r[idPelatihan].""] = $r[judulPelatihan];
			$jmlPeserta[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			if($r[statusAbsensi] == "t")
				$jmlHadir[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;
			else
				$jmlTidak[$tahunJadwal.$bulanJadwal]["".$r[idDepartemen].""]++;			
		}
		
		$arrJadwal = arrayQuery("select concat(year(t2.tanggalJadwal), LPAD(month(t2.tanggalJadwal),2,0)), t1.idDepartemen, concat(sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");
		
		list($tanggalMulai, $bulanMulai, $tahunMulai) = explode("/",$_GET['mSearch']);
		list($tanggalSelesai, $bulanSelesai, $tahunSelesai) = explode("/",$_GET['tSearch']);		
		$tMulai = mktime(0,0,0,$bulanMulai,$tanggalMulai,$tahunMulai);
		$tSelesai = mktime(0,0,0,$bulanSelesai,$tanggalSelesai,$tahunSelesai);
		
		while($tMulai <= $tSelesai){						
			list($tahun, $bulan) = explode("-",date("Y-m", $tMulai));
						
			$tPelatihan = $tPeserta = $tHadir = $tTidak = $tWaktu = $aPelatihan = $aPresent = 0;
			if(is_array($arrDepartemen)){					
				reset($arrDepartemen);
				while(list($idDepartemen, $valDepartemen) = each($arrDepartemen)){
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
					$menitPelatihan = $menitPelatihan%60;			
					$waktuPelatihan = $jamPelatihan.".".round($menitPelatihan/60);
				
					list($jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal[$tahun.$bulan][$idDepartemen]);
					$jmlAverage = $jmlPeserta[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlPeserta[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPelatihan = $jamAverage.".".round($menitAverage/60);
									
					$jmlAverage = $jmlHadir[$tahun.$bulan][$idDepartemen] > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlHadir[$tahun.$bulan][$idDepartemen]) : 0;
					$jamAverage = floor($jmlAverage/60);
					$menitAverage = $jmlAverage%60;			
					$avgPresent = $jamAverage.".".round($menitAverage/60);
								
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								
					if(getBulan($bulan)." ".$tahun != $tmpPeriode)
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, getBulan($bulan)." ".$tahun);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($valDepartemen));		
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka(count($arrPelatihan[$tahun.$bulan][$idDepartemen])));		
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($jmlPeserta[$tahun.$bulan][$idDepartemen]));		
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($jmlHadir[$tahun.$bulan][$idDepartemen]));		
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($jmlTidak[$tahun.$bulan][$idDepartemen]));		
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $waktuPelatihan);		
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $avgPelatihan);		
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $avgPresent);		
					
					$tPelatihan+=count($arrPelatihan[$tahun.$bulan][$idDepartemen]);
					$tPeserta+=$jmlPeserta[$tahun.$bulan][$idDepartemen];
					$tHadir+=$jmlHadir[$tahun.$bulan][$idDepartemen];
					$tTidak+=$jmlTidak[$tahun.$bulan][$idDepartemen];
					$tWaktu+=$waktuPelatihan;
					$aPelatihan+=$avgPelatihan;
					$aPresent+=$avgPresent;
					
					$tmpPeriode = getBulan($bulan)." ".$tahun;
					$rows++;
				}
			}
				
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");		
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($tPelatihan));		
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($tPeserta));		
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($tHadir));		
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($tTidak));		
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($tWaktu,1));		
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($aPelatihan,1));		
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($aPresent,1));					
			$rows++;
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
			$rows++;
			
			$sPelatihan+=$tPelatihan;
			$sPeserta+=$tPeserta;
			$sHadir+=$tHadir;
			$sTidak+=$tTidak;
			$sWaktu+=$tWaktu;
			$vPelatihan+=$aPelatihan;
			$vPresent+=$aPresent;
			
			$tMulai = dateAdd("m", 1, $tMulai);
			$cnt++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "GRAND TOTAL");		
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($sPelatihan));		
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($sPeserta));		
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($sHadir));		
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($sTidak));		
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($sWaktu,1));		
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($vPelatihan,1));		
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($vPresent,1));					
		
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
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("Laporan");
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;
			case "get":
			$text = gData();
			break;

			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
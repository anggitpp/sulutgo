<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	
	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and year(t1.mulaiPelatihan)='".$_GET['tSearch']."' and month(t1.mulaiPelatihan)='".$_GET['mSearch']."'";
		
		$arrAbsensi = arrayQuery("select t1.idPelatihan, t2.statusAbsensi, count(t2.idPeserta) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");		
		$sql="select * from plt_pelatihan t1 join dta_vendor t2 join dta_trainer t3 on (t1.idVendor=t2.kodeVendor and t1.idTrainer=t3.idTrainer) $sWhere";
		$res=db($sql);
		$totalParticipant=0;
		$totalPresent=0;	
		$totalAbsen=0;	
		while($r=mysql_fetch_array($res)){			
			$totalParticipant+= array_sum($arrAbsensi["".$r[idPelatihan].""]);
			$totalPresent+= $arrAbsensi["".$r[idPelatihan].""]["t"];	
			$totalAbsen+= $arrAbsensi["".$r[idPelatihan].""]["f"];	
		}		
		
		return $totalParticipant."\t".$totalPresent."\t".$totalAbsen;
	}
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and year(t1.mulaiPelatihan)='".$_GET['tSearch']."' and month(t1.mulaiPelatihan)='".$_GET['mSearch']."'";
				
		$arrOrder = array(	
			"t1.mulaiPelatihan",
			"t1.mulaiPelatihan",
			"t1.judulPelatihan",
			"t3.namaTrainer",
			"t2.namaVendor",
			"t1.pelaksanaanPelatihan",
			"t1.lokasiPelatihan",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		$sql="select * from plt_pelatihan t1 join dta_vendor t2 join dta_trainer t3 on (t1.idVendor=t2.kodeVendor and t1.idTrainer=t3.idTrainer) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$arrAbsensi = arrayQuery("select t1.idPelatihan, t2.statusAbsensi, count(t2.idPeserta) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");		
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from plt_pelatihan t1 join dta_vendor t2 join dta_trainer t3 on (t1.idVendor=t2.kodeVendor and t1.idTrainer=t3.idTrainer) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			$pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",								
				"<div align=\"center\">".getTanggal($r[mulaiPelatihan])."</div>",
				"<div align=\"left\">".$r[judulPelatihan]."</div>",
				"<div align=\"left\">".$r[namaTrainer]."</div>",
				"<div align=\"left\">".$r[namaVendor]."</div>",
				"<div align=\"left\">".$pelaksanaanPelatihan."</div>",
				"<div align=\"left\">".$r[lokasiPelatihan]."</div>",
				"<div align=\"center\">".getAngka(array_sum($arrAbsensi["".$r[idPelatihan].""]))."</div>",
				"<div align=\"center\">".getAngka($arrAbsensi["".$r[idPelatihan].""]["t"])."</div>",
				"<div align=\"center\">".getAngka($arrAbsensi["".$r[idPelatihan].""]["f"])."</div>",
			);		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
				
		$mSearch = empty($_GET[mSearch]) ? date('m') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('Y') : $_GET[tSearch];
		$text = table(10, array(8,9,10));
		
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
				<td>".comboMonth("mSearch", $mSearch, "onchange=\"gData('".getPar($par, "mode")."');\"")."</td>
				<td>".comboYear("tSearch", $tSearch, "", "onchange=\"gData('".getPar($par, "mode")."');\"")."</td>								
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
					<th rowspan=\"2\" style=\"max-width:30px; vertical-align:middle;\">No.</th>
					<th rowspan=\"2\" style=\"max-width:100px; vertical-align:middle;\">Date of Conduct</th>
					<th rowspan=\"2\" style=\"min-width:200px; vertical-align:middle;\">Training Topic</th>
					<th colspan=\"2\" style=\"vertical-align:middle;\">Training Provider</th>
					<th rowspan=\"2\" style=\"width:100px; vertical-align:middle;\">Type of Training</th>
					<th rowspan=\"2\" style=\"width:100px; vertical-align:middle;\">Training Venue</th>
					<th colspan=\"3\" style=\"vertical-align:middle;\">Peserta</th>
				</tr>
				<tr>
					<th style=\"width:200px; vertical-align:middle;\">Fasilitator</th>
					<th style=\"width:200px; vertical-align:middle;\">Institution</th>
					<th style=\"width:75px; vertical-align:middle;\">No Participant</th>
					<th style=\"width:75px; vertical-align:middle;\">Present</th>
					<th style=\"width:75px; vertical-align:middle;\">Absen</th>
				</tr>				
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>					
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tParticipant\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tPresent\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tAbsen\">&nbsp;</td>
				</tr>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pParticipant\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pPresent\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pAbsen\">&nbsp;</td>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
			
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Monthly Trainning Report"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($_GET[mSearch])." ".$_GET[tSearch]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
		$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
		$objPHPExcel->getActiveSheet()->mergeCells('G4:G5');
		$objPHPExcel->getActiveSheet()->mergeCells('H4:J4');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DATE OF CONDUCT');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'TRAINING TOPIC');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TRAINING PROVIDER');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'FASILITATOR');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'INSTITUTION');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TYPE OF TRAINING');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TRAINING VENUE');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'NO PARTICIPANT');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'PRESENT');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'ABSEN');
								
		$rows = 6;
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and year(t1.mulaiPelatihan)='".$_GET['tSearch']."' and month(t1.mulaiPelatihan)='".$_GET['mSearch']."'";
		
		$arrAbsensi = arrayQuery("select t1.idPelatihan, t2.statusAbsensi, count(t2.idPeserta) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2");		
		
		$sql="select * from plt_pelatihan t1 join dta_vendor t2 join dta_trainer t3 on (t1.idVendor=t2.kodeVendor and t1.idTrainer=t3.idTrainer) $sWhere order by t1.mulaiPelatihan";						
		$res=db($sql);
		$no=1;
		while($r=mysql_fetch_array($res)){
			
			$pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getTanggal($r[mulaiPelatihan]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[judulPelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaTrainer]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[namaVendor]);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $pelaksanaanPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[lokasiPelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka(array_sum($arrAbsensi["".$r[idPelatihan].""])));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($arrAbsensi["".$r[idPelatihan].""]["t"]));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, getAngka($arrAbsensi["".$r[idPelatihan].""]["f"]));
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$objPHPExcel->getActiveSheet()->getStyle('H'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$totalParticipant+= array_sum($arrAbsensi["".$r[idPelatihan].""]);
			$totalPresent+= $arrAbsensi["".$r[idPelatihan].""]["t"];	
			$totalAbsen+= $arrAbsensi["".$r[idPelatihan].""]["f"];	
			
			$no++;
			$rows++;
		}
						
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "TOTAL");	
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($totalParticipant));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($totalPresent));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, getAngka($totalAbsen));
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$rows++;
				
		$persenPresent = $totalParticipant > 0 ? $totalPresent / $totalParticipant * 100 : 0;
		$persenAbsen = $totalParticipant > 0 ? $totalAbsen / $totalParticipant * 100 : 0;
			
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka(100)."%");
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($persenPresent,2)."%");
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, getAngka($persenAbsen,2)."%");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 5);
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
				$text=gData();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
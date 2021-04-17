<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		for($i=1; $i<=4; $i++){
			for($b=1; $b<=3; $b++) $a++;						
			
			$d = array();
			$e = str_pad($a-2, 2, "0", STR_PAD_LEFT);
			for($c=1; $c<=3; $c++){
				$d[]=str_pad($e, 2, "0", STR_PAD_LEFT);
				$e++;
			}
			
			$tanggalMulai[$i] = $_GET['tSearch']."-".str_pad($a-2, 2, "0", STR_PAD_LEFT)."-01";
			$tanggalSelesai[$i] = $_GET['tSearch']."-".str_pad($a, 2, "0", STR_PAD_LEFT)."-".date("t", strtotime($_GET['tSearch']."-".str_pad($a, 2, "0", STR_PAD_LEFT)."-01"));
			$arrBulan[$i] = $d;
		}
		
		$sWhere= "where t1.tanggalJadwal between '".$tanggalMulai["".$_GET['mSearch'].""]."' and '".$tanggalSelesai["".$_GET['mSearch'].""]."'";		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				
						
		$sql="select * from plt_pelatihan_jadwal t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan and t1.idJadwal=t2.idJadwal) $sWhere";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunJadwal, $bulanJadwal) = explode("-", $r[tanggalJadwal]);						
			for($i=0; $i<3; $i++){								
				if($bulanJadwal == $arrBulan["".$_GET['mSearch'].""][$i]){
					$jmlPeserta[$i]++;
					if($r[statusAbsensi] == "t")
						$jmlHadir[$i]++;
					else
						$jmlTidak[$i]++;
				}
			}
		}
	
		$json = array(			
			"iTotalDisplayRecords" => 3,
			"aaData" => array(),
		);
		
		
		
		$persenHadir = array_sum($jmlPeserta) > 0 ? array_sum($jmlHadir)/array_sum($jmlPeserta)*100 : 0;
		$data=array(
			"<div align=\"center\">1.</div>",								
			"<div align=\"left\">Jumlah peserta yang hadir</div>",
			"<div align=\"center\">".getAngka($jmlHadir[0])."</div>",
			"<div align=\"center\">".getAngka($jmlHadir[1])."</div>",
			"<div align=\"center\">".getAngka($jmlHadir[2])."</div>",
			"<div align=\"center\">".getAngka(array_sum($jmlHadir))."</div>",
			"<div align=\"center\">".getAngka($persenHadir)."%</div>",
			"<div align=\"left\">&nbsp;</div>",
		);			
		$json['aaData'][]=$data;
		
		$persenTidak = array_sum($jmlPeserta) > 0 ? array_sum($jmlTidak)/array_sum($jmlPeserta)*100 : 0;
		$data=array(
			"<div align=\"center\">2.</div>",								
			"<div align=\"left\">Jumlah peserta yang tidak hadir</div>",
			"<div align=\"center\">".getAngka($jmlTidak[0])."</div>",
			"<div align=\"center\">".getAngka($jmlTidak[1])."</div>",
			"<div align=\"center\">".getAngka($jmlTidak[2])."</div>",
			"<div align=\"center\">".getAngka(array_sum($jmlTidak))."</div>",
			"<div align=\"center\">".getAngka($persenTidak)."%</div>",
			"<div align=\"left\">&nbsp;</div>",
		);			
		$json['aaData'][]=$data;
		
		$persenPeserta = array_sum($jmlPeserta) > 0 ? 100 : 0;
		$data=array(
			"<div align=\"center\">3.</div>",								
			"<div align=\"left\">Jumlah Total Peserta yang ditugaskan dari training yang diselenggarakan</div>",
			"<div align=\"center\">".getAngka($jmlPeserta[0])."</div>",
			"<div align=\"center\">".getAngka($jmlPeserta[1])."</div>",
			"<div align=\"center\">".getAngka($jmlPeserta[2])."</div>",
			"<div align=\"center\">".getAngka(array_sum($jmlPeserta))."</div>",
			"<div align=\"center\">".getAngka($persenPeserta)."%</div>",
			"<div align=\"left\">&nbsp;</div>",
		);
		$json['aaData'][]=$data;
		
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$pSearch = empty($_GET[pSearch]) ? "" : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? 1 : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('Y') : $_GET[tSearch];
		$text = table(8, array(1,2,3,4,5,6,7,8));
		
		$text.="<script>
				function setTriwulan(){
					triwulan = document.getElementById('mSearch').value;
					if(triwulan == 1){
						$s1 = 'Jan';
						$s2 = 'Feb';
						$s3 = 'Mar';
					}
					if(triwulan == 2){
						$s1 = 'Apr';
						$s2 = 'Mei';
						$s3 = 'Jun';
					}
					if(triwulan == 3){
						$s1 = 'Jul';
						$s2 = 'Ags';
						$s3 = 'Sep';
					}
					if(triwulan == 4){
						$s1 = 'Okt';
						$s2 = 'Nov';
						$s3 = 'Des';
					}
				}
			</script>
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Pelatihan : ".comboData("select * from plt_pelatihan where statusPelatihan='t' order by judulPelatihan","idPelatihan","judulPelatihan","pSearch","All",$pSearch,"", "310px")."			
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td>".comboKey("mSearch", array(1=>"Triwulan I", 2=>"Triwulan II", 3=>"Triwulan III", 4=>"Triwulan IV"), $mSearch, "onchange=\"setTriwulan();\"")."</td>				
				<td>".comboYear("tSearch", $tSearch)."</td>								
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value + '&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"min-width:30px; max-width:30px; vertical-align:middle;\">No.</th>
					<th rowspan=\"2\" style=\"min-width:100px; vertical-align:middle;\">Description</th>
					<th colspan=\"3\" style=\"vertical-align:middle;\">Month</th>
					<th rowspan=\"2\" style=\"min-width:75px; max-width:75px; vertical-align:middle;\">Total</th>
					<th rowspan=\"2\" style=\"min-width:75px; max-width:75px; vertical-align:middle;\">%</th>
					<th rowspan=\"2\" style=\"min-width:100px; vertical-align:middle;\">Remarks</th>
				</tr>
				<tr>
					<th style=\"min-width:75px; max-width:75px; vertical-align:middle;\"><span id=\"s1\">Jan</span></th>
					<th style=\"min-width:75px; max-width:75px; vertical-align:middle;\"><span id=\"s2\">Feb</span></th>
					<th style=\"min-width:75px; max-width:75px; vertical-align:middle;\"><span id=\"s3\">Mar</span></th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
			
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		
		$arrTriwulan = array(1=>"Triwulan I", 2=>"Triwulan II", 3=>"Triwulan III", 4=>"Triwulan IV");
		$namaPelatihan = getField("select judulPelatihan from plt_pelatihan where idPelatihan='".$_GET[pSearch]."'");
		if(empty($namaPelatihan)) $namaPelatihan = "ALL";
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Trainning Report Triwulan"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', $arrTriwulan["".$_GET[mSearch].""]." Tahun ".$_GET[tSearch]);
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Pelatihan : ".$namaPelatihan);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:H5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:E4');
		$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');
		$objPHPExcel->getActiveSheet()->mergeCells('G4:G5');
		$objPHPExcel->getActiveSheet()->mergeCells('H4:H5');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DESCRIPTION');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'MONTH');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TOTAL');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', '%');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'REMARKS');
									
		if($_GET['mSearch'] == 1){
			$s1 = 'JAN';
			$s2 = 'FEB';
			$s3 = 'MAR';
		}
		if($_GET['mSearch'] == 2){
			$s1 = 'APR';
			$s2 = 'MEI';
			$s3 = 'JUN';
		}
		if($_GET['mSearch'] == 3){
			$s1 = 'JUL';
			$s2 = 'AGS';
			$s3 = 'SEP';
		}
		if($_GET['mSearch'] == 4){
			$s1 = 'OKT';
			$s2 = 'NOV';
			$s3 = 'DES';
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue('C5', $s1);		
		$objPHPExcel->getActiveSheet()->setCellValue('D5', $s2);		
		$objPHPExcel->getActiveSheet()->setCellValue('E5', $s3);		
		
		$rows = 6;
		
		for($i=1; $i<=4; $i++){
			for($b=1; $b<=3; $b++) $a++;						
			
			$d = array();
			$e = str_pad($a-2, 2, "0", STR_PAD_LEFT);
			for($c=1; $c<=3; $c++){
				$d[]=str_pad($e, 2, "0", STR_PAD_LEFT);
				$e++;
			}
			
			$tanggalMulai[$i] = $_GET['tSearch']."-".str_pad($a-2, 2, "0", STR_PAD_LEFT)."-01";
			$tanggalSelesai[$i] = $_GET['tSearch']."-".str_pad($a, 2, "0", STR_PAD_LEFT)."-".date("t", strtotime($_GET['tSearch']."-".str_pad($a, 2, "0", STR_PAD_LEFT)."-01"));
			$arrBulan[$i] = $d;
		}
		
		$sWhere= "where t1.tanggalJadwal between '".$tanggalMulai["".$_GET['mSearch'].""]."' and '".$tanggalSelesai["".$_GET['mSearch'].""]."'";		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				
						
		$sql="select * from plt_pelatihan_jadwal t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan and t1.idJadwal=t2.idJadwal) $sWhere";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunJadwal, $bulanJadwal) = explode("-", $r[tanggalJadwal]);						
			for($i=0; $i<3; $i++){								
				if($bulanJadwal == $arrBulan["".$_GET['mSearch'].""][$i]){
					$jmlPeserta[$i]++;
					if($r[statusAbsensi] == "t")
						$jmlHadir[$i]++;
					else
						$jmlTidak[$i]++;
				}
			}
		}
			
		
		$persenHadir = array_sum($jmlPeserta) > 0 ? array_sum($jmlHadir)/array_sum($jmlPeserta)*100 : 0;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "1.");				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "Jumlah peserta yang hadir");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($jmlHadir[0]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($jmlHadir[1]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($jmlHadir[2]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka(array_sum($jmlHadir)));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($persenHadir)."%");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$rows++;
		
		$persenTidak = array_sum($jmlPeserta) > 0 ? array_sum($jmlTidak)/array_sum($jmlPeserta)*100 : 0;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "2.");				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "Jumlah peserta yang tidak hadir");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($jmlTidak[0]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($jmlTidak[1]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($jmlTidak[2]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka(array_sum($jmlTidak)));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($persenTidak)."%");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$rows++;
		
		$persenPeserta = array_sum($jmlPeserta) > 0 ? 100 : 0;		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "3.");				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "Jumlah Total Peserta yang ditugaskan dari training yang diselenggarakan");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($jmlPeserta[0]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($jmlPeserta[1]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getAngka($jmlPeserta[2]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka(array_sum($jmlPeserta)));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($persenPeserta)."%");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$rows++;
		
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
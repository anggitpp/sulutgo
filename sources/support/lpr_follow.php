<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
		
		if(empty($par[mulaiTiket])) $par[mulaiTiket] = date('01/m/Y');
		if(empty($par[selesaiTiket])) $par[selesaiTiket] = date('d/m/Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td><input type=\"text\" id=\"mulaiTiket\" name=\"par[mulaiTiket]\" size=\"10\" maxlength=\"10\" value=\"".$par[mulaiTiket]."\" class=\"vsmallinput hasDatePicker\"/></td>
				<td>s.d</td>
				<td><input type=\"text\" id=\"selesaiTiket\" name=\"par[selesaiTiket]\" size=\"10\" maxlength=\"10\" value=\"".$par[selesaiTiket]."\" class=\"vsmallinput hasDatePicker\"/></td>				
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>	
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" width=\"75\">Nomor</th>
					<th rowspan=\"2\">Judul</th>
					<th colspan=\"5\">Tanggal</th>					
					<th rowspan=\"2\" width=\"50\">Status</th>
				</tr>
				<tr>
					<th width=\"75\">Analisa</th>
					<th width=\"75\">Diperiksa</th>
					<th width=\"75\">Disetujui</th>
					<th width=\"75\">Dikerjakan</th>
					<th width=\"75\">Testing</th>
				</tr>
			</thead>
			<tbody>";
		
		
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."'";
		
		$arrIcon = array(
			"<img src=\"styles/images/f.png\" title=\"Belum Selesai\">",
			"<img src=\"styles/images/f.png\" title=\"Belum Selesai\">",
			"<img src=\"styles/images/p.png\" title=\"Masih Diproses\">",
			"<img src=\"styles/images/o.png\" title=\"Pending\">",
			"<img src=\"styles/images/t.png\" title=\"Selesai\">",
		);
		$arrStatus = arrayQuery("select kodeData, urutanData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData");		
				
		$sql="select *, t1.idStatus from sup_tiket t1 join sup_analisa t2 join sup_status t3 on (t1.idTiket=t2.idTiket and t1.idTiket=t3.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;						
			$urutanStatus = $arrStatus["$r[idStatus]"];
			
			$tesStatus = "<img src=\"styles/images/f.png\" title=\"Belum Tes\">";
			if($r[tesStatus] == "t") $tesStatus = "<img src=\"styles/images/p.png\" title=\"Proses Testing\">";
			if($r[tesStatus] == "f") $tesStatus = "<img src=\"styles/images/t.png\" title=\"Selesai\">";
			
			$text.="<tr>
					<td>$no.</td>
					<td align=\"center\">".getTanggal($r[tanggalTiket])."</td>
					<td align=\"center\">".str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)."</td>
					<td>$r[namaTiket]</td>
					<td align=\"center\">".getTanggal($r[tanggalAnalisa])."</td>
					<td align=\"center\">".getTanggal($r[diperiksaTanggal])."</td>
					<td align=\"center\">".getTanggal($r[disetujuiTanggal])."</td>
					<td align=\"center\">".getTanggal($r[dikerjakanMulai])."</td>
					<td align=\"center\">".getTanggal($r[tesMulai])."</td>
					<td align=\"center\">".$tesStatus."</td>
					</tr>";			
		}
		
		$text.="</tbody>
			</table>
			</div>";
			
		if($par[mode] == "xls"){			
			xls();
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}
		
		return $text;
	}		
	
	function xls(){		
		global $s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'REKAP FOLLOW UP');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal(setTanggal($par[mulaiTiket]),"t")." s.d ".getTanggal(setTanggal($par[selesaiTiket]),"t"));
		
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
		$objPHPExcel->getActiveSheet()->mergeCells('E4:I4');
		$objPHPExcel->getActiveSheet()->mergeCells('J4:J5');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NOMOR');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JUDUL');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'PRIORITAS');		
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'STATUS');
		
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'ANALISA');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'DIPERIKSA');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'DISETUJUI');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'DIKERJAKAN');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'TESTING');
		
		$rows = 6;
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."'";
		
		$arrIcon = array(
			"fff00000",
			"fff00000",
			"fffff000",
			"00000999",
			"11199000",
		);
		$arrStatus = arrayQuery("select kodeData, urutanData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData");		
				
		$sql="select *, t1.idStatus from sup_tiket t1 join sup_analisa t2 join sup_status t3 on (t1.idTiket=t2.idTiket and t1.idTiket=t3.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;					
			$urutanStatus = $arrStatus["$r[idStatus]"];
					
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':I'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
			
			$tesStatus = "fff00000";
			if($r[tesStatus] == "t") $tesStatus = "fffff000";
			if($r[tesStatus] == "f") $tesStatus = "11199000";
			
			$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($tesStatus);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getTanggal($r[tanggalTiket]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)." ");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaTiket]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tanggalAnalisa]));	
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getTanggal($r[diperiksaTanggal]));	
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($r[disetujuiTanggal]));			
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getTanggal($r[dikerjakanMulai]));			
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getTanggal($r[tesMulai]));			
			
			$rows++;							
		}
		
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
		$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){				
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
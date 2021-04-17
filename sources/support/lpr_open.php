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
					<th width=\"20\">No.</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"75\">Nomor</th>
					<th>Judul</th>			
					<th width=\"150\">User</th>
					<th width=\"75\">Prioritas</th>
					<th width=\"75\">Jenis</th>
					<th width=\"75\">Target Selesai</th>
				</tr>
			</thead>
			<tbody>";
		
				
		$idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData desc limit 1");		
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."' and t5.tesStatus!='f'";
						
		$sql="select *, t3.namaData as namaPrioritas, t4.namaData as namaJenis from sup_tiket t1 join app_user t2 join mst_data t3 join mst_data t4 join sup_status t5 on (t1.createBy=t2.username and t1.idPrioritas=t3.kodeData and t1.idTipe=t4.kodeData and t1.idTiket=t5.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;			
			$text.="<tr>
					<td>$no.</td>
					<td align=\"center\">".getTanggal($r[tanggalTiket])."</td>
					<td align=\"center\">".str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)."</td>
					<td>$r[namaTiket]</td>
					<td>$r[namaUser]</td>
					<td>$r[namaPrioritas]</td>
					<td>$r[namaJenis]</td>
					<td align=\"center\">".getTanggal($r[dikerjakanSelesai])."</td>					
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(40);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'OPEN TICKET');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal(setTanggal($par[mulaiTiket]),"t")." s.d ".getTanggal(setTanggal($par[selesaiTiket]),"t"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NOMOR');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JUDUL');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'USER');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'PRIORITAS');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'JENIS');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'TARGET SELESAI');
		
		$rows = 5;
		$idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData desc limit 1");		
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."' and t5.tesStatus!='f'";
						
		$sql="select *, t3.namaData as namaPrioritas, t4.namaData as namaJenis from sup_tiket t1 join app_user t2 join mst_data t3 join mst_data t4 join sup_status t5 on (t1.createBy=t2.username and t1.idPrioritas=t3.kodeData and t1.idTipe=t4.kodeData and t1.idTiket=t5.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
						
		
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getTanggal($r[tanggalTiket]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)." ");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaTiket]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[namaUser]);			
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[namaPrioritas]);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[namaJenis]);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getTanggal($r[dikerjakanSelesai]));
			
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
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
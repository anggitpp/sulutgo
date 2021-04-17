	<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;						
	
	



	

	$text="
	
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."			
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				
				
				 
						
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>	
			</div>
			
			
				
			
		</form>
		
		
		";
		
		// if(!empty($par[lokasi])){
		// 	$filter =" and t2.location = '$par[lokasi]'";
		// }
		
		$pria = getField("select count(t1.id) from emp t1 join emp_phist t2 on t1.id = t2.parent_id where upper(t1.gender) = 'M' AND cat IN (531,532) $filter");
		$wanita = getField("select count(t1.id) from emp t1 join emp_phist t2 on t1.id = t2.parent_id where upper(t1.gender) = 'F' AND cat IN (531,532) $filter");

		$persenpria = ($pria / ($pria+$wanita)) * 100;
		$persenwanita = ($wanita / ($pria+$wanita)) * 100;
		$text.="
		<table cellpadding=\"0\" style=\"width:700px;float:left;\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" >
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th width=\"*\">Jenis Kelamin</th>
					<th width=\"100\">Jumlah</th>
					<th width=\"50\">%</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Pria</td>
					<td align=\"right\">".$pria."</td>
					<td align=\"right\">".round($persenpria,2)." %</td>
				</tr>
				<tr>
					<td>2</td>
					<td>Wanita</td>
					<td align=\"right\">".$wanita."</td>
					<td align=\"right\">".round($persenwanita,2)." %</td>
				</tr>
			</tbody>
		</table>
	

	<div class=\"widgetbox\" style=\"width:30%;margin-left:50px;float:left;\">
						
					
					<div id=\"divPelatihan\" align=\"center\"></div>
					<script type=\"text/javascript\">
					var pelatihanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";
					
					
					
							
							$text.="<set value=\"".$pria."\" label=\"Pria\" showValue=\"1\" />";
							$text.="<set value=\"".$wanita."\" label=\"Wanita\" showValue=\"1\" />";
								
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPelatihan\", \"100%\", 350);
						chart.setXMLData( pelatihanChart );
						chart.render(\"divPelatihan\");
					</script>
		</div>
		
	</div>";

	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=PERSENTASE GENDER TETAP.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}	

	return $text;
}		

function xls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
	require_once 'plugins/PHPExcel.php';

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);	

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PERSENTASE GENDER TETAP');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'JENIS KELAMIN');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'JUMLAH');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', '%');

	$rows = 5;

		
		$pria = getField("select count(t1.id) from emp t1 join emp_phist t2 on t1.id = t2.parent_id where upper(t1.gender) = 'M' t1.cat IN (531,532) $filter");
		$wanita = getField("select count(t1.id) from emp t1 join emp_phist t2 on t1.id = t2.parent_id where upper(t1.gender) = 'F' t1.cat IN (531,532) $filter");

		$persenpria = ($pria / ($pria+$wanita)) * 100;
		$persenwanita = ($wanita / ($pria+$wanita)) * 100;		

	$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A5'.':D6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A5', '1');				
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'PRIA');
	$objPHPExcel->getActiveSheet()->setCellValue('C5', $pria);
	$objPHPExcel->getActiveSheet()->setCellValue('D5', round($persenpria,2));

	$objPHPExcel->getActiveSheet()->setCellValue('A6', '2');				
	$objPHPExcel->getActiveSheet()->setCellValue('B6', 'WANITA');
	$objPHPExcel->getActiveSheet()->setCellValue('C6', $wanita);
	$objPHPExcel->getActiveSheet()->setCellValue('D6', round($persenwanita,2));
	




$objPHPExcel->getActiveSheet()->getStyle('A4:A6')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A4:A6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B4:B6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C4:C6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('D4:D6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('A1:D6')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:D6')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('A6:D6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

$objPHPExcel->getActiveSheet()->setTitle("PERSENTASE GENDER TETAP");
$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel file
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($fFile."PERSENTASE GENDER TETAP.xls");
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
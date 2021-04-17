<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fExport = "files/export/";
	
	function lihat(){
		global $s,$inp,$par,$arrParameter,$arrTitle,$menuAccess,$cUsername;		
		if(empty($par[bulan])) $par[bulan]=date('m');
		if(empty($par[tahun])) $par[tahun]=date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">			
			<table style=\"width:100%; margin-bottom:5px;\">
				<tr>
				<td width=\"50\">Period</td>
				<td>: ".comboMonth("par[bulan]",$par[bulan])." ".comboYear("par[tahun]",$par[tahun])." 
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" style=\"width:200px;\" />
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_document\"  style=\"float:right; margin-left:10px;\"><span>Export Data</span></a>
				</td>				
				</tr>				
			</table>			
			</form>			
			<form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"75\">Date</th>
					<th width=\"50\">Time</th>
					<th>User</th>					
					<th width=\"250\">Menu</th>
					<th width=\"100\">Activity</th>";
		$text.="</tr>
			</thead>
			<tbody>";		
				
		$filter = "where month(t1.createTime) = '$par[bulan]' and year(t1.createTime) = '$par[tahun]'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(t2.namaMenu) like '%".strtolower($par[filter])."%'
			or lower(t3.namaUser) like '%".strtolower($par[filter])."%'
		)";
		
		$arrModul = arrayQuery("select kodeModul, namaModul from app_modul order by kodeModul");		
		
		$sql="select t1.*,t2.kodeInduk,t2.namaMenu,t3.namaUser from log_access t1 join app_menu t2 join app_user t3 on (t1.kodeMenu=t2.kodeMenu and t1.createBy=t3.username) $filter order by t1.createTime desc";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$no++;
			list($tanggalCreate, $waktuCreate) = explode(" ", $r[createTime]);
			$namaMenu = isset($arrTitle["$r[kodeInduk]"]) ?
			$arrTitle["$r[kodeInduk]"]." &rsaquo; ".$r[namaMenu]:
			$r[namaMenu];
			$text.="<tr>
					<td>$no.</td>
					<td align=\"center\">".getTanggal($tanggalCreate)."</td>
					<td align=\"center\">".$waktuCreate."</td>										
					<td>$r[namaUser]</td>					
					<td nowrap=\"nowrap\">".$arrModul["$r[kodeModul]"]." - $r[namaMenu]</td>
					<td>".aktivitasLog($r[aktivitasLog])."</td>
					</tr>";
		}
		
		$text.="</tbody>
			</table>			
			<form>
			</div>";
			
		if($par[mode] == "xls"){
			xls();
			db("truncate table log_access");
			$text.="<iframe src=\"download.php?d=exp&f=".$arrTitle[$s]." ".date('Y-m-d_H').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";			
		}
		
		return $text;
	}
	
	function aktivitasLog($aktivitasLog){
		if($aktivitasLog == "open page")
		$aktivitasLog = "<div style=\"background:#fff; color:#000; border:solid 1px #999; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		if($aktivitasLog == "view detail")
		$aktivitasLog = "<div style=\"background:#000; color:#fff; border:solid 1px #ccc; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		if($aktivitasLog == "input data")
		$aktivitasLog = "<div style=\"background:#00c000; color:#fff; border:solid 1px #333; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		if($aktivitasLog == "edit data")
		$aktivitasLog = "<div style=\"background:#0000ff; color:#fff; border:solid 1px #333; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		if($aktivitasLog == "delete data")
		$aktivitasLog = "<div style=\"background:#ff0000; color:#fff; border:solid 1px #333; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		if($aktivitasLog == "update data")
		$aktivitasLog = "<div style=\"background:#ffff00; color:#000; border:solid 1px #999; padding:0 5px;\">".ucwords($aktivitasLog)."</div>";
		
		return $aktivitasLog;
	}
	
	function xls(){
		global $s,$m,$inp,$par,$arrParameter,$arrTitle,$menuAccess,$fExport,$cUser;	
		require_once 'plugins/PHPExcel.php';
				
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cUser)
							 ->setLastModifiedBy($cUser)
							 ->setTitle($arrTitle[$s]);										
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);		
				
		$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
		
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
		$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
		$objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(20);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($arrTitle[$s]));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', strtoupper($namaProduk));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DATE');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'TIME');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'USER');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'MENU');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'ACTIVITY');		
		
		$rows=5;
		$sql="select t1.*,t2.kodeInduk,t2.namaMenu,t3.namaUser from log_access t1 join app_menu t2 join app_user t3 on (t1.kodeMenu=t2.kodeMenu and t1.createBy=t3.username) order by t1.createTime desc";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$no++;
			list($tanggalCreate, $waktuCreate) = explode(" ", $r[createTime]);
			$namaMenu = isset($arrTitle["$r[kodeInduk]"]) ?
			$arrTitle["$r[kodeInduk]"]." &rsaquo; ".$r[namaMenu]:
			$r[namaMenu];
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);									
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
			$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(20);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no.'.');
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $tanggalCreate);			
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $waktuCreate);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaUser]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[namaMenu]);			
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[aktivitasLog]);
			
			$no++;
			$rows++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.($rows-1))->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.($rows-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getFont()->setName('Arial');
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(85);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(85);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 5);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle($arrTitle[$s]);
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fExport.$arrTitle[$s]." ".date('Y-m-d_H').".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;		
		switch($par[mode]){				
			case "det":
				$text = detail();
			break;					
			default:				
				$text = lihat();				
			break;
		}
		return $text;
	}	
?>
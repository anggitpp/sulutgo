<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
	if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');								

	$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));				

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
			Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>	
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			<br clear=\"all\" />			
		</form>
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:350px;\">Nama</th>
					<th style=\"min-width:100px;\">NPP</th>";					
					for($i=1; $i<=$day; $i++)
						$text.="<th style=\"min-width:40px;\">".$i."</th>";
					$text.="<th style=\"min-width:40px;\">Total</th>					
				</tr>
			</thead>
			<tbody>";

				$sql="select * from att_absen where month(tanggalAbsen)='".$par[bulanAbsen]."' and year(tanggalAbsen)='".$par[tahunAbsen]."'";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					list($tahunAbsen, $bulanAbsen, $tanggalAbsen) = explode("-", $r[tanggalAbsen]);
					$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
					$arrAbsen["$r[idPegawai]"][intval($tanggalAbsen)] = str_replace("-","",$r[durasiAbsen]);
				}

				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
				$filter = "where t1.id is not null and t1.status='".$status."' and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";

				if(!empty($par[idLokasi]))
					$filter.= " and t2.location='".$par[idLokasi]."'";
				
				if($par[search] == "Nama")
					$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$filter .= " AND t2.location IN ($areaCheck)";

				$sql="select t1.* from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$no++;													
					$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>";
					for($i=1; $i<=$day; $i++){
						$week = date("w", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
						$color = in_array($week, array(0,6)) ? "style=\"background:#f2dbdb\"" : "";
						$text.="<td align=\"center\" ".$color.">".substr($arrAbsen["$r[id]"][$i],0,5)."</td>";
					}
					$text.="<td align=\"center\">".substr(sumTime($arrAbsen["$r[id]"]),0,5)."</td>
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
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
	require_once 'plugins/PHPExcel.php';

	$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));													

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$cols = 4;
	for($i=1; $i<=$day; $i++){
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);		

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$namaDivisi = getField("select namaData from mst_data where kodeData='".$par[idDivisi]."'");
	$namaDepartemen = getField("select namaData from mst_data where kodeData='".$par[idDepartemen]."'");				

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN ABSENSI BULANAN');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanAbsen]).' '.$par[tahunAbsen]);
	$objPHPExcel->getActiveSheet()->setCellValue('A3', $namaDivisi.' '.$namaDepartemen);

	$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');

	$cols=4;
	for($i=1; $i<=$day; $i++){
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $i);		
		$cols++;
	}
	$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TOTAL');								

	$rows = 5;
	$sql="select * from att_absen where month(tanggalAbsen)='".$par[bulanAbsen]."' and year(tanggalAbsen)='".$par[tahunAbsen]."'";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		list($tahunAbsen, $bulanAbsen, $tanggalAbsen) = explode("-", $r[tanggalAbsen]);
		$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
		$arrAbsen["$r[idPegawai]"][intval($tanggalAbsen)] = str_replace("-","",$r[durasiAbsen]);
	}

	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
	$filter = "where t1.id is not null and t1.status='".$status."' and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";

	if(!empty($par[idLokasi]))
		$filter.= " and t2.location='".$par[idLokasi]."'";

	if($par[search] == "Nama")
		$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
	else if($par[search] == "NPP")
		$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
	else
		$filter.= " and (
	lower(t1.name) like '%".strtolower($par[filter])."%'
	or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
	)";

	$filter .= " AND t2.location IN ($areaCheck)";
	
	$sql="select t1.* from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$no++;								

		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);

		$cols=4;
		for($i=1; $i<=$day; $i++){
			$week = date("w", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));

			if(in_array($week, array(0,6)))
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fff88888");

			if(isset($arrAbsen["$r[id]"][$i]))
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, substr($arrAbsen["$r[id]"][$i],0,5));
			$cols++;
		}

		if(isset($arrAbsen["$r[id]"]))
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, substr(sumTime($arrAbsen["$r[id]"]),0,5));
		$rows++;

	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$cols=4;
	for($i=1; $i<=$day; $i++){
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
	}	
	$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){			
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
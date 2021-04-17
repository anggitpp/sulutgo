<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
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
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>".comboMonth("par[bulanData]", $par[bulanData], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunData]", $par[tahunData], "", "onchange=\"document.getElementById('form').submit();\"")."</td>
						<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"", "310px")."</td>
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
					<th style=\"min-width:100px;\">Nama</th>
					<th style=\"width:100px;\">NPP</th>
					<th style=\"min-width:100px;\">Pangkat</th>
					<th style=\"min-width:100px;\">Jabatan</th>					
					<th style=\"width:85px;\">Start Date</th>
					<th style=\"width:85px;\">End Date</th>
				</tr>
			</thead>
			<tbody>";


				$kontrakBulan = $par[bulanData] > 9 ? $par[bulanData] + 3 - 12 : $par[bulanData] + 3;
				$kontrakTahun = $par[bulanData] > 9 ? $par[tahunData] + 1 : $par[tahunData];		
				$kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);

				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");

				if(!empty($par[idLokasi]))
					$filter= " and t1.location='".$par[idLokasi]."'";

				$filter .= " AND t1.location IN ($areaCheck)";

				$sql="select * from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= ".$kontrakMax." and t2.status='1' and t1.status='".$status."' ".$filter." and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."')";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){			
					$no++;			
					$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>".strtoupper($r[reg_no])."</td>
					<td>".strtoupper($arrPangkat["$r[rank]"])."</td>
					<td>".strtoupper($r[pos_name])."</td>					
					<td align=\"center\">".getTanggal($r[start_date])."</td>
					<td align=\"center\">".getTanggal($r[end_date])."</td>					
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

	$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");
	$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[14]."' order by urutanData");

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		

	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PEGAWAI YANG AKAN HABIS KONTRAK');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanData])." ".$par[tahunData]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'PANGKAT');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JABATAN');		
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'START DATE');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'END DATE');

	$rows = 5;

	$kontrakBulan = $par[bulanData] > 9 ? $par[bulanData] + 3 - 12 : $par[bulanData] + 3;
	$kontrakTahun = $par[bulanData] > 9 ? $par[tahunData] + 1 : $par[tahunData];		
	$kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);

	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
	$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");

	if(!empty($par[idLokasi]))
		$filter= " and t1.location='".$par[idLokasi]."'";

	$filter .= " AND t1.location IN ($areaCheck)";

	$sql="select * from dta_pegawai t1 join emp_pcontract t2 on (t1.id=t2.parent_id) where concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= ".$kontrakMax." and t2.status='1' and t1.status='".$status."' ".$filter." and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."')";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;			

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($arrPangkat["$r[rank]"]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, strtoupper($r[pos_name]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getTanggal($r[start_date]));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($r[end_date]));

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			

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

	$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
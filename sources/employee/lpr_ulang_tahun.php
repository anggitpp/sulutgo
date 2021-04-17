<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;						
	if(empty($par[bulan])) $par[bulan] = date('m');
	if(empty($par[tahun])) $par[tahun] = date('Y');		

	$text="
	<script>
		function nextDate(getPar){
			bulanData=document.getElementById('par[bulan]');
			tahunData=document.getElementById('par[tahun]');

			bulan = bulanData.value == 12 ? 01 : bulanData.value * 1 + 1;	
			tahun = bulanData.value == 12 ? tahunData.value * 1 + 1 : tahunData.value;

			bulanData.value = bulan > 9 ? bulan : '0' + bulan;
			tahunData.value = tahun;

			document.getElementById('form').submit();
		}

		function prevDate(getPar){
			bulanData=document.getElementById('par[bulan]');
			tahunData=document.getElementById('par[tahun]');

			bulan = bulanData.value == 01 ? 12 : bulanData.value * 1 - 1;	
			tahun = bulanData.value == 01 ? tahunData.value * 1 - 1 : tahunData.value;	

			bulanData.value = bulan > 9 ? bulan : '0' + bulan;
			tahunData.value = tahun;

			document.getElementById('form').submit();
		}
	</script>
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."			
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[lokasi]","All",$par[lokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				".comboMonth("par[bulan]", $par[bulan], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahun]", $par[tahun], "", "onchange=\"document.getElementById('form').submit();\"")."
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>				
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>				
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\"></td>
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
					<th style=\"min-width:150px;\">Nama</th>
					<th style=\"width:100px;\">NPP</th>										
					<th style=\"min-width:150px;\">Jabatan</th>
					<th width=\"150\">Tanggal Ulang Tahun</th>
					<th width=\"75\">Usia</th>
				</tr>
			</thead>
			<tbody>";

				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$filter = "where status='".$status."'";

				if(!empty($par[bulan]))
					$filter.= " and month(birth_date)='$par[bulan]'";

				if(!empty($par[lokasi]))
					$filter.= " and location='".$par[lokasi]."'";

				if($par[search] == "Nama")
					$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
				)";					

				$filter .= " AND location IN ($areaCheck)";
				$sql="select * from dta_pegawai $filter order by name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					list($tahun, $bulan, $tanggal) = explode("-", $r[birth_date]);
					$umur = getAngka(selisihTahun($r[birth_date], $par[tahun]."-".$bulan."-".$tanggal));
					$no++;						
					$text.="
					<tr>
						<td>$no.</td>			
						<td>".strtoupper($r[name])."</td>					
						<td>$r[reg_no]</td>					
						<td>".strtoupper($r[pos_name])."</td>					
						<td align=\"center\">".getTanggal($par[tahun]."-".$bulan."-".$tanggal, "t")."</td>		
						<td align=\"right\">".getAngka($umur)." Tahun</td>
					</tr>";				
				}	

				$text.="
			</tbody>
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

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);	

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PEGAWAI ULANG TAHUN');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JABATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TANGGAL ULANG TAHUN');				
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'USIA');

	$rows = 5;
	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
	$filter = "where status='".$status."'";

	if(!empty($par[bulan]))
		$filter.= " and month(birth_date)='$par[bulan]'";

	if(!empty($par[lokasi]))
		$filter.= " and location='".$par[lokasi]."'";

	if($par[search] == "Nama")
		$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
	else if($par[search] == "NPP")
		$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
	else
		$filter.= " and (
	lower(name) like '%".strtolower($par[filter])."%'
	or lower(reg_no) like '%".strtolower($par[filter])."%'
	)";					

	$filter .= " AND location IN ($areaCheck)";
	$sql="select * from dta_pegawai $filter order by name";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		list($tahun, $bulan, $tanggal) = explode("-", $r[birth_date]);
		$umur = getAngka(selisihTahun($r[birth_date], $par[tahun]."-".$bulan."-".$tanggal));
		$no++;						
		$text.="<tr>
		<td>$no.</td>			
		<td>".strtoupper($r[name])."</td>					
		<td>$r[reg_no]</td>					
		<td>".strtoupper($r[pos_name])."</td>					
		<td align=\"center\">".getTanggal($par[tahun]."-".$bulan."-".$tanggal, "t")."</td>		
		<td align=\"right\">".getAngka($umur)." Tahun</td>
	</tr>";				

	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($r[pos_name]));
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($par[tahun]."-".$bulan."-".$tanggal, "t"));			
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($umur)." Tahun");

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

$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('A6:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
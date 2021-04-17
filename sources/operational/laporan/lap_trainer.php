<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lData(){
	global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

	$sWhere= " where t1.statusPelatihan='t'";
	$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";

	if (!empty($_GET['pSearch']))
		$sWhere.= " and t1.idDepartemen='".$_GET['pSearch']."'";				

	$arrOrder = array(	
		"t1.mulaiPelatihan",			
		"t1.judulPelatihan",
		"t2.namaTrainer",			
		);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql="select * from plt_pelatihan t1 join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $sWhere order by $orderBy $sLimit";
	$res=db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(*) from plt_pelatihan t1 join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $sWhere"),
		"aaData" => array(),
		);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;

		$jamPelatihan = $menitPelatihan = 0;
		list($pertemuanPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));

		$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
		$menitPelatihan = $menitPelatihan%60;			
		$waktuPelatihan = $menitPelatihan > 0 ?
		getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
		getAngka($jamPelatihan)." Jam";

		$data=array(
			"<div align=\"center\">".$no.".</div>",								
			"<div align=\"left\">".$r[kodePelatihan]."</div>",
			"<div align=\"left\">".$r[namaTrainer]."</div>",								
			"<div align=\"right\">".$waktuPelatihan."</div>",				
			"<div align=\"right\">".$waktuPelatihan."</div>",				
			);		
		
		$json['aaData'][]=$data;
	}
	return json_encode($json);
}

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$pSearch = empty($_GET[pSearch]) ? "" : $_GET[pSearch];
	$mSearch = empty($_GET[mSearch]) ? date('01/m/Y') : $_GET[mSearch];
	$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];
	$text = table(5, array(4,5));

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."			
</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Departemen : ".comboData("select kodeData, upper(trim(namaData)) as namaData from mst_data where statusData='t' and kodeCategory='X06' group by 2 order by namaData","kodeData","namaData","pSearch","ALL",$pSearch,"", "310px")."		
		</div>
		<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
					<td>Periode : </td>
					<td><input type=\"text\" id=\"mSearch\" name=\"mSearch\" size=\"10\" maxlength=\"10\" value=\"".$mSearch."\" class=\"vsmallinput hasDatePicker\" /></td>
					<td>s.d</td>
					<td><input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" /></td>								
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
				<th style=\"width:30px; vertical-align:middle;\">No.</th>
				<th style=\"width:100px; vertical-align:middle;\">Training ID</th>
				<th style=\"min-width:100px; vertical-align:middle;\">Nama Training</th>
				<th style=\"width:100px; vertical-align:middle;\">Training Hours</th>
				<th style=\"width:100px; vertical-align:middle;\">Trainer Hours</th>
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);	


	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		

	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Laporan Jam Training dan Trainer"));
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
	$objPHPExcel->getActiveSheet()->setCellValue('A3', "Departemen : ".getField("select namaData from mst_data where kodeData='".$_GET[pSearch]."'"));

	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'TRAINING ID');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA TRAINING');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TRAINING HOURS');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TRAINER HOURS');


	$rows = 5;

	$sWhere= " where t1.statusPelatihan='t'";
	$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";

	if (!empty($_GET['pSearch']))
		$sWhere.= " and t1.idDepartemen='".$_GET['pSearch']."'";				

	$sql="select * from plt_pelatihan t1 join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $sWhere order by t1.mulaiPelatihan";
	$res=db($sql);
	$no=1;
	while($r=mysql_fetch_array($res)){
		$jamPelatihan = $menitPelatihan = 0;
		list($pertemuanPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));

		$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
		$menitPelatihan = $menitPelatihan%60;			
		$waktuPelatihan = $menitPelatihan > 0 ?
		getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
		getAngka($jamPelatihan)." Jam";

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[kodePelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[namaTrainer]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $waktuPelatihan);	
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $waktuPelatihan);	


		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$no++;
		$rows++;
	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
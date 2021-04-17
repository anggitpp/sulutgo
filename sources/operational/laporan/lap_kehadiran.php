<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= " where t2.statusPelatihan='t'";
		$sWhere.= " and t3.tanggalJadwal between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t2.idDepartemen='".$_GET['pSearch']."'";				
		
		$arrOrder = array(	
			"t3.tanggalJadwal",
			"t5.reg_no",
			"t5.name",
			"t3.idJadwal",
			"t3.tanggalJadwal",
			"t1.statusAbsensi",
			"t1.keteranganAbsensi",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		$sql="select * from plt_pelatihan_absensi t1 join plt_pelatihan t2 join plt_pelatihan_jadwal t3 join plt_pelatihan_peserta t4 join emp t5 on (t1.idPelatihan=t2.idPelatihan and t1.idPelatihan=t3.idPelatihan and t1.idJadwal=t3.idJadwal and t1.idPelatihan=t4.idPelatihan and t1.idPeserta=t4.idPeserta and t4.idPegawai=t5.id) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from plt_pelatihan_absensi t1 join plt_pelatihan t2 join plt_pelatihan_jadwal t3 join plt_pelatihan_peserta t4 join emp t5 on (t1.idPelatihan=t2.idPelatihan and t1.idPelatihan=t3.idPelatihan and t1.idJadwal=t3.idJadwal and t1.idPelatihan=t4.idPelatihan and t1.idPeserta=t4.idPeserta and t4.idPegawai=t5.id) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$statusAbsensi = $r[statusAbsensi] == "t" ? "&#x2714;" : "";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",								
				"<div align=\"left\">".$r[reg_no]."</div>",
				"<div align=\"left\">".strtoupper($r[name])."</div>",
				"<div align=\"center\">".getAngka($r[idJadwal])."</div>",
				"<div align=\"center\">".getTanggal($r[tanggalJadwal])."</div>",
				"<div align=\"center\">".$statusAbsensi."</div>",				
				"<div align=\"left\">".$r[keteranganAbsensi]."</div>",
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
		$text = table(7);
		
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
					<th style=\"width:125px; vertical-align:middle;\">NPP</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Nama</th>
					<th style=\"width:125px; vertical-align:middle;\">Pertemuan Ke</th>
					<th style=\"width:100px; vertical-align:middle;\">Tanggal</th>
					<th style=\"width:100px; vertical-align:middle;\">Hadir</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Alasan</th>
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

	
	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
	
	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Daftar Kehadiran Pelatihan Kelas"));
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
	$objPHPExcel->getActiveSheet()->setCellValue('A3', "Departemen : ".getField("select namaData from mst_data where kodeData='".$_GET[pSearch]."'"));
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
	
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'PERTEMUAN KE');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TANGGAL');
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'HADIR');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'ALASAN');
	
	$rows = 5;
	
	$sWhere= " where t2.statusPelatihan='t'";
	$sWhere.= " and t3.tanggalJadwal between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
	
	if (!empty($_GET['pSearch']))
		$sWhere.= " and t2.idDepartemen='".$_GET['pSearch']."'";				
	
	$sql="select * from plt_pelatihan_absensi t1 join plt_pelatihan t2 join plt_pelatihan_jadwal t3 join plt_pelatihan_peserta t4 join emp t5 on (t1.idPelatihan=t2.idPelatihan and t1.idPelatihan=t3.idPelatihan and t1.idJadwal=t3.idJadwal and t1.idPelatihan=t4.idPelatihan and t1.idPeserta=t4.idPeserta and t4.idPegawai=t5.id) $sWhere order by t1.idPelatihan";
 // echo $sql;
	$res=db($sql);
	$no=1;
	while($r=mysql_fetch_array($res)){
		

		$statusAbsensi = $r[statusAbsensi] == "t" ? "v" : "";
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, ' '.$r[reg_no]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getAngka($r[idJadwal]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tanggalJadwal]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows,$statusAbsensi);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[keteranganAbsensi]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
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
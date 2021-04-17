<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/export/";

$par[dlg] = "true";
switch($par[mode]){
	case "export":
	xls();
	default:
	include "perusahaan_view.php";
}

function xls(){		
	global $s,$par,$fFile, $cNama, $arrTitle, $cID;

	require_once 'plugins/PHPExcel.php';

	$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
	list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi) = explode("~", $infoSetting);

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]."_".$cNama);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

	$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');

	$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
	$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');

	$objPHPExcel->getActiveSheet()->mergeCells('C4:E4');
	$objPHPExcel->getActiveSheet()->mergeCells('C5:E5');

	$objPHPExcel->getActiveSheet()->mergeCells('A6:E6');


	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "Nama Atasan: " . $cNama);

	$objPHPExcel->getActiveSheet()->getStyle('A4:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->setCellValue('A4', "Penilaian");
	$objPHPExcel->getActiveSheet()->setCellValue('A5', "Pelaksanaan");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', $namaSetting);
	$objPHPExcel->getActiveSheet()->setCellValue('C5',  getTanggal($pelaksanaanMulai)." s/d ".getTanggal($pelaksanaanSelesai));

	$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A7:G7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A7', "NO");
	$objPHPExcel->getActiveSheet()->setCellValue('B7', "NAMA ASPEK");
	$objPHPExcel->getActiveSheet()->setCellValue('C7', "KETERANGAN");
	$objPHPExcel->getActiveSheet()->setCellValue('D7', "TARGET");
	$objPHPExcel->getActiveSheet()->setCellValue('E7', "HASL");
	
	$filter = "WHERE t3.idPenilai = '$cID'";
	if(!empty($par[idSetting]))
		$filter .= " AND t3.idSetting = '$par[idSetting]'";
	$sql = "
	SELECT 
	t1.namaAspek, t1.penilaianAspek, t1.targetAspek, t3.nilaiPenilaian,
	(
	SELECT 
	IFNULL(
	(
	SELECT 
	warnaKonversi 
	FROM pen_setting_konversi 
	WHERE 
	(
	t3.nilaiPenilaian BETWEEN nilaiMin AND nilaiMax) AND kodeKonversi = '$kodeKonversi'
	), '#FF0000')) as warnaKonversi
	FROM pen_setting_aspek t1
	JOIN pen_penilaian_detail t2 
	ON t2.idAspek = t1.idAspek 
	JOIN pen_penilaian t3
	ON t3.idPenilaian = t2.idPenilaian
	$filter 
	GROUP BY t1.idAspek
	ORDER BY t1.urutanAspek ASC
	";
	$res = db($sql);
	$ret = array();
	$currentRow = 8;
	$no = 0;
	$subNilai = 0;
	while($r = mysql_fetch_array($res)){
		$no++;
		$r[tglPenilaian] = $r[tglPenilaian] == "0000-00-00" || empty($r[tglPenilaian]) ? "-" : getTanggal($r[tglPenilaian]);
		$r[nilaiPenilaian] = getAngka($r[nilaiPenilaian], 2);
		
		$subNilai += $r[nilaiPenilaian];

		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$nilaiStyle = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FFFFFF')
				),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => str_replace("#", "", $r[warnaKonversi]))
				));

		$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fff00000");

		$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$currentRow, $no.".",PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$currentRow, $r[namaAspek]);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$currentRow, $r[penilaianAspek],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$currentRow, $r[targetAspek]);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->applyFromArray($nilaiStyle);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$currentRow, $r[nilaiPenilaian],PHPExcel_Cell_DataType::TYPE_STRING);

		$currentRow++;
	}

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
	$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s]))."_".$cNama.".xls");
}
/* End of file perusahaan_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/laporan/perusahaan/perusahaan_index.php */

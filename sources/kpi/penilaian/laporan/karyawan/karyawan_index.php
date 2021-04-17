<?php
if (!isset($menuAccess[$s]['view']))
    echo "<script>logout();</script>";

global $s, $par, $menuAccess;

$fFile = "files/export/";

$par[dlg] = "true";
switch ($par[mode]) {

    case "edit":
        include "./sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_edit.php";
        break;

    case "export":
        xls();

    default:
        include "karyawan_view.php";
        break;

}

function xls()
{
    global $s, $par, $fFile, $cNama, $arrTitle, $cID;

    require_once 'plugins/PHPExcel.php';

    $infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
    list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi) = explode("~", $infoSetting);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s] . "_" . $cNama);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

    $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(25);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
    $objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

    $objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
    $objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
    $objPHPExcel->getActiveSheet()->mergeCells('A6:B6');

    $objPHPExcel->getActiveSheet()->mergeCells('C4:F4');
    $objPHPExcel->getActiveSheet()->mergeCells('C5:F5');
    $objPHPExcel->getActiveSheet()->mergeCells('C6:F6');

    $objPHPExcel->getActiveSheet()->mergeCells('G4:G5');

    $objPHPExcel->getActiveSheet()->mergeCells('A7:G7');


    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
    $objPHPExcel->getActiveSheet()->setCellValue('A2', "Nama Atasan: " . $cNama);

    $objPHPExcel->getActiveSheet()->getStyle('A4:A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $objPHPExcel->getActiveSheet()->setCellValue('A4', "Penilaian");
    $objPHPExcel->getActiveSheet()->setCellValue('A5', "Pelaksanaan");
    $objPHPExcel->getActiveSheet()->setCellValue('A6', "Jumlah Karyawan");
    $objPHPExcel->getActiveSheet()->setCellValue('C4', $namaSetting);
    $objPHPExcel->getActiveSheet()->setCellValue('C5', getTanggal($pelaksanaanMulai) . " s/d " . getTanggal($pelaksanaanSelesai));
    $objPHPExcel->getActiveSheet()->setCellValue('C6', "0 Karyawan");

    $boxStyle = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => 'FFFFFF')
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '31849B')
        ));

    $objPHPExcel->getActiveSheet()->getStyle('G4:G6')->applyFromArray($boxStyle);
    $objPHPExcel->getActiveSheet()->getStyle('G4:G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('G4:G6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('G6', "NILAI RATA-RATA");

    $objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $objPHPExcel->getActiveSheet()->setCellValue('A8', "NO");
    $objPHPExcel->getActiveSheet()->setCellValue('B8', "NAMA");
    $objPHPExcel->getActiveSheet()->setCellValue('C8', "NPP");
    $objPHPExcel->getActiveSheet()->setCellValue('D8', "JABATAN");
    $objPHPExcel->getActiveSheet()->setCellValue('E8', "POSISI");
    $objPHPExcel->getActiveSheet()->setCellValue('F8', "NILAI");
    $objPHPExcel->getActiveSheet()->setCellValue('G8', "TANGGAL");

    $sql = "
	SELECT 
	t1.id, t1.name, t1.reg_no, t1.pos_name, t2.namaData as posisi, t3.idPenilaian, t3.nilaiPenilaian, t3.tglPenilaian, t3.apprStatus, 
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
	), '#FF0000')) as warnaKonversi, t4.tipePenilaian
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.rank
	LEFT JOIN pen_penilaian t3
	ON t3.idSetting = '$par[idSetting]' AND t3.idPenilai = '$cID' AND t3.idPegawai = t1.id
	JOIN pen_pegawai t4
	ON t4.idPegawai = t1.id
	WHERE t1.leader_id = '$cID'";
    $res = db($sql);
    $ret = array();
    $currentRow = 9;
    $no = 0;
    $subNilai = 0;
    while ($r = mysql_fetch_array($res)) {
        $no++;
        $r[tglPenilaian] = $r[tglPenilaian] == "0000-00-00" || empty($r[tglPenilaian]) ? "-" : getTanggal($r[tglPenilaian]);
        $r[nilaiPenilaian] = getAngka($r[nilaiPenilaian], 2);

        $subNilai += $r[nilaiPenilaian];

        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $nilaiStyle = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => str_replace("#", "", $r[warnaKonversi]))
            ));

        $objPHPExcel->getActiveSheet()->getStyle('F' . $currentRow)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fff00000");

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $currentRow, $no . ".", PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $currentRow, $r[name]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $currentRow, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $currentRow, $r[pos_name]);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $currentRow, $r[posisi]);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $currentRow)->applyFromArray($nilaiStyle);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $currentRow, $r[nilaiPenilaian], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $currentRow, $r[tglPenilaian], PHPExcel_Cell_DataType::TYPE_STRING);

        $currentRow++;
    }

    $objPHPExcel->getActiveSheet()->setCellValue('C6', "$no Karyawan");
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G4', getAngka(($subNilai / $no), 2), PHPExcel_Cell_DataType::TYPE_STRING);

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
    $objWriter->save($fFile . ucwords(strtolower($arrTitle[$s])) . "_" . $cNama . ".xls");
}

/* End of file karyawan_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/laporan/karyawan/karyawan_index.php */

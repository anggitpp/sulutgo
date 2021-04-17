<?php

/*
 *  Build on pojay.dev @42A
 */


/**
 * Description of class
 *
 * @author mazte
 */
require_once HOME_DIR . DS . "plugins/PHPExcel.php";

class UiHelperExtra {

  function exportXLS($datas, $header = array(), $fname = "xls_output.xlsx", $format = "xlsx") {
    $writerType = "Excel2007";
    if ($format == "xls") {
      $writerType = "Excel5";
      $fname = str_replace(".xlsx", ".xls", $fname);
    }
//    error_reporting(E_ALL);
//    ini_set('display_errors', TRUE);
//    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Asia/Jakarta');
    $hdrStyleArray = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 13,
            'name' => 'Verdana'
    ));
    $rowHdrStyleArray = array(
        'font' => array(
            'bold' => true,
    ));
    $ope = new PHPExcel();
    $ope->setActiveSheetIndex(0);
    $ws = $ope->getActiveSheet();
    $row = 1;
    $col = 0;
    foreach ($header as $hdr) {
      $ws->setCellValueByColumnAndRow($col, $row, $hdr);
      $ws->getStyleByColumnAndRow($col, $row)->applyFromArray($hdrStyleArray);
      $row++;
    }
    $dc = 0;
    $row++;
    foreach ($datas as $d) {
      $col = 0;
      if ($dc == 0) {
        foreach ($d as $key => $value) {
          $ws->setCellValueByColumnAndRow($col, $row, $key);
          $ws->getStyleByColumnAndRow($col, $row)->applyFromArray($rowHdrStyleArray);
          $col++;
        }
        $dc++;
        $row++;
        $col = 0;
      }
      foreach ($d as $key => $value) {
        if (strpos($key, "Code") > -1 || strpos($key, "No") > -1) {
          $ws->setCellValueExplicitByColumnAndRow($col, $row, $value, PHPExcel_Cell_DataType::TYPE_STRING);
        } else {
          $ws->setCellValueByColumnAndRow($col, $row, $value);
        }
        $col++;
      }
      $row++;
    }
    $ope->setActiveSheetIndex(0);
    foreach (range('B', 'Z') as $columnID) {
      $ope->getActiveSheet()->getColumnDimension($columnID)
              ->setAutoSize(true);
    }
    if ($format == "xls") {
      header('Content-Type: application/vnd.ms-excel');
    } else {
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
    header('Content-Disposition: attachment;filename="' . $fname . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($ope, $writerType);
    $objWriter->save('php://output');
    exit;
  }

}

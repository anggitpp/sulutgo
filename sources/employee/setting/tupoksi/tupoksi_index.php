<?php
$fExport = "files/struktur/";
/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  case "add":
  if (isset($menuAccess[$s]["add"]))
      include 'tupoksi_edit.php';
  else
      include 'tupoksi_view.php';
  break;
  case "edit":
  if (isset($menuAccess[$s]["edit"]))
      include 'tupoksi_edit.php';
  else 
      include 'tupoksi_view.php';
  break;
  case "del":
  $empc = new EmpTup();
  $empc->id = $_GET["id"];
  $empc->desparent();
  include 'tupoksi_view.php';
  break;
  default :
  include 'tupoksi_view.php';
  break;
}
function xls(){     
    global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID;
    require_once 'plugins/PHPExcel.php';

    $kodeInduk = isset($_SESSION["pg_org"]) ? $_SESSION["pg_org"] : isset($_POST["kodeInduk"]) ? $_POST["kodeInduk"] : "";
    // echo $par[idKonseling];
    // die();

    
    // if(empty($par[tgl1])) $par[tgl1] = date('d/m/Y',strtotime('-30 days'));
    // if(empty($par[tgl2])) $par[tgl2] = date('d/m/Y');

    $sekarang = date('Y-m-d');
    
    $objPHPExcel = new PHPExcel();              
    $objPHPExcel->getProperties()->setCreator($cName)
    ->setLastModifiedBy($cName)
    ->setTitle($arrTitle["".$_GET[p].""]);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);


    $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');        
    $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');        
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "DATA TUPOKSI");
    
    $objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getFont()->setBold(true);    
    $objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    
    $objPHPExcel->getActiveSheet()->setCellValue('A4', "KANTOR - DIVISI");
    $objPHPExcel->getActiveSheet()->setCellValue('B4', "DEPARTEMEN - SUB DEPARTEMEN");
    $objPHPExcel->getActiveSheet()->setCellValue('C4', "STATUS");
    
    
    $rows=5;        


    $sql = "
    SELECT * 
    FROM mst_data
    WHERE kodeCategory = 'X04'
    
    ";

    $res = db($sql);
    $no=0;
    while ($r = mysql_fetch_assoc($res)) {
        // $no++;
        $r[tanggalKonseling] = getTanggal($r[tanggalKonseling]);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $r[namaData]);

        $sql_ = "
        SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData 
        FROM mst_data
        WHERE kodeCategory = 'X05' and kodeInduk = '$r[kodeData]'
        ORDER BY kodeData 
        ";

        $res_ = db($sql_);
        $no_anakan=0;
        while ($r_ = mysql_fetch_assoc($res_)) {
            $r_[statusData] = $r_[statusData] > 0  ? "Active" : "Not Active";
           $no_anakan++;
           $objPHPExcel->getActiveSheet()->setCellValue('A'.($rows+$no_anakan), "    ".$no_anakan.". ".$r_[namaData]);
           $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan), $r_[statusData]);
                    $sql__ = "SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData FROM mst_data
                    WHERE kodeInduk = '$r_[kodeData]'
                    ORDER BY kodeData";
                    $res__ = db($sql__);
                    $no__anakan=0;
                    $urut_huruf=0;
                    // $no__anakan=0;
                                while ($r__ = mysql_fetch_assoc($res__)) {
                                 $r__[statusData] = $r__[statusData] > 0  ? "Active" : "Not Active";
                                    $no__anakan++;
                                    $urut_huruf++;
                                    $objPHPExcel->getActiveSheet()->setCellValue('B'.($rows+$no_anakan+$no__anakan), numToAlpha($urut_huruf).". ".$r__[namaData]);
                                    $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan+$no__anakan), $r__[statusData]);




                                        $sql___ = "SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData FROM mst_data where kodeInduk = '$r__[kodeData]' order by kodeData";
                                        $res___ = db($sql___);
                                        $no___anakan = 0;
                                        while($r___ = mysql_fetch_assoc($res___)){
                                           $r___[statusData] = $r___[statusData] > 0  ? "Active" : "Not Active";
                                            $no__anakan++;
                                             $no___anakan++;
                                            $objPHPExcel->getActiveSheet()->setCellValue('B'.($rows+$no_anakan+$no__anakan), "   ".strtolower(numToAlpha($no___anakan)).". ".$r___[namaData]);
                                            $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan+$no__anakan), $r___[statusData]);
                                        }



                                 }
       }
       // $rows = $rows + $no___anakan;
       $rows = $rows + $no__anakan;
       $rows = $rows + $no_anakan;
       
       
       $rows++;
   }

   $rows--;
   $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


   $objPHPExcel->getActiveSheet()->getStyle('A1:C'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

   $objPHPExcel->getActiveSheet()->getStyle('A4:C'.$rows)->getAlignment()->setWrapText(true);                      

   $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

   $objPHPExcel->getActiveSheet()->setTitle("TUPOKSI");
   $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file

   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
   $objWriter->save($fExport."TUPOKSI.".time().".xls");
}
?>


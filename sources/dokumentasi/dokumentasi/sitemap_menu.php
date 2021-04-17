<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fExport = "files/export/";

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$fFile, $cGroup;
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
										<div id=\"pos_l\" style=\"float:left;\">
			<p>
        ".comboData("select * from mst_data where kodeCategory='BE' order by namaData","kodeData","namaData","par[kategoriModul]","All Kategori",$par[kategoriModul],"onchange=\"getSub('".getPar($par,"mode,kategoriModul")."');\"", "190px","chosen-select")."

        ".comboData("select * from app_modul where  kategoriModul='$par[kategoriModul]' and statusLink !='p' and namaModul !='Setting' order by urutanModul","kodeModul","namaModul","par[wew]","All Modul",$par[wew],"","190px;","chosen-select")."
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>		
			<div id=\"pos_r\">
			 <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>";
		$text.="</div>

			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
					<th width=\"50\">Status</th>
					</tr>
			</thead>
			<tbody>";
		
		$filter ="where kodeModul is not null and namaModul !='Setting' ";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(namaModul) like '%".strtolower($par[filter])."%'				
		)";
		if(!empty($par[kategoriModul]))
			$filter.= " AND kategoriModul = '$par[kategoriModul]'";
		  
    if(!empty($par[wew]))
          $filter.= " AND kodeModul = '$par[wew]'";
	
	
		$sql="select * from app_modul $filter order by urutanModul";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$r[statusModul] = $r[statusModul] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";	
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[namaModul])."</td>
					<td align=\"center\">$r[statusModul]</td>
					
			</tr>";
			$sql_ = "select * from app_site where kodeModul = '$r[kodeModul]' and namaSite != 'Setting' order by urutanSite";
			$res_=db($sql_);
			while($r_=mysql_fetch_array($res_)){	
				$no++;
				$r_[statusSite] = $r_[statusSite] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";	
				$text.="
				<tr>
					<td>$no.</td>
					<td style=\"padding-left:40px;\">$r_[namaSite]</td>
					<td align=\"center\">$r_[statusSite]</td>
					
				</tr>";
				$sql__ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '0' order by urutanMenu";
				$res__=db($sql__);
				while($r__=mysql_fetch_array($res__)){	
					$no++;
					$r__[statusMenu] = $r__[statusMenu] == "t"?
					"<img src=\"styles/images/t.png\" title='Active'>":
					"<img src=\"styles/images/f.png\" title='Not Active'>";	
					$text.="
					<tr>
					<td>$no.</td>
					<td style=\"padding-left:60px;\">$r__[namaMenu]</td>
					<td align=\"center\">$r__[statusMenu]</td>
					
					</tr>";
					$sql___ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r__[kodeMenu]' order by urutanMenu";
					$res___=db($sql___);
					while($r___=mysql_fetch_array($res___)){	
						$no++;
						$r___[statusMenu] = $r___[statusMenu] == "t"?
						"<img src=\"styles/images/t.png\" title='Active'>":
						"<img src=\"styles/images/f.png\" title='Not Active'>";	
						$text.="
						<tr>
						<td>$no.</td>
						<td style=\"padding-left:80px;\">$r___[namaMenu]</td>
						<td align=\"center\">$r___[statusMenu]</td>

						</tr>";
					}
				}
			}
		}	
		
		$text.="</tbody>
			</table>
			</div>";
			if($par[mode] == "xls"){     
    xls();      
    $text.= "<iframe src=\"download.php?d=exp&f=SITEMAP.".time().".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
  } 
		return $text;
	}

	function xls(){     
    global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID;
    require_once 'plugins/PHPExcel.php';

    $sekarang = date('Y-m-d');
    
    $objPHPExcel = new PHPExcel();              
    $objPHPExcel->getProperties()->setCreator($cName)
    ->setLastModifiedBy($cName)
    ->setTitle($arrTitle["".$_GET[p].""]);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);


    $objPHPExcel->getActiveSheet()->mergeCells('B1:F1');        
    $objPHPExcel->getActiveSheet()->mergeCells('B2:F2');        
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B1:B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(15);
    $objPHPExcel->getActiveSheet()->setCellValue('B1', "DATA SITEMAP");
    
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getFont()->setBold(true);    
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    
    $objPHPExcel->getActiveSheet()->setCellValue('B4', "NO.");
    $objPHPExcel->getActiveSheet()->setCellValue('C4', "MODUL");
    $objPHPExcel->getActiveSheet()->setCellValue('D4', "SUB MODUL");
    $objPHPExcel->getActiveSheet()->setCellValue('E4', "MENU");
    $objPHPExcel->getActiveSheet()->setCellValue('F4', "SUB MENU");
    
    $objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('CCCCFFFF');

    $rows=5;        

    $filter = "where namaModul !='Setting'";


    if(!empty($par[kategoriModul])){
    	$filter.= "and kategoriModul = '$par[kategoriModul]'";
    }

    if(!empty($par[wew]))
      $filter.= " AND kodeModul = '$par[wew]'";

    $sql = "
    SELECT * 
    FROM app_modul $filter order by urutanModul
     
    ";


    $res = db($sql);
    $no=0;
    while ($r = mysql_fetch_assoc($res)) {
      $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
     $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

        $no++;
        $r[tanggalKonseling] = getTanggal($r[tanggalKonseling]);

        $objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $no.".");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[namaModul]));

        $sql_ = "select * from app_site where kodeModul = '$r[kodeModul]' AND namaSite != 'Setting' order by urutanSite";
        $res_ = db($sql_);
        while ($r_ = mysql_fetch_array($res_)) {
          $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

        	$no++;
        	$rows++;
        	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        	$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $no.".");
        	$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($r_[namaSite]));
        	 $sql__ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' order by urutanMenu";
       		 $res__ = db($sql__);
       		 while ($r__ = mysql_fetch_array($res__)) {
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

       		 	$no++;
       		 	$rows++;
       		 	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
       		 	$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $no.".");
        		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r__[namaMenu]);
        		$sql___ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r__[kodeMenu]' order by urutanMenu";
        		$res___=db($sql___);
        		while($r___=mysql_fetch_array($res___)){
              $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
              $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

        			$no++;
        			$rows++;
        			$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $no.".");
        			
        			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r___[namaMenu]);
        		}
       		 }
        }

        // $sql_ = "
        // SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData 
        // FROM mst_data
        // WHERE kodeCategory = 'X05' and kodeInduk = '$r[kodeData]'
        // ORDER BY kodeData 
        // ";

       //  $res_ = db($sql_);
       //  $no_anakan=0;
       //  while ($r_ = mysql_fetch_assoc($res_)) {
       //      $r_[statusData] = $r_[statusData] > 0  ? "Active" : "Not Active";
       //     $no_anakan++;
       //     $objPHPExcel->getActiveSheet()->setCellValue('A'.($rows+$no_anakan), "    ".$no_anakan.". ".$r_[namaData]);
       //     $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan), $r_[statusData]);
       //              $sql__ = "SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData FROM mst_data
       //              WHERE kodeInduk = '$r_[kodeData]'
       //              ORDER BY kodeData";
       //              $res__ = db($sql__);
       //              $no__anakan=0;
       //              $urut_huruf=0;
       //              // $no__anakan=0;
       //                          while ($r__ = mysql_fetch_assoc($res__)) {
       //                           $r__[statusData] = $r__[statusData] > 0  ? "Active" : "Not Active";
       //                              $no__anakan++;
       //                              $urut_huruf++;
       //                              $objPHPExcel->getActiveSheet()->setCellValue('B'.($rows+$no_anakan+$no__anakan), numToAlpha($urut_huruf).". ".$r__[namaData]);
       //                              $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan+$no__anakan), $r__[statusData]);




       //                                  $sql___ = "SELECT *,coalesce((select count(id) from emp_tupoksi where parent_id=kodeData),0) statusData FROM mst_data where kodeInduk = '$r__[kodeData]' order by kodeData";
       //                                  $res___ = db($sql___);
       //                                  $no___anakan = 0;
       //                                  while($r___ = mysql_fetch_assoc($res___)){
       //                                     $r___[statusData] = $r___[statusData] > 0  ? "Active" : "Not Active";
       //                                      $no__anakan++;
       //                                       $no___anakan++;
       //                                      $objPHPExcel->getActiveSheet()->setCellValue('B'.($rows+$no_anakan+$no__anakan), "   ".strtolower(numToAlpha($no___anakan)).". ".$r___[namaData]);
       //                                      $objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan+$no__anakan), $r___[statusData]);
       //                                  }



       //                           }
       // }
       // // $rows = $rows + $no___anakan;
       // $rows = $rows + $no__anakan;
       // $rows = $rows + $no_anakan;
       
       
       $rows++;
   }

   $rows--;
   $objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   


   $objPHPExcel->getActiveSheet()->getStyle('B1:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

   $objPHPExcel->getActiveSheet()->getStyle('B4:F'.$rows)->getAlignment()->setWrapText(true);                      

   $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

   $objPHPExcel->getActiveSheet()->setTitle("SITEMAP");
   $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file

   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
   $objWriter->save($fExport."SITEMAP.".time().".xls");
}

function submodul(){
  global $db,$s,$id,$inp,$par,$arrParameter;        
  $data = arrayQuery("select concat(kodeModul, '\t', namaModul) from app_modul where kategoriModul='$par[kategoriModul]' and namaModul !='Setting' and statusLink ='s' order by namaModul");  

  return implode("\n", $data);
}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){
      case "submod":
      $text = submodul();
      break;

			case "chk":
				$text = chk();
			break;
			case "delIco":
				if(isset($menuAccess[$s]["edit"])) $text = hapusIcon(); else $text = lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
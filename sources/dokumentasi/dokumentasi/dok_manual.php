<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fManual = "files/FileMenu/";

function uploadManual($kodeMenu) {
  global $s, $inp, $par, $fManual;
  $fileUpload = $_FILES["fileMenu"]["tmp_name"];
  $fileUpload_name = $_FILES["fileMenu"]["name"];
  if (($fileUpload != "") and ( $fileUpload != "none")) {
    fileUpload($fileUpload, $fileUpload_name, $fManual);
    $foto_file = "Manual-" . time() . "." . getExtension($fileUpload_name);
    fileRename($fManual, $fileUpload_name, $foto_file);
  }
  if (empty($foto_file))
    $foto_file = getField("select fileMenu from app_menu where kodeMenu ='$kodeMenu'");

  return $foto_file;
}



function submodul(){
  global $db,$s,$id,$inp,$par,$arrParameter;        

  $data = arrayQuery("select concat(kodeModul, '\t', namaModul) from app_modul where kategoriModul='$par[kategoriModul]' and namaModul !='Setting' order by namaModul");  
  
  return implode("\n", $data);


}

function hapusManual() {
  global $s, $inp, $par, $fManual, $cUsername;

  $foto_file = getField("select fileMenu from app_menu where kodeMenu='$par[kodeMenu]'");
  if (file_exists($fManual . $foto_file) and $foto_file != "")
    unlink($fManual . $foto_file);

  $sql = "update app_menu set fileMenu='' where kodeMenu='$par[kodeMenu]'";
  db($sql);

  echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}


function ubah(){
  global $s, $inp, $par, $cUsername, $arrParam;
  $inp[fileMenu] = uploadManual($par[kodeMenu]);
  $sql = "update app_menu set fileMenu = '$inp[fileMenu]', ketMenu = '$inp[ketMenu]' where kodeMenu = '$par[kodeMenu]'";
  db($sql);
  // echo $sql;
  // die();
  echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}

function lihat(){

  global $s,$inp,$par,$arrTitle,$menuAccess,$fManual,$arrColor,$cVac,$cyear,$m,$arrParam;

  $modul = getField("select kodeModul from app_modul order by urutanModul asc limit 1");
  $par[modul] = empty($par[modul]) ? $modul : $par[modul];
  $par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";
  $cols=6;  
  if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
    $cols=7;  
  }


  $text = table($cols, array(($cols-3),($cols-2),($cols-1),$cols));

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
    <div id=\"pos_r\" style=\"float:right;\">
      <a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
    </div>



  </form>

  <br clear=\"all\" />

  <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"\">

    <thead>

      <tr>
        <th width=\"20\">No.</th>
        <th width=\"*\">Sub Modul</th>  
        <th width=\"50\">D/L</th>
        <th width=\"50\">View</th>
        <th width=\"80\">SIZE</th>
        
        
        ";if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\">Kontrol</th>";
        $text.="


      </thead>

      <tbody>";


        $filter ="where namaModul !='Setting'";
        
        if(!empty($par[kategoriModul]))
          $filter.= " AND kategoriModul = '$par[kategoriModul]'";

        if(!empty($par[wew]))
          $filter.= " AND kodeModul = '$par[wew]'";

        $sql="select * from app_modul $filter order by urutanModul";
        $res=db($sql);
        while($r=mysql_fetch_array($res)){    



          $r[download] = "<a href=\"download.php?d=fileMenu&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";

          $no++;
          $r[statusModul] = $r[statusModul] == "t"?
          "<img src=\"styles/images/t.png\" title='Active'>":
          "<img src=\"styles/images/f.png\" title='Not Active'>"; 
          $text.="<tr>
          <td style=\"background-color:#e9e9e9\">$no.</td>
          <td style=\"background-color:#e9e9e9\" colspan=\"5\">".strtoupper($r[namaModul])."</td>
          
        </tr>";
        $sql_ = "select * from app_site where kodeModul = '$r[kodeModul]' and namaSite != 'Setting' order by urutanSite";
        $res_=db($sql_);
        $xno=0;
        while($r_=mysql_fetch_array($res_)){  
          $xno++;
          $r[download] = "<a href=\"download.php?d=fileMenu&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
          $r_[statusSite] = $r_[statusSite] == "t"?
          "<img src=\"styles/images/t.png\" title='Active'>":
          "<img src=\"styles/images/f.png\" title='Not Active'>"; 
          $text.="
          <tr>
            <td></td>
            <td colspan=\"5\" style=\"padding-left:40px;\">$xno. $r_[namaSite]</td>
            

          </tr>";
          $sql__ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '0' order by urutanMenu";
          $res__=db($sql__);
          while($r__=mysql_fetch_array($res__)){  
            $r[download] = "<a href=\"download.php?d=fileMenu&f=$r__[kodeMenu]\"><img src=\"".getIcon($r__[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
            if(empty($r__[fileMenu])){
              $r__[download] = " - ";
              $r__[view] = " - ";
              $r__[size] = " - ";
            }else{
              $r__[download] = "<a href=\"download.php?d=fileMenu&f=$r__[kodeMenu]\"><img src=\"".getIcon($r__[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
              $r__[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileMenu&id=$r__[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
              $r__[size] = getSizeFile($fManual.$r__[fileMenu]);
            }
            $r__[statusMenu] = $r__[statusMenu] == "t"?
            "<img src=\"styles/images/t.png\" title='Active'>":
            "<img src=\"styles/images/f.png\" title='Not Active'>"; 
            $text.="
            <tr>
              <td></td>
              <td style=\"padding-left:60px;\">$r__[namaMenu]</td>
              <td align=\"center\">$r__[download]</td>
              <td align=\"center\">$r__[view]</td>
              <td align=\"center\">$r__[size]</td>
              ";
              if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
                $text.="<td align=\"center\">";                 
                if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r__[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,370);\"><span>Edit</span></a>";
                if(isset($menuAccess[$s]["delete"])) 
                  $text.="<a href=\"?par[mode]=del&par[kodeMenu]=$r__[kodeMenu]".getPar($par,"mode,kodeMenu")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                // $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeModul]','".getPar($par,"mode,kodeModul")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                $text.="</td>";
              }

              $text.="</tr>";
              $sql___ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r__[kodeMenu]' order by urutanMenu";
              $res___=db($sql___);
              while($r___=mysql_fetch_array($res___)){

                if(empty($r___[fileMenu])){
                  $r___[download] = " - ";
                  $r___[view] = " - ";
                  $r___[size] = " - ";
                }else{
                  $r___[download] = "<a href=\"download.php?d=fileMenu&f=$r___[kodeMenu]\"><img src=\"".getIcon($r___[fileMenu])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
                  $r___[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileMenu&id=$r___[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
                  $r___[size] = getSizeFile($fManual.$r___[fileMenu]);
                }
                $r___[statusMenu] = $r___[statusMenu] == "t"?
                "<img src=\"styles/images/t.png\" title='Active'>":
                "<img src=\"styles/images/f.png\" title='Not Active'>"; 
                $text.="
                <tr>
                  <td></td>
                  <td style=\"padding-left:80px;\">$r___[namaMenu]</td>
                  <td align=\"center\">$r___[download]</td>
                  <td align=\"center\">$r___[view]</td>
                  <td align=\"center\">$r___[size]</td>
                  ";
                  if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
                    $text.="<td align=\"center\">";                 
                    if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeMenu]=$r___[kodeMenu]".getPar($par,"mode,kodeMenu")."',825,370);\"><span>Edit</span></a>";
                    if(isset($menuAccess[$s]["delete"])) 
                      $text.="<a href=\"?par[mode]=del&par[kodeMenu]=$r___[kodeMenu]".getPar($par,"mode,kodeMenu")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                // $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeModul]','".getPar($par,"mode,kodeModul")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                    $text.="</td>";
                  }

                  $text.="</tr>";
                }
              }
            }
          } 

          $text.="</tbody>
        </table>

      </div>";
      $sekarang = date('Y-m-d');
      if($par[mode] == "xls"){
        xls();      
        $text.="<iframe src=\"download.php?d=exp&f=MANUAL BOOK.".time().".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);


    $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');        
    $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');        
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "MANUAL BOOK");
    
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);    
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    
    $objPHPExcel->getActiveSheet()->setCellValue('A4', "NO.");
    $objPHPExcel->getActiveSheet()->setCellValue('B4', "MODUL");
    $objPHPExcel->getActiveSheet()->setCellValue('C4', "SUB MODUL");
    $objPHPExcel->getActiveSheet()->setCellValue('D4', "MENU");
    $objPHPExcel->getActiveSheet()->setCellValue('E4', "SUB MENU");
    
    
    $rows=5;        

    $filter = "where namaModul !='Setting' and statusLink ='s'";




    $sql = "
    SELECT * 
    FROM app_modul $filter order by urutanModul
     
    ";


    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $no++;
        $r[tanggalKonseling] = getTanggal($r[tanggalKonseling]);

        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        // $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no.".");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[namaModul]));

        $sql_ = "select * from app_site where kodeModul = '$r[kodeModul]' AND namaSite != 'Setting' order by urutanSite";
        $res_ = db($sql_);
                $xno=0;

        while ($r_ = mysql_fetch_array($res_)) {
          $xno++;
          $rows++;
          $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
          $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows);
          $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "    ".$xno.".".$r_[namaSite]);
           $sql__ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '0' order by urutanMenu";
           $res__ = db($sql__);
           while ($r__ = mysql_fetch_array($res__)) {
            $rows++;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows,"          ". $r__[namaMenu]);
            $sql___ = "select * from app_menu where kodeModul = '$r[kodeModul]' AND kodeSite = '$r_[kodeSite]' AND kodeInduk = '$r__[kodeMenu]' order by urutanMenu";
            $res___=db($sql___);
            while($r___=mysql_fetch_array($res___)){
              $rows++;
              $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
              $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows);
              
              $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "               ".$r___[namaMenu]);
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
   $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   $objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


   $objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

   $objPHPExcel->getActiveSheet()->getStyle('A4:E'.$rows)->getAlignment()->setWrapText(true);                      

   $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
   $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
   $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

   $objPHPExcel->getActiveSheet()->setTitle("MANUAL BOOK");
   $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file

   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
   $objWriter->save($fExport."MANUAL BOOK.".time().".xls");
}



  function form(){
    global $s,$inp,$par,$menuAccess,$fManual,$cUsername,$arrTitle;  

  // file_get_contents
  // echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
    $sql="SELECT * FROM app_menu t1 join app_site t2 on t1.kodeSite = t2.kodeSite join app_modul t3 on t1.kodeModul = t3.kodeModul WHERE t1.kodeMenu='$par[kodeMenu]'";
  // echo $sql;
    $res=db($sql);
    $r=mysql_fetch_array($res); 

  // $r[appr_div_by] = empty($r[appr_div_by]) ? $cUsername : $r[appr_div_by];

    $text.="<div class=\"centercontent contentpopup\">
    <div class=\"pageheader\">
      <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
      ".getBread(ucwords($par[mode]." data"))."
    </div>
    <div id=\"contentwrapper\" class=\"contentwrapper\">
      <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">   
        <p style=\"position:absolute;right:5px;top:5px;\">
          <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
          <input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
        </p>
        <div id=\"general\" class=\"subcontent\">


          <p>
            <label class=\"l-input-small\">Modul</label>
            <span class=\"field\">$r[namaModul]&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small\">Sub Modul</label>
            <span class=\"field\">$r[namaSite]&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small\">Menu</label>
            <span class=\"field\">$r[namaMenu]&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small\">File</label>
            <div class=\"field\">";
              $text.=empty($r[fileMenu])?
              "<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
              <div class=\"fakeupload\" style=\"width:360px;\">
                <input type=\"file\"  id=\"fileMenu\" name=\"fileMenu\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
              </div>":
              "<img src=\"".getIcon($r[fileMenu])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
              <a href=\"?par[mode]=delManual".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
              <br clear=\"all\">";
              $text.="
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Keterangan</label>
            <span class=\"fieldB\">
              <textarea style=\"width:350px;height:50px;\" id=\"inp[ketMenu]\" name=\"inp[ketMenu]\">$r[ketMenu]</textarea>
            </span>
          </p>
        </div>



      </form> 
    </div>";
    return $text;
  }


  function getContent($par){
    global $s,$_submit,$menuAccess;
    switch($par[mode]){


    // case "lst":

    // $text=lData();

    // break; 
      case "submod":

      $text = submodul();

      break;
      case "delManual":
      $text = hapusManual();
      break;

      case "edit":
      if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
      break;

      default:
      $text = lihat();
      break;
    }
    return $text;
  } 
  ?>
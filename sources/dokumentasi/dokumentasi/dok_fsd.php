<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fFsd = "files/dokumentasi/fsd/";

function uploadFsd($kodeMenu) {
	global $s, $inp, $par, $fFsd;
	$fileUpload = $_FILES["fileFsd"]["tmp_name"];
	$fileUpload_name = $_FILES["fileFsd"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFsd);
		$foto_file = "FSD-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fFsd, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select fileFsd from app_menu where kodeMenu ='$kodeMenu'");

	return $foto_file;
}

function hapusFsd() {
	global $s, $inp, $par, $fFsd, $cUsername;

	$foto_file = getField("select fileFsd from app_menu where kodeMenu='$par[kodeMenu]'");
	if (file_exists($fFsd . $foto_file) and $foto_file != "")
		unlink($fFsd . $foto_file);

	$sql = "update app_menu set fileFsd='' where kodeMenu='$par[kodeMenu]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}


function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$inp[fileFsd] = uploadFsd($par[kodeMenu]);
	$sql = "update app_menu set fileFsd = '$inp[fileFsd]', ketFsd = '$inp[ketFsd]' where kodeMenu = '$par[kodeMenu]'";
	db($sql);
	// echo $sql;
	// die();
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;

	$modul = getField("select kodeModul from app_modul order by urutanModul asc limit 1");
	$par[modul] = empty($par[modul]) ? $modul : $par[modul];
	$par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";
	$cols=7;	
	if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
		$cols=8;	
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

				<input type=\"text\" id=\"par[cari]\" name=\"par[cari]\" value=\"".$par[cari]."\" style=\"width:200px;\" placeholder=\"Search\"/>

				<!--".comboData("select * from app_modul where namaModul != 'Setting'  order by urutanModul","kodeModul","namaModul","par[modul]","",$par[modul],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."-->
				".comboData("select * from mst_data where kodeCategory='BE' order by namaData","kodeData","namaData","par[kategoriModul]","All Kategori",$par[kategoriModul],"onchange=\"getSub('".getPar($par,"mode,kategoriModul")."');\"", "190px","chosen-select")."

				".comboData("select * from app_modul where  kategoriModul='$par[kategoriModul]' and namaModul !='Setting' order by urutanModul","kodeModul","namaModul","par[wew]","All Modul",$par[wew],"","190px;","chosen-select")."

				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/>
			</p>

		</div>	
	</form>
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

      	if (!empty($par['cari'])){
			$filter .= " and (     
			lower(namaModul) like '%".mysql_real_escape_string(strtolower($par['cari']))."%'
			)";
		}

        $sql="select * from app_modul $filter order by urutanModul";
        $res=db($sql);
        while($r=mysql_fetch_array($res)){    



          $r[download] = "<a href=\"download.php?d=fileFsd&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileFsd])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";

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
          $r[download] = "<a href=\"download.php?d=fileFsd&f=$r[kodeMenu]\"><img src=\"".getIcon($r[fileFsd])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
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
            $r[download] = "<a href=\"download.php?d=fileFsd&f=$r__[kodeMenu]\"><img src=\"".getIcon($r__[fileFsd])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
            if(empty($r__[fileFsd])){
              $r__[download] = " - ";
              $r__[view] = " - ";
              $r__[size] = " - ";
            }else{
              $r__[download] = "<a href=\"download.php?d=fileFsd&f=$r__[kodeMenu]\"><img src=\"".getIcon($r__[fileFsd])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
              $r__[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileFsd&par[kodeMenu]=$r__[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
              $r__[size] = getSizeFile($fManual.$r__[fileFsd]);
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

                if(empty($r___[fileFsd])){
                  $r___[download] = " - ";
                  $r___[view] = " - ";
                  $r___[size] = " - ";
                }else{
                  $r___[download] = "<a href=\"download.php?d=fileFsd&f=$r___[kodeMenu]\"><img src=\"".getIcon($r___[fileFsd])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
                  $r___[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileFsd&par[kodeMenu]=$r___[kodeMenu]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
                  $r___[size] = getSizeFile($fManual.$r___[fileFsd]);
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

			$text.="
		</tbody>
	</table>

</div>";
$sekarang = date('Y-m-d');
if($par[mode] == "xls"){
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=DATA FSD ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

return $text;

}


function xls(){		
	global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID,$areaCheck;
	require_once 'plugins/PHPExcel.php';
	$sekarang = date('Y-m-d');
	
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cName)
	->setLastModifiedBy($cName)
	->setTitle($arrTitle["".$_GET[p].""]);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP FSD");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);

	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "MENU");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "SUB MODUL");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "UPDATE");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "PIC");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "DOC");
	
	$rows=5;

	$sql = " SELECT * FROM app_menu t1 join app_site t2 on t1.kodeSite = t2.kodeSite where t2.namaSite != 'Setting' order by t2.namasite,t1.namaMenu";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
		$status = $r[status] == "t" ? "Aktif" : "Tidak Aktif";
		$r[gender] = $r[gender] == "p" ? "Wanita" : "Pria";
		$komunitas = getField("SELECT GROUP_CONCAT(komunitas) FROM komunitas_relasi t1 JOIN komunitas_data t2 ON t1.`idKomunitas` = t2.`id` WHERE t1.`idAnggota` = '$r[id]' ");







		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$r[status] = empty($r[fileFsd]) ? "Belum" : "Sudah";
		if(!empty($r[fileFsd])){
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '016f00')
						)
					)
				);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaMenu]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[namaSite]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[updateTime]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[updateBy]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[status]);

		
		
		$rows++;
	}
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F'.$rows)->getAlignment()->setWrapText(true);						
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA FSD");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."DATA FSD ".$sekarang.".xls");
}	




function form(){
	global $s,$inp,$par,$menuAccess,$fFsd,$cUsername,$arrTitle;	

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
						$text.=empty($r[fileFsd])?
						"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"fileFsd\" name=\"fileFsd\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[fileFsd])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delFsd".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[ketFsd]\" name=\"inp[ketFsd]\">$r[ketFsd]</textarea>
					</span>
				</p>
			</div>


			
		</form>	
	</div>";
	return $text;
}

function submodul(){
	global $db,$s,$id,$inp,$par,$arrParameter;        
	$data = arrayQuery("select concat(kodeModul, '\t', namaModul) from app_modul where kategoriModul='$par[kategoriModul]' and namaModul !='Setting' order by namaModul");  

	return implode("\n", $data);
}


function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){

		case "submod":
		$text = submodul();
		break;

		case "lst":

		$text=lData();

		break;	

		case "delFsd":
		$text = hapusFsd();
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
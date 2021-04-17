<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fChecklist = "files/dokumentasi/menu_checklist/";

function uploadFile($id) {
	global $s, $inp, $par, $fChecklist;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fChecklist);
		$foto_file = "dok_ceklist-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fChecklist, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_menuchecklist where id ='$id'");

	return $foto_file;
}

function hapusFile() {
	global $s, $inp, $par, $fChecklist, $cUsername;

	$foto_file = getField("select file from doc_menuchecklist where id='$par[id]'");
	if (file_exists($fChecklist . $foto_file) and $foto_file != "")
		unlink($fChecklist . $foto_file);

	$sql = "update doc_menuchecklist set file='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}


function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	if(!empty($par[id])){
		$inp[file] = uploadFile($par[id]);
		$sql = "update doc_menuchecklist set file = '$inp[file]', keterangan = '$inp[keterangan]', pic = '$inp[pic]', updateDate = '".date('Y-m-d H:i:s')."', updateBy = '".$cUsername."' where id = '$par[id]'";
		db($sql);
	// echo $sql;
	}else{
		$id = getField("select id from doc_menuchecklist order by id desc limit 1")+1;
		$inp[file] = uploadFile($id);
		$sql = "insert into doc_menuchecklist (id, kodeModul, file, keterangan, pic, createDate, createBy) values ('$id','$par[kodeModul]', '$inp[file]','$inp[keterangan]','$inp[pic]','".date('Y-m-d H:i:s')."','$cUsername')";
		db($sql);
	// echo $sql;
	}
	// echo $sql;
	// die();
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}


function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;

	$modul = getField("select kodeModul from app_modul order by urutanModul asc limit 1");
	$par[modul] = empty($par[modul]) ? $modul : $par[modul];
	$par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";
	$cols=8;	
	if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
		$cols=9;	
	}


	$text = table2($cols, array(($cols-4),($cols-3),($cols-2),($cols-1),$cols));

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

				".comboData("select * from mst_data where kodeCategory='BE' order by namaData","kodeData","namaData","par[kategoriModul]","All Kategori",$par[kategoriModul],"onchange=\"getSub('".getPar($par,"mode,kategoriModul")."');\"", "190px","chosen-select")."

				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 

			</p>

		</div>	
	</form>
	<div id=\"pos_r\" style=\"float:right;\">
		<a href=\"#\" id=\"btnExport1\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
	</div>



	

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"\">

		<thead>

			<tr>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"20\">No.</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"*\">Modul</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"100\">Jumlah Menu</th>
				
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"50\">Doc</th>
				<th colspan=\"2\" width=\"100\">Pelaksanaan</th>

				
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"90\">SIZE</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"150\">Update</th>

				
				";if(isset($menuAccess[$s]["edit"])) $text.="<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"50\">Kontrol</th>";
				$text.="
			</tr>
			<tr>
				<th width=\"50\">View</th>

				<th width=\"50\">D/L</th>
			</tr>
		</thead>

		<tbody>";

			$filters= " where t1.statusModul = 't' and t1.namaModul !='Setting' ";

			if (!empty($par['cari']))
				$filters.= " and (				
			lower(namaModul) like '%".mysql_real_escape_string(strtolower($par['cari']))."%'
			)";

			if(!empty($par['kategoriModul']))
				$filters.= " and t1.kategoriModul='".$par[kategoriModul]."'";

			$sql = " SELECT t1.*,t2.id,t2.file FROM app_modul t1 left join doc_menuchecklist t2 on t1.kodeModul = t2.kodeModul $filters order by urutanModul asc";
	
			$sql = db($sql);
			$count_kat1 = getField("select count(kodeModul) from app_modul where kategoriModul = '963'");
			while ($r = mysql_fetch_array($sql)) {
				@$no++;
				if($no == 1){
					if(!empty($r[kategoriModul])){
						$text.="
						<tr>
							<td style=\"background-color:#e9e9e9\"></td>
							<td colspan=\"8\" style=\"background-color:#e9e9e9\">".strtoupper(namaData($r[kategoriModul]))."</td>
						</tr>
						";
					}
				}elseif($no == $count_kat1 + 1){
					if(!empty($r[kategoriModul])){
						$text.="
						<tr>
							<td style=\"background-color:#e9e9e9\"></td>
							<td colspan=\"8\" style=\"background-color:#e9e9e9\">".strtoupper(namaData($r[kategoriModul]))."</td>
						</tr>
						";
					}
				}
				if (isset($menuAccess[$s]["edit"])) {
					$controlKebutuhan = "<a onclick=\"openBox('popup.php?par[mode]=edit&par[kodeModul]=$r[kodeModul]&par[id]=$r[id]". getPar($par, "mode,idp") . "',825,400);\"  href=\"#\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
				}

				$r[jumlahMenu] = getField("select count(kodeMenu) from app_menu where kodeModul = '$r[kodeModul]' and targetMenu NOT LIKE '%setting%' and targetMenu != ''

					");


				$wew = getField("SELECT updateDate from doc_menuchecklist where kodeModul = '$r[kodeModul]'");
				if(empty($r[file])){
					$r[download] = " - ";
					$r[view] = " - ";
					$r[size] = " - ";
				}else{
					$r[download] = "<a href=\"download.php?d=fileChecklist&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
					$r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileChecklist&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					$r[size] = getSizeFile($fChecklist.$r[file]);
				}

				$text.="
				<tr>
					<td align=\"center\">$no</td>
					<td>$r[namaModul]</td>
					<td align=\"center\">$r[jumlahMenu]</td>
					<td><a href=\"#\" onclick=\"window.location.href='?par[mode]=xls2".getPar($par,"mode","kodeModul")."&par[kodeModul]=$r[kodeModul]'\"><img src=\"".getIcon("hehe.xls")."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a></td>
					<td align=\"center\">$r[view]</td>
					<td align=\"center\">$r[download]</td>
					<td align=\"center\">$r[size]</td>
					<td align=\"center\">$wew</td>
					<td align=\"center\">$controlKebutuhan</td>
				</tr>
				";
			}
			$text.="
		</tbody>
	</table>

</div>
<script>
	jQuery(\"#btnExport1\").live('click\', function(e){
		e.preventDefault();
		alert('HEHE');
	});
	jQuery(\"#btnExport\").live('click', function(e){
		e.preventDefault();
		window.location.href=\"?par[mode]=xls2\"+ \"".getPar($par,"mode","modul")."\"+ \"&par[modul]=\" + $par[modul];
	});

</script>
";

$sekarang = date('Y-m-d');
if($par[mode] == "xls"){
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=DATA CHECKLIST ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

if($par[mode] == "xls2"){
	xls2();			
	$text.="<iframe src=\"download.php?d=exp&f=XLS CHECKLIST ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

return $text;

}



function lData(){

	global $s,$par,$fChecklist,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
	if($_GET[json]==1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;


	$filters= " where t1.statusModul = 't' and t1.namaModul !='Setting' ";

	if (!empty($_GET['fSearch']))
		$filters.= " and (				
	lower(namaModul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
	)";

	if(!empty($_GET['bSearch']))
		$filters.= " and t1.kategoriModul='".$_GET[bSearch]."'";


	$arrOrder = array(	

		"urutanModul",
		"namaModul",
		"namaMenu",
		"edu_id",
		"edu_dept_id",
		"person_needed",
		"person_needed",
		"",




		);


	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql = " SELECT t1.*,t2.id,t2.file FROM app_modul t1 left join doc_menuchecklist t2 on t1.kodeModul = t2.kodeModul $filters order by $orderBy $sLimit ";
		// echo $sql;

	$res=db($sql);



	$json = array(

		"iTotalRecords" => mysql_num_rows($res),

		"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM app_modul t1 left join doc_menuchecklist t2 on t1.kodeModul = t2.kodeModul $filters"),


		"aaData" => array(),

		);







	$no=intval($_GET['iDisplayStart']);

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	while($r=mysql_fetch_array($res)){
		$no++;

		if (isset($menuAccess[$s]["edit"])) {
			$controlKebutuhan = "<a onclick=\"openBox('popup.php?par[mode]=edit&par[kodeModul]=$r[kodeModul]&par[id]=$r[id]". getPar($par, "mode,idp") . "',825,400);\"  href=\"#\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
		}

		$r[jumlahMenu] = getField("select count(kodeMenu) from app_menu where kodeModul = '$r[kodeModul]' and targetMenu NOT LIKE '%setting%' and targetMenu != ''

			");

		
		$wew = getField("SELECT updateDate from doc_menuchecklist where kodeModul = '$r[kodeModul]'");
		if(empty($r[file])){
			$r[download] = " - ";
			$r[view] = " - ";
			$r[size] = " - ";
		}else{
			$r[download] = "<a href=\"download.php?d=fileChecklist&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
			$r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileChecklist&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
			$r[size] = getSizeFile($fChecklist.$r[file]);
		}

		$data=array(

			"<div align=\"center\">".$no.".</div>",				

			"<div align=\"left\">$r[namaModul]</div>",

			"<div align=\"center\">$r[jumlahMenu]</div>",

			"<div align=\"center\"><a href=\"#\" onclick=\"window.location.href='?par[mode]=xls2".getPar($par,"mode","kodeModul")."&par[kodeModul]=$r[kodeModul]'\"><img src=\"".getIcon("hehe.xls")."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a></div>",

			"<div align=\"center\">$r[view]</div>",


			"<div align=\"center\">$r[download]</div>",	


			"<div align=\"center\">$r[size]</div>",	
			"<div align=\"center\">$wew</div>",	

			"<div align=\"center\">$controlKebutuhan</div>",		



			);





		$json['aaData'][]=$data;


	}

	if($par[mode] == "xls"){
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=DATA CHECKLIST ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}



	return json_encode($json);

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
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP CHECKLIST");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "MODUL");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "JML MENU");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "UPLOAD");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "USER");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "STATUS");
	
	$rows=5;

	$sql = " SELECT t1.*,t2.id,t2.file FROM app_modul t1 left join doc_menuchecklist t2 on t1.kodeModul = t2.kodeModul where  t1.namaModul !='Setting' order by urutanModul asc";

	$res=db($sql);
	$arrNama = arrayQuery("select username, nickName from app_user");

	while($r=mysql_fetch_array($res)){			
		$no++;
		$tanggalCreate = getField("SELECT createDate from doc_menuchecklist where kodeModul = '$r[kodeModul]'");

		$r[jumlahMenu] = getField("select count(kodeMenu) from app_menu where kodeModul = '$r[kodeModul]'");

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$r[status] = empty($r[file]) ? "Belum" : "Sudah";
		if(!empty($r[file])){
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '016f00')
						)
					)
				);
		}else{
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'ff0000')
						)
					)
				);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaModul]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[jumlahMenu]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $tanggalCreate);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrNama[$r[createBy]]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[status]);

		
		
		$rows++;
	}
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F'.$rows)->getAlignment()->setWrapText(true);						
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)->setFitToWidth(1)
	->setFitToHeight(1);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA CHECKLIST");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."DATA CHECKLIST ".$sekarang.".xls");
}	


function xls2(){		
	global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID,$areaCheck;
	require_once 'plugins/PHPExcel.php';

	// $par[kodeModul] = '19';
	$sekarang = date('Y-m-d');


	
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cName)
	->setLastModifiedBy($cName)
	->setTitle($arrTitle["".$_GET[p].""]);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(45);

	$objPHPExcel->getActiveSheet()->mergeCells('B1:H1');		
	$objPHPExcel->getActiveSheet()->mergeCells('B2:H2');		
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);

	$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
	// $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('B1', "CHECKLIST MENU");
	$objPHPExcel->getActiveSheet()->setCellValue('B2', "MODUL : ".getField("select namaModul from app_modul where kodeModul ='$par[kodeModul]'"));

	
	$objPHPExcel->getActiveSheet()->getStyle('B4:H4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('D5:G5')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('B4:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B4:H5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B4:H5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:H5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:H5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('C5:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	// $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

	// $objPHPExcel->getActiveSheet()->mergeCells('A4:A5');	
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');	
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');	
	$objPHPExcel->getActiveSheet()->mergeCells('H4:H5');	
	$objPHPExcel->getActiveSheet()->mergeCells('D4:G4');	
	
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "MENU");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "CHECKLIST");
	$objPHPExcel->getActiveSheet()->setCellValue('D5', "VIEW");
	$objPHPExcel->getActiveSheet()->setCellValue('E5', "INPUT");
	$objPHPExcel->getActiveSheet()->setCellValue('F5', "EDIT");
	$objPHPExcel->getActiveSheet()->setCellValue('G5', "HAPUS");
	$objPHPExcel->getActiveSheet()->setCellValue('H4', "KETERANGAN");
	
	$rows=6;

	$sql = " SELECT * from app_site where kodeModul = '$par[kodeModul]' and namaSite != 'Setting' order by urutanSite";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($rows), $r[namaSite]);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($rows), $no);

		$sql_ = "
		SELECT * from app_menu where kodeSite = '$r[kodeSite]' order by kodeMenu asc
		";

		$res_ = db($sql_);
		$no_anakan=0;

		while ($r_ = mysql_fetch_assoc($res_)) {
			$r_[statusData] = $r_[statusData] > 0  ? "Active" : "Not Active";
			$no_anakan++;
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($rows+$no_anakan), "       ".$r_[namaMenu]);
		}      
		$rows = $rows + $no_anakan;



		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		
		$rows++;
	}
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:H'.$rows)->getAlignment()->setWrapText(true);		

	$rows = $rows+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':E'.$rows);	
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':H'.$rows);	
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, 'VENDOR');
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, 'CLIENT');

	$rows++;
	$rowsmax = $rows+6;

	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':B'.$rowsmax)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':B'.$rowsmax)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':E'.$rowsmax)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$rowsmax)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rowsmax)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$rows.':H'.$rowsmax)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':F'.$rowsmax)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':E'.$rowsmax);	
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':H'.$rowsmax);

	



	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA CHECKLIST");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."XLS CHECKLIST ".$sekarang.".xls");
}	




function form(){
	global $s,$inp,$par,$menuAccess,$fChecklist,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM doc_menuchecklist t1 join app_modul t2 where t1.kodeModul = '$par[kodeModul]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	$r[namaModul] = empty($r[namaModul]) ? getField("select namaModul from app_modul where kodeModul = '$par[kodeModul]'") : $r[namaModul];

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
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[file])?
						"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[file])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[keterangan]\" name=\"inp[keterangan]\">$r[keterangan]</textarea>
					</span>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[pic]\" name=\"inp[pic]\"  value=\"$r[pic]\" class=\"mediuminput\" />
					</div>
				</p>
			</div>


			
		</form>	
	</div>";
	return $text;
}


function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){


		case "lst":

		$text=lData();

		break;	

		case "delFile":
		$text = hapusFile();
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
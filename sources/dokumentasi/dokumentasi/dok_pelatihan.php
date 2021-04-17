<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fUat = "files/dokumentasi/pelatihan/";

function uploadUat($id) {
	global $s, $inp, $par, $fUat;
	$fileUpload = $_FILES["fileUat"]["tmp_name"];
	$fileUpload_name = $_FILES["fileUat"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fUat);
		$foto_file = "pelatihan-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fUat, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select fileUat from doc_pelatihan where id ='$id'");

	return $foto_file;
}

function hapusUat() {
	global $s, $inp, $par, $fUat, $cUsername;

	$foto_file = getField("select fileUat from doc_pelatihan where id='$par[id]'");
	if (file_exists($fUat . $foto_file) and $foto_file != "")
		unlink($fUat . $foto_file);

	$sql = "update doc_pelatihan set fileUat='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function uploadBa($id) {
	global $s, $inp, $par, $fUat;
	$fileUpload = $_FILES["fileBa"]["tmp_name"];
	$fileUpload_name = $_FILES["fileBa"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fUat);
		$foto_file = "ba-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fUat, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select fileBa from doc_pelatihan where id ='$id'");

	return $foto_file;
}

function hapusBa() {
	global $s, $inp, $par, $fUat, $cUsername;

	$foto_file = getField("select fileBa from doc_pelatihan where id='$par[id]'");
	if (file_exists($fUat . $foto_file) and $foto_file != "")
		unlink($fUat . $foto_file);

	$sql = "update doc_pelatihan set fileBa='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function uploadPlk($id) {
	global $s, $inp, $par, $fUat;
	$fileUpload = $_FILES["filePlk"]["tmp_name"];
	$fileUpload_name = $_FILES["filePlk"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fUat);
		$foto_file = "plk-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fUat, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select filePlk from doc_pelatihan where id ='$id'");

	return $foto_file;
}

function hapusPlk() {
	global $s, $inp, $par, $fUat, $cUsername;

	$foto_file = getField("select filePlk from doc_pelatihan where id='$par[id]'");
	if (file_exists($fUat . $foto_file) and $foto_file != "")
		unlink($fUat . $foto_file);

	$sql = "update doc_pelatihan set filePlk='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}


function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	if(!empty($par[id])){
		$inp[fileUat] = uploadUat($par[id]);
		$inp[fileBa] = uploadBa($par[id]);
		$inp[filePlk] = uploadPlk($par[id]);
		$inp[tanggalPlk] = setTanggal($inp[tanggalPlk]);
		$sql = "update doc_pelatihan set fileUat = '$inp[fileUat]',ketUat = '$inp[ketUat]',picUat = '$inp[picUat]',fileBa = '$inp[fileBa]',ketBa = '$inp[ketBa]',picBa = '$inp[picBa]',filePlk = '$inp[filePlk]', ketPlk = '$inp[ketPlk]',tanggalPlk = '$inp[tanggalPlk]',status = '$inp[status]', updateDate = '".date('Y-m-d H:i:s')."', updateBy = '".date('Y-m-d H:i:s')."' where id = '$par[id]'";
	// echo $sql;
		db($sql);
	}else{
		$id = getField("select id from doc_pelatihan order by id desc limit 1")+1;
		$inp[fileUat] = uploadUat($id);
		$inp[fileBa] = uploadBa($id);
		$inp[filePlk] = uploadPlk($id);
		$inp[tanggalPlk] = setTanggal($inp[tanggalPlk]);
		$sql = "insert into doc_pelatihan (id, kodeModul, fileUat,ketUat,picUat,fileBa,ketBa,picBa,filePlk,ketPlk,tanggalPlk, status, createDate, createBy) values ('$id','$par[kodeModul]', '$inp[fileUat]','$inp[ketUat]','$inp[picUat]','$inp[fileBa]','$inp[ketBa]','$inp[picBa]','$inp[filePlk]','$inp[ketPlk]','$inp[tanggalPlk]','$inp[status]','".date('Y-m-d H:i:s')."','$cUsername')";
		db($sql);
	// echo $sql;
	}
	// echo $sql;
	// die();
	echo "<script>alert('UPDATE DATA BERHASIL')</script>";
	echo "<script>window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;

	$modul = getField("select kodeModul from app_modul order by urutanModul asc limit 1");
	$par[modul] = empty($par[modul]) ? $modul : $par[modul];
	$par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";
	$cols=6;	
	if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
		$cols=7;	
	}


	$text = table($cols, array(($cols-4),($cols-3),($cols-2),($cols-1),$cols));

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
		<a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
	</div>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"\">

		<thead>

			<tr>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"20\">No.</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"*\">Modul</th>
				<th colspan=\"2\" width=\"100\">PELAKSANAAN</th>
				<th colspan=\"2\" width=\"100\">BA</th>
				
				
				
				";if(isset($menuAccess[$s]["edit"])) $text.="<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"50\">Kontrol</th>";
				$text.="

			</tr>
			<tr>

				<th width=\"50\">VIEW</th>
				<th width=\"50\">DL</th>
				<th width=\"50\">VIEW</th>
				<th width=\"50\">DL</th>
			</tr>

		</thead>

		<tbody>";
			$filters= " where t1.statusModul = 't'";

			if (!empty($par['cari']))
				$filters.= " and (				
			lower(namaModul) like '%".mysql_real_escape_string(strtolower($par['cari']))."%'
			)";

			if (!empty($par['kategoriModul']))
				$filters.= " and kategoriModul = $par[kategoriModul]";

			$sql = " SELECT t1.*,t2.id, t2.fileUat, t2.fileBa, t2.filePlk FROM app_modul t1 left join doc_pelatihan t2 on t1.kodeModul = t2.kodeModul $filters order by urutanModul asc";

			$sql = db($sql);
			
			$count_kat1 = getField("select count(kodeModul) from app_modul where kategoriModul = '963'");

			while ($r = mysql_fetch_array($sql)) {
				@$no++;
				if($no == 1){
					if(!empty($r[kategoriModul])){
						$text.="
						<tr>
							<td style=\"background-color:#e9e9e9\"></td>
							<td colspan=\"7\" style=\"background-color:#e9e9e9\">".strtoupper(namaData($r[kategoriModul]))."</td>
						</tr>
						";
					}
				}elseif($no == $count_kat1 + 1){
					if(!empty($r[kategoriModul])){
						$text.="
						<tr>
							<td style=\"background-color:#e9e9e9\"></td>
							<td colspan=\"7\" style=\"background-color:#e9e9e9\">".strtoupper(namaData($r[kategoriModul]))."</td>
						</tr>
						";
					}
				}

				if (isset($menuAccess[$s]["edit"])) {
					$controlKebutuhan = "<a href=\"?par[mode]=edit&par[kodeModul]=$r[kodeModul]&par[id]=$r[id]". getPar($par, "mode,idp") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
				}

				if(empty($r[fileUat])){
					$r[downloadUat] = " - ";
					$r[viewUat] = " - ";
					$r[sizeUat] = " - ";
				}else{
					$r[downloadUat] = "<a href=\"download.php?d=fileUat&f=$r[id]\"><img src=\"".getIcon($r[fileUat])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a></a>";
					$r[viewUat] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileUat&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					$r[sizeUat] = getSizeFile($fChecklist.$r[file]);
				}

				if(empty($r[fileBa])){
					$r[downloadBa] = " - ";
					$r[viewBa] = " - ";
					$r[sizeBa] = " - ";
				}else{
					$r[downloadBa] = "<a href=\"download.php?d=filePelatiB&f=$r[id]\"><img src=\"".getIcon($r[fileBa])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
					$r[viewBa] = "<a href=\"#\" onclick=\"openBox('view.php?doc=filePelatiB&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					$r[sizeBa] = getSizeFile($fChecklist.$r[file]);
				}

				if(empty($r[filePlk])){
					$r[downloadPlk] = " - ";
					$r[viewPlk] = " - ";
					$r[sizePlk] = " - ";
				}else{
					$r[downloadPlk] = "<a href=\"download.php?d=filePelati&f=$r[id]\"><img src=\"".getIcon($r[filePlk])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
					$r[viewPlk] = "<a href=\"#\" onclick=\"openBox('view.php?doc=filePelati&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
					$r[sizePlk] = getSizeFile($fChecklist.$r[file]);
				}
				$text.="
				<tr>
					<td align=\"center\">$no</td>
					<td>$r[namaModul]</td>
					<td align=\"center\">$r[viewPlk]</td>
					<td align=\"center\">$r[downloadPlk]</td>
					<td align=\"center\">$r[viewBa]</td>
					<td align=\"center\">$r[downloadBa]</td>
					<td align=\"center\">$controlKebutuhan</td>
				</tr>";
	
			}
			$text.="
		</tbody>
	</table>

</div>";
$sekarang = date('Y-m-d');
if($par[mode] == "xls"){
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=DATA PELATIHAN ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

return $text;

}



function lData(){

	global $s,$par,$fUat,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
	if($_GET[json]==1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;


	$filters= " where t1.statusModul = 't'";

	if (!empty($_GET['fSearch']))

		$filters.= " and (				

	lower(namaModul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


	)";




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

	$sql = " SELECT t1.*,t2.id, t2.fileUat, t2.fileBa, t2.filePlk FROM app_modul t1 left join doc_pelatihan t2 on t1.kodeModul = t2.kodeModul $filters order by $orderBy $sLimit ";
		// echo $sql;

	$res=db($sql);



	$json = array(

		"iTotalRecords" => mysql_num_rows($res),

		"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM app_modul t1 left join doc_pelatihan t2 on t1.kodeModul = t2.kodeModul $filters"),


		"aaData" => array(),

		);







	$no=intval($_GET['iDisplayStart']);

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	while($r=mysql_fetch_array($res)){
		$no++;

		if (isset($menuAccess[$s]["edit"])) {
			$controlKebutuhan = "<a href=\"?par[mode]=edit&par[kodeModul]=$r[kodeModul]&par[id]=$r[id]". getPar($par, "mode,idp") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
		}

		if(empty($r[fileUat])){
			$r[downloadUat] = " - ";
			$r[viewUat] = " - ";
			$r[sizeUat] = " - ";
		}else{
			$r[downloadUat] = "<a href=\"download.php?d=fileUat&f=$r[id]\"><img src=\"".getIcon($r[fileUat])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a></a>";
			$r[viewUat] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileUat&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
			$r[sizeUat] = getSizeFile($fChecklist.$r[file]);
		}

		if(empty($r[fileBa])){
			$r[downloadBa] = " - ";
			$r[viewBa] = " - ";
			$r[sizeBa] = " - ";
		}else{
			$r[downloadBa] = "<a href=\"download.php?d=filePelatiB&f=$r[id]\"><img src=\"".getIcon($r[fileBa])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
			$r[viewBa] = "<a href=\"#\" onclick=\"openBox('view.php?doc=filePelatiB&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
			$r[sizeBa] = getSizeFile($fChecklist.$r[file]);
		}

		if(empty($r[filePlk])){
			$r[downloadPlk] = " - ";
			$r[viewPlk] = " - ";
			$r[sizePlk] = " - ";
		}else{
			$r[downloadPlk] = "<a href=\"download.php?d=filePelati&f=$r[id]\"><img src=\"".getIcon($r[filePlk])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
			$r[viewPlk] = "<a href=\"#\" onclick=\"openBox('view.php?doc=filePelati&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
			$r[sizePlk] = getSizeFile($fChecklist.$r[file]);
		}



		$data=array(

			"<div align=\"center\">".$no.".</div>",				

			"<div align=\"left\">$r[namaModul]</div>",
			"<div align=\"center\">$r[viewPlk]</div>",

			"<div align=\"center\">$r[downloadPlk]</div>",	

			"<div align=\"center\">$r[viewBa]</div>",

			"<div align=\"center\">$r[downloadBa]</div>",	


			

			"<div align=\"center\">$controlKebutuhan</div>",		



			);





		$json['aaData'][]=$data;


	}

	if($par[mode] == "xls"){
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=DATA PELATIHAN ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP PELATIHAN");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F5')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');	
	$objPHPExcel->getActiveSheet()->mergeCells('C4:D4');	
	$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');	
	$objPHPExcel->getActiveSheet()->mergeCells('F4:F5');	
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "MODUL");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "DOKUMEN");
	$objPHPExcel->getActiveSheet()->setCellValue('C5', "PELATIHAN");
	$objPHPExcel->getActiveSheet()->setCellValue('D5', "BA");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "PELAKSANAAN");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "STATUS");

	
	$rows=6;

	$sql = " SELECT t1.*,t2.id, t2.fileUat, t2.fileBa, t2.filePlk,t2.status,t2.tanggalPlk FROM app_modul t1 left join doc_pelatihan t2 on t1.kodeModul = t2.kodeModul";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
		
		$r[jumlahMenu] = getField("select count(kodeMenu) from app_menu where kodeModul = '$r[kodeModul]'");

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$r[fileUat] = empty($r[fileUat]) ? "Tidak Ada" : "Ada";
		$r[fileBa] = empty($r[fileBa]) ? "Tidak Ada" : "Ada";

		switch ($r[status]) {
			case 't':
			$r[status] = "Sudah";
			break;
			case 'o':
			$r[status] = "Pending";
			break;
			
			default:
			$r[status] = "Belum";
			break;
		}
		if($r[status]=="Sudah"){
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
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaModul]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[fileUat]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[fileBa]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tanggalPlk]));
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
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
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
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA PELATIHAN");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."DATA PELATIHAN ".$sekarang.".xls");
}	




function form(){
	global $s,$inp,$par,$menuAccess,$fUat,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM doc_pelatihan t1 join app_modul t2 where t1.kodeModul = '$par[kodeModul]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	$r[namaModul] = empty($r[namaModul]) ? getField("select namaModul from app_modul where kodeModul = '$par[kodeModul]'") : $r[namaModul];

	$belum =  $r[status] == "p" ? "checked=\"checked\"" : "";
	$pending =  $r[status] == "o" ? "checked=\"checked\"" : "";
	$selesai =  empty($belum) && empty($pending) ? "checked=\"checked\"" : "";

	// $r[appr_div_by] = empty($r[appr_div_by]) ? $cUsername : $r[appr_div_by];

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	 	
			<p style=\"position:absolute;right:5px;top:5px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, id,kodeModul,modul") . "';\"/>
			</p>
			
			

			<p>
				<label class=\"l-input-small\">Modul</label>
				<span class=\"field\">$r[namaModul]&nbsp;</span>
			</p>
			<fieldset>
				<legend> UAT </legend>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[fileUat])?
						"<input type=\"text\" id=\"fotoUat\" name=\"fotoUat\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"fileUat\" name=\"fileUat\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoUat.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[fileUat])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delUat".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[ketUat]\" name=\"inp[ketUat]\">$r[ketUat]</textarea>
					</span>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[picUat]\" name=\"inp[picUat]\"  value=\"$r[picUat]\" class=\"mediuminput\" />
					</div>
				</p>
			</fieldset>

			<fieldset>
				<legend> BAST </legend>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[fileBa])?
						"<input type=\"text\" id=\"fotoBa\" name=\"fotoBa\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"fileBa\" name=\"fileBa\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoBa.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[fileBa])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delBa".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[ketBa]\" name=\"inp[ketBa]\">$r[ketBa]</textarea>
					</span>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[picBa]\" name=\"inp[picBa]\"  value=\"$r[picBa]\" class=\"mediuminput\" />
					</div>
				</p>
			</fieldset>

			<fieldset>
				<legend> PELAKSANAAN </legend>
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tanggalPlk]\" name=\"inp[tanggalPlk]\"  value=\"".getTanggal($r[tanggalPlk])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[filePlk])?
						"<input type=\"text\" id=\"fotoPlk\" name=\"fotoPlk\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"filePlk\" name=\"filePlk\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoPlk.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[filePlk])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delPlk".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[ketPlk]\" name=\"inp[ketPlk]\">$r[ketPlk]</textarea>
					</span>
				</p>
				<p>
					<label class=\"l-input-small\" >Status</label>
					<div class=\"field\">     
						<input type=\"radio\" id=\"inp[status1]\" name=\"inp[status]\" value=\"p\" $belum /> <span class=\"sradio\">Belum</span>
						<input type=\"radio\" id=\"inp[status2]\" name=\"inp[status]\" value=\"o\" $pending /> <span class=\"sradio\">Pending</span>  
						<input type=\"radio\" id=\"inp[status2]\" name=\"inp[status]\" value=\"t\" $selesai /> <span class=\"sradio\">Selesai</span>       
					</div>
				</p>
			</fieldset>
			


			
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

		case "delUat":
		$text = hapusUat();
		break;

		case "delBa":
		$text = hapusBa();
		break;

		case "delPlk":
		$text = hapusPlk();
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
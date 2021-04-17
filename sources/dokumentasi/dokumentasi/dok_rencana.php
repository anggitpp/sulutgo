<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fRencana = "files/dokumentasi/rencana/";

function uploadFile($id) {
	global $s, $inp, $par, $fRencana;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fRencana);
		$foto_file = "rencana-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fRencana, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_rencana where id ='$id'");

	return $foto_file;
}
function hapus(){
	global $s,$inp,$par,$fRencana,$cUsername;
	$foto_file = getField("select file from doc_file where idRencana='$par[id]'");
	if(file_exists($fRencana.$foto_file) and $foto_file!="")unlink($fRencana.$foto_file);

	$sql="delete from doc_rencana where id='$par[id]'";
	db($sql);
	echo "<script>window.location='?par[mode]=lihat".getPar($par,"mode,id")."';</script>";
}

function hapusFile() {
	global $s, $inp, $par, $fRencana, $cUsername;

	$foto_file = getField("select file from doc_rencana where id='$par[id]'");
	if (file_exists($fRencana . $foto_file) and $foto_file != "")
		unlink($fRencana . $foto_file);

	$sql = "update doc_rencana set file='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function uploadDok($id) {
	global $s, $inp, $par, $fRencana;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fRencana);
		$foto_file = "rencana_dok-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fRencana, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_file where id ='$id'");

	return $foto_file;
}

function hapusDok() {
	global $s, $inp, $par, $fRencana, $cUsername;

	$foto_file = getField("select file from doc_file where id='$par[idDoc]'");
	if (file_exists($fRencana . $foto_file) and $foto_file != "")
		unlink($fRencana . $foto_file);

	$sql = "update doc_file set file='' where id='$par[idDoc]'";
	db($sql);

	echo "<script>window.location='?par[mode]=editDoc" . getPar($par, "mode") . "';</script>";
}

function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	if(!empty($par[id])){
		$id = $par[id];
		$wew = uploadFile($id);
		$inp[tglMulai] = setTanggal($inp[tglMulai]);
		$inp[tglSelesai] = setTanggal($inp[tglSelesai]);
		$inp[tglPelaksanaan] = setTanggal($inp[tglPelaksanaan]);
		$sql = "update doc_rencana set aktifitas = '$inp[aktifitas]',ketAktifitas = '$inp[ketAktifitas]',tglMulai = '$inp[tglMulai]',tglSelesai = '$inp[tglSelesai]',jamMulai = '$inp[jamMulai]',jamSelesai = '$inp[jamSelesai]',idKategori = '$inp[idKategori]', pic = '$inp[pic]',file = '$wew',tglPelaksanaan = '$inp[tglPelaksanaan]',pelaksana = '$inp[pelaksana]',ketPelaksanaan = '$inp[ketPelaksanaan]', updateDate = '".date('Y-m-d H:i:s')."', updateBy = '".date('Y-m-d H:i:s')."' where id = '$par[id]'";
		db($sql);

	}else{
		$id = getField("select id from doc_rencana order by id desc limit 1")+1;
		$inp[file] = uploadFile($id);
		$inp[tglMulai] = setTanggal($inp[tglMulai]);
		$inp[tglSelesai] = setTanggal($inp[tglSelesai]);
		$inp[tglPelaksanaan] = setTanggal($inp[tglPelaksanaan]);
		$sql = "insert into doc_rencana (id, aktifitas, ketAktifitas, tglMulai, tglSelesai, jamMulai, jamSelesai, idKategori, pic, file, tglPelaksanaan, pelaksana, ketPelaksanaan, createDate, createBy) values ('$id', '$inp[aktifitas]','$inp[ketAktifitas]','$inp[tglMulai]','$inp[tglSelesai]','$inp[jamMulai]','$inp[jamSelesai]','$inp[idKategori]','$inp[pic]','$inp[file]','$inp[tglPelaksanaan]','$inp[pelaksana]','$inp[ketPelaksanaan]','".date('Y-m-d H:i:s')."','$cUsername')";
		db($sql);
	}
	
	echo "<script>alert('UPDATE DATA BERHASIL')</script>";
	echo "<script>window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function tambahFile(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$id = getField("select id from doc_file order by id desc limit 1")+1;
	$inp[file] = uploadDok($id);
	$sql = "insert into doc_file (id, idRencana, namaFile, file, keterangan, createDate, createBy) values ('$id', '$par[id]','$inp[namaFile]','$inp[file]','$inp[keterangan]','".date('Y-m-d H:i:s')."','$cUsername')";
	db($sql);
	
	echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
}

function ubahFile(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$inp[file] = uploadDok($par[idDoc]);
	$sql = "update doc_file set namaFile = '$inp[namaFile]', file = '$inp[file]', keterangan = '$inp[keterangan]', updateBy = '$cUsername', updateDate = '".date('Y-m-d H:i:s')."' where id = '$par[idDoc]'";
	db($sql);
	
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
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


	$text = table($cols, array(($cols-1),$cols));

	$text.="<div class=\"pageheader\">

	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

	".getBread()."

	<span class=\"pagedesc\">&nbsp;</span>

</div>    

<div id=\"contentwrapper\" class=\"contentwrapper\">

	<form action=\"\" method=\"post\" id = \"form\" class=\"stdform\" onsubmit=\"return false;\">

		<div id=\"pos_l\" style=\"float:left;\">

			<p>					

				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
				
				".comboData("select * from mst_data where kodeCategory = 'KRK' order by urutanData","kodeData","namaData","bSearch","All","","\"","210px;","chosen-select")."
			</p>

		</div>	
		<div id=\"pos_r\" style=\"float:right;\"><a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
			";
			if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idSakit")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
			$text.="
			
		</div>



	</form>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"20\">No.</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"*\">Aktifitas</th>

				<th colspan=\"2\" width=\"100\">Rencana</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"100\">Aktual</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"100\">Gap</th>
				
				
				";if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"50\">Kontrol</th>";
				$text.="

			</tr>
			<tr>
				<th width=\"50\">Mulai</th>
				<th width=\"50\">Selesai</th>				
			</tr>

		</thead>

		<tbody></tbody>
	</table>

</div>";
$sekarang = date('Y-m-d');
if($par[mode] == "xls"){
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=DATA UAT ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

return $text;

}



function lData(){

	global $s,$par,$fRencana,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
	if($_GET[json]==1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;


	$filters= " where id is not null";

	if (!empty($_GET['fSearch']))

		$filters.= " and (				

	lower(aktifitas) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


	)";
	if(!empty($_GET['bSearch']))
		$filters.= " and idKategori='".$_GET[bSearch]."'";



	$arrOrder = array(	

		"tglMulai",
		"aktifitas",
		"tglMulai",
		"tglSelesai",
		"",




		);


	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql = " SELECT * from doc_rencana $filters order by $orderBy $sLimit ";
		// echo $sql;

	$res=db($sql);



	$json = array(

		"iTotalRecords" => mysql_num_rows($res),

		"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM doc_rencana $filters"),


		"aaData" => array(),

		);







	$no=intval($_GET['iDisplayStart']);

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	while($r=mysql_fetch_array($res)){
		$no++;

		list($y,$m,$d) = explode("-", $r[tglPelaksanaan]);
		$day = dateDiff("d", mktime(0,0,0,date("m"), date("d"), date("Y")), mktime(0,0,0,$m, $d, $y));
		$day = mktime(0,0,0,date("m"), date("d"), date("Y")) < mktime(0,0,0,$m, $d, $y) ? $day : $day * -1;
		if($r[tglPelaksanaan]=="0000-00-00"){
			$sisa = "";
		}else{
			$sisa = getAngka($day)."hari";
		}

		if (isset($menuAccess[$s]["edit"])) {
			$controlKebutuhan = "<a href=\"?par[mode]=edit&par[id]=$r[id]". getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
		}

		if(isset($menuAccess[$s]["delete"]))

				// $controlDokumen.= "<a href=\"#Delete\" onclick=\"del('$r[username]','".getPar($par,"mode,username")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

			$controlKebutuhan.= "<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

		$data=array(

			"<div align=\"center\">".$no.".</div>",				

			"<div align=\"left\">$r[aktifitas]</div>",

			"<div align=\"left\">".getTanggal($r[tglMulai])."</div>",

			"<div align=\"left\">".getTanggal($r[tglSelesai])."</div>",

			"<div align=\"center\">".getTanggal($r[tglPelaksanaan])."</div>",

			"<div align=\"left\">&nbsp;".$sisa."</div>",

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

	$objPHPExcel->getActiveSheet()->mergeCells('B1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');		
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);

	$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
	// $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('B1', "JADWAL RENCANA KERJA");

	
	$objPHPExcel->getActiveSheet()->getStyle('B4:F4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('D5:F5')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('B4:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B4:F5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B4:F5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('C5:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	// $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

	// $objPHPExcel->getActiveSheet()->mergeCells('A4:A5');	
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');	
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');	
	$objPHPExcel->getActiveSheet()->mergeCells('D4:E4');	
	
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "Aktifitas");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "Rencana");
	$objPHPExcel->getActiveSheet()->setCellValue('D5', "Mulai");
	$objPHPExcel->getActiveSheet()->setCellValue('E5', "Selesai");
	$objPHPExcel->getActiveSheet()->setCellValue('F5', "PIC KLIEN");
	
	$rows=6;
		
	$sql = " SELECT * from doc_rencana";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
		$tanggalCreate = getField("SELECT createDate from doc_menuchecklist where kodeModul = '$r[kodeModul]'");

		$r[jumlahMenu] = getField("select count(kodeMenu) from app_menu where kodeModul = '$r[kodeModul]'");
							
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		
	
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[namaModul]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[tglMulai]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[tglSelesai]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pelaksana]);

		
		
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




function form(){
	global $s,$inp,$par,$menuAccess,$fRencana,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM doc_rencana where id = '$par[id]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	 	
			<p style=\"position:absolute;right:5px;top:5px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</p>
			
			<fieldset>
				<legend> RENCANA </legend>

				<p>
					<label class=\"l-input-small\">Aktifitas</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[aktifitas]\" name=\"inp[aktifitas]\"  value=\"$r[aktifitas]\" class=\"mediuminput\" />
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:502px;height:50px;\" id=\"inp[ketAktifitas]\" name=\"inp[ketAktifitas]\">$r[ketAktifitas]</textarea>
					</span>
				</p>
				<p>
					<label class=\"l-input-small\">Mulai</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tglMulai]\" name=\"inp[tglMulai]\"  value=\"".getTanggal($r[tglMulai])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
						<input type=\"text\" id=\"jamMulai\" name=\"inp[jamMulai]\" size=\"10\" maxlength=\"5\" value=\"$r[jamMulai]\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Selesai</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tglSelesai]\" name=\"inp[tglSelesai]\"  value=\"".getTanggal($r[tglSelesai])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
						<input type=\"text\" id=\"jamSelesai\" name=\"inp[jamSelesai]\" size=\"10\" maxlength=\"5\" value=\"$r[jamSelesai]\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						".comboData("select * from mst_data where statusData='t' and kodeCategory='KRK' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "204px","chosen-select")."
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[pic]\" name=\"inp[pic]\"  style=\"width:300px;\" value=\"$r[pic]\" class=\"mediuminput\" />
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[file])?
						"<input type=\"text\" id=\"fotoUat\" name=\"fotoUat\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoUat.value = this.value;\" />
						</div>":
						"<a href=\"download.php?d=fileRencana&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>
						<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>


			</fieldset>

			<fieldset>
				<legend> PELAKSANAAN </legend>
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tglPelaksanaan]\" name=\"inp[tglPelaksanaan]\"  value=\"".getTanggal($r[tglPelaksanaan])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Pelaksana</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[pelaksana]\" name=\"inp[pelaksana]\"  style=\"width:300px;\" value=\"$r[pelaksana]\" class=\"mediuminput\" />
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:502px;height:50px;\" id=\"inp[ketPelaksanaan]\" name=\"inp[ketPelaksanaan]\">$r[ketPelaksanaan]</textarea>
					</span>
				</p>
			</fieldset>

			<div class=\"widgetbox\">
				<div class=\"title\" >
					<h3 style=\"margin-bottom: -36px;\">Dokumen</h3>	
					<p style=\"position: relative; left: 966px; bottom:-6px;\">
						"; 
						if(empty($par[id])){
							$text.="<a onclick=\"alert('Silahkan klik tombol SAVE terlebih dahulu');\" href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
						}else{
							$text.="<a onclick=\"openBox('popup.php?par[mode]=tambahFile".getPar($par,"mode")."',725,300);\" href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
						}
						$text.="
					</p>
				</div>



				<br clear=\"all\"/>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntables\">

					<thead>

						<tr>
							<th width=\"20\">No.</th>
							<th width=\"*\">Dokumen</th>

							<th width=\"50\">View</th>
							<th width=\"50\">DL</th>
							<th width=\"100\">Upload</th>
							<th width=\"100\">User</th>
							<th width=\"80\">Size</th>


							<th width=\"50\">Kontrol</th>

						</tr>
					</thead>

					<tbody>
						";
						$sql="select *, date(createDate) as tanggal from doc_file where idRencana = '$par[id]' ";
						$res=db($sql);
						while($r=mysql_fetch_array($res)){			
							$no++;
							$text.="<tr>
							<td>$no.</td>
							<td>$r[namaFile]</td>

							<td align=\"center\"><a href=\"#\" onclick=\"openBox('view.php?doc=fileDoc&par[idDoc]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a></td>
							<td align=\"center\"><a href=\"download.php?d=fileDocRencana&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a></td>
							<td align=\"center\">".getTanggal($r[tanggal])."</td>				
							<td align=\"left\">".$r[createBy]."</td>

							<td align=\"center\">".getSizeFile($fRencana.$r[file])."</td>
							<td align=\"center\">
								<a onclick=\"openBox('popup.php?par[mode]=editDoc&par[idDoc]=$r[id]".getPar($par,"mode")."',725,300);\" href=\"#\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>
								<a href=\"?par[mode]=delDoc&par[idDoc]=$r[id]".getPar($par,"mode,idDoc")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";

								$text.="</tr>";				
							}
							$text.="</tbody>
						</table>




					</form>	
				</div>";
				return $text;
			}

			function formFile(){
				global $s,$inp,$par,$menuAccess,$fManual,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
				$sql="SELECT * FROM doc_file WHERE id='$par[idDoc]'";
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
								<label class=\"l-input-small\">Nama</label>
								<div class=\"field\">								
									<input type=\"text\" id=\"inp[namaFile]\" name=\"inp[namaFile]\"  value=\"$r[namaFile]\" class=\"mediuminput\" />
								</div>
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
									<a href=\"?par[mode]=delDok".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
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
						</div>



					</form>	
				</div>";
				return $text;
			}

			function view(){
				global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		

				if(strlen($par[bulanRencana]) < 1)$par[bulanRencana] = date('n')-1;
				if(strlen($par[tahunRencana]) < 1)$par[tahunRencana] = date('Y');		

				$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."					
			</div>
			<br clear=\"all\">
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div class=\"one_half last dashboard_left\">";			

					$arr = explode("/",$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);
					$url="http://";
					for($i=0; $i<(count($arr)-1); $i++){
						$url.=$arr[$i]."/";
					}
					$url.="calendar/rencana_kerja.php?".getPar($par)."";
					$arrData = json_decode(file_get_contents($url), true);

					$text.="<script type=\"text/javascript\" src=\"scripts/calendar.js\"></script>
					<script type=\"text/javascript\">
						jQuery(function () {								
							jQuery('#calendar').fullCalendar({
								month: $par[bulanRencana],
								year: $par[tahunRencana],

								buttonText: {
									prev: '&laquo;',
									next: '&raquo;',
									prevYear: '&nbsp;&lt;&lt;&nbsp;',
									nextYear: '&nbsp;&gt;&gt;&nbsp;',
									today: 'today',
									month: 'month',
									week: 'week',
									day: 'day'
								},

								header: {
									left: 'title',
									right: 'prev,next',															
								},
								
								events: {
									url: '$url',
									cache: true
								},								

								eventMouseover: function(calEvent, jsEvent) {									
									var tooltip = '<div class=\"tooltipevent\" style=\"padding:0 5px; position:absolute; z-index:10000; font-size:10px; background:#fff; color:#666; border:solid 1px #ccc; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;\">' + calEvent.data.namaAktifitas +'</div>';
									
									jQuery(\"body\").append(tooltip);
									jQuery(this).mouseover(function(e) {
										jQuery(this).css('z-index', 10000);
										jQuery('.tooltipevent').fadeIn('500');
										jQuery('.tooltipevent').fadeTo('10', 1.9);
									}).mousemove(function(e) {
										jQuery('.tooltipevent').css('top', e.pageY + 10);
										jQuery('.tooltipevent').css('left', e.pageX + 20);
									});
								},
								
								eventMouseout: function(calEvent, jsEvent) {
									jQuery(this).css('z-index', 8);
									jQuery('.tooltipevent').remove();
								},
								
								eventClick: function (calEvent, jsEvent, view) {								
									window.location = '?par[id]=' + calEvent.id + '".getPar($par,"id")."';
								},
							});
							
							jQuery('.fc-button-prev span').click(function(){
								document.getElementById('daftarLibur').style.display = 'none';
								var date = jQuery('#calendar').fullCalendar('getDate');
								var bulanRencana = date.getMonth() == 0 ? 11 : date.getMonth()-1;
								var tahunRencana = date.getMonth() == 0 ? date.getFullYear()-1 : date.getFullYear();
								window.location = '?par[bulanRencana]=' + bulanRencana + '&par[tahunRencana]=' + tahunRencana + '".getPar($par,"bulanRencana,tahunRencana,idLibur")."';
							});

							jQuery('.fc-button-next span').click(function(){
								document.getElementById('daftarLibur').style.display = 'none';
								var date = jQuery('#calendar').fullCalendar('getDate');
								var bulanRencana = date.getMonth() == 12 ? 0 : date.getMonth()+1;
								var tahunRencana = date.getMonth() == 12 ? date.getFullYear()+1 : date.getFullYear();
								window.location = '?par[bulanRencana]=' + bulanRencana + '&par[tahunRencana]=' + tahunRencana + '".getPar($par,"bulanRencana,tahunRencana,idLibur")."';
							});
						});
					</script>";
					
					$text.="<div id=\"calendar\"></div>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:5px;\">
							<span style=\"float:left;\"><h3>Daftar Rencana Kerja</h3></span>
							<input type=\"button\" class=\"cancel radius2\" value=\"VIEW ALL\" onclick=\"window.location='?par[mode]=lihat".getPar($par,"mode,idLibur")."';\"  style=\"float:right; margin-top:-15px;\"/>
						</div>
						<div id=\"daftarLibur\" class=\"widgetcontent userlistwidget nopadding\">
							<ul>";
								$no=1;
								if(is_array($arrData)){						
									while(list($i, $r)=each($arrData)){
										$data=$r[data];
										list($tglMulai) = explode(" ", $data[tglMulai]);
										list($tglSelesai) = explode(" ", $data[tglSelesai]);
										$text.="<li>
										<div style=\"width:10px; height:10px; background:$r[color]; margin-top:4px; margin-right:5px; float:left;\">&nbsp;</div>
										<a href=\"?par[id]=$data[id]".getPar($par,"id")."\">$data[namaAktifitas]</a>
										<div style=\"font-size:10px; color:#888;\">".getTanggal($tglMulai)." s.d ".getTanggal($tglSelesai)."</div>
									</li>";
								}
							}	
							$text.="</ul>
						</div>
					</div>
				</div>
				<div class=\"one_half last dashboard_right\" style=\"margin-left:20px;\">					
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:5px;\"><h3>Keterangan</h3></div>";
						
						if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,id")."\" class=\"btn btn1 btn_document\"  style=\"position:absolute; right:0; top:0; margin-top:0px;\"><span>Tambah Data</span></a>";
						
						if(empty($par[id])) $par[id] = getField("select id from doc_rencana where ".$par[tahunRencana].($par[bulanRencana]+1)." between concat(year(tglMulai),month(tglMulai)) and concat(year(tglSelesai),month(tglSelesai)) order by tglMulai limit 1");

						$sql="select * from doc_rencana t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.id='$par[id]'";
						$res=db($sql);
						$r=mysql_fetch_array($res);										
$r[download] = "<a href=\"download.php?d=fileRencana&f=$par[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
						$tanggalLibur = $r[tglMulai] == $r[tglSelesai] ? getTanggal($r[tglMulai]) : getTanggal($r[tglMulai])." s.d ".getTanggal($r[tglSelesai]);

						$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">					
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Judul</label>
							<span class=\"field\" style=\"margin-left:140px;\">$r[aktifitas]&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Kategori</label>
							<span class=\"field\" style=\"margin-left:140px;\">$r[namaData]&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Tanggal</label>
							<span class=\"field\" style=\"margin-left:140px;\">$tanggalLibur&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Keterangan</label>
							<span class=\"field\" style=\"margin-left:140px;\">".nl2br($r[ketAktifitas])."&nbsp;</span>
						</p>	
						<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">PIC</label>
							<span class=\"field\" style=\"margin-left:140px;\">$r[pic]&nbsp;</span>
						</p>	
						<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">File</label>
							<span class=\"field\" style=\"margin-left:140px;\">$r[download]&nbsp;</span>
						</p>		

					</form>
				</div>
				<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:5px;\"><h3>Pelaksanaan</h3></div>";
						
						if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,id")."\" class=\"btn btn1 btn_document\"  style=\"position:absolute; right:0; top:0; margin-top:0px;\"><span>Tambah Data</span></a>";
						
						if(empty($par[id])) $par[id] = getField("select id from doc_rencana where ".$par[tahunRencana].($par[bulanRencana]+1)." between concat(year(tglMulai),month(tglMulai)) and concat(year(tglSelesai),month(tglSelesai)) order by tglMulai limit 1");

						$sql="select * from doc_rencana t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.id='$par[id]'";
						$res=db($sql);
						$r=mysql_fetch_array($res);										


						$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">					
					
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Tanggal</label>
							<span class=\"field\" style=\"margin-left:140px;\">".getTanggal($r[tglPelaksanaan])."</span>
						</p>
						<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Pelaksana</label>
							<span class=\"field\" style=\"margin-left:140px;\">$r[pelaksana]</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Keterangan</label>
							<span class=\"field\" style=\"margin-left:140px;\">".nl2br($r[ketPelaksanaan])."&nbsp;</span>
						</p>	
							
					

					</form>
				</div>
			</div>
		</div>";				
		return $text;
	}


	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){


			case "lst":

			$text=lData();

			break;	

			case "view":
			$text = view();
			break;

			case "delFile":
			$text = hapusFile();
			break;

			case "delDok":
			$text = hapusDok();
			break;
			case "del":
			if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;


			case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;

			case "editDoc":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formFile() : ubahFile(); else $text = lihat();
			break;

			case "add":
			if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;

			case "lihat":
			$text = lihat();
			break;

			case "tambahFile":
			if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formFile() : tambahFile(); else $text = lihat();
			break;

			default:
			$text = view();
			break;
		}
		return $text;
	}	
	?>
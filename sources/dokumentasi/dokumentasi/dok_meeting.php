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

	echo "<script>window.location='?par[mode]=editDoc" . getPar($par, "mode,idDoc") . "';</script>";
}

function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	if(!empty($par[id])){
		$inp[file] = uploadFile($par[id]);
		$inp[tglMulai] = setTanggal($inp[tglMulai]);
		$inp[tglSelesai] = setTanggal($inp[tglSelesai]);
		$inp[tglPelaksanaan] = setTanggal($inp[tglPelaksanaan]);
		$sql = "update doc_rencana set aktifitas = '$inp[aktifitas]',ketAktifitas = '$inp[ketAktifitas]',tglMulai = '$inp[tglMulai]',tglSelesai = '$inp[tglSelesai]',jamMulai = '$inp[jamMulai]',jamSelesai = '$inp[jamSelesai]',idKategori = '$inp[idKategori]', pic = '$inp[pic]',file = '$inp[file]',tglPelaksanaan = '$inp[tglPelaksanaan]',pelaksana = '$inp[pelaksana]',ketPelaksanaan = '$inp[ketPelaksanaan]', updateDate = '".date('Y-m-d H:i:s')."', updateBy = '".date('Y-m-d H:i:s')."' where id = '$par[id]'";
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
				

			</p>

		</div>	
		<div id=\"pos_r\" style=\"float:right;\">
			
			<a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
		</div>



	</form>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>
				<th  width=\"20\">No.</th>
				<th  width=\"*\">Aktifitas</th>

				<th width=\"200\">AGENDA</th>
				<th width=\"80\">Rencana</th>
				<th  width=\"80\">Aktual</th>
				<th  width=\"30\">File</th>
				
				
				";if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th  width=\"50\">Kontrol</th>";
				$text.="

			</tr>
		</thead>

		<tbody></tbody>
	</table>

</div>";
$sekarang = date('Y-m-d');
if($par[mode] == "xls"){
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=MOM ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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

	$kMeeting = getField("select kodeData from mst_data where kodeMaster = 'MTG'");
	$filters= " where id is not null and idKategori = '$kMeeting'";

	if (!empty($_GET['fSearch']))

		$filters.= " and (				

	lower(aktifitas) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


	)";
	/*if(!empty($par[kategori])){
		$filters .= " AND idKategori ='969";
	}*/



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

		$file = getField("select count(id) from doc_file where idRencana = '$r[id]'");

		if (isset($menuAccess[$s]["edit"])) {
			$controlKebutuhan = "<a href=\"?par[mode]=edit&par[id]=$r[id]". getPar($par, "mode,id") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
		}


		if(isset($menuAccess[$s]["delete"]))

				// $controlDokumen.= "<a href=\"#Delete\" onclick=\"del('$r[username]','".getPar($par,"mode,username")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

			$controlKebutuhan.= "<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
		$data=array(

			"<div align=\"center\">".$no.".</div>",				

			"<div align=\"left\">$r[aktifitas]</div>",

			"<div align=\"left\">".$arrMaster[$r[idKategori]]."</div>",

			"<div align=\"center\">".getTanggal($r[tglMulai])."</div>",

			"<div align=\"center\">".getTanggal($r[tglPelaksanaan])."</div>",

			"<div align=\"center\">".$file."</div>",

			"<div align=\"center\">$controlKebutuhan</div>",		



			);





		$json['aaData'][]=$data;


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
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP MOM");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "AKTIFITAS");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "AGENDA");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "RENCANA");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "AKTUAL");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "FILE");
	$objPHPExcel->getActiveSheet()->setCellValue('G4', "STATUS");

	
	$rows=5;
	$filters= " where id is not null and idKategori = '969'";
	if (!empty($_GET['fSearch']))
		$filters.= " and (				
	lower(aktifitas) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
	)";
	
	$sql = "SELECT * from doc_rencana $filters";

	$res=db($sql);
	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
	while($r=mysql_fetch_array($res)){			
		$no++;
		
		$file = getField("select count(id) from doc_file where idRencana = '$r[id]'");

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$r[status] = !empty($r[tglPelaksanaan]) ? "Sudah" : "Belum";

		if($r[status]=="Sudah"){
			$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '016f00')
						)
					)
				);
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[aktifitas]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $arrMaster[$r[idKategori]]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getTanggal($r[tglMulai]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tglPelaksanaan]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $file);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[status]);
		
		
		$rows++;
	}
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G'.$rows)->getAlignment()->setWrapText(true);						
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
	$objPHPExcel->getActiveSheet()->setTitle("MOM");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."MOM ".$sekarang.".xls");
}	




function form(){
	global $s,$inp,$par,$menuAccess,$fRencana,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
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
					<span class=\"field\" style=\"margin-left:23%\">".$r[aktifitas]."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\" style=\"margin-left:23%\">".$r[ketAktifitas]."&nbsp;</span>
				</p>

				<table style=\"width:100%\">

					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Mulai</label>
								<span class=\"field\" style=\"margin-left:46%\">".getTanggal($r[tglMulai])."&nbsp;</span>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Jam</label>
								<span class=\"field\">".$r[jamMulai]."&nbsp;</span>
							</p>
						</td>
					</tr>
					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Selesai</label>
								<span class=\"field\" style=\"margin-left:46%\">".getTanggal($r[tglSelesai])."&nbsp;</span>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Jam</label>
								<span class=\"field\">".$r[jamSelesai]."&nbsp;</span>
							</p>
						</td>
					</tr>
					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Kategori</label>
								<span class=\"field\" style=\"margin-left:46%\">".$arrMaster[$r[idKategori]]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">PIC</label>
								<span class=\"field\" style=\"margin-left:46%\">".$r[pic]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small2\">File</label>
								<div class=\"field\" style=\"margin-left:46%\"><a href=\"download.php?d=fileRencana&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>&nbsp;
									<a href=\"#\" onclick=\"openBox('view.php?doc=fileRencana&par[idRencana]=$par[id]".getPar($par,"mode")."',725,500);\" </a><img src=\"http://apics.id/sekolah/sister/styles/images/icons/detail.png\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\">
										

									</div>
								</p>

							</td>
							<td style=\"width:50%\">
								&nbsp;
							</td>
						</tr>
					</table>			
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
							<input type=\"text\" id=\"inp[pelaksana]\" name=\"inp[pelaksana]\"  value=\"$r[pelaksana]\" class=\"mediuminput\" />
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
							"; $text.="<a onclick=\"openBox('popup.php?par[mode]=tambahFile".getPar($par,"mode")."',725,300);\" href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
							
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
									<a href=\"?par[mode]=delDoc&par[idDoc]=$r[id]&par[idInti]=$par[id]".getPar($par,"mode,idDoc")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";

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
				function hapus(){
					global $s,$inp,$par,$fRencana,$cUsername;
					$foto_file = getField("select file from doc_file where idRencana='$par[id]'");
					if(file_exists($fRencana.$foto_file) and $foto_file!="")unlink($fRencana.$foto_file);

					$sql="delete from doc_rencana where id='$par[id]'";
					db($sql);
					echo "<script>window.location='?par[mode]=lihat".getPar($par,"mode,id")."';</script>";
				}

				function hapusDoc(){
					global $s,$inp,$par,$fRencana,$cUsername;
					$foto_file = getField("select file from doc_file where idRencana='$par[id]'");
					if(file_exists($fRencana.$foto_file) and $foto_file!="")unlink($fRencana.$foto_file);

					$sql="delete from doc_file where id='$par[idDoc]'";
					db($sql);
					echo "<script>window.location='?par[mode]=edit&par[id]=$par[idInti]" . getPar($par, "mode,idDoc,id") . "';</script>";
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

						case "delDok":
						$text = hapusDok();
						break;
						case "delDoc":
						$text = hapusDoc();
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

						case "tambahFile":
						if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formFile() : tambahFile(); else $text = lihat();
						break;

						default:
						$text = lihat();
						break;
					}
					return $text;
				}	
				?>
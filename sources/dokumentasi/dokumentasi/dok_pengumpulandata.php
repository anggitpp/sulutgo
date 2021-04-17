<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fRencana = "files/dokumentasi/rencana/";

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

function aktifitas() {
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(id, '\t', aktifitas) from doc_rencana where idKategori='$par[kategori_dokumen]' order by aktifitas");
  return implode("\n", $data);
}

function hapusDok() {
	global $s, $inp, $par, $fRencana, $cUsername;

	$foto_file = getField("select file from doc_file where id='$par[id]'");
	if (file_exists($fRencana . $foto_file) and $foto_file != "")
		unlink($fRencana . $foto_file);

	$sql = "update doc_file set file='' where id='$par[id]'";
	// echo $sql;
	// die();
	db($sql);

	echo "<script>window.location='?par[mode]=editDokumen" . getPar($par, "mode") . "';</script>";
}

function tambah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$id = getField("select id from doc_file order by id desc limit 1")+1;
	$inp[file] = uploadDok($id);
	$sql = "insert into doc_file (id, idRencana, namaFile, file, keterangan,kategori_dokumen,kegiatan_dokumen, createdDate, createdBy) values ('$id', '$inp[idRencana]','$inp[namaFile]','$inp[file]','$inp[keterangan]','$inp[kategori_dokumen]','$inp[kegiatan_dokumen]','".date('Y-m-d H:i:s')."','$cUsername')";
	// echo $sql;
	 //die();
	db($sql);
	
	echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
}

function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$inp[file] = uploadDok($par[id]);
	$sql = "update doc_file set namaFile = '$inp[namaFile]', file = '$inp[file]', kategori_dokumen = '$inp[kategori_dokumen]',kegiatan_dokumen = '$inp[kegiatan_dokumen]',keterangan = '$inp[keterangan]', updatedBy = '$cUsername', updatedDate = '".date('Y-m-d H:i:s')."' where id = '$par[id]'";
	db($sql);
	
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

".comboData("select * from doc_rencana where idKategori='964'  order by aktifitas","id","aktifitas","par[kegiatan_dokumen]","All",$par[kegiatan_dokumen],"onchange=\"document.getElementById('form').submit();\"", "310px","chosen-select")."


			
			</p>

		</div>	
		<div id=\"pos_r\" style=\"float:right;\">
		";
		if(isset($menuAccess[$s]["add"])) $text.="<a onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode")."',725,500);\" href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="
			<a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
		</div>



	</form>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>
				<th width=\"20\">No.</th>
				<th width=\"*\">Dokumen</th>

				<th width=\"50\">View</th>
				<th width=\"50\">D/L</th>
				<th width=\"100\">Date</th>
				<th width=\"100\">User</th>
				<th width=\"80\">Size</th>
				
				
				";if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
				$text.="

				</tr>
				

			</thead>

			<tbody></tbody>
			</table>

		</div>";
		$sekarang = date('Y-m-d');
		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA DOKUMEN ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}

		return $text;

	}

	

	function lData(){

		global $s,$par,$fRencana,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m,$fRencana;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
		if($_GET[json]==1){
			header("Content-type: application/json");
		}

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;
		

		$filters= " where t1.id is not null";

		if (!empty($_GET['fSearch']))

			$filters.= " and (				

		lower(namaFile) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


		)";

		
		

		$arrOrder = array(	

			"namaFile",
			"namaFile",
			"",
			"",
			"t1.date(createdDate)",
			"",





			);

		if(!empty($par[kegiatan_dokumen])){
			$filters.=" and t2.kategori_dokumen = '$par[kegiatan_dokumen]'";
		}


		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		$sql = " SELECT t1.*,date(t1.createdDate) as tanggal from doc_file t1 join doc_rencana t2 on t1.idRencana = t2.id $filters order by $orderBy $sLimit ";
		// echo $sql;

		$res=db($sql);

		

		$json = array(

			"iTotalRecords" => mysql_num_rows($res),

			"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM doc_file t1 join doc_rencana t2 on t1.idRencana = t2.id $filters"),
			

			"aaData" => array(),

			);

		

		$no=intval($_GET['iDisplayStart']);

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		while($r=mysql_fetch_array($res)){
			$no++;

			$controlKebutuhan = "";

			if (isset($menuAccess[$s]["edit"])) {
					$controlKebutuhan = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editDokumen&par[id]=$r[id]".getPar($par,"mode")."',725,500);\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
			}
if(isset($menuAccess[$s]["delete"]))

				// $controlDokumen.= "<a href=\"#Delete\" onclick=\"del('$r[username]','".getPar($par,"mode,username")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

				$controlKebutuhan.= "<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";


			if(empty($r[file])){
				$r[download] = " - ";
				$r[view] = " - ";
				$r[size] = " - ";
			}else{
				$r[download] = "<a href=\"download.php?d=fileDocRencana&f=$r[id]\"><img src=\"".getIcon($r[file])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:20px; max-height:20px;\"></a>";
				$r[view] = "<a href=\"#\" onclick=\"openBox('view.php?doc=fileDoc&par[idDoc]=$r[id]".getPar($par,"mode")."',725,500);\" class=\"detail\"><span>Detail</span></a>";
				$r[size] = getSizeFile($fRencana.$r[file]);
			}


			$data=array(

				"<div align=\"center\">".$no.".</div>",				

				"<div align=\"left\">$r[namaFile]</div>",

				"<div align=\"center\">$r[download]</div>",	

				"<div align=\"center\">$r[view]</div>",

				"<div align=\"center\">".getTanggal($r[tanggal])."</div>",
				"<div align=\"center\">$r[createdBy]</div>",	

				"<div align=\"center\">$r[size]</div>",	

				"<div align=\"center\">$controlKebutuhan</div>",		

				

				);





			$json['aaData'][]=$data;


		}

		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA DOKUMEN ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP DOKUMEN");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "DOKUMEN");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "KATEGORI");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "KEGIATAN");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "TANGGAL");

	
	$rows=5;
	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
	$sql = " SELECT t1.*,date(t1.createDate) as tanggal,t2.idKategori,t2.aktifitas from doc_file t1 join doc_rencana t2 on t1.idRencana = t2.id";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaFile]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $arrMaster[$r[idKategori]]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[aktifitas]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tanggal]));
		
		
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
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA DOKUMEN");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."DATA DOKUMEN ".$sekarang.".xls");
}	


function hapus(){
	global $s,$inp,$par,$fRencana,$cUsername;


	$sql="delete from doc_file where id='$par[id]'";
	db($sql);
	echo "<script>window.location='?par[mode]=lihat".getPar($par,"mode,id")."';</script>";
}

function form(){
	global $s,$inp,$par,$menuAccess,$fManual,$cUsername,$arrTitle,$arrParameter,$cNama,$cNickname;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT *,t1.file as file FROM doc_file t1 join doc_rencana t2 on t1.idRencana = t2.id WHERE t1.id='$par[id]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	
$arrNama = arrayQuery("select username, nickName from app_user");
$r[createdBy] = empty($r[createdBy]) ? $cNickname : $arrNama[$r[createdBy]];
$r[updatedBy] = empty($r[updatedBy]) ? $cNickname : $arrNama[$r[updatedBy]];

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
			"; if($par[mode] != "add") {
			    
			    $text.="	<fieldset>

			<legend>HISTORY</legend>

		<table style=\"width: 100%\">
				<tr>
					<td width=\"50%\">
						<label class=\"l-input-small\" style=\"width:50%\">Input Date</label>
						<span class=\"field\">$r[createdDate]&nbsp;</span>
					</td>
					<td width=\"50%\">
						<label class=\"l-input-small\" style=\"width:50%\">Input By</label>
						<span class=\"field\">$r[createdBy]&nbsp;</span>		
					</td>
				</tr>
			</table>

			<table style=\"width: 100%\">
				<tr>
					<td width=\"50%\">
						<label class=\"l-input-small\" style=\"width:50%\">Update Date</label>
						<span class=\"field\">$r[updatedDate]&nbsp;</span>
					</td>
					<td width=\"50%\">
						<label class=\"l-input-small\" style=\"width:50%\">Update By</label>
						<span class=\"field\">".$arrNama[$r[updatedBy]]."&nbsp;</span>		
					</td>
				</tr>
			</table>
				
			</fieldset>";
		}$text.="
			    
			    
			<fieldset>
			<legend> RENCANA KERJA </legend>
			<p>
				<label class=\"l-input-small\">Kategori</label>
				<div class=\"field\">
					".comboData("select * from mst_data where statusData='t' and kodeCategory='KRK' and namaData LIKE '%pengumpulan data%' order by urutanData","kodeData","namaData","inp[kategori_dokumen]","",$r[kategori_dokumen],"onchange=\"getAktifitas('" . getPar($par, "mode,kategori_dokumen") . "');\"", "310px")."
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Kegiatan</label>
				<div class=\"field\">
					".comboData("select * from doc_rencana where idKategori='964'  order by aktifitas","id","aktifitas","inp[kegiatan_dokumen]"," ",$r[kegiatan_dokumen],"", "310px")."
				</div>
			</p>
			</fieldset>
			<fieldset>
			<legend> UPLOAD </legend>
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

		case "delFile":
		$text = hapusFile();
		break;

		case "aktifitas":
		$text = aktifitas();
		break;

		case "delDok":
		$text = hapusDok();
		break;
  case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
				break;
	

		case "editDokumen":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;

		case "add":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
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
<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fFoto = "files/dokumentasi/team/";

function uploadFoto($id) {
	global $s, $inp, $par, $fFoto;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fFoto);
		$foto_file = "team-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fFoto, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_team where id ='$id'");

	return $foto_file;
}

function hapusFoto() {
	global $s, $inp, $par, $fFoto, $cUsername;

	$foto_file = getField("select file from doc_team where id='$par[id]'");
	if (file_exists($fFoto . $foto_file) and $foto_file != "")
		unlink($fFoto . $foto_file);

	$sql = "update doc_team set file='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus() {
	global $s, $inp, $par, $fFoto, $cUsername;

	$sql = "delete from doc_team where id='$par[id]'";
	db($sql);

	echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?" . getPar($par, "mode,id") . "';</script>";
}


function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$inp[file] = uploadFoto($par[id]);
	$sql = "update doc_team set nama = '$inp[nama]',hp = '$inp[hp]',email = '$inp[email]',bagian = '$inp[bagian]',jabatan = '$inp[jabatan]',file = '$inp[file]', keterangan = '$inp[keterangan]' where id = '$par[id]'";
	db($sql);
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}

function tambah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$id = getField("select id from doc_team order by id desc limit 1")+1;
	$inp[file] = uploadFoto($id);
	$sql = "insert into doc_team (id, nama, hp, email, bagian, jabatan, file, keterangan, createDate, createBy) values ('$id', '$inp[nama]','$inp[hp]','$inp[email]','$inp[bagian]','$inp[jabatan]','$inp[file]','$inp[keterangan]','".date('Y-m-d H:i:s')."','$cUsername')";
	db($sql);
	// echo $sql;
	// die();
	
	echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
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


	$text = table($cols, array(($cols-3),($cols-2),($cols-1),$cols));

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
		";
		if(isset($menuAccess[$s]["add"])) $text.="<a onclick=\"openBox('popup.php?par[mode]=add". getPar($par, "mode") . "',825,500);\"  href=\"#\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="
			<a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
		</div>



	</form>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>
				<th width=\"20\">No.</th>
				<th width=\"80\">Foto</th>
				<th width=\"*\">Nama</th>
				
				<th width=\"100\">No. HP</th>
				<th width=\"150\">Bagian</th>
				<th width=\"150\">Jabatan</th>
				
				
				";if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\">Kontrol</th>";
				$text.="


			</thead>

			<tbody></tbody>
			</table>

		</div>";
		$sekarang = date('Y-m-d');
		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA TEAM ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}

		return $text;

	}

	

	function lData(){

		global $s,$par,$fFoto,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
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

		lower(nama) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


		)";

		
		

		$arrOrder = array(	

			"nama",
			"",
			"nama",
			"hp",
			"bagian",
			"jabatan",
			"",




			);


		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		$sql = " SELECT * from doc_team $filters order by $orderBy $sLimit ";
		// echo $sql;

		$res=db($sql);

		

		$json = array(

			"iTotalRecords" => mysql_num_rows($res),

			"iTotalDisplayRecords" => getField("SELECT COUNT(*) from doc_team $filters"),
			

			"aaData" => array(),

			);





		

		$no=intval($_GET['iDisplayStart']);

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		while($r=mysql_fetch_array($res)){
			$no++;

			$foto = "<a href = \"#\" onclick=\"openBox('view.php?doc=fotoTeam&par[id]=$r[id]".getPar($par,"mode")."',725,500);\"><img src = \"".$fFoto.$r[file]."\" height=\"50\"></a>";

			$controlKebutuhan = "";

			if (isset($menuAccess[$s]["edit"])) {
					$controlKebutuhan.= "<a onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]". getPar($par, "mode,idp") . "',825,500);\"  href=\"#\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";

					$controlKebutuhan.= "<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			}


			$data=array(

				"<div align=\"center\">".$no.".</div>",				

				"<div align=\"center\">".$foto."</div>",	

				"<div align=\"left\">$r[nama]</div>",

				"<div align=\"center\">$r[hp]</div>",
				"<div align=\"left\">$r[bagian]</div>",
				"<div align=\"left\">$r[jabatan]</div>",
				
				"<div align=\"center\">$controlKebutuhan</div>",		

				

				);





			$json['aaData'][]=$data;


		}

		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA TEAM ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP TEAM");
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "NAMA");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "NO HP");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "EMAIL");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "BAGIAN");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "JABATAN");
	
	$rows=5;
		
	$sql = " SELECT * FROM doc_team";

	$res=db($sql);
	while($r=mysql_fetch_array($res)){			
		$no++;
							
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[nama]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[hp]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[email]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[bagian]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[jabatan]);

		
		
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
	
	$objPHPExcel->getActiveSheet()->setTitle("DATA TEAM");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."DATA TEAM ".$sekarang.".xls");
}	




function form(){
	global $s,$inp,$par,$menuAccess,$fFoto,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM doc_team WHERE id='$par[id]'";
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
				<input type=\"text\" id=\"inp[nama]\" name=\"inp[nama]\"  value=\"$r[nama]\" class=\"mediuminput\" />
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">No. HP</label>
				<div class=\"field\">								
				<input type=\"text\" id=\"inp[hp]\" name=\"inp[hp]\" onkeyup=\"cekPhone(this);\" value=\"$r[hp]\" class=\"mediuminput\" />
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Email</label>
				<div class=\"field\">								
				<input type=\"text\" id=\"inp[email]\" name=\"inp[email]\"  value=\"$r[email]\" class=\"mediuminput\" />
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Bagian</label>
				<div class=\"field\">								
				<input type=\"text\" id=\"inp[bagian]\" name=\"inp[bagian]\"  value=\"$r[bagian]\" class=\"mediuminput\" />
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Jabatan</label>
				<div class=\"field\">								
				<input type=\"text\" id=\"inp[jabatan]\" name=\"inp[jabatan]\"  value=\"$r[jabatan]\" class=\"mediuminput\" />
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Foto</label>
				<div class=\"field\">";
					$text.=empty($r[file])?
					"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:360px;\">
						<input type=\"file\"  id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
					</div>":
					"<img src=\"".getIcon($r[file])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
					<a href=\"?par[mode]=delManual".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
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


function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){


		case "lst":

		$text=lData();

		break;	

		case "delManual":
		$text = hapusManual();
		break;

		case "del":
		if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
		break;

		case "add":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
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
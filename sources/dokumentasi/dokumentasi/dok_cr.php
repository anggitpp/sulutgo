<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fRencana = "files/dokumentasi/rencana/";

function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){
		case "lst":
		$text=lData();
		break;	

		case "cetak":
		$text = pdf();
		break;

		case "approve":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formApprove() : approve(); else $text = lihat();
		break;

		case "del":
		if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
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

function pdf(){
	global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
	require_once('plugins/TCPDF/tcpdf.php');

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf = new TCPDF ('P', 'mm', 'A4', true, 'UTF-8', false);

	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetMargins(5, 5, 5);

// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 1);

// add a page
	$pdf->AddPage();

// set font
	$pdf->SetFont('helvetica', '', 12);

	$sql = db("select * from catatan_sistem as a
		left join catatan_changereq as b on(a.idCatatan = b.id_temuan)
		where a.idCatatan = '$par[id]'");
	$r = mysql_fetch_assoc($sql);

	$content  = "";  
	$content .= "
	<table border=\"1\">  
		<tr>
			<th align=\"left\" width=\"200\"><b> PROJECT</b></th>
			<th align=\"center\" rowspan=\"4\" width=\"180\"><h2>CHANGES REQUEST</h2></th>
			<th align=\"center\" rowspan=\"2\"><b>CODE</b></th>
			<th align=\"center\" width=\"40\" rowspan=\"4\"><b>SCS 09</b></th>
		</tr>
		<tr>
			<th align=\"left\"><h2> $r[proyek]</h2></th>
		</tr>
		<tr>
			<th align=\"left\"><b> APPLICATION MODUL</b></th>
			<th align=\"center\" rowspan=\"2\"><b>CR</b></th>
		</tr>
		<tr>
			<th align=\"left\" rowspan=\"2\"><h2> $r[modul]</h2></th>
		</tr>
		<tr>
			<th align=\"center\">DATE  : 24-04-2019</th>
			<th align=\"center\" colspan=\"2\">NO :  $r[nomor]</th>
		</tr>
	</table>
	<br><br>
	<table border=\"1\">
		<tr>
			<td colspan=\"4\"><b> PERMINTAAN PERUBAHAN – PERMINTAAN BARU</b></td>
		</tr>
		<tr>
			<td> Nama<br> $r[user]<br></td>
			<td> ttd</td>
			<td> Jabatan - Unit<br> $r[user_jabatan]<br></td>
			<td> Tanggal</td>
		</tr>
		<tr>
			<td colspan=\"4\"><b> PERSETUJUAN ATASAN / PROJECT MANAGER</b></td>
		</tr>
		<tr>
			<td> Nama<br> $r[atasan]<br></td>
			<td> ttd</td>
			<td> Jabatan - Unit<br> $r[atasan_jabatan]<br></td>
			<td> Tanggal</td>
		</tr>
		<tr>
			<td colspan=\"4\"><b> DISKRIPSI PERMINTAAN</b></td>
		</tr>
		<tr>
			<td colspan=\"4\"> \t$r[uraian]<br></td>
		</tr>
		<tr>
			<td colspan=\"4\"> Detail Penjelasan<br> $r[penjelasan]<br></td>
		</tr>
		<tr>
			<td colspan=\"4\"> Letak Perubahan<br><br>";
				$sql = db("select kodeData, namaData From mst_data where kodeCategory = 'KLP'");
				while($r = mysql_fetch_assoc($sql)){
					$content.=" [&nbsp;&nbsp;] $r[namaData] &nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$content .= "
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=\"4\"> Tipe Permintaan<br><br>";
				$sql2 = db("select kodeData, namaData From mst_data where kodeCategory = 'KTP'");
				while($r2 = mysql_fetch_assoc($sql2)){
					$content.=" [&nbsp;&nbsp;] $r2[namaData] &nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$content .= "
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=\"4\"> Prioritas<br><br>";
				$sql3 = db("select kodeData, namaData From mst_data where kodeCategory = 'KP'");
				while($r3 = mysql_fetch_assoc($sql3)){
					$content.=" [&nbsp;&nbsp;] $r3[namaData] &nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$content .= "
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=\"4\"> Penjelasan Tambahan<br><br>";
				$sql4 = db("select kodeData, namaData From mst_data where kodeCategory = 'KPT'");
				while($r4 = mysql_fetch_assoc($sql4)){
					$content.=" [&nbsp;&nbsp;] $r4[namaData] &nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$content .= "
				<br>
			</td>
		</tr>
		<tr>
			<td colspan=\"4\"> Rekomendasi / Catatan<br></td>
		</tr>
		<tr>
			<td colspan=\"2\" rowspan=\"2\"> Permintaan Tanggal Implementasi<br> ".getTanggal($r[target])."</td>
			<td colspan=\"2\"> Pembiayaan Baru</td>
		</tr>
		<tr>
			<td> ADA</td>
			<td> TIDAK ADA</td>
		</tr>
	</table>";

// output the HTML content
	$pdf->writeHTML($content, true, true, true, true, '');

// reset pointer to the last page
	$pdf->lastPage();
// ---------------------------------------------------------
	ob_end_clean();
//Close and output PDF document
	$pdf->Output();
}

/*function pdf(){
	global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
	require_once 'plugins/PHPPdf.php';



	$pdf = new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetLeftMargin(15);

	$sql = db("select * from catatan_sistem as a
		left join catatan_changereq as b on(a.idCatatan = b.id_temuan)
		where a.idCatatan = '$par[id]'");
	$r = mysql_fetch_assoc($sql);

	$pdf->SetLeftMargin(15);
	$pdf->SetFont('Arial','',10);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("PROJECT\tb"));

	$pdf->SetFont('Arial','',14);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("".$r[proyek]."\tb"));

	$pdf->SetFont('Arial','',10);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("APPLICATION MODUL\tb"));

	$pdf->SetFont('Arial','',15);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("".$r[modul]."\tb"));
	$pdf->Ln(10);

	$pdf->SetLeftMargin(15);
	$pdf->SetFont('Arial','',10);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("PERMINTAAN PERUBAHAN PERMINTAAN BARU\tb"));
	
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(45,45,45,45));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Nama\n".$r[user]."","ttd\n","Jabatan - Unit\n".$r[user_jabatan]."","Tanggal\n"));	

	$pdf->SetFont('Arial','',10);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("PERSETUJUAN ATASAN / PROJECT MANAGER\tb"));
	
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(45,45,45,45));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Nama\n".$r[atasan]."","ttd\n","Jabatan - Unit\n".$r[atasan_jabatan]."","Tanggal\n"));	

	$pdf->SetFont('Arial','',10);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("DISKRIPSI PERMINTAAN\tb"));
	
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("".$r[proyek]." ".$r[modul].":\n".$r[uraian].""));

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Detail Penjelasan:\n".$r[penjelasan].""));

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Letak Perubahan\n\n"));	

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Tipe Permintaan\n\n"));

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Prioritas\n\n"));

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Penjelasan Tambahan\n\n"));

	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(180));
	$pdf->SetAligns(array('L'));
	$pdf->Row(array("Rekomendasi Catatan\n\n"));
	ob_end_clean();
	$pdf->Output(); 
}*/

function formApprove(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $arrParam, $cUsername;
	
	$sql="select * from nonpayroll_thr_setting where id = '$par[idt]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);
	
	$false =  $r["approve"] == "f" ? "checked=\"checked\"" : "";		
	$true =  empty($false) ? "checked=\"checked\"" : "";

	$r["approveBy"] = empty($r["approveBy"]) ? $cUsername : $r["approveBy"];
	
	$text = getValidation();
	
	$text.="

	<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
			".getBread(ucwords($par[mode]." data"))."
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Approve By</label>
						<div class=\"field\">

							<input type=\"text\" id=\"inp[approveBy]\" name=\"inp[approveBy]\"  value=\"".$r["approveBy"]."\" class=\"mediuminput\" style=\"width:100px;\" readonly/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"approveDate\" name=\"inp[approveDate]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r["approveDate"])."\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[approve]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[approve]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[approveKet]\" name=\"inp[approveKet]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">".$r["approveKet"]."</textarea>
						</div>
					</p>
					

				</div>
				

				<p style=\"position:absolute;top:5px;right:10px;\">
					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Submit\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>

				</p>

			</form>	
		</div>";
		return $text;
	}

	function approve(){
		global $db,$s,$inp,$par,$cUsername, $arrParam;

		$sql = "update catatan_sistem set approve = '".$inp["approve"]."', approveBy = '".$inp["approveBy"]."', approveKet = '".$inp["approveKet"]."', approveDate = '".setTanggal($inp["approveDate"])."' where idCatatan = '$par[idCatatan]' ";
		db($sql);
		echo "<script>closeBox();alert('DATA BERHASIL DIAPPROVE');reloadPage();</script>";
	}

	function ubah(){
		global $s, $inp, $par, $cUsername, $arrParam, $db, $cID;
		$id = getField("select id from catatan_changereq where id_temuan = '$par[id]'");
		$lastID = getField("select id from catatan_changereq order by id desc limit 1")+1;
		$perubahan = "$inp[perubahan0],$inp[perubahan1],$inp[perubahan2],$inp[perubahan3]";
		$tipe = "$inp[tipe0],$inp[tipe1],$inp[tipe2],$inp[tipe3]";
		$prioritas = "$inp[prioritas0],$inp[prioritas1],$inp[prioritas2]";
		$tambahan = "$inp[tambahan0],$inp[tambahan1],$inp[tambahan2],$inp[tambahan3]";
		if(empty($id)){
			// insert
			$sql = "INSERT INTO `catatan_changereq` (`id`, `id_temuan`, `proyek`, `modul`, `uraian`, `user`, `user_jabatan`, `atasan`, `atasan_jabatan`, `penjelasan`, `perubahan`, `tipe`, `prioritas`, `tambahan`, `catatan`, `target`, `biaya`, `created_date`, `created_by`) VALUES ('$lastID', '$par[id]', '$inp[proyek]', '$inp[modul]', '$inp[uraian]', '$inp[user]', '$inp[user_jabatan]', '$inp[atasan]', '$inp[atasan_jabatan]', '$inp[penjelasan]', '$perubahan', '$tipe', '$prioritas', '$tambahan', '$catatan', '".setTanggal($inp[target])."', '$inp[biaya]', now(), '$cID')";
		}else{
			// update
			$sql = "UPDATE `catatan_changereq` SET `id` = '$par[idcr]', `id_temuan` = '$par[id]', `proyek` = '$inp[proyek]', `modul` = '$inp[modul]', `uraian` = '$inp[uraian]', `user` = '$inp[user]', `user_jabatan` = '$inp[user_jabatan]', `atasan` = '$inp[atasan]', `atasan_jabatan` = '$inp[atasan_jabatan]', `penjelasan` = '$inp[penjelasan]', `perubahan` = '$perubahan', `tipe` = '$tipe', `prioritas` = '$prioritas', `tambahan` = '$tambahan', `catatan` = '$catatan', `target` = '".setTanggal($inp[target])."', `biaya` = '$inp[biaya]', `updated_date` = now(), `updated_by` = '$cID' WHERE `catatan_changereq`.`id` = $par[idcr]";
		}

		/*var_dump($sql);
		die();*/

		db($sql);

		echo "<script>alert('Data Berhasil Disimpan')</script>";
		echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
	}

	function lihat(){

		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;

		$cols=8;	
		if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
			$cols=9;	
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

					".comboData("SELECT * FROM mst_data WHERE kodeCategory = 'KP' AND statusData ='t' order by namaData","kodeData","namaData","bSearch","All Prioritas",$bSearch,"","210px;","chosen-select")."
				</p>
			</div>	
			<div id=\"pos_r\" style=\"float:right;\">
				<a href=\"#\" class=\"btn btn1 btn_inboxi\" style=\"margin-left:5px;\"><span>Export Data</span></a>
			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th  width=\"20\">No.</th>
					<th width=\"100\">Tanggal</th>
					<th width=\"100\">Nomor</th>
					<th  width=\"*\">Judul</th>
					<th width=\"100\">User</th>
					<th width=\"100\">Atasan</th>
					<th  width=\"70\">Status</th>
					<th  width=\"70\">Print</th>
					";if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th  width=\"50\">Detail</th>";
					$text.="

				</tr>
			</thead>

			<tbody></tbody>
		</table>

	</div>";
	$sekarang = date('Y-m-d');
	if($par[mode] == "xls"){
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=CHANGES REQUEST".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	return $text;

}



function lData(){
	global $s,$par,$fRencana,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	

	if($_GET[json]==1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	
	$kategori_catatan = getField("SELECT kodeData from mst_data where kodeMaster = 'CR'");

	$sWhere= " where t1.idCatatan is not null AND t1.kategori_catatan = '$kategori_catatan'";
	
	if (!empty($_GET['fSearch']))
		$sWhere.= " and (				
	lower(t1.Temuan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
	)";

	if (!empty($_GET['bSearch']))
		$sWhere.= " and (				
	lower(t3.prioritas) like '%".mysql_real_escape_string(strtolower($_GET['bSearch']))."%'
	)";

	$arrOrder = array(	
		"t1.Tanggal",
		"t1.Temuan",
		"t1.Temuan",
		"t1.Temuan",
		"",
		);


	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql = "select * from catatan_sistem t1 
	inner join app_user t2 on t1.createdBy = t2.username
	left join catatan_changereq t3 on t1.idCatatan = t3.id_temuan $sWhere order by $orderBy $sLimit";
	// echo $sql;

	$res=db($sql);
	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(t1.idCatatan) from catatan_sistem t1 left join catatan_changereq as t3 on t1.idCatatan = t3.id_temuan $sWhere"),
		"aaData" => array(),
		);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;
		if($r[approve] == 't'){
			$r[approve] = "<img src=\"styles/images/t.png\" title=\"Selesai\">";
		}else{
			$r[approve] = "<img src=\"styles/images/p.png\" title=\"Pending\">";
		}
		/*switch ($r[Status]) {
			case 't':
			$r[Status] = "<img src=\"styles/images/t.png\" title=\"Selesai\">";
			break;
			case '2':
			$r[Status] = "<img src=\"styles/images/p.png\" title=\"Pending\">";
			break;

			default:
			$r[Status] = "<img src=\"styles/images/f.png\" title=\"Belum\">";
			break;
		}*/

		$print = "<a href=\"#\" class=\"print\"></a>";
		$idcr=getField("select id from catatan_changereq where id_temuan = $r[idCatatan]");
		$data=array(
			"<div align=\"center\">".$no.".</div>",				
			"<div align=\"center\">".getTanggal($r[Tanggal])."</div>",
			"<div align=\"left\">".$r[nomor]."</div>",
			"<div align=\"left\">".$r[Temuan]."</div>",
			"<div align=\"left\">".$r[user]."</div>",
			"<div align=\"left\">".$r[atasan]."</div>",
			"<div align=\"center\" onclick=\"openBox('popup.php?par[mode]=approve&par[idCatatan]=$r[idCatatan]".getPar($par,"mode")."',725,450);\">".$r[approve]."</div>",
			
			"<div align=\"center\" onclick=\"openBox('popup.php?par[mode]=cetak&par[id]=$r[idCatatan]&par[idcr]=$idcr".getPar($par,"mode,id,idcr")."',925,550);\">".$print."</div>",

			"<div align=\"center\"><a href=\"index.php?par[mode]=edit&par[id]=$r[idCatatan]&par[idcr]=$idcr".getPar($par,"mode,id,idcr")."\" class=\"edit\"></a></div>"		
			);
		$json['aaData'][]=$data;
	}
	return json_encode($json);
}

function form(){
	global $s,$inp,$par,$menuAccess,$fRencana,$cUsername,$arrTitle;	

	$sql="SELECT * FROM catatan_sistem where idCatatan = '$par[id]'";
	
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	$sql_="SELECT * FROM catatan_changereq WHERE id = '$par[idcr]'";
	$res_=db($sql_);
	$r2=mysql_fetch_assoc($res_);
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
				<legend> REQUEST </legend>

				<p>
					<label class=\"l-input-small\">Nomor</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"$r[nomor]\" class=\"mediuminput\" style=\"width:220px;\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tanggal]\" name=\"inp[tanggal]\"  value=\"".getTanggal($r[Tanggal])."\" class=\"hasDatePicker\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Nama Proyek</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[proyek]\" name=\"inp[proyek]\"  value=\"$r2[proyek]\" class=\"mediuminput\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Modul</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[modul]\" name=\"inp[modul]\"  value=\"$r2[modul]\" class=\"mediuminput\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Judul</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"$r[Temuan]\" class=\"mediuminput\" readonly/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Uraian</label>
					<textarea id=\"inp[uraian]\" name=\"inp[uraian]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">".$r2[uraian]."</textarea>
				</p>

				<table style=\"width:100%\">

					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">User Pengusul</label>
								<input type=\"text\" id=\"inp[user]\" name=\"inp[user]\"  value=\"$r2[user]\" style=\"width:150px;\" class=\"mediuminput\"/>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<input type=\"text\" id=\"inp[user_jabatan]\" name=\"inp[user_jabatan]\"  value=\"$r2[user_jabatan]\" style=\"width:150px;\" class=\"mediuminput\"/>
							</p>
						</td>
					</tr>
					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Project Manager</label>
								<input type=\"text\" id=\"inp[atasan]\" name=\"inp[atasan]\"  value=\"$r2[atasan]\" style=\"width:150px;\" class=\"mediuminput\"/>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<input type=\"text\" id=\"inp[atasan_jabatan]\" name=\"inp[atasan_jabatan]\"  value=\"$r2[atasan_jabatan]\" style=\"width:150px;\" class=\"mediuminput\"/>
							</p>
						</td>
					</tr>
				</table>			
			</fieldset>

			<fieldset>
				<legend> ANALISA </legend>
				<p>
					<label class=\"l-input-small\">Detail Penjelasan</label>
					<br><br>
					<span class=\"fieldB\">
						<textarea class=\"tinymce\" name=\"inp[penjelasan]\">$r2[penjelasan]</textarea>
					</span>
				</p>
			</fieldset>

			<fieldset>
				<legend> KATEGORI </legend>
				<table style=\"width:100%\">

					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Letak Perubahaan</label>
								<br style=\"clear:both; margin-bottom:10px;\">";

								$sql = db("select kodeData, namaData From mst_data where kodeCategory = 'KLP'");
								$data = explode(",",$r2[perubahan]);
								$no = -1;
								while($r = mysql_fetch_assoc($sql)){
									$no++;
									$text.="<input type=\"checkbox\" name=\"inp[perubahan$no]\" value=\"$r[kodeData]\" ".(empty($data[$no])?"":"checked")."> $r[namaData] </br>";
								}

								$text.="
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Tipe Permintaan</label>
								<br style=\"clear:both; margin-bottom:10px;\">";

								$sql= db("select kodeData, namaData From mst_data where kodeCategory = 'TPRT'");
								$data2 = explode(",",$r2[tipe]);
								$no2 = -1;
								while($r = mysql_fetch_assoc($sql)){
									$no2++;
									$text.="<input type=\"checkbox\" name=\"inp[tipe$no2]\" value=\"$r[kodeData]\" ".(empty($data2[$no2])?"":"checked")."> $r[namaData] </br>";
								}

								$text.="
							</p>
						</td>
					</tr>
					
				</table>

				<table style=\"width:100%\">

					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-medium\">Prioritas</label>
								<br style=\"clear:both; margin-bottom:10px;\">";

								$sql= db("select kodeData, namaData From mst_data where kodeCategory = 'KP'");
								$data3 = explode(",",$r2[prioritas]);
								$no3 = -1;
								while($r = mysql_fetch_assoc($sql)){
									$no3++;
									$text.="<input type=\"checkbox\" name=\"inp[prioritas$no3]\" value=\"$r[kodeData]\" ".(empty($data3[$no3])?"":"checked")."> $r[namaData] </br>";
								}

								$text.="
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-medium\">Penjelasan Tambahan</label>
								<br style=\"clear:both; margin-bottom:10px;\">";

								$sql= db("select kodeData, namaData From mst_data where kodeCategory = 'KPT'");
								$data4 = explode(",",$r2[tambahan]);
								$no4 = -1;
								while($r = mysql_fetch_assoc($sql)){
									$no4++;
									$text.="<input type=\"checkbox\" name=\"inp[tambahan$no4]\" value=\"$r[kodeData]\" ".(empty($data4[$no4])?"":"checked")."> $r[namaData] </br>";
								}

								$text.="
							</p>
						</td>
					</tr>
					
				</table>
			</fieldset>

			<fieldset>
				<legend> REKOMENDASI </legend>
				<table style=\"width:100%\">

					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small2\">Rencana Implementasi</label>
								<div class=\"field\">
									<input type=\"text\" id=\"inp[target]\" name=\"inp[target]\"  value=\"".getTanggal($r2[target])."\" class=\"hasDatePicker\"/>
								</div>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\">Biaya</label>
								<input type=\"radio\" name=\"inp[biaya]\"  value=\"1\" class=\"mediuminput\" checked/> Ya

								<input type=\"radio\" name=\"inp[biaya]\"  value=\"0\" class=\"mediuminput\"/> Tidak
							</p>
						</td>
					</tr>
					
				</table>
			</fieldset>

			<p align=\"right\" style=\"margin-top:20px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</p>
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
?>
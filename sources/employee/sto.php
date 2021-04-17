<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$dFile = "files/emp/sto/";	

	function uploadFile($id) {
	global $s, $inp, $par, $dFile;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $dFile);
		$foto_file = "sto-" . time() . "." . getExtension($fileUpload_name);
		fileRename($dFile, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from emp_struktur where id ='$id'");

	return $foto_file;
}

function hapusFile() {
	global $s, $inp, $par, $dFile, $cUsername;

	$foto_file = getField("select file from emp_struktur where id='$par[id]'");
	if (file_exists($dFile . $foto_file) and $foto_file != "")
		unlink($dFile . $foto_file);

	$sql = "update emp_struktur set file='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from emp_struktur where id='$par[id]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=lihat2".getPar($par,"mode,id")."';</script>";
	}
	
	
function tambah(){

	global $s, $inp, $par, $cUsername, $arrProduk, $cUsername;
	repField();
	$file = uploadFile($id);
	$id = getField("select id from emp_struktur order by id desc limit 1") + 1;
	$sql = "insert into emp_struktur (id, file, keterangan, status, createDate, createBy) values ('$id','$file', '$inp[keterangan]', '$inp[status]', '".date('Y-m-d H:i:s')."', '$cUsername')";
	// echo $sql;
	// die();
	db($sql);

	
	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
}

function ubah(){

	global $s, $inp, $par, $cUsername, $arrProduk;
	repField();
	$file = uploadFile($par[id]);
	$sql = "update emp_struktur set file='$file', status='$inp[status]',keterangan='$inp[keterangan]', status='$inp[status]', updateBy = '$cUsername', updateDate = '".date('Y-m-d H:i:s')."' where id='$par[id]'";
	// echo $sql;
	// die();
	db($sql);

	echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";

}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$dFile;
		
		$sql="select * from emp_struktur where id='$par[id]'";
		// echo $sql;
		$res=db($sql);
		$r=mysql_fetch_array($res);		

		$true = $r[status] == "t" ? "checked=\"checked\"" : "";
		$false = empty($true) ? "checked=\"checked\"" : "";			
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
										
		</div>
		<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
		<br clear=\"all\"/>
			<p>
				<label class=\"l-input-small\">Foto</label>
				<div class=\"field\">";
					$text.=empty($r[file])?
					"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:285px;\" />
					<div class=\"fakeupload\">
						<input type=\"file\" id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
					</div>":
					"<img src=\"".$dFile."".$r[file]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
					<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";
					$text.="
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<div class=\"field\">
					<textarea id=\"inp[keterangan]\" name=\"inp[keterangan]\"  rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:60%;\">$r[keterangan]</textarea>
				</div>
			</p>	
			<p>
				<label class=\"l-input-small\" >Status</label>
				<div class=\"field\" >     
					<input type=\"radio\" id=\"true\" name=\"inp[status]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
					<input type=\"radio\" id=\"false\" name=\"inp[status]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>       
				</div>
			</p>
			
		<p style=\"position:absolute;right:25px;top:15px;\">					
		<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
		<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
		</p>
		</form>";
		return $text;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck, $cGroup,$dFile;
		$par[idPegawai] = $cID;
		if(empty($par[tahunCuti])) $par[tahunCuti]=date('Y');
		$sql = "select * from emp_struktur order by createDate desc LIMIT 1";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$r[file] = empty($r[file]) ? "<img src=\"files/emp/sto/2.png\" width=\"100%\" >" : "<img src=\"".$dFile.$r[file]."\" width=\"100%\">";

		$text.="
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>
	
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute;top:10px;right:25px;\">
		<a href=\"download.php?d=sto&f=$r[id]\" class=\"btn btn1 btn_document\"/><span>Download</span></a>	
		<a href=\"?par[mode]=lihat2".getPar($par,"mode,idCuti")."\" class=\"btn btn1 btn_document\"/><span>Edit Data</span></a>		
		</div>
		$r[file]
		</form>
		</div>";
		return $text;
	}		
	

	function lihat2(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck, $cGroup, $dFile;
		$par[idPegawai] = $cID;
		if(empty($par[tahunCuti])) $par[tahunCuti]=date('Y');
		$text.="
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>
		
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
		
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idCuti")."',700,300)\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>
		<th width=\"150\">Tanggal</th>
		<th width=\"*\">Keterangan</th>
		<th width=\"100\">Size</th>
		<th width=\"50\">View</th>
		<th width=\"50\">D/L</th>
		<th width=\"50\">Status</th>
		";
		if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
	
		</thead>
		<tbody>";
		
		$sql="select *, date(createDate) as tanggal from emp_struktur";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$size = ceil(filesize($dFile.$r[file]) / 1024)." KB";
			$r[status] = $r[status] == "t"? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak\">";
			$text.="<tr>
			<td>$no.</td>
			
			<td align=\"center\">".$r[tanggal]."</td>
			<td align=\"left\">".$r[keterangan]."</td>
			<td align=\"center\">".$size."</td>
			
			<td align=\"center\"><a href=\"#\" onclick=\"openBox('view.php?doc=sto&id=$r[id]',1000,500);\" class=\"detail\"><span>Detail</span></a></td>
			<td align=\"center\"><a href=\"download.php?d=sto&f=$r[id]\"/><img src=\"".getIcon($r[file])."\" ></a></td>					
			
			<td align=\"center\">$r[status]</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";	
				if(isset($menuAccess[$s]["edit"])) $text.="<a onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode,id")."',700,300)\" href=\"#\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";
		}	
		
		$text.="</tbody>
		</table>
		</div>";
		return $text;
	}	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		if(empty($par[tahunPinjaman])) $par[tahunPinjaman]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		
		$sql="select * from att_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
		if(empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');		
		
		$sql_="select
		id as idPegawai,
		reg_no as nikPegawai,
		name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."								
		</div>
		<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
		<div id=\"general\" style=\"margin-top:20px;\">
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Nomor</label>
		<span class=\"field\">".$r[nomorCuti]."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">NPP</label>
		<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Nama</label>
		<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
		</p>
		</td>
		<td width=\"55%\">
		<p>
		<label class=\"l-input-small\">Tanggal</label>
		<span class=\"field\">".getTanggal($r[tanggalCuti],"t")."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Jabatan</label>
		<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Divisi</label>
		<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
		</p>
		</td>
		</tr>
		</table>
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Tipe Cuti</label>
		<span class=\"field\">
		".getField("select namaCuti from dta_cuti where idCuti='".$r[idTipe]."'")."&nbsp;
		</span>
		</p>
		<p>
		<label class=\"l-input-small\">Tanggal Cuti</label>
		<span class=\"field\">
		".getTanggal($r[mulaiCuti], "t")." <strong>s.d</strong> ".getTanggal($r[selesaiCuti], "t")."
		</span>
		</p>
		<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
		<p>
		<label class=\"l-input-small\">Jatah Cuti</label>
		<span>
		<div style=\"float:left; width:125px;\">".getAngka($r[jatahCuti])." hari</div>
		<span>
		<label class=\"l-input-small\">&nbsp;&nbsp;&nbsp;Pengambilan</label>
		".getAngka($r[jumlahCuti])." hari
		</span>
		</span>
		</p>
		</div>											
		<p>
		<label class=\"l-input-small\">Sisa Cuti</label>
		<span class=\"field\">
		".getAngka($r[sisaCuti])." hari
		</span>
		</p>
		</td>
		<td width=\"55%\">";
		
		$sql_="select
		id as idPengganti,
		reg_no as nikPengganti,
		name as namaPengganti
		from emp where id='".$r[idPengganti]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);					
		$text.="<p>
		<label class=\"l-input-small\">Pengganti</label>
		<span class=\"field\">".$r_[nikPengganti]." - ".$r_[namaPengganti]."&nbsp;</span>
		</p>";
		
		
		$sql_="select
		id as idAtasan,
		reg_no as nikAtasan,
		name as namaAtasan
		from emp where id='".$r[idAtasan]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		
		$text.="<p>
		<label class=\"l-input-small\">Atasan</label>
		<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">
		".nl2br($r[keteranganCuti])."&nbsp;
		</span>
		</p>
		</td>
		</tr>
		</table>";
		
		$persetujuanCuti = "Belum Diproses";
		$persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
		$persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;	
		$persetujuanCuti = $r[persetujuanCuti] == "r" ? "Diperbaiki" : $persetujuanCuti;	
		
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
		</div>			
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Status</label>
		<span class=\"field\">".$persetujuanCuti."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[catatanCuti])."&nbsp;</span>
		</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
		</tr>
		</table>";
		
		$sdmCuti = "Belum Diproses";
		$sdmCuti = $r[sdmCuti] == "t" ? "Disetujui" : $sdmCuti;
		$sdmCuti = $r[sdmCuti] == "f" ? "Ditolak" : $sdmCuti;		
		$sdmCuti = $r[sdmCuti] == "r" ? "Diperbaiki" : $sdmCuti;	
		
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
		</div>			
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Status</label>
		<span class=\"field\">".$sdmCuti."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[noteCuti])."&nbsp;</span>
		</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
		</tr>
		</table>";
		
		$text.="</div>
		<p>					
		<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\"/>		
		</p>
		</form>";
		return $text;
	}
	
	function pegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID,$cGroup, $areaCheck;
		$par[idPegawai] = $cID;		
		$text.="
		<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Daftar Pegawai</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>    
		" . empLocHeader() . "
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
		<table>
		<tr>
		<td>Search : </td>
		<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
		<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
		<td>
		<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
		<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
		</td>
		</tr>
		</table>
		</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>
		<th style=\"min-width:150px;\">Nama</th>
		<th width=\"100\">NPP</th>
		<th style=\"min-width:150px;\">Jabatan</th>					
		<th style=\"min-width:150px;\">Divisi</th>
		<th width=\"50\">Kontrol</th>
		</tr>
		</thead>
		<tbody>";
		
		if($cGroup != 1)
		$filter = "where reg_no is not null and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
		else
		$filter = "where reg_no is not null";
		
		if($par[search] == "Nama")
		$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
		$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
		$filter.= " and (
		lower(t1.name) like '%".strtolower($par[filter])."%'
		or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
		)";		
		
		$filter .= " AND t2.location IN ($areaCheck)";
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
		$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$text.="<tr>
			<td>$no.</td>
			<td>".strtoupper($r[name])."</td>
			<td>$r[reg_no]</td>
			<td>$r[pos_name]</td>
			<td>".$arrDivisi["$r[div_id]"]."</td>
			<td align=\"center\">
			<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
			</td>
			</tr>";
		}	
		
		$text.="</tbody>
		</table>
		</div>
		</div>";
		return $text;
	}
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from att_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$tanggalCuti = getTanggal($r[mulaiCuti], "t");
		if($r[mulaiCuti] != $r[selesaiCuti]) $tanggalCuti.= " - ".getTanggal($r[selesaiCuti], "t");
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(15);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',12);					
		$pdf->Cell(20,6,'PRATAMA MITRA SEJATI',0,0,'L');
		$pdf->Ln();		
		
		$pdf->SetFont('Arial','BU',14);
		$pdf->Cell(180,6,'PERMOHONAN CUTI / IZIN',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','BI',10);
		$pdf->Cell(180,6,'LEAVE APPLICATION / PERMISSION',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(180,6,'Nomor : '.$r[nomorCuti],0,0,'C');
		$pdf->Ln(20);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Nama Karyawan',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Employees Name',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,getField("select name from emp where id='".$r[idPegawai]."'"),0,'L');
		$pdf->Ln(3.5);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Jabatan',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Position',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
		$pdf->Ln(3.5);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Departemen',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Department',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
		$pdf->Ln(3.5);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Tipe Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Type of leave',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,getField("select namaCuti from dta_cuti where idCuti='".$r[idTipe]."'"),0,'L');
		$pdf->Ln(3.5);
		
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Jumlah Hak Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Sum of the leave rights',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[jatahCuti]),0,'R');		
		$pdf->SetXY($setX+70, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');				
		$pdf->Ln(8);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Jumlah Cuti Diambil',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Sum of leave in taken',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[jumlahCuti]),0,'R');		
		$pdf->SetXY($setX+70, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');				
		$pdf->Ln(8);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Sisa Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Rest of leave',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[sisaCuti]),0,'R');		
		$pdf->SetXY($setX+70, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');				
		$pdf->Ln(8);
					
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Tanggal Awal Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Date of early leave',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(125,6,$tanggalCuti,0,'L');				
		$pdf->Ln(3.5);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Alasan Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Reason of leave',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,$r[keteranganCuti],0,'L');
		$pdf->Ln(20);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
		$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
		$pdf->Cell(60,3,'Pemohon Cuti,',0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','I');
		$pdf->Cell(60,6,'Approved by,',0,0,'C');
		$pdf->Cell(60,6,'Approved by,',0,0,'C');
		$pdf->Cell(60,6,'Leave applicant,',0,0,'C');
		$pdf->Ln(20);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(60,5,'Maman Somantri',0,0,'C');
		$pdf->Cell(60,5,'                                ',0,0,'C');
		$pdf->Cell(60,5,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial');
		$pdf->Cell(60,3,'Human Resource Manager',0,0,'C');
		$pdf->Cell(60,3,'Head of Department',0,0,'C');
		$pdf->Cell(60,3,'',0,0,'C');
		
		$pdf->Ln();
		$pdf->Cell(60,6,'Tgl/Date :                            ',0,0,'C');
		$pdf->Cell(60,6,'Tgl/Date :                 ',0,0,'C');
		$pdf->Cell(60,6,'Tgl/Date :  '.getTanggal($r[tanggalCuti],"t"),0,0,'C');
		$pdf->AutoPrint(true);
		
		$pdf->Output();	
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "no":
			$text = gNomor();
			break;
			case "get":
			$text = gPegawai();
			break;
			case "lihat2":
			$text = lihat2();
			break;
			case "cut":
			$text = gCuti();
			break;
			case "peg":
			$text = pegawai();
			break;
			case "det":
			$text = detail();
			break;
			case "delFile":
			$text = hapusFile();
			break;
			case "print":
			$text = pdf();
			break;
			case "sdm":
			if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
			if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
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
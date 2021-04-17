<?php
require_once '../global.php';
require_once '../plugins/PHPPdf.php';
$arrApproval = array("p" => "Belum Diproses", "t" => "Disetujui", "f" => "Ditolak", "r" => "Diperbaiki");

$sql="SELECT * FROM att_hadir WHERE idHadir='$par[idHadir]'";
$res=db($sql);
$r=mysql_fetch_array($res);	

$sql_="
SELECT
id AS idPegawai, reg_no AS nikPegawai, name AS namaPegawai
FROM emp 
WHERE id='".$r[idPegawai]."'";
$res_=db($sql_);
$r_=mysql_fetch_array($res_);

$sql__="SELECT * FROM emp_phist WHERE parent_id='".$r_[idPegawai]."' AND status='1'";
$res__=db($sql__);
$r__=mysql_fetch_array($res__);
$r_[namaJabatan] = $r__[pos_name];
$r_[namaDivisi] = getField("SELECT namaData FROM mst_data WHERE kodeData='".$r__[div_id]."'");
$sqlPengganti="
SELECT
id AS idPengganti, reg_no AS nikPengganti, name AS namaPengganti
FROM emp 
WHERE id='".$r[idPengganti]."'";
$resPengganti=db($sqlPengganti);
$rPengganti=mysql_fetch_array($resPengganti);	

$sqlAtasan="
SELECT
id AS idAtasan, reg_no AS nikAtasan, name AS namaAtasan
FROM emp 
WHERE id='".$r[idAtasan]."'";
$resAtasan=db($sqlAtasan);
$rAtasan=mysql_fetch_array($resAtasan);	

$isAllDay = FALSE;
list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);
if($mulaiHadir_tanggal != $selesaiHadir_tanggal){
	$isAllDay = TRUE;
}else{
	if($r[hariHadir] == "t"){
		$isAllDay = TRUE;
	}else{				
		$isAllDay = FALSE;
	}	
}

$pdf = new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetLeftMargin(5);

// start-header
$pdf->Ln();		
$pdf->SetFont('Arial','B',20);
$pdf->Cell(100,7,$arrTitle[$s],0,0,'L');
$pdf->Line(6, 19, 204, 19);
$pdf->Ln(11);
// end-header

// start-content
$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(29, 5, 55, 0, 35, 5, 65));		
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("NOMOR\tb",":",$r[nomorHadir],"","TANGGAL PENGAJUAN\tb",":",getTanggal($r[tanggalHadir], "t")), false);
$pdf->Row(array("NIK\tb",":",$r_[nikPegawai],"","JABATAN\tb",":",$r_[namaJabatan]), false);
$pdf->Row(array("NAMA\tb",":",$r_[namaPegawai],"","DIVISI\tb",":",$r_[namaDivisi]), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"DATA IZIN KETIDAKHADIRAN",0,0,'L');
$pdf->Line(6, 52, 204, 52);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(29, 5, 55, 0, 35, 5, 65));		
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("TANGGAL MULAI\tb",":",getTanggal($mulaiHadir_tanggal, "t"),"","PENGGANTI\tb",":",$rPengganti[nikPengganti] . " - " . $rPengganti[namaPengganti]), false);
$pdf->Row(array("TANGGAL SELESAI\tb",":",getTanggal($selesaiHadir_tanggal, "t"),"","ATASAN\tb",":",$rAtasan[nikAtasan] . " - " . $rAtasan[namaAtasan]), false);
if(!$isAllDay){
	$pdf->Row(array("JAM MULAI\tb",":",substr($mulaiHadir_waktu,0,5),"","KATEGORI IZIN\tb",":",getField("SELECT namaData FROM mst_data WHERE kodeData = '$r[idKategori]'")), false);
	$pdf->Row(array("JAM SELESAI\tb",":",substr($selesaiHadir_waktu,0,5),"","KETERANGAN\tb",":",""), false);
}else{
	$pdf->Row(array("KETERANGAN\tb",":",$r[keteranganHadir],"","KATEGORI IZIN\tb",":",getField("SELECT namaData FROM mst_data WHERE kodeData = '$r[idKategori]'")), false);
}

$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"DATA APPROVAL",0,0,'L');
if(!$isAllDay){
	$pdf->Line(6, 91, 204, 91);
}else{
	$pdf->Line(6, 85, 204, 85);
}
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(29, 5, 55, 0, 35, 5, 65));		
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("ATASAN\tb",":",getField("SELECT t2.name FROM app_user t1 JOIN emp t2 ON t2.id = t1.idPegawai WHERE t1.username = '$r[approveBy]'"),"","TATA USAHA\tb",":",getField("SELECT t2.name FROM app_user t1 JOIN emp t2 ON t2.id = t1.idPegawai WHERE t1.username = '$r[sdmBy]'")), false);
$pdf->Row(array("APPROVE\tb",":",$arrApproval["$r[persetujuanHadir]"],"","APPROVE\tb",":",$arrApproval["$r[sdmHadir]"]), false);
$pdf->ln();
// end-content

$pdf->Output(strtoupper($arrTitle[$s] . "_" . $r_[namaPegawai] . "_" . date("Ymd", strtotime($r[tanggalCuti]))) . ".pdf", 'D');	

/* End of file pdf.cuti.php */
/* Location: .//C/xampp/htdocs/sariater/hrms/tmpl/pdf.cuti.php */
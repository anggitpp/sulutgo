<?php
require_once '../global.php';
require_once '../plugins/PHPPdf.php';
$arrApproval = array("p" => "Belum Diproses", "t" => "Disetujui", "f" => "Ditolak", "r" => "Diperbaiki");

$sql="SELECT * FROM att_lembur WHERE idLembur='$par[idLembur]'";
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

list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
list($approveDate) = explode(" ", $r[approveTime]);
list($sdmDate) = explode(" ", $r[sdmTime]);

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
$pdf->SetWidths(array(32, 5, 55, 0, 35, 5, 65));	
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("NOMOR\tb",":",$r[nomorLembur],"","TANGGAL PENGAJUAN\tb",":",getTanggal($r[tanggalLembur], "t")), false);
$pdf->Row(array("NIK\tb",":",$r_[nikPegawai],"","JABATAN\tb",":",$r_[namaJabatan]), false);
$pdf->Row(array("NAMA\tb",":",$r_[namaPegawai],"","DIVISI\tb",":",$r_[namaDivisi]), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"DATA IZIN LEMBUR",0,0,'L');
$pdf->Line(6, 52, 204, 52);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(32, 5, 55, 0, 35, 5, 65));	
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("TANGGAL\tb",":",getTanggal($mulaiLembur_tanggal, "t"),"","ATASAN\tb",":",$rAtasan[nikAtasan] . " - " . $rAtasan[namaAtasan]), false);
$pdf->Row(array("WAKTU\tb",":",substr($mulaiLembur,0,5) . " s.d " . substr($selesaiLembur,0,5),"","KETERANGAN\tb",":",$r[keteranganLembur]), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"APPROVAL ATASAN",0,0,'L');
$pdf->Line(6, 79, 204, 79);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(32, 5, 167));	
$pdf->SetAligns(array('L','L','L'));
$pdf->Row(array("TANGGAL\tb",":", getTanggal($approveDate,"t")), false);
$pdf->Row(array("NAMA\tb",":", getField("SELECT t2.name FROM app_user t1 JOIN emp t2 ON t2.id = t1.idPegawai WHERE t1.username = '$r[approveBy]'")), false);
$pdf->Row(array("STATUS\tb",":", $arrApproval["$r[persetujuanLembur]"]), false);
$pdf->Row(array("KETERANGAN\tb",":", $r[catatanLembur]), false);
$pdf->Row(array("OVERTIME\tb",":", getAngka($r[overtimeLembur]) . " Jam"), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"APPROVAL TATA USAHA",0,0,'L');
$pdf->Line(6, 124, 204, 124);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(32, 5, 167));	
$pdf->SetAligns(array('L','L','L'));
$pdf->Row(array("TANGGAL\tb",":", getTanggal($sdmDate,"t")), false);
$pdf->Row(array("NAMA\tb",":", getField("SELECT t2.name FROM app_user t1 JOIN emp t2 ON t2.id = t1.idPegawai WHERE t1.username = '$r[sdmBy]'")), false);
$pdf->Row(array("STATUS\tb",":", $arrApproval["$r[sdmLembur]"]), false);
$pdf->Row(array("KETERANGAN\tb",":", $r[noteLembur]), false);
$pdf->Row(array("OVERTIME\tb",":", getAngka($r[overtimeLembur]) . " Jam"), false);
$pdf->ln();
// end-content

$pdf->Output(strtoupper($arrTitle[$s] . "_" . $r_[namaPegawai] . "_" . date("Ymd", strtotime($r[tanggalLembur]))) . ".pdf", 'D');	

/* End of file pdf.cuti.php */
/* Location: .//C/xampp/htdocs/sariater/hrms/tmpl/pdf.cuti.php */
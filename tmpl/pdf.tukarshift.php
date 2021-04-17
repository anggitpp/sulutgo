<?php
require_once '../global.php';
require_once '../plugins/PHPPdf.php';
$arrApproval = array("p" => "Belum Diproses", "t" => "Disetujui", "f" => "Ditolak", "r" => "Diperbaiki");

$sql="SELECT * FROM att_shift WHERE idShift='$par[idShift]'";
$res=db($sql);
$r=mysql_fetch_array($res);					

$sqlPegawai="
SELECT
id AS idPegawai, reg_no AS nikPegawai, upper(name) AS namaPegawai
FROM emp 
WHERE id='".$r[idPegawai]."'";
$resPegawai=db($sqlPegawai);
$rPegawai=mysql_fetch_array($resPegawai);

$sqlPegawai2="SELECT * FROM emp_phist WHERE parent_id='".$rPegawai[idPegawai]."' AND status='1'";
$resPegawai2=db($sqlPegawai2);
$rPegawai2=mysql_fetch_array($resPegawai2);
$rPegawai[namaJabatan] = $rPegawai2[pos_name];
$rPegawai[namaDivisi] = getField("SELECT namaData FROM mst_data WHERE kodeData='".$rPegawai2[div_id]."'");

$sqlPengganti="
SELECT
id AS idPegawai, reg_no AS nikPegawai, upper(name) AS namaPegawai
FROM emp 
WHERE id='".$r[idPengganti]."'";
$resPengganti=db($sqlPengganti);
$rPengganti=mysql_fetch_array($resPengganti);

$sqlPenggant2="SELECT * FROM emp_phist WHERE parent_id='".$rPengganti[idPegawai]."' AND status='1'";
$resPengganti2=db($sqlPenggant2);
$rPengganti2=mysql_fetch_array($resPengganti2);
$rPengganti[namaJabatan] = $rPengganti2[pos_name];
$rPengganti[namaDivisi] = getField("SELECT namaData FROM mst_data WHERE kodeData='".$rPengganti2[div_id]."'");

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
$pdf->SetWidths(array(45, 5, 55, 0, 40, 5, 65));	
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("NOMOR\tb",":",$r[nomorShift],"","KETERANGAN\tb",":",$r[keteranganShift]), false);
$pdf->Row(array("TUKAR SHIFT PADA TANGGAL\tb",":",getTanggal($r[tanggalShift], "t"),"","","",""), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"TUKAR SHIFT",0,0,'L');
$pdf->Line(6, 46, 204, 46);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(45, 5, 55, 0, 40, 5, 65));	
$pdf->SetAligns(array('L','L','L','L','L','L','L'));
$pdf->Row(array("NIK\tb",":",$rPegawai[nikPegawai],"","TUKAR SHIFT DENGAN NIK\tb",":",$rPengganti[nikPegawai]), false);
$pdf->Row(array("NAMA\tb",":",$rPegawai[namaPegawai],"","NAMA\tb",":",$rPengganti[namaPegawai]), false);
$pdf->Row(array("JABATAN\tb",":",$rPegawai[namaJabatan],"","JABATAN\tb",":",$rPengganti[namaJabatan]), false);
$pdf->Row(array("DIVISI\tb",":",$rPegawai[namaDivisi],"","DIVISI\tb",":",$rPengganti[namaDivisi]), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"APPROVAL ATASAN",0,0,'L');
$pdf->Line(6, 85, 204, 85);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(45, 5, 154));	
$pdf->SetAligns(array('L','L','L'));
$pdf->Row(array("STATUS\tb",":", $arrApproval["$r[persetujuanShift]"]), false);
$pdf->Row(array("KETERANGAN\tb",":", $r[catatanLembur]), false);
$pdf->ln();

$pdf->SetFont('Arial','B',10);
$pdf->Cell(100,7,"APPROVAL TATA USAHA",0,0,'L');
$pdf->Line(6, 112, 204, 112);
$pdf->ln(9);

$pdf->SetFont('Arial','','8');		
$pdf->SetWidths(array(45, 5, 154));	
$pdf->SetAligns(array('L','L','L'));
$pdf->Row(array("STATUS\tb",":", $arrApproval["$r[sdmShift]"]), false);
$pdf->Row(array("KETERANGAN\tb",":", $r[catatanLembur]), false);
$pdf->ln();
// end-content

$pdf->Output(strtoupper($arrTitle[$s] . "_" . $rPegawai[namaPegawai] . "_" . date("Ymd", strtotime($r[tanggalLembur]))) . ".pdf", 'D');	

/* End of file pdf.cuti.php */
/* Location: .//C/xampp/htdocs/sariater/hrms/tmpl/pdf.cuti.php */
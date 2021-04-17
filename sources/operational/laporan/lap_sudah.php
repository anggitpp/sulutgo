<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lData(){
	global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

	$sWhere= " where t1.statusPelatihan='t'";	
	if (!empty($_GET['pSearch']))
		$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				

	$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $sWhere order by t1.idPelatihan");		
	$cntEvaluasi = arrayQuery("select idPegawai, count(*) from plt_pertanyaan_evaluasi where idPelatihan='".$_GET['pSearch']."' group by 1");
	
	list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t",getField("select concat(count(t2.idJadwal), '\t', sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere"));
	$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
	$menitPelatihan = $menitPelatihan%60;			
	$waktuPelatihan = $menitPelatihan > 0 ?
	getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
	getAngka($jamPelatihan)." Jam";
	$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan > 1 ? getAngka($menitPelatihan)." Menit": $waktuPelatihan;
	$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan < 1 ? "" : $waktuPelatihan;
	
	$arrAbsensi = arrayQuery("select t2.idPeserta, count(t2.statusAbsensi) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere and t2.statusAbsensi='t' group by 1");		
	
	$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $sWhere order by t1.idPelatihan";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$arrJawaban = explode("\t", $r[evaluasiJawaban]);
		$nilaiPelatihan = 0;
		if(is_array($arrJawaban)){
			reset($arrJawaban);
			while(list($i,$d)=each($arrJawaban)){
				$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
				$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
				$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
			}
		}			
		$arrNilai["$r[idPegawai]"]+= $nilaiPelatihan;			
		$cntKategori["$r[idPegawai]"]["$r[idKategori]"]=$r[idKategori];
	}
	
	$arrOrder = array(	
		"n1.name",
		"n1.reg_no",
		"n1.name",			
		"n1.namaDivisi",	
		"n2.namaData",	
		"n1.pos_name",	
		"n1.kodePelatihan",	
		"n1.mulaiPelatihan",	
		);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql="select n1.*, n2.namaData as namaDepartemen from (
			select d1.*, d2.namaData as namaDivisi from (
				select t1.kodePelatihan, t1.mulaiPelatihan, t2.idPeserta, t3.* from plt_pelatihan t1 join plt_pelatihan_peserta t2 join dta_pegawai t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) $sWhere
			) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
		) as n1 left join mst_data n2 on (n1.dept_id=n2.kodeData) order by $orderBy $sLimit";
	$res=db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(*) from plt_pelatihan t1 join plt_pelatihan_peserta t2 join dta_pegawai t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) $sWhere"),
		"aaData" => array(),
		);
	
	
	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;
		$nilaiPelatihan = $cntEvaluasi["$r[id]"] > 0 ? getAngka(round($arrNilai["$r[id]"] / $cntEvaluasi["$r[id]"])) : "-";
		
		$data=array(
			"<div align=\"center\">".$no.".</div>",								
			"<div align=\"center\">".$r[reg_no]."</div>",
			"<div align=\"left\">".strtoupper($r[name])."</div>",
			"<div align=\"left\">".$r[namaDivisi]."</div>",			
			"<div align=\"left\">".$r[namaDepartemen]."</div>",			
			"<div align=\"left\">".$r[pos_name]."</div>",
			"<div align=\"left\">".$r[kodePelatihan]."</div>",			
			"<div align=\"center\">".getTanggal($r[mulaiPelatihan])."</div>",
			"<div align=\"center\">".$waktuPelatihan."</div>",	
			"<div align=\"center\">".getAngka($jmlPelatihan)."</div>",				
			"<div align=\"center\">".getAngka($arrAbsensi["".$r[idPeserta].""]["t"])."</div>",						
			"<div align=\"left\">&nbsp;</div>",
			"<div align=\"center\">".$nilaiPelatihan."</div>",
			);		
		
		$json['aaData'][]=$data;
	}
	return json_encode($json);
}

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$pSearch = empty($_GET[pSearch]) ? getField("select idPelatihan from plt_pelatihan where statusPelatihan='t' order by judulPelatihan limit 1") : $_GET[pSearch];
	$text = table(13, array(9,10,11,12,13));

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."			
</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">		
		<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
					<td>Pelatihan : </td>
					<td>".comboData("select * from plt_pelatihan where statusPelatihan='t' order by judulPelatihan","idPelatihan","judulPelatihan","pSearch","",$pSearch,"", "310px;")."</td>					
				</tr>
			</table>
		</div>
		<div id=\"pos_r\">
			<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
		</div>
	</form>
	<br clear=\"all\" />
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
		<thead>
			<tr>
				<th style=\"width:30px; vertical-align:middle;\">No.</th>
				<th style=\"width:100px; vertical-align:middle;\">ID Pegawai</th>
				<th style=\"min-width:100px; vertical-align:middle;\">Nama</th>
				<th style=\"min-width:100px; vertical-align:middle;\">Divisi</th>
				<th style=\"min-width:100px; vertical-align:middle;\">Departemen</th>
				<th style=\"min-width:100px; vertical-align:middle;\">Jabatan</th>
				<th style=\"width:75px; vertical-align:middle;\">Kelas</th>
				<th style=\"width:100px; vertical-align:middle;\">Tanggal</th>
				<th style=\"width:75px; vertical-align:middle;\">Jam</th>
				<th style=\"width:75px; vertical-align:middle;\">Jml Pertemuan</th>
				<th style=\"width:75px; vertical-align:middle;\">Jml Hadir</th>
				<th style=\"width:75px; vertical-align:middle;\">Lulus</th>
				<th style=\"width:75px; vertical-align:middle;\">Nilai</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>";

if($par[mode] == "xls"){			
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}	

return $text;
}

function xls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
	require_once 'plugins/PHPExcel.php';

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		

	$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:M3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Daftar Karyawan Sudah Ikut Training"));
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getField("select judulPelatihan from plt_pelatihan where idPelatihan='".$_GET[pSearch]."'"));	

	$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'ID PEGAWAI');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'DIVISI');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'DEPARTEMEN');
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'KELAS');
	$objPHPExcel->getActiveSheet()->setCellValue('H4', 'TANGGAL');
	$objPHPExcel->getActiveSheet()->setCellValue('I4', 'JAM');
	$objPHPExcel->getActiveSheet()->setCellValue('J4', 'JML PERTEMUAN');
	$objPHPExcel->getActiveSheet()->setCellValue('K4', 'JML HADIR');
	$objPHPExcel->getActiveSheet()->setCellValue('L4', 'LULUS');
	$objPHPExcel->getActiveSheet()->setCellValue('M4', 'NILAI');

	$rows = 5;

	$sWhere= " where t1.statusPelatihan='t'";	
	if (!empty($_GET['pSearch']))
		$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				

	$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $sWhere order by t1.idPelatihan");		
	$cntEvaluasi = arrayQuery("select idPegawai, count(*) from plt_pertanyaan_evaluasi where idPelatihan='".$_GET['pSearch']."' group by 1");
	
	
	list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t",getField("select concat(count(t2.idJadwal), '\t', sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere"));
	$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
	$menitPelatihan = $menitPelatihan%60;			
	$waktuPelatihan = $menitPelatihan > 0 ?
	getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
	getAngka($jamPelatihan)." Jam";
	$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan > 1 ? getAngka($menitPelatihan)." Menit": $waktuPelatihan;
	$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan < 1 ? "" : $waktuPelatihan;
	
	$arrAbsensi = arrayQuery("select t2.idPeserta, count(t2.statusAbsensi) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere and t2.statusAbsensi='t' group by 1");		
	
	$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $sWhere order by t1.idPelatihan";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$arrJawaban = explode("\t", $r[evaluasiJawaban]);
		$nilaiPelatihan = 0;
		if(is_array($arrJawaban)){
			reset($arrJawaban);
			while(list($i,$d)=each($arrJawaban)){
				$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
				$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
				$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
			}
		}			
		$arrNilai["$r[idPegawai]"]+= $nilaiPelatihan;			
		$cntKategori["$r[idPegawai]"]["$r[idKategori]"]=$r[idKategori];
	}
	
	$sql="select n1.*, n2.namaData as namaDepartemen from (
			select d1.*, d2.namaData as namaDivisi from (
				select t1.kodePelatihan, t1.mulaiPelatihan, t2.idPeserta, t3.* from plt_pelatihan t1 join plt_pelatihan_peserta t2 join dta_pegawai t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) $sWhere
			) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
		) as n1 left join mst_data n2 on (n1.dept_id=n2.kodeData) order by n1.name";
	$res=db($sql);
	$no=1;
	while($r=mysql_fetch_array($res)){	
		$nilaiPelatihan = $cntEvaluasi["$r[id]"] > 0 ? getAngka(round($arrNilai["$r[id]"] / $cntEvaluasi["$r[id]"])) : "-";
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[reg_no]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaDivisi]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[namaDepartemen]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[kodePelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getTanggal($r[mulaiPelatihan]));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $waktuPelatihan);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, getAngka($jmlPelatihan));
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, getAngka($arrAbsensi["".$r[idPeserta].""]["t"]));
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $nilaiPelatihan);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':M'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rows.':M'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$no++;
		$rows++;
	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:M'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

	$objPHPExcel->getActiveSheet()->setTitle("Laporan");
	$objPHPExcel->setActiveSheetIndex(0);

	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
}

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){			
		case "lst":
		$text=lData();
		break;

		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
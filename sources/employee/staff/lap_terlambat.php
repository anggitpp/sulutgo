<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[tanggalAwal])) $par[tanggalAwal]=date('01/m/Y');
	if(empty($par[tanggalAkhir])) $par[tanggalAkhir]=date('d/m/Y');

	$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");
	$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData");

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."			
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."			
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>	
						<td><input type=\"text\" id=\"tanggalAwal\" name=\"par[tanggalAwal]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAwal]."\" class=\"vsmallinput hasDatePicker\" /></td>
						<td>s.d</td>
						<td><input type=\"text\" id=\"tanggalAkhir\" name=\"par[tanggalAkhir]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAkhir]."\" class=\"vsmallinput hasDatePicker\" /></td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:100px;\">Nama</th>
					<th width=\"75\">NPP</th>												
					<th style=\"min-width:100px;\">Lokasi</th>
					<th style=\"min-width:100px;\">Posisi</th>
					<th style=\"min-width:100px;\">Jabatan</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"50\">Jadwal</th>
					<th width=\"50\">Aktual</th>
					<th width=\"75\">Durasi</th>
				</tr>
			</thead>
			<tbody>";

				$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift)) in ('normal', 'ho')");
				$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");

				$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' and lower(trim(t2.namaShift)) not in ('off', 'cuti')");

				if(!empty($par[idLokasi])) $filter=" and t1.location='".$par[idLokasi]."'";
				if($par[search] == "Nama")
					$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$filter .= " AND t1.location IN ($areaCheck)";

				$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where date(t2.mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') ".$filter." order by t2.mulaiAbsen";
				$res=db($sql);		
				while($r=mysql_fetch_array($res)){
					list($tanggalAbsen, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);

					$mulaiJadwal = isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];

					if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
					list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
					list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);

					if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){		
						$no++;

						$d1 = $tanggalAbsen." ".$mulaiJadwal;
						$d2 = $r[mulaiAbsen];
						$durasiMenit = selisihMenit($d1, $d2);

						$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>
						<td>".$arrLokasi["$r[location]"]."</td>
						<td>".$arrPangkat["$r[rank]"]."</td>
						<td>$r[pos_name]</td>
						<td align=\"center\">".getTanggal($tanggalAbsen)."</td>
						<td align=\"center\">".substr($mulaiJadwal,0,5)."</td>
						<td align=\"center\">".substr($mulaiAbsen,0,5)."</td>						
						<td align=\"right\">".getAngka($durasiMenit)." Menit</td>
					</tr>";	
				}					
			}

			$text.="</tbody>
		</table>
	</div>";

	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}	

	return $text;
}

function xls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
	require_once 'plugins/PHPExcel.php';

	$arrPangkat = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData");
	$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData");

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);		

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		

	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PEGAWAI TERLAMBAT DATANG ABSEN');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($par[tanggalAwal],"t")." s.d ".getTanggal($par[tanggalAkhir],"t"));

	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'LOKASI');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'POSISI');		
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TANGGAL');
	$objPHPExcel->getActiveSheet()->setCellValue('H4', 'JADWAL');
	$objPHPExcel->getActiveSheet()->setCellValue('I4', 'AKTUAL');
	$objPHPExcel->getActiveSheet()->setCellValue('J4', 'DURASI');

	$rows = 5;
	$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift)) in ('normal', 'ho')");
	$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");

	$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' and lower(trim(t2.namaShift)) not in ('off', 'cuti')");

	if(!empty($par[idLokasi])) $filter=" and t1.location='".$par[idLokasi]."'";		
	if($par[search] == "Nama")
		$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
	else if($par[search] == "NPP")
		$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
	else
		$filter.= " and (
	lower(t1.name) like '%".strtolower($par[filter])."%'
	or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
	)";

	$filter .= " AND t1.location IN ($areaCheck)";
	
	$sql="select * from dta_pegawai t1 join dta_absen t2 on (t1.id=t2.idPegawai) where date(t2.mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' and (t1.leader_id='".$par[idPegawai]."' or t1.administration_id='".$par[idPegawai]."') ".$filter." order by t2.mulaiAbsen";
	$res=db($sql);		
	while($r=mysql_fetch_array($res)){
		list($tanggalAbsen, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);

		$mulaiJadwal = isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]) ? $arrJadwal[$tanggalAbsen]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];

		if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
		list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
		list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);

		if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){
			$no++;

			$d1 = $tanggalAbsen." ".$mulaiJadwal;
			$d2 = $r[mulaiAbsen];
			$durasiMenit = selisihMenit($d1, $d2);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrLokasi["$r[location]"]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrPangkat["$r[rank]"]);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($tanggalAbsen));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, substr($mulaiJadwal,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, substr($mulaiAbsen,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, getAngka($durasiMenit)." Menit");

			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$rows.':I'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$rows++;			
		}
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

	$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
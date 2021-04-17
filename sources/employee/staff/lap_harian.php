<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');						

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
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				<input type=\"text\" id=\"tanggalAbsen\" name=\"par[tanggalAbsen]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAbsen]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			<br clear=\"all\" />
		</form>
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th colspan=\"2\" style=\"width:80px;\">Jadwal</th>					
					<th colspan=\"2\" style=\"width:80px;\">Aktual</th>
					<th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
					<th rowspan=\"2\" width=\"85\">Keterangan</th>					
				</tr>
				<tr>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
				</tr>
			</thead>
			<tbody>";		

				$arrNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
				$arrShift=arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
				$arrJadwal=arrayQuery("select idPegawai, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where tanggalJadwal='".setTanggal($par[tanggalAbsen])."'");

				$filter = "where '".setTanggal($par[tanggalAbsen])."' between date(t1.mulaiAbsen) and date(t1.selesaiAbsen) and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";

				if(!empty($par[idLokasi]))
					$filter.= " and t2.location='".$par[idLokasi]."'";

				if($par[search] == "Nama")
					$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$filter .= " AND t2.location IN ($areaCheck)";

				$sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
					explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

					list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
					list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

					if(isset($arrJadwal["$r[idPegawai]"]))
						list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"]);

					$arr["$r[idPegawai]"]=$r;
				}

				if(is_array($arr)){				
					reset($arr);		
					while(list($idPegawai, $r)=each($arr)){
						$no++;			

						if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
						if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

						$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>
						<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
						<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
						<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
						<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
						<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
						<td>$r[keteranganAbsen]</td>						
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

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
	$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:I3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN ABSENSI HARIAN');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Tanggal : '.$par[tanggalAbsen]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
	$objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
	$objPHPExcel->getActiveSheet()->mergeCells('F4:G4');
	$objPHPExcel->getActiveSheet()->mergeCells('H4:H5');
	$objPHPExcel->getActiveSheet()->mergeCells('I4:I5');

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JADWAL');
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'AKTUAL');				
	$objPHPExcel->getActiveSheet()->setCellValue('H4', 'DURASI');
	$objPHPExcel->getActiveSheet()->setCellValue('I4', 'KETERANGAN');

	$objPHPExcel->getActiveSheet()->setCellValue('D5', 'MASUK');
	$objPHPExcel->getActiveSheet()->setCellValue('E5', 'PULANG');
	$objPHPExcel->getActiveSheet()->setCellValue('F5', 'MASUK');
	$objPHPExcel->getActiveSheet()->setCellValue('G5', 'PULANG');								

	$rows = 6;
	$arrNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
	$arrShift=arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
	$arrJadwal=arrayQuery("select idPegawai, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where tanggalJadwal='".setTanggal($par[tanggalAbsen])."'");

	$filter = "where '".setTanggal($par[tanggalAbsen])."' between date(t1.mulaiAbsen) and date(t1.selesaiAbsen) and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";

	if(!empty($par[idLokasi]))
		$filter.= " and t2.location='".$par[idLokasi]."'";

	if($par[search] == "Nama")
		$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
	else if($par[search] == "NPP")
		$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
	else
		$filter.= " and (
	lower(t2.name) like '%".strtolower($par[filter])."%'
	or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
	)";

	$filter .= " AND t2.location IN ($areaCheck)";
	
	$sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
		explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

		list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
		list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

		if(isset($arrJadwal["$r[idPegawai]"]))
			list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"]);
		
		$arr["$r[idPegawai]"]=$r;
	}

	if(is_array($arr)){				
		reset($arr);		
		while(list($idPegawai, $r)=each($arr)){
			$no++;			

			if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
			if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':I'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, substr($r[mulaiShift],0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, substr($r[selesaiShift],0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, substr($r[masukAbsen],0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, substr($r[pulangAbsen],0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, substr(str_replace("-","",$r[durasiAbsen]),0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $r[keteranganAbsen]);

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

	$objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:I'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

	$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
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
<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[tanggalAwal])) $par[tanggalAwal]=date('d/m/Y');
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
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td><input type=\"text\" id=\"tanggalAwal\" name=\"par[tanggalAwal]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAwal]."\" class=\"vsmallinput hasDatePicker\" /></td>
						<td>s.d</td>
						<td><input type=\"text\" id=\"tanggalAkhir\" name=\"par[tanggalAkhir]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAkhir]."\" class=\"vsmallinput hasDatePicker\" /></td>
						<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='S06' AND kodeData IN ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"", "310px")."</td>
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
					<th style=\"min-width:100px;\">Alasan</th>
				</tr>
			</thead>
			<tbody>";

				$arrNormal=getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('normal', 'ho')");
				$arrShift=arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
				$arrJadwal=arrayQuery("select tanggalJadwal, idPegawai, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");		
				$arrAbsen=arrayQuery("select date(mulaiAbsen), idPegawai, mulaiAbsen from dta_absen where date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");		
				
				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				if(!empty($par[idLokasi])) $filter=" and location='".$par[idLokasi]."'";
				$filter .= " AND location IN ($areaCheck)";

				$sql="select * from dta_pegawai where status='".$status."'  and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."') ".$filter." order by name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$tanggalAbsen = setTanggal($par[tanggalAwal]);
					while($tanggalAbsen<=setTanggal($par[tanggalAkhir])){

						list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = isset($arrShift["$r[idPegawai]"]) ?
						explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

						if(isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]))
							list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$tanggalAbsen]["$r[idPegawai]"]);

						if($r[mulaiShift] == "00:00:00" && $r[selesaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c","off"))) list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);

						$arrTanggal[$tanggalAbsen]=$tanggalAbsen;


						$x[kodeShift] = $r[kodeShift];
						$x[name] = $r[name];
						$x[reg_no] = $r[reg_no];
						$x[location] = $r[location];
						$x[rank] = $r[rank];
						$x[pos_name] = $r[pos_name];
						$arrPegawai[$tanggalAbsen]["$r[id]"]=$x;

						list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);
						$tanggalAbsen = date("Y-m-d", dateAdd("d", 1, mktime(0, 0, 0, $bulanAbsen, $hariAbsen, $tahunAbsen)));
					}
				}

				if(is_array($arrTanggal)){		
					reset($arrTanggal);	
					while(list($tanggalJadwal)=each($arrTanggal)){		
						if(is_array($arrPegawai[$tanggalJadwal])){				
							reset($arrPegawai[$tanggalJadwal]);		
							while(list($idPegawai, $r)=each($arrPegawai[$tanggalJadwal])){
								if(							
									!in_array(trim(strtolower($r[kodeShift])), array("c","off")) &&
									!isset($arrAbsen[$tanggalJadwal][$idPegawai]) &&
									in_array(trim(strtolower($r[kodeShift])), array("","n","d")) &&
									date('w', strtotime($tanggalJadwal)) > 0 &&
									!getField("select idLibur from dta_libur where '".$tanggalJadwal."' between mulaiLibur and selesaiLibur")
									){
									$no++;
								$text.="<tr>
								<td>$no.</td>
								<td>".strtoupper($r[name])."</td>
								<td>$r[reg_no]</td>
								<td>".$arrLokasi["$r[location]"]."</td>
								<td>".$arrPangkat["$r[rank]"]."</td>
								<td>$r[pos_name]</td>
								<td align=\"center\">".getTanggal($tanggalJadwal)."</td>
								<td>&nbsp;</td>
							</tr>";
						}
					}
				}					
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);		

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		

	$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PEGAWAI TIDAK MASUK KERJA');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($par[tanggalAwal],"t")." s.d ".getTanggal($par[tanggalAkhir],"t"));

	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'LOKASI');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'POSISI');		
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TANGGAL');
	$objPHPExcel->getActiveSheet()->setCellValue('H4', 'ALASAN');		

	$rows = 5;
	if(!empty($par[idLokasi])) $filter=" and location='".$par[idLokasi]."'";

	$arrNormal=getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('normal', 'ho')");
	$arrShift=arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
	$arrJadwal=arrayQuery("select tanggalJadwal, idPegawai, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");		
	$arrAbsen=arrayQuery("select date(mulaiAbsen), idPegawai, mulaiAbsen from dta_absen where date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");		

	$filter .= " AND location IN ($areaCheck)";
	$sql="select * from dta_pegawai where name is not null ".$filter." order by name";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$tanggalAbsen = setTanggal($par[tanggalAwal]);
		while($tanggalAbsen<=setTanggal($par[tanggalAkhir])){

			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = isset($arrShift["$r[idPegawai]"]) ?
			explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

			if(isset($arrJadwal[$tanggalAbsen]["$r[idPegawai]"]))
				list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$tanggalAbsen]["$r[idPegawai]"]);
			
			if($r[mulaiShift] == "00:00:00" && $r[selesaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c","off"))) list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);

			$arrTanggal[$tanggalAbsen]=$tanggalAbsen;


			$x[kodeShift] = $r[kodeShift];
			$x[name] = $r[name];
			$x[reg_no] = $r[reg_no];
			$x[location] = $r[location];
			$x[rank] = $r[rank];
			$x[pos_name] = $r[pos_name];
			$arrPegawai[$tanggalAbsen]["$r[id]"]=$x;

			list($tahunAbsen, $bulanAbsen, $hariAbsen) = explode("-", $tanggalAbsen);
			$tanggalAbsen = date("Y-m-d", dateAdd("d", 1, mktime(0, 0, 0, $bulanAbsen, $hariAbsen, $tahunAbsen)));
		}
	}

	if(is_array($arrTanggal)){		
		reset($arrTanggal);	
		while(list($tanggalJadwal)=each($arrTanggal)){		
			if(is_array($arrPegawai[$tanggalJadwal])){				
				reset($arrPegawai[$tanggalJadwal]);		
				while(list($idPegawai, $r)=each($arrPegawai[$tanggalJadwal])){												
					if(							
						!in_array(trim(strtolower($r[kodeShift])), array("c","off")) &&
						!isset($arrAbsen[$tanggalJadwal][$idPegawai]) &&
						in_array(trim(strtolower($r[kodeShift])), array("","n","d")) &&
						date('w', strtotime($tanggalJadwal)) > 0 &&
						!getField("select idLibur from dta_libur where '".$tanggalJadwal."' between mulaiLibur and selesaiLibur")
						){
						$no++;
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrLokasi["$r[location]"]);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrPangkat["$r[rank]"]);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($tanggalJadwal));				

					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$rows++;			
				}
			}
		}					
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

$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('A6:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
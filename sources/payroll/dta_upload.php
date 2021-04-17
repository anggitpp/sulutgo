<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$areaCheck;
		$par[idKomponen] = getField("select idKomponen from dta_komponen where kodeKomponen='".$arrParam[$s]."'");
		if(empty($par[tahunUpload])) $par[tahunUpload] = date('Y');
		if(empty($par[bulanUpload])) $par[bulanUpload] = date('m');
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
				</h1>
				".getBread(ucwords("detail gaji"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:20px; top:5px;\">
			<p>				
				<span>Lokasi Kerja : </span>
				".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "210px")."
			</p>
			</div>	
			<div style=\"float:right;\">
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboMonth("par[bulanUpload]", $par[bulanUpload])."</td>
				<td>".comboYear("par[tahunUpload]", $par[tahunUpload])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>		
			</form>
			<br clear=\"all\" />";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
			$filter= " where t1.status='".$status."' and t2.bulanUpload='".$par[bulanUpload]."' and t2.tahunUpload='".$par[tahunUpload]."' and t2.idKomponen='".$par[idKomponen]."'";
			if(!empty($par[idStatus]))
				$filter = " and t1.cat=".$par[idStatus]."";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t1.location='".$par[idLokasi]."'";
			
			if(!empty($par[filter]))
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";
			
			$sql="select * from dta_pegawai t1 join pay_upload t2 join mst_data t3 on (t1.id=t2.idPegawai and t1.div_id=t3.kodeData) $filter and t1.location IN ( $areaCheck ) group by t1.id, t2.keteranganUpload order by t1.name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){				
				$arrDetail["".$r[idPegawai].""] = $r;
				$arrNilai["".$r[idPegawai].""]+= $r[nilaiUpload];
				$arrKeterangan["".$r[idPegawai].""][]= $r[keteranganUpload];
				$sumNilai+= $r[nilaiUpload];				
			}
			
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_v\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">ID</th>
					<th style=\"min-width:150px;\">Unit Kerja</th>
					<th width=\"100\">Tanggal Pembayaran</th>
					<th>Nilai</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>";
						
			if (is_array($arrDetail)) {
				asort($arrDetail);
				reset($arrDetail);
				while (list($idDetail, $r) = each($arrDetail)) {
				$no++;
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>		
						<td>$r[namaData]</td>	
						<td align=\"center\">".getTanggal($par[tahunUpload]."-".$par[bulanUpload]."-".date("t", strtotime($par[tahunUpload]."-".$par[bulanUpload]."-01")))."</td>	
						<td align=\"right\">".getAngka($arrNilai["".$r[idPegawai].""])."</td>
						<td>".implode(", ", $arrKeterangan["".$r[idPegawai].""])."</td>
					</tr>";				
				}
			}
			
			$text.="<tr>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:left; font-weight:bold\">JUMLAH</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:right;\">".getAngka($sumNilai)."</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>		
			</table>			
			</div>";
			
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
		
		return $text;
	}		
	
	function xls(){		
		global $s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);				
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanUpload])." ".$par[tahunUpload]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'ID');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'UNIT KERJA');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TANGGAL BAYAR');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'NILAI');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'KETERANGAN');
									
		$rows = 5;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$filter= " where t1.status='".$status."' and t2.bulanUpload='".$par[bulanUpload]."' and t2.tahunUpload='".$par[tahunUpload]."' and t2.idKomponen='".$par[idKomponen]."'";
		if(!empty($par[idStatus]))
			$filter = " and t1.cat=".$par[idStatus]."";		
		
		if(!empty($par[idLokasi]))
			$filter.= " and t1.location='".$par[idLokasi]."'";
		
		if(!empty($par[filter]))
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";
		
		$sql="select * from dta_pegawai t1 join pay_upload t2 join mst_data t3 on (t1.id=t2.idPegawai and t1.div_id=t3.kodeData) $filter and t1.location IN ( $areaCheck ) group by t1.id, t2.keteranganUpload order by t1.name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){				
			$arrDetail["".$r[idPegawai].""] = $r;
			$arrNilai["".$r[idPegawai].""]+= $r[nilaiUpload];
			$arrKeterangan["".$r[idPegawai].""][]= $r[keteranganUpload];
			$sumNilai+= $r[nilaiUpload];				
		}
		
		if (is_array($arrDetail)) {
			asort($arrDetail);
			reset($arrDetail);
			while (list($idDetail, $r) = each($arrDetail)) {
				$no++;
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$rows, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaData]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($par[tahunUpload]."-".$par[bulanUpload]."-".date("t", strtotime($par[tahunUpload]."-".$par[bulanUpload]."-01"))));			
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $arrNilai["".$r[idPegawai].""]);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, implode(", ", $arrKeterangan["".$r[idPegawai].""]));
				
				$rows++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "JUMLAH");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $sumNilai);

		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower("Laporan")));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")])).".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
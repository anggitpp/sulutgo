<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
		
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;		
		// if(empty($par[bulanKoreksi])) $par[bulanKoreksi] = date('m');
		if(empty($par[tahunCuti])) $par[tahunCuti] = date('Y');		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				".comboYear("par[tahunCuti]", $par[tahunCuti], "", "onchange=\"document.getElementById('form').submit();\"")."
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" width=\"*\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" width=\"50\">Gender</th>
					";

					$sql = "select * from dta_cuti where statusCuti = 't' AND year(mulaiCuti) = '$par[tahunCuti]'";
					// echo $sql;
					$res = db($sql);
					while ($r = mysql_fetch_array($res)) {
						$text.="<th colspan=\"3\" width=\"150\">".$r[namaCuti]."</th>";
					}
					$text.="
				</tr>
				<tr>
					";
					$sql = "select * from dta_cuti where statusCuti = 't' AND year(mulaiCuti) = '$par[tahunCuti]'";
					$res = db($sql);
					while ($r = mysql_fetch_array($res)) {
						$text.="
						<th width=\"50\">PLAFON</th>
						<th width=\"50\">PAKAI</th>
						<th width=\"50\">SISA</th>
						";
					}
					$text.="
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where id is not null ";		
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(name) like '%".strtolower($par[filter])."%'
		or lower(reg_no) like '%".strtolower($par[filter])."%'	
		)";
		$sql="select * from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanKoreksi;
			
			$sdmKoreksi = $r[sdmKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmKoreksi = $r[sdmKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKoreksi;			
			
			list($mulaiKoreksi) = explode(" ",$r[mulaiKoreksi]);
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorKoreksi]</td>";
					$sql_ = "select *,(select count(idCuti) from att_cuti where idTipe = t1.idCuti AND idPegawai = '$r[id]') as jumlahPakai from dta_cuti t1 where statusCuti = 't' AND year(mulaiCuti) = '$par[tahunCuti]'";
					// echo $sql_;
					$res_ = db($sql_);
					while ($r_ = mysql_fetch_array($res_)) {
						$r_[sisaCuti] = $r_[jatahCuti] - $r_[jumlahPakai];
						$text.="<td>$r_[jatahCuti]</td>
								<td>$r_[jumlahPakai]</td>
								<td>$r_[sisaCuti]</td>
						";
					}
					$text.="				
					</tr>";				
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
		if(empty($par[tahunCuti])) $par[tahunCuti] = date('Y');		
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$col = 4;
		$sql = "select * from dta_cuti where statusCuti = 't' and year(mulaiCuti) = '$par[tahunCuti]'";
		$res = db($sql);
		while ($r = mysql_fetch_array($res)) {
			$col++;
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($col))->setWidth(10);
			$col++;
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($col))->setWidth(10);
			$col++;
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($col))->setWidth(10);
		}
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($col).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($col).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($col).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN KOREKSI ABSEN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanKoreksi])." ".$par[tahunKoreksi]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($col).'5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($col).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($col).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($col).'5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($col).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($col).'5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		$sql = "select * from dta_cuti where statusCuti = 't' and year(mulaiCuti) = '$par[tahunCuti]'";
		$res = db($sql);
		$col = 4;
		while ($r = mysql_fetch_array($res)) {
			$col++;
			$colMerge = $col+2;
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($col).'4:'.numToAlpha($colMerge).'4');
			$col = $col+2;
			// $col++;
			
		}

		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'GENDER');
		$sql = "select * from dta_cuti where statusCuti = 't' and year(mulaiCuti) = '$par[tahunCuti]'";
		$res = db($sql);
		$col = 4;
		while ($r = mysql_fetch_array($res)) {
			$col++;
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).'4', $r[namaCuti]);
			$col = $col+2;
		}
									
		$sql = "select * from dta_cuti where statusCuti = 't' and year(mulaiCuti) = '$par[tahunCuti]'";
		$res = db($sql);
		$col = 4;
		while ($r = mysql_fetch_array($res)) {
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).'5', 'PLAFON');
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).'5', 'PAKAI');
		$col++;
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).'5', 'SISA');
		}

		$rows = 6;
		$filter = "where nomorKoreksi is not null";		
		if(!empty($par[bulanKoreksi]))
			$filter.= " and month(t1.mulaiKoreksi)='$par[bulanKoreksi]'";
		if(!empty($par[tahunKoreksi]))
			$filter.= " and year(t1.mulaiKoreksi)='$par[tahunKoreksi]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[statusPegawai])) $filter.= " and t2.cat = '".$par[statusPegawai]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorKoreksi) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from dta_pegawai order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[gender]);
			$col = 4;
			$sql_ = "select *,(select count(idCuti) from att_cuti where idTipe = t1.idCuti AND idPegawai = '$r[id]') as jumlahPakai from dta_cuti t1 where statusCuti = 't' AND year(mulaiCuti) = '$par[tahunCuti]'";
					// echo $sql_;
			$res_ = db($sql_);
			while ($r_ = mysql_fetch_array($res_)) {
				$r_[sisaCuti] = $r_[jatahCuti] - $r_[jumlahPakai];
				$col++;
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).$rows, $r_[jatahCuti]);
				$col++;
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).$rows, $r_[jumlahPakai]);
				$col++;
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($col).$rows, $r_[sisaCuti]);
			}		
			
			$rows++;							
		}
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$sql = "select * from dta_cuti where statusCuti = 't' and year(mulaiCuti) = '$par[tahunCuti]'";
		$res = db($sql);
		$col = 4;
		while ($r = mysql_fetch_array($res)) {
			$col++;
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($col).'4:'.numToAlpha($col).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$col++;
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($col).'4:'.numToAlpha($col).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$col++;
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($col).'4:'.numToAlpha($col).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($col).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($col).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.numToAlpha($col).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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

<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess, $areaCheck;						
		
		if(!isset($par[tanggalMulai])) $par[tanggalMulai] = date("21/m/Y", strtotime("-1 MONTH"));
		if(!isset($par[tanggalSelesai])) $par[tanggalSelesai] = date("20/m/Y");
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[$s]."
				</h1>
				".getBread(ucwords("detail"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>	
									<td  nowrap=\"nowrap\">
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
									</td>
									<td style=\"vertical-align:top;\" id=\"bView\">
										<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'none';
										document.getElementById('bHide').style.display = 'table-cell';
										document.getElementById('dFilter').style.visibility = 'visible';							
										document.getElementById('fSet').style.height = 'auto';
										document.getElementById('fSet').style.padding = '10px';
										\">
									</td>
									<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
										<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 9px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'table-cell';
										document.getElementById('bHide').style.display = 'none';
										document.getElementById('dFilter').style.visibility = 'collapse';							
										document.getElementById('fSet').style.height = '0px';
										document.getElementById('fSet').style.padding = '0px';
										\">					
									</td>
									<td nowrap=\"nowrap\">
										&nbsp;
										<input type=\"text\" class=\"smallinput hasDatePicker\" name=\"par[tanggalMulai]\" value=\"$par[tanggalMulai]\" onchange=\"document.getElementById('form').submit();\">
										&nbsp;s/d&nbsp;
										<input type=\"text\" class=\"smallinput hasDatePicker\" name=\"par[tanggalSelesai]\" value=\"$par[tanggalSelesai]\" onchange=\"document.getElementById('form').submit();\">
										&nbsp;					
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>	
			<div id=\"pos_l\" style=\"float:left\">
				<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP", "Jabatan"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\" style=\"float:right\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
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
					<th style=\"width:125px;\">Jumlah Hari</th>
					<th style=\"width:125px;\">Jumlah HK</th>
					<th style=\"width:125px;\">Total HK</th>
				</tr>
			</thead>
			<tbody>";			
						
			$arrLibur = arrayQuery("select mulaiLibur, selesaiLibur from dta_libur where idKategori = '582' and statusLibur = 't' and (mulaiLibur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."' or selesaiLibur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."')");
			
			$arrAbsen = arrayQuery("select idPegawai, tanggalAbsen, masukAbsen from att_absen where tanggalAbsen between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'");
			
			//$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			//$filter = "WHERE id is not null and status='$status' and location in ( $areaCheck )";
			$filter= " where id is not null and (leave_date>'".setTanggal($par[tanggalSelesai])."' or leave_date is null or leave_date='0000-00-00') and location in ( $areaCheck )";
			if(!empty($par[idLokasi]))
				$filter .= " and location = '$par[idLokasi]'";
			if(!empty($par[divId]))
				$filter.= " and div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
					$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "Jabatan")
					$filter.= " and lower(pos_name) like '%".strtolower($par[filter])."%'";
				else
				$filter.= " and (
					lower(name) like '%".strtolower($par[filter])."%'
					or lower(reg_no) like '%".strtolower($par[filter])."%'
					or lower(pos_name) like '%".strtolower($par[filter])."%'
				)";
				
			$sql="select * from dta_pegawai $filter order by name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$jumlah = 0;
				if (is_array($arrLibur)) {		  
					reset($arrLibur);
					while(list($tanggalMulai, $tanggalSelesai) = each($arrLibur)){
						$tanggalAbsen = $tanggalMulai;						
						while($tanggalAbsen <= $tanggalSelesai){
							if(isset($arrAbsen["".$r[id].""][$tanggalAbsen])) $jumlah++;
							list($Y,$m,$d) = explode("-",$tanggalAbsen);
							$tanggalAbsen = date("Y-m-d", dateAdd("d",1,mktime(0,0,0,$m,$d,$Y)));
						}
					}
				}						
				$arrPegawai["$r[id]"]=$r[reg_no]."\t".strtoupper($r[name])."\t".strtoupper($r[pos_name])."\t".$jumlah;
			}
			
			$jumlahLibur=2;
			if(is_array($arrPegawai)){
				reset($arrPegawai);
				while (list($id, $val) = each($arrPegawai)){
				list($nikPegawai, $namaPegawai, $namaJabatan, $jumlahHari) = explode("\t", $val);
				
				$no++;	
				$text.="<tr>
						<td>$no.</td>						
						<td>$namaPegawai</td>
						<td align=\"center\">$nikPegawai</td>
						<td>$namaJabatan</td>
						<td align=\"center\">".getAngka($jumlahHari)."</td>
						<td align=\"center\">".getAngka($jumlahLibur)."</td>
						<td align=\"center\">".getAngka($jumlahHari * $jumlahLibur)."</td>
					</tr>";
					$totalHari+=$jumlahHari;
				}
			}
			$text.="</tbody>
			<tfoot>				
				<tr>
					<td colspan=\"4\" style=\"text-align:right\"><strong>TOTAL</strong></td>
					<td style=\"text-align:center\"><span>".getAngka($totalHari)."</span></td>
					<td style=\"text-align:center\"><span>".getAngka($jumlahLibur)."</span></td>
					<td style=\"text-align:center\"><span>".getAngka($totalHari * $jumlahLibur)."</span></td>
				</tr>
			</tfoot>
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
		
		if(!isset($par[tanggalMulai])) $par[tanggalMulai] = date("21/m/Y", strtotime("-1 MONTH"));
		if(!isset($par[tanggalSelesai])) $par[tanggalSelesai] = date("20/m/Y");
				
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);			
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
			
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'REKAP KEHADIRAN UPAH ISTIMEWA');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal(setTanggal($par[tanggalMulai]),"t")." s.d ".getTanggal(setTanggal($par[tanggalSelesai]),"t"));		
				
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JUMLAH HARI');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JUMLAH HK');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TOTAL HK');
									
		$rows = 5;
					
		$arrLibur = arrayQuery("select mulaiLibur, selesaiLibur from dta_libur where idKategori = '582' and statusLibur = 't' and (mulaiLibur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."' or selesaiLibur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."')");
		
		$arrAbsen = arrayQuery("select idPegawai, tanggalAbsen, masukAbsen from att_absen where tanggalAbsen between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'");
		
		//$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		//$filter = "WHERE id is not null and status='$status' and location in ( $areaCheck )";
		$filter= " where id is not null and (leave_date>'".setTanggal($par[tanggalSelesai])."' or leave_date is null or leave_date='0000-00-00') and location in ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filter .= " and location = '$par[idLokasi]'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";
		
		if($par[search] == "Nama")
				$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "Jabatan")
				$filter.= " and lower(pos_name) like '%".strtolower($par[filter])."%'";
			else
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
				or lower(pos_name) like '%".strtolower($par[filter])."%'
			)";
			
		$sql="select * from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$jumlah = 0;
			if (is_array($arrLibur)) {		  
				reset($arrLibur);
				while(list($tanggalMulai, $tanggalSelesai) = each($arrLibur)){
					$tanggalAbsen = $tanggalMulai;						
					while($tanggalAbsen <= $tanggalSelesai){
						if(isset($arrAbsen["".$r[id].""][$tanggalAbsen])) $jumlah++;
						list($Y,$m,$d) = explode("-",$tanggalAbsen);
						$tanggalAbsen = date("Y-m-d", dateAdd("d",1,mktime(0,0,0,$m,$d,$Y)));
					}
				}
			}
			$arrPegawai["$r[id]"]=$r[reg_no]."\t".strtoupper($r[name])."\t".strtoupper($r[pos_name])."\t".$jumlah;
		}
		
		$jumlahLibur=2;
		if(is_array($arrPegawai)){
			reset($arrPegawai);
			while (list($id, $val) = each($arrPegawai)){
			list($nikPegawai, $namaPegawai, $namaJabatan, $jumlahHari) = explode("\t", $val);					
			$no++;	
				
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);						
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);	
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $namaPegawai);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $nikPegawai);			
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $namaJabatan);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahHari);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $jumlahLibur);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, '=E'.$rows.'*F'.$rows);
			
			$totalHari+=$jumlahHari;
			$rows++;			
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totalHari);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $jumlahLibur);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, '=E'.$rows.'*F'.$rows);
			
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);		
		$rows++;
		
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(5, 5);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("LAPORAN");
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
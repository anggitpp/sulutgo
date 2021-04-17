<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
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
									<td>
									<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
									".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
									<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
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
			</form>			
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" >
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Departemen</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th style=\"min-width:150px;\">Jabatan</th>				
					<th style=\"width:100px;\">HK</th>
					<th style=\"width:100px;\">Nilai Hari</th>
					<th style=\"width:100px;\">Total Service</th>
				</tr>
			</thead>
			<tbody>";
				
			$filter = "where t1.id is not null AND t2.group_id IN ( $areaCheck )";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";		
			
			$arrHadir = arrayQuery("select idPegawai, count(*) from att_absen where month(tanggalAbsen)='".$par[bulanProses]."' and year(tanggalAbsen)='".$par[tahunProses]."' group by 1");						
			
			$sql="select t1.*, t2.pos_name, t3.kodeData, t3.namaData, t4.serviceManual from emp t1 join emp_phist t2 join mst_data t3 join pay_manual t4 on (t1.id=t2.parent_id and t2.dept_id=t3.kodeData and t1.id=t4.idPegawai and t2.status=1 and t4.bulanManual='".intval($par[bulanProses])."' and tahunManual='".intval($par[tahunProses])."') $filter order by t3.namaData";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){							
				$arrDept["$r[kodeData]"]= $r[namaData];
				$arrService["$r[kodeData]"]["$r[id]"] = $r;
			}
			
			$style="style=\"background:#DDD;\"";
			if(is_array($arrDept)){
				asort($arrDept);
				reset($arrDept);
				while(list($idDept, $namaDept) = each($arrDept)){					
					if(is_array($arrService[$idDept])){
						asort($arrService[$idDept]);
						reset($arrService[$idDept]);
						$no=1;
						while(list($idPegawai, $r) = each($arrService[$idDept])){
							$text.="<tr>
								<td>$no.</td>							
								<td>$r[namaData]</td>
								<td>$r[reg_no]</td>
								<td>".strtoupper($r[name])."</td>
								<td>$r[pos_name]</td>
								<td align=\"center\">".getAngka($arrHadir["$r[id]"])."</td>
								<td align=\"right\">".getAngka($r[serviceManual])."</td>
								<td align=\"right\">".getAngka($arrHadir["$r[id]"] * $r[serviceManual])."</td>
							</tr>";
							$sumHK[$idDept]+=$arrHadir["$r[id]"];
							$sumSC[$idDept]+=$arrHadir["$r[id]"] * $r[serviceManual];
							$no++;
						}
					}
					
					$text.="<tr>
							<td ".$style.">&nbsp;</td>
							<td ".$style.">&nbsp;</td>
							<td ".$style.">&nbsp;</td>
							<td ".$style."><strong>TOTAL</strong></td>
							<td ".$style.">&nbsp;</td>
							<td ".$style." align=\"center\">".getAngka($sumHK[$idDept])."</td>
							<td ".$style.">&nbsp;</td>
							<td ".$style." align=\"right\">".getAngka($sumSC[$idDept])."</td>
						</tr>";
				}
			}
			$style="style=\"background:#999; color:#fff;\"";
			$styleC="style=\"background:#999; color:#fff; text-align:center;\"";
			$styleR="style=\"background:#999; color:#fff; text-align:right;\"";
			$text.="</tbody>			
			<tfoot>
				<tr>
					<td ".$style.">&nbsp;</td>
					<td ".$style.">&nbsp;</td>
					<td ".$style.">&nbsp;</td>
					<td ".$style."><strong>GRAND TOTAL</strong></td>
					<td ".$style.">&nbsp;</td>
					<td ".$styleC.">".getAngka(array_sum($sumHK))."</td>
					<td ".$style.">&nbsp;</td>
					<td ".$styleR.">".getAngka(array_sum($sumSC))."</td>
				</tr>
			</tfoot>
			</table>
			</div>";
			
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower("Pengajuan Service Charge")).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PENGAJUAN SERVICE CHARGE KARYAWAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'HK');		
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'NILAI HARI');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'TOTAL SERVICE');
									
		$rows = 5;
		$filter = "where t1.id is not null AND t2.group_id IN ( $areaCheck )";		
			
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		$arrHadir = arrayQuery("select idPegawai, count(*) from att_absen where month(tanggalAbsen)='".$par[bulanProses]."' and year(tanggalAbsen)='".$par[tahunProses]."' group by 1");						
		
		$sql="select t1.*, t2.pos_name, t3.kodeData, t3.namaData, t4.serviceManual from emp t1 join emp_phist t2 join mst_data t3 join pay_manual t4 on (t1.id=t2.parent_id and t2.dept_id=t3.kodeData and t1.id=t4.idPegawai and t2.status=1 and t4.bulanManual='".intval($par[bulanProses])."' and tahunManual='".intval($par[tahunProses])."') $filter order by t3.namaData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){							
			$arrDept["$r[kodeData]"]= $r[namaData];
			$arrService["$r[kodeData]"]["$r[id]"] = $r;
		}
		
		
		if(is_array($arrDept)){
			asort($arrDept);
			reset($arrDept);
			while(list($idDept, $namaDept) = each($arrDept)){					
				$no=1;
				$rStart=$rows;
				if(is_array($arrService[$idDept])){
					asort($arrService[$idDept]);
					reset($arrService[$idDept]);					
					while(list($idPegawai, $r) = each($arrService[$idDept])){												
						$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(20);		
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('G'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
						
						if(empty($arrHadir["$r[id]"])) $arrHadir["$r[id]"] = 0;
						if(empty($r[serviceManual])) $r[serviceManual] = 0;
						
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaData]);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($r[name]));
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[pos_name]);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $arrHadir["$r[id]"]);			
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[serviceManual]);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, '=F'.$rows.'*G'.$rows);
						
						$rows++;
						$no++;
					}
				}
				$rEnd=$rows-1;
				$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(20);		
				
				$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, 'TOTAL');				
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, '=SUM(F'.$rStart.':F'.$rEnd.')');
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, '=SUM(H'.$rStart.':H'.$rEnd.')');
				
				$arrRow[] = $rows;				
				$rows++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(20);		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, 'GRAND TOTAL');				
		if(is_array($arrRow)){
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, '=F'.implode("+F", $arrRow));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, '=H'.implode("+H", $arrRow));
		}
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A5:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower("Pengajuan Service Charge")));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower("Pengajuan Service Charge")).".xls");
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
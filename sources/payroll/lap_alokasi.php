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
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 700px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>".comboKey("par[tipePegawai]", array("All","531"=>"Tetap", "532"=>"Kontrak", "3195"=>"Outsource", "3196"=>"Magang", "3197"=>"Pengemudi Project"), $par[tipePegawai],"onchange=\"document.getElementById('form').submit();\"")."
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
						<td>Lokasi Proses : </td>
						<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."</td>
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
					<th style=\"min-width:150px;\">Alokasi</th>
					<th style=\"width:150px;\">Nilai</th>
				</tr>
			</thead>
			<tbody>";
			
			$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
			$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");
		
			$filter = "";
			if(!empty($par[idJenis]))
				$filter.= " and t1.idJenis='".$par[idJenis]."'";
			
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
			
			$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' and t1.bulanProses='".$par[bulanProses]."'".$filter."";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, t1.idProses, t0.idLokasi, t0.idJenis, t1.idPegawai, t1.idKomponen, t1.nilaiProses from pay_proses t0 join pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t0.idProses=t1.idProses and t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
				$arrProses[] = $r;
			}
			
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			if(!empty($par[idLokasi]))
				$filter.= " and t3.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t3.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t3.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t3.unit_id='".$par[unitId]."'";
			if(!empty($par[tipePegawai])){
				$filter.=" and t4.cat = '$par[tipePegawai]'";
			}
			
			if(is_array($arrDetail)){
				$arrNilai=arrayQuery("select t3.proses_id , sum(case when t2.tipeKomponen='t' then t1.nilaiProses else t1.nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter."  group by 1");
			}	
			
			$sql="select * from mst_data  where kodeCategory='".$arrParameter[49]."' order by kodeInduk, urutanData";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$no++;	
				$text.="<tr>
						<td>$no.</td>
						<td>".$r[namaData]."</td>
						<td align=\"right\">".getAngka($arrNilai["$r[kodeData]"])."</td>							
					</tr>";		
				$totalGaji+=$arrNilai["$r[kodeData]"];
			}
			$text.="</tbody>
			<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td><strong>TOTAL</strong></td>
				<td style=\"text-align:right\">".getAngka($totalGaji)."</td>							
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
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:C1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAP NILAI ALOKASI GROUP');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode : '.getBulan($par[bulanProses])." ".$par[tahunProses]);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'ALOKASI');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NILAI');
									
		$rows = 5;						
		
		$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
		$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");
	
		$filter = "";
		if(!empty($par[idJenis]))
			$filter.= " and t1.idJenis='".$par[idJenis]."'";
		
		$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' and t1.bulanProses='".$par[bulanProses]."'".$filter."";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, t1.idProses, t0.idLokasi, t0.idJenis, t1.idPegawai, t1.idKomponen, t1.nilaiProses from pay_proses t0 join pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t0.idProses=t1.idProses and t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
			$arrProses[] = $r;
		}
		
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t3.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t3.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t3.unit_id='".$par[unitId]."'";
		if(!empty($par[tipePegawai])){
			$filter.=" and t4.cat = '$par[tipePegawai]'";
		}
		
		if(is_array($arrDetail)){
			$arrNilai=arrayQuery("select t3.proses_id , sum(case when t2.tipeKomponen='t' then t1.nilaiProses else t1.nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter."  group by 1");
		}	
		
		$sql="select * from mst_data  where kodeCategory='".$arrParameter[49]."' order by kodeInduk, urutanData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;				
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaData]);					
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $arrNilai["$r[kodeData]"]);
			
			$totalGaji+=$arrNilai["$r[kodeData]"];
			$rows++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");					
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $totalGaji);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "Jakarta, ".date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"))." ".getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$rows++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "Dept. SDM");
		
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "Who Dyah Wismorini");
		
		$rows++;
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "Direktur Operational & SDM");
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:C'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:C'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:C'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower("Laporan")));
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
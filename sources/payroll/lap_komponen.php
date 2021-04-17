<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;		
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');	
		if(empty($par[tipeKomponen])) $par[tipeKomponen] = "t";	
		
		if(!empty($par[idLokasi])) $filter=" and kodeData='".$par[idLokasi]."'";
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X03' ".$filter." order by urutanData");
		
		$potongan =  $par[tipeKomponen] == "p" ? "checked=\"checked\"" : "";
		$penerimaan =  empty($potongan) ? "checked=\"checked\"" : "";
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 600px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
						<td>Tipe Komponen : </td>				
						<td>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"par[tipeKomponen]\" value=\"t\" onclick=\"document.getElementById('form').submit();\" $penerimaan /> <span class=\"sradio\">Penerimaan</span>
								<input type=\"radio\" id=\"false\" name=\"par[tipeKomponen]\" value=\"p\" onclick=\"document.getElementById('form').submit();\" $potongan /> <span class=\"sradio\">Potongan</span>					
							</div>
						</td>									
						</tr>
					</table>
				</div>				
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_hv\">
			<thead>";
			
			if(count($arrLokasi) > 1){
				$text.="<tr>
						<th rowspan=\"2\" style=\"vertical-align:middle; min-width:300px;\">Description / Komponen Gaji</th>
						<th colspan=\"".count($arrLokasi)."\" width=\"".(count($arrLokasi) * 125)."\">Lokasi Kerja</th>
						<th rowspan=\"2\" style=\"vertical-align:middle; min-width:150px;\">Total</th>
					</tr>
					<tr>";			
					if(is_array($arrLokasi)){
						reset($arrLokasi);				
						while(list($idLokasi, $namaData) = each($arrLokasi)){
							$text.="<th style=\"vertical-align:middle; min-width:150px;\">".$namaData."</th>";					
						}
					}
				$text.="</tr>";
			}else{
				$text.="<tr>
					<th style=\"min-width:150px;\">Description / Komponen Gaji</th>					
					<th width=\"125\">Total</th>
				</tr>";
			}
			$text.="</thead>
			<tbody>";
		
		if(getField("select idProses from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".intval($par[bulanProses])."'")){
			$filter = " AND t2.group_id is not null and t2.status = '535'";
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			$detailProses = "pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
			$arrNilai = arrayQuery("select t2.group_id, t0.kodeKomponen, sum(t1.nilaiProses) from dta_komponen t0 join ".$detailProses." t1 join dta_pegawai t2 on (t0.idKomponen=t1.idKomponen and t1.idPegawai=t2.id) where t0.tipeKomponen='$par[tipeKomponen]' ".$filter." group by 1,2");
		}
		
		$sql="select * from dta_komponen where statusKomponen='t' and tipeKomponen='$par[tipeKomponen]' group by kodeKomponen order by tipeKomponen desc, urutanKomponen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;			
			$text.="<tr>
					<td>".strtoupper($r[namaKomponen])."</td>";
			
			$totalKomponen = 0;
			if(is_array($arrLokasi)){
				reset($arrLokasi);
				while(list($idLokasi) = each($arrLokasi)){
					if(count($arrLokasi) > 1)$text.="<td align=\"right\">".getAngka($arrNilai[$idLokasi]["$r[kodeKomponen]"])."</td>";
					$jumlahKomponen[$idLokasi]+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
					$totalKomponen+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
					$grandKomponen+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
				}
			}
			
			$text.="<td align=\"right\">".getAngka($totalKomponen)."</td>
					</tr>";				
		}
		
		$text.="</tbody>
				<tfoot>
				<tr>
					<td><strong>TOTAL</strong></td>";
						
			if(is_array($arrLokasi)){
				reset($arrLokasi);
				while(list($idLokasi) = each($arrLokasi)){
					if(count($arrLokasi) > 1)$text.="<td style=\"text-align:right\">".getAngka($jumlahKomponen[$idLokasi])."</td>";					
				}
			}
			
			$text.="<td style=\"text-align:right\">".getAngka($grandKomponen)."</td>
					</tr>";	
		
		$text.="</tfoot>
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
		
		$tipeKomponen = $par[tipeKomponen] == "t" ? "PENERIMAAN" : "POTONGAN";
		if(!empty($par[idLokasi])) $filter=" and kodeData='".$par[idLokasi]."'";
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X03' ".$filter." order by urutanData");
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);		
		
		$cols = 3;
		if(is_array($arrLokasi)){
			reset($arrLokasi);
			while(list($idLokasi) = each($arrLokasi)){
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);	
				$cols++;
			}
		}		
		
		if(count($arrLokasi) > 1)
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);	
		
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(40);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'GAJI PER KOMPONEN GAJI ('.$tipeKomponen.')');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		if(count($arrLokasi) > 1){
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getFont()->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols).'5')->getBorders()->getBottom()->setBorderStyle
			(PHPExcel_Style_Border::BORDER_THIN);
		}else{
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'5')->getFont()->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols-1).'5')->getBorders()->getBottom()->setBorderStyle
			(PHPExcel_Style_Border::BORDER_THIN);
		}
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');							
		if(count($arrLokasi) > 1)
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');				
		$objPHPExcel->getActiveSheet()->mergeCells('C4:'.numToAlpha($cols-1).'4');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DESCRIPTION / KOMPONEN GAJI');		
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'LOKASI KERJA');
		
		$cols = 3;
		if(is_array($arrLokasi)){
			reset($arrLokasi);
			while(list($idLokasi, $namaLokasi) = each($arrLokasi)){
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', strtoupper($namaLokasi));
				$cols++;
			}
		}
		
		if(count($arrLokasi) > 1)
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TOTAL');
									
		
		if(getField("select idProses from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".intval($par[bulanProses])."'")){
			$filter = " AND t2.group_id is not null and t2.status = '535'";
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			$detailProses = "pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
			$arrNilai = arrayQuery("select t2.group_id, t0.kodeKomponen, sum(t1.nilaiProses) from dta_komponen t0 join ".$detailProses." t1 join dta_pegawai t2 on (t0.idKomponen=t1.idKomponen and t1.idPegawai=t2.id) where t0.tipeKomponen='$par[tipeKomponen]' ".$filter." group by 1,2");
		}
		
		$rows=6;
		$sql="select * from dta_komponen where statusKomponen='t' and tipeKomponen='$par[tipeKomponen]' group by kodeKomponen order by tipeKomponen desc, urutanKomponen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){		
			$no++;
						
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[namaKomponen]));			
			
			$cols = 3;
			$totalKomponen = 0;
			if(is_array($arrLokasi)){
				reset($arrLokasi);
				while(list($idLokasi, $namaLokasi) = each($arrLokasi)){
					$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrNilai[$idLokasi]["$r[kodeKomponen]"]));
					
					$jumlahKomponen[$idLokasi]+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
					$totalKomponen+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
					$grandKomponen+=$arrNilai[$idLokasi]["$r[kodeKomponen]"];
					
					$cols++;
				}
			}
			
			if(count($arrLokasi) > 1){
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($totalKomponen));
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}else{
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
			}
						
			$rows++;							
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");			
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		
		$cols = 3;		
		if(is_array($arrLokasi)){
			reset($arrLokasi);
			while(list($idLokasi, $namaLokasi) = each($arrLokasi)){
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($jumlahKomponen[$idLokasi]));
				
				$cols++;
			}
		}
		
		if(count($arrLokasi) > 1){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($grandKomponen));
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		}else{
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		}
				
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$cols=3;
		if(is_array($arrLokasi)){
			reset($arrLokasi);
			while(list($idLokasi, $namaLokasi) = each($arrLokasi)){		
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$cols++;
			}
		}
		
		if(count($arrLokasi) > 1)
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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

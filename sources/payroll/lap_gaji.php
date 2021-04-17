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
						<td>Search : </td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>				
			<div id=\"pos_r\">
			   ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."
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
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
				</tr>
			</thead>
			<tbody>";
				
			$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X03' order by urutanData");
	    	$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");	
					
			if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
				$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
				$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join dta_pegawai t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
						if($r[cat] == 531 || !in_array($r[idKomponen], $pph21)){
			        	$arrPegawai["$r[group_id]"]["$r[payroll_id]"]["$r[idPegawai]"]=$r[idPegawai];
					    	$arrNilai["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
						}
					}
				}
			}
			
    		$filter = "";
    		if(!empty($par[idLokasi]))
    			$filter.= " and idLokasi='".$par[idLokasi]."'";
			
			$sql="select * from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".$par[bulanProses]."'".$filter;
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
			    if(isset($arrJenis["".$r[idJenis].""]) && isset($arrLokasi["".$r[idLokasi].""]))
			    {
			        if(is_array($arrPegawai["$r[idLokasi]"]["$r[idJenis]"])){;
		            	reset($arrPegawai["$r[idLokasi]"]["$r[idJenis]"]);
		            	while (list($idPegawai) = each($arrPegawai["$r[idLokasi]"]["$r[idJenis]"])){
		            	    //if($arrNilai[$idPegawai] > 0)
		            	    $arrGaji[$idPegawai]+=$arrNilai[$idPegawai];
		            	}
			        }
			    }
			}
			
			$filter = "where t1.id is not null and t2.group_id is not null and t1.status = '535' ";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			if(!empty($par[tipePegawai])){
				$filter.=" and t1.cat = '$par[tipePegawai]'";
			}
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";		
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(isset($arrGaji["$r[id]"])){
					$no++;	
					$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$r[reg_no]</td>
							<td>$r[pos_name]</td>
							<td>".$arrDivisi["$r[div_id]"]."</td>
							<td style=\"text-align:right\">".getAngka($arrGaji["$r[id]"])."</td>							
						</tr>";					
					$totalGaji+=$arrGaji["$r[id]"];
				}
			}
			$text.="</tbody>
			<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td><strong>TOTAL</strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:F4');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	//$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	//$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAP GAJI BULANAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode Gaji : '.getBulan($par[bulanProses])." ".$par[tahunProses]);
		$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Tanggal        : '.getTanggal(date("Y-m-d"), "t"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:F5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'DIVISI');		
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'GAJI');
									
		$rows = 6;						
		
		$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X03' order by urutanData");
	    	$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");	
					
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
			
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join dta_pegawai t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21)){
					$arrPegawai["$r[group_id]"]["$r[payroll_id]"]["$r[idPegawai]"]=$r[idPegawai];
						$arrNilai["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
					}
				}
			}
		}
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and idLokasi='".$par[idLokasi]."'";
		
		$sql="select * from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".$par[bulanProses]."'".$filter;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(isset($arrJenis["".$r[idJenis].""]) && isset($arrLokasi["".$r[idLokasi].""]))
			{
				if(is_array($arrPegawai["$r[idLokasi]"]["$r[idJenis]"])){;
					reset($arrPegawai["$r[idLokasi]"]["$r[idJenis]"]);
					while (list($idPegawai) = each($arrPegawai["$r[idLokasi]"]["$r[idJenis]"])){
						//if($arrNilai[$idPegawai] > 0)
						$arrGaji[$idPegawai]+=$arrNilai[$idPegawai];
					}
				}
			}
		}
		
		$filter = "where t1.id is not null and t2.group_id is not null and t1.status = '535'";		
		
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[tipePegawai])){
			$filter.=" and t1.cat = '$par[tipePegawai]'";
		}
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
		$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(isset($arrGaji["$r[id]"])){
				$no++;				
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));				
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$rows, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[pos_name]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrDivisi["$r[div_id]"]);			
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $arrGaji["$r[id]"]);
				
				$totalGaji+=$arrGaji["$r[id]"];
				$rows++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);	
		
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");				
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $totalGaji);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B5:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C5:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D5:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E5:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F5:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
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

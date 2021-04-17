<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $areaCheck;
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
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "250px")."
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
						<td>Tahun : </td>								
						<td>".comboYear("par[tahunProses]", $par[tahunProses])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>				
			<div id=\"pos_r\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Total Nilai</th>
					<th width=\"125\">Mulai</th>
					<th width=\"125\">Selesai</th>
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
		
		$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
		$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t1.idLokasi='".$par[idLokasi]."'";
		
		$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' ".$filter."";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
			$arrData[] = $r;
		}
		
		$filter = " AND t3.group_id IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t3.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t3.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t3.unit_id='".$par[unitId]."'";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		if(is_array($arrDetail)){
			$arrNilai=arrayQuery("select group_id, payroll_id, periodeProses, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1,2,3");
			$arrJumlah=arrayQuery("select group_id, payroll_id, periodeProses, count(*) from (select group_id, payroll_id, periodeProses, idPegawai from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."'  AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1,2,3,4) as t group by 1,2,3");
		}		
		
		if(is_array($arrData)){;
			reset($arrData);
			while (list($idProses, $r) = each($arrData)){
				$r[pegawaiProses] = $arrJumlah["".$r[idLokasi].""]["".$r[idJenis].""]["".$r[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)];
				$r[nilaiProses] = $arrNilai["".$r[idLokasi].""]["".$r[idJenis].""]["".$r[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)];
					
				if($r[nilaiProses] > 0 && isset($arrJenis["".$r[idJenis].""]) && isset($arrLokasi["".$r[idLokasi].""])){
					$dtaNilai["$r[bulanProses]"]+=$r[nilaiProses];
					$dtaJumlah["$r[bulanProses]"]+=$r[pegawaiProses];
					$arrProses["$r[bulanProses]"]=$r;
				}
			}
		}
		
		if(is_array($arrProses)){;
			reset($arrProses);
			while (list($i, $r) = each($arrProses)){
				$r[pegawaiProses] = $dtaJumlah["$r[bulanProses]"];
				$r[nilaiProses] = $dtaNilai["$r[bulanProses]"];
				
				if($r[nilaiProses] > 0){
					$no++;
					list($mulaiTanggal, $mulaiWaktu) = explode(" ",$r[mulaiProses]);
					list($selesaiTanggal, $selesaiWaktu) = explode(" ",$r[selesaiProses]);
					$text.="<tr>
							<td>$no.</td>
							<td>".getBulan($r[bulanProses])."</td>
							<td align=\"center\">".getAngka($r[pegawaiProses])."</td>
							<td align=\"right\">".getAngka($r[nilaiProses])."</td>
							<td align=\"center\">".getTanggal($mulaiTanggal)." ".substr($mulaiWaktu,0,5)."</td>
							<td align=\"center\">".getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5)."</td>
							<td>$r[namaUser]</td>
							</tr>";
					$pegawaiProses+=$r[pegawaiProses];
					$nilaiProses+=$r[nilaiProses];
				}
			}
		}
		$text.="</tbody>
			<tfoot>
				<tr>
				<td>&nbsp;</td>
				<td><strong>TOTAL</strong></td>
				<td style=\"text-align:center\">".getAngka($pegawaiProses)."</td>
				<td style=\"text-align:right\">".getAngka($nilaiProses)."</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);				
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PROSES GAJI');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', $par[tahunProses]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'BULAN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'JUMLAH PEGAWAI');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TOTAL NILAI');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'MULAI');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'SELESAI');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'PETUGAS');
									
		$rows = 5;
		$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
		$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t1.idLokasi='".$par[idLokasi]."'";
		
		$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' ".$filter."";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
			$arrData[] = $r;
		}
		
		$filter = " AND t3.group_id IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t3.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t3.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t3.unit_id='".$par[unitId]."'";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		if(is_array($arrDetail)){
			$arrNilai=arrayQuery("select group_id, payroll_id, periodeProses, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1,2,3");
			$arrJumlah=arrayQuery("select group_id, payroll_id, periodeProses, count(*) from (select group_id, payroll_id, periodeProses, idPegawai from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."'  AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1,2,3,4) as t group by 1,2,3");
		}		
		
		if(is_array($arrData)){;
			reset($arrData);
			while (list($idProses, $r) = each($arrData)){
				$r[pegawaiProses] = $arrJumlah["".$r[idLokasi].""]["".$r[idJenis].""]["".$r[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)];
				$r[nilaiProses] = $arrNilai["".$r[idLokasi].""]["".$r[idJenis].""]["".$r[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)];
					
				if($r[nilaiProses] > 0 && isset($arrJenis["".$r[idJenis].""]) && isset($arrLokasi["".$r[idLokasi].""])){
					$dtaNilai["$r[bulanProses]"]+=$r[nilaiProses];
					$dtaJumlah["$r[bulanProses]"]+=$r[pegawaiProses];
					$arrProses["$r[bulanProses]"]=$r;
				}
			}
		}
		
		if(is_array($arrProses)){;
			reset($arrProses);
			while (list($i, $r) = each($arrProses)){
				$r[pegawaiProses] = $dtaJumlah["$r[bulanProses]"];
				$r[nilaiProses] = $dtaNilai["$r[bulanProses]"];
				
				if($r[nilaiProses] > 0){
					$no++;
					list($mulaiTanggal, $mulaiWaktu) = explode(" ",$r[mulaiProses]);
					list($selesaiTanggal, $selesaiWaktu) = explode(" ",$r[selesaiProses]);
					
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
					$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
					
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getBulan($r[bulanProses]));
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($r[pegawaiProses]));
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[nilaiProses]);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($mulaiTanggal)." ".substr($mulaiWaktu,0,5));			
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5));
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[namaUser]);
					
					$pegawaiProses+=$r[pegawaiProses];
					$nilaiProses+=$r[nilaiProses];
					$rows++;		
				}
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($pegawaiProses));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $nilaiProses);

		
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
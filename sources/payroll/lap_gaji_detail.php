<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		if(empty($par[idJenis])) $par[idJenis] = getField("select idJenis from pay_jenis where statusJenis='t' order by idJenis limit 1");
		
		$arrData = arrayQuery("select t2.kodeKomponen, concat(t2.tipeKomponen, '\t', t2.namaKomponen) from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' and t2.realisasiKomponen='t' group by 1 order by t2.tipeKomponen desc, t2.urutanKomponen");
		//$arrDetail = arrayQuery("select t2.kodeKomponen, t2.idKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen");
		$arrDetail = arrayQuery("select t2.kodeKomponen, t2.idKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.statusKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen");
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		</div>    
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
		<table>
		<tr>
		<td>Search : </td>
		<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
		<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
		<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
		</tr>
		</table>
		<p style=\"position: absolute; right: 20px; top: 4px;\">".
		comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")."&nbsp;".
		comboYear("par[tahunProses]", $par[tahunProses], "5", "onchange=\"document.getElementById('form').submit();\"")."&nbsp;".
		comboKey("par[tipePegawai]", array("All","531"=>"Tetap", "532"=>"Kontrak", "3195"=>"Outsource", "3196"=>"Magang", "3197"=>"Pengemudi Project"), $par[tipePegawai],"onchange=\"document.getElementById('form').submit();\"")."
		</p>
		</div>	
		<div id=\"pos_r\">
			".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","Semua Lokasi",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "200px", "chosen-select")."
			".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]","",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "200px", "chosen-select")."
			<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
		</div>
		</form>			
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>
		<th style=\"min-width:150px;\">NPP</th>
		<th style=\"min-width:150px;\">Acc Number</th>
		<th style=\"min-width:150px;\">Perusahaan</th>
		<th style=\"min-width:150px;\">Nama</th>
		<th style=\"min-width:150px;\">Departemen</th>					
		<th style=\"min-width:150px;\">Posisi</th>";
		$tipeTemp="";
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen)
					$text.="<th style=\"min-width:150px;\">Total Pendapatan Bruto</th>";
				$text.="<th style=\"min-width:150px;\">".$namaKomponen."</th>";
				$tipeTemp = $tipeKomponen;
			}
		}
		$text.="<th style=\"min-width:150px;\">Total Potongan</th>";
		$text.="<th style=\"min-width:150px;\">Total</th>
		</tr>
		</thead>
		<tbody>";
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
		$bulanKeluar = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
		$tahunKeluar = $par[bulanProses] == 1 ? $par[tahunProses] - 1 : $par[tahunProses];
		$tanggalKeluar = $tahunKeluar."-".str_pad($bulanKeluar, 2, "0", STR_PAD_LEFT)."-15";
		
		$filter = "where t3.id is not null and t3.status = '535'";
		if(!empty($par[idJenis]))
			$filter.= " and t3.payroll_id='".$par[idJenis]."'";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		
		if($idProses = getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			/*
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
			*/
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join dta_pegawai t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) ".$filter." and t2.realisasiKomponen='t' order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				/*
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21)){*/
						$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
						$arrKomponen["$r[idPegawai]"]["$r[idKomponen]"] = $r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
					/*}
				}*/
			}
		}
		
		$arrDept = arrayQuery("select kodeData, namaData from mst_data");
		$arrPerusahaan = arrayQuery("select kodeData, namaData from mst_data");
		
		$filter = "where t1.id is not null and (t1.status='".$status."' or t1.leave_date > '".$tanggalKeluar."') and t2.status=1  and (t3.status=1 or t3.status is null)";			
		
		if($par[search] == "Nama")
		$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
		$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
		$filter.= " and (
		lower(t1.name) like '%".strtolower($par[filter])."%'
		or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
		)";		
		
		if(!empty($par[tipePegawai])){
			$filter.=" and t1.cat = '$par[tipePegawai]'";
		}
		
		$sql="select t1.*, t2.pos_name,t1.id as idPegawai, t2.dept_id, t2.dir_id, t3.account_no from emp t1 join emp_phist t2 on (t1.id=t2.parent_id) left join emp_bank t3 on (t1.id = t3.parent_id and t3.bank_id > 0) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(isset($arrGaji["$r[id]"])){
				$no++;	
				
				$text.="<tr>
				<td>$no.</td>
				<td>$r[reg_no]</td>
				<td>$r[account_no]</td>
				<td>".$arrPerusahaan["$r[dir_id]"]."</td>
				<td>".strtoupper($r[name])."</td>
				<td>".$arrDept["$r[dept_id]"]."</td>
				<td>$r[pos_name]</td>";
				
				$totalKomponen=0;
				$nilaiTipe=0;
				$tipeTemp="";
				if(is_array($arrData)){
					reset($arrData);
					while (list($kodeKomponen, $valKomponen) = each($arrData)){
						list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
						$nilaiKomponen=0;
						if(is_array($arrDetail[$kodeKomponen])){;
							reset($arrDetail[$kodeKomponen]);
							while (list($idKomponen) = each($arrDetail[$kodeKomponen])){
								$nilaiKomponen+= $arrKomponen["$r[idPegawai]"][$idKomponen];
							}
						}
						if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
							$text.="<td align=\"right\">".getAngka(abs($nilaiTipe))."</td>";
							$nilaiTipe=0;
						}
						$text.="<td align=\"right\">".getAngka(abs($nilaiKomponen))."</td>";
						$jumlahKomponen[$kodeKomponen]+=$nilaiKomponen;
						$totalKomponen+=$nilaiKomponen;
						$tipeTemp=$tipeKomponen;
						$nilaiTipe+=$nilaiKomponen;
					}
				}
				$text.="<td align=\"right\">".getAngka(abs($nilaiTipe))."</td>";
				$text.="<td align=\"right\">".getAngka($totalKomponen)."</td>
				</tr>";					
				$jumlahTotal+=$totalKomponen;
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
		<td>&nbsp;</td>
		<td>&nbsp;</td>";
		
		$tipeTemp="";
		$totalTipe=0;
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
							$text.="<td style=\"text-align:right\">".getAngka(abs($totalTipe))."</td>";
							$totalTipe=0;
				}
				$text.="<td style=\"text-align:right\">".getAngka(abs($jumlahKomponen[$kodeKomponen]))."</td>";
				$tipeTemp=$tipeKomponen;
				$totalTipe+=$jumlahKomponen[$kodeKomponen];
			}
		}
		$text.="<td style=\"text-align:right\">".getAngka(abs($totalTipe))."</td>";
		$text.="<td style=\"text-align:right\">".getAngka($jumlahTotal)."</td>
		
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
		
		$arrData = arrayQuery("select t2.kodeKomponen, concat(t2.tipeKomponen, '\t', t2.namaKomponen) from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' and t2.realisasiKomponen='t' group by 1 order by t2.tipeKomponen desc, t2.urutanKomponen");
		//$arrDetail = arrayQuery("select t2.kodeKomponen, t2.idKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen");
		$arrDetail = arrayQuery("select t2.kodeKomponen, t2.idKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.statusKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen");
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
		->setLastModifiedBy($cNama)
		->setTitle($arrTitle[$s]);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		
		$cols=8;
		$tipeTemp="";
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
					$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
					$cols++;
				}
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
				$tipeTemp = $tipeKomponen;
				$cols++;
			}
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(40);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAP GAJI BULANAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'ACC NUMBER');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'PERUSAHAAN');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'NAMA');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'POSISI');
		
		$cols=8;
		$tipeTemp="";
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', strtoupper("Total Pendapatan Bruto"));
					$cols++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', strtoupper($namaKomponen));
				$tipeTemp = $tipeKomponen;
				$cols++;
			}
		}
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', strtoupper("Total Potongan"));
		$cols++;
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TOTAL');
		
		$rows = 5;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
		$bulanKeluar = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
		$tahunKeluar = $par[bulanProses] == 1 ? $par[tahunProses] - 1 : $par[tahunProses];
		$tanggalKeluar = $tahunKeluar."-".str_pad($bulanKeluar, 2, "0", STR_PAD_LEFT)."-15";
		
		$filter = "where t3.id is not null and t3.status = '535'";
		if(!empty($par[idJenis]))
			$filter.= " and t3.payroll_id='".$par[idJenis]."'";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		
		if($idProses = getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			/*
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
			*/
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join dta_pegawai t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) ".$filter." and t2.realisasiKomponen='t' order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				/*
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21)){
				*/
						$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
						$arrKomponen["$r[idPegawai]"]["$r[idKomponen]"] = $r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
				/*
					}
				}
				*/
			}
		}
		
		$arrDept = arrayQuery("select kodeData, namaData from mst_data");
		$arrPerusahaan = arrayQuery("select kodeData, namaData from mst_data");
		
		$filter = "where t1.id is not null and (t1.status='".$status."' or t1.leave_date > '".$tanggalKeluar."') and t2.status=1  and (t3.status=1 or t3.status is null) ";			
		
		if($par[search] == "Nama")
		$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
		$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
		$filter.= " and (
		lower(t1.name) like '%".strtolower($par[filter])."%'
		or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
		)";		
		
		if(!empty($par[tipePegawai])){
			$filter.=" and t1.cat = '$par[tipePegawai]'";
		}
		
		$sql="select t1.*, t2.pos_name,t1.id as idPegawai, t2.dept_id, t2.dir_id, t3.account_no from emp t1 join emp_phist t2 on (t1.id=t2.parent_id) left join emp_bank t3 on (t1.id = t3.parent_id and t3.bank_id > 0) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(isset($arrGaji["$r[id]"])){
				$no++;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);			
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$rows, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);	
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[account_no]));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrPerusahaan["$r[dir_id]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, strtoupper($r[name]));				
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $arrDept["$r[dept_id]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[pos_name]);	
				
				$cols=8;
				$totalKomponen=0;
				$tipeTemp="";
				$nilaiTipe=0;
				if(is_array($arrData)){
					reset($arrData);
					while (list($kodeKomponen, $valKomponen) = each($arrData)){
						list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
						$nilaiKomponen=0;
						if(is_array($arrDetail[$kodeKomponen])){;
							reset($arrDetail[$kodeKomponen]);
							while (list($idKomponen) = each($arrDetail[$kodeKomponen])){
								$nilaiKomponen+= $arrKomponen["$r[idPegawai]"][$idKomponen];
							}
						}
						if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
							$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
							$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($nilaiTipe)));
							$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$nilaiTipe=0;
							$cols++;
						}
						
						$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
						$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($nilaiKomponen)));
						$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$jumlahKomponen[$kodeKomponen]+=$nilaiKomponen;
						$totalKomponen+=$nilaiKomponen;
						$tipeTemp=$tipeKomponen;
						$nilaiTipe+=$nilaiKomponen;
						$cols++;
					}
				}
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($nilaiTipe)));
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$cols++;
				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($totalKomponen));
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$jumlahTotal+=$totalKomponen;
				$rows++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$rows, "TOTAL");	
		
		$cols=8;
		$tipeTemp="";
		$totalTipe=0;
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
					$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($totalTipe)));
					$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$totalTipe=0;
					$cols++;
				}
				
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($jumlahKomponen[$kodeKomponen])));
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$tipeTemp=$tipeKomponen;
				$totalTipe+=$jumlahKomponen[$kodeKomponen];
				$cols++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka(abs($totalTipe)));
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($jumlahTotal));
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols=8;
		$tipeTemp="";
		if(is_array($arrData)){
			reset($arrData);
			while (list($kodeKomponen, $valKomponen) = each($arrData)){
				list($tipeKomponen, $namaKomponen) = explode("\t", $valKomponen);
				if(!empty($tipeTemp) && $tipeTemp!=$tipeKomponen){
					$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$cols++;
				}
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$cols++;
				$tipeTemp=$tipeKomponen;
			}
		}
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		/*
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols-1).$rows.':'.numToAlpha($cols).$rows);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols-1).$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols-1).$rows, "Jakarta, ".date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"))." ".getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols-1).$rows.':'.numToAlpha($cols).$rows);
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols-1).$rows, "Dept. SDM");
		
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols-1).$rows.':'.numToAlpha($cols).$rows);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols-1).$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols-1).$rows, "Who Dyah Wismorini");
		
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols-1).$rows.':'.numToAlpha($cols).$rows);
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols-1).$rows, "Direktur Operational & SDM");
		
		*/
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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

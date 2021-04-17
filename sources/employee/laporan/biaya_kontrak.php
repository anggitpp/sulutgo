	<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
	$fFile = "files/export/";

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;						

		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');


		$arrDepartemen = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X05' and kodeInduk !='0' order by namaData");

		$departemen = getField("select GROUP_CONCAT(kodeData) from mst_data where statusData='t' and kodeCategory='X05' and kodeInduk != '0' order by namaData");


		$text="

		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
			".getBread()."			
			<span class=\"pagedesc\">&nbsp;</span>
		</div>
		" . empLocHeader() . "
		<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<script>
					function nextDate(getPar){
						bulanProses=document.getElementById('par[bulanProses]');
						tahunProses=document.getElementById('par[tahunProses]');

						bulan = bulanProses.value == 12 ? 01 : bulanProses.value * 1 + 1;	
						tahun = bulanProses.value == 12 ? tahunProses.value * 1 + 1 : tahunProses.value;

						bulanProses.value = bulan > 9 ? bulan : '0' + bulan;
						tahunProses.value = tahun;

						document.getElementById('form').submit();
					}

					function prevDate(getPar){
						bulanProses=document.getElementById('par[bulanProses]');
						tahunProses=document.getElementById('par[tahunProses]');

						bulan = bulanProses.value == 01 ? 12 : bulanProses.value * 1 - 1;	
						tahun = bulanProses.value == 01 ? tahunProses.value * 1 - 1 : tahunProses.value;	

						bulanProses.value = bulan > 9 ? bulan : '0' + bulan;
						tahunProses.value = tahun;

						document.getElementById('form').submit();
					}
				</script>
				<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
					


					<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
					<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
					".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
					<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>	
				</div>


				

			</form>


			";

			// if(!empty($par[lokasi])){
			// 	$filter =" and t1.location = '$par[lokasi]'";
			// }

			$idProses = getField("select idProses from pay_proses where bulanProses = '$par[bulanProses]' AND tahunProses = '$par[tahunProses]'");
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$sql="SELECT * FROM pay_proses_".$par[tahunProses].$par[bulanProses]." t1 JOIN dta_komponen t2 ON (t1.idKomponen=t2.idKomponen) JOIN dta_pegawai t3 ON t1.`idPegawai` = t3.id WHERE idProses='$idProses' AND cat = '532' AND dept_id IN($departemen)";
			// echo $sql;
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
					$arrNilaiDept[$r[dept_id]]	+= $r[nilaiProses];
					// $totalNilai += $r[nilaiProses];
			}

			$text.="
			<table cellpadding=\"0\" style=\"width:500px;float:left;\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" >
				<thead>
					<tr>
						<th width=\"20\">No.</th>					
						<th width=\"*\">Departemen</th>
						<th width=\"100\">Jumlah</th>
						<th width=\"50\">%</th>
					</tr>
				</thead>
				<tbody>
					";
					// $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
					if(is_array($arrDepartemen)){
						reset($arrDepartemen);
						while(list($id, $namaDepartemen) = each($arrDepartemen)){
							if($arrNilaiDept[$id]!=0){
								$r[persen] = ($arrNilaiDept[$id] / array_sum($arrNilaiDept)) * 100;
							// echo $arrNilaiDept[$id]. " a ".$total."<br>";
								$no++;
								$showValue = setAngka($arrNilaiDept[$id]) > 0 ? 1 : 0;
							// $text.="<set value=\"".setAngka($arrNilaiDept[$id])."\" label=\"".$namaDepartemen."\" showValue=\"".$showValue."\" />";
								$text.="<tr>
								<td>$no.</td>
								<td>".$namaDepartemen."</td>
								<td align=\"right\">".getAngka($arrNilaiDept[$id])."</td>
								<td align=\"center\">".round($r[persen],2)."</td>
							</tr>";
							$totalPersen += $r[persen];
							// $total += $jmlLocation[$idPendidikan];
							
							// echo $jumlahPegawai;
						}	
					}
				}

					// echo $total;

				$text.="
				
					<tr>
						<td  align=\"right\" colspan = \"2\">Total</td>
						<td align=\"right\">".getAngka(array_sum($arrNilaiDept))."</td>
						<td align=\"center\">".getAngka($totalPersen)."</td>
					</tr>
				
			</tbody>
		</table>


		<div class=\"widgetbox\" style=\"width:50%;margin-left:50px;float:left;\">


			<div id=\"divPelatihan\" align=\"center\"></div>
			<script type=\"text/javascript\">
				var pelatihanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";

				$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
				if(is_array($arrDepartemen)){
					reset($arrDepartemen);
					while(list($id, $namaDepartemen) = each($arrDepartemen)){
						if($arrNilaiDept[$id] > 0){
							$showValue = setAngka($arrNilaiDept[$id]) > 0 ? 1 : 0;
							$text.="<set value=\"".setAngka($arrNilaiDept[$id])."\" label=\"".$namaDepartemen."\" showValue=\"".$showValue."\" />";
						}	
					}
				}		
				$text.="</chart>';
				var chart = new FusionCharts(\"Pie2D\", \"chartPelatihan\", \"100%\", 650);
				chart.setXMLData( pelatihanChart );
				chart.render(\"divPelatihan\");
			</script>
		</div>

	</div>";

	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=PROSENTASE BIAYA KONTRAK.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}	

	return $text;
}		

function xls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
	if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');

	require_once 'plugins/PHPExcel.php';
	$arrDepartemen = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X05' and kodeInduk !='0' order by namaData");

	$departemen = getField("select GROUP_CONCAT(kodeData) from mst_data where statusData='t' and kodeCategory='X05' and kodeInduk != '0' order by namaData");

	$idProses = getField("select idProses from pay_proses where bulanProses = '$par[bulanProses]' AND tahunProses = '$par[tahunProses]'");
	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
	$sql="SELECT * FROM pay_proses_".$par[tahunProses].$par[bulanProses]." t1 JOIN dta_komponen t2 ON (t1.idKomponen=t2.idKomponen) JOIN dta_pegawai t3 ON t1.`idPegawai` = t3.id WHERE idProses='$idProses' AND cat = '532' AND dept_id IN($departemen)";
			// echo $sql;
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$arrNilaiDept[$r[location]]	+= $r[nilaiProses];
		// $totalNilai += $r[nilaiProses];
	}


	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);	

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PROSENTASE BIAYA KONTRAK');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DEPARTEMEN');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'JUMLAH');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', '%');

	$rows = 5;

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
	if(is_array($arrDepartemen)){
		reset($arrDepartemen);
		while(list($id, $namaDepartemen) = each($arrDepartemen)){
			if($arrNilaiDept[$id]!=0){
				$r[persen] = ($arrNilaiDept[$id] / $totalNilai) * 100;
							// echo $arrNilaiDept[$id]. " a ".$total."<br>";
				$no++;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':D'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $namaDepartemen);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getAngka($arrNilaiDept[$id]));
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, round($r[persen],2));

							// echo $jumlahPegawai;






				$rows++;	
			}		
		}					
	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:D'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:D'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A6:D'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

	$objPHPExcel->getActiveSheet()->setTitle("PROSENTASE BIAYA KONTRAK");
	$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fFile."PROSENTASE BIAYA KONTRAK.xls");
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
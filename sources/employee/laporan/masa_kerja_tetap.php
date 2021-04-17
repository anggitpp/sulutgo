	<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
	$fFile = "files/export/";

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;						

		$text="

		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
			".getBread()."			
			<span class=\"pagedesc\">&nbsp;</span>
		</div>
		" . empLocHeader() . "
		<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
					


					<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>	
				</div>


				

			</form>


			";

			

					
			$text.="
			<table cellpadding=\"0\" style=\"width:700px;float:left;\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" >
				<thead>
					<tr>
						<th width=\"20\">No.</th>					
						<th width=\"*\">Masa Kerja</th>
						<th width=\"100\">Jumlah</th>
						<th width=\"50\">%</th>
					</tr>
				</thead>
				<tbody>
					";
					$masaKerja["&gt;26"]=0;		
					$masaKerja["21-25"]=0;
					$masaKerja["16-20"]=0;
					$masaKerja["11-15"]=0;
					$masaKerja["5-10"]=0;
					$masaKerja["&lt;5"]=0;

					$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

					$sql="select *,REPLACE(
					CASE WHEN COALESCE(leave_date,NULL) IS NULL THEN
					CONCAT(TIMESTAMPDIFF(YEAR, join_date, CURRENT_DATE ))
					ELSE
					CONCAT(TIMESTAMPDIFF(YEAR, join_date, leave_date))
					END,' 0 bln','') empWorkDuration from dta_pegawai
					
					WHERE location IN ($areaCheck) AND cat = '531' order by id";

					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						if($r[status] == $status){
							// list($tahunLahir, $bulanLahir) = explode("-", $r[birth_date]);
							// $r[empWorkDuration] = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", date('Y')."-".date('m')."-01 00:00:00");

							if($r[empWorkDuration] <= 5)
								$masaKerja["&lt;5"]++;
							else if($r[empWorkDuration] <= 11)
								$masaKerja["5-10"]++;
							else if($r[empWorkDuration] <= 16)
								$masaKerja["11-15"]++;
							else if($r[empWorkDuration] <= 21)
								$masaKerja["16-20"]++;
							else if($r[empWorkDuration] <= 26)
								$masaKerja["21-25"]++;
							else
								$masaKerja["&gt;26"]++;

							$jumlahPegawai++;
						}

					}

					$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
					if(is_array($masaKerja)){
						reset($masaKerja);
						while(list($lblMasa, $valMasa) = each($masaKerja)){
							$r[persen] = ($valMasa / $jumlahPegawai) * 100;
							// echo $jmlPendidikan[$lblMasa]. " a ".$total."<br>";
							$no++;
							$showValue = setAngka($jmlPendidikan[$lblMasa]) > 0 ? 1 : 0;
							// $text.="<set value=\"".setAngka($jmlPendidikan[$lblMasa])."\" label=\"".$namaPendidikan."\" showValue=\"".$showValue."\" />";
							$text.="<tr>
							<td>$no.</td>
							<td>".$lblMasa."</td>
							<td align=\"right\">".setAngka($valMasa)."</td>
							<td align=\"right\">".round($r[persen],2)."</td>
							</tr>";
							
							
							// echo $jumlahPegawai;
						}	
					}
					
					// echo $total;

					$text.="
				</tbody>
			</table>


			<div class=\"widgetbox\" style=\"width:30%;margin-left:50px;float:left;\">


				<div id=\"divPelatihan\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var pelatihanChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";

			
					if(is_array($masaKerja)){
						reset($masaKerja);
						while(list($lblMasa, $valMasa) = each($masaKerja)){
							$showValue = $valMasa > 0 ? 1 : 0;
							$text.="<set value=\"".$valMasa."\" label=\"".$lblMasa." Tahun\" showValue=\"".$showValue."\" />";
						}	
					}		
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartPelatihan\", \"100%\", 350);
					chart.setXMLData( pelatihanChart );
					chart.render(\"divPelatihan\");
				</script>
			</div>

		</div>";

		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=PERSENTASE MASA KERJA TETAP.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);	

		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PERSENTASE MASA KERJA TETAP');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'MASA KERJA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'JUMLAH');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', '%');

		$rows = 5;

		$masaKerja["&gt;26"]=0;		
					$masaKerja["21-25"]=0;
					$masaKerja["16-20"]=0;
					$masaKerja["11-15"]=0;
					$masaKerja["5-10"]=0;
					$masaKerja["&lt;5"]=0;

					$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

					$sql="select *,REPLACE(
					CASE WHEN COALESCE(leave_date,NULL) IS NULL THEN
					CONCAT(TIMESTAMPDIFF(YEAR, join_date, CURRENT_DATE ))
					ELSE
					CONCAT(TIMESTAMPDIFF(YEAR, join_date, leave_date))
					END,' 0 bln','') empWorkDuration from dta_pegawai
					
					WHERE location IN ($areaCheck) AND cat = '531' order by id";
					
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						if($r[status] == $status){
							

							if($r[empWorkDuration] <= 5)
								$masaKerja["&lt;5"]++;
							else if($r[empWorkDuration] <= 11)
								$masaKerja["5-10"]++;
							else if($r[empWorkDuration] <= 16)
								$masaKerja["11-15"]++;
							else if($r[empWorkDuration] <= 21)
								$masaKerja["16-20"]++;
							else if($r[empWorkDuration] <= 26)
								$masaKerja["21-25"]++;
							else
								$masaKerja["&gt;26"]++;

							$jumlahPegawai++;
						}

					}
					
					$arrMaster = arrayQuery("select kodeData, namaData from mst_data");		
					if(is_array($masaKerja)){
						reset($masaKerja);
						while(list($lblMasa, $valMasa) = each($masaKerja)){
							$r[persen] = ($valMasa / $jumlahPegawai) * 100;
							// echo $jmlPendidikan[$idPendidikan]. " a ".$total."<br>";
							$no++;
							$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

							$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':D'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$search = array('&gt;','&lt;');
							$replace = array('>','<');
							$str = $lblMasa;
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
							$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, str_replace($search,$replace,$str));
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, setAngka($valMasa));
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, round($r[persen],2));
							
							// echo $jumlahPegawai;
						
					
							

		

		$rows++;	
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

		$objPHPExcel->getActiveSheet()->setTitle("PERSENTASE MASA KERJA TETAP");
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile."PERSENTASE MASA KERJA TETAP.xls");
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
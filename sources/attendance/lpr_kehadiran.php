<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;
	if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
	if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');								

	$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));				

		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 810px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
										".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[statusPegawai]","All",$par[statusPegawai],"onchange=\"document.getElementById('form').submit();\"", "110px")."
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
										".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."
										<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 230px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
		<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
		<div class=\"field\" style=\"margin-left:150px;\">
		".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       where t3.kodeCategory='X03' order by t1.urutanData", "id", "description", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
		<div class=\"field\" style=\"margin-left:150px;\">
		".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       where t4.kodeCategory='X03' AND t1.kodeInduk = '$par[divId]' order by t1.urutanData", "id", "description", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
		<div class=\"field\" style=\"margin-left:150px;\">
		".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk

                       where t5.kodeCategory='X03' AND t1.kodeInduk = '$par[deptId]' order by t1.urutanData", "id", "description", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
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
			<br clear=\"all\" />
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:350px;\">Nama</th>
					<th style=\"min-width:100px;\">NPP</th>";					
					for($i=1; $i<=$day; $i++)
						$text.="<th style=\"min-width:40px;\">".$i."</th>";
				$text.="
				<th style=\"min-width:40px;\">H</th>
				<th style=\"min-width:40px;\">I</th>
				<th style=\"min-width:40px;\">S</th>
				<th style=\"min-width:40px;\">A</th>
				
				<th style=\"min-width:40px;\">Total</th>
					
						
				</tr>

			</thead>
			<tbody>";

				$sql="select * from dta_absen where month(mulaiAbsen)='".$par[bulanAbsen]."' and year(mulaiAbsen)='".$par[tahunAbsen]."'";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					list($tahunAbsen, $bulanAbsen, $mulaiAbsen) = explode("-", $r[mulaiAbsen]);
					list($tanggalAbsen) = explode(" ", $r[mulaiAbsen]);
					$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
					$arrAbsen["$r[idPegawai]"][intval($mulaiAbsen)] = str_replace("-","",$r[durasiAbsen]);
					$arrAbsenMasuk["$r[idPegawai]"][$tanggalAbsen] = $r[durasiAbsen];

					if(strtolower($r[keteranganAbsen]) == "izin sakit"){
						$arrSakit[$r[idPegawai]]++;
					}else if(strtolower($r[keteranganAbsen] !="izin sakit") && $r[statusAbsen] == "I"){
						$arrIzin[$r[idPegawai]]++;
					}

					
				}

				$sql="select * from dta_jadwal where month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal) = '$par[tahunAbsen]' and idShift>0 group by 1,2";
				$res=db($sql);		
				$arrAlpha=array();
				while($r=mysql_fetch_array($res)){	
					$arrAlpha["$r[idPegawai]"]+= isset($arrAbsenMasuk["$r[idPegawai]"]["$r[tanggalJadwal]"]) ? 0 : 1;
				}

				$type = CAL_GREGORIAN;
				$month = $par[bulanAbsen]; // Month ID, 1 through to 12.
				$year = $par[tahunAbsen]; // Year in 4 digit 2009 format.
        //    $day_count = cal_days_in_month($type, $month, $year); // Get the amount of days
                 $day_count = date('t', mktime(0, 0, 0, $month, 1, $year)); // Get the amount of days

				//loop through all days
				for ($i = 1; $i <= $day_count; $i++) {

        		$date = $year.'/'.$month.'/'.$i; //format date
        		$get_name = date('l', strtotime($date)); //get week day
        		$day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

       			 //if not a weekend add day to array
        		 if($day_name != 'Sun' && $day_name != 'Sat'){
            			$workdays[] = $i;
            			$weekDays++;
       			 }
        		

    			}

    			// echo $weekDays;

				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
				$filter = "where t1.id is not null and t1.status='".$status."' and t2.location in ($areaCheck)";



				if(!empty($par[idLokasi]))
					$filter.= " and t2.location='".$par[idLokasi]."'";
				if(!empty($par[divId]))
					$filter.= " and t2.div_id='".$par[divId]."'";
				if(!empty($par[deptId]))
					$filter.= " and t2.dept_id='".$par[deptId]."'";
				if(!empty($par[unitId]))
					$filter.= " and t2.unit_id='".$par[unitId]."'";
				if(!empty($par[statusPegawai])) 
					$filter.= " and t1.cat = '".$par[statusPegawai]."'";
				
				if($par[search] == "Nama")
					$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NPP")
					$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
				else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
					)";

					$sql="select t1.* from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						$no++;													
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>";
						for($i=1; $i<=$day; $i++){
							$week = date("w", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
							$color = in_array($week, array(0,6)) ? "style=\"background:#f2dbdb\"" : "";
							$text.="<td align=\"center\" ".$color.">".substr($arrAbsen["$r[id]"][$i],0,5)."</td>";
							if(!empty($arrAbsen["$r[id]"][$i])){
								$arrHK[$r[id]]++;
							}
						}

				$cekJadwal = getField("select idJadwal from dta_jadwal where month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal = '$par[tahunAbsen]' AND idPegawai = '$r[id]'");
				if(!empty($arrHK[$r[id]])){
				if($cekJadwal){
					$alpha = $arrAlpha[$r[id]];
				}else{
					$alpha = $weekDays - $arrHK[$r[id]];
				}
				}else{
					$alpha = "";
				}
				$text.="
				<td align=\"center\">".$arrHK[$r[id]]."</td>
				<td align=\"center\">".$arrSakit[$r[id]]."</td>
				<td align=\"center\">".$arrIzin[$r[id]]."</td>
				<td align=\"center\">".$alpha."</td>
				<td align=\"center\">".substr(sumTime($arrAbsen["$r[id]"]),0,5)."</td>
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

		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));													
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
		->setLastModifiedBy($cNama)
		->setTitle($arrTitle[$s]);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$cols = 4;
		for($i=1; $i<=$day; $i++){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
			$cols++;
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols+1))->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols+2))->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols+3))->setWidth(10);		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols+4))->setWidth(10);		

		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$namaDivisi = getField("select namaData from mst_data where kodeData='".$par[idDivisi]."'");
		$namaDepartemen = getField("select namaData from mst_data where kodeData='".$par[idDepartemen]."'");				
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN ABSENSI BULANAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanAbsen]).' '.$par[tahunAbsen]);
		$objPHPExcel->getActiveSheet()->setCellValue('A3', $namaDivisi.' '.$namaDepartemen);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		
		$cols=4;
		for($i=1; $i<=$day; $i++){
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $i);		
			$cols++;
		}
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'H');								
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols+1).'4', 'I');								
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols+2).'4', 'S');								
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols+3).'4', 'A');								
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols+4).'4', 'TOTAL');								

		$rows = 5;
		$sql="select * from dta_absen where month(mulaiAbsen)='".$par[bulanAbsen]."' and year(mulaiAbsen)='".$par[tahunAbsen]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunAbsen, $bulanAbsen, $mulaiAbsen) = explode("-", $r[mulaiAbsen]);
			list($tanggalAbsen) = explode(" ", $r[mulaiAbsen]);
			$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
			$arrAbsen["$r[idPegawai]"][intval($mulaiAbsen)] = str_replace("-","",$r[durasiAbsen]);
			$arrAbsenMasuk["$r[idPegawai]"][$tanggalAbsen] = $r[durasiAbsen];

			if(strtolower($r[keteranganAbsen]) == "izin sakit"){
				$arrSakit[$r[idPegawai]]++;
			}else if(strtolower($r[keteranganAbsen] !="izin sakit") && $r[statusAbsen] == "I"){
				$arrIzin[$r[idPegawai]]++;
			}

		}

		$type = CAL_GREGORIAN;
				$month = $par[bulanAbsen]; // Month ID, 1 through to 12.
				$year = $par[tahunAbsen]; // Year in 4 digit 2009 format.
                $day_count = date('t', mktime(0, 0, 0, $month, 1, $year)); // Get the amount of days


				//loop through all days
				for ($i = 1; $i <= $day_count; $i++) {

        		$date = $year.'/'.$month.'/'.$i; //format date
        		$get_name = date('l', strtotime($date)); //get week day
        		$day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

       			 //if not a weekend add day to array
        		if($day_name != 'Sun' && $day_name != 'Sat'){
        			$workdays[] = $i;
        			$weekDays++;
        		}
        		

        	}

		$sql="select * from dta_jadwal where month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal) = '$par[tahunAbsen]' and idShift>0 group by 1,2";
				// echo $sql;
				$res=db($sql);		
				$arrAlpha=array();
				while($r=mysql_fetch_array($res)){	
					// $arrJadwal["$r[idPegawai]"] = $r[tanggalJadwal]; 
					$arrAlpha["$r[idPegawai]"]+= isset($arrAbsenMasuk["$r[idPegawai]"]["$r[tanggalJadwal]"]) ? 0 : 1;
					// if(!isset($arrData["$r[idPegawai]"]["$r[tanggalJadwal]"])) $arrAbsen["$r[idPegawai]"]["$r[tanggalJadwal]"] = "A";
				}
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$filter = "where t1.id is not null and t1.status='".$status."'";
		
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[statusPegawai])) $filter.= " and t1.cat = '".$par[statusPegawai]."'";

		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$sql="select t1.* from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$no++;								

					$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);

					$cols=4;
					for($i=1; $i<=$day; $i++){
						$week = date("w", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));

						if(in_array($week, array(0,6)))
							$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fff88888");

						if(isset($arrAbsen["$r[id]"][$i]))
							$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, substr($arrAbsen["$r[id]"][$i],0,5));
						if(!empty($arrAbsen["$r[id]"][$i])){
								$arrHK[$r[id]]++;
							}
						$cols++;
					}
					$cekJadwal = getField("select idJadwal from dta_jadwal where month(tanggalJadwal) = '$par[bulanAbsen]' AND year(tanggalJadwal = '$par[tahunAbsen]' AND idPegawai = '$r[id]'");
					if(!empty($arrHK[$r[id]])){
						if($cekJadwal){
							$alpha = $arrAlpha[$r[id]];
						}else{
							$alpha = $weekDays - $arrHK[$r[id]];
						}
					}else{
						$alpha = "";
					}
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrHK[$r[id]]);
					$cols++;
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrSakit[$r[id]]);
					$cols++;
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrIzin[$r[id]]);
					$cols++;
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $alpha);
					$cols++;
					if(isset($arrAbsen["$r[id]"]))

						$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, substr(sumTime($arrAbsen["$r[id]"]),0,5));

					$rows++;

				}

				$rows--;
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

				$cols=4;
				for($i=1; $i<=$day; $i++){
					$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$cols++;
				}	
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols+1).'4:'.numToAlpha($cols+1).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols+2).'4:'.numToAlpha($cols+2).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols+3).'4:'.numToAlpha($cols+3).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols+4).'4:'.numToAlpha($cols+4).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

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
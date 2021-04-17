<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";
$arrTunjangan = arrayQuery("select idKomponen, namaKomponen from dta_komponen where tipeKomponen='t'");

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $arrTunjangan,$areaCheck;
	if(empty($par[bulanProses])) $par[bulanProses] = date('m');
	if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		

		$text.="
		<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 800px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
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
										<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location = '?".preg_replace("/(&[ms]=\w+)/", "", getPar())."';\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 290px; top: 40px; z-index: 800;\">
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
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_custom\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:190px;\">$arrParameter[40]</th>
					<th width=\"100\">Empl.</th>";
					foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
						$text .= "<th style=\"min-width:100px;\">$namaTunjangan</th>";
					}
					$text .= "					
					<th style=\"min-width:100px;\">Upah Istimewa</th>
					<th style=\"min-width:100px;\">Total Take Home Pay</th>
					<th style=\"min-width:100px;\">Total Potongan</th>
					<th style=\"min-width:100px;\">Net Gaji</th>
				</tr>
			</thead>
			<tbody>";
				
				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
				$filter = "where id is not null and (leave_date >= '".$par[tahunProses]."-".$par[bulanProses]."-01' or leave_date is null or leave_date='0000-00-00' or status='".$status."') and group_id is not null";		
				
				if(!empty($par[filter]))
				$filter.= " and (
					lower(name) like '%".strtolower($par[filter])."%'
					or lower(reg_no) like '%".strtolower($par[filter])."%'
				)";
				
				if(!empty($par[idLokasi]))
					$filter.= " and group_id='".$par[idLokasi]."'";
				if(!empty($par[divId]))
					$filter.= " and div_id='".$par[divId]."'";
				if(!empty($par[deptId]))
					$filter.= " and dept_id='".$par[deptId]."'";
				if(!empty($par[unitId]))
					$filter.= " and unit_id='".$par[unitId]."'";

				if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
					$sql="select t1.idPegawai, t1.nilaiProses, t2.tipeKomponen, t3.dept_id, t1.idKomponen from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) join dta_pegawai t3 on t3.id = t1.idPegawai $filter order by idDetail";
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						$arrPenerimaan["$r[dept_id]"]["$r[idKomponen]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : 0;
						$arrDetail["$r[dept_id]"]["penerimaan"]["$r[idPegawai]"]["$r[idKomponen]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : 0;
						
						if(in_array($r[tipeKomponen], array("p"))){
							$arrDetail["$r[dept_id]"]["potongan"]["$r[idPegawai]"] += $r[tipeKomponen] == "p" ? $r[nilaiProses] : 0;
							$arrPotongan["$r[dept_id]"] += $r[tipeKomponen] == "p" ? $r[nilaiProses] : 0;
						}
						
						$arrEmpl["$r[dept_id]"]["$r[idPegawai]"] = "";
					}
				}

				$totalEmployee = $totalTakeHomePay = $totalPotongan = $totalNet = 0;
				$arrDept = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X06' AND kodeInduk NOT IN (898, 899) AND statusData = 't' ORDER BY namaData");
				while(list($idDepartemen, $namaDepartemen) = each($arrDept)){
					if(isset($arrEmpl["$idDepartemen"])){
						$no++;
						
						$text .= "<tr>";
						$text .= "<td align=\"right\">$no.</td>";
						$text .= "<td><a href=\"#det\" onclick=\"jQuery('tr.detail_$idDepartemen').css('display', (jQuery('tr.detail_$idDepartemen').css('display') != 'none' ? 'none' : 'table-row')); return false;\">".strtoupper($namaDepartemen)."</a></td>";
						$text .= "<td align=\"center\">".count($arrEmpl["$idDepartemen"])."</td>";
						$subTotalTakeHomePay = 0;
						foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
							$text .= "<td align=\"right\">".getAngka($arrPenerimaan["$idDepartemen"]["$idTunjangan"])."</td>";
							$subTotalTakeHomePay += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];

							$arrTotal["$idTunjangan"] += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];
							$totalTakeHomePay += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];
						}

						$totalEmployee += count($arrEmpl["$idDepartemen"]);
						$totalPotongan += $arrPotongan["$idDepartemen"];
						$totalNet += ($subTotalTakeHomePay - $arrPotongan["$idDepartemen"]);
						
						$text .= "<td align=\"right\">0</td>";
						$text .= "<td align=\"right\">".getAngka($subTotalTakeHomePay)."</td>";
						$text .= "<td align=\"right\">".getAngka($arrPotongan["$idDepartemen"])."</td>";
						$text .= "<td align=\"right\">".getAngka(($subTotalTakeHomePay - $arrPotongan["$idDepartemen"]))."</td>";
						$text .= "</tr>";

						if(count($arrEmpl["$idDepartemen"]) > 0){
							$arrEmp = arrayQuery("SELECT id, CONCAT(name, 'tabsplit\t', reg_no) FROM dta_pegawai $filter AND dept_id = '$idDepartemen' ORDER BY name");
							while(list($idEmp, $nameRegNo) = each($arrEmp)){
								list($namaEmp, $nikEmp) = explode("tabsplit\t", $nameRegNo);
								$text .= "
								<tr class=\"detail_$idDepartemen\" style=\"display:none;\">
									<td align=\"right\">&nbsp;</td>
									<td>$namaEmp</td>
									<td>$nikEmp</td>";
									$subTotalTakeHomePay = 0;
									foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
										$text .= "<td align=\"right\">".getAngka($arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"])."</td>";
										$subTotalTakeHomePay += $arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"];
									}
									$text .="									
									<td align=\"right\">0</td>
									<td align=\"right\">".getAngka($subTotalTakeHomePay)."</td>
									<td align=\"right\">".getAngka($arrDetail["$idDepartemen"]["potongan"]["$idEmp"])."</td>
									<td align=\"right\">".getAngka(($subTotalTakeHomePay - $arrDetail["$idDepartemen"]["potongan"]["$idEmp"]))."</td>
								</tr>";
							}
						}
					}
				}
			$text.="
			</tbody>
			<tfoot>
				<tr>
					<td width=\"20\">&nbsp;</td>
					<td style=\"min-width:190px; text-align: center;\">Total</td>
					<td style=\"text-align: center;\">$totalEmployee</td>";
					foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
						$text .= "<td style=\"text-align: right;\">".getAngka($arrTotal["$idTunjangan"])."</td>";
					}
					$text .= "					
					<td style=\"text-align: right;\">0</td>
					<td style=\"text-align: right;\">".getAngka($totalTakeHomePay)."</td>
					<td style=\"text-align: right;\">".getAngka($totalPotongan)."</td>
					<td style=\"text-align: right;\">".getAngka($totalNet)."</td>
				</tr>
			</tfoot>
			</table>
		</div>";

		if($par[mode] == "xls"){			
			xls();
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower(str_replace("( Penerimaan )", "", $arrTitle[$s]))).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}		

		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $arrTunjangan,$areaCheck;
		require_once 'plugins/PHPExcel.php';

		$common = new Common();
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
		->setLastModifiedBy($cNama)
		->setTitle($arrTitle[$s]);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);

		$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(25);		

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	//$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	//$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($arrTitle[$s]));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Tanggal Gaji : '.getBulan($par[bulanProses])." ".$par[tahunProses]);
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Lokasi : " . (!empty($par[idLokasi]) ? $common->getMstDataDesc($par[idLokasi]) : "ALL"));
		$objPHPExcel->getActiveSheet()->setCellValue('A4', $arrParameter[39] . " : " . (!empty($par[divId]) ? $common->getMstDataDesc($par[divId]) : "ALL"));
		$objPHPExcel->getActiveSheet()->setCellValue('A5', $arrParameter[40] . " : " . (!empty($par[deptId]) ? $common->getMstDataDesc($par[deptId]) : "ALL"));
		$objPHPExcel->getActiveSheet()->setCellValue('A6', $arrParameter[41] . " : " . (!empty($par[unitId]) ? $common->getMstDataDesc($par[unitId]) : "ALL"));
		
		$objPHPExcel->getActiveSheet()->setCellValue('A8', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B8', strtoupper($arrParameter[40]));
		$objPHPExcel->getActiveSheet()->setCellValue('C8', "EMPL.");
		$alpha = "D";
		foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
			$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
			$objPHPExcel->getActiveSheet()->setCellValue($alpha.'8', strtoupper($namaTunjangan));
			$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$alpha++;
		}
		
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.'8', "UPAH ISTIMEWA");
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.'8', "TOTAL TAKE HOME PAY");
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.'8', "TOTAL POTONGAN");
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.'8', "NET GAJI");
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle('A8:'.$alpha.'8')->applyFromArray($styleArray);

		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$alpha.'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.$alpha.'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.$alpha.'3');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:'.$alpha.'4');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:'.$alpha.'5');
		$objPHPExcel->getActiveSheet()->mergeCells('A6:'.$alpha.'6');

		$rows = 9;						
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$filter = "where id is not null and (leave_date >= '".$par[tahunProses]."-".$par[bulanProses]."-01' or leave_date is null or leave_date='0000-00-00' or status='".$status."') and group_id is not null";

		if(!empty($par[filter]))
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
			)";
		
		if(!empty($par[idLokasi]))
			$filter.= " and group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";

		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			$sql="select t1.idPegawai, t1.nilaiProses, t2.tipeKomponen, t3.dept_id, t1.idKomponen from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) join dta_pegawai t3 on t3.id = t1.idPegawai $filter order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrPenerimaan["$r[dept_id]"]["$r[idKomponen]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : 0;
				$arrDetail["$r[dept_id]"]["penerimaan"]["$r[idPegawai]"]["$r[idKomponen]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : 0;
				
				if(in_array($r[tipeKomponen], array("p"))){
					$arrDetail["$r[dept_id]"]["potongan"]["$r[idPegawai]"] += $r[tipeKomponen] == "p" ? $r[nilaiProses] : 0;
					$arrPotongan["$r[dept_id]"] += $r[tipeKomponen] == "p" ? $r[nilaiProses] : 0;
				}
				
				$arrEmpl["$r[dept_id]"]["$r[idPegawai]"] = "";				
			}
		}

		$totalEmployee = $totalTakeHomePay = $totalPotongan = $totalNet = 0;
		$arrDept = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X06' AND kodeInduk NOT IN (898, 899) AND statusData = 't' ORDER BY namaData");
		while(list($idDepartemen, $namaDepartemen) = each($arrDept)){
			if(isset($arrEmpl["$idDepartemen"])){
				$no++;

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($namaDepartemen));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, count($arrEmpl["$idDepartemen"]));
				$subTotalTakeHomePay = 0;
				$alpha = "D";
				foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
					$objPHPExcel->getActiveSheet()->setCellValueExplicit($alpha.$rows, getAngka($arrPenerimaan["$idDepartemen"]["$idTunjangan"]), PHPExcel_Cell_DataType::TYPE_STRING);
					$subTotalTakeHomePay += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];

					$arrTotal["$idTunjangan"] += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];
					$totalTakeHomePay += $arrPenerimaan["$idDepartemen"]["$idTunjangan"];

					$alpha++;
				}
				$totalEmployee += count($arrEmpl["$idDepartemen"]);
				$totalPotongan += $arrPotongan["$idDepartemen"];
				$totalNet += ($subTotalTakeHomePay - $arrPotongan["$idDepartemen"]);
				
				$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "0");
				$alpha++;

				$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($subTotalTakeHomePay));
				$alpha++;

				$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($arrPotongan["$idDepartemen"]));
				$alpha++;

				$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka(($subTotalTakeHomePay - $arrPotongan["$idDepartemen"])));

				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.":".$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
				$styleArray = array(
					'borders' => array(
						'allborders' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);
				$rows++;
			}
		}

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':B'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $totalEmployee);
		$alpha = "D";
		foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($alpha.$rows, getAngka($arrTotal["$idTunjangan"]), PHPExcel_Cell_DataType::TYPE_STRING);
			
			$alpha++;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "0");
		$alpha++;

		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalTakeHomePay));
		$alpha++;

		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalPotongan));
		$alpha++;

		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalNet));

		$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.":".$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);
		
		$rows++;
		$rows++;
		
		$alpha='B';
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");		
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Approved by,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Ciater, 20 ".getBulan($par[bulanProses])." ".$par[tahunProses]);
		$rows++;		
		
		$alpha='B';
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Direktur Utama,");		
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Direktur,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Finance Controller");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Corporate HR GA Manager");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "General Manager");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Chief Accounting");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Human Resources Manager,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Payroll Master,");
		$rows++;
		$rows++;
		$rows++;
		$rows++;	
		$rows++;	
		
		$alpha='B';
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Dra. Hj. Metty Hendriatty. Ak");		
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Hermanie Soewarma");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "H. Supriyanto");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "H.M.Dadang Djulia");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Donie Novian");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "H.Slamet Haryanto");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Maman Somantri,");
		$alpha++;
		$alpha++;
		
		$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "Zaenusollah");
		
		
		$objPHPExcel->getActiveSheet()->setTitle("Rekapitulasi");
		$objPHPExcel->createSheet(1);
		$objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->setTitle("Departemen");
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		
		$alpha = "D";
		foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
			$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);			
			$alpha++;
		}
		
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);		
		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);				
		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);		
		$alpha++;
		$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);		
		
		$rows=0;
		
		$arrDept = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X06' AND kodeInduk NOT IN (898, 899) AND statusData = 't' ORDER BY namaData");
		while(list($idDepartemen, $namaDepartemen) = each($arrDept)){
			if(isset($arrEmpl["$idDepartemen"])){
				if(count($arrEmpl["$idDepartemen"]) > 0){
					$no = 0;
					$rows++;
					$rows++;
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, strtoupper($arrParameter[40]) . " : " . $namaDepartemen);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':'.$alpha.$rows);
					$rows++;
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setItalic(true);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, strtoupper("Bagian") . " : " . $namaDepartemen);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':'.$alpha.$rows);

					$rows++;

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, 'NO.');
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "NAMA");
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "EMPL. ID");
					$alpha = "D";
					foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
						$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, strtoupper($namaTunjangan));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$alpha++;
					}					
					$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "UPAH ISTIMEWA");

					$alpha++;
					$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "TOTAL TAKE HOME PAY");

					$alpha++;
					$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "TOTAL POTONGAN");

					$alpha++;
					$objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setWidth(25);
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "NET GAJI");

					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getFont()->setBold(true);	
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);

					$rows++;
					$arrTotal = array();
					$totalTakeHomePay = $totalPotongan = $totalNet = 0;
					$arrEmp = arrayQuery("SELECT id, CONCAT(name, 'tabsplit\t', reg_no) FROM dta_pegawai $filter AND dept_id = '$idDepartemen' ORDER BY name");
					while(list($idEmp, $nameRegNo) = each($arrEmp)){
						list($namaEmp, $nikEmp) = explode("tabsplit\t", $nameRegNo);
						$no++;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($namaEmp));
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $nikEmp);
						$alpha = "D";
						$subTotalTakeHomePay = 0;
						foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
							$objPHPExcel->getActiveSheet()->setCellValueExplicit($alpha.$rows, getAngka($arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"]), PHPExcel_Cell_DataType::TYPE_STRING);
							$subTotalTakeHomePay += $arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"];

							$arrTotal["$idTunjangan"] += $arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"];
							$totalTakeHomePay += $arrDetail["$idDepartemen"]["penerimaan"]["$idEmp"]["$idTunjangan"];

							$alpha++;
						}

						$totalPotongan += $arrDetail["$idDepartemen"]["potongan"]["$idEmp"];
						$totalNet += ($subTotalTakeHomePay - $arrDetail["$idDepartemen"]["potongan"]["$idEmp"]);
						
						$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "0");
						$alpha++;

						$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($subTotalTakeHomePay));
						$alpha++;

						$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($arrDetail["$idDepartemen"]["potongan"]["$idEmp"]));
						$alpha++;

						$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka(($subTotalTakeHomePay - $arrDetail["$idDepartemen"]["potongan"]["$idEmp"])));

						$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.":".$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
						$styleArray = array(
							'borders' => array(
								'allborders' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);
						$rows++;
					}

					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "BAG. " . $namaDepartemen . " SUB TOTAL, " . $no . " Emp.");

					$alpha = "D";
					foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
						$objPHPExcel->getActiveSheet()->setCellValueExplicit($alpha.$rows, getAngka($arrTotal["$idTunjangan"]), PHPExcel_Cell_DataType::TYPE_STRING);

						$alpha++;
					}					
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "0");
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalTakeHomePay));
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalPotongan));
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalNet));

					$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.":".$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);

					$rows++;
					$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(5);	
					$rows++;

					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, strtoupper(substr($arrParameter[40], 0, 3)) . ". " . $namaDepartemen . " SUB TOTAL, " . $no . " Emp.");

					$alpha = "D";
					foreach($arrTunjangan as $idTunjangan => $namaTunjangan){
						$objPHPExcel->getActiveSheet()->setCellValueExplicit($alpha.$rows, getAngka($arrTotal["$idTunjangan"]), PHPExcel_Cell_DataType::TYPE_STRING);

						$alpha++;
					}					
					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, "0");
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalTakeHomePay));
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalPotongan));
					$alpha++;

					$objPHPExcel->getActiveSheet()->setCellValue($alpha.$rows, getAngka($totalNet));

					$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.":".$alpha.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.$alpha.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.":".$alpha.$rows)->applyFromArray($styleArray);
				}
			}
		}

		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$alpha.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$alpha.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A9:'.$alpha.'9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower(str_replace("( Penerimaan )", "", $arrTitle[$s]))).".xls");
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
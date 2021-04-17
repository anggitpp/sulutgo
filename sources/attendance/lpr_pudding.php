<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
	
	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$areaCheck,$arrParameter;				
		$arrShift=arrayQuery("select idShift from dta_shift where (left(kodeShift,1)=3 or right(kodeShift,3)='2/3') and statusShift='t'");
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		$sWhere.= " and t3.tanggalJadwal='".setTanggal($_GET['tSearch'])."' and t3.idShift in ('".implode("','", $arrShift)."')";
		if($_GET['pSearch'] == "NAMA")
			$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else if($_GET['pSearch'] == "NO ID")
			$sWhere.= " and lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else
			$sWhere.= " and (
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";	
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
		
		return getAngka(getField("select count(*) from dta_pegawai t1 join mst_data t2 join dta_jadwal t3 on (t1.dept_id=t2.kodeData and t1.id=t3.idPegawai) $sWhere"));
	}
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$areaCheck,$arrParameter;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		$arrShift=arrayQuery("select idShift from dta_shift where (left(kodeShift,1)=3 or right(kodeShift,3)='2/3') and statusShift='t'");
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		$sWhere.= " and t3.tanggalJadwal='".setTanggal($_GET['tSearch'])."' and t3.idShift in ('".implode("','", $arrShift)."')";
		if($_GET['pSearch'] == "NAMA")
			$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else if($_GET['pSearch'] == "NO ID")
			$sWhere.= " and lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else
			$sWhere.= " and (
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";	
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
		
		$arrOrder = array(	
			"t2.namaData, t1.name",
			"t1.name",
			"t2.namaData",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];		
		
		$sql="select * from dta_pegawai t1 join mst_data t2 join dta_jadwal t3 on (t1.dept_id=t2.kodeData and t1.id=t3.idPegawai) $sWhere order by $orderBy $sLimit";		
		$res=db($sql);		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai t1 join mst_data t2 join dta_jadwal t3 on (t1.dept_id=t2.kodeData and t1.id=t3.idPegawai) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;			
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".strtoupper($r[name])."</div>",
				"<div align=\"left\">".strtoupper($r[namaData])."</div>",
			);
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}	
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$arrParameter, $areaCheck;
				
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];		
		$text = table(3, array(2,3));		
		
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
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "200px")."
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
										<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location = '?".preg_replace("/(&[ms]=\w+)/", "", getPar())."';\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X06' and kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X07' and kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>	
				
				<div id=\"pos_l\" style=\"float:left\">
				<table>
					<tr>
					<td><input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" onkeyup=\"gData('".getPar($par, "mode")."');\" style=\"width:200px;\"/>
					".comboArray("pSearch", array("All", "NAMA", "NO ID"), $par[paramData], "onchange=\"gData('".getPar($par, "mode")."');\"")."</td>
					<td>						
						<input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\"/>						
					</td>
					</tr>
				</table>
				</div>
				<div id=\"pos_r\" style=\"float:right\">
				<table>
				<tr>
				<td id=\"tPeriode\" style=\"padding-right:10px; font-weight:bold\">&nbsp;</td>";
		if(isset($menuAccess[$s]["add"])) $text.="<td>
					<a href=\"#Add\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl".getPar($par,"mode,idMesin")."',725,250);\"><span>Upload Data</span></a>
				</td>";
		$text.="<td><a href=\"#\" onclick=\"window.location='?par[mode]=xls&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
				</td>
				</tr>
				</table>
				</div>	
				<br clear=\"all\" />	
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th style=\"width:30px; vertical-align:middle;\">No.</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Nama</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Departemen</th>
				</tr>
			</thead>
			<tbody></tbody>			
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\"><strong>TOTAL</strong></td>
					<td style=\"vertical-align:middle; text-align:center; font-weight:bold\" id=\"tCount\">&nbsp;</td>
				</tr>
			</tfoot>
			</table>
			</div>
			<script>
				gData('".getPar($par, "mode")."');
			</script>";
			
		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=Jatah Ekstra Pudding.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
			
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$areaCheck;
		require_once 'plugins/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(35);
		
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
		$objPHPExcel->getActiveSheet()->mergeCells('E3:G3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		list($d,$m,$Y)=explode("/", $_GET[tSearch]);
		$arrHari=array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("DAFTAR KARYAWAN YANG MENDAPAT JATAH EKSTRA PUDDING"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', "SHIFT 3");
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Hari / Tanggal : ".$arrHari[date("w", mktime(0,0,0,$m,$d,$Y))].", ".getTanggal(setTanggal($_GET[tSearch]),"t"));
		$objPHPExcel->getActiveSheet()->setCellValue('E3', "Hari / Tanggal : ".$arrHari[date("w", mktime(0,0,0,$m,$d,$Y))].", ".getTanggal(setTanggal($_GET[tSearch]),"t"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'DEPARTEMEN');
		
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'DEPARTEMEN');
								
		
		
		$arrShift=arrayQuery("select idShift from dta_shift where (left(kodeShift,1)=3 or right(kodeShift,3)='2/3') and statusShift='t'");
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		$sWhere.= " and t3.tanggalJadwal='".setTanggal($_GET['tSearch'])."' and t3.idShift in ('".implode("','", $arrShift)."')";
		if($_GET['pSearch'] == "NAMA")
			$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else if($_GET['pSearch'] == "NO ID")
			$sWhere.= " and lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else
			$sWhere.= " and (
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";	
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
		
		$sql="select * from dta_pegawai t1 join mst_data t2 join dta_jadwal t3 on (t1.dept_id=t2.kodeData and t1.id=t3.idPegawai) $sWhere order by t2.namaData, t1.name";		
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			$arrPegawai[]=$r;
		}
		
		$count = count($arrPegawai);
		$max = ceil($count/2) + 4;
		
		$rows = 5;
		$no=1;
		for($i=0; $i<=$max; $i++){
			$r=$arrPegawai[$i];
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[name]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[namaData]);
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':C'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$rows++;
			$no++;
		}
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$rows = 5;
		for($i=$max+1; $i<$count; $i++){
			$r=$arrPegawai[$i];
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $no);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[name]);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[namaData]);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$rows++;
			$no++;
		}
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$rows.':G'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, "TOTAL : ".getAngka($count));
		
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':G'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$rows.':G'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, "Ciater, ".getTanggal(setTanggal($_GET[tSearch]),"t"));
		
		$rows++;
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$rows.':G'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, "Time Keeper");
		
		$rows++;
		$rows++;
		$rows++;
		
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$rows.':G'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, "............................");
		$rows++;				
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows+20)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows+20)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:G'.$rows+20)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("Laporan");
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile."Jatah Ekstra Pudding.xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;			
			case "get":
				$text=gData();
			break;
			
			case "end":
				if(isset($menuAccess[$s]["add"])) $text = endProses();
			break;
			case "dat":
				if(isset($menuAccess[$s]["add"])) $text = setData();
			break;
			case "tab":
				if(isset($menuAccess[$s]["add"])) $text = setTable();
			break;
				
			case "upl":
				$text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
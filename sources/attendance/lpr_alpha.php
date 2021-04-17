<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
		
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;
		if(empty($par[tanggalAwal])) $par[tanggalAwal]=date('d/m/Y');
		if(empty($par[tanggalAkhir])) $par[tanggalAkhir]=date('d/m/Y');
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[13]."' order by urutanData");
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[10]."' order by urutanData");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 610px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
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
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
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
						<td><input type=\"text\" id=\"tanggalAwal\" name=\"par[tanggalAwal]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAwal]."\" class=\"vsmallinput hasDatePicker\" /></td>
						<td>s.d</td>
						<td><input type=\"text\" id=\"tanggalAkhir\" name=\"par[tanggalAkhir]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAkhir]."\" class=\"vsmallinput hasDatePicker\" /></td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
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
					<th style=\"min-width:100px;\">Nama</th>
					<th width=\"75\">NPP</th>												
					<th style=\"min-width:100px;\">Lokasi</th>
					<th style=\"min-width:100px;\">Divisi</th>
					<th style=\"min-width:100px;\">Jabatan</th>
					<th width=\"75\">Tanggal</th>
					<th style=\"min-width:100px;\">Alasan</th>
				</tr>
			</thead>
			<tbody>";
		if(!empty($par[idLokasi])) $filter=" and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId])) $filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[statusPegawai])) 
			$filter.= " and t1.cat = '".$par[statusPegawai]."'";
				
		if($par[search] == "Nama")
			$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";
		
		$sql="select * from dta_absen where date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' or date(selesaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tanggalAbsen) = explode(" ", $r[mulaiAbsen]);
			list($selesaiAbsen) = explode(" ", $r[selesaiAbsen]);			
			while($tanggalAbsen <= $selesaiAbsen){				
				list($Y,$m,$d) = explode("-", $tanggalAbsen);
				$arrMasuk["".$r[idPegawai].""][$tanggalAbsen] = $r[statusAbsen];
				$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}
		
		
		$tanggalJadwal=setTanggal($par[tanggalAkhir]) < date("Y-m-d") ? setTanggal($par[tanggalAkhir]) : date("Y-m-d");
		$sql="select * from dta_jadwal t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".$tanggalJadwal."' ".$filter." and t1.idShift not in (select idShift from dta_shift where mulaiShift='00:00:00' and selesaiShift='00:00:00') and t1.idShift!='0'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){		
			$arrPegawai[] = $r[idPegawai];
			if(!isset($arrMasuk["".$r[idPegawai].""]["".$r[tanggalJadwal].""])){
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>".$arrLokasi["$r[location]"]."</td>
					<td>".$arrDivisi["$r[rank]"]."</td>
					<td>$r[pos_name]</td>
					<td align=\"center\">".getTanggal($r[tanggalJadwal])."</td>
					<td>&nbsp;</td>
					</tr>";
			}
		}

		$idPegawai = implode(", ", $arrPegawai);

		$arrAbsen = arrayQuery("select idPegawai, date(mulaiAbsen), idAbsen from dta_absen where date(mulaiAbsen) BETWEEN '".setTanggal($par[tanggalAwal])."' AND '".setTanggal($par[tanggalAkhir])."'");

		$begin = new DateTime(setTanggal($par[tanggalAwal]));
		$end = new DateTime(setTanggal($par[tanggalAkhir]));

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);

		foreach ($period as $dt) {
			$day_num = $dt->format("N");
			if($day_num < 6){
				$arrTanggal[] = $dt->format('Y-m-d');
			}
		}

		array_push($arrTanggal, setTanggal($par[tanggalAkhir]));

		if(empty($arrTanggal)){
			$arrTanggal[] = setTanggal($par[tanggalAwal]);
		}

		$sql = "select * from dta_pegawai where id NOT IN($idPegawai)";
		$res = db($sql);
		while($r=mysql_fetch_array($res)){	
			foreach ($arrTanggal as $key => $value) {
				if(empty($arrAbsen[$r[id]][$value])){
					$no++;
					$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>".$arrLokasi["$r[location]"]."</td>
					<td>".$arrDivisi["$r[rank]"]."</td>
					<td>$r[pos_name]</td>
					<td>".getTanggal($value)."</td>
					<td></td>
					</tr>";
				}
    	
    		}
			
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[13]."' order by urutanData");
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[10]."' order by urutanData");
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);		
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PEGAWAI TIDAK MASUK KERJA');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($par[tanggalAwal],"t")." s.d ".getTanggal($par[tanggalAkhir],"t"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'LOKASI');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'DIVISI');		
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'ALASAN');		
								
		$rows = 5;
		if(!empty($par[idLokasi])) $filter=" and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId])) $filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[statusPegawai])) $filter.= " and t2.cat = '".$par[statusPegawai]."'";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";

		$sql="select * from dta_absen where date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."' or date(selesaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tanggalAbsen) = explode(" ", $r[mulaiAbsen]);
			list($selesaiAbsen) = explode(" ", $r[selesaiAbsen]);			
			while($tanggalAbsen <= $selesaiAbsen){				
				list($Y,$m,$d) = explode("-", $tanggalAbsen);
				$arrMasuk["".$r[idPegawai].""][$tanggalAbsen] = $r[statusAbsen];
				$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}
		
		$tanggalJadwal=setTanggal($par[tanggalAkhir]) < date("Y-m-d") ? setTanggal($par[tanggalAkhir]) : date("Y-m-d");
		$sql="select * from dta_jadwal t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".$tanggalJadwal."' ".$filter." and t1.idShift not in (select idShift from dta_shift where mulaiShift='00:00:00' and selesaiShift='00:00:00') and t1.idShift!='0'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){		
			$arrPegawai[] = $r[idPegawai];
			if(!isset($arrMasuk["".$r[idPegawai].""]["".$r[tanggalJadwal].""])){
				$no++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrLokasi["$r[location]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrDivisi["$r[rank]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($r[tanggalJadwal]));				
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$rows++;
			}			
		}

		$idPegawai = implode(", ", $arrPegawai);

		$arrAbsen = arrayQuery("select idPegawai, date(mulaiAbsen), idAbsen from dta_absen where date(mulaiAbsen) BETWEEN '".setTanggal($par[tanggalAwal])."' AND '".setTanggal($par[tanggalAkhir])."'");

		$begin = new DateTime(setTanggal($par[tanggalAwal]));
		$end = new DateTime(setTanggal($par[tanggalAkhir]));

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);

		foreach ($period as $dt) {
			$day_num = $dt->format("N");
			if($day_num < 6){
				$arrTanggal[] = $dt->format('Y-m-d');
			}
		}

		array_push($arrTanggal, setTanggal($par[tanggalAkhir]));

		if(empty($arrTanggal)){
			$arrTanggal[] = setTanggal($par[tanggalAwal]);
		}

		$sql = "select * from dta_pegawai where id NOT IN($idPegawai)";
		$res = db($sql);
		while($r=mysql_fetch_array($res)){	
			foreach ($arrTanggal as $key => $value) {
				if(empty($arrAbsen[$r[id]][$value])){
					$no++;
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrLokasi["$r[location]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrDivisi["$r[rank]"]);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getTanggal($value));				
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
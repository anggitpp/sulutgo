<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;
		if(empty($par[tanggalMulai])){
			$m = date("m") == 1 ? 12 : date("m")-1;
			$Y = date("m") == 1 ? date("Y")-1 : date("Y");
			$par[tanggalMulai] = "21/".str_pad($m, 2, "0", STR_PAD_LEFT)."/".$Y;
		}				
		$par[tanggalSelesai] = empty($par[tanggalSelesai]) ? date('20/m/Y') : $par[tanggalSelesai];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 700px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
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
										<input type=\"text\" class=\"smallinput hasDatePicker\" name=\"par[tanggalMulai]\" value=\"".$par[tanggalMulai]."\" onchange=\"document.getElementById('form').submit();\">
										&nbsp;s/d&nbsp;
										<input type=\"text\" class=\"smallinput hasDatePicker\" name=\"par[tanggalSelesai]\" value=\"".$par[tanggalSelesai]."\" onchange=\"document.getElementById('form').submit();\">
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
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" style=\"min-width:100px;\">No. SPL</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th colspan=\"2\" width=\"100\">Jadwal Shift</th>
					<th colspan=\"3\" width=\"150\">Izin Lembur</th>
					<th colspan=\"3\" width=\"150\">Data Absen</th>
					<th rowspan=\"2\" width=\"50\">Hari Kerja/ Libur</th>
					<th colspan=\"2\" width=\"100\">Approval</th>					
				</tr>
				<tr>
					<th width=\"50\">In</th>
					<th style=\"50\">Out</th>
					<th width=\"50\">In</th>
					<th style=\"50\">Out</th>
					<th style=\"50\">Durasi</th>
					<th width=\"50\">In</th>
					<th style=\"50\">Out</th>
					<th style=\"50\">Durasi</th>
					<th style=\"50\">Overtime</th>
					<th style=\"50\">Status</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where t1.idPegawai is not null and t1.tanggalJadwal between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";	
		
		$arrShift=arrayQuery("select t1.idPegawai, t2.kodeShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select t1.idPegawai, t1.tanggalJadwal, t2.kodeShift from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) $filter order by t1.idJadwal");
		
		$filter = "where idPegawai is not null and tanggalAbsen between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, concat(masukAbsen, '\t', pulangAbsen, '\t', durasiAbsen) from att_absen $filter order by idAbsen");
				
		$filter = "where nomorLembur is not null and t2.location in ($areaCheck) and t1.mulaiLembur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";		
				
		$sql="select * from att_lembur t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;
			
			list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);					
			list($mulaiLembur_jam, $mulaiLembur_menit, $mulaiLembur_detik) = explode(":", $mulaiLembur);
			
			list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
			list($selesaiLembur_tahun, $selesaiLembur_bulan, $selesaiLembur_hari) = explode("-", $selesaiLembur_tanggal);
			list($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik) = explode(":", $selesaiLembur);
			
			$tanggalLembur = $mulaiLembur_tanggal;
			$jadwalShift = empty($arrJadwal["$r[id]"][$tanggalLembur]) ? $arrShift["$r[id]"] : $arrJadwal["$r[id]"][$tanggalLembur];
			$durasiLembur = $mulaiLembur_jam > $selesaiLembur_jam ?
			selisihJam($r[mulaiLembur], date('Y-m-d H:i:s', dateAdd("d", 1, mktime($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik, $selesaiLembur_bulan, $selesaiLembur_hari, $selesaiLembur_tahun)))):
			selisihJam($r[mulaiLembur], $r[selesaiLembur]);
			
			list($mulaiAbsen, $selesaiAbsen, $durasiAbsen) = explode("\t", $arrAbsen["$r[id]"][$tanggalLembur]);
			$week = date("w", strtotime($tanggalLembur));
			$hariLembur = (getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($jadwalShift, array("OFF","C")) || (in_array($week, array(0,6)) && in_array($jadwalShift, array("N")))) ? "LIBUR" : "KERJA";
			$overtimeLembur = empty($r[overtimeLembur]) ? $durasiAbsen : $r[overtimeLembur];
			
			$text.="<tr>
					<td>$no.</td>		
					<td>$r[reg_no]</td>
					<td>$r[nomorLembur]</td>
					<td align=\"center\">".getTanggal($tanggalLembur)."</td>
					<td align=\"center\">".$jadwalShift."</td>
					<td align=\"center\">".$jadwalShift."</td>
					<td align=\"center\">".substr($mulaiLembur,0,5)."</td>
					<td align=\"center\">".substr($selesaiLembur,0,5)."</td>
					<td align=\"center\">".getAngka($durasiLembur)."</td>
					<td align=\"center\">".substr($mulaiAbsen,0,5)."</td>
					<td align=\"center\">".substr($selesaiAbsen,0,5)."</td>
					<td align=\"center\">".getAngka($durasiAbsen)."</td>
					<td align=\"center\">".$hariLembur."</td>
					<td align=\"center\">".getAngka($overtimeLembur)."</td>
					<td align=\"center\">$persetujuanLembur</td>					
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		if(empty($par[tanggalMulai])){
			$m = date("m") == 1 ? 12 : date("m")-1;
			$Y = date("m") == 1 ? date("Y")-1 : date("Y");
			$par[tanggalMulai] = "21/".str_pad($m, 2, "0", STR_PAD_LEFT)."/".$Y;
		}				
		$par[tanggalSelesai] = empty($par[tanggalSelesai]) ? date('20/m/Y') : $par[tanggalSelesai];
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:O3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'REKAP LEMBUR BULANAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanLembur])." ".$par[tahunLembur]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:O5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:O5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		$objPHPExcel->getActiveSheet()->mergeCells('M4:M5');
		
		$objPHPExcel->getActiveSheet()->mergeCells('E4:F4');
		$objPHPExcel->getActiveSheet()->mergeCells('G4:I4');
		$objPHPExcel->getActiveSheet()->mergeCells('J4:L4');
		$objPHPExcel->getActiveSheet()->mergeCells('N4:O4');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NO. SPL');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JADWAL SHIFT');		
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'IZIN LEMBUR');		
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'DATA ABSEN');	
		$objPHPExcel->getActiveSheet()->setCellValue('M4', 'HARI KERJA/ LIBUR');
		$objPHPExcel->getActiveSheet()->setCellValue('N4', 'APPROVAL');
									
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'IN');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'OUT');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'IN');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'OUT');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'DURASI');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'IN');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'OUT');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'DURASI');
		$objPHPExcel->getActiveSheet()->setCellValue('N5', 'OVERTIME');
		$objPHPExcel->getActiveSheet()->setCellValue('O5', 'STATUS');
									
		$rows = 6;
		$filter = "where t1.idPegawai is not null and t1.tanggalJadwal between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";	
		
		$arrShift=arrayQuery("select t1.idPegawai, t2.kodeShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select t1.idPegawai, t1.tanggalJadwal, t2.kodeShift from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) $filter order by t1.idJadwal");
		
		$filter = "where idPegawai is not null and tanggalAbsen between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, concat(masukAbsen, '\t', pulangAbsen, '\t', durasiAbsen) from att_absen $filter order by idAbsen");
				
		$filter = "where nomorLembur is not null and t2.location in ($areaCheck) and t1.mulaiLembur between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";		
				
		$sql="select * from att_lembur t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
		// echo $sql;
		// die();
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;
			
			list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);					
			list($mulaiLembur_jam, $mulaiLembur_menit, $mulaiLembur_detik) = explode(":", $mulaiLembur);
			
			list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
			list($selesaiLembur_tahun, $selesaiLembur_bulan, $selesaiLembur_hari) = explode("-", $selesaiLembur_tanggal);
			list($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik) = explode(":", $selesaiLembur);
			
			$tanggalLembur = $mulaiLembur_tanggal;
			$jadwalShift = empty($arrJadwal["$r[id]"][$tanggalLembur]) ? $arrShift["$r[id]"] : $arrJadwal["$r[id]"][$tanggalLembur];
			$durasiLembur = $mulaiLembur_jam > $selesaiLembur_jam ?
			selisihJam($r[mulaiLembur], date('Y-m-d H:i:s', dateAdd("d", 1, mktime($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik, $selesaiLembur_bulan, $selesaiLembur_hari, $selesaiLembur_tahun)))):
			selisihJam($r[mulaiLembur], $r[selesaiLembur]);
			
			list($mulaiAbsen, $selesaiAbsen, $durasiAbsen) = explode("\t", $arrAbsen["$r[id]"][$tanggalLembur]);
			$week = date("w", strtotime($tanggalLembur));
			$hariLembur = (getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($jadwalShift, array("OFF","C")) || (in_array($week, array(0,6)) && in_array($jadwalShift, array("N")))) ? "LIBUR" : "KERJA";
			$overtimeLembur = empty($r[overtimeLembur]) ? $durasiAbsen : $r[overtimeLembur];			
			
			if($r[persetujuanLembur] == "t") $objPHPExcel->getActiveSheet()->getStyle('O'.$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("11199000");
			else if($r[persetujuanLembur] == "f") $objPHPExcel->getActiveSheet()->getStyle('O'.$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fff00000");
			else $objPHPExcel->getActiveSheet()->getStyle('O'.$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("fffff000");
						
			
			$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':O'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':O'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[nomorLembur]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getTanggal($r[tanggalLembur]));			
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jadwalShift);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $jadwalShift);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, empty($mulaiLembur) ? "" : substr($mulaiLembur,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, empty($selesaiLembur) ? "" : substr($selesaiLembur,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, getAngka($durasiLembur));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, empty($mulaiAbsen) ? "" : substr($mulaiAbsen,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, empty($selesaiAbsen) ? "" : substr($selesaiAbsen,0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$rows, getAngka($durasiAbsen));
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $hariLembur);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$rows, getAngka($overtimeLembur));
			
			$rows++;							
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
		$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('N4:N'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('O4:O'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:O'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:O'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:O'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
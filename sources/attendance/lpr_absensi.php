<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;
		if(empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');	
		if(empty($par[tanggalAbsen2])) $par[tanggalAbsen2] = date('d/m/Y');						
		
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
		<td>&nbsp;&nbsp;
		
		<input type=\"text\" id=\"tanggalAbsen\" name=\"par[tanggalAbsen]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAbsen]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />&nbsp;s/d&nbsp;
		<input type=\"text\" id=\"tanggalAbsen2\" name=\"par[tanggalAbsen2]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAbsen2]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />
		
		</td>
		</tr>
		</table>
		</div>
		<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 190px; top: 40px; z-index: 800;\">
		<div id=\"dFilter\" style=\"visibility:collapse;\">
		<p>
		<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[38]) . "</label>
		<div class=\"field\" style=\"margin-left:150px;\">
		".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       where t2.kodeCategory='X03' order by t1.urutanData", "id", "description", "par[dirId]", "--".strtoupper($arrParameter[38])."--", $par[dirId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
		<div class=\"field\" style=\"margin-left:150px;\">
		".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       where t3.kodeCategory='X03' AND t1.kodeInduk = '$par[dirId]' order by t1.urutanData", "id", "description", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
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
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>					
		<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
		<th rowspan=\"2\" width=\"100\">NPP</th>
		<th rowspan=\"2\" width=\"100\">Tanggal</th>
		<th colspan=\"2\" style=\"width:80px;\">Jadwal</th>					
		<th colspan=\"2\" style=\"width:80px;\">Aktual</th>
		<th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
		<th rowspan=\"2\" width=\"85\">Keterangan</th>					
		</tr>
		<tr>
		<th style=\"width:40px;\">Masuk</th>
		<th style=\"width:40px;\">Pulang</th>
		<th style=\"width:40px;\">Masuk</th>
		<th style=\"width:40px;\">Pulang</th>
		</tr>
		</thead>
		<tbody>";		
		
		$arrNormal=getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='OFFICE HOUR'");
		$arrShift=arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select idPegawai, tanggalJadwal, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where t1.tanggalJadwal BETWEEN '".setTanggal($par[tanggalAbsen])."' AND '".setTanggal($par[tanggalAbsen2])."'");
		
		$filter = "where date(t1.mulaiAbsen) BETWEEN '".setTanggal($par[tanggalAbsen])."' AND '".setTanggal($par[tanggalAbsen2])."' and t2.location in ($areaCheck)";		
		
		if(!empty($par[idLokasi]))
		$filter.= " and t2.location='".$par[idLokasi]."'";
        if(!empty($par[dirId]))
            $filter.= " and t2.dir_id='".$par[dirId]."'";
		if(!empty($par[divId]))
		$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
		$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
		$filter.= " and t2.unit_id='".$par[unitId]."'";
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
		
		$sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
		// echo $sql."<br>";
		// echo "<pre>";
		// print_r($arrJadwal);
		// echo "</pre>";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = isset($arrShift["$r[idPegawai]"]) ?
			explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;
			
			list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
			list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);
			
			if(isset($arrJadwal["$r[idPegawai]"][$r[tanggalAbsen]]))
			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"][$r[tanggalAbsen]]);
			
			if($r[mulaiShift] == "00:00:00" && $r[mulaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c","off"))) list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);
			
			$arr["$r[idPegawai]"]=$r;

			$no++;			
				
				if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
				if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
				
				$text.="<tr>
				<td>$no.</td>
				<td>".strtoupper($r[name])."</td>
				<td>$r[reg_no]</td>
				<td>$r[tanggalAbsen]</td>
				<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
				<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
				<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
				<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
				<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
				<td>$r[keteranganAbsen]</td>						
				</tr>";		
		}
		
		// if(is_array($arr)){				
		// 	reset($arr);		
		// 	while(list($idPegawai, $r)=each($arr)){
		// 		$no++;			
				
		// 		if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
		// 		if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
				
		// 		$text.="<tr>
		// 		<td>$no.</td>
		// 		<td>".strtoupper($r[name])."</td>
		// 		<td>$r[reg_no]</td>
		// 		<td>$r[tanggalAbsen]</td>
		// 		<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
		// 		<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
		// 		<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
		// 		<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
		// 		<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
		// 		<td>$r[keteranganAbsen]</td>						
		// 		</tr>";			
		// 	}
		// }
		
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
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
		->setLastModifiedBy($cNama)
		->setTitle($arrTitle[$s]);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN ABSENSI HARIAN');
		if(empty($par[tanggalAbsen2])){
			$par[tanggalAbsenN] = $par[tanggalAbsen];
		}else{
			$par[tanggalAbsenN] = $par[tanggalAbsen]." - ".$par[tanggalAbsen2];
		}
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Tanggal : '.$par[tanggalAbsenN]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:F4');
		$objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
		$objPHPExcel->getActiveSheet()->mergeCells('I4:I5');
		$objPHPExcel->getActiveSheet()->mergeCells('J4:J5');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TANGGAL');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JADWAL');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'AKTUAL');				
		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'DURASI');
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'KETERANGAN');
		
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'MASUK');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'PULANG');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'MASUK');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'PULANG');								
		
		$rows = 6;
		$arrNormal=getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='OFFICE HOUR'");
		$arrShift=arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select idPegawai, tanggalJadwal, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where t1.tanggalJadwal BETWEEN '".setTanggal($par[tanggalAbsen])."' AND '".setTanggal($par[tanggalAbsen2])."'");
		
		$filter = "where date(t1.mulaiAbsen) BETWEEN '".setTanggal($par[tanggalAbsen])."' AND '".setTanggal($par[tanggalAbsen2])."' and t2.location in ($areaCheck)";		
		
		if(!empty($par[idLokasi]))
		$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[dirId]))
		$filter.= " and t2.dir_id='".$par[dirId]."'";
        if(!empty($par[divId]))
            $filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
		$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
		$filter.= " and t2.unit_id='".$par[unitId]."'";
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
		
		$sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
		// echo $sql;
		// echo "<pre>";
		// print_r($arrJadwal);
		// echo "</pre>";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = isset($arrShift["$r[idPegawai]"]) ?
			explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;
			
			list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
			list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);
			
			if(isset($arrJadwal["$r[idPegawai]"][$r[tanggalAbsen]]))
			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"][$r[tanggalAbsen]]);
			
			if($r[mulaiShift] == "00:00:00" && $r[mulaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c","off"))) list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);
			
			$arr["$r[idPegawai]"]=$r;

			$no++;			
				
				if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
				if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
				
				$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':I'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[tanggalAbsen]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, substr($r[mulaiShift],0,5));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, substr($r[selesaiShift],0,5));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, substr($r[masukAbsen],0,5));
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, substr($r[pulangAbsen],0,5));
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, substr(str_replace("-","",$r[durasiAbsen]),0,5));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $r[keteranganAbsen]);
				
				$rows++;	

		}
		
		// if(is_array($arr)){				
		// 	reset($arr);		
		// 	while(list($idPegawai, $r)=each($arr)){
							
		// 	}
		// }
		
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
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='TJL'");
		list($mulaiKomponen, $selesaiKomponen) = explode("\t", getField("select concat(mulaiKomponen, '\t', selesaiKomponen) from dta_komponen where idKomponen='".$idKomponen."'"));
		
		$mulaiPeriode = $par[tahunProses]."-".str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."-".$mulaiKomponen;
		$hariMulai = $selesaiKomponen == 31 ? date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01")) : $selesaiKomponen;
		$selesaiPeriode = $par[tahunProses]."-".str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."-".$hariMulai;

		list($Ys,$Ms,$Ds) = explode("-", $mulaiPeriode);
		list($Ye,$Me,$De) = explode("-", $selesaiPeriode);
		
		$mulaiPeriode = date("Y-m-d", dateMin("m", 1, mktime(0,0,0,$Ms,$Ds,$Ys)));
		$selesaiPeriode = date("Y-m-d", dateMin("d", $hariMulai, mktime(0,0,0,$Me,$De,$Ye)));
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 700px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
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
						<td>Lokasi Proses : </td>
						<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."</td>
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
					<th style=\"width:150px;\">NIP</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th style=\"width:150px;\">Jumlah Jam</th>
					<th style=\"width:150px;\">Rupiah</th>
				</tr>
			</thead>
			<tbody>";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$filter = " and t2.status='".$status."' AND t2.location IN ( $areaCheck ) ";		
			
			if(!empty($par[idStatus]))
				$filter.= " and t2.cat='".$par[idStatus]."'";
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.location='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";			
			else if($par[search] == "NPP")
				$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";
			
			$sql="select * from pay_pokok where tanggalPokok<='".$selesaiPeriode."' order by tanggalPokok";
			$res=db($sql);			
			while($r=mysql_fetch_array($res)){		
				$arrGaji["".$r[idPegawai].""] = $r[nilaiPokok];
			}
			$pegawaiTetap = getField("select kodeData from mst_data where kodeCategory='S04' and urutanData='1'");
			
			$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.mulaiLembur) between '".$mulaiPeriode."' and '".$selesaiPeriode."' and t1.sdmLembur='t' ".$filter." order by t1.mulaiLembur";
			$res=db($sql);			
			while($r=mysql_fetch_array($res)){	
				$arrPegawai["$r[idPegawai]"]=$r[name]."\t".$r[reg_no]."\t".$r[pos_name]."\t".$r[div_id];
							
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
				
				#GAJI POKOK			
				$nilaiPokok = $arrGaji["$r[idPegawai]"];
				$nilaiUpah=$nilaiPokok / 173;
								
				list($tanggalLembur) = explode(" ", $r[mulaiLembur]);					
				$week = date("w", strtotime($tanggalLembur));
				$overtimeLembur = getAngka($r[overtimeLembur]);
				$r[durasiLembur] = empty($overtimeLembur) ? $durasiLembur : $overtimeLembur;
				
				$namaShift = getField("select lower(trim(t2.namaShift)) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal='".$tanggalLembur."' and t1.idPegawai='".$r[idPegawai]."'");
				
				$nilaiLembur = 0;
				$nilaiMakan = 0;
				$nilaiTransport = 0;
				
				#hari libur
				if(getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($namaShift, array("off","cuti")) || (in_array($week, array(0,6)) && in_array($namaShift, array("", "office hour")))){		
					if($r[durasiLembur] > 8){
						$nilaiLembur+=(8 * 2 * $nilaiUpah) + (3 * $nilaiUpah) + (($r[durasiLembur]-9) * 4 * $nilaiUpah);
					}/*else if($r[durasiLembur] > 7){
						$nilaiLembur+=(7 * 2 * $nilaiUpah) + (($r[durasiLembur]-7) * 3 * $nilaiUpah);
					}*/else{
						$nilaiLembur+=$r[durasiLembur] * 2 * $nilaiUpah;
					}
					$hariLembur = "LIBUR";
				#hari biasa
				}else{
					if($r[durasiLembur] > 1){
						$nilaiLembur+=(1.5 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
						//$nilaiLembur+=(2 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
					}else{
						$nilaiLembur+=$r[durasiLembur] * 1.5 * $nilaiUpah;
						//$nilaiLembur+=$r[durasiLembur] * 2 * $nilaiUpah;
					}
					$hariLembur = "KERJA";
				}
				$nilaiLembur = $pegawaiTetap == $r[cat] ? $nilaiLembur : 0.75 * $nilaiLembur;
				
				/*
				if($r[durasiLembur] >= 3 && $r[durasiLembur] <= 5)
					$nilaiMakan = 15000;
				else if($r[durasiLembur] > 5 && $r[durasiLembur] <= 10)
					$nilaiMakan = 25000;
				else if($r[durasiLembur] > 10 && $r[durasiLembur] <= 15)
					$nilaiMakan = 35000;
				else if($r[durasiLembur] > 15 && $r[durasiLembur] <= 20)
					$nilaiMakan = 45000;
				else if($r[durasiLembur] > 20 && $r[durasiLembur] <= 24)
					$nilaiMakan = 55000;
				*/
				
				$nilaiTransport = $hariLembur == "LIBUR" ? 40000 : 0;
				$nilaiTransport = $mulaiLembur_jam >= 21 ? 40000 : $nilaiTransport;
				$nilaiTransport = $selesaiLembur_jam >= 21 ? 40000 : $nilaiTransport;
				$nilaiTransport = $pegawaiTetap == $r[cat] ? $nilaiTransport : 0;
				$nilaiTransport = (in_array($week, array(0,6)) && !in_array($namaShift, array(""))) ? 0 : $nilaiTransport;
				
				$jamLembur["$r[idPegawai]"]+= $r[durasiLembur];
				$arrLembur["$r[idPegawai]"]+= $nilaiLembur + $nilaiMakan + $nilaiTransport;
			}
			
			if(is_array($arrPegawai)){
				reset($arrPegawai);
				while (list($id, $val) = each($arrPegawai)){
					list($name, $reg_no) = explode("\t", $val);
					$no++;	
					$text.="<tr>
							<td>$no.</td>
							<td>".$reg_no."</td>
							<td>".$name."</td>
							<td align=\"right\">".getAngka($jamLembur[$id])."</td>
							<td align=\"right\">".getAngka($arrLembur[$id])."</td>
						</tr>";		
					$totalJam+=$jamLembur[$id];
					$totalNilai+=$arrLembur[$id];
				}
			}
			
			$text.="</tbody>
			<tfoot>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><strong>TOTAL</strong></td>
				<td style=\"text-align:right\">".getAngka($totalJam)."</td>
				<td style=\"text-align:right\">".getAngka($totalNilai)."</td>
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
		
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='TJL'");
		list($mulaiKomponen, $selesaiKomponen) = explode("\t", getField("select concat(mulaiKomponen, '\t', selesaiKomponen) from dta_komponen where idKomponen='".$idKomponen."'"));
		
		$mulaiPeriode = $par[tahunProses]."-".str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."-".$mulaiKomponen;
		$hariMulai = $selesaiKomponen == 31 ? date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01")) : $selesaiKomponen;
		$selesaiPeriode = $par[tahunProses]."-".str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."-".$hariMulai;

		list($Ys,$Ms,$Ds) = explode("-", $mulaiPeriode);
		list($Ye,$Me,$De) = explode("-", $selesaiPeriode);
		
		$mulaiPeriode = date("Y-m-d", dateMin("m", 1, mktime(0,0,0,$Ms,$Ds,$Ys)));
		$selesaiPeriode = date("Y-m-d", dateMin("d", $hariMulai, mktime(0,0,0,$Me,$De,$Ye)));
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAPITULASI LEMBUR');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode : '.getBulan($par[bulanProses])." ".$par[tahunProses]);
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NIP');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JUMLAH JAM');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'RUPIAH');
									
		$rows = 5;						
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
		$filter = " and t2.status='".$status."' AND t2.location IN ( $areaCheck ) ";		
		
		if(!empty($par[idStatus]))
			$filter.= " and t2.cat='".$par[idStatus]."'";
		
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";			
		else if($par[search] == "NPP")
			$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			)";
		
		$sql="select * from pay_pokok where tanggalPokok<='".$selesaiPeriode."' order by tanggalPokok";
		$res=db($sql);			
		while($r=mysql_fetch_array($res)){		
			$arrGaji["".$r[idPegawai].""] = $r[nilaiPokok];
		}
		$pegawaiTetap = getField("select kodeData from mst_data where kodeCategory='S04' and urutanData='1'");
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.mulaiLembur) between '".$mulaiPeriode."' and '".$selesaiPeriode."' and t1.sdmLembur='t' ".$filter." order by t1.mulaiLembur";
		$res=db($sql);			
		while($r=mysql_fetch_array($res)){	
			$arrPegawai["$r[idPegawai]"]=$r[name]."\t".$r[reg_no]."\t".$r[pos_name]."\t".$r[div_id];
						
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
			
			#GAJI POKOK			
			$nilaiPokok = $arrGaji["$r[idPegawai]"];
			$nilaiUpah=$nilaiPokok / 173;
							
			list($tanggalLembur) = explode(" ", $r[mulaiLembur]);					
			$week = date("w", strtotime($tanggalLembur));
			$overtimeLembur = getAngka($r[overtimeLembur]);
			$r[durasiLembur] = empty($overtimeLembur) ? $durasiLembur : $overtimeLembur;
			
			$namaShift = getField("select lower(trim(t2.namaShift)) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal='".$tanggalLembur."' and t1.idPegawai='".$r[idPegawai]."'");
			
			$nilaiLembur = 0;
			$nilaiMakan = 0;
			$nilaiTransport = 0;
			
			#hari libur
			if(getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($namaShift, array("off","cuti")) || (in_array($week, array(0,6)) && in_array($namaShift, array("", "office hour")))){		
				if($r[durasiLembur] > 8){
					$nilaiLembur+=(8 * 2 * $nilaiUpah) + (3 * $nilaiUpah) + (($r[durasiLembur]-9) * 4 * $nilaiUpah);
				}/*else if($r[durasiLembur] > 7){
					$nilaiLembur+=(7 * 2 * $nilaiUpah) + (($r[durasiLembur]-7) * 3 * $nilaiUpah);
				}*/else{
					$nilaiLembur+=$r[durasiLembur] * 2 * $nilaiUpah;
				}
				$hariLembur = "LIBUR";
			#hari biasa
			}else{
				if($r[durasiLembur] > 1){
					$nilaiLembur+=(1.5 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
					//$nilaiLembur+=(2 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
				}else{
					$nilaiLembur+=$r[durasiLembur] * 1.5 * $nilaiUpah;
					//$nilaiLembur+=$r[durasiLembur] * 2 * $nilaiUpah;
				}
				$hariLembur = "KERJA";
			}
			$nilaiLembur = $pegawaiTetap == $r[cat] ? $nilaiLembur : 0.75 * $nilaiLembur;
			
			/*
			if($r[durasiLembur] >= 3 && $r[durasiLembur] <= 5)
				$nilaiMakan = 15000;
			else if($r[durasiLembur] > 5 && $r[durasiLembur] <= 10)
				$nilaiMakan = 25000;
			else if($r[durasiLembur] > 10 && $r[durasiLembur] <= 15)
				$nilaiMakan = 35000;
			else if($r[durasiLembur] > 15 && $r[durasiLembur] <= 20)
				$nilaiMakan = 45000;
			else if($r[durasiLembur] > 20 && $r[durasiLembur] <= 24)
				$nilaiMakan = 55000;
			*/
			
			$nilaiTransport = $hariLembur == "LIBUR" ? 40000 : 0;
			$nilaiTransport = $mulaiLembur_jam >= 21 ? 40000 : $nilaiTransport;
			$nilaiTransport = $selesaiLembur_jam >= 21 ? 40000 : $nilaiTransport;
			$nilaiTransport = $pegawaiTetap == $r[cat] ? $nilaiTransport : 0;
			$nilaiTransport = (in_array($week, array(0,6)) && !in_array($namaShift, array(""))) ? 0 : $nilaiTransport;
			
			$jamLembur["$r[idPegawai]"]+= $r[durasiLembur];
			$arrLembur["$r[idPegawai]"]+= $nilaiLembur + $nilaiMakan + $nilaiTransport;
		}
		
		if(is_array($arrPegawai)){
			reset($arrPegawai);
			while (list($id, $val) = each($arrPegawai)){
				list($name, $reg_no) = explode("\t", $val);
				$no++;				
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $reg_no);					
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $name);				
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $jamLembur[$id]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrLembur[$id]);
				
				$totalJam+=$jamLembur[$id];
				$totalNilai+=$arrLembur[$id];
				$rows++;
			}
		}
			
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, "TOTAL");				
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $totalJam);					
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totalNilai);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Jakarta, ".date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"))." ".getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Dept. SDM");
		
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Who Dyah Wismorini");
		
		$rows++;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Direktur Operational & SDM");
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A5:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
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

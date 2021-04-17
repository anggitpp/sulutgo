<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess, $areaCheck;						
		if(empty($par[bulanLembur])) $par[bulanLembur] = date('m');
		if(empty($par[tahunLembur])) $par[tahunLembur] = date('Y');		
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
		list($idKomponen, $mulaiKomponen, $selesaiKomponen) = explode("\t", getField("select concat(idKomponen, '\t' ,mulaiKomponen, '\t', selesaiKomponen) from dta_komponen where kodeKomponen='".$kodeKomponen."'"));
		if($mulaiKomponen > $selesaiKomponen){
			$bulanMulai = $par[bulanLembur] == 1 ? 12 : $par[bulanLembur] - 1;
			$tahunMulai = $par[bulanLembur] == 1 ? $par[tahunLembur] - 1 : $par[tahunLembur];			
		}else{
			$bulanMulai = $par[bulanLembur];
			$tahunMulai = $par[tahunLembur];
		}					
		$mulaiPeriode = $tahunMulai."-".str_pad($bulanMulai, 2, "0", STR_PAD_LEFT)."-".$mulaiKomponen;
		$selesaiPeriode = $par[tahunLembur]."-".str_pad($par[bulanLembur], 2, "0", STR_PAD_LEFT)."-".$selesaiKomponen;
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[$s]."
				</h1>
				".getBread(ucwords("detail"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
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
										".comboMonth("par[bulanLembur]", $par[bulanLembur], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunLembur]", $par[tahunLembur], "", "onchange=\"document.getElementById('form').submit();\"")."
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
			<div id=\"pos_l\" style=\"float:left\">
				Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
			</div>
			<div id=\"pos_r\" style=\"float:right\">
				<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Nama</th>					
					<th style=\"min-width:150px;\">Jabatan</th>
					<th style=\"width:125px;\">Total</th>
					<th style=\"min-width:150px;\">Keterangan</th>
				</tr>
			</thead>
			<tbody>";			
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$filter = " and t2.status='".$status."' AND t2.group_id IN ( $areaCheck )";						
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."' AND t3.kodeData = '".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			list($tahun, $bulan) = explode("-", $mulaiPeriode);
			$periode=$tahun.$bulan;
			if(getField("select idProses from pay_proses where detailProses='pay_proses_".$periode."'"))
				$arrGaji=arrayQuery("select t1.idPegawai, sum(t1.nilaiProses) from pay_proses_".$periode." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idKomponen not in (5) and t2.tipeKomponen='t' group by 1");
			
			$sql="select *, CASE WHEN time(t1.selesaiLembur) >= time(t1.mulaiLembur) THEN TIMEDIFF(time(t1.selesaiLembur), time(t1.mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(t1.mulaiLembur)), TIMEDIFF(time(t1.selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur t1 join dta_pegawai t2 join mst_data t3 on (t1.idPegawai=t2.id and t2.dept_id=t3.kodeData) where date(t1.mulaiLembur) between '".$mulaiPeriode."' and '".$selesaiPeriode."' and t1.sdmLembur='t' ".$filter." order by t2.name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){	
				$arrPegawai["$r[idPegawai]"]=$r[name]."\t".$r[reg_no]."\t".$r[pos_name];
				
				#GAJI POKOK
				$nilaiPokok = $arrGaji["$r[idPegawai]"];
				$nilaiUpah=$nilaiPokok / 173;
								
				list($tanggalLembur) = explode(" ", $r[mulaiLembur]);					
				$week = date("w", strtotime($tanggalLembur));
				$overtimeLembur = getAngka($r[overtimeLembur]);
				$r[durasiLembur] = empty($overtimeLembur) ? $r[durasiLembur] : $r[overtimeLembur];	
				
				$namaShift = getField("select lower(trim(t2.namaShift)) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal='".$tanggalLembur."' and t1.idPegawai='".$r[idPegawai]."'");
				
				#hari libur
				$nilaiMakan=0;
				if(getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($namaShift, array("off","cuti")) || (in_array($week, array(0,6)) && in_array($namaShift, array("shift 1")))){										
					
					if($r[durasiLembur] > 8){
						$nilaiInsentif["$r[idPegawai]"]+=(7 * 2 * $nilaiUpah) + (3 * $nilaiUpah) + (($r[durasiLembur]-8) * 4 * $nilaiUpah);
					}else if($r[durasiLembur] > 7){
						$nilaiInsentif["$r[idPegawai]"]+=(7 * 2 * $nilaiUpah) + (($r[durasiLembur]-7) * 3 * $nilaiUpah);
					}else{
						$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 2 * $nilaiUpah;
					}
					
				#hari biasa
				}else{
					if($r[durasiLembur] > 1){
						//$nilaiInsentif["$r[idPegawai]"]+=(1.5 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
						$nilaiInsentif["$r[idPegawai]"]+=(2 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
					}else{
						//$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 1.5 * $nilaiUpah;
						$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 2 * $nilaiUpah;
					}
				}	
				$nilaiInsentif["$r[idPegawai]"]+=$nilaiMakan;
			}
			
			if(is_array($arrPegawai)){
				reset($arrPegawai);
				while (list($id, $val) = each($arrPegawai)){
				list($name, $reg_no, $pos_name) = explode("\t", $val);
					
				$no++;	
				$text.="<tr>
						<td>$no.</td>						
						<td>$reg_no</td>
						<td><a href=\"#\" onclick=\"openBox('ajax.php?par[mode]=det&par[mulaiPeriode]=$mulaiPeriode&par[selesaiPeriode]=$selesaiPeriode&par[idPegawai]=$id".getPar($par,"mode,mulaiPeriode,selesaiPeriode,idPegawai")."',925,550);\">".strtoupper($name)."</a></td>
						<td>$pos_name</td>
						<td align=\"right\">".getAngka($nilaiInsentif[$id])."</td>
						<td>&nbsp;</td>
					</tr>";
					$totInsentif+=$nilaiInsentif[$id];
				}
			}
			$text.="</tbody>
			<tfoot>				
				<tr>
					<td colspan=\"4\" style=\"text-align:right\"><strong>TOTAL</strong></td>
					<td style=\"text-align:right\"><span>".getAngka($totInsentif)."</span></td>
					<td>&nbsp;</td>
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
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		list($tahun, $bulan) = explode("-", $par[mulaiPeriode]);
		$periode=$tahun.$bulan;
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$periode."'"))
			$arrGaji=arrayQuery("select t1.idPegawai, sum(t1.nilaiProses) from pay_proses_".$periode." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idKomponen not in (5) and t2.tipeKomponen='t' group by 1");
		
		$sql="select * from dta_pegawai where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$nilaiPokok = $arrGaji["$par[idPegawai]"];
		$nilaiUpah=$nilaiPokok/173;
		$uangMakan=0;
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);				
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(25,5,'Nama','LTB',0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(50,5,$r[name],'LTBR',0,'L');		
		$pdf->Cell(50,5,'','T',0,'C');
		$pdf->Cell(25,5,'Esseon','LTB',0,'L');		
		$pdf->Cell(50,5,getField("select namaData from mst_data where kodeData='".$r[grade]."'"),'LTBR',0,'L');
		$pdf->Ln();		
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(25,5,'Title','LB',0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(50,5,$r[pos_name],'LBR',0,'L');
		$pdf->SetFont('Arial','B');
		$pdf->Cell(50,5,'PERHITUNGAN UPAH LEMBUR',0,0,'C');
		$pdf->SetFont('Arial');
		$pdf->Cell(25,5,'Gaji Pokok','LB',0,'L');		
		$pdf->Cell(50,5,getAngka($gajiPokok),'LBR',0,'L');
		$pdf->Ln();		
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(25,5,'Dept','LB',0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(50,5,getField("select namaData from mst_data where kodeData='".$r[dept_id]."'"),'LBR',0,'L');		
		$pdf->Cell(50,5,'','B',0,'C');
		$pdf->Cell(25,5,'Tarif/Jam','LB',0,'L');		
		$pdf->Cell(50,5,getAngka($nilaiUpah),'LBR',0,'L');
		$pdf->Ln();		
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(25,5,'Periode','LB',0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(50,5,getBulan($par[bulanLembur])." ".$par[tahunLembur],'LB',0,'L');		
		$pdf->Cell(15,5,'TOTAL','LB',0,'C');
		$pdf->Cell(35,5,'HARI KERJA','LB',0,'C');
		$pdf->Cell(25,5,'','LB',0,'L');		
		$pdf->Cell(50,5,'','LBR',0,'L');
		$pdf->Ln();
				
		$pdf->Cell(7,8,'NO','LB',0,'C');
		$pdf->Cell(18,8,'TANGGAL','LB',0,'C');
		$pdf->Cell(50,8,'NAMA PEKERJAAN','LB',0,'C');		
		$pdf->Cell(15,4,'JAM','L',0,'C');		
		$pdf->Cell(7,8,'(A)','LB',0,'C');
		$pdf->Cell(7,8,'(B)','LB',0,'C');
		$pdf->Cell(7,8,'(C)','LB',0,'C');
		$pdf->Cell(7,8,'(D)','LB',0,'C');
		$pdf->Cell(7,8,'(E)','LB',0,'C');
		$pdf->Cell(25,8,'MEALS','LB',0,'C');		
		$pdf->Cell(50,8,'','LBR',0,'C');
		$pdf->Ln(4);
		
		$pdf->Cell(75,4,'',0,0);
		$pdf->Cell(15,4,'LEMBUR','LB',0,'C');
		$pdf->Ln(4);
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.mulaiLembur) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and t1.sdmLembur='t' and t1.idPegawai='".$par[idPegawai]."' order by mulaiLembur";
		$res=db($sql);			
		$no=1;
		while($r=mysql_fetch_array($res)){						
								
			list($tanggalLembur) = explode(" ", $r[mulaiLembur]);					
			$week = date("w", strtotime($tanggalLembur));
			$overtimeLembur = getAngka($r[overtimeLembur]);
			$jamLembur = empty($overtimeLembur) ? $r[durasiLembur] : $r[overtimeLembur];
			$jamLembur = setAngka(getAngka($jamLembur));
			
			$namaShift = getField("select lower(trim(t2.namaShift)) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal='".$tanggalLembur."' and t1.idPegawai='".$par[idPegawai]."'");
			$jam1 = "";
			$jam2 = "";
			$jam3 = "";
			$jam4 = "";
			$jam5 = "";
			
			#hari libur
			if(getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($namaShift, array("off","cuti")) || (in_array($week, array(0,6)) && in_array($namaShift, array("shift 1")))){
				if($jamLembur > 8){
					$jam2 = 7;
					$jam3 = 1;
					$jam5 = $jamLembur-8;
					$nilaiLembur+=(7 * 2 * $nilaiUpah) + (3 * $nilaiUpah) + (($jamLembur-8) * 4 * $nilaiUpah);;
				}else if($jamLembur > 7){
					$jam2 = 7;
					$jam3 = $jamLembur-7;
					$nilaiLembur+=(7 * 2 * $nilaiUpah) + (($jamLembur-7) * 3 * $nilaiUpah);
				}else{
					$jam2 = 1;
					$nilaiLembur+=$jamLembur * 2 * $nilaiUpah;
				}
			#hari biasa
			}else{
				if($jamLembur > 1){
					$jam1 = 1;
					$jam2 = $jamLembur-1;
					//$nilaiLembur=(1.5 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
					$nilaiLembur+=(2 * $nilaiUpah) + (($jamLembur-1) * 2 * $nilaiUpah);
				}else{
					$jam1 = 1;
					//$nilaiLembur=$r[durasiLembur] * 1.5 * $nilaiUpah;
					$nilaiLembur+=$jamLembur * 2 * $nilaiUpah;
				}
			}
			
			$pdf->Cell(7,5,$no.'.','LB',0,'C');
			$pdf->Cell(18,5,getTanggal($tanggalLembur),'LB',0,'C');
			$pdf->Cell(50,5,$r[keteranganLembur],'LB',0,'L');		
			$pdf->Cell(15,5,$jamLembur,'LB',0,'C');		
			$pdf->Cell(7,5,getAngka($jam1),'LB',0,'C');
			$pdf->Cell(7,5,getAngka($jam2),'LB',0,'C');
			$pdf->Cell(7,5,getAngka($jam3),'LB',0,'C');
			$pdf->Cell(7,5,getAngka($jam4),'LB',0,'C');
			$pdf->Cell(7,5,getAngka($jam5),'LB',0,'C');
			$pdf->Cell(25,5,getAngka($uangMakan),'LB',0,'R');		
			$pdf->Cell(50,5,'','LBR',0,'C');
			$pdf->Ln();
			
			$totalJam+=$jamLembur;
			$totalJam1+=$jam1;
			$totalJam2+=$jam2;
			$totalJam3+=$jam3;
			$totalJam4+=$jam4;
			$totalJam5+=$jam5;
			$nilaiMakan+=$uangMakan;
			
			$no++;
		}
		
		$pdf->Cell(75,5,'TOTAL','LB',0,'C');		
		$pdf->Cell(15,5,$totalJam,'LB',0,'C');		
		$pdf->Cell(7,5,getAngka($totalJam1),'LB',0,'C');
		$pdf->Cell(7,5,getAngka($totalJam2),'LB',0,'C');
		$pdf->Cell(7,5,getAngka($totalJam3),'LB',0,'C');
		$pdf->Cell(7,5,getAngka($totalJam4),'LB',0,'C');
		$pdf->Cell(7,5,getAngka($totalJam5),'LB',0,'C');
		$pdf->Cell(25,5,getAngka($nilaiMakan),'LB',0,'R');		
		$pdf->Cell(50,5,'','LBR',0,'C');
		$pdf->Ln();
		
		$jumlahJam1=1.5 * $totalJam1;
		$pdf->Cell(75,5,'Total (A) Dikali','LB',0,'L');		
		$pdf->Cell(15,5,'1.5','LB',0,'C');		
		$pdf->Cell(14,5,$jumlahJam1,'LB',0,'C');
		$pdf->Cell(46,5,'Upah Lembur','LB',0,'L');
		$pdf->Cell(50,5,getAngka($nilaiLembur),'LBR',0,'R');
		$pdf->Ln();
		
		$jumlahJam2=2 * $totalJam2;
		$pdf->Cell(75,5,'Total (B) Dikali','LB',0,'L');		
		$pdf->Cell(15,5,'2','LB',0,'C');		
		$pdf->Cell(14,5,$jumlahJam2,'LB',0,'C');
		$pdf->Cell(46,5,'Uang Makan','LB',0,'L');
		$pdf->Cell(50,5,getAngka($nilaiMakan),'LBR',0,'R');
		$pdf->Ln();
		
		$jumlahJam3=3 * ($totalJam3 + $totalJam4);
		$pdf->Cell(75,5,'Total (C-D) Dikali','LB',0,'L');		
		$pdf->Cell(15,5,'3','LB',0,'C');		
		$pdf->Cell(14,5,$jumlahJam3,'LB',0,'C');
		$pdf->Cell(46,5,'','L',0,'L');
		$pdf->Cell(50,5,'','LR',0,'C');
		$pdf->Ln();
		
		$jumlahJam4=4 * $totalJam5;
		$pdf->Cell(75,5,'Total (E) Dikali','LB',0,'L');		
		$pdf->Cell(15,5,'4','LB',0,'C');		
		$pdf->Cell(14,5,$jumlahJam4,'LB',0,'C');
		$pdf->SetFont('Arial','B');
		$pdf->Cell(46,5,'TOTAL UPAH LEMBUR','L',0,'C');		
		$pdf->Cell(50,5,getAngka($nilaiLembur + $nilaiMakan),'LR',0,'R');
		$pdf->Ln();
		
		$pdf->SetFont('Arial');
		$pdf->Cell(90,5,'TOTAL','LB',0,'C');		
		$pdf->Cell(14,5,$jumlahJam1 + $jumlahJam2 + $jumlahJam3 + $jumlahJam4,'LB',0,'C');
		$pdf->Cell(46,5,'','LB',0,'L');
		$pdf->Cell(50,5,'','LBR',0,'C');
		$pdf->Ln(15);
		
		$pdf->Cell(80,5,'Approved by,');		
		$pdf->Cell(80,5,'Approved by,');		
		$pdf->Cell(40,5,'Payroll Master');
		$pdf->Ln();		
		
		$pdf->Cell(80,5,'Chief Accounting');		
		$pdf->Cell(80,5,'Human Resource Manager');		
		$pdf->Cell(40,5,getTanggal($tanggalLembur,"t"));
		$pdf->Ln(20);				
		
		$pdf->Cell(80,5,'(...........................................)');		
		$pdf->Cell(80,5,'(...........................................)');		
		$pdf->Cell(40,5,'(...........................................)');
		$pdf->Ln();	
		
		$pdf->Output();	
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		
		if(empty($par[bulanLembur])) $par[bulanLembur] = date('m');
		if(empty($par[tahunLembur])) $par[tahunLembur] = date('Y');		
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
		list($idKomponen, $mulaiKomponen, $selesaiKomponen) = explode("\t", getField("select concat(idKomponen, '\t' ,mulaiKomponen, '\t', selesaiKomponen) from dta_komponen where kodeKomponen='".$kodeKomponen."'"));
		if($mulaiKomponen > $selesaiKomponen){
			$bulanMulai = $par[bulanLembur] == 1 ? 12 : $par[bulanLembur] - 1;
			$tahunMulai = $par[bulanLembur] == 1 ? $par[tahunLembur] - 1 : $par[tahunLembur];			
		}else{
			$bulanMulai = $par[bulanLembur];
			$tahunMulai = $par[tahunLembur];
		}					
		$mulaiPeriode = $tahunMulai."-".str_pad($bulanMulai, 2, "0", STR_PAD_LEFT)."-".$mulaiKomponen;
		$selesaiPeriode = $par[tahunLembur]."-".str_pad($par[bulanLembur], 2, "0", STR_PAD_LEFT)."-".$selesaiKomponen;
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);			
				
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
		$objPHPExcel->getActiveSheet()->mergeCells('C2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('C3:G3');
			
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'REKAPITUSALI PENGAJUAN LEMBURAN PER DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'DEPT');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', 'PERIODE');
		
		$namaDepartemen = empty($par[idDepartemen]) ? "SEMUA DEPARTEMEN" : strtoupper($par[idDepartemen]);
		$objPHPExcel->getActiveSheet()->setCellValue('C2', ': '.$namaDepartemen);
		$objPHPExcel->getActiveSheet()->setCellValue('C3', ': '.strtoupper(getBulan($par[bulanLembur]))." ".$par[tahunLembur]);
		
		
		$objPHPExcel->getActiveSheet()->mergeCells('C5:D5');
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:G5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'TOTAL');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'KETERANGAN');
									
		$rows = 6;
			
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
		$filter = " and t2.status='".$status."' AND t2.group_id IN ( $areaCheck )";						
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."' AND t3.kodeData = '".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		list($tahun, $bulan) = explode("-", $mulaiPeriode);
		$periode=$tahun.$bulan;
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$periode."'"))
			$arrGaji=arrayQuery("select t1.idPegawai, sum(t1.nilaiProses) from pay_proses_".$periode." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idKomponen not in (5) and t2.tipeKomponen='t' group by 1");
		
		$sql="select *, CASE WHEN time(t1.selesaiLembur) >= time(t1.mulaiLembur) THEN TIMEDIFF(time(t1.selesaiLembur), time(t1.mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(t1.mulaiLembur)), TIMEDIFF(time(t1.selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur t1 join dta_pegawai t2 join mst_data t3 on (t1.idPegawai=t2.id and t2.dept_id=t3.kodeData) where date(t1.mulaiLembur) between '".$mulaiPeriode."' and '".$selesaiPeriode."' and t1.sdmLembur='t' ".$filter." order by t2.name";
		$res=db($sql);
		$nilaiMakan=0;
		while($r=mysql_fetch_array($res)){										
		
			#GAJI POKOK
			$nilaiPokok = $arrGaji["$r[idPegawai]"];							
			$nilaiUpah=$nilaiPokok / 173;
			$arrPegawai["$r[idPegawai]"]=$r[name]."\t".$r[reg_no]."\t".$r[pos_name]."\t".$r[namaData]."\t".$r[grade]."\t".$nilaiPokok;
							
			list($tanggalLembur) = explode(" ", $r[mulaiLembur]);					
			$week = date("w", strtotime($tanggalLembur));
			$overtimeLembur = getAngka($r[overtimeLembur]);
			$r[durasiLembur] = empty($overtimeLembur) ? $r[durasiLembur] : $r[overtimeLembur];
			$jamLembur = empty($overtimeLembur) ? $r[durasiLembur] : $r[overtimeLembur];
			$jamLembur = setAngka(getAngka($jamLembur));												
			$namaShift = getField("select lower(trim(t2.namaShift)) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal='".$tanggalLembur."' and t1.idPegawai='".$r[idPegawai]."'");
			
			$jam1 = "";
			$jam2 = "";
			$jam3 = "";
			$jam4 = "";
			$jam5 = "";
			
			#hari libur			
			if(getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($namaShift, array("off","cuti")) || (in_array($week, array(0,6)) && in_array($namaShift, array("shift 1")))){														
				if($r[durasiLembur] > 8){
					$jam2 = 7;
					$jam3 = 1;
					$jam5 = $jamLembur-8;
					$nilaiInsentif["$r[idPegawai]"]+=(7 * 2 * $nilaiUpah) + (3 * $nilaiUpah) + (($r[durasiLembur]-8) * 4 * $nilaiUpah);
				}else if($r[durasiLembur] > 7){
					$jam2 = 7;
					$jam3 = $jamLembur-7;
					$nilaiInsentif["$r[idPegawai]"]+=(7 * 2 * $nilaiUpah) + (($r[durasiLembur]-7) * 3 * $nilaiUpah);
				}else{
					$jam2 = 1;
					$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 2 * $nilaiUpah;
				}
				
			#hari biasa
			}else{
				if($r[durasiLembur] > 1){
					$jam1 = 1;
					$jam2 = $jamLembur-1;
					//$nilaiInsentif["$r[idPegawai]"]+=(1.5 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
					$nilaiInsentif["$r[idPegawai]"]+=(2 * $nilaiUpah) + (($r[durasiLembur]-1) * 2 * $nilaiUpah);
				}else{
					$jam1 = 1;
					//$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 1.5 * $nilaiUpah;
					$nilaiInsentif["$r[idPegawai]"]+=$r[durasiLembur] * 2 * $nilaiUpah;
				}
			}	
			
			$arrDetail["$r[idPegawai]"][] = $tanggalLembur."\t".$r[keteranganLembur]."\t".$jamLembur."\t".$jam1."\t".$jam2."\t".$jam3."\t".$jam4."\t".$jam5;
			
			$nilaiInsentif["$r[idPegawai]"]+=$nilaiMakan;
		}
		
		if(is_array($arrPegawai)){
			reset($arrPegawai);
			while (list($id, $val) = each($arrPegawai)){
			list($name, $reg_no, $pos_name, $dept_name) = explode("\t", $val);				
			$no++;	
				
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':B'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$rows.':D'.$rows);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $reg_no);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($name));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $pos_name);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $nilaiInsentif[$id]);
			
			$totInsentif+=$nilaiInsentif[$id];
			$rows++;			
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $totInsentif);
			
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B5:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('D5:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E5:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F5:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G5:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);		
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);		
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Approved by,");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Approved by,");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "Payroll Master,");
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Chief Acconting");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Human Resource Manager");
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "(...........................................)");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "(...........................................)");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "(...........................................)");
		$rows++;
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);		
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);				
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Approved by,");		
		$rows++;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':E'.$rows);
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':G'.$rows);				
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "General Manager");
		$rows++;
		$rows++;
		$rows++;
		$rows++;
		$rows++;
				
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "(...........................................)");		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(5, 5);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle($namaDepartemen);
		$objPHPExcel->setActiveSheetIndex(0);
		
		$idx=1;
		if(is_array($arrPegawai)){
			reset($arrPegawai);
			while (list($id, $val) = each($arrPegawai)){
				list($name, $reg_no, $pos_name, $dept_name, $grade, $gajiPokok) = explode("\t", $val);
				$nilaiUpah=$gajiPokok/173;
				
				$objPHPExcel->createSheet($idx);
				$objPHPExcel->setActiveSheetIndex($idx);
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(40);
				
				$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
				$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');				
				$objPHPExcel->getActiveSheet()->setCellValue('A1', "LAMPIRAN PENGAJUAN LEMBUR KARYAWAN");
								
				$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
				$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
				$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->mergeCells('E6:I6');
				$objPHPExcel->getActiveSheet()->mergeCells('D3:I5');
				
				$objPHPExcel->getActiveSheet()->getStyle('A3:A6')->getFont()->setBold(true);	
				$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('J3:K3')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:C4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('J4:K4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				
				$objPHPExcel->getActiveSheet()->setCellValue('A3', "Nama");
				$objPHPExcel->getActiveSheet()->setCellValue('A4', "Title");
				$objPHPExcel->getActiveSheet()->setCellValue('A5', "Dept");
				$objPHPExcel->getActiveSheet()->setCellValue('A6', "Periode");
				$objPHPExcel->getActiveSheet()->setCellValue('D3', "PERHITUNGAN UPAH LEMBUR");
				$objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('K4:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('K4:K5')->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				
				$objPHPExcel->getActiveSheet()->setCellValue('J3', "Esseon");
				$objPHPExcel->getActiveSheet()->setCellValue('J4', "Gaji Pokok");
				$objPHPExcel->getActiveSheet()->setCellValue('J5', "Tarif/Jam");
															
				$objPHPExcel->getActiveSheet()->setCellValue('C3', strtoupper($name));
				$objPHPExcel->getActiveSheet()->setCellValue('C4', strtoupper($pos_name));
				$objPHPExcel->getActiveSheet()->setCellValue('C5', strtoupper($dept_name));
				$objPHPExcel->getActiveSheet()->setCellValue('C6', getBulan($par[bulanLembur])." ".$par[tahunLembur]);
				$objPHPExcel->getActiveSheet()->setCellValue('K3', getField("select namaData from mst_data where kodeData='".$grade."'"));
				$objPHPExcel->getActiveSheet()->setCellValue('K4', $gajiPokok);
				$objPHPExcel->getActiveSheet()->setCellValue('K5', $nilaiUpah);
				
				$objPHPExcel->getActiveSheet()->getStyle('D6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('D6', "TOTAL");
				$objPHPExcel->getActiveSheet()->setCellValue('E6', "HARI KERJA");
				
				$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(30);
								
				$objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$objPHPExcel->getActiveSheet()->setCellValue('A7', "NO");
				$objPHPExcel->getActiveSheet()->setCellValue('B7', "TANGGAL");
				$objPHPExcel->getActiveSheet()->setCellValue('C7', "NAMA PEKERJAAN");
				$objPHPExcel->getActiveSheet()->setCellValue('D7', "JAM LEMBUR");
				$objPHPExcel->getActiveSheet()->setCellValue('E7', "(A)");
				$objPHPExcel->getActiveSheet()->setCellValue('F7', "(B)");
				$objPHPExcel->getActiveSheet()->setCellValue('G7', "(C)");
				$objPHPExcel->getActiveSheet()->setCellValue('H7', "(D)");
				$objPHPExcel->getActiveSheet()->setCellValue('I7', "(E)");
				$objPHPExcel->getActiveSheet()->setCellValue('J7', "MEALS");
								
				$rows=8;
				$totalJam=0;
				$totalJam1=0;
				$totalJam2=0;
				$totalJam3=0;
				$totalJam4=0;
				$totalJam5=0;
				$totalMakan=0;
				if(is_array($arrDetail[$id])){
					reset($arrDetail[$id]);
					while (list($i, $v) = each($arrDetail[$id])){
						list($tanggalLembur, $keteranganLembur, $jamLembur, $jam1, $jam2, $jam3, $jam4, $jam5) = explode("\t",  $v);
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':I'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':J'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getTanggal($tanggalLembur));
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $keteranganLembur);
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $jamLembur);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jam1);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $jam2);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $jam3);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $jam4);
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $jam5);
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $nilaiMakan);
						
						$totalJam+=$jamLembur;
						$totalJam1+=$jam1;
						$totalJam2+=$jam2;
						$totalJam3+=$jam3;
						$totalJam4+=$jam4;
						$totalJam5+=$jam5;
						$totalMakan+=$nilaiMakan;
						
						$rows++;
					}
				}
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':I'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':J'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "TOTAL");				
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $totalJam);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totalJam1);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $totalJam2);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $totalJam3);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $totalJam4);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $totalJam5);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $totalMakan);
				$rows++;
				
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$jumlahJam1=1.5 * $totalJam1;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Total (A) Dikali");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "1.5");
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahJam1);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "Upah Lembur");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $nilaiInsentif[$id] - $totalMakan);
				$rows++;
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
				$jumlahJam2=2 * $totalJam2;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Total (B) Dikali");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "2");
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahJam2);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "Uang Makan");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $totalMakan);
				$rows++;								
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
				
				$jumlahJam3=3 * ($totalJam3 + $totalJam4);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Total (C-D) Dikali");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "3");
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahJam3);								
				$rows++;
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':K'.$rows)->getFont()->setBold(true);	
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
				
				$jumlahJam4=4 * $totalJam5;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Total (E) Dikali");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "4");
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahJam4);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "TOTAL UPAH LEMBUR");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $nilaiInsentif[$id]);
				$rows++;
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':D'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$objPHPExcel->getActiveSheet()->getStyle('K'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;;');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "TOTAL");				
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahJam1 + $jumlahJam2 + $jumlahJam3 + $jumlahJam4);		
				
				$objPHPExcel->getActiveSheet()->getStyle('A3:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A3:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('B3:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('C3:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('D3:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('E3:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('F3:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('G3:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('H3:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('I3:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('J3:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('K3:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);												
				
				$objPHPExcel->getActiveSheet()->getStyle('A7:K'.$rows)->getAlignment()->setWrapText(true);		
				$rows++;
				$rows++;
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Approved by,");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Approved by,");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, "Approved by,");				
				$rows++;
				
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Chief Accounting");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Human Resource Manager");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, getTanggal($tanggalLembur,"t"));				
				$rows++;
				$rows++;
				$rows++;
				$rows++;
				$rows++;
								
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':C'.$rows);
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':J'.$rows);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "(...........................................)");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "(...........................................)");
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, "(...........................................)");
				
				$objPHPExcel->getActiveSheet()->setTitle(strtoupper(substr($name,0,30)));
				$idx++;
			}
		}
		
		//$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "det":
				$text = pdf();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>

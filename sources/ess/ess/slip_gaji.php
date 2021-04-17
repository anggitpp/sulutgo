<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function lihat(){
		global $s, $par, $arrTitle, $cID;

		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		
		$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'");
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;				
		$iuranPensiun = getField("select join_date from emp where id='".$par[idPegawai]."'") < "2008-01-01" ? "PPMP" : "PPIP";
		
		if($par[mode] != "print"){
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
					<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">";
				/*
				echo empty($idProses) ? "<a href=\"#\" class=\"btn btn1 btn_print\" onclick=\"alert('maaf, slip gaji periode ".getBulan($par[bulanProses])." ".$par[tahunProses]." belum diproses');\"><span>Cetak Slip</span></a>":
				"<a href=\"ajax.php?par[mode]=print".getPar($par,"mode")."\" target=\"print\" class=\"btn btn1 btn_print\"><span>Cetak Slip</span></a>";
				*/
				
				echo empty($idProses) ? "":
				"<a href=\"ajax.php?par[mode]=print".getPar($par,"mode")."\" target=\"print\" class=\"btn btn1 btn_print\"><span>Cetak Slip</span></a>";
				
				echo "<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
						".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
						<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
						<input type=\"hidden\" id=\"par[idPegawai]\" name=\"par[idPegawai]\" value=\"".$par[idPegawai]."\" >
						<input type=\"hidden\" id=\"par[idDivisi]\" name=\"par[idDivisi]\" value=\"".$par[idDivisi]."\" >
						<input type=\"hidden\" id=\"par[idDepartemen]\" name=\"par[idDepartemen]\" value=\"".$par[idDepartemen]."\" >
						<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"".$par[mode]."\" >
					</div>
				</form>				
			<div class=\"contentwrapper\">				
			<div style=\"padding-top:20px;\">";				
		require_once "tmpl/emp_header_basic.php";						
		}
		$text.="</div>";
		
		if(!empty($idProses)){
			$text.="<div id=\"general\">						
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
				
				if(!empty($idProses)){
					$cat =  getField("select cat from dta_pegawai where id='".$par[idPegawai]."'");
					//$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
					//$pph21 = $cat != 532 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
					//$arrNot = array_merge($slipLain, $pph21);
					
					$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
						//if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $arrNot))
						{
							if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
							$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
							$tipeKomponen = $r[tipeKomponen];
							$urutanKomponen++;
						}
						
						//if(in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $cat != 532 ? "" : "PPH 21 Rp. ".getAngka($r[nilaiProses]);
					}
				}
				
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));											
				
				for($i=1; $i<=max($cntKomponen); $i++){					
					$text.="<tr>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["t"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["t"][$i]["nilaiProses"])."</span>
							<span style=\"float:right;\">Rp.</span>";								
					$text.="</td>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["p"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["p"][$i]["nilaiProses"])."</span>
							<span style=\"float:right;\">Rp.</span>";
					$text.="</td>
						</tr>";
						
					$totKomponen["t"]+=$arrKomponen["t"][$i]["nilaiProses"];
					$totKomponen["p"]+=$arrKomponen["p"][$i]["nilaiProses"];
				}				
				
				$text.="<tr>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["p"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";
				
				$text.="</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\"><strong>THP</strong></span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"]-$totKomponen["p"])."</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang($totKomponen["t"]-$totKomponen["p"]))." Rupiah</span>
							</td>
						</tr>
					</tbody>
					</table>";
					
			$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status='1'";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">Ditransfer ke :</th>							
							<th width=\"50%\" style=\"text-align:left;\">Catatan :</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td style=\"padding:3px 20px; height:75px;\">
							Rek. ".$r[namaBank]." ".$r[branch]."&nbsp;<br>
							No. Acc ".$r[account_no]."&nbsp;<br>
							a.n. ".$namaPegawai."
						</td>
						<td style=\"padding:3px 20px; height:75px;\">".nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'"))."&nbsp;".$catatanProses."</td>
						</tr>
					</tbody>
					</table>
			</div>";
		}else{
			$text.="<div class=\"notibar msgalert\" style=\"margin-top:20px;\">                        
                        <p>Maaf, slip gaji periode <strong>".getBulan($par[bulanProses])." ".$par[tahunProses]."</strong> belum diproses</p>
                    </div>";
		}
	$text.="</div>
		<iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		
		if($par[mode] == "print") pdf();
		
		return $text;
	}
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and concat(year(start_date), lpad(month(start_date),2,'0')) <= '".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."' order by start_date desc limit 1";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai=$r[name];
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);

		$pdf->Image("images/info/logo-1.png", $pdf->GetX()+152.5, $pdf->GetY()+1, 45, 10);
		//$pdf->Image("images/info/bgslip.png", $pdf->GetX()+35.5, $pdf->GetY()+50, 120, 50);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(200,7,'SLIP GAJI',0,0,'L');
		$pdf->Ln(5);		
		$pdf->Cell(200,7,strtoupper(getBulan($par[bulanProses]))." ".$par[tahunProses],0,0,'L');		
		$pdf->Ln();		
		
		$pdf->Cell(200,1,'','B');
		$pdf->Ln(1.25);
		$pdf->Cell(200,1,'','T');
		$pdf->Ln();
		$pdf->Cell(200,1,'','T');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','','8');		
		$pdf->SetWidths(array(20, 5, 70, 10, 30, 5, 60));	
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("NAMA\tb",":",$r[name],"","NPWP\tb",":",$r[npwp_no]), false);
		$pdf->Row(array("NPP\tb",':',$r[reg_no],'',"PANGKAT/GRADE\tb",":",getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")), false);
		$pdf->Row(array("JABATAN\tb",":",$r_[pos_name],"","LOKASI\tb",":",getField("select namaData from mst_data where kodeData='".$r_[location]."'")), false);
		
		$pdf->Ln();
		
				
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("PENERIMAAN\tb","POTONGAN\tb"));
		
		$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'");
		if(!empty($idProses)){
			$cat =  getField("select cat from dta_pegawai where id='".$par[idPegawai]."'");
			//$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			//$pph21 = $cat != 532 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
			//$arrNot = array_merge($slipLain, $pph21);
			
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
				//if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $arrNot))
				{
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
					$tipeKomponen = $r[tipeKomponen];
					$urutanKomponen++;
				}
				
				//if(in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $cat != 532 ? "" : "PPH 21 Rp. ".getAngka($r[nilaiProses]);
			}
		}
		$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));											
				
		$pdf->SetFont('Arial','','8');
		$pdf->SetWidths(array(10, 55, 10, 25, 10, 55, 10, 25));
		$pdf->SetAligns(array('C','L','C','R','C','L','C','R'));
		
		for($i=1; $i<=max($cntKomponen); $i++){					
		
			$noT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : $i.".";
			$noP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : $i."."; 
			$rpT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : "Rp.";
			$rpP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : "Rp.";
			$nilaiT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["t"][$i]["nilaiProses"]);
			$nilaiP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["p"][$i]["nilaiProses"]);
			
			$pdf->Row(array(
				$noT."\tf", $arrKomponen["t"][$i]["namaKomponen"]."\tf", $rpT."\tf", $nilaiT."\tr",
				$noP."\tf",$arrKomponen["p"][$i]["namaKomponen"]."\tf",$rpP."\tf", $nilaiP."\tf")
				);
		
			$totKomponen["t"]+=$arrKomponen["t"][$i]["nilaiProses"];
			$totKomponen["p"]+=$arrKomponen["p"][$i]["nilaiProses"];
		}

		$pdf->Row(array(
				"\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["t"])."\tr",
				"\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["p"])."\tf")
				);
		$pdf->Cell(200,1,'','T');
		$pdf->Ln(5);
		
		$pdf->Row(array(
				"\tf", "THP\tf", "Rp.\tf", getAngka($totKomponen["t"]-$totKomponen["p"])."\tf",
				"\tf", "\tf", "\tf", "\tf")
				);
		
		$pdf->SetWidths(array(10, 55, 135));
		$pdf->SetAligns(array('C','L','L'));
		$pdf->Row(array(
				"\tf", "Terbilang\tf", terbilang($totKomponen["t"]-$totKomponen["p"])." Rupiah\tf")
				);				
		$pdf->Cell(200,1,'','T');		
		$pdf->Ln(5);
		
		
			
		$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status='1'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		$rekeningGaji = "Rek. ".$r[namaBank]." ".$r[branch]."\nNo. Acc ".$r[account_no]."\na.n. ".$namaPegawai."";
		
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("Ditransfer ke :\tb","Catatan :\tb"));
		$pdf->Row(array($rekeningGaji, getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'")." ".$catatanProses));
		
		$pdf->AutoPrint(true);
		$pdf->Output();	
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
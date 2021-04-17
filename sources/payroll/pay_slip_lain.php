<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$idPayroll;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		//$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and concat(year(start_date), lpad(month(start_date),2,'0')) <= '".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."' order by start_date desc limit 1";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		$nilaiKomponen = getField("select nilaiUpload from pay_upload where tahunUpload='".$par[tahunProses]."' and bulanUpload='".intval($par[bulanProses])."' and idKomponen='".$par[idKomponen]."' and idPegawai='".$par[idPegawai]."'");
		$nilaiPph = getField("select nilaiProses from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." where idKomponen='285' and idPegawai='".$par[idPegawai]."'");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
				</h1>
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">				
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r[name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">ID</label>
							<span class=\"field\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[pos_name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Status Perkawinan</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[marital]'")."</span>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NPWP</label>
							<span class=\"field\">".$r[npwp_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Pangkat / Grade</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[rank]."'")."&nbsp;/&nbsp;".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Lokasi</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[location]."'")."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:1px solid #ddd;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\">".getField("select namaKomponen from dta_komponen where idKomponen='".$par[idKomponen]."'")."</span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiKomponen)."</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">";
						$text.=$nilaiPph > 0 ?"<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\">".getField("select namaKomponen from dta_komponen where idKomponen='286'")."</span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiPph)."</span>
								<span style=\"float:right;\">Rp.</span>":"&nbsp;";
						$text.="</td>
						</tr>";
				$text.=$nilaiPph > 0 ?"<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-right:1px solid #ddd;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\">".getField("select namaKomponen from dta_komponen where idKomponen='285'")."</span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiPph)."</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px;\">&nbsp;</td>
						</tr>":"";
						
				$text.="<tr>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiKomponen + $nilaiPph)."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiPph)."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";		
						
				$text.="<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang($nilaiKomponen))." Rupiah</span>
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
						<td style=\"padding:3px 20px; height:75px;\">".nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'"))."&nbsp;</td>
						</tr>
					</tbody>
					</table>
					
				</div>
				</form>
			</div>";
		
		return $text;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cUsername,$idPayroll;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$par[idKomponen] = getField("select idKomponen from dta_komponen where kodeKomponen='".$arrParam[$s]."'");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")." 
			<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
			".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
			<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			<div style=\"float:right;\">
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."				
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "ID"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>			
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">ID</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Nilai</th>
					<th width=\"50\">Detail</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";
							
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
			$bulanKeluar = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
			$tahunKeluar = $par[bulanProses] == 1 ? $par[tahunProses] - 1 : $par[tahunProses];
			$tanggalKeluar = $tahunKeluar."-".str_pad($bulanKeluar, 2, "0", STR_PAD_LEFT)."-15";
			
			$filter = "where t1.cat=".$par[idStatus]." and (t1.status='".$status."' or t1.leave_date > '".$tanggalKeluar."') and t2.status=1";
				
			if(!empty($par[idLokasi]))
				$filter.= " and t2.location='".$par[idLokasi]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "ID")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";		
					
			
			$sql="select * from pay_upload where tahunUpload='".$par[tahunProses]."' and bulanUpload='".intval($par[bulanProses])."' and idKomponen='".$par[idKomponen]."' order by idPegawai";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrGaji["$r[idPegawai]"]=$r[nilaiUpload];
			}
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) $filter order by name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(isset($arrGaji["$r[id]"])){
					$no++;	
					$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$r[reg_no]</td>
							<td>$r[pos_name]</td>
							<td>".$arrDivisi["$r[div_id]"]."</td>
							<td align=\"right\">".getAngka($arrGaji["$r[id]"])."</td>
							<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=det&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,550);\"><span>Detail</span></a></td>
							<td align=\"center\"><a onclick=\"openBox('ajax.php?par[mode]=print&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,500);\" href=\"#\" title=\"Detail Data\" class=\"print\" target=\"print\"><span>Print</span></a></td>

						</tr>";					
				}
			}
			$text.="</tbody>			
			</table>
			</div>
			<iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			
			if($par[mode] == "print") pdf();
			
		return $text;
	}	
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam,$idPayroll;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);

		$status1 = getField("select namaData from mst_data where kodeData = '$r[marital]'");
		$status2 = getField("select keteranganData from mst_data where kodeData = '$r[marital]'");
		$kawin = $status1.", ".$status2;
		
		$namaPegawai=$r[name];
		$nilaiKomponen = getField("select nilaiUpload from pay_upload where tahunUpload='".$par[tahunProses]."' and bulanUpload='".intval($par[bulanProses])."' and idKomponen='".$par[idKomponen]."' and idPegawai='".$par[idPegawai]."'");
		$nilaiPph = getField("select nilaiProses from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." where idKomponen='285' and idPegawai='".$par[idPegawai]."'");
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Image("images/info/sliplogo.png", $pdf->GetX()+152.5, $pdf->GetY()+1, 45, 12.5);

		$pdf->Image("images/info/bgslip.png", $pdf->GetX()+35.5, $pdf->GetY()+50, 120, 50);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(200,7,strtoupper($arrTitle[$s]),0,0,'L');
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
		$pdf->SetWidths(array(20, 5, 70, 10, 30, 5, 70));	
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("NAMA\tb",":",$r[name],"","NPWP\tb",":",$r[npwp_no]), false);
		$pdf->Row(array("ID\tb",':',$r[reg_no],'',"PANGKAT/GRADE\tb",":",getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")), false);
		$pdf->Row(array("JABATAN\tb",":",$r_[pos_name],"","LOKASI\tb",":",getField("select namaData from mst_data where kodeData='".$r_[location]."'")), false);
			$pdf->Row(array("\tb","","","","STATUS\tb",":",$status1), false);
		// $pdf->Row(array("STATUS PERKAWINAN\tb",":",$kawin,"","STATUS PERKAWINAN\tb",":",$kawin), false);
		
		
		
		$pdf->Ln();
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("PENERIMAAN\tb","POTONGAN\tb"));
		
		$pdf->SetFont('Arial','','8');
		$pdf->SetWidths(array(5, 60, 10, 25, 5, 60, 10, 25));
		$pdf->SetAligns(array('C','L','C','R','C','L','C','R'));
		
		if($nilaiPph > 0){
			$pdf->Row(array(
				"\tf", "".getField("select namaKomponen from dta_komponen where idKomponen='".$par[idKomponen]."'")."\tf", "Rp.\tf", getAngka($nilaiKomponen)."\tr", "\tf",
				"".getField("select namaKomponen from dta_komponen where idKomponen='286'")."\tf", "Rp.\tf", getAngka($nilaiPph)."\tf")
				);
				
			$pdf->Row(array(
				"\tf", "".getField("select namaKomponen from dta_komponen where idKomponen='285'")."\tf", "Rp.\tf", getAngka($nilaiPph)."\tr",
				"\tf", "\tf", "\tf", "\tf")
				);
		}else{
			$pdf->Row(array(
				"\tf", "".getField("select namaKomponen from dta_komponen where idKomponen='".$par[idKomponen]."'")."\tf", "Rp.\tf", getAngka($nilaiKomponen)."\tr",
				"\tf", "\tf", "\tf", "\tf")
				);
		}
			
		$pdf->Row(array(
				"\tf", "TOTAL\tf", "Rp.\tf", getAngka($nilaiKomponen + $nilaiPph)."\tr",
				"\tf", "TOTAL\tf", "Rp.\tf", getAngka($nilaiPph)."\tf")
				);	
			
		
		$pdf->SetWidths(array(5, 20, 175));
		$pdf->SetAligns(array('C','L','L'));
		$pdf->SetFont('Arial','','7');
		
		$pdf->Row(array(
				"\tf", "Terbilang\tf", ": ".terbilang($nilaiKomponen)." Rupiah\tf")
				);				
		$pdf->Cell(200,1,'','T');		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','','8');
		
			
		$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status='1'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		$rekeningGaji = "Rek. ".$r[namaBank]." ".$r[branch]."\nNo. Acc ".$r[account_no]."\na.n. ".$namaPegawai."";
		
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("Ditransfer ke :\tb","Catatan :\tb"));
		$pdf->Row(array($rekeningGaji, getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'")));
		
		// $pdf->AutoPrint(true);
		$pdf->Output();	
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){		
			case "det":
				$text = detail();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
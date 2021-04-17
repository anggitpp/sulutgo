<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					Slip Gaji
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
							<label style=\"width:150px\" class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[pos_name]."&nbsp;</span>
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
					<tbody>";
																
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
					$tipeKomponen = $r[tipeKomponen];
					$urutanKomponen++;
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
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
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
						<td>Search : </td>
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>
				<div style=\"float:right;\">
					Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."				
					<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
				</div>				
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
					<th width=\"50\">Detail</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";
				
			$filter = "where t1.cat=".$par[idStatus]." AND t2.location IN ( $areaCheck )";		
			if(!empty($par[idLokasi]))
				$filter.= " and t2.location='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";		
					
			if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) order by idDetail";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
				}
			}
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
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
							<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('ajax.php?par[mode]=det&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,500);\"><span>Detail</span></a></td>
							<td align=\"center\"><a href=\"ajax.php?par[mode]=print&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."\" title=\"Detail Data\" class=\"print\" target=\"print\"><span>Print</span></a></td>
						</tr>";					
				}
			}
			$text.="</tbody>			
			</table>
			</div>
			<iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			
			if($par[mode] == "print") pdf();
			if($par[mode] == "xls"){			
				xls();			
				$text.="<iframe src=\"download.php?d=exp&f=".strtoupper(strtolower("SLIP GAJI - ".getBulan($par[bulanProses])." ".$par[tahunProses])).".xls\" frameborder=\"1\" width=\"100%\" height=\"500\"></iframe>";
			}
			
		return $text;
	}	
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai=$r[name];
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'SLIP GAJI',0,0,'L');
		$pdf->Cell(100,7,strtoupper(getBulan($par[bulanProses]))." ".$par[tahunProses],0,0,'R');
		$pdf->Ln();		
		
		$pdf->Cell(200,1,'','B');
		$pdf->Ln(1.25);
		$pdf->Cell(200,1,'','T');
		$pdf->Ln();
		$pdf->Cell(200,1,'','T');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','','8');		
		$pdf->SetWidths(array(20, 5, 70, 10, 30, 5, 60));	
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("NAMA\tb",":",$r[name],"","NPWP\tb",":",$r[npwp_no]), false);
		$pdf->Row(array("NPP\tb",':',$r[reg_no],'',"PANGKAT/GRADE\tb",":",getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")), false);
		$pdf->Row(array("JABATAN\tb",":",$r_[pos_name],"","LOKASI\tb",":",getField("select namaData from mst_data where kodeData='".$r_[location]."'")), false);
		
		$pdf->Ln(1);
				
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("PENERIMAAN\tb","POTONGAN\tb"));
		
		
		$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' order by t2.tipeKomponen desc, t2.urutanKomponen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
			$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
			$tipeKomponen = $r[tipeKomponen];
			$urutanKomponen++;
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
		$pdf->Ln(2);
		
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
		$pdf->Ln(2);
		
		$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status='1'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		$rekeningGaji = "Rek. ".$r[namaBank]." ".$r[branch]."\nNo. Acc ".$r[account_no]."\na.n. ".$namaPegawai."";
		
		$keteranganCatatan = getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'");
		if(empty($keteranganCatatan)) $keteranganCatatan = "Pelanggaran yang mengakibatkan Remainding letter I, sesuai PKB Pasal 13 ayat 7. Bagi karyawan/pekerja yang menginformasikan besaran salary dirinya sendiri atau menanyakan atau berusaha mencari tahu salary orang lain, baik dilakukan secara lisan atau secara data.";
		
		$pdf->SetWidths(array(100, 100));
		$pdf->SetAligns(array('L','L'));
		$pdf->Row(array("Ditransfer ke :\tb","Catatan :\tb"));
		$pdf->Row(array($rekeningGaji, $keteranganCatatan));
		
		if($par[mode] == "xls") $pdf->AutoPrint(true);
		$pdf->Output();	
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';				
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);				
		
		$filter = "where cat=".$par[idStatus];		
		if(!empty($par[idLokasi]))
			$filter.= " and location='".$par[idLokasi]."'";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) order by t1.idPegawai, t2.tipeKomponen desc, t2.urutanKomponen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
			$arrKomponen["$r[idPegawai]"]["$r[tipeKomponen]"][$urutanKomponen] = $r[namaKomponen]."\t".$r[nilaiProses];
			$tipeKomponen = $r[tipeKomponen];
			$urutanKomponen++;
		}
		
		$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where status='1'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrBank["$r[parent_id]"]=$r[namaBank]."\t".$r[branch]."\t".$r[account_no];
		}
		
		$arrCatatan=arrayQuery("select idPegawai, keteranganCatatan from pay_catatan");		
		$arrLokasi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."'");
		$arrRank = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."'");
		$arrGrade = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[12]."'");
		
		$sql="select t1.id, t1.name, t1.reg_no, t1.pos_name, t1.npwp_no, t1.rank, t1.grade, t1.location, t2.namaData from dta_pegawai t1 left join mst_data t2 on (t1.div_id=t2.kodeData) ".$filter." order by t1.name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			if(empty($r[namaData])) $r[namaData] = "CORPORATE";
			$arrDepartemen["".$r[namaData].""] = $r[namaData];
			$arrPegawai["".$r[namaData].""]["".$r[id].""] = $r;
		}
		
		$idx=0;
		if(is_array($arrDepartemen)){	  
			reset($arrDepartemen);
			while (list($namaDepartemen) = each($arrDepartemen)){
				if($idx > 0) $objPHPExcel->createSheet($idx);
				$objPHPExcel->setActiveSheetIndex($idx);										
				$srow = $rows = 1;
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(21.75);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17.5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(21.75);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17.5);
								
				if(is_array($arrPegawai[$namaDepartemen])){	  
					reset($arrPegawai[$namaDepartemen]);
					while (list($idPegawai, $r) = each($arrPegawai[$namaDepartemen])){
						$namaPegawai=$r[name];
						
						$objPHPExcel->getActiveSheet()->getRowDimension($srow)->setRowHeight(25);		
						//$objPHPExcel->getActiveSheet()->getRowDimension($srow+1)->setRowHeight(5);		
						//$objPHPExcel->getActiveSheet()->getRowDimension($srow+5)->setRowHeight(5);		
						
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$srow.':E'.$srow);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$srow.':J'.$srow);		
						$objPHPExcel->getActiveSheet()->mergeCells('A'.($srow+6).':E'.($srow+6));
						$objPHPExcel->getActiveSheet()->mergeCells('F'.($srow+6).':J'.($srow+6));
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow)->getFont()->setBold(true);	
						$objPHPExcel->getActiveSheet()->getStyle('F'.$srow)->getFont()->setBold(true);							
						 
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow)->getFont()->setSize(14);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$srow)->getFont()->setSize(14);
						 
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$srow, 'SLIP GAJI');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$srow, strtoupper(getBulan($par[bulanProses])." ".$par[tahunProses]));
						$objPHPExcel->getActiveSheet()->getStyle('F'.$srow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow.':J'.$srow)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow.':J'.$srow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

						$objPHPExcel->getActiveSheet()->mergeCells('A'.($srow+2).':B'.($srow+2));
						$objPHPExcel->getActiveSheet()->mergeCells('A'.($srow+3).':B'.($srow+3));
						$objPHPExcel->getActiveSheet()->mergeCells('A'.($srow+4).':B'.($srow+4));
						$objPHPExcel->getActiveSheet()->mergeCells('C'.($srow+2).':E'.($srow+2));
						$objPHPExcel->getActiveSheet()->mergeCells('C'.($srow+3).':E'.($srow+3));
						$objPHPExcel->getActiveSheet()->mergeCells('C'.($srow+4).':E'.($srow+4));
						$objPHPExcel->getActiveSheet()->mergeCells('F'.($srow+2).':G'.($srow+2));
						$objPHPExcel->getActiveSheet()->mergeCells('F'.($srow+3).':G'.($srow+3));
						$objPHPExcel->getActiveSheet()->mergeCells('F'.($srow+4).':G'.($srow+4));
						$objPHPExcel->getActiveSheet()->mergeCells('H'.($srow+2).':J'.($srow+2));
						$objPHPExcel->getActiveSheet()->mergeCells('H'.($srow+3).':J'.($srow+3));
						$objPHPExcel->getActiveSheet()->mergeCells('H'.($srow+4).':J'.($srow+4));
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+1).':B'.($srow+4))->getFont()->setBold(true);	
						$objPHPExcel->getActiveSheet()->getStyle('F'.($srow+1).':G'.($srow+4))->getFont()->setBold(true);	
						
						$objPHPExcel->getActiveSheet()->setCellValue('A'.($srow+2), 'NAMA');
						$objPHPExcel->getActiveSheet()->setCellValue('A'.($srow+3), 'NPP');
						$objPHPExcel->getActiveSheet()->setCellValue('A'.($srow+4), 'JABATAN');		
						$objPHPExcel->getActiveSheet()->setCellValue('C'.($srow+2), ': '.strtoupper($r[name]));
						$objPHPExcel->getActiveSheet()->setCellValue('C'.($srow+3), ': '.$r[reg_no]);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.($srow+4), ': '.$r[pos_name]);
						
						$objPHPExcel->getActiveSheet()->setCellValue('F'.($srow+2), 'NPWP');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.($srow+3), 'PANGKAT/GRADE');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.($srow+4), 'LOKASI');
						$objPHPExcel->getActiveSheet()->setCellValue('H'.($srow+2), ': '.$r[npwp_no]);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.($srow+3), ': '.$arrRank["".$r[rank].""].' / '.$arrGrade["".$r[grade].""]);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.($srow+4), ': '.$arrLokasi["".$r[location].""]);
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+6).':J'.($srow+6))->getFont()->setBold(true);			
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+6).':J'.($srow+6))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+6).':J'.($srow+6))->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+6).':J'.($srow+6))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								
						$objPHPExcel->getActiveSheet()->setCellValue('A'.($srow+6), 'PENERIMAAN');		
						$objPHPExcel->getActiveSheet()->setCellValue('F'.($srow+6), 'POTONGAN');
													
						$rows = $srow + 7;
						
						$cntKomponen = array(count($arrKomponen["$r[id]"]["t"]), count($arrKomponen["$r[id]"]["p"]));			
						for($i=1; $i<=max($cntKomponen); $i++){

							list($namaT, $nilaiT) = explode("\t", $arrKomponen["$r[id]"]["t"][$i]);
							list($namaP, $nilaiP) = explode("\t", $arrKomponen["$r[id]"]["p"][$i]);
						
							$noT = empty($namaT) ? "" : $i.".";
							$noP = empty($namaP) ? "" : $i."."; 
							$rpT = empty($namaT) ? "" : "Rp.";
							$rpP = empty($namaP) ? "" : "Rp.";
							$nilaiT = empty($namaT) ? "" : $nilaiT;
							$nilaiP = empty($namaP) ? "" : $nilaiP;
										
							$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
							$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
							$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':C'.$rows);
							$objPHPExcel->getActiveSheet()->mergeCells('G'.$rows.':H'.$rows);
							
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $noT);
							$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $namaT);
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $rpT);
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $nilaiT);
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $noP);
							$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $namaP);
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $rpP);
							$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $nilaiP);
								
						
							$totKomponen["$r[id]"]["t"]+=$nilaiT;
							$totKomponen["$r[id]"]["p"]+=$nilaiP;
							
							$rows++;
						}
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
						$objPHPExcel->getActiveSheet()->getStyle('J'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
						
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':C'.$rows);
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rows.':H'.$rows);
									
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Rp.");
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totKomponen["$r[id]"]["t"]);			
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, "TOTAL");
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, "Rp.");
						$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $totKomponen["$r[id]"]["p"]);
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+6).':A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('E'.($srow+6).':E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
						$objPHPExcel->getActiveSheet()->getStyle('J'.($srow+6).':J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						
						$rows++;						
						//$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(5);						
						$rows++;
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':A'.($rows+1))->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
						$objPHPExcel->getActiveSheet()->getStyle('J'.$rows.':J'.($rows+1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
						
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':C'.$rows);
						$objPHPExcel->getActiveSheet()->mergeCells('G'.$rows.':H'.$rows);
									
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "THP");
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, "Rp.");
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totKomponen["$r[id]"]["t"]-$totKomponen["$r[id]"]["p"]);

						$rows++;
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':C'.$rows);
						$objPHPExcel->getActiveSheet()->mergeCells('D'.$rows.':J'.$rows);
									
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "Terbilang");
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, trim(terbilang($totKomponen["$r[id]"]["t"]-$totKomponen["$r[id]"]["p"])));			
						
						$rows++;
						//$objPHPExcel->getActiveSheet()->getRowDimension($rows)->setRowHeight(5);
						$rows++;
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':A'.($rows+3))->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':E'.($rows+3))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
						$objPHPExcel->getActiveSheet()->getStyle('J'.$rows.':J'.($rows+3))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':E'.$rows);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.$rows);
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getFont()->setBold(true);	
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Ditransfer ke :");
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, "Catatan :");
						
						$rows++;
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':E'.$rows);
						$objPHPExcel->getActiveSheet()->mergeCells('F'.$rows.':J'.($rows+2));			
						list($namaBank, $branch, $account_no) = explode("\t", $arrBank["$r[id]"]);			
						
						$keteranganCatatan = $arrCatatan["$r[id]"];
						if(empty($keteranganCatatan)) $keteranganCatatan = "Pelanggaran yang mengakibatkan Remainding letter I, sesuai PKB Pasal 13 ayat 7. Bagi karyawan/pekerja yang menginformasikan besaran salary dirinya sendiri atau menanyakan atau berusaha mencari tahu salary orang lain, baik dilakukan secara lisan atau secara data.";
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "Rek. ".$namaBank." ".$branch);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $keteranganCatatan);
						
						$rows++;
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':E'.$rows);
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "No. Acc ".$account_no);
						
						$rows++;
						$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':E'.$rows);
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, "a.n. ".$namaPegawai);									
						
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow.':J'.$rows)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$srow.':J'.$rows)->getFont()->setName('Arial');
						$objPHPExcel->getActiveSheet()->getStyle('A'.($srow+7).':J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
					
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$rows++;
						$srow = $rows++;			
				
					}
				}
			
				$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
				
				$objPHPExcel->getActiveSheet()->setTitle($namaDepartemen);				
				$idx++;
			}
		}
		
		$objPHPExcel->setActiveSheetIndex(0);		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.strtoupper(strtolower("SLIP GAJI - ".getBulan($par[bulanProses])." ".$par[tahunProses])).".xls");
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
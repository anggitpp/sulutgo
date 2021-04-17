<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
			
	function lihat(){
		global $db,$s,$db,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<fieldset style=\"
				-moz-border-radius:5px; border-radius:5px; padding:10px;
				margin-bottom:10px;
				clear:both;
			\">
			<legend style=\"
				font-weight:bold;
				padding: 0 5px;
				text-transform:uppercase;
			\">FILTER</legend>
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
				<table width=\"100%\">
					<tr>
					<td width=\"50%\">
					<p>
						<label class=\"l-input-small\">Lokasi Proses</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","ALL",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "90%", "chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kategori Gaji</label>
						<div class=\"field\">
							".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]","ALL",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "90%", "chosen-select")."
						</div>
					</p>
					</td>
					<td width=\"50%\">
					<p>
						<label class=\"l-input-small\">Bulan</label>
						<div class=\"field\">
							".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"", "30")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tahun</label>
						<div class=\"field\">
							".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"", "30")."
						</div>
					</p>
					</td>
					</tr>
				</table>
				</form>
			</fieldset>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Kategori Gaji</th>
					<th>Lokasi Proses</th>
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Total Nilai</th>
					<th>Petugas</th>
					<th width=\"50\">Detail</th>
				</tr>
			</thead>
			<tbody>";
		
		$arrLokasi=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData");
		$arrJenis=arrayQuery("select idJenis, namaJenis from pay_jenis where statusJenis='t' order by idJenis");
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t1.idLokasi='".$par[idLokasi]."'";
		if(!empty($par[idJenis]))
			$filter.= " and t1.idJenis='".$par[idJenis]."'";
		
		//$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		//$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$arrUser = arrayQuery("select username, namaUser from app_user");
		$sql="select t1.* from pay_proses t1 where t1.tahunProses='".$par[tahunProses]."' and t1.bulanProses='".$par[bulanProses]."'".$filter."";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$r[namaUser] = empty($r[updateBy]) ? $arrUser["".$r[createBy].""] : $arrUser["".$r[updateBy].""];
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, t1.idProses, t0.idLokasi, t0.idJenis, t1.idPegawai, t1.idKomponen, t1.nilaiProses from pay_proses t0 join pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 join dta_komponen t3 on (t0.idProses=t1.idProses and t1.idPegawai=t2.id and t1.idKomponen=t3.idKomponen) where t3.realisasiKomponen='t'";
			$arrProses[] = $r;
		}
		
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[idJenis]))
			$filter.= " and t3.payroll_id='".$par[idJenis]."'";
		
		if(is_array($arrDetail)){
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$arrNilai=arrayQuery("select t3.group_id, t3.payroll_id , sum(case when t2.tipeKomponen='t' then t1.nilaiProses else t1.nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter."  group by 1,2");
			$arrJumlah=arrayQuery("select group_id, payroll_id , count(*) from (select t3.group_id, t3.payroll_id, t1.idPegawai from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."'  AND t3.group_id IN ( $areaCheck ) ".$filter."  group by 1,2,3) as t group by 1,2");
		}	
		
		if(is_array($arrProses)){;
			reset($arrProses);
			while (list($idProses, $r) = each($arrProses)){
				$r[pegawaiProses] = $arrJumlah["".$r[idLokasi].""]["".$r[idJenis].""];
				$r[nilaiProses] = $arrNilai["".$r[idLokasi].""]["".$r[idJenis].""];
				
				if($r[nilaiProses] > 0 && isset($arrJenis["".$r[idJenis].""]) && isset($arrLokasi["".$r[idLokasi].""])){
				$no++;
				$text.="<tr>
						<td>$no.</td>
						<td>".$arrJenis["".$r[idJenis].""]."</td>
						<td>".$arrLokasi["".$r[idLokasi].""]."</td>
						<td align=\"center\">".getAngka($r[pegawaiProses])."</td>
						<td align=\"right\">".getAngka($r[nilaiProses])."</td>
						<td>$r[namaUser]</td>
						<td align=\"center\"><a href=\"?par[mode]=det&par[idLokasi]=$r[idLokasi]&par[idJenis]=$r[idJenis]&par[idProses]=$r[idProses]".getPar($par,"mode,idLokasi,idJenis,idProses")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>
						</tr>";
				}
			}
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$db,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess, $areaCheck;						
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".getBulan($par[bulanProses])." ".$par[tahunProses]."
				</h1>
				".getBread(ucwords("detail gaji"))."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position:absolute; top:10px; right: 20px;\">
					Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","ALL",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
					".setPar($par,"idStatus,search,filter")."
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,idJenis,idStatus,idLokasi,idProses")."';\"/>
				</div>
			<fieldset style=\"
				-moz-border-radius:5px; border-radius:5px; padding:10px;
				margin-bottom:10px;
				clear:both;
			\">
			<legend style=\"
				font-weight:bold;
				padding: 0 5px;
				text-transform:uppercase;
			\">DETAIL</legend>
				<p>
					<label class=\"l-input-small\">Lokasi Proses</label>
					<span class=\"field\">
						".getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'")."
					</span>
				</p>
				<p>
					<label class=\"l-input-small\">Kategori Gaji</label>
					<span class=\"field\">
						".getField("select namaJenis from pay_jenis where idJenis='".$par[idJenis]."'")."
					</span>
				</p>
			</fieldset>			
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"subtable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$filter = "where t1.status='".$status."' AND t2.group_id IN ( $areaCheck )";		
			
			if(!empty($par[idStatus]))
				$filter.=" and t1.cat='".$par[idStatus]."'";
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[idJenis]))
				$filter.= " and t2.payroll_id='".$par[idJenis]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";	
					
			//$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			//$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) where t2.realisasiKomponen='t' order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				//if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					//if($r[cat] != 532 || !in_array($r[idKomponen], $pph21))
					$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
				//}
			}
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter group by t1.id order by name";
			// echo $sql;
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
							<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=dta&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,550);\"><span>Detail</span></a></td>
						</tr>";
					$totGaji+=$arrGaji["$r[id]"];
				}
			}			
			$text.="</tbody>
			<tfoot>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>SUB TOTAL</strong></td>
					<td style=\"text-align:right\"><span id=\"subTotal\"></span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>TOTAL</strong></td>
					<td style=\"text-align:right\"><span>".getAngka($totGaji)."</span></td>
					<td></td>
				</tr>
			</tfoot>
			</table>			
			</div>";
		return $text;
	}
	
	function data(){
		global $db,$s,$db,$inp,$par,$arrTitle;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
		
		//$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and concat(year(start_date), lpad(month(start_date),2,'0')) <= '".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."' order by start_date desc limit 1";
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
							
				//$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
				//$pph21 = $r[cat] != 532 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
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
					
					//if(in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $r[cat] != 532 ? "" : "PPH 21 Rp. ".getAngka($r[nilaiProses]);
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
					
				</div>
				</form>
			</div>";
		
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$db,$_submit,$menuAccess;
		switch($par[mode]){
			case "dta":
				$text = data();
			break;
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
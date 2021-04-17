<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fPokok = "files/pokok/";
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;						
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		
		$filterEmp = " and group_id in ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filterEmp.= " and group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filterEmp.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filterEmp.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filterEmp.= " and unit_id='".$par[unitId]."'";
		$cat = !empty($par[idStatus]) ? " and cat = '$par[idStatus]'" : "";

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
									<td>
									Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","-- ALL --",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."		
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
										Tahun : ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
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
			</form>
			<table style=\"width:100%;\">
			<tr>
				<td style=\"width:65%; vertical-align:top;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>NILAI PROSES GAJI BULANAN ".$par[tahunProses]."</h3></div>												
					</div>
					<div id=\"divProses\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detProses".getPar($par,"mode")."',875,450);\"></div>
					<script type=\"text/javascript\">
					var prosesChart ='<chart numberScaleValue=\"1000,1000,1000\" showValues=\"0\" numberScaleUnit=\" Ribu, Juta, Miliar\" useRoundEdges=\"1\"  bgColor=\"F7F7F7,E9E9E9\" showBorder=\"1\" borderColor=\"888888\" exportEnabled=\"0\">";
					
					$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
					$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
					
					$filter = "";
					if(!empty($par[idLokasi]))
						$filter.= " and t1.idLokasi='".$par[idLokasi]."'";
					
					$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' ".$filter;
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
						$arrProses["$r[bulanProses]"] = $r;
					}
					
					$filter = "";
					if(!empty($par[idLokasi]))
						$filter.= " and t3.group_id='".$par[idLokasi]."'";
					if(!empty($par[idStatus]))
						$filter.= " and t4.cat='".$par[idStatus]."'";
										
					if(is_array($arrDetail)){
						$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
						
						$arrProses=arrayQuery("select periodeProses, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1");
						
						$arrNilai=arrayQuery("select cat, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1");
						;	
					}
					
					for($i=1; $i<=12; $i++)
					$text.="<set label=\"".getBulan($i,"t")."\" value=\"".$arrProses[$par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT)]."\" />";
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Column3D\", \"chartProses\", \"100%\", 350);
						chart.setXMLData( prosesChart );
						chart.render(\"divProses\");
					</script>
				<td style=\"width:35%; vertical-align:top; padding-left:30px;\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>KOMPOSISI NILAI</h3></div>
					</div>
					<div id=\"divNilai\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detNilai".getPar($par,"mode")."',875,550);\"></div>
					<script type=\"text/javascript\">
					var nilaiChart ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";
					
					$sql="select * from mst_data where kodeCategory='".$arrParameter[5]."'";
					$res=db($sql);
					while($r=mysql_fetch_array($res)){
						$showValue = setAngka($arrNilai["$r[kodeData]"]) > 0 ? 1 : 0;
						$text.="<set value=\"".setAngka($arrNilai["$r[kodeData]"])."\" label=\"".$r[namaData]."\" showValue=\"".$showValue."\"/>";
					}					
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Pie2D\", \"chartNilai\", \"100%\", 350);
						chart.setXMLData( nilaiChart );
						chart.render(\"divNilai\");
					</script>
				</td>
				</tr>				
			</table>

			<br clear=\"all\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>KOREKSI GAJI</h3></div>
					</div>
					<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynsimple1\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th style=\"min-width:150px;\">Nama</th>
							<th width=\"100\">NPP</th>
							<th style=\"min-width:150px;\">Jabatan</th>
							<th style=\"width:75px;\">Approval</th>
							<th style=\"width:75px;\">Tanggal</th>
						</tr>
					</thead>
					<tbody>";
					
					$sql="select * from (
					 select t1.*, t2.name, t2.reg_no from pay_koreksi t1 join emp t2 on (t1.idPegawai=t2.id) where tahunKoreksi='$par[tahunProses]'
				 	$cat) as t0 left join emp_phist t3 on (t0.idPegawai=t3.parent_id and t3.status=1) WHERE t0.name IS NOT NULL $filterEmp ";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){								
						list($tanggalApprove, $waktuApprove) = explode(" ", $r[approveTime]);
						$statusKoreksi = $r[statusKoreksi] == "t"? getTanggal($tanggalApprove) : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
						$statusKoreksi = $r[statusKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $statusKoreksi;						
						
						$text.="<tr>
								<td>$no.</td>
								<td>";
						$text.=$r[statusKoreksi] == "t" ?
								"<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=koreksi&par[idPegawai]=$r[idPegawai]&par[bulanProses]=$r[bulanKoreksi]&par[tahunProses]=$r[tahunKoreksi]".getPar($par,"mode,idPegawai,bulanProses,tahunProses")."',925,550);\" class=\"detil\" title=\"Koreksi Data\">".strtoupper($r[name])."</a>":
								"".strtoupper($r[name])."";
						$text.="</td>
								<td>$r[reg_no]</td>
								<td>$r[pos_name]</td>								
								<td align=\"center\">".$statusKoreksi."</td>
								<td align=\"center\">".getTanggal($r[tanggalKoreksi])."</td>
								</tr>";
						$no++;
					}
					$text.="</tbody>
					</table>
					</td>					
					</tr>
					<tr>
					<td>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-bottom:0px;\"><h3>PERUBAHAN GAJI</h3></div>
					</div>
					
					<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynsimple2\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th style=\"min-width:150px;\">Nama</th>
							<th width=\"100\">NPP</th>
							<th style=\"min-width:150px;\">Jabatan</th>
							<th style=\"width:75px;\">Gaji Pokok</th>
							<th style=\"width:75px;\">Tanggal</th>
						</tr>
					</thead>
					<tbody>";
					
					$sql="select * from (
						select t1.*, t2.name, t2.reg_no from pay_pokok t1 join emp t2 on (t1.idPegawai=t2.id) where year(tanggalPokok)='$par[tahunProses]' $cat
					) as t0 left join emp_phist t3 on (t0.idPegawai=t3.parent_id and t3.status=1) WHERE t0.name IS NOT NULL $filterEmp ";
					// echo $sql;
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){
						$text.="<tr>
								<td>$no.</td>
								<td>
									<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=pokok&par[idPokok]=$r[idPokok]".getPar($par,"mode,idPokok")."',700,425);\" class=\"detil\" title=\"Detail Data\">".strtoupper($r[name])."</a>
								</td>
								<td>$r[reg_no]</td>
								<td>$r[pos_name]</td>								
								<td align=\"right\">".getAngka($r[nilaiPokok])."</td>
								<td align=\"center\">".getTanggal($r[tanggalPokok])."</td>
								</tr>";					
						$no++;
					}
					$text.="</tbody>
					</table>
			
			<iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			
			if($par[mode] == "print") pdf();
					
		return $text;
	}	
	
	function pokok(){
		global $db,$s,$inp,$par,$fPokok,$arrTitle,$menuAccess;		
		$sql="select * from pay_pokok where idPokok='$par[idPokok]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$sql_="select * from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Gaji Pokok</h1>
					".getBread(ucwords("gaji pokok"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Nama</label>
						<span class=\"field\">".$r_[name]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">NPP</label>
						<span class=\"field\">".$r_[reg_no]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Tanggal SK</label>
						<span class=\"field\">".getTanggal($r[tanggalPokok])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">No. SK</label>
						<span class=\"field\">".$r[nomorPokok]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<span class=\"field\">".getAngka($r[nilaiPokok])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[keteranganPokok])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">File SK</label>
						<div class=\"field\">";
							$text.=empty($r[filePokok])? "":
								"<a href=\"download.php?d=pokok&f=$r[idPokok]\"><img src=\"".getIcon($fPokok."/".$r[filePokok])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
						$text.="&nbsp;</div>
					</p>								
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function koreksi(){
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
					Koreksi Gaji
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
		
		list($bulanProses, $tahunProses) = explode("\t", getField("select concat(bulanProses,'\t',tahunProses) from pay_proses where detailProses='$par[detailProses]'"));
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'SLIP GAJI',0,0,'L');
		$pdf->Cell(100,7,strtoupper(getBulan($bulanProses))." ".$tahunProses,0,0,'R');
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
		
		
		$sql="select * from ".$par[detailProses]." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' order by t2.tipeKomponen desc, t2.urutanKomponen";
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
		$pdf->Row(array($rekeningGaji, getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'")));
		
		$pdf->AutoPrint(true);
		$pdf->Output();	
	}
	
	function detProses(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		if(!empty($par[idLokasi]))
			$subTitle = " (".getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'").")";
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Proses Gaji Bulanan ".$subTitle."</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Total Nilai</th>
					<th width=\"125\">Proses</th>					
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t1.idLokasi='".$par[idLokasi]."'";
							
						
		$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' ".$filter;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
			$arrProses["$r[bulanProses]"] = $r;
		}
		
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[idStatus]))
			$filter.= " and t4.cat='".$par[idStatus]."'";
							
		if(is_array($arrDetail)){
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$arrNilai=arrayQuery("select periodeProses, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1");
			$arrJumlah=arrayQuery("select periodeProses, count(*) from (select periodeProses, idPegawai from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."'  AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1, 2) as t group by 1");
		}
		
		for($i=1; $i<=12; $i++){
			$r = $arrProses[$i];
			$r[pegawaiProses] = $arrJumlah[$par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT)];
			$r[nilaiProses] = $arrNilai[$par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT)];
			
			list($selesaiTanggal, $selesaiWaktu) = explode(" ", $r[selesaiProses]);
						
			$pegawaiProses = getAngka($r[pegawaiProses]) > 0 ? getAngka($r[pegawaiProses]) : "";
			$nilaiProses = getAngka($r[nilaiProses]) > 0 ? getAngka($r[nilaiProses]) : "";
			$selesaiProses = "".getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5)."";
										
			if(getAngka($r[nilaiProses]) > 0)
				$text.="<tr>
						<td>$i.</td>
						<td>".getBulan($i)."</td>
						<td align=\"center\">".$pegawaiProses."</td>
						<td align=\"right\">".$nilaiProses."</td>
						<td align=\"center\">".$selesaiProses."</td>					
						<td>$r[namaUser]</td>
						</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		
		return $text;
	}
	
	function detNilai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanProses])) $par[bulanProses] = "01";
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		if(!empty($par[idLokasi]))
			$subTitle = " (".getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'").")";
		$text.="<script>
					jQuery(document).ready(function() {
						_page = parseInt(document.getElementById(\"_page\").value);
						_len = parseInt(document.getElementById(\"_len\").value);
						
						jQuery(\"#subtable\").dataTable( {
							\"sPaginationType\": \"full_numbers\",
							\"iDisplayLength\": _len,
							\"aLengthMenu\": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, \"All\"]],
							\"bSort\": false,		
							\"bFilter\": false,		
							\"sDom\": \"rt<'bottom'lip><'clear'>\",
							\"oLanguage\": {
								\"sEmptyTable\": \"&nbsp;\"
							},
							\"fnDrawCallback\": function () {
								jQuery(\"#_page\").val(this.fnPagingInfo().iPage);
								jQuery(\"#_len\").val(this.fnPagingInfo().iLength);			
							},
							
							\"fnFooterCallback\": function ( nFoot, aData, iStart, iEnd, aiDisplay ){
								subTotal = 0;
								for(i=iStart ; i<iEnd ; i++) {
									subTotal = subTotal * 1 + convert(aData[aiDisplay[i]][5]) * 1;
								}
								document.getElementById(\"subTotal\").innerHTML = formatNumber(subTotal);
							}
						} );
					} );
				</script>
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Komposisi Nilai ".$subTitle."</h1>
				<span>&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			".setPar($par, "tahunProses")."
			<div style=\"float:right;\">				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td>".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."</td>
				</tr>
			</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"subtable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
				</tr>
			</thead>
			<tbody>";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$filter = "where t1.cat=".$par[idStatus]." and t1.status='".$status."'";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[idStatus]))
				$filter.= " and t1.cat='".$par[idStatus]."'";
				
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
				$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
				$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
					
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
						if($r[cat] == 531 || !in_array($r[idKomponen], $pph21))
						$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
					}
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
						</tr>";
					$totGaji+=$arrGaji["$r[id]"];
				}
			}			
			$text.="</tbody>
			<tfoot>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>SUB TOTAL</strong></td>
					<td style=\"text-align:right\"><span id=\"subTotal\"></span></td>					
				</tr>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>TOTAL</strong></td>
					<td style=\"text-align:right\"><span>".getAngka($totGaji)."</span></td>					
				</tr>
			</tfoot>
			</table>
			</div>";
		
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){	
			case "detProses":
				$text = detProses();
			break;
			case "detNilai":
				$text = detNilai();
			break;
			case "koreksi":
				$text = koreksi();
			break;
			case "pokok":
				$text = pokok();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
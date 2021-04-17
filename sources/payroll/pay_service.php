<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
		
	function update(){
		global $s,$inp,$par,$cUsername,$arrParameter;
		repField();
		
		$sql="delete from dta_service where bulanService='".$par[bulanProses]."' and tahunService='".$par[tahunProses]."'";
		db($sql);
		
		$sql="insert into dta_service (bulanService, tahunService, totalService, extraService, otherService, roomService, breakageService, welfareService, lossService, adjustmentService, ticketService, netService, hariService, nilaiService, createBy, createTime) values ('".$par[bulanProses]."', '".$par[tahunProses]."', '".setAngka($inp[totalService])."', '".setAngka($inp[extraService])."', '".setAngka($inp[otherService])."', '".setAngka($inp[roomService])."', '".setAngka($inp[breakageService])."', '".setAngka($inp[welfareService])."', '".setAngka($inp[lossService])."', '".setAngka($inp[adjustmentService])."', '".setAngka($inp[ticketService])."', '".setAngka($inp[netService])."', '".setAngka($inp[hariService])."', '".setAngka($inp[nilaiService])."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$nilaiService = setAngka($inp[netService])." / ".setAngka($inp[hariService]);
		$sql="update pay_service set nilaiService=".$nilaiService.", totalService = jumlahService * (".$nilaiService.") where bulanService='".$par[bulanProses]."' and tahunService='".$par[tahunProses]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,bulanProses")."';</script>";
	}	
	
	function form(){
		global $s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$areaCheck;
		
		$input = "width:95%; text-align:right;";
		$style = "width:95%; border:0; box-shadow: none; text-align:right; background:transparent;";		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$bulanProses = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
		$lokasiKerja = empty($par[idLokasi]) ? "ALL" : getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'");
		
		$filter=" where t2.location in ( $areaCheck ) and t2.status='$status'";
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		$sql="select * from dta_service where bulanService='".$par[bulanProses]."' and tahunService='".$par[tahunProses]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		list($jumlahPegawai, $jumlahHari)=explode("\t", getField("select concat(count(t1.idPegawai), '\t', sum(t1.jumlahService)) from pay_service t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) ".$filter." and t1.tahunService='".$par[tahunProses]."' and bulanService='".$par[bulanProses]."'"));
		if(empty($jumlahPegawai)) $jumlahPegawai = 0;
		if(empty($jumlahHari)) $jumlahHari = 0;
		$r[hariService] = $jumlahHari;
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords("set data"))."					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" >	
				<br clear=\"all\">
				<fieldset style=\"padding:10px; -moz-border-radius: 5px ; -webkit-border-radius: 5px ; border-radius: 5px ; border:1px solid #ccc;\">
					<p>
						<label class=\"l-input-small\">Bulan</label>
						<span class=\"field\">".getBulan($par[bulanProses])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Periode</label>
						<span class=\"field\">21 ".getBulan($bulanProses)." s.d 20 ".getBulan($par[bulanProses])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Jumlah Pegawai</label>
						<span class=\"field\">".getAngka($jumlahPegawai)." Orang&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Lokasi</label>
						<span class=\"field\">".$lokasiKerja."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Tahun</label>
						<span class=\"field\">".$par[tahunProses]."&nbsp;</span>
					</p>					
				</fieldset>
				<br clear=\"all\">
				<div style=\"position:absolute; right:0; margin-right:40px;\">
					<input type=\"submit\" class=\"btnSubmit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location.href='?".getPar($par,"mode,bulanProses")."';\"/>
				</div>
				<ul class=\"hornav\">
					<li class=\"current\"><a href=\"#service\">Service Charge</a></li>
					<li><a href=\"#departemen\">Departemen</a></li>
				</ul>
				<div id=\"service\" class=\"subcontent\">
					<table cellspacing=\"5\" style=\"width:100%\">
						<tr>
							<td style=\"width:30px;\">&nbsp;</td>
							<td>TOTAL SERVICE CHARGE KARYAWAN</td>
							<td style=\"width:30px;\">&nbsp;</td>
							<td style=\"width:125px;\">&nbsp;</td>
							<td style=\"width:50px;\">&nbsp;</td>
							<td style=\"width:30px;\">Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"totalService\" name=\"inp[totalService]\" value=\"".getAngka($r[totalService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>1.</td>
							<td>Total Service Room & Extra Facilties</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"extraService\" name=\"inp[extraService]\" value=\"".getAngka($r[extraService])."\" style=\"".$input."\" onkeyup=\"setProses();\"/></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>2.</td>
							<td>Total Service FB & Other Income</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"otherService\" name=\"inp[otherService]\" value=\"".getAngka($r[otherService])."\" style=\"".$input."\"  onkeyup=\"setProses();\"/></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>3.</td>
							<td>Total Service Room & FB</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"roomService\" name=\"inp[roomService]\" value=\"".getAngka($r[roomService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>4.</td>
							<td>Loss</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"fbService\" name=\"inp[fbService]\" value=\"".getAngka($r[roomService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>- Breakage</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"breakageText\" name=\"inp[breakageText]\" value=\"".getAngka($r[roomService])."&nbsp;&nbsp;&nbsp;x&nbsp;&nbsp;&nbsp;5%\" style=\"".$style."\" readonly=\"readonly\"/></td>
							<td style=\"text-align:center\">:</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"breakageService\" name=\"inp[breakageService]\" value=\"".getAngka($r[breakageService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>							
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>- Welfare</td>							
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"welfareText\" name=\"inp[welfareText]\" value=\"".getAngka($r[roomService])."&nbsp;&nbsp;&nbsp;x&nbsp;&nbsp;&nbsp;2%\" style=\"".$style."\" readonly=\"readonly\"/></td>
							<td style=\"text-align:center\">:</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"welfareService\" name=\"inp[welfareService]\" value=\"".getAngka($r[welfareService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>- Rafel</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Loss Total</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>Rp.</td>						
							<td style=\"width:125px;\"><input type=\"text\" id=\"lossService\" name=\"inp[lossService]\" value=\"".getAngka($r[lossService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Net Total Service Room & FB - Adjustment</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>Rp.</td>						
							<td style=\"width:125px;\"><input type=\"text\" id=\"adjustmentService\" name=\"inp[adjustmentService]\" value=\"".getAngka($r[adjustmentService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>5.</td>
							<td>Total Service Recreation & Ticket</td>							
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>Rp.</td>							
							<td style=\"width:125px;\"><input type=\"text\" id=\"ticketService\" name=\"inp[ticketService]\" value=\"".getAngka($r[ticketService])."\" style=\"".$input."\" onkeyup=\"setProses();\"/></td>
						</tr>
						<tr>
							<td>6.</td>
							<td>NET TOTAL SERVICE CHARGE YANG DIBAGIKAN</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"netService\" name=\"inp[netService]\" value=\"".getAngka($r[netService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>7.</td>
							<td>PENDAPATAN SERVICE CHARGE PER KARYAWAN /HARI</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<table cellspacing=\"5\" style=\"width:100%\">
						<tr>
							<td style=\"width:30px;\">&nbsp;</td>
							<td>A. NET TOTAL SERVICE</td>
							<td style=\"width:30px;\">Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"jumlahService\" name=\"inp[jumlahService]\" value=\"".getAngka($r[netService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
							<td style=\"width:50px;\">&nbsp;</td>
							<td style=\"text-align:center\">Service / HK</td>							
							<td style=\"width:30px;\">Rp.</td>
							<td style=\"width:125px;\"><input type=\"text\" id=\"nilaiService\" name=\"inp[nilaiService]\" value=\"".getAngka($r[nilaiService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>B. TOTAL HARI KERJA</td>
							<td>&nbsp;</td>
							<td><input type=\"text\" id=\"hariService\" name=\"inp[hariService]\" value=\"".getAngka($r[hariService])."\" style=\"".$style."\" readonly=\"readonly\"/></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
				<div id=\"departemen\" class=\"subcontent\" style=\"display:none\">
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th>Departemen</th>
							<th width=\"100\">Total Person</th>
							<th width=\"125\">Total H-Kerja</th>
							<th width=\"125\">Total Service</th>					
							<th width=\"50\">Detail</th>
						</tr>
					</thead>
					<tbody id=\"detailService\">";
				
				echo $sql_="select *, count(*) as jumlahPegawai, sum(jumlahService) as jumlahHari  from pay_service t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) ".$filter." and t1.tahunService='".$par[tahunProses]."' and bulanService='".$par[bulanProses]."' group by t2.dept_id order by t2.dept_id";
				$res_=db($sql_);
				$no=1;				
				while($r_=mysql_fetch_array($res_)){
				$text.="<tr>
							<td align=\"center\">".$no.".</td>
							<td>".strtoupper(getField("select namaData from mst_data where kodeData='".$r_[dept_id]."'"))."</td>
							<td align=\"right\">
								".getAngka($r_[jumlahPegawai])."
								<input type=\"hidden\" id=\"nilaiService_".$no."\" name=\"dta[nilaiService_".$no."]\" value=\"".getAngka($r_[jumlahHari] * $r[nilaiService])."\" readonly=\"readonly\"/>
							</td>
							<td align=\"right\" id=\"hariDepartemen_".$no."\">".getAngka($r_[jumlahHari])."</td>
							<td align=\"right\" id=\"serviceDepartemen_".$no."\">".getAngka($r_[jumlahHari] * $r[nilaiService])."</td>		
							<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=det&par[idDepartemen]=$r_[dept_id]&par[nilaiService]='+ convert(document.getElementById('netService').value) / convert(document.getElementById('hariService').value)  +'".getPar($par,"mode,idDepartemen,nilaiService")."', 950, 550);\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>
						</tr>";
						$no++;
						$pegawaiTotal+=$r_[jumlahPegawai];
						$hariTotal+=$r_[jumlahHari];
						$serviceTotal+=$r_[jumlahHari] * $r[nilaiService];
				}
				$text.="</tbody>
				<tfoot>
					<tr>
						<td align=\"center\">&nbsp;</td>
						<td>GENERAL TOTAL</td>
						<td style=\"text-align:right\" id=\"pegawaiTotal\">".getAngka($pegawaiTotal)."</td>
						<td style=\"text-align:right\" id=\"hariTotal\">".getAngka($hariTotal)."</td>
						<td style=\"text-align:right\" id=\"serviceTotal\">".getAngka($serviceTotal)."</td>		
						<td align=\"center\">&nbsp;</td>
					</tr>
				</tfoot>
				</div>
				
				<div id=\"prosesImg\" align=\"center\" style=\"display:none; position:absolute; left:50%; top:50%;\">						
					<img src=\"styles/images/loaders/loader6.gif\">
				</div>
				<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">						
					<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
					<div class=\"bar2\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%;\"></div></div>
				</div>					
				<span id=\"progresRes\"></span>
				<div id=\"progresEnd\" class=\"progress\" style=\"margin-top:30px; display:none;\">
					<input type=\"hidden\" id=\"tanggalAbsen\" name=\"tanggalAbsen\" value=\"".date('d/m/Y')."\">
					<a href=\"download.php?d=logManual\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>				
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?par[tanggalAbsen]=' + document.getElementById('tanggalAbsen').value + '".getPar($par,"mode,tanggalAbsen")."';\"/>
				</div>
			</form>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$arrParameter, $areaCheck;
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
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
										<span style=\"margin-left: 30px;\">Tahun : </span>
										".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
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
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>						
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"150\">Periode</th>
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Jumlah H-Kerja</th>					
					<th width=\"125\">Nilai</th>					
					<th width=\"50\">Status</th>
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."' and t2.status='".$status."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		$sql="select t1.*, t2.namaUser from dta_service t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunService='".$par[tahunProses]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$arrProses["$r[bulanService]"] = $r;
		}
		
		$arrJumlah = arrayQuery("select bulanService, count(*) as jumlahPegawai from pay_service where tahunService='".$par[tahunProses]."'");
		
		for($i=1; $i<=12; $i++){
			if(isset($menuAccess[$s]["add"]))
			$serviceProses = " onclick=\"window.location='?par[mode]=set&par[bulanProses]=$i".getPar($par,"mode,bulanProses")."';\"";
			
			$r = $arrProses[$i];			
			$statusProses =  getAngka($r[netService]) > 0 ? "<img src=\"styles/images/t.png\" title=\"Sudah Diproses\">" : "<img src=\"styles/images/f.png\" title=\"Belum Diproses\">";		
			
			list($tanggalCreate, $waktuCreate) = explode(" ",$r[createTime]);			
			$waktuUpload = getTanggal($tanggalCreate) != "" ? getTanggal($tanggalCreate)." @ ".substr($waktuCreate,0,5) : "";
			
			$b = $i == 1 ? 12 : $i - 1;			
			
			$text.="<tr>
					<td>$i.</td>
					<td>".getBulan($i)."</td>
					<td>21 ".getBulan($b,"t")." s.d 20 ".getBulan($i,"t")."</td>					
					<td align=\"center\">".getAngka($arrJumlah[$i])."</td>
					<td align=\"right\">".getAngka($r[hariService])." hari</td>
					<td align=\"right\">".getAngka($r[netService])."</td>
					<td align=\"center\"><a href=\"#\" ".$serviceProses." class=\"detil\">".$statusProses."</a></td>
					<td>$r[namaUser]<br>".$waktuUpload."</td>
					</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$areaCheck;						
		
		$lokasiKerja = empty($par[idLokasi]) ? "ALL" : getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'");
		$nilaiService = setAngka($par[nilaiService]);

		$filter=" where t2.location in ( $areaCheck )";
		$filter.= " and t2.dept_id='".$par[idDepartemen]."'";		
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		list($jumlahPegawai, $jumlahHari)=explode("\t", getField("select concat(count(t1.idPegawai), '\t', sum(t1.jumlahService)) from pay_service t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) ".$filter." and t1.tahunService='".$par[tahunProses]."' and bulanService='".$par[bulanProses]."'"));
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
				</h1>
				".getBread(ucwords("detail data"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<br clear=\"all\" />
			<fieldset style=\"padding:10px; -moz-border-radius: 5px ; -webkit-border-radius: 5px ; border-radius: 5px ; border:1px solid #ccc;\">
				<div class=\"one_half\">
				<p>
					<label class=\"l-input-small\">Bulan</label>
					<span class=\"field\">".getBulan($par[bulanProses])."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Periode</label>
					<span class=\"field\">21 ".getBulan($bulanProses)." s.d 20 ".getBulan($par[bulanProses])."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Lokasi</label>
					<span class=\"field\">".$lokasiKerja."&nbsp;</span>
				</p>								
				<p>
					<label class=\"l-input-small\">Tahun</label>
					<span class=\"field\">".$par[tahunProses]."&nbsp;</span>
				</p>					
				</div>
				<div class=\"one_half last\">
				<p>
					<label class=\"l-input-small\">Departemen</label>
					<span class=\"field\">".getField("select upper(namaData) from mst_data where kodeData='".$par[idDepartemen]."'")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Jml Pegawai</label>
					<span class=\"field\">".getAngka($jumlahPegawai)." Orang&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Total Hari</label>
					<span class=\"field\">".getAngka($jumlahHari)."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Total Nilai</label>
					<span class=\"field\">".getAngka($nilaiService * $jumlahHari)."&nbsp;</span>
				</p>
				</div>
			</fieldset>	
			</form>
			<br clear=\"all\" />";
			
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th>Jabatan</th>
					<th width=\"100\">H-Kerja</th>
					<th width=\"100\">Nilai Hari</th>
					<th width=\"100\">Total Service</th>
				</tr>				
			</thead>
			<tbody>";
			
			$sql="select * from pay_service t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) ".$filter." and t1.tahunService='".$par[tahunProses]."' and bulanService='".$par[bulanProses]."' order by t2.name";
			$res=db($sql);
			$no=1;
			while($r=mysql_fetch_array($res)){
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>						
						<td>$r[pos_name]</td>			
						<td align=\"right\">".getAngka($r[jumlahService])."</td>
						<td align=\"right\">".getAngka($nilaiService)."</td>
						<td align=\"right\">".getAngka($r[jumlahService] * $par[nilaiService])."</td>
					</tr>";
					
					$no++;
					$totalHari+=$r[jumlahService];
					$totalService+=$r[jumlahService] * $par[nilaiService];
			}
			$text.="</tbody>			
			<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:left; font-weight:bold\">TOTAL</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:right;\">".getAngka($totalHari)."</td>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:right;\">".getAngka($totalService)."</td>
				</tr>
			</tfoot>
			</table>			
			</div>
			</div>";
		return $text;
	}		
	
	function data(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Detail Posting Proses Gaji</h1>
					".getBread(ucwords("detail data"))."
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		
		$sql="select * from pay_upload where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idPegawai='$par[idPegawai]' and idKomponen='$par[idKomponen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">							
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<span class=\"field\" style=\"margin-left:150px;\">
							".getAngka($r[nilaiUpload])."&nbsp;
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">
							".nl2br($r[keteranganUpload])."&nbsp;
						</span>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "det":
				$text = detail();
			break;
			
			case "dta":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = data();
			break;
			case "del":
				if(isset($menuAccess[$s]["edit"])) $text = hapus();
			break;
			case "end":
				if(isset($menuAccess[$s]["add"])) $text = endProses();
			break;
			case "dat":
				if(isset($menuAccess[$s]["add"])) $text = setData();
			break;
			case "tab":
				if(isset($menuAccess[$s]["add"])) $text = setTable();
			break;
				
			case "set":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : update(); else $text = lihat();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IZ";
		$date=empty($_GET[tanggalLembur]) ? $inp[tanggalLembur] : $_GET[tanggalLembur];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorLembur from att_lembur where month(tanggalLembur)='$bulan' and year(tanggalLembur)='$tahun' order by nomorLembur desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}
	
	function gPegawai(){
		global $db,$s,$inp,$par;
		$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$data["idPegawai"] = $r[id];
		$data["nikPegawai"] = $r[reg_no];
		$data["namaPegawai"] = strtoupper($r[name]);
		
		$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$data["namaJabatan"] = $r_[pos_name];
		$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");
		
		return json_encode($data);
	}	
	
	function all(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$filter = "where idPegawai is not null";
		if(!empty($par[bulanLembur]))
			$filter.= " and month(tanggalAbsen)='$par[bulanLembur]'";
		if(!empty($par[tahunLembur]))
			$filter.= " and year(tanggalAbsen)='$par[tahunLembur]'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, durasiAbsen from att_absen $filter order by idAbsen");
		
		$filter = "where nomorLembur is not null";		
		if(!empty($par[bulanLembur]))
			$filter.= " and month(t1.mulaiLembur)='$par[bulanLembur]'";
		if(!empty($par[tahunLembur]))
			$filter.= " and year(t1.mulaiLembur)='$par[tahunLembur]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";		
		
		$persetujuanLembur = $par[mode] == "allSdm" ? "sdmLembur" : "persetujuanLembur";
		$keteranganLembur = $par[mode] == "allSdm" ? "noteLembur" : "catatanLembur";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$sql="select * from att_lembur t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){				
			list($tanggalLembur) = explode(" ", $r[mulaiLembur]);
			$durasiAbsen = $arrAbsen["$r[id]"][$tanggalLembur];
			$overtimeLembur = empty($r[overtimeLembur]) ? getAngka($durasiAbsen) : getAngka($r[overtimeLembur]);
			
			$sql="update att_lembur set $keteranganLembur='".$inp[$keteranganLembur]."', $persetujuanLembur='".$inp[$persetujuanLembur]."', overtimeLembur='".setAngka($overtimeLembur)."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' where idLembur='$r[idLembur]'";
			db($sql);
		}
		echo "<script>closeBox();reloadPage();</script>";
	}	
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[mulaiLembur];
		$selesaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[selesaiLembur];
		
		$sql="update att_lembur set idPegawai='$inp[idPegawai]', idAtasan='$inp[idAtasan]', nomorLembur='$inp[nomorLembur]', tanggalLembur='".setTanggal($inp[tanggalLembur])."', mulaiLembur='".$mulaiLembur."', selesaiLembur='".$selesaiLembur."', keteranganLembur='$inp[keteranganLembur]', noteLembur='$inp[noteLembur]', sdmLembur='$inp[sdmLembur]', overtimeLembur='".setAngka($inp[overtimeLembur])."', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idLembur='$par[idLembur]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idLembur")."';</script>";
	}	
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[mulaiLembur];
		$selesaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[selesaiLembur];
		
		$sql="update att_lembur set idPegawai='$inp[idPegawai]', idAtasan='$inp[idAtasan]', nomorLembur='$inp[nomorLembur]', tanggalLembur='".setTanggal($inp[tanggalLembur])."', mulaiLembur='".$mulaiLembur."', selesaiLembur='".$selesaiLembur."', keteranganLembur='$inp[keteranganLembur]', catatanLembur='$inp[catatanLembur]', persetujuanLembur='$inp[persetujuanLembur]', overtimeLembur='".setAngka($inp[overtimeLembur])."', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idLembur='$par[idLembur]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idLembur")."';</script>";
	}	
	
	function formAll(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanLembur = $par[mode] == "allSdm" ? "sdmLembur" : "persetujuanLembur";
		$keteranganLembur = $par[mode] == "allSdm" ? "noteLembur" : "catatanLembur";
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Approve All</h1>
					".getBread(ucwords("approve ".$par[mode]))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[$persetujuanLembur]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[$persetujuanLembur]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[$persetujuanLembur]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[$keteranganLembur]\" name=\"inp[$keteranganLembur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[$keteranganLembur]</textarea>
						</div>
					</p>					
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanLembur] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanLembur] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanLembur] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmLembur] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmLembur] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmLembur] == "r" ? "checked=\"checked\"" : "";
				
		if(empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
		if(empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		
		$overtimeLembur = getAngka($r[overtimeLembur]);
		if(empty($overtimeLembur)) $r[overtimeLembur] = $r[durasiLembur];
		
		setValidation("is_null","inp[nomorLembur]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalLembur","anda harus mengisi tanggal");
		setValidation("is_null","mulaiLembur_tanggal","anda harus mengisi tanggal");
		setValidation("is_null","mulaiLembur","anda harus mengisi waktu");
		setValidation("is_null","selesaiLembur","anda harus mengisi waktu");		
		setValidation("is_null","inp[idAtasan]","anda harus mengisi atasan");
		$text = getValidation();

		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[nomorLembur]\" name=\"inp[nomorLembur]\"  value=\"$r[nomorLembur]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
								<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg".getPar($par,"mode,filter")."',1000,525);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalLembur\" name=\"inp[tanggalLembur]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalLembur])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Divisi</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaDivisi]\" name=\"inp[namaDivisi]\"  value=\"$r_[namaDivisi]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN LEMBUR</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiLembur_tanggal\" name=\"inp[mulaiLembur_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($mulaiLembur_tanggal)."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiLembur\" name=\"inp[mulaiLembur]\" size=\"10\" maxlength=\"5\" value=\"".substr($mulaiLembur,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/> s.d 
								<input type=\"text\" id=\"selesaiLembur\" name=\"inp[selesaiLembur]\" size=\"10\" maxlength=\"5\" value=\"".substr($selesaiLembur,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						
					</td>
					<td width=\"55%\">";
					
					$sql_="select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='".$r[idAtasan]."'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
					$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
					$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanLembur;
					
					list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
					$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
					$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<div class=\"field\">					
								<input type=\"hidden\" id=\"inp[idAtasan]\" name=\"inp[idAtasan]\"  value=\"$r[idAtasan]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikAtasan]\" name=\"inp[nikAtasan]\"  value=\"$r_[nikAtasan]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getAtasan('".getPar($par,"mode, nikPegawai")."');\" />
								<input type=\"text\" id=\"inp[namaAtasan]\" name=\"inp[namaAtasan]\"  value=\"$r_[namaAtasan]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganLembur]\" name=\"inp[keteranganLembur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[keteranganLembur]</textarea>
							</div>
						</p>						
					</td>
					</tr>
					</table>
					<div style=\"float:right; margin-top:10px;\">
						<table width=\"100%\">
						<tr>						
						<td>
							<table>
							<tr>
							<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
							<td>".$persetujuanLembur." ".$approveTime."</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
					</div>";
					
			if($par[mode] == "app")		
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanLembur]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanLembur]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanLembur]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanLembur]\" name=\"inp[catatanLembur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanLembur]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[overtimeLembur]\" name=\"inp[overtimeLembur]\"  value=\"".getAngka($r[overtimeLembur])."\" class=\"mediuminput\" style=\"text-align:right; width:50px;\" onkeyup=\"cekAngka(this);\" /> Jam
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";					
			
			if($par[mode] == "sdm")		
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[sdmLembur]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmLembur]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmLembur]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteLembur]\" name=\"inp[noteLembur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteLembur]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[overtimeLembur]\" name=\"inp[overtimeLembur]\"  value=\"".getAngka($r[overtimeLembur])."\" class=\"mediuminput\" style=\"text-align:right; width:50px;\" onkeyup=\"cekAngka(this);\" /> Jam
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$text.="</div>
				<p>				
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $cGroup, $areaCheck;
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 620px;\">
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
									 	<span style=\"margin-left: 30px;\">Periode : </span>".comboMonth("par[bulanLembur]", $par[bulanLembur], "onchange=\"document.getElementById('form').submit();\"", "", "t")." ".comboYear("par[tahunLembur]", $par[tahunLembur], "", "onchange=\"document.getElementById('form').submit();\"", "", "t")."
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
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idLembur")."',725,300);\"><span>All Atasan</span></a> 
		<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idLembur")."',725,300);\"><span>All SDM</span></a>";
		$text.="</div>
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
					<th colspan=\"3\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>
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
					<th style=\"50\">Atasan</th>
					<th style=\"50\">SDM</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where t1.idPegawai is not null and t1.idPegawai > 0";
		if(!empty($par[bulanLembur]))
			$filter.= " and month(t1.tanggalJadwal)='$par[bulanLembur]'";
		if(!empty($par[tahunLembur]))
			$filter.= " and year(t1.tanggalJadwal)='$par[tahunLembur]'";
		if(!empty($par[statusPegawai]))
			$filter.=" and t2.cat = '$par[statusPegawai]'";		
		
		$arrShift=arrayQuery("select t1.idPegawai, t2.kodeShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal=arrayQuery("select t1.idPegawai, t1.tanggalJadwal, t2.kodeShift from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) $filter order by t1.idJadwal");
		
		$filter = "where idPegawai is not null and idPegawai > 0";
		if(!empty($par[bulanLembur]))
			$filter.= " and month(tanggalAbsen)='$par[bulanLembur]'";
		if(!empty($par[tahunLembur]))
			$filter.= " and year(tanggalAbsen)='$par[tahunLembur]'";
		$arrAbsen=arrayQuery("select idPegawai, tanggalAbsen, concat(masukAbsen, '\t', pulangAbsen, '\t', durasiAbsen) from att_absen $filter order by idAbsen");
				
		$filter = "where nomorLembur is not null and idPegawai > 0";		
		if(!empty($par[bulanLembur]))
			$filter.= " and month(t1.mulaiLembur)='$par[bulanLembur]'";
		if(!empty($par[tahunLembur]))
			$filter.= " and year(t1.mulaiLembur)='$par[tahunLembur]'";				
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
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;
			
			$sdmLembur = $r[sdmLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmLembur = $r[sdmLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmLembur;
			
			list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);					
			list($mulaiLembur_jam, $mulaiLembur_menit, $mulaiLembur_detik) = explode(":", $mulaiLembur);
			
			list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
			list($selesaiLembur_tahun, $selesaiLembur_bulan, $selesaiLembur_hari) = explode("-", $selesaiLembur_tanggal);
			list($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik) = explode(":", $selesaiLembur);
			
			$persetujuanLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=app&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur") : "#";
			$sdmLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=sdm&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur") : "#";
			
			
			$tanggalLembur = $mulaiLembur_tanggal;
			$jadwalShift = empty($arrJadwal["$r[id]"][$tanggalLembur]) ? $arrShift["$r[id]"] : $arrJadwal["$r[id]"][$tanggalLembur];
			$durasiLembur = $mulaiLembur_jam > $selesaiLembur_jam ?
			selisihJam($r[mulaiLembur], date('Y-m-d H:i:s', dateAdd("d", 1, mktime($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik, $selesaiLembur_bulan, $selesaiLembur_hari, $selesaiLembur_tahun)))):
			selisihJam($r[mulaiLembur], $r[selesaiLembur]);
			
			list($mulaiAbsen, $selesaiAbsen, $durasiAbsen) = explode("\t", $arrAbsen["$r[id]"][$tanggalLembur]);
			$week = date("w", strtotime($tanggalLembur));
			$hariLembur = (getField("select idLibur from dta_libur where '".$tanggalLembur."' between mulaiLibur and selesaiLibur and statusLibur='t'") || in_array($jadwalShift, array("OFF","C")) || (in_array($week, array(0,6)) && in_array($jadwalShift, array("N")))) ? "LIBUR" : "KERJA";
			$overtimeLembur = getAngka($r[overtimeLembur]);
			$overtimeLembur = empty($overtimeLembur) ? $durasiLembur : $overtimeLembur;
			
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
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanLembur</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmLembur</a></td>
					<td align=\"center\">
						<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."',900,500);\" ><span>Cetak</span></a>
						<a href=\"?par[mode]=det&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>
				</tr>";
		}
		
		$text.="</tbody>
			</table>
			</div>
			<script>
				hideMenu();
			</script>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur  from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		if(empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
		if(empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		
		$overtimeLembur = getAngka($r[overtimeLembur]);
		if(empty($overtimeLembur)) $r[overtimeLembur] = $r[durasiLembur];
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">".$r[nomorLembur]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalLembur],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Divisi</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN LEMBUR</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($mulaiLembur_tanggal,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<span class=\"field\">".substr($mulaiLembur,0,5)." <strong>s.d</strong> ".substr($selesaiLembur,0,5)."&nbsp;</span>
						</p>						
					</td>
					<td width=\"55%\" style=\"vertical-align:top\">";
					
					$sql_="select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='".$r[idAtasan]."'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
					$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
					$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanLembur;
					
					list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
					$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
					$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
						</p>						
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganLembur])."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanLembur = "Belum Diproses";
			$persetujuanLembur = $r[persetujuanLembur] == "t" ? "Disetujui" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "f" ? "Ditolak" : $persetujuanLembur;		
			$persetujuanLembur = $r[persetujuanLembur] == "r" ? "Diperbaiki" : $persetujuanLembur;		
			list($approveDate) = explode(" ", $r[approveTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($approveDate,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[approveBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanLembur."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanLembur])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";					
					
			$sdmLembur = "Belum Diproses";
			$sdmLembur = $r[sdmLembur] == "t" ? "Disetujui" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "f" ? "Ditolak" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "r" ? "Diperbaiki" : $sdmLembur;
			list($sdmDate) = explode(" ", $r[sdmTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($sdmDate,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[sdmBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmLembur."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteLembur])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			$text.="</div>
				<p>									
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\"/>
				</p>
			</form>";
		return $text;
	}
	
	function pegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;		
		$text.="<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Daftar Pegawai</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>
					<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
				</td>
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
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where reg_no is not null";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
		$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrDivisi["$r[div_id]"]."</td>
					<td align=\"center\">
						<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
					</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>
		</div>";
		return $text;
	}
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		list($Y,$m,$d) = explode("-", $mulaiLembur_tanggal);
		$hariLembur = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];		
		
		$r[overtimeLembur] = getAngka($r[overtimeLembur]);
		if(empty($r[overtimeLembur])){
			list($H,$m) = explode(":", $r[durasiLembur]);
			$r[overtimeLembur] = $H + $m/60;
		}
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(15);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',12);					
		$pdf->Cell(20,6,'Sari Ater',0,0,'L');
		$pdf->SetFont('Arial','I',10);
		$pdf->Cell(30,6,'Hotel & Resort',0,0,'L');
		$pdf->Ln();
		
		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(180,6,'SURAT PERINTAH KERJA LEMBUR',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(180,6,'No. SPL : '.$r[nomorLembur],0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(180,2,'Kepada karyawan yang namanya tersebut dibawah ini diperintahkan kerja lembur',0,0,'L');		
		$pdf->Ln();		
		$pdf->SetFont('Arial','I');
		$pdf->Cell(180,6,'To the employees whose name is noted below working overtime',0,0,'L');		
		$pdf->Ln(10);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Untuk keperluan/Tugas',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'For/Duty',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,$r[keteranganLembur],0,'L');
		$pdf->Ln(3);		
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Pada Hari/Tanggal',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Day/Date',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,$hariLembur.", ".getTanggal($mulaiLembur_tanggal,"t"),0,'L');
		$pdf->Ln();
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Dimulai Jam',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Start Form',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(15,6,substr($mulaiLembur,0,5),0,'L');		
		$pdf->SetXY($setX+70, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'s/d',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Up to',0,0,'L');				
		$pdf->SetXY($setX+85, $setY);
		$pdf->SetFont('Arial');
		$pdf->MultiCell(15,6,substr($selesaiLembur,0,5),0,'L');		
		$pdf->Ln();
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Jumlah Jam',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Number of hours',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(15,6,$r[overtimeLembur],0,'L');		
		$pdf->SetXY($setX+70, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Jam',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Hours',0,0,'L');				
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(10,10,'No',1,0,'C');
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->Cell(80,10,'',1,0,'C');
		$pdf->Cell(60,10,'',1,0,'C');
		$pdf->Cell(30,10,'',1,0,'C');
		$pdf->SetFont('Arial','BU');
		
		$pdf->SetXY($setX, $setY+1);	
		$pdf->Cell(80,5,'Nama',0,0,'C');
		$pdf->Cell(60,5,'Jabatan',0,0,'C');
		$pdf->Cell(30,5,'Tanda Tangan',0,0,'C');
		$pdf->Ln();
		$pdf->SetXY($setX, $setY+4.5);	
		$pdf->SetFont('Arial','I');
		$pdf->Cell(80,5,'Name',0,0,'C');
		$pdf->Cell(60,5,'Position',0,0,'C');
		$pdf->Cell(30,5,'Signature',0,0,'C');
		$pdf->Ln(5.5);		
		
		$sql_="select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);		
		$pdf->SetFont('Arial');
		$pdf->SetAligns(array('C','L','L','L'));
		$pdf->SetWidths(array(10,80,60,30));
		$pdf->Cols(array(
		array("1.",
		getField("select name from emp where id='".$r[idPegawai]."'"),
		getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),
		"")
		),10);
		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(90,3,'Menyetujui,',0,0,'C');
		$pdf->Cell(90,3,'Diajukan Oleh,',0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','I');
		$pdf->Cell(90,6,'Approved by,',0,0,'C');
		$pdf->Cell(90,6,'Requested by,',0,0,'C');
		$pdf->Ln(20);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(90,5,'Maman Somantri',0,0,'C');
		$pdf->Cell(90,5,'                                           ',0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial');
		$pdf->Cell(90,3,'Human Resource Manager',0,0,'C');
		$pdf->Cell(90,3,'Head Department Concern',0,0,'C');
		
		$pdf->Ln();
		$pdf->SetFont('Arial','B');
		$pdf->Cell(90,6,'Date/Tgl :                           ',0,0,'C');
		$pdf->Cell(90,6,'Date/Tgl :                           ',0,0,'C');
		
		$pdf->Output();	
	}
	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "no":
				$text = gNomor();
			break;
			case "get":
				$text = gPegawai();
			break;
			case "peg":
				$text = pegawai();
			break;
			case "det":
				$text = detail();
			break;
			case "print":
			$text = pdf();
			break;
			case "allSdm":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allAts":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "sdm":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
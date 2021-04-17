<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="PS";
		$date=empty($_GET[tanggalGanti]) ? $inp[tanggalGanti] : $_GET[tanggalGanti];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorGanti from att_ganti where month(tanggalGanti)='$bulan' and year(tanggalGanti)='$tahun' order by nomorGanti desc limit 1");
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

		$data["namaJabatan"] = strtoupper($r_[pos_name]);
		$data["namaDivisi"] = strtoupper(getField("select namaData from mst_data where kodeData='".$r_[dir_id]."'"));
		
		return json_encode($data);
	}	

	function all(){
		global $db,$s,$inp,$par,$cUsername,$areaCheck;
		repField();				
		
		$sWhere= "where t1.idPegawai is not null and year(t1.tanggalGanti)='".$par['tSearch']."' AND t2.location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$sWhere.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t2.unit_id='".$par[unitId]."'";

		$persetujuanGanti = $par[mode] == "allSdm" ? "sdmGanti" : "persetujuanGanti";
		$keteranganGanti = $par[mode] == "allSdm" ? "noteGanti" : "catatanGanti";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
				
		$sql="select * from att_ganti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere order by t1.nomorGanti";
		$res=db($sql);	
		while($r=mysql_fetch_array($res)){			
			
			if($par[mode] == "allSdm"){				
				$sdmGanti = getField("select sdmGanti from att_ganti where idGanti='$r[idGanti]'");				
				if($sdmGanti == "t"){
					if($inp[$persetujuanGanti] != "t") updateAll($r);
				}else{
					if($inp[$persetujuanGanti] == "t") updateAll($r);
				}
			}
			
			$sql="update att_ganti set $keteranganGanti='".$inp[$keteranganGanti]."', $persetujuanGanti='".$inp[$persetujuanGanti]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' where idGanti='$r[idGanti]'";
			db($sql);
			
		}
		echo "<script>closeBox();reloadPage();</script>";
	}	
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();
		
		$sdmGanti = getField("select sdmGanti from att_ganti where idGanti='$par[idGanti]'");
		if($sdmGanti == "t"){
			if($inp[sdmGanti] != "t") update();
		}else{
			if($inp[sdmGanti] == "t") update();
		}
		
		$sql="update att_ganti set idPegawai='$inp[idPegawai]', nomorGanti='$inp[nomorGanti]', tanggalGanti='".setTanggal($inp[tanggalGanti])."', awalGanti='".setTanggal($inp[awalGanti])."', akhirGanti='".setTanggal($inp[akhirGanti])."', keteranganGanti='$inp[keteranganGanti]', noteGanti='$inp[noteGanti]', sdmGanti='$inp[sdmGanti]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idGanti='$par[idGanti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idGanti")."';</script>";
	}
	
	function updateAll($inp){
		global $db,$s,$par,$cUsername;		
				
		$sql="select * from dta_jadwal where idPegawai='$inp[idPegawai]' and tanggalJadwal='".$inp[awalGanti]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from dta_jadwal where idPegawai='$inp[idPegawai]' and tanggalJadwal='".$inp[akhirGanti]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
		$sql=empty($r_[idJadwal])?
		"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$inp[idPegawai]', '$r[idShift]', '".$inp[awalGanti]."', '$r[mulaiJadwal]', '$r[selesaiJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')":
		"update dta_jadwal set idShift='$r[idShift]', mulaiJadwal='$r[mulaiJadwal]', selesaiJadwal='$r[selesaiJadwal]', keteranganJadwal='$r[keteranganJadwal]' where idJadwal='$r_[idJadwal]'";
		db($sql);
		echo ($sql)."<br>";
		
		$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
		$sql_=empty($r[idJadwal])?
		"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$inp[idPegawai]', '$r_[idShift]', '".$inp[akhirGanti]."', '$r_[mulaiJadwal]', '$r_[selesaiJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')":
		"update dta_jadwal set idShift='$r_[idShift]', mulaiJadwal='$r_[mulaiJadwal]', selesaiJadwal='$r_[selesaiJadwal]', keteranganJadwal='$r_[keteranganJadwal]' where idJadwal='$r[idJadwal]'";
		db($sql_);
	}
	
	function update(){
		global $db,$s,$inp,$par,$cUsername;
		repField();
		
		$sql="select * from dta_jadwal where idPegawai='$inp[idPegawai]' and tanggalJadwal='".setTanggal($inp[awalGanti])."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from dta_jadwal where idPegawai='$inp[idPegawai]' and tanggalJadwal='".setTanggal($inp[akhirGanti])."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
		$sql=empty($r_[idJadwal])?
		"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$inp[idPegawai]', '$r[idShift]', '".setTanggal($inp[awalGanti])."', '$r[mulaiJadwal]', '$r[selesaiJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')":
		"update dta_jadwal set idShift='$r[idShift]', mulaiJadwal='$r[mulaiJadwal]', selesaiJadwal='$r[selesaiJadwal]', keteranganJadwal='$r[keteranganJadwal]' where idJadwal='$r_[idJadwal]'";
		db($sql);
		
		$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
		$sql_=empty($r[idJadwal])?
		"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$inp[idPegawai]', '$r_[idShift]', '".setTanggal($inp[akhirGanti])."', '$r_[mulaiJadwal]', '$r_[selesaiJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')":
		"update dta_jadwal set idShift='$r_[idShift]', mulaiJadwal='$r_[mulaiJadwal]', selesaiJadwal='$r_[selesaiJadwal]', keteranganJadwal='$r_[keteranganJadwal]' where idJadwal='$r[idJadwal]'";
		db($sql_);
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update att_ganti set idPegawai='$inp[idPegawai]', nomorGanti='$inp[nomorGanti]', tanggalGanti='".setTanggal($inp[tanggalGanti])."', awalGanti='".setTanggal($inp[awalGanti])."', akhirGanti='".setTanggal($inp[akhirGanti])."', keteranganGanti='$inp[keteranganGanti]', catatanGanti='$inp[catatanGanti]', persetujuanGanti='$inp[persetujuanGanti]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idGanti='$par[idGanti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idGanti")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from att_ganti where idGanti='$par[idGanti]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idGanti")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update att_ganti set idPegawai='$inp[idPegawai]', nomorGanti='$inp[nomorGanti]', tanggalGanti='".setTanggal($inp[tanggalGanti])."', awalGanti='".setTanggal($inp[awalGanti])."', akhirGanti='".setTanggal($inp[akhirGanti])."', keteranganGanti='$inp[keteranganGanti]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idGanti='$par[idGanti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idGanti")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();				
		$idGanti = getField("select idGanti from att_ganti order by idGanti desc limit 1")+1;		
				
		$sql="insert into att_ganti (idGanti, idPegawai, nomorGanti, tanggalGanti, awalGanti, akhirGanti, keteranganGanti, persetujuanGanti, sdmGanti, createBy, createTime) values ('$idGanti', '$inp[idPegawai]', '$inp[nomorGanti]', '".setTanggal($inp[tanggalGanti])."', '".setTanggal($inp[awalGanti])."', '".setTanggal($inp[akhirGanti])."', '$inp[keteranganGanti]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idGanti")."';</script>";
	}
	
	function formAll(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanGanti = $par[mode] == "allSdm" ? "sdmGanti" : "persetujuanGanti";
		$keteranganGanti = $par[mode] == "allSdm" ? "noteGanti" : "catatanGanti";
		
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
							<input type=\"radio\" id=\"true\" name=\"inp[$persetujuanGanti]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[$persetujuanGanti]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[$persetujuanGanti]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[$keteranganGanti]\" name=\"inp[$keteranganGanti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[$keteranganGanti]</textarea>
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
		
		$sql="select * from att_ganti where idGanti='$par[idGanti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanGanti] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanGanti] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanGanti] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmGanti] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmGanti] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmGanti] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorGanti])) $r[nomorGanti] = gNomor();
		if(empty($r[tanggalGanti])) $r[tanggalGanti] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorGanti]","anda harus mengisi nomor");
		setValidation("is_null","tanggalGanti","anda harus mengisi tanggal");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","awalGanti","anda harus mengisi pengganti");
		$text = getValidation();
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			upper(name) as namaPegawai
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
					<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[nomorGanti]\" name=\"inp[nomorGanti]\"  value=\"$r[nomorGanti]\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
								<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg".getPar($par,"mode,filter")."',1000,525);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
							</div>
						</p>																			
					</td>
					<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalGanti\" name=\"inp[tanggalGanti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalGanti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Jabatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Divisi</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaDivisi]\" name=\"inp[namaDivisi]\"  value=\"$r_[namaDivisi]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
							</div>
						</p>										
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PINDAH LIBUR</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Tanggal Shift</label>
							<div class=\"field\">
								<input type=\"text\" id=\"awalGanti\" name=\"inp[awalGanti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[awalGanti])."\" class=\"vsmallinput hasDatePicker\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Ganti Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"akhirGanti\" name=\"inp[akhirGanti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[akhirGanti])."\" class=\"vsmallinput hasDatePicker\" />
							</div>
						</p>
					</td>
					<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganGanti]\" name=\"inp[keteranganGanti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganGanti]</textarea>
							</div>
						</p>
					</td>
					</tr>
					</table>";
					
			if($par[mode] == "app")
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanGanti]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanGanti]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanGanti]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanGanti]\" name=\"inp[catatanGanti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanGanti]</textarea>
							</div>
						</p>
					</td>
					<td width=\"50%\">&nbsp;</td>
					</tr>
					</table>";
					
			if($par[mode] == "sdm")
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[sdmGanti]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmGanti]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmGanti]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteGanti]\" name=\"inp[noteGanti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteGanti]</textarea>
							</div>
						</p>
					</td>
					<td width=\"50%\">&nbsp;</td>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
		$par[idPegawai] = $cID;
		if(empty($par[tahunData])) $par[tahunData]=date('Y');
		
		$cols = 13;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		$text = table($cols, array($cols-1, $cols-2, $cols-3, $cols-4, $cols-5, $cols-6, $cols-7, $cols));			
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 500px;\">
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
					<p>					
						<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:150px;\"/>
						".comboYear("tSearch", $par[tahunData])."
					</p>
				</div>	
			</form>				
			<div id=\"pos_r\">";		
		if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts&par[tSearch]=' + document.getElementById('tSearch').value + '".getPar($par,"mode,idGanti")."',725,300);\"><span>All Atasan</span></a> 
		<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm&par[tSearch]=' + document.getElementById('tSearch').value + '".getPar($par,"mode,idGanti")."',725,300);\"><span>All SDM</span></a> ";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idGanti")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\" style=\"vertical-align:middle;\">No.</th>					
					<th rowspan=\"2\" width=\"100\" style=\"vertical-align:middle;\">Nomor</th>
					<th rowspan=\"2\" width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>
					<th rowspan=\"2\" width=\"75\" style=\"vertical-align:middle;\">NPP</th>					
					<th rowspan=\"2\" style=\"vertical-align:middle;\">Nama</th>
					<th colspan=\"2\" style=\"vertical-align:middle;\">Shift Awal</th>
					<th colspan=\"2\" style=\"vertical-align:middle;\">Shift Akhir</th>
					<th colspan=\"2\" width=\"100\" style=\"vertical-align:middle;\">Approval</th>
					<th rowspan=\"2\" width=\"50\" style=\"vertical-align:middle;\">Detail</th>
					<th rowspan=\"2\" width=\"50\">Kontrol</th>
				</tr>
				<tr>
					<th width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>
					<th width=\"50\" style=\"vertical-align:middle;\">Shift</th>
					<th width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>					
					<th width=\"50\" style=\"vertical-align:middle;\">Shift</th>
					<th width=\"50\" style=\"vertical-align:middle;\">Atasan</th>
					<th width=\"50\" style=\"vertical-align:middle;\">SDM</th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>
			<script>
				hideMenu();
			</script>";
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_ganti where idGanti='$par[idGanti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		if(empty($r[nomorGanti])) $r[nomorGanti] = gNomor();
		if(empty($r[tanggalGanti])) $r[tanggalGanti] = date('Y-m-d');				
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			upper(name) as namaPegawai
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
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Nomor</label>
							<span class=\"field\">".$r[nomorGanti]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>											
					</td>
					<td width=\"50%\">		
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalGanti],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Divisi</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>	
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PINDAH LIBUR</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"50%\" style=\"text-align:top\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Tanggal Shift</label>
							<span class=\"field\">".getTanggal($r[awalGanti],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Ganti Tanggal</label>
							<span class=\"field\">".getTanggal($r[akhirGanti],"t")."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\" style=\"text-align:top\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganGanti])."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanGanti = "Belum Diproses";
			$persetujuanGanti = $r[persetujuanGanti] == "t" ? "Disetujui" : $persetujuanGanti;
			$persetujuanGanti = $r[persetujuanGanti] == "f" ? "Ditolak" : $persetujuanGanti;	
			$persetujuanGanti = $r[persetujuanGanti] == "r" ? "Diperbaiki" : $persetujuanGanti;	
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Status</label>
							<span class=\"field\">".$persetujuanGanti."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanGanti])."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmGanti = "Belum Diproses";
			$sdmGanti = $r[sdmGanti] == "t" ? "Disetujui" : $sdmGanti;
			$sdmGanti = $r[sdmGanti] == "f" ? "Ditolak" : $sdmGanti;
			$sdmGanti = $r[sdmGanti] == "r" ? "Diperbaiki" : $sdmGanti;
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Status</label>
							<span class=\"field\">".$sdmGanti."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteGanti])."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">&nbsp;</td>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;		
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
					<th style=\"min-width:150px;\">Gedung</th>
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
					<td align=\"center\">";
			$text.=$par[mode] == "peg" ?
			"<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>":
			"<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPengganti('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>";	
			$text.="</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>
		</div>";
		return $text;
	}
	
	function lData(){
		global $s,$par,$menuAccess,$cID, $areaCheck;		
		$par[idPegawai] = $cID;		
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= "where t1.idPegawai is not null and year(t1.tanggalGanti)='".$_GET['tSearch']."' AND t2.location IN ( $areaCheck )";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.nomorGanti) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
		if(!empty($par[idLokasi]))
			$sWhere.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t2.unit_id='".$par[unitId]."'";

		$filter = "WHERE location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filter.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";
		$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where year(t1.tanggalJadwal)='".$_GET['tSearch']."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"] = $r[kodeShift];
		}
		$defShift=getField("select kodeShift from dta_shift where statusShift='t' order by idShift limit 1");
		
		$arrOrder = array(	
			"t1.nomorGanti",			
			"t1.nomorGanti",
			"t1.tanggalGanti",
			"t2.name",
			"t2.reg_no",				
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		$arrPegawai = arrayQuery("select id, concat(reg_no,'\t',name) from dta_pegawai $filter order by id");		
		$sql="select * from att_ganti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from att_ganti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$persetujuanGanti = $r[persetujuanGanti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanGanti = $r[persetujuanGanti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanGanti;
			$persetujuanGanti = $r[persetujuanGanti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanGanti;
			
			$sdmGanti = $r[sdmGanti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmGanti = $r[sdmGanti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmGanti;
			$sdmGanti = $r[sdmGanti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmGanti;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idGanti]=$r[idGanti]".getPar($par,"mode,idGanti") : "#";			
			$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idGanti]=$r[idGanti]".getPar($par,"mode,idGanti") : "#";												
			
			$controlGanti="<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idGanti]=$r[idGanti]".getPar($par,"mode,idGanti")."',900,500);\" ><span>Cetak</span></a>";
			
			if(isset($menuAccess[$s]["edit"]))
			$controlGanti.="<a href=\"#\" onclick=\"window.location='?par[mode]=edit&par[idGanti]=$r[idGanti]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idGanti,filterData,tahunData")."';\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controlGanti.=" <a href=\"#".getPar($par,"mode,idGanti,filterData,tahunData")."\" onclick=\"
			if(confirm('are you sure to delete data ?')){
				window.location='?par[mode]=del&par[idGanti]=$r[idGanti]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idGanti,filterData,tahunData")."';
			}
			\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$awalGanti = trim($arrJadwal["$r[idPegawai]"]["$r[awalGanti]"]);										
			$awalGanti_ = empty($awalGanti) ? "X" : $awalGanti;			
			
			$akhirGanti = trim($arrJadwal["$r[idPegawai]"]["$r[akhirGanti]"]);
			$akhirGanti_ = empty($akhirGanti) ? "X" : $akhirGanti;	
			
			$awalGanti = $r[sdmGanti] == "t" ? $akhirGanti_ : $awalGanti_;
			$akhirGanti = $r[sdmGanti] == "t" ? $awalGanti_ : $akhirGanti_;
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",								
				"<div align=\"left\">".$r[nomorGanti]."</div>",								
				"<div align=\"center\">".getTanggal($r[tanggalGanti])."</div>",				
				"<div align=\"left\">".$r[reg_no]."</div>",			
				"<div align=\"left\">".strtoupper($r[name])."</div>",
				"<div align=\"center\">".getTanggal($r[awalGanti])."</div>",
				"<div align=\"center\">".$awalGanti."</div>",
				"<div align=\"center\">".getTanggal($r[akhirGanti])."</div>",
				"<div align=\"center\">".$akhirGanti."</div>",
				"<div align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanGanti</a></div>",
				"<div align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmGanti</a></div>",
				"<div align=\"center\">				
				<a href=\"?par[mode]=det&par[idGanti]=$r[idGanti]".getPar($par,"mode,idGanti")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></div>",
				"<div align=\"center\">".$controlGanti."</div>",
			);
		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from att_ganti where idGanti='$par[idGanti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");		
		list($Y,$m,$d) = explode("-", $r[awalGanti]);
		$awalHari = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];		
		$awalTanggal = $awalHari.", ".getTanggal($r[awalGanti],"t");
		
		list($Y,$m,$d) = explode("-", $r[akhirGanti]);
		$akhirHari = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];		
		$akhirTanggal = $akhirHari.", ".getTanggal($r[akhirGanti],"t");
		
		$sql_="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal in ('".$r[awalGanti]."', '".$r[akhirGanti]."') and t1.idPegawai='".$r[idPegawai]."'";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){			
			$arrJadwal["$r_[idPegawai]"]["$r_[tanggalJadwal]"] = $r_[kodeShift];
		}
		$defShift=getField("select kodeShift from dta_shift where statusShift='t' order by idShift limit 1");
		
		$awalGanti = trim($arrJadwal["$r[idPegawai]"]["$r[awalGanti]"]);												
		$awalGanti_ = empty($awalGanti) ? "X" : $awalGanti;			
		
		$akhirGanti = trim($arrJadwal["$r[idPegawai]"]["$r[akhirGanti]"]);
		$akhirGanti_ = empty($akhirGanti) ? "X" : $akhirGanti;	
		
		$awalGanti = $r[sdmGanti] == "t" ? $akhirGanti_ : $awalGanti_;
		$akhirGanti = $r[sdmGanti] == "t" ? $awalGanti_ : $akhirGanti_;
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(10);
		
		$pdf->Ln();	
		$pdf->SetFont('Arial','B',10);					
		$pdf->Cell(20,6,'Sari Ater',0,0,'L');
		$pdf->SetFont('Arial','I',8);
		$pdf->Cell(30,6,'Hotel & Resort',0,0,'L');
		$pdf->Ln();
				
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(80,6,'FORM PINDAH LIBUR',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(80,6,'Nomor : '.$r[nomorGanti],0,0,'C');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(80,6,'Pindah Libur Oleh :',0,0,'L');		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial');
		$pdf->SetAligns(array('L','L','L'));
		$pdf->SetWidths(array(25,5,50));
				
		$pdf->Row(array("Nama\tb", ":", getField("select name from emp where id='".$r[idPegawai]."'")), false);
		$pdf->Row(array("Departemen\tb", ":", getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'")), false);
		$pdf->Row(array("Jabatan\tb", ":", getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'")), false);
		$pdf->Row(array("Divisi\tb", ":", getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.div_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'")), false);
		$pdf->Row(array("Shift\tb", ":", $awalTanggal." (".$awalGanti.")"), false);		
		$pdf->Row(array("Ganti\tb", ":", $akhirTanggal." (".$akhirGanti.")"), false);		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial');
		$pdf->Cell(80,3,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'").', '.getTanggal($r[tanggalGanti],"t"),0,0,'L');
		$pdf->Ln(20);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(40,5,'                                           ',0,0,'L');
		$pdf->Cell(40,5,'                                           ',0,0,'L');
		$pdf->Ln();
		$pdf->SetFont('Arial','B');
		$pdf->Cell(40,3,'Head Departemen Ybs.',0,0,'L');				
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial');
		$pdf->Cell(6,6,'',1,0,'L');
		$pdf->Cell(10,6,'Asli',0,0,'L');
		$pdf->Cell(50,6,': Diserahkan ke Time Keeper',0,0,'L');
		$pdf->Ln(8);
		
		$pdf->Cell(6,6,'',1,0,'L');
		$pdf->Cell(10,6,'Copy',0,0,'L');
		$pdf->Cell(50,6,': Head Departemen Ybs.',0,0,'L');
		
		$pdf->Output();	
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "lst":
				$text=lData();
			break;
						
			case "no":
				$text = gNomor();
			break;
			case "get":
				$text = gPegawai();
			break;			
			case "peg":
				$text = pegawai();
			break;	
			case "peng":
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
				if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
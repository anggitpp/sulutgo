<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="KA";
		$date=empty($_GET[tanggalKoreksi]) ? $inp[tanggalKoreksi] : $_GET[tanggalKoreksi];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorKoreksi from att_koreksi where month(tanggalKoreksi)='$bulan' and year(tanggalKoreksi)='$tahun' order by nomorKoreksi desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}
	
	function gAbsen(){
		global $db,$s,$inp,$par;		
		$sql="select * from att_absen where idPegawai='".$par[idPegawai]."' and tanggalAbsen='".setTanggal($par[tanggalAbsen])."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$r[masukAbsen] = substr($r[masukAbsen],0,5);
		$r[pulangAbsen] = substr($r[pulangAbsen],0,5);
		
		return json_encode($r);
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

		
		return json_encode($data);
	}
	
	function all(){
		global $s,$inp,$par,$cUsername, $areaCheck, $cGroup;
		repField();				
		
		$sWhere= "where year(t1.tanggalKoreksi)='".$par['tahunData']."' ".($cGroup != "1" ? "and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		
		$sWhere.= " AND location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$sWhere.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and unit_id='".$par[unitId]."'";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.nomorKoreksi) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
		
		$persetujuanKoreksi = $par[mode] == "allSdm" ? "sdmKoreksi" : "persetujuanKoreksi";
		$keteranganKoreksi = $par[mode] == "allSdm" ? "noteKoreksi" : "catatanKoreksi";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$sql="update att_koreksi t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganKoreksi='".$inp[$keteranganKoreksi]."', $persetujuanKoreksi='".$inp[$persetujuanKoreksi]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' ".$sWhere;
		db($sql);
		
		echo "<script>window.parent.location='index.php?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[mulaiKoreksi];
		$selesaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[selesaiKoreksi];
		
		$sql="update att_koreksi set idPegawai='$inp[idPegawai]', nomorKoreksi='$inp[nomorKoreksi]', tanggalKoreksi='".setTanggal($inp[tanggalKoreksi])."', mulaiKoreksi='".$mulaiKoreksi."', selesaiKoreksi='".$selesaiKoreksi."', masukKoreksi='$inp[masukKoreksi]', masukKoreksi_jam='$inp[masukKoreksi_jam]', pulangKoreksi='$inp[pulangKoreksi]', pulangKoreksi_jam='$inp[pulangKoreksi_jam]', durasiKoreksi=CASE WHEN pulangKoreksi >= masukKoreksi THEN TIMEDIFF(pulangKoreksi, masukKoreksi) ELSE ADDTIME(TIMEDIFF('24:00:00', masukKoreksi), TIMEDIFF(pulangKoreksi, '00:00:00')) END, keteranganKoreksi='$inp[keteranganKoreksi]', noteKoreksi='$inp[noteKoreksi]', sdmKoreksi='$inp[sdmKoreksi]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idKoreksi='$par[idKoreksi]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[mulaiKoreksi];
		$selesaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[selesaiKoreksi];
		
		$sql="update att_koreksi set idPegawai='$inp[idPegawai]', nomorKoreksi='$inp[nomorKoreksi]', tanggalKoreksi='".setTanggal($inp[tanggalKoreksi])."', mulaiKoreksi='".$mulaiKoreksi."', selesaiKoreksi='".$selesaiKoreksi."', masukKoreksi='$inp[masukKoreksi]', masukKoreksi_jam='$inp[masukKoreksi_jam]', pulangKoreksi='$inp[pulangKoreksi]', pulangKoreksi_jam='$inp[pulangKoreksi_jam]', durasiKoreksi=TIMEDIFF(pulangKoreksi, masukKoreksi), keteranganKoreksi='$inp[keteranganKoreksi]', catatanKoreksi='$inp[catatanKoreksi]', persetujuanKoreksi='$inp[persetujuanKoreksi]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idKoreksi='$par[idKoreksi]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from att_koreksi where idKoreksi='$par[idKoreksi]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[mulaiKoreksi];
		$selesaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[selesaiKoreksi];
		
		$sql="update att_koreksi set idPegawai='$inp[idPegawai]', nomorKoreksi='$inp[nomorKoreksi]', tanggalKoreksi='".setTanggal($inp[tanggalKoreksi])."', mulaiKoreksi='".$mulaiKoreksi."', selesaiKoreksi='".$selesaiKoreksi."', masukKoreksi='$inp[masukKoreksi]', masukKoreksi_jam='$inp[masukKoreksi_jam]', pulangKoreksi='$inp[pulangKoreksi]', pulangKoreksi_jam='$inp[pulangKoreksi_jam]', durasiKoreksi=CASE WHEN pulangKoreksi >= masukKoreksi THEN TIMEDIFF(pulangKoreksi, masukKoreksi) ELSE ADDTIME(TIMEDIFF('24:00:00', masukKoreksi), TIMEDIFF(pulangKoreksi, '00:00:00')) END, keteranganKoreksi='$inp[keteranganKoreksi]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idKoreksi='$par[idKoreksi]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();				
		$idKoreksi = getField("select idKoreksi from att_koreksi order by idKoreksi desc limit 1")+1;		
		
		$mulaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[mulaiKoreksi];
		$selesaiKoreksi = setTanggal($inp[mulaiKoreksi_tanggal])." ".$inp[selesaiKoreksi];
		
		$sql="insert into att_koreksi (idKoreksi, idPegawai, nomorKoreksi, tanggalKoreksi, mulaiKoreksi, selesaiKoreksi, masukKoreksi, masukKoreksi_jam, pulangKoreksi, pulangKoreksi_jam, durasiKoreksi, keteranganKoreksi, persetujuanKoreksi, sdmKoreksi, createBy, createTime) values ('$idKoreksi', '$inp[idPegawai]', '$inp[nomorKoreksi]', '".setTanggal($inp[tanggalKoreksi])."', '".$mulaiKoreksi."', '".$selesaiKoreksi."', '$inp[masukKoreksi]', '$inp[masukKoreksi_jam]', '$inp[pulangKoreksi]', '$inp[pulangKoreksi_jam]', CASE WHEN pulangKoreksi >= masukKoreksi THEN TIMEDIFF(pulangKoreksi, masukKoreksi) ELSE ADDTIME(TIMEDIFF('24:00:00', masukKoreksi), TIMEDIFF(pulangKoreksi, '00:00:00')) END, '$inp[keteranganKoreksi]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function formAll(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanKoreksi = $par[mode] == "allSdm" ? "sdmKoreksi" : "persetujuanKoreksi";
		$keteranganKoreksi = $par[mode] == "allSdm" ? "noteKoreksi" : "catatanKoreksi";
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
				<h1 class=\"pagetitle\">";
		$text.=$par[mode] == "allSdm" ? "Approve All (SDM)" : "Approve All (Atasan)";
		$text.="</h1>
					".getBread(ucwords("approve all"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanKoreksi."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanKoreksi."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanKoreksi."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganKoreksi."]\" name=\"inp[".$keteranganKoreksi."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		
		$sql="select * from att_koreksi where idKoreksi='$par[idKoreksi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanKoreksi] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanKoreksi] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanKoreksi] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmKoreksi] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmKoreksi] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmKoreksi] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorKoreksi])) $r[nomorKoreksi] = gNomor();
		if(empty($r[tanggalKoreksi])) $r[tanggalKoreksi] = date('Y-m-d');		
		list($mulaiKoreksi_tanggal, $mulaiKoreksi) = explode(" ", $r[mulaiKoreksi]);
		list($selesaiKoreksi_tanggal, $selesaiKoreksi) = explode(" ", $r[selesaiKoreksi]);
		$masukKoreksi_jam = $r[masukKoreksi_jam] == "t" ? "checked" : "";
		$pulangKoreksi_jam = $r[pulangKoreksi_jam] == "t" ? "checked" : "";
		
		setValidation("is_null","inp[nomorKoreksi]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalKoreksi","anda harus mengisi tanggal");		
		setValidation("is_null","mulaiKoreksi_tanggal","anda harus mengisi tanggal");
		setValidation("is_null","mulaiKoreksi","anda harus mengisi jam datang");
		setValidation("is_null","selesaiKoreksi","anda harus mengisi jam pulang");
		setValidation("is_null","masukKoreksi","anda harus mengisi perubahan datang");
		setValidation("is_null","pulangKoreksi","anda harus mengisi perubahan pulang");
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
								<input type=\"text\" id=\"inp[nomorKoreksi]\" name=\"inp[nomorKoreksi]\"  value=\"$r[nomorKoreksi]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalKoreksi\" name=\"inp[tanggalKoreksi]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalKoreksi])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaDivisi]\" name=\"inp[namaDivisi]\"  value=\"$r_[namaDivisi]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA ABSENSI</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiKoreksi_tanggal\" name=\"inp[mulaiKoreksi_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($mulaiKoreksi_tanggal)."\" class=\"vsmallinput hasDatePicker\" onchange=\"getAbsen('".getPar($par,"mode,idPegawai,tanggalAbsen")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jam Datang</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiKoreksi\" name=\"inp[mulaiKoreksi]\" size=\"10\" maxlength=\"5\" value=\"".substr($mulaiKoreksi,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jam Pulang</label>
							<div class=\"field\">
								<input type=\"text\" id=\"selesaiKoreksi\" name=\"inp[selesaiKoreksi]\" size=\"10\" maxlength=\"5\" value=\"".substr($selesaiKoreksi,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganKoreksi]\" name=\"inp[keteranganKoreksi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganKoreksi]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Koreksi Jam</label>
							<div class=\"field\">
								<input type=\"checkbox\" id=\"inp[masukKoreksi_jam]\" name=\"inp[masukKoreksi_jam]\" value=\"t\" onclick=\"cekMasuk();\" $masukKoreksi_jam /> Datang&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=\"checkbox\" id=\"inp[pulangKoreksi_jam]\" name=\"inp[pulangKoreksi_jam]\" value=\"t\" onclick=\"cekPulang();\" $pulangKoreksi_jam /> Pulang
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Perubahan Datang</label>
							<div class=\"field\">
								<input type=\"text\" id=\"masukKoreksi\" name=\"inp[masukKoreksi]\" size=\"10\" maxlength=\"5\" value=\"".substr($r[masukKoreksi],0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\" onchange=\"cekMasuk();\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Perubahan Pulang</label>
							<div class=\"field\">
								<input type=\"text\" id=\"pulangKoreksi\" name=\"inp[pulangKoreksi]\" size=\"10\" maxlength=\"5\" value=\"".substr($r[pulangKoreksi],0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\" onchange=\"cekPulang();\"/>
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
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanKoreksi]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanKoreksi]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanKoreksi]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanKoreksi]\" name=\"inp[catatanKoreksi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanKoreksi]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmKoreksi]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmKoreksi]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmKoreksi]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteKoreksi]\" name=\"inp[noteKoreksi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteKoreksi]</textarea>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
		$par[idPegawai] = $cID;
		if(empty($par[tahunData])) $par[tahunData]=date('Y');
		
		$cols = 11;		
		$text = table($cols, array($cols-1, $cols-2, $cols-3, $cols));			
		
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
										".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[statusPegawai]","All",$par[statusPegawai],"onchange=\"document.getElementById('form').submit();\"", "110px")."
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
						<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
						".comboYear("tSearch", $par[tahunData])."
					</p>
				</div>	
			</form>			
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts&par[tahunData]=' + document.getElementById('tSearch').value + '".getPar($par,"mode,idKoreksi,tahunData")."',725,300);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm&par[tahunData]=' + document.getElementById('tSearch').value + '".getPar($par,"mode,idKoreksi,tahunData")."',725,300);\"><span>All SDM</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\" style=\"vertical-align:middle;\">No.</th>
					<th rowspan=\"2\" style=\"min-width:150px; vertical-align:middle;\">Nama</th>
					<th rowspan=\"2\" width=\"100\" style=\"vertical-align:middle;\">NPP</th>
					<th rowspan=\"2\" width=\"100\" style=\"vertical-align:middle;\">Nomor</th>
					<th rowspan=\"2\" width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>
					<th colspan=\"3\" width=\"175\" style=\"vertical-align:middle;\">Koreksi</th>
					<th colspan=\"2\" width=\"100\" style=\"vertical-align:middle;\">Approval</th>
					<th rowspan=\"2\" width=\"50\" style=\"vertical-align:middle;\">Detail</th>
				</tr>
				<tr>
					<th width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>
					<th width=\"75\" style=\"vertical-align:middle;\">Datang</th>
					<th width=\"75\" style=\"vertical-align:middle;\">Pulang</th>
					<th width=\"75\" style=\"vertical-align:middle;\">Atasan</th>
					<th width=\"75\" style=\"vertical-align:middle;\">SDM</th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_koreksi where idKoreksi='$par[idKoreksi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		if(empty($r[nomorKoreksi])) $r[nomorKoreksi] = gNomor();
		if(empty($r[tanggalKoreksi])) $r[tanggalKoreksi] = date('Y-m-d');		
		list($mulaiKoreksi_tanggal, $mulaiKoreksi) = explode(" ", $r[mulaiKoreksi]);
		list($selesaiKoreksi_tanggal, $selesaiKoreksi) = explode(" ", $r[selesaiKoreksi]);
		$arrKoreksi[] = $r[masukKoreksi_jam] == "t" ? "Datang" : "";
		$arrKoreksi[] = $r[pulangKoreksi_jam] == "t" ? "Pulang" : "";
		
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
							<span class=\"field\">".$r[nomorKoreksi]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalKoreksi],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA ABSENSI</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($mulaiKoreksi_tanggal,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jam Datang</label>
							<span class=\"field\">".substr($mulaiKoreksi,0,5)."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jam Pulang</label>
							<span class=\"field\">".substr($selesaiKoreksi,0,5)."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganKoreksi])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Koreksi Jam</label>
							<span class=\"field\">".implode(", ", $arrKoreksi)."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Perubahan Datang</label>
							<span class=\"field\">".substr($r[masukKoreksi],0,5)."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Perubahan Pulang</label>
							<span class=\"field\">".substr($r[pulangKoreksi],0,5)."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanKoreksi = "Belum Diproses";
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "t" ? "Disetujui" : $persetujuanKoreksi;
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "f" ? "Ditolak" : $persetujuanKoreksi;	
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "r" ? "Diperbaiki" : $persetujuanKoreksi;	
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanKoreksi."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanKoreksi])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmKoreksi = "Belum Diproses";
			$sdmKoreksi = $r[sdmKoreksi] == "t" ? "Disetujui" : $sdmKoreksi;
			$sdmKoreksi = $r[sdmKoreksi] == "f" ? "Ditolak" : $sdmKoreksi;
			$sdmKoreksi = $r[sdmKoreksi] == "r" ? "Diperbaiki" : $sdmKoreksi;
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmKoreksi."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteKoreksi])."&nbsp;</span>
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
		
		$filter = "where reg_no is not null and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
		
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
	
	function lData(){
		global $s,$par,$menuAccess,$cID, $areaCheck, $cGroup;		
		$par[idPegawai] = $cID;		
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= "where year(t1.tanggalKoreksi)='".$_GET['tSearch']."' ".($cGroup != "1" ? "and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		
		$sWhere.= " AND location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$sWhere.= " and location='".$par[idLokasi]."'";
		if(!empty($par[statusPegawai]))
			$sWhere.= " and t2.cat='".$par[statusPegawai]."'";
		if(!empty($par[divId]))
			$sWhere.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and unit_id='".$par[unitId]."'";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.nomorKoreksi) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$arrOrder = array(	
			"t1.nomorKoreksi",
			"t2.name",
			"t2.reg_no",	
			"t1.nomorKoreksi",
			"t1.tanggalKoreksi",
			"t1.mulaiKoreksi",
			"t1.masukKoreksi",
			"t1.pulangKoreksi",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select * from att_koreksi t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from att_koreksi t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$persetujuanKoreksi = $r[persetujuanKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanKoreksi;
			$persetujuanKoreksi = $r[persetujuanKoreksi] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanKoreksi;
			
			$sdmKoreksi = $r[sdmKoreksi] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmKoreksi = $r[sdmKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKoreksi;
			$sdmKoreksi = $r[sdmKoreksi] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmKoreksi;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi") : "#";			
			$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi") : "#";
			
			list($mulaiKoreksi) = explode(" ",$r[mulaiKoreksi]);
			
			$controlAbsen="";
			
			if(isset($menuAccess[$s]["edit"]))
			$controlAbsen.="<a href=\"#\" onclick=\"window.location='?par[mode]=edit&par[idKoreksi]=$r[idKoreksi]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idKoreksi,filterData,tahunData")."';\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controlAbsen.=" <a href=\"#".getPar($par,"mode,idKoreksi,filterData,tahunData")."\" onclick=\"
			if(confirm('are you sure to delete data ?')){
				window.location='?par[mode]=del&par[idKoreksi]=$r[idKoreksi]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idKoreksi,filterData,tahunData")."';
			}
			\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".strtoupper($r[name])."</div>",
				"<div align=\"left\">".$r[reg_no]."</div>",
				"<div align=\"left\">".$r[nomorKoreksi]."</div>",								
				"<div align=\"center\">".getTanggal($r[tanggalKoreksi])."</div>",
				"<div align=\"center\">".getTanggal($mulaiKoreksi)."</div>",				
				"<div align=\"center\">".substr($r[masukKoreksi],0,5)."</div>",
				"<div align=\"center\">".substr($r[pulangKoreksi],0,5)."</div>",
				"<div align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanKoreksi</a></div>",
				"<div align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmKoreksi</a></div>",
				"<div align=\"center\"><a href=\"?par[mode]=det&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></div>",												
				
			);
		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
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
			case "abs":
				$text = gAbsen();
			break;
			case "peg":
				$text = pegawai();
			break;		
			case "det":
				$text = detail();
			break;
			case "sdm":
				if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			case "allSdm":
				if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allAts":
				if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
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
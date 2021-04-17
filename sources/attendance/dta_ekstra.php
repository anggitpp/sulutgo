<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IE";
		$date=empty($_GET[tanggalEkstra]) ? $inp[tanggalEkstra] : $_GET[tanggalEkstra];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorEkstra from att_ekstra where month(tanggalEkstra)='$bulan' and year(tanggalEkstra)='$tahun' order by nomorEkstra desc limit 1");
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
		
		$data["idPengganti"] = $r_[replacement_id];
		$data["idAtasan"] = $r_[leader_id];
		
		list($data[nikPengganti], $data[namaPengganti]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[replacement_id]."'"));
		list($data[nikAtasan], $data[namaAtasan]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[leader_id]."'"));
		
		return json_encode($data);
	}	
	
	function all(){
		global $s,$inp,$par,$cUsername, $areaCheck, $cGroup;
		repField();				
		
		$filter = "where year(t1.tanggalEkstra)='$par[tahunEkstra]'".($cGroup != "1" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(t1.nomorEkstra) like '%".strtolower($par[filter])."%'
		or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
		or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$filter .= " AND t2.location IN ($areaCheck)";
		
		$persetujuanEkstra = $par[mode] == "allSdm" ? "sdmEkstra" : "persetujuanEkstra";
		$keteranganEkstra = $par[mode] == "allSdm" ? "noteEkstra" : "catatanEkstra";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$sql="update att_ekstra t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganEkstra='".$inp[$keteranganEkstra]."', $persetujuanEkstra='".$inp[$persetujuanEkstra]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' ".$sWhere;
		db($sql);
		
		$sql="select * from att_ekstra t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorEkstra";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){		
			$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$r[idDeposit]."'";
			db($sql);
		}
		
		echo "<script>window.parent.location='index.php?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();					
		
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="update att_ekstra set idTipe='$r[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorEkstra='$inp[nomorEkstra]', tanggalEkstra='".setTanggal($inp[tanggalEkstra])."', penggantiEkstra='".setTanggal($inp[penggantiEkstra])."', keteranganEkstra='$inp[keteranganEkstra]', noteEkstra='$inp[noteEkstra]', sdmEkstra='$inp[sdmEkstra]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idEkstra='$par[idEkstra]'";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		repField();				
		
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="update att_ekstra set idTipe='$r[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorEkstra='$inp[nomorEkstra]', tanggalEkstra='".setTanggal($inp[tanggalEkstra])."', penggantiEkstra='".setTanggal($inp[penggantiEkstra])."', keteranganEkstra='$inp[keteranganEkstra]', catatanEkstra='$inp[catatanEkstra]', persetujuanEkstra='$inp[persetujuanEkstra]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idEkstra='$par[idEkstra]'";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;						
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="delete from att_ekstra where idEkstra='$par[idEkstra]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="update att_ekstra set idTipe='$r[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorEkstra='$inp[nomorEkstra]', tanggalEkstra='".setTanggal($inp[tanggalEkstra])."', penggantiEkstra='".setTanggal($inp[penggantiEkstra])."', keteranganEkstra='$inp[keteranganEkstra]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idEkstra='$par[idEkstra]'";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;				
		repField();				
		$idEkstra = getField("select idEkstra from att_ekstra order by idEkstra desc limit 1")+1;		
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="insert into att_ekstra (idEkstra, idTipe, idPegawai, idPengganti, idAtasan, idDeposit, nomorEkstra, tanggalEkstra, penggantiEkstra, keteranganEkstra, persetujuanEkstra, sdmEkstra, createBy, createTime) values ('$idEkstra', '$r[idTipe]', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[idDeposit]', '$inp[nomorEkstra]', '".setTanggal($inp[tanggalEkstra])."', '".setTanggal($inp[penggantiEkstra])."', '$inp[keteranganEkstra]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function formAll(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanEkstra = $par[mode] == "allSdm" ? "sdmEkstra" : "persetujuanEkstra";
		$keteranganEkstra = $par[mode] == "allSdm" ? "noteEkstra" : "catatanEkstra";
		
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
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanEkstra."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanEkstra."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanEkstra."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganEkstra."]\" name=\"inp[".$keteranganEkstra."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		
		$sql="select * from att_ekstra where idEkstra='$par[idEkstra]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanEkstra] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanEkstra] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanEkstra] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmEkstra] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmEkstra] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmEkstra] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorEkstra])) $r[nomorEkstra] = gNomor();
		if(empty($r[idPegawai])) $r[idPegawai] = $par[idPegawai];
		if(empty($r[tanggalEkstra])) $r[tanggalEkstra] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorEkstra]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalEkstra","anda harus mengisi tanggal");				
		setValidation("is_null","penggantiEkstra","anda harus mengisi tgl. digunakan");		
		
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
		
		if(empty($r[idAtasan])) $r[idAtasan] = $r__[leader_id];
		if(empty($r[idPengganti])) $r[idPengganti] = $r__[replacement_id];
		
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
		<input type=\"text\" id=\"inp[nomorEkstra]\" name=\"inp[nomorEkstra]\"  value=\"$r[nomorEkstra]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">NPP</label>
		<div class=\"field\">								
		<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
		<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."', '".getPar($par,"idPegawai")."');\"/>
		<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg&mode=".$par[mode]."".getPar($par,"mode,filter")."',1000,525);\" />
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
		<input type=\"text\" id=\"tanggalEkstra\" name=\"inp[tanggalEkstra]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalEkstra])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DASAR EKSTRA OFF</h3></div>
		</div>
		
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>					
		<th width=\"100\">Tanggal</th>
		<th width=\"200\">Jenis</th>
		<th>Keterangan</th>
		<th width=\"50\">Pilih</th>
		</tr>
		</thead>
		<tbody>";
		
		$sql_="select * from dta_deposit t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idPegawai='".$r[idPegawai]."' and ((t1.statusDeposit='t' and t1.expiredDeposit>'".date('Y-m-d')."') or t1.idDeposit='$r[idDeposit]')";
		$res_=db($sql_);
		$no=1;
		while($r_=mysql_fetch_array($res_)){
			$checked = $r[idDeposit] == $r_[idDeposit] ? "checked=\"checked\"" : "";
			$text.="<tr>
			<td>".$no.".</td>
			<td align=\"center\">".getTanggal($r_[tanggalDeposit])."</td>
			<td>".$r_[namaData]."</td>
			<td>".$r_[keteranganDeposit]."</td>
			<td align=\"center\"><input type=\"radio\" id=\"det[".$r_[idDeposit]."]\" name=\"inp[idDeposit]\" value=\"".$r_[idDeposit]."\" $checked /></td>
			</tr>";
			$no++;
		}					
		
		if($no == 1){
			$text.="<tr>
			<td colspan=\"5\">&nbsp;</td>
			</tr>";
		}
		
		$text.="</tbody>
		</table>
		<br clear=\"all\">
		
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA EKSTRA OFF</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Tgl. Digunakan</label>
		<div class=\"field\">
		<input type=\"text\" id=\"penggantiEkstra\" name=\"inp[penggantiEkstra]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[penggantiEkstra])."\" class=\"vsmallinput hasDatePicker\" />
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<div class=\"field\">
		<textarea id=\"inp[keteranganEkstra]\" name=\"inp[keteranganEkstra]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganEkstra]</textarea>
		</div>
		</p>
		</td>
		<td width=\"55%\">";
		
		$sql_="select
		id as idPengganti,
		reg_no as nikPengganti,
		name as namaPengganti
		from emp where id='".$r[idPengganti]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);					
		$text.="<p>
		<label class=\"l-input-small\">Pengganti</label>
		<div class=\"field\">						
		<input type=\"hidden\" id=\"inp[idPengganti]\" name=\"inp[idPengganti]\"  value=\"$r[idPengganti]\" readonly=\"readonly\"/>
		<input type=\"text\" id=\"inp[nikPengganti]\" name=\"inp[nikPengganti]\"  value=\"$r_[nikPengganti]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPengganti('".getPar($par,"mode, nikPegawai")."');\" />
		<input type=\"text\" id=\"inp[namaPengganti]\" name=\"inp[namaPengganti]\"  value=\"$r_[namaPengganti]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
		</div>
		</p>";
		
		
		$sql_="select
		id as idAtasan,
		reg_no as nikAtasan,
		name as namaAtasan
		from emp where id='".$r[idAtasan]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		
		$text.="<p>
		<label class=\"l-input-small\">Atasan</label>
		<div class=\"field\">					
		<input type=\"hidden\" id=\"inp[idAtasan]\" name=\"inp[idAtasan]\"  value=\"$r[idAtasan]\" readonly=\"readonly\"/>
		<input type=\"text\" id=\"inp[nikAtasan]\" name=\"inp[nikAtasan]\"  value=\"$r_[nikAtasan]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getAtasan('".getPar($par,"mode, nikPegawai")."');\" />
		<input type=\"text\" id=\"inp[namaAtasan]\" name=\"inp[namaAtasan]\"  value=\"$r_[namaAtasan]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
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
		<input type=\"radio\" id=\"true\" name=\"inp[persetujuanEkstra]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
		<input type=\"radio\" id=\"false\" name=\"inp[persetujuanEkstra]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
		<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanEkstra]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<div class=\"field\">
		<textarea id=\"inp[catatanEkstra]\" name=\"inp[catatanEkstra]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanEkstra]</textarea>
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
		<input type=\"radio\" id=\"true\" name=\"inp[sdmEkstra]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
		<input type=\"radio\" id=\"false\" name=\"inp[sdmEkstra]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
		<input type=\"radio\" id=\"revisi\" name=\"inp[sdmEkstra]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<div class=\"field\">
		<textarea id=\"inp[noteEkstra]\" name=\"inp[noteEkstra]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteEkstra]</textarea>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck, $cGroup;
		$par[idPegawai] = $cID;
		if(empty($par[tahunEkstra])) $par[tahunEkstra]=date('Y');
		$text.="
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>
		" . empLocHeader() . "
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
		<table>
		<tr>
		<td>Search : </td>				
		<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
		<td>".comboYear("par[tahunEkstra]", $par[tahunEkstra])."</td>
		<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
		</tr>
		</table>
		</div>
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idEkstra")."',725,300);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idEkstra")."',725,300);\"><span>All SDM</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>
		<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
		<th rowspan=\"2\" width=\"100\">NPP</th>
		<th rowspan=\"2\" width=\"100\">Nomor</th>
		<th colspan=\"2\" width=\"225\">Tanggal</th>
		<th colspan=\"2\" width=\"100\">Approval</th>
		<th rowspan=\"2\" width=\"50\">Detail</th>
		</tr>
		<tr>
		<th width=\"75\">Dibuat</th>
		<th width=\"75\">Digunakan</th>		
		<th width=\"50\">Atasan</th>
		<th width=\"50\">SDM</th>
		</tr>
		</thead>
		<tbody>";
		
		$filter = "where year(t1.tanggalEkstra)='$par[tahunEkstra]'".($cGroup != "1" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(t1.nomorEkstra) like '%".strtolower($par[filter])."%'
		or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
		or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$filter .= " AND t2.location IN ($areaCheck)";
		
		$sql="select * from att_ekstra t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorEkstra";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			$persetujuanEkstra = $r[persetujuanEkstra] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanEkstra = $r[persetujuanEkstra] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanEkstra;
			$persetujuanEkstra = $r[persetujuanEkstra] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanEkstra;
			
			$sdmEkstra = $r[sdmEkstra] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmEkstra = $r[sdmEkstra] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmEkstra;
			$sdmEkstra = $r[sdmEkstra] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmEkstra;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra") : "#";			
			$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra") : "#";
			
			$text.="<tr>
			<td>$no.</td>
			<td>".strtoupper($r[name])."</td>
			<td>$r[reg_no]</td>
			<td>$r[nomorEkstra]</td>
			<td align=\"center\">".getTanggal($r[tanggalEkstra])."</td>
			<td align=\"center\">".getTanggal($r[penggantiEkstra])."</td>			
			<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanEkstra</a></td>
			<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmEkstra</a></td>
			<td align=\"center\">
			<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra")."',900,500);\" ><span>Cetak</span></a>
			<a href=\"?par[mode]=det&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
			</td>
			</tr>";
		}	
		
		$text.="</tbody>
		</table>
		</div>";
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		$sql="select * from att_ekstra where idEkstra='$par[idEkstra]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanEkstra] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanEkstra] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanEkstra] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmEkstra] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmEkstra] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmEkstra] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorEkstra])) $r[nomorEkstra] = gNomor();
		if(empty($r[idPegawai])) $r[idPegawai] = $par[idPegawai];
		if(empty($r[tanggalEkstra])) $r[tanggalEkstra] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorEkstra]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalEkstra","anda harus mengisi tanggal");				
		setValidation("is_null","penggantiEkstra","anda harus mengisi tgl. digunakan");		
		
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
		
		if(empty($r[idAtasan])) $r[idAtasan] = $r__[leader_id];
		if(empty($r[idPengganti])) $r[idPengganti] = $r__[replacement_id];
		
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
		<span class=\"field\">$r[nomorEkstra]&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">NPP</label>
		<span class=\"field\">$r_[nikPegawai]&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Nama</label>							
		<span class=\"field\">$r_[namaPegawai]&nbsp;</span>		
		</p>
		</td>
		<td width=\"55%\">
		<p>
		<label class=\"l-input-small\">Tanggal</label>
		<span class=\"field\">".getTanggal($r[tanggalEkstra],"t")."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Jabatan</label>
		<span class=\"field\">$r_[namaJabatan]&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Divisi</label>
		<span class=\"field\">$r_[namaDivisi]&nbsp;</span>
		</p>
		</td>
		</tr>
		</table>
		
		
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DASAR EKSTRA OFF</h3></div>
		</div>		
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>					
		<th width=\"100\">Tanggal</th>
		<th width=\"200\">Jenis</th>
		<th>Keterangan</th>		
		</tr>
		</thead>
		<tbody>";
		
		$sql_="select * from dta_deposit t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idPegawai='".$r[idPegawai]."' and t1.idDeposit='$r[idDeposit]'";
		$res_=db($sql_);
		$no=1;
		while($r_=mysql_fetch_array($res_)){
			$checked = $r[idDeposit] == $r_[idDeposit] ? "checked=\"checked\"" : "";
			$text.="<tr>
			<td>".$no.".</td>
			<td align=\"center\">".getTanggal($r_[tanggalDeposit])."</td>
			<td>".$r_[namaData]."</td>
			<td>".$r_[keteranganDeposit]."</td>			
			</tr>";
			$no++;
		}					
		
		if($no == 1){
			$text.="<tr>
			<td colspan=\"5\">&nbsp;</td>
			</tr>";
		}
		
		$text.="</tbody>
		</table>
		<br clear=\"all\">
		
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA EKSTRA OFF</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Tgl. Digunakan</label>
		<span class=\"field\">".getTanggal($r[penggantiEkstra],"t")."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[keteranganEkstra])."&nbsp;</span>
		</p>
		</td>
		<td width=\"55%\">";
		
		$sql_="select
		id as idPengganti,
		reg_no as nikPengganti,
		name as namaPengganti
		from emp where id='".$r[idPengganti]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);					
		$text.="<p>
		<label class=\"l-input-small\">Pengganti</label>
		<span class=\"field\">".$r_[nikPengganti]." -- ".$r_[namaPengganti]."&nbsp;</span>		
		</p>";
		
		
		$sql_="select
		id as idAtasan,
		reg_no as nikAtasan,
		name as namaAtasan
		from emp where id='".$r[idAtasan]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		
		$text.="<p>
		<label class=\"l-input-small\">Atasan</label>
		<span class=\"field\">".$r_[nikAtasan]." -- ".$r_[namaAtasan]."&nbsp;</span>
		</p>
		
		</td>
		</tr>
		</table>";
				
		$persetujuanEkstra = "Belum Diproses";
		$persetujuanEkstra = $r[persetujuanEkstra] == "t" ? "Disetujui" : $persetujuanEkstra;
		$persetujuanEkstra = $r[persetujuanEkstra] == "f" ? "Ditolak" : $persetujuanEkstra;		
		$persetujuanEkstra = $r[persetujuanEkstra] == "r" ? "Diperbaiki" : $persetujuanEkstra;		
		
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
		</div>			
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Status</label>
		<span class=\"field\">".$persetujuanEkstra."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[catatanEkstra])."&nbsp;</span>
		</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
		</tr>
		</table>";
		
		$sdmEkstra = "Belum Diproses";
		$sdmEkstra = $r[sdmEkstra] == "t" ? "Disetujui" : $sdmEkstra;
		$sdmEkstra = $r[sdmEkstra] == "f" ? "Ditolak" : $sdmEkstra;
		$sdmEkstra = $r[sdmEkstra] == "r" ? "Diperbaiki" : $sdmEkstra;
		
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
		</div>			
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">
		<p>
		<label class=\"l-input-small\">Status</label>
		<span class=\"field\">".$sdmEkstra."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[noteEkstra])."&nbsp;</span>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID,$cGroup, $areaCheck;
		$par[idPegawai] = $cID;		
		$text.="
		<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Daftar Pegawai</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>    
		" . empLocHeader() . "
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
		
		if($cGroup != 1)
		$filter = "where reg_no is not null and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
		else
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
		
		$filter .= " AND t2.location IN ($areaCheck)";
		
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
			<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."', '&par[mode]=".$_GET[mode]."".getPar($par, "mode,idPegawai")."')\"><span>Detail</span></a>
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
		
		$sql="select * from att_ekstra where idEkstra='$par[idEkstra]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");		
		list($Y,$m,$d) = explode("-", $r[penggantiEkstra]);
		$penggantiHari = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];		
		$penggantiTanggal = $penggantiHari.", ".getTanggal($r[penggantiEkstra],"t");
		
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
		$pdf->Cell(80,6,'SURAT IJIN EKSTRA OFF',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(80,6,'Nomor : '.$r[nomorEkstra],0,0,'C');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(80,6,'Kami berikan ijin kepada :',0,0,'L');		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial');
		$pdf->SetAligns(array('L','L','L'));
		$pdf->SetWidths(array(25,5,50));
				
		$pdf->Row(array("Nama\tb", ":", getField("select name from emp where id='".$r[idPegawai]."'")), false);
		$pdf->Row(array("Departemen\tb", ":", getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'")), false);
		$pdf->Row(array("Jabatan\tb", ":", getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'")), false);
		$pdf->Row(array("Hari, Tanggal\tb", ":", $penggantiTanggal), false);
				
		$sql_="select * from dta_deposit t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idPegawai='".$r[idPegawai]."' and t1.idDeposit='$r[idDeposit]'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$pdf->Row(array("Ekstra Off\tb", ":", getTanggal($r_[tanggalDeposit],"t")."\n".$r_[keteranganDeposit]." (".$r_[namaData].") "), false);		
		$pdf->Row(array("Keterangan\tb", ":", $r[keteranganEkstra]), false);
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial');
		$pdf->Cell(80,3,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'").', '.getTanggal($r[tanggalEkstra],"t"),0,0,'L');
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
			case "no":
			$text = gNomor();
			break;
			case "get":
			$text = gPegawai();
			break;
			case "cut":
			$text = gEkstra();
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
			case "sdm":
			if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
			if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			case "allSdm":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allAts":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
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
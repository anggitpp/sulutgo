<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $s,$inp,$par;
		$prefix="IC";
		$date=empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorCuti from att_cuti where month(tanggalCuti)='$bulan' and year(tanggalCuti)='$tahun' order by nomorCuti desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}
	
	function gCuti(){
		global $s,$inp,$par;
		$idTipe = empty($_GET[idTipe]) ? $inp[idTipe] : $_GET[idTipe];
		$tanggalCuti = empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
		$nikPegawai = empty($_GET[nikPegawai]) ? $inp[nikPegawai] : $_GET[nikPegawai];		
		$idPegawai = getField("select id from emp where reg_no='".$nikPegawai."'");
		list($tahunCuti) = explode("-", setTanggal($tanggalCuti));
		
		$sql="select * from dta_cuti where idCuti='".$idTipe."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$toleransiCuti = date('Y-m-d', dateMin("m", $r[toleransiCuti], mktime(0,0,0,12,31,$tahunCuti)));
		list($tahunToleransi) = explode($toleransiCuti);
		
		$arrCuti = arrayQuery("select year(tanggalCuti) from att_cuti where idPegawai='".$idPegawai."' and idTipe='".$idTipe."' and persetujuanCuti='t' and sdmCuti='t'  and year(tanggalCuti) between '".$tahunToleransi."' and '".($tahunCuti-1)."'");
		$cntCuti = count($arrCuti) + 1;
		
		$jumlahCuti = getField("select sum(jumlahCuti) from att_cuti where idPegawai='".$idPegawai."' and idTipe='".$idTipe."' and persetujuanCuti='t' and sdmCuti='t' and year(tanggalCuti) between '".$tahunToleransi."' and '".$tahunCuti."'");
				
		$jatahCuti = $r[jatahCuti] * $cntCuti - $jumlahCuti;
		
		return $jatahCuti;
	}
	
	function gPegawai(){
		global $s,$inp,$par;
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
	
	function hapus(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from att_cuti where idCuti='$par[idCuti]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	
	function all(){
		global $s,$inp,$par,$cUsername;
		repField();				
		
		$filter = "where nomorCuti is not null";		
		if(!empty($par[bulanCuti]))
			$filter.= " and month(t1.mulaiCuti)='$par[bulanCuti]'";
		if(!empty($par[tahunCuti]))
			$filter.= " and year(t1.mulaiCuti)='$par[tahunCuti]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorCuti) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$persetujuanCuti = $par[mode] == "allSdm" ? "sdmCuti" : "persetujuanCuti";
		$keteranganCuti = $par[mode] == "allSdm" ? "noteCuti" : "catatanCuti";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$sql="update att_cuti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganCuti='".$inp[$keteranganCuti]."', $persetujuanCuti='".$inp[$persetujuanCuti]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' $filter";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function sdm(){
		global $s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update att_cuti set idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', noteCuti='$inp[noteCuti]', sdmCuti='$inp[sdmCuti]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	function approve(){
		global $s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update att_cuti set idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', catatanCuti='$inp[catatanCuti]', persetujuanCuti='$inp[persetujuanCuti]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update att_cuti set idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;				
		repField();				
		$idCuti = getField("select idCuti from att_cuti order by idCuti desc limit 1")+1;		
				
		$sql="insert into att_cuti (idCuti, idTipe, idPegawai, idPengganti, idAtasan, nomorCuti, tanggalCuti, mulaiCuti, selesaiCuti, jatahCuti, jumlahCuti, sisaCuti, keteranganCuti, persetujuanCuti, sdmCuti, createBy, createTime) values ('$idCuti', '$inp[idTipe]', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[nomorCuti]', '".setTanggal($inp[tanggalCuti])."', '".setTanggal($inp[mulaiCuti])."', '".setTanggal($inp[selesaiCuti])."', '".setAngka($inp[jatahCuti])."', '".setAngka($inp[jumlahCuti])."', '".setAngka($inp[sisaCuti])."', '$inp[keteranganCuti]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	function formAll(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanCuti = $par[mode] == "allSdm" ? "sdmCuti" : "persetujuanCuti";
		$keteranganCuti = $par[mode] == "allSdm" ? "noteCuti" : "catatanCuti";
		
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
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanCuti."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanCuti."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanCuti."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganCuti."]\" name=\"inp[".$keteranganCuti."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanCuti] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanCuti] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanCuti] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmCuti] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmCuti] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmCuti] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
		if(empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorCuti]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalCuti","anda harus mengisi tanggal");		
		setValidation("is_null","inp[idTipe]","anda harus mengisi tipe cuti");
		setValidation("is_null","mulaiCuti","anda harus mengisi mulai");
		setValidation("is_null","selesaiCuti","anda harus mengisi selesai");
		setValidation("is_null","inp[idPengganti]","anda harus mengisi pengganti");
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
								<input type=\"text\" id=\"inp[nomorCuti]\" name=\"inp[nomorCuti]\"  value=\"$r[nomorCuti]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalCuti\" name=\"inp[tanggalCuti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalCuti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tipe Cuti</label>
							<div class=\"field\">
								".comboData("select * from dta_cuti where jatahCuti > 0 and (idLokasi='".$r__[location]."' or idLokasi='') order by idCuti","idCuti","namaCuti","inp[idTipe]"," ",$r[idTipe],"onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tanggal Cuti</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiCuti\" name=\"inp[mulaiCuti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiCuti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\" /> s.d <input type=\"text\" id=\"selesaiCuti\" name=\"inp[selesaiCuti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiCuti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jatah Cuti</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[jatahCuti]\" name=\"inp[jatahCuti]\"  value=\"".getAngka($r[jatahCuti])."\" class=\"mediuminput\" style=\"width:50px; text-align:right; float:left\" readonly=\"readonly\"/> <span style=\"float:left; margin-left:2px; margin-top:5px; margin-right:30px;\">hari</span>
								<label class=\"l-input-small\">&nbsp;&nbsp;&nbsp;Pengambilan</label>
								<div class=\"field\">
									<input type=\"text\" id=\"inp[jumlahCuti]\" name=\"inp[jumlahCuti]\"  value=\"".getAngka($r[jumlahCuti])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" readonly=\"readonly\"/> hari
								</div>
							</div>
						</p>												
						<p>
							<label class=\"l-input-small\">Sisa Cuti</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[sisaCuti]\" name=\"inp[sisaCuti]\"  value=\"".getAngka($r[sisaCuti])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" readonly=\"readonly\"/> hari
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
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganCuti]\" name=\"inp[keteranganCuti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:370px;\">$r[keteranganCuti]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanCuti]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanCuti]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanCuti]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanCuti]\" name=\"inp[catatanCuti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanCuti]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmCuti]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmCuti]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmCuti]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteCuti]\" name=\"inp[noteCuti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteCuti]</textarea>
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
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")." <span style=\"margin-left:30px;\">Periode :</span> ".comboMonth("par[bulanCuti]", $par[bulanCuti], "onchange=\"document.getElementById('form').submit();\"", "", "t")." ".comboYear("par[tahunCuti]", $par[tahunCuti], "", "onchange=\"document.getElementById('form').submit();\"", "", "t")."
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
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idCuti")."',725,300);\"><span>All Atasan</span></a> 
		<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idCuti")."',725,300);\"><span>All SDM</span></a>";
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
					<th colspan=\"3\" width=\"225\">Tanggal</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>
				</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"75\">Mulai</th>
					<th width=\"75\">Selesai</th>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorCuti is not null and t2.location in ( $areaCheck )";		
		if(!empty($par[bulanCuti]))
			$filter.= " and month(t1.mulaiCuti)='$par[bulanCuti]'";
		if(!empty($par[tahunCuti]))
			$filter.= " and year(t1.mulaiCuti)='$par[tahunCuti]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorCuti) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_cuti t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorCuti";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanCuti = $r[persetujuanCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanCuti = $r[persetujuanCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanCuti;
			$persetujuanCuti = $r[persetujuanCuti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanCuti;
			
			$sdmCuti = $r[sdmCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmCuti = $r[sdmCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmCuti;
			$sdmCuti = $r[sdmCuti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmCuti;
			
			$persetujuanLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=app&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti") : "#";			
			$sdmLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=sdm&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti") : "#";
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorCuti]</td>
					<td align=\"center\">".getTanggal($r[tanggalCuti])."</td>
					<td align=\"center\">".getTanggal($r[mulaiCuti])."</td>
					<td align=\"center\">".getTanggal($r[selesaiCuti])."</td>					
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanCuti</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmCuti</a></td>
					<td align=\"center\">
						<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."',900,500);\" ><span>Cetak</span></a>
						<a href=\"?par[mode]=det&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>
					</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		if(empty($par[tahunPinjaman])) $par[tahunPinjaman]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		
		$sql="select * from att_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
		if(empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');		
				
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
							<span class=\"field\">".$r[nomorCuti]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalCuti],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tipe Cuti</label>
							<span class=\"field\">
								".getField("select namaCuti from dta_cuti where idCuti='".$r[idTipe]."'")."&nbsp;
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Tanggal Cuti</label>
							<span class=\"field\">
								".getTanggal($r[mulaiCuti], "t")." <strong>s.d</strong> ".getTanggal($r[selesaiCuti], "t")."
							</span>
						</p>
						<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
						<p>
							<label class=\"l-input-small\">Jatah Cuti</label>
							<span>
								<div style=\"float:left; width:125px;\">".getAngka($r[jatahCuti])." hari</div>
								<span>
									<label class=\"l-input-small\">&nbsp;&nbsp;&nbsp;Pengambilan</label>
									".getAngka($r[jumlahCuti])." hari
								</span>
							</span>
						</p>
						</div>											
						<p>
							<label class=\"l-input-small\">Sisa Cuti</label>
							<span class=\"field\">
								".getAngka($r[sisaCuti])." hari
							</span>
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
							<span class=\"field\">".$r_[nikPengganti]." - ".$r_[namaPengganti]."&nbsp;</span>
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
							<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">
								".nl2br($r[keteranganCuti])."&nbsp;
							</span>
						</p>
					</td>
					</tr>
					</table>";
					
			$persetujuanCuti = "Belum Diproses";
			$persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
			$persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;	
			$persetujuanCuti = $r[persetujuanCuti] == "r" ? "Diperbaiki" : $persetujuanCuti;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanCuti."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanCuti])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmCuti = "Belum Diproses";
			$sdmCuti = $r[sdmCuti] == "t" ? "Disetujui" : $sdmCuti;
			$sdmCuti = $r[sdmCuti] == "f" ? "Ditolak" : $sdmCuti;		
			$sdmCuti = $r[sdmCuti] == "r" ? "Diperbaiki" : $sdmCuti;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmCuti."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteCuti])."&nbsp;</span>
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
		global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;		
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
		
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[7]."'");
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
		
		$sql="select * from att_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$tanggalCuti = getTanggal($r[mulaiCuti], "t");
		if($r[mulaiCuti] != $r[selesaiCuti]) $tanggalCuti.= " - ".getTanggal($r[selesaiCuti], "t");
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(15);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',10);					
		$pdf->Cell(20,6,'Sari Ater',0,0,'L');
		$pdf->SetFont('Arial','I',8);
		$pdf->Cell(30,6,'Hotel & Resort',0,0,'L');
		$pdf->Ln();
				
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(180,6,'PERMOHONAN CUTI / IZIN',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','BI',8);
		$pdf->Cell(180,6,'LEAVE APPLICATION / PERMISSION',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(180,6,'Nomor : '.$r[nomorCuti],0,0,'C');
		$pdf->Ln(10);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Nama Karyawan',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Employees Name',0,0,'L');		
		$pdf->SetXY($setX+30, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(75,6,getField("select name from emp where id='".$r[idPegawai]."'"),0,'L');		
		$pdf->SetXY($setX+110, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Jumlah Hak Cuti',0,0,'L');		
		$pdf->SetXY($setX+110, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Sum of the leave rights',0,0,'L');		
		$pdf->SetXY($setX+140, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[jatahCuti]),0,'R');		
		$pdf->SetXY($setX+155, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+155, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');		
		$pdf->Ln(6);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Jabatan',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Position',0,0,'L');		
		$pdf->SetXY($setX+30, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(75,6,getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
		$pdf->SetXY($setX+110, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Jumlah Cuti Diambil',0,0,'L');		
		$pdf->SetXY($setX+110, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Sum of leave in taken',0,0,'L');		
		$pdf->SetXY($setX+140, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[jumlahCuti]),0,'R');		
		$pdf->SetXY($setX+155, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+155, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');		
		$pdf->Ln(6);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Departemen',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Department',0,0,'L');		
		$pdf->SetXY($setX+30, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(75,6,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
		$pdf->SetXY($setX+110, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Sisa Cuti',0,0,'L');		
		$pdf->SetXY($setX+110, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Rest of leave',0,0,'L');		
		$pdf->SetXY($setX+140, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(10,6,getAngka($r[sisaCuti]),0,'R');		
		$pdf->SetXY($setX+155, $setY);		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Hari',0,0,'L');		
		$pdf->SetXY($setX+155, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Day',0,0,'L');		
		$pdf->Ln(6);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Tipe Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Type of leave',0,0,'L');		
		$pdf->SetXY($setX+30, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(75,6,getField("select namaCuti from dta_cuti where idCuti='".$r[idTipe]."'"),0,'L');
		$pdf->SetXY($setX+110, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Tanggal Awal Cuti',0,0,'L');		
		$pdf->SetXY($setX+110, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Date of early leave',0,0,'L');		
		$pdf->SetXY($setX+140, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');			
		$pdf->MultiCell(35,6,$tanggalCuti,0,'L');
		$pdf->Ln(2);
			
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(30,3,'Alasan Cuti',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(30,6,'Reason of leave',0,0,'L');		
		$pdf->SetXY($setX+30, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(75,6,$r[keteranganCuti],0,'L');
		$pdf->Ln(8);
		
		if(getField("select trim(lower(t2.namaData)) from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'") == "ciater"){		
			$pdf->SetFont('Arial','B');
			$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
			$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
			$pdf->Cell(60,3,'Pemohon Cuti,',0,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial','I');
			$pdf->Cell(60,6,'Approved by,',0,0,'C');
			$pdf->Cell(60,6,'Approved by,',0,0,'C');
			$pdf->Cell(60,6,'Leave applicant,',0,0,'C');
			$pdf->Ln(20);		
			$pdf->SetFont('Arial','BU');
			$pdf->Cell(60,5,'Maman Somantri',0,0,'C');
			$pdf->Cell(60,5,'                                           ',0,0,'C');
			$pdf->Cell(60,5,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial');
			$pdf->Cell(60,3,'Human Resource Manager',0,0,'C');
			$pdf->Cell(60,3,'Head of Department          ',0,0,'C');
			$pdf->Cell(60,3,'',0,0,'C');
			
			$pdf->Ln();
			$pdf->Cell(60,6,'Tgl/Date :                            ',0,0,'C');
			$pdf->Cell(60,6,'Tgl/Date :                            ',0,0,'C');
			$pdf->Cell(60,6,'Tgl/Date :  '.getTanggal($r[tanggalCuti],"t"),0,0,'C');
		}else{
			$pdf->SetFont('Arial','B');
			$pdf->Cell(45,3,'Menyetujui,',0,0,'C');
			$pdf->Cell(45,3,'Menyetujui,',0,0,'C');
			$pdf->Cell(45,3,'Menyetujui,',0,0,'C');
			$pdf->Cell(45,3,'Pemohon Cuti,',0,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial','I');
			$pdf->Cell(45,6,'Approved by,',0,0,'C');
			$pdf->Cell(45,6,'Approved by,',0,0,'C');
			$pdf->Cell(45,6,'Approved by,',0,0,'C');
			$pdf->Cell(45,6,'Leave applicant,',0,0,'C');
			$pdf->Ln(20);		
			$pdf->SetFont('Arial','BU');
			$pdf->Cell(45,5,'                                           ',0,0,'C');
			$pdf->Cell(45,5,'                                           ',0,0,'C');
			$pdf->Cell(45,5,'                                           ',0,0,'C');
			$pdf->Cell(45,5,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial');
			$pdf->Cell(45,3,'Human Resource Manager',0,0,'C');
			$pdf->Cell(45,3,'',0,0,'C');
			$pdf->Cell(45,3,'',0,0,'C');
			$pdf->Cell(45,3,'',0,0,'C');
			
			$pdf->Ln();
			$pdf->Cell(45,6,'Tgl/Date :                            ',0,0,'C');
			$pdf->Cell(45,6,'Tgl/Date :                            ',0,0,'C');
			$pdf->Cell(45,6,'Tgl/Date :                            ',0,0,'C');
			$pdf->Cell(45,6,'Tgl/Date :  '.getTanggal($r[tanggalCuti],"t"),0,0,'C');
		}	
		$pdf->Output();	
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){
			case "no":
				$text = gNomor();
			break;
			case "get":
				$text = gPegawai();
			break;
			case "cut":
				$text = gCuti();
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
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			case "allSdm":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allAts":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
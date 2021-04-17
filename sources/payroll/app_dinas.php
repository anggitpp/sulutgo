<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
	$fFile = "files/dinas/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="RPJ";
		$date=empty($_GET[tanggalDinas]) ? $inp[tanggalDinas] : $_GET[tanggalDinas];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorDinas from ess_dinas where month(tanggalDinas)='$bulan' and year(tanggalDinas)='$tahun' order by nomorDinas desc limit 1");
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
		
	function upload($idDinas){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["fileDinas"]["tmp_name"];
		$fileUpload_name = $_FILES["fileDinas"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileDinas = "doc-".$idDinas.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileDinas);			
		}		
		
		return $fileDinas;
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileDinas = getField("select fileDinas from ess_dinas where idDinas='$par[idDinas]'");
		if(file_exists($fFile.$fileDinas) and $fileDinas!="")unlink($fFile.$fileDinas);
		
		$sql="update ess_dinas set fileDinas='' where idDinas='$par[idDinas]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileDinas = getField("select fileDinas from ess_dinas where idDinas='$par[idDinas]'");
		if(file_exists($fFile.$fileDinas) and $fileDinas!="")unlink($fFile.$fileDinas);
		
		$sql="delete from ess_dinas where idDinas='$par[idDinas]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
	
	function all(){
		global $db,$s,$inp,$par,$cUsername, $areaCheck;
		repField();				
		
		$filter = "where nomorDinas is not null AND t2.group_id IN ( $areaCheck )";		
		if(!empty($par[bulanDinas]))
			$filter.= " and month(t1.tanggalDinas)='$par[bulanDinas]'";
		if(!empty($par[tahunDinas]))
			$filter.= " and year(t1.tanggalDinas)='$par[tahunDinas]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$persetujuanDinas = $par[mode] == "allSdm" ? "sdmDinas" : "persetujuanDinas";
		$keteranganDinas = $par[mode] == "allSdm" ? "noteDinas" : "catatanDinas";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$persetujuanDinas = $par[mode] == "allPay" ? "pembayaranDinas" : $persetujuanDinas;
		$keteranganDinas = $par[mode] == "allPay" ? "deskripsiDinas" : $keteranganDinas;
		$approveBy = $par[mode] == "allPay" ? "paidBy" : $approveBy;
		$approveTime = $par[mode] == "allPay" ? "paidTime" : $approveTime;
		
		
		$sql="update ess_dinas t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganDinas='".$inp[$keteranganDinas]."', $persetujuanDinas='".$inp[$persetujuanDinas]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' $filter";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileDinas=upload($par[idDinas]);
				
		$sql="update ess_dinas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorDinas='$inp[nomorDinas]', tanggalDinas='".setTanggal($inp[tanggalDinas])."', namaDinas='$inp[namaDinas]', mulaiDinas='".setTanggal($inp[mulaiDinas])."', selesaiDinas='".setTanggal($inp[selesaiDinas])."', nilaiDinas='".setAngka($inp[nilaiDinas])."', keteranganDinas='$inp[keteranganDinas]', fileDinas='$fileDinas', noteDinas='$inp[noteDinas]', sdmDinas='$inp[sdmDinas]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idDinas='$par[idDinas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
	
	function paid(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileDinas=upload($par[idDinas]);
				
		$sql="update ess_dinas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorDinas='$inp[nomorDinas]', tanggalDinas='".setTanggal($inp[tanggalDinas])."', namaDinas='$inp[namaDinas]', mulaiDinas='".setTanggal($inp[mulaiDinas])."', selesaiDinas='".setTanggal($inp[selesaiDinas])."', nilaiDinas='".setAngka($inp[nilaiDinas])."', keteranganDinas='$inp[keteranganDinas]', fileDinas='$fileDinas', deskripsiDinas='$inp[deskripsiDinas]', pembayaranDinas='$inp[pembayaranDinas]', paidBy='$cUsername', paidTime='".setTanggal($inp[paidTime])." 00:00:00' where idDinas='$par[idDinas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileDinas=upload($par[idDinas]);
				
		$sql="update ess_dinas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorDinas='$inp[nomorDinas]', tanggalDinas='".setTanggal($inp[tanggalDinas])."', namaDinas='$inp[namaDinas]', mulaiDinas='".setTanggal($inp[mulaiDinas])."', selesaiDinas='".setTanggal($inp[selesaiDinas])."', nilaiDinas='".setAngka($inp[nilaiDinas])."', keteranganDinas='$inp[keteranganDinas]', fileDinas='$fileDinas', catatanDinas='$inp[catatanDinas]', persetujuanDinas='$inp[persetujuanDinas]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idDinas='$par[idDinas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileDinas=upload($par[idDinas]);
				
		$sql="update ess_dinas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorDinas='$inp[nomorDinas]', tanggalDinas='".setTanggal($inp[tanggalDinas])."', namaDinas='$inp[namaDinas]', mulaiDinas='".setTanggal($inp[mulaiDinas])."', selesaiDinas='".setTanggal($inp[selesaiDinas])."', nilaiDinas='".setAngka($inp[nilaiDinas])."', keteranganDinas='$inp[keteranganDinas]', fileDinas='$fileDinas', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDinas='$par[idDinas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();				
		$idDinas = getField("select idDinas from ess_dinas order by idDinas desc limit 1")+1;		
		$fileDinas=upload($idDinas);
		
		$sql="insert into ess_dinas (idDinas, idPegawai, idKategori, nomorDinas, tanggalDinas, namaDinas, mulaiDinas, selesaiDinas, nilaiDinas, keteranganDinas, fileDinas, persetujuanDinas, pembayaranDinas, createBy, createTime) values ('$idDinas', '$inp[idPegawai]', '$inp[idKategori]', '$inp[nomorDinas]', '".setTanggal($inp[tanggalDinas])."', '$inp[namaDinas]', '".setTanggal($inp[mulaiDinas])."', '".setTanggal($inp[selesaiDinas])."', '".setAngka($inp[nilaiDinas])."', '$inp[keteranganDinas]', '$fileDinas', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
		
	function formAll(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess,$cUsername;
		
		$persetujuanDinas = $par[mode] == "allSdm" ? "sdmDinas" : "persetujuanDinas";
		$keteranganDinas = $par[mode] == "allSdm" ? "noteDinas" : "catatanDinas";
		$approveBy = $par[mode] == "allSdm" ? "approveBy" : "sdmBy";
		$approveTime = $par[mode] == "allSdm" ? "approveTime" : "sdmTime";
		
		$persetujuanDinas = $par[mode] == "allPay" ? "pembayaranDinas" : $persetujuanDinas;
		$keteranganDinas = $par[mode] == "allPay" ? "deskripsiDinas" : $keteranganDinas;
		$approveBy = $par[mode] == "allPay" ? "paidBy" : $approveBy;
		$approveTime = $par[mode] == "allPay" ? "paidTime" : $approveTime;		
		$approveBy = $cUsername;
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
				<h1 class=\"pagetitle\">";
		if($par[mode] == "allPay")
			$text.="Pembayaran";
		else
			$text.=$par[mode] == "allSdm" ? "Approve All (SDM)" : "Approve All (Atasan)";
		$text.="</h1>
					".getBread(ucwords("approve all"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"".$approveTime."\" name=\"inp[".$approveTime."]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal(date('Y-m-d'))."\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nama</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[".$approveBy."]\" name=\"inp[".$approveBy."]\"  value=\"".getField("select namaUser from ".$db['setting'].".app_user where username='$approveBy' ")."\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanDinas."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanDinas."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanDinas."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganDinas."]\" name=\"inp[".$keteranganDinas."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID,$cUsername;
		
		$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorDinas])) $r[nomorDinas] = gNomor();
		if(empty($r[tanggalDinas])) $r[tanggalDinas] = date('Y-m-d');		
		
		$true = $r[persetujuanDinas] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanDinas] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanDinas] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmDinas] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmDinas] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmDinas] == "r" ? "checked=\"checked\"" : "";
		
		$pTrue = $r[pembayaranDinas] == "t" ? "checked=\"checked\"" : "";
		$pFalse = $r[pembayaranDinas] == "f" ? "checked=\"checked\"" : "";
		$pRevisi = $r[pembayaranDinas] == "r" ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[nomorDinas]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalDinas","anda harus mengisi tanggal");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");		
		setValidation("is_null","inp[namaDinas]","anda harus mengisi judul");
		setValidation("is_null","mulaiDinas","anda harus mengisi pelaksanaan");
		setValidation("is_null","selesaiDinas","anda harus mengisi pelaksanaan");
		setValidation("is_null","inp[nilaiDinas]","anda harus mengisi nilai");		
		$text = getValidation();

		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;								
		
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
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return save('".getPar($par, "mode")."')\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[nomorDinas]\" name=\"inp[nomorDinas]\"  value=\"$r[nomorDinas]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalDinas\" name=\"inp[tanggalDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalDinas])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PERJALANAN DINAS</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[19]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Judul</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaDinas]\" name=\"inp[namaDinas]\"  value=\"$r[namaDinas]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>		
						<p>
							<label class=\"l-input-small\">Pelaksanaan</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiDinas\" name=\"inp[mulaiDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiDinas])."\" class=\"vsmallinput hasDatePicker\"/> s.d <input type=\"text\" id=\"selesaiDinas\" name=\"inp[selesaiDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiDinas])."\" class=\"vsmallinput hasDatePicker\"/> 
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiDinas]\" name=\"inp[nilaiDinas]\"  value=\"".getAngka($r[nilaiDinas])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Bukti</label>
							<div class=\"field\">";								
								$text.=empty($r[fileDinas])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"fileDinas\" name=\"fileDinas\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=dinas&f=$par[idDinas]\"><img src=\"".getIcon($fFile."/".$r[fileDinas])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileDinas\" name=\"fileDinas\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganDinas]\" name=\"inp[keteranganDinas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[keteranganDinas]</textarea>
							</div>
						</p>						
					</td>
					</tr>
					</table>";					
					
			if($par[mode] == "app"){
				$approveBy = empty($r[approveBy]) ? $cUsername : $r[approveBy];
				list($r[approveTime]) = explode(" ",$r[approveTime]);
				if(empty($r[approveTime]) || $r[approveTime] == "0000-00-00") $r[approveTime] = date('Y-m-d');
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"approveTime\" name=\"inp[approveTime]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[approveTime])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[approveBy]\" name=\"inp[approveBy]\"  value=\"".getField("select namaUser from ".$db['setting'].".app_user where username='$approveBy' ")."\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanDinas]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanDinas]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanDinas]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanDinas]\" name=\"inp[catatanDinas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanDinas]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			}
			
			if($par[mode] == "sdm"){
			$sdmBy = empty($r[sdmBy]) ? $cUsername : $r[sdmBy];
			list($r[sdmTime]) = explode(" ",$r[sdmTime]);
			if(empty($r[sdmTime]) || $r[sdmTime] == "0000-00-00") $r[sdmTime] = date('Y-m-d');
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"sdmTime\" name=\"inp[sdmTime]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[sdmTime])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[sdmBy]\" name=\"inp[sdmBy]\"  value=\"".getField("select namaUser from ".$db['setting'].".app_user where username='$sdmBy' ")."\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[sdmDinas]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmDinas]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmDinas]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteDinas]\" name=\"inp[noteDinas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteDinas]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			}			
			
			if($par[mode] == "byr"){
			$paidBy = empty($r[paidBy]) ? $cUsername : $r[paidBy];
			list($r[paidTime]) = explode(" ",$r[paidTime]);
			if(empty($r[paidTime])) $r[paidTime] = date('Y-m-d');
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PEMBAYARAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"paidTime\" name=\"inp[paidTime]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[paidTime])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[paidBy]\" name=\"inp[paidBy]\"  value=\"".getField("select namaUser from ".$db['setting'].".app_user where username='$paidBy' ")."\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[pembayaranDinas]\" value=\"t\" $pTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[pembayaranDinas]\" value=\"f\" $pFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[pembayaranDinas]\" value=\"r\" $pRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[deskripsiDinas]\" name=\"inp[deskripsiDinas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[deskripsiDinas]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";		
			}
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
		if(empty($par[tahun])) $par[tahun]=date('Y');		
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
										<span style=\"margin-left:30px;\">Periode :</span> ".comboMonth("par[bulanDinas]", $par[bulanDinas], "onchange=\"document.getElementById('form').submit();\"", "", "t")." ".comboYear("par[tahunDinas]", $par[tahunDinas], "", "onchange=\"document.getElementById('form').submit();\"", "", "t")."
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
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idDinas")."',725,400);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idDinas")."',725,400);\"><span>All SDM</span></a> ";
		if(isset($menuAccess[$s]["apprlv3"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allPay".getPar($par,"mode,idDinas")."',725,400);\"><span>All Bayar</span></a>";
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
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" style=\"min-width:100px;\">Kategori</th>					
					<th rowspan=\"2\" width=\"100\">Nilai</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th colspan=\"2\" width=\"100\">Bayar</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>
				</tr>
				<tr>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
					<th width=\"50\">Bayar</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where nomorDinas is not null and t1.persetujuanDinas='t' AND t2.group_id IN ( $areaCheck )";	
		if(!empty($par[bulanDinas]))
			$filter.= " and month(t1.tanggalDinas)='$par[bulanDinas]'";
		if(!empty($par[tahunDinas]))
			$filter.= " and year(t1.tanggalDinas)='$par[tahunDinas]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'
			or lower(t1.namaDinas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			or lower(t2.name) like '%".strtolower($par[filter])."%'
		)";
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[19]."'");
		
		$sql="select t1.*, t2.name, t2.reg_no from ess_dinas t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorDinas";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			if(empty($r[persetujuanDinas])) $r[persetujuanDinas] = "p";
			$persetujuanDinas = $r[persetujuanDinas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanDinas = $r[persetujuanDinas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanDinas;
			$persetujuanDinas = $r[persetujuanDinas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanDinas;			
						
			if(empty($r[sdmDinas])) $r[sdmDinas] = "p";
			$sdmDinas = $r[sdmDinas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmDinas = $r[sdmDinas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmDinas;
			$sdmDinas = $r[sdmDinas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmDinas;				
						
			if(empty($r[pembayaranDinas])) $r[pembayaranDinas] = "p";
			$pembayaranDinas = $r[pembayaranDinas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$pembayaranDinas = $r[pembayaranDinas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pembayaranDinas;
			$pembayaranDinas = $r[pembayaranDinas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pembayaranDinas;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas") : "#";
			$sdmLink = (isset($menuAccess[$s]["apprlv2"]) && $r[persetujuanDinas] == "t") ? "?par[mode]=sdm&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas") : "#";
			$pembayaranLink = (isset($menuAccess[$s]["apprlv3"]) && $r[persetujuanDinas] == "t" && $r[sdmDinas] == "t") ? "?par[mode]=byr&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas") : "#";
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorDinas]</td>
					<td align=\"center\">".getTanggal($r[tanggalDinas])."</td>
					<td>".$arrKategori["$r[idKategori]"]."</td>
					<td align=\"right\">".getAngka($r[nilaiDinas])."</td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."',750,425);\" >$persetujuanDinas</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmDinas</a></td>
					<td align=\"center\"><a href=\"".$pembayaranLink."\" title=\"Detail Data\">$pembayaranDinas</a></td>
					<td align=\"center\">";
			if($r[pembayaranDinas] == "t")
			$text.="<a href=\"ajax.php?par[mode]=print&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" title=\"Print Data\" class=\"print\" target=\"print\"><span>Print</span></a>";
			$text.="&nbsp;</td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>
					</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			
			if($par[mode] == "print") pdf();
		return $text;
	}		
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql_="select * from pay_profile limit 1";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql="select * from ess_dinas t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.idDinas='".$par[idDinas]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'PERJALANAN DINAS',0,0,'L');		
		$pdf->Ln();		
		
		$pdf->Cell(200,1,'','B');
		$pdf->Ln(1.25);
		$pdf->Cell(200,1,'','T');
		$pdf->Ln();
		$pdf->Cell(200,1,'','T');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','','8');
		$pdf->SetWidths(array(90, 10, 30, 5, 65));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array($r_[namaProfile]."\tb","","Tanggal\tb",":",getTanggal($r[tanggalDinas],"t")), false);
		$pdf->Row(array($r_[alamatProfile],"","Nomor\tb",":",$r[nomorDinas]), false);
		
		$pdf->Ln();
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "DINAS\tf",":\tf",$r[namaDinas]."\tf", "\tf"));
		$pdf->SetWidths(array(5, 20, 5, 60, 10, 30, 5, 60, 5));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("\tf", "NAMA\tf",":\tf",$r[name]."\tf","\tf","KATEGORI\tf",":\tf",getField("select namaData from mst_data where kodeData='".$r[idKategori]."'")."\tf", "\tf"));
		$pdf->Row(array("\tf", "NPP\tf",":\tf",$r[reg_no]."\tf","\tf","NILAI\tf",":\tf","Rp. ".getAngka($r[nilaiDinas])."\tf", "\tf"));
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "TERBILANG\tf",":\tf",terbilang($r[nilaiDinas])." Rupiah\tf", "\tf"));
		
		$pdf->Cell(200,1,'','T');		
		$pdf->Ln(5);
		
		
		$pdf->SetWidths(array(100, 50, 50));
		$pdf->SetAligns(array('L','C','C'));
		$pdf->Row(array("KETERANGAN\tb","PENERIMA\tb","KASIR\tb"));
		$pdf->Row(array($r[pay_remark], "\n\n(".$r[name].")", "\n\n(".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[paidBy]."'").")"));
		
		$pdf->AutoPrint(true);
		$pdf->Output();
	}
	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmDinas" : "persetujuanDinas";
		$catatanField = $par[mode] == "detSdm" ? "noteDinas" : "catatanDinas";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";
		
		$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranDinas" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiDinas" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "paidTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);
				
		$persetujuanDinas = "Belum Diproses";
		$persetujuanDinas = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanDinas;
		$persetujuanDinas = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanDinas;	
		$persetujuanDinas = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanDinas;	
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$titleField."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\"  class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($dateField,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[$userField]."' ")."&nbsp;</span>
						</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$persetujuanDinas."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[$catatanField])."&nbsp;</span>
					</p>				
					<p>						
						<input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorDinas])) $r[nomorDinas] = gNomor();
		if(empty($r[tanggalDinas])) $r[tanggalDinas] = date('Y-m-d');
				
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
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
							<span class=\"field\">".$r[nomorDinas]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalDinas],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PERJALANAN DINAS</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idKategori]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Judul</label>
							<span class=\"field\">".$r[namaDinas]."&nbsp;</span>
						</p>		
						<p>
							<label class=\"l-input-small\">Pelaksanaan</label>
							<span class=\"field\">".getTanggal($r[mulaiDinas],"t")." s.d ".getTanggal($r[selesaiDinas],"t")."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[nilaiDinas])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Bukti</label>
							<div class=\"field\">";								
								$text.=empty($r[fileDinas])?
									"":
									"<a href=\"download.php?d=dinas&f=$par[idDinas]\"><img src=\"".getIcon($fFile."/".$r[fileDinas])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a><br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganDinas])."&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>";
			$persetujuanDinas = "Belum Diproses";
			$persetujuanDinas = $r[persetujuanDinas] == "t" ? "Disetujui" : $persetujuanDinas;
			$persetujuanDinas = $r[persetujuanDinas] == "f" ? "Ditolak" : $persetujuanDinas;	
			$persetujuanDinas = $r[persetujuanDinas] == "r" ? "Diperbaiki" : $persetujuanDinas;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanDinas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanDinas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmDinas = "Belum Diproses";
			$sdmDinas = $r[sdmDinas] == "t" ? "Disetujui" : $sdmDinas;
			$sdmDinas = $r[sdmDinas] == "f" ? "Ditolak" : $sdmDinas;	
			$sdmDinas = $r[sdmDinas] == "r" ? "Diperbaiki" : $sdmDinas;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmDinas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteDinas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$pembayaranDinas = "Belum Diproses";
			$pembayaranDinas = $r[pembayaranDinas] == "t" ? "Disetujui" : $pembayaranDinas;
			$pembayaranDinas = $r[pembayaranDinas] == "f" ? "Ditolak" : $pembayaranDinas;	
			$pembayaranDinas = $r[pembayaranDinas] == "r" ? "Diperbaiki" : $pembayaranDinas;	
			
			list($r[paidTime]) = explode(" ", $r[paidTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PEMBAYARAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[paidTime],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[paidBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$pembayaranDinas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[deskripsiDinas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idDinas")."';\" style=\"float:right;\"/>		
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
					<th style=\"min-width:150px;\">Divisi</th>
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
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
			break;
			
			case "det":
				$text = detail();
			break;
			case "detAts":
				$text = detailApproval();
			break;
			case "detSdm":
				$text = detailApproval();
			break;
			case "detPay":
				$text = detailApproval();
			break;
			
			case "byr":
				if(isset($menuAccess[$s]["apprlv3"])) $text = empty($_submit) ? form() : paid(); else $text = lihat();
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
			case "allPay":
				if(isset($menuAccess[$s]["apprlv3"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allSdm":
				if(isset($menuAccess[$s]["apprlv2"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;
			case "allAts":
				if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? formAll() : all(); else $text = lihat();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/kas/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="RKK";
		$date=empty($_GET[tanggalKas]) ? $inp[tanggalKas] : $_GET[tanggalKas];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorKas from ess_kas where month(tanggalKas)='$bulan' and year(tanggalKas)='$tahun' order by nomorKas desc limit 1");
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
		
	function upload($idKas){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["fileKas"]["tmp_name"];
		$fileUpload_name = $_FILES["fileKas"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileKas = "doc-".$idKas.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileKas);			
		}		
		
		return $fileKas;
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileKas = getField("select fileKas from ess_kas where idKas='$par[idKas]'");
		if(file_exists($fFile.$fileKas) and $fileKas!="")unlink($fFile.$fileKas);
		
		$sql="update ess_kas set fileKas='' where idKas='$par[idKas]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileKas = getField("select fileKas from ess_kas where idKas='$par[idKas]'");
		if(file_exists($fFile.$fileKas) and $fileKas!="")unlink($fFile.$fileKas);
		
		$sql="delete from ess_kas where idKas='$par[idKas]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
	
	function all(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$filter = "where nomorKas is not null";		
		if(!empty($par[bulanKas]))
			$filter.= " and month(t1.tanggalKas)='$par[bulanKas]'";
		if(!empty($par[tahunKas]))
			$filter.= " and year(t1.tanggalKas)='$par[tahunKas]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorKas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$persetujuanKas = $par[mode] == "allSdm" ? "sdmKas" : "persetujuanKas";
		$keteranganKas = $par[mode] == "allSdm" ? "noteKas" : "catatanKas";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$persetujuanKas = $par[mode] == "allPay" ? "pembayaranKas" : $persetujuanKas;
		$keteranganKas = $par[mode] == "allPay" ? "deskripsiKas" : $keteranganKas;
		$approveBy = $par[mode] == "allPay" ? "paidBy" : $approveBy;
		$approveTime = $par[mode] == "allPay" ? "paidTime" : $approveTime;
		
		
		$sql="update ess_kas t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganKas='".$inp[$keteranganKas]."', $persetujuanKas='".$inp[$persetujuanKas]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' $filter";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileKas=upload($par[idKas]);
				
		$sql="update ess_kas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorKas='$inp[nomorKas]', tanggalKas='".setTanggal($inp[tanggalKas])."', namaKas='$inp[namaKas]', mulaiKas='".setTanggal($inp[mulaiKas])."', selesaiKas='".setTanggal($inp[selesaiKas])."', nilaiKas='".setAngka($inp[nilaiKas])."', keteranganKas='$inp[keteranganKas]', fileKas='$fileKas', noteKas='$inp[noteKas]', sdmKas='$inp[sdmKas]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idKas='$par[idKas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
	
	function paid(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileKas=upload($par[idKas]);
				
		$sql="update ess_kas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorKas='$inp[nomorKas]', tanggalKas='".setTanggal($inp[tanggalKas])."', namaKas='$inp[namaKas]', mulaiKas='".setTanggal($inp[mulaiKas])."', selesaiKas='".setTanggal($inp[selesaiKas])."', nilaiKas='".setAngka($inp[nilaiKas])."', keteranganKas='$inp[keteranganKas]', fileKas='$fileKas', deskripsiKas='$inp[deskripsiKas]', pembayaranKas='$inp[pembayaranKas]', paidBy='$cUsername', paidTime='".setTanggal($inp[paidTime])." 00:00:00' where idKas='$par[idKas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileKas=upload($par[idKas]);
				
		$sql="update ess_kas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorKas='$inp[nomorKas]', tanggalKas='".setTanggal($inp[tanggalKas])."', namaKas='$inp[namaKas]', mulaiKas='".setTanggal($inp[mulaiKas])."', selesaiKas='".setTanggal($inp[selesaiKas])."', nilaiKas='".setAngka($inp[nilaiKas])."', keteranganKas='$inp[keteranganKas]', fileKas='$fileKas', catatanKas='$inp[catatanKas]', persetujuanKas='$inp[persetujuanKas]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idKas='$par[idKas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileKas=upload($par[idKas]);
				
		$sql="update ess_kas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorKas='$inp[nomorKas]', tanggalKas='".setTanggal($inp[tanggalKas])."', namaKas='$inp[namaKas]', mulaiKas='".setTanggal($inp[mulaiKas])."', selesaiKas='".setTanggal($inp[selesaiKas])."', nilaiKas='".setAngka($inp[nilaiKas])."', keteranganKas='$inp[keteranganKas]', fileKas='$fileKas', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idKas='$par[idKas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();				
		$idKas = getField("select idKas from ess_kas order by idKas desc limit 1")+1;		
		$fileKas=upload($idKas);
		
		$sql="insert into ess_kas (idKas, idPegawai, idKategori, nomorKas, tanggalKas, namaKas, mulaiKas, selesaiKas, nilaiKas, keteranganKas, fileKas, persetujuanKas, pembayaranKas, createBy, createTime) values ('$idKas', '$inp[idPegawai]', '$inp[idKategori]', '$inp[nomorKas]', '".setTanggal($inp[tanggalKas])."', '$inp[namaKas]', '".setTanggal($inp[mulaiKas])."', '".setTanggal($inp[selesaiKas])."', '".setAngka($inp[nilaiKas])."', '$inp[keteranganKas]', '$fileKas', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idKas")."';</script>";
	}
	
	function formAll(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess,$cUsername;
		
		$persetujuanKas = $par[mode] == "allSdm" ? "sdmKas" : "persetujuanKas";
		$keteranganKas = $par[mode] == "allSdm" ? "noteKas" : "catatanKas";
		$approveBy = $par[mode] == "allSdm" ? "approveBy" : "sdmBy";
		$approveTime = $par[mode] == "allSdm" ? "approveTime" : "sdmTime";
		
		$persetujuanKas = $par[mode] == "allPay" ? "pembayaranKas" : $persetujuanKas;
		$keteranganKas = $par[mode] == "allPay" ? "deskripsiKas" : $keteranganKas;
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
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanKas."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanKas."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanKas."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganKas."]\" name=\"inp[".$keteranganKas."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		
		$sql="select * from ess_kas where idKas='$par[idKas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorKas])) $r[nomorKas] = gNomor();
		if(empty($r[tanggalKas])) $r[tanggalKas] = date('Y-m-d');		
		
		$true = $r[persetujuanKas] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanKas] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanKas] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmKas] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmKas] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmKas] == "r" ? "checked=\"checked\"" : "";
		
		$pTrue = $r[pembayaranKas] == "t" ? "checked=\"checked\"" : "";
		$pFalse = $r[pembayaranKas] == "f" ? "checked=\"checked\"" : "";
		$pRevisi = $r[pembayaranKas] == "r" ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[nomorKas]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalKas","anda harus mengisi tanggal");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");		
		setValidation("is_null","inp[namaKas]","anda harus mengisi judul");
		setValidation("is_null","mulaiKas","anda harus mengisi pelaksanaan");
		setValidation("is_null","selesaiKas","anda harus mengisi pelaksanaan");
		setValidation("is_null","inp[nilaiKas]","anda harus mengisi nilai");		
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
								<input type=\"text\" id=\"inp[nomorKas]\" name=\"inp[nomorKas]\"  value=\"$r[nomorKas]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalKas\" name=\"inp[tanggalKas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalKas])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM KAS KECIL</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[25]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Judul</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaKas]\" name=\"inp[namaKas]\"  value=\"$r[namaKas]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>		
						<p>
							<label class=\"l-input-small\">Pelaksanaan</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiKas\" name=\"inp[mulaiKas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiKas])."\" class=\"vsmallinput hasDatePicker\"/> s.d <input type=\"text\" id=\"selesaiKas\" name=\"inp[selesaiKas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiKas])."\" class=\"vsmallinput hasDatePicker\"/> 
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiKas]\" name=\"inp[nilaiKas]\"  value=\"".getAngka($r[nilaiKas])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Bukti</label>
							<div class=\"field\">";								
								$text.=empty($r[fileKas])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"fileKas\" name=\"fileKas\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=kas&f=$par[idKas]\"><img src=\"".getIcon($fFile."/".$r[fileKas])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileKas\" name=\"fileKas\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganKas]\" name=\"inp[keteranganKas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[keteranganKas]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanKas]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanKas]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanKas]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanKas]\" name=\"inp[catatanKas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanKas]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmKas]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmKas]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmKas]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteKas]\" name=\"inp[noteKas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteKas]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[pembayaranKas]\" value=\"t\" $pTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[pembayaranKas]\" value=\"f\" $pFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[pembayaranKas]\" value=\"r\" $pRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[deskripsiKas]\" name=\"inp[deskripsiKas]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[deskripsiKas]</textarea>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID,$areaCheck;
		$par[idPegawai] = $cID;
		if(empty($par[tahun])) $par[tahun]=date('Y');		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."'  and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")." <span style=\"margin-left:30px;\">Periode :</span> ".comboMonth("par[bulanKas]", $par[bulanKas], "onchange=\"document.getElementById('form').submit();\"", "", "t")." ".comboYear("par[tahunKas]", $par[tahunKas], "", "onchange=\"document.getElementById('form').submit();\"", "", "t")."
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idKas")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a> ";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idKas")."',725,400);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idKas")."',725,400);\"><span>All SDM</span></a> ";
		if(isset($menuAccess[$s]["apprlv3"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allPay".getPar($par,"mode,idKas")."',725,400);\"><span>All Bayar</span></a>";		
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
					<th rowspan=\"2\" width=\"50\">Detail</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
				<tr>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
					<th width=\"50\">Bayar</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where nomorKas is not null and t1.persetujuanKas='t' and t2.group_id in ($areaCheck)";
		if(!empty($par[bulanKas]))
			$filter.= " and month(t1.tanggalKas)='$par[bulanKas]'";
		if(!empty($par[tahunKas]))
			$filter.= " and year(t1.tanggalKas)='$par[tahunKas]'";				
		if(!empty($par[idLokasi]))
			$filter.= " and t2.group_id='".$par[idLokasi]."'";		
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorKas) like '%".strtolower($par[filter])."%'
			or lower(t1.namaKas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			or lower(t2.name) like '%".strtolower($par[filter])."%'			
		)";
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[25]."'");
		
		$sql="select t1.*, t2.name, t2.reg_no from ess_kas t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorKas";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
						
			$persetujuanKas = $r[persetujuanKas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanKas = $r[persetujuanKas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanKas;
			$persetujuanKas = $r[persetujuanKas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanKas;			
									
			$sdmKas = $r[sdmKas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmKas = $r[sdmKas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKas;
			$sdmKas = $r[sdmKas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmKas;				
									
			$pembayaranKas = $r[pembayaranKas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$pembayaranKas = $r[pembayaranKas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pembayaranKas;
			$pembayaranKas = $r[pembayaranKas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pembayaranKas;							
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idKas]=$r[idKas]".getPar($par,"mode,idKas") : "#";
			$sdmLink = (isset($menuAccess[$s]["apprlv2"]) && $r[persetujuanKas] == "t") ? "?par[mode]=sdm&par[idKas]=$r[idKas]".getPar($par,"mode,idKas") : "#";
			$pembayaranLink = (isset($menuAccess[$s]["apprlv3"]) && $r[persetujuanKas] == "t" && $r[sdmKas] == "t") ? "?par[mode]=byr&par[idKas]=$r[idKas]".getPar($par,"mode,idKas") : "#";
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorKas]</td>
					<td align=\"center\">".getTanggal($r[tanggalKas])."</td>
					<td>".$arrKategori["$r[idKategori]"]."</td>
					<td align=\"right\">".getAngka($r[nilaiKas])."</td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idKas]=$r[idKas]".getPar($par,"mode,idKas")."',750,425);\" >$persetujuanKas</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmKas</a></td>
					<td align=\"center\"><a href=\"".$pembayaranLink."\" title=\"Detail Data\">$pembayaranKas</a></td>
					<td align=\"center\">";
			if($r[pembayaranKas] == "t")
			$text.="<a href=\"ajax.php?par[mode]=print&par[idKas]=$r[idKas]".getPar($par,"mode,idKas")."\" title=\"Print Data\" class=\"print\" target=\"print\"><span>Print</span></a>";
			$text.="&nbsp;</td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[idKas]=$r[idKas]".getPar($par,"mode,idKas")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";		
				if(in_array($r[persetujuanKas], array(0,2)))
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idKas]=$r[idKas]".getPar($par,"mode,idKas")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
			
				if(in_array($r[persetujuanKas], array(0)))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idKas]=$r[idKas]".getPar($par,"mode,idKas")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
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
		
		$sql="select * from ess_kas t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.idKas='".$par[idKas]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'REIMBURS KASIR',0,0,'L');		
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
		$pdf->Row(array($r_[namaProfile]."\tb","","Tanggal\tb",":",getTanggal($r[tanggalKas],"t")), false);
		$pdf->Row(array($r_[alamatProfile],"","Nomor\tb",":",$r[nomorKas]), false);
		
		$pdf->Ln();
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "REIMBURS\tf",":\tf",$r[namaKas]."\tf", "\tf"));
		$pdf->SetWidths(array(5, 20, 5, 60, 10, 30, 5, 60, 5));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("\tf", "NAMA\tf",":\tf",$r[name]."\tf","\tf","KATEGORI\tf",":\tf",getField("select namaData from mst_data where kodeData='".$r[idKategori]."'")."\tf", "\tf"));
		$pdf->Row(array("\tf", "NPP\tf",":\tf",$r[reg_no]."\tf","\tf","NILAI\tf",":\tf","Rp. ".getAngka($r[nilaiKas])."\tf", "\tf"));
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "TERBILANG\tf",":\tf",terbilang($r[nilaiKas])." Rupiah\tf", "\tf"));
		
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
		
		$sql="select * from ess_kas where idKas='$par[idKas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmKas" : "persetujuanKas";
		$catatanField = $par[mode] == "detSdm" ? "noteKas" : "catatanKas";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";
		
		$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranKas" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiKas" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "paidTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);
				
		$persetujuanKas = "Belum Diproses";
		$persetujuanKas = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanKas;
		$persetujuanKas = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanKas;	
		$persetujuanKas = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanKas;	
		
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
						<span class=\"field\">".$persetujuanKas."&nbsp;</span>
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
		
		$sql="select * from ess_kas where idKas='$par[idKas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorKas])) $r[nomorKas] = gNomor();
		if(empty($r[tanggalKas])) $r[tanggalKas] = date('Y-m-d');
				
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
							<span class=\"field\">".$r[nomorKas]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalKas],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM KAS KECIL</h3></div>
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
							<span class=\"field\">".$r[namaKas]."&nbsp;</span>
						</p>		
						<p>
							<label class=\"l-input-small\">Pelaksanaan</label>
							<span class=\"field\">".getTanggal($r[mulaiKas],"t")." s.d ".getTanggal($r[selesaiKas],"t")."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[nilaiKas])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Bukti</label>
							<div class=\"field\">";								
								$text.=empty($r[fileKas])?
									"":
									"<a href=\"download.php?d=kas&f=$par[idKas]\"><img src=\"".getIcon($fFile."/".$r[fileKas])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a><br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganKas])."&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>";
			$persetujuanKas = "Belum Diproses";
			$persetujuanKas = $r[persetujuanKas] == "t" ? "Disetujui" : $persetujuanKas;
			$persetujuanKas = $r[persetujuanKas] == "f" ? "Ditolak" : $persetujuanKas;	
			$persetujuanKas = $r[persetujuanKas] == "r" ? "Diperbaiki" : $persetujuanKas;	
			
			list($r[approveTime]) = explode(" ", $r[approveTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[approveTime],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[approveBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanKas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanKas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmKas = "Belum Diproses";
			$sdmKas = $r[sdmKas] == "t" ? "Disetujui" : $sdmKas;
			$sdmKas = $r[sdmKas] == "f" ? "Ditolak" : $sdmKas;	
			$sdmKas = $r[sdmKas] == "r" ? "Diperbaiki" : $sdmKas;	
			
			list($r[sdmTime]) = explode(" ", $r[sdmTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[sdmTime],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[sdmBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmKas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteKas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$pembayaranKas = "Belum Diproses";
			$pembayaranKas = $r[pembayaranKas] == "t" ? "Disetujui" : $pembayaranKas;
			$pembayaranKas = $r[pembayaranKas] == "f" ? "Ditolak" : $pembayaranKas;	
			$pembayaranKas = $r[pembayaranKas] == "r" ? "Diperbaiki" : $pembayaranKas;	
			
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
							<span class=\"field\">".$pembayaranKas."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[deskripsiKas])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idKas")."';\" style=\"float:right;\"/>		
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
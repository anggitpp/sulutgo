<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	$fFile = "files/tiket/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="PST";
		$date=empty($_GET[tanggalTiket]) ? $inp[tanggalTiket] : $_GET[tanggalTiket];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorTiket from ess_tiket where month(tanggalTiket)='$bulan' and year(tanggalTiket)='$tahun' order by nomorTiket desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}
	
	function gTipe(){
		global $db,$s,$inp,$par;		
		return getField("select lower(trim(namaData)) from mst_data where kodeData='$par[idTipe]'") == "sekali jalan" ? "none" : "block";
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
		
	function upload($idTiket){
		global $db,$s,$inp,$par,$fFile;		
		$fileTiket = getField("select fileTiket from ess_tiket where idTiket='$par[idTiket]'");
		
		$fileUpload = $_FILES["fileTiket"]["tmp_name"];
		$fileUpload_name = $_FILES["fileTiket"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileTiket = "doc-".$idTiket.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileTiket);			
		}		
		
		return $fileTiket;
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileTiket = getField("select fileTiket from ess_tiket where idTiket='$par[idTiket]'");
		if(file_exists($fFile.$fileTiket) and $fileTiket!="")unlink($fFile.$fileTiket);
		
		$sql="update ess_tiket set fileTiket='' where idTiket='$par[idTiket]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileTiket = getField("select fileTiket from ess_tiket where idTiket='$par[idTiket]'");
		if(file_exists($fFile.$fileTiket) and $fileTiket!="")unlink($fFile.$fileTiket);
		
		$sql="delete from ess_tiket where idTiket='$par[idTiket]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileTiket=upload($par[idTiket]);
		
		$berangkatTiket = setTanggal($inp[berangkatTiket_tanggal])." ".$inp[berangkatTiket_waktu];
		$pulangTiket = setTanggal($inp[pulangTiket_tanggal])." ".$inp[pulangTiket_waktu];

		$inp[cekinTanggal] = setTanggal($inp[cekinTanggal]);
		$inp[cekoutTanggal] = setTanggal($inp[cekoutTanggal]);

		$sql="update ess_tiket set namaHotel='$inp[namaHotel]',idBintang='$inp[idBintang]',cekinTanggal='$inp[cekinTanggal]',cekinJam='$inp[cekinJam]',cekoutTanggal='$inp[cekoutTanggal]',cekoutJam='$inp[cekoutJam]',alamatTiket='$inp[alamatTiket]', idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', idTipe='$inp[idTipe]', nomorTiket='$inp[nomorTiket]', tanggalTiket='".setTanggal($inp[tanggalTiket])."', berangkatTiket='$berangkatTiket', berangkatTiket_asal='$inp[berangkatTiket_asal]', berangkatTiket_tujuan='$inp[berangkatTiket_tujuan]', pulangTiket='$pulangTiket', pulangTiket_asal='$inp[pulangTiket_asal]', pulangTiket_tujuan='$inp[pulangTiket_tujuan]', keteranganTiket='$inp[keteranganTiket]', fileTiket='$fileTiket', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idTiket='$par[idTiket]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();				
		$idTiket = getField("select idTiket from ess_tiket order by idTiket desc limit 1")+1;		
		$fileTiket=upload($idTiket);
		
		$berangkatTiket = setTanggal($inp[berangkatTiket_tanggal])." ".$inp[berangkatTiket_waktu];
		$pulangTiket = setTanggal($inp[pulangTiket_tanggal])." ".$inp[pulangTiket_waktu];

		$inp[cekinTanggal] = setTanggal($inp[cekinTanggal]);
		$inp[cekoutTanggal] = setTanggal($inp[cekoutTanggal]);
		
		$sql="insert into ess_tiket (namaHotel, idBintang, cekinTanggal, cekinJam, cekoutTanggal, cekoutJam, alamatTiket, idTiket, idPegawai, idKategori, idTipe, nomorTiket, tanggalTiket, berangkatTiket, berangkatTiket_asal, berangkatTiket_tujuan, pulangTiket, pulangTiket_asal, pulangTiket_tujuan, keteranganTiket, fileTiket, persetujuanTiket, sdmTiket, createBy, createTime) values ('$inp[namaHotel]', '$inp[idBintang]', '$inp[cekinTanggal]', '$inp[cekinJam]', '$inp[cekoutTanggal]', '$inp[cekoutJam]', '$inp[alamatTiket]','$idTiket', '$inp[idPegawai]', '$inp[idKategori]', '$inp[idTipe]', '$inp[nomorTiket]', '".setTanggal($inp[tanggalTiket])."', '$berangkatTiket', '$inp[berangkatTiket_asal]', '$inp[berangkatTiket_tujuan]', '$pulangTiket', '$inp[pulangTiket_asal]', '$inp[pulangTiket_tujuan]', '$inp[keteranganTiket]', '$fileTiket', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from ess_tiket where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$persetujuanTitle = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmTiket" : "persetujuanTiket";
		$catatanField = $par[mode] == "detSdm" ? "noteTiket" : "catatanTiket";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";		
		
		$persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranTiket" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiTiket" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);		
		$persetujuanTiket = "Belum Diproses";
		$persetujuanTiket = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanTiket;
		$persetujuanTiket = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanTiket;	
		$persetujuanTiket = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanTiket;	
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$persetujuanTitle."</h1>
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
						<span class=\"field\">".$persetujuanTiket."&nbsp;</span>
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
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from ess_tiket where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorTiket])) $r[nomorTiket] = gNomor();
		if(empty($r[tanggalTiket])) $r[tanggalTiket] = date('Y-m-d');		
		
		list($berangkatTiket_tanggal, $berangkatTiket_waktu) = explode(" ", $r[berangkatTiket]);
		list($pulangTiket_tanggal, $pulangTiket_waktu) = explode(" ", $r[pulangTiket]);
		
		$pulangTiket = getField("select lower(trim(namaData)) from mst_data where kodeData='$r[idTipe]'") == "sekali jalan" ? "none" : "block";
		$txtPulang = $pulangTiket == "none" ? "&nbsp;" : "PULANG";
		
		setValidation("is_null","inp[nomorTiket]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalTiket","anda harus mengisi tanggal");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");
		setValidation("is_null","inp[idTipe]","anda harus mengisi tipe");		
		setValidation("is_null","berangkatTiket_tanggal","anda harus mengisi waktu berangkat");
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
								<input type=\"text\" id=\"inp[nomorTiket]\" name=\"inp[nomorTiket]\"  value=\"$r[nomorTiket]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							</div>
						</p>";
				$text.=empty($cID) ? 
						"<p>
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
						</p>":
						"<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"".$cID."\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>";
			$text.="</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalTiket\" name=\"inp[tanggalTiket]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalTiket])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PEMESANAN TIKET</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[20]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[21]."' order by urutanData","kodeData","namaData","inp[idTipe]"," ",$r[idTipe],"onchange=\"setPulang('".getPar($par,"mode,idKategori")."');\"", "310px")."
							</div>
						</p>						
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<div class=\"field\">";								
								$text.=empty($r[fileTiket])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"fileTiket\" name=\"fileTiket\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=tiket&f=$par[idTiket]\"><img src=\"".getIcon($fFile."/".$r[fileTiket])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileTiket\" name=\"fileTiket\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganTiket]\" name=\"inp[keteranganTiket]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[keteranganTiket]</textarea>
							</div>
						</p>						
					</td>
					</tr>
					</table>

					<table width=\"100%\">
					<tr>
					<td style=\"vertical-align:top;\">
						<div class=\"widgetbox\">
							<div class=\"title\" style=\"width:90%; margin-top:10px; margin-bottom:0px;\"><h3>BERANGKAT</h3></div>
						</div>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<div class=\"field\">
								<input type=\"text\" id=\"berangkatTiket_tanggal\" name=\"inp[berangkatTiket_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($berangkatTiket_tanggal)."\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"berangkatTiket_waktu\" name=\"inp[berangkatTiket_waktu]\" size=\"10\" maxlength=\"5\" value=\"".substr($berangkatTiket_waktu,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Asal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[berangkatTiket_asal]\" name=\"inp[berangkatTiket_asal]\"  value=\"$r[berangkatTiket_asal]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tujuan</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[berangkatTiket_tujuan]\" name=\"inp[berangkatTiket_tujuan]\"  value=\"$r[berangkatTiket_tujuan]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<div class=\"widgetbox\">
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3 id=\"txtPulang\">$txtPulang</h3></div>
						</div>
						<div id=\"pulangTiket\" style=\"display:".$pulangTiket."\">
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<div class=\"field\">
								<input type=\"text\" id=\"pulangTiket_tanggal\" name=\"inp[pulangTiket_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($pulangTiket_tanggal)."\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"pulangTiket_waktu\" name=\"inp[pulangTiket_waktu]\" size=\"10\" maxlength=\"5\" value=\"".substr($pulangTiket_waktu,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Asal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[pulangTiket_asal]\" name=\"inp[pulangTiket_asal]\"  value=\"$r[pulangTiket_asal]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tujuan</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[pulangTiket_tujuan]\" name=\"inp[pulangTiket_tujuan]\"  value=\"$r[pulangTiket_tujuan]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
						</div>
					</tr>
					</tr>
					</table>
					<div class=\"widgetbox\">
									<div class=\"title\" style=\"width:90%; margin-top:10px; margin-bottom:0px;\"><h3>DATA PEMESANAN HOTEL</h3></div>
								</div>
					<table width=\"100%\">
						<tr>
							<td width=\"45%\" style=\"vertical-align:top;\">
								
								<p>
									<label class=\"l-input-small\">Nama Hotel</label>
									<div class=\"field\">
										<input type=\"text\" id=\"inp[namaHotel]\" name=\"inp[namaHotel]\"  value=\"$r[namaHotel]\" class=\"mediuminput\" style=\"width:300px;\" />
									</div>
								</p>
								<p>
								<label class=\"l-input-small\">Bintang</label>
								<div class=\"field\">
									".comboData("select * from mst_data where statusData='t' and kodeCategory='BH' order by urutanData","kodeData","namaData","inp[idBintang]"," ",$r[idBintang],"onchange=\"setPulang('".getPar($par,"mode,idKategori")."');\"", "310px")."
								</div>
							</p>
								<p>
									<label class=\"l-input-small\">Check In</label>
									<div class=\"field\">
										<input type=\"text\" id=\"cekinTanggal\" name=\"inp[cekinTanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[cekinTanggal])."\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"cekinJam\" name=\"inp[cekinJam]\" size=\"10\" maxlength=\"5\" value=\"$r[cekinJam]\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
									</div>
								</p>
								
								
							</td>
							<td width=\"55%\" style=\"vertical-align:top;\">
								<p>
									<label class=\"l-input-small\">Alamat</label>
									<div class=\"field\">
										<textarea id=\"inp[alamatTiket]\" name=\"inp[alamatTiket]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[alamatTiket]</textarea>
									</div>
								</p>	
								<p>
									<label class=\"l-input-small\">Check Out</label>
									<div class=\"field\">
										<input type=\"text\" id=\"cekoutTanggal\" name=\"inp[cekoutTanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[cekoutTanggal])."\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"cekoutJam\" name=\"inp[cekoutJam]\" size=\"10\" maxlength=\"5\" value=\"$r[cekoutJam]\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
									</div>
								</p>
								
							</td>
						</tr>
					</table>
				</div>
				<p>					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;				
		if(empty($par[tahun])) $par[tahun]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		if($par[mode]!="print"){
			echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
					
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">";				
			require_once "tmpl/__emp_header__.php";				
		}
		$text.="</div>
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>				
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>".comboYear("par[tahun]", $par[tahun])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idTiket")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" width=\"100\">Nomor</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Kategori</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Tipe</th>					
					<th rowspan=\"2\" style=\"min-width:100px;\">Asal</th>
					<th rowspan=\"2\" style=\"min-width:100px;\">Tujuan</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
				<tr>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
				</tr>
			</thead>
			<tbody>";
				
		$filter = "where year(t1.tanggalTiket)='$par[tahun]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorTiket) like '%".strtolower($par[filter])."%'
			or lower(t1.namaTiket) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			or lower(t2.name) like '%".strtolower($par[filter])."%'
		)";
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[20]."'");
		$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[21]."'");
		
		$sql="select t1.*, t2.name, t2.reg_no from ess_tiket t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			$persetujuanTiket = $r[persetujuanTiket] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanTiket = $r[persetujuanTiket] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanTiket;
			$persetujuanTiket = $r[persetujuanTiket] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanTiket;			
									
			$sdmTiket = $r[sdmTiket] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmTiket = $r[sdmTiket] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmTiket;
			$sdmTiket = $r[sdmTiket] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmTiket;				
												
			$text.="<tr>
					<td>$no.</td>			
					<td>$r[nomorTiket]</td>
					<td align=\"center\">".getTanggal($r[tanggalTiket])."</td>
					<td>".$arrKategori["$r[idKategori]"]."</td>
					<td>".$arrTipe["$r[idTipe]"]."</td>					
					<td>$r[berangkatTiket_asal]</td>
					<td>$r[berangkatTiket_tujuan]</td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."',750,425);\" >$persetujuanTiket</a></td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."',750,425);\" >$sdmTiket</a></td>
					<td align=\"center\">
									<a href=\"?par[mode]=det&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
								</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";		
				if(in_array($r[persetujuanTiket], array(0,2)))
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
			
				if(in_array($r[persetujuanTiket], array(0)))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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

	function detail(){
					global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;

					$sql="select * from ess_tiket where idTiket='$par[idTiket]'";
					$res=db($sql);
					$r=mysql_fetch_array($res);					

					if(empty($r[nomorTiket])) $r[nomorTiket] = gNomor();
					if(empty($r[tanggalTiket])) $r[tanggalTiket] = date('Y-m-d');

					list($berangkatTiket_tanggal, $berangkatTiket_waktu) = explode(" ", $r[berangkatTiket]);
					list($pulangTiket_tanggal, $pulangTiket_waktu) = explode(" ", $r[pulangTiket]);

					$pulangTiket = getField("select lower(trim(namaData)) from mst_data where kodeData='$r[idTipe]'") == "sekali jalan" ? "none" : "block";
					$txtPulang = $pulangTiket == "none" ? "&nbsp;" : "PULANG";

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
											<span class=\"field\">".$r[nomorTiket]."&nbsp;</span>
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
											<span class=\"field\">".getTanggal($r[tanggalTiket],"t")."&nbsp;</span>
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
								<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PEMESANAN TIKET</h3></div>
							</div>
							<table width=\"100%\">
								<tr>
									<td width=\"45%\" style=\"vertical-align:top;\">
										<p>
											<label class=\"l-input-small\">Kategori</label>
											<span class=\"field\">
												".getField("select namaData from mst_data where kodeData='$r[idKategori]'")."&nbsp;
											</span>
										</p>
										<p>
											<label class=\"l-input-small\">Tipe</label>
											<span class=\"field\">
												".getField("select namaData from mst_data where kodeData='$r[idTipe]'")."&nbsp;
											</span>
										</p>						
									</td>
									<td width=\"55%\" style=\"vertical-align:top;\">						
										<p>
											<label class=\"l-input-small\">Dokumen</label>
											<div class=\"field\">";								
												$text.=empty($r[fileTiket])?
												"":
												"<a href=\"download.php?d=tiket&f=$par[idTiket]\"><img src=\"".getIcon($fFile."/".$r[fileTiket])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a><br clear=\"all\">";
												$text.="</div>
											</p>
											<p>
												<label class=\"l-input-small\">Keterangan</label>
												<span class=\"field\">".nl2br($r[keteranganTiket])."&nbsp;</span>
											</p>						
										</td>
									</tr>
								</table>

								<table width=\"100%\">
									<tr>
										<td width=\"45%\" style=\"vertical-align:top;\">
											<div class=\"widgetbox\">
												<div class=\"title\" style=\"width:90%; margin-top:10px; margin-bottom:0px;\"><h3>BERANGKAT</h3></div>
											</div>
											<p>
												<label class=\"l-input-small\">Waktu</label>
												<span class=\"field\">".getTanggal($berangkatTiket_tanggal,"t")." @ ".substr($berangkatTiket_waktu,0,5)."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Asal</label>
												<span class=\"field\">".$r[berangkatTiket_asal]."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Tujuan</label>
												<span class=\"field\">".$r[berangkatTiket_tujuan]."&nbsp;</span>
											</p>
										</td>
										<td width=\"55%\" style=\"vertical-align:top;\">
											<div class=\"widgetbox\">
												<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3 id=\"txtPulang\">$txtPulang</h3></div>
											</div>
											<div id=\"pulangTiket\" style=\"display:".$pulangTiket."\">					
												<p>
													<label class=\"l-input-small\">Waktu</label>
													<span class=\"field\">".getTanggal($pulangTiket_tanggal,"t")." @ ".substr($pulangTiket_waktu,0,5)."&nbsp;</span>
												</p>
												<p>
													<label class=\"l-input-small\">Asal</label>
													<span class=\"field\">".$r[pulangTiket_asal]."&nbsp;</span>
												</p>
												<p>
													<label class=\"l-input-small\">Tujuan</label>
													<span class=\"field\">".$r[pulangTiket_tujuan]."&nbsp;</span>
												</p>
											</div>
										</tr>
									</tr>
								</table>";
								$persetujuanTiket = "Belum Diproses";
								$persetujuanTiket = $r[persetujuanTiket] == "t" ? "Disetujui" : $persetujuanTiket;
								$persetujuanTiket = $r[persetujuanTiket] == "f" ? "Ditolak" : $persetujuanTiket;	
								$persetujuanTiket = $r[persetujuanTiket] == "r" ? "Diperbaiki" : $persetujuanTiket;	

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
											<span class=\"field\">".$persetujuanTiket."&nbsp;</span>
										</p>
										<p>
											<label class=\"l-input-small\">Keterangan</label>
											<span class=\"field\">".nl2br($r[catatanTiket])."&nbsp;</span>
										</p>
									</td>
									<td width=\"55%\">&nbsp;</td>
								</tr>
							</table>";

							$sdmTiket = "Belum Diproses";
							$sdmTiket = $r[sdmTiket] == "t" ? "Disetujui" : $sdmTiket;
							$sdmTiket = $r[sdmTiket] == "f" ? "Ditolak" : $sdmTiket;	
							$sdmTiket = $r[sdmTiket] == "r" ? "Diperbaiki" : $sdmTiket;	

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
										<span class=\"field\">".$sdmTiket."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Keterangan</label>
										<span class=\"field\">".nl2br($r[noteTiket])."&nbsp;</span>
									</p>
								</td>
								<td width=\"55%\">&nbsp;</td>
							</tr>
						</table>";


						$text.="</div>
						<p>					
							<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\" style=\"float:right;\"/>		
						</p>
					</form>";
					return $text;
				}	
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql_="select * from pay_profile limit 1";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql="select * from ess_tiket t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.idTiket='".$par[idTiket]."'";
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
		$pdf->Row(array($r_[namaProfile]."\tb","","Tanggal\tb",":",getTanggal($r[tanggalTiket],"t")), false);
		$pdf->Row(array($r_[alamatProfile],"","Nomor\tb",":",$r[nomorTiket]), false);
		
		$pdf->Ln();
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "REIMBURS\tf",":\tf",$r[namaTiket]."\tf", "\tf"));
		$pdf->SetWidths(array(5, 20, 5, 60, 10, 30, 5, 60, 5));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("\tf", "NAMA\tf",":\tf",$r[name]."\tf","\tf","KATEGORI\tf",":\tf",getField("select namaData from mst_data where kodeData='".$r[idKategori]."'")."\tf", "\tf"));
		$pdf->Row(array("\tf", "NPP\tf",":\tf",$r[reg_no]."\tf","\tf","NILAI\tf",":\tf","Rp. ".getAngka($r[nilaiTiket])."\tf", "\tf"));
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "TERBILANG\tf",":\tf",terbilang($r[nilaiTiket])." Rupiah\tf", "\tf"));
		
		$pdf->Cell(200,1,'','T');		
		$pdf->Ln(5);
		
		
		$pdf->SetWidths(array(100, 50, 50));
		$pdf->SetAligns(array('L','C','C'));
		$pdf->Row(array("KETERANGAN\tb","PENERIMA\tb","KASIR\tb"));
		$pdf->Row(array($r[pay_remark], "\n\n(".$r[name].")", "\n\n(".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[paidBy]."'").")"));
		
		$pdf->AutoPrint(true);
		$pdf->Output();
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
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "no":
				$text = gNomor();
			break;
			case "tpe":
				$text = gTipe();
			break;
			case "get":
				$text = gPegawai();
			break;			
			case "peg":
				$text = pegawai();
			break;			
			
			case "detAts":
				$text = detailApproval();
			break;
			case "detSdm":
				$text = detailApproval();
			break;
			case "det":
						$text = detail();
						break;
			
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
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
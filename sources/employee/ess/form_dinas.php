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

	function gDinas(){
		global $db,$s,$inp,$par;

		if(!empty($par[mulaiDinas]) && !empty($par[selesaiDinas])){
		$start = new DateTime(setTanggal($par[mulaiDinas]));
		$end = new DateTime(setTanggal($par[selesaiDinas]));
		$end->modify('+1 day');
		$interval = $end->diff($start);
		$days = $interval->days;

		for ($i=0 ; $i < $days ; $i++ ) { 
			$no = new DateTime(setTanggal($par[mulaiDinas]));
			$no->modify('+'.$i.' day');
			$no->format('Y-m-d');
			$arrTglPilih[] = $no->format('Y-m-d');
		}

		$sql = "select mulaiLibur from dta_libur";
		$res = db($sql);
		while ($b = mysql_fetch_array($res)) {
			$holidays[] = $b[mulaiLibur];
		}

		foreach ($arrTglPilih as $key) {
			if(in_array($key, $holidays))
			{
				$days--;
			}
		}

		// sele
		}
		
		echo $days;
	}
	
	function gPegawai(){
		global $db,$s,$inp,$par, $arrParam;


		$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		

		$data["idPegawai"] = $r[id];
		$data["nikPegawai"] = $r[reg_no];
		$data["namaPegawai"] = strtoupper($r[name]);
		
		$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);

		$r_[nilaiTransport] = getField("select pesawat2 + ka2 + bus2 + sewa as nilaiTransport from perjadin_transport where idGolongan = '$r_[perdin]' AND idTipe = '".$arrParam[$s]."'");
		$r_[nilaiAkomodasi] = getField("select jumlah5 + jumlah4 + jumlah3 + jumlah2 + jumlah1 as nilaiAkomodasi from perjadin_akomodasi where idGolongan = '$r_[perdin]' AND idTipe = '".$arrParam[$s]."'");
		$r_[nilaiUang] = getField("select saku + harian + pelengkap as nilaiUang from perjadin_uang where idGolongan = '$r_[perdin]' AND idTipe = '".$arrParam[$s]."'");
		$r_[nilaiPulsa] = getField("select pulsa from perjadin_uang where idGolongan = '$r_[perdin]' AND idTipe = '".$arrParam[$s]."'");
		$r_[nilaiTaxi] = getField("select taxi from perjadin_transport where idGolongan = '$r_[perdin]' AND idTipe = '".$arrParam[$s]."'");
		
		$data["nilaiTransport"] = getAngka($r_[nilaiTransport]);
		$data["nilaiAkomodasi"] = getAngka($r_[nilaiAkomodasi]);
		$data["nilaiUang"] = getAngka($r_[nilaiUang]);
		$data["nilaiPulsa"] = getAngka($r_[nilaiPulsa]);
		$data["nilaiTaxi"] = getAngka($r_[nilaiTaxi]);

		$data["namaJabatan"] = $r_[pos_name];
		$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");


		
		echo json_encode($data);
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
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$inp[nilaiMakan] = setAngka($inp[nilaiMakan]);
		$inp[nilaiSaku] = setAngka($inp[nilaiSaku]);
		$inp[nilaiPelengkap] = setAngka($inp[nilaiPelengkap]);
		$inp[nilaiBerangkat] = setAngka($inp[nilaiBerangkat]);
		$inp[nilaiPulang] = setAngka($inp[nilaiPulang]);
		$inp[totalSaku] = setAngka($inp[totalSaku]);
		$inp[totalMakan] = setAngka($inp[totalMakan]);
		$inp[totalPelengkap] = setAngka($inp[totalPelengkap]);
		$inp[totalBiaya] = setAngka($inp[totalBiaya]);
		$inp[totalBerangkat] = setAngka($inp[totalBerangkat]);
		
		$fileDinas=upload($par[idDinas]);
		$inp[totalDinas] = $inp[totalBiaya] + $inp[totalBerangkat];
				
		$sql="update ess_dinas set idPegawai='$inp[idPegawai]', idKategori='$inp[idKategori]', nomorDinas='$inp[nomorDinas]', tanggalDinas='".setTanggal($inp[tanggalDinas])."', namaDinas='$inp[namaDinas]', mulaiDinas='".setTanggal($inp[mulaiDinas])."', selesaiDinas='".setTanggal($inp[selesaiDinas])."', nilaiDinas='".setAngka($inp[totalDinas])."', keteranganDinas='$inp[keteranganDinas]', nilaiMakan='$inp[nilaiMakan]', nilaiSaku='$inp[nilaiSaku]', nilaiPelengkap='$inp[nilaiPelengkap]', nilaiBerangkat='$inp[nilaiBerangkat]', nilaiPulang='$inp[nilaiPulang]', fileDinas='$fileDinas', keberangkatanDinas = '$inp[keberangkatanDinas]', kotaTujuanDinas = '$inp[kotaTujuanDinas]', kendaraanDinas = '$inp[kendaraanDinas]', tujuanDinas = '$inp[tujuanDinas]', untukDinas = '$inp[untukDinas]', berangkatDinas = '$inp[berangkatDinas]', pulangDinas = '$inp[pulangDinas]', hariBerangkat = '$inp[hariBerangkat]', hariPulang = '$inp[hariPulang]', hariDinas = '$inp[hariDinas]', totalSaku = '$inp[totalSaku]', totalMakan = '$inp[totalMakan]', totalPelengkap = '$inp[totalPelengkap]', totalBiaya = '$inp[totalBiaya]', totalBerangkat = '$inp[totalBerangkat]',updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDinas='$par[idDinas]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParam;	
		repField();				
		$idDinas = getField("select idDinas from ess_dinas order by idDinas desc limit 1")+1;		
		$fileDinas=upload($idDinas);
		
		$inp[nilaiMakan] = setAngka($inp[nilaiMakan]);
		$inp[nilaiSaku] = setAngka($inp[nilaiSaku]);
		$inp[nilaiPelengkap] = setAngka($inp[nilaiPelengkap]);
		$inp[nilaiBerangkat] = setAngka($inp[nilaiBerangkat]);
		$inp[nilaiPulang] = setAngka($inp[nilaiPulang]);
		$inp[totalSaku] = setAngka($inp[totalSaku]);
		$inp[totalMakan] = setAngka($inp[totalMakan]);
		$inp[totalPelengkap] = setAngka($inp[totalPelengkap]);
		$inp[totalBiaya] = setAngka($inp[totalBiaya]);
		$inp[totalBerangkat] = setAngka($inp[totalBerangkat]);
		$inp[totalDinas] = $inp[totalBiaya] + $inp[totalBerangkat];
		

		if($arrParam[$s] != 5){
			// $inp[nilaiDinas] = setAngka($inp[totalDinas]);
		$sql="insert into ess_dinas (idDinas, idPegawai, idKategori, tipeMenu, nomorDinas, tanggalDinas, namaDinas, mulaiDinas, selesaiDinas, nilaiDinas, keteranganDinas, fileDinas, persetujuanDinas, pembayaranDinas, nilaiMakan, nilaiSaku, nilaiPelengkap, nilaiBerangkat, nilaiPulang, keberangkatanDinas, kotaTujuanDinas,  kendaraanDinas, tujuanDinas, untukDinas, berangkatDinas, pulangDinas, hariBerangkat, hariPulang, hariDinas, totalSaku, totalMakan, totalPelengkap, totalBiaya, totalBerangkat, createBy, createTime) values ('$idDinas', '$inp[idPegawai]', '$inp[idKategori]','".$arrParam[$s]."', '$inp[nomorDinas]', '".setTanggal($inp[tanggalDinas])."', '$inp[namaDinas]', '".setTanggal($inp[mulaiDinas])."', '".setTanggal($inp[selesaiDinas])."', '".setAngka($inp[totalDinas])."', '$inp[keteranganDinas]', '$fileDinas', 'p', 'p','$inp[nilaiMakan]', '$inp[nilaiSaku]', '$inp[nilaiPelengkap]', '$inp[nilaiBerangkat]', '$inp[nilaiPulang]', '$inp[keberangkatanDinas]', '$inp[kotaTujuanDinas]', '$inp[kendaraanDinas]', '$inp[tujuanDinas]', '$inp[untukDinas]', '$inp[berangkatDinas]', '$inp[pulangDinas]', '$inp[hariBerangkat]', '$inp[hariPulang]', '$inp[hariDinas]', '$inp[totalSaku]', '$inp[totalMakan]', '$inp[totalPelengkap]', '$inp[totalBiaya]', '$inp[totalBerangkat]','$cUsername', '".date('Y-m-d H:i:s')."')";
		

		db($sql);
		}else{
		$sql="insert into ess_dinas (idDinas, idPegawai, idKategori, tipeMenu, nomorDinas, tanggalDinas, namaDinas, mulaiDinas, selesaiDinas, nilaiDinas, keteranganDinas, fileDinas, persetujuanDinas, pembayaranDinas, nilaiMakan, nilaiSaku, nilaiPelengkap, nilaiBerangkat, nilaiPulang, keberangkatanDinas, kotaTujuanDinas,  kendaraanDinas, tujuanDinas, untukDinas, createBy, createTime) values ('$idDinas', '$inp[namaPegawai]', '$inp[idKategori]','".$arrParam[$s]."', '$inp[nomorDinas]', '".setTanggal($inp[tanggalDinas])."', '$inp[namaDinas]', '".setTanggal($inp[mulaiDinas])."', '".setTanggal($inp[selesaiDinas])."', '".setAngka($inp[nilaiDinas])."', '$inp[keteranganDinas]', '$fileDinas', 'p', 'p','$inp[nilaiMakan]', '$inp[nilaiSaku]', '$inp[nilaiPelengkap]', '$inp[nilaiBerangkat]', '$inp[nilaiPulang]', '$inp[keberangkatanDinas]', '$inp[kotaTujuanDinas]', '$inp[kendaraanDinas]', '$inp[tujuanDinas]', '$inp[untukDinas]','$cUsername', '".date('Y-m-d H:i:s')."')";
		

		db($sql);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idDinas")."';</script>";
	}
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $arrParam;
		
		$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		

		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;		

		$false = $r[kendaraanDinas] == "f" ? "checked=\"checked\"" : "";
		$true = empty($false) ? "checked=\"checked\"" : "";	
		
		if(empty($r[nomorDinas])) $r[nomorDinas] = gNomor();
		if(empty($r[tanggalDinas])) $r[tanggalDinas] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorDinas]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalDinas","anda harus mengisi tanggal");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");		
		setValidation("is_null","inp[namaDinas]","anda harus mengisi judul");
		setValidation("is_null","mulaiDinas","anda harus mengisi pelaksanaan");
		setValidation("is_null","selesaiDinas","anda harus mengisi pelaksanaan");
		setValidation("is_null","inp[nilaiDinas]","anda harus mengisi nilai");		
		$text = getValidation();								
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		// echo $sql__;
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
				<fieldset>
					<legend>DATA PEGAWAI</legend>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" readonly id=\"inp[nomorDinas]\" name=\"inp[nomorDinas]\"  value=\"$r[nomorDinas]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							</div>
						</p>";
						if($arrParam[$s]!= 5){
				$text.="<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\"/>
								
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>";
					}else{
					$text.="

						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai2]\" name=\"inp[namaPegawai]\"  value=\"$r[idPegawai]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>";
						}$text.="</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalDinas\" name=\"inp[tanggalDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalDinas])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>";
						if($arrParam[$s]!= 5){
				$text.="
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
						</p>";
					}else{
					$text.="
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
						";
					}$text.="
					</td>
					</tr>
					</table>
					</fieldset>
					<br clear=\"all\"/>
					<fieldset>
					<legend>DATA PERJALANAN DINAS</legend>
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
								<input type=\"text\" id=\"mulaiDinas\" name=\"inp[mulaiDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiDinas])."\" onchange=\"getJumlah('".getPar($par,"mode, id")."');\" class=\"vsmallinput hasDatePicker\"/> s.d <input type=\"text\" id=\"selesaiDinas\" name=\"inp[selesaiDinas]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiDinas])."\" onchange=\"getJumlah('".getPar($par,"mode, id")."');\" class=\"vsmallinput hasDatePicker\"/> 
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
					";
					if($arrParam[$s] == 5){
						$text.="
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiDinas]\" name=\"inp[nilaiDinas]\"  value=\"".getAngka($r[nilaiDinas])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>";
						}$text.="
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
					</table>
					</fieldset>
					<br clear=\"all\"/>
					<fieldset>
					<legend>DATA TRANSPORTASI</legend>
					<table width=\"100%\">
					<tr>
					<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small2\">Kota Keberangkatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[keberangkatanDinas]\" name=\"inp[keberangkatanDinas]\"  value=\"$r[keberangkatanDinas]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Kota Tujuan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[kotaTujuanDinas]\" name=\"inp[kotaTujuanDinas]\"  value=\"$r[kotaTujuanDinas]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Kendaraan</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"inp[kendaraanDinas]\" onclick=\"cekKendaraan(this.value);\" name=\"inp[kendaraanDinas]\" value=\"t\" $true /> <span class=\"sradio\">Mobil Dinas</span>
								<input type=\"radio\" id=\"inp[kendaraanDinas2]\" onclick=\"cekKendaraan(this.value);\" name=\"inp[kendaraanDinas]\" value=\"f\" $false /> <span class=\"sradio\">Kendaraan Umum</span>

							</div>
						</p>	
						</td>
						<td width=\"50%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Tujuan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[tujuanDinas]\" name=\"inp[tujuanDinas]\"  value=\"$r[tujuanDinas]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Untuk Tujuan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[untukDinas]\" name=\"inp[untukDinas]\"  value=\"$r[untukDinas]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>	
						</td>
						</tr>
					</table>
					</fieldset>
					<br clear=\"\">
					";
					if($arrParam[$s] != 5){
						$text.="
					<fieldset>
					<legend>DATA BIAYA</legend>
					
					";

					$sql___ = "select * from perjadin_uang where idGolongan = '$r__[perdin]' AND idTipe= '".$arrParam[$s]."'";
					// echo $sql___;
					$res___ = db($sql___);
					$r___ = mysql_fetch_array($res___); 

					$r[nilaiSaku] = $r___[saku];
					$r[nilaiMakan] = $r___[harian];
					$r[nilaiPelengkap] = $r___[pelengkap];
					
					$text.="
						<p>
							<label class=\"l-input-small\">Uang Saku</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiSaku]\" name=\"inp[nilaiSaku]\"  value=\"".getAngka($r[nilaiSaku])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly /> &nbsp;&nbsp;x&nbsp;&nbsp;
								<input type=\"text\" id=\"hariSaku\" name=\"inp[hariDinas]\" class=\"mediuminput\" style=\"text-align:right; width:20px;\" value = \"$r[hariDinas]\" readonly />&nbsp; Hari = &nbsp; 
								<input type=\"text\" id=\"jumlahSaku\" name=\"inp[totalSaku]\" value=\"".getAngka($r[totalSaku])."\" class=\"mediuminput\" style=\"text-align:right; width:80px;\" readonly />
							</div>
						</p>
						
						<p>
							<label class=\"l-input-small\">Uang Makan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiMakan]\" name=\"inp[nilaiMakan]\"  value=\"".getAngka($r[nilaiMakan])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly /> &nbsp;&nbsp;x&nbsp;&nbsp;
								<input type=\"text\" id=\"hariMakan\" class=\"mediuminput\" style=\"text-align:right; width:20px;\" value = \"$r[hariDinas]\" readonly />&nbsp; Hari = &nbsp; 
								<input type=\"text\" id=\"jumlahMakan\" name=\"inp[totalMakan]\" value=\"".getAngka($r[totalMakan])."\" class=\"mediuminput\" style=\"text-align:right; width:80px;\" readonly />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Pelengkap</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiPelengkap]\" name=\"inp[nilaiPelengkap]\"  value=\"".getAngka($r[nilaiPelengkap])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly /> &nbsp;&nbsp;x&nbsp;&nbsp;
								<input type=\"text\" id=\"hariPelengkap\" class=\"mediuminput\" style=\"text-align:right; width:20px;\" value = \"$r[hariDinas]\" readonly />&nbsp; Hari = &nbsp; 
								<input type=\"text\" id=\"jumlahPelengkap\" name=\"inp[totalPelengkap]\" value=\"".getAngka($r[totalPelengkap])."\" class=\"mediuminput\" style=\"text-align:right; width:80px;\" readonly /> 
							</div>
						</p>
						
						<p>
						<label class=\"l-input-small\">Sub Total</label>
							<div class=\"field\">								
								<span>&nbsp;</span><input type=\"text\" id=\"inp[total]\" name=\"inp[totalBiaya]\"  value=\"".getAngka($r[totalBiaya])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly />
							</div>
						</p>
					
					</fieldset>
					<br clear=\"all\"/>";
					$style = $r[kendaraanDinas] == "f" ? " style=\"display:block;\" " :  "style=\"display:none;\"";
					$text.="
					<fieldset $style id=\"fieldsetKendaraan\">
					<legend>TRANSPORT DARI DAN KE TEMPAT PEMBERANGKATAN</legend>";
					$r[biayaTaxi] = getField("select taxi from perjadin_transport where idGolongan = '$r__[perdin]' AND idTipe = '".$arrParam[$s]."'");
					$r[totalAll] = $r[totalBerangkat] + $r[totalBiaya];
					$text.="
					<p>
					
						<input type=\"text\" id=\"inp[berangkatDinas]\" name=\"inp[berangkatDinas]\"  value=\"$r[berangkatDinas]\" class=\"mediuminput\" style=\"width:200px;\" />				
						<input type=\"text\" value=\"$r[biayaTaxi]\" id=\"jumlahBerangkat\" class=\"mediuminput\" style=\"text-align:right; width:80px;\" readonly />	&nbsp;&nbsp;x&nbsp;&nbsp;			
						<input type=\"text\" id=\"inp[hariBerangkat]\" name=\"inp[hariBerangkat]\" class=\"mediuminput\" style=\"text-align:right; width:20px;\" value=\"$r[hariBerangkat]\" onkeyup=\"totalBerangkat();\" maxlength=\"2\" />&nbsp; Kali = &nbsp; 
							<input type=\"text\" id=\"inp[nilaiBerangkat]\" name=\"inp[nilaiBerangkat]\" onchange=\"totalTaxi();\" value=\"".getAngka($r[nilaiBerangkat])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly /> 
						
					</p>
					<p>
					
						<input type=\"text\" id=\"inp[pulangDinas]\" name=\"inp[pulangDinas]\"  value=\"$r[pulangDinas]\" class=\"mediuminput\" style=\"width:200px;\" />				
						<input type=\"text\" value=\"$r[biayaTaxi]\" id=\"jumlahPulang\" class=\"mediuminput\" style=\"text-align:right; width:80px;\" readonly />	&nbsp;&nbsp;x&nbsp;&nbsp;			
						<input type=\"text\" id=\"inp[hariPulang]\" name=\"inp[hariPulang]\" class=\"mediuminput\" style=\"text-align:right; width:20px;\" value=\"$r[hariPulang]\"  maxlength=\"2\" onkeyup=\"totalBerangkat();\" />&nbsp; Kali = &nbsp; 
							<input type=\"text\" id=\"inp[nilaiPulang]\" name=\"inp[nilaiPulang]\" onchange=\"totalTaxi();\" value=\"".getAngka($r[nilaiPulang])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly /> 
						
					</p>
					<p>
						<label class=\"l-input-small\">Sub Total</label>
							<div class=\"field\">								
								<span>&nbsp;</span><input type=\"text\" id=\"inp[totalTaxi]\" name=\"inp[totalBerangkat]\"  value=\"".getAngka($r[totalBerangkat])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly />
							</div>
						</p>
						<p>
						<label class=\"l-input-small\">Total</label>
							<div class=\"field\">								
								<span>&nbsp;</span><input type=\"text\" id=\"inp[totalAll]\" name=\"inp[totalAll]\"  value=\"".getAngka($r[totalAll])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly />
							</div>
						</p>
					</fieldset>
					<br clear=\"all\"/>
					
							";
					}
					$text.="			
				</div>
				<p>					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}

	function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck,$arrParam;
	$par[idPegawai] = $cID;
	if(empty($par[tahun])) $par[tahun]=date('Y');		
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>";
	// require_once "tmpl/__emp_header__.php";		
	$text.="<div id=\"contentwrapper\" class=\"contentwrapper\">			
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
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idDinas")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
				$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
				<thead>
					<tr>
						<th rowspan=\"2\" width=\"20\">No.</th>
						<th rowspan=\"2\" style=\"min-width:100px;\">Nama</th>
						<th rowspan=\"2\" width=\"100\">NPP</th>
						<th rowspan=\"2\" width=\"100\">Nomor</th>
						<th rowspan=\"2\" width=\"75\">Tanggal</th>
						<th rowspan=\"2\" style=\"min-width:100px;\">Kategori</th>					
						<th rowspan=\"2\" width=\"100\">Nilai</th>
						<th colspan=\"2\" width=\"50\">Approval</th>
						<th rowspan=\"2\" width=\"50\">Bayar</th>
						<th rowspan=\"2\" width=\"30\">Bukti</th>
						<th rowspan=\"2\" width=\"50\">Detail</th>
						<th rowspan=\"2\" width=\"50\">Kontrol</th>";
						$text.="</tr>
						<tr>
							<th width=\"50\">Atasan</th>
							<th width=\"50\">MANAGER</th>
							
						</tr>
					</thead>
					<tbody>";

		$filter = "where year(t1.tanggalDinas)='$par[tahun]' AND tipeMenu = '".$arrParam[$s]."' AND t1.idPegawai = '$cID'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'
			or lower(t1.namaDinas) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
			or lower(t2.name) like '%".strtolower($par[filter])."%'		
		)";
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[19]."'");
		
		$sql="select t1.*, t2.name, t2.reg_no from ess_dinas t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorDinas";
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
							$view = empty($r[fileDinas]) ? "" : "<a href=\"#\" onclick=\"openBox('view.php?doc=fileDinas&id=$r[idDinas]',1000,500)\"><img src=\"".getIcon($r[fileDinas])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";

							$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas") : "#";			
							$pembayaranLink = (isset($menuAccess[$s]["apprlv3"]) && $r[persetujuanDinas] == "t" && $r[sdmDinas] == "t") ? "?par[mode]=byr&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas") : "#";

							$text.="<tr>
							<td>$no.</td>";
							if($arrParam[$s] != 5){
							$text.="
							<td>".strtoupper($r[name])."</td>";
							}else{
							$text.="
							<td>".strtoupper($r[idPegawai])."</td>";	
							}
							$text.="
							<td>$r[reg_no]</td>
							<td>$r[nomorDinas]</td>
							<td align=\"center\">".getTanggal($r[tanggalDinas])."</td>
							<td>".$arrKategori["$r[idKategori]"]."</td>
							<td align=\"right\">".getAngka($r[nilaiDinas])."</td>
							<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanDinas</a></td>
							<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."',750,425);\" >$sdmDinas</a></td>
							<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detPay&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."',750,425);\" >$pembayaranDinas</a></td>
							<td align=\"center\">$view</td>
							
								<td align=\"center\">
									<a href=\"?par[mode]=det&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
								</td>";
								if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
									$text.="<td align=\"center\"><a href=\"?par[mode]=xls&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" title=\"Print Data\" class=\"print\" target=\"print\"><span>Print</span></a>";		
									if(in_array($r[persetujuanDinas], array(0,2)))
										if(isset($menuAccess[$s]["edit"])&&$r[persetujuanDinas]!='t') $text.="<a href=\"?par[mode]=edit&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				

									if(in_array($r[persetujuanDinas], array(0)))
										if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idDinas]=$r[idDinas]".getPar($par,"mode,idDinas")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
									$text.="</td>";
								}
								$text.="</tr>";				
							}	

							$text.="</tbody>
						</table>
					</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";

					if($par[mode] == "print") pdf();
					if($par[mode] == "print2") pdf2();
if($par[mode] == "xls"){			
						xls();			
						$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
					}	
					return $text;
				}		

	function pdf2(){
					global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
					require_once 'plugins/PHPPdf.php';

					$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
					$res=db($sql);
					$r=mysql_fetch_array($res);	
					$arrMaster = arrayQuery("select kodeData, namaData from mst_data");				

					$tanggalDinas = getTanggal($r[mulaiDinas], "t");
					if($r[mulaiDinas] != $r[selesaiDinas]) $tanggalDinas.= " - ".getTanggal($r[selesaiDinas], "t");

					$pdf = new PDF('P','mm','A4');
					$pdf->AddPage();
					$pdf->SetLeftMargin(15);

					$pdf->Ln();		
					$pdf->SetFont('Arial','B',12);					
					$pdf->Cell(20,6,'PRATAMA MITRA SEJATI',0,0,'L');
					$pdf->Ln();		

					$pdf->SetFont('Arial','BU',14);
					$pdf->Cell(180,6,'SURAT PERJALANAN DINAS',0,0,'C');
					$pdf->Ln();

					$pdf->SetFont('Arial','BI',10);

					$pdf->SetFont('Arial','B');
					$pdf->Cell(180,6,'Nomor : '.$r[nomorDinas],0,0,'C');
					$pdf->Ln(20);

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Nama Karyawan',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Employees Name',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');
					$pdf->Cell(5,6,':',0,0,'L');		
					$pdf->MultiCell(125,6,getField("select name from emp where id='".$r[idPegawai]."'"),0,'L');
					$pdf->Ln(3.5);

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Jabatan',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Position',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');
					$pdf->Cell(5,6,':',0,0,'L');		
					$pdf->MultiCell(125,6,getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
					$pdf->Ln(3.5);

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Kategori',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Category',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');
					$pdf->Cell(5,6,':',0,0,'L');		
					$pdf->MultiCell(125,6,$arrMaster[$r[idKategori]],0,'L');
					$pdf->Ln(3.5);

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Tanggal Pelaksanaan',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Implementation Date',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');		
					$pdf->Cell(5,6,':',0,0,'L');
					$pdf->MultiCell(125,6,$tanggalDinas,0,'L');				
					$pdf->Ln(3.5);

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Biaya',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Cost',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');
					$pdf->Cell(5,6,':',0,0,'L');		
					$pdf->MultiCell(125,6,"Rp. ".getAngka($r[nilaiDinas]),0,'L');
					$pdf->Ln(3.5);			
					

					$setX = $pdf->GetX();
					$setY = $pdf->GetY();
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(50,3,'Keterangan',0,0,'L');		
					$pdf->SetXY($setX, $setY+2);
					$pdf->SetFont('Arial','I');
					$pdf->Cell(50,6,'Information',0,0,'L');		
					$pdf->SetXY($setX+50, $setY);
					$pdf->SetFont('Arial');
					$pdf->Cell(5,6,':',0,0,'L');		
					$pdf->MultiCell(125,6,$r[keteranganDinas],0,'L');
					$pdf->Ln(20);

					$pdf->SetFont('Arial','B');
					$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
					$pdf->Cell(60,3,'Menyetujui,',0,0,'C');
					$pdf->Cell(60,3,'Pemohon,',0,0,'C');
					$pdf->Ln();
					$pdf->SetFont('Arial','I');
					$pdf->Cell(60,6,'Approved by,',0,0,'C');
					$pdf->Cell(60,6,'Approved by,',0,0,'C');
					$pdf->Cell(60,6,'Leave applicant,',0,0,'C');
					$pdf->Ln(20);		
					$pdf->SetFont('Arial','BU');
					$pdf->Cell(60,5,'Maman Somantri',0,0,'C');
					$pdf->Cell(60,5,'                                ',0,0,'C');
					$pdf->Cell(60,5,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'C');
					$pdf->Ln();
					$pdf->SetFont('Arial');
					$pdf->Cell(60,3,'Human Resource Manager',0,0,'C');
					$pdf->Cell(60,3,'Head of Department',0,0,'C');
					$pdf->Cell(60,3,'',0,0,'C');

					$pdf->Ln();
					$pdf->Cell(60,6,'Tgl/Date :                            ',0,0,'C');
					$pdf->Cell(60,6,'Tgl/Date :                 ',0,0,'C');
					$pdf->Cell(60,6,'Tgl/Date :  '.getTanggal($r[tanggalDinas],"t"),0,0,'C');
					$pdf->AutoPrint(true);

					$pdf->Output();	
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
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;		
		$text.="<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Daftar Pegawai</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			
				Search : 
				".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" />
				
					<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
				
			</div>
			<div id=\"\" style=\"float:right;\">
			".comboData("select t1.kodeData id, concat(t2.namaData, ' - ', t1.namaData) description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       where t2.kodeCategory='X04' order by t1.urutanData","id","description","par[divisi]"," ",$par[divisi],"onchange=\"document.getElementById('form').submit();\"", "500px","chosen-select")."
			</div>
			</form>
			<br clear=\"all\" />
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

		if(!empty($par[divisi]))
			$filter.=" and t2.div_id = '$par[divisi]'";		
		
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
function xls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
	require_once 'plugins/PHPExcel.php';

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

	$sql = "select * from ess_dinas t1 join dta_pegawai t2 on t1.idPegawai = t2.id where t1.idDinas = '$par[idDinas]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(1);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

	/* SET MERGE */

	$objPHPExcel->getActiveSheet()->mergeCells('B2:G2');
	$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');
	$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
	$objPHPExcel->getActiveSheet()->mergeCells('B8:C8');
	$objPHPExcel->getActiveSheet()->mergeCells('D12:E12');
	$objPHPExcel->getActiveSheet()->mergeCells('D14:E14');
	$objPHPExcel->getActiveSheet()->mergeCells('B17:C17');
	$objPHPExcel->getActiveSheet()->mergeCells('E17:G17');
	$objPHPExcel->getActiveSheet()->mergeCells('B19:C19');
	$objPHPExcel->getActiveSheet()->mergeCells('E19:G19');
	$objPHPExcel->getActiveSheet()->mergeCells('B21:C21');
	$objPHPExcel->getActiveSheet()->mergeCells('E21:G21');
	$objPHPExcel->getActiveSheet()->mergeCells('B25:C25');
	$objPHPExcel->getActiveSheet()->mergeCells('B26:C26');
	$objPHPExcel->getActiveSheet()->mergeCells('B27:C27');
	$objPHPExcel->getActiveSheet()->mergeCells('B28:C28');

	$objPHPExcel->getActiveSheet()->mergeCells('F25:G25');
	$objPHPExcel->getActiveSheet()->mergeCells('F26:G26');
	$objPHPExcel->getActiveSheet()->mergeCells('F27:G27');
	$objPHPExcel->getActiveSheet()->mergeCells('F28:G28');

	$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('B2', 'SURAT  PERINTAH   PERJALANAN  DINAS');
	$objPHPExcel->getActiveSheet()->setCellValue('B3', 'LETTER  OF  ASSIGNMENT');
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Nomor    : '.$r[nomorDinas]);

	$objPHPExcel->getActiveSheet()->setCellValue('B8', 'PT. PRATAMA MITRA SEJATI');
	$objPHPExcel->getActiveSheet()->setCellValue('B9', 'menugaskan kepada :');

	$objPHPExcel->getActiveSheet()->setCellValue('B12', 'No');
	$objPHPExcel->getActiveSheet()->setCellValue('C12', 'Nama');
	$objPHPExcel->getActiveSheet()->setCellValue('D12', 'Jabatan');
	$objPHPExcel->getActiveSheet()->setCellValue('F12', 'Direktorat');
	$objPHPExcel->getActiveSheet()->setCellValue('G12', 'Periode');

	$objPHPExcel->getActiveSheet()->setCellValue('B14', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C14', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('D14', $r[pos_name]);
	$objPHPExcel->getActiveSheet()->setCellValue('F14', $arrMaster[$r[dir_id]]);
	$objPHPExcel->getActiveSheet()->setCellValue('G14', getTanggal($r[mulaiDinas],"t").' s/d '.getTanggal($r[selesaiDinas],"t"));

	$objPHPExcel->getActiveSheet()->setCellValue('B17', 'Tujuan');
	$objPHPExcel->getActiveSheet()->setCellValue('D17', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E17', $r[kotaTujuanDinas]);

	$objPHPExcel->getActiveSheet()->setCellValue('B19', 'Untuk Tujuan');
	$objPHPExcel->getActiveSheet()->setCellValue('D19', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E19', $r[untukDinas]);

	$r[kendaraanDinas] = $r[kendaraanDinas] == "t" ? "Mobil Dinas" : "Kendaraan Umum";

	$objPHPExcel->getActiveSheet()->setCellValue('B21', 'Dengan Menggunakan');
	$objPHPExcel->getActiveSheet()->setCellValue('D21', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E21', $r[kendaraanDinas]);

	$objPHPExcel->getActiveSheet()->setCellValue('B25', 'Demikian Surat Perintah Perjalanan Dinas');
	$objPHPExcel->getActiveSheet()->setCellValue('B26', 'ini dibuat, agar dilaksanakan sebagaimana');
	$objPHPExcel->getActiveSheet()->setCellValue('B27', 'mestinya dan kepada yang berkepentingan');
	$objPHPExcel->getActiveSheet()->setCellValue('B28', 'dimohon bantuannya bilamana perlu.');

	$objPHPExcel->getActiveSheet()->setCellValue('F25', 'This Letter of Assignment is issued in order');
	$objPHPExcel->getActiveSheet()->setCellValue('F26', 'to be implemented as should be done and');
	$objPHPExcel->getActiveSheet()->setCellValue('F27', 'to whomsoever concerned  assistance when');
	$objPHPExcel->getActiveSheet()->setCellValue('F28', 'required is apreciated.');

	$tanggal = date('Y-m-d');

	$objPHPExcel->getActiveSheet()->setCellValue('F30', 'Jakarta, '.getTanggal($tanggal,'t'));
	$objPHPExcel->getActiveSheet()->getStyle('F31')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('F31', 'PT PRATAMA MITRA SEJATI');

	$objPHPExcel->getActiveSheet()->getStyle('F35')->getFont()->setUnderline(true);
	$objPHPExcel->getActiveSheet()->getStyle('F35')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->setCellValue('F35', 'Ronny Suhendi');
	$objPHPExcel->getActiveSheet()->setCellValue('F36', 'Direktur Operasi & SDM');

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

	$objPHPExcel->createSheet();

	$objPHPExcel->setActiveSheetIndex(1);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(1);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(2);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(2);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);

	/* SET MERGE */

	$objPHPExcel->getActiveSheet()->mergeCells('B2:K2');
	$objPHPExcel->getActiveSheet()->mergeCells('B3:K3');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:K4');
	$objPHPExcel->getActiveSheet()->mergeCells('B5:G5');
	$objPHPExcel->getActiveSheet()->mergeCells('B8:C8');
	$objPHPExcel->getActiveSheet()->mergeCells('D12:E12');
	$objPHPExcel->getActiveSheet()->mergeCells('D14:E14');
	$objPHPExcel->getActiveSheet()->mergeCells('B17:C17');
	$objPHPExcel->getActiveSheet()->mergeCells('E17:G17');
	$objPHPExcel->getActiveSheet()->mergeCells('B19:C19');
	$objPHPExcel->getActiveSheet()->mergeCells('E19:G19');
	$objPHPExcel->getActiveSheet()->mergeCells('B21:C21');
	$objPHPExcel->getActiveSheet()->mergeCells('E21:G21');
	$objPHPExcel->getActiveSheet()->mergeCells('B25:C25');
	$objPHPExcel->getActiveSheet()->mergeCells('B26:C26');
	$objPHPExcel->getActiveSheet()->mergeCells('B27:C27');
	$objPHPExcel->getActiveSheet()->mergeCells('B28:C28');

	$objPHPExcel->getActiveSheet()->mergeCells('F25:G25');
	$objPHPExcel->getActiveSheet()->mergeCells('F26:G26');
	$objPHPExcel->getActiveSheet()->mergeCells('F27:G27');
	$objPHPExcel->getActiveSheet()->mergeCells('F28:G28');

	$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('B2', 'LAMPIRAN');
	$objPHPExcel->getActiveSheet()->setCellValue('B3', 'SURAT PERINTAH PERJALANAN DINAS');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Nomor    : '.$r[nomorDinas]);

	$objPHPExcel->getActiveSheet()->setCellValue('B6', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('C6', 'Nama');
	$objPHPExcel->getActiveSheet()->setCellValue('D6', 'Jabatan');
	$objPHPExcel->getActiveSheet()->setCellValue('H6', 'Tujuan');
	$objPHPExcel->getActiveSheet()->setCellValue('J6', 'Periode Tanggal');
	$objPHPExcel->getActiveSheet()->setCellValue('K6', 'Rekening');

	$objPHPExcel->getActiveSheet()->setCellValue('B7', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C7', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('D7', $r[pos_name]);
	$objPHPExcel->getActiveSheet()->setCellValue('H7', $r[kotaTujuanDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('J7', getTanggal($r[mulaiDinas],"t").' s/d '.getTanggal($r[selesaiDinas],"t"));
	$objPHPExcel->getActiveSheet()->setCellValue('K7', getField("select account_no from emp_bank where parent_id = '$r[idPegawai]'"));

	$objPHPExcel->getActiveSheet()->setCellValue('B9', 'I. Uang Saku');
	$objPHPExcel->getActiveSheet()->setCellValue('B10', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C10', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('D10', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E10', $r[hariDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('F10', 'x');
	$objPHPExcel->getActiveSheet()->setCellValue('G10', getAngka($r[nilaiSaku]));
	$objPHPExcel->getActiveSheet()->setCellValue('H10', '=');
	$objPHPExcel->getActiveSheet()->setCellValue('I10', getAngka($r[totalSaku]));

	$objPHPExcel->getActiveSheet()->setCellValue('B11', 'II. Uang Makan');
	$objPHPExcel->getActiveSheet()->setCellValue('B12', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C12', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('D12', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E12', $r[hariDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('F12', 'x');
	$objPHPExcel->getActiveSheet()->setCellValue('G12', getAngka($r[nilaiMakan]));
	$objPHPExcel->getActiveSheet()->setCellValue('H12', '=');
	$objPHPExcel->getActiveSheet()->setCellValue('I12', getAngka($r[totalMakan]));

	$objPHPExcel->getActiveSheet()->setCellValue('B13', 'III. Pelengkap');
	$objPHPExcel->getActiveSheet()->setCellValue('B14', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C14', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('D14', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E14', $r[hariDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('F14', 'x');
	$objPHPExcel->getActiveSheet()->setCellValue('G14', getAngka($r[nilaiMakan]));
	$objPHPExcel->getActiveSheet()->setCellValue('H14', '=');
	$objPHPExcel->getActiveSheet()->setCellValue('I14', getAngka($r[totalMakan]));

	$objPHPExcel->getActiveSheet()->setCellValue('H15', 'Sub Total');
	$objPHPExcel->getActiveSheet()->setCellValue('J15', getAngka($r[totalBiaya]));

	$jumlahBerangkat = $r[nilaiBerangkat] / $r[hariBerangkat];
	$jumlahPulang = $r[nilaiPulang] / $r[hariPulang];
	// $r[totalAll] = $r[totalBerangkat] + $r[totalBiaya];

	$objPHPExcel->getActiveSheet()->setCellValue('B16', 'IV. Transport dari dan ke tempat pemberangkatan');
	$objPHPExcel->getActiveSheet()->setCellValue('B17', '1');
	$objPHPExcel->getActiveSheet()->setCellValue('C17', $r[name]);
	$objPHPExcel->getActiveSheet()->setCellValue('C18', $r[berangkatDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('D18', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E18', $r[hariBerangkat]);
	$objPHPExcel->getActiveSheet()->setCellValue('F18', 'x');
	$objPHPExcel->getActiveSheet()->setCellValue('G18', getAngka($jumlahBerangkat));
	$objPHPExcel->getActiveSheet()->setCellValue('H18', '=');
	$objPHPExcel->getActiveSheet()->setCellValue('I18', getAngka($r[nilaiBerangkat]));
	$objPHPExcel->getActiveSheet()->setCellValue('C18', $r[pulangDinas]);
	$objPHPExcel->getActiveSheet()->setCellValue('D18', ':');
	$objPHPExcel->getActiveSheet()->setCellValue('E18', $r[hariPulang]);
	$objPHPExcel->getActiveSheet()->setCellValue('F18', 'x');
	$objPHPExcel->getActiveSheet()->setCellValue('G18', getAngka($jumlahPulang));
	$objPHPExcel->getActiveSheet()->setCellValue('H18', '=');
	$objPHPExcel->getActiveSheet()->setCellValue('I18', getAngka($r[nilaiPulang]));

	$objPHPExcel->getActiveSheet()->setCellValue('H20', 'Sub Total');
	$objPHPExcel->getActiveSheet()->setCellValue('J20', getAngka($r[totalBerangkat]));

	$objPHPExcel->getActiveSheet()->setCellValue('J21', 'Total');
	$objPHPExcel->getActiveSheet()->setCellValue('K21', getAngka($r[nilaiDinas]));



	// $objPHPExcel->getActiveSheet()->setCellValue('B19', 'Untuk Tujuan');
	// $objPHPExcel->getActiveSheet()->setCellValue('D19', ':');
	// $objPHPExcel->getActiveSheet()->setCellValue('E19', 'Persiapan Renovasi Kantor PT. ATPI Cabang Bali');

	// $objPHPExcel->getActiveSheet()->setCellValue('B21', 'Dengan Menggunakan');
	// $objPHPExcel->getActiveSheet()->setCellValue('D21', ':');
	// $objPHPExcel->getActiveSheet()->setCellValue('E21', 'Pesawat Udara');

	// $objPHPExcel->getActiveSheet()->setCellValue('B25', 'Demikian Surat Perintah Perjalanan Dinas');
	// $objPHPExcel->getActiveSheet()->setCellValue('B26', 'ini dibuat, agar dilaksanakan sebagaimana');
	// $objPHPExcel->getActiveSheet()->setCellValue('B27', 'mestinya dan kepada yang berkepentingan');
	// $objPHPExcel->getActiveSheet()->setCellValue('B28', 'dimohon bantuannya bilamana perlu.');

	// $objPHPExcel->getActiveSheet()->setCellValue('F25', 'This Letter of Assignment is issued in order');
	// $objPHPExcel->getActiveSheet()->setCellValue('F26', 'to be implemented as should be done and');
	// $objPHPExcel->getActiveSheet()->setCellValue('F27', 'to whomsoever concerned  assistance when');
	// $objPHPExcel->getActiveSheet()->setCellValue('F28', 'required is apreciated.');



	// $objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getFont()->setBold(true);	
	// $objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:I5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	// // $objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	// // $objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
	// // $objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
	// // $objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
	// // $objPHPExcel->getActiveSheet()->mergeCells('F4:G4');
	// // $objPHPExcel->getActiveSheet()->mergeCells('H4:H5');
	// // $objPHPExcel->getActiveSheet()->mergeCells('I4:I5');

	// // $objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	// // $objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
	// // $objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
	// // $objPHPExcel->getActiveSheet()->setCellValue('D4', 'JADWAL');
	// // $objPHPExcel->getActiveSheet()->setCellValue('F4', 'AKTUAL');				
	// // $objPHPExcel->getActiveSheet()->setCellValue('H4', 'DURASI');
	// // $objPHPExcel->getActiveSheet()->setCellValue('I4', 'KETERANGAN');

	// // $objPHPExcel->getActiveSheet()->setCellValue('D5', 'MASUK');
	// // $objPHPExcel->getActiveSheet()->setCellValue('E5', 'PULANG');
	// // $objPHPExcel->getActiveSheet()->setCellValue('F5', 'MASUK');
	// // $objPHPExcel->getActiveSheet()->setCellValue('G5', 'PULANG');								

	// $rows = 6;
	// $arrNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
	// $arrShift=arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
	// $arrJadwal=arrayQuery("select idPegawai, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where tanggalJadwal='".setTanggal($par[tanggalAbsen])."'");

	// $filter = "where '".setTanggal($par[tanggalAbsen])."' between date(t1.mulaiAbsen) and date(t1.selesaiAbsen) and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";

	// if(!empty($par[idLokasi]))
	// 	$filter.= " and t2.location='".$par[idLokasi]."'";

	// if($par[search] == "Nama")
	// 	$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
	// else if($par[search] == "NPP")
	// 	$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
	// else
	// 	$filter.= " and (
	// lower(t2.name) like '%".strtolower($par[filter])."%'
	// or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
	// )";

	// $filter .= " AND t2.location IN ($areaCheck)";
	
	// $sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
	// $res=db($sql);
	// while($r=mysql_fetch_array($res)){
	// 	list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
	// 	explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

	// 	list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
	// 	list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

	// 	if(isset($arrJadwal["$r[idPegawai]"]))
	// 		list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"]);
		
	// 	$arr["$r[idPegawai]"]=$r;
	// }

	// if(is_array($arr)){				
	// 	reset($arr);		
	// 	while(list($idPegawai, $r)=each($arr)){
	// 		$no++;			

	// 		if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
	// 		if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

	// 		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	// 		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// 		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':I'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	// 		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
	// 		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
	// 		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, substr($r[mulaiShift],0,5));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, substr($r[selesaiShift],0,5));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, substr($r[masukAbsen],0,5));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, substr($r[pulangAbsen],0,5));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, substr(str_replace("-","",$r[durasiAbsen]),0,5));
	// 		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $r[keteranganAbsen]);

	// 		$rows++;				
	// 	}
	// }

	// $rows--;
	// $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	// $objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	// $objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getAlignment()->setWrapText(true);
	// $objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getFont()->setName('Arial');
	// $objPHPExcel->getActiveSheet()->getStyle('A6:I'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

	$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
	$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "no":
				$text = gNomor();
			break;
			case "dinas":
			$text = gDinas();
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
			case "detAts":
				$text = detailApproval();
			break;
			case "detSdm":
				$text = detailApproval();
			break;
			case "detPay":
				$text = detailApproval();
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

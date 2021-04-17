<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/hadir/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IK";
		$date=empty($_GET[tanggalHadir]) ? $inp[tanggalHadir] : $_GET[tanggalHadir];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorHadir from att_hadir where month(tanggalHadir)='$bulan' and year(tanggalHadir)='$tahun' order by nomorHadir desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}
	
	function gKategori(){
		global $db,$s,$inp,$par;		
		return getField("select lower(trim(namaData)) from mst_data where kodeData='$par[idKategori]'") == "izin tidak masuk kerja" ? "block" : "none";
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
	
	function upload($idHadir){
		global $db,$s,$inp,$par,$fFile;
		$fileUpload = $_FILES["fileHadir"]["tmp_name"];
		$fileUpload_name = $_FILES["fileHadir"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileHadir = "doc-".$idHadir.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileHadir);			
		}
		if(empty($fileHadir)) $fileHadir = getField("select fileHadir from att_hadir where idHadir='$idHadir'");
		
		return $fileHadir;
	}
			
	function all(){
		global $s,$inp,$par,$cUsername, $areaCheck, $cGroup;
		repField();				
		
		$filter = "where year(t1.tanggalHadir)='$par[tahunHadir]' AND location IN ( $areaCheck )".($cGroup != "1" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		if(!empty($par[idLokasi]))
			$filter.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorHadir) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$persetujuanHadir = $par[mode] == "allSdm" ? "sdmHadir" : "persetujuanHadir";
		$keteranganHadir = $par[mode] == "allSdm" ? "noteHadir" : "catatanHadir";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$sql="update att_hadir t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganHadir='".$inp[$keteranganHadir]."', $persetujuanHadir='".$inp[$persetujuanHadir]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' ".$sWhere;
		db($sql);
		
		echo "<script>window.parent.location='index.php?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileHadir=upload($par[idHadir]);
		
		$hariHadir = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
		$mulaiHadir = setTanggal($inp[mulaiHadir_tanggal])." ".$inp[mulaiHadir_waktu];
		$selesaiHadir = setTanggal($inp[selesaiHadir_tanggal])." ".$inp[selesaiHadir_waktu];
		
		$sql="update att_hadir set idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', idKategori='$inp[idKategori]', idTipe='$inp[idTipe]', nomorHadir='$inp[nomorHadir]', tanggalHadir='".setTanggal($inp[tanggalHadir])."', mulaiHadir='$mulaiHadir', selesaiHadir='$selesaiHadir', keteranganHadir='$inp[keteranganHadir]', fileHadir='$fileHadir', hariHadir='$hariHadir', noteHadir='$inp[noteHadir]', sdmHadir='$inp[sdmHadir]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idHadir='$par[idHadir]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		repField();				
		$fileHadir=upload($par[idHadir]);
		
		$hariHadir = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
		$mulaiHadir = setTanggal($inp[mulaiHadir_tanggal])." ".$inp[mulaiHadir_waktu];
		$selesaiHadir = setTanggal($inp[selesaiHadir_tanggal])." ".$inp[selesaiHadir_waktu];
		
		$sql="update att_hadir set idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', idKategori='$inp[idKategori]', idTipe='$inp[idTipe]', nomorHadir='$inp[nomorHadir]', tanggalHadir='".setTanggal($inp[tanggalHadir])."', mulaiHadir='$mulaiHadir', selesaiHadir='$selesaiHadir', keteranganHadir='$inp[keteranganHadir]', fileHadir='$fileHadir', hariHadir='$hariHadir', catatanHadir='$inp[catatanHadir]', persetujuanHadir='$inp[persetujuanHadir]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idHadir='$par[idHadir]'";
		db($sql);
				
		if($cell_no = getField("select cell_no from emp where id='".$inp[idPegawai]."'")){			
			if($inp[persetujuanHadir] == "t") $persetujuanHadir = "Disetujui";
			if($inp[persetujuanHadir] == "f") $persetujuanHadir = "Ditolak";
			if($inp[persetujuanHadir] == "r") $persetujuanHadir = "Diperbaiki";
			
			$message = $arrParameter[35];
			$message = str_replace("[NOMOR]", $inp[nomorHadir], $message);
			$message = str_replace("[STATUS]", $persetujuanHadir, $message);
			$message = str_replace("[NAMA]", getField("select namaUser from ".$db['setting'].".app_user where username='".$cUsername."'"), $message);
			sendSMS($cell_no, $message);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;						
		$fileHadir = getField("select fileHadir from att_hadir where idHadir='$par[idHadir]'");
		if(file_exists($fFile.$fileHadir) and $fileHadir!="")unlink($fFile.$fileHadir);
		
		$sql="delete from att_hadir where idHadir='$par[idHadir]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileHadir = getField("select fileHadir from att_hadir where idHadir='$par[idHadir]'");
		if(file_exists($fFile.$fileHadir) and $fileHadir!="")unlink($fFile.$fileHadir);
		
		$sql="update att_hadir set fileHadir='' where idHadir='$par[idHadir]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileHadir=upload($par[idHadir]);
		
		$hariHadir = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
		$mulaiHadir = setTanggal($inp[mulaiHadir_tanggal])." ".$inp[mulaiHadir_waktu];
		$selesaiHadir = setTanggal($inp[selesaiHadir_tanggal])." ".$inp[selesaiHadir_waktu];
		
		$sql="update att_hadir set idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', idKategori='$inp[idKategori]', idTipe='$inp[idTipe]', nomorHadir='$inp[nomorHadir]', tanggalHadir='".setTanggal($inp[tanggalHadir])."', mulaiHadir='$mulaiHadir', selesaiHadir='$selesaiHadir', keteranganHadir='$inp[keteranganHadir]', fileHadir='$fileHadir', hariHadir='$hariHadir', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idHadir='$par[idHadir]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;			
		repField();
		$idHadir = getField("select idHadir from att_hadir order by idHadir desc limit 1")+1;				
		$fileHadir=upload($idHadir);
		
		$hariHadir = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
		$mulaiHadir = setTanggal($inp[mulaiHadir_tanggal])." ".$inp[mulaiHadir_waktu];
		$selesaiHadir = setTanggal($inp[selesaiHadir_tanggal])." ".$inp[selesaiHadir_waktu];
		
		$sql="insert into att_hadir (idHadir, idPegawai, idPengganti, idAtasan, idKategori, idTipe, nomorHadir, tanggalHadir, mulaiHadir, selesaiHadir, keteranganHadir, fileHadir, persetujuanHadir, sdmHadir, hariHadir, createBy, createTime) values ('$idHadir', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[idKategori]', '$inp[idTipe]', '$inp[nomorHadir]', '".setTanggal($inp[tanggalHadir])."', '$mulaiHadir', '$selesaiHadir', '$inp[keteranganHadir]', '$fileHadir', 'p', 'p', '$hariHadir', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$leader_id = getField("select leader_id from dta_pegawai where id='".$inp[idPegawai]."'");
		if($cell_no = getField("select cell_no from emp where id='".$leader_id."'")){
			$message = $arrParameter[32];
			$message = str_replace("[NOMOR]", $inp[nomorHadir], $message);
			$message = str_replace("[NAMA]", getField("select name from emp where id='".$inp[idPegawai]."'"), $message);
			sendSMS($cell_no, $message);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
	}
	
	function formAll(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		
		$persetujuanHadir = $par[mode] == "allSdm" ? "sdmHadir" : "persetujuanHadir";
		$keteranganHadir = $par[mode] == "allSdm" ? "noteHadir" : "catatanHadir";
		
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
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanHadir."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanHadir."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanHadir."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganHadir."]\" name=\"inp[".$keteranganHadir."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		
		$sql="select * from att_hadir where idHadir='$par[idHadir]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanHadir] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanHadir] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanHadir] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmHadir] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmHadir] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmHadir] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorHadir])) $r[nomorHadir] = gNomor();
		if(empty($r[tanggalHadir])) $r[tanggalHadir] = date('Y-m-d');
		
		$hTrue =  $r[hariHadir] == "t" ? "checked=\"checked\"" : "";		
		$hFalse =  empty($hTrue) ? "checked=\"checked\"" : "";
		
		list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
		list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);
		
		if($mulaiHadir_tanggal != $selesaiHadir_tanggal){
			$jamMulai = "none";
			$jamSelesai = "none";
			$allDay = "none";
		}else{
			if($r[hariHadir] == "t"){
				$jamMulai = "none";
				$jamSelesai = "none";
			}else{				
				$jamMulai = "block";
				$jamSelesai = "block";			
			}
			
			$allDay = "block";	
		}
		
		setValidation("is_null","inp[nomorHadir]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalHadir","anda harus mengisi tanggal");
		setValidation("is_null","mulaiHadir_tanggal","anda harus mengisi tanggal mulai");
		setValidation("is_null","selesaiHadir_tanggal","anda harus mengisi tanggal selesai");
		
		setValidation("is_null","inp[idAtasan]","anda harus mengisi atasan");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori izin");
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
								<input type=\"text\" id=\"inp[nomorHadir]\" name=\"inp[nomorHadir]\"  value=\"$r[nomorHadir]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalHadir\" name=\"inp[tanggalHadir]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalHadir])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN KETIDAKHADIRAN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Tanggal Mulai</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiHadir_tanggal\" name=\"inp[mulaiHadir_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($mulaiHadir_tanggal)."\" class=\"vsmallinput hasDatePicker\" style=\"float:left; margin-right:10px;\" onchange=\"setHide();\"/>
								<div id=\"jamMulai\" style=\"display:".$jamMulai."\">
									<label class=\"l-input-small\">Jam Mulai</label>
									<input type=\"text\" id=\"mulaiHadir_waktu\" name=\"inp[mulaiHadir_waktu]\" size=\"10\" maxlength=\"5\" value=\"".substr($mulaiHadir_waktu,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
								</div>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tanggal Selesai</label>
							<div class=\"field\">
								<input type=\"text\" id=\"selesaiHadir_tanggal\" name=\"inp[selesaiHadir_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($selesaiHadir_tanggal)."\" class=\"vsmallinput hasDatePicker\" style=\"float:left; margin-right:10px;\" onchange=\"setHide();\"/>
								<div id=\"jamSelesai\" style=\"display:".$jamSelesai."\">
									<label class=\"l-input-small\">Jam Selesai</label>
									<input type=\"text\" id=\"selesaiHadir_waktu\" name=\"inp[selesaiHadir_waktu]\" size=\"10\" maxlength=\"5\" value=\"".substr($selesaiHadir_waktu,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
								</div>
							</div>
						</p>
						<div id=\"allDay\" style=\"display:".$allDay."\">
						<p>
							<label class=\"l-input-small\">All Day</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[hariHadir]\" value=\"t\" onclick=\"setHide();\" $hTrue /> <span class=\"sradio\">Ya</span>
								<input type=\"radio\" id=\"false\" name=\"inp[hariHadir]\" value=\"f\" onclick=\"setHide();\" $hFalse /> <span class=\"sradio\">Tidak</span>
							</div>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganHadir]\" name=\"inp[keteranganHadir]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganHadir]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						";
					
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
					
					$tipeIzin = strtolower(getField("select lower(trim(namaData)) from mst_data where kodeData='$r[idKategori]'")) == "izin tidak masuk kerja" ? "block" : "none";
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<div class=\"field\">					
								<input type=\"hidden\" id=\"inp[idAtasan]\" name=\"inp[idAtasan]\"  value=\"$r[idAtasan]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikAtasan]\" name=\"inp[nikAtasan]\"  value=\"$r_[nikAtasan]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getAtasan('".getPar($par,"mode, nikPegawai")."');\" />
								<input type=\"text\" id=\"inp[namaAtasan]\" name=\"inp[namaAtasan]\"  value=\"$r_[namaAtasan]\" class=\"mediuminput\" style=\"width:250px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Kategori Izin</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[14]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"onchange=\"setTipe('".getPar($par,"mode, idKategori")."');\"", "310px")."
							</div>
						</p>
						<div id=\"tipeIzin\" style=\"display:$tipeIzin\">
						<p>
							<label class=\"l-input-small\">Tipe Izin</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[16]."' order by urutanData","kodeData","namaData","inp[idTipe]"," ",$r[idTipe],"", "310px")."
							</div>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<div class=\"field\">";
								$text.=empty($r[fileHadir])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"fileHadir\" name=\"fileHadir\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=hadir&f=$r[idHadir]\"><img src=\"".getIcon($fFile."/".$r[fileHadir])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileHadir\" name=\"fileHadir\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
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
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanHadir]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanHadir]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanHadir]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanHadir]\" name=\"inp[catatanHadir]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanHadir]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmHadir]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmHadir]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmHadir]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteHadir]\" name=\"inp[noteHadir]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteHadir]</textarea>
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
		if(empty($par[tahunHadir])) $par[tahunHadir]=date('Y');
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
					<table>
						<tr>
						<td>Search : </td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td>".comboYear("par[tahunHadir]", $par[tahunHadir])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>	
			</form>	
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idHadir")."',725,300);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idHadir")."',725,300);\"><span>All SDM</span></a>";
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
		
		$filter = "where year(t1.tanggalHadir)='$par[tahunHadir]' AND location IN ( $areaCheck )".($cGroup != "1" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");
		if(!empty($par[idLokasi]))
			$filter.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";
		if(!empty($par[statusPegawai]))
			$filter.=" and t2.cat = '$par[statusPegawai]'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorHadir) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_hadir t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorHadir";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanHadir = $r[persetujuanHadir] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanHadir = $r[persetujuanHadir] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanHadir;
			$persetujuanHadir = $r[persetujuanHadir] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanHadir;
			
			$sdmHadir = $r[sdmHadir] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmHadir = $r[sdmHadir] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmHadir;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir") : "#";			
			$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir") : "#";
			
			list($mulaiHadir) = explode(" ",$r[mulaiHadir]);
			list($selesaiHadir) = explode(" ",$r[selesaiHadir]);
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorHadir]</td>
					<td align=\"center\">".getTanggal($r[tanggalHadir])."</td>
					<td align=\"center\">".getTanggal($mulaiHadir)."</td>
					<td align=\"center\">".getTanggal($selesaiHadir)."</td>					
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanHadir</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmHadir</a></td>
					<td align=\"center\">
						<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."',900,500);\" ><span>Cetak</span></a>
						<a href=\"?par[mode]=det&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>
					</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_hadir where idHadir='$par[idHadir]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
		
		if(empty($r[nomorHadir])) $r[nomorHadir] = gNomor();
		if(empty($r[tanggalHadir])) $r[tanggalHadir] = date('Y-m-d');		

		$hariHadir =  $r[hariHadir] == "t" ? "Ya" : "Tidak";
		
		list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
		list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);
		
		if($mulaiHadir_tanggal != $selesaiHadir_tanggal){
			$jamMulai = "none";
			$jamSelesai = "none";
			$allDay = "none";
		}else{
			if($r[hariHadir] == "t"){
				$jamMulai = "none";
				$jamSelesai = "none";
			}else{				
				$jamMulai = "block";
				$jamSelesai = "block";			
			}
			
			$allDay = "block";	
		}
		
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
							<span class=\"field\">".$r[nomorHadir]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalHadir],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN KETIDAKHADIRAN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
						<p>
							<label class=\"l-input-small\">Tanggal Mulai</label>
							<span>
								<div style=\"float:left; width:125px;\">".getTanggal($mulaiHadir_tanggal,"t")."</div>
								<span id=\"jamMulai\" style=\"display:".$jamMulai."\">
									<label class=\"l-input-small\">Jam Mulai</label>
									".substr($mulaiHadir_waktu,0,5)."
								</span>&nbsp;
							</span>
						</p>
						</div>
						<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
						<p>
							<label class=\"l-input-small\">Tanggal Selesai</label>
							<span>
								<div style=\"float:left; width:125px;\">".getTanggal($selesaiHadir_tanggal,"t")."</div>
								<span id=\"jamMulai\" style=\"display:".$jamSelesai."\">
									<label class=\"l-input-small\">Jam Selesai</label>
									".substr($selesaiHadir_waktu,0,5)."
								</span>&nbsp;
							</span>
						</p>
						</div>
						<div id=\"allDay\" style=\"display:".$allDay."\">
						<p>
							<label class=\"l-input-small\">All Day</label>
							<span class=\"field\">".$hariHadir."&nbsp;</div>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganHadir])."&nbsp;</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						";
					
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
							<label class=\"l-input-small\">Kategori Izin</label>
							<span class=\"field\">
								".getField("select namaData from mst_data where kodeData='$r[idKategori]'")."&nbsp;
							</span>
						</p>
						<div id=\"tipeIzin\" style=\"display:$tipeIzin\">
						<p>
							<label class=\"l-input-small\">Tipe Izin</label>
							<span class=\"field\">
								".getField("select namaData from mst_data where kodeData='$r[idTipe]'")."&nbsp;
							</span>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<span class=\"field\">";
							$text.=empty($r[fileHadir])? "":
								"<a href=\"download.php?d=hadir&f=$r[idHadir]\"><img src=\"".getIcon($fFile."/".$r[fileHadir])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
							$text.="&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanHadir = "Belum Diproses";
			$persetujuanHadir = $r[persetujuanHadir] == "t" ? "Disetujui" : $persetujuanHadir;
			$persetujuanHadir = $r[persetujuanHadir] == "f" ? "Ditolak" : $persetujuanHadir;		
			$persetujuanHadir = $r[persetujuanHadir] == "r" ? "Diperbaiki" : $persetujuanHadir;		
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanHadir."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanHadir])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmHadir = "Belum Diproses";
			$sdmHadir = $r[sdmHadir] == "t" ? "Disetujui" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "f" ? "Ditolak" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "r" ? "Diperbaiki" : $sdmHadir;
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmHadir."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteHadir])."&nbsp;</span>
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
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from att_hadir where idHadir='$par[idHadir]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
		list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
		list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);
		
		list($Y,$m,$d) = explode("-", $mulaiHadir_tanggal);
		$mulaiHari = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];
		$tanggalHadir = $mulaiHari.", ".getTanggal($mulaiHadir_tanggal,"t");
		if($mulaiHadir_tanggal != $selesaiHadir_tanggal){
			list($Y,$m,$d) = explode("-", $selesaiHadir_tanggal);
			$selesaiHari = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];
			$tanggalHadir.= " s.d ".$selesaiHari.", ".getTanggal($selesaiHadir_tanggal,"t");
		}
		
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
		$pdf->Cell(80,6,'SURAT IJIN SERBA GUNA',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(80,6,'Nomor : '.$r[nomorHadir],0,0,'C');
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
		$pdf->Row(array("Hari, Tanggal\tb", ":", $tanggalHadir), false);
		
		if($mulaiHadir_tanggal == $selesaiHadir_tanggal && $mulaiHadir_waktu != "00:00:00"){			
			if($selesaiHadir_waktu == "00:00:00")
				$pdf->Row(array("Waktu\tb", ":", substr($mulaiHadir_waktu,0,5)), false);
			else
				$pdf->Row(array("Waktu\tb", ":", substr($mulaiHadir_waktu,0,5)." s.d ".substr($selesaiHadir_waktu,0,5)), false);
		}		
		$pdf->Row(array("Kategori\tb", ":", getField("select namaData from mst_data where kodeData='$r[idKategori]'")), false);		
		if(!empty($r[idTipe])) $pdf->Row(array("Tipe\tb", ":", getField("select namaData from mst_data where kodeData='$r[idTipe]'")), false);		
		$pdf->Row(array("Keterangan\tb", ":", $r[keteranganHadir]), false);
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial');
		$pdf->Cell(80,3,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'").', '.getTanggal($r[tanggalHadir],"t"),0,0,'L');
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
			case "peg":
				$text = pegawai();
			break;
			case "kat":
				$text = gKategori();
			break;
			case "det":
				$text = detail();
			break;
			case "print":
			$text = pdf();
			break;
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
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
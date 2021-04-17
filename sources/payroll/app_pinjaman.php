<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/pinjaman/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="PJM";
		$date=empty($_GET[tanggalPinjaman]) ? $inp[tanggalPinjaman] : $_GET[tanggalPinjaman];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorPinjaman from ess_pinjaman where month(tanggalPinjaman)='$bulan' and year(tanggalPinjaman)='$tahun' order by nomorPinjaman desc limit 1");
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
	
	function upload($idPinjaman){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["filePinjaman"]["tmp_name"];
		$fileUpload_name = $_FILES["filePinjaman"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$filePinjaman = "doc-".$idPinjaman.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $filePinjaman);			
		}
		if(empty($filePinjaman)) $filePinjaman = getField("select filePinjaman from ess_pinjaman where idPinjaman='$idPinjaman'");
		
		return $filePinjaman;
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;						
		$filePinjaman = getField("select filePinjaman from ess_pinjaman where idPinjaman='$par[idPinjaman]'");
		if(file_exists($fFile.$filePinjaman) and $filePinjaman!="")unlink($fFile.$filePinjaman);
		
		$sql="delete from ess_pinjaman where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$sql="delete from ess_angsuran where idPinjaman='$par[idPinjaman]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPinjaman")."';</script>";
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$filePinjaman = getField("select filePinjaman from ess_pinjaman where idPinjaman='$par[idPinjaman]'");
		if(file_exists($fFile.$filePinjaman) and $filePinjaman!="")unlink($fFile.$filePinjaman);
		
		$sql="update ess_pinjaman set filePinjaman='' where idPinjaman='$par[idPinjaman]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function all(){
		global $db,$s,$inp,$par,$cUsername, $areaCheck;
		repField();				
		
		$filter = "where nomorPinjaman is not null AND t2.group_id IN ( $areaCheck )";		
		$filter.= " and t1.tanggalPinjaman between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
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
			lower(t1.nomorPinjaman) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$persetujuanPinjaman = $par[mode] == "allSdm" ? "sdmPinjaman" : "persetujuanPinjaman";
		$keteranganPinjaman = $par[mode] == "allSdm" ? "notePinjaman" : "catatanPinjaman";
		$approveBy = $par[mode] == "allSdm" ? "sdmBy" : "approveBy";
		$approveTime = $par[mode] == "allSdm" ? "sdmTime" : "approveTime";
		
		$persetujuanPinjaman = $par[mode] == "allPay" ? "pembayaranPinjaman" : $persetujuanPinjaman;
		$keteranganPinjaman = $par[mode] == "allPay" ? "deskripsiPinjaman" : $keteranganPinjaman;
		$approveBy = $par[mode] == "allPay" ? "paidBy" : $approveBy;
		$approveTime = $par[mode] == "allPay" ? "paidTime" : $approveTime;
		
		$sql="update ess_pinjaman t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) set $keteranganPinjaman='".$inp[$keteranganPinjaman]."', $persetujuanPinjaman='".$inp[$persetujuanPinjaman]."', $approveBy='$cUsername', $approveTime='".date('Y-m-d H:i:s')."' $filter";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function paid(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$filePinjaman=upload($par[idPinjaman]);
		
		$sql="update ess_pinjaman set idPegawai='$inp[idPegawai]', nomorPinjaman='$inp[nomorPinjaman]', tanggalPinjaman='".setTanggal($inp[tanggalPinjaman])."', nilaiPinjaman='".setAngka($inp[nilaiPinjaman])."', waktuPinjaman='".setAngka($inp[waktuPinjaman])."', keteranganPinjaman='$inp[keteranganPinjaman]', filePinjaman='$filePinjaman', deskripsiPinjaman='$inp[deskripsiPinjaman]', pembayaranPinjaman='$inp[pembayaranPinjaman]', paidBy='$cUsername', paidTime='".date('Y-m-d H:i:s')."' where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$sql="delete from ess_angsuran where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$tanggalAngsuran = setTanggal($inp[tanggalPinjaman]);
		$nilaiAngsuran = setAngka($inp[waktuPinjaman]) > 0 ? setAngka($inp[nilaiPinjaman]) / setAngka($inp[waktuPinjaman]) : 0;
		for($idAngsuran=1; $idAngsuran<=setAngka($inp[waktuPinjaman]); $idAngsuran++){
			list($tahun, $bulan, $tanggal) = explode("-", $tanggalAngsuran);
			$tahun = $bulan == 12 ? $tahun + 1 : $tahun;
			$bulan = $bulan == 12 ? 1 : $bulan + 1;
			$tanggalAngsuran = $tahun."-".$bulan."-".date("t", strtotime($tahun."-".$bulan."-01"));
			
			$sql="insert into ess_angsuran (idPinjaman, idAngsuran, tanggalAngsuran, nilaiAngsuran, statusAngsuran, createBy, createTime) values ('$par[idPinjaman]', '$idAngsuran', '$tanggalAngsuran', '".setAngka($nilaiAngsuran)."', 'f', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idPinjaman")."';</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		repField();				
		$filePinjaman=upload($par[idPinjaman]);
		
		$sql="update ess_pinjaman set idPegawai='$inp[idPegawai]', nomorPinjaman='$inp[nomorPinjaman]', tanggalPinjaman='".setTanggal($inp[tanggalPinjaman])."', nilaiPinjaman='".setAngka($inp[nilaiPinjaman])."', waktuPinjaman='".setAngka($inp[waktuPinjaman])."', keteranganPinjaman='$inp[keteranganPinjaman]', filePinjaman='$filePinjaman', notePinjaman='$inp[notePinjaman]', sdmPinjaman='$inp[sdmPinjaman]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$sql="delete from ess_angsuran where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$tanggalAngsuran = setTanggal($inp[tanggalPinjaman]);
		$nilaiAngsuran = setAngka($inp[waktuPinjaman]) > 0 ? setAngka($inp[nilaiPinjaman]) / setAngka($inp[waktuPinjaman]) : 0;
		for($idAngsuran=1; $idAngsuran<=setAngka($inp[waktuPinjaman]); $idAngsuran++){
			list($tahun, $bulan, $tanggal) = explode("-", $tanggalAngsuran);
			$tahun = $bulan == 12 ? $tahun + 1 : $tahun;
			$bulan = $bulan == 12 ? 1 : $bulan + 1;
			$tanggalAngsuran = $tahun."-".$bulan."-".date("t", strtotime($tahun."-".$bulan."-01"));
			
			$sql="insert into ess_angsuran (idPinjaman, idAngsuran, tanggalAngsuran, nilaiAngsuran, statusAngsuran, createBy, createTime) values ('$par[idPinjaman]', '$idAngsuran', '$tanggalAngsuran', '".setAngka($nilaiAngsuran)."', 'f', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		
		if($cell_no = getField("select cell_no from emp where id='".$inp[idPegawai]."'")){			
			if($inp[sdmPinjaman] == "t") $sdmPinjaman = "Disetujui";
			if($inp[sdmPinjaman] == "f") $sdmPinjaman = "Ditolak";
			if($inp[sdmPinjaman] == "r") $sdmPinjaman = "Diperbaiki";
			
			$message = $arrParameter[36];
			$message = str_replace("[NOMOR]", $inp[nomorPinjaman], $message);
			$message = str_replace("[STATUS]", $sdmPinjaman, $message);
			$message = str_replace("[NAMA]", getField("select namaUser from ".$db['setting'].".app_user where username='".$cUsername."'"), $message);
			sendSMS($cell_no, $message);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idPinjaman")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		repField();				
		$filePinjaman=upload($par[idPinjaman]);
		
		$sql="update ess_pinjaman set idPegawai='$inp[idPegawai]', nomorPinjaman='$inp[nomorPinjaman]', tanggalPinjaman='".setTanggal($inp[tanggalPinjaman])."', nilaiPinjaman='".setAngka($inp[nilaiPinjaman])."', waktuPinjaman='".setAngka($inp[waktuPinjaman])."', keteranganPinjaman='$inp[keteranganPinjaman]', filePinjaman='$filePinjaman', catatanPinjaman='$inp[catatanPinjaman]', persetujuanPinjaman='$inp[persetujuanPinjaman]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$sql="delete from ess_angsuran where idPinjaman='$par[idPinjaman]'";
		db($sql);
		
		$tanggalAngsuran = setTanggal($inp[tanggalPinjaman]);
		$nilaiAngsuran = setAngka($inp[waktuPinjaman]) > 0 ? setAngka($inp[nilaiPinjaman]) / setAngka($inp[waktuPinjaman]) : 0;
		for($idAngsuran=1; $idAngsuran<=setAngka($inp[waktuPinjaman]); $idAngsuran++){
			list($tahun, $bulan, $tanggal) = explode("-", $tanggalAngsuran);
			$tahun = $bulan == 12 ? $tahun + 1 : $tahun;
			$bulan = $bulan == 12 ? 1 : $bulan + 1;
			$tanggalAngsuran = $tahun."-".$bulan."-".date("t", strtotime($tahun."-".$bulan."-01"));
			
			$sql="insert into ess_angsuran (idPinjaman, idAngsuran, tanggalAngsuran, nilaiAngsuran, statusAngsuran, createBy, createTime) values ('$par[idPinjaman]', '$idAngsuran', '$tanggalAngsuran', '".setAngka($nilaiAngsuran)."', 'f', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		
		if($cell_no = getField("select cell_no from emp where id='".$inp[idPegawai]."'")){			
			if($inp[persetujuanPinjaman] == "t") $persetujuanPinjaman = "Disetujui";
			if($inp[persetujuanPinjaman] == "f") $persetujuanPinjaman = "Ditolak";
			if($inp[persetujuanPinjaman] == "r") $persetujuanPinjaman = "Diperbaiki";
			
			$message = $arrParameter[36];
			$message = str_replace("[NOMOR]", $inp[nomorPinjaman], $message);
			$message = str_replace("[STATUS]", $persetujuanPinjaman, $message);
			$message = str_replace("[NAMA]", getField("select namaUser from ".$db['setting'].".app_user where username='".$cUsername."'"), $message);
			sendSMS($cell_no, $message);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idPinjaman")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;			
		repField();				
		$idPinjaman = getField("select idPinjaman from ess_pinjaman order by idPinjaman desc limit 1")+1;				
		$filePinjaman=upload($idPinjaman);
		
		$sql="insert into ess_pinjaman (idPinjaman, idPegawai, nomorPinjaman, tanggalPinjaman, nilaiPinjaman, waktuPinjaman, keteranganPinjaman, filePinjaman, persetujuanPinjaman, sdmPinjaman, createBy, createTime) values ('$idPinjaman', '$inp[idPegawai]', '$inp[nomorPinjaman]', '".setTanggal($inp[tanggalPinjaman])."', '".setAngka($inp[nilaiPinjaman])."', '".setAngka($inp[waktuPinjaman])."', '$inp[keteranganPinjaman]', '$filePinjaman', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$tanggalAngsuran = setTanggal($inp[tanggalPinjaman]);
		$nilaiAngsuran = setAngka($inp[waktuPinjaman]) > 0 ? setAngka($inp[nilaiPinjaman]) / setAngka($inp[waktuPinjaman]) : 0;
		for($idAngsuran=1; $idAngsuran<=setAngka($inp[waktuPinjaman]); $idAngsuran++){
			list($tahun, $bulan, $tanggal) = explode("-", $tanggalAngsuran);
			$tahun = $bulan == 12 ? $tahun + 1 : $tahun;
			$bulan = $bulan == 12 ? 1 : $bulan + 1;
			$tanggalAngsuran = $tahun."-".$bulan."-".date("t", strtotime($tahun."-".$bulan."-01"));
			
			$sql="insert into ess_angsuran (idPinjaman, idAngsuran, tanggalAngsuran, nilaiAngsuran, statusAngsuran, createBy, createTime) values ('$idPinjaman', '$idAngsuran', '$tanggalAngsuran', '".setAngka($nilaiAngsuran)."', 'f', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		
		$leader_id = getField("select leader_id from dta_pegawai where id='".$inp[idPegawai]."'");
		if($cell_no = getField("select cell_no from emp where id='".$leader_id."'")){
			$message = $arrParameter[33];
			$message = str_replace("[NOMOR]", $inp[nomorPinjaman], $message);
			$message = str_replace("[NAMA]", getField("select name from emp where id='".$inp[idPegawai]."'"), $message);
			sendSMS($cell_no, $message);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idPinjaman")."';</script>";
	}
	
	function formAll(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess,$cUsername;
		
		$persetujuanPinjaman = $par[mode] == "allSdm" ? "sdmPinjaman" : "persetujuanPinjaman";
		$keteranganPinjaman = $par[mode] == "allSdm" ? "notePinjaman" : "catatanPinjaman";
		$approveBy = $par[mode] == "allSdm" ? "approveBy" : "sdmBy";
		$approveTime = $par[mode] == "allSdm" ? "approveTime" : "sdmTime";
		
		$persetujuanPinjaman = $par[mode] == "allPay" ? "pembayaranPinjaman" : $persetujuanPinjaman;
		$keteranganPinjaman = $par[mode] == "allPay" ? "deskripsiPinjaman" : $keteranganPinjaman;
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
							<input type=\"radio\" id=\"true\" name=\"inp[".$persetujuanPinjaman."]\" value=\"t\" checked=\"checked\" /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[".$persetujuanPinjaman."]\" value=\"f\" > <span class=\"sradio\">Ditolak</span>
							<input type=\"radio\" id=\"revisi\" name=\"inp[".$persetujuanPinjaman."]\" value=\"r\" /> <span class=\"sradio\">Diperbaiki</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[".$keteranganPinjaman."]\" name=\"inp[".$keteranganPinjaman."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\"></textarea>
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
		
		$sql="select * from ess_pinjaman where idPinjaman='$par[idPinjaman]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanPinjaman] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanPinjaman] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanPinjaman] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmPinjaman] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmPinjaman] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmPinjaman] == "r" ? "checked=\"checked\"" : "";
		
		$pTrue = $r[pembayaranPinjaman] == "t" ? "checked=\"checked\"" : "";
		$pFalse = $r[pembayaranPinjaman] == "f" ? "checked=\"checked\"" : "";
		$pRevisi = $r[pembayaranPinjaman] == "r" ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[nomorPinjaman]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalPinjaman","anda harus mengisi tanggal");		
		setValidation("is_null","inp[keteranganPinjaman]","anda harus mengisi keperluan");
		setValidation("is_null","inp[nilaiPinjaman]","anda harus mengisi nilai");
		setValidation("is_null","inp[waktuPinjaman]","anda harus mengisi waktu");
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
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return chk();\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[nomorPinjaman]\" name=\"inp[nomorPinjaman]\"  value=\"$r[nomorPinjaman]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalPinjaman\" name=\"inp[tanggalPinjaman]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalPinjaman])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PENGAJUAN PINJAMAN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Keperluan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganPinjaman]\" name=\"inp[keteranganPinjaman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganPinjaman]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<div class=\"field\">";
								$text.=empty($r[filePinjaman])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"filePinjaman\" name=\"filePinjaman\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=pinjaman&f=$r[idPinjaman]\"><img src=\"".getIcon($fFile."/".$r[filePinjaman])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"filePinjaman\" name=\"filePinjaman\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[nilaiPinjaman]\" name=\"inp[nilaiPinjaman]\"  value=\"".getAngka($r[nilaiPinjaman])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"setAngsuran();\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[waktuPinjaman]\" name=\"inp[waktuPinjaman]\"  value=\"".getAngka($r[waktuPinjaman])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"setAngsuran();\" /> bulan
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Angsuran</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[angsuranPinjaman]\" name=\"inp[angsuranPinjaman]\"  value=\"".getAngka($r[nilaiPinjaman]/$r[waktuPinjaman])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" readonly=\"readonly\" />
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
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanPinjaman]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanPinjaman]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanPinjaman]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanPinjaman]\" name=\"inp[catatanPinjaman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanPinjaman]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmPinjaman]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmPinjaman]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmPinjaman]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[notePinjaman]\" name=\"inp[notePinjaman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[notePinjaman]</textarea>
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
			if(empty($r[paidTime]) || $r[paidTime] == "0000-00-00") $r[paidTime] = date('Y-m-d');
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
								<input type=\"radio\" id=\"true\" name=\"inp[pembayaranPinjaman]\" value=\"t\" $pTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[pembayaranPinjaman]\" value=\"f\" $pFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[pembayaranPinjaman]\" value=\"r\" $pRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[deskripsiPinjaman]\" name=\"inp[deskripsiPinjaman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[deskripsiPinjaman]</textarea>
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
		if(empty($par[tanggalAwal])) $par[tanggalAwal] = date('01/m/Y');
		if(empty($par[tanggalAkhir])) $par[tanggalAkhir] = date('d/m/Y');
		
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
										<span style=\"margin-left:30px;\">Periode :</span> ".comboMonth("par[bulan]", $par[bulan], "onchange=\"document.getElementById('form').submit();\"", "", "t")." ".comboYear("par[tahun]", $par[tahun], "", "onchange=\"document.getElementById('form').submit();\"", "", "t")."
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
						<td>Periode : </td>
						<td><input type=\"text\" id=\"tanggalAwal\" name=\"par[tanggalAwal]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAwal]."\" class=\"vsmallinput hasDatePicker\"/> s.d <input type=\"text\" id=\"tanggalAkhir\" name=\"par[tanggalAkhir]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAkhir]."\" class=\"vsmallinput hasDatePicker\"/></td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>				
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["apprlv1"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allAts".getPar($par,"mode,idPinjaman")."',725,400);\"><span>All Atasan</span></a> ";
		if(isset($menuAccess[$s]["apprlv2"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allSdm".getPar($par,"mode,idPinjaman")."',725,400);\"><span>All SDM</span></a> ";
		if(isset($menuAccess[$s]["apprlv3"])) $text.="<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('popup.php?par[mode]=allPay".getPar($par,"mode,idPinjaman")."',725,400);\"><span>All Bayar</span></a>";
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
					<th rowspan=\"2\" width=\"100\">Nilai</th>
					<th rowspan=\"2\" width=\"50\">Lunas</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th colspan=\"2\" width=\"100\">Bayar</th>
					<th rowspan=\"2\" width=\"50\">Kontrol</th>
				</tr>
				<tr>					
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
					<th width=\"50\">Bayar</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where nomorPinjaman is not null and t1.persetujuanPinjaman='t' AND t2.group_id IN ( $areaCheck )";		
		$filter.= " and t1.tanggalPinjaman between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
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
			lower(t1.nomorPinjaman) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from ess_pinjaman t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorPinjaman";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanPinjaman;
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanPinjaman;
			
			$sdmPinjaman = $r[sdmPinjaman] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmPinjaman = $r[sdmPinjaman] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmPinjaman;
			$sdmPinjaman = $r[sdmPinjaman] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmPinjaman;
			
			if(empty($r[pembayaranPinjaman])) $r[pembayaranPinjaman] = "p";
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pembayaranPinjaman;
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pembayaranPinjaman;
			
			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman") : "#";			
			$sdmLink = (isset($menuAccess[$s]["apprlv2"]) && $r[persetujuanPinjaman] == "t") ? "?par[mode]=sdm&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman") : "#";
			$pembayaranLink = (isset($menuAccess[$s]["apprlv3"]) && $r[persetujuanPinjaman] == "t" && $r[sdmPinjaman] == "t") ? "?par[mode]=byr&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman") : "#";
			
			$statusPinjaman = getField("select count(*) from ess_angsuran where statusAngsuran='f' and idPinjaman='$r[idPinjaman]'") > 0 ?
			"<img src=\"styles/images/f.png\" title=\"Belum Lunas\">":
			"<img src=\"styles/images/t.png\" title=\"Sudah Lunas\">";
			
			$text.="<tr>
					<td>$no.</td>					
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorPinjaman]</td>
					<td align=\"center\">".getTanggal($r[tanggalPinjaman])."</td>
					<td align=\"right\">".getAngka($r[nilaiPinjaman])."</td>					
					<td align=\"center\">$statusPinjaman</td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman")."',750,425);\" >$persetujuanPinjaman</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmPinjaman</a></td>
					<td align=\"center\"><a href=\"".$pembayaranLink."\" title=\"Detail Data\">$pembayaranPinjaman</a></td>
					<td align=\"center\">";
			if($r[pembayaranPinjaman] == "t")
			$text.="<a href=\"ajax.php?par[mode]=print&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman")."\" title=\"Print Data\" class=\"print\" target=\"print\"><span>Print</span></a>";
			$text.="&nbsp;</td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[idPinjaman]=$r[idPinjaman]".getPar($par,"mode,idPinjaman")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
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
		
		$sql="select * from ess_pinjaman t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.idPinjaman='".$par[idPinjaman]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'PINJAMAN KARYAWAN',0,0,'L');		
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
		$pdf->Row(array($r_[namaProfile]."\tb","","Tanggal\tb",":",getTanggal($r[tanggalPinjaman],"t")), false);
		$pdf->Row(array($r_[alamatProfile],"","Nomor\tb",":",$r[nomorPinjaman]), false);
		
		$pdf->Ln();
		
		$pdf->SetWidths(array(5, 20, 5, 60, 10, 30, 5, 60, 5));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("\tf", "KEPERLUAN\tf",":\tf",$r[keteranganPinjaman]."\tf", "\tf","NILAI\tf",":\tf","Rp. ".getAngka($r[nilaiPinjaman])."\tf", "\tf"));
		$pdf->Row(array("\tf", "NAMA\tf",":\tf",$r[name]."\tf","\tf","WAKTU\tf",":\tf",getAngka($r[waktuPinjaman])." bulan\tf", "\tf"));
		$pdf->Row(array("\tf", "NPP\tf",":\tf",$r[reg_no]."\tf","\tf","ANGSURAN\tf",":\tf","Rp. ".getAngka($r[nilaiPinjaman]/$r[waktuPinjaman])."\tf", "\tf"));
		
		$pdf->SetWidths(array(5, 20, 5, 165, 5));
		$pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->Row(array("\tf", "TERBILANG\tf",":\tf",terbilang($r[nilaiPinjaman])." Rupiah\tf", "\tf"));
		
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
		
		$sql="select * from ess_pinjaman where idPinjaman='$par[idPinjaman]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmPinjaman" : "persetujuanPinjaman";
		$catatanField = $par[mode] == "detSdm" ? "notePinjaman" : "catatanPinjaman";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";
		
		$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranPinjaman" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiPinjaman" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "paidTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);
				
		$persetujuanPinjaman = "Belum Diproses";
		$persetujuanPinjaman = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanPinjaman;
		$persetujuanPinjaman = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanPinjaman;	
		$persetujuanPinjaman = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanPinjaman;	
		
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
						<span class=\"field\">".$persetujuanPinjaman."&nbsp;</span>
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
		
		$sql="select * from ess_pinjaman where idPinjaman='$par[idPinjaman]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorPinjaman])) $r[nomorPinjaman] = gNomor();
		if(empty($r[tanggalPinjaman])) $r[tanggalPinjaman] = date('Y-m-d');
				
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
							<span class=\"field\">".$r[nomorPinjaman]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalPinjaman],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA PENGAJUAN PINJAMAN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Keperluan</label>
							<span class=\"field\">".nl2br($r[keteranganPinjaman])."&nbsp;</span>							
						</p>
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<span class=\"field\">";
							$text.=empty($r[filePinjaman])? "":
								"<a href=\"download.php?d=pinjaman&f=$r[idPinjaman]\"><img src=\"".getIcon($fFile."/".$r[filePinjaman])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
							$text.="&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[nilaiPinjaman])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<span class=\"field\">".getAngka($r[waktuPinjaman])." bulan&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			$persetujuanPinjaman = "Belum Diproses";
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "t" ? "Disetujui" : $persetujuanPinjaman;
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "f" ? "Ditolak" : $persetujuanPinjaman;	
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "r" ? "Diperbaiki" : $persetujuanPinjaman;	
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
							<span class=\"field\">".$persetujuanPinjaman."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanPinjaman])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmPinjaman = "Belum Diproses";
			$sdmPinjaman = $r[sdmPinjaman] == "t" ? "Disetujui" : $sdmPinjaman;
			$sdmPinjaman = $r[sdmPinjaman] == "f" ? "Ditolak" : $sdmPinjaman;		
			$sdmPinjaman = $r[sdmPinjaman] == "r" ? "Diperbaiki" : $sdmPinjaman;		
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
							<span class=\"field\">".$sdmPinjaman."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[notePinjaman])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			$pembayaranPinjaman = "Belum Diproses";
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "t" ? "Disetujui" : $pembayaranPinjaman;
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "f" ? "Ditolak" : $pembayaranPinjaman;	
			$pembayaranPinjaman = $r[pembayaranPinjaman] == "r" ? "Diperbaiki" : $pembayaranPinjaman;	
			
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
							<span class=\"field\">".$pembayaranPinjaman."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[deskripsiPinjaman])."&nbsp;</span>
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

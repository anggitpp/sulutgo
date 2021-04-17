<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
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
		// die();
		$cek = getField("select idHadir from att_hadir where tanggalHadir ='".setTanggal($inp[tanggalHadir])."' AND idPegawai = '$inp[idPegawai]' AND persetujuanHadir !='t'");
		if($cek){
			echo "<script>alert('TAMBAH DATA GAGAL, DATA PEGAWAI UNTUK HARI INI SUDAH ADA');</script>";
		}else{
		$idHadir = getField("select idHadir from att_hadir order by idHadir desc limit 1")+1;				
		$fileHadir=upload($idHadir);
		
		$hariHadir = $inp[mulaiHadir_tanggal] == $inp[selesaiHadir_tanggal] ? $inp[hariHadir] : "f";
		$mulaiHadir = setTanggal($inp[mulaiHadir_tanggal])." ".$inp[mulaiHadir_waktu];
		$selesaiHadir = setTanggal($inp[selesaiHadir_tanggal])." ".$inp[selesaiHadir_waktu];
		
		$arrNama = arrayQuery("select id, name from emp");
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		$sql="insert into att_hadir (idHadir, idPegawai, idPengganti, idAtasan, idKategori, idTipe, nomorHadir, tanggalHadir, mulaiHadir, selesaiHadir, keteranganHadir, fileHadir, persetujuanHadir, sdmHadir, hariHadir, createBy, createTime) values ('$idHadir', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[idKategori]', '$inp[idTipe]', '$inp[nomorHadir]', '".setTanggal($inp[tanggalHadir])."', '$mulaiHadir', '$selesaiHadir', '$inp[keteranganHadir]', '$fileHadir', 'p', 'p', '$hariHadir', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);

		$subjek = "Pemberitahuan Rencana Ketidakhadiran $inp[tanggalHadir]";	

		$link = "<a href=\"http://pratamamitra.net/hrms/index.php?c=3&p=12&m=127&s=138\"><b>DISINI</b></a>";			

		$isi1 = "<table width=\"100%\">

			<tr>
			<td colspan=\"3\">Sebagai informasi bahwasannya rencana Izin Ketidakhadiran pada : </td> 
			</tr>
			<br>

			<tr>

			<td style=\"width:100px;\">Tanggal</td>

			<td style=\"width:10px;\">:</td>

			<td>".getTanggal($inp[tanggalHadir], "t")."</td>

			</tr>

			<tr>

			<td style=\"width:100px;\">Nomor</td>

			<td style=\"width:10px;\">:</td>

			<td><strong>".$inp[nomorHadir]."</strong></td>			

			</tr>

			<tr>

			<td style=\"width:100px;\">Kategori Izin</td>

			<td style=\"width:10px;\">:</td>

			<td>".$arrMaster[$inp[idKategori]]."</td>

			</tr>

			<tr>

			<td style=\"width:100px;\">Nama</td>

			<td style=\"width:10px;\">:</td>

			<td>".$arrNama[$inp[idPegawai]]."</td>

			</tr>

			<tr>

			<td style=\"width:100px;\">Pengganti</td>

			<td style=\"width:10px;\">:</td>

			<td>".$arrNama[$inp[idPengganti]]."</td>

			</tr>

			<tr>

			<td style=\"width:100px;\">Mulai Izin</td>

			<td style=\"width:10px;\">:</td>

			<td>".$mulaiHadir." s/d ".$selesaiHadir."</td>

			</tr>					

			<tr>

			<td style=\"width:100px;\">Keterangan</td>

			<td style=\"width:10px;\">:</td>

			<td>$inp[keteranganHadir]</td>

			</tr>
			</table>
			<table>
			<br>
			<tr>
			<td colspan=\"3\">Dimohon untuk melakukan Approval Atasan pada nomor hadir di atas, silahkan klik $link</td>
			</tr>
			<br>
			<tr>
			<td colspan=\"3\">Jakarta, ".date('d M Y')." 
			</tr>
			<tr>
			<td></td>
			</tr>
			<br><br>
			<tr>
			<td>TTD.</td>
			</tr>
			<tr>
			<td>PRATAMA MITRA SEJATI</td>
			</tr>

		</table>";			

		

		$email = getField("select email from dta_pegawai where id = '$inp[idAtasan]'");
		// echo $email;
		// die();

		sendMail($email,$subjek,$isi1);
		echo "<script>alert('TAMBAH DATA BERHASIL');</script>";
		}
		
		// $leader_id = getField("select leader_id from dta_pegawai where id='".$inp[idPegawai]."'");
		// if($cell_no = getField("select cell_no from emp where id='".$leader_id."'")){
		// 	$message = $arrParameter[32];
		// 	$message = str_replace("[NOMOR]", $inp[nomorHadir], $message);
		// 	$message = str_replace("[NAMA]", getField("select name from emp where id='".$inp[idPegawai]."'"), $message);
		// 	sendSMS($cell_no, $message);
		// }
		
		echo "<script>window.location='?".getPar($par,"mode,idHadir")."';</script>";
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
		<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
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
		<label class=\"l-input-small\">Divisi</label>
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
	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from att_hadir where idHadir='$par[idHadir]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			
		
		$persetujuanTitle = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmHadir" : "persetujuanHadir";
		$catatanField = $par[mode] == "detSdm" ? "noteHadir" : "catatanHadir";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";		
		
		$persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranHadir" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiHadir" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);		
		$persetujuanHadir = "Belum Diproses";
		$persetujuanHadir = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanHadir;
		$persetujuanHadir = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanHadir;	
		$persetujuanHadir = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanHadir;	
		
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
		<span class=\"field\">".$persetujuanHadir."&nbsp;</span>
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
		
		$sql="select * from att_hadir where idHadir='$par[idHadir]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
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
		
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;						
		if(empty($r[idPengganti]) || empty($r[idAtasan])){
			list($idPengganti, $idAtasan) = explode("\t", getField("select concat(replacement_id, '\t', leader_id) from dta_pegawai where id='".$r[idPegawai]."'"));
			if(empty($r[idPengganti])) $r[idPengganti] = $idPengganti;
			if(empty($r[idAtasan])) $r[idAtasan] = $idAtasan;	
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
		<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
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
		<label class=\"l-input-small\">Divisi</label>
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
		
		$tipeIzin = getField("select lower(trim(namaData)) from mst_data where kodeData='$r[idKategori]'") == "izin tidak masuk kerja" ? "block" : "none";
		
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
		if(empty($par[tahunHadir])) $par[tahunHadir]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		
		echo "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
		".getBread()."
		
		</div>    
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<div style=\"padding-bottom:10px;\">";				
		require_once "tmpl/__emp_header__.php";						
		$text.="</div>
		<form action=\"\" method=\"post\" class=\"stdform\">
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
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idHadir")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>										
		<th rowspan=\"2\" style=\"min-width:150px;\">Nomor</th>
		<th colspan=\"3\" width=\"225\">Tanggal</th>
		<th colspan=\"2\" width=\"100\">Approval</th>
		<th rowspan=\"2\" width=\"30\">Bukti</th>
		<th rowspan=\"2\" width=\"50\">Detail</th>";
		if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
		<tr>
		<th width=\"75\">Dibuat</th>
		<th width=\"75\">Mulai</th>
		<th width=\"75\">Selesai</th>
		<th width=\"50\">Atasan</th>
		<th width=\"50\">Manager</th>
		</tr>
		</thead>
		<tbody>";
		
		$filter = "where year(t1.tanggalHadir)='$par[tahunHadir]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(t1.nomorHadir) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from att_hadir t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorHadir";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanHadir = $r[persetujuanHadir] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanHadir = $r[persetujuanHadir] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanHadir;
			$persetujuanHadir = $r[persetujuanHadir] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanHadir;
			
			$sdmHadir = $r[sdmHadir] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmHadir = $r[sdmHadir] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmHadir;
			
			list($mulaiHadir) = explode(" ",$r[mulaiHadir]);
			list($selesaiHadir) = explode(" ",$r[selesaiHadir]);

			$view = empty($r[fileHadir]) ? "" : "<a href=\"#\" onclick=\"openBox('view.php?doc=fileHadir&id=$r[idHadir]',1000,500)\"><img src=\"".getIcon($r[fileHadir])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
			
			$text.="<tr>
			<td>$no.</td>					
			<td>$r[nomorHadir]</td>
			<td align=\"center\">".getTanggal($r[tanggalHadir])."</td>
			<td align=\"center\">".getTanggal($mulaiHadir)."</td>
			<td align=\"center\">".getTanggal($selesaiHadir)."</td>					
			<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."',750,425);\" >$persetujuanHadir</a></td>
			<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."',750,425);\" >$sdmHadir</a></td>
			<td align=\"center\">$view</td>
			<td align=\"center\">
			<a href=\"?par[mode]=det&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
			</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">
				<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."',900,500);\" ><span>Cetak</span></a>";
				
				if(in_array($r[persetujuanHadir], array("p","r")) || in_array($r[sdmHadir], array("p","r")))
				if(isset($menuAccess[$s]["edit"])&&$r[persetujuanHadir]!='t') $text.="<a href=\"?par[mode]=edit&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				
				if(in_array($r[persetujuanHadir], array("p")) || in_array($r[sdmHadir], array("p")))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idHadir]=$r[idHadir]".getPar($par,"mode,idHadir")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
		</table>
		</div>";
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
		$pdf->SetLeftMargin(15);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',12);					
		$pdf->Cell(20,6,'PRATAMA MITRA SEJATI',0,0,'L');

		$pdf->Ln();
		
		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(180,6,'SURAT IJIN KETIDAKHADIRAN',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(180,6,'Nomor : '.$r[nomorHadir],0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(180,6,'Kami berikan ijin kepada :',0,0,'L');		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Nama',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->Cell(140,6,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'L');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Departemen',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->Cell(140,6,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'"),0,0,'L');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Jabatan',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->Cell(140,6,getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),0,0,'L');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Hari, Tanggal',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->Cell(140,6,$tanggalHadir,0,0,'L');
		$pdf->Ln();
		
		if($mulaiHadir_tanggal == $selesaiHadir_tanggal && $mulaiHadir_waktu != "00:00:00"){
			$pdf->SetFont('Arial','B');
			$pdf->Cell(35,6,'Waktu',0,0,'L');
			$pdf->SetFont('Arial');
			$pdf->Cell(5,6,':',0,0,'L');
			if($selesaiHadir_waktu == "00:00:00")
				$pdf->Cell(140,6,substr($mulaiHadir_waktu,0,5),0,0,'L');				
			else
				$pdf->Cell(140,6,substr($mulaiHadir_waktu,0,5)." s.d ".substr($selesaiHadir_waktu,0,5),0,0,'L');
				
			$pdf->Ln();
		}
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Kategori',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->Cell(140,6,getField("select namaData from mst_data where kodeData='$r[idKategori]'"),0,0,'L');
		$pdf->Ln();
		
		if(!empty($r[idTipe])){
			$pdf->SetFont('Arial','B');
			$pdf->Cell(35,6,'Tipe',0,0,'L');
			$pdf->SetFont('Arial');
			$pdf->Cell(5,6,':',0,0,'L');
			$pdf->Cell(140,6,getField("select namaData from mst_data where kodeData='$r[idTipe]'"),0,0,'L');
			$pdf->Ln();
		}
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(35,6,'Keterangan',0,0,'L');
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(140,6,$r[keteranganHadir],0,'L');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial');
		$pdf->Cell(110,3,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.location=t2.kodeData) where t1.parent_id='$r[idPegawai]' and status='1'").', '.getTanggal($r[tanggalHadir],"t"),0,0,'L');
		$pdf->Ln(25);		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(60,5,'   '.getField("select name from emp where id='".$r[idPegawai]."'").'   ',0,0,'C');
		$pdf->Cell(60,5,getField("SELECT NAME FROM emp_phist t1 JOIN emp t2 ON t1.`manager_id` = t2.id WHERE parent_id='$r[idPegawai]' AND t1.status = '1'"),0,0,'C');
		$pdf->Cell(60,5,getField("SELECT NAME FROM emp_phist t1 JOIN emp t2 ON t1.`parent_id` = t2.id WHERE parent_id='$r[idAtasan]' AND t1.status = '1'"),0,0,'C');
		
		$pdf->Ln(2);
		$pdf->SetFont('Arial','U');
		$pdf->Cell(60,3,'                                    ',0,0,'C');				
		$pdf->Cell(60,3,'                                    ',0,0,'C');				
		$pdf->Cell(60,3,'                                    ',0,0,'C');				
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B');
		$pdf->Cell(60,3,' ',0,0,'C');				
		$pdf->Cell(60,3,'Manajer Ybs.',0,0,'C');				
		$pdf->Cell(60,3,'Atasan Langsung Ybs.',0,0,'C');				
		$pdf->Ln(15);
		
		
		$pdf->AutoPrint(true);
		
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
			case "kat":
			$text = gKategori();
			break;
			case "peg":
			$text = pegawai();
			break;
			
			case "print":
			$text = pdf();
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
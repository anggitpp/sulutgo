<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
$fFile = "files/cuti/";	

function upload($idCuti){
	global $fFile;

	$fileUpload = $_FILES["fileCuti"]["tmp_name"];
	$fileUpload_name = $_FILES["fileCuti"]["name"];
	if(($fileUpload!="") and ($fileUpload!="none")){	
		fileUpload($fileUpload,$fileUpload_name,$fFile);			
		$fileCuti = "doc-".$idCuti.".".getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileCuti);			
	}
	if(empty($fileCuti)) $fileCuti = getField("select fileCuti from att_cuti where idCuti='$idCuti'");
	
	return $fileCuti;
}
function hapusFile(){
	global $par, $fFile;

	$fileCuti = getField("select fileCuti from att_cuti where idCuti='$par[idCuti]'");
	if(file_exists($fFile.$fileCuti) and $fileCuti!="")unlink($fFile.$fileCuti);
	
	$sql="update att_cuti set fileCuti='' where idCuti='$par[idCuti]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
}

function gNomor(){
	global $inp;

	$prefix="IC";
	$date=empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
	$date=empty($date) ? date('d/m/Y') : $date;
	list($tanggal, $bulan, $tahun) = explode("/", $date);
	$nomor=getField("select nomorCuti from att_cuti where month(tanggalCuti)='$bulan' and year(tanggalCuti)='$tahun' order by nomorCuti desc limit 1");
	list($count) = explode("/", $nomor);

	return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
}

function gCuti(){
	global $inp, $par;

	$idTipe = empty($_GET[idTipe]) ? $inp[idTipe] : $_GET[idTipe];
	$tanggalCuti = empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
	$nikPegawai = empty($_GET[nikPegawai]) ? $inp[nikPegawai] : $_GET[nikPegawai];		
	$idPegawai = getField("select id from emp where reg_no='".$nikPegawai."'");
	list($tahunCuti) = explode("-", setTanggal($tanggalCuti));
	
	$sql="select * from dta_cuti where idCuti='".$idTipe."'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$getMasaKerja = getField("select timestampdiff(month, join_date, current_date) from emp where id = '$idPegawai'");
	$jumlahCuti = getField("select sum(jumlahCuti) from att_cuti where idPegawai='".$idPegawai."' and idTipe='".$idTipe."' and persetujuanCuti='t' and sdmCuti='t'");

	$jatahCuti = $r[jatahCuti] - $jumlahCuti;
	$jatahCuti = $r[masaCuti] != 0 && $r[masaCuti] > $getMasaKerja ? 0 : $jatahCuti;
	if(!empty($par[mulaiCuti]) && !empty($par[selesaiCuti])){
		$start = new DateTime(setTanggal($par[mulaiCuti]));
		$end = new DateTime(setTanggal($par[selesaiCuti]));
		$end->modify('+1 day');
		$interval = $end->diff($start);
		$days = $interval->days;

		for ($i=0 ; $i < $days ; $i++ ) {
			$no = new DateTime(setTanggal($par[mulaiCuti]));
			$no->modify('+'.$i.' day');
			$no->format('Y-m-d');
			$arrTglPilih[] = $no->format('Y-m-d');
		}

		$sql = "select * from dta_libur";
		$res = db($sql);
		while ($b = mysql_fetch_array($res)) {
			$hariLibur = $b[mulaiLibur];
			while($hariLibur <= $b[selesaiLibur]){
				$holidays[] = $hariLibur;
				$hariLibur = date('Y-m-d',strtotime($hariLibur . "+1 days"));
			}
		}

		foreach ($arrTglPilih as $key) {
			$cekDay = date('w', strtotime($key));
			if($cekDay == 6 || $cekDay == 0 || in_array($key, $holidays)){
				$days--;
			}
		}
	}
	
	return $jatahCuti."\t".$days;
}

function gPegawai(){
	global $par;

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

function sdm(){
	global $inp, $par, $cUsername;

	repField();				
	
	$sql="update att_cuti set idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', noteCuti='$inp[noteCuti]', sdmCuti='$inp[sdmCuti]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
	db($sql);
	
	echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
}

function approve(){
	global $db, $inp, $par, $cUsername, $arrParameter;

	repField();				
	
	$sql="update att_cuti set idTipe='$inp[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', catatanCuti='$inp[catatanCuti]', persetujuanCuti='$inp[persetujuanCuti]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
	db($sql);
	
	if($cell_no = getField("select cell_no from emp where id='".$inp[idPegawai]."'")){			
		if($inp[persetujuanCuti] == "t") $persetujuanCuti = "Disetujui";
		if($inp[persetujuanCuti] == "f") $persetujuanCuti = "Ditolak";
		if($inp[persetujuanCuti] == "r") $persetujuanCuti = "Diperbaiki";
		
		$message = $arrParameter[34];
		$message = str_replace("[NOMOR]", $inp[nomorCuti], $message);
		$message = str_replace("[STATUS]", $persetujuanCuti, $message);
		$message = str_replace("[NAMA]", getField("select namaUser from ".$db['setting'].".app_user where username='".$cUsername."'"), $message);
		sendSMS($cell_no, $message);
	}
	
	echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
}

function hapus(){
	global $par;

	$sql="delete from att_cuti where idCuti='$par[idCuti]'";
	db($sql);

	echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
}

function ubah(){
	global $inp, $par, $cUsername;

	repField();				

	$fileCuti=upload($par[idCuti]);
	
	$sql="update att_cuti set idTipe='$inp[idTipe]',fileCuti='$fileCuti', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', mulaiCuti='".setTanggal($inp[mulaiCuti])."', selesaiCuti='".setTanggal($inp[selesaiCuti])."', jatahCuti='".setAngka($inp[jatahCuti])."', jumlahCuti='".setAngka($inp[jumlahCuti])."', sisaCuti='".setAngka($inp[sisaCuti])."', keteranganCuti='$inp[keteranganCuti]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
	db($sql);
	
	echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
}

function tambah(){
	global $inp, $par, $cUsername;	

	$cek = getField("select idCuti from att_cuti where tanggalCuti ='".setTanggal($inp[tanggalCuti])."' AND idPegawai = '$inp[idPegawai]' AND persetujuanCuti !='t'");
	if($cek){
		echo "<script>alert('TAMBAH DATA GAGAL, DATA PEGAWAI UNTUK HARI INI SUDAH ADA');</script>";
	}else{	
		repField();		

		$idCuti = getField("select idCuti from att_cuti order by idCuti desc limit 1")+1;
		$fileCuti=upload($idCuti);		
		$arrNama = arrayQuery("select id, name from emp");
		$arrMaster = arrayQuery("select idCuti, namaCuti from dta_cuti");
		$sql="insert into att_cuti (idCuti, idTipe, idPegawai, idPengganti, idAtasan, nomorCuti, tanggalCuti, mulaiCuti, selesaiCuti, jatahCuti, jumlahCuti, sisaCuti, keteranganCuti, persetujuanCuti, sdmCuti, createBy, createTime, fileCuti) values ('$idCuti', '$inp[idTipe]', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[nomorCuti]', '".setTanggal($inp[tanggalCuti])."', '".setTanggal($inp[mulaiCuti])."', '".setTanggal($inp[selesaiCuti])."', '".setAngka($inp[jatahCuti])."', '".setAngka($inp[jumlahCuti])."', '".setAngka($inp[sisaCuti])."', '$inp[keteranganCuti]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."','$fileCuti')";
		db($sql);

		$subjek = "Pemberitahuan Rencana Cuti $inp[tanggalCuti]";	
		$link = "<a href=\"http://pratamamitra.net/hrms/index.php?c=3&p=12&m=127&s=139\"><b>DISINI</b></a>";			
		$isi1 = "
		<table width=\"100%\">
			<tr>
				<td colspan=\"3\">Sebagai informasi bahwasannya rencana Izin Cuti pada : </td> 
			</tr>
			<br>
			<tr>
				<td style=\"width:100px;\">Tanggal</td>
				<td style=\"width:10px;\">:</td>
				<td>".getTanggal($inp[tanggalCuti], "t")."</td>
			</tr>
			<tr>
				<td style=\"width:100px;\">Nomor</td>
				<td style=\"width:10px;\">:</td>
				<td><strong>".$inp[nomorCuti]."</strong></td>			
			</tr>
			<tr>
				<td style=\"width:100px;\">Tipe Cuti</td>
				<td style=\"width:10px;\">:</td>
				<td>".$arrMaster[$inp[idTipe]]."</td>
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
				<td>".getTanggal($inp[mulaiCuti], "t")." s/d ".getTanggal($inp[selesaiCuti], "t")."</td>
			</tr>					
			<tr>
				<td style=\"width:100px;\">Keterangan</td>
				<td style=\"width:10px;\">:</td>
				<td>$inp[keteranganCuti]</td>
			</tr>
		</table>
		<table style=\"width:100%\">
			<br>
			<tr>
				<td colspan=\"3\">Dimohon untuk melakukan Approval Atasan pada nomor cuti di atas, silahkan klik $link</td>
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

		// sendMail($email,$subjek,$isi1);

		echo "<script>alert('TAMBAH DATA BERHASIL');</script>";
	}
	
	echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
}

function form(){
	global $s, $par, $arrTitle, $fFile;
	
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
	
	$text.="
	<div class=\"pageheader\">
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
							<input type=\"text\" id=\"inp[nomorCuti]\" name=\"inp[nomorCuti]\"  value=\"$r[nomorCuti]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">NIK</label>
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
							".comboData("select * from dta_cuti where jatahCuti > 0 and (idLokasi='".$r__[location]."' or idLokasi='') and statusCuti='t' and '$r[tanggalCuti]' between mulaiCuti and selesaiCuti order by idCuti","idCuti","namaCuti","inp[idTipe]"," ",$r[idTipe],"onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\"", "310px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tanggal Cuti</label>
						<div class=\"field\">
							<input type=\"text\" id=\"mulaiCuti\" name=\"inp[mulaiCuti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiCuti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\" /> 
							s.d 
							<input type=\"text\" id=\"selesaiCuti\" name=\"inp[selesaiCuti]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiCuti])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getJumlah('".getPar($par,"mode, idCuti")."');\" />
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

					$text.="
					<p>
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
					
					$text.="
					<p>
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
					<p>
						<label class=\"l-input-small\">Dokumen</label>
						<div class=\"field\">";
							$text.=empty($r[fileCuti])?
							"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
							<div class=\"fakeupload\" style=\"width:300px;\">
							<input type=\"file\" id=\"fileCuti\" name=\"fileCuti\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>":
							"<a href=\"download.php?d=cuti&f=$r[idCuti]\"><img src=\"".getIcon($fFile."/".$r[fileCuti])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
							<input type=\"file\" id=\"fileCuti\" name=\"fileCuti\" style=\"display:none;\" />
							<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
							<br clear=\"all\">";
							$text.="
						</div>
					</p>
				</td>
				</tr>
			</table>";
		
			if($par[mode] == "app")
			$text.="
			<div class=\"widgetbox\">
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
			$text.="
			<div class=\"widgetbox\">
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
	global $s, $par, $arrTitle, $menuAccess, $cID, $areaCheck, $cGroup;

	$par[idPegawai] = $cID;
	if(empty($par[tahunCuti])) $par[tahunCuti]=date('Y');
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
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
						<td>".comboYear("par[tahunCuti]", $par[tahunCuti])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idCuti")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
				$text.="
			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NIK</th>
					<th rowspan=\"2\" width=\"100\">Nomor</th>
					<th colspan=\"3\" width=\"225\">Tanggal</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"30\">Bukti</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
					$text.="
				</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"75\">Mulai</th>
					<th width=\"75\">Selesai</th>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">Manager</th>
				</tr>
			</thead>
			<tbody>";
			
			$filter = "where year(t1.tanggalCuti)='$par[tahunCuti]'".($cGroup != "1" && $cGroup != "20" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."' or manager_id='".$par[idPegawai]."')" : "");
			if(!empty($par[filter]))		
			$filter.= " and (
			lower(t1.nomorCuti) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
			)";
			
			$filter .= " AND t2.location IN ($areaCheck)";
			
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
				
				$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti") : "#";			
				$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti") : "#";

				$view = empty($r[fileCuti]) ? "" : "<a href=\"#\" onclick=\"openBox('view.php?doc=fileCuti&id=$r[idCuti]',1000,500)\"><img src=\"".getIcon($r[fileCuti])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
				
				$text.="
				<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorCuti]</td>
					<td align=\"center\">".getTanggal($r[tanggalCuti])."</td>
					<td align=\"center\">".getTanggal($r[mulaiCuti])."</td>
					<td align=\"center\">".getTanggal($r[selesaiCuti])."</td>					
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanCuti</a></td>
					<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmCuti</a></td>
					<td align=\"center\">$view</td>
					<td align=\"center\">
					<a href=\"?par[mode]=det&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
						$text.="<td align=\"center\">
						<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."',900,500);\" ><span>Cetak</span></a>";				
						if(isset($menuAccess[$s]["edit"])&&$r[persetujuanCuti]!='t') $text.="<a href=\"?par[mode]=edit&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
						if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						$text.="</td>";
					}
					$text.="
				</tr>";
			}	
			
			$text.="
			</tbody>
		</table>
	</div>";
	return $text;
}		

function detail(){
	global $s, $par, $arrTitle, $cID;

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
	
	$text.="
	<div class=\"pageheader\">
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
						<span class=\"field\">".$r[nomorCuti]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">NIK</label>
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
					$text.="
					<p>
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
					
					$text.="
					<p>
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
			
			$text.="
			<div class=\"widgetbox\">
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
			
			$text.="
			<div class=\"widgetbox\">
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
			
			$text.="
		</div>
		<p>					
			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\"/>		
		</p>
		</form>
	</div>";

	return $text;
}

function pegawai(){
	global $par, $cID, $cGroup, $areaCheck;

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
							<td>".comboArray("par[search]", array("All", "Nama", "NIK"), $par[search])."</td>
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
						<th width=\"100\">NIK</th>
						<th style=\"min-width:150px;\">Jabatan</th>					
						<th style=\"min-width:150px;\">Divisi</th>
						<th width=\"50\">Kontrol</th>
					</tr>
				</thead>
				<tbody>";
				
				if($cGroup != 1 && $cGroup != 20)
				$filter = "where reg_no is not null and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
				else
				$filter = "where reg_no is not null";
				
				if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NIK")
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
					
					$text.="
					<tr>
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
				
				$text.="
				</tbody>
			</table>
		</div>
	</div>";

	return $text;
}

function pdf(){
	global $par;
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
	$pdf->Image("images/info/logo-1.png", $pdf->GetX() + 0, $pdf->GetY() + 0, 45, 10);
	$pdf->Ln();
	
	$pdf->SetFont('Arial','BU',14);
	$pdf->Cell(180,6,'PERMOHONAN CUTI / IZIN',0,0,'C');
	$pdf->Ln();
	
	$pdf->SetFont('Arial','BI',10);
	$pdf->Cell(180,6,'LEAVE APPLICATION / PERMISSION',0,0,'C');
	$pdf->Ln();
	
	$pdf->SetFont('Arial','B');
	$pdf->Cell(180,6,'Nomor : '.$r[nomorCuti],0,0,'C');
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
	$pdf->Cell(50,3,'NIK Karyawan',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Employees NIK',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');
	$pdf->Cell(5,6,':',0,0,'L');		
	$pdf->MultiCell(125,6,getField("select reg_no from emp where id='".$r[idPegawai]."'"),0,'L');
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
	$pdf->Cell(50,3,'Departemen',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Department',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');
	$pdf->Cell(5,6,':',0,0,'L');		
	$pdf->MultiCell(125,6,getField("select t2.namaData from emp_phist t1 join mst_data t2 on (t1.dept_id=t2.kodeData) where t1.parent_id='".$r[idPegawai]."' and status='1'"),0,'L');
	$pdf->Ln(3.5);
	
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Tipe Cuti',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Type of leave',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');
	$pdf->Cell(5,6,':',0,0,'L');		
	$pdf->MultiCell(125,6,getField("select namaCuti from dta_cuti where idCuti='".$r[idTipe]."'"),0,'L');
	$pdf->Ln(3.5);
	
	
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Jumlah Hak Cuti',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Sum of the leave rights',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');		
	$pdf->Cell(5,6,':',0,0,'L');
	$pdf->MultiCell(10,6,getAngka($r[jatahCuti]),0,'R');		
	$pdf->SetXY($setX+70, $setY);		
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(10,3,'Hari',0,0,'L');		
	$pdf->SetXY($setX+70, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(10,6,'Day',0,0,'L');				
	$pdf->Ln(8);
	
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Jumlah Cuti Diambil',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Sum of leave in taken',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');		
	$pdf->Cell(5,6,':',0,0,'L');
	$pdf->MultiCell(10,6,getAngka($r[jumlahCuti]),0,'R');		
	$pdf->SetXY($setX+70, $setY);		
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(10,3,'Hari',0,0,'L');		
	$pdf->SetXY($setX+70, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(10,6,'Day',0,0,'L');				
	$pdf->Ln(8);
	
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Sisa Cuti',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Rest of leave',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');		
	$pdf->Cell(5,6,':',0,0,'L');
	$pdf->MultiCell(10,6,getAngka($r[sisaCuti]),0,'R');		
	$pdf->SetXY($setX+70, $setY);		
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(10,3,'Hari',0,0,'L');		
	$pdf->SetXY($setX+70, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(10,6,'Day',0,0,'L');				
	$pdf->Ln(8);
				
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Tanggal Awal Cuti',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Date of early leave',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');		
	$pdf->Cell(5,6,':',0,0,'L');
	$pdf->MultiCell(125,6,$tanggalCuti,0,'L');				
	$pdf->Ln(3.5);
	
	$setX = $pdf->GetX();
	$setY = $pdf->GetY();
	$pdf->SetFont('Arial','BU');
	$pdf->Cell(50,3,'Alasan Cuti',0,0,'L');		
	$pdf->SetXY($setX, $setY+2);
	$pdf->SetFont('Arial','I');
	$pdf->Cell(50,6,'Reason of leave',0,0,'L');		
	$pdf->SetXY($setX+50, $setY);
	$pdf->SetFont('Arial');
	$pdf->Cell(5,6,':',0,0,'L');		
	$pdf->MultiCell(125,6,$r[keteranganCuti],0,'L');
	$pdf->Ln(20);
	
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
	$pdf->Cell(60,5,getField("select name from emp where id='".$r[idAtasan]."'"),0,0,'C');
	$pdf->Cell(60,5,getField("select name from emp t1 join app_user t2 on t1.id = t2.idPegawai where t2.username='".$r[sdmBy]."'"),0,0,'C');
	$pdf->Cell(60,5,getField("select name from emp where id='".$r[idPegawai]."'"),0,0,'C');
	$pdf->Ln();
	$pdf->SetFont('Arial');
	$pdf->Cell(60,3,getField("select pos_name from emp_phist where parent_id='".$r[idAtasan]."' AND status = '1'"),0,0,'C');
	$pdf->Cell(60,3,getField("select pos_name from emp_phist t1 join app_user t2 on t1.parent_id = t2.idPegawai where t2.username='".$r[sdmBy]."' AND t1.status = '1'"),0,0,'C');
	$pdf->Cell(60,3,getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' AND status = '1'"),0,0,'C');
	
	$pdf->Ln();
	$pdf->Cell(60,6,'Tgl/Date :                            ',0,0,'C');
	$pdf->Cell(60,6,'Tgl/Date :                 ',0,0,'C');
	$pdf->Cell(60,6,'Tgl/Date :  '.getTanggal($r[tanggalCuti],"t"),0,0,'C');
	$pdf->AutoPrint(true);
	
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
		case "delFile":
			if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
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
		default:
			$text = lihat();
		break;
	}

	return $text;
}	
?>

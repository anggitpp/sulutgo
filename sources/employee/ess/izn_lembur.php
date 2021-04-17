<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}

	$fFile = "files/lembur/";

	function upload($idLembur){
		global $db,$s,$inp,$par,$fFile;
		$fileUpload = $_FILES["fileLembur"]["tmp_name"];
		$fileUpload_name = $_FILES["fileLembur"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileLembur = "doc-".$idLembur.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileLembur);			
		}
		if(empty($fileLembur)) $fileLembur = getField("select fileLembur from att_lembur where idLembur='$idLembur'");
		
		return $fileLembur;
	}
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileHadir = getField("select fileLembur from att_lembur where idLembur='$par[idLembur]'");
		if(file_exists($fFile.$fileLembur) and $fileLembur!="")unlink($fFile.$fileLembur);
		
		$sql="update att_lembur set fileLembur='' where idLembur='$par[idLembur]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}	
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="SPL";
		$date=empty($_GET[tanggalLembur]) ? $inp[tanggalLembur] : $_GET[tanggalLembur];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorLembur from att_lembur where month(tanggalLembur)='$bulan' and year(tanggalLembur)='$tahun' order by nomorLembur desc limit 1");
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
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from att_lembur where idLembur='$par[idLembur]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idLembur")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();	
		
		$fileLembur=upload($par[idLembur]);
		
		$mulaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[mulaiLembur];
		$selesaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[selesaiLembur];
		
		$sql="update att_lembur set idPegawai='$inp[idPegawai]', idAtasan='$inp[idAtasan]', nomorLembur='$inp[nomorLembur]', tanggalLembur='".setTanggal($inp[tanggalLembur])."', mulaiLembur='".$mulaiLembur."', selesaiLembur='".$selesaiLembur."', keteranganLembur='$inp[keteranganLembur]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idLembur='$par[idLembur]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idLembur")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();				
		$idLembur = getField("select idLembur from att_lembur order by idLembur desc limit 1")+1;		

		$fileLembur=upload($idLembur);			
		
		$mulaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[mulaiLembur];
		$selesaiLembur = setTanggal($inp[mulaiLembur_tanggal])." ".$inp[selesaiLembur];
		
		$sql="insert into att_lembur (fileLembur, idLembur, idPegawai, idAtasan, nomorLembur, tanggalLembur, mulaiLembur, selesaiLembur, keteranganLembur, persetujuanLembur, sdmLembur, createBy, createTime) values ('$fileLembur','$idLembur', '$inp[idPegawai]', '$inp[idAtasan]', '$inp[nomorLembur]', '".setTanggal($inp[tanggalLembur])."', '".$mulaiLembur."', '".$selesaiLembur."', '$inp[keteranganLembur]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idLembur")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
		if(empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		
		setValidation("is_null","inp[nomorLembur]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalLembur","anda harus mengisi tanggal");
		setValidation("is_null","mulaiLembur_tanggal","anda harus mengisi tanggal");
		setValidation("is_null","mulaiLembur","anda harus mengisi waktu");
		setValidation("is_null","selesaiLembur","anda harus mengisi waktu");		
		setValidation("is_null","inp[idAtasan]","anda harus mengisi atasan");
		$text = getValidation();
		
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;						
		if(empty($r[idAtasan])) $r[idAtasan] = getField("select leader_id from dta_pegawai where id='".$r[idPegawai]."'");
		
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
		<input type=\"text\" id=\"inp[nomorLembur]\" name=\"inp[nomorLembur]\"  value=\"$r[nomorLembur]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
		<input type=\"text\" id=\"tanggalLembur\" name=\"inp[tanggalLembur]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalLembur])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN LEMBUR</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\" style=\"vertical-align:top;\">
		<p>
		<label class=\"l-input-small\">Tanggal</label>
		<div class=\"field\">
		<input type=\"text\" id=\"mulaiLembur_tanggal\" name=\"inp[mulaiLembur_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($mulaiLembur_tanggal)."\" onchange=\"cekTanggal();\" class=\"vsmallinput hasDatePicker\"/>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Waktu</label>
		<div class=\"field\">
		<input type=\"text\" id=\"mulaiLembur\" name=\"inp[mulaiLembur]\" size=\"10\" maxlength=\"5\" value=\"".substr($mulaiLembur,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/> s.d 
		<input type=\"text\" id=\"selesaiLembur\" name=\"inp[selesaiLembur]\" size=\"10\" maxlength=\"5\" value=\"".substr($selesaiLembur,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
		</div>
		</p>						
		</td>
		<td width=\"55%\" style=\"vertical-align:top;\">";
		
		$sql_="select
		id as idAtasan,
		reg_no as nikAtasan,
		name as namaAtasan
		from emp where id='".$r[idAtasan]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
		$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
		
		list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
		$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
		$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
		
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
		<textarea id=\"inp[keteranganLembur]\" name=\"inp[keteranganLembur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[keteranganLembur]</textarea>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Dokumen</label>
		<div class=\"field\">";
		$text.=empty($r[fileLembur])?
		"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
		<div class=\"fakeupload\" style=\"width:300px;\">
		<input type=\"file\" id=\"fileLembur\" name=\"fileLembur\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
		</div>":
		"<a href=\"download.php?d=fileLembur&f=$r[idLembur]\"><img src=\"".getIcon($fFile."/".$r[fileLembur])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
		<input type=\"file\" id=\"fileLembur\" name=\"fileLembur\" style=\"display:none;\" />
		<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
		<br clear=\"all\">";
		$text.="</div>
		</p>
		</td>
		</tr>
		</table>
		<div style=\"float:right; margin-top:10px;\">
		<table width=\"100%\">
		<tr>						
		<td>
		<table>
		<tr>
		<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
		<td>".$persetujuanLembur." ".$approveTime."</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		</div>
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
		if(empty($par[tahunLembur])) $par[tahunLembur]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		
		echo "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
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
		<td>".comboYear("par[tahunLembur]", $par[tahunLembur])."</td>
		<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
		</tr>
		</table>
		</div>
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idLembur")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>					
		<th rowspan=\"2\" style=\"min-width:150px;\">Nomor</th>
		<th rowspan=\"2\" width=\"75\">Mulai</th>
		<th rowspan=\"2\" width=\"75\">Selesai</th>
		<th rowspan=\"2\" width=\"75\">Tanggal</th>
		<th colspan=\"2\" width=\"50\">Approval</th>
		<th rowspan=\"2\" width=\"30\">Bukti</th>
		<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
		<tr>
		<th width=\"50\">Atasan</th>
		<th width=\"50\">MANAGER</th>
		</tr>
		</thead>
		<tbody>";
		
		$filter = "where year(t1.tanggalLembur)='$par[tahunLembur]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from att_lembur t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanLembur;
			
			$sdmLembur = $r[sdmLembur] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmLembur = $r[sdmLembur] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmLembur;
			
			list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
			list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
			$view = empty($r[fileLembur]) ? "" : "<a href=\"#\" onclick=\"openBox('view.php?doc=fileLembur&id=$r[idLembur]',1000,500)\"><img src=\"".getIcon($r[fileLembur])."\" align=\"center\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
			
			$text.="<tr>
			<td>$no.</td>					
			<td>$r[nomorLembur]</td>
			<td align=\"center\">".substr($mulaiLembur,0,5)."</td>
			<td align=\"center\">".substr($selesaiLembur,0,5)."</td>
			<td align=\"center\">".getTanggal($mulaiLembur_tanggal)."</td>
			<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."',750,425);\" >$persetujuanLembur</a></td>
			<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."',750,425);\" >$sdmLembur</a></td>
			<td align=\"center\">$view</td>";			
			$text.="<td align=\"center\">
			<a href=\"#\" class=\"print\" title=\"Cetak Form\" onclick=\"openBox('ajax.php?par[mode]=print&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."',900,500);\" ><span>Cetak</span></a>
			<a href=\"?par[mode]=det&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";		
			if(in_array($r[persetujuanLembur], array("p","r")) || in_array($r[sdmLembur], array("p","r")))
			if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
			
			if(in_array($r[persetujuanLembur], array("p")) || in_array($r[sdmLembur], array("p")))
			if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idLembur]=$r[idLembur]".getPar($par,"mode,idLembur")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			$text.="</td>";			
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
		</table>
		</div>";
		return $text;
	}		
	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			
		
		$persetujuanTitle = $par[mode] == "detSdm" ? "Approval Manager" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmLembur" : "persetujuanLembur";
		$catatanField = $par[mode] == "detSdm" ? "noteLembur" : "catatanLembur";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";		
		
		$persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranLembur" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiLembur" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);		
		$persetujuanLembur = "Belum Diproses";
		$persetujuanLembur = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanLembur;
		$persetujuanLembur = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanLembur;	
		$persetujuanLembur = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanLembur;	
		
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
		<span class=\"field\">".$persetujuanLembur."&nbsp;</span>
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
		if(empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		
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
		<span class=\"field\">".$r[nomorLembur]."&nbsp;</span>
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
		<span class=\"field\">".getTanggal($r[tanggalLembur],"t")."&nbsp;</span>
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
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN LEMBUR</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\" style=\"vertical-align:top\">
		<p>
		<label class=\"l-input-small\">Tanggal</label>
		<span class=\"field\">".getTanggal($mulaiLembur_tanggal,"t")."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Waktu</label>
		<span class=\"field\">".substr($mulaiLembur,0,5)." <strong>s.d</strong> ".substr($selesaiLembur,0,5)."&nbsp;</span>
		</p>						
		</td>
		<td width=\"55%\" style=\"vertical-align:top\">";
		
		$sql_="select
		id as idAtasan,
		reg_no as nikAtasan,
		name as namaAtasan
		from emp where id='".$r[idAtasan]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
		$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
		$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanLembur;
		
		list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
		$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
		$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
		
		$text.="<p>
		<label class=\"l-input-small\">Atasan</label>
		<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
		</p>						
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[keteranganLembur])."&nbsp;</span>
		</p>
		</td>
		</tr>
		</table>";
		
		$persetujuanLembur = "Belum Diproses";
		$persetujuanLembur = $r[persetujuanLembur] == "t" ? "Disetujui" : $persetujuanLembur;
		$persetujuanLembur = $r[persetujuanLembur] == "f" ? "Ditolak" : $persetujuanLembur;		
		$persetujuanLembur = $r[persetujuanLembur] == "r" ? "Diperbaiki" : $persetujuanLembur;		
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
		<span class=\"field\">".$persetujuanLembur."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[catatanLembur])."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Overtime</label>
		<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
		</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
		</tr>
		</table>";					
		
		$sdmLembur = "Belum Diproses";
		$sdmLembur = $r[sdmLembur] == "t" ? "Disetujui" : $sdmLembur;
		$sdmLembur = $r[sdmLembur] == "f" ? "Ditolak" : $sdmLembur;
		$sdmLembur = $r[sdmLembur] == "r" ? "Diperbaiki" : $sdmLembur;
		list($sdmDate) = explode(" ", $r[sdmTime]);
		
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL MANAGER</h3></div>
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
		<span class=\"field\">".$sdmLembur."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<span class=\"field\">".nl2br($r[noteLembur])."&nbsp;</span>
		</p>
		<p>
		<label class=\"l-input-small\">Overtime</label>
		<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
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
	
	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select *, CASE WHEN time(selesaiLembur) >= time(mulaiLembur) THEN TIMEDIFF(time(selesaiLembur), time(mulaiLembur)) ELSE ADDTIME(TIMEDIFF('24:00:00', time(mulaiLembur)), TIMEDIFF(time(selesaiLembur), '00:00:00')) END as durasiLembur from att_lembur where idLembur='$par[idLembur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		list($Y,$m,$d) = explode("-", $mulaiLembur_tanggal);
		$hariLembur = $arrHari[date('w', mktime(0,0,0,$m,$d,$Y))];		
		
		if($selesaiLembur < $mulaiLembur){

			$selisih = selisihMenit2(substr($mulaiLembur, 0,5), substr($selesaiLembur, 0,5))/60;
	
			$r[overtimeLembur] = (24 - substr($mulaiLembur, 0,2)) + substr($selesaiLembur, 1,1);
	
			}else{
	
			$r[overtimeLembur] = selisihMenit2(substr($mulaiLembur, 0,5), substr($selesaiLembur, 0,5))/60;
			if($r[overtimeLembur] < 10){
				$r[overtimeLembur] = substr($r[overtimeLembur], 0, 1);
			}else{
				$r[overtimeLembur] = substr($r[overtimeLembur], 0, 2);
			}
			}
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(15);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',12);					
		$pdf->Cell(50,6,'PRATAMA MITRA SEJATI',0,0,'L');
		
		$pdf->Ln();
		
		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(180,6,'SURAT PERINTAH KERJA LEMBUR',0,0,'C');
		$pdf->Ln();
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(180,6,'No. SPL : '.$r[nomorLembur],0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(180,2,'Kepada karyawan yang namanya tersebut dibawah ini diperintahkan kerja lembur',0,0,'L');		
		$pdf->Ln();		
		$pdf->SetFont('Arial','I');
		$pdf->Cell(180,6,'To the employees whose name is noted below working overtime',0,0,'L');		
		$pdf->Ln(10);
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Untuk keperluan/Tugas',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'For/Duty',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,$r[keteranganLembur],0,'L');
		$pdf->Ln(3);		
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Pada Hari/Tanggal',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Day/Date',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');
		$pdf->Cell(5,6,':',0,0,'L');		
		$pdf->MultiCell(125,6,$hariLembur.", ".getTanggal($mulaiLembur_tanggal,"t"),0,'L');
		$pdf->Ln();
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Dimulai Jam',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Start Form',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(15,6,substr($mulaiLembur,0,5),0,'L');		
		$pdf->SetXY($setX+70, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'s/d',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Up to',0,0,'L');				
		$pdf->SetXY($setX+85, $setY);
		$pdf->SetFont('Arial');
		$pdf->MultiCell(15,6,substr($selesaiLembur,0,5),0,'L');		
		$pdf->Ln();
		
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(50,3,'Jumlah Jam',0,0,'L');		
		$pdf->SetXY($setX, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(50,6,'Number of hours',0,0,'L');		
		$pdf->SetXY($setX+50, $setY);
		$pdf->SetFont('Arial');		
		$pdf->Cell(5,6,':',0,0,'L');
		$pdf->MultiCell(15,6,$r[overtimeLembur],0,'L');		
		$pdf->SetXY($setX+70, $setY);
		$pdf->SetFont('Arial','BU');
		$pdf->Cell(10,3,'Jam',0,0,'L');		
		$pdf->SetXY($setX+70, $setY+2);
		$pdf->SetFont('Arial','I');
		$pdf->Cell(10,6,'Hours',0,0,'L');				
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B');
		$pdf->Cell(10,10,'No',1,0,'C');
		$setX = $pdf->GetX();
		$setY = $pdf->GetY();
		$pdf->Cell(80,10,'',1,0,'C');
		$pdf->Cell(60,10,'',1,0,'C');
		$pdf->Cell(30,10,'',1,0,'C');
		$pdf->SetFont('Arial','BU');
		
		$pdf->SetXY($setX, $setY+1);	
		$pdf->Cell(80,5,'Nama',0,0,'C');
		$pdf->Cell(60,5,'Jabatan',0,0,'C');
		$pdf->Cell(30,5,'Tanda Tangan',0,0,'C');
		$pdf->Ln();
		$pdf->SetXY($setX, $setY+4.5);	
		$pdf->SetFont('Arial','I');
		$pdf->Cell(80,5,'Name',0,0,'C');
		$pdf->Cell(60,5,'Position',0,0,'C');
		$pdf->Cell(30,5,'Signature',0,0,'C');
		$pdf->Ln(5.5);		
		
		$sql_="select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);		
		$pdf->SetFont('Arial');
		$pdf->SetAligns(array('C','L','L','L'));
		$pdf->SetWidths(array(10,80,60,30));
		$pdf->Cols(array(
		array("1.",
		getField("select name from emp where id='".$r[idPegawai]."'"),
		getField("select pos_name from emp_phist where parent_id='".$r[idPegawai]."' and status='1'"),
		"")
		),10);
		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial','I');
		$pdf->Cell(90,6,'Requested by,',0,0,'C');
		$pdf->Cell(90,6,'Approved by,',0,0,'C');
		
		$pdf->Ln();
		$pdf->SetFont('Arial','B');
		$pdf->Cell(90,3,'Diajukan Oleh,',0,0,'C');
		$pdf->Cell(90,3,'Menyetujui,',0,0,'C');
		
		$pdf->Ln(20);		
		$pdf->Cell(90,5,'                                           ',0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B');
		$pdf->Cell(90,5,getField("select name from emp t1 join emp_phist t2 on (t1.id = t2.leader_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "),0,0,'C');
		$pdf->Cell(90,5,getField("select name from emp t1 join emp_phist t2 on (t1.id = t2.manager_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "),0,0,'C');
		$pdf->Ln(3);
		$pdf->SetFont('Arial','U');
		$pdf->Cell(90,3,'                                            ',0,0,'C');				
		$pdf->Cell(90,3,'                                            ',0,0,'C');	
		$pdf->Ln(5);
		$pdf->SetFont('Arial');
		$pdf->Cell(90,3,getField("select pos_name from emp t1 join emp_phist t2 on (t1.id = t2.leader_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "),0,0,'C');
		$pdf->Cell(90,3,getField("select pos_name from emp t1 join emp_phist t2 on (t1.id = t2.manager_id AND t2.status = 1) where t2.parent_id = '$r[idPegawai]' "),0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B');
		$pdf->Cell(90,6,'Date/Tgl :                           ',0,0,'C');
		$pdf->Cell(90,6,'Date/Tgl :                           ',0,0,'C');
		
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
			
			case "print":
			$text = pdf();
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

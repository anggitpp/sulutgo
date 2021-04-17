<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="KCT";
		$date=empty($_GET[tanggalCuti]) ? $inp[tanggalCuti] : $_GET[tanggalCuti];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorCuti from ess_cuti where month(tanggalCuti)='$bulan' and year(tanggalCuti)='$tahun' order by nomorCuti desc limit 1");
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
		$data["tanggalPegawai"] = getTanggal($r[join_date]);
		
		$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$data["namaJabatan"] = $r_[pos_name];
		$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");		
		
		return json_encode($data);
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from ess_cuti where idCuti='$par[idCuti]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$inp[uangCuti] = $inp[uangCuti] == "t" ? "t" : "f";
		$inp[ambilCuti] = $inp[ambilCuti] == "t" ? "t" : "f";
		
		$sql="update ess_cuti set idPegawai='$inp[idPegawai]', nomorCuti='$inp[nomorCuti]', tanggalCuti='".setTanggal($inp[tanggalCuti])."', namaCuti='$inp[namaCuti]', uangCuti='$inp[uangCuti]', ambilCuti='$inp[ambilCuti]', keteranganCuti='$inp[keteranganCuti]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idCuti='$par[idCuti]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();				
		$idCuti = getField("select idCuti from ess_cuti order by idCuti desc limit 1")+1;		
				
		$inp[uangCuti] = $inp[uangCuti] == "t" ? "t" : "f";
		$inp[ambilCuti] = $inp[ambilCuti] == "t" ? "t" : "f";
				
		$sql="insert into ess_cuti (idCuti, idPegawai, nomorCuti, tanggalCuti, namaCuti, uangCuti, ambilCuti, keteranganCuti, persetujuanCuti, sdmCuti, createBy, createTime) values ('$idCuti', '$inp[idPegawai]', '$inp[nomorCuti]', '".setTanggal($inp[tanggalCuti])."', '$inp[namaCuti]', '$inp[uangCuti]', '$inp[ambilCuti]', '$inp[keteranganCuti]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idCuti")."';</script>";
	}
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from ess_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
		if(empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');			
		
		$uangCuti = $r[uangCuti] == "t" ? "checked=\"checked\""  : "";
		$ambilCuti = $r[ambilCuti] == "t" ? "checked=\"checked\""  : "";
		
		setValidation("is_null","inp[nomorCuti]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalCuti","anda harus mengisi tanggal");		
		setValidation("is_null","inp[namaCuti]","anda harus mengisi judul");
		$text = getValidation();
		
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
		
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai,
			join_date as tanggalPegawai
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
								<input type=\"text\" id=\"inp[nomorCuti]\" name=\"inp[nomorCuti]\"  value=\"$r[nomorCuti]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
							<label class=\"l-input-small\">Tanggal Masuk</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalPegawai\" name=\"inp[tanggalPegawai]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r_[tanggalPegawai])."\" class=\"vsmallinput hasDatePicker\" disabled/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Judul</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaCuti]\" name=\"inp[namaCuti]\"  value=\"$r[namaCuti]\" class=\"mediuminput\" style=\"width:300px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Fasilitas</label>
							<div class=\"fradio\">								
								<input type=\"checkbox\" id=\"inp[uangCuti]\" name=\"inp[uangCuti]\"  value=\"t\" $uangCuti /> Uang<br>
								<input type=\"checkbox\" id=\"inp[ambilCuti]\" name=\"inp[ambilCuti]\"  value=\"t\" $ambilCuti/> Cuti
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganCuti]\" name=\"inp[keteranganCuti]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganCuti]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">
						&nbsp;
					</td>
					</tr>
					</table>					
				</div>
				<p>					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
			
			if(!empty($cID))
			$text.="<script>
						getPegawai('".getPar($par,"mode,nikPegawai")."');
					</script>";
			
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		if(empty($par[tahunCuti])) $par[tahunCuti]=date('Y');
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
				<td>".comboYear("par[tahunCuti]", $par[tahunCuti])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idCuti")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>									
					<th rowspan=\"2\" width=\"100\">Nomor</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Judul</th>					
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"50\">Bayar</th>
					<th rowspan=\"2\" width=\"50\">Kontrol</th>
				</tr>
				<tr>					
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where year(t1.tanggalCuti)='$par[tahunCuti]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorCuti) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from ess_cuti t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorCuti";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanCuti = $r[persetujuanCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanCuti = $r[persetujuanCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanCuti;
			$persetujuanCuti = $r[persetujuanCuti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanCuti;
			
			$sdmCuti = $r[sdmCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmCuti = $r[sdmCuti] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmCuti;
			$sdmCuti = $r[sdmCuti] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmCuti;
			
			$pembayaranCuti = $r[pembayaranCuti] == "t"? "<img src=\"styles/images/t.png\" title=\"Sudah Dibayar\">" : "<img src=\"styles/images/f.png\" title=\"Belum Dibayar\">";
			
			$text.="<tr>
					<td>$no.</td>					
					<td>$r[nomorCuti]</td>
					<td align=\"center\">".getTanggal($r[tanggalCuti])."</td>
					<td>$r[namaCuti]</td>					
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detAts&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."',750,425);\" >$persetujuanCuti</a></td>
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."',750,425);\" >$sdmCuti</a></td>
					<td align=\"center\">$pembayaranCuti</td>";
			
				$text.="<td align=\"center\">
				<a href=\"?par[mode]=det&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";				
				
				if(in_array($r[persetujuanCuti], array("p","r")) || in_array($r[sdmCuti], array("p","r")))
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
			
				if(in_array($r[persetujuanCuti], array("p")) || in_array($r[sdmCuti], array("p")))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idCuti]=$r[idCuti]".getPar($par,"mode,idCuti")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
		
		$sql="select * from ess_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$persetujuanTitle = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmCuti" : "persetujuanCuti";
		$catatanField = $par[mode] == "detSdm" ? "noteCuti" : "catatanCuti";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";		
		
		$persetujuanTitle = $par[mode] == "detPay" ? "Pembayaran" : $persetujuanTitle;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranCuti" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiCuti" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "padiTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);		
		$persetujuanCuti = "Belum Diproses";
		$persetujuanCuti = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanCuti;
		$persetujuanCuti = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanCuti;	
		$persetujuanCuti = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanCuti;	
		
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
						<span class=\"field\">".$persetujuanCuti."&nbsp;</span>
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
		
		$sql="select * from ess_cuti where idCuti='$par[idCuti]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorCuti])) $r[nomorCuti] = gNomor();
		if(empty($r[tanggalCuti])) $r[tanggalCuti] = date('Y-m-d');
		
		$fasilitasCuti = array();
		if($r[uangCuti] == "t") $fasilitasCuti[]= "Uang";
		if($r[ambilCuti] == "t") $fasilitasCuti[]= "Cuti";		
		$fasilitasCuti = implode(", ", $fasilitasCuti);
		
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai,
			join_date as tanggalPegawai
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI 5 TAHUN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal Masuk</label>
							<span class=\"field\">".getTanggal($r_[tanggalPegawai],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Judul</label>
							<span class=\"field\">".$r[namaCuti]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Fasilitas</label>
							<span class=\"field\">".$fasilitasCuti."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganCuti])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						&nbsp;
					</td>
					</tr>
					</table>";
			$persetujuanCuti = "Belum Diproses";
			$persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
			$persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;	
			$persetujuanCuti = $r[persetujuanCuti] == "r" ? "Diperbaiki" : $persetujuanCuti;	
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
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idCuti")."';\" style=\"float:right;\"/>		
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
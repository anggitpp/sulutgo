<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IZ";
		$date=empty($_GET[tanggalIzin]) ? $inp[tanggalIzin] : $_GET[tanggalIzin];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorIzin from att_izin where month(tanggalIzin)='$bulan' and year(tanggalIzin)='$tahun' order by nomorIzin desc limit 1");
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
		
		$data["idPengganti"] = $r_[replacement_id];
		$data["idAtasan"] = $r_[leader_id];
		
		list($data[nikPengganti], $data[namaPengganti]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[replacement_id]."'"));
		list($data[nikAtasan], $data[namaAtasan]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[leader_id]."'"));
		
		return json_encode($data);
	}	
		
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[mulaiIzin];
		$selesaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[selesaiIzin];
		
		$sql="update att_izin set idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorIzin='$inp[nomorIzin]', tanggalIzin='".setTanggal($inp[tanggalIzin])."', mulaiIzin='".$mulaiIzin."', selesaiIzin='".$selesaiIzin."', keteranganIzin='$inp[keteranganIzin]', catatanIzin='$inp[catatanIzin]', persetujuanIzin='$inp[persetujuanIzin]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idIzin='$par[idIzin]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idIzin")."';</script>";
	}	
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from att_izin where idIzin='$par[idIzin]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idIzin")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$mulaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[mulaiIzin];
		$selesaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[selesaiIzin];
		
		$sql="update att_izin set idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorIzin='$inp[nomorIzin]', tanggalIzin='".setTanggal($inp[tanggalIzin])."', mulaiIzin='".$mulaiIzin."', selesaiIzin='".$selesaiIzin."', keteranganIzin='$inp[keteranganIzin]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idIzin='$par[idIzin]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idIzin")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();				
		$idIzin = getField("select idIzin from att_izin order by idIzin desc limit 1")+1;		
		
		$mulaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[mulaiIzin];
		$selesaiIzin = setTanggal($inp[mulaiIzin_tanggal])." ".$inp[selesaiIzin];
		
		$sql="insert into att_izin (idIzin, idPegawai, idPengganti, idAtasan, nomorIzin, tanggalIzin, mulaiIzin, selesaiIzin, keteranganIzin, persetujuanIzin, createBy, createTime) values ('$idIzin', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[nomorIzin]', '".setTanggal($inp[tanggalIzin])."', '".$mulaiIzin."', '".$selesaiIzin."', '$inp[keteranganIzin]', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idIzin")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_izin where idIzin='$par[idIzin]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[persetujuanIzin] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanIzin] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanIzin] == "r" ? "checked=\"checked\"" : "";
		
		if(empty($r[nomorIzin])) $r[nomorIzin] = gNomor();
		if(empty($r[tanggalIzin])) $r[tanggalIzin] = date('Y-m-d');
		list($mulaiIzin_tanggal, $mulaiIzin) = explode(" ", $r[mulaiIzin]);
		list($selesaiIzin_tanggal, $selesaiIzin) = explode(" ", $r[selesaiIzin]);
		
		setValidation("is_null","inp[nomorIzin]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalIzin","anda harus mengisi tanggal");
		setValidation("is_null","mulaiIzin_tanggal","anda harus mengisi tanggal");
		setValidation("is_null","mulaiIzin","anda harus mengisi waktu");
		setValidation("is_null","selesaiIzin","anda harus mengisi waktu");
		setValidation("is_null","inp[idPengganti]","anda harus mengisi pengganti");
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
								<input type=\"text\" id=\"inp[nomorIzin]\" name=\"inp[nomorIzin]\"  value=\"$r[nomorIzin]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalIzin\" name=\"inp[tanggalIzin]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalIzin])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SEMENTARA</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiIzin_tanggal\" name=\"inp[mulaiIzin_tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($mulaiIzin_tanggal)."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiIzin\" name=\"inp[mulaiIzin]\" size=\"10\" maxlength=\"5\" value=\"".substr($mulaiIzin,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/> s.d 
								<input type=\"text\" id=\"selesaiIzin\" name=\"inp[selesaiIzin]\" size=\"10\" maxlength=\"5\" value=\"".substr($selesaiIzin,0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganIzin]\" name=\"inp[keteranganIzin]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganIzin]</textarea>
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
					
					$persetujuanIzin = $r[persetujuanIzin] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
					$persetujuanIzin = $r[persetujuanIzin] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanIzin;
					$persetujuanIzin = $r[persetujuanIzin] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanIzin;
					
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
					</td>
					</tr>
					</table>
					<div style=\"float:right; margin-top:-30px;\">
						<table width=\"100%\">
						<tr>
						<td>Tahun ini sudah pernah melakukan izin : <strong>".getField("select count(*) from att_izin where idPegawai='$r[idPegawai]' and persetujuanIzin='t'")." Kali</strong></td>
						<td>
							<table>
							<tr>
							<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
							<td>".$persetujuanIzin." ".$approveTime."</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
					</div>";
					
			if($par[mode] == "app")
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[persetujuanIzin]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[persetujuanIzin]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[persetujuanIzin]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[catatanIzin]\" name=\"inp[catatanIzin]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanIzin]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>					
				</div>";
		$text.="<p>				
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;
		if(empty($par[tahunIzin])) $par[tahunIzin]=date('Y');
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>				
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>".comboYear("par[tahunIzin]", $par[tahunIzin])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idIzin")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Nomor</th>
					<th width=\"75\">Mulai</th>
					<th width=\"75\">Selesai</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"50\">Approval</th>
					<th width=\"50\">Detail</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter = "where year(t1.tanggalIzin)='$par[tahunIzin]' and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorIzin) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from att_izin t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorIzin";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanIzin = $r[persetujuanIzin] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanIzin = $r[persetujuanIzin] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanIzin;
			$persetujuanIzin = $r[persetujuanIzin] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanIzin;
			
			list($mulaiIzin_tanggal, $mulaiIzin) = explode(" ", $r[mulaiIzin]);
			list($selesaiIzin_tanggal, $selesaiIzin) = explode(" ", $r[selesaiIzin]);
			
			$persetujuanLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=app&par[idIzin]=$r[idIzin]".getPar($par,"mode,idIzin") : "#";
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorIzin]</td>
					<td align=\"center\">".substr($mulaiIzin,0,5)."</td>
					<td align=\"center\">".substr($selesaiIzin,0,5)."</td>
					<td align=\"center\">".getTanggal($mulaiIzin_tanggal)."</td>
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanIzin</a></td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[idIzin]=$r[idIzin]".getPar($par,"mode,idIzin")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idIzin]=$r[idIzin]".getPar($par,"mode,idIzin")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idIzin]=$r[idIzin]".getPar($par,"mode,idIzin")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_izin where idIzin='$par[idIzin]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		if(empty($r[nomorIzin])) $r[nomorIzin] = gNomor();
		if(empty($r[tanggalIzin])) $r[tanggalIzin] = date('Y-m-d');
		list($mulaiIzin_tanggal, $mulaiIzin) = explode(" ", $r[mulaiIzin]);
		list($selesaiIzin_tanggal, $selesaiIzin) = explode(" ", $r[selesaiIzin]);
		
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
							<span class=\"field\">".$r[nomorIzin]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[tanggalIzin],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SEMENTARA</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($mulaiIzin_tanggal,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<span class=\"field\">".substr($mulaiIzin,0,5)." <strong>s.d</strong> ".substr($selesaiIzin,0,5)."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganIzin])."&nbsp;</span>
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
					
					$persetujuanIzin = $r[persetujuanIzin] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
					$persetujuanIzin = $r[persetujuanIzin] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanIzin;
					$persetujuanIzin = $r[persetujuanIzin] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanIzin;
					
					list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
					$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
					$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>
					<div style=\"float:right; margin-top:-30px;\">
						<table width=\"100%\">
						<tr>
						<td>Tahun ini sudah pernah melakukan izin : <strong>".getField("select count(*) from att_izin where idPegawai='$r[idPegawai]' and persetujuanIzin='t'")." Kali</strong></td>
						<td>
							<table>
							<tr>
							<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
							<td>".$persetujuanIzin." ".$approveTime."</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
					</div>";
			
			$persetujuanIzin = "Belum Diproses";
			$persetujuanIzin = $r[persetujuanIzin] == "t" ? "Disetujui" : $persetujuanIzin;
			$persetujuanIzin = $r[persetujuanIzin] == "f" ? "Ditolak" : $persetujuanIzin;		
			$persetujuanIzin = $r[persetujuanIzin] == "r" ? "Diperbaiki" : $persetujuanIzin;		
					
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanIzin."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanIzin])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>					
				</div>
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
			case "det":
				$text = detail();
			break;
			case "app":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
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
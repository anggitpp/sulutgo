<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="KBD";
		$date=empty($_GET[tanggalRumah]) ? $inp[tanggalRumah] : $_GET[tanggalRumah];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorRumah from ess_rumah where month(tanggalRumah)='$bulan' and year(tanggalRumah)='$tahun' order by nomorRumah desc limit 1");
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
		$sql="delete from ess_rumah where idRumah='$par[idRumah]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idRumah")."';</script>";
	}
	
	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update ess_rumah set idPegawai='$inp[idPegawai]', nomorRumah='$inp[nomorRumah]', tanggalRumah='".setTanggal($inp[tanggalRumah])."', nilaiRumah='".setAngka($inp[nilaiRumah])."', keteranganRumah='$inp[keteranganRumah]', noteRumah='$inp[noteRumah]', sdmRumah='$inp[sdmRumah]', sdmBy='$cUsername', sdmTime='".date('Y-m-d H:i:s')."' where idRumah='$par[idRumah]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idRumah")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update ess_rumah set idPegawai='$inp[idPegawai]', nomorRumah='$inp[nomorRumah]', tanggalRumah='".setTanggal($inp[tanggalRumah])."', nilaiRumah='".setAngka($inp[nilaiRumah])."', keteranganRumah='$inp[keteranganRumah]', catatanRumah='$inp[catatanRumah]', persetujuanRumah='$inp[persetujuanRumah]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idRumah='$par[idRumah]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idRumah")."';</script>";
	}	

	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		
		$sql="update ess_rumah set idPegawai='$inp[idPegawai]', nomorRumah='$inp[nomorRumah]', tanggalRumah='".setTanggal($inp[tanggalRumah])."', nilaiRumah='".setAngka($inp[nilaiRumah])."', keteranganRumah='$inp[keteranganRumah]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idRumah='$par[idRumah]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idRumah")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();			
		$cek = getField("select idRumah from ess_rumah where tanggalRumah ='".setTanggal($inp[tanggalRumah])."' AND idPegawai = '$inp[idPegawai]' AND persetujuanRumah !='t'");
		if($cek){
			echo "<script>alert('TAMBAH DATA GAGAL, DATA PEGAWAI UNTUK HARI INI SUDAH ADA');</script>";
		}else{	
		$idRumah = getField("select idRumah from ess_rumah order by idRumah desc limit 1")+1;		

		$sql="insert into ess_rumah (idRumah, idPegawai, nomorRumah, tanggalRumah, nilaiRumah, keteranganRumah, persetujuanRumah, sdmRumah, createBy, createTime) values ('$idRumah', '$inp[idPegawai]', '$inp[nomorRumah]', '".setTanggal($inp[tanggalRumah])."', '".setAngka($inp[nilaiRumah])."', '$inp[keteranganRumah]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>alert('TAMBAH DATA BERHASIL');</script>";
	}
		
		echo "<script>window.location='?".getPar($par,"mode,idRumah")."';</script>";
	}

	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID,$cUsername;
		
		$sql="select * from ess_rumah where idRumah='$par[idRumah]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorRumah])) $r[nomorRumah] = gNomor();
		if(empty($r[tanggalRumah])) $r[tanggalRumah] = date('Y-m-d');		
		
		$true = $r[persetujuanRumah] == "t" ? "checked=\"checked\"" : "";
		$false = $r[persetujuanRumah] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[persetujuanRumah] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmRumah] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmRumah] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmRumah] == "r" ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[nomorRumah]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalRumah","anda harus mengisi tanggal");		
		setValidation("is_null","inp[nilaiRumah]","anda harus mengisi nilai");
		setValidation("is_null","inp[keteranganRumah]","anda harus mengisi keterangan");
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
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<div class=\"field\">
									<input type=\"text\" id=\"inp[nomorRumah]\" name=\"inp[nomorRumah]\"  value=\"$r[nomorRumah]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
									<input type=\"text\" id=\"tanggalRumah\" name=\"inp[tanggalRumah]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalRumah])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA BANTUAN PERUMAHAN</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">						
							<p>
								<label class=\"l-input-small\">Nilai</label>
								<div class=\"field\">								
									<input type=\"text\" id=\"inp[nilaiRumah]\" name=\"inp[nilaiRumah]\"  value=\"".getAngka($r[nilaiRumah])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" onkeyup=\"cekAngka(this);\" />
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<div class=\"field\">
									<textarea id=\"inp[keteranganRumah]\" name=\"inp[keteranganRumah]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganRumah]</textarea>
								</div>
							</p>
						</td>
						<td width=\"55%\">
							&nbsp;
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
									<input type=\"radio\" id=\"true\" name=\"inp[persetujuanRumah]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
									<input type=\"radio\" id=\"false\" name=\"inp[persetujuanRumah]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
									
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<div class=\"field\">
									<textarea id=\"inp[catatanRumah]\" name=\"inp[catatanRumah]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[catatanRumah]</textarea>
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
								<input type=\"radio\" id=\"true\" name=\"inp[sdmRumah]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmRumah]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[noteRumah]\" name=\"inp[noteRumah]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[noteRumah]</textarea>
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

	if(!empty($cID))
		$text.="<script>
	getPegawai('".getPar($par,"mode,nikPegawai")."');
</script>";

return $text;
}

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID,$areaCheck, $cGroup;
	$par[idPegawai] = $cID;
	if(empty($par[tahunRumah])) $par[tahunRumah]=date('Y');
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
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
						<td>".comboYear("par[tahunRumah]", $par[tahunRumah])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idRumah")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
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

						$filter = "where year(t1.tanggalRumah)='$par[tahunRumah]' ".($cGroup != "1" && $cGroup != "20" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");

						if(!empty($par[filter]))		
							$filter.= " and (
						lower(t1.nomorRumah) like '%".strtolower($par[filter])."%'
						or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
						or lower(t2.name) like '%".strtolower($par[filter])."%'	
						)";

						$filter .= " AND t2.location IN ($areaCheck)";

						$sql="select * from ess_rumah t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorRumah";
						$res=db($sql);
						while($r=mysql_fetch_array($res)){			
							$no++;
							$persetujuanRumah = $r[persetujuanRumah] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
							$persetujuanRumah = $r[persetujuanRumah] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanRumah;
							$persetujuanRumah = $r[persetujuanRumah] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanRumah;

							$sdmRumah = $r[sdmRumah] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
							$sdmRumah = $r[sdmRumah] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmRumah;
							$sdmRumah = $r[sdmRumah] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmRumah;					

							$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah") : "#";

			#$sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah") : "#";
							$sdmLink = "#";

							$text.="<tr>
							<td>$no.</td>					
							<td>".strtoupper($r[name])."</td>
							<td>$r[reg_no]</td>
							<td>$r[nomorRumah]</td>
							<td align=\"center\">".getTanggal($r[tanggalRumah])."</td>
							<td align=\"right\">".getAngka($r[nilaiRumah])."</td>					
							<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$persetujuanRumah</a></td>
							<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah")."',750,425);\" >$sdmRumah</a></td>					
							<td align=\"center\">
								<a href=\"?par[mode]=det&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
							</td>";
							if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
								$text.="<td align=\"center\">";				

								if(in_array($r[persetujuanRumah], array("p","r")) || in_array($r[sdmRumah], array("p","r")))
									if(isset($menuAccess[$s]["edit"])&&$r[persetujuanRumah]!='t') $text.="<a href=\"?par[mode]=edit&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				

								if(in_array($r[persetujuanRumah], array("p")) || in_array($r[sdmRumah], array("p")))
									if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idRumah]=$r[idRumah]".getPar($par,"mode,idRumah")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
								$text.="</td>";
							}
							$text.="</tr>";				
						}	

						$text.="</tbody>
					</table>
				</div>";
				return $text;
			}		

			function detailApproval(){
				global $db,$s,$inp,$par,$arrTitle,$menuAccess;

				$sql="select * from ess_rumah where idRumah='$par[idRumah]'";
				$res=db($sql);
				$r=mysql_fetch_array($res);			

				$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
				$persetujuanField = $par[mode] == "detSdm" ? "sdmRumah" : "persetujuanRumah";
				$catatanField = $par[mode] == "detSdm" ? "noteRumah" : "catatanRumah";
				$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
				$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";

				$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
				$persetujuanField = $par[mode] == "detPay" ? "pembayaranRumah" : $persetujuanField;
				$catatanField = $par[mode] == "detPay" ? "deskripsiRumah" : $catatanField;
				$timeField = $par[mode] == "detPay" ? "paidTime" : $timeField;
				$userField = $par[mode] == "detPay" ? "paidBy" : $userField;

				list($dateField) = explode(" ", $r[$timeField]);
				
				$persetujuanRumah = "Belum Diproses";
				$persetujuanRumah = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanRumah;
				$persetujuanRumah = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanRumah;	
				$persetujuanRumah = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanRumah;	

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
								<span class=\"field\">".$persetujuanRumah."&nbsp;</span>
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

				$sql="select * from ess_rumah where idRumah='$par[idRumah]'";
				$res=db($sql);
				$r=mysql_fetch_array($res);					

				if(empty($r[nomorRumah])) $r[nomorRumah] = gNomor();
				if(empty($r[tanggalRumah])) $r[tanggalRumah] = date('Y-m-d');
				
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
										<span class=\"field\">".$r[nomorRumah]."&nbsp;</span>
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
										<span class=\"field\">".getTanggal($r[tanggalRumah],"t")."&nbsp;</span>
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
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA BANTUAN PERUMAHAN</h3></div>
						</div>
						<table width=\"100%\">
							<tr>
								<td width=\"45%\">						
									<p>
										<label class=\"l-input-small\">Nilai</label>
										<span class=\"field\">".getAngka($r[nilaiRumah])."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Keterangan</label>
										<span class=\"field\">".nl2br($r[keteranganRumah])."&nbsp;</span>
									</p>
								</td>
								<td width=\"55%\">
									&nbsp;
								</td>
							</tr>
						</table>";
						$persetujuanRumah = "Belum Diproses";
						$persetujuanRumah = $r[persetujuanRumah] == "t" ? "Disetujui" : $persetujuanRumah;
						$persetujuanRumah = $r[persetujuanRumah] == "f" ? "Ditolak" : $persetujuanRumah;	
						$persetujuanRumah = $r[persetujuanRumah] == "r" ? "Diperbaiki" : $persetujuanRumah;	

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
									<span class=\"field\">".$persetujuanRumah."&nbsp;</span>
								</p>
								<p>
									<label class=\"l-input-small\">Keterangan</label>
									<span class=\"field\">".nl2br($r[catatanRumah])."&nbsp;</span>
								</p>
							</td>
							<td width=\"55%\">&nbsp;</td>
						</tr>
					</table>";

					$sdmRumah = "Belum Diproses";
					$sdmRumah = $r[sdmRumah] == "t" ? "Disetujui" : $sdmRumah;
					$sdmRumah = $r[sdmRumah] == "f" ? "Ditolak" : $sdmRumah;	
					$sdmRumah = $r[sdmRumah] == "r" ? "Diperbaiki" : $sdmRumah;	

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
								<span class=\"field\">".$sdmRumah."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[noteRumah])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">&nbsp;</td>
					</tr>
				</table>";

				$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idRumah")."';\" style=\"float:right;\"/>		
				</p>
			</form>";
			return $text;
		}

		function pegawai(){
			global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID, $areaCheck, $cGroup;
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

							$filter = "where reg_no is not null ".($cGroup != "1" && $cGroup != "20" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");

							if($par[search] == "Nama")
								$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
							else if($par[search] == "NPP")
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
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/koreksi/";
	
	function gPegawai(){
		global $db,$s,$inp,$par;
		$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		$data["nikPegawai"] = "";
		$data["namaPegawai"] = "";				
		$data["namaJabatan"] = "";
		$data["namaDivisi"] = "";
		
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)."'")){
			if(getField("select idKomponen from pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)." where idPegawai='$r[id]'")){		
				$data["idPegawai"] = $r[id];
				$data["nikPegawai"] = $r[reg_no];
				$data["namaPegawai"] = strtoupper($r[name]);
				
				$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
				$res_=db($sql_);
				$r_=mysql_fetch_array($res_);
				
				$data["namaJabatan"] = $r_[pos_name];
				$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");
			}
		}
		return json_encode($data);
	}
	
	function upload($idKoreksi){
		global $db,$s,$inp,$par,$fFile;
		$fileUpload = $_FILES["fileKoreksi"]["tmp_name"];
		$fileUpload_name = $_FILES["fileKoreksi"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileKoreksi = "doc-".$idKoreksi.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileKoreksi);			
		}
		if(empty($fileKoreksi)) $fileKoreksi = getField("select fileKoreksi from pay_koreksi where idKoreksi='$idKoreksi'");
		
		return $fileKoreksi;
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileKoreksi = getField("select fileKoreksi from pay_koreksi where idKoreksi='$par[idKoreksi]'");
		if(file_exists($fFile.$fileKoreksi) and $fileKoreksi!="")unlink($fFile.$fileKoreksi);
		
		$sql="update pay_koreksi set fileKoreksi='' where idKoreksi='$par[idKoreksi]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;
		$fileKoreksi = getField("select fileKoreksi from pay_koreksi where idKoreksi='$par[idKoreksi]'");
		if(file_exists($fFile.$fileKoreksi) and $fileKoreksi!="")unlink($fFile.$fileKoreksi);
		
		$sql="delete from pay_koreksi where idKoreksi='$par[idKoreksi]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function update(){
		global $db,$s,$inp,$par,$cUsername;
		
		$sql="select * from pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)." where idPegawai='$par[idPegawai]'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$sql_="update pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)." set nilaiProses='".setAngka($inp["$r[idKomponen]"])."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDetail='".$r[idDetail]."'";
			db($sql_);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idKoreksi")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();		
		
		$sql="update pay_koreksi set keteranganKoreksi='$inp[keteranganKoreksi]', statusKoreksi='$inp[statusKoreksi]', approveBy='$cUsername', approveTime='".date('Y-m-d H:i:s')."' where idKoreksi='$par[idKoreksi]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();
		$fileKoreksi=upload($par[idKoreksi]);
		
		$sql="update pay_koreksi set idPegawai='$inp[idPegawai]', tanggalKoreksi='".setTanggal($inp[tanggalKoreksi])."', perubahanKoreksi='$inp[perubahanKoreksi]', alasanKoreksi='$inp[alasanKoreksi]', fileKoreksi='$fileKoreksi', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idKoreksi='$par[idKoreksi]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idKoreksi=getField("select idKoreksi from pay_koreksi order by idKoreksi desc limit 1")+1;				
		$fileKoreksi=upload($idKoreksi);
		
		repField();
		$sql="insert into pay_koreksi (idKoreksi, idPegawai, tanggalKoreksi, bulanKoreksi, tahunKoreksi, perubahanKoreksi, alasanKoreksi, fileKoreksi, statusKoreksi, createBy, createTime) values ('$idKoreksi', '$inp[idPegawai]', '".setTanggal($inp[tanggalKoreksi])."', '$par[bulanKoreksi]', '$par[tahunKoreksi]', '$inp[perubahanKoreksi]', '$inp[alasanKoreksi]', '$fileKoreksi', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function koreksi(){
		global $db,$s,$inp,$par,$arrTitle;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanKoreksi])." ".$par[tahunKoreksi]."</div>
				</h1>
				".getBread(ucwords("koreksi data"))."
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" >	
				<div id=\"general\" class=\"subcontent\">				
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r[name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[pos_name]."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NPWP</label>
							<span class=\"field\">".$r[npwp_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Pangkat / Grade</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[rank]."'")."&nbsp;/&nbsp;".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">Lokasi</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[location]."'")."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
																
				$sql="select * from pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
					
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
					$tipeKomponen = $r[tipeKomponen];
					$urutanKomponen++;
				}				
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));											
				
				for($i=1; $i<=max($cntKomponen); $i++){					
					$text.="<tr>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["t"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">
								<input type=\"text\" id=\"inp[t][$i][nilaiProses]\" name=\"inp[".$arrKomponen["t"][$i]["idKomponen"]."]\"  value=\"".getAngka($arrKomponen["t"][$i]["nilaiProses"])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" onkeyup=\"setKoreksi(this);\" />
							</span>
							<span style=\"float:right;\">Rp.</span>";								
					$text.="</td>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["p"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">
								<input type=\"text\" id=\"inp[p][$i][nilaiProses]\" name=\"inp[".$arrKomponen["p"][$i]["idKomponen"]."]\"  value=\"".getAngka($arrKomponen["p"][$i]["nilaiProses"])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" onkeyup=\"setKoreksi(this);\" />
							</span>
							<span style=\"float:right;\">Rp.</span>";
					$text.="</td>
						</tr>";
						
					$totKomponen["t"]+=$arrKomponen["t"][$i]["nilaiProses"];
					$totKomponen["p"]+=$arrKomponen["p"][$i]["nilaiProses"];
				}				
				
				$text.="<tr>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>						
						<span style=\"float:right; text-align:right; width:125px;\">
							<input type=\"text\" id=\"totalPenerimaan\" name=\"totalPenerimaan\"  value=\"".getAngka($totKomponen["t"])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" readonly=\"readonly\" />
						</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">
							<input type=\"text\" id=\"totalPotongan\" name=\"totalPotongan\"  value=\"".getAngka($totKomponen["p"])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" readonly=\"readonly\" />
						</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";
				
				$text.="</tbody>
					</table>
					<input type=\"hidden\" id=\"cntPenerimaan\" value=\"".count($arrKomponen["t"])."\">
					<input type=\"hidden\" id=\"cntPotongan\" value=\"".count($arrKomponen["p"])."\">
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\"><strong>THP</strong></span>								
								<span style=\"float:right; text-align:right; width:125px;\">
									<input type=\"text\" id=\"totalGaji\" name=\"totalGaji\"  value=\"".getAngka($totKomponen["t"]-$totKomponen["p"])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" readonly=\"readonly\" />
								</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:32%;\"><strong>Terbilang</strong></span>
								<span id=\"nilaiTerbilang\">".trim(terbilang($totKomponen["t"]-$totKomponen["p"]))." Rupiah</span>
							</td>
						</tr>
					</tbody>
					</table>";
					
			$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status='1'";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">Ditransfer ke :</th>							
							<th width=\"50%\" style=\"text-align:left;\">Catatan :</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td style=\"padding:3px 20px; height:75px;\">
							Rek. ".$r[namaBank]." ".$r[branch]."&nbsp;<br>
							No. Acc ".$r[account_no]."&nbsp;<br>
							a.n. ".$namaPegawai."
						</td>
						<td style=\"padding:3px 20px; height:75px;\">".nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'"))."&nbsp;</td>
						</tr>
					</tbody>
					</table>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>
					</p>
				</div>
				</form>
			</div>";
		
		return $text;
	}
	
	function persetujuan(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from pay_koreksi where idKoreksi='$par[idKoreksi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$true = $r[statusKoreksi] == "t" ? "checked=\"checked\"" : "";
		$false = $r[statusKoreksi] == "f" ? "checked=\"checked\"" : "";
		
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
		
		setValidation("is_null","inp[keteranganKoreksi]","anda harus mengisi keterangan");		
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">
						".$arrTitle[$s]."
						<div id=\"periodeKoreksi\" style=\"float:right;\">".getBulan($par[bulanKoreksi])." ".$par[tahunKoreksi]."</div>
					</h1>
					".getBread(ucwords("approve data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalKoreksi], t)."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Point Perubahan</label>
							<span class=\"field\">".$r[perubahanKoreksi]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Alasan Perubahan</label>
							<span class=\"field\">".nl2br($r[alasanKoreksi])."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">File</label>
							<span class=\"field\">";
					if(!empty($r[fileKoreksi])) $text.="<a href=\"download.php?d=koreksi&f=$r[idKoreksi]\"><img src=\"".getIcon($fFile."/".$r[fileKoreksi])."\" align=\"left\" style=\"margin-top:5px; padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
					$text.="&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Divisi</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:125px;\" class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PERSETUJUAN KOREKSI GAJI</h3></div>
					</div>			
					
					<p>
						<label style=\"width:125px;\" class=\"l-input-small\">Status</label>
						<div style=\"margin-left:145px;\" class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusKoreksi]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusKoreksi]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
						</div>
					</p>
					<p>
						<label style=\"width:125px;\" class=\"l-input-small\">Keterangan</label>
						<div style=\"margin-left:145px;\" class=\"field\">
							<textarea id=\"inp[keteranganKoreksi]\" name=\"inp[keteranganKoreksi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganKoreksi]</textarea>
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
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from pay_koreksi where idKoreksi='$par[idKoreksi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		if(empty($r[tanggalKoreksi])) $r[tanggalKoreksi] = date('Y-m-d');
		
		setValidation("is_null","inp[idPegawai]","anda harus mengisi NPP");
		setValidation("is_null","inp[perubahanKoreksi]","anda harus mengisi point perubahan");
		setValidation("is_null","inp[alasanKoreksi]","anda harus mengisi alasan perubahan");
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
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">
						".$arrTitle[$s]."
						<div id=\"periodeKoreksi\" style=\"float:right;\">".getBulan($par[bulanKoreksi])." ".$par[tahunKoreksi]."</div>
					</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalKoreksi\" name=\"inp[tanggalKoreksi]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalKoreksi])."\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">NPP</label>
						<div class=\"field\">								
							<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
							<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
							<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg".getPar($par,"mode,filter")."',850,475);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nama</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Divisi</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[namaDivisi]\" name=\"inp[namaDivisi]\"  value=\"$r_[namaDivisi]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Jabatan</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Point Perubahan</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[perubahanKoreksi]\" name=\"inp[perubahanKoreksi]\"  value=\"$r[perubahanKoreksi]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Alasan Perubahan</label>
						<div class=\"field\">
							<textarea id=\"inp[alasanKoreksi]\" name=\"inp[alasanKoreksi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[alasanKoreksi]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[fileKoreksi])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"fileKoreksi\" name=\"fileKoreksi\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=koreksi&f=$r[idKoreksi]\"><img src=\"".getIcon($fFile."/".$r[fileKoreksi])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"fileKoreksi\" name=\"fileKoreksi\" style=\"display:none;\" />
								<a href=\"?mode=".$par[mode]."&par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
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

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;
		if(empty($par[bulanKoreksi])) $par[bulanKoreksi] = date('m');
		if(empty($par[tahunKoreksi])) $par[tahunKoreksi] = date('Y');
		
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
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
										<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
										".comboMonth("par[bulanKoreksi]", $par[bulanKoreksi], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunKoreksi]", $par[tahunKoreksi], "", "onchange=\"document.getElementById('form').submit();\"")."
										<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
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
						<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>				
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idKoreksi")."',950,550);\"><span>Tambah Data</span></a>";
		$text.="</div>
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
					<th style=\"width:75px;\">Approval</th>
					<th style=\"width:75px;\">Tanggal</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
				
			$filter = "where t1.bulanKoreksi='$par[bulanKoreksi]' and t1.tahunKoreksi='$par[tahunKoreksi]' AND t2.group_id is not null";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";		
						
			$arrParam = explode(",", getField("select nilaiParameter from pay_parameter where idParameter='1'"));
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");			
			
			$sql="select * from (
			 select t1.*, t2.name, t2.reg_no from pay_koreksi t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter
			) as t0 left join emp_phist t3 on (t0.idPegawai=t3.parent_id and t3.status=1)";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){		
				$no++;
				list($tanggalApprove, $waktuApprove) = explode(" ", $r[approveTime]);
				$statusKoreksi = $r[statusKoreksi] == "t"? getTanggal($tanggalApprove) : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
				$statusKoreksi = $r[statusKoreksi] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $statusKoreksi;
				$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "onclick=\"openBox('popup.php?par[mode]=app&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi")."',925,550);\"" : "";
				
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>";
				$text.=$r[statusKoreksi] == "t" ?
						"<a href=\"?par[mode]=upd&par[idPegawai]=$r[idPegawai]".getPar($par,"mode,idPegawai")."\" class=\"detil\" title=\"Koreksi Data\">$r[reg_no]</a>":
						"$r[reg_no]";
				$text.="</td>
						<td>$r[pos_name]</td>
						<td>".$arrDivisi["$r[div_id]"]."</td>
						<td align=\"center\"><a href=\"#\" title=\"Detail Data\" ".$persetujuanLink.">".$statusKoreksi."</a></td>
						<td align=\"center\">".getTanggal($r[tanggalKoreksi])."</td>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
					$text.="<td align=\"center\">";				
					if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi")."',950,550);\"><span>Edit</span></a>";
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idKoreksi]=$r[idKoreksi]".getPar($par,"mode,idKoreksi")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $areaCheck;		
		$text.="<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					Slip Gaji
					<div style=\"float:right;\">".getBulan($par[bulanKoreksi])." ".$par[tahunKoreksi]."</div>
				</h1>
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
					".setPar($par, "filter,search")."
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
					<th style=\"width:150px;\">Gaji</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where reg_no is not null AND group_id is not null ";
		
		if($par[search] == "Nama")
			$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(name) like '%".strtolower($par[filter])."%'
				or lower(reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)."'")){
			$sql="select * from pay_proses_".$par[tahunKoreksi].str_pad($par[bulanKoreksi], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
			}
		}
		
		$sql="select * from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(isset($arrGaji["$r[id]"])){
				$no++;			
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>
						<td align=\"right\">".getAngka($arrGaji["$r[id]"])."</td>
						<td align=\"center\">
							<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
						</td>
					</tr>";
			}
		}	
		
		$text.="</tbody>
			</table>
			</div>
		</div>";
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess,$cUsername;
		$arrParam = explode(",", getField("select nilaiParameter from pay_parameter where idParameter='1'"));
		
		switch($par[mode]){
			case "get":
				$text = gPegawai();
			break;
			case "peg":
				$text = pegawai();
			break;
						
			case "upd":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? koreksi() : update(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? persetujuan() : approve(); else $text = lihat();
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
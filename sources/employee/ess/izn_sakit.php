<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	$fFile = "files/sakit/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IS";
		$date=empty($_GET[tanggalSakit]) ? $inp[tanggalSakit] : $_GET[tanggalSakit];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorSakit from att_sakit where month(tanggalSakit)='$bulan' and year(tanggalSakit)='$tahun' order by nomorSakit desc limit 1");
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
	
	function upload($idSakit){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["fileSakit"]["tmp_name"];
		$fileUpload_name = $_FILES["fileSakit"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileSakit = "doc-".$idSakit.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileSakit);			
		}
		if(empty($fileSakit)) $fileSakit = getField("select fileSakit from att_sakit where idSakit='$idSakit'");
		
		return $fileSakit;
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;						
		$fileSakit = getField("select fileSakit from att_sakit where idSakit='$par[idSakit]'");
		if(file_exists($fFile.$fileSakit) and $fileSakit!="")unlink($fFile.$fileSakit);
		
		$sql="delete from att_sakit where idSakit='$par[idSakit]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idSakit")."';</script>";
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileSakit = getField("select fileSakit from att_sakit where idSakit='$par[idSakit]'");
		if(file_exists($fFile.$fileSakit) and $fileSakit!="")unlink($fFile.$fileSakit);
		
		$sql="update att_sakit set fileSakit='' where idSakit='$par[idSakit]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$fileSakit=upload($par[idSakit]);
		
		$sql="update att_sakit set idPegawai='$inp[idPegawai]', idPerawatan='$inp[idPerawatan]', nomorSakit='$inp[nomorSakit]', tanggalSakit='".setTanggal($inp[tanggalSakit])."', mulaiSakit='".setTanggal($inp[mulaiSakit])."', selesaiSakit='".setTanggal($inp[selesaiSakit])."', keteranganSakit='$inp[keteranganSakit]', fileSakit='$fileSakit', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idSakit='$par[idSakit]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idSakit")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();				
		$idSakit = getField("select idSakit from att_sakit order by idSakit desc limit 1")+1;				
		$fileSakit=upload($idSakit);
		
		$sql="insert into att_sakit (idSakit, idPegawai, idPerawatan, nomorSakit, tanggalSakit, mulaiSakit, selesaiSakit, keteranganSakit, fileSakit, persetujuanSakit, sdmSakit, createBy, createTime) values ('$idSakit', '$inp[idPegawai]', '$inp[idPerawatan]', '$inp[nomorSakit]', '".setTanggal($inp[tanggalSakit])."', '".setTanggal($inp[mulaiSakit])."', '".setTanggal($inp[selesaiSakit])."', '$inp[keteranganSakit]', '$fileSakit', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idSakit")."';</script>";
	}
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from att_sakit where idSakit='$par[idSakit]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorSakit])) $r[nomorSakit] = gNomor();
		if(empty($r[tanggalSakit])) $r[tanggalSakit] = date('Y-m-d');
		
		setValidation("is_null","inp[nomorSakit]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalSakit","anda harus mengisi tanggal");
		setValidation("is_null","mulaiSakit","anda harus mengisi mulai");		
		setValidation("is_null","selesaiSakit","anda harus mengisi selesai");
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
								<input type=\"text\" id=\"inp[nomorSakit]\" name=\"inp[nomorSakit]\"  value=\"$r[nomorSakit]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
								<input type=\"text\" id=\"tanggalSakit\" name=\"inp[tanggalSakit]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalSakit])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SAKIT</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Mulai</label>
							<div class=\"field\">
								<input type=\"text\" id=\"mulaiSakit\" name=\"inp[mulaiSakit]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiSakit])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Selesai</label>
							<div class=\"field\">
								<input type=\"text\" id=\"selesaiSakit\" name=\"inp[selesaiSakit]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiSakit])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganSakit]\" name=\"inp[keteranganSakit]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganSakit]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Perawatan</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","inp[idPerawatan]","",$r[idPerawatan],"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Surat Dokter</label>
							<div class=\"field\">";
								$text.=empty($r[fileSakit])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"fileSakit\" name=\"fileSakit\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=sakit&f=$r[idSakit]\"><img src=\"".getIcon($fFile."/".$r[fileSakit])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileSakit\" name=\"fileSakit\" style=\"display:none;\" />
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
		if(empty($par[tahunSakit])) $par[tahunSakit]=date('Y');
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
				<td>".comboYear("par[tahunSakit]", $par[tahunSakit])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idSakit")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>										
					<th rowspan=\"2\" style=\"min-width:150px;\">Nomor</th>
					<th colspan=\"3\" width=\"225\">Tanggal</th>
					<th colspan=\"2\" width=\"100\">Approval</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
				<tr>
					<th width=\"75\">Dibuat</th>
					<th width=\"75\">Mulai</th>
					<th width=\"75\">Selesai</th>
					<th width=\"50\">Atasan</th>
					<th width=\"50\">MANAGER</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where year(t1.tanggalSakit)='$par[tahunSakit]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorSakit) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from att_sakit t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorSakit";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanSakit = $r[persetujuanSakit] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanSakit = $r[persetujuanSakit] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanSakit;
			$persetujuanSakit = $r[persetujuanSakit] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanSakit;
			
			$sdmSakit = $r[sdmSakit] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmSakit = $r[sdmSakit] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmSakit;
			$sdmSakit = $r[sdmSakit] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmSakit;
			
			
			$text.="<tr>
					<td>$no.</td>					
					<td>$r[nomorSakit]</td>
					<td align=\"center\">".getTanggal($r[tanggalSakit])."</td>
					<td align=\"center\">".getTanggal($r[mulaiSakit])."</td>
					<td align=\"center\">".getTanggal($r[selesaiSakit])."</td>					
					<td align=\"center\">$persetujuanSakit</td>
					<td align=\"center\">$sdmSakit</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				
				if(in_array($r[persetujuanSakit], array("p","r")) || in_array($r[sdmSakit], array("p","r")))
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idSakit]=$r[idSakit]".getPar($par,"mode,idSakit")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
			
				if(in_array($r[persetujuanIzin], array("p")) || in_array($r[sdmSakit], array("p")))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idSakit]=$r[idSakit]".getPar($par,"mode,idSakit")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
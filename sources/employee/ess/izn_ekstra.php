<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="IE";
		$date=empty($_GET[tanggalEkstra]) ? $inp[tanggalEkstra] : $_GET[tanggalEkstra];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorEkstra from att_ekstra where month(tanggalEkstra)='$bulan' and year(tanggalEkstra)='$tahun' order by nomorEkstra desc limit 1");
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
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;						
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="delete from att_ekstra where idEkstra='$par[idEkstra]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$idDeposit = getField("select idDeposit from att_ekstra where idEkstra='$par[idEkstra]'");
		$sql="update dta_deposit set statusDeposit='t' where idDeposit='".$idDeposit."'";
		db($sql);
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="update att_ekstra set idTipe='$r[idTipe]', idPegawai='$inp[idPegawai]', idPengganti='$inp[idPengganti]', idAtasan='$inp[idAtasan]', nomorEkstra='$inp[nomorEkstra]', tanggalEkstra='".setTanggal($inp[tanggalEkstra])."', penggantiEkstra='".setTanggal($inp[penggantiEkstra])."', keteranganEkstra='$inp[keteranganEkstra]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idEkstra='$par[idEkstra]'";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;				
		repField();				
		$idEkstra = getField("select idEkstra from att_ekstra order by idEkstra desc limit 1")+1;		
		
		$sql="select * from dta_deposit where idDeposit='".$inp[idDeposit]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql="insert into att_ekstra (idEkstra, idTipe, idPegawai, idPengganti, idAtasan, idDeposit, nomorEkstra, tanggalEkstra, penggantiEkstra, keteranganEkstra, persetujuanEkstra, sdmEkstra, createBy, createTime) values ('$idEkstra', '$r[idTipe]', '$inp[idPegawai]', '$inp[idPengganti]', '$inp[idAtasan]', '$inp[idDeposit]', '$inp[nomorEkstra]', '".setTanggal($inp[tanggalEkstra])."', '".setTanggal($inp[penggantiEkstra])."', '$inp[keteranganEkstra]', 'p', 'p', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$sql="update dta_deposit set statusDeposit='f' where idDeposit='".$inp[idDeposit]."'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idEkstra")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from att_ekstra where idEkstra='$par[idEkstra]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomorEkstra])) $r[nomorEkstra] = gNomor();
		if(empty($r[tanggalEkstra])) $r[tanggalEkstra] = date('Y-m-d');		
		
		setValidation("is_null","inp[nomorEkstra]","anda harus mengisi nomor");
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","tanggalEkstra","anda harus mengisi tanggal");				
		setValidation("is_null","penggantiEkstra","anda harus mengisi tgl. digunakan");		
		
		setValidation("is_null","inp[idAtasan]","anda harus mengisi atasan");
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
		<input type=\"text\" id=\"inp[nomorEkstra]\" name=\"inp[nomorEkstra]\"  value=\"$r[nomorEkstra]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
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
		<input type=\"text\" id=\"tanggalEkstra\" name=\"inp[tanggalEkstra]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalEkstra])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DASAR EKSTRA OFF</h3></div>
		</div>
		
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>					
		<th width=\"100\">Tanggal</th>
		<th width=\"200\">Jenis</th>
		<th>Keterangan</th>
		<th width=\"50\">Pilih</th>
		</tr>
		</thead>
		<tbody>";
		
		$sql_="select * from dta_deposit t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idPegawai='".$r[idPegawai]."' and ((t1.statusDeposit='t' and t1.expiredDeposit>'".date('Y-m-d')."') or t1.idDeposit='$r[idDeposit]')";
		$res_=db($sql_);
		$no=1;
		while($r_=mysql_fetch_array($res_)){
			$checked = $r[idDeposit] == $r_[idDeposit] ? "checked=\"checked\"" : "";
			$text.="<tr>
			<td>".$no.".</td>
			<td align=\"center\">".getTanggal($r_[tanggalDeposit])."</td>
			<td>".$r_[namaData]."</td>
			<td>".$r_[keteranganDeposit]."</td>
			<td align=\"center\"><input type=\"radio\" id=\"det[".$r_[idDeposit]."]\" name=\"inp[idDeposit]\" value=\"".$r_[idDeposit]."\" $checked /></td>
			</tr>";
			$no++;
		}					
		
		if($no == 1){
			$text.="<tr>
			<td colspan=\"5\">&nbsp;</td>
			</tr>";
		}
		
		$text.="</tbody>
		</table>
		<br clear=\"all\">
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA EKSTRA OFF</h3></div>
		</div>
		<table width=\"100%\">
		<tr>
		<td width=\"45%\">						
		<p>
		<label class=\"l-input-small\">Tgl. Digunakan</label>
		<div class=\"field\">
		<input type=\"text\" id=\"penggantiEkstra\" name=\"inp[penggantiEkstra]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[penggantiEkstra])."\" class=\"vsmallinput hasDatePicker\" />
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<div class=\"field\">
		<textarea id=\"inp[keteranganEkstra]\" name=\"inp[keteranganEkstra]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganEkstra]</textarea>
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
		if(empty($par[tahunEkstra])) $par[tahunEkstra]=date('Y');
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
		<td>".comboYear("par[tahunEkstra]", $par[tahunEkstra])."</td>
		<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
		</tr>
		</table>
		</div>
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idEkstra")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>					
		<th rowspan=\"2\" width=\"100\">Nomor</th>
		<th rowspan=\"2\">Tipe</th>
		<th colspan=\"2\" width=\"225\">Tanggal</th>
		<th colspan=\"2\" width=\"100\">Approval</th>";
		if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
		$text.="</tr>
		<tr>
		<th width=\"75\">Dibuat</th>
		<th width=\"75\">Digunakan</th>					
		<th width=\"50\">Atasan</th>
		<th width=\"50\">SDM</th>
		</tr>
		</thead>
		<tbody>";
		
		$filter = "where year(t1.tanggalEkstra)='$par[tahunEkstra]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		if(!empty($par[filter]))		
		$filter.= " and (
		lower(t1.nomorEkstra) like '%".strtolower($par[filter])."%'
		)";
		
		$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[57]."'");
		$sql="select * from att_ekstra t1 left join emp t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorEkstra";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$no++;
			$persetujuanEkstra = $r[persetujuanEkstra] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanEkstra = $r[persetujuanEkstra] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanEkstra;
			$persetujuanEkstra = $r[persetujuanEkstra] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanEkstra;
			
			$sdmEkstra = $r[sdmEkstra] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmEkstra = $r[sdmEkstra] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmEkstra;
			$sdmEkstra = $r[sdmEkstra] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmEkstra;
			
			$text.="<tr>
			<td>$no.</td>					
			<td>$r[nomorEkstra]</td>
			<td>".$arrTipe["$r[idTipe]"]."</td>
			<td align=\"center\">".getTanggal($r[tanggalEkstra])."</td>
			<td align=\"center\">".getTanggal($r[penggantiEkstra])."</td>					
			<td align=\"center\">$persetujuanEkstra</td>
			<td align=\"center\">$sdmEkstra</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				
				if(in_array($r[persetujuanEkstra], array("p","r")) || in_array($r[sdmEkstra], array("p","r")))
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				
				if(in_array($r[persetujuanEkstra], array("p")) || in_array($r[sdmEkstra], array("p")))
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idEkstra]=$r[idEkstra]".getPar($par,"mode,idEkstra")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
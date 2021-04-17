<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/emp/rmb";
		
	function approve(){
		global $db,$s,$inp,$par,$cUsername;
		repField();								
		$sql="update emp_rmb set apr_remark='$inp[apr_remark]', status='$inp[status]', apr_by='$cUsername', apr_date='".date('Y-m-d')."' where id='$par[id]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}	
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from emp_rmb where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[status] == "1" ? "checked=\"checked\"" : "";
		$false = $r[status] == "2" ? "checked=\"checked\"" : "";
		$revisi = $r[status] == "3" ? "checked=\"checked\"" : "";
							
		setValidation("is_null","inp[apr_remark]","anda harus mengisi keterangan");		
		$text = getValidation();

		if(!empty($cID) && empty($r[parent_id])) $r[parent_id] = $cID;
		$sql_="select
			id as parent_id,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[parent_id]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[parent_id]."' and status='1'";
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
							<span class=\"field\">".$r[rmb_no]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[rmb_date],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[rmb_type]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[rmb_val])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[remark])."&nbsp;</span>							
						</p>
						
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">						
						&nbsp;
					</td>
					</tr>
					</table>";
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
								<input type=\"radio\" id=\"true\" name=\"inp[status]\" value=\"1\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[status]\" value=\"2\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[status]\" value=\"3\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[apr_remark]\" name=\"inp[apr_remark]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[apr_remark]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			$text.="</div>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,parent_id")."';\"/>
				</p>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;
		if(empty($par[tahunKlaim])) $par[tahunKlaim]=date('Y');		
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
				<td>".comboYear("par[tahunKlaim]", $par[tahunKlaim])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,id")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
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
					<th width=\"75\">Tanggal</th>
					<th width=\"100\">Nilai</th>					
					<th width=\"50\">Approval</th>
					<th width=\"50\">Bayar</th>
					<th width=\"50\">Kontrol</th>
				</tr>				
			</thead>
			<tbody>";
		
		$filter = "where year(t1.rmb_date)='$par[tahunKlaim]' and rmb_jenis='".$arrParam[$s]."' and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";				
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.rmb_no) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select t1.*, t2.name, t2.reg_no from emp_rmb t1 join dta_pegawai t2 on (t1.parent_id=t2.id) $filter order by t1.rmb_no";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$status = $r[status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$status = $r[status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $status;
			$status = $r[status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $status;
			
			$persetujuanLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=app&par[id]=$r[id]".getPar($par,"mode,id") : "#";
			
			$bayar = empty($r[pay_date]) ?
			"<img src=\"styles/images/f.png\" title=\"Belum Lunas\">":
			"<img src=\"styles/images/t.png\" title=\"Sudah Lunas\">";
			
			$text.="<tr>
					<td>$no.</td>					
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[rmb_no]</td>
					<td align=\"center\">".getTanggal($r[rmb_date])."</td>
					<td align=\"right\">".getAngka($r[rmb_val])."</td>										
					<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$status</a></td>
					<td align=\"center\">$bayar</td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[id]=$r[id]".getPar($par,"mode,id")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
					</td>
					</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from emp_rmb where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[rmb_no])) $r[rmb_no] = gNomor();
		if(empty($r[rmb_date])) $r[rmb_date] = date('Y-m-d');
				
		if(!empty($cID) && empty($r[parent_id])) $r[parent_id] = $cID;
		$sql_="select
			id as parent_id,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[parent_id]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[parent_id]."' and status='1'";
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
							<span class=\"field\">".$r[rmb_no]."&nbsp;</span>
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
							<span class=\"field\">".getTanggal($r[rmb_date],"t")."&nbsp;</span>
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">						
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[rmb_type]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[rmb_val])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[remark])."&nbsp;</span>							
						</p>
						
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">						
						&nbsp;
					</td>
					</tr>
					</table>";
			$status = "Belum Diproses";
			$status = $r[status] == "1" ? "Disetujui" : $status;
			$status = $r[status] == "2" ? "Ditolak" : $status;	
			$status = $r[status] == "3" ? "Diperbaiki" : $status;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$status."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[apr_remark])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,parent_id")."';\" style=\"float:right;\"/>		
				</p>
			</form>";
		return $text;
	}
		
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){					
			case "det":
				$text = detail();
			break;
			
			case "app":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
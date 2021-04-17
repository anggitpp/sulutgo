<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
	$fFile = "files/kacamata/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;				
		if(empty($par[tahun])) $par[tahun]=date('Y');				
		$text="<div class=\"pageheader\">
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
				<td>".comboYear("par[tahun]", $par[tahun])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
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
					<th style=\"min-width:150px;\">Nomor</th>
					<th width=\"75\">Tanggal</th>
					<th style=\"min-width:150px;\">Kategori/Tipe</th>
					<th width=\"100\">Nilai</th>					
					<th width=\"50\">Bayar</th>
					<th width=\"50\">Detail</th>
					</tr>
			</thead>
			<tbody>";
				
		$filter = "where year(t1.rmb_date)='$par[tahun]' and rmb_jenis='m' and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."') and t1.status='1'";		
		if(!empty($par[filter]))
		$filter.= " and (
			lower(t1.rmb_no) like '%".strtolower($par[filter])."%'
		)";
		
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[17]."'");
		$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[18]."'");
		
		$sql="select t1.*, t2.name, t2.reg_no from emp_rmb t1 left join dta_pegawai t2 on (t1.parent_id=t2.id) $filter order by t1.rmb_no";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			if(empty($r[pay_status])) $r[pay_status] = 0;
			$pay_status = $r[pay_status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$pay_status = $r[pay_status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pay_status;			
			$pay_status = $r[pay_status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pay_status;
									
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[rmb_no]</td>					
					<td align=\"center\">".getTanggal($r[rmb_date])."</td>
					<td>".$arrKategori["$r[rmb_cat]"]." - ".$arrTipe["$r[rmb_type]"]."</td>
					<td align=\"right\">".getAngka($r[rmb_val])."</td>					
					<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detPay&par[id]=$r[id]".getPar($par,"mode,id")."',750,425);\" >$pay_status</a></td>
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
	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from emp_rmb where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdm_status" : "status";
		$catatanField = $par[mode] == "detSdm" ? "sdm_remark" : "remark";
		$timeField = $par[mode] == "detSdm" ? "sdm_date" : "apr_date";
		$userField = $par[mode] == "detSdm" ? "sdm_by" : "apr_by";
		
		$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
		$persetujuanField = $par[mode] == "detPay" ? "pay_status" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "pay_remark" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "pay_date" : $timeField;
		$userField = $par[mode] == "detPay" ? "pay_by" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);
				
		$persetujuanPinjaman = "Belum Diproses";
		$persetujuanPinjaman = $r[$persetujuanField] == "1" ? "Disetujui" : $persetujuanPinjaman;
		$persetujuanPinjaman = $r[$persetujuanField] == "2" ? "Ditolak" : $persetujuanPinjaman;	
		$persetujuanPinjaman = $r[$persetujuanField] == "3" ? "Diperbaiki" : $persetujuanPinjaman;	
		
		
		
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
						<span class=\"field\">".$persetujuanPinjaman."&nbsp;</span>
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
		
		$sql="select * from emp_rmb where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$true = $r[status] == "1" ? "checked=\"checked\"" : "";
		$false = $r[status] == "2" ? "checked=\"checked\"" : "";
		$revisi = $r[status] == "3" ? "checked=\"checked\"" : "";
		
		if(empty($r[rmb_no])) $r[rmb_no] = gNomor();
		if(empty($r[rmb_date])) $r[rmb_date] = date('Y-m-d');		
		
		setValidation("is_null","inp[rmb_no]","anda harus mengisi nomor");
		setValidation("is_null","inp[parent_id]","anda harus mengisi nik");
		setValidation("is_null","rmb_date","anda harus mengisi tanggal");
		setValidation("is_null","inp[rmb_cat]","anda harus mengisi kategori");
		setValidation("is_null","inp[rmb_type]","anda harus mengisi tipe");
		setValidation("is_null","inp[rmb_val]","anda harus mengisi nilai");
		$text = getValidation();

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
		
		list($rmb_year) = explode("-", $r[rmb_date]);
		$sql__="select * from pay_kacamata where idKategori='".$r[rmb_cat]."' and idTipe='".$r[rmb_type]."'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
	
		$r[rmb_limit] = $r__[nilaiKacamata];						
		$r[rmb_balance] = $r__[nilaiKacamata] - getField("select sum(rmb_val) from emp_rmb where parent_id='".$r[parent_id]."' and rmb_cat='".$r[rmb_cat]."' and rmb_type='".$r[rmb_type]."' and year(rmb_date)='".$rmb_year."' and rmb_date<='".$r[rmb_date]."' and status='1' and rmb_jenis='m' and id!='".$par[id]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return save('".getPar($par, "mode")."')\" enctype=\"multipart/form-data\">	
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
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM KACAMATA</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[rmb_cat]."'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[rmb_type]."'")."&nbsp;</span>
						</p>			
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<span class=\"field\">".getAngka($r[rmb_val])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Document</label>
							<div class=\"field\">";
								$filename = getField("select filename from emp_rmb_files where parent_id='$par[id]'");
								$text.=empty($filename)?
									"&nbsp;":
									"<a href=\"download.php?d=kacamata&f=$par[id]\"><img src=\"".getIcon($fFile."/".$filename)."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a><br clear=\"all\">";
							$text.="</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Balance</label>
							<span class=\"field\">".getAngka($r[rmb_balance])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Limit</label>
							<span class=\"field\">".getAngka($r[rmb_limit])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[remark])."&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>";
			
			$status = "Belum Diproses";
			$status = $r[status] == "1" ? "Disetujui" : $status;
			$status = $r[status] == "2" ? "Ditolak" : $status;	
			$status = $r[status] == "3" ? "Diperbaiki" : $status;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[apr_date],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[apr_by]'")."&nbsp;</span>
						</p>
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
			
			
			$status = "Belum Diproses";
			$status = $r[sdm_status] == "1" ? "Disetujui" : $status;
			$status = $r[sdm_status] == "2" ? "Ditolak" : $status;	
			$status = $r[sdm_status] == "3" ? "Diperbaiki" : $status;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[sdm_date],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[sdm_by]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$status."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[sdm_remark])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$status = "Belum Diproses";
			$status = $r[pay_status] == "1" ? "Disetujui" : $status;
			$status = $r[pay_status] == "2" ? "Ditolak" : $status;	
			$status = $r[pay_status] == "3" ? "Diperbaiki" : $status;	
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PEMBAYARAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[pay_date],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[pay_by]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$status."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[pay_remark])."&nbsp;</span>
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
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "det":
				$text = detail();
			break;			
			case "detPay":
				$text = detailApproval();
			break;		
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
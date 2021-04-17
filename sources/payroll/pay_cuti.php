<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
				
	function setTable(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;						
		$detailTemp = "tmp_insentif_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT);
		$detailInsentif = "pay_proses_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT);
		$tanggalInsentif = date("t", strtotime($par[tahunInsentif]."-".$par[bulanInsentif]."-01"));
		
		db("DROP TABLE IF EXISTS ".$detailTemp);				
		db("CREATE TABLE IF NOT EXISTS ".$detailTemp." (
			  idDetail int(11) NOT NULL  AUTO_INCREMENT,
			  idInsentif int(11) NOT NULL,
			  idPegawai int(11) NOT NULL,			  
			  createBy varchar(30) NOT NULL,
			  createTime datetime NOT NULL,			  
			  PRIMARY KEY (idDetail)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1");
				
		db("CREATE TABLE IF NOT EXISTS ".$detailInsentif." (
			  idDetail int(11) NOT NULL  AUTO_INCREMENT,
			  idProses int(11) NOT NULL,
			  idPegawai int(11) NOT NULL,
			  idKomponen int(11) NOT NULL,
			  nilaiProses int(11) NOT NULL,	  
			  flagProses int(11) NOT NULL,
			  createBy varchar(30) NOT NULL,
			  createTime datetime NOT NULL,
			  updateBy varchar(30) NOT NULL,
			  updateTime datetime NOT NULL,
			  PRIMARY KEY (idDetail)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1");
		
		$sql="delete from ".$detailInsentif." where flagProses='9'";
		db($sql);
		
		if(!getField("select idProses from pay_proses where bulanProses='".$par[bulanInsentif]."' and tahunProses='".$par[tahunInsentif]."'")){
			$idProses = getField("select idProses from pay_proses order by idProses desc limit 1")+1;
			$sql="insert into pay_proses (idProses, bulanProses, tahunProses, detailProses) values ('$idProses', '$par[bulanInsentif]', '$par[tahunInsentif]','$detailInsentif')";
			db($sql);
		}
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='7'");
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
		$idInsentif = getField("select idInsentif from pay_insentif order by idInsentif desc limit 1")+1;		
		
		$sql=getField("select idInsentif from pay_insentif where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'")?
		"update pay_insentif set mulaiInsentif='".date('Y-m-d H:i:s')."', pegawaiInsentif='0', nilaiInsentif='0', detailInsentif='".$detailInsentif."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'":
		"insert into pay_insentif (idInsentif, idKomponen, bulanInsentif, tahunInsentif, mulaiInsentif, pegawaiInsentif, nilaiInsentif, detailInsentif, createBy, createTime) values ('$idInsentif', '$idKomponen', '$par[bulanInsentif]', '$par[tahunInsentif]', '".date('Y-m-d H:i:s')."', '0', '0', '".$detailInsentif."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);				
		
		$sql="insert into ".$detailTemp." (idInsentif, idPegawai, createBy, createTime) select '".$idInsentif."' as idInsentif, idPegawai, '$cUsername' as createBy, '".date('Y-m-d H:i:s')."' as createTime from ess_cuti where date(sdmTime) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and sdmCuti='t' group by idPegawai order by idPegawai";		
		db($sql);
	}
	
	function setData(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		$detailTemp = "tmp_insentif_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT);
		$detailInsentif = "pay_proses_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT);
		
		$cntPegawai = getField("select count(*) from ".$detailTemp."");
		$idPegawai = getField("select idPegawai from ".$detailTemp." where idDetail='$par[idDetail]'");
		
		$progresData = getAngka($par[idDetail]/$cntPegawai * 100);		
		
		if(!empty($idPegawai)){								
			$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='7'");
			$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
			
			$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanInsentif]."' and tahunProses='".$par[tahunInsentif]."'");
			$idInsentif = getField("select idInsentif from pay_insentif where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'");
		
			$sql="select * from ess_cuti t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.sdmTime) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and t1.sdmCuti='t' and t1.idPegawai='".$idPegawai."'";
			$res=db($sql);			
			while($r=mysql_fetch_array($res)){			
				$sql="update ess_cuti set pembayaranCuti='t' where idCuti='$r[idCuti]'";
				db($sql);
				$nilaiInsentif+=$r[nilaiCuti];
			}
			
			if($nilaiInsentif > 0){
				$sql="insert into ".$detailInsentif." (idProses, idPegawai, idKomponen, nilaiProses, flagProses, createBy, createTime) values ('$idProses', '$idPegawai', '$idKomponen', '".setAngka($nilaiInsentif)."', '9', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
								
				$sql="update pay_insentif set nilaiInsentif=nilaiInsentif + ".setAngka($nilaiInsentif)."  where idInsentif='$idInsentif'";
				db($sql);
			}
			
			$sql="update pay_insentif set pegawaiInsentif=pegawaiInsentif + 1, selesaiInsentif='".date('Y-m-d H:i:s')."', progesInsentif='$progresData' where idInsentif='$idInsentif'";
			db($sql);						
			
			return $progresData."\t(".$progresData."%) ".getAngka($par[idDetail])." of ".getAngka($cntPegawai);		
		}
	}
	
	function endInsentif(){
		global $db,$s,$inp,$par,$cUsername;		
		$detailTemp = "tmp_insentif_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT);	
		db("DROP TABLE IF EXISTS ".$detailTemp);
		
		return "proses lembur selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$cNama;				
		$pegawaiInsentif = array();
		$nilaiInsentif = array();		
		$sql="select * from ess_cuti t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.sdmTime) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and t1.sdmCuti='t'";
		$res=db($sql);			
		while($r=mysql_fetch_array($res)){	
			$pegawaiInsentif["$r[idPegawai]"]=$r[idPegawai];				
			$nilaiInsentif["$r[idPegawai]"]+=$r[nilaiCuti];
		}		
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Insentif Cuti</h1>
					".getBread(ucwords("proses data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Bulan</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getBulan($par[bulanInsentif])."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Tahun</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$par[tahunInsentif]."&nbsp;</span>
					</p>					
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Tanggal Proses</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getTanggal(date('Y-m-d'),"t").", ".date('H:i')."&nbsp;</span>
					</p>					
					</td>
					<td width=\"50%\">
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Jumlah Pegawai</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getAngka(count($pegawaiInsentif))."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Total Nilai</label>
						<span style=\"margin-left:170px;\" class=\"field\">Rp. ".getAngka(array_sum($nilaiInsentif))."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Petugas</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$cNama."&nbsp;</span>
					</p>
					</td>
					</tr>
					</table>
					<div id=\"prosesBtn\" align=\"center\">						
						<input type=\"button\" class=\"cancel radius2\" value=\"Proses Cuti\" onclick=\"setInsentif('".getPar($par,"mode")."');\"/>
					</div>
					<div id=\"prosesImg\" align=\"center\" style=\"display:none;\">						
						<img src=\"styles/images/loaders/loader6.gif\">
					</div>
					<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">						
						<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
						<div class=\"bar2\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%;\"></div></div>
					</div>					
				</div>
			</form>	
			</div>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$areaCheck;
		if(empty($par[tahunInsentif])) $par[tahunInsentif] = date('Y');
						
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:20px; top:5px;\">
			<p>
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."'  and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")." 
				<span>Tahun : </span>
				".comboYear("par[tahunInsentif]", $par[tahunInsentif], "", "onchange=\"document.getElementById('form').submit();\"")."
			</p>
			</div>			
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"125\">Periode Cut Off</th>
					<th width=\"100\">Jml. Pegawai</th>
					<th width=\"100\">Nilai</th>
					<th width=\"100\">Proses</th>
					<th width=\"50\">Detail</th>
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='7'");
		list($idKomponen, $mulaiKomponen, $selesaiKomponen) = explode("\t", getField("select concat(idKomponen, '\t' ,mulaiKomponen, '\t', selesaiKomponen) from dta_komponen where kodeKomponen='".$kodeKomponen."'"));
		
		$sql="select t1.*, t2.namaUser from pay_insentif t1 left join ".$db['setting'].".app_user t2 on (t1.createBy=t2.username) where t1.tahunInsentif='".$par[tahunInsentif]."' and idKomponen='".$idKomponen."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrInsentif["$r[bulanInsentif]"] = $r;
		}
		
		for($i=1; $i<=12; $i++){
			if($mulaiKomponen > $selesaiKomponen){
				$bulanMulai = $i == 1 ? 12 : $i - 1;
				$tahunMulai = $i == 1 ? $par[tahunInsentif] - 1 : $par[tahunInsentif];			
			}else{
				$bulanMulai = $i;
				$tahunMulai = $par[tahunInsentif];
			}
						
			$mulaiPeriode = $tahunMulai."-".str_pad($bulanMulai, 2, "0", STR_PAD_LEFT)."-".$mulaiKomponen;
			$selesaiPeriode = $par[tahunInsentif]."-".str_pad($i, 2, "0", STR_PAD_LEFT)."-".$selesaiKomponen;
			
			$pegawaiInsentif = array();
			$nilaiInsentif = array();
			$sql="select * from ess_cuti t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.sdmTime) between '".$mulaiPeriode."' and '".$selesaiPeriode."' and t1.sdmCuti='t'";
			$res=db($sql);			
			while($r=mysql_fetch_array($res)){	
				$pegawaiInsentif["$r[idPegawai]"]=$r[idPegawai];				
				$nilaiInsentif["$r[idPegawai]"]+=$r[nilaiCuti];							
			}
			$jumlahPegawai = count($pegawaiInsentif) > 0 ? getAngka(count($pegawaiInsentif)) : "";
			$jumlahInsentif = array_sum($nilaiInsentif) > 0 ? getAngka(array_sum($nilaiInsentif)) : "";
						
			$detailInsentif = count($pegawaiInsentif) > 0 ? "<a href=\"?par[mode]=det&par[mulaiPeriode]=$mulaiPeriode&par[selesaiPeriode]=$selesaiPeriode&par[bulanInsentif]=$i".getPar($par,"mode,mulaiPeriode,selesaiPeriode,bulanInsentif")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>" : "";
			
			
			$arrProses = $arrInsentif[$i];
			list($selesaiTanggal, $selesaiWaktu) = explode(" ", $arrProses[selesaiInsentif]);
			
			$selesaiInsentif = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[mulaiPeriode]=$mulaiPeriode&par[selesaiPeriode]=$selesaiPeriode&par[bulanInsentif]=$i".getPar($par,"mode,mulaiPeriode,selesaiPeriode,bulanInsentif")."',925,425);\">".getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5)."</a>";
			
			if($par[tahunInsentif].str_pad($i, 2, "0", STR_PAD_LEFT) <= date('Ym') && $arrProses[progesInsentif] < 100 && isset($menuAccess[$s]["add"]))
			$selesaiInsentif = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[mulaiPeriode]=$mulaiPeriode&par[selesaiPeriode]=$selesaiPeriode&par[bulanInsentif]=$i".getPar($par,"mode,mulaiPeriode,selesaiPeriode,bulanInsentif")."',925,425);\" class=\"detil\">PROSES</a>";
			
			if(count($pegawaiInsentif) < 1) $selesaiInsentif = "";
			
			$text.="<tr>
					<td>$i.</td>
					<td>".getBulan($i)."</td>
					<td>".str_pad($mulaiKomponen, 2, "0", STR_PAD_LEFT)." ".getBulan($bulanMulai,"t")." s.d ".str_pad($selesaiKomponen, 2, "0", STR_PAD_LEFT)." ".getBulan($i,"t")."</td>
					<td align=\"center\">".$jumlahPegawai."</td>
					<td align=\"right\">".$jumlahInsentif."</td>
					<td align=\"center\">".$selesaiInsentif."</td>
					<td align=\"center\">".$detailInsentif."</td>
					<td>".$arrProses[namaUser]."</td>
					</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$areaCheck;						
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanInsentif])." ".$par[tahunInsentif]."</div>
				</h1>
				".getBread(ucwords("detail"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"float:right;\">
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
				
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
				".setPar($par,"idStatus,idLokasi,search,filter")."
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,mulaiPeriode,selesaiPeriode,bulanInsentif")."';\"/>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"subtable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Insentif</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
				
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$filter = " and t2.status='".$status."'";		
			
			if(!empty($par[idStatus]))
				$filter.= " and t2.cat='".$par[idStatus]."'";
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";			
			else if($par[search] == "NPP")
				$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";
						
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			
			$sql="select * from ess_cuti t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.sdmTime) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and t1.sdmCuti='t'".$filter;
			$res=db($sql);			
			while($r=mysql_fetch_array($res)){	
				$arrPegawai["$r[idPegawai]"]=$r[name]."\t".$r[reg_no]."\t".$r[pos_name]."\t".$r[div_id];
				$nilaiInsentif["$r[idPegawai]"]+=$r[nilaiCuti];
			}
			
			if(is_array($arrPegawai)){
				reset($arrPegawai);
				while (list($id, $val) = each($arrPegawai)){
				list($name, $reg_no, $pos_name, $div_id) = explode("\t", $val);
					
				$no++;	
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($name)."</td>
						<td>$reg_no</td>
						<td>$pos_name</td>
						<td>".$arrDivisi[$div_id]."</td>
						<td align=\"right\">".getAngka($nilaiInsentif[$id])."</td>
						<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=dta&par[idPegawai]=$id".getPar($par,"mode,idPegawai")."',925,550);\"><span>Detail</span></a></td>
					</tr>";
					$totGaji+=$nilaiInsentif[$id];				
				}
			}
			$text.="</tbody>
			<tfoot>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>SUB TOTAL</strong></td>
					<td style=\"text-align:right\"><span id=\"subTotal\"></span></td>
					<td></td>
				</tr>
				<tr>
					<td colspan=\"5\" style=\"text-align:right\"><strong>TOTAL</strong></td>
					<td style=\"text-align:right\"><span>".getAngka($totGaji)."</span></td>
					<td></td>
				</tr>
			</tfoot>
			</table>			
			</div>";
		return $text;
	}
	
	function data(){
		global $db,$s,$inp,$par,$arrTitle;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanInsentif])." ".$par[tahunInsentif]."</div>
				</h1>
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\">	
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
							<th colspan=\"2\" style=\"text-align:left;\">INSENTIF</th>							
						</tr>
					</thead>
					<tbody>";
					
			$sql="select * from ess_cuti t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where date(t1.sdmTime) between '".$par[mulaiPeriode]."' and '".$par[selesaiPeriode]."' and t1.sdmCuti='t' and t1.idPegawai='".$par[idPegawai]."'";
			$res=db($sql);			
			$no=1;
			while($r=mysql_fetch_array($res)){									
				$text.="<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
							<span style=\"float:left; width:30px;\">$no .</span>
							<span style=\"float:left;\">".$r[keteranganCuti]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($r[nilaiCuti])."</span>
							<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
					</tr>";
				$nilaiInsentif+=$r[nilaiCuti];
			}
			
			$text.="<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
							<span style=\"float:left; width:30px;\">&nbsp;</span>
							<span style=\"float:left;\"><strong>Jumlah</strong></span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($nilaiInsentif)."</span>
							<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
					</tr>";
			
			$text.="</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>						
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang(setAngka(getAngka($nilaiInsentif))))." Rupiah</span>
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
					
				</div>
				</form>
			</div>";
		
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "end":
				$text = endInsentif();
			break;
			case "dat":
				$text = setData();
			break;
			case "tab":
				$text = setTable();
			break;
			
			case "dta":
				$text = data();
			break;
			case "det":
				$text = detail();
			break;
			case "set":
				$text = isset($menuAccess[$s]["add"]) ? form() : lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
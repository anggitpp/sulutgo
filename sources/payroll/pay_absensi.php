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
		
		$sql="delete from ".$detailInsentif." where flagProses='1'";
		db($sql);
		
		if(!getField("select idProses from pay_proses where bulanProses='".$par[bulanInsentif]."' and tahunProses='".$par[tahunInsentif]."'")){
			$idProses = getField("select idProses from pay_proses order by idProses desc limit 1")+1;
			$sql="insert into pay_proses (idProses, bulanProses, tahunProses, detailProses) values ('$idProses', '$par[bulanInsentif]', '$par[tahunInsentif]','$detailInsentif')";
			db($sql);
		}
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
		$idInsentif = getField("select idInsentif from pay_insentif order by idInsentif desc limit 1")+1;		
		
		$sql=getField("select idInsentif from pay_insentif where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'")?
		"update pay_insentif set mulaiInsentif='".date('Y-m-d H:i:s')."', pegawaiInsentif='0', nilaiInsentif='0', detailInsentif='".$detailInsentif."', updateBy='$$cUsername', updateTime='".date('Y-m-d H:i:s')."' where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'":
		"insert into pay_insentif (idInsentif, idKomponen, bulanInsentif, tahunInsentif, mulaiInsentif, pegawaiInsentif, nilaiInsentif, detailInsentif, createBy, createTime) values ('$idInsentif', '$idKomponen', '$par[bulanInsentif]', '$par[tahunInsentif]', '".date('Y-m-d H:i:s')."', '0', '0', '".$detailInsentif."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sql="insert into ".$detailTemp." (idInsentif, idPegawai, createBy, createTime) select '".$idInsentif."' as idInsentif, id, '$cUsername' as createBy, '".date('Y-m-d H:i:s')."' as createTime from emp where status='".$status."' order by id";		
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
			#GAJI POKOK			
			$tanggalInsentif = date("t", strtotime($par[tahunInsentif]."-".$par[bulanInsentif]."-01"));
			$nilaiPokok = getField("select nilaiPokok from pay_pokok where idPegawai='".$idPegawai."' and tanggalPokok<='".$par[tahunInsentif]."-".$par[bulanInsentif]."-".$tanggalInsentif."' order by tanggalPokok desc limit 1");		
		
			#TUNJANGAN JABATAN
			$sql="select * from emp_phist where parent_id='".$idPegawai."' and status='1'";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$nilaiTunjangan = $r[location] == getField("select kodeData from mst_data where kodeCategory='".$arrParameter[7]."' order by urutanData limit 1") ? "pusatTunjangan" : "cabangTunjangan";			
			$nilaiJabatan = getField("select sum(t1.".$nilaiTunjangan.") from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r[rank]."' and idGrade='".$r[grade]."'");
			
			# SET KOMPONEN GAJI			
			$arrData = arrayQuery("select idKomponen, tipeKomponen from pay_komponen where idPegawai='".$idPegawai."'");
			if(count($arrData) < 1){					
				$arrData = array();
				$idStatus = getField("select cat from emp where id='".$idPegawai."'");
				$arrData = arrayQuery("select idKomponen, tipeMaster from pay_master where idStatus='".$idStatus."'");
			}
			
			$sql="select * from dta_komponen where statusKomponen='t' and realisasiKomponen='t' and dasarKomponen!='3' order by tipeKomponen desc,urutanKomponen";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){				
				$arrKomponen["$r[idKomponen]"] = $r;		
			}
			
			if(is_array($arrData)){
				reset($arrData);
				while (list($idKomponen, $tipeKomponen) = each($arrData)){					
					$r = $arrKomponen[$idKomponen];							
					if($r[realisasiKomponen] == "t"){				
						$nilaiKomponen = 0;
						
						# Gaji Pokok
						if($r[flagKomponen] == 1){ 						
							$nilaiKomponen = $nilaiPokok;
						}
						
						# Tunjangan Jabatan
						if($r[flagKomponen] == 2){
							$nilaiKomponen = $nilaiJabatan;
						}
						
						# Komponen Penerimaan & Potongan
						if($r[flagKomponen] == 0){
							#fixed
							if($r[dasarKomponen] == 4){
								$nilaiKomponen = $r[nilaiKomponen];
							}
						
							#formula
							if($r[dasarKomponen] < 2 ){
								$nilaiKomponen = $r[nilaiKomponen] * $r[maxKomponen] * $dasarKomponen[$idPegawai]["$r[idPengali]"];
							}
						}
						
						$dasarKomponen[$idPegawai][$idKomponen] = $nilaiKomponen;						
					}
				}
			}
						
			$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
			$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
			
			$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanInsentif]."' and tahunProses='".$par[tahunInsentif]."'");
			$idInsentif = getField("select idInsentif from pay_insentif where bulanInsentif='".$par[bulanInsentif]."' and tahunInsentif='".$par[tahunInsentif]."' and idKomponen='$idKomponen'");
			
			$sql_="select * from dta_komponen where idKomponen='".$idKomponen."'";
			$res_=db($sql_);
			$r_=mysql_fetch_array($res_);		
			
			$sql="select *, abs(hour(durasiAbsen)) as durasi from dta_absen t1 join emp t2 on (t1.idPegawai=t2.id) where year(t1.mulaiAbsen)='".$par[tahunInsentif]."' and month(t1.mulaiAbsen)='".$par[bulanInsentif]."' and abs(hour(durasiAbsen)) > 7 and idPegawai='".$idPegawai."'";
			$res=db($sql);
			$nilaiKomponen=0;
			while($r=mysql_fetch_array($res)){			
				$nilaiKomponen+= empty($r_[idPengali]) ? 
				$r_[nilaiKomponen] * $r_[maxKomponen]:
				$r_[nilaiKomponen] * $r_[maxKomponen] * $dasarKomponen["$r[idPegawai]"]["$r_[idPengali]"];
			}
			
			if($nilaiKomponen > 0){
				$sql="insert into ".$detailInsentif." (idProses, idPegawai, idKomponen, nilaiProses, flagProses, createBy, createTime) values ('$idProses', '$idPegawai', '$idKomponen', '".setAngka($nilaiKomponen)."', '1', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
				
				$nilaiInsentif = $r_[tipeKomponen] == "t" ? $nilaiKomponen : $nilaiKomponen * -1;
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
		
		return "proses absensi selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$cNama;				
		$pegawaiInsentif = 0;
		$nilaiInsentif = 0;		
		$tanggalInsentif = date("t", strtotime($par[tahunInsentif]."-".$par[bulanInsentif]."-01"));
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sql__="select * from emp where status='".$status."' order by id";
		$res__=db($sql__);		
		while($r__=mysql_fetch_array($res__)){
			$idPegawai = $r__[id];
			
			#GAJI POKOK			
			$nilaiPokok = getField("select nilaiPokok from pay_pokok where idPegawai='".$idPegawai."' and tanggalPokok<='".$par[tahunInsentif]."-".$par[bulanInsentif]."-".$tanggalInsentif."' order by tanggalPokok desc limit 1");		
		
			#TUNJANGAN JABATAN
			$sql="select * from emp_phist where parent_id='".$idPegawai."' and status='1'";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$nilaiTunjangan = $r[location] == getField("select kodeData from mst_data where kodeCategory='".$arrParameter[7]."' order by urutanData limit 1") ? "pusatTunjangan" : "cabangTunjangan";			
			$nilaiJabatan = getField("select sum(t1.".$nilaiTunjangan.") from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r[rank]."' and idGrade='".$r[grade]."'");
			
			# SET KOMPONEN GAJI			
			$arrData = arrayQuery("select idKomponen, tipeKomponen from pay_komponen where idPegawai='".$idPegawai."'");
			if(count($arrData) < 1){					
				$arrData = array();
				$idStatus = getField("select cat from emp where id='".$idPegawai."'");
				$arrData = arrayQuery("select idKomponen, tipeMaster from pay_master where idStatus='".$idStatus."'");
			}
			
			$sql="select * from dta_komponen where statusKomponen='t' and realisasiKomponen='t' and dasarKomponen!='3' order by tipeKomponen desc,urutanKomponen";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){				
				$arrKomponen["$r[idKomponen]"] = $r;		
			}
			
			if(is_array($arrData)){
				reset($arrData);
				while (list($idKomponen, $tipeKomponen) = each($arrData)){					
					$r = $arrKomponen[$idKomponen];							
					if($r[realisasiKomponen] == "t"){				
						$nilaiKomponen = 0;
						
						# Gaji Pokok
						if($r[flagKomponen] == 1){ 						
							$nilaiKomponen = $nilaiPokok;
						}
						
						# Tunjangan Jabatan
						if($r[flagKomponen] == 2){
							$nilaiKomponen = $nilaiJabatan;
						}
						
						# Komponen Penerimaan & Potongan
						if($r[flagKomponen] == 0){
							#fixed
							if($r[dasarKomponen] == 4){
								$nilaiKomponen = $r[nilaiKomponen];
							}
						
							#formula
							if($r[dasarKomponen] < 2 ){
								$nilaiKomponen = $r[nilaiKomponen] * $r[maxKomponen] * $dasarKomponen[$idPegawai]["$r[idPengali]"];
							}
						}
						
						$dasarKomponen[$idPegawai][$idKomponen] = $nilaiKomponen;						
					}
				}
			}
			$pegawaiInsentif++;			
		}
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
		$sql_="select * from dta_komponen where idKomponen='".$idKomponen."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);		
		
		$sql="select *, abs(hour(durasiAbsen)) as durasi from dta_absen t1 join emp t2 on (t1.idPegawai=t2.id) where year(t1.mulaiAbsen)='$par[tahunInsentif]' and month(t1.mulaiAbsen)='$par[bulanInsentif]' and abs(hour(durasiAbsen)) > 7";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$nilaiInsentif+= empty($r_[idPengali]) ? 
			$r_[nilaiKomponen] * $r_[maxKomponen]:
			$r_[nilaiKomponen] * $r_[maxKomponen] * $dasarKomponen["$r[idPegawai]"]["$r_[idPengali]"];
		}
				
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Insentif Absensi</h1>
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
						<span style=\"margin-left:170px;\" class=\"field\">".getAngka($pegawaiInsentif)."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Total Nilai</label>
						<span style=\"margin-left:170px;\" class=\"field\">Rp. ".getAngka($nilaiInsentif)."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Petugas</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$cNama."&nbsp;</span>
					</p>
					</td>
					</tr>
					</table>
					<div id=\"prosesBtn\" align=\"center\">						
						<input type=\"button\" class=\"cancel radius2\" value=\"Proses Absensi\" onclick=\"setInsentif('".getPar($par,"mode")."');\"/>
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
				Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")." 
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
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Total Nilai</th>
					<th width=\"125\">Insentif</th>
					<th width=\"50\">Detail</th>
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
		
		$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
		$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
		
		$sql="select t1.*, t2.namaUser from pay_insentif t1 left join ".$db['setting'].".app_user t2 on (t1.createBy=t2.username) where t1.tahunInsentif='$par[tahunInsentif]' and idKomponen='".$idKomponen."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrInsentif["$r[bulanInsentif]"] = $r;
		}
		
		for($i=1; $i<=12; $i++){
			$r = $arrInsentif[$i];
			
			list($selesaiTanggal, $selesaiWaktu) = explode(" ", $r[selesaiInsentif]);
			
			$detailInsentif = "";
			$pegawaiInsentif = getAngka($r[pegawaiInsentif]) > 0 ? getAngka($r[pegawaiInsentif]) : "";
			$nilaiInsentif = getAngka($r[nilaiInsentif]) > 0 ? getAngka($r[nilaiInsentif]) : "";
			$selesaiInsentif = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[bulanInsentif]=$i".getPar($par,"mode,bulanInsentif")."',925,425);\">".getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5)."</a>";
			
			if($par[tahunInsentif].str_pad($i, 2, "0", STR_PAD_LEFT) <= date('Ym') && $r[progesInsentif] < 100)
			$selesaiInsentif = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[bulanInsentif]=$i".getPar($par,"mode,bulanInsentif")."',925,425);\" class=\"detil\">PROSES</a>";				
			
			if($r[progesInsentif] == 100)
			$detailInsentif = "<a href=\"?par[mode]=det&par[bulanInsentif]=$r[bulanInsentif]".getPar($par,"mode,bulanInsentif")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";
			
			
			$text.="<tr>
					<td>$i.</td>
					<td>".getBulan($i)."</td>
					<td align=\"center\">".$pegawaiInsentif."</td>
					<td align=\"right\">".$nilaiInsentif."</td>
					<td align=\"center\">".$selesaiInsentif."</td>
					<td align=\"center\">".$detailInsentif."</td>
					<td>$r[namaUser]</td>
					</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;						
		
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
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
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
				".setPar($par,"idStatus,search,filter")."
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,bulanInsentif")."';\"/>
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
			$filter = "where t1.cat=".$par[idStatus]." and t1.status='".$status."'";		
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";
			
			$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
			$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
			$sql="select * from pay_proses_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idKomponen='".$idKomponen."' order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
			}
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(isset($arrGaji["$r[id]"])){
					$no++;	
					$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$r[reg_no]</td>
							<td>$r[pos_name]</td>
							<td>".$arrDivisi["$r[div_id]"]."</td>
							<td align=\"right\">".getAngka($arrGaji["$r[id]"])."</td>
							<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=dta&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,550);\"><span>Detail</span></a></td>
						</tr>";
					$totGaji+=$arrGaji["$r[id]"];
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
				
				$kodeKomponen = getField("select nilaiParameter from pay_parameter where idParameter='2'");
				$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen."'");
				$sql="select * from pay_proses_".$par[tahunInsentif].str_pad($par[bulanInsentif], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idKomponen='".$idKomponen."' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){															
					$text.="<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
							<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$r["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($r["nilaiProses"])."</span>
							<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>";			
					$totKomponen+=$r[nilaiProses];
				}				
				
				$text.="</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>						
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang($totKomponen))." Rupiah</span>
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
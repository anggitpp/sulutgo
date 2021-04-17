<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
				
	function setTable(){
		global $db,$s,$db,$inp,$par,$cUsername,$arrParameter,$areaCheck;						
		$detailTemp = "tmp_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."_".$par[idLokasi]."_".$par[idJenis];
		$detailProses = "pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
		$tanggalProses = date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"));
		
		db("DROP TABLE IF EXISTS ".$detailTemp);				
		db("CREATE TABLE IF NOT EXISTS ".$detailTemp." (
			  idDetail int(11) NOT NULL  AUTO_INCREMENT,
			  idProses int(11) NOT NULL,
			  idPegawai int(11) NOT NULL,			  
			  createBy varchar(30) NOT NULL,
			  createTime datetime NOT NULL,			  
			  PRIMARY KEY (idDetail)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1");
				
		db("CREATE TABLE IF NOT EXISTS ".$detailProses." (
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
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
		
		$filter="";
		if(!empty($par[idLokasi]))$filter.= " and t2.group_id='".$par[idLokasi]."'";
		if(!empty($par[idJenis]))$filter.= " and t2.payroll_id='".$par[idJenis]."'";
		//$filter.=" and t1.id='98'";
		
		$arrId= arrayQuery("select t1.id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) where t1.status='".$status."' and t2.status='1' ".$filter." and t2.group_id IN ( $areaCheck ) order by t1.id");
		
		$sql="delete from ".$detailProses." where flagProses='0' and idPegawai in ('".implode("','", $arrId)."')";
		db($sql);	
				
		$idProses = getField("select idProses from pay_proses order by idProses desc limit 1")+1;
		$sql=getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."' and idLokasi='".$par[idLokasi]."' and idJenis='".$par[idJenis]."'")?
		"update pay_proses set mulaiProses='".date('Y-m-d H:i:s')."', pegawaiProses='0', nilaiProses='0', detailProses='".$detailProses."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."' and idLokasi='".$par[idLokasi]."' and idJenis='".$par[idJenis]."'":
		"insert into pay_proses (idProses, idLokasi, idJenis, bulanProses, tahunProses, mulaiProses, pegawaiProses, nilaiProses, detailProses, createBy, createTime, updateBy, updateTime) values ('$idProses', '$par[idLokasi]', '$par[idJenis]', '$par[bulanProses]', '$par[tahunProses]', '".date('Y-m-d H:i:s')."', '0', '0', '".$detailProses."', '$cUsername', '".date('Y-m-d H:i:s')."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
			
		$sql="insert into ".$detailTemp." (idProses, idPegawai, createBy, createTime) select '".$idProses."' as idProses, t1.id, '$cUsername' as createBy, '".date('Y-m-d H:i:s')."' as createTime from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) where t1.status='".$status."' and t2.status='1' ".$filter." and t2.group_id IN ( $areaCheck ) order by t1.id";
		db($sql);
	}
	
	function setData(){
		global $db,$s,$db,$inp,$par,$cUsername,$arrParameter;
		$detailTemp = "tmp_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."_".$par[idLokasi]."_".$par[idJenis];
		$detailProses = "pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
		
		$cntPegawai = getField("select count(*) from ".$detailTemp."");
		$idPegawai = getField("select idPegawai from ".$detailTemp." where idDetail='$par[idDetail]'");
		
		$progresData = getAngka($par[idDetail]/$cntPegawai * 100);						
		if(!empty($idPegawai)){	
			$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."' and idLokasi='".$par[idLokasi]."' and idJenis='".$par[idJenis]."'");
						
			#PAY MANUAL
			$sql__ = "select * from pay_upload where bulanUpload='".$par[bulanProses]."' and tahunUpload='".$par[tahunProses]."' and idPegawai='".$idPegawai."'";
			$res__ = db($sql__);
			while($r__ = mysql_fetch_array($res__)){
				$arrManual["".$r__[idKomponen].""]+=$r__[nilaiUpload];
			}
			
			#GAJI POKOK
			$tanggalProses = date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"));

			$kota = getField("select location from emp_phist where parent_id = '$idPegawai' and status = '1'");
			$flagPokok = getField("select flagPokok from pay_pokok where idPegawai='".$idPegawai."' and tanggalPokok<='".$par[tahunProses]."-".$par[bulanProses]."-".$tanggalProses."' order by tanggalPokok desc limit 1");
			if($flagPokok == "f"){
				$nilaiPokok = setAngka(getField("select ump from mst_ump where kodeKota='$kota'"));
			}else{
				$nilaiPokok = getField("select nilaiPokok from pay_pokok where idPegawai='".$idPegawai."' and tanggalPokok<='".$par[tahunProses]."-".$par[bulanProses]."-".$tanggalProses."' order by tanggalPokok desc limit 1");
			}	
						
			#TUNJANGAN JABATAN
			$sql="select * from emp_phist where parent_id='".$idPegawai."' and status='1'";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$nilaiTunjangan = $r[location] == getField("select kodeData from mst_data where kodeCategory='".$arrParameter[7]."' order by urutanData limit 1") ? "pusatTunjangan" : "cabangTunjangan";			
			$nilaiJabatan = getField("select sum(t1.".$nilaiTunjangan.") from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r[rank]."' and idGrade='".$r[grade]."'");
			
			#POTONGAN KOPERASI
			$nilaiKoperasi = getField("select sum(nilai) from pay_koperasi where idPangkat='".$r[rank]."'");
			
			# SET KOMPONEN GAJI						
			$arrData = arrayQuery("select t1.idKomponen, t1.tipeMaster, t1.idJenis from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' order by t2.tipeKomponen desc,t2.urutanKomponen");
			
			$sql="select t1.* from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$idPegawai."' order by t2.tipeKomponen desc,t2.urutanKomponen";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrData["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[nilaiKomponen]."\t".$r[flagKomponen];
				$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[statusKomponen];
			}
			
			$sql="select * from dta_komponen where statusKomponen='t' and realisasiKomponen='t' order by tipeKomponen desc,urutanKomponen";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){				
				$arrKomponen["$r[idKomponen]"] = $r;		
			}
			
			$nilaiTHP=0;
			if(is_array($arrData)){
				//asort($arrData);
				reset($arrData);
				while (list($idKomponen, $tipeKomponen) = each($arrData)){					
					$cnt++;
					$r = $arrKomponen[$idKomponen];		
					//$result.=$idKomponen."<br>";
					if($r[realisasiKomponen] == "t"){				
						$nilaiKomponen = 0;
						
						# Gaji Pokok
						if($r[flagKomponen] == 1){ 						
							if($r[dasarKomponen] == 4){
								$nilaiKomponen = $r[nilaiKomponen];
							}else{
								$nilaiKomponen = $nilaiPokok;
							}
						}
						
						# Tunjangan Jabatan
						if($r[flagKomponen] == 2){
							$nilaiKomponen = $nilaiJabatan;
						}
						
						# Potongan Pinjaman
						/*
						if($r[flagKomponen] == 3){
							list($idPinjaman, $idAngsuran, $nilaiKomponen) = explode("\t", getField("select concat(t2.idPinjaman, '\t', t2.idAngsuran, '\t', t2.nilaiAngsuran) from ess_pinjaman t1 join ess_angsuran t2 on (t1.idPinjaman=t2.idPinjaman) where t1.idPegawai='".$idPegawai."' and t1.persetujuanPinjaman='t' and t1.sdmPinjaman='t' and month(tanggalAngsuran)='".$par[bulanProses]."' and year(tanggalAngsuran)='".$par[tahunProses]."'"));
							
							$sql="update ess_angsuran set statusAngsuran='t', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPinjaman='$idPinjaman' and idAngsuran='$idAngsuran'";
							db($sql);
						}
						*/
						
						# Potongan Koperasi
						if($r[flagKomponen] == 5){
							$nilaiKomponen = $nilaiKoperasi;
						}	
						
						# Komponen PPh21
						if($r[flagKomponen] == 4){
							$nilaiKomponen = 0;
						}
						
						# Komponen Penerimaan & Potongan
						if($r[flagKomponen] == 0){
							#fixed
							if($r[dasarKomponen] == 4){
								$nilaiKomponen = $r[nilaiKomponen];
							}
						
							#proses
							if($r[dasarKomponen] == 3){
								$nilaiKomponen = getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$idKomponen."'");
							}
						
							#formula
							if($r[dasarKomponen] < 2 ){	
								$nilaiKomponen = 0;
								$sql___="select * from pay_formula_detail where idFormula='".$r[idPengali]."'";
								$res___=db($sql___);
								while($r___=mysql_fetch_array($res___)){
									$kodeKomponen__ = getField("select kodeKomponen from dta_komponen where idKomponen='".$r___[idKomponen]."'");
									$idKomponen__ = arrayQuery("select idKomponen from dta_komponen where kodeKomponen='".$kodeKomponen__."'");
									
									$nilaiProses___ = $r___[tipeDetail] == "u" ? 
									setAngka(getField("select ump from mst_ump where kodeKota='".getField("select location from dta_pegawai where id='".$idPegawai."'")."'")):
									getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen in ('".implode("', '", $idKomponen__)."')");
									
									
									if($r___[operasi1Detail] == "*")
										$nilaiDetail = $r___[nilaiDetail] * $nilaiProses___;
									if($r___[operasi1Detail] == "/")
										$nilaiDetail = $r___[nilaiDetail] / $nilaiProses___;
									if($r___[operasi1Detail] == "+")
										$nilaiDetail = $r___[nilaiDetail] + $nilaiProses___;
									if($r___[operasi1Detail] == "-")
										$nilaiDetail = $r___[nilaiDetail] - $nilaiProses___;
									
									if($r___[operasi2Detail] == "+")
										$nilaiKomponen += $nilaiDetail;
									
									if($r___[operasi2Detail] == "-")
										$nilaiKomponen -= $nilaiDetail;
									
									//$result.= $r[idKomponen]." X ".$r___[idKomponen]." - ".$nilaiDetail."<br>";
								}
							}
						}
						
						if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
							$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
						}else{
							$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
						}
						if($r[maxKomponen] > 0 && $nilaiKomponen > $r[maxKomponen]) $nilaiKomponen = $r[maxKomponen];
						
						list($nilaiData, $flagData) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
						$nilaiData = ($nilaiData > 0  && $flagData == "t")? $nilaiData : $nilaiKomponen;
						
						$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiData : 0;
					
						#pay_upload
						if(isset($arrManual["".$r[idKomponen].""])) $nilaiKomponen = $arrManual["".$r[idKomponen].""];
						
						$sql="insert into ".$detailProses." (idProses, idPegawai, idKomponen, nilaiProses, flagProses, createBy, createTime) values ('$idProses', '$idPegawai', '$idKomponen', '".setAngka($nilaiKomponen)."', '0', '$cUsername', '".date('Y-m-d H:i:s')."')";
						if(!in_array($r[dasarKomponen], array(3)) && $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f" && ($nilaiKomponen > 0 || in_array($r[idKomponen], array(3,8,285,286)))){
							db($sql);
						}else{
							$nilaiKomponen = 0;
						}
											
						if(in_array($r[dasarKomponen], array(3))){
							$nilaiKomponen = getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$idKomponen."' and flagProses > 0");
						}					
											
						$nilaiProses = $r[tipeKomponen] == "t" ? $nilaiKomponen : $nilaiKomponen * -1;
						$sql="update pay_proses set nilaiProses=nilaiProses + ".setAngka($nilaiProses)."  where idProses='$idProses'";
						db($sql);
						
						#$result.= $r[idKomponen]." - ".$r[namaKomponen]." - ".$nilaiKomponen.""."<br>";			
									
						#update PPh21
						if(in_array($r[idKomponen], array(96,97))){
							$nilaiTHR = $nilaiKomponen;
							$nilaiKomponen = 0;
						}
						
						
						if($r[pajakKomponen] == "t" && $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f"){
							$nilaiTHP+= $r[tipeKomponen] == "t" ? $nilaiKomponen : $nilaiKomponen * -1;				
							if($r[tipeKomponen] == "t" || $r[idKomponen] == 40){
								if($r[idKomponen] == 40) // potongan absen mengurasi gaji bruto
									$nilaiBruto-=$nilaiKomponen;	
								else
									$nilaiBruto+=$nilaiKomponen;
								//if($nilaiKomponen != 0) $result.= $r[idKomponen]." - ".$r[namaKomponen]." - ".$nilaiKomponen."<br>";
							}else{
								$potonganBruto+=$nilaiKomponen;
								//if($nilaiKomponen != 0) $result.= $r[idKomponen]." - ".$r[namaKomponen]." - ".($nilaiKomponen*12)."<br>";
							}	
						}	
						
						if($cnt == count($arrData)){
							list($join_date, $npwp_no, $ptkp) = explode("\t", getField("select concat(coalesce(join_date,''), '\t', coalesce(npwp_no,''), '\t', coalesce(ptkp,'')) from emp where id='".$idPegawai."'"));
							list($Y,$m,$d) = explode("-", $join_date);
							$start_date = 12;
							if($Y == $par[tahunProses]){
								$b = $m == 12 ? 1 : $m + 1;
								$t = $m == 12 ? $Y + 1 : $Y;
								$bulan = $d > 15 ? $b : $m;
								$tahun = $d > 15 ? $t : $Y;
							}
						
							$masaPajak = selisihBulan($tahun."-".str_pad($bulan, 2, "0", STR_PAD_LEFT)."-01", $par[tahunProses]."-12-01") + 1;
							$masaPajak = $masaPajak > 12 ? 12 : $masaPajak;
							$totalBruto = $nilaiBruto * $masaPajak + $nilaiTHR;
							
							if(getField("select gender from emp where id='".$idPegawai."'") == "F"){
								$idPerkawinan = getField("select substring(lower(trim(namaData)),1,1) from mst_data where kodeData='".$ptkp."'") == "k" ? 
								getField("select kodeData from mst_data where lower(trim(namaData))='tk/0'") : $ptkp;
								$nilaiPtkp = getField("SELECT nilaiPPh FROM pay_pph where tahunPPh='".$par[tahunProses]."' and idPerkawinan='".$idPerkawinan."'");
							}else{
								$nilaiPtkp = getField("SELECT t2.nilaiPPh FROM emp t1 join pay_pph t2 on (t1.ptkp=t2.idPerkawinan) where t2.tahunPPh='".$par[tahunProses]."' and t1.id='".$idPegawai."'");
							}
							
							// NETT
							if(getField("select pphCatatan from pay_catatan where idPegawai='".$idPegawai."' and pphCatatan='t'")){
								$tempPph = 0;
								for($i=1; $i<=12; $i++){
									$tempBruto = $nilaiBruto + $tempPph; 
									$tempPotongan= 5/100 * $tempBruto > 500000 ? 500000 : 5/100 * $tempBruto;
									$nilaiNet = $tempBruto-$potonganBruto-$tempPotongan;
									$totalNet = $nilaiNet * $masaPajak;
								
									$nilaiPkp = $totalNet - $nilaiPtkp;
									$persenPkp = in_array($npwp_no, array("", "000000000000000")) ? 1.2 : 1;
								
									//RESULT
									//$result.= $i." - ".$tempPph."<br>";
								
									$nilaiPph = 0;
									if($nilaiPkp > 500000000){								
										$nilaiPph+= $persenPkp * 30/100 * ($nilaiPkp - 500000000);								
										$nilaiPph+= $persenPkp * 25/100 * (500000000 - 250000000);
										$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 250000000){								
										$nilaiPph+= $persenPkp * 25/100 * ($nilaiPkp - 250000000);
										$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 50000000){																
										$nilaiPph+= $persenPkp * 15/100 * ($nilaiPkp - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 0){		
										$nilaiPph+= $persenPkp * 5/100 * $nilaiPkp;
									}else{								
										$nilaiPph = $persenPkp * 0;
									}
									$nilaiPph = $nilaiPph / $masaPajak;
									$tempPph = $nilaiPph;
								}
								$defPph = $nilaiPph;
								
								
								// BONUS
								$tempPph = 0;
								$minPph = $nilaiPph * $masaPajak;
								$nilaiBruto= $totalBruto+($nilaiPph * $masaPajak);
								for($i=1; $i<=12; $i++){
									$tempBruto = $nilaiBruto + $tempPph; 
									$tempPotongan= 5/100 * $tempBruto > 6000000 ? 6000000 : 5/100 * $tempBruto;
									$nilaiNet = $tempBruto-($potonganBruto * $masaPajak)-$tempPotongan;
									$totalNet = $nilaiNet;
								
									$nilaiPkp = $totalNet - $nilaiPtkp;
									$persenPkp = in_array($npwp_no, array("", "000000000000000")) ? 1.2 : 1;
								
									//RESULT
									//$result.= $i." - ".$totalNet."<br>";
								
									$nilaiPph = 0;
									if($nilaiPkp > 500000000){								
										$nilaiPph+= $persenPkp * 30/100 * ($nilaiPkp - 500000000);								
										$nilaiPph+= $persenPkp * 25/100 * (500000000 - 250000000);
										$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 250000000){								
										$nilaiPph+= $persenPkp * 25/100 * ($nilaiPkp - 250000000);
										$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 50000000){																
										$nilaiPph+= $persenPkp * 15/100 * ($nilaiPkp - 50000000);
										$nilaiPph+= $persenPkp * 5/100 * 50000000;
									}else if($nilaiPkp > 0){		
										$nilaiPph+= $persenPkp * 5/100 * $nilaiPkp;
									}else{								
										$nilaiPph = $persenPkp * 0;
									}
									$nilaiPph = $nilaiPph - $minPph;
									$tempPph = $nilaiPph;
								}
								if(empty($nilaiTHR)) $nilaiPph = 0;
								
							// GROSS
							}else{
								$potonganBruto+= 5/100 * $nilaiBruto > 500000 ? 500000 : 5/100 * $nilaiBruto;
							
								$nilaiNet = $nilaiBruto-$potonganBruto;
								$totalNet = $nilaiNet * $masaPajak;
								
								$nilaiPkp = $totalNet - $nilaiPtkp;
								$persenPkp = in_array($npwp_no, array("", "000000000000000")) ? 1.2 : 1;
								
								//RESULT
								$result.= "GROSS : ".$nilaiBruto."<br>";
								
								$nilaiPph = 0;
								if($nilaiPkp > 500000000){								
									$nilaiPph+= $persenPkp * 30/100 * ($nilaiPkp - 500000000);								
									$nilaiPph+= $persenPkp * 25/100 * (500000000 - 250000000);
									$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
									$nilaiPph+= $persenPkp * 5/100 * 50000000;
								}else if($nilaiPkp > 250000000){								
									$nilaiPph+= $persenPkp * 25/100 * ($nilaiPkp - 250000000);
									$nilaiPph+= $persenPkp * 15/100 * (250000000 - 50000000);
									$nilaiPph+= $persenPkp * 5/100 * 50000000;
								}else if($nilaiPkp > 50000000){																
									$nilaiPph+= $persenPkp * 15/100 * ($nilaiPkp - 50000000);
									$nilaiPph+= $persenPkp * 5/100 * 50000000;
								}else if($nilaiPkp > 0){		
									$nilaiPph+= $persenPkp * 5/100 * $nilaiPkp;
								}else{								
									$nilaiPph = $persenPkp * 0;
								}
								
								$nilaiPph = $nilaiPph / $masaPajak;
							}
							
							//RESULT
							$result.= "PPH 21 : ".($nilaiPph + $defPph)."<br>";
							//$nilaiPph = $nilaiPph + $defPph;
							
							/*
							#pay_upload
							$idKomponen = getField("select t1.idKomponen from dta_komponen t1 join pay_upload t2 on (t1.idKomponen=t2.idKomponen) where t1.flagKomponen='4' and tipeKomponen='".$r[tipeKomponen]."' limit 1");
							if(isset($arrManual["".$idKomponen.""])) $nilaiPph = $arrManual["".$idKomponen.""];
							*/
							
							$sql="update dta_komponen t1 join ".$detailProses." t2 on (t1.idKomponen=t2.idKomponen) set t2.nilaiProses='".setAngka($defPph)."' where t1.idKomponen in ('3','8') and t2.idPegawai='".$idPegawai."'";
							db($sql);
							
							$sql="update dta_komponen t1 join ".$detailProses." t2 on (t1.idKomponen=t2.idKomponen) set t2.nilaiProses='".setAngka($nilaiPph)."' where t1.idKomponen in ('285', '286') and t2.idPegawai='".$idPegawai."'";
							db($sql);
						}
						#end PPh21	
					}				
				}
			}
			#END KOMPONEN GAJI
						
			$sql="update pay_proses set pegawaiProses=pegawaiProses + 1, selesaiProses='".date('Y-m-d H:i:s')."', progesProses='$progresData' where idProses='$idProses'";
			db($sql);
			
			return $progresData."\t(".$progresData."%) ".getAngka($par[idDetail])." of ".getAngka($cntPegawai)."\t".$result;		
		}
	}
	
	function endProses(){
		global $db,$s,$db,$inp,$par,$cUsername;		
		$detailTemp = "tmp_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);	
		db("DROP TABLE IF EXISTS ".$detailTemp);
		
		return "proses gaji selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	function form(){
		global $db,$s,$db,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$cNama;				
		
		$pegawaiProses = 0;
		$nilaiProses = 0;		
		$tanggalProses = date("t", strtotime($par[tahunProses]."-".$par[bulanProses]."-01"));		
		$detailProses = getField("select detailProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'");
				
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Proses Gaji</h1>
					".getBread(ucwords("proses data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Lokasi Proses</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getField("select namaData from mst_data where kodeData='".$par[idLokasi]."'")."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Kategori Gaji</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getField("select namaJenis from pay_jenis where idJenis='".$par[idJenis]."'")."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Waktu Proses</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getTanggal(date('Y-m-d'),"t").", ".date('H:i')."&nbsp;</span>
					</p>									
					</td>
					<td width=\"50%\">
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Bulan</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getBulan($par[bulanProses])."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Tahun</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$par[tahunProses]."&nbsp;</span>
					</p>	
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Petugas</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$cNama."&nbsp;</span>
					</p>
					</td>
					</tr>
					</table>
					<div id=\"progresRes\"></div>
					<div id=\"prosesBtn\" align=\"center\">						
						<input type=\"button\" class=\"cancel radius2\" value=\"Proses Gaji\" onclick=\"setProses('".getPar($par,"mode")."');\"/>
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
		global $db,$s,$db,$inp,$par,$arrTitle,$menuAccess,$arrParameter, $areaCheck;
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');
		if(empty($par[idLokasi])) $par[idLokasi] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData limit 1");
		if(empty($par[idJenis])) $par[idJenis] = getField("select idJenis from pay_jenis where statusJenis='t' order by idJenis limit 1");
		
		$bulanProses = "";
		for($i=1; $i<=12; $i++){
			if($par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT) <= date('Ym')) $bulanProses=$i;
		}
	
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<fieldset style=\"
				-moz-border-radius:5px; border-radius:5px; padding:10px;
				margin-bottom:10px;
				clear:both;
			\">
			<legend style=\"
				font-weight:bold;
				padding: 0 5px;
				text-transform:uppercase;
			\">FILTER</legend>
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
				<table width=\"100%\">
					<tr>
					<td width=\"50%\">
					<p>
						<label class=\"l-input-small\">Lokasi Proses</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "90%", "chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kategori Gaji</label>
						<div class=\"field\">
							".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]","",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "90%", "chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tahun</label>
						<div class=\"field\">
							".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
						</div>
					</p>
					</td>
					<td width=\"50%\">
					<p>
						<label class=\"l-input-small\">Proses</label>
						<span class=\"field\">".getField("select count(*) from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck )")." / ".getField("select count(*) from (select idLokasi from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".$bulanProses."' group by 1) as t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Proses</label>
						<span class=\"field\">".getField("select count(*) from pay_jenis where statusJenis='t' order by idJenis")." / ".getField("select count(*) from (select idJenis from pay_proses where tahunProses='".$par[tahunProses]."' and bulanProses='".$bulanProses."' group by 1) as t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Bulan</label>
						<span class=\"field\">".getBulan($bulanProses)." ".$par[tahunProses]."&nbsp;</span>
					</p>
					</td>
					</tr>
				</table>
				</form>
			</fieldset>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"125\">Jumlah Pegawai</th>
					<th width=\"125\">Total Nilai</th>
					<th width=\"125\">Proses</th>
					<th width=\"50\">Detail</th>
					<th>Petugas</th>
				</tr>
			</thead>
			<tbody>";
					
		$filter = "";
		if(!empty($par[idLokasi]))
			$filter.= " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[idJenis]))
			$filter.= " and t3.payroll_id='".$par[idJenis]."'";
		
											
		$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
		$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
		
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join app_user t2 on (t1.updateBy=t2.username) where t1.tahunProses='".$par[tahunProses]."' and t1.idLokasi='".$par[idLokasi]."' and t1.idJenis='".$par[idJenis]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idKomponen not in ('165') and t1.idKomponen not in ('".implode("', '", $slipLain)."') and (t1.idKomponen not in ('".implode("', '", $pph21)."') or t2.cat='531')";
			$arrProses["$r[bulanProses]"] = $r;
		}
		
		if(is_array($arrDetail)){
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$arrNilai=arrayQuery("select periodeProses, sum(case when tipeKomponen='t' then nilaiProses else nilaiProses * -1 end) as jumlahProses from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."' AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1");
			$arrJumlah=arrayQuery("select periodeProses, count(*) from (select periodeProses, idPegawai from (".implode(" union ", $arrDetail).") as t1 join dta_komponen t2 join emp_phist t3 join emp t4 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.parent_id and t3.parent_id=t4.id) where t3.status='1' and t4.status='".$status."'  AND t3.group_id IN ( $areaCheck ) ".$filter." group by 1, 2) as t group by 1");
		}		
		
		for($i=1; $i<=12; $i++){
			$r = $arrProses[$i];
			$r[pegawaiProses] = $arrJumlah[$par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT)];
			$r[nilaiProses] = $arrNilai[$par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT)];
						
			list($selesaiTanggal, $selesaiWaktu) = explode(" ", $r[selesaiProses]);
			
			$detailProses = "";
			$pegawaiProses = getAngka($r[pegawaiProses]) > 0 ? getAngka($r[pegawaiProses]) : "";
			$nilaiProses = getAngka($r[nilaiProses]) > 0 ? getAngka($r[nilaiProses]) : "";
			$selesaiProses = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[bulanProses]=$i".getPar($par,"mode,bulanProses")."',925,425);\">".getTanggal($selesaiTanggal)." ".substr($selesaiWaktu,0,5)."</a>";
			
			if($par[tahunProses].str_pad($i, 2, "0", STR_PAD_LEFT) <= date('Ym') && $r[progesProses] < 100)
			$selesaiProses = "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=set&par[bulanProses]=$i".getPar($par,"mode,bulanProses")."',925,425);\" class=\"detil\">PROSES</a>";				
			
			if($r[progesProses] == 100)
			$detailProses = "<a href=\"?par[mode]=det&par[bulanProses]=$r[bulanProses]".getPar($par,"mode,bulanProses")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";
			
			
			$text.="<tr>
					<td>$i.</td>
					<td>".getBulan($i)."</td>
					<td align=\"center\">".$pegawaiProses."</td>
					<td align=\"right\">".$nilaiProses."</td>
					<td align=\"center\">".$selesaiProses."</td>
					<td align=\"center\">".$detailProses."</td>
					<td>$r[namaUser]</td>
					</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$db,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess, $areaCheck;						
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".getBulan($par[bulanProses])." ".$par[tahunProses]."
				</h1>
				".getBread(ucwords("detail gaji"))."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position:absolute; top:10px; right: 20px;\">
					Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","ALL",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
					".setPar($par,"idStatus,idLokasi,idJenis,search,filter")."
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,bulanProses")."';\"/>
				</div>
			<fieldset style=\"
				-moz-border-radius:5px; border-radius:5px; padding:10px;
				margin-bottom:10px;
				clear:both;
			\">
			<legend style=\"
				font-weight:bold;
				padding: 0 5px;
				text-transform:uppercase;
			\">FILTER</legend>
				<p>
					<label class=\"l-input-small\">Search</label>
					<div class=\"field\">
						<table>
							<tr>
							<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
							<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
							<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
							</tr>
						</table>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Lokasi Proses</label>
					<div class=\"field\">
						".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "325px", "chosen-select")."
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Kategori Gaji</label>
					<div class=\"field\">
						".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]","",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "325px", "chosen-select")."
					</div>
				</p>
			</fieldset>			
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"subtable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$filter = "where t1.status='".$status."' AND t2.group_id IN ( $areaCheck )";		
			
			if(!empty($par[idStatus]))
				$filter.=" and t1.cat='".$par[idStatus]."'";
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[idJenis]))
				$filter.= " and t2.payroll_id='".$par[idJenis]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";	
					
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21))
					$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
				}
			}
			
			$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
			$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter group by t1.id order by name";
			// echo $sql;
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
		global $db,$s,$db,$inp,$par,$arrTitle;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					Slip Gaji
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
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
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
							
				$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
				$pph21 = $r[cat] == 531 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				$arrNot = array_merge($slipLain, $pph21);
				
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idKomponen not in (285, 286) order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $arrNot)){
						if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
						$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
						$tipeKomponen = $r[tipeKomponen];
						$urutanKomponen++;
					}
					
					if(in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $r[cat] == 531 ? "" : "PPH 21 Rp. ".getAngka($r[nilaiProses]);
				}				
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));											
				
				for($i=1; $i<=max($cntKomponen); $i++){					
					$text.="<tr>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["t"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["t"][$i]["nilaiProses"])."</span>
							<span style=\"float:right;\">Rp.</span>";								
					$text.="</td>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["p"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["p"][$i]["nilaiProses"])."</span>
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
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["p"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";
				
				$text.="</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\"><strong>THP</strong></span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"]-$totKomponen["p"])."</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang($totKomponen["t"]-$totKomponen["p"]))." Rupiah</span>
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
						<td style=\"padding:3px 20px; height:75px;\">".nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'"))."&nbsp;".$catatanProses."</td>
						</tr>
					</tbody>
					</table>
					
				</div>
				</form>
			</div>";
		
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$db,$_submit,$menuAccess;
		switch($par[mode]){
			case "end":
				$text = endProses();
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
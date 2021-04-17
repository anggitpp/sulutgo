<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
				
	function setTable(){
		global $db,$s,$db,$inp,$par,$cUsername,$arrParameter,$areaCheck;						
		$detailTemp = "tmp_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
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
		if(!empty($par[idLokasi]))$filter = " and t2.group_id='".$par[idLokasi]."'";
		//$filter.=" and t1.id='98'";
		$arrId= arrayQuery("select t1.id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) where t1.status='".$status."' and t2.status='1' ".$filter." and t2.group_id IN ( $areaCheck ) order by t1.id");
		
		$sql="delete from ".$detailProses." where flagProses='0' and idPegawai in ('".implode("','", $arrId)."')";
		db($sql);	
				
		$idProses = getField("select idProses from pay_proses order by idProses desc limit 1")+1;
		$sql=getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'")?
		"update pay_proses set mulaiProses='".date('Y-m-d H:i:s')."', pegawaiProses='0', nilaiProses='0', detailProses='".$detailProses."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'":
		"insert into pay_proses (idProses, bulanProses, tahunProses, mulaiProses, pegawaiProses, nilaiProses, detailProses, createBy, createTime) values ('$idProses', '$par[bulanProses]', '$par[tahunProses]', '".date('Y-m-d H:i:s')."', '0', '0', '".$detailProses."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
				
		#$sql="insert into ".$detailTemp." (idProses, idPegawai, createBy, createTime) select '".$idProses."' as idProses, idPegawai, '$cUsername' as createBy, '".date('Y-m-d H:i:s')."' as createTime from pay_pokok where tanggalPokok<='".$par[tahunProses]."-".$par[bulanProses]."-".$tanggalProses."' group by idPegawai order by tanggalPokok desc";
				
		$sql="insert into ".$detailTemp." (idProses, idPegawai, createBy, createTime) select '".$idProses."' as idProses, t1.id, '$cUsername' as createBy, '".date('Y-m-d H:i:s')."' as createTime from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) where t1.status='".$status."' and t2.status='1' ".$filter." and t2.group_id IN ( $areaCheck ) order by t1.id";
		db($sql);
	}
	
	function setData(){
		global $db,$s,$db,$inp,$par,$cUsername,$arrParameter;
		$detailTemp = "tmp_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
		$detailProses = "pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT);
		
		$cntPegawai = getField("select count(*) from ".$detailTemp."");
		$idPegawai = getField("select idPegawai from ".$detailTemp." where idDetail='$par[idDetail]'");
		
		$progresData = getAngka($par[idDetail]/$cntPegawai * 100);						
		if(!empty($idPegawai)){	
			$idProses = getField("select idProses from pay_proses where bulanProses='".$par[bulanProses]."' and tahunProses='".$par[tahunProses]."'");
						
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
			
			# SET KOMPONEN GAJI			
			$idStatus = getField("select cat from emp where id='".$idPegawai."'");
			$arrData = arrayQuery("select t1.idKomponen, t1.tipeMaster, t1.idStatus from pay_master t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idStatus='".$idStatus."' order by t2.tipeKomponen desc,t2.urutanKomponen");
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
					$result.=$idKomponen."<br>";
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
									if($r___[operasi1Detail] == "*")
										$nilaiDetail = $r___[nilaiDetail] * getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$r___[idKomponen]."'");
									if($r___[operasi1Detail] == "/")
										$nilaiDetail = $r___[nilaiDetail] / getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$r___[idKomponen]."'");
									if($r___[operasi1Detail] == "+")
										$nilaiDetail = $r___[nilaiDetail] + getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$r___[idKomponen]."'");
									if($r___[operasi1Detail] == "-")
										$nilaiDetail = $r___[nilaiDetail] - getField("select nilaiProses from ".$detailProses." where idPegawai='".$idPegawai."' and idKomponen='".$r___[idKomponen]."'");
									
									if($r___[operasi2Detail] == "+")
										$nilaiKomponen += $nilaiDetail;
									
									if($r___[operasi2Detail] == "-")
										$nilaiKomponen -= $nilaiDetail;
									
									//$result.= $nilaiDetail."<br>";
								}
																
								
							}
						}
						
						if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
							$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
						}else{
							$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
						}
						
						list($nilaiData, $flagData) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
						$nilaiData = ($nilaiData > 0  && $flagData == "t")? $nilaiData : $nilaiKomponen;
						$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiData : 0;
						
						#pay_upload
						if(isset($arrManual["".$r[idKomponen].""])) $nilaiKomponen = $arrManual["".$r[idKomponen].""];
						
						$sql="insert into ".$detailProses." (idProses, idPegawai, idKomponen, nilaiProses, flagProses, createBy, createTime) values ('$idProses', '$idPegawai', '$idKomponen', '".setAngka($nilaiKomponen)."', '0', '$cUsername', '".date('Y-m-d H:i:s')."')";
						if(!in_array($r[dasarKomponen], array(3)) && $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f") db($sql);											
											
						$nilaiProses = $r[tipeKomponen] == "t" ? $nilaiKomponen : $nilaiKomponen * -1;
						$sql="update pay_proses set nilaiProses=nilaiProses + ".setAngka($nilaiProses)."  where idProses='$idProses'";
						db($sql);
												
						#update PPh21
						$nilaiTHP+= $r[tipeKomponen] == "t" ? $nilaiKomponen : $nilaiKomponen * -1;				
						if($r[tipeKomponen] == "t")
							$nilaiBruto+=$nilaiKomponen;
						else
							$potonganBruto+=$nilaiKomponen;
						
						if($cnt == count($arrData)){
							$nilaiMaster = getField("SELECT t2.nilaiPPh FROM emp t1 join pay_pph t2 on (t1.marital=t2.idPerkawinan) where t2.tahunPPh='".$par[tahunProses]."' and t1.id='".$idPegawai."'");
							
							#Start Cycle
							$nilaiNet = 0;							
							for($i=1; $i<=12; $i++)
							{											
								$jumlahBruto=$nilaiBruto + $nilaiNet;								
								$totalTHP=((5/100 * $jumlahBruto) > 500000) ?
									($jumlahBruto - 500000 - $potonganBruto) * 12 :
									($jumlahBruto - (5/100 * $jumlahBruto) - $potonganBruto) * 12;
								
								$nilaiPPH = 0;
								$totalPPH = floorDec($totalTHP - $nilaiMaster, -3);
								if($totalPPH > 500000000){								
									$nilaiPPH+= 30/100 * ($totalPPH - 500000000);								
									$nilaiPPH+= 25/100 * (500000000 - 250000000);
									$nilaiPPH+= 15/100 * (250000000 - 50000000);
									$nilaiPPH+= 5/100 * 50000000;
								}else if($totalPPH > 250000000){								
									$nilaiPPH+= 25/100 * ($totalPPH - 250000000);
									$nilaiPPH+= 15/100 * (250000000 - 50000000);
									$nilaiPPH+= 5/100 * 50000000;
								}else if($totalPPH > 50000000){																
									$nilaiPPH+= 15/100 * ($totalPPH - 50000000);
									$nilaiPPH+= 5/100 * 50000000;
								}else if($totalPPH > 0){		
									$nilaiPPH+= 5/100 * $totalPPH;
								}else{								
									$nilaiPPH = 0;
								}
								$nilaiPPH = $nilaiPPH / 12;
								$nilaiNet = $nilaiPPH;
							}
							#End Cycle
							
							$sql="update dta_komponen t1 join ".$detailProses." t2 on (t1.idKomponen=t2.idKomponen) set t2.nilaiProses='".setAngka($nilaiPPH)."' where t1.flagKomponen=4 and t2.idPegawai='".$idPegawai."'";						
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
						<label style=\"width:150px;\" class=\"l-input-small\">Bulan</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getBulan($par[bulanProses])."&nbsp;</span>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Tahun</label>
						<span style=\"margin-left:170px;\" class=\"field\">".$par[tahunProses]."&nbsp;</span>
					</p>										
					</td>
					<td width=\"50%\">
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Tanggal Proses</label>
						<span style=\"margin-left:170px;\" class=\"field\">".getTanggal(date('Y-m-d'),"t").", ".date('H:i')."&nbsp;</span>
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
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
										<span style=\"margin-left: 30px;\">Tahun : </span>
										".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
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
			</form>
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
												
		if(!empty($par[idLokasi]))
			$filter = " and t3.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t3.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t3.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t3.unit_id='".$par[unitId]."'";										
												
		$sql="select t1.*, t2.namaUser from pay_proses t1 left join ".$db[setting].".app_user t2 on (t1.createBy=t2.username) where t1.tahunProses='$par[tahunProses]'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrDetail[] = "select '".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)."' as periodeProses, idPegawai, idKomponen, nilaiProses from pay_proses_".$par[tahunProses].str_pad($r[bulanProses], 2, "0", STR_PAD_LEFT)." where flagProses='0'";
			$arrProses["$r[bulanProses]"] = $r;
		}
		/*
		$r[nilaiProses] = getField("select sum(case when t0.tipeKomponen='t' then t1.nilaiProses else t1.nilaiProses * -1 end) from dta_komponen t0 join ".$detailProses." t1 join dta_pegawai t2 on (t0.idKomponen=t1.idKomponen and t1.idPegawai=t2.id) where t1.flagProses=0 ".$filter);				
		$r[pegawaiProses] = getField("select count(*) from (select t1.idPegawai from dta_komponen t0 join ".$detailProses." t1 join dta_pegawai t2 on (t0.idKomponen=t1.idKomponen and t1.idPegawai=t2.id) where t1.flagProses=0 ".$filter." group by idPegawai) as t");
		*/
		
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
		
		if(empty($par[idStatus])) $par[idStatus] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - Detail Gaji
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
				</h1>
				".getBread(ucwords("detail gaji"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position:absolute; top:60px; right: 20px;\">
					<table>
						<tr>
							<td>
								Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
								<span style=\"margin-left: 30px;\">Status Pegawai : </span>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
								".setPar($par,"idStatus,idLokasi,search,filter")."
								<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,bulanProses")."';\"/>
							</td>
						</tr>
					</table>
				</div>			
				<div style=\"position: absolute; right: 0; top: 0; vertical-align:top; padding-top:2px; width: 470px;\">
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
					<th style=\"width:100px;\">Gaji</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			$filter = "where t1.cat=".$par[idStatus]." and t1.status='".$status."' AND t2.group_id IN ( $areaCheck )";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t2.group_id='".$par[idLokasi]."'";
			if(!empty($par[divId]))
				$filter.= " and t2.div_id='".$par[divId]."'";
			if(!empty($par[deptId]))
				$filter.= " and t2.dept_id='".$par[deptId]."'";
			if(!empty($par[unitId]))
				$filter.= " and t2.unit_id='".$par[unitId]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";	
					
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrGaji["$r[idPegawai]"]+=$r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
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
																
				$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' order by t2.tipeKomponen desc, t2.urutanKomponenT";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
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
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	
	function update(){
		global $db,$s,$inp,$par,$det,$cUsername;
		repField("saranPeserta");
		
		$sql="update plt_pelatihan_peserta set saranPeserta='$inp[saranPeserta]' where idPelatihan='$par[idPelatihan]' and idPegawai='$par[idPegawai]'";
		db($sql);		
		
		$idEvaluasi = getField("select idEvaluasi from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
		db("delete from plt_pertanyaan_evaluasi where idPelatihan='$par[idPelatihan]' and idPegawai='$par[idPegawai]'");
		if(is_array($det)){	
			while (list($idPertanyaan) = each($det)){
				$tipeJawaban = getField("select tipePertanyaan from plt_pertanyaan where idPertanyaan='$idPertanyaan'");
				$sql="insert into plt_pertanyaan_evaluasi (idPertanyaan, idPegawai, idPelatihan, idEvaluasi, tipeJawaban, evaluasiJawaban, createBy, createTime) values ('$idPertanyaan', '$par[idPegawai]', '$par[idPelatihan]', '$idEvaluasi', '$tipeJawaban', '".implode("\t", $det[$idPertanyaan])."', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idPelatihan")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
		include "plugins/mces.jsp";
		
		$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		$idEvaluasi = $r[idEvaluasi];
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  >
				<div id=\"general\" style=\"margin-top:20px;\">					
					<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
						<legend style=\"padding:10px; margin-left:20px;\"><h4>INFORMASI</h4></legend>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Pelatihan</label>
							<span class=\"field\" style=\"margin-left:150px;\">$r[judulPelatihan]&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Nama Pegawai</label>
							<span class=\"field\" style=\"margin-left:150px;\">".getField("select name from emp where id='".$par[idPegawai]."'")."&nbsp;</span>
						</p>
					</fieldset>";
					
			$sql="select * from plt_pertanyaan t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.idEvaluasi='".$idEvaluasi."' and t1.statusPertanyaan='t'";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrKategori["$r[idKategori]"] = $r[urutanData]."\t".$r[namaData];
			}
											
			$text.="<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
					<legend style=\"padding:10px; margin-left:20px;\"><h4>PERTANYAAN</h4></legend>
					<div style=\"float:right; margin-right:20px;\">
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/> <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					</div>";
								
			if(is_array($arrKategori)){
				$text.="<ul class=\"hornav\">";
				
				$tab = 1;
				asort($arrKategori);
				reset($arrKategori);				
				while (list($idKategori, $valKategori) = each($arrKategori)){
					list($urutanKategori, $namaKategori) = explode("\t", $valKategori);
					$current = $tab == 1 ? "class=\"current\"" : "";
					$text.="<li ".$current." style=\"margin-bottom:10px;\"><a href=\"#tab_".$urutanKategori."\">".$namaKategori."</a></li>";
					$tab++;
				}
				$text.="</ul>";					
				
				$tab = 1;
				asort($arrKategori);
				reset($arrKategori);
				while (list($idKategori, $valKategori) = each($arrKategori)){
					$display = $tab == 1 ? "display: block;" : "display: none;";
					
					list($urutanKategori, $namaKategori) = explode("\t", $valKategori);
					$text.="<div id=\"tab_".$urutanKategori."\" class=\"subcontent\" style=\"border:0px; clear:both; ".$display."\">";
					
					$sql="select * from plt_pertanyaan where idEvaluasi='".$idEvaluasi."' and idKategori='".$idKategori."' and statusPertanyaan='t' order by idPertanyaan";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){		
						$tipePertanyaan = $r[tipePertanyaan];
						$evaluasiJawaban = getField("select evaluasiJawaban from plt_pertanyaan_evaluasi where idPertanyaan='$r[idPertanyaan]' and idPegawai='$par[idPegawai]'");
						
						$text.="<table style=\"width:100%; margin-bottom:20px;\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
						<td width=\"30\">$no.</td>
						<td>$r[detailPertanyaan]</td>
						</tr>
						<tr>
						<td>&nbsp;</td>
						<td>";
						
						$idJawaban = 0;
						if(in_array($tipePertanyaan, array("a", "i"))){
							$text.="<textarea id=\"det[".$r[idPertanyaan]."][".$idJawaban."]\" name=\"det[".$r[idPertanyaan]."][".$idJawaban."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:500px;\">".$evaluasiJawaban."</textarea>";
						}else{
							$arrJawaban = explode("\t", $evaluasiJawaban);
							$type = $tipePertanyaan == "c" ? "checkbox" : "radio";							
							$text.="<div class=\"fradio\">";							
							$sql_="select * from plt_pertanyaan_jawaban where idPertanyaan='".$r[idPertanyaan]."'";
							$res_=db($sql_);
							while($r_=mysql_fetch_array($res_)){
								$checked = in_array($r_[idJawaban], $arrJawaban) ? "checked=\"checked\"" : "";
								$idJawaban = $tipePertanyaan == "c" ? $r_[idJawaban] : $idJawaban;
								$text.="<input type=\"".$type."\" id=\"det[".$r_[idPertanyaan]."][".$r_[idJawaban]."][".$r_[idJawaban]."]\" name=\"det[".$r_[idPertanyaan]."][".$idJawaban."]\" value=\"$r_[idJawaban]\" $checked /> <span class=\"sradio\">$r_[detailJawaban]</span>";
							}
							$text.="</div>";
						}
						
						$text.="</td>
						</tr>
						</table>";
						
						$no++;						
					}					
					
					$text.="</div>";					
					$tab++;
				}
				
			}
						
			$text.="</fieldset>
			
				<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
					<legend style=\"padding:10px; margin-left:20px;\"><h4>MASUKAN & SARAN</h4></legend>
					<textarea id=\"mce1\" name=\"inp[saranPeserta]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">".getField("select saranPeserta from plt_pelatihan_peserta where idPegawai='".$par[idPegawai]."'")."</textarea>
				</fieldset>
			
				</div>				
			</form>";
		
		return $text;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess,$cID;
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;
		
		if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">";				
		require_once "tmpl/__emp_header__.php";						
		$text.="</div>
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<input type=\"hidden\" name=\"_submit\" value=\"t\">
			<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\" /></td>
				<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>				
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\" style=\"margin-top:12px;\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"vertical-align:middle\" max-width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"vertical-align:middle\">Pelatihan</th>					
					<th colspan=\"2\" style=\"vertical-align:middle; max-width:150px;\">Pelaksanaan</th>
					<th rowspan=\"2\" style=\"vertical-align:middle\">Lokasi</th>
					<th rowspan=\"2\" style=\"vertical-align:middle\">Vendor</th>
					<th rowspan=\"2\" style=\"vertical-align:middle\">PIC</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; max-width:50px;\">Evaluasi</th>
				</tr>
				<tr>
					<th style=\"vertical-align:middle; max-width:75px;\">Mulai</th>
					<th style=\"vertical-align:middle; max-width:75px;\">Selesai</th>			
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
		if(!empty($par[tahunPelatihan]))
			$filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";
		
		if(!empty($par[idKategori]))
			$filter.=" and t1.idKategori='".$par[idKategori]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.namaPic) like '%".strtolower($par[filter])."%'
			or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
		)";
						
		$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from (
				select p1.*, case when p1.pelaksanaanPelatihan='e' then p2.namaTrainer else p1.namaPegawai end as namaPic from (
					select d1.*, d2.name as namaPegawai from (
						select s1.* from plt_pelatihan s1 join plt_pelatihan_peserta s2 on (s1.idPelatihan=s2.idPelatihan) where s2.idPegawai='$par[idPegawai]'
					) as d1 left join emp d2 on (d1.idPegawai=d2.id)
				) as p1 left join dta_trainer p2 on (p1.idTrainer=p2.idTrainer)
			) as t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){					
			$no++;						
			$cntEvaluasi = getField("select count(idPertanyaan) from plt_pertanyaan_evaluasi where idPegawai='$par[idPegawai]' and idPelatihan='$r[idPelatihan]'");
			$statusEvaluasi = $cntEvaluasi > 0 ?
							"<img src=\"styles/images/t.png\" title='Sudah Dievaluasi'>":
							"<img src=\"styles/images/f.png\" title='Belum Dievaluasi'>";
			$text.="<tr>
					<td align=\"center\">$no.</td>			
					<td><a href=\"?par[mode]=eval&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>
					<td align=\"center\">".getTanggal($r[mulaiPelatihan])."</td>
					<td align=\"center\">".getTanggal($r[selesaiPelatihan])."</td>
					<td>$r[lokasiPelatihan]</td>
					<td>$r[namaVendor]</td>
					<td>$r[namaPegawai]</td>
					<td align=\"center\">".$statusEvaluasi."</td>					
					</tr>";			
		}
		
		$text.="</tbody>
			</table>
			</div>";	
			
		return $text;
	}	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){		
			case "eval":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : update(); else $text = lihat();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/pinjaman/";
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;
		if(empty($par[tahunPinjaman])) $par[tahunPinjaman]=date('Y');		
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
				<td>".comboYear("par[tahunPinjaman]", $par[tahunPinjaman])."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idPinjaman")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NPP</th>
					<th rowspan=\"2\" width=\"100\">Nomor</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" width=\"100\">Nilai</th>
					<th rowspan=\"2\" width=\"50\">Lunas</th>
					<th colspan=\"2\" width=\"100\">Approval</th>
					<th rowspan=\"2\" width=\"50\">Kontrol</th>
				</tr>
				<tr>					
					<th width=\"50\">Atasan</th>
					<th width=\"50\">SDM</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where year(t1.tanggalPinjaman)='$par[tahunPinjaman]' and persetujuanPinjaman='t' and sdmPinjaman='t'";	

		// if(!isset($menuAccess[$s]["apprlv2"])){
		// 					$filter.=" and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."') ";
		// 				}

		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.nomorPinjaman) like '%".strtolower($par[filter])."%'
			or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
			or lower(t2.name) like '%".strtolower($par[filter])."%'	
		)";
		
		$sql="select * from ess_pinjaman t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorPinjaman";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $persetujuanPinjaman;
			$persetujuanPinjaman = $r[persetujuanPinjaman] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $persetujuanPinjaman;
			
			$sdmPinjaman = $r[sdmPinjaman] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmPinjaman = $r[sdmPinjaman] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmPinjaman;
			$sdmPinjaman = $r[sdmPinjaman] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmPinjaman;
					
			$statusPinjaman = getField("select count(*) from ess_angsuran where statusAngsuran='f' and idPinjaman='$r[idPinjaman]'") > 0 ?
			"<img src=\"styles/images/f.png\" title=\"Belum Lunas\">":
			"<img src=\"styles/images/t.png\" title=\"Sudah Lunas\">";
			
			$text.="<tr>
					<td>$no.</td>					
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[nomorPinjaman]</td>
					<td align=\"center\">".getTanggal($r[tanggalPinjaman])."</td>
					<td align=\"right\">".getAngka($r[nilaiPinjaman])."</td>					
					<td align=\"center\">$statusPinjaman</td>
					<td align=\"center\"><a href=\"#\" title=\"Detail Data\">$persetujuanPinjaman</a></td>
					<td align=\"center\"><a href=\"#\" title=\"Detail Data\">$sdmPinjaman</a></td>
					<td align=\"center\">
						<a href=\"?par[mode]=det&par[idPinjaman]=$r[idPinjaman]&par[idPegawai]=$r[id]".getPar($par,"mode,idPinjaman,idPegawai")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
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
		if(empty($par[tahunPinjaman])) $par[tahunPinjaman]=date('Y');
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		
		echo "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<div style=\"padding-bottom:10px;\">";				
		require_once "tmpl/emp_header_basic.php";						
		
		$sql="select * from ess_pinjaman where idPinjaman='$par[idPinjaman]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);	
		list($tanggalApprove) = explode(" ", $r[approveTime]);				
		$nilaiPinjaman = $r[nilaiPinjaman];

		$r___[marginPinjaman] = $r[marginPinjaman]; 
		
		$text.="</div>					
			<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\">
				<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PINJAMAN KARYAWAN</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
						<td width=\"45%\" style=\"vertical-align:top;\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorPinjaman]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Tanggal</label>
								<span class=\"field\">".getTanggal($r[tanggalPinjaman],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keperluan</label>
								<span class=\"field\">".nl2br($r[keteranganPinjaman])."&nbsp;</span>							
							</p>
							<p>
								<label class=\"l-input-small\">Dokumen</label>
								<span class=\"field\">";
								$text.=empty($r[filePinjaman])? "":
									"<a href=\"download.php?d=pinjaman&f=$r[idPinjaman]\"><img src=\"".getIcon($fFile."/".$r[filePinjaman])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
								$text.="&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\" style=\"vertical-align:top;\">
							<p>
								<label class=\"l-input-small\">Nilai</label>
								<span class=\"field\">".getAngka($r[nilaiPinjaman])."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Tanggal Approve</label>
								<span class=\"field\">".getTanggal($tanggalApprove,"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Nilai</label>
								<span class=\"field\">".getAngka($r[nilaiPinjaman])."&nbsp;</span>
							</p>";
							
					$sql="select * from ess_angsuran where idPinjaman='$r[idPinjaman]'";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){
						list($tahun, $bulan, $tanggal) = explode("-", $r[tanggalAngsuran]);
						$arrAngsuran["$r[idAngsuran]"]=$r;
						
						if($no==1) $periodeAwal = getBulan($bulan)." ".$tahun;
						$periodeAkhir = getBulan($bulan)." ".$tahun;
						
						$no++;
					}
							
					$text.="<p>
								<label class=\"l-input-small\">Periode</label>
								<span class=\"field\">".$periodeAwal." s.d ".$periodeAkhir."&nbsp;</span>
							</p>
						</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>ANGSURAN</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"150\">Angsuran</th>
							<th style=\"min-width:150px;\">Tanggal Angsuran</th>
							<th style=\"min-width:150px;\">Tanggal Bayar</th>
							<th width=\"150\">Nilai</th>
							<th width=\"150\">Margin</th>
							<th width=\"150\">Angsuran</th>
							<th width=\"150\">Sisa</th>
						</tr>						
					</thead>
					<tbody>";
					
					$sisaPinjaman = $nilaiPinjaman;
					if(is_array($arrAngsuran)){
						asort($arrAngsuran);
						reset($arrAngsuran);
						while(list($idAngsuran, $r) = each($arrAngsuran)){
						list($bayarAngsuran) = explode(" ",$r[updateTime]);
						$nilaiAngsuran = $r[nilaiAngsuran];
						$sisaPinjaman-=$r[totalAngsuran];
						if($sisaPinjaman>=0){
						$text.="<tr>
							<td align=\"center\">$idAngsuran</td>					
							<td>".getTanggal($r[tanggalAngsuran],"t")."</td>
							<td>".getTanggal($bayarAngsuran,"t")."</td>
							<td align=\"right\">".getAngka($nilaiAngsuran)."</td>
							<td align=\"right\">".getAngka($r___[marginPinjaman])."</td>
							<td align=\"right\">".getAngka($r[totalAngsuran])."</td>
							<td align=\"right\">".getAngka($sisaPinjaman)."</td>
							</tr>";
							
							$totalAllAngsuran += $r[totalAngsuran];
							}
							$totalNilai += $r[nilaiAngsuran];
							$totalMargin += $r___[marginPinjaman];
						}
					}
					
					
				$text.="</tbody>
				<tfoot>
				<tr>
				<td colspan=\"3\" align=\"right\" style=\"align:right\">
				<p align=\"right\">".getAngka($totalNilai)."</p>
				</td>
				<td><p align=\"right\">".getAngka($totalMargin)."</p></td>
				<td><p align=\"right\">".getAngka($totalAllAngsuran)."</p></td>
				<td></td>
				</tfoot>
				</table>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPinjaman")."';\" style=\"float:right;\"/>		
				</p>
				</form>
			</div>";
		return $text;
	}		
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "det":
				$text = detail();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>

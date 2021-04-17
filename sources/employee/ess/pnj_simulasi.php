<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	if(empty($cID)){
		echo "<script>alert('maaf, anda tidak terdaftar sebagai pegawai'); history.back();</script>";
		exit();
	}
	$fFile = "files/pinjaman/";
		
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		if(empty($par[tahunPinjaman])) $par[tahunPinjaman]=date('Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">				
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\">	
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[nilaiPinjaman]\" name=\"inp[nilaiPinjaman]\"  value=\"".getAngka($inp[nilaiPinjaman])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Waktu</label>
						<div class=\"field\">								
							<input type=\"text\" id=\"inp[waktuPinjaman]\" name=\"inp[waktuPinjaman]\"  value=\"".getAngka($inp[waktuPinjaman])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" /> bulan<br>
							<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"VIEW\" style=\"margin:10px;\"/>	
						</div>
					</p>								
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PERHITUNGAN</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"150\">Angsuran</th>							
							<th width=\"150\">Nilai</th>							
						</tr>						
					</thead>
					<tbody>";
					$nilaiAngsuran = setAngka($inp[waktuPinjaman]) > 0 ? setAngka($inp[nilaiPinjaman]) / setAngka($inp[waktuPinjaman]) : 0;
					for($i=1; $i<=$inp[waktuPinjaman]; $i++){
						$text.="<tr>
							<td align=\"center\">$i</td>							
							<td align=\"right\">".getAngka($nilaiAngsuran)."</td>
							</tr>";
					}
				$text.="</tbody>
				</table>";
		
		$text.="</form>
			</div>";
		return $text;
	}		
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
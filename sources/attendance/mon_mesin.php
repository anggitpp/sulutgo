<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
		
	function ping($ip, $port){
		$starttime = microtime(true);
		$file      = fsockopen ($ip, $port, $errno, $errstr, 10);
		$stoptime  = microtime(true);
		$status    = 0;

		if (!$file) $status = "";  // Site is down
		else {
			fclose($file);
			$status = ($stoptime - $starttime) * 1000;
			$status = floor($status);
		}
		return $status;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
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
					<th width=\"100\">SN</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"150\">IP</th>
					<th width=\"50\">Status</th>
					<th style=\"min-width:150px;\">Respon</th>					
					<th style=\"min-width:150px;\">Lokasi</th>										
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where namaMesin is not null";
		if(!empty($par[filter]))			
		$filter.= " and (
			lower(snMesin) like '%".strtolower($par[filter])."%'
			or lower(namaMesin) like '%".strtolower($par[filter])."%'
			or lower(alamatMesin) like '%".strtolower($par[filter])."%'
			or lower(lokasiMesin) like '%".strtolower($par[filter])."%'
		)";				
		
		$sql="select * from dta_mesin $filter order by idMesin";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			
			if(!empty($r[alamatMesin])) $pingMesin = ping($r[alamatMesin], $r[portMesin]);			
			$responMesin = empty($pingMesin) ? "Offline":"Replay ".$pingMesin." second";
			
			$statusMesin = empty($pingMesin) ? "<img src=\"styles/images/f.png\">":"<img src=\"styles/images/t.png\">";
			$statusMesin = $pingMesin > 30 ? "<img src=\"styles/images/p.png\">":$statusMesin;
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[snMesin]</td>
					<td>$r[namaMesin]</td>
					<td>$r[alamatMesin]:$r[portMesin]</td>
					<td align=\"center\">$statusMesin</td>
					<td align=\"center\">$responMesin</td>
					<td>$r[lokasiMesin]</td>
					</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){						
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
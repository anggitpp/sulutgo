<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')			
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";
		
		$sql="select * from plt_pelatihan t1 join plt_pelatihan_dokumen t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere and t2.tipeDokumen='f' order by t2.idDokumen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrFoto["".$r[idPelatihan].""]["".$r[idDokumen].""] = $r;
		}
		
		$sql="select * from (
			select t1.* from plt_pelatihan t1 join plt_pelatihan_dokumen t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by t1.idPelatihan
		) as dta order by mulaiPelatihan";		
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from (
				select t1.* from plt_pelatihan t1 join plt_pelatihan_dokumen t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by t1.idPelatihan
			) as dta"),
			"aaData" => array(),
		);				
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$fotoPelatihan = "";
			if(is_array($arrFoto["".$r[idPelatihan].""])){				
				$fotoPelatihan ="<table style=\"width:100%; margin-bottom:30px;\">
					<tr>";
				$i=1;
				while (list($idDokumen, $f) = each($arrFoto["".$r[idPelatihan].""])) {
					if(is_file("files/dokumen/".$f[fileDokumen]) && $i<=2){
						$fotoPelatihan.="<td style=\"width:50%; text-align:center; vertical-align:top;\"><img src=\"files/dokumen/".$f[fileDokumen]."\" width=\"90%\"></td>";
						$i++;
					}
				}
				if($i==2){
					$fotoPelatihan.="<td style=\"width:50%; text-align:center;\">&nbsp;</td>";
				}
				
				$fotoPelatihan.="</tr>
				</table>";
				
				if($i>1){
					$data=array(
						"<table style=\"width:100%; margin-top:20px;\">
							<tr>
							<td style=\"background:#999; padding:5px; color:#fff; border:solid 1px #666;\"><strong>".strtoupper($r[judulPelatihan])."</strong>&nbsp;&nbsp;&nbsp;".$r[lokasiPelatihan]." | ".getTanggal($r[mulaiPelatihan], "t")."</td>
							</tr>
						</table>
						".$fotoPelatihan."",	
					);
					$json['aaData'][]=$data;
				}				
			}
						
		}
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;				
		$pSearch = empty($_GET[pSearch]) ? "" : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? date('01/01/Y') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];
		$text = table(1, array(1), "lst", "false");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Pelatihan : ".comboData("select * from plt_pelatihan where statusPelatihan='t' order by judulPelatihan","idPelatihan","judulPelatihan","pSearch","-- ALL",$pSearch,"", "360px","chosen-select")."
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td><input type=\"text\" id=\"mSearch\" name=\"mSearch\" size=\"10\" maxlength=\"10\" value=\"".$mSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>
				<td>s.d</td>
				<td><input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>								
				</tr>
			</table>
			</div>			
			</form>
			<br clear=\"all\" />
			<table style=\"width:100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"dataList\">					
			<tbody></tbody>			
			</table>
			</div>";
			
		return $text;
	}	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
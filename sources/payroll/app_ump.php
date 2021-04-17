<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	
	function ubahDet() {
		global $s,$inp,$par,$arrParameter,$cUsername;		
		repField();	
		$kodeUmp=getField("select kodeUmp from mst_ump order by kodeUmp desc")+1;
		$sql2="select ump from mst_ump where kodeKota=".$inp['kodeKota']." order by kodeUmp";
		$res2=db($sql2);
		$r2=mysql_fetch_array($res2);
		
		if ($r2['ump']=="") {
		$sql="insert into mst_ump (kodeUmp,kodeProp, kodeKota, ump, createBy, createTime) values (".$kodeUmp.",'".$inp['kodeProp']."', '".$inp['kodeKota']."', '".setAngka($inp['ump'])."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		} else {
		$sql="update mst_ump set ump='".setAngka($inp['ump'])."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeKota=".$inp['kodeKota']."";
		db($sql);
		}
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	
	function formDet(){
		global $s,$inp,$par,$arrTitle,$menuAccess;
		$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='S02'");
		$namaCategory = getField("select namaCategory from mst_category where kodeCategory='$kodeCategory'");
		
		$sql="select * from mst_data where kodeData='$par[kodeData]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
		
		if(empty($r[urutanData])) $r[urutanData] = getField("select urutanData from mst_data where kodeInduk='$par[kodeInduk]' and kodeCategory='S02' order by urutanData desc limit 1") + 1;
		if(empty($r[kodeInduk])) $r[kodeInduk] = $par[kodeInduk];
		
		$Propinsi = getField("select namaData from mst_data where kodeData='".$r[kodeInduk]."'");
		$sql2="select ump from mst_ump where kodeKota=".$r['kodeData']." order by kodeUmp";
		$res2=db($sql2);$r2=mysql_fetch_array($res2);
	
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords(str_replace("Det","",$par[mode])." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">";
				/*
				if(!empty($kodeCategory))
				$text.="<p>
						<label class=\"l-input-small\">".$namaCategory."</label>
						<div class=\"field\">".$Propinsi."
							<input type=\"hidden\" id=\"inp[kodeProp]\" name=\"inp[kodeProp]\"  value=\"$r[kodeInduk]\"  maxlength=\"150\"/>
						</div>
					</p>";			
				*/
				$text.="<p>
						<label class=\"l-input-small\">Lokasi Kerja</label>
						<div class=\"field\">".$r[namaData]."
							<input type=\"hidden\" id=\"inp[kodeKota]\" name=\"inp[kodeKota]\"  value=\"$r[kodeData]\" maxlength=\"150\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Upah Minimum</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[ump]\" name=\"inp[ump]\"  value=\"".getAngka($r2[ump])."\" class=\"mediuminput\" style=\"width:150px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					
				</div>
				<p style=\"position:absolute;right:20px;top:5px;\">	
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
			</form>	
			</div>";
		return $text;
	}
	
	
	
	function detail(){
		global $p,$m,$s,$inp,$par,$arrTitle,$menuAccess;
		$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='S02'");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" style=\"width:230px;\" />";
			//$text.=" ".comboData("select * from mst_data where kodeCategory='$kodeCategory' and statusData='t' order by urutanData","kodeData","namaData","par[kodeInduk]","All",$par[kodeInduk],"", "250px");
			$text.="".setPar($par, "filter, kodeInduk")."
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>
			
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Lokasi Kerja</th>
					<th width=\"150\">UMP</th>
					";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter ="where kodeCategory='S06'";
		if(!empty($par[kodeInduk])) $filter.=" and kodeInduk='$par[kodeInduk]'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(namaData) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from mst_data $filter order by kodeInduk, urutanData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
		
			$sql2="select ump from mst_ump where kodeProp = ".$r['kodeInduk']." and kodeKota=".$r['kodeData']." order by kodeUmp";
			$res2=db($sql2);$r2=mysql_fetch_array($res2);	
					
			$no++;
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaData]</td>
					<td align=\"right\">".$r2['ump']."</td>					
					";
			if(isset($menuAccess[$s]["edit"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editDet&par[kodeData]=$r[kodeData]".getPar($par,"mode,kodeData")."',825,270);\"><span>Edit</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";
		}
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){
			case "editDet":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formDet() : ubahDet(); else $text = detail();
			break;
			case "det":
				$text = detail();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = detail();
			break;
			case "view":
				$text = detail();
			break;
			default:
				$text = detail();
			break;
		}
		return $text;
	}	
?>
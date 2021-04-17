<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$kodeCategory;		
		
		$text="<script>
				jQuery(document).ready(function() {";
		$sql="select * from mst_data where statusData='t' and kodeCategory in ('X03','X04', 'X05', 'X06', 'X07') order by urutanData";
		$res=db($sql);
		$cnt=1;
		while($r=mysql_fetch_array($res)){
			$arrData["$r[kodeInduk]"]["$r[kodeData]"] = $r;
			if($r[kodeCategory] == "X03"){							
				$text.="jQuery('#team-source-".$r[kodeData]."').orgChart({		
						container: jQuery('#team-chart-".$r[kodeData]."'), interactive: true
					});";
				$cntData["$r[kodeData]"]=$cnt;
				$cnt++;
			}
		}
		$text.="});
		</script>";		
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">";
				
				$levelData = 1;
				$sql="select * from mst_data where statusData='t' and kodeCategory='X03' order by kodeData";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					if(is_array($arrData["$r[kodeInduk]"])){
						reset($arrData["$r[kodeInduk]"]);
						$cnt = $cntData["$r[kodeData]"];
						$color = ($r[kodeCategory] != "X03" || empty($r[kodeInduk])) ? "" : "style=\"background:".$arrColor[$cnt]."; color:#fff;\"";						
						$text.="<div class=\"widgetbox\">
									<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>$r[namaData]</h3></div>
								</div>								
									<div id=\"team-chart-".$r[kodeData]."\"></div>
									<ul id=\"team-source-".$r[kodeData]."\" style=\"display:none\">
									<li ".$color.">".$r[namaData]."<br>".getField("select count(*) from dta_pegawai where top_id='".$r[kodeData]."'")." Orang<label style=\"display:none\">$r[kodeData]</label>";														
							$text.=getStruktur($arrData, $r[kodeData], $cntData, $levelData+1);
							$text.="</li>
									</ul>									
								<br clear=\"all\">";
					}
				}
				
				$text.="</div>";			
			
		return $text;
	}
	
	function getStruktur($arrData, $kodeInduk, $cntData, $levelData){
		global $db,$arrColor;
		$fieldData = "dir_id";
		if($levelData == 3) $fieldData = "div_id";
		if($levelData == 4) $fieldData = "dept_id";
		if($levelData == 5) $fieldData = "unit_id";
		if(is_array($arrData[$kodeInduk])){		
			reset($arrData[$kodeInduk]);
			$text.="<ul style=\"display:none\">";
			while(list($kodeData, $r)=each($arrData[$kodeInduk])){
				$cnt = $cntData["$r[kodeData]"];
				$color = ($r[kodeCategory] != "X03" || empty($r[kodeInduk])) ? "" : "style=\"background:".$arrColor[$cnt]."; color:#fff;\"";
								
				$text.="<li ".$color.">".$r[namaData]."<br>".getField("select count(*) from dta_pegawai where ".$fieldData."='".$r[kodeData]."'")." Orang<label style=\"display:none\">$r[kodeData]</label>";
				if($r[kodeCategory] != "X03" || empty($r[kodeInduk])){
					$text.=getStruktur($arrData, $r[kodeData], $cntData, $levelData+1);
				}
				
				$text.="</li>";
			}
			$text.="</ul>";
		}
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;												
		$cols = 7;		
		$text = table($cols, array());
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Data Personil</h1>
			</div>    
			<br>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>					
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
			</p>
			</div>	
			</form>
			<br clear=\"all\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:150px;\">Nama</th>					
					<th style=\"min-width:100px;\">Pangkat</th>
					<th style=\"min-width:100px;\">".$arrParameter[39]."</th>
					<th style=\"min-width:100px;\">".$arrParameter[40]."</th>
					<th style=\"min-width:100px;\">".$arrParameter[41]."</th>
					<th style=\"min-width:100px;\">STATUS</th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}	
	
	function lData(){
		global $s,$par,$menuAccess,$arrParameter;	
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$fieldData="top_id";
		if($par[levelData] == 2) $fieldData="dir_id";
		if($par[levelData] == 3) $fieldData="div_id";
		if($par[levelData] == 4) $fieldData="dept_id";
		if($par[levelData] == 5) $fieldData="unit_id";
		
		$sWhere= " where t1.status='".$status."' and t1.".$fieldData."='".$par[kodeData]."'";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$arrOrder = array(	
			"t.name",
			"t.name",
			"t.namaDivisi",
			"t.namaGedung",	
			"t.namaGroup",	
			"t5.namaData",	
		);
		$arrMaster = arrayQuery("Select kodeData, namaData from mst_data");
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select t.*, t5.namaData as namaPos from (
			select v2.*, t4.namaData as namaGroup from (
				select v1.*, t3.namaData as namaGedung from (
					select t1.*, t2.namaData as namaDivisi from dta_pegawai t1 left join mst_data t2 on (t1.rank=t2.kodeData) $sWhere
				) as v1 left join mst_data t3 on (v1.div_id=t3.kodeData)
			) as v2 left join mst_data t4 on (v2.dept_id=t4.kodeData)
		) as t left join mst_data t5 on (t.unit_id=t5.kodeData) order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai t1 $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[name]."</div>",
				"<div align=\"left\">".$r[namaDivisi]."</div>",
				"<div align=\"left\">".$r[namaGedung]."</div>",
				"<div align=\"left\">".$r[namaGroup]."</div>",
				"<div align=\"left\">".$r[namaPos]."</div>",
				"<div align=\"left\">".$arrMaster[$r[cat]]."</div>",
			);		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){		
			case "lst":
				$text=lData();
			break;
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
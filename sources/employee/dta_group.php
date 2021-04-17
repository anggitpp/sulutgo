<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
		
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;						
		$cols = 6;		
		$text = table($cols, array());
		
		if(!empty($par[div_id])) $fGroup = " and kodeInduk='".$par[div_id]."'";
		if(!empty($par[dept_id])) $fPos = " and kodeInduk='".$par[dept_id]."'";
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; top:0; right:0; margin-top:95px; margin-right:30px;\">
				<input id=\"bView\" type=\"button\" value=\"+ View\" class=\"btn btn_search btn-small\" onclick=\"
					document.getElementById('bView').style.display = 'none';
					document.getElementById('bHide').style.display = 'block';
					document.getElementById('dFilter').style.visibility = 'visible';
					document.getElementById('fSet').style.height = '250px';
				\" />
				<input id=\"bHide\" type=\"button\" value=\"- Hide\" class=\"btn btn_search btn-small\" style=\"display:none\" onclick=\"
					document.getElementById('bView').style.display = 'block';
					document.getElementById('bHide').style.display = 'none';
					document.getElementById('dFilter').style.visibility = 'collapse';
					document.getElementById('fSet').style.height = '90px';
				\" />
			</div>
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px; height:90px;\">						
			<legend style=\"padding:10px; margin-left:20px;\"><h4>FILTER PENCARIAN</h4></legend>						
			<p>
				<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">NAMA</label>
				<div class=\"field\" style=\"margin-left:100px;\">
					<input type=\"text\" id=\"sSearch\" name=\"sSearch\" value=\"\" style=\"width:290px;\"/>
				</div>
			</p>
			<div id=\"dFilter\" style=\"visibility:collapse;\">
			<p>
				<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">DIVISI</label>
				<div class=\"field\" style=\"margin-left:100px;\">
					".comboData("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[10]."' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"pSearch", "ALL", "", "", "300px;")."
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">GEDUNG</label>
				<div class=\"field\" style=\"margin-left:100px;\">
					".comboData("select kodeData, namaData from mst_data where kodeCategory='X05' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"bSearch", "ALL", "", "", "300px;")."
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">GROUP</label>
				<div class=\"field\" style=\"margin-left:100px;\">
					".comboData("select kodeData, namaData from mst_data where kodeCategory='X06' and statusData='t' ".$fGroup." order by urutanData", "kodeData" , "namaData" ,"tSearch", "ALL", "", "", "300px;")."
				</div>
			</p>
			<p>
				<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">POS</label>
				<div class=\"field\" style=\"margin-left:100px;\">
					".comboData("select kodeData, namaData from mst_data where kodeCategory='X07' and statusData='t' ".$fPos." order by urutanData", "kodeData" , "namaData" ,"mSearch", "ALL", "", "", "300px;")."
				</div>
			</p>		
			</div>
			</fieldset>			
			</form>
			<br clear=\"all\" />
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>VIEW DATA</h3></div>
			</div>				
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:150px;\">Nama</th>					
					<th style=\"min-width:150px;\">Lokasi</th>
					<th style=\"min-width:150px;\">Level 2</th>
					<th style=\"min-width:150px;\">Level 3</th>
					<th style=\"min-width:150px;\">Level 4</th>
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
		
		$sWhere= " where t1.status='".$status."'";						
		if (!empty($_GET['sSearch']))
			$sWhere.= " and (				
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'
			)";
		
		if (!empty($_GET['pSearch'])) $sWhere.= " and t1.rank='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $sWhere.= " and t1.div_id='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $sWhere.= " and t1.dept_id='".$_GET['tSearch']."'";
		if (!empty($_GET['mSearch'])) $sWhere.= " and t1.unit_id='".$_GET['mSearch']."'";
			
		$arrOrder = array(	
			"t.name",
			"t.name",
			"t.namaDivisi",
			"t.namaGedung",	
			"t.namaGroup",	
			"t5.namaData",	
		);
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
			);		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
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
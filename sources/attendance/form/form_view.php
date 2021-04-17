<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	
	function lihat(){
		global $s,$inp,$par,$arrParameter,$arrTitle,$menuAccess,$arrColor;
		$cols = 7;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		$text = table($cols, array($cols));
		
		if(empty($par[empType])) $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>					
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>				
				".comboData("select kodeData, upper(namaData) as namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[empType]","",$par[empType],"onchange=\"window.location='?par[empType]=' + document.getElementById('par[empType]').value + '".getPar($par,"empType")."';\"", "150px;")."
			</p>
			</div>		
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?mode=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
					<th width=\"75\">NPP</th>
					<th>Jabatan</th>
					<th width=\"100\">Tgl. Lahir</th>
					<th width=\"125\">Masa Kerja</th>";
				if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"75\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}
	
	function lData(){
		global $s,$par,$menuAccess,$arrParameter;		
		if(empty($par[empType])) $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$sWhere= " where status='".$status."' and cat='".$par[empType]."'";
			
		
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(pos_name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$arrOrder = array(	
			"name",
			"name",
			"reg_no",
			"pos_name",	
			"birth_date",
			"join_date",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select *, replace(
			  case when coalesce(leave_date,NULL) IS NULL THEN
				  CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
			  ELSE
				  CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
			  END,' 0 bln','') masaKerja from dta_pegawai $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			$statusSite=$r[statusSite] == "t" ?
					"<img src=\"styles/images/t.png\" title=\"Active\">":
					"<img src=\"styles/images/f.png\" title=\"Not Active\">";
			
			$controlEmp="";
			
			if(isset($menuAccess[$s]["edit"]))
			$controlEmp.="<a href=\"?mode=edit&id=$r[id]".getPar($par,"mode")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[name]."</div>",
				"<div align=\"center\">".$r[reg_no]."</div>",
				"<div align=\"left\">".$r[pos_name]."</div>",
				"<div align=\"center\">".getTanggal($r[birth_date])."</div>",
				"<div align=\"right\">".$r[masaKerja]."</div>",
				"<div align=\"center\">".$controlEmp."</div>",
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
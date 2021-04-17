<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	
	function lihat(){
		global $s,$inp,$par,$arrParameter,$arrTitle,$menuAccess,$arrColor, $areaCheck;
		$cols = 8;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		$text = table($cols, array($cols));
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		if(empty($par[empType])) $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' and kodeData!='".$status."' order by urutanData limit 1");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
				<div id=\"pos_l\" style=\"float:left;\">
					<p>					
						<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
						".comboData("select kodeData, upper(namaData) as namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' and kodeData!='".$status."' order by urutanData","kodeData","namaData","par[empType]","",$par[empType],"onchange=\"window.location='?par[empType]=' + document.getElementById('par[empType]').value + '".getPar($par,"empType")."';\"", "150px;")."
					</p>
				</div>	
			</form>	
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
					<th width=\"100\">Tgl. Masuk</th>
					<th width=\"100\">Tgl. Keluar</th>
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
		global $s,$par,$menuAccess,$arrParameter, $areaCheck;				
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		if(empty($par[empType])) $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' and kodeData!='".$status."' order by urutanData limit 1");
		
		$sWhere= " where status='".$par[empType]."' AND location IN ( $areaCheck )";
		if(!empty($par[idLokasi]))
			$filter.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and unit_id='".$par[unitId]."'";		
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
			"join_date",
			"leave_date",
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
				"<div align=\"center\">".getTanggal($r[join_date])."</div>",
				"<div align=\"center\">".getTanggal($r[leave_date])."</div>",
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
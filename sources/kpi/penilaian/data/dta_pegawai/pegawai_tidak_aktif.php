<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/upload/";

function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode])
    {
        case "lst":
		$text=lData();
		break;  

		default:
		$text = lihat();
		break;
	}
	return $text;
}


function lData()
{
	global $s,$par,$menuAccess, $cIdPegawai;
    
	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit="limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);


	$sWhere = " WHERE status = 'f'";

	if (!empty($_GET['fSearch']))
		$sWhere.= " and (				
    	lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
    	)";
        
	$arrOrder = array(	
		"name"
		);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql="
    SELECT * FROM dta_pegawai
	$sWhere order by $orderBy $sLimit";
    
	$res=db($sql);
    
	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("SELECT count(id) FROM dta_pegawai
                                            $sWhere"),
		"aaData" => array(),
		);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;
		$data=array(
			"<div align=\"center\">$no</div>",	
			"<div align=\"left\">$r[name]</div>",
            "<div align=\"center\">$r[reg_no]</div>",
			"<div align=\"left\">".getField("select keterangan from pen_pegawai where idPegawai = $r[id] limit 1")."</div>",
            "<div align=\"left\">".getField("select namaData from mst_data where kodeData = $r[div_id]")."</div>",
            "<div align=\"left\">".getField("select namaData from mst_data where kodeData = $r[dir_id]")."</div>"
			);
		$json['aaData'][]=$data;
	}
	return json_encode($json);
}

function lihat(){
	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;
    
	$cols = 6;  
	$text = table($cols, array($cols,($cols-1),($cols-2),($cols-3)));
    
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
	</div> 

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>					
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$_GET['fSearch']."\" style=\"width:200px;\"/>
				</p>
			</div>

			<div id=\"pos_r\" style=\"float:right; margin-top:15px;\">";
				if(isset($menuAccess[$s]["add"])) {
					$text.="
					<a href=\"index.php?par[mode]=add".getPar($par,"mode")."\" id=\"\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
					";
				}
				$text.="
			</div>	
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"200\">nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">jabatan</th>
					<th width=\"100\">divisi</th>
					<th width=\"100\">lokasi</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
    </div>
	";
	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=exp-".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	$text.="
	<script>
		jQuery(\"#btnExport\").live('click', function(e){
			e.preventDefault();
			window.location.href=\"?par[mode]=xls\"+\"".getPar($par,"mode")."\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
		});
	</script>
	";
	return $text;
}

?>
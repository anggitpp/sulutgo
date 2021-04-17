<?php

if (empty($par['idPeriode'])) $par['idPeriode'] = getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1");

if(!isset($par[tipe])) $par[tipe] = getField("SELECT kodeTipe FROM pen_tipe WHERE statusTipe='t' LIMIT 1");

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/trainee/penilaian/";			

function getContent($par){

	global $s,$_submit,$menuAccess;

	switch($par[mode]){

		case "lst":

			$text=lData();

		break;

		case "get":

			$text=gData();

		break;

		

		case "det":

			$text = data();

		break;

		case "del":

			if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();

		break;

		case "edit":

			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();

		break;			

		case "add":

			if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();

		break;	

		default:

			$text = lihat();

		break;

	}

	return $text;

}

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;

	$cols = 6;		

	$text = table($cols, array(($cols-6),($cols-3),($cols-2),($cols-1),$cols));

	

	$text.="<div class=\"pageheader\">

			<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

			".getBread()."

		</div>    

		<div id=\"contentwrapper\" class=\"contentwrapper\">

		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">

			<div id=\"pos_l\" style=\"float:left;\">

    			<p>					
    
    				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
    
    				".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc","kodeData","namaData","combo1","",$combo1,"","200px;","chosen-select")."
                    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='CMC' and statusData='t' order by namaData asc","kodeData","namaData","combo2","- All Tipe -",$combo2,"","200px;","chosen-select")."
                    
    			</p>

			</div>
            
            <div id=\"pos_r\" style=\"float:right;\">

    			

			</div>					

		</form>

		<br clear=\"all\" />

		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>

				<th width=\"20\">No.</th>
                <th width=\"100\">Tanggal</th>
				<th width=\"200\">tipe</th>	
				<th width=\"200\">kategori</th>	
				<th>judul</th>				
                <th width=\"50\">detail</th>
			</tr>

		</thead>

		<tbody></tbody>

		</table>

		</div>";

	return $text;

}

		

function lData(){

	global $s,$par,$menuAccess, $cIdPegawai;
    
    $par[idPegawai] = empty($_SESSION['idPegawai']) ? $cIdPegawai : $_SESSION['idPegawai'];		

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

	$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
    
	$sWhere= " WHERE id IS NOT NULL and peserta = $par[idPegawai]";

	if (!empty($_GET['fSearch']))

		$sWhere.= " and (
            			lower(judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'	
            		)";
                    
    if(!empty($_GET['combo2'])) $sWhere.= " and kategori = '".$_GET['combo2']."'";
        
	$arrOrder = array(	

		"tanggal",

		"tanggal"	

	);

	

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql="SELECT * FROM pen_cmc
    $sWhere order by $orderBy $sLimit";

	$res=db($sql);

	

	$json = array(

		"iTotalRecords" => mysql_num_rows($res),

		"iTotalDisplayRecords" => getField("SELECT count(id) FROM pen_cmc $sWhere"),

		"aaData" => array(),

	);	

			

	$no=intval($_GET['iDisplayStart']);

	while($r=mysql_fetch_array($res)){						

		$no++;	
        
        $getPhoto = ($r["pic_filename"] == "" ? "files/emp/pic/nophoto.jpg" : "images/foto/".$r["pic_filename"]);
        $photo = "<img src=\"$getPhoto\" width=\"50\">";
        
		$data=array(

			"<div align=\"center\">".$no.".</div>",				

			"<div align=\"center\">".getTanggal($r[tanggal])."</div>",
            
            "<div align=\"left\">".getField("select namaData from mst_data where kodeData = $r[kategori]")."</div>",
            
            "<div align=\"left\">".getField("select namaData from mst_data where kodeData = $r[id_kategori]")."</div>",
            
            "<div align=\"left\">$r[judul]</div>",

			"<div align=\"center\"><a href=\"#\" title=\"Detail\" class=\"detail\"></a></div>",


		);				

		$json['aaData'][]=$data;			

	}

	return json_encode($json);

}

	

		

?>

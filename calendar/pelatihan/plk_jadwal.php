<?php
	include "../../global.php";
	
	$filter = "where idPelatihan is not null and statusPelatihan='t'";
	if(!empty($par[tahunPelatihan]))
		$filter.= " and ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan)";
		
	$sql="select * from plt_pelatihan $filter order by idPelatihan";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$arr[] = array(
			"id" => $r[idPelatihan],
			"title" => "",
			"start" => $r[mulaiPelatihan],
			"end" => $r[selesaiPelatihan],
			"color" => $arrColor[$no%8],			
			"tmp" => $r[judulPelatihan]."\n".$r[lokasiPelatihan]." : ".getTanggal($r[mulaiPelatihan])." s.d ".getTanggal($r[selesaiPelatihan]),
		);
		$no++;
	}
	echo json_encode($arr);
?>
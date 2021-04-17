<?php
	include "../global.php";

	$sql="select * from dta_libur t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where ".$par[tahunLibur].($par[bulanLibur]+1)." between concat(year(t1.mulaiLibur),month(t1.mulaiLibur)) and concat(year(t1.selesaiLibur),month(t1.selesaiLibur)) order by t1.mulaiLibur";
	$res=db($sql);
	$no=0;

	while($r=mysql_fetch_array($res)){
		$arr[$no] = array(
			"id" => $r[idLibur],
			"title" => "",
			"start" => $r[mulaiLibur],
			"end" => $r[selesaiLibur],
			"color" => $arrColor[$no%8],
			"data" => array(
						"idLibur" => $r[idLibur],
						"namaLibur" => $r[namaData]." : ".$r[namaLibur],						
						"mulaiLibur" => $r[mulaiLibur],
						"selesaiLibur" => $r[selesaiLibur],						
					),
			);
		$no++;
	}

	echo json_encode($arr);
?>
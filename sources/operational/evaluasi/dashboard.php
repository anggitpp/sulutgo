<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fPokok = "files/pokok/";
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;							
		if(empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
				
		$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[50]."' order by urutanData");
		
		$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
		if(!empty($par[tahunPelatihan]))
			$filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";
		
		$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $filter order by t1.idPelatihan");
		
		$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $filter order by t1.mulaiPelatihan desc";
		$res=db($sql);
		$no=1;
		while($r=mysql_fetch_array($res)){
			$arrJawaban = explode("\t", $r[evaluasiJawaban]);
			$nilaiPelatihan = 0;
			if(is_array($arrJawaban)){
				reset($arrJawaban);
				while(list($i,$d)=each($arrJawaban)){
					$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
					$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
					$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
				}
			}
			
			$sumKategori["$r[idKategori]"]+= $nilaiPelatihan;
			$avgKategori["$r[idKategori]"]=$r[idKategori];
			$avgPelatihan["$r[idPelatihan]"]=$r[idPelatihan];
			$avgEvaluasi["$r[idPelatihan].$r[idPegawai]"]=$r[idPelatihan];
			
			if($no < 10) $arrPelatihan["$r[idPelatihan]"]=$r[judulPelatihan];
			$arrNilai["$r[idPelatihan]"]["$r[idKategori]"]+= $nilaiPelatihan;
			$cntKategori["$r[idPelatihan]"]["$r[idKategori]"]=$r[idKategori];
			
			
			$no++;
		}
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
			Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"")."
			</div>				
			</form>	

			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai Rata-Rata Pelatihan</h3></div>
			</div>			
			<div id=\"divKategori\" align=\"center\"></div>
			<script type=\"text/javascript\">
			var kategoriChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";
						
			if(is_array($arrKategori)){
				reset($arrKategori);
				while(list($idKategori, $namaKategori) = each($arrKategori)){				
					$nilaiPelatihan = round($sumKategori[$idKategori] / count($avgEvaluasi) / count($avgKategori) / count($avgPelatihan));
					$text.="<set label=\"".$namaKategori."\" value=\"".$nilaiPelatihan."\"/> ";					
				}
			}
			
			$text.="</chart>';
			var chart = new FusionCharts(\"Column3D\", \"chartKategori\", \"100%\", 250);
				chart.setXMLData( kategoriChart );
				chart.render(\"divKategori\");
			</script>
			
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Score 10 Pelatihan</h3></div>
			</div>
			<div id=\"divNilai\" align=\"center\"></div>
			<script type=\"text/javascript\">
			var nilaiChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";
			$text.="<categories>";
			if(is_array($arrPelatihan)){
				reset($arrPelatihan);
				while(list($idPelatihan, $namaPelatihan) = each($arrPelatihan)){
					$cntEvaluasi[$idPelatihan] = count(arrayQuery("select idPegawai from plt_pertanyaan_evaluasi where idPelatihan='$idPelatihan' group by idPegawai"));
					$text.="<category label=\"".$namaPelatihan."\" />";
				}
			}
			$text.="</categories>";
			
			if(is_array($arrKategori)){
				reset($arrKategori);
				while(list($idKategori, $namaKategori) = each($arrKategori)){
					$text.="<dataset seriesName=\"".$namaKategori."\" showValues=\"0\">";
					if(is_array($arrPelatihan)){
						reset($arrPelatihan);
						while(list($idPelatihan, $namaPelatihan) = each($arrPelatihan)){
							$nilaiPelatihan = round($arrNilai[$idPelatihan][$idKategori] / $cntEvaluasi[$idPelatihan] / count($cntKategori[$idPelatihan]));
							$text.="<set value=\"".$nilaiPelatihan."\" />";
						}
					}
					$text.="</dataset>";
				}
			}
				
			$text.="</chart>';
			var chart = new FusionCharts(\"StackedBar2D\", \"chartNilai\", \"100%\", 250);
				chart.setXMLData( nilaiChart );
				chart.render(\"divNilai\");
			</script>
			";
					
		return $text;
	}	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){				
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
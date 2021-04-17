<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;							
    
    
    $par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];
    $par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc limit 1") : $par[bulanPenilaian];
    
	$text.="
    <div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    	".getBread()."				
    </div>    
    
    <!-- Resources -->
    <script src=\"https://www.amcharts.com/lib/4/core.js\"></script>
    <script src=\"https://www.amcharts.com/lib/4/charts.js\"></script>
    <script src=\"https://www.amcharts.com/lib/4/themes/animated.js\"></script>
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
    
    	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px; \">		
                ".comboData("SELECT namaData,kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc","kodeData","namaData","par[bulanPenilaian]","",$par['bulanPenilaian'],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."
                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('form').submit();\"", "200px" ,"chosen-select") ."
    		</div>				
        </form>	
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Pegawai</h3></div>
    	</div>
        
        ";
        $warna = array("murky-green", "purple-orchid", "moon-raker", "camarone", "citrus", "allports", "light-blue");
        $getData = queryAssoc("SELECT * FROM pen_tipe WHERE statusTipe = 't'");
        $no = -1;
        foreach($getData as $data)
        {
            $no++;
            $text.="
            <a href=\"index.php?c=25&p=86&m=997&s=997&par[combo1]=$data[kodeTipe]\">
            <div class=\"dashboard-box $warna[$no]\" style=\"width:220px; float:left; margin-right:20px; margin-bottom:20px;\">
                <div class=\"dashboard-box-header\">
                    <p class=\"dashboard-box-title\">".substr($data[namaTipe],0,23)."</p>
                </div>
                <div class=\"dashboard-box-content\">
                    <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka(getField("SELECT COUNT(id) FROM pen_pegawai WHERE tipePenilaian = '$data[kodeTipe]' AND tahunPenilaian = '$par[idPeriode]'"))."</font></p>
                </div>
            </div>
            </a>
            ";
        }
        $getAspek = queryAssoc("select * from pen_setting_aspek order by urutanAspek");
        $text.="
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Data</h3></div>
    	</div>
       
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
    		<thead>
    			<tr>
    				<th width=\"20\"  style=\"vertical-align: middle\" rowspan=\"3\">NO</th>
    				<th width=\"300\" style=\"vertical-align: middle\" rowspan=\"3\">Tipe penilaian</th>
                    <th width=\"\" style=\"vertical-align: middle\" colspan=\"100\">Jumlah data </th>
    			</tr>
                <tr>
    				<th width=\"150\"  style=\"vertical-align: middle\" rowspan=\"2\">Posisi</th>
                    <th width=\"150\"  style=\"vertical-align: middle\" rowspan=\"2\">Pegawai</th>
                    ";
                    foreach($getAspek as $asp) { $text.="<th width=\"\" style=\"vertical-align: middle\">$asp[namaAspek]</th>"; }
                    $text.="
    			</tr>
    		</thead>
            <tbody>
                ";
                $no=0;
                $totalPosisi=0;
                $totalPegawai=0;
                $total1=0;
                $total2=0;
                foreach($getData as $data)
                {
                    
                    $getPosisi = getField("select count(idKode) from pen_setting_kode where kodeTipe = '$data[kodeTipe]'");
                    $getPegawai = queryAssoc("SELECT * FROM pen_pegawai WHERE tipePenilaian = '$data[kodeTipe]' AND tahunPenilaian = '$par[idPeriode]'");
                    
                    $count1 = 0;
                    $count2 = 0;
                    foreach($getPegawai as $peg)
                    {
                        $cek1 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 1 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$par[bulanPenilaian]'");
                        if($cek1) $count1++;
                        
                        $cek2 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 2 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$par[bulanPenilaian]'");
                        if($cek2) $count2++;
                    }
                    
                    $no++;
                    $text.="
                    <tr>
                        <td style=\"text-align:center;\">$no</td>
                        <td style=\"text-align:left;\">$data[namaTipe]</td>
                        <td style=\"text-align:center;\">".getAngka($getPosisi)."</td>
                        <td style=\"text-align:center;\"><a href=\"index.php?c=25&p=86&m=997&s=997&par[combo1]=$data[kodeTipe]\">".getAngka(count($getPegawai))."</a></td>
                        <td style=\"text-align:center;\">".getAngka($count1)."</td>
                        <td style=\"text-align:center;\">".getAngka($count2)."</td>
                    </tr>
                    ";
                    $totalPosisi = $totalPosisi + $getPosisi;
                    $totalPegawai = $totalPegawai + count($getPegawai);
                    $total1 = $total1 + $count1;
                    $total2 = $total2 + $count2;
                }
                $text.="
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=\"2\" style=\"text-align:right;\"><strong>Total</strong></td>
                    <td style=\"text-align:center;\"><strong>$totalPosisi</strong></td>
                    <td style=\"text-align:center;\"><strong>$totalPegawai</strong></td>
                    <td style=\"text-align:center;\"><strong>$total1</strong></td>
                    <td style=\"text-align:center;\"><strong>$total2</strong></td>
                </tr>
            </tfoot>
        </table>
        
        <br />
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Per-Divisi : ".getField("select namaData from mst_data where kodeData = '$par[bulanPenilaian]'")." - ".getField("select namaData from mst_data where kodeData = '$par[idPeriode]'")."</h3></div>
    	</div>
        
        <style>
        #chartdiv2 {
          width: 100%;
          height: 500px;
        }
        </style>
        
        <fieldset>
            <div id=\"chartdiv2\"></div>
        </fieldset>
        
        <script>
            AmCharts.makeChart(\"chartdiv2\",
				{
					\"type\": \"serial\",
					\"categoryField\": \"category\",
					\"startDuration\": 1,
					\"theme\": \"light\",
					\"categoryAxis\": {
						\"gridPosition\": \"start\"
					},
					\"trendLines\": [],
					\"graphs\": [
						{
							\"balloonText\": \"[[title]] = [[value]]\",
							\"fillAlphas\": 1,
							\"id\": \"AmGraph-1\",
							\"labelText\": \"[[value]]\",
							\"title\": \"Total Pegawai\",
							\"type\": \"column\",
							\"valueField\": \"total\"
						},
						{
							\"balloonText\": \"[[title]] = [[value]]\",
							\"bullet\": \"round\",
							\"id\": \"AmGraph-2\",
							\"labelText\": \"[[value]]\",
							\"lineThickness\": 2,
							\"title\": \"BUSINESS PERFORMANCE\",
							\"valueField\": \"business\"
						},
						{
						    \"balloonText\": \"[[title]] = [[value]]\",
							\"bullet\": \"round\",
							\"id\": \"AmGraph-3\",
                            \"labelText\": \"[[value]]\",
							\"lineThickness\": 2,
							\"title\": \"SOFT COMPETENCY\",
							\"valueField\": \"soft\"
						}
					],
					\"guides\": [],
					\"allLabels\": [],
					\"balloon\": {},
					\"legend\": {
						\"enabled\": true,
						\"useGraphSettings\": true
					},
					\"dataProvider\": [
                    
                        ";
                        
                        
                        $no=0;
                        $totalPosisi=0;
                        $total1=0;
                        $total2=0;
                        foreach($getData as $data)
                        {
                            
                            
                            $getPegawai = queryAssoc("SELECT * FROM pen_pegawai WHERE tipePenilaian = '$data[kodeTipe]' AND tahunPenilaian = '$par[idPeriode]'");
                            
                            $count1 = 0;
                            $count2 = 0;
                            foreach($getPegawai as $peg)
                            {
                                $cek1 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 1 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$par[bulanPenilaian]'");
                                if($cek1) $count1++;
                                
                                $cek2 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 2 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$par[bulanPenilaian]'");
                                if($cek2) $count2++;
                            }
                            
                            $no++;
                            $text.="
                            {
    							\"category\": \"$data[namaTipe]\",
    							\"total\": ".count($getPegawai).",
    							\"business\": ".getAngka($count1).",
    							\"soft\": ".getAngka($count2)."
    						},
                            ";
                        }
                        
                        
                        
                        
                        
                        $text.="
                    
					]
				}
			);
        </script>

        
        <br />
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Tahunan : ".getField("select namaData from mst_data where kodeData = '$par[idPeriode]'")."</h3></div>
    	</div>
        
        <style>
        #chartdiv {
          width: 100%;
          height: 500px;
        }
        </style>
        
        <fieldset>
            <div id=\"chartdiv\"></div>
        </fieldset>
        
        <script>
            AmCharts.makeChart(\"chartdiv\",
				{
					\"type\": \"serial\",
					\"categoryField\": \"category\",
					\"startDuration\": 1,
					\"theme\": \"light\",
					\"categoryAxis\": {
						\"gridPosition\": \"start\"
					},
					\"trendLines\": [],
					\"graphs\": [
						{
							\"balloonText\": \"[[title]] = [[value]]\",
							\"fillAlphas\": 1,
							\"id\": \"AmGraph-1\",
							\"labelText\": \"[[value]]\",
							\"title\": \"Total Pegawai\",
							\"type\": \"column\",
							\"valueField\": \"total\"
						},
						{
							\"balloonText\": \"[[title]] = [[value]]\",
							\"bullet\": \"round\",
							\"id\": \"AmGraph-2\",
							\"labelText\": \"[[value]]\",
							\"lineThickness\": 2,
							\"title\": \"BUSINESS PERFORMANCE\",
							\"valueField\": \"business\"
						},
						{
						    \"balloonText\": \"[[title]] = [[value]]\",
							\"bullet\": \"round\",
							\"id\": \"AmGraph-3\",
                            \"labelText\": \"[[value]]\",
							\"lineThickness\": 2,
							\"title\": \"SOFT COMPETENCY\",
							\"valueField\": \"soft\"
						}
					],
					\"guides\": [],
					\"allLabels\": [],
					\"balloon\": {},
					\"legend\": {
						\"enabled\": true,
						\"useGraphSettings\": true
					},
					\"dataProvider\": [
                    
                        ";
                        $getBulan = queryAssoc("SELECT * FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc");
                        foreach($getBulan as $bln)
                        {   
                            $count11 = 0;
                            $count22 = 0;
                            foreach($getData as $data)
                            {
                                $getPegawai = queryAssoc("SELECT * FROM pen_pegawai WHERE tipePenilaian = '$data[kodeTipe]' AND tahunPenilaian = '$par[idPeriode]'");
                                foreach($getPegawai as $peg)
                                {
                                    $cek11 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 1 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$bln[kodeData]'");
                                    if($cek11) $count11++;
                                    $cek22 = queryAssoc("select DISTINCT(id_aspek) from pen_realisasi_individu where id_aspek = 2 and id_pegawai = $peg[idPegawai] AND id_tahun = '$par[idPeriode]' AND id_bulan = '$bln[bulanPenilaian]'");
                                    if($cek22) $count22++;
                                    
                                }
                            }
            
                            $text.="
                            {
    							\"category\": \"$bln[namaData]\",
    							\"total\": $totalPegawai,
    							\"business\": $count11,
    							\"soft\": $count22
    						},
                            ";
                        }
                        
                        
                        
                        $text.="
                    
					]
				}
			);
        </script>
        
        
        <br />
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Hasil Penilaian Per-Divisi : ".getField("select namaData from mst_data where kodeData = '$par[bulanPenilaian]'")." - ".getField("select namaData from mst_data where kodeData = '$par[idPeriode]'")."</h3></div>
    	</div>
        
        <style>
        #chartdiv3 {
          width: 100%;
          height: 500px;
        }
        </style>
        
        <fieldset>
            <div id=\"chartdiv3\"></div>
        </fieldset>
        
        <script>
            AmCharts.makeChart(\"chartdiv3\",
				{
					\"type\": \"serial\",
					\"categoryField\": \"category\",
					\"startDuration\": 1,
					\"theme\": \"light\",
					\"categoryAxis\": {
						\"gridPosition\": \"start\"
					},
					\"trendLines\": [],
					\"graphs\": [
						{
							\"balloonText\": \"[[value]]\",
							\"fillAlphas\": 1,
							\"id\": \"AmGraph-1\",
							\"labelText\": \"[[value]]\",
							\"title\": \"Total Pegawai\",
							\"type\": \"column\",
							\"valueField\": \"total\",
                            // \"fillColors\": \"#A72424\",
						}
					],
					\"guides\": [],
					\"allLabels\": [],
					\"balloon\": {},
					\"dataProvider\": [
                    
                        ";
                        
                        
                        foreach($getData as $data)
                        {
                            
                            
                            $getPegawai = queryAssoc("SELECT * FROM pen_pegawai WHERE tipePenilaian = '$data[kodeTipe]' AND tahunPenilaian = '$par[idPeriode]'");
                            
                            foreach($getPegawai as $peg)
                            {
                                $arrIdPegawai[] = $peg[idPegawai];
                            }
                            $idPegawai = implode(",", $arrIdPegawai);
                            $idPegawai = empty($getPegawai) ? 0 : $idPegawai;
                            
                            $nilai = getField("select sum(nilai) from pen_hasil where id_periode = '$par[idPeriode]' and id_bulan = '$par[bulanPenilaian]' and id_pegawai in ($idPegawai)");
                            $nilai = empty($nilai) ? 0 : $nilai;
                            
                            $totalPegawai = count($getPegawai);
                            $nilaiRata2 = round($nilai/$totalPegawai, 2);
                            
                            $totalNilai = $nilai == 0 ? 0 : $nilaiRata2;
                            
                            $text.="
                            {
    							\"category\": \"$data[namaTipe]\",
    							\"total\": ".$totalNilai.",
    						},
                            ";
                        }
                        
                        
                        $text.="
                    
					]
				}
			);
        </script>

        
        <br />
        
        
    </div>
    
    
    
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
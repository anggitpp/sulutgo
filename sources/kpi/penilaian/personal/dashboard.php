<?php
function getContent($par) {
	global $s, $_submit, $menuAccess, $cIdPegawai;		
	switch ($par[mode]) {
		default:
		$text = lihat();
		break;
	}
	return $text;
}

function lihat() {
	global $s, $inp, $par, $arrTitle, $db, $arrParameter, $menuAccess, $brandName,$db, $cIdPegawai;
    
    $par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];
    
    $totalStaff = getField("SELECT count(id) FROM dta_pegawai where id in (select idPegawai from pen_pegawai where atasan_langsung = $cIdPegawai or atasan_dari_atasan = $cIdPegawai)");
    $totalPria = getField("SELECT count(id) FROM dta_pegawai where id in (select idPegawai from pen_pegawai where atasan_langsung = $cIdPegawai or atasan_dari_atasan = $cIdPegawai) AND gender = 'M'");
    $totalWanita = getField("SELECT count(id) FROM dta_pegawai where id in (select idPegawai from pen_pegawai where atasan_langsung = $cIdPegawai or atasan_dari_atasan = $cIdPegawai) AND gender = 'F'");

	$text.="
    <!-- Resources -->
    <script src=\"https://www.amcharts.com/lib/4/core.js\"></script>
    <script src=\"https://www.amcharts.com/lib/4/charts.js\"></script>
    <script src=\"https://www.amcharts.com/lib/4/themes/animated.js\"></script>
    
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . " </h1>
		" . getBread() . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
    
        <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px; \">		
                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('form').submit();\"", "200px", "chosen-select") ."
    		</div>
        </form>	
    
		<table style=\"width:100%\">
			<tr>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box\" style=\"background-color:#53e25d;\">
                        <div class=\"dashboard-box-header\">
                            <p class=\"dashboard-box-title\">Total Staff</p>
                        </div>
                        <div class=\"dashboard-box-content\">
                            <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">$totalStaff</font></p>
                        </div>
                    </div>
				</td>
                <td style=\"width: 33%\">
					<div class=\"dashboard-box\" style=\"background-color:#66b5ff;\">
                        <div class=\"dashboard-box-header\">
                            <p class=\"dashboard-box-title\">Pria</p>
                        </div>
                        <div class=\"dashboard-box-content\">
                            <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">$totalPria</font></p>
                        </div>
                    </div>
				</td>
                <td style=\"width: 33%\">
					<div class=\"dashboard-box\" style=\"background-color:#ffa0ed;\">
                        <div class=\"dashboard-box-header\">
                            <p class=\"dashboard-box-title\">Wanita</p>
                        </div>
                        <div class=\"dashboard-box-content\">
                            <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">$totalWanita</font></p>
                        </div>
                    </div>
				</td>
			</tr>
		</table>
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai rata-rata</h3></div>
    	</div>
        
        <style>
        #chartdiv {
          width: 100%;
          height: 400px;
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
        		\"categoryAxis\": {
        			\"gridPosition\": \"start\"
        		},
        		\"trendLines\": [],
        		\"graphs\": [
        			{
        				\"balloonText\": \"[[value]]\",
        				\"bullet\": \"round\",
        				\"id\": \"AmGraph-1\",
        				\"title\": \"Nilai\",
        				\"valueField\": \"nilai\"
        			}
        		],
        		\"guides\": [],
        		\"allLabels\": [],
        		\"balloon\": {},
        		\"legend\": {
        			\"enabled\": true,
        			\"useGraphSettings\": true
        		},
        		\"dataProvider\": [";
                
                $getBulan = queryAssoc("SELECT kodeData,namaData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]'");
                foreach($getBulan as $data)
                {
                    $nilai = getField("SELECT AVG(nilai) FROM pen_hasil WHERE id_periode = $par[idPeriode] and id_bulan = $data[kodeData] and id_pegawai IN (SELECT idPegawai FROM pen_pegawai WHERE atasan_langsung = $cIdPegawai OR atasan_dari_atasan = $cIdPegawai)");
                    $nilai = empty($nilai) ? 0 : $nilai;
                    $text.="
                            {
                				\"category\": \"$data[namaData]\",
                				\"nilai\": $nilai
                			},
                    ";
                }
                $text.="
        		]
        	}
        );
        </script>
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Penilaian Individu</h3></div>
    	</div>
        
        <style>
        #chartdiv2 {
          width: 100%;
          height: 400px;
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
        		\"categoryAxis\": {
        			\"gridPosition\": \"start\"
        		},
        		\"trendLines\": [],
        		\"graphs\": [
                ";
                $getPegawai = queryAssoc("SELECT id,name FROM dta_pegawai where id in (select idPegawai from pen_pegawai where atasan_langsung = $cIdPegawai or atasan_dari_atasan = $cIdPegawai)");
                foreach($getPegawai as $peg)
                {
                    $text.="
                    {
        				\"balloonText\": \"[[value]]\",
        				\"bullet\": \"round\",
        				\"id\": \"AmGraph-$peg[id]\",
        				\"title\": \"$peg[name]\",
        				\"valueField\": \"nilai_$peg[id]\"
        			},
                    ";
                }
                $text.="
        			
        		],
        		\"guides\": [],
        		\"allLabels\": [],
        		\"balloon\": {},
        		\"legend\": {
        			\"enabled\": true,
        			\"useGraphSettings\": true
        		},
        		\"dataProvider\": [";
                
                $getBulan = queryAssoc("SELECT kodeData,namaData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]'");
                foreach($getBulan as $data)
                {
                    
                    $text.="
                            {
                				\"category\": \"$data[namaData]\",
                                ";
                                foreach($getPegawai as $peg)
                                {
                                    $nilai = getField("SELECT nilai FROM pen_hasil WHERE id_periode = $par[idPeriode] and id_bulan = $data[kodeData] and id_pegawai = $peg[id]");
                                    $nilai = empty($nilai) ? 0 : $nilai;
                                    $text.="
                                    \"nilai_$peg[id]\": $nilai,
                                    ";
                                }
                                $text.="
                				
                			},
                    ";
                }
                $text.="
        		]
        	}
        );
        </script>
        
    </div>";

    return $text;
}
?>
<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;							
    if(empty($par[bulanData])) $par[bulanData] = date('m');
    if(empty($par[tahunData])) $par[tahunData] = date('Y');
    
    // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
    $arrStatus = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='S04' order by urutanData");
    $arrPendidikan = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='R11' order by urutanData");
    $arrDivisi = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='DIV' AND namaData NOT LIKE '%CABANG%' order by urutanData");
    $arrCabang = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='DIV' AND namaData LIKE '%CABANG%' order by urutanData");

    $jmlUmur["&gt;50"]=0;		
	$jmlUmur["41-50"]=0;
	$jmlUmur["31-40"]=0;
    $jmlUmur["&lt;31"]=0;
    
    $arrGender["M"]=getField("SELECT count(id) FROM emp WHERE gender = 'M'");	
    $arrGender["F"]=getField("SELECT count(id) FROM emp WHERE gender = 'F'");
    
    $sql = "select * from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1')";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        list($tahunLahir, $bulanLahir) = explode("-", $r[birth_date]);
		$usiaPegawai = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", $par[tahunData]."-".$par[bulanData]."-01 00:00:00");
        $jmlPegawai++;
        
        //if($r[gender]=="M")$arrGender["M"]++;
        //if($r[gender]=="F")$arrGender["F"]++;
        
        //$arrGender[$r[gender]]++;	
        $jmlStatus["$r[cat]"]++;
        $jmlPendidikan["$r[pendidikan_pegawai]"]++;
        $jmlDivisi["$r[div_id]"]++;

        if($r[birth_date] != '0000-00-00'){
        if($usiaPegawai < 31)
            $jmlUmur["&lt;31"]++;
        else if($usiaPegawai <= 41)
            $jmlUmur["31-40"]++;
		else if($usiaPegawai <= 51)
            $jmlUmur["41-50"]++;
		else
            $jmlUmur["&gt;50"]++;
        }
    }

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."				
</div>    
<!-- Styles -->
<style>

    .chart-size {
        width: 100%;
        height: 500px;
    }

    tspan {
        font-size: 10px;
    }

</style>

<!-- Resources -->
<script src=\"https://www.amcharts.com/lib/4/core.js\"></script>
<script src=\"https://www.amcharts.com/lib/4/charts.js\"></script>
<script src=\"https://www.amcharts.com/lib/4/themes/animated.js\"></script>
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
            ".comboMonth("par[tahunData]", $par[bulanData], "onchange=\"document.getElementById('form').submit();\"")."
            ".comboYear("par[tahunData]", $par[tahunData], "", "onchange=\"document.getElementById('form').submit();\"")."
		</div>				
    </form>	
    
    <table style=\"width:100%; margin-top:20px;\">
        <tr>
            <td style=\"width:30%;\">
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width: 50%\">
                            <a href=\"index.php?c=15&p=57&m=436&s=436\">
                            <div class=\"dashboard-box murky-green\">
                                <div class=\"dashboard-box-header\">
                                    <p class=\"dashboard-box-title\">Total Pegawai</p>
                                </div>
                                <div class=\"dashboard-box-content\">
                                    <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($jmlPegawai)."</font></p>
                                </div>
                            </div>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
            <td style=\"width:30%;\">
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width: 50%\">
                            <a href=\"index.php?c=15&p=57&m=436&s=436&par[gender]=M\">
                            <div class=\"dashboard-box goldenrod\">
                                <div class=\"dashboard-box-header\">
                                    <p class=\"dashboard-box-title\">Pria</p>
                                </div>
                                <div class=\"dashboard-box-content\">
                                    <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($arrGender["M"])."</font></p>
                                </div>
                            </div>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
            <td style=\"width:30%;\">
                <table style=\"width:100%\">
                    <tr>
                        <td style=\"width: 50%\">
                            <a href=\"index.php?c=15&p=57&m=436&s=436&par[gender]=F\">
                            <div class=\"dashboard-box allports\">
                                <div class=\"dashboard-box-header\">
                                    <p class=\"dashboard-box-title\">Wanita</p>
                                </div>
                                <div class=\"dashboard-box-content\">
                                    <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($arrGender["F"])."</font></p>
                                </div>
                            </div>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Pegawai Berdasarkan Umur</h3></div>
	</div>
	<div id=\"divUmur\" align=\"center\"></div>
	<script type=\"text/javascript\">
		var umurChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" basefontColor=\"000000\" stack100Percent=\"1\" showPercentValues=\"0\" numberSuffix=\" Orang\">";
		$text.="<categories>";
		$text.="<category label=\"\" />";
		$text.="</categories>";

		if(is_array($jmlUmur)){
			reset($jmlUmur);
			while(list($lblUmur, $valUmur) = each($jmlUmur)){					
				$text.="<dataset seriesName=\"Umur ".$lblUmur."\" showValues=\"1\">";
				$text.="<set value=\"".$valUmur."\" />";
				$text.="</dataset>";
			}
		}
		$text.="</chart>';

		var chart = new FusionCharts(\"StackedBar3D\", \"chartUmur\", \"100%\", \"150\");
		chart.setXMLData( umurChart );
		chart.render(\"divUmur\");
    </script>
    
    <table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
		<tr>
			<td style=\"width:67%; vertical-align:top; padding-left:15px; padding-right:15px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Status Pegawai</h3></div>
				</div>
				<div id=\"divStatus\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var statusChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";
					
					if(is_array($arrStatus)){
						reset($arrStatus);
						while(list($idStatus, $namaStatus) = each($arrStatus)){				
							$text.="<set label=\"".$namaStatus."\" value=\"".$jmlStatus[$idStatus]."\"/> ";					
						}
					}
					
					$text.="</chart>';
					var chart = new FusionCharts(\"Bar2D\", \"chartStatus\", \"100%\", 200);
					chart.setXMLData( statusChart );
					chart.render(\"divStatus\");
				</script>
			</td>
			<td style=\"width:33%; vertical-align:top; padding-left:15px; padding-right:15px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Gender</h3></div>
				</div>
				<ul class=\"toplist\">";

					$totGender=array_sum($arrGender);
					if(is_array($arrGender)){
						reset($arrGender);
						while(list($idGender, $jmlGender) = each($arrGender)){	
                            if(!empty($idGender)){						
							$persenGender = $jmlGender / $totGender * 100;

							$text.="<li>
							<div>
								<span class=\"one_fourth\">
									<span class=\"left\">
										<img src=\"styles/images/".strtolower($idGender)."gender.jpg\">
									</span>
								</span>
								<span class=\"three_fourth last\">
									<span class=\"right\">
										<h1>".round($persenGender)." %</h1>
										<span class=\"title\" style=\"margin-top:10px;\">".getAngka($jmlGender)." Orang</span>										
									</span>
								</span>
								<br clear=\"all\">
							</div>
                        </li>";
                        }
					}
				}
				$text.="</ul>					
			</td>
		</tr>
    </table> 
    
    <table style=\"width:100%; margin-top:30px; margin-bottom:10px; margin-left:-15px;\">
		<tr>
            <td style=\"width: 50%\">
                <br>
                <div style=\"border: 1px solid #CCC; align-item: center;\">
                    <div style=\"padding: 8px; border-bottom: 1px solid #ccc; text-align: center;\">Status Pegawai</div>
                    <div id=\"div_status\" class=\"chart-size\"></div>
                </div>
        
                <script type=\"text/javascript\">
                    am4core.ready(function() {
                        // Themes begin
                        am4core.useTheme(am4themes_animated);
                        // Themes end

                        // Create chart instance
                        var chart = am4core.create(\"div_status\", am4charts.PieChart);

                        chart.exporting.menu = new am4core.ExportMenu()
                        chart.hiddenState.properties.opacity = 0 // this creates initial fade-in
                        chart.data = [";
                            if(is_array($arrStatus)){
                                reset($arrStatus);
                                while(list($idStatus, $namaStatus) = each($arrStatus)){				
                                    $text.="{\"status\": \"$namaStatus\",\"total\": \"".$jmlStatus[$idStatus]."\"},";		
                                }
                            }
                        $text.="
                        ];
                        // Add a legend
                        chart.legend = new am4charts.Legend();
                        // Add and configure Series
                        var series = chart.series.push(new am4charts.PieSeries());
                        series.dataFields.category = \"status\";
                        series.dataFields.value = \"total\";
                        // Let's cut a hole in our Pie chart the size of 30% the radius
                        chart.innerRadius = am4core.percent(30);
                        series.slices.template
                        // change the cursor on hover to make it apparent the object can be interacted with
                        .cursorOverStyle = [
                            {
                            \"property\": \"cursor\",
                            \"value\": \"pointer\"
                            }
                        ];
                        series.alignLabels = true;
                        series.labels.template.bent = true;
                        series.labels.template.radius = 3;
                        series.labels.template.padding(0,0,0,0);
                        series.ticks.template.disabled = false;

                        // Create a base filter effect (as if it's not there) for the hover to return to
                        var shadow = series.slices.template.filters.push(new am4core.DropShadowFilter);
                        shadow.opacity = 0;

                        // Create hover state
                        var hoverState = series.slices.template.states.getKey(\"hover\"); // normally we have to create the hover state, in this case it already exists
                    });
                </script>
            </td>
            <td style=\"width: 50%\">
                <br>
                <div style=\"border: 1px solid #CCC; align-item: center;\">
                    <div style=\"padding: 8px; border-bottom: 1px solid #ccc; text-align: center;\">Pendidikan</div>
                    <div id=\"div_pendidikan\" class=\"chart-size\"></div>
                </div>
        
                <script type=\"text/javascript\">
                    am4core.ready(function() {
                        // Themes begin
                        am4core.useTheme(am4themes_animated);
                        // Themes end

                        // Create chart instance
                        var chart = am4core.create(\"div_pendidikan\", am4charts.PieChart);

                        chart.exporting.menu = new am4core.ExportMenu()
                        chart.hiddenState.properties.opacity = 0 // this creates initial fade-in
                        chart.data = [";
                            if(is_array($arrPendidikan)){
                                reset($arrPendidikan);
                                while(list($idPendidikan, $namaPendidikan) = each($arrPendidikan)){				
                                    $text.="{\"pendidikan\": \"$namaPendidikan\",\"total\": \"".$jmlPendidikan[$idPendidikan]."\"},";		
                                }
                            }
                        $text.="
                        ];
                        // Add a legend
                        chart.legend = new am4charts.Legend();
                        // Add and configure Series
                        var series = chart.series.push(new am4charts.PieSeries());
                        series.dataFields.category = \"pendidikan\";
                        series.dataFields.value = \"total\";
                        // Let's cut a hole in our Pie chart the size of 30% the radius
                        chart.innerRadius = am4core.percent(30);
                        series.slices.template
                        // change the cursor on hover to make it apparent the object can be interacted with
                        .cursorOverStyle = [
                            {
                            \"property\": \"cursor\",
                            \"value\": \"pointer\"
                            }
                        ];
                        series.alignLabels = true;
                        series.labels.template.bent = true;
                        series.labels.template.radius = 3;
                        series.labels.template.padding(0,0,0,0);
                        series.ticks.template.disabled = false;

                        // Create a base filter effect (as if it's not there) for the hover to return to
                        var shadow = series.slices.template.filters.push(new am4core.DropShadowFilter);
                        shadow.opacity = 0;

                        // Create hover state
                        var hoverState = series.slices.template.states.getKey(\"hover\"); // normally we have to create the hover state, in this case it already exists
                    });
                </script>
            </td>
		</tr>
    </table> 
    
    <div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Pegawai per Divisi</h3></div>
	</div>

	<div id=\"divPangkat\" align=\"center\"></div>
	<script type=\"text/javascript\">
		var pangkatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";

		if(is_array($jmlDivisi)){
			reset($jmlDivisi);
			while(list($idDivisi, $valDivisi) = each($jmlDivisi)){									
				if(isset($arrDivisi[$idDivisi])) $text.="<set label=\"".$arrDivisi[$idDivisi]."\" value=\"".$valDivisi."\"/> ";					
			}
		}

		$text.="</chart>';
		var chart = new FusionCharts(\"Column3D\", \"chartPangkat\", \"100%\", 250);
		chart.setXMLData( pangkatChart );
		chart.render(\"divPangkat\");
    </script>
    
    <div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Pegawai per Cabang</h3></div>
	</div>

	<div id=\"divCabang\" align=\"center\"></div>
	<script type=\"text/javascript\">
		var cabangChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";

		if(is_array($jmlDivisi)){
			reset($jmlDivisi);
			while(list($idDivisi, $valDivisi) = each($jmlDivisi)){									
				if(isset($arrCabang[$idDivisi])) $text.="<set label=\"".$arrCabang[$idDivisi]."\" value=\"".$valDivisi."\"/> ";					
			}
		}

		$text.="</chart>';
		var chart = new FusionCharts(\"Column3D\", \"chartPangkat\", \"100%\", 250);
		chart.setXMLData( cabangChart );
		chart.render(\"divCabang\");
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
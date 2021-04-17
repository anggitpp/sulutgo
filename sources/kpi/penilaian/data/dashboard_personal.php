<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck, $cIdPegawai;							
    
    if(empty($par[idPegawai]))
    {
        $par[idPegawai] = $cIdPegawai;
        unset($_SESSION['idPegawai']);
    }
    else
    {
        $_SESSION['idPegawai'] = $par[idPegawai];
    }
    
    $par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];
    $par[bulanPenilaian] = !getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' and kodeData = '$par[bulanPenilaian]' order by kodeData asc limit 1") ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc limit 1") : $par[bulanPenilaian];
    $getBulan = queryAssoc("SELECT * FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk = $par[idPeriode]");
    $get10thn = queryAssoc("select * from ( SELECT * FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData desc limit 10) as x order by kodeData asc");
    
    $query = db("select * from dta_pegawai where id = ".$par["idPegawai"]."");
    $r = mysql_fetch_array($query);
    
	$text.="
    <div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]." </h1>
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
                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('form').submit();\"", "200px", "chosen-select") ."
    		</div>				
        
           
            ";
            $getDiv = queryAssoc("select * from pen_pegawai where idPegawai = $par[idPegawai] and tahunPenilaian = $par[idPeriode] limit 1");
            $div = $getDiv[0];
            $nilai = getField("select nilai from pen_hasil WHERE id_periode = $par[idPeriode] and id_bulan = $par[bulanPenilaian] and id_pegawai = $par[idPegawai]");
            $nilai = empty($nilai) ? 0 : $nilai;
            
            $wom = getWom($nilai, $par[idPeriode]);
            
            $text.="
            <table style=\"width: 100%;\">
                <tr>
                    <td>
                        <fieldset>
                            <table style=\"width: 100%;\">
                                <tr>
                                    <td width=\"100px\" style=\"vertical-align:top;\">
                                        <img width=\"120\" height=\"146\" src=\"".($r["pic_filename"] == "" ? "files/emp/pic/nophoto.jpg" : "images/foto/".$r["pic_filename"])."\">
                                    </td>
                                    <td>
                                        <p>
                        					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Nama Pegawai </label>
                        					<span class=\"field\" style=\"margin-left:100px;\">
                                                $r[name] &nbsp;
                                            </span>
                        				</p>
                                        <p>
                        					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">NPP</label>
                        					<span class=\"field\" style=\"margin-left:100px;\">
                                                $r[reg_no] &nbsp;
                                            </span>
                        				</p>
                                        <p>
                        					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Tipe Penilaian</label>
                        					<span class=\"field\" style=\"margin-left:100px;\">
                                                ".getField("select namaTipe from pen_tipe where kodeTipe = '$div[tipePenilaian]'")." &nbsp;
                                            </span>
                        				</p>
                                        <p>
                        					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Kode Penilaian</label>
                        					<span class=\"field\" style=\"margin-left:100px;\">
                                                ".getField("select subKode from pen_setting_kode where idKode = '$div[kodePenilaian]'")." &nbsp;
                                            </span>
                        				</p>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td style=\"width:315px; vertical-align:top;\">
                        <div class=\"dashboard-box \" style=\"background-color:$wom[warna]; width:300px; height:170px; float:right; margin-left:15px;\">
                            <div class=\"dashboard-box-header\">
                                <p class=\"dashboard-box-title\">".getField("select namaData from mst_data where kodeData = $par[bulanPenilaian]")."</p>
                            </div>
                            <div class=\"dashboard-box-content\">
                                <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">$nilai</font></p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
            
            <br />
            
            ";
            $getAspek = queryAssoc("select * from pen_setting_aspek order by urutanAspek");
            $text.="
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
        		<thead>
        			<tr>
        				<th width=\"30\" style=\"vertical-align:middle;\" rowspan=\"2\">NO</th>
        				<th width=\"150\" style=\"vertical-align:middle;\" rowspan=\"2\">Bulan</th>
                        ";
                        foreach($getAspek as $asp)
                        {
                            $tt = getField("select count(*) from pen_setting_prespektif where idTipe = '$div[tipePenilaian]' and idKode = '$div[kodePenilaian]' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) and idAspek = '$asp[idAspek]'");
                            $text.="<th width=\"\" colspan=\"$tt\">$asp[namaAspek]</th>";
                        }
                        $text.="
        			</tr>
                    <tr>
                        ";
                        foreach($getAspek as $asp)
                        {
                            $getComp = queryAssoc("select namaPrespektif from pen_setting_prespektif where idTipe = '$div[tipePenilaian]' and idKode = '$div[kodePenilaian]' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) and idAspek = '$asp[idAspek]'");
                            foreach($getComp as $comp)
                            {
                                $text.="<th>$comp[namaPrespektif]</th>";
                            }
                        }
                        $text.=" 
                    </tr>
        		</thead>
                <tbody>
                    ";
                    $no=0;
                    foreach($getBulan as $bln)
                    {
                        $no++;
                        $text.="
                        <tr>
                            <td style=\"text-align:center;\">$no</td>
                            <td style=\"text-align:left;\">$bln[namaData]</td>
                            ";
                            foreach($getAspek as $asp)
                            {
                                $getComp = queryAssoc("select idPrespektif from pen_setting_prespektif where idTipe = '$div[tipePenilaian]' and idKode = '$div[kodePenilaian]' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) and idAspek = '$asp[idAspek]'");
                                foreach($getComp as $comp)
                                {
                                    $nilai = getField("select nilai from pen_hasil_detail where id_pegawai = $par[idPegawai] and id_periode = $par[idPeriode] and id_bulan = $bln[kodeData] and id_prespektif = $comp[idPrespektif]");
                                    $nilai = (empty($nilai)) ? 0 : $nilai;
                                    $text.="
                                    <td style=\"text-align:center;\">".round($nilai,1)."</td>
                                    ";
                                }
                            }
                            $text.=" 
                        </tr>
                        ";
                    }
                    $text.="
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan=\"2\" style=\"text-align:right;\"><strong>Rata - rata</strong></td>
                        ";
                        foreach($getAspek as $asp)
                        {
                            $getComp = queryAssoc("select idPrespektif from pen_setting_prespektif where idTipe = '$div[tipePenilaian]' and idKode = '$div[kodePenilaian]' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) and idAspek = '$asp[idAspek]'");
                            foreach($getComp as $comp)
                            {
                                $text.="
                                <td style=\"text-align:center;\">-</td>
                                ";
                            }
                        }
                        $text.=" 
                    </tr>
                </tfoot>
            </table>
            
            
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai rata-rata per bulan</h3></div>
    	</div>
        
        <!-- Styles -->
        <style>
        #chartRata2Bulan {
          width: 100%;
          height: 500px;
        }
        </style>
        
        
        <!-- Chart code -->
        <script>
        am4core.ready(function() {
        
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end
        
        // Create chart instance
        var chart = am4core.create(\"chartRata2Bulan\", am4charts.XYChart);
        chart.scrollbarX = new am4core.Scrollbar();
        
        // Add data
        chart.data = [
        
        ";
            foreach($getBulan as $bln)
            {
                $nilai = getField("select nilai from pen_hasil where id_pegawai = '$par[idPegawai]' and id_periode = '$par[idPeriode]' and id_bulan = '$bln[kodeData]'");
                $nilai = empty($nilai) ? 0 : $nilai;
                $text.="
                {
                  \"country\": \"".$bln[namaData]."\",
                  \"visits\": $nilai
                },
                ";
            }
            $text.="
       
        ];
        
        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = \"country\";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;
        categoryAxis.renderer.labels.template.horizontalCenter = \"right\";
        categoryAxis.renderer.labels.template.verticalCenter = \"middle\";
        categoryAxis.renderer.labels.template.rotation = 270;
        categoryAxis.tooltip.disabled = true;
        categoryAxis.renderer.minHeight = 110;
        
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.minWidth = 50;
        
        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.sequencedInterpolation = true;
        series.dataFields.valueY = \"visits\";
        series.dataFields.categoryX = \"country\";
        series.tooltipText = \"[{categoryX}: bold]{valueY}[/]\";
        series.columns.template.strokeWidth = 0;
        
        series.tooltip.pointerOrientation = \"vertical\";
        
        series.columns.template.column.cornerRadiusTopLeft = 10;
        series.columns.template.column.cornerRadiusTopRight = 10;
        series.columns.template.column.fillOpacity = 0.8;
        
        // on hover, make corner radiuses bigger
        var hoverState = series.columns.template.column.states.create(\"hover\");
        hoverState.properties.cornerRadiusTopLeft = 0;
        hoverState.properties.cornerRadiusTopRight = 0;
        hoverState.properties.fillOpacity = 1;
        
        series.columns.template.adapter.add(\"fill\", function(fill, target) {
          return chart.colors.getIndex(target.dataItem.index);
        });
        
        // Cursor
        chart.cursor = new am4charts.XYCursor();
        
        }); // end am4core.ready()
        </script>
        
        <fieldset>
            <div id=\"chartRata2Bulan\"></div>	
        </fieldset>
        <br />
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai rata-rata Tahunan</h3></div>
    	</div>
        
        <!-- Styles -->
        <style>
        #chartRata2Tahun {
          width: 100%;
          height: 500px;
        }
        </style>
        
        
        <!-- Chart code -->
        <script>
        am4core.ready(function() {
        
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end
        
        // Create chart instance
        var chart = am4core.create(\"chartRata2Tahun\", am4charts.XYChart);
        chart.scrollbarX = new am4core.Scrollbar();
        
        // Add data
        chart.data = [
         ";
            foreach($get10thn as $thn)
            {
                $nilai = getField("select avg(nilai) from pen_hasil where id_pegawai = '$par[idPegawai]' and id_periode = '$thn[kodeData]'");
                $nilai = empty($nilai) ? 0 : $nilai;
                $text.="
                {
                  \"country\": \"".$thn[namaData]."\",
                  \"visits\": $nilai
                },
                ";
            }
            $text.="
        ];
        
        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = \"country\";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;
        categoryAxis.renderer.labels.template.horizontalCenter = \"right\";
        categoryAxis.renderer.labels.template.verticalCenter = \"middle\";
        categoryAxis.renderer.labels.template.rotation = 270;
        categoryAxis.tooltip.disabled = true;
        categoryAxis.renderer.minHeight = 110;
        
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.minWidth = 50;
        
        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.sequencedInterpolation = true;
        series.dataFields.valueY = \"visits\";
        series.dataFields.categoryX = \"country\";
        series.tooltipText = \"[{categoryX}: bold]{valueY}[/]\";
        series.columns.template.strokeWidth = 0;
        
        series.tooltip.pointerOrientation = \"vertical\";
        
        series.columns.template.column.cornerRadiusTopLeft = 10;
        series.columns.template.column.cornerRadiusTopRight = 10;
        series.columns.template.column.fillOpacity = 0.8;
        
        // on hover, make corner radiuses bigger
        var hoverState = series.columns.template.column.states.create(\"hover\");
        hoverState.properties.cornerRadiusTopLeft = 0;
        hoverState.properties.cornerRadiusTopRight = 0;
        hoverState.properties.fillOpacity = 1;
        
        series.columns.template.adapter.add(\"fill\", function(fill, target) {
          return chart.colors.getIndex(target.dataItem.index);
        });
        
        // Cursor
        chart.cursor = new am4charts.XYCursor();
        
        }); // end am4core.ready()
        </script>
        
        <fieldset>
            <div id=\"chartRata2Tahun\"></div>	
        </fieldset>
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
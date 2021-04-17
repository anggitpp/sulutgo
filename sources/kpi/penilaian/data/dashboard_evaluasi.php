<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;							
    
    $par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];
    $par[bulanPenilaian] = !getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' and kodeData = '$par[bulanPenilaian]' order by kodeData asc limit 1") ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc limit 1") : $par[bulanPenilaian];
    $getBulan = queryAssoc("SELECT * FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk = $par[idPeriode]");
    $get10thn = queryAssoc("select * from ( SELECT * FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData desc limit 10) as x order by kodeData asc");
    
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
                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('form').submit();\"", "200px") ."
    		</div>				
        
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai rata-rata</h3></div>
    	</div>
        
        <!-- Styles -->
        <style>
        #chartRata2 {
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
        var chart = am4core.create(\"chartRata2\", am4charts.XYChart);
        chart.scrollbarX = new am4core.Scrollbar();
        
        // Add data
        chart.data = [
        ";
            $getData = queryAssoc("SELECT * FROM pen_tipe WHERE statusTipe = 't'");
            foreach($getData as $pen)
            {
                $nilai = getField("SELECT AVG(nilai) FROM pen_hasil WHERE id_periode = $par[idPeriode] AND id_pegawai IN (SELECT idPegawai FROM pen_pegawai WHERE tipePenilaian = $pen[kodeTipe] AND tahunPenilaian = $par[idPeriode])");
                $nilai = empty($nilai) ? 0 : $nilai;
                $text.="
                {
                  \"country\": \"".substr($pen[namaTipe],0,23)."\",
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
            <div id=\"chartRata2\"></div>	
        </fieldset>
        <br />
        
        
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
                $nilai = getField("SELECT AVG(nilai) FROM pen_hasil WHERE id_periode = $par[idPeriode] and id_bulan = $bln[kodeData]");
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
        
        <!-- 
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Nilai rata-rata Tahunan</h3></div>
    	</div>
        
        <style>
        #chartRata2Tahun {
          width: 100%;
          height: 500px;
        }
        </style>
        
        
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
                $text.="
                {
                  \"country\": \"".$thn[namaData]."\",
                  \"visits\": 1882
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
        
        
        <br />
        
        -->
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\">
                <div style=\"float:left; margin-top:20px;\"><h3>Top 10 Evaluasi</h3></div>
                <div style=\"float:right; margin-right:-20px; \">".comboData("SELECT namaData,kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[idPeriode]' order by kodeData asc","kodeData","namaData","par[bulanPenilaian]","",$par['bulanPenilaian'],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."</div>
            </div>
    	</div>
        
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
    		<thead>
    			<tr>
    				<th width=\"20\">NO</th>
    				<th width=\"300\">Nama</th>
                    <th width=\"\">Divisi</th>
                    <th width=\"\">unit</th>
                    <th width=\"70\">wom</th>
                    <th width=\"70\">nilai</th>
    			</tr>
    		</thead>
            <tbody>
            ";
            $getPenilaian = queryAssoc("SELECT * FROM pen_hasil WHERE id_periode =' $par[idPeriode]' and id_bulan =' $par[bulanPenilaian]' ORDER BY nilai DESC LIMIT 10");
            $no=0;
            foreach($getPenilaian as $pen)
            {
                $getDiv = queryAssoc("select * from pen_pegawai where idPegawai = '$pen[id_pegawai]' and tahunPenilaian = '$par[idPeriode]' limit 1");
                $div = $getDiv[0];
                $wom = getWom($pen[nilai], $par[idPeriode]);
                $no++;
                $text.="
                <tr>
                    <td style=\"text-align:center;\">$no</td>
                    <td style=\"text-align:left;\">".getField("select name from emp where id = $pen[id_pegawai]")."</td>
                    <td style=\"text-align:left;\">".getField("select namaTipe from pen_tipe where kodeTipe = $div[tipePenilaian]")."</td>
                    <td style=\"text-align:left;\">".getField("select subKode from pen_setting_kode where idKode = $div[kodePenilaian]")."</td>
                    <td style=\"text-align:center;\"><div style=\"background-color:$wom[warna];\">&nbsp;&nbsp;&nbsp;&nbsp; <strong>$wom[uraian]</strong> &nbsp;&nbsp;&nbsp;&nbsp;</div></td>
                    <td style=\"text-align:center;\">$pen[nilai]</td>
    			</tr>
                ";
            }
            $text.="
            </tbody>
        </table>
        
        </form>	
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
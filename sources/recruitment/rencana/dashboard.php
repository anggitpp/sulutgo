<?php
function lihat() {
	global $s, $inp, $par, $arrTitle, $db, $arrParameter, $menuAccess, $brandName,$db;
	if(empty($par[tahun])){
		$par[tahun] = date("Y");
	}

	$jumlah_wajib = "12";
	$tahun_awal = getField("SELECT year(propose_date) from rec_plan order by propose_date asc limit 1");
	$tahun_akhir = date("Y");

	$sqlP = db("select id_posisi, subject from rec_job_posisi order by subject");
	$text="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p style=\"position: absolute; right: 20px; top:10px;\">
					".
					comboYear("par[tahun]", $par[tahun], "", "onchange=\"document.getElementById('form').submit();\"","100px","","$tahun_awal","$tahun_akhir")."
				</p>
			</div>	
			<div id=\"pos_r\">

			</div>
		</form>	
		<table style=\"width:100%\">
			<tr>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box nephritis\">

						<div class=\"dashboard-box-content\">
							";
							$total_kebutuhan = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($total_kebutuhan)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">TOTAL KEBUTUHAN</p>
						</div>
					</div>
				</td>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							";
							$usulan = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and cat = '1'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($usulan)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">USULAN</p>
						</div>
					</div>
				</td>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							";
							$rencana = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and cat = '2'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($rencana)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">RENCANA</p>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<table style=\"width:100%\">
			<tr>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box goldenrod\">

						<div class=\"dashboard-box-content\">
							";
							$terpenuhi = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and status = 't'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($terpenuhi)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">TERPENUHI</p>
						</div>
					</div>
				</td>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							";
							$pending = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and status != 't'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($pending)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">PENDING</p>
						</div>
					</div>
				</td>
				<td style=\"width: 33%\">
					<div class=\"dashboard-box goldenrod\">
						<div class=\"dashboard-box-content\">
							";
							$tolak = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and status = 'f'");
							$text.="
							<p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka($tolak)."</font></p>
						</div>
						<div class=\"dashboard-box-title-content\">
							<p class=\"dashboard-box-title\">TOLAK</p>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<div class=\"widgetbox\">
			<div class=\"title\" style=\"margin-bottom:0px;\"><h3 style=\"float:left; width:50%;text-align:left;\"><a href=\"\" title=\"Lihat tabel kategori\">RENCANA</a></h3>
			</div>
		</div>


		<style>
	#chartdiv5 {
			width: 100%;
			height: 500px;
		}
	</style>


	<script type=\"text/javascript\">
		var chart5= AmCharts.makeChart(\"chartdiv5\",
		{
			\"type\": \"serial\",
			\"categoryField\": \"category\",
			\"startDuration\": 1,
			\"colors\": [
			\"#0D8ECF\",
			\"#FF6600\",
			\"#2A0CD0\",
			\"#CD0D74\",
			\"#CC0000\",
			\"#00CC00\",
			\"#0000CC\",
			\"#DDDDDD\",
			\"#999999\",
			\"#333333\",
			\"#990000\"
			],
			\"chartCursor\": {
				\"enabled\": true
			},
			\"categoryAxis\": {
				\"gridPosition\": \"start\"
			},
			\"trendLines\": [],
			\"graphs\": [
			{
				\"balloonText\": \"[[title]] of [[category]]:[[value]]\",
				\"fillAlphas\": 1,
				\"id\": \"AmGraph-1\",
				\"title\": \"USULAN\",
				\"type\": \"column\",
				\"valueField\": \"column-1\"
			},
			{
				\"balloonText\": \"[[title]] of [[category]]:[[value]]\",
				\"fillAlphas\": 1,
				\"id\": \"AmGraph-2\",
				\"title\": \"RENCANA\",
				\"type\": \"column\",
				\"valueField\": \"column-2\"
			}
			],
			\"guides\": [],
			\"valueAxes\": [
			{
				\"id\": \"ValueAxis-1\",
				\"stackType\": \"regular\",
				\"title\": \"\"
			}
			],
			\"allLabels\": [],
			\"balloon\": {},
			\"legend\": {
				\"enabled\": true,
				\"useGraphSettings\": true
			},
			\"export\": {
				\"enabled\": true
			},
			\"dataProvider\": [";
			for($i=1; $i<=12; $i++) {
				$bulan = substr(getBulan($i),0,3);
				$usulan = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and cat = '1' and month(propose_date) = $i");
				$rencana = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and cat = '2' and month(propose_date) = $i");
				$text.="
				{
					\"category\": \"$bulan\",
					\"column-1\": $usulan,
					\"column-2\": $rencana

				},";
			}
			$text.="

			]
		}
		);
	</script>

	<!-- HTML -->
	<div id=\"chartdiv5\"></div>

	<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3 style=\"float:left; width:50%;text-align:left;\"><a href=\"\" title=\"Lihat tabel kategori\">TERPENUHI</a></h3>
		</div>
	</div>


	<style>
	#chartdiv6 {
		width: 100%;
		height: 500px;
	}
</style>


<script type=\"text/javascript\">
	var chart5= AmCharts.makeChart(\"chartdiv6\",
	{
		\"type\": \"serial\",
		\"categoryField\": \"category\",
		\"startDuration\": 1,
		\"colors\": [
		\"#0D8ECF\",
		\"#FF6600\",
		\"#2A0CD0\",
		\"#CD0D74\",
		\"#CC0000\",
		\"#00CC00\",
		\"#0000CC\",
		\"#DDDDDD\",
		\"#999999\",
		\"#333333\",
		\"#990000\"
		],
		\"chartCursor\": {
			\"enabled\": true
		},
		\"categoryAxis\": {
			\"gridPosition\": \"start\"
		},
		\"trendLines\": [],
		\"graphs\": [
		{
			\"balloonText\": \"[[title]] of [[category]]:[[value]]\",
			\"fillAlphas\": 1,
			\"id\": \"AmGraph-1\",
			\"title\": \"TOTAL KEBUTUHAN TERPENUHI\",
			\"type\": \"column\",
			\"valueField\": \"column-1\"
		},
		],
		\"guides\": [],
		\"valueAxes\": [
		{
			\"id\": \"ValueAxis-1\",
			\"stackType\": \"regular\",
			\"title\": \"\"
		}
		],
		\"allLabels\": [],
		\"balloon\": {},
		\"legend\": {
			\"enabled\": true,
			\"useGraphSettings\": true
		},
		\"export\": {
			\"enabled\": true
		},
		\"dataProvider\": [";
		for($i=1; $i<=12; $i++) {
			$bulan = substr(getBulan($i),0,3);
			$terpenuhi = getField("select count(id) from rec_plan where year(propose_date) = '$par[tahun]' and status = 't' and month(propose_date) = $i");
			$text.="
			{
				\"category\": \"$bulan\",
				\"column-1\": $terpenuhi

			},";
		}
		$text.="

		]
	}
	);
</script>

<!-- HTML -->
<div id=\"chartdiv6\"></div>

<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3 style=\"float:left; width:50%;text-align:left;\"><a href=\"\" title=\"Lihat tabel kategori\">POSISI</a></h3>
	</div>
</div>
<!--<div id=\"divStatus\" align=\"center\" onclick=\"openBox('popup.php?par[mode]=detPegawai".getPar($par,"mode")."',1075,450);\"></div>-->
<div id=\"divStatus\" align=\"center\"></div>
<script type=\"text/javascript\">
	var statusChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Orang\">";

	while($r = mysql_fetch_assoc($sqlP)){			
		$r[jumlah] = getField("SELECT id_posisi from rec_plan WHERE id_posisi = $r[id_posisi]");	
		$text.="<set label=\"".$r[subject]."\" value=\"".$r[jumlah]."\"/> ";		
	}

	$text.="</chart>';
	var chart = new FusionCharts(\"Bar2D\", \"chartStatus\", \"100%\", 200);
	chart.setXMLData( statusChart );
	chart.render(\"divStatus\");
</script>
</div>";
return $text;
}

function getContent($par) {
	global $s, $_submit, $menuAccess, $fFile, $cUsername;
	switch ($par[mode]) {
		default:
		$text = lihat();
		break;
	}
	return $text;
}
?>


<?php
global $s, $par, $menuAccess, $arrTitle,$cGroup;

$maxday = date("t", strtotime($tanggal));
if(empty($par[bulanProses])) $par[bulanProses] = date('m');
if(empty($par[tahunProses])) $par[tahunProses] = date('Y');		

$no = 1;

$filter = "";
if(!empty($par[bulanProses]))
	$filter.= " AND MONTH(t1.create_time)='$par[bulanProses]'";

if(!empty($par[tahunProses]))
	$filter.= " AND YEAR(t1.create_time)='$par[tahunProses]'";

// $totalp = getField("SELECT count(*) AS count FROM dta_pegawai $filter");

// $jmlUmur["&gt;50"]=0;		
// $jmlUmur["41-50"]=0;
// $jmlUmur["31-40"]=0;
// $jmlUmur["&lt;31"]=0;

// $arrGender["m"]=0;		
// $arrGender["f"]=0;

$sql="SELECT * from app_menu where kodeInduk='362'";
$res=db($sql);
while($r=mysql_fetch_array($res)){
	// if($r[statusPegawai] == "t"){
	// 	list($tahunLahir, $bulanLahir) = explode("-", $r[tanggalLahir]);
	// 	$usiaPegawai = selisihTahun($tahunLahir."-".$bulanLahir."-01 00:00:00", $par[tahunData]."-".$par[bulanData]."-01 00:00:00");

		// if($usiaPegawai < 31)
		// 	$jmlUmur["&lt;31"]++;
		// else if($usiaPegawai <= 41)
		// 	$jmlUmur["31-40"]++;
		// else if($usiaPegawai <= 51)
		// 	$jmlUmur["41-50"]++;
		// else
		// 	$jmlUmur["&gt;50"]++;

		// $arrGender["$r[genderPegawai]"]++;

	$arrAngkaPegawai["$r[parameterMenu]"]++;

	// }
}

$arrbox = array("1"=> "goldenrod","2"=> " murky-green","3"=> "allports","4"=> "chocolate","5"=> "dark-orchid","6"=> "camarone","7"=> "light-blue","8"=> "orange","9"=> "pink","10"=> "sky-blue");

// .dark-orchid{ background-color: #9A00AD; }
// .camarone{ background-color: #297140; }
// .goldenrod{ background-color: #E1A300; }
// .citrus{ background-color: #9CB500; }
// .caper{ background-color: #C2D69B; }
// .moon-raker{ background-color: #B2A1C7; }
// .allports{ background-color: #31849B; }
// .chocolate{ background-color: #E36C0A; }
// .purple-orchid{ background-color: #CBC0D9; }
// .light-blue { background-color: #B7DDE7; }
// .murky-green { background-color: #D6E3BC; }
// .orange{ background-color: #FBD4B4; }
// .purple-orchid{ background-color: #E5B9FF; }
// .purple-orchid2{ background-color: #ECE8FF; }
// .light-blue { background-color: #3DD2F9; }
// .murky-green { background-color: #9BCA3E; }
// .pale-green { background-color: #EAFFF1; }
// .sky-blue{ background-color: #E8F2FF; }
// .orange { background-color: #FF9945; }
// .pink { background-color: #FFB9B7;}
// .pink2 { background-color: #FFEDF5; };

$jumlahArrayPegawai = count($arrAngkaPegawai) + 1;
$jumlahArrayPegawai = 100/$jumlahArrayPegawai;
$jumlahArrayPegawai = str_replace(",", ".", $jumlahArrayPegawai);
$jumlahArrayPegawai = substr($jumlahArrayPegawai, 0,5);
// var_dump($jumlahArrayPegawai);


$no = 1;
?>
<style type="text/css">
	h3{
		margin-bottom:-4px;
	}
	.scrollsIMG {
		overflow-x: scroll;
		overflow-y: hidden;
		height: auto;
		padding-bottom: 20px;
		white-space:nowrap
	}
</style>
<div class="pageheader">
	<h1 class="pagetitle">Dashboard</h1>
	<?= getBread() ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<form id="form" name="form" action="" method="post" class="stdform">
	<p style="position: absolute; right: 20px; top: 4px;">
		<?=  comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")."&nbsp;".comboYear("par[tahunProses]", $par[tahunProses], "5", "onchange=\"document.getElementById('form').submit();\"")."&nbsp;"; ?>
	</p>
</form>
<div id="contentwrapper" class="contentwrapper">
	<div class="widgetbox" style="position: relative;">

		<div style="margin-bottom:-10px;"><h3>JUMLAH AKTIFITAS</h3></div>

		<div style="position: absolute; bottom: 0px; right: 0px;"><h5></h5></div>

	</div>
	<hr style="margin-bottom: 15px;">
	<table style="width:100%">
		<tr style="width:100%">
			

			<?php

			foreach ($arrAngkaPegawai as $parameterMenu => $angka) {

				$nilai         = getField("SELECT count(*) FROM `aktifitas_setting` t1 join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t2.layanan='$parameterMenu' $filter");


				$parameterMenu = getField("SELECT namaData from mst_data where kodeMaster = '$parameterMenu'");
				// echo $parameterMenu;
				?>
				<td style="width: 20%;">
					<div class="dashboard-box <?= $arrbox[$no] ?>" style="padding-bottom: 10px;margin-bottom: 10px">
						<div class="dashboard-box-header">
							<p class="dashboard-box-title"><?= $parameterMenu  ?></p>
						</div>
						<div class="dashboard-box-content">
							<p class="dashboard-box-number"><?= getAngka($nilai)  ?></p>
						</div>
					</div>
				</td>
				<?php

				if(++$no%5 == 0) 
					echo "
			</tr>
			<tr style=\"width:100%\">
				";
			}

			?>
		</tr>
	</table>
	<br />
	<div class="widgetbox" style="position: relative;">

		<div style="margin-bottom:-10px;"><h3>JUMLAH OBYEK</h3></div>

		<div style="position: absolute; bottom: 0px; right: 0px;"><h5></h5></div>

	</div>
	<hr style="margin-bottom: 15px;">
	<table style="width:100%">
		<tr style="width:100%">
			<?php

			foreach ($arrAngkaPegawai as $parameterMenu => $angka) {

				$nilai         = getField("SELECT count(*) FROM `area_ruang` t1 where t1.area ='' and t1.layanan='$parameterMenu' $filter");

				$parameterMenu = getField("SELECT namaData from mst_data where kodeMaster = '$parameterMenu'");
				// echo $parameterMenu;
				?>
				<td style="width: 20%;">
					<div class="dashboard-box <?= $arrbox[$no] ?>" style="padding-bottom: 10px;margin-bottom: 10px">
						<div class="dashboard-box-header">
							<p class="dashboard-box-title"><?= $parameterMenu;  ?></p>
						</div>
						<div class="dashboard-box-content">
							<p class="dashboard-box-number"><?= getAngka($nilai); ?></p>
						</div>
					</div>
				</td>
				<?php

				if(++$no%5 == 0) 
					echo "
			</tr>
			<tr style=\"width:100%\">
				";
			}

			?>
		</tr>
	</table>
	<br />
	<?php
echo "

<div class=\"widgetbox\" style=\"position: relative;\">

	<div style=\"margin-bottom:-10px;\"><h3>AKTIFITAS</h3></div>

	<div style=\"position: absolute; bottom: 0px; right: 0px;\"><h5></h5></div>

</div>

<hr style=\"margin-bottom: 15px;\">
<table width=\"100%\">
	<tr>
		<td width=\"100%\">
		<ul class=\"hornav\" style=\"margin-top:20px;\">
		";
		$urutan=0;
			foreach ($arrAngkaPegawai as $parameterMenu => $angka) {
			$parameterMenu = getField("SELECT namaData from mst_data where kodeMaster = '$parameterMenu'");
			if($urutan == 0){$active="current";}
			else{$active='';}
			$urutan++;
			echo "
				<li class=\"".$active."\"><a href=\"#tab_".$parameterMenu."\">".$parameterMenu."</a></li>
			";
		}
		echo "
		</ul>";
		$no=2;
			foreach ($arrAngkaPegawai as $parameterMenu => $angka) {
			if($no == 2){$active="block";}
			else{$active='none';}
			// $no++;
				$getparameter  = $parameterMenu;
				$getkodeInduk  = getField("SELECT kodeData from mst_data where kodeMaster = '$getparameter'");
				$parameterMenu = getField("SELECT namaData from mst_data where kodeMaster = '$parameterMenu'");
				$no++;
				$kode=$parameterMenu;
		echo "
		<div class=\"subcontent\" id=\"tab_".$parameterMenu."\" style=\"border-radius:0; display: ".$active."; padding:20px; padding-top:30px;\">
			<style>
					#chartdiv".$no." {
				width	: 100%;
				height	: 400px;
			}										
		</style>
		<script type=\"text/javascript\">
			var chart2 = AmCharts.makeChart(\"chartdiv".$no."\",
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
							\"balloonText\": \"[[title]] of [[category]]:[[value]]\",
							\"fillAlphas\": 1,
							\"id\": \"AmGraph-1\",
							\"title\": \"AKTIFITAS\",
							\"type\": \"column\",
							\"valueField\": \"column-1\"
						}
					],
					\"guides\": [],
					\"valueAxes\": [
						{
							\"id\": \"ValueAxis-1\",
							\"title\": \"AKTIFITAS\"
						}
					],
					\"allLabels\": [],
					\"balloon\": {},
					\"legend\": {
						\"enabled\": true,
						\"useGraphSettings\": true
					},
					
					\"dataProvider\": [
						";
						$sql="SELECT * from mst_data  where kodeCategory='OBY' order by namaData DESC";
						$res=db($sql);
						while ($r=mysql_fetch_array($res)) {
						
						$getvalue =getField("SELECT count(t1.id_setting) FROM `mst_aktifitas` t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) where t2.namaData = '$r[namaData]' and t2.kodeInduk='$getkodeInduk' ");
						echo "
						{
							\"category\": \"".$r[namaData]."\",
							\"column-1\": \"".$getvalue."\"
							
						},
						";
						}
						echo "
					
					]
				}
			);
</script>
<div id=\"chartdiv".$no."\"></div>
</div>
";
}
echo "

</td>
</tr>

</table>
<br />
";

// $array_model = array("Checklist", "Parameter", "Isian");
?>
<div class="widgetbox" style="position: relative;">

	<div style="margin-bottom:-10px;"><h3>KATEGORI MODEL</h3></div>

	<div style="position: absolute; bottom: 0px; right: 0px;"><h5></h5></div>

</div>
<hr style="margin-bottom: 15px;">
<table style="width:100%">
	<tr style="width:100%">

		<td style="width: 33.33%;">
			<div class="dashboard-box goldenrod" style="padding-bottom: 10px;margin-bottom: 10px">
				<div class="dashboard-box-header">
					<p class="dashboard-box-title">CEKLIST</p>
				</div>
				<div class="dashboard-box-content">
					<p class="dashboard-box-number"><?= getAngka(getField("SELECT count(*) FROM `aktifitas_setting`  t1 where t1.model='0' $filter")) ; ?></p>
				</div>
			</div>
		</td>
		<td style="width: 33.33%;">
			<div class="dashboard-box goldenrod" style="padding-bottom: 10px;margin-bottom: 10px">
				<div class="dashboard-box-header">
					<p class="dashboard-box-title">ISIAN</p>
				</div>
				<div class="dashboard-box-content">
					<p class="dashboard-box-number"><?= getAngka(getField("SELECT count(*) FROM `aktifitas_setting` t1 where t1.model='2' $filter") ); ?></p>
				</div>
			</div>
		</td>
		<td style="width: 33.33%;">
			<div class="dashboard-box goldenrod" style="padding-bottom: 10px;margin-bottom: 10px">
				<div class="dashboard-box-header">
					<p class="dashboard-box-title">PARAMETER</p>
				</div>
				<div class="dashboard-box-content">
					<p class="dashboard-box-number"><?= getAngka(getField("SELECT count(*) FROM `aktifitas_setting` t1 where t1.model='1' $filter") ); ?></p>
				</div>
			</div>
		</td>
		
	</tr>
</table>
<br />
<?php
echo "
<table style=\"width:100%; margin-top:20px; margin-bottom:10px; margin-left:-15px;\">
	<tr>
		<div class=\"widgetbox\">
			<div class=\"title\" style=\"margin-bottom:0px;\"><h3>KONTROL MODEL</h3></div>
		</div>
		";

		$no=1;
		foreach ($arrAngkaPegawai as $parameterMenu => $angka) {

			$getparameter  = $parameterMenu;
			$parameterMenu = getField("SELECT namaData from mst_data where kodeMaster = '$parameterMenu'");
			// echo $parameterMenu;

			echo "

			



				<td style=\"width:50%; vertical-align:top; padding-left:15px; padding-right:15px;\">
					<div style=\"border: 1px solid #CCC; margin: 4px 4px 4px 0px; align-item: center;\">
					<div style=\"padding: 8px; border-bottom: 1px solid #ccc; text-align: center;\">

					".strtoupper($parameterMenu)."

					</div>
					<div id=\"$parameterMenu\" align=\"center\" ></div>
					<script type=\"text/javascript\">
						var ".$parameterMenu."1"." ='<chart showLabels=\"0\" showValues=\"0\" showLegend=\"1\"  chartrightmargin=\"40\" bgcolor=\"F7F7F7,E9E9E9\" bgalpha=\"70\" bordercolor=\"888888\" basefontcolor=\"2F2F2F\" basefontsize=\"11\" showpercentvalues=\"1\" bgratio=\"0\" startingangle=\"200\" animation=\"1\" >";




				// $sql="SELECT * FROM `mst_data` WHERE  `kodeCategory` = 'WILAYAH' AND `statusData` = 't' ORDER BY `urutanData`";
				// $res=db($sql);
				// while ($r=mysql_fetch_array($res)) {
					// $getJumlah=getField("SELECT count(*) from data_komplain where lokasi='$r[kodeData]'");
						echo "<set value=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='2' and t2.layanan='$getparameter' $filter")."\" label=\"ISIAN\" showValue=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='2' and t2.layanan='$getparameter' $filter")."\" />";
						echo "<set value=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='0' and t2.layanan='$getparameter' $filter")."\" label=\"CEKLIST\" showValue=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='0' and t2.layanan='$getparameter' $filter")."\" />";
						echo "<set value=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='1' and t2.layanan='$getparameter' $filter")."\" label=\"PARAMETER\" showValue=\"".getField("SELECT count(*) FROM `aktifitas_setting` t1  join area_ruang t2 on(t1.id_ruang=t2.id_ruang) where t1.model='1' and t2.layanan='$getparameter' $filter")."\" />";
				// }		

						echo "</chart>';
						var chart = new FusionCharts(\"Pie2D\", \"chart\", \"100%\", 350);
						chart.setXMLData( ".$parameterMenu."1"." );
						chart.render(\"$parameterMenu\");
					</script>
				</div>
			</td>

			
			";


			if(++$no%3 == 0) 
				echo "
		</tr>
		<tr style=\"width:100%\">
			";
		}


		echo "
		
	</tr>
</table>
<br />
";

?>






</div>

<?php  
global $cUsername;
	global $s, $inp, $par, $arrTitle, $_submit,$db, $arrParameter, $menuAccess, $brandName;

if(empty($par[tahun]))
{
	$par[tahun] = date('Y');
}
if(empty($par[bulan]))
{
	$par[bulan] = date('m');
}

	echo "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<input type=\"hidden\" name=\"_submit\" value=\"t\">
			<div style=\"position:absolute; top:0; right:0; margin-right:20px; margin-top:10px;\">".comboYear('par[tahun]', $par[tahun], '', "onchange=\"document.getElementById('form').submit();\"", '', 'All')."</div>
			<input type=\"hidden\" name=\"_submit\" value=\"b\">
			<div style=\"position:absolute; top:0; right:0; margin-right:85px; margin-top:10px;\">".comboMonth('par[bulan]', $par[bulan], "onchange=\"document.getElementById('form').submit();\"", '', 'All')."</div>
		</form>
	</form>";
	$total = getField("SELECT COUNT(*) FROM app_user where jenisUser = '1'"); //userakses
echo"
	<table style=\"width:100%;\">
		<tr>
			<td style=\"width: 20%;\">
				<div class=\"bintang-box2 light-blue\" style=\"height:180px;\">
					<div class=\"bintang-box2-header\">
						<p class=\"bintang-box2-title\">TOTAL USER</p>
					</div>
					<div class=\"bintang-box2-content\">
						<p class=\"bintang-box2-number\">$total</p>
					</div>
				</div>
			</td>
			<td style=\"width: 80%;\">
				<table style=\"margin-left:5px; width:100%;\">
					<tr>
						<td colspan=5; style=\"width:100%; height:20%;\">
							<div style=\"width:100%; margin-top:-30px; border-bottom: 2px solid black;\">
								<h4>LAST LOGIN</h4>
							</div>
						</td>
					</tr>
					<tr>";
					$sql  = "SELECT namaUser, loginUser,fotoUser FROM app_user WHERE jenisUser = '1' ORDER BY loginUser DESC LIMIT 5 ";
					$result = mysql_query($sql);
					while($top = mysql_fetch_array($result))
					{

					if(empty($top[fotoUser])) $foto = "images/user/12aee6a103afc2f182ce41fd5dc21b41.png"; else $foto = "images/user/$top[fotoUser]";
					echo"
						<td style=\"\">
							<div style=\"margin-left:25px;\">
							<img width=\"75\" style=\"border-radius:50px; margin-left:20px;\" src=\"$foto\"><br>
							<span style=\"font-size:12px; font-weight:bold; color:#444\">$top[namaUser]</span><br>
							
							<span style=\"font-size:11px; color:#999;\">".$top[loginUser]."</span>
							</div>
						</td>";

					}
					echo "
					</tr>
				</table>
			</td>
		</tr>
	</table>";


echo "
	<div class=\"widgetbox\">
			<div class=\"title\" style=\"margin-bottom:0px;\"><h3>GROUP USER</h3></div>
		</div>

		<div id=\"group\" align=\"center\"></div>
		<script type=\"text/javascript\">
			var pangkatChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\" yAxisName=\"Jumlah\">";

			$sql  = "SELECT kodeGroup, count(username) as jumlah FROM app_user where jenisUser = '1' GROUP BY kodeGroup ORDER BY jumlah DESC";
			$result = mysql_query($sql);
			while($top = mysql_fetch_array($result))
			{
				$getLabel = getField("SELECT namaGroup FROM app_group where kodeGroup ='$top[kodeGroup]'");
				echo "<set label=\"".$getLabel."\" value=\"".$top['jumlah']."\" toolText=\"\"/>";
			}

			echo "</chart>';
			var chart = new FusionCharts(\"Column3D\", \"chartPangkat\", \"100%\", 250);
			chart.setXMLData( pangkatChart );
			chart.render(\"group\");
		</script>";

		$open = getfield("SELECT count(*) FROM log_access t1 join app_user t2 on t1.createBy = t2.username where t2.jenisUser = '1' AND t1.aktivitasLog = 'open page' AND year(t1.createTime) = '$par[tahun]' AND month(t1.createTime) = '$par[bulan]' ");
		$view = getfield("SELECT count(*) FROM log_access t1 join app_user t2 on t1.createBy = t2.username where t2.jenisUser = '1' AND t1.aktivitasLog = 'view detail' AND year(t1.createTime) = '$par[tahun]' AND month(t1.createTime) = '$par[bulan]' ");
		$input = getfield("SELECT count(*) FROM log_access t1 join app_user t2 on t1.createBy = t2.username where t2.jenisUser = '1' AND t1.aktivitasLog = 'input data' AND year(t1.createTime) = '$par[tahun]' AND month(t1.createTime) = '$par[bulan]' ");
		$edit = getfield("SELECT count(*) FROM log_access t1 join app_user t2 on t1.createBy = t2.username where t2.jenisUser = '1' AND t1.aktivitasLog = 'edit data' AND year(t1.createTime) = '$par[tahun]' AND month(t1.createTime) = '$par[bulan]' ");
		$delete = getfield("SELECT count(*) FROM log_access t1 join app_user t2 on t1.createBy = t2.username where t2.jenisUser = '1' AND t1.aktivitasLog = 'delete data' AND year(t1.createTime) = '$par[tahun]' AND month(t1.createTime) = '$par[bulan]' ");

		echo"
		<div class=\"widgetbox\">
		<div class=\"title\" style=\"font-size:15px; margin-top:20px;\">
			<strong><h3>LOG AKSES ".getBulan($par[bulan])." ".$par[tahun]."</h3></strong>
		</div>
	</div>
		<table style=\"width:100%; margin-top:-10px;\">
			<tr>
				<td style=\"width: 20%;\">
					<div class=\"bintang-box2 pink\">
						<div class=\"bintang-box2-header\">
							<p class=\"bintang-box2-title\">OPEN</p>
						</div>
						<div class=\"bintang-box2-content\">
							<p class=\"bintang-box2-number\">$open</p>
						</div>
					</div>
				</td>
				<td style=\"width: 20%;\">
					<div class=\"bintang-box2 goldenrod\">
						<div class=\"bintang-box2-header\">
							<p class=\"bintang-box2-title\">VIEW</p>
						</div>
						<div class=\"bintang-box2-content\">
							<p class=\"bintang-box2-number\">$view</p>
						</div>
					</div>
				</td>
				<td style=\"width: 20%;\">
					<div class=\"bintang-box2 citrus\">
						<div class=\"bintang-box2-header\">
							<p class=\"bintang-box2-title\">INPUT</p>
						</div>
						<div class=\"bintang-box2-content\">
							<p class=\"bintang-box2-number\">$input</p>
						</div>
					</div>
				</td>
				<td style=\"width: 20%;\">
					<div class=\"bintang-box2 light-blue\">
						<div class=\"bintang-box2-header\">
							<p class=\"bintang-box2-title\">EDIT</p>
						</div>
						<div class=\"bintang-box2-content\">
							<p class=\"bintang-box2-number\">$edit</p>
						</div>
					</div>
				</td>
				<td style=\"width: 20%;\">
					<div class=\"bintang-box2 allports\">
						<div class=\"bintang-box2-header\">
							<p class=\"bintang-box2-title\">DELETE</p>
						</div>
						<div class=\"bintang-box2-content\">
							<p class=\"bintang-box2-number\">$delete</p>
						</div>
					</div>
				</td>
			<tr>
		</table>";
?>
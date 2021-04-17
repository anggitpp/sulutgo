<?php
session_start();
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	

$_SESSION["curr_emp_id"] = (isset($_GET["empid"]) ? $_GET["empid"] : $_SESSION["curr_emp_id"] );
if(!empty($par[idPegawai])) $_SESSION["curr_emp_id"] = $par[idPegawai];

if (empty($_SESSION["curr_emp_id"])) {
    echo 
  "<script>
    alert(\"Silakan memilih Pegawai terlebih dahulu...\");
    window.location.href=\"".APP_URL . "/?c=3&p=8&m=79&s=82\";
  </script>";
//  header("Location: " . APP_URL . "/index.php?c=3&p=8&m=79&s=82");
}


function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck,$arrParam;
	$htemp = new Emp();
$htemp->id = $_SESSION["curr_emp_id"];
$htemps = $htemp->getByIdHeader();
foreach ($htemps as $htemp) {
  $htemp = $htemp;
}

$cutil = new Common();
$ui = new UIHelper();
	$par[idPegawai] = $_SESSION["curr_emp_id"];
	if(empty($par[tahunJalan])) $par[tahunJalan]=date('Y');		
	$text.="
	<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."
	<span class=\"pagedesc\">&nbsp;</span>
	</div>";
	$par[kodeData] = $par[kodeData] == 0 ? 1 : $par[kodeData];
		
	$text.="<div id=\"contentwrapper\" class=\"contentwrapper\">
	
	<form action=\"\" method=\"post\" class=\"stdform\" id=\"form\">
	<table style=\"width: 100%;margin-top:10px;\">
    <tr>
      <td rowspan=\"3\" style=\"width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;\">
        <img alt=\"".$htemp["regNo"]."\" width=\"100%\" height=\"100px\" src=\"files/emp/pic/" . ($htemp["picFilename"] == "" ? "nophoto.jpg" : $htemp["picFilename"])."\" class=\"pasphoto\">
      </td>
      <td style=\"width: 40%;vertical-align: top; padding-left: 5px; padding-right: 10px;\">";
        
        $text.= $ui->createPLabelSpanDisplay('Nama ', $htemp['name']);
        $text.= $ui->createPLabelSpanDisplay('NPP', $htemp['regNo']);
        $text.= $ui->createPLabelSpanDisplay('Jabatan', $htemp['jabatan']);
        $text.="
      </td>
      <td style=\"width: 40%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;\">
        <p>&nbsp;<br/></p>";
        $text.= $ui->createPLabelSpanDisplay("Status", $htemp["category"]);
        $text.= $ui->createPLabelSpanDisplay("Unit", $htemp["unit"]);
        $text.="

      </td>
    </tr>    
  </table>
	";
	
	// $text.= include './tmpl/__emp_header__.php';
	$text.="	
	
	</form>
	<br clear=\"all\"/>
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
	<thead>
	<tr>
	<th  width=\"20\">No.</th>
	<th  width=\"*\">Posisi</th>
	<th  width=\"120\">Jabatan</th>
	<th  width=\"120\">Lokasi</th>
	<th  width=\"100\">Tahun</th>
	<th  width=\"100\">Nomor SK</th>
	<th  width=\"50\">Filename</th>
	<th  width=\"50\">Status</th>
	</tr>
	</thead>
	<tbody>";

$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
$sql="select *, year(start_date) as tahunMulai, year(end_date) as tahunSelesai from emp_phist where parent_id = '$par[idPegawai]'";
// echo $sql;
$res=db($sql);
while($r=mysql_fetch_array($res)){			
	$r[tahunSelesai] = empty($r[tahunSelesai]) || $r[tahunSelesai] == "0000" ? "current" : $r[tahunSelesai];
	$r[periode] = $r[tahunMulai]." - ".$r[tahunSelesai];
	$r[status] = $r[status] == 1 ? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";
	$no++;
	$text.="
	<td>$no.</td>
	<td>$r[pos_name]</td>
	<td align=\"left\">".$arrMaster[$r[rank]]."</td>
	<td align=\"left\">".$arrMaster[$r[location]]."</td>
	<td align=\"center\">".$r[periode]."</td>
	<td align=\"center\">".$r[sk_no]."</td>
	<td align=\"left\">-</td>
	<td align=\"center\">$r[status]</td>
	
	";
	// if(isset($menuAccess[$s]["edit"])){
	//  $text.="<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[kodeData]=$r[kodeData]&par[tahunJalan]=$par[tahunJalan]".getPar($par,"mode,kodeData")."',700,300)\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a></td>";
	// }
	$text.="</tr>";				
}	

$text.="</tbody>
</table>

</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";

if($par[mode] == "print") pdf();
if($par[mode] == "print2") pdf2();
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

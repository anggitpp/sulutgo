<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/export/";

$par[dlg] = "true";
switch($par[mode]){
	case "export":
	xls();
	default:
	include "prespektif_view.php";
}
/* End of file prespektif_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/laporan/prespektif/prespektif_index.php */

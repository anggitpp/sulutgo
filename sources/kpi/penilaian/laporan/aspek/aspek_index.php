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
	include "aspek_view.php";
}
/* End of file aspek_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/laporan/aspek/aspek_index.php */

<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$par[dlg] = "true";
switch($par[mode]){
	case "edit":
	include "./sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_edit.php";
	break;

	default:
	include "history_penilaian_view.php";
	break;
}
/* End of file history_penilaian_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/history_penilaian/history_penilaian_index.php */
?>
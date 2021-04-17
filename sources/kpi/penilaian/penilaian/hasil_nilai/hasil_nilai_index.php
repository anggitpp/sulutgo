<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$par[dlg] = "true";
switch($par[mode]){
	case "appr":
	include "dlg_approval.php";
	break;

	case "edit":
	include "./sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_edit.php";
	break;

	default:
	include "hasil_nilai_view.php";
	break;
}
/* End of file penilaian_tahunan_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_index.php */
?>
<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']) && empty($par[dlg]))
	echo "<script>logout();</script>";

switch($par[mode]){
	case "view":
	include "penilaian_tahunan_view.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "penilaian_tahunan_edit.php";
	else
		include "penilaian_tahunan_view.php";
	break;

	case "appr":
	if(isset($menuAccess[$s]['apprlv1']))
		include "dlg_approval.php";
	else
		echo "<script>closeBox();</script>";
	break;

	default:
	include "penilaian_tahunan_list.php";
	break;
}
/* End of file penilaian_tahunan_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_index.php */
?>
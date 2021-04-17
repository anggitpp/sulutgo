<?php
global $s, $par, $menuAccess, $arrTitle, $arrParameter, $cUsername, $json, $migrasi;
if($json==1){
	header("Content-type: application/json");

	$filter = "WHERE t1.id IS NOT NULL";
	if(!empty($par[empType])){
		$empType = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='$par[empType]'");
		$filter .= " AND t1.cat = '$empType'";
	}	

	$sql = "SELECT 
	t1.id, 
	t1.name, t1.reg_no, t1.pos_name, t2.namaData as div_name
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.div_id
	$filter";
	
	// echo $sql;
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){		
		$r[pilihData] = "<div align=\"center\" style=\"width:50px;\"><input type=\"checkbox\" id=\"id_".$r[id]."\" name=\"id_".$r[id]."\" value=\"".$r[id]."\" style=\"margin-left:-75px;\" onclick=\"pilihPegawai(this, '$r[id]');\" /></div>";
		$ret[] = $r;
	}
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}

if($_submit==1){
	$arrPegawai = explode("\t", $chk);
	if(is_array($arrPegawai)){
		while(list($id, $idPegawai)=each($arrPegawai)){
			if(!empty($idPegawai)){
			$nextId = getField("SELECT id FROM pen_pegawai ORDER BY id DESC LIMIT 1")+1;
			$sql = "INSERT INTO pen_pegawai (id, idPegawai, tipePenilaian, groupPenilaian, tanggalPenilaian, createBy, createDate) VALUES ('$nextId', '$idPegawai', '0', '0', '".date("Y-m-d")."', '$cUsername', '".date("Y-m-d H:i:s")."')";
			db($sql);
			}
		}
	}
	echo "<script>closeBox();reloadPage();</script>";
}

?>
<div class="centercontent contentpopup" style="overflow-x: hidden;">
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div id="contentwrapper" class="contentwrapper">								
	<form id="form" name="form" method="post" class="stdform" action="<?= "?_submit=1".getPar($par) ?>" enctype="multipart/form-data">
		<input type="hidden" id="chk" name="chk" value=""/>
	</form>
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th style="max-width:30px" style="vertical-align: middle">NO.</th>
				<th style="vertical-align: middle">NAMA</th>
				<th style="width:100px" style="vertical-align: middle">NPP</th>
				<th style="vertical-align: middle">POSISI</th>
				<th style="vertical-align: middle">UNIT KERJA</th>
				<th style="max-width:50px" style="vertical-align: middle">PILIH</th>				
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
</div>
<script type="text/javascript">
	var afterMigrasi = false;
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource":  "ajax.php?json=1<?= getPar($par); ?>",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "name", "bSortable": true},
			{"mData": "reg_no", "sClass": "alignCenter", "bSortable": true},
			{"mData": "pos_name"},
			{"mData": "div_name"},
			{"mData": "pilihData", "sClass": "alignCenter", "bSortable": false},	
			],
			"aaSorting": [[0, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				return nRow;
			},
			
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px").css("margin-bottom", "20px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboData("SELECT parameterMenu, namaMenu FROM app_menu WHERE kodeInduk = '79' AND statusMenu='t' and parameterMenu!=''", "parameterMenu", "namaMenu", "par[empType]", "All", $par[empType], "", "110px"); ?>');
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		
		jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#pilih\" id=\"btnPilih\" class=\"btn btn1 btn_document\"><span>Pilih Data</span></a>");
		
		jQuery("#btnPilih").live("click", function (e) {
				document.getElementById('form').submit();
		});
		
		jQuery("#par\\[empType\\], #par\\[tipePenilaian\\]").live("change", function (e) {
			e.preventDefault();
			ot.fnReloadAjax("ajax.php?json=1<?= getPar($par); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val() + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());			
		});

		jQuery("#datatable_wrapper .top").append("</div>");		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
	
	function pilihPegawai(obj, id){	
		dta = document.getElementById("chk");	
		arr = dta.value.split("\t");
		
		data = "";
		for(i=0; i< arr.length; i++){
			if(arr[i] != id && arr[i].length > 0) data=data + arr[i] + "\t";
		}
		if(obj.checked == true) data=data + id + "\t";	
		
		dta.value = data;
	}
</script>
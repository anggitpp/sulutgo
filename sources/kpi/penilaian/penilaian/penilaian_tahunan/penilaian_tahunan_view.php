<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json, $type;
$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi) = explode("~", $infoSetting);

if($json == 1){
	header("Content-type: application/json");
	$filter  = "WHERE t1.id IS NOT NULL";
	if(!empty($par[empType]))
		$filter .= " AND t1.rank = '$par[empType]'";

	$sql = "
	SELECT 
	t1.id, t1.name, t1.reg_no, t1.pos_name, t2.namaData as posisi, t3.idPenilaian, t3.nilaiPenilaian, t3.tglPenilaian, t3.apprStatus, 
	(
	SELECT 
	IFNULL(
	(
	SELECT 
	warnaKonversi 
	FROM pen_setting_konversi 
	WHERE 
	(
	t3.nilaiPenilaian BETWEEN nilaiMin AND nilaiMax) AND kodeKonversi = '$kodeKonversi'
	), '#FF0000')) as warnaKonversi, t4.tipePenilaian, t3.kodeTipe
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.rank
	LEFT JOIN pen_penilaian t3
	ON t3.idSetting = '$par[idSetting]' ".(empty($type) ? "AND t3.idPenilai = '$cID'" : "")." AND t3.idPegawai = t1.id
	JOIN pen_pegawai t4
	ON t4.idPegawai = t1.id
	$filter
	".(empty($type) ? "AND t1.leader_id = '$cID'" : "")
	."ORDER BY t1.name ASC";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){
		$apprStatus = "";
		if(isset($menuAccess[$s]['apprlv1']) && !empty($r[idPenilaian]))
			$apprStatus .= "<a href=\"#appr\" onclick=\"openBox('popup.php?par[mode]=appr&par[idPegawai]=$r[id]".(!empty($r[idPenilaian]) ? "&par[idPenilaian]=$r[idPenilaian]" : "").getPar($par, "mode")."', 700, 375);\">";
		switch($r[apprStatus]){
			case "t":
			$apprStatus .= "<img src=\"styles/images/t.png\" title=\"Approved\">";
			break;

			default:
			$apprStatus .= "<img src=\"styles/images/f.png\" title=\"Tidak Approve\">";
			break;
		}
		if(isset($menuAccess[$s]['apprlv1']))
			$apprStatus .= "</a>";
		$r[apprStatus] = $apprStatus;
		$r[tglPenilaian] = $r[tglPenilaian] == "0000-00-00" || empty($r[tglPenilaian]) ? "-" : getTanggal($r[tglPenilaian]);
		$r[nilaiPenilaian] = getAngka($r[nilaiPenilaian], 2);
		$ret[] = $r;
	}
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
		<p style="position: absolute; right: 20px; top: 10px;">
			<input type="button" class="cancel radius2" value="Cancel" onclick="window.location='?<?= getPar($par, "mode,idPenilaian,kodeTipe,idPegawai,idSetting"); ?>';"/>
		</p>
		<p>
			<label class="l-input-small">Penilaian</label>
			<span class="field">
				<?= $namaSetting ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">Pelaksanaan</label>
			<span class="field">
				<?= getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai) ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">Penilai</label>
			<span class="field">
				<?= getField("SELECT name FROM dta_pegawai WHERE id = '$cID'") ?>&nbsp;
			</span>
		</p>
	</form>
	<br clear="all">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20">NO</th>
				<th>NAMA</th>
				<th width="100">NPP</th>
				<th width="100">JABATAN</th>
				<th width="100">POSISI</th>
				<th width="80">NILAI</th>
				<th width="80">TANGGAL</th>
				<th width="80">APPROVAL</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": sajax + "&json=1",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "name", "bSortable": true},
			{"mData": "reg_no", "sClass": "alignCenter", "bSortable": false},
			{"mData": "pos_name", "bSortable": false},
			{"mData": "posisi", "bSortable": false},
			{"mData": null, "bSortable": false, "sClass": "alignCenter"},
			{"mData": "tglPenilaian", "bSortable": false, "sClass": "alignCenter"},
			{"mData": "apprStatus", "bSortable": false, "sClass": "alignCenter"},
			],
			"aaSorting": [[0, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				var ret = "";
				<?php 
				if(isset($menuAccess[$s]['edit'])){ 
					?>
					ret += "<a href='?par[mode]=edit&amp;par[kodeTipe]=" + (aData['kodeTipe'] ? aData['kodeTipe'] : aData['tipePenilaian']) + "&amp;par[idPegawai]=" + aData['id'] + ((aData['idPenilaian'] != "") ? "&par[idPenilaian]=" + aData['idPenilaian'] :  "") + "<?= getPar($par, 'mode') ?>' title='Edit data' style='color: white; padding: 10px 20px; text-decoration: none;'>";
					<?php 
				} 
				?>
				ret += "<b>" + aData['nilaiPenilaian'] + "</b>";
				<?php 
				if(isset($menuAccess[$s]['edit'])){ 
					?>
					ret += "</a>";
					<?php 
				} 
				?>
				jQuery("td:nth-child(6)", nRow).css('background', aData['warnaKonversi']).html(ret);
				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		<?php if(!empty($type)){
			?>
			jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S09' AND statusData='t' ORDER BY urutanData", "kodeData", "namaData", "par[empType]", "All", $par[empType], "", "110px"); ?>');
			jQuery("#par\\[empType\\]").live("change", function (e) {
				e.preventDefault();
				ot.fnReloadAjax("ajax.php?json=1<?= getPar($par); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val());
				console.log("ajax.php?json=1<?= getPar($par); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val());
			});
			<?php
		}
		?>
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>
<?php 
/* End of file penilaian_tahunan_view.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_view.php */
<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json;

if(!isset($par[idSetting]))
	$par[idSetting] = getField("SELECT idSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai LIMIT 1");

$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi) = explode("~", $infoSetting);

if($json == 2){
	echo json_encode(array("namaSetting" => $namaSetting, "tanggalPelaksanaan" => getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai)));
	exit();
}

if($json == 1){
	header("Content-type: application/json");

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
	), '#FF0000')) as warnaKonversi, t4.tipePenilaian
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.rank
	LEFT JOIN pen_penilaian t3
	ON t3.idSetting = '$par[idSetting]' AND t3.idPenilai = '$cID' AND t3.idPegawai = t1.id
	JOIN pen_pegawai t4
	ON t4.idPegawai = t1.id
	WHERE t1.leader_id = '$cID'";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){
		$apprStatus = "";
		$apprStatus .= "<a href=\"#appr\" onclick=\"openBox('popup.php?par[mode]=appr&par[idPegawai]=$r[id]".(!empty($r[idPenilaian]) ? "&par[idPenilaian]=$r[idPenilaian]" : "").getPar($par, "mode")."', 700, 325);\">";
		switch($r[apprStatus]){
			case "t":
			$apprStatus .= "<img src=\"styles/images/t.png\" title=\"Approved\">";
			break;

			default:
			$apprStatus .= "<img src=\"styles/images/f.png\" title=\"Tidak Approve\">";
			break;
		}
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
			<?= comboData("SELECT idSetting, namaSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai", "idSetting", "namaSetting", "par[idSetting]", "", $par[idSetting], "", "225px"); ?>
		</p>
		<table width="100%">
			<tr>
				<td style="padding-right: 25px; vertical-align: top;">
					<p>
						<label class="l-input-small">Penilaian</label>
						<span class="field">
							<span id="displayNamaSetting"><?= $namaSetting ?></span>&nbsp;
						</span>
					</p>
					<p>
						<label class="l-input-small">Pelaksanaan</label>
						<span class="field">
							<span id="displayTanggalPelaksanaan"><?= getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai) ?></span>&nbsp;
						</span>
					</p>
					<p>
						<label class="l-input-small">Jumlah Karyawan</label>
						<span class="field">
							<span id="displayJumlahKaryawan">0</span> Karyawan
						</span>
					</p>
				</td>
				<td width="20%">
					<div class="ucup-box2 allports" style="width: 100%; height: 110px;">
						<div class="ucup-box2-content" style="padding-top: 30px;">
							<p class="ucup-box2-number" style="font-size: 45pt; margin-bottom: 15px;">0.00</p>
							<p class="ucup-box2-description" style="font-size: 10pt;">NILAI RATA-RATA</p>
						</div>
					</div>
				</td>
			</tr>
		</table>
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
	var rowCount = 0;
	var subNilai = 0;

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
				rowCount++;
				subNilai += parseFloat(aData['nilaiPenilaian']);
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				jQuery("#displayJumlahKaryawan").html((iDisplayIndexFull + 1));
				var ret = "";
				ret += "<a href='#view' onclick=\"openBox('popup.php?par[idSetting]=" + jQuery("#par\\[idSetting\\]").val() + "&par[mode]=edit&amp;par[kodeTipe]=" + aData['tipePenilaian'] + "&amp;par[idPegawai]=" + aData['id'] + ((aData['idPenilaian'] != "") ? "&par[idPenilaian]=" + aData['idPenilaian'] :  "") + "<?= getPar($par, "mode,idSetting") ?>', 1000, 600);\" title='Edit data' style='color: white; padding: 10px 20px; text-decoration: none;'>";
				ret += "<b>" + aData['nilaiPenilaian'] + "</b>";
				ret += "</a>";
				jQuery("td:nth-child(6)", nRow).css('background', aData['warnaKonversi']).html(ret);

				jQuery(".ucup-box2-number").html(Number((subNilai / rowCount)).toFixed(2));
				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

		jQuery("#par\\[idSetting\\]").live("change", function (e) {
			e.preventDefault();
			rowCount = 0;
			subNilai = 0;
			jQuery.ajax({
				url: sajax + "&json=2&par[idSetting]=" + jQuery("#par\\[idSetting\\]").val(),
				type: "GET",
				dataType: "json",
				success: function(data){
					jQuery("#displayNamaSetting").html(data['namaSetting']);
					jQuery("#displayTanggalPelaksanaan").html(data['tanggalPelaksanaan']);
				}
			});
			ot.fnReloadAjax(sajax + "&json=1&par[idSetting]=" + jQuery("#par\\[idSetting\\]").val());
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>
<?php 
/* End of file penilaian_tahunan_view.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_view.php */
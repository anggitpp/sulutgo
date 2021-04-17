<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json;

$infoEmp = getField("SELECT CONCAT(t1.reg_no, '~', IFNULL(t1.pic_filename, ''), '~', t1.name, '~', IFNULL(t1.pos_name, '-'), '~', IFNULL(t2.namaData, '-'), '~', IFNULL(t3.namaData, '-'), '~', IFNULL(t5.kodeTipe, '')) FROM dta_pegawai t1 JOIN mst_data t2 ON t2.kodeData = t1.cat LEFT JOIN mst_data t3 ON t3.kodeData = t1.rank LEFT JOIN pen_pegawai t4 ON t4.idPegawai = t1.id LEFT JOIN pen_tipe t5 ON t5.kodeTipe = t4.tipePenilaian WHERE t1.id = '$cID'");
list($reg_no, $pic_filename, $name, $pos_name, $status, $posisi, $kodeTipe) = explode("~", $infoEmp);

$arrAspek = arrayQuery("SELECT CONCAT(t3.idAspek, '~', t3.namaAspek) FROM pen_penilaian t1 LEFT JOIN pen_penilaian_detail t2 ON t2.idPenilaian = t1.idPenilaian LEFT JOIN pen_setting_aspek t3 ON t3.idAspek = t2.idAspek WHERE t1.idPegawai = '$cID' GROUP BY t3.namaAspek");
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<?php
	if(!empty($infoEmp)){
		?>
		<form class="stdform" >
			<fieldset>
				<table style="width: 100%">
					<tr>
						<td style="width: 10%; padding-right: 10px; padding-top: 5px;">
							<img alt="<?= $reg_no ?>" width="100%" height="105px" src="<?= APP_URL . "/files/emp/pic/" . (empty($pic_filename) ? "nophoto.jpg" : $pic_filename) ?>" >
						</td>
						<td style="width: 80%;vertical-align: top;">
							<div class="widgetbox" style="margin-bottom: 0px;">
								<table style="width: 100%;padding-top: -5px;" >
									<tr>
										<td style="padding-right: 5px;">
											<p>
												<label class="l-input-small">Nama</label>
												<span class="field">
													<?= $name ?>&nbsp;
												</span>
											</p>
											<p>
												<label class="l-input-small">NPP</label>
												<span class="field">
													<?= $reg_no ?>&nbsp;
												</span>
											</p>
											<p>
												<label class="l-input-small">Jabatan</label>
												<span class="field">
													<?= $pos_name ?>&nbsp;
												</span>
											</p>
										</td>
										<td style="width: 350px;">
											<p>
												<label class="l-input-small">Posisi</label>
												<span class="field">
													<?= $posisi ?>&nbsp;
												</span>
											</p>
											<p>
												<label class="l-input-small">Status</label>
												<span class="field">
													<?= $status ?>&nbsp;
												</span>
											</p>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
		<?php
		if(count($arrAspek) >= 1){
			?>
			<br clear="all">
			<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
				<thead>
					<tr>
						<th width="20">NO</th>
						<th>PENILAIAN</th>
						<?php
						foreach($arrAspek as $aspek){
							if($aspek != NULL){
								list($idAspek, $namaAspek) = explode("~", $aspek);
								echo "<th width=\"180\">$namaAspek</th>";	
							}
						}
						?>
						<th width="100">NILAI</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "SELECT t1.idPenilaian, t1.idSetting, t2.namaSetting, t3.kodeKonversi FROM pen_penilaian t1 JOIN pen_setting_penilaian t2 ON t2.idSetting = t1.idSetting JOIN pen_setting_kode t3 ON t3.idKode = t2.idKode WHERE t1.idPegawai = '$cID'";
					$res = db($sql);
					$no = 0;
					while($r = mysql_fetch_array($res)){
						$no++;
						$finalNilai = 0;
						$subNilai = 0;
						$subNo = 0;
						echo "
						<tr>
							<td width=\"20\" align=\"right\">$no.</td>
							<td>$r[namaSetting]</td>";
							foreach($arrAspek as $aspek){
								if($aspek != NULL){
									list($idAspek, $namaAspek) = explode("~", $aspek);
									$tempIdAspek = getField("SELECT t2.idAspek FROM pen_penilaian_detail t1 JOIN pen_setting_aspek t2 ON t2.idAspek = t1.idAspek WHERE t2.namaAspek = '$namaAspek' AND t1.idPenilaian = '$r[idPenilaian]'");
									$tempNilai = getField("SELECT AVG(bobotPilihan) FROM pen_penilaian_detail WHERE idPenilaian = '$r[idPenilaian]' AND idAspek = '$tempIdAspek'");
									if(!empty($tempNilai)){
										$subNo++;
										$subNilai += $tempNilai;
										$tempNilai = getAngka($tempNilai, 2);
									}else
									$tempNilai = "-";
									echo "<td width=\"180\" align=\"center\">$tempNilai</td>";	
								}
							}
							if($subNo > 0){
								$finalNilai = $subNilai / $subNo;
								$finalNilai = getAngka($finalNilai, 2);
							}else{
								$finalNilai = "-";
							}
							$warnaNilai = getField("
								SELECT IFNULL((SELECT warnaKonversi FROM pen_setting_konversi WHERE (".($finalNilai != "-" ? $finalNilai : 0)." BETWEEN nilaiMin AND nilaiMax) AND kodeKonversi = '$r[kodeKonversi]'), '#FF0000')
								");
							echo "
							<td width=\"100\" align=\"center\" style=\"background: $warnaNilai\">
								<a href='#view' onclick=\"openBox('popup.php?par[idSetting]=$r[idSetting]&par[mode]=edit&amp;par[kodeTipe]=$kodeTipe&amp;par[idPegawai]=$cID&par[idPenilaian]=$r[idPenilaian]".getPar($par, "mode")."', 1000, 600);\" title='Edit data' style='color: white; padding: 10px 20px; text-decoration: none;'>
									<b>$finalNilai</b>
								</a>
							</td>
						</tr>
						";
					}
					?>
				</tbody>
			</table>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				ot = jQuery('#datatable').dataTable({
					"sScrollY": "100%",
					"bSort": false,
					"bFilter": true,
					"iDisplayStart": 0,
					"sPaginationType": "full_numbers",
					"aaSorting": [[0, "asc"]],
					"fnInitComplete": function (oSettings) {
						oSettings.oLanguage.sZeroRecords = "No data available";
					}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
					"bProcessing": true,
					"oLanguage": {
						"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
					}
				});

				jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
				jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
				jQuery(window).bind('resize', function () {
					ot.fnAdjustColumnSizing();
				});
			});
		</script>
		<?php
	}else{
		?>
		<br clear="all">
		<div class="notibar announcement" style="background-color: #F44336; border: none;">
			<a class="close"></a>
			<p style="color: #FFF"><b>Belum ada penilaian</b></p>
		</div>
		<?php
	}
}else{
	?>
	<div class="notibar announcement" style="background-color: #F44336; border: none;">
		<a class="close"></a>
		<p style="color: #FFF"><b>Anda bukan karyawan</b></p>
	</div>
</div>
<?php
}
?>
<?php
/* End of file history_penilaian_view.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/history_penilaian/history_penilaian_view.php */